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
 * Insert DML command
 */
define('DML_BUILDER_INSERT', 1);
/**
 * Update DML command
 */
define('DML_BUILDER_UPDATE', 2);
/**
 * Update DML command for multiple records
 */
define('DML_BUILDER_UPDATE_MULTIPLE', 3);
/**
 * Base SQL code to build insert statements
 */
define('DML_BUILDER_INSERTSQL', "INSERT INTO %s (%s) VALUES (%s)");
/**
 * Base SQL code to build update statements
 */
define('DML_BUILDER_UPDATESQL', "UPDATE %s SET %s WHERE %s");
/**
 * Base SQL code to build update statements without condition clause
 */
define('DML_BUILDER_UPDATEALLSQL', "UPDATE %s SET %s");
/**
 * Special value to initialize oracle CLOB columns
 */
define('OCI_EMPTY_CLOB', 'EMPTY_CLOB()');
/**
 * Special value to initialize oracle BLOB columns
 */
define('OCI_EMPTY_BLOB', 'EMPTY_BLOB()');

/**
 * Builds DML commands
 *
 * Based on the table name and a hash array of fields and
 * values, this class is able to build INSERT and UPDATE
 * commands.
 *
 * It supports bind variables, handling of empty values and
 * conversion of values to the formats, according with the
 * column native types.
 *
 * Example:
 * <code>
 * $db =& Db::getInstance();
 * $dml = new DMLBuilder($db);
 * $dml->prepare(
 *   DML_BUILDER_UPDATE, 'person',
 *   array('name'=>$newName),
 *   'id_person=?', array($idPerson)
 * );
 * $result = @$dml->execute();
 * </code>
 *
 * @package db
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DMLBuilder extends PHP2Go
{
	/**
	 * Whether to ignore empty values when building insert/update statements
	 *
	 * @var bool
	 */
	var $ignoreEmptyValues = FALSE;

	/**
	 * Force an update command even when database row is unchanged
	 *
	 * @var bool
	 */
	var $forceUpdate = FALSE;

	/**
	 * Use bind variables
	 *
	 * @var bool
	 */
	var $useBind = FALSE;

	/**
	 * Active mode
	 *
	 * @var int
	 * @access private
	 */
	var $_mode;

	/**
	 * Active table name
	 *
	 * @var string
	 * @access private
	 */
	var $_table;

	/**
	 * Active field values
	 *
	 * @var array
	 * @access private
	 */
	var $_values = array();

	/**
	 * Active condition clause
	 *
	 * @var string
	 * @access private
	 */
	var $_clause;

	/**
	 * Bind variables used in the condition clause
	 *
	 * @var array
	 * @access private
	 */
	var $_clauseBindVars = array();

	/**
	 * Bind variables used in the fields
	 *
	 * @var array
	 * @access private
	 */
	var $_bindVars = array();

	/**
	 * Holds a hash array containing all changed values in an UPDATE command
	 *
	 * @var array
	 * @access private
	 */
	var $_updateVars = array();

	/**
	 * Holds an instance of the database connection class
	 *
	 * @var object Db
	 * @access private
	 */
	var $_Db = NULL;

	/**
	 * Class constructor
	 *
	 * @param Db &$Db Database connection
	 * @return DMLBuilder
	 */
	function DMLBuilder(&$Db) {
		parent::PHP2Go();
		if (!TypeUtils::isInstanceOf($Db, 'Db'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Db'), E_USER_ERROR, __FILE__, __LINE__);
		$this->_Db =& $Db;
	}

	/**
	 * Prepare a DML command
	 *
	 * Examples:
	 * <code>
	 * /* Preparing an INSERT command {@*}
	 * $dml->prepare(DML_BUILDER_INSERT, 'table_name', $arrayOfFields);
	 * /* Preparing an UPDATE command {@*}
	 * $dml->prepare(DML_BUILDER_UPDATE, 'table_name', $arrayOfFields, 'pk_field = '.$pkValue);
	 * </code>
	 *
	 * @param int $mode Mode ({@link DML_BUILDER_INSERT} or {@link DML_BUILDER_UPDATE})
	 * @param string $table Table name
	 * @param array $values Field values
	 * @param string $clause Condition clause
	 * @param array $clauseBindVars Bind variables to be used in the condition clause
	 */
	function prepare($mode, $table, $values, $clause=NULL, $clauseBindVars=array()) {
		if ($mode != DML_BUILDER_INSERT && $mode != DML_BUILDER_UPDATE && $mode != DML_BUILDER_UPDATE_MULTIPLE)
			$mode = DML_BUILDER_INSERT;
		$this->_mode = $mode;
		$this->_table = $table;
		$this->_values = (array)$values;
		if (!empty($clause)) {
			$this->_clause = $clause;
			$this->_clauseBindVars = (array)$clauseBindVars;
		} else {
			unset($this->_clause);
			$this->_clauseBindVars = array();
		}
		$this->_bindVars = array();
		$this->_updateVars = array();
	}

	/**
	 * Build and return the SQL code of the active statement
	 *
	 * Should be called after {@link prepare}.
	 *
	 * @return string
	 */
	function getSql() {
		if ($this->_mode == DML_BUILDER_INSERT) {
			if (!empty($this->_table) && !empty($this->_values)) {
				return $this->_insertSql();
			}
		} elseif ($this->_mode == DML_BUILDER_UPDATE) {
			if (!empty($this->_table) && !empty($this->_values) && !empty($this->_clause)) {
				return $this->_updateSql();
			}
		} elseif ($this->_mode == DML_BUILDER_UPDATE_MULTIPLE) {
			if (!empty($this->_table) && !empty($this->_values)) {
				return $this->_updateMultipleSql();
			}
		}
		return FALSE;
	}

	/**
	 * Build and prepare the active statement
	 *
	 * Should be called after {@link prepare}.
	 *
	 * @uses getSql()
	 * @uses Db::prepare()
	 * @return array
	 */
	function getPreparedStatement() {
		$sql = $this->getSql();
		if (!empty($sql))
			return $this->_Db->prepare($sql);
		return NULL;
	}

	/**
	 * Get all bind variables of the active statement
	 *
	 * Should be called after {@link execute}.
	 *
	 * @return array
	 */
	function getBindVars() {
		return array_merge($this->_bindVars, $this->_clauseBindVars);
	}

	/**
	 * Return a hash array of changed fields
	 *
	 * This information is captured when an UPDATE statement is
	 * prepared and executed inside this class. The list of
	 * updated fields can be useful to track  changes on
	 * database records.
	 *
	 * Should be called after {@link execute}.
	 *
	 * @return array
	 */
	function getUpdateVars() {
		return $this->_updateVars;
	}

	/**
	 * Build, prepare and run the the DML command
	 *
	 * @uses getSql()
	 * @uses Db::prepare()
	 * @uses Db::execute()
	 * @return bool
	 */
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

	/**
	 * Internal method used to build INSERT statements
	 *
	 * @return string
	 * @access private
	 */
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
				// empty values
				if ($this->_isEmpty($values[$colUpper])) {
					if ($this->ignoreEmptyValues) {
						continue;
					} else {
						$values[$colUpper] = NULL;
					}
				}
				// bind variables enabled
				if ($this->useBind) {
					// special handling of empty CLOB/BLOB values for oracle
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

	/**
	 * Internal method used to build UPDATE statements
	 *
	 * @return string
	 * @access private
	 */
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
				// format the field name, if necessary, and get the metatype
				$colQuote = (strpos($colUpper, ' ') !== FALSE ? $this->_Db->AdoDb->nameQuote . $colUpper . $this->_Db->AdoDb->nameQuote : $colUpper);
				$colType = $rs->MetaType($dbCol->type);
				if ($colType == 'null')
					$colType = 'C';
				// get the current field value
				// name tests are necessary because database drivers have different case patterns for record set fields
				if (isset($rs->fields[$colUpper]))
					$curVal = $rs->fields[$colUpper];
				elseif (isset($rs->fields[$dbCol->name]))
					$curVal = $rs->fields[$dbCol->name];
				elseif (isset($rs->fields[strtolower($colUpper)]))
					$curVal = $rs->fields[strtolower($colUpper)];
				else
					$curVal = '';
				// define new value for comparison
				if ($this->forceUpdate || strcmp($curVal, $values[$colUpper])) {
					// empty values
					if ($this->_isEmpty($values[$colUpper])) {
						if (empty($curVal) && $this->ignoreEmptyValues) {
							continue;
						} else {
							$values[$colUpper] = NULL;
						}
					}
					// add the field in the update fields list
					$this->_updateVars[$dbCol->name] = array(
						'old' => $curVal,
						'new' => ($values[$colUpper] === NULL ? 'null' : (string)$values[$colUpper])
					);
					// bind variables enabled
					if ($this->useBind) {
						if ($isOci) {
							// special handling of empty BLOB/CLOB columns on oracle
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
	
	/**
	 * Internal method used to build UPDATE statements on multiple records
	 *
	 * @return string
	 * @access private
	 */
	function _updateMultipleSql() {
		$setValues = '';
		$rs =& $this->_getRecordSet();
		$dbCols = $this->_getColumnsList();
		$values = array_change_key_case($this->_values, CASE_UPPER);
		$isOci = ($this->_Db->AdoDb->dataProvider == 'oci8');
		foreach ($dbCols as $dbCol) {
			$colUpper = strtoupper($dbCol->name);
			if (array_key_exists($colUpper, $values)) {
				$colQuote = (strpos($colUpper, ' ') !== FALSE ? $this->_Db->AdoDb->nameQuote . $colUpper . $this->_Db->AdoDb->nameQuote : $colUpper);
				$colType = $rs->MetaType($dbCol->type);
				// empty values
				if ($this->_isEmpty($values[$colUpper])) {
					if ($this->ignoreEmptyValues) {
						continue;
					} else {
						$values[$colUpper] = NULL;
					}
				}
				// bind variables enabled
				if ($this->useBind) {
					// special handling of empty CLOB/BLOB values for oracle
					if ($isOci) {
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
		if (!empty($setValues)) {
			$setValues = substr($setValues, 0, -2);
			// empty clause (global update)
			if (empty($this->_clause))
				return sprintf(DML_BUILDER_UPDATEALLSQL, $this->_table, $setValues);
			else
				return sprintf(DML_BUILDER_UPDATESQL, $this->_table, $setValues, $this->_clause);
		}
		return '';					
	}

	/**
	 * Build a fake record set using the same driver used
	 * by the active connection
	 *
	 * @return ADORecordSet
	 * @access private
	 */
	function &_getRecordSet() {
		static $rsObj;
		if (!isset($rsObj)) {
			$rsClass = $this->_Db->AdoDb->rsPrefix . $this->_Db->AdoDb->databaseType;
			$rsObj = new $rsClass(-1, $this->_Db->AdoDb->fetchMode);
			$rsObj->connection =& $this->_Db->AdoDb;
		}
		return $rsObj;
	}

	/**
	 * Retrieve the column list of the active table
	 *
	 * This method uses internal static variables to cache
	 * information about tables already visited.
	 *
	 * @uses Db::getColumns()
	 * @return array
	 * @access private
	 */
	function _getColumnsList() {
		static $cache;
		if (!isset($cache))
			$cache = array();
		if (!array_key_exists($this->_table, $cache)) {
			$cache[$this->_table] = $this->_Db->getColumns($this->_table);
		}
		return $cache[$this->_table];
	}

	/**
	 * Builds SQL for a column, to be used on an INSERT or UPDATE statement
	 *
	 * @uses Db::quoteString()
	 * @uses Db::date()
	 * @param string $value Column value
	 * @param string $metaType Column meta type
	 * @param string $type Column type
	 * @param string $nameQuote Quoted column name
	 * @return string
	 * @access private
	 */
	function _getColumnSql($value, $metaType, $type, $nameQuote) {
		if ($this->_Db->AdoDb->dataProvider == 'postgres' && $metaType == 'L')
			$metaType = 'C';
		switch ($metaType) {
			case 'C' :
				$sqlValue = $this->_Db->quoteString($value) . ', ';
				break;
			case 'X' :
				// special handling of empty CLOB values on oracle
				if ($this->_Db->AdoDb->dataProvider == 'oci8' && $type == 'CLOB')
					$sqlValue = ($value == OCI_EMPTY_CLOB ? $value : $this->_Db->quoteString($value)) . ', ';
				else
					$sqlValue = $this->_Db->quoteString($value) . ', ';
				break;
			case 'B' :
				// special handling of empty BLOB values on oracle
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

	/**
	 * Verify if a given value is empty: an empty string
	 * a NULL value or a 'null' string
	 *
	 * @param mixed $value Input value
	 * @return bool
	 * @access private
	 */
	function _isEmpty($value) {
		return (is_null($value) || (empty($value) && strlen($value) == 0) || $value === 'null');
	}
}