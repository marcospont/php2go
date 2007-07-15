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
import('php2go.util.AbstractList');

/**
 * Array data adapter
 *
 * Implementation of a data adapter that is able to read and navigate
 * through a PHP array.
 *
 * The arrays provided to load methods must be indexed numerically,
 * and each member of the first dimension (another array, an object or
 * a scalar variable) will be a record in the data set.
 *
 * The example below demonstrates how to use this class
 * to handle arrays of objects:
 * <code>
 * class person {
 *   var $firstName;
 *   var $lastName;
 *   function persion($firstName, $lastName) {
 *     $this->firstName = $firstName;
 *     $this->lastName = $lastName;
 *   }
 * }
 * $source = array();
 * $source[] = new person('John', 'Smith');
 * $source[] = new person('Mary', 'Smith');
 * $ds = DataSet::factory('array');
 * $ds->load($source);
 * print $ds->getField('firstName'); /* prints 'John' {@*}
 * $ds->moveNext();
 * print $ds->getField('lastName'); /* prints 'Smith' {@*}
 * </code>
 *
 * @package data
 * @subpackage adapter
 * @uses AbstractList
 * @uses ListIterator
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DataSetArray extends DataAdapter
{
	/**
	 * Iterator used to navigate through the array members
	 *
	 * @var object ListIterator
	 * @access private
	 */
	var $Iterator;

	/**
	 * Class constructor
	 *
	 * @param array $params Configuration parameters
	 * @return DataSetArray
	 */
	function DataSetArray($params=array()) {
		parent::DataAdapter($params);
	}

	/**
	 * Loads an array
	 *
	 * @param array $arr Data array
	 * @return bool
	 */
	function load($arr) {
		if (is_array($arr)) {
			if (!empty($arr)) {
				$DataList = new AbstractList($arr);
				$this->Iterator =& $DataList->iterator();
				$this->absolutePosition = 0;
				$this->recordCount = sizeof($arr);
				$this->_parseFields($this->Iterator->next());
				$this->eof = FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Loads a subset of an array
	 *
	 * @param int $offset Starting offset (zero-based)
	 * @param int $size Subset size
	 * @param array $arr Original array
	 * @return bool
	 */
	function loadSubSet($offset, $size, $arr) {
		if (is_array($arr)) {
			$content = $arr;
			if (empty($content)) {
				$this->recordCount = 0;
			} else {
				$subSet = array_slice($content, $offset, $size);
				if (sizeof($subSet) > 0) {
					$this->absolutePosition = 0;
					$this->recordCount = sizeof($subSet);
					$this->totalRecordCount = sizeof($content);
					$DataList = new AbstractList($subSet);
					$this->Iterator =& $DataList->iterator();
					$this->_parseFields($this->Iterator->next());
					$this->eof = FALSE;
				} else {
					$this->recordCount = 0;
				}
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Overrides parent class implementation in order to correctly
	 * fetch field values when records are objects
	 *
	 * @param string $fieldId Field name
	 * @return mixed
	 */
	function getField($fieldId) {
		if (array_key_exists($fieldId, $this->fields))
			return $this->fields[$fieldId];
		return NULL;
	}

	/**
	 * Move to a given position in the data set
	 *
	 * @param int $index Record index
	 * @return bool
	 */
	function move($index) {
		if (is_object($this->Iterator) && TypeUtils::isInteger($index)) {
			if ($this->Iterator->moveToIndex($index)) {
				$this->absolutePosition = $this->Iterator->getCurrentIndex();
				$this->_parseFields($this->Iterator->next());
				$this->eof = FALSE;
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Move to the next record, if existent
	 *
	 * @return bool
	 */
	function moveNext() {
		if (is_object($this->Iterator) && $this->Iterator->hasNext()) {
			$this->_parseFields($this->Iterator->next());
			$this->absolutePosition = $this->Iterator->getCurrentIndex();
			return TRUE;
		}
		$this->eof = TRUE;
		return FALSE;
	}

	/**
	 * Move to the previous record, if existent
	 *
	 * @return bool
	 */
	function movePrevious() {
		if (is_object($this->Iterator) && $this->getAbsolutePosition() > 0) {
			$this->_parseFields($this->Iterator->previous());
			$this->absolutePosition = $this->Iterator->getCurrentIndex();
			if ($this->eof())
				$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Free internal {@link Iterator}
	 */
	function close() {
		unset($this->Iterator);
	}

	/**
	 * Define fields, field names and field count, depending
	 * on the native type of a record
	 *
	 * This method is called inside {@link load} and
	 * {@link loadSubSet}, and determines the value of
	 * {@link recordType}, {@link fieldNames} and
	 * {@link fieldCount}.
	 *
	 * @param array $row Data set row
	 * @access private
	 */
	function _parseFields($row) {
		if (is_array($row)) {
			$this->fields = $row;
			$this->fieldNames = array_keys($row);
			$this->fieldCount = sizeof($this->fieldNames);
		} elseif (is_object($row)) {
			if (method_exists($row, 'toArray'))
				$this->fields = $row->toArray();
			else
				$this->fields = get_object_vars($row);
			$this->fieldNames = array_keys($this->fields);
			$this->fieldCount = sizeof($this->fieldNames);
		} else {
			$this->fields = $row;
			$this->fieldNames = array(0);
			$this->fieldCount = 1;
		}
	}
}
?>