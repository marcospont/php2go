<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.data.DataSet');
import('php2go.util.Statement');
import('php2go.xml.XmlDocument');

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
	var $condition;
	
	/**
	 * Order clause
	 *
	 * @var string
	 */
	var $orderby;
	
	/**
	 * Limit settings
	 *
	 * @var array
	 */
	var $limit = array();
	
	/**
	 * Uppercase all SQL keywords inside the query
	 *
	 * @var bool
	 */
	var $upCaseWords = TRUE;

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
		$this->condition = '';
		$this->orderby = $orderby;
	}

	/**
	 * Enable/disable use of the DISTINCT keyword
	 *
	 * @param bool $setting Enable/disable
	 */
	function setDistinct($setting=TRUE) {
		$this->distinct = TypeUtils::toBoolean($setting);
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
	 * Adds a table in the SQL query
	 *
	 * @param string $tableName
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
	 * @param string $groupby Group by column or columns
	 * @param string $condition Group by condition (without HAVING keyword)
	 */
	function setGroup($groupby='', $condition='') {
		$this->groupby = $groupby;
		if (trim($groupby) == '')
			$this->condition = '';
		else if (trim($condition) != '')
			$this->condition = $condition;
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
	 * Defines limit settings
	 *
	 * @param int $rows Number of rows
	 * @param int $offset Starting offset
	 */
	function setLimit($rows, $offset=NULL) {
		$this->limit['rows'] = (int)$rows;
		$this->limit['offset'] = (int)$offset;
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
	 *   print $row->getField('name') . '<br/>';
	 * }
	 * </code>
	 *
	 * @param string $xmlFile XML file path
	 */
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

	/**
	 * Reset all query properties
	 */
	function reset() {
		$this->distinct = FALSE;
		$this->fields = '';
		$this->tables = '';
		$this->clause = '';
		$this->groupby = '';
		$this->condition = '';
		$this->orderby = '';
	}

	/**
	 * Build and display the SQL query
	 *
	 * @param bool $preFormatted Whether to use pre tags
	 */
	function displayQuery($preFormatted=TRUE) {
		$sql = $this->_formatReserved($this->_buildQuery(TRUE));
		if ($preFormatted)
			print '<pre>' . $sql . '</pre><br>';
		else
        	print $sql . '<br>';
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
		$Db =& Db::getInstance($connectionId);
		if (@$this->limit['rows'] > 0)
			$Rs =& $Db->limitQuery($this->getQuery(), $this->limit['rows'], $this->limit['offset'], TRUE, $bindVars);
		else
			$Rs =& $Db->query($this->getQuery(), TRUE, $bindVars);
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
		$DataSet =& DataSet::factory('db', $params);
		if (@$this->limit['rows'] > 0)
			$DataSet->loadSubSet($this->limit['offset'], $this->limit['rows'], $this->getQuery(), $bindVars);
		else
			$DataSet->load($this->getQuery());
		return $DataSet;
	}

	/**
	 * Internal method used to build the SQL query
	 *
	 * @param bool $isDisplay Indicates if the query will be displayed
	 * @return string
	 * @access private
	 */
	function _buildQuery($isDisplay=FALSE) {
		$char = ($isDisplay) ? "\r\n\t" : ' ';
		$sql = "SELECT " . $char . ($this->distinct ? "DISTINCT " : "") . $this->fields;
		$sql .= $char . "FROM " . $char . eregi_replace("JOIN[ ]", "JOIN$char", $this->tables);
		if (trim($this->clause) != '')
			$sql .= $char . "WHERE " . $char . $this->clause;
		if (trim($this->groupby) != '') {
			$sql .= $char . "GROUP BY " . $char . $this->groupby;
			if (!empty($this->condition))
				$sql .= " HAVING " . $this->condition;
		}
		if (trim($this->orderby) != '')
			$sql .= $char . "ORDER BY " . $char . $this->orderby;
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
							"DESC","DISTINCT","ELSE","END","EXISTS","FALSE","FROM","FLOAT","GROUP","HAVING","IN","INNER","INTERSECT","INTERVAL","IS","JOIN","LAST","LEFT","LIKE","MAX","MIN","MONTH","" .
							"NATURAL","NEXT","NOT","NULL","NULLIF","NUMERIC","OF","ON","ONLY","OR","ORDER","OUTER","RETURNING","RIGHT","SELECT","SMALLINT","SUBSTRING","SUM","THEN","TRIM","TRUE","UNION","UPPER","" .
							"USING","VARYING","WHEN","WHERE","WITH");
		$reservedWordsLow =	array("all","and","as","asc","avg","between","by","case","cast","char","count","current","current_date","current_time","current_timestamp","current_user","cursor","date","dec","" .
							"desc","distinct","else","end","exists","false","from","float","group","having","in","inner","intersect","interval","is","join","last","left","like","max","min","month","" .
							"natural","next","not","null","nullif","numeric","of","on","only","or","order","outer","returning","right","select","smallint","substring","sum","then","trim","true","union","upper","" .
							"using","varying","when","where","with");
		if ($this->upCaseWords) {
			while (list(, $word) = each($reservedWordsUp)) {
				$sql = substr(eregi_replace('[[:space:]]' . $word . '[[:space:]]', ' ' . $word  . ' ', ' ' . $sql), 1);
			}
		} else {
			while (list(, $word) = each($reservedWordsLow)) {
				$sql = substr(eregi_replace('[[:space:]]' . $word . '[[:space:]]', ' ' . $word  . ' ', ' ' . $sql), 1);
			}
		}
		return $sql;
	}
}
?>