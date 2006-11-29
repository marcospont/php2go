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

/**
 * Abstract data adapter
 * 
 * This class is the base for all data adapters used by {@link DataSet}
 * and its child classes. All load, fetch and navigation operations
 * are implemented in the adapter classes.
 * 
 * @package data
 * @subpackage adapter
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 * @abstract
 */
class DataAdapter extends PHP2Go
{
	/**
	 * Field count
	 *
	 * @var int
	 */
	var $fieldCount = 0;
	
	/**
	 * Field names of the data set
	 *
	 * @var array
	 */
	var $fieldNames = array();
	
	/**
	 * Used to fetch records
	 *
	 * @var array
	 */
	var $fields = array();
	
	/**
	 * Current cursor position
	 *
	 * @var int
	 */
	var $absolutePosition = 0;
	
	/**
	 * Indicates end of data set was reached
	 *
	 * @var bool
	 */
	var $eof = TRUE;
	
	/**
	 * Number of records in the data set
	 *
	 * @var int
	 */
	var $recordCount = 0;
	
	/**
	 * Total number of records, when subsets are used
	 *
	 * @var int
	 */
	var $totalRecordCount = 0;
	
	/**
	 * Adapter parameters
	 *
	 * @var array
	 * @access private
	 */
	var $params = array();
	
	/**
	 * Class constructor
	 * 
	 * Must be explictly called by child adapters
	 *
	 * @param array $params Configuration arguments
	 * @return DataAdapter
	 */
	function DataAdapter($params=array()) {
		parent::PHP2Go();
		if ($this->isA('DataAdapter', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'DataAdapter'), E_USER_ERROR, __FILE__, __LINE__);
		$this->params = TypeUtils::toArray($params);
	}
	
	/**
	 * Get adapter type
	 *
	 * @return string
	 */
	function getType() {
		$className = parent::getClassName();
		switch (strtolower($className)) {
			case 'datasetdb' :
				return 'db';
			case 'datasetcsv' :
				return 'csv';
			case 'datasetxml' :
				return 'xml';
			case 'datasetarray' :
				return 'array';
			default :
				return NULL;
		}
	}
	
	/**
	 * Get an adapter parameter
	 *
	 * @param string $param Parameter name
	 * @return mixed
	 */
	function getParameter($param) {
		return (isset($this->params[$param]) ? $this->params[$param] : NULL);
	}
	
	/**
	 * Set an adapter parameter
	 *
	 * @param string $param Name
	 * @param mixed $value Value
	 */
	function setParameter($param, $value) {
		$this->params[$param] = $value;
	}
	
	/**#@+
	 * Must be implemented by child adapters
     * 
     * @abstract
     */
	function load($index) {
	}
	function loadSubSet($offset, $size) {		
	}
	/**#@-*/
	
	/**
	 * Get number of fields/columns of the data set
	 *
	 * @return int
	 */
	function getFieldCount() {
		return $this->fieldCount;
	}
	
	/**
	 * Get the names of the fields/columns of the data set
	 *
	 * @return array
	 */
	function getFieldNames() {
		return $this->fieldNames;
	}
	
	/**
	 * Get a field given its name
	 *
	 * @param string $fieldId Field name
	 * @return mixed
	 */
	function getField($fieldId) {
		return (array_key_exists($fieldId, $this->fields) ? $this->fields[$fieldId] : NULL);
	}
	
	/**
	 * Get current cursor position
	 *
	 * @return int
	 */
	function getAbsolutePosition() {
		return $this->absolutePosition;
	}
	
	/**
	 * Get number of records in the data set
	 *
	 * @return int
	 */
	function getRecordCount() {
		return $this->recordCount;
	}
	
	/**
	 * Return current record
	 *
	 * @return array
	 */
	function current() {
		return $this->fields;
	}
	
	/**
	 * Check if the end of the data set was reached
	 *
	 * @return array
	 */
	function eof() {
		return $this->eof;
	}	

	/**
	 * Fetches the record pointed by current cursor position,
	 * and increments cursor position
	 *
	 * @return mixed
	 */
	function fetch() {
		if (!$this->eof) {
			$dataArray = $this->fields;
			$this->moveNext();
			return $dataArray;
		}
		return FALSE;
	}
	
	/**
	 * Fetches the record pointed by internal cursor into a
	 * given variable, and increments cursor position
	 *
	 * @param mixed &$dataArray Variable to copy record data
	 * @return bool
	 */
	function fetchInto(&$dataArray) {
		if (!$this->eof) {
			$dataArray = $this->fields;
			$this->moveNext();
			return TRUE;
		}
		return FALSE;
	}	
	
	/**#@+
	 * Must be implemented by child adapters
     * 
     * @abstract
     */
	function move($index) {
		return FALSE;
	}
	function movePrevious() {
		return FALSE;
	}
	function moveNext() {
		return FALSE;
	}
	/**#@-*/
	
	/**
	 * Move internal pointer to the first record
	 *
	 * @return bool
	 */
	function moveFirst() {
		return ($this->getAbsolutePosition() == 0 ? TRUE : $this->move(0));
	}
	
	/**
	 * Move internal pointer to the last record
	 *
	 * @return bool
	 */
	function moveLast() {
		return $this->move($this->getRecordCount() - 1);
	}	
}
?>