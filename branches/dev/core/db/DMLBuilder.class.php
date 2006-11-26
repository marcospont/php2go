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
// $Header: /www/cvsroot/php2go/core/db/DMLBuilder.class.php,v 1.8 2006/05/07 15:21:50 mpont Exp $
// $Date: 2006/05/07 15:21:50 $

// @const DML_BUILDER_INSERT "1"
// Modo de execu��o para montagem de instru��es DML INSERT
define('DML_BUILDER_INSERT', 1);
// @const DML_BUILDER_UPDATE "2"
// Modo de execu��o para montagem de instru��es DML UPDATE
define('DML_BUILDER_UPDATE', 2);
// @const DML_BUILDER_INSERTSQL "INSERT INTO %s (%s) VALUES (%s)"
// Base para montagem de uma instru��o INSERT
define('DML_BUILDER_INSERTSQL', "INSERT INTO %s (%s) VALUES (%s)");
// @const DML_BUILDER_UPDATESQL "UPDATE %s SET %s WHERE %s"
// Base para montagem de uma instru��o UPDATE
define('DML_BUILDER_UPDATESQL', "UPDATE %s SET %s WHERE %s");
// @const OCI_EMPTY_CLOB "EMPTY_CLOB()"
// Valor especial para colunas Oracle CLOB
define('OCI_EMPTY_CLOB', 'EMPTY_CLOB()');
// @const OCI_EMPTY_BLOB "EMPTY_BLOB()"
// Valor especial para colunas Oracle BLOB
define('OCI_EMPTY_BLOB', 'EMPTY_BLOB()');

