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

import('php2go.xml.XmlDocument');
import('php2go.xml.XmlNode');

/**
 * Creates and renders a XML tree based on PHP values
 *
 * The XmlRender is able to convert PHP values (strings, numbers,
 * arrays, objects) to XML nodes. Finally, the generated XML tree
 * can be transformed into a XML string.
 *
 * Example:
 * <code>
 * $renderer = new XmlRender('root', array('attrib' => 'value'));
 * $renderer->setCharset('iso-8859-1');
 * $renderer->Document->setDoctype('SYSTEM', 'file.dtd');
 * $root =& $renderer->getRoot();
 * $root->addChild(new XmlNode('child', array('attrib' => 'value')));
 * $renderer->render('iso-8859-1');
 * $renderer->download('file_name.xml');
 * </code>
 *
 * @package xml
 * @uses TypeUtils
 * @uses XmlDocument
 * @uses XmlNode
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class XmlRender extends PHP2Go
{
	/**
	 * XML document's charset
	 *
	 * @var string
	 */
	var $charset;

	/**
	 * XML contents
	 *
	 * @var string
	 */
	var $content = '';

	/**
	 * Rendering options
	 *
	 * Indicates how objects, arrays and other PHP native
	 * types should be converted into XML nodes.
	 *
	 * @var array
	 */
	var $addOptions = array();

	/**
	 * XML document
	 *
	 * @var XmlDocument
	 */
	var $Document = NULL;

	/**
	 * Builds the root node of the XML tree
	 *
	 * @param string $rootTag Root tag name
	 * @param array $rootAttrs Root attributes
	 * @return XmlRender
	 */
	function XmlRender($rootTag, $rootAttrs=array()) {
		parent::PHP2Go();
		$this->charset = PHP2GO_DEFAULT_CHARSET;
		$this->Document = new XmlDocument();
		$this->Document->DocumentElement = new XmlNode($rootTag, $rootAttrs);
	}

	/**
	 * Get the tree's root node
	 *
	 * @return XmlNode
	 */
	function &getRoot() {
		return $this->Document->getRoot();
	}

	/**
	 * Set the document's charset
	 *
	 * @param string $charset Charset
	 */
	function setCharset($charset) {
		$this->charset = $charset;
	}

	/**
	 * Set default rendering options
	 *
	 * These options will be used when add methods are called.
	 * The list of available options are:
	 * # defaultNodeName: default name for XML nodes
	 * # typeHints: add type hints when rendering nodes
	 * # classAsNodeName: define the class name as node name, when adding objects
	 * # createArrayNode: must create a node representing the array itself (not only its members)
	 * # arrayEntryAsRepeat: create a sequence of nodes of the same name when the value of a hash key is a numeric array
	 * # attributeKey: array key that contains the attributes of a node
	 * # cdataKey: array key that contains the CDATA section of a node
	 *
	 * @param array $options Options
	 * @param bool $overwrite Override existent options
	 */
	function setAddOptions($options, $overwrite=FALSE) {
		if (TypeUtils::isHashArray($options)) {
			$this->addOptions = (!empty($this->addOptions) && !$overwrite ? array_merge($this->addOptions, $options) : $options);
		}
	}

	/**
	 * Generic method to add content in the XML tree
	 *
	 * Content will be added under the tree's root node
	 *
	 * @param mixed $value Content to be added
	 * @param array $options Add options
	 */
	function addContent($value, $options=array()) {
		$this->addContentAt($this->Document->DocumentElement, $value, $options);
	}

	/**
	 * Generic method to add content under a given node
	 *
	 * @param XmlNode &$Node Insert point
	 * @param mixed $value Content to be added
	 * @param array $options Add options
	 */
	function addContentAt(&$Node, $value, $options=array()) {
		$this->setAddOptions($options, FALSE);
		if (is_object($value))
			$this->_addObject($Node, $value, $options);
		elseif (is_array($value))
			$this->_addArray($Node, $value, $options);
		else
			$this->_addValue($Node, $value);
	}

	/**
	 * Adds a data set in the XML tree
	 *
	 * The data set is added under the tree's root node.
	 * <code>
	 * $xml = new XmlRender('rows');
	 * $xml->setAddOptions(array('defaultNodeName'=>'row'));
	 * $dataset =& DataSet::factory('db');
	 * $dataset->load('select * from my_table');
	 * $xml->addDataSet($dataset);
	 * $xml->toFile('my_table.xml');
	 * </code>
	 *
	 * @param DataSet $DataSet Loaded data set
	 * @param array $options Add options
	 */
	function addDataSet($DataSet, $options=array()) {
		if (TypeUtils::isInstanceOf($DataSet, 'DataSet') && $DataSet->getRecordCount() > 0) {
			$options['createArrayNode'] = TRUE;
			while (!$DataSet->eof()) {
				$this->_addArray($this->Document->DocumentElement, $DataSet->current(), $options);
				$DataSet->moveNext();
			}
		}
	}

	/**
	 * Renders the XML string
	 *
	 * @param string $lineEnd Line end chars
	 * @param string $indent Indentation chars
	 */
	function render($lineEnd='', $indent='') {
		$this->Document->addXmlDeclaration('1.0', $this->charset);
		$this->content = $this->Document->render($lineEnd, $indent);
	}

	/**
	 * Returns the generated XML string
	 *
	 * You must call {@link render()} before calling {@link getContent()},
	 * {@link download()} or {@link toFile()}.
	 *
	 * @return string
	 */
	function getContent() {
		return $this->content;
	}

	/**
	 * Displays the generated XML string, along with the download HTTP headers
	 *
	 * @param string $fileName File name
	 * @param bool $showHeaders If set to FALSE, HTTP download headers will be supressed
	 * @param string $mimeType Alternate MIME type
	 */
	function download($fileName, $showHeaders=TRUE, $mimeType='text/xml') {
		import('php2go.net.HttpResponse');
		if ($showHeaders && !headers_sent()) {
			HttpResponse::download($fileName, strlen($this->getContent()), $mimeType, 'inline');
			print $this->getContent();
		} else {
			print htmlspecialchars($this->getContent());
		}
	}

	/**
	 * Saves the XML string on a file
	 *
	 * @param string $fileName File path
	 * @return bool
	 */
	function toFile($fileName) {
		$content = $this->getContent();
		if (!empty($content)) {
			$fp = @fopen($fileName, 'w');
			if ($fp === FALSE) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			fputs($fp, $content);
			fclose($fp);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Internal method used to add an object in the XML tree
	 *
	 * @param XmlNode &$Node Insert point
	 * @param object $Object Object to be added
	 * @param array $options Add options
	 * @access private
	 */
	function _addObject(&$Node, $Object, $options=array()) {
		$options = (TypeUtils::isHashArray($options) ? array_merge($this->addOptions, $options) : $this->addOptions);
		$typeHints = (bool)$options['typeHints'];
		$defaultNodeName = (isset($options['defaultNodeName']) ? $options['defaultNodeName'] : XML_NODE_DEFAULT_NAME);
		$nodeName = ((bool)$options['classAsNodeName'] ? get_class($Object) : $defaultNodeName);
		$attributes = ($typeHints ? array('type' => 'object', 'class' => get_class($Object)) : array());
		$Child =& $Node->addChild(new XmlNode($nodeName, $attributes));
		$this->_addArray($Child, get_object_vars($Object), array('createArrayNode'=>FALSE));
	}

	/**
	 * Internal method used to add an array in the XML tree
	 *
	 * @param XmlNode &$Node Insert point
	 * @param array $array Array to be added
	 * @param array $options Add options
	 * @access private
	 */
	function _addArray(&$Node, $array, $options=array()) {
		$options = (TypeUtils::isHashArray($options) ? array_merge($this->addOptions, $options) : $this->addOptions);
		$typeHints = (bool)$options['typeHints'];
		$defaultNodeName = (isset($options['defaultNodeName']) ? $options['defaultNodeName'] : XML_NODE_DEFAULT_NAME);
		if (!isset($options['createArrayNode']) || $options['createArrayNode'] === TRUE) {
			if ($typeHints) {
				$attributes = array(
					'type' => 'array',
					'hash' => (TypeUtils::isHashArray($array) ? 1 : 0)
				);
			} else {
				$attributes = array();
			}
			$Child =& $Node->addChild(new XmlNode($defaultNodeName, $attributes));
		} else {
			$Child =& $Node;
		}
		if (TypeUtils::isHashArray($array)) {
			$arrayEntryAsRepeat = (bool)$options['arrayEntryAsRepeat'];
			foreach ($array as $key => $value) {				
				if (sizeof(($parts = explode(':', $key))) > 1) {
					$InnerChild =& $Child->addChild(new XmlNode($parts[0], array()));
					$opt = array('defaultNodeName' => $parts[1]);
					$this->addContentAt($InnerChild, $value, $opt);
					continue;
				}
				if (is_object($value)) {
					$opt = array('defaultNodeName' => $key, 'classAsNodeName' => FALSE);
					$this->_addObject($Child, $value, $opt);
				} elseif (isset($options['attributeKey']) && $key === $options['attributeKey']) {
					$Child->addAttributes($value);
				} elseif (isset($options['cdataKey']) && $key === $options['cdataKey']) {
					$Child->setData(strval($value));
				} elseif ($arrayEntryAsRepeat && is_array($value) && !TypeUtils::isHashArray($value)) {
					for ($i=0,$s=sizeof($value); $i<$s; $i++) {
						$InnerChild =& $Child->addChild(new XmlNode($key, array()));
						if (is_array($value[$i])) {
							$opt = array('createArrayNode' => FALSE);
							$this->_addArray($InnerChild, $value[$i], $opt);
						} else {
							$InnerChild->setData($value[$i]);
						}
					}
				} else {
					$attributes = ($typeHints ? array('type' => gettype($value)) : array());
					$InnerChild =& $Child->addChild(new XmlNode($key, $attributes));
					if (is_array($value)) {
						$this->_addArray($InnerChild, $value, array(), FALSE);
					} else {
						$InnerChild->setData($value);
					}
				}
			}
		} else {
			for ($i=0, $size=sizeof($array); $i<$size; $i++) {
				if (is_object($array[$i])) {
					$opt = array('classAsNodeName' => TRUE);
					$this->_addObject($Child, $array[$i], $opt);
				} else {
					$attributes = ($typeHints ? array('type' => gettype($array[$i])) : array());
					$InnerChild =& $Child->addChild(new XmlNode($defaultNodeName, $attributes));
					if (is_array($array[$i])) {
						$this->_addArray($InnerChild, $array[$i]);
					} else {
						$InnerChild->setData($array[$i]);
					}
				}
			}
		}
	}

	/**
	 * Internal method to add a string, boolean or numeric value in the XML tree
	 *
	 * @param XmlNode &$Node Insert point
	 * @param mixed $value Value to be added
	 * @param array $options Add options
	 */
	function _addValue(&$Node, $value, $options=array()) {
		$options = (TypeUtils::isHashArray($options) ? array_merge($this->addOptions, $options) : $this->addOptions);
		$typeHints = (bool)$options['typeHints'];
		$nodeName = (isset($this->addOptions['defaultNodeName']) ? $this->addOptions['defaultNodeName'] : XML_NODE_DEFAULT_NAME);
		$attributes = ($typeHints ? array('type' => gettype($value)) : array());
		$Node->addChild(new XmlNode($nodeName, $attributes, NULL, $value));
	}
}
?>