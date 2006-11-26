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
// $Header: /www/cvsroot/php2go/core/db/QueryBuilder.class.php,v 1.19 2006/09/21 00:32:59 mpont Exp $
// $Date: 2006/09/21 00:32:59 $

//------------------------------------------------------------------
import('php2go.data.DataSet');
import('php2go.util.Statement');
import('php2go.xml.XmlDocument');
//------------------------------------------------------------------

// @const QUERY_BUILDER_AND 	"AND"
// Constante referente ao operador AND
define('QUERY_BUILDER_AND', 'AND');
// @const QUERY_BUILDER_OR		"OR"
// Constante referente ao operador OR
define('QUERY_BUILDER_OR', 'OR');
// @const QUERY_BUILDER_OP_NONE	"0"
// Define que a cláusula não deve ser parentizada com outro operador
define('QUERY_BUILDER_OP_NONE', 0);
// @const QUERY_BUILDER_OP_LAST "1"
// Operar com o último operando
define('QUERY_BUILDER_OP_LAST', 1);
// @const QUERY_BUILDER_OP_ALL "2"
// Operar com todos os operandos
define('QUERY_BUILDER_OP_ALL', 2);

//!-----------------------------------------------------------------
// @class 		QueryBuilder
// @desc 		Constrói consultas SQL (apenas DQL) a partir de porções
// 				do código, permitindo a combinação dos elementos e a manipulação
// 				dos mesmos independentemente
// @package		php2go.db
// @extends 	PHP2Go
// @author 		Marcos Pont
// @version		$Revision: 1.19 $
//!-----------------------------------------------------------------
class QueryBuilder extends PHP2Go
{
	var $distinct;				// @var distinct bool		Indica se apenas linhas distintas devem ser buscadas na consulta
	var $fields;				// @var fields string		Campos envolvidos na consulta
	var $tables;				// @var tables string		Tabelas e suas junções envolvidas na consulta
	var $clause;				// @var clause string		Cláusula de condição da consulta
	var $groupby;				// @var groupby string		Agrupamento para a consulta
	var $condition;				// @var condition string	Condição da cláusula de agrupamento
	var $orderby;				// @var orderby string		Ordenação da consulta
	var $limit = array();		// @var limit array			"array()" Informações de limite (offset, lower bound)
	var $queryCode = '';		// @var queryCode string	"" Código final montado para a query
	var $upCaseWords = TRUE;	// @var upCaseWords bool	"TRUE" Utilizar maiúsculas nas palavras reservadas da query

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::QueryBuilder
	// @desc 		Inicializa o construtor de queries de banco de dados
	// @param 		fields string		"" Campos
	// @param 		tables string		"" Tabelas
	// @param 		clause string		"" Cláusula de Condição
	// @param 		groupby string		"" Agrupamento
	// @param 		orderby string		"" Ordenação
	// @note 		Os elementos da consulta devem ser fornecidos sem as
	// 				palavras reservadas SELECT, FROM, WHERE, ...
	// @access 		public
	//!-----------------------------------------------------------------
	function QueryBuilder($fields='', $tables='', $clause='', $groupby='', $orderby='') {
		parent::PHP2Go();
		$this->distinct = FALSE;
		$this->fields = $fields;
		$this->tables = $tables;
		$this->clause = $clause;
		$this->groupby = $groupby;
		$this->condition = '';
		$this->orderby = $orderby;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::setDistinct
	// @desc		Define valor para o flag distinct
	// @param		setting bool	"TRUE" Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setDistinct($setting=TRUE) {
		$this->distinct = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::addFields
	// @desc		Adiciona um ou mais campos na consulta SQL em construção
	// @param		fields string	Campos a serem inseridos, um ou mais, separados por vírgula
	// @note		Se a propriedade fields estiver vazia, o valor fornecido
	//				como parâmetro para este método será atribuído e não concatenado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addFields($fields) {
    	if (empty($this->fields))
        	$this->fields = $fields;
		else
        	$this->fields .= ', ' . $fields;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::setFields
	// @desc		Configura a propriedade fields da classe
	// @param		fields string	"*" Valor para a propriedade fields
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function setFields($fields='*') {
		$oldValue = $this->fields;
		$this->fields = $fields;
		return $oldValue;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::addTable
	// @desc		Adiciona uma tabela à lista de tabelas envolvidas na consulta
	// @param		tableName string	Nome da tabela
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addTable($tableName) {
		if (empty($this->tables))
			$this->tables = $tableName;
		else
			$this->tables .= ', ' . $tableName;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::joinTable
	// @desc		Adiciona uma tabela à consulta SQL utilizando uma operação de junção
	// @param		tableName string		Nome da tabela
	// @param		joinType string			Operação de junção (INNER JOIN, LEFT OUTER JOIN)
	// @param		joinCondition string	Condição da operação de junção
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function joinTable($tableName, $joinType, $joinCondition) {
		if (!empty($this->tables)) {
			$this->tables .= " $joinType $tableName ON ($joinCondition)";
		}
	}

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::addClause
	// @desc 		Inclui um valor à cláusula de condição da consulta
	// @param 		clause string	Valor a ser adicionado à cláusula de condição, sem a palavra reservada 'WHERE'
	// @param 		and bool		"TRUE" Utilizar ou não o operador AND. O valor FALSE indica o uso do operador OR
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function addClause($clause, $operator = QUERY_BUILDER_AND, $action = QUERY_BUILDER_OP_NONE) {
		if (empty($clause)) {
			return FALSE;
		} elseif (empty($this->clause)) {
			$this->clause = $clause;
			return TRUE;
		} else if (!in_array($operator, array(QUERY_BUILDER_AND, QUERY_BUILDER_OR))) {
			return FALSE;
		} else {
			switch ($action) {
				case QUERY_BUILDER_NONE :
					$this->clause .= ' ' . $operator . ' ' . $clause;
					break;
				case QUERY_BUILDER_OP_LAST :
					$v = preg_split('/and|or/i', $this->clause, -1);
					if (sizeof($v) == 1) {
						$this->clause = '( ' . $this->clause . ' ' . $operator . ' ' . $clause . ' )';
					} else {
						$last = $v[sizeof($v)-1];
						if (preg_match("/([^\)]+)(\)[ ]?)+/i", $last, $matches)) {
							$this->clause = eregi_replace("$matches[1]", " (\\0$operator $clause ) ", $this->clause);
						} else {
							$this->clause = eregi_replace("$last", " (\\0 $operator $clause )", $this->clause);
						}
					}
					break;
				case QUERY_BUILDER_OP_ALL :
					$this->clause = '(' . $this->clause . ') ' . $operator . ' ' . $clause;
					break;
				default :
					$this->clause .= ' ' . $operator . ' ' . $clause;
			}
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::setClause
	// @desc		Configura o valor da cláusula de condição da query na classe
	// @param		clause string	"" Valor para a cláusula
	// @return		string Valor antigo da propriedade
	// @access		public
	//!-----------------------------------------------------------------
	function setClause($clause='') {
		$oldValue = $this->clause;
		$this->clause = $clause;
		return $oldValue;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::clearClause
	// @desc		Reseta a cláusula de condição da consulta SQL
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clearClause() {
		$this->clause = '';
	}

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::setGroup
	// @desc 		Configura o agrupamento da consulta
	// @param 		groupby string		"" Trecho de agrupamento da consulta
	// @param		condition string	"" Condição HAVING para a cláusula groupby
	// @note		Utilize QueryBuilder::setGroup() para resetar o valor da propriedade groupby na classe
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setGroup($groupby='', $condition='') {
		$this->groupby = $groupby;
		if (trim($groupby) == '')
			$this->condition = '';
		else if (trim($condition) != '')
			$this->condition = $condition;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::prefixOrder
	// @desc		Prefixa a propriedade 'orderby' da consulta com o valor
	//				passado no parâmetro $orderby
	// @param		orderby string	Campo(s) prioritário(s) de ordenação para a consulta
	// @note		Se a consulta não possuir ordenação, ela será criada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function prefixOrder($orderby) {
    	if (empty($this->orderby))
        	$this->setOrder($orderby);
		else
        	$this->orderby = $orderby . ' , ' . $this->orderby;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::addOrder
	// @desc		Concatena um novo valor à cláusula de ordenação da consulta
	// @param		orderby string	Nova cláusula de ordenação
	// @note		Se a consulta não possui ordenação, ela será criada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addOrder($orderby) {
		if (empty($this->orderby))
			$this->setOrder($orderby);
		else
			$this->orderby = $this->orderby . ' , ' . $orderby;
	}

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::setOrder
	// @desc 		Configura a ordenação da consulta
	// @param 		orderby string	Trecho de ordenação de uma consulta, sem a palavra reservada 'ORDER BY'
	// @note		Utilize QueryBuilder::setOrder() para resetar o valor da propriedade orderby na classe
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setOrder($orderby='') {
		$this->orderby = $orderby;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::setLimit
	// @desc		Define um limite de linhas para a execução da consulta
	// @param		rows int		Número de registros
	// @param		offset int		"NULL" Deslocamento inicial
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLimit($rows, $offset=NULL) {
		$this->limit['rows'] = (int)$rows;
		$this->limit['offset'] = (int)$offset;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::loadFromXml
	// @desc		Carrega os parâmetros da query a partir de um arquivo
	//				XML que contenha um nodo "DATASOURCE" que seja descendente
	//				imediato do nodo raiz
	// @note		Arquivos XML utilizados na classes php2go.data.Report
	//				podem ser utilizados neste método
	// @param		xmlFile string	Caminho do arquivo XML
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadFromXml($xmlFile) {
		$Doc = new XmlDocument();
		$Doc->parseXml($xmlFile);
		$Root =& $Doc->getRoot();
		$children = $Root->getChildrenTagsArray();
		if (TypeUtils::isInstanceOf(@$children['DATASOURCE'], 'XmlNode')) {
			$dataSource =& $children['DATASOURCE'];
			$children = $dataSource->getChildrenTagsArray();
			$candidates = array('FIELDS', 'TABLES', 'CLAUSE', 'GROUPBY', 'ORDERBY');
			foreach ($candidates as $candidate) {
				if ($children[$candidate]) {
					$propName = strtolower($candidate);
					$value = $children[$candidate]->value;
					if (!empty($value)) {
						if (preg_match("/~[^~]+~/", $value))
							$value = Statement::evaluate($value);
						$this->{$propName} = $value;
					}
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::reset
	// @desc		Reseta todos os parâmetros da consulta SQL
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function reset() {
		$this->distinct = FALSE;
		$this->fields = '';
		$this->tables = '';
		$this->clause = '';
		$this->groupby = '';
		$this->condition = '';
		$this->orderby = '';
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::displayQuery
	// @desc		Exibe o código da query
	// @param		preFormatted bool		"TRUE" Manter a formatação existente no código SQL
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function displayQuery($preFormatted=TRUE) {
		$this->_buildQuery(TRUE);
		$this->_formatReserved();
		if ($preFormatted)
			print '<pre>' . $this->queryCode . '</pre><br>';
		else
        	print $this->queryCode . '<br>';
	}

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::getQuery
	// @desc 		Constrói e retorna o código da consulta SQL a partir
	// 				dos valores atuais encontrados no objeto
	// @access 		public
	// @return		string
	//!-----------------------------------------------------------------
	function getQuery() {
		if (empty($this->fields) || empty($this->tables)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_QUERY_ELEMENTS'), E_USER_ERROR, __FILE__, __LINE__);
			return NULL;
		}
		$this->_buildQuery();
		$this->_formatReserved();
		return $this->queryCode;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::&executeQuery
	// @desc		Busca uma conexão com o banco de dados e executa a consulta SQL
	// @param		bindVars array		"array()" Variáveis de amarração para a consulta
	// @param		connectionId string	"NULL" ID da conexão ao banco de dados a ser utilizada
	// @return		ADORecordSet object Conjunto de resultados da consulta
	// @access		public
	//!-----------------------------------------------------------------
	function &executeQuery($bindVars=array(), $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		if (@$this->limit['rows'] > 0)
			$Rs =& $Db->limitQuery($this->getQuery(), $this->limit['rows'], $this->limit['offset'], TRUE, $bindVars);
		else
			$Rs =& $Db->query($this->getQuery(), TRUE, $bindVars);
		return $Rs;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::&createDataSet
	// @desc		Constrói um dataset a partir da consulta SQL
	// @param		params array	"array()" Parâmetros para a construção do dataset
	// @return		DataSetDb object
	// @access		public
	//!-----------------------------------------------------------------
	function &createDataSet($params=array()) {
		$DataSet =& DataSet::factory('db', $params);
		if (@$this->limit['rows'] > 0)
			$DataSet->loadSubSet($this->limit['offset'], $this->limit['rows'], $this->getQuery(), $bindVars);
		else
			$DataSet->load($this->getQuery());
		return $DataSet;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::_buildQuery
	// @desc		Monta o código da query a partir das partes armazenadas
	// @param		idDisplay bool	"FALSE" Indica se a query está sendo construída para exibição ou para execução
	// @note		Este método privado é executado em displayQuery() e getQuery()
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildQuery($isDisplay=FALSE) {
		$char = ($isDisplay) ? "\r\n\t" : ' ';
		$this->queryCode = "SELECT " . $char . ($this->distinct ? "DISTINCT " : "") . $this->fields;
		$this->queryCode .= $char . "FROM " . $char . eregi_replace("JOIN[ ]", "JOIN$char", $this->tables);
		if (trim($this->clause) != '')
			$this->queryCode .= $char . "WHERE " . $char . $this->clause;
		if (trim($this->groupby) != '') {
			$this->queryCode .= $char . "GROUP BY " . $char . $this->groupby;
			if (!empty($this->condition))
				$this->queryCode .= " HAVING " . $this->condition;
		}
		if (trim($this->orderby) != '')
			$this->queryCode .= $char . "ORDER BY " . $char . $this->orderby;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::_formatReserved
	// @desc		Formata as palavras reservadas da query
	// @note		É montado um código SQL com as palavras reservadas formatadas para
	//				maiúsculas ou minúsculas de acordo com o valor da propriedade upCaseWords.
	//				O conjunto de palavras reservadas foi extraído do padrão ANSI SQL 1998
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
    function _formatReserved() {
		$reservedWordsUp = 	array("ALL","AND","AS","ASC","AVG","BETWEEN","BY","CASE","CAST","CHAR","COUNT","CURRENT","CURRENT_DATE","CURRENT_TIME","CURRENT_TIMESTAMP","CURRENT_USER","CURSOR","DATE","DEC","" .
							"DESC","DISTINCT","ELSE","END","EXISTS","FALSE","FROM","FLOAT","GROUP","HAVING","IN","INNER","INTERSECT","INTERVAL","IS","JOIN","LAST","LEFT","LIKE","MAX","MIN","MONTH","" .
							"NATURAL","NEXT","NOT","NULL","NULLIF","NUMERIC","OF","ON","ONLY","OR","ORDER","OUTER","RETURNING","RIGHT","SELECT","SMALLINT","SUBSTRING","SUM","THEN","TRIM","TRUE","UNION","UPPER","" .
							"USING","VARYING","WHEN","WHERE","WITH");
		$reservedWordsLow =	array("all","and","as","asc","avg","between","by","case","cast","char","count","current","current_date","current_time","current_timestamp","current_user","cursor","date","dec","" .
							"desc","distinct","else","end","exists","false","from","float","group","having","in","inner","intersect","interval","is","join","last","left","like","max","min","month","" .
							"natural","next","not","null","nullif","numeric","of","on","only","or","order","outer","returning","right","select","smallint","substring","sum","then","trim","true","union","upper","" .
							"using","varying","when","where","with");
		if ($this->upCaseWords) {
			while (list(, $word) = each($reservedWordsUp)) {
				$this->queryCode = substr(eregi_replace('[[:space:]]' . $word . '[[:space:]]', ' ' . $word  . ' ', ' ' . $this->queryCode), 1);
			}
		} else {
			while (list(, $word) = each($reservedWordsLow)) {
				$this->queryCode = substr(eregi_replace('[[:space:]]' . $word . '[[:space:]]', ' ' . $word  . ' ', ' ' . $this->queryCode), 1);
			}
		}
	}
}
?>