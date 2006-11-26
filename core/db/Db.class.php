<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/core/db/Db.class.php,v 1.61 2006/10/11 22:11:12 mpont Exp $
// $Date: 2006/10/11 22:11:12 $

//------------------------------------------------------------------
require_once(PHP2GO_ROOT . "vendor/adodb/adodb.inc.php");
require_once(PHP2GO_ROOT . "vendor/adodb/adodb-active-record.inc.php");
import('php2go.datetime.Date');
import('php2go.util.Callback');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		Db
// @desc		Respons�vel por criar conex�es a banco atrav�s da
//				biblioteca ADODb, e por conter fun��es que facilitam
//				a execu��o de instru��es DML e opera��es sobre os
//				result sets que retornam do banco. Sobrecarrega as
//				fun��es mais importantes implementas na classe ADOConnection
// @package		php2go.db
// @extends		PHP2Go
// @uses		ADOConnection
// @uses		Callback
// @uses		Date
// @uses		TypeUtils
// @note		Esta classe utiliza as funcionalidades da biblioteca
//				ADODb. Para maiores informa��es sobre o projeto ADODb,
//				manuais e documenta��o, acesse http://adodb.sourceforge.net
// @author		Marcos Pont
// @version		$Revision: 1.61 $
//!-----------------------------------------------------------------
class Db extends PHP2Go
{
	var $connected;					// @var connected bool				Flag de controle do status da conex�o com o banco de dados
	var $affectedRows;				// @var affectedRows int			Linhas afetadas ou resultantes da consulta
	var $lastStatement = array();	// @var lastStatement array			"array()" Armazena o �ltimo statement (comando ou query) executado no banco de dados
	var $makeCache;					// @var makeCache bool				Flag para utiliza��o de cache nos comandos de query e busca por resultados
	var $cacheSecs;					// @var cacheSecs int				N�mero de segundos para cache de um comando/consulta
	var $AdoDb;						// @var AdoDb ADOConnection object	Objeto da conex�o ao banco. Atrav�s dele, podem ser executados outros m�todos implementados pela classe AdoConnection

