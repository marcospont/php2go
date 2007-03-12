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

import('php2go.xml.XmlNode');

/**
 * XML parser implementation based on the PHP's expat parser
 *
 * Builds a tree of objects from a XML file or string. The objects
 * are instances of the {@link XmlNode} class, which in turn extends
 * {@link AbstractNode}. Tree nodes contains methods inspired on the
 * DOM model.
 *
 * @package xml
 * @uses XmlNode
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class XmlParser extends PHP2Go
{
	/**
	 * Included files
	 *
	 * @var array
	 */
	var $includedFiles = array();

	/**
	 * Indicates if namespaces are enabled for this parser
	 *
	 * @var bool
	 */
	var $namespaceAware = FALSE;

	/**
	 * Owner document
	 *
	 * @var XmlDocument
	 */
	var $Document = NULL;

	/**
	 * Class constructor
	 *
	 * @param XmlDocument &$Document
	 * @param bool $namespace Whether namespaces should be parsed
	 * @return XmlParser
	 */
	function XmlParser(&$Document, $namespace=FALSE) {
		parent::PHP2Go();
		if (!function_exists('xml_parser_create'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'xml'), E_USER_ERROR, __FILE__, __LINE__);
		$this->Document =& $Document;
		$this->namespaceAware = (bool)$namespace;
	}

	/**
	 * Static method used to create an expat parser
	 *
	 * @param string &$xmlSource XML source
	 * @param string $srcEncoding Input encoding
	 * @param array $optionFlags Option flags
	 * @param bool $namespace Namespace awareness
	 * @return resource
	 * @static
	 */
	function createParser(&$xmlSource, $srcEncoding=NULL, $optionFlags=array(), $namespace=FALSE) {
		$validEncodings = array('iso-8859-1', 'us-ascii', 'utf-8');
		if (empty($srcEncoding)) {
			$matches = array();
			if (preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/m', $xmlSource, $matches))
				$srcEncoding = strtolower($matches[1]);
			else
				$srcEncoding = 'utf-8';
		}
		if (!empty($srcEncoding) && !in_array(strtolower($srcEncoding), $validEncodings)) {
			if (function_exists('iconv')) {
				$xmlSource = iconv($srcEncoding, 'utf-8', $xmlSource);
				$srcEncoding = 'utf-8';
			}
			elseif (function_exists('mb_convert_encoding')) {
				$xmlSource = mb_convert_encoding($xmlSource, 'utf-8', $srcEncoding);
				$srcEncoding = 'utf-8';
			}
			else {
				$srcEncoding = NULL;
			}
		}
		if ($namespace)
			$parser = (TypeUtils::isNull($srcEncoding, TRUE) ? xml_parser_create_ns() : xml_parser_create_ns($srcEncoding));
		else
			$parser = (TypeUtils::isNull($srcEncoding, TRUE) ? xml_parser_create() : xml_parser_create($srcEncoding));
		foreach ($optionFlags as $code => $value)
			xml_parser_set_option($parser, $code, $value);
		return $parser;
	}

	/**
	 * Parses a XML file or string
	 *
	 * @param string $xmlContent XML source
	 * @param int $srcType Source type ({@link T_BYFILE} or {@link T_BYVAR})
	 * @param string $srcEncoding Input encoding
	 * @param string $trgEncoding Output encoding
	 * @return bool
	 */
	function parse($xmlContent, $srcType=T_BYFILE, $srcEncoding=NULL, $trgEncoding=NULL) {
		if ($srcType == T_BYFILE) {
			$this->includedFiles[] = $xmlContent;
			$xmlData = @file_get_contents($xmlContent);
			if (!$xmlData)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $xmlContent), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$xmlData = $xmlContent;
		}
		$xmlData = preg_replace("/>\s+</", "><", $xmlData);
		return ($this->_parseExternalEntities($xmlData) && $this->_parseXmlString($xmlData, $srcEncoding, $trgEncoding));
	}

	/**
	 * Parses external entities declared inside a XML string
	 *
	 * External entities are resolved by the XmlParser class before the
	 * XML string is parsed. The syntax of an external entity follows:
	 * <code>
	 * <?xml version="1.0" encoding="iso-8859-1"?>
	 * <!ENTITY entity_name SYSTEM "path/to/entity_file.xml">
	 * <xml_root>
	 *   <xml_child>
	 *   </xml_child>
	 *   &entity_name;
	 * </xml_root>
	 * </code>
	 *
	 * @param string &$xmlSource XML source
	 * @access private
	 * @return bool
	 */
	function _parseExternalEntities(&$xmlSource) {
		$matches = array();
		if (preg_match_all('/<!ENTITY[ ](%[ ])?([a-zA-Z0-9_]+)[ ]SYSTEM[ ]\"([^\"]+)\"/', $xmlSource, $matches, PREG_SET_ORDER)) {
			for ($i=0, $s=sizeof($matches); $i<$s; $i++) {
				if (!in_array($matches[$i][3], $this->includedFiles)) {
					if ($fileData = @file_get_contents($matches[$i][3])) {
						$this->_parseExternalEntities($fileData);
						$xmlSource = ereg_replace("(&|%){$matches[$i][2]};", $fileData, $xmlSource);
					} else {
						return FALSE;
					}
				} else {
					$xmlSource = eregi_replace("(&|%){$matches[$i][2]};", "", $xmlSource);
				}
			}
		}
		return TRUE;
	}

	/**
	 * Parses a given XML string
	 *
	 * Internally uses {@link xml_parse_into_struct()} to transform the
	 * XML string into a tree of {@link XmlNode} objects.
	 *
	 * @param string $xmlSource XML source
	 * @param string $srcEncoding Input encoding
	 * @param string $trgEncoding Output encoding
	 * @access private
	 * @return bool
	 */
	function _parseXmlString($xmlSource, $srcEncoding, $trgEncoding) {
		$i = 0;
		$cdata = '';
		$parserVals = array();
		$parserIdx = array();
		$parserOptions = array();
		if (!empty($trgEncoding))
			$parserOptions[XML_OPTION_TARGET_ENCODING] = $trgEncoding;
		$parser = XmlParser::createParser($xmlSource, $srcEncoding, $parserOptions, $this->namespaceAware);
		if (!@xml_parse_into_struct($parser, $xmlSource, &$parserVals, &$parserIdx)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_XML_PARSE', array(xml_error_string(xml_get_error_code($parser)), xml_get_current_line_number($parser), xml_get_current_column_number($parser))), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$this->Document->DocumentElement = new XmlNode($parserVals[$i]['tag'], @$parserVals[$i]['attributes'], $this->_getChildren($parserVals, $i, $cdata), @$parserVals[$i]['value']);
		$this->Document->DocumentElement->value .= $cdata;
		xml_parser_free($parser);
		return TRUE;
	}

	/**
	 * Recursive method used to build the nodes tree
	 *
	 * @param array $vals Parsed values
	 * @param int &$i Current parser index
	 * @param string &$cdataBuffer Used to collect CDATA contents
	 * @return array Parsed child nodes
	 * @access private
	 */
	function _getChildren($vals, &$i, &$cdataBuffer) {
		$children = array();
		$buffer = '';
		$lastNode = NULL;
		while (++$i < sizeof($vals)) {
			switch ($vals[$i]['type']) {
				case 'cdata':
					$buffer .= $vals[$i]['value'];
					break;
				case 'complete':
					array_push($children, new XmlNode($vals[$i]['tag'], (isset($vals[$i]['attributes']) ? $vals[$i]['attributes'] : NULL), NULL, (isset($vals[$i]['value']) ? $vals[$i]['value'] : NULL)));
					break;
				case 'open':
					$nodeBuffer = '';
					$Node = new XmlNode($vals[$i]['tag'], (isset($vals[$i]['attributes']) ? $vals[$i]['attributes'] : NULL), $this->_getChildren($vals, $i, $nodeBuffer), (isset($vals[$i]['value']) ? $vals[$i]['value'] : NULL));
					if (!empty($nodeBuffer)) {
						$Node->addChild(new XmlNode('#cdata-section', NULL, NULL, $nodeBuffer));
						$Node->value = $nodeBuffer;
					}
					$children[sizeof($children)] = $Node;
					break;
				case 'close':
					$cdataBuffer = $buffer;
					return $children;
			}
		}
		return TRUE;
	}
}
?>