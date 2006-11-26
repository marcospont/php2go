<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/core/xml/AbstractSAXParser.class.php,v 1.7 2006/11/19 18:01:01 mpont Exp $
// $Date: 2006/11/19 18:01:01 $

//------------------------------------------------------------------
import('php2go.file.FileSystem');
import('php2go.xml.XmlParser');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		AbstractSAXParser
// @desc		Classe abstrata que tem como prop�sito interpretar cont�udo XML
//				utilizando o parser expat do PHP e m�todos tratadores de eventos
//				(handlers). Estes m�todos devem ser implementados nas classes filhas.
// @package		php2go.xml
// @extends		PHP2Go
// @uses		FileSystem
// @uses		XmlParser
// @uses		TypeUtils
// @version		$Revision: 1.7 $
// @author		Marcos Pont
//!-----------------------------------------------------------------
class AbstractSAXParser extends PHP2Go
{
	var $srcEncoding = NULL;			// @var srcEncoding string		"NULL" Codifica��o (charset) a ser utilizado na interpreta��o do conte�do XML
	var $namespaceAware = FALSE;		// @var namespaceAware bool		"FALSE" Indica se as informa��es de namespace devem ser processadas
	var $preserveWhitespace = FALSE;	// @var preserveWhitespace bool	"FALSE" Preservar espa�os em branco no conte�do XML
	var $parserOptions = array();		// @var parserOptions array		"array()" Conjunto de op��es para o parser XML

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::AbstractSAXParser
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function AbstractSAXParser() {
		parent::PHP2Go();
		// classe abstrata
		if ($this->isA('AbstractSAXParser', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'AbstractSAXParser'), E_USER_ERROR, __FILE__, __LINE__);
		// verifica a exist�ncia da xml extension
		if (!function_exists('xml_parser_create'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'xml'), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setSourceEncoding
	// @desc		Define a codifica��o de entrada a ser utilizada na interpreta��o do conte�do XML
	// @param		encoding string		Codifica��o de entrada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSourceEncoding($encoding) {
		$this->srcEncoding = $encoding;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setTargetEncoding
	// @desc		Define a codifica��o de sa�da a ser utilizada nos dados lidos do XML
	// @param		encoding string		Codifica��o de sa�da
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTargetEncoding($encoding) {
		$this->parserOptions[XML_OPTION_TARGET_ENCODING] = $encoding;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setParserOption
	// @desc		Define o valor de uma op��o do parser XML
	// @param		option int		Op��o
	// @param		value mixed		Valor para a op��o
	// @note		Para detalhes sobre as op��es dispon�veis, consulte a documenta��o
	//				em http://www.php.net/xml_parser_set_option
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setParserOption($option, $value) {
		$this->parserOptions[$option] = $value;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setNamespaceAwareness
	// @desc		Define se os namespaces devem ou n�o ser processados pelo parser XML
	// @param		setting bool	"TRUE" Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setNamespaceAwareness($setting=TRUE) {
		$this->namespaceAware = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setPreserveWhitespace
	// @desc		Define se os espa�os em branco contidos no conte�do XML devem ser preservados
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setPreserveWhitespace($setting) {
		$this->preserveWhitespace = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::parse
	// @desc		Interpreta o conte�do de um arquivo XML ou uma string XML,
	//				chamando os m�todos tratadores de eventos definidos na classe
	// @param		xmlContent mixed	Caminho do arquivo XML ou string XML
	// @param		srcType int			"T_BYFILE" T_BYFILE: arquivo, T_BYVAR: string
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function parse($xmlContent, $srcType=T_BYFILE) {
		// buscar conte�do, quando $srcType==T_BYFILE
		if ($srcType == T_BYFILE)
			$xmlContent = FileSystem::getContents($xmlContent);
		// remover espa�os em branco
		if (!$this->preserveWhitespace)
			$xmlContent = eregi_replace(">[[:space:]]+<", "><", $xmlContent);
		// cria��o do parser
		$parser = XmlParser::createParser(
			$xmlContent, $this->srcEncoding,
			$this->parserOptions, $this->namespaceAware
		);
		// configura��o dos tratadores de eventos
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

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::startElement
	// @desc		M�todo que trata o evento de in�cio de elemento (nodo XML)
	// @param		parser resource		Parser XML
	// @param		name string			Nome do elemento
	// @param		attrs array			Atributos do elemento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function startElement($parser, $name, $attrs) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::startElementNS
	// @desc		M�todo que trata o evento de in�cio de elemento, para os
	//				casos em que o processamento de namespaces estiver ativo na classe
	// @param		parser resource		Parser XML
	// @param		name string			Namespace e nome do elemento
	// @param		attrs array			Atributos do elemento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function startElementNS($parser, $name, $attrs) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::endElement
	// @desc		M�todo que trata o evento de fim de elemento (nodo XML)
	// @param		parser resource		Parser XML
	// @param		name string			Nome do elemento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function endElement($parser, $name) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::characterData
	// @desc		M�todo que trata as se��es CDATA (character data) do conte�do XML
	// @param		parser resource		Parser XML
	// @param		data string			Character data, na forma de string
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function characterData($parser, $data) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::startNamespace
	// @desc		M�todo que recebe a notifica��o do in�cio da declara��o de um namespace no conte�do XML
	// @param		parser resource		Parser XML
	// @param		prefix string		Prefixo do namespace
	// @param		uri string			URI do namespace
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function startNamespace($parser, $prefix, $uri) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::endNamespace
	// @desc		M�todo que recebe a notifica��o do final da declara��o de um namespace no conte�do XML
	// @param		parser resource		Parser XML
	// @param		prefix string		Prefixo do namespace
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function endNamespace($parser, $prefix) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::notationDecl
	// @desc		Recebe a notifica��o de uma declara��o de nota��o no conte�do XML
	// @param		parser resource		Parser XML
	// @param		notationName string	Nome da nota��o
	// @param		base string			Base para resolu��o dos identificadores de sistema (SYSTEM). Atualmente � enviado sempre como uma string vazia
	// @param		systemId string		Identificador de sistema da declara��o externa da nota��o
	// @param		publicId string		Identificador p�blico da declara��o externa da nota��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function notationDecl($parser, $notationName, $base, $systemId, $publicId) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::processingInstruction
	// @desc		Recebe a notifica��o da declara��o de uma instru��o de processamento (PI)
	// @param		parser resource		Parser XML
	// @param		target string		Alvo da PI (Ex: php)
	// @param		data string			Dados da instru��o (Ex: c�digo PHP)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function processingInstruction($parser, $target, $data) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::externalEntityRef
	// @desc		M�todo que trata uma refer�ncia para uma entidade externa no conte�do XML
	// @param		parser resource			Parser XML
	// @param		openEntityNames string	Lista separada por espa�os das entidades que aguardam pela interpreta��o desta entidade
	// @param		base string				Base de resolu��o do identificador de sistema da entidade externa (SYSTEM). Atualmente � enviado sempre como uma string vazia
	// @param		systemId string			Identificador de sistema da entidade externa
	// @param		publicId string			Identificador p�blico da entidade externa
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function externalEntityRef($parser, $openEntityNames, $base, $systemId, $publicId) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::unparsedEntityDecl
	// @desc		Recebe a notifica��o de uma declara��o de entidade externa
	//				associada a uma declara��o de nota��o (NDATA)
	// @param		parser resource		Parser XML
	// @param		entityName string	Nome da entidade
	// @param		base string			Base para resolu��o do identificador de sistema da entidade
	// @param		systemId string		Identificador de sistema da entidade
	// @param		publicId string		Identificador p�blico da entidade
	// @param		notationName string	Nome da nota��o associada
	//!-----------------------------------------------------------------
	function unparsedEntityDecl($parser, $entityName, $base, $systemId, $publicId, $notationName) {
	}
}
?>