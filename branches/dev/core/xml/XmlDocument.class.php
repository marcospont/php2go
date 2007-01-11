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

import('php2go.xml.XmlNode');
import('php2go.xml.XmlParser');

/**
 * XML declaration
 */
define('XML_DECLARATION', "<?xml version=\"%s\" encoding=\"%s\"?>");

/**
 * Builds XML documents or loads them from a XML file or string
 *
 * Examples:
 * <code>
 * /* create a simple XML tree {@*}
 * $doc = new XmlDocument();
 * $doc->DocumentElement = new XmlNode('root', array('attr'=>'value'));
 * $child =& $doc->DocumentElement->addChild(new XmlNode('child', array('attr'=>'value')));
 *
 * /* parsing a XML file {@*}
 * $doc = new XmlDocument();
 * if ($doc->parseXml('data.xml', T_BYFILE)) {
 *   $root =& $doc->getDocumentElement();
 *   for ($i=0; $i<$root->getChildrenCount(); $i++)
 *     print $root->children[$i]->getName() . "<br />";
 * }
 * </code>
 *
 * @package xml
 * @uses XmlNode
 * @uses XmlParser
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class XmlDocument extends PHP2Go
{
	/**
	 * XML declaration
	 *
	 * @var string
	 */
	var $xmlDecl = '';

	/**
	 * Document stylesheets
	 *
	 * @var array
	 */
	var $styleSheet = array();

	/**
	 * DOCTYPE settings and entries
	 *
	 * @var array
	 */
	var $doctype = array();

	/**
	 * Whether namespaces are enabled
	 *
	 * @var bool
	 */
	var $namespaceAware = FALSE;

	/**
	 * Document's root node
	 *
	 * @var XmlNode
	 */
	var $DocumentElement = NULL;

	/**
	 * Class constructor
	 *
	 * @return XmlDocument
	 */
	function XmlDocument() {
		parent::PHP2Go();
	}

	/**
	 * An alias for {@link getDocumentElement()}
	 *
	 * @return XmlNode
	 */
	function &getRoot() {
		return $this->DocumentElement;
	}

	/**
	 * Get the document's root node
	 *
	 * @return XmlNode
	 */
	function &getDocumentElement() {
		return $this->DocumentElement;
	}

	/**
	 * Adds a XML declaration in the document
	 *
	 * @param string $version XML version
	 * @param string $encoding Encoding
	 */
	function addXmlDeclaration($version='1.0', $encoding=NULL) {
		if (empty($encoding))
			$encoding = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->xmlDecl = sprintf(XML_DECLARATION, $version, $encoding);
	}

	/**
	 * Adds a stylesheet in the XML document
	 *
	 * @param string $link Stylesheet path
	 * @param bool $alternate Whether this is an alternate style
	 * @param string $type Content type
	 */
	function addStylesheet($link, $alternate=FALSE, $type='text/css') {
		$this->styleSheet[] = array('href'=>$link, 'type'=>$type, 'alternate'=>((bool)$alternate ? 'yes' : 'no'));
	}

	/**
	 * Set DOCTYPE source, path and entries
	 *
	 * @param string $uri URI
	 * @param string $id Public identifier
	 * @param string $entries Entries
	 */
	function setDoctype($uri, $id=NULL, $entries='') {
		if ($id != NULL) {
			$this->doctype['SOURCE'] = 'PUBLIC';
			$this->doctype['ID'] = " \"$id\"";
		} else {
			$this->doctype['SOURCE'] = 'SYSTEM';
			$this->doctype['ID'] = NULL;
		}
		$this->doctype['URI'] = "\"$uri\"";
		$this->doctype['ENTRIES'] = $entries;
	}

	/**
	 * Enable/disable support for namespaces
	 *
	 * @param bool $setting Flag value
	 */
	function setNamespaceAwareness($setting=TRUE) {
		$this->namespaceAware = (bool)$setting;
	}

	/**
	 * Builds a tree from a XML file or string
	 *
	 * @param string $xmlContent XML contents or file path
	 * @param string $srcType Source type ({@link T_BYFILE} or {@link T_BYVAR})
	 * @param string $srcEncoding Input encoding
	 * @param string $trgEncoding Output encoding
	 * @return bool
	 */
	function parseXml($xmlContent, $srcType=T_BYFILE, $srcEncoding=NULL, $trgEncoding=NULL) {
		$this->DocumentElement = NULL;
		$Parser = new XmlParser($this, $this->namespaceAware);
		return $Parser->parse($xmlContent, $srcType, $srcEncoding, $trgEncoding);
	}

	/**
	 * Converts the XML document into a XML string
	 *
	 * The {@link XmlNode::render()} method of each document node
	 * is called recursively.
	 *
	 * @param string $lineEnd Line end string
	 * @param string $indent Indentation string
	 * @return string XML string
	 */
	function render($lineEnd='', $indent='') {
		$result = '';
		if ($this->DocumentElement != NULL) {
			// XML declaration
			if (!empty($this->xmlDecl))
				$result .= $this->xmlDecl . $lineEnd;
			// generator comment
			$result .= "<!-- generator=\"PHP2Go Web Development Framework " . PHP2GO_VERSION . "\" -->\n";
			// stylesheets
			for ($i=0,$s=sizeof($this->styleSheet); $i<$s; $i++)
				$result .= "<?xml-stylesheet href=\"{$this->styleSheet[$i]['href']}\" type=\"{$this->styleSheet[$i]['type']}\" alternate=\"{$this->styleSheet[$i]['alternate']}\"?>" . $lineEnd;
			// doctype
			if (!empty($this->doctype)) {
				if (!empty($this->doctype['ENTRIES']))
					$result .= sprintf("<!DOCTYPE %s %s%s %s [ %s%s %s]>", $this->DocumentElement->getName(), $this->doctype['SOURCE'], $this->doctype['ID'], $this->doctype['URI'], $lineEnd, $this->doctype['ENTRIES'], $lineEnd) . $lineEnd;
				else
					$result .= sprintf("<!DOCTYPE %s %s%s %s>", $this->DocumentElement->getName(), $this->doctype['SOURCE'], $this->doctype['ID'], $this->doctype['URI']) . $lineEnd;
			}
			// nodes, starting from root recursively
			$result .= $this->DocumentElement->render($lineEnd, 0, $indent);
		}
		return $result;
	}
}
?>