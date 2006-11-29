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

import('php2go.data.adapter.DataAdapter');
import('php2go.util.AbstractList');

/**
 * CSV data adapter
 * 
 * Implementation of a data adapter that is able to read and navigate
 * through a CSV (comma-separated values) file.
 * 
 * When loaded, the first line of the CSV file defines the field names 
 * of the data set, even if the file doesn't contain a header line.
 *
 * @package data
 * @subpackage adapter
 * @uses AbstractList
 * @uses ListIterator
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DataSetCsv extends DataAdapter 
{
	/**
	 * Iterator used to navigate through the records
	 *
	 * @var object ListIterator
	 * @access private
	 */
	var $Iterator;
	
	/**
	 * Class constructor
	 *
	 * @param array $params
	 * @return DataSetCsv
	 */
	function DataSetCsv($params=array()) {
		parent::DataAdapter($params);
	}
	
	/**
	 * Loads a CSV file
	 * 
	 * The file must be built so that the first line
	 * contain the field names, and the set of records
	 * start in the second line.
	 *
	 * @param string $fileName File path
	 * @return bool
	 */
	function load($fileName) {
		$content = @file($fileName);
		if (!$content)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		if (sizeof($content) > 1) {
			$this->absolutePosition = 0;
			$this->fieldNames = explode(',', str_replace(array("\"", "'"), array('', ''), trim($content[0])));
			$this->fieldCount = sizeof($this->fieldNames);
			array_shift($content);
			$this->recordCount = sizeof($content);
			$DataList = new AbstractList($content);
			$this->Iterator =& $DataList->iterator();
			$this->fields = $this->_buildRecord($this->Iterator->next());
			$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Loads a subset of a CSV file
	 *
	 * @param int $offset Startinf offset (zero-based)
	 * @param int $size Subset size
	 * @param string $fileName File path
	 * @return bool
	 */
	function loadSubSet($offset, $size, $fileName) {
		$content = @file($fileName);
		if (!$content)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		if (sizeof($content) > 1) {
			$this->absolutePosition = 0;
			$this->fieldNames = explode(',', str_replace(array("\"", "'"), array('', ''), $content[0]));
			$this->fieldCount = sizeof($this->fieldNames);
			array_shift($content);
			$subSet = array_slice($content, $offset, $size);
			if (sizeof($subSet) > 0) {
				$DataList = new AbstractList($subSet);
				$this->Iterator =& $DataList->iterator();				
				$this->recordCount = sizeof($subSet);
				$this->totalRecordCount = sizeof($content);				
				$this->fields = $this->_buildRecord($this->Iterator->next());
				$this->eof = FALSE;
				return TRUE;
			}
		}
		return FALSE;
	}	
	
	/**
	 * Move to a given position in the data set
	 *
	 * @param int $index
	 * @return bool
	 */
	function move($recordNumber) {
		if (is_object($this->Iterator) && TypeUtils::isInteger($recordNumber)) {
			if ($this->Iterator->moveToIndex($recordNumber)) {
				$this->absolutePosition = $this->Iterator->getCurrentIndex();
				$this->fields = $this->_buildRecord($this->Iterator->next());
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
			$this->fields = $this->_buildRecord($this->Iterator->next());
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
		if (is_object($this->Iterator) && $this->absolutePosition > 0) {
			$this->fields = $this->_buildRecord($this->Iterator->previous());
			$this->absolutePosition = $this->Iterator->getCurrentIndex();
			if ($this->eof)
				$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Parse fields from a raw CSV line
	 *
	 * @param string $fileLine Line from the CSV file
	 * @return array Parsed fields
	 * @access private
	 */	
	function _buildRecord($fileLine) {
		// remove string delimiter
		$preparedLineData = ereg_replace("\"|'", "", $fileLine);
		// split CSV line
		$lineArray = explode(',', $preparedLineData);
		$resultRecord = array();
		foreach($this->fieldNames as $index => $name)
			$resultRecord[$name] = isset($lineArray[$index]) ? $lineArray[$index] : '';
		return $resultRecord;
	}
}
?>