//!-----------------------------------------------------------------
// @class		DMLBuilder
// @desc		Constr�i instru��es DML INSERT e UPDATE, a partir do
//				nome da tabela e de um conjunto de valores. Permite
//				montagem de comandos com e sem vari�veis de amarra��o,
//				com tratamento de valores vazios e convers�o de valores
//				para os formatos corretos de acordo com o banco de dados
// @package		php2go.db
// @extends		PHP2Go
// @version		$Revision: 1.8 $
// @author		Marcos Pont
//!-----------------------------------------------------------------
class DMLBuilder extends PHP2Go
{
	var $ignoreEmptyValues = FALSE;		// @var ignoreEmptyValues bool	"FALSE" Incluir ou n�o no comando SQL valores vazios ou nulos
	var $forceUpdate = FALSE;			// @var forceUpdate bool		"FALSE" For�ar a execu��o de um UPDATE mesmo que o valor da coluna n�o tenha sido alterado
	var $useBind = FALSE;				// @var useBind bool			"FALSE" Utilizar vari�veis de amarra��o (bind)
	var $_mode;							// @var _mode int				Modo ativo
	var $_table;						// @var _table string			Tabela ativa
	var $_values = array();				// @var _values array			"array()" Valores para a instru��o
	var $_clause;						// @var _clause string			Cl�usula de condi��o, para instru��es UPDATE
	var $_clauseBindVars = array();		// @var _clauseBindVars array	"array()" Vari�veis de amarra��o para a cl�usula da instru��o UPDATE
	var $_bindVars = array();			// @var _bindVars array			"array()" Conjunto de vari�veis de amarra��o calculadas para uma determinada instru��o
	var $_updateVars = array();			// @var _updateVars array		"array()" Armazena a lista de valores alterados em uma instru��o UPDATE, contendo valor novo e valor velho
	var $_Db = NULL;					// @var _Db Db object			"NULL" Armazena a conex�o ao banco de dados

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::DMLBuilder
	// @desc		Construtor da classe
	// @param		&Db Db object	Conex�o com o banco de dados
	// @access		public
	//!-----------------------------------------------------------------
	function DMLBuilder(&$Db) {
		parent::PHP2Go();
		if (!TypeUtils::isInstanceOf($Db, 'Db'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Db'), E_USER_ERROR, __FILE__, __LINE__);
		$this->_Db =& $Db;
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::prepare
	// @desc		Prepara uma nova instru��o
	// @param		mode int				Modo (DML_BUILDER_INSERT ou DML_BUILDER_UPDATE)
	// @param		table string			Nome da tabela
	// @param		values array			Conjunto de valores
	// @param		clause string			"NULL" Cl�usula de condi��o
	// @param		clauseBindVars array	"array()" Vari�veis bind da cl�usula de condi��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function prepare($mode=DML_BUILDER_INSERT, $table, $values, $clause=NULL, $clauseBindVars=array()) {
		if ($mode != DML_BUILDER_INSERT && $mode != DML_BUILDER_UPDATE)
			$mode = DML_BUILDER_INSERT;
		$this->_mode = $mode;
		$this->_table = $table;
		$this->_values = TypeUtils::toArray($values);
		if (!empty($clause)) {
			$this->_clause = $clause;
			$this->_clauseBindVars = TypeUtils::toArray($clauseBindVars);
		} else {
			unset($this->_clause);
			$this->_clauseBindVars = array();
		}
		$this->_bindVars = array();
		$this->_updateVars = array();
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::getSql
	// @desc		Monta e retorna o c�digo SQL da instru��o
	// @return		mixed C�digo SQL da instru��o ou FALSE em caso de erros
	// @access		public
	//!-----------------------------------------------------------------
	function getSql() {
		if ($this->_mode == DML_BUILDER_INSERT) {
			if (!empty($this->_table) && !empty($this->_values)) {
				return $this->_insertSql();
			}
		} elseif ($this->_mode == DML_BUILDER_UPDATE) {
			if (!empty($this->_table) && !empty($this->_values) && !empty($this->_clause)) {
				return $this->_updateSql();
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::getBindVars
	// @desc		Retorna o conjunto de vari�veis de amarra��o calculadas
	//				para a instru��o INSERT ou UPDATE
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getBindVars() {
		return array_merge($this->_bindVars, $this->_clauseBindVars);
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::getPreparedStatement
	// @desc		Monta o c�digo SQL da instru��o e prepara para execu��o
	//				utilizando a conex�o ao banco de dados
	// @return		mixed Statement preparado ou NULL em caso de erros
	// @access		public
	//!-----------------------------------------------------------------
	function getPreparedStatement() {
		$sql = $this->getSql();
		if (!empty($sql))
			return $this->_Db->prepare($this->getSql());
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::getUpdateVars
	// @desc		Retorna o conjunto de colunas cujos valores foram alterados
	//				em rela��o aos atuais, em uma instru��o UPDATE. Cada posi��o
	//				do array retornado cont�m outro array com duas chaves: old,
	//				contendo o valor antigo da coluna, e new, contendo o novo valor
	// @note		Quando o modo ativo na classe for DML_BUILDER_INSERT, este m�todo
	//				retornar� um array vazio
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getUpdateVars() {
		return $this->_updateVars;
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::execute
	// @desc		Monta o c�digo SQL da instru��o, prepara e executa no banco de dados
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function execute() {
		$sql = $this->getSql();
		if (!empty($sql)) {
			$stmt = $this->_Db->prepare($sql);
			if ($stmt)
				return $this->_Db->execute($stmt, $this->getBindVars());
		} elseif ($sql === '' && $this->_mode == DML_BUILDER_UPDATE) {
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::_insertSql
	// @desc		M�todo interno respons�vel pela constru��o de instru��es INSERT
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _insertSql() {
		$sqlFields = '';
		$sqlValues = '';
		$rs =& $this->_getRecordSet();
		$dbCols = $this->_getColumnsList();
		$values = array_change_key_case($this->_values, CASE_UPPER);
		$isOci = ($this->_Db->AdoDb->dataProvider == 'oci8');
		foreach ($dbCols as $dbCol) {
			$colUpper = strtoupper($dbCol->name);
			if (array_key_exists($colUpper, $values)) {
				$colQuote = (strpos($colUpper, ' ') !== FALSE ? $this->_Db->AdoDb->nameQuote . $colUpper . $this->_Db->AdoDb->nameQuote : $colUpper);
				$colType = $rs->MetaType($dbCol->type);
				// valores vazios
				if ($this->_isEmpty($values[$colUpper])) {
					if ($this->ignoreEmptyValues) {
						continue;
					} else {
						$values[$colUpper] = NULL;
					}
				}
				// uso de vari�veis bind
				if ($this->useBind) {
					// tratamento especial para fun��es EMPTY_BLOB() e EMPTY_CLOB() no oracle
					if ($isOci) {
						if (($dbCol->type == 'CLOB' && $values[$colUpper] == OCI_EMPTY_CLOB) || ($dbCol->type == 'BLOB' && $values[$colUpper] == OCI_EMPTY_BLOB)) {
							$sqlValues .= $values[$colUpper] . ', ';
						} else {
							$this->_bindVars[$colUpper] = $values[$colUpper];
							$sqlValues .= ':' . $dbCol->name . ', ';
						}
					} else {
						$this->_bindVars[] = $values[$colUpper];
						$sqlValues .= '?, ';
					}
				} else {
					if ($values[$colUpper] === NULL)
						$sqlValues .= 'null, ';
					else
						$sqlValues .= $this->_getColumnSql($values[$colUpper], $colType, $dbCol->type, $colQuote);
				}
				$sqlFields .= $colQuote . ', ';
			}
		}
		if (!empty($sqlFields) && !empty($sqlValues)) {
			$sqlFields = substr($sqlFields, 0, -2);
			$sqlValues = substr($sqlValues, 0, -2);
			return sprintf(DML_BUILDER_INSERTSQL, $this->_table, $sqlFields, $sqlValues);
		}
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::_updateSql
	// @desc		M�todo interno respons�vel pela constru��o de instru��es UPDATE
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _updateSql() {
		$setValues = '';
		$oldMode = $this->_Db->setFetchMode(ADODB_FETCH_ASSOC);
		$rs =& $this->_Db->query(sprintf("SELECT * FROM %s WHERE %s", $this->_table, $this->_clause), TRUE, $this->_clauseBindVars);
		$this->_Db->setFetchMode($oldMode);
		$values = array_change_key_case($this->_values, CASE_UPPER);
		$isOci = ($this->_Db->AdoDb->dataProvider == 'oci8');
		for ($i=0,$s=$rs->FieldCount(); $i<$s; $i++) {
			$dbCol = $rs->FetchField($i);
			$colUpper = strtoupper($dbCol->name);
			if (array_key_exists($colUpper, $values)) {
				// formata o nome do campo, se necess�rio, e busca o metatype
				$colQuote = (strpos($colUpper, ' ') !== FALSE ? $this->_Db->AdoDb->nameQuote . $colUpper . $this->_Db->AdoDb->nameQuote : $colUpper);
				$colType = $rs->MetaType($dbCol->type);
				if ($colType == 'null')
					$colType = 'C';
				// busca do valor atual da coluna
				// os testes por nome s�o necess�rios porque os bancos t�m diferentes padr�es nos nomes das colunas nos record sets
				if (isset($rs->fields[$colUpper]))
					$curVal = $rs->fields[$colUpper];
				elseif (isset($rs->fields[$dbCol->name]))
					$curVal = $rs->fields[$dbCol->name];
				elseif (isset($rs->fields[strtolower($colUpper)]))
					$curVal = $rs->fields[strtolower($colUpper)];
				else
					$curVal = '';
				// defini��o do novo valor, para compara��o
				if ($this->forceUpdate || strcmp($curVal, $values[$colUpper])) {
					// valores vazios
					if ($this->_isEmpty($values[$colUpper])) {
						if (empty($curVal) && $this->ignoreEmptyValues) {
							continue;
						} else {
							$values[$colUpper] = NULL;
						}
					}
					// registra a coluna na lista de valores alterados
					$this->_updateVars[$dbCol->name] = array(
						'old' => $curVal,
						'new' => ($values[$colUpper] === NULL ? 'null' : (string)$values[$colUpper])
					);
					// uso de vari�veis bind
					if ($this->useBind) {
						if ($isOci) {
							// tratamento especial para fun��es EMPTY_BLOB() e EMPTY_CLOB() no oracle
							if (($dbCol->type == 'CLOB' && $values[$colUpper] == OCI_EMPTY_CLOB) || ($dbCol->type == 'BLOB' && $values[$colUpper] == OCI_EMPTY_BLOB)) {
								$setValues .= $colQuote . ' = ' . $values[$colUpper] . ', ';
							} else {
								$this->_bindVars[$colUpper] = $values[$colUpper];
								$setValues .= $colQuote . ' = :' . $dbCol->name . ', ';
							}
						} else {
							$this->_bindVars[] = $values[$colUpper];
							$setValues .= $colQuote . ' = ?, ';
						}
					} else {
						if ($values[$colUpper] === NULL)
							$setValues .= $colQuote . ' = null, ';
						else
							$setValues .= $this->_getColumnSql($values[$colUpper], $colType, $dbCol->type, $colQuote);
					}
				}
			}
		}
		if (!empty($setValues)) {
			$setValues = substr($setValues, 0, -2);
			return sprintf(DML_BUILDER_UPDATESQL, $this->_table, $setValues, $this->_clause);
		}
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::&_getRecordSet
	// @desc		Constr�i um recordset do mesmo tipo da conex�o ao banco de dados
	//				a fim de extrair meta dados das colunas da tabela
	// @return		ADORecordSet object
	// @access		private
	//!-----------------------------------------------------------------
	function &_getRecordSet() {
		static $rsObj;
		if (!isset($rsObj)) {
			$rsClass = $this->_Db->AdoDb->rsPrefix . $this->_Db->AdoDb->databaseType;
			$rsObj = new $rsClass(-1, $this->_Db->AdoDb->fetchMode);
			$rsObj->connection =& $this->_Db->AdoDb;
		}
		return $rsObj;
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::_getColumnsList
	// @desc		Monta a lista de colunas da tabela. Mant�m a �ltima tabela
	//				e a �ltima lista de colunas em cache, para aumento de performance
	//				na execu��o de m�ltiplas instru��es
	// @access		private
	// @return		array Lista de colunas da tabela alvo
	//!-----------------------------------------------------------------
	function _getColumnsList() {
		static $cacheTable;
		static $cacheColumns;
		if (isset($cacheTable) && $cacheTable == $this->_table) {
			$columns =& $cacheColumns;
			return $columns;
		} else {
			$columns = $this->_Db->getColumns($this->_table);
			$cacheTable = $this->_table;
			$cacheColumns = $columns;
			return $columns;
		}
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::_getColumnSql
	// @desc		Monta o c�digo SQL de inser��o ou atualiza��o de uma coluna
	// @param		value string		Valor para a coluna
	// @param		metaType string		Meta type
	// @param		type string			Tipo original (dependente do banco de dados)
	// @param		nameQuote string	Nome do campo (somente utilizando em instru��es UPDATE
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _getColumnSql($value, $metaType, $type, $nameQuote) {
		if ($this->_Db->AdoDb->dataProvider == 'postgres' && $metaType == 'L')
			$metaType = 'C';
		switch ($metaType) {
			case 'C' :
				$sqlValue = $this->_Db->quoteString($value) . ', ';
				break;
			case 'X' :
				// tratamento especial para CLOB no oracle, com o valor EMPTY_CLOB()
				if ($this->_Db->AdoDb->dataProvider == 'oci8' && $type == 'CLOB')
					$sqlValue = ($value == OCI_EMPTY_CLOB ? $value : $this->_Db->quoteString($value)) . ', ';
				else
					$sqlValue = $this->_Db->quoteString($value) . ', ';
				break;
			case 'B' :
				// tratamento especial para CLOB no oracle, com o valor EMPTY_BLOB()
				if ($this->_Db->AdoDb->dataProvider == 'oci8' && $type == 'BLOB')
					$sqlValue = ($value == OCI_EMPTY_BLOB ? $value : $this->_Db->quoteString($value)) . ', ';
				else
					$sqlValue = $this->_Db->quoteString($value) . ', ';
				break;
			case 'D' :
				$sqlValue = $this->_Db->date($value) . ', ';
				break;
			case 'T' :
				$sqlValue = $this->_Db->date($value, TRUE) . ', ';
				break;
			default :
				if ($metaType == 'I' || $metaType == 'N')
					$value = str_replace(',', '.', $value);
				if (empty($value))
					$sqlValue = '0, ';
				else
					$sqlValue = $value . ', ';
				break;
		}
		if ($this->_mode == DML_BUILDER_INSERT)
			return $sqlValue;
		else
			return "{$nameQuote} = {$sqlValue}";
	}

	//!-----------------------------------------------------------------
	// @function	DMLBuilder::_isEmpty
	// @desc		Verifica se um determinado valor inclu�do no conjunto de
	//				valores da instru��o � vazio: vazio, NULL ou 'null'
	// @param		value mixed		Valor a ser testado
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _isEmpty($value) {
		return (is_null($value) || (empty($value) && strlen($value) == 0) || $value === 'null');
	}
}