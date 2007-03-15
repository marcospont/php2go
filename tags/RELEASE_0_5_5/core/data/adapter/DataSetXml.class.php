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
import('php2go.xml.XmlDocument');

/**
 * XML records whose fields are inside CDATA sections of child nodes
 */
define('DS_XML_CDATA', 1);
/**
 * XML records whose fields are inside attributes
 */
define('DS_XML_ATTR', 2);

/**
 * XML data adapter
 *
 * Implementation of a data adapter that is able to read records
 * from a XML file, if this file respects a special structure,
 * and navigate through them.
 *
 * DataSetXml can parse 2 types of XML structures: in the first,
 * fields are nodes inside the row node, and field value is the
 * text child node; in the second, fields are attributes of the
 * row node. See examples of both types below:
 *
 * CDATA based ({@link DS_XML_CDATA}):
 * <code>
 * <dataset>
 *   <row>
 *     <field>value</field>
 *     <another_field>value</another_field>
 *   </row>
 * </dataset>
 * </code>
 *
 * Attribute based ({@link DS_XML_ATTR}):
 * <code>
 * <dataset>
 *   <row field="value" another_field="value"/>
 *   <row field="value2" another_field="value2"/>
 * </dataset>
 * </code>
 *
 * @package data
 * @subpackage adapter
 * @uses AbstractList
 * @uses ListIterator
 * @uses XmlDocument
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DataSetXml extends DataAdapter
{
	/**
	 * Iterator used to nagivate through the records
	 *
	 * @var object ListIterator
	 * @access private
	 */
	var $Iterator;

	/**
	 * Attributes of the root node of the XML tree
	 *
	 * @var array
	 * @access private
	 */
	var $rootAttrs;

	/**
	 * Struct type ({@link DS_XML_CDATA} or {@link DS_XML_ATTR})
	 *
	 * @var int
	 * @access private
	 */
	var $structType;

	/**
	 * Class constructor
	 *
	 * @param array $params Configuration parameters
	 * @return DataSetXml
	 */
	function DataSetXml($params=array()) {
		parent::DataAdapter($params);
	}

	/**
	 * Loads a XML file
	 *
	 * @param string $fileName File path
	 * @param int $structType Structure type ({@link DS_XML_CDATA} or {@link DS_XML_ATTR})
	 * @return bool
	 */
	function load($fileName, $structType=DS_XML_CDATA) {
		$this->structType = $structType;
		$XmlDocument = new XmlDocument();
		$XmlDocument->parseXml($fileName);
		$RootNode =& $XmlDocument->getRoot();
		$this->rootAttrs = $RootNode->getAttributes();
		if ($RootNode->hasChildren()) {
			$DataList = new AbstractList($RootNode->getChildNodes());
			$this->Iterator =& $DataList->iterator();
			$this->absolutePosition = 0;
			$this->recordCount = $RootNode->getChildrenCount();
			$this->fields = $this->_buildRecord($this->Iterator->next());
			$this->fieldNames = array_keys($this->fields);
			$this->fieldCount = sizeof($this->fieldNames);
			$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Loads a subset of a XML file
	 *
	 * @param int $offset Starting offset (zero-based)
	 * @param int $size Subset size
	 * @param string $fileName File path
	 * @param int $structType Structure type ({@link DS_XML_CDATA} or {@link DS_XML_ATTR})
	 * @return bool
	 */
	function loadSubSet($offset, $size, $fileName, $structType=DS_XML_CDATA) {
		$this->structType = $structType;
		$XmlDocument = new XmlDocument();
		$XmlDocument->parseXml($fileName);
		$RootNode =& $XmlDocument->getRoot();
		$subSet = array_slice($RootNode->getChildNodes(), $offset, $size);
		if ($RootNode->hasChildren() && sizeof($subSet) > 0) {
			$DataList = new AbstractList($subSet);
			$this->Iterator =& $DataList->iterator();
			$this->absolutePosition = 0;
			$this->recordCount = sizeof($subSet);
			$this->totalRecordCount = $RootNode->getChildrenCount();
			$this->fields = $this->_buildRecord($this->Iterator->next());
			$this->fieldNames = array_keys($this->fields);
			$this->fieldCount = sizeof($this->fieldNames);
			$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get attributes of the XML root
	 *
	 * @return array
	 */
	function getRootAttributes() {
		return (isset($this->rootAttrs) ? $this->rootAttrs : NULL);
	}

	/**
	 * Move cursor to a given position
	 *
	 * @param int $index
	 * @return bool
	 */
	function move($index) {
		if (is_object($this->Iterator) && TypeUtils::isInteger($index)) {
			if ($this->Iterator->moveToIndex($index)) {
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
	 * Free internal {@link Iterator}
	 */
	function close() {
		unset($this->Iterator);
	}

	/**
	 * Parse fields from a xml node
	 *
	 * @param XmlNode $RecordNode Record read from the XML file
	 * @return array Parsed fields
	 */
	function _buildRecord($RecordNode) {
		switch ($this->structType) {
			// Attribute based records
			case DS_XML_ATTR :
				return $RecordNode->getAttributes();
			// CDATA based records
			default :
				$result = array();
				for ($i=0,$s=$RecordNode->getChildrenCount(); $i<$s; $i++) {
					$Child =& $RecordNode->getChild($i);
					$result[$Child->getName()] = $Child->getData();
				}
				return $result;
		}
	}
}
?>