	//!-----------------------------------------------------------------
	// @function	Db::Db
	// @desc		Construtor da classe. Verifica as vari�veis
	//				da configura��o do PHP2Go necess�rias e cria a
	//				conex�o com o banco atrav�s do ADODb
	// @param		id string	"NULL" ID da conex�o desejada
	// @access		public
	//!-----------------------------------------------------------------
	function Db($id=NULL) {
		parent::PHP2Go();
		// busca dos par�metros de conex�o
		$connParameters = Conf::getConnectionParameters($id);
		if (!empty($connParameters['DSN'])) {
			// conex�o utilizando DSN (Database Storage Name)
			$this->AdoDb =& ADONewConnection($connParameters['DSN']);
			if (!$this->AdoDb)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATABASE_CONNECTION_FAILED'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			// conex�o com os par�metros de conex�o separados (type, host, user, password, database)
			$this->AdoDb =& AdoNewConnection($connParameters['TYPE']);
			$connFunc = ($connParameters['PERSISTENT'] ? 'PConnect' : 'Connect');
			if (!$this->AdoDb || !$this->AdoDb->$connFunc(@$connParameters['HOST'], $connParameters['USER'], @$connParameters['PASS'], $connParameters['BASE']))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATABASE_CONNECTION_FAILED'), E_USER_ERROR, __FILE__, __LINE__);
		}
		// fetch mode padr�o
		if (array_key_exists('FETCH_MODE', $connParameters))
			$this->AdoDb->SetFetchMode($connParameters['FETCH_MODE']);
		// transaction mode padr�o
		if (array_key_exists('TRANSACTION_MODE', $connParameters))
			$this->AdoDb->SetTransactionMode($connParameters['TRANSACTION_MODE']);
		$this->AdoDb->raiseErrorFn = 'dbErrorHandler';
		$this->connected = ($this->AdoDb->_connectionID !== FALSE);
		$this->affectedRows = 0;
		$this->makeCache = FALSE;
		if ($this->connected)
			$this->onAfterConnect();
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	Db::__destruct
	// @desc		Destrutor da classe
	// @note		Este m�todo ser� executado automaticamente pelo PHP2Go
	//				ao t�rmino do script que cont�m a inst�ncia do objeto
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
  	function __destruct() {
    	$this->close();
  	}

  	//!-----------------------------------------------------------------
  	// @function	Db::&getInstance
  	// @desc		M�todo est�tico que armazena inst�ncias �nicas de diferentes conex�es a banco de dados
  	// @param		id string	"NULL" ID da conex�o desejada
  	// @return		Db object Inst�ncia da classe Db
	// @note		� recomend�vel que todo e qualquer acesso a uma conex�o a banco de dados
	//				se inicie por uma chamada a este m�todo, garantindo economia de recursos
	//				tanto na aplica��o quanto no SGBD
  	// @access		public
	// @static
  	//!-----------------------------------------------------------------
  	function &getInstance($id=NULL) {
  		static $instances;
  		if (!isset($instances))
  			$instances = array();
  		$Conf =& Conf::getInstance();
  		if (!TypeUtils::isNull($id)) {
  			$key = $id;
  		} else {
  			$default = $Conf->getConfig('DATABASE.DEFAULT_CONNECTION');
  			if (!empty($default)) {
  				$key = $default;
  			} else {
  				$connections = $Conf->getConfig('DATABASE.CONNECTIONS');
  				if (TypeUtils::isArray($connections)) {
					reset($connections);
  					list($key, $value) = each($connections);
  				} else {
  					$key = 'DEFAULT';
  				}
  			}
  		}
  		if (!isset($instances[$key])) {
  			if ($connectionClassPath = $Conf->getConfig('DATABASE.CONNECTION_CLASS_PATH')) {
  				if ($connectionClass = classForPath($connectionClassPath)) {
  					$instances[$key] =& new $connectionClass($id);
  					if (!TypeUtils::isInstanceOf($instances[$key], 'Db'))
  						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_CONNECTION_CLASS', $connectionClass), E_USER_ERROR, __FILE__, __LINE__);
  				} else {
  					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_CONNECTION_CLASS_PATH', $connectionClassPath), E_USER_ERROR, __FILE__, __LINE__);
  				}
  			} else {
  				$instances[$key] =& new Db($id);
  			}
  		}
  		return $instances[$key];
  	}

	//!-----------------------------------------------------------------
	// @function	Db::setCache
	// @desc		Configura o objeto de banco de dados para utilizar
	//				ou n�o cache em consultas, comandos DML e m�todos
	//				que buscam resultados
	// @note		Para utilizar cache, utilize $flag=TRUE e $seconds>0
	// @note		Para remover result sets em cache, utilize $flag=TRUE e $seconds=0
	// @note		Para n�o utilizar cache, utilize $flag=FALSE
	// @param		flag bool		Novo valor para configura��o de uso de cache
	// @param		seconds int		N�mero de segundos de durabilidade da cache realizada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCache($flag, $seconds=0) {
		$flag = !!$flag;
		$seconds = abs(intval($seconds));
		$seconds = TypeUtils::parseIntegerPositive($seconds);
		if ($flag) {
			$this->makeCache = TRUE;
			$this->cacheSecs = $seconds;
		} else {
			$this->makeCache = FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::setDebug
	// @desc		Habilita ou desabilita debug na conex�o com o banco de dados
	// @param		setting bool	Valor para o flag de debug
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setDebug($setting=TRUE) {
		$this->AdoDb->debug = ($setting ? 1 : 0);
	}

	//!-----------------------------------------------------------------
	// @function	Db::setErrorHandler
	// @desc		Configura a fun��o de tratamento de erros no banco de dados
	// @param		errorHandler mixed	Nome da fun��o
	// @return		string Nome do tratador de erros configurado antes da execu��o deste m�todo
	// @note		Utilize o valor FALSE para desabilitar o tratamento de erros de
	//				banco de dados do PHP2Go
	// @access		public
	//!-----------------------------------------------------------------
	function setErrorHandler($errorHandler) {
		$oldErrorHandler = $this->AdoDb->raiseErrorFn;
		$this->AdoDb->raiseErrorFn = $errorHandler;
		return $oldErrorHandler;
	}

	//!-----------------------------------------------------------------
	// @function	Db::setFetchMode
	// @desc		Configura o modo de constru��o dos resultados de uma consulta
	// @param		mode int	Modo a ser utilizado
	// @note		Valores possiveis para o par�metro $mode (constantes):<br>
	//				ADODB_FETCH_DEFAULT: utilizar o padr�o do banco de dados<br>
	//				ADODB_FETCH_NUM: fetch num�rico<br>
	//				ADODB_FETCH_ASSOC: fetch associativo<br>
	//				ADODB_FETCH_BOTH: fetch num�rico E associativo
	// @return		int Valor antigo da propriedade
	// @access		public
	//!-----------------------------------------------------------------
	function setFetchMode($mode) {
		return $this->AdoDb->SetFetchMode($mode);
	}

	//!-----------------------------------------------------------------
	// @function	Db::setForceType
	// @desc		Define o tipo de tratamento que deve ser dado a colunas vazias
	//				nos m�todos insert (constru��o autom�tica de comando INSERT) e
	//				update (constru��o autom�tica de comando UPDATE)
	// @param		forceType int	Tipo de tratamento
	// @note		Valores poss�veis para o par�metro $forceType (constantes):<br>
	//				ADODB_FORCE_IGNORE: ignorar colunas vazias, NULL ou 'null'<br>
	//				ADODB_FORCE_NULL: transformar em SQL NULL as colunas vazias, null ou 'null'<br>
	//				ADODB_FORCE_EMPTY: for�ar valor SQL vazio para colunas vazias, null ou 'null'<br>
	//				ADODB_FORCE_VALUE: deixar o valor como est�; colunas vazias ser�o mapeadas para SQL vazio e NULL e 'null' para SQL NULL
	// @return		mixed	Valor antigo da propriedade, ou FALSE se a altera��o n�o pode ser efetuada
	// @access		public
	//!-----------------------------------------------------------------
	function setForceType($forceType) {
		if (in_array($forceType, array(ADODB_FORCE_IGNORE, ADODB_FORCE_NULL, ADODB_FORCE_EMPTY, ADODB_FORCE_VALUE))) {
			global $ADODB_FORCE_TYPE;
			$old = $ADODB_FORCE_TYPE;
			$ADODB_FORCE_TYPE = $forceType;
			return $old;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Db::affectedRows
	// @desc		Retorna o n�mero de linhas retornadas da consulta
	//				ou o n�mero de linhas afetadas pelo comando DML
	// @return		int N�mero de linhas da consulta ou afetadas por DML
	// @access		public
	//!-----------------------------------------------------------------
	function affectedRows() {
		return $this->affectedRows;
	}

	//!-----------------------------------------------------------------
	// @function	Db::lastInsertId
	// @desc		Retorna o �ltimo c�digo AUTONUMBER gerado pelo banco de dados
	// @return		int O �ltimo c�digo gerado ou FALSE se n�o suportado pelo tipo de banco utilizado
	// @access		public
	//!-----------------------------------------------------------------
	function lastInsertId() {
		return ($this->AdoDb->hasInsertID ? $this->AdoDb->Insert_ID() : 0);
	}

  	//!-----------------------------------------------------------------
  	// @function	Db::getConnectionId
  	// @desc		Retorna o handle da conex�o ativa
  	// @return		resource Handle da conex�o ativa ou NULL se n�o existir
  	// @access		public
  	//!-----------------------------------------------------------------
  	function getConnectionId() {
  		return ($this->connected ? $this->AdoDb->_connectionID : NULL);
  	}

  	//!-----------------------------------------------------------------
  	// @function	Db::getDatabaseType
  	// @desc		Retorna o nome do driver associado a esta conex�o
  	// @return		string Nome do driver
  	// @access		public
  	//!-----------------------------------------------------------------
  	function getDatabaseType() {
  		return $this->AdoDb->databaseType;
  	}

  	//!-----------------------------------------------------------------
  	// @function	Db::getServerInfo
  	// @desc		Busca as informa��es sobre o servidor de banco de
  	//				dados da conex�o ativa
  	// @return		array Vetor de informa��es
  	// @note		Para uma documenta��o mais detalhada das informa��es
  	//				retornadas, consulte a documenta��o da biblioteca ADODb
  	// @access		public
  	//!-----------------------------------------------------------------
  	function getServerInfo() {
  		return ($this->connected ? $this->AdoDb->ServerInfo() : NULL);
  	}

	//!-----------------------------------------------------------------
	// @function	Db::getError
	// @desc		Verifica se existe uma mensagem de erro armazenada
	// @return		string A �ltima mensagem de erro armazenada ou FALSE se n�o existir
	// @access		public
	//!-----------------------------------------------------------------
	function getError() {
		$errorMsg = $this->AdoDb->ErrorMsg();
		if (!empty($errorMsg) && strlen($errorMsg) > 0)
			return $errorMsg;
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Db::getErrorCode
	// @desc		Busca o c�digo de erro do banco de dados
	// @return		int C�digo de erro do banco de dados, ou NULL se n�o existir
	// @access		public
	//!-----------------------------------------------------------------
	function getErrorCode() {
		return $this->AdoDb->ErrorNo();
	}

	//!-----------------------------------------------------------------
	// @function	Db::getDatabases
	// @desc		Retorna as bases de dados existentes no banco de dados
	// @return		array Vetor contendo as bases de dados encontradas
	// @see			Db::getTables
	// @access		public
	//!-----------------------------------------------------------------
	function getDatabases() {
		return $this->AdoDb->MetaDatabases();
	}

	//!-----------------------------------------------------------------
	// @function	Db::getTables
	// @desc		Retorna as tabelas existentes na base de dados atual
	// @param		tableType string	'TABLE' lista apenas tabelas, 'VIEW' lista apenas views
	// @return		array Vetor contendo as tabelas encontradas
	// @see			Db::getDatabases
	// @access		public
	//!-----------------------------------------------------------------
	function getTables($tableType=FALSE) {
		$tables = $this->AdoDb->MetaTables($tableType);
		return $tables;
	}

	//!-----------------------------------------------------------------
	// @function	Db::getColumns
	// @desc		Retorna a lista de colunas de uma tabela ou view, onde
	//				cada elemento da lista � uma inst�ncia de ADOFieldObject
	// @param		table string		Nome da tabela ou view
	// @return		array Vetor contendo os objetos das colunas da tabela
	// @see			Db::getColumnNames
	// @access		public
	//!-----------------------------------------------------------------
	function getColumns($table) {
		return $this->AdoDb->MetaColumns($table);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getColumnNames
	// @desc		Busca os nomes das colunas de uma tabela ou view
	// @param		table string		Nome da tabela ou view
	// @param		assoc bool			"TRUE" Retornar um array associativo (TRUE) ou num�rico (FALSE)
	// @return		array Vetor contendo os nomes das colunas da tabela
	// @see			Db::getColumns
	// @access		public
	//!-----------------------------------------------------------------
	function getColumnNames($table, $assoc=TRUE) {
		return $this->AdoDb->MetaColumnNames($table, !$assoc);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getPrimaryKeys
	// @desc		Busca os nomes das chaves prim�rias da tabela $table
	// @param		table string		Nome da tabela
	// @return		array Vetor contendo os nomes das chaves prim�rias
	// @access		public
	//!-----------------------------------------------------------------
	function getPrimaryKeys($table) {
		return $this->AdoDb->MetaPrimaryKeys($table);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getIndexes
	// @desc		Busca os nomes dos �ndices definidos para uma tabela
	// @param		table string		Nome da tabela
	// @return		array Vetor contendo os nomes dos �ndices da tabela
	// @access		public
	//!-----------------------------------------------------------------
	function getIndexes($table) {
		return $this->AdoDb->MetaIndexes($table);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getProcedureSQL
	// @desc		Monta o SQL para a execu��o de uma procedure no banco de dados.
	//				Possui implementa��es diferentes dependendo do banco utilizado
	// @param		stmt string		SQL da procedure
	// @param		prepare bool	"FALSE" Retornar um statement preparado ou somente a string SQL
	// @return		mixed String SQL ou o array do statement preparado
	// @access		public
	//!-----------------------------------------------------------------
	function getProcedureSQL($stmt, $prepare=FALSE) {
		switch ($this->AdoDb->dataProvider) {
			// oci8, oci805, ocipo
			case 'oci8' :
				$stmt = "begin {$stmt}; end;";
				break;
			// db2
			case 'db2' :
			// mysqli
			case 'mysqli' :
				$stmt = "call {$stmt};";
				break;
			// sybase, sybase_ase
			case 'sybase' :
				$stmt = "exec {$stmt}";
				break;
			// @todo suportar outros formatos de execu��o de procedure
			default :
				break;
		}
		if ($prepare)
			return $this->prepare($stmt, TRUE);
		else
			return $stmt;
	}

  	//!-----------------------------------------------------------------
  	// @function	Db::getNextId
  	// @desc		Busca o pr�ximo valor de uma seq��ncia, para preenchimento de
  	//				chaves prim�rias nas inser��es de dados
  	// @param		seqName string		"p2gseq" Nome da seq��ncia
  	// @param		startId int			"1" ID inicial, caso a seq��ncia n�o exista
  	// @return		int Pr�ximo valor da seq��ncia indicada
  	// @access		public
  	//!-----------------------------------------------------------------
  	function getNextId($seqName='p2gseq', $startId=1) {
  		return ($this->connected ? $this->AdoDb->GenID($seqName, $startId) : 0);
  	}

	//!-----------------------------------------------------------------
	// @function	Db::getFirstCell
	// @desc		Executa o comando SQL indicado pela vari�vel $sql,
	//				buscando apenas a primeira c�lula do result set resultante
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Vari�veis de bind a serem utilizadas
	// @return		string Valor da primeira c�lula do result set ou FALSE se ocorrer algum erro
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				� consulta SQL na cache
	// @see			Db::getFirstRow
	// @see			Db::getFirstCol
	// @see			Db::getAll
	// @access		public
	//!-----------------------------------------------------------------
	function getFirstCell($stmt, $bindVars=FALSE) {
		$this->lastStatement = array(
			'source' => 'getFirstCell',
			'statement' => $stmt,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if ($this->makeCache)
			return $this->AdoDb->CacheGetOne($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetOne($stmt, $bindVars);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getFirstRow
	// @desc		Executa o comando SQL indicado pelo par�metro $sql,
	//				buscando a primeira linha do result set e ignorando o restante
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Vari�veis de bind a serem utilizadas
	// @return		string Vetor unidimensional da primeira linha do result set ou FALSE se ocorrerem erros
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				� consulta SQL na cache
	// @see			Db::getFirstCell
	// @see			Db::getFirstCol
	// @see			Db::getAll
	// @access		public
	//!-----------------------------------------------------------------
	function getFirstRow($stmt, $bindVars=FALSE) {
		$this->lastStatement = array(
			'source' => 'getFirstRow',
			'statement' => $stmt,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if ($this->makeCache)
			return $this->AdoDb->CacheGetRow($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetRow($stmt, $bindVars);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getFirstCol
	// @desc		Executa o comando SQL indicado pela vari�vel $sql,
	//				buscando a primeira coluna do result set e ignorando o restante
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Vari�veis de bind a serem utilizadas
	// @return		array Vetor unidimensional da primeira coluna do result set ou FALSE se ocorrerem erros
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				� consulta SQL na cache
	// @see			Db::getFirstCell
	// @see			Db::getFirstRow
	// @see			Db::getAll
	// @access		public
	//!-----------------------------------------------------------------
	function getFirstCol($stmt, $bindVars=FALSE) {
		$this->lastStatement = array(
			'source' => 'getFirstCol',
			'statement' => $stmt,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if ($this->makeCache)
			return $this->AdoDb->CacheGetCol($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetCol($stmt, $bindVars);
	}

	//!-----------------------------------------------------------------
	// @function	Db::&getActiveRecords
	// @desc		Executa uma query na tabela $table, usando a cl�usula
	//				de condi��o $clause, e retorna uma lista de objetos que
	//				podem ser manipulados. Estes objetos s�o baseados no
	//				padr�o ActiveRecord
	// @param		table string	Nome da tabela
	// @param		clause string	"NULL" Cl�usula de condi��o
	// @param		bindVars array	"FALSE" Conjunto de vari�veis de bind
	// @param		options array	"array()" Op��es extra para o m�todo
	// @note		O par�metro $options aceita as seguintes op��es:<br>
	//				class: nome da classe a ser instanciada para cada resultado da consulta<br>
	//				order: cl�usula de ordena��o para a consulta<br>
	//				primaryKeys: conjunto de chaves prim�rias da tabela
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function &getActiveRecords($table, $clause=NULL, $bindVars=FALSE, $options=array()) {
		$options = (array)$options;
		$className = (array_key_exists('class', $options) ? $options['class'] : 'ADODB_Active_Record');
		$clause = (empty($clause) ? "1=1" : $clause);
		$clause .= (array_key_exists('order', $options) ? " ORDER BY {$options['order']}" : '');
		$primaryKeys = (array_key_exists('primaryKeys', $options) ? $options['primaryKeys'] : FALSE);
		$this->lastStatement = array(
			'source' => 'getActiveRecords',
			'statement' => "SELECT * FROM {$table} WHERE {$clause}",
			'vars' => ($bindVars ? $bindVars : array())
		);
		$records =& $this->AdoDb->GetActiveRecordsClass($className, $table, $clause, $bindVars, $primaryKeys);
		return $records;
	}

	//!-----------------------------------------------------------------
	// @function	Db::getAll
	// @desc		Executa o comando SQL indicado pela vari�vel $sql,
	//				retornando todo o conte�do do result set
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Vari�veis de bind a serem utilizadas
	// @return		array Vetor bidimensional do result set ou FALSE se ocorrerem erros
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				� consulta SQL na cache
	// @see			Db::getFirstCell
	// @see			Db::getFirstRow
	// @see			Db::getFirstCol
	// @access		public
	//!-----------------------------------------------------------------
	function getAll($stmt, $bindVars=FALSE) {
		$this->lastStatement = array(
			'source' => 'getAll',
			'statement' => $stmt,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if ($this->makeCache)
			return $this->AdoDb->CacheGetAll($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetAll($stmt, $bindVars);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getCount
	// @desc		Executa o comando SQL a fim de buscar o total de linhas resultante da consulta
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Vari�veis de bind a serem utilizadas
	// @param		optimize bool		"TRUE" Otimizar a contagem retirando cl�usulas de ordena��o da consulta
	// @return		int Total de linhas resultantes da consulta fornecida
	// @access		public
	//!-----------------------------------------------------------------
	function getCount($stmt, $bindVars=FALSE, $optimize=TRUE) {
		$count = 0;
		$matches = array();
		$sql = (TypeUtils::isArray($stmt) ? $stmt[0] : $stmt);
		// drivers com nestedSQL, consultas com DISTINCT ou GROUP BY
		if (!empty($this->AdoDb->_nestedSQL) || preg_match("/^\s*SELECT\s+DISTINCT/is", $sql) || preg_match('/\s+GROUP\s+BY\s+/is',$sql) || preg_match('/\s+UNION\s+/is',$sql)) {
			$rewriteSql = $sql;
			// oci8 e oci8po
			if ($this->AdoDb->dataProvider == 'oci8') {
				if ($optimize)
					$rewriteSql = preg_replace('/(\sORDER\s+BY\s[^)]*)/is', '', $rewriteSql);
				if (preg_match('#/\\*+.*?\\*\\/#', $sql, $matches))
					$rewriteSql = "SELECT {$matches[0]} COUNT(*) FROM ({$rewriteSql})";
				else
					$rewriteSql = "SELECT COUNT(*) FROM ({$rewriteSql})";
			}
			// mysql e mysqli
			elseif (strncmp($this->AdoDb->databaseType, 'mysql', 5) == 0) {
				$info = $this->AdoDb->ServerInfo();
				$version = TypeUtils::parseFloat($info['version']);
				if ($version >= 4.1) {
					if ($optimize) {
						$rewriteSql = preg_replace("/(\sORDER\s+BY\s[^(]*)(LIMIT)/Uis", "\\2", $rewriteSql);
						$rewriteSql = preg_replace('/(\sORDER\s+BY\s[^(]*)?/is', '', $rewriteSql);
					}
					$rewriteSql = "select COUNT(*) from ($rewriteSql) _ADODB_ALIAS_";
				}
			}
			// postgres7 e postgres8
			elseif (strncmp($this->AdoDb->databaseType, 'postgres', 8) == 0) {
				if ($optimize)
					$rewriteSql = preg_replace('/(\sORDER\s+BY\s[^)]*)/is', '', $rewriteSql);
				$rewriteSql = "select COUNT(*) from ($rewriteSql) _ADODB_ALIAS_";
			}
		// outros tipos de consultas: substituir o select (.+) from mais significativo por select count(*) from
		} else {
			$stack = 1;
			$index = -1;
			// remover a primeira instru��o select
			$sql = preg_replace("/^select/i", "", trim($sql));
			// remover fun��es que utilizam a palavra "from"
			$sql = preg_replace('/(substring|extract)\s*\([^\)]+\)\s*/is', '', $sql);
			$words = preg_split('/\s+/', $sql);
			for ($i=0, $size=sizeof($words); $i<$size; $i++) {
				if (preg_match('/select/i', $words[$i]))
					$stack++;
				elseif (preg_match('/from/i', $words[$i]))
					$stack--;
				if ($stack == 0) {
					$index = $i;
					break;
				}
			}
			if ($index > -1) {
				$result = array_slice($words, $index);
				$rewriteSql = "select COUNT(*) " . implode(" ", $result);
				if ($optimize) {
					if (preg_match('/\sORDER\s+BY\s*\(/i', $rewriteSql))
						$rewriteSql = preg_replace('/(\sORDER\s+BY\s.*)/is', '', $rewriteSql);
					else
						$rewriteSql = preg_replace('/(\sORDER\s+BY\s[^)]*)/is', '', $rewriteSql);
				}
			}
		}
		// executa a consulta de count se ela for v�lida
		if (isset($rewriteSql) && $rewriteSql != $sql) {
			if ($this->makeCache)
				$count = $this->AdoDb->CacheGetOne($this->cacheSecs, $rewriteSql, $bindVars);
			else
				$count = $this->AdoDb->GetOne($rewriteSql, $bindVars);
			if ($count !== FALSE) {
				$this->lastStatement = array(
					'source' => 'getCount',
					'statement' => $rewriteSql,
					'vars' => ($bindVars ? $bindVars : array())
				);
				return $count;
			}
		}
		// reescrita da query falhou, a consulta original ser� utilizada
		if (preg_match('/\s*UNION\s*/is', $sql) || !$optimize)
			$rewriteSql = $sql;
		else
			$rewriteSql = preg_replace('/(\sORDER\s+BY\s.*)/is', '', $sql);
		$this->lastStatement = array(
			'source' => 'getCount',
			'statement' => $rewriteSql,
			'vars' => ($bindVars ? $bindVars : array())
		);
		$rs =& $this->AdoDb->Execute($rewriteSql, $bindVars);
		if ($rs) {
			$count = $rs->RecordCount();
			if ($count == -1) {
				while (!$rs->EOF)
					$rs->MoveNext();
				$count = $rs->_currentRow;
			}
			$rs->Close();
			if ($count > -1)
				return $count;
		}
		return 0;
	}

	//!-----------------------------------------------------------------
	// @function	Db::setTransactionMode
	// @desc		Define o tipo de transa��es que devem ser criadas pela
	//				conex�o ativa ao banco de dados.
	// @note		Consulte a documenta��o da biblioteca ADODb para maiores
	//				informa��es sobre os poss�veis valores para $mode
	// @param		mode string		Tipo de transa��es
	// @return		void
	//!-----------------------------------------------------------------
	function setTransactionMode($mode) {
		$this->AdoDb->SetTransactionMode($mode);
	}

	//!-----------------------------------------------------------------
	// @function	Db::startTransaction
	// @desc		Cria uma nova transa��o no banco de dados
	// @note		Se for executada em um tipo de banco de dados que n�o
	//				suporta transa��es, retorna FALSE
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function startTransaction() {
		return $this->AdoDb->StartTrans();
	}

	//!-----------------------------------------------------------------
	// @function	Db::failTransaction
	// @desc		Reporta um erro na execu��o de um comando de uma transa��o
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function failTransaction() {
		return $this->AdoDb->FailTrans();
	}

	//!-----------------------------------------------------------------
	// @function	Db::hasFailedTransaction
	// @desc		Verifica se a transa��o ativa falhou
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasFailedTransaction() {
		return $this->AdoDb->HasFailedTrans();
	}

	//!-----------------------------------------------------------------
	// @function	Db::completeTransaction
	// @desc		Finaliza a transa��o, verificando os erros e executando
	//				automaticamente a efetiva��o com commit ou a recupera��o
	//				com rollback
	// @param		forceRollback bool	"FALSE" For�ar a execu��o de rollback mesmo que n�o existam erros
	// @return		bool TRUE se a transa��o foi comitada, ou FALSE em caso contr�rio
	// @note		Se for executada em um driver que n�o suporta transa��es, retorna FALSE
	// @access		public
	//!-----------------------------------------------------------------
	function completeTransaction($forceRollback=FALSE) {
		return $this->AdoDb->CompleteTrans(!TypeUtils::toBoolean($forceRollback));
	}

	//!-----------------------------------------------------------------
	// @function	Db::commit
	// @desc		Encerra uma transa��o com sucesso. Se o par�metro
	//				$flag for FALSE, executa um rollback na transa��o
	// @param		flag bool		"TRUE" Indica se a transa��o deve ser encerrada com sucesso (TRUE) ou n�o (FALSE)
	// @return		bool Indica o status da opera��o realizada
	// @note		Se for executada em um tipo de banco de dados que n�o
	//				suporta transa��es, retorna TRUE
	// @access		public
	//!-----------------------------------------------------------------
	function commit($flag=TRUE) {
		return $this->AdoDb->CommitTrans(TypeUtils::toBoolean($flag));
	}

	//!-----------------------------------------------------------------
	// @function	Db::rollback
	// @desc		Encerra uma transa��o desfazendo todas as suas
	//				altera��es no estado do banco de dados
	// @return		bool Indica o status da opera��o realizada
	// @note		Se for executada em um tipo de banco de dados que n�o
	//				suporta transa��es, retorna FALSE
	// @access		public
	//!-----------------------------------------------------------------
	function rollback() {
		return $this->AdoDb->RollbackTrans();
	}

	//!-----------------------------------------------------------------
	// @function	Db::&prepare
	// @desc		Prepara uma instru��o SQL para execu��o
	// @param		stmtCode string	C�digo da instru��o a ser preparada
	// @param		cursor bool		"FALSE" Indica se haver� retorno de cursor na instru��o executada
	// @return		string Array contendo instru��o SQL e par�metros ou a instru��o
	//				SQL original se o driver utilizado n�o suportar esta funcionalidade
	// @access		public
	//!-----------------------------------------------------------------
	function prepare($stmtCode, $cursor=FALSE) {
		return $this->AdoDb->Prepare($stmtCode, TypeUtils::toBoolean($cursor));
	}

	//!-----------------------------------------------------------------
	// @function	Db::bind
	// @desc		Atribui um valor a uma vari�vel de substitui��o em um statement criado
	// @param		statement array		Statement previamente criado com $Db->prepare()
	// @param		&value mixed		Valor para o par�metro
	// @param		varName string		Nome da vari�vel no statement
	// @param		type mixed			"FALSE" Tipo da vari�vel, depende dos tipos pr�-definidos pelo BD
	// @param		maxLen int			"4000" Tamanho m�ximo para a vari�vel bind
	// @param		isOutput bool		"FALSE" Indica se o par�metro � IN (FALSE) ou OUT (TRUE)
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function bind($statement, &$value, $varName, $type=FALSE, $maxLen=4000, $isOutput=FALSE) {
		return $this->AdoDb->Parameter($statement, $value, $varName, $isOutput, $maxLen, $type);
	}

	//!-----------------------------------------------------------------
	// @function	Db::quoteString
	// @desc		Insere corretamente haspas em uma string levando em conta
	//				os caracteres de escape
	// @param		str string			String a ser processada
	// @param		magicQuotes bool	"FALSE" Forne�a o retorno da fun��o get_magic_quotes_gpc()
	//									ou get_magic_quotes_runtime() para levar em conta estes casos
	//									no tratamento de caracteres de escape
	// @access		public
	// @return		string String processada
	//!-----------------------------------------------------------------
	function quoteString($str, $magicQuotes=FALSE) {
		return $this->AdoDb->qstr($str, $magicQuotes);
	}

	//!-----------------------------------------------------------------
	// @function	Db::date
	// @desc		Permite transformar uma data ou timestamp para o formato
	//				de data do banco de dados, incluindo os quotes
	// @note		Forne�a TRUE no par�metro $bind quando o valor de data ou data/hora
	//				for utilizando na amarra��o de vari�veis para um statement, pois
	//				os valores retornados n�o ir�o possuir os quotes
	// @param		date mixed Data em formato string (EURO, SQL ou US) ou unix timestamp
	// @param		time bool	"FALSE" Formatar data/hora (TRUE) ou data (FALSE)
	// @param		bind bool	"FALSE" Formatar para bind
	// @return		string Data formatada de acordo com os padr�es da conex�o ativa
	// @access		public
	//!-----------------------------------------------------------------
	function date($date=NULL, $time=FALSE, $bind=FALSE) {
		if (empty($date)) {
			return ($time ? $this->AdoDb->sysTimeStamp : $this->AdoDb->sysDate);
		} else {
			if (!TypeUtils::isInteger($date)) {
				// aplica convers�o euro->sql (se n�o for data euro, n�o muda o valor)
				$date = Date::fromEuroToSqlDate($date, $time);
				// aplica convers�o us->sql (se n�o for data us, n�o muda o valor)
				$date = Date::fromUsToSqlDate($date, $time);
			}
			if ($time)
				return ($bind ? $this->AdoDb->BindTimeStamp($date) : $this->AdoDb->DBTimeStamp($date));
			else
				return ($bind ? $this->AdoDb->BindDate($date) : $this->AdoDb->DBDate($date));
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::&execute
	// @desc		Executa um statement na conex�o com o banco de dados
	// @param		statement mixed		Vetor com dados do statement ou instru��o SQL a ser executada
	// @param		bindVars mixed		"FALSE" Vari�veis de bind a serem utilizadas
	// @param		cursorName string	"NULL" Nome do cursor dentro do c�digo do statement (apenas para oci8)
	// @return		ADORecordset object Result Set ou FALSE em caso de erros ou resultado vazio
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				na cache
	// @access		public
	//!-----------------------------------------------------------------
	function &execute($statement, $bindVars=FALSE, $cursorName=NULL) {
		$this->lastStatement = array(
			'source' => 'execute',
			'statement' => $statement,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if (!TypeUtils::isNull($cursorName) && $this->AdoDb->dataProvider == 'oci8')
			$rs =& $this->AdoDb->ExecuteCursor($statement, $cursorName, $bindVars);
		elseif ($this->makeCache)
			$rs =& $this->AdoDb->CacheExecute($this->cacheSecs, $statement, $bindVars);
		else
			$rs =& $this->AdoDb->Execute($statement, $bindVars);
		if ($rs) {
			$this->affectedRows = ($rs->EOF ? 0 : $rs->RecordCount());
			return $rs;
		} else {
			$false = FALSE;
			$this->affectedRows = 0;
			return $false;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::&query
	// @desc		Executa uma query na conex�o com o banco de dados
	// @param		sqlCode string	C�digo SQL a ser executado
	// @param		execute	bool		"TRUE" Flag para executar ou exibir o c�digo
	// @param		bindVars  mixed		"FALSE" Vari�veis de bind a serem aplicadas no c�digo SQL
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente � consulta SQL na cache
	// @return		ADORecordset object Result set se a consulta puder ser executada ou FALSE em caso de erros
	// @access		public
	//!-----------------------------------------------------------------
	function &query($sqlCode, $execute=TRUE, $bindVars=FALSE) {
		if ($execute) {
			$this->lastStatement = array(
				'source' => 'query',
				'statement' => $sqlCode,
				'vars' => ($bindVars ? $bindVars : array())
			);
			if ($this->makeCache)
				$rs =& $this->AdoDb->CacheExecute($this->cacheSecs, $sqlCode, $bindVars);
			else
				$rs =& $this->AdoDb->Execute($sqlCode, $bindVars);
			if ($rs) {
				$this->affectedRows = $rs->RecordCount();
			} else {
				$this->affectedRows = 0;
				$rs =& $this->emptyRecordSet();
			}
			return $rs;
		} else {
			$true = TRUE;
			println((is_array($sqlCode) ? $sqlCode[0] : $sqlCode));
			return $true;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::&limitQuery
	// @desc		Executa uma query com limite no banco de dados
	// @param		sqlCode string	C�digo da consulta SQL
	// @param		offset int			"-1" N�mero de linhas requerido, omita para buscar todas a partir de $lowerBound
	// @param		lowerBound int		"0" Limite inferior requerido, omita para buscar todas at� $offset
	// @param		execute bool		"TRUE" Flag para executar ou exibir o c�digo
	// @param		bindVars array		"FALSE" Vetor opcional de vari�veis bind a serem aplicadas no c�digo SQL
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente � consulta SQL na cache
	// @return		ADORecordset object Result set se a consulta puder ser executada
	// @access		public
	//!-----------------------------------------------------------------
	function &limitQuery($sqlCode, $offset=-1, $lowerBound=0, $execute=TRUE, $bindVars=FALSE) {
		if ($lowerBound < 0) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MUST_BE_POSITIVE', array("\$lowerBound", "limitQuery")), E_USER_WARNING, __FILE__, __LINE__);
			$lowerBound = 0;
		}
		if ($execute) {
			$this->lastStatement = array(
				'source' => 'limitQuery',
				'statement' => $sqlCode,
				'vars' => ($bindVars ? $bindVars : array())
			);
			if ($this->makeCache)
				$rs =& $this->AdoDb->CacheSelectLimit($this->cacheSecs, $sqlCode, $offset, $lowerBound, $bindVars);
			else
				$rs =& $this->AdoDb->SelectLimit($sqlCode, $offset, $lowerBound, $bindVars);
			if ($rs) {
				$this->affectedRows = $rs->RecordCount();
			} else {
				$this->affectedRows = 0;
				$rs =& $this->emptyRecordSet();
			}
			return $rs;
		} else {
			$true = TRUE;
			println((is_array($sqlCode) ? $sqlCode[0] : $sqlCode));
			return $true;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::insert
	// @desc		Constr�i e executa um comando DML 'INSERT'
	// @param		table string		Nome da tabela ou view
	// @param		arrData array		Array associativo com os dados
	// @param		options array		"array()" Op��es de inser��o
	// @note		Conjunto de op��es dispon�veis para o par�metro $options:<br>
	//				forceType: tipo de tratamento para as colunas vazias (string vazia, NULL ou string 'null')
	//				sequenceName: nome da seq��ncia ou ID generator para a chave prim�ria<br>
	// @return		mixed Se o banco suportar, retorna o �ltimo ID inserido. Do
	//				contr�rio, retorna um valor booleano representando o sucesso
	//				ou a falha da opera��o de inser��o
	// @see			Db::update
	// @see			Db::delete
	// @access		public
	//!-----------------------------------------------------------------
	function insert($table, $arrData, $options=array()) {
		if (empty($table))
			return FALSE;
		if (TypeUtils::isHashArray($arrData)) {
			// defini��o do forceType
			if (isset($options['forceType']))
				$this->setForceType($options['forceType']);
			// defini��o da chave prim�ria a partir do nome de seq��ncia fornecido
			if (isset($options['sequenceName'])) {
				$pk = $this->AdoDb->MetaPrimaryKeys($table);
				if ($pk && sizeof($pk) == 1) {
					$insertId = $this->AdoDb->GenID($options['sequenceName']);
					$arrData[$pk[0]] = $insertId;
				}
			}
			$insertSQL = $this->AdoDb->GetInsertSQL($table, $arrData);
			if (!empty($insertSQL)) {
				$this->lastStatement = array(
					'source' => 'insert',
					'statement' => $insertSQL,
					'vars' => array()
				);
        		$result = $this->AdoDb->Execute($insertSQL);
				if ($result) {
					// apenas para manter populada esta propriedade
					$this->affectedRows = $this->AdoDb->Affected_Rows();
					if (!isset($insertId)) {
						// retorna o �ltimo ID inserido apenas se ele for n�o-zero
						$insertId = $this->lastInsertId();
					}
					return ($insertId ? $insertId : TRUE);
				} else {
					$this->affectedRows = 0;
					return FALSE;
				}
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Db::update
	// @desc		Constr�i e executa um comando DML 'UPDATE'
	// @param		table string		Nome da tabela ou view
	// @param		arrData array		Array associativo de valores a serem alterados
	// @param		clause string		Cl�usula de condi��o
	// @param		force bool			"FALSE" For�ar a execu��o do update mesmo quando n�o existirem campos a serem atualizados
	// @param		options array		"array()" Op��es de atualiza��o
	// @note		Se n�o for informada uma cl�usula de condi��o para o
	//				comando UPDATE, este m�todo retornar� FALSE
	// @see			Db::insert
	// @see			Db::delete
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function update($table, $arrData, $clause, $force=FALSE, $options=array()) {
		if (empty($table) || empty($clause))
			return FALSE;
		$rs =& $this->AdoDb->Execute(sprintf("SELECT * FROM %s WHERE %s", $table, $clause));
		if ($rs && TypeUtils::isHashArray($arrData)) {
			// force type
			if (isset($options['forceType']))
				$this->setForceType($options['forceType']);
			$updateSQL = $this->AdoDb->GetUpdateSQL($rs, $arrData, $force);
			if (!empty($updateSQL)) {
				$this->lastStatement = array(
					'source' => 'update',
					'statement' => $updateSQL,
					'vars' => array()
				);
				$result = $this->AdoDb->Execute($updateSQL);
				$this->affectedRows = $this->AdoDb->Affected_Rows();
				return ($result ? TRUE : FALSE);
			}
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Db::updateLob
	// @desc		Armazena valores em campos do tipo CLOB e BLOB
	// @param		table string	Nome da tabela
	// @param		column string	Nome da coluna
	// @param		value mixed		Valor do LOB ou arquivo contendo os dados
	// @param		clause string	Cl�usula de condi��o (para o INSERT ou para o UPDATE j� realizado)
	// @param		blobType string	"BLOB" Tipo do LOB (BLOB ou CLOB)
	// @param		valueType int	"T_BYVAR" T_BYVAR: string, T_BYFILE: arquivo
	// @note		Em bancos de dados onde valores do tipo LOB n�o podem ser diretamente
	//				populados em cl�usulas INSERT e UPDATE, deve ser utilizado o m�todo
	//				updateLob, logo ap�s a execu��o da instru��o INSERT ou UPDATE
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function updateLob($table, $column, $value, $clause, $lobType='BLOB', $valueType=T_BYVAR) {
		$lobType = strtoupper($lobType);
		if ($valueType == T_BYVAR)
			return $this->AdoDb->UpdateBlob($table, $column, $value, $clause, $lobType);
		return $this->AdoDb->UpdateBlobFile($table, $column, $value, $clause, $lobType);
	}

	//!-----------------------------------------------------------------
	// @function	Db::replace
	// @desc		Busca por registros na tabela $table que satisfa�am
	//				o(s) valor(s) da(s) chave(s) indicadas em $keyFields
	//				cujos valores devem estar em $arrFields. Atualiza
	//				o(s) registro(s) se forem encontrados ou insere um
	//				registro novo em caso contr�rio
	// @param		table string		Nome da tabela a ser atualizada/incrementada
	// @param		arrFields array		Array associativo de valores para o novo registro ou atualiza��o dos registros existentes
	// @param		keyFields mixed		Chave simples em um string ou chave composta em um array
	// @param		quoteVals bool		"FALSE" Quotar os valores n�o num�ricos automaticamente nos comandos DML executados
	// @return		int 0 em caso de falha, 1 se a atualiza��o foi efetuada e 2 se a inser��o foi efetuada
	// @access		public
	//!-----------------------------------------------------------------
	function replace($table, $arrFields, $keyFields, $quoteVals=FALSE) {
		return $this->AdoDb->Replace($table, $arrFields, $keyFields, $quoteVals);
	}

	//!-----------------------------------------------------------------
	// @function	Db::delete
	// @desc		Constr�i e executa um comando DML 'DELETE'
	// @param		table string		Nome da tabela ou view
	// @param		clause string		Cl�usula de condi��o
	// @note		Se n�o for informada uma cl�usula de condi��o para o
	//				comando DELETE, este m�todo retornar� FALSE
	// @see			Db::insert
	// @see			Db::update
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function delete($table, $clause, $bindVars=FALSE) {
		if (empty($table) || empty($clause))
			return FALSE;
		$sqlCode = sprintf("DELETE FROM %s WHERE %s", $table, $clause);
		$this->lastStatement = array(
			'source' => 'delete',
			'statement' => $sqlCode,
			'vars' => ($bindVars ? $bindVars : array())
		);
		$result = $this->AdoDb->Execute($sqlCode, $bindVars);
		$this->affectedRows = $this->AdoDb->Affected_Rows();
		return ($result ? TRUE : FALSE);
	}

	//!-----------------------------------------------------------------
	// @function	Db::checkIntegrity
	// @desc		Verifica a integridade referencial de uma tabela
	//				em uma determinada coluna para as refer�ncias do
	//				par�metro 'reference'
	// @param		table string		Tabela a ser testada
	// @param		column string		Coluna da tabela acima
	// @param		value mixed		Valor de 'column' sendo testado
	// @param		reference mixed	Vetor 'tabela'=>'coluna' ou tabela simples a ser testada a integridade
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function checkIntegrity($table, $column, $value, $reference) {
		$ok = TRUE;
		if (TypeUtils::isArray($reference)) {
			foreach($reference as $tb => $col) {
				$fields = "{$table}.{$column}";
				$tables = "{$table},{$tb}";
				$clause = "{$table}.{$column} = {$value} AND {$table}.{$column} = {$tb}.{$col}";
				$sqlCode = "SELECT {$fields} FROM {$tables} WHERE {$clause}";
				$this->lastStatement = array(
					'source' => 'checkIntegrity',
					'statement' => $sqlCode,
					'vars' => array()
				);
				$rs =& $this->query($sqlCode);
				if (!$rs || $rs->RecordCount() == 0) {
					$ok = FALSE;
					break;
				}
			}
		} else {
			$fields = "{$table}.{$column}";
			$tables = "{$table},{$reference}";
			$clause = "{$table}.{$column} = {$value} AND {$table}.{$column} = {$reference}.{$column}";
			$sqlCode = "SELECT {$fields} FROM {$tables} WHERE {$clause}";
			$this->lastStatement = array(
				'source' => 'checkIntegrity',
				'statement' => $sqlCode,
				'vars' => array()
			);
			$rs =& $this->query($sqlCode);
			$ok = ($rs && $rs->RecordCount() > 0);
		}
		return $ok;
     }

	//!-----------------------------------------------------------------
	// @function	Db::toGlobals
	// @desc		Publica como vari�veis globais os valores das
	//				colunas da primeira linha do resultado de 'sqlCode'
	// @param		sqlCode string			Consulta SQL para publica��o das vari�veis
	// @param		bindVars array			"FALSE" Vari�veis de amarra��o para a consulta
	// @param		ignoreEmptyResults bool	"TRUE" N�o gerar erro para uma consulta SQL que n�o retorna resultados
	// @note		A principal utilidade deste m�todo � publicar no escopo global os
	//				dados de um registro para que eles possam ser carregados para um
	//				formul�rio de edi��o
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function toGlobals($sqlCode, $bindVars=FALSE, $ignoreEmptyResults=FALSE) {
		// testa a natureza do comando passado por par�metro
		if (!$this->isDbQuery($sqlCode) || $this->isDbDesign($sqlCode)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TOGLOBALS_WRONG_USAGE'), E_USER_WARNING, __FILE__, __LINE__);
			return FALSE;
		}
		$oldFetchMode = $this->AdoDb->fetchMode;
		$this->setFetchMode(ADODB_FETCH_ASSOC);
		$this->lastStatement = array(
			'source' => 'toGlobals',
			'statement' => $sqlCode,
			'vars' => $bindVars
		);
		$rs =& $this->AdoDb->Execute($sqlCode, $bindVars);
		$this->setFetchMode($oldFetchMode);
		if ($rs->RecordCount() > 0) {
			foreach ($rs->fields as $key => $value) {
				Registry::set($key, $value);
			}
			return TRUE;
		} else if (!$ignoreEmptyResults) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_TOGLOBALS_QUERY', $sqlCode), E_USER_NOTICE, __FILE__, __LINE__);
			return FALSE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Db::&emptyRecordSet
	// @desc		Retorna um result set vazio em consultas e opera��es
	//				que retornam erros ou n�o retornam resultados no BD
	// @return		ADORecordSet_empty object
	// @note		Ao executar uma query ou uma opera��o no BD, tanto o
	//				teste $Db->affectedRows() quanto o $Rs->RecordCount()
	//				dever�o retornar zero. Por�m, poss�veis erros poder�o
	//				ser encontrados em $Db->getError()
	// @access		public
	//!-----------------------------------------------------------------
	function &emptyRecordSet() {
		$Rs = new ADORecordSet_empty();
		return $Rs;
	}

	//!-----------------------------------------------------------------
	// @function	Db::isDbDesign
	// @desc		Verifica se uma query possui palavras reservadas
	//				indicativas de um comando DML ou DDL
	// @param		sql string	C�digo SQL
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isDbDesign($sql) {
		if (TypeUtils::isArray($sql))
			$sql = $sql[0];
		$resWords = 'INSERT|UPDATE|DELETE|' . 'REPLACE|CREATE|DROP|' .
					'ALTER|GRANT|REVOKE|' . 'LOCK|UNLOCK';
		if (preg_match('/^\s*"?(' . $resWords . ')\s+/i', $sql)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::isDbQuery
	// @desc		Verifica se uma query possui a palavra SELECT,
	//				indicativa de um comando DQL
	// @param		sql string	C�digo SQL
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isDbQuery($sql) {
		if (TypeUtils::isArray($sql))
			$sql = $sql[0];
		$resWord = 'SELECT';
		if (preg_match('/^\s*"?(' . $resWord . ')\s+/i', $sql)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::close
	// @desc		Fecha a conex�o atual com o banco de dados
	// @return		bool Indica o status da opera��o realizada
	// @access		public
	//!-----------------------------------------------------------------
	function close() {
		if (isset($this->AdoDb->_connectionID) && TypeUtils::isResource($this->AdoDb->_connectionID)) {
			$this->onBeforeClose();
			$this->connected = $this->AdoDb->Close();
		} else {
			$this->connected = FALSE;
		}
		return ($this->connected === FALSE);
	}

	//!-----------------------------------------------------------------
	// @function	Db::onAfterConnect
	// @desc		M�todo abstrato que pode ser implementado em uma classe
	//				extendida para executar comandos no momento em que a
	//				conex�o ao banco de dados � estabelecida
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onAfterConnect() {
	}

	//!-----------------------------------------------------------------
	// @function	Db::onBeforeClose
	// @desc		M�todo abstrato que pode ser implementado em uma classe
	//				extendida para executar comandos antes que a conex�o
	//				ao banco de dados seja encerrada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onBeforeClose() {
	}
}
?>