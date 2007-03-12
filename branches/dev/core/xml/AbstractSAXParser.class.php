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

import('php2go.xml.XmlParser');

/**
 * Abstract implementation of a SAX parser
 *
 * This implementation is based on the expat parser bundled with PHP. The class
 * maps all events to methods. These methods must be implemented by child classes.
 *
 * @package xml
 * @uses XmlParser
 */
class AbstractSAXParser extends PHP2Go
{
	/**
	 * Encoding to be used by the XML parser
	 *
	 * @var string
	 * @access private
	 */
	var $srcEncoding = NULL;

	/**
	 * Whether namespaces should be parsed
	 *
	 * @var bool
	 * @access private
	 */
	var $namespaceAware = FALSE;

	/**
	 * Preserve whitespace chars found in the XML file
	 *
	 * @var bool
	 * @access private
	 */
	var $preserveWhitespace = FALSE;

	/**
	 * Parser options
	 *
	 * @var array
	 * @access private
	 */
	var $parserOptions = array();

	/**
	 * Class constructor
	 *
	 * @return AbstractSAXParser
	 */
	function AbstractSAXParser() {
		parent::PHP2Go();
		if ($this->isA('AbstractSAXParser', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'AbstractSAXParser'), E_USER_ERROR, __FILE__, __LINE__);
		if (!function_exists('xml_parser_create'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'xml'), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Set parser input encoding
	 *
	 * @param string $encoding Encoding
	 */
	function setSourceEncoding($encoding) {
		$this->srcEncoding = $encoding;
	}

	/**
	 * Set parser output encoding
	 *
	 * @param string $encoding Output encoding
	 */
	function setTargetEncoding($encoding) {
		$this->parserOptions[XML_OPTION_TARGET_ENCODING] = $encoding;
	}

	/**
	 * Set an option of the XML parser
	 *
	 * For more details, please read the documentation of the
	 * {@link xml_parser_set_option()} function.
	 *
	 * @param int $option Option
	 * @param mixed $value Value
	 */
	function setParserOption($option, $value) {
		$this->parserOptions[$option] = $value;
	}

	/**
	 * Enable/disable parsing of namespaces
	 *
	 * @param bool $setting Flag value
	 */
	function setNamespaceAwareness($setting=TRUE) {
		$this->namespaceAware = (bool)$setting;
	}

	/**
	 * Enable/disable preservation of whitespace chars
	 *
	 * @param bool $setting Flag value
	 */
	function setPreserveWhitespace($setting) {
		$this->preserveWhitespace = (bool)$setting;
	}

	/**
	 * Parses XML content
	 *
	 * @uses XmlParser::createParser()
	 * @param string $xmlContent XML contents or file path
	 * @param int $srcType Source type: {@link T_BYFILE} or {@link T_BYVAR}
	 * @return bool
	 */
	function parse($xmlContent, $srcType=T_BYFILE) {
		if ($srcType == T_BYFILE)
			$xmlContent = file_get_contents($xmlContent);
		if (!$this->preserveWhitespace)
			$xmlContent = eregi_replace(">[[:space:]]+<", "><", $xmlContent);
		// creates the XML parser
		$parser = XmlParser::createParser(
			$xmlContent, $this->srcEncoding,
			$this->parserOptions, $this->namespaceAware
		);
		// setup event handlers
		xml_set_object($parser, $this);
		if ($this->namespaceAware) {
			xml_set_element_handler($parser, 'startElementNS', 'endElement');
			xml_set_start_namespace_decl_handler($parser, 'startNamespace');
			xml_set_end_namespace_decl_handler($parser, 'endNamespace');
		} else {
			xml_set_element_handler($parser, 'startElement', 'endElement');
		}
		xml_set_character_data_handler($parser, 'characterData');
		xml_set_notation_decl_handler($parser, 'notationDecl');
		xml_set_processing_instruction_handler($parser, 'processingInstruction');
		xml_set_external_entity_ref_handler($parser, 'externalEntityRef');
		xml_set_unparsed_entity_decl_handler($parser, 'unparsedEntityDecl');
		$result = TRUE;
		if (!xml_parse($parser, $xmlContent)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_XML_PARSE', array(xml_error_string(xml_get_error_code($parser)), xml_get_current_line_number($parser), xml_get_current_column_number($parser))), E_USER_ERROR, __FILE__, __LINE__);
			$result = FALSE;
		}
		xml_parser_free($parser);
		return $result;
	}

	/**
	 * Handles the start of a XML element
	 *
	 * @param resource $parser XML parser
	 * @param string $name Element name
	 * @param array $attrs Element attributes
	 * @abstract
	 */
	function startElement($parser, $name, $attrs) {
	}

	/**
	 * Handles the start of a XML element, when namespaces parsing is enabled
	 *
	 * @param resource $parser XML parser
	 * @param string $name Element name
	 * @param array $attrs Element attributes
	 * @abstract
	 */
	function startElementNS($parser, $name, $attrs) {
	}

	/**
	 * Handles the end of a XML element
	 *
	 * @param resource $parser XML parser
	 * @param string $name Element name
	 * @abstract
	 */
	function endElement($parser, $name) {
	}

	/**
	 * Handles a block of CDATA
	 *
	 * @param resource $parser XML parser
	 * @param string $data Character data
	 * @abstract
	 */
	function characterData($parser, $data) {
	}

	/**
	 * Handles the start of a namespace declaration
	 *
	 * @param resource $parser XML parser
	 * @param string $prefix NS prefix
	 * @param string $uri NS URI
	 * @abstract
	 */
	function startNamespace($parser, $prefix, $uri) {
	}

	/**
	 * Handles the end of a namespace declaration
	 *
	 * @param resource $parser XML parser
	 * @param string $prefix NS prefix
	 * @abstract
	 */
	function endNamespace($parser, $prefix) {
	}

	/**
	 * Handles a notation declaration
	 *
	 * @param resource $parser XML parser
	 * @param string $notationName Notation name
	 * @param string $base Base for resolving the system identifier (system_id) of the notation declaration
	 * @param string $systemId System identifier
	 * @param string $publicId Public identifier
	 * @abstract
	 */
	function notationDecl($parser, $notationName, $base, $systemId, $publicId) {
	}

	/**
	 * Handles a processing instruction
	 *
	 * @param resource $parser XML parser
	 * @param string $target Processing instruction target
	 * @param string $data Instruction data
	 * @abstract
	 */
	function processingInstruction($parser, $target, $data) {
	}

	/**
	 * Handles a reference to an external entity
	 *
	 * @param resource $parser XML parser
	 * @param string $openEntityNames Space-separated list of open entity names
	 * @param string $base Base for resolving the system identifier
	 * @param string $systemId System identifier
	 * @param string $publicId Public identifier
	 * @abstract
	 */
	function externalEntityRef($parser, $openEntityNames, $base, $systemId, $publicId) {
	}

	/**
	 * Handles unparsed entity declarations
	 *
	 * @param resource $parser XML parser
	 * @param string $entityName Entity name
	 * @param string $base Base for resolving the system identifier
	 * @param string $systemId System identifier
	 * @param string $publicId Public identifier
	 * @param string $notationName Name of the entity's notation
	 * @abstract
	 */
	function unparsedEntityDecl($parser, $entityName, $base, $systemId, $publicId, $notationName) {
	}
}
?>