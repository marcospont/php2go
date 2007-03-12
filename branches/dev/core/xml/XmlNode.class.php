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

import('php2go.base.AbstractNode');

/**
 * Default node name
 */
define('XML_NODE_DEFAULT_NAME',	'node');

/**
 * Implementation of a XML node
 *
 * This class is used by XmlParser and XmlRender classes to represent a node
 * in a XML tree. When parsing and creating XML trees, nodes will be instances
 * of XmlNode class.
 *
 * @package xml
 * @uses Db
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class XmlNode extends AbstractNode
{
	/**
	 * Namespace prefix
	 *
	 * @var string
	 */
	var $prefix;

	/**
	 * Local name
	 *
	 * @var string
	 */
	var $localName;

	/**
	 * CDATA section
	 *
	 * @var string
	 */
	var $value;

	/**
	 * Owner document
	 *
	 * @var XmlDocument
	 */
	var $ownerDocument;

	/**
	 * Class constructor
	 *
	 * @param string $nodeTag Tag name
	 * @param array $nodeAttrs Attributes
	 * @param array $nodeChildren Child nodes
	 * @param string $nodeValue CDATA section
	 * @return XmlNode
	 */
	function XmlNode($nodeTag, $nodeAttrs, $nodeChildren=NULL, $nodeValue=NULL) {
		parent::AbstractNode($nodeTag, $nodeAttrs, $nodeChildren);
		if (ereg('[a-zA-Z]+\:[a-zA-Z]+', $nodeTag))
			list($this->prefix, $this->localName) = explode(':', $nodeTag);
		$this->value = $nodeValue;
		$this->ownerDocument = NULL;
	}

	/**
	 * Get the node's tag name
	 *
	 * @return string
	 */
	function getTag() {
		return parent::getName();
	}

	/**
	 * Changes the node's tag name
	 *
	 * The $newTag parameter can be a simple tag name or a prefix:name pair:
	 * <code>
	 * $node->setTag('tag');
	 * $node->setTag('ns1:tag');
	 * </code>
	 *
	 * @param string $newTag New tag name
	 */
	function setTag($newTag) {
		parent::setName($newTag);
		if (ereg("[a-zA-Z]+\:[a-zA-Z]+", $newTag))
			list($this->prefix, $this->localName) = explode(':', $newTag);
	}

	/**
	 * Gets the namespace prefix of the node
	 *
	 * @return string
	 */
	function getPrefix() {
		return $this->prefix;
	}

	/**
	 * Sets the namespace prefix of the node
	 *
	 * @param string $newPrefix New prefix
	 */
	function setPrefix($newPrefix) {
		$this->prefix = $newPrefix;
		parent::setName($this->prefix . ':' . $this->localName);
	}

	/**
	 * Gets the local part of the node name
	 *
	 * @return string
	 */
	function getLocalName() {
		return $this->localName;
	}

	/**
	 * Returns the elements (including the node itself) whose name is $tagName
	 *
	 * @param string $tagName Tag name
	 * @return array
	 */
	function &getElementsByTagName($tagName) {
		$elements = array();
		$this->getNamedItem($tagName, $elements);
		return $elements;
	}

	/**
	 * Builds an array containing the elements identified by $name
	 *
	 * @param string $name Tag name
	 * @param array &$elements Used to return the found elements
	 */
	function getNamedItem($name, &$elements) {
		if (!is_array($elements))
			$elements = array();
		if ($name == '*' || $this->name == $name)
			$elements[] =& $this;
		if ($this->hasChildren())
			for ($i=0; $i<$this->childrenCount; $i++)
				$this->children[$i]->getNamedItem($name, $elements);
	}

	/**
	 * Builds an array of child nodes grouped by tag name
	 *
	 * @return array
	 */
	function getChildrenTagsArray() {
		if (!$this->children) {
			return FALSE;
		} else {
			$childrenArr = array();
			foreach($this->children as $child) {
				$childTag = $child->getTag();
				if (isset($childrenArr[$childTag])) {
					$tmp = $childrenArr[$childTag];
					if (is_array($tmp))
						$childrenArr[$childTag][] = $child;
					else
						$childrenArr[$childTag] = array($tmp, $child);
				} else
					$childrenArr[$childTag] = $child;

			}
			return $childrenArr;
		}
	}

	/**
	 * Verifies if the node has a CDATA section
	 *
	 * @return bool
	 */
	function hasData() {
		return (isset($this->value) && $this->value != NULL);
	}

	/**
	 * Gets the CDATA section of the node
	 *
	 * @return string
	 */
	function getData() {
		return $this->value;
	}

	/**
	 * Defines the CDATA section of the node
	 *
	 * @param string $value CDATA contents
	 */
	function setData($value) {
		$this->value = $value;
	}

	/**
	 * Gets the owner document of the node
	 *
	 * @return XmlDocument
	 */
	function &getOwnerDocument() {
		return $this->ownerDocument;
	}

	/**
	 * Sets the owner document of the node
	 *
	 * @param XmlDocument &$Document XML document
	 */
	function setOwnerDocument(&$Document) {
		if (TypeUtils::isInstanceOf($Document, 'XmlDocument'))
			$this->ownerDocument =& $Document;
	}

	/**
	 * Overrides parent class implementation to set the owner document of the node
	 *
	 * @param XmlNode $childNode New child node
	 * @return XmlNode
	 */
	function &addChild($childNode) {
		$Child =& parent::addChild($childNode);
		$Child->setOwnerDocument($this->ownerDocument);
		return $Child;
	}

	/**
	 * Add child nodes based on a SQL query
	 *
	 * @param string $queryString SQL query
	 * @param string $tagName Tag name to represent each database row
	 * @param string $connectionId Connection ID to be used
	 */
	function addFromQuery($queryString, $tagName=XML_NODE_DEFAULT_NAME, $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		$oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
		$Rs =& $Db->query($queryString);
		if ($Rs->recordCount() > 0) {
			while (!$Rs->EOF) {
				$this->addChild(new XmlNode($tagName, $Rs->fields));
				$Rs->moveNext();
			}
		}
		$Db->setFetchMode($oldMode);
	}

	/**
	 * Sort this node's children by a given attribute
	 *
	 * @param string $attributeName Attribute name
	 * @return bool
	 */
	function sortChildrenBy($attributeName) {
		$orderArray = array();
		$attributeArray = array();
		$childrenArray = array();
		for ($i=0; $i<$this->getChildrenCount(); $i++) {
			$child = $this->getChild($i);
			if (!isset($child->attrs[$attributeName])) {
				return FALSE;
			} else {
				$attributeArray[] = array($i, $child->attrs[$attributeName]);
				$orderArray[] = $child->attrs[$attributeName];
			}
		}
		array_multisort($orderArray, $attributeArray);
		reset($attributeArray);
		for ($i=0; $i<$this->getChildrenCount(); $i++)
			$childrenArray[$i] = $this->getChild($attributeArray[$i][0]);
		$this->children =& $childrenArray;
		return TRUE;
	}

	/**
	 * Creates a clone of the node
	 *
	 * @return XmlNode
	 */
	function &createClone() {
		$Clone = new XmlNode($this->name, $this->attrs, NULL, $this->value);
		return $Clone;
	}

	/**
	 * Generates the string representation of the node
	 *
	 * A recursive call is made for each child node.
	 *
	 * @param string $lineEnd Line end chars
	 * @param int $depth Current depth
	 * @param string $indent Indentation chars
	 * @return string
	 */
	function render($lineEnd='', $depth=0, $indent='') {
		$cdata = FALSE;
		$content  = str_repeat($indent, $depth) . '<' . $this->getTag() . $this->_renderAttributeString();
		if ($this->hasChildren() || $this->hasData()) {
			$content .= '>';
			if ($this->hasData()) {
				if (strlen($this->value) != strlen(htmlspecialchars($this->value)) && !preg_match("/^<!\[CDATA.*/", $this->value)) {
					$cdata = TRUE;
					$content .= $lineEnd . str_repeat($indent, $depth) . '<![CDATA[' . $this->value . ']]>' . $lineEnd;
				} else {
					$content .= $this->value;
				}
			}
			if ($this->hasChildren() && !$cdata)
				$content .= $lineEnd;
			for ($i=0; $i<$this->getChildrenCount(); $i++) {
				$content .= $this->children[$i]->render($lineEnd, $depth+1, $indent);
			}
			$content .= (($this->hasChildren() || $cdata) ? str_repeat($indent, $depth) : '') . '</' . $this->getTag() . '>' . $lineEnd;
		} else {
			$content .= '/>' . $lineEnd;
		}
		return $content;
	}

	/**
	 * Renders the attributes of the node
	 *
	 * @access private
	 * @return string
	 */
	function _renderAttributeString() {
		$buffer = '';
		foreach((array)$this->attrs as $attr => $value)
			$buffer .= " {$attr}=\"" . $this->_prepareValue($value) . "\"";
		return $buffer;
	}

	/**
	 * Escapes special chars on a given value
	 *
	 * @param string $value Input value
	 * @access private
	 * @return string
	 */
	function _prepareValue($value) {
		return str_replace(array('<','>','&'), array('&lt;', '&gt;', '&amp;'), stripslashes($value));
	}
}