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

import('php2go.data.adapter.DataAdapter');

/**
 * DB data adapter
 *
 * Implementation of a data adapter that is able to read and navigate
 * through a set of records returned by a database query or cursor.
 *
 * The $stmt argument passed to {@link load()} and {@link loadSubSet()}
 * methods can be either an SQL string or an array returned by
 * {@link Db::prepare()}. Below you can see some use cases:
 *
 * <code>
 * /* simple navigation example using {@link fetch()} {@*}
 * $ds =& DataSet::factory('db');
 * $ds->load("select * from users");
 * while ($row = $ds->fetch()) {
 *   print $ds->getField('name');
 * }
 *
 * /* using {@link eof()} and {@link moveNext()} and a different connection ID {@*}
 * $ds =& DataSet::factory('db', array('connectionId'=>'CONN_ID'));
 * $ds->load("select * from table");
 * while (!$ds->eof()) {
 *   $row = $ds->current();
 *   $ds->moveNext();
 * }
 * </code>
 *
 * @package data
 * @subpackage adapter
 * @uses Db
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DataSetDb extends DataAdapter
{
	/**
	 * ADODb recordset used to navigate and fetch records
	 *
	 * @var ADORecordSet
	 */
	var $RecordSet = NULL;

	/**
	 * Class constructor
	 *
	 * @param array $params Configuration parameters
	 * @return DataSetDb
	 */
	function DataSetDb($params=array()) {
		parent::DataAdapter($params);
	}

	/**
	 * Loads data using an SQL query, prepared statement or stored procedure
	 *
	 * Examples:
	 * <code>
	 * $ds->load("select * from users where status = ?", array($status));
	 * $ds->load("package.proc_name(:STATUS, :CURSOR)", array('STATUS'=>$status), TRUE, 'CURSOR');
	 * $ds->load("proc_get_users(?)", array($status), TRUE);
	 * </code>
	 *
	 * @param mixed $stmt SQL query, stored procedure call or prepared statement
	 * @param array $bindVars Bind vars
	 * @param bool $isProcedure Whether $stmt is a stored procedure call
	 * @param string $cursorName Cursor name used by $stmt
	 * @return bool
	 */
	function load($stmt, $bindVars=FALSE, $isProcedure=FALSE, $cursorName=NULL) {
		// open connection and prepare statement, if not prepared
		$Db =& Db::getInstance(@$this->params['connectionId']);
		$Db->setDebug(@$this->params['debug']);
		if (is_string($stmt))
			$stmt = $Db->prepare(($isProcedure ? $Db->getProcedureSQL($stmt) : $stmt), $isProcedure);
		// execute statement
		$oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
		$this->RecordSet =& $Db->execute($stmt, $bindVars, ($isProcedure ? $cursorName : NULL));
		$Db->setFetchMode($oldMode);
		// set class properties
		if ($this->RecordSet) {
			$this->absolutePosition =& $this->RecordSet->_currentRow;
			$this->fields =& $this->RecordSet->fields;
			$this->fieldCount = $this->RecordSet->fieldCount();
			$this->eof =& $this->RecordSet->EOF;
			$this->recordCount = $this->RecordSet->recordCount();
			$this->totalRecordCount = $this->recordCount;
			$this->_buildFieldNames();
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Loads a subset of data from an SQL query, prepared
	 * statement or procedure call
	 *
	 * When $stmt is a procedure call, it <b>must</b> accept 3 mandatory arguments:
	 * # record count (to be used by the procedure to return the total record count, ignoring offset and size)
	 * # offset (starting offset of the requested subset)
	 * # size (subset size)
	 *
	 * Examples:
	 * <code>
	 * $ds->loadSubSet(0, 30, "select * from table where active = ?", array($active));
	 * $ds->loadSubSet(30, 30, "proc_name(?, ?, ?)", array(), TRUE);
	 * $ds->loadSubSet(0, 30, "package.proc(:RECORD_COUNT, :OFFSET, :SIZE, :CURSOR)", array(), TRUE, 'CURSOR');
	 * </code>
	 *
	 * @param int $offset Starting offset (zero-based)
	 * @param int $size Subset size
	 * @param mixed $stmt SQL query, stored procedure call or prepared statement
	 * @param array $bindVars Bind vars
	 * @param bool $isProcedure Whether $stmt is a procedure call
	 * @param string $cursorName Cursor name used by $stmt
	 * @return bool
	 */
	function loadSubSet($offset, $size, $stmt, $bindVars=FALSE, $isProcedure=FALSE, $cursorName=NULL) {
		// open connection and prepare statement, if not prepared
		$Db =& Db::getInstance(@$this->params['connectionId']);
		$Db->setDebug(@$this->params['debug']);
		if (TypeUtils::isString($stmt))
			$stmt = $Db->prepare(($isProcedure ? $Db->getProcedureSQL($stmt) : $stmt), $isProcedure);
		// execute statement
		$oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
		if ($isProcedure) {
			$Db->bind($stmt, &$this->totalRecordCount, 'RECORD_COUNT');
			$Db->bind($stmt, $offset, 'OFFSET');
			$Db->bind($stmt, $size, 'SIZE');
			$this->RecordSet =& $Db->execute($stmt, $bindVars, ($isProcedure ? $cursorName : NULL));
		} else {
			$optimize = (isset($this->params['optimizeCount']) ? (bool)$this->params['optimizeCount'] : TRUE);
			$this->totalRecordCount = $Db->getCount($stmt, $bindVars, $optimize);
			$this->RecordSet =& $Db->limitQuery((TypeUtils::isArray($stmt) ? $stmt[0] : $stmt), $size, $offset, TRUE, $bindVars);
		}
		$Db->setFetchMode($oldMode);
		// set class properties
		if ($this->RecordSet) {
			$this->absolutePosition =& $this->RecordSet->_currentRow;
			$this->fields =& $this->RecordSet->fields;
			$this->fieldCount = $this->RecordSet->fieldCount();
			$this->eof =& $this->RecordSet->EOF;
			$this->recordCount = $this->RecordSet->recordCount();
			$this->_buildFieldNames();
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Overrides parent implementation to call fetchRow
	 * method of the internal {@link RecordSet}
	 *
	 * @return array
	 */
	function fetch() {
		if (is_object($this->RecordSet))
			return $this->RecordSet->fetchRow();
		return array();
	}

	/**
	 * Overrides parent implementation to call fetchInto
	 * method of the internal {@link RecordSet}
	 *
	 * @param array $dataArray Variable to copy record data
	 * @return bool
	 */
	function fetchInto(&$dataArray) {
		if (is_object($this->RecordSet))
			return $this->RecordSet->fetchInto($dataArray);
		return FALSE;
	}

	/**
	 * Move cursor to a given position
	 *
	 * @param int $index
	 * @return bool
	 */
	function move($index) {
		if (is_object($this->RecordSet) && TypeUtils::isInteger($index))
			return $this->RecordSet->move($index);
		return FALSE;
	}

	/**
	 * Move to the next record, if existent
	 *
	 * @return bool
	 */
	function moveNext() {
		if (is_object($this->RecordSet))
			return $this->RecordSet->moveNext();
		return FALSE;
	}

	/**
	 * Move to the previous record, if existent
	 *
	 * @return bool
	 */
	function movePrevious() {
		return ($this->absolutePosition > 1 && is_object($this->RecordSet) ? $this->RecordSet->move($this->absolutePosition-1) : FALSE);
	}

	/**
	 * Closes internal {@link RecordSet}
	 */
	function close() {
		if (isset($this->RecordSet) && is_object($this->RecordSet)) {
			$this->RecordSet->close();
			unset($this->RecordSet);
		}
	}

	/**
	 * Parse field names using the meta data provided by {@link RecordSet}
	 *
	 * @access private
	 */
	function _buildFieldNames() {
		$this->fieldNames = array();
		if (is_object($this->RecordSet)) {
			for ($i=0, $s=$this->RecordSet->fieldCount(); $i<$s; $i++) {
				$FieldObject =& $this->RecordSet->fetchField($i);
				$this->fieldNames[] = $FieldObject->name;
			}
		}
	}
}
?>
