<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
 * AND operator
 */
define('QUERY_BUILDER_AND', 'AND');
/**
 * OR operator
 */
define('QUERY_BUILDER_OR', 'OR');
/**
 * An operand that must not parenthize with another operand
 */
define('QUERY_BUILDER_OP_NONE', 0);
/**
 * An operand that must parenthize with last added operand
 */
define('QUERY_BUILDER_OP_LAST', 1);
/**
 * An operand that must parenthize with all existent operands
 */
define('QUERY_BUILDER_OP_ALL', 2);


/**
 * Builds database queries
 *
 * Based on provided query parts (fields, tables, condition clause,
 * grouping clause, orderby clause, ...), this class is able to build,
 * extend, modify and execute regular SQL queries.
 *
 * Examples:
 * <code>
 * $qry = new QueryBuilder('*', 'person');
 * print $qry->getQuery();
 * /**
 *  * Prints:
 *  * SELECT * FROM person
 * {@*}
 *
 * $qry = new QueryBuilder();
 * $qry->setFields('p.name,p.address,p.category');
 * $qry->addTable('person p');
 * $qry->joinTable('user u', 'inner join', 'p.id_person = u.id_user');
 * $qry->addClause('p.active = 1');
 * $qry->addClause('u.blocked = 0', QUERY_BUILDER_AND, QUERY_BUILDER_OP_LAST);
 * $qry->setOrder('p.name');
 * print $qry->getQuery();
 * /**
 *  * Prints:
 *  * SELECT p.name,p.address,p.category
 *  * FROM person p INNER JOIN
 *  * user u ON (p.id_person = u.id_user)
 *  * WHERE ( p.active = 1 AND u.blocked = 0 )
 *  * ORDER BY p.name
 * {@*}
 * </code>
 *
 * @package db
 * @uses Db
 * @uses DataSet
 * @uses QueryParser
 * @uses TypeUtils
 * @uses XmlDocument
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class QueryBuilder extends PHP2Go
{
	/**
	 * Whether to use the DISTINCT keyword
	 *
	 * @var bool
	 */
	var $distinct;

	/**
	 * Query fields
	 *
	 * @var string
	 */
	var $fields;

	/**
	 * Query tables (including join operations)
	 *
	 * @var string
	 */
	var $tables;

	/**
	 * Query condition clause
	 *
	 * @var string
	 */
	var $clause;

	/**
	 * Query grouping clause
	 *
	 * @var string
	 */
	var $groupby;

	/**
	 * Grouping clause condition (HAVING keyword)
	 *
	 * @var string
	 */
	var $groupcond;

	/**
	 * Order clause
	 *
	 * @var string
	 */
	var $orderby;

	/**
	 * Limit
	 *
	 * @var mixed
	 */
	var $limit;

	/**
	 * Top (limit)
	 *
	 * @var mixed
	 */
	var $top;

	/**
	 * Uppercase all SQL keywords inside the query
	 *
	 * @var bool
	 */
	var $upCaseWords = FALSE;

	/**
	 * Class constructor
	 *
	 * @param string $fields Initial fields
	 * @param string $tables Initial tables
	 * @param string $clause Initial condition clause
	 * @param string $groupby Initial grouping clause
	 * @param string $orderby Initial order clause
	 * @return QueryBuilder
	 */
	function QueryBuilder($fields='', $tables='', $clause='', $groupby='', $orderby='') {
		parent::PHP2Go();
		$this->distinct = FALSE;
		$this->fields = $fields;
		$this->tables = $tables;
		$this->clause = $clause;
		$this->groupby = $groupby;
		$this->groupcond = '';
		$this->orderby = $orderby;
		$this->limit = '';
		$this->top = '';
	}

	/**
	 * Builds a query from a full SQL string
	 *
	 * @param string $sql SQL query
	 * @return QueryBuilder
	 * @static
	 */
	function createFromSql($sql) {
		import('php2go.db.QueryParser');
		$Parser = new QueryParser($sql);
		$Query = new QueryBuilder($Parser->parts['fields'], $Parser->parts['tables'], $Parser->parts['clause'], $Parser->parts['groupby'], $Parser->parts['orderby']);
		$Query->setLimit($Parser->parts['limit']);
		return $Query;
	}

	/**
	 * Enable/disable use of the DISTINCT keyword
	 *
	 * @param bool $setting Enable/disable
	 */
	function setDistinct($setting=TRUE) {
		$this->distinct = (bool)$setting;
	}

	/**
	 * Set the fields of the SQL query
	 *
	 * Replaces existent fields.
	 *
	 * @param string $fields Query fields (comma separated, if more than one)
	 * @return string Old query fields
	 */
	function setFields($fields='*') {
		$oldValue = $this->fields;
		$this->fields = $fields;
		return $oldValue;
	}

	/**
	 * Adds one or more fields in the SQL query
	 *
	 * @param string $fields Field name or comma separated list of fields
	 */
	function addFields($fields) {
    	if (empty($this->fields))
        	$this->fields = $fields;
		else
        	$this->fields .= ', ' . $fields;
	}

	/**
	 * Set query table
	 *
	 * @param string $tableName Table name
	 */
	function setTable($tableName) {
		$this->tables = $tableName;
	}

	/**
	 * Adds a table in the SQL query
	 *
	 * @param string $tableName Table name
	 */
	function addTable($tableName) {
		if (empty($this->tables))
			$this->tables = $tableName;
		else
			$this->tables .= ', ' . $tableName;
	}

	/**
	 * Adds a join operation in the SQL query
	 *
	 * @param string $tableName Table name
	 * @param string $joinType Join type (e.g.: inner join, left join)
	 * @param string $joinCondition Full join condition
	 */
	function joinTable($tableName, $joinType, $joinCondition) {
		if (!empty($this->tables)) {
			$this->tables .= " $joinType $tableName ON ($joinCondition)";
		}
	}

	/**
	 * Adds a condition clause
	 *
	 * @param string $clause Condition clause
	 * @param string $operator Operator ({@link QUERY_BUILDER_AND} or {@link QUERY_BUILDER_OR})
	 * @param int $action How to parenthize the new condition operand with existent operands
	 * @return bool
	 */
	function addClause($clause, $operator=QUERY_BUILDER_AND, $action=QUERY_BUILDER_OP_NONE) {
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

	/**
	 * Set the query condition clause
	 *
	 * Replaces the existent condition clause.
	 *
	 * @param string $clause Condition clause
	 * @return string Old clause
	 */
	function setClause($clause='') {
		$oldValue = $this->clause;
		$this->clause = $clause;
		return $oldValue;
	}

	/**
	 * Clears the condition clause
	 */
	function clearClause() {
		$this->clause = '';
	}

	/**
	 * Set grouping clause
	 *
	 * @param string $by Group by column or columns
	 * @param string $condition Group by condition (without HAVING keyword)
	 */
	function setGroup($by='', $condition='') {
		$this->groupby = $by;
		if (trim($by) == '')
			$this->groupcond = '';
		elseif (trim($condition) != '')
			$this->groupcond = $condition;
	}

	/**
	 * Clears the grouping clause
	 */
	function clearGroup() {
		$this->groupby = '';
		$this->groupcond = '';
	}

	/**
	 * Prepends one or more columns in the orderby clause
	 *
	 * @param string $orderby Order clause
	 */
	function prefixOrder($orderby) {
    	if (empty($this->orderby))
        	$this->setOrder($orderby);
		else
        	$this->orderby = $orderby . ' , ' . $this->orderby;
	}

	/**
	 * Adds a new order clause
	 *
	 * @param string $orderby Order clause
	 */
	function addOrder($orderby) {
		if (empty($this->orderby))
			$this->setOrder($orderby);
		else
			$this->orderby = $this->orderby . ' , ' . $orderby;
	}

	/**
	 * Set the order clause of the SQL query
	 *
	 * Replaces existent order clause.
	 *
	 * @see prefixOrder()
	 * @see addOrder()
	 * @param string $orderby Order clause
	 */
	function setOrder($orderby='') {
		$this->orderby = $orderby;
	}

	/**
	 * Clears the order by clause
	 */
	function clearOrder() {
		$this->orderby = '';
	}

	/**
	 * Set limit settings
	 *
	 * @param int $limit Limit clause (string) or number of rows
	 * @param int $offset Starting offset
	 */
	function setLimit($limit, $offset=NULL) {
		if (is_numeric($limit) && is_numeric($offset)) {
			$this->limit = array(
				'rows' => intval($limit),
				'offset' => intval($offset)
			);
		} elseif (is_string($limit) || is_numeric($limit)) {
			$this->limit = strval($limit);
		}
	}

	/**
	 * Clears limit settings
	 */
	function clearLimit() {
		$this->limit = '';
	}

	/**
	 * Loads the SQL query from a XML file containing
	 * a DATASOURCE specification
	 *
	 * This method is able to parse DATASOURCE nodes from
	 * XML files used by {@link Report} class to build
	 * HTML reports.
	 *
	 * Example:
	 * <code>
	 * /* my_xml_query.xml {@*}
	 * <report ...>
	 *   <layout ...>
	 *     ...
	 *   </layout>
	 *   <datasource>
	 *     <fields>client_id, name, address, category</fields>
	 *     <tables>client</tables>
	 *   </datasource>
	 * </report>
	 * /* my_xml_query.php {@*}
	 * $qry = new QueryBuilder();
	 * $qry->loadFromXml('my_xml_query.xml');
	 * $dataset =& $qry->createDataSet();
	 * while ($row = $dataset->fetch()) {
	 *   print $row->getField('name') . '<br />';
	 * }
	 * </code>
	 *
	 * @param string $xmlFile XML file path
	 */
	function loadFromXml($xmlFile) {
		import('php2go.util.Statement');
		import('php2go.xml.XmlDocument');
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
					$value = $children[$candidate]->value;
					if (!empty($value)) {
						if (preg_match("/~[^~]+~/", $value))
							$value = Statement::evaluate($value);
						$propName = strtolower($candidate);
						$this->{$propName} = $value;
					}
				}
			}
		}
	}

	/**
	 * Reset all query properties
	 */
	function reset() {
		$this->distinct = FALSE;
		$this->fields = '';
		$this->tables = '';
		$this->clause = '';
		$this->groupby = '';
		$this->groupcond = '';
		$this->orderby = '';
		$this->limit = '';
		$this->top = '';
	}

	/**
	 * Build and display the SQL query
	 *
	 * @param bool $preFormatted Whether to use pre tags
	 */
	function displayQuery($preFormatted=TRUE) {
		$sql = $this->_formatReserved($this->_buildQuery(TRUE));
		if ($preFormatted)
			println("<pre>{$sql}</pre>");
		else
			println($sql);
	}

	/**
	 * Build and return the SQL query
	 *
	 * @return string
	 */
	function getQuery() {
		if (empty($this->fields) || empty($this->tables)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_QUERY_ELEMENTS'), E_USER_ERROR, __FILE__, __LINE__);
			return NULL;
		}
		return $this->_formatReserved($this->_buildQuery());
	}

	/**
	 * Build and execute the SQL query in the database
	 *
	 * If the query has limit settings, the record set will be
	 * built based on these settings.
	 *
	 * @uses Db::query()
	 * @uses Db::limitQuery()
	 * @param array $bindVars Bind variables
	 * @param string $connectionId Connection ID
	 * @return ADORecordSet
	 */
	function &executeQuery($bindVars=array(), $connectionId=NULL) {
		if (empty($this->fields) || empty($this->tables)) {
			$null = NULL;
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_QUERY_ELEMENTS'), E_USER_ERROR, __FILE__, __LINE__);
			return $null;
		}
		$Db =& Db::getInstance($connectionId);
		$sql = $this->_formatReserved($this->_buildQuery(FALSE, TRUE));
		$limitMatches = array();
		if (is_string($this->limit) && preg_match("/([0-9]+)(?:\s*(?:,|offset)\s*([0-9]+))?/", $this->limit, $limitMatches))
			$Rs =& $Db->limitQuery($sql, $limitMatches[1], @$limitMatches[2]);
		elseif (is_array($this->limit) && $this->limit['rows'] > 0)
			$Rs =& $Db->limitQuery($sql, $this->limit['rows'], $this->limit['offset'], TRUE, $bindVars);
		elseif (!empty($this->top))
			$Rs =& $Db->limitQuery($sql, $this->top, 0, TRUE, $bindVars);
		else
			$Rs =& $Db->query($query, TRUE, $bindVars);
		return $Rs;
	}

	/**
	 * Builds and creates a data set from the SQL query
	 *
	 * If the query has limit settings, the data set will be
	 * built based on these settings.
	 *
	 * @uses DataSet::load()
	 * @uses DataSet::loadSubSet()
	 * @param array $params Data adapter parameters
	 * @return DataSet
	 */
	function &createDataSet($params=array()) {
		if (empty($this->fields) || empty($this->tables)) {
			$null = NULL;
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_QUERY_ELEMENTS'), E_USER_ERROR, __FILE__, __LINE__);
			return $null;
		}
		import('php2go.data.DataSet');
		$Dataset = DataSet::factory('db', $params);
		$sql = $this->_formatReserved($this->_buildQuery(FALSE, TRUE));
		$limitMatches = array();
		if (is_string($this->limit) && preg_match("/([0-9]+)(?:\s*(?:,|offset)\s*([0-9]+))?/", $this->limit, $limitMatches))
			$Dataset->loadSubSet(@$limitMatches[2], $limitMatches[1], $sql);
		elseif (is_array($this->limit) && $this->limit['rows'] > 0)
			$Dataset->loadSubSet($this->limit['offset'], $this->limit['rows'], $sql);
		elseif (!empty($this->top))
			$Dataset->loadSubSet(0, $this->top, $sql);
		else
			$Dataset->load($sql);
		return $Dataset;
	}

	/**
	 * Internal method used to build the SQL query
	 *
	 * @param bool $isDisplay Indicates if the query will be displayed
	 * @param bool $isExecute Indicates if the query will be executed
	 * @return string
	 * @access private
	 */
	function _buildQuery($isDisplay=FALSE, $isExecute=FALSE) {
		$c1 = ($isDisplay ? "\r\n" : ' ');
		$c2 = ($isDisplay ? "\t" : '');
		$sql = $c1 . 'SELECT ';
		// add "top N" limit clause
		if (!$isExecute && !empty($this->top) && empty($this->limit))
			$sql .= ' TOP ' . $this->top;
		// add fields
		$sql .= $c1 . $c2 . ($this->distinct ? 'DISTINCT ' : '') . $this->fields;
		// add tables
		$sql .= $c1 . 'FROM ' . $c1 . $c2 . preg_replace("/JOIN\s/i", "JOIN" . $c1 . $c2, $this->tables);
		// add condition clause
		if (trim($this->clause) != '')
			$sql .= $c1 . 'WHERE ' . $c1 . $c2 . $this->clause;
		// add group by clause
		if (trim($this->groupby) != '') {
			$sql .= $c1 . 'GROUP BY ' . $c1 . $c2 . $this->groupby;
			if (!empty($this->groupcond))
				$sql .= ' HAVING ' . $this->groupcond;
		}
		// add order by clause
		if (trim($this->orderby) != '')
			$sql .= $c1 . 'ORDER BY ' . $c1 . $c2 . $this->orderby;
		// add "limit M offset N" limit clause
		if (!$isExecute && is_string($this->limit) && !empty($this->limit) && empty($this->top))
			$sql .= $c1 . 'LIMIT' . $c1 . $c2 . $this->limit;
		return $sql;
	}

	/**
	 * Format all SQL keywords inside an SQL query
	 *
	 * @param string $sql Input query
	 * @return string Transformed query
	 * @access private
	 */
    function _formatReserved($sql) {
		$reservedWordsUp = 	array("ALL","AND","AS","ASC","AVG","BETWEEN","BY","CASE","CAST","CHAR","COUNT","CURRENT","CURRENT_DATE","CURRENT_TIME","CURRENT_TIMESTAMP","CURRENT_USER","CURSOR","DATE","DEC","" .
							"DESC","DISTINCT","ELSE","END","EXISTS","FALSE","FROM","FLOAT","GROUP","HAVING","IN","INNER","INTERSECT","INTERVAL","IS","JOIN","LAST","LEFT","LIKE","LIMIT","MAX","MIN","MONTH","" .
							"NATURAL","NEXT","NOT","NULL","NULLIF","NUMERIC","OF","OFFSET","ON","ONLY","OR","ORDER","OUTER","RETURNING","RIGHT","SELECT","SMALLINT","SUBSTRING","SUM","THEN","TOP","TRIM","TRUE","UNION","UPPER","" .
							"USING","VARYING","WHEN","WHERE","WITH");
		$reservedWordsLow =	array("all","and","as","asc","avg","between","by","case","cast","char","count","current","current_date","current_time","current_timestamp","current_user","cursor","date","dec","" .
							"desc","distinct","else","end","exists","false","from","float","group","having","in","inner","intersect","interval","is","join","last","left","like","limit","max","min","month","" .
							"natural","next","not","null","nullif","numeric","of","offset","on","only","or","order","outer","returning","right","select","smallint","substring","sum","then","top","trim","true","union","upper","" .
							"using","varying","when","where","with");
		if ($this->upCaseWords) {
			while (list(, $word) = each($reservedWordsUp)) {
				$sql = preg_replace("/\b{$word}\b/i", $word, $sql);
			}
		} else {
			while (list(, $word) = each($reservedWordsLow)) {
				$sql = preg_replace("/\b{$word}\b/i", $word, $sql);
			}
		}
		return $sql;
	}
}
?>