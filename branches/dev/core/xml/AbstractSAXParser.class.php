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
// @desc		Classe abstrata que tem como propósito interpretar contéudo XML
//				utilizando o parser expat do PHP e métodos tratadores de eventos
//				(handlers). Estes métodos devem ser implementados nas classes filhas.
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
	var $srcEncoding = NULL;			// @var srcEncoding string		"NULL" Codificação (charset) a ser utilizado na interpretação do conteúdo XML
	var $namespaceAware = FALSE;		// @var namespaceAware bool		"FALSE" Indica se as informações de namespace devem ser processadas
	var $preserveWhitespace = FALSE;	// @var preserveWhitespace bool	"FALSE" Preservar espaços em branco no conteúdo XML
	var $parserOptions = array();		// @var parserOptions array		"array()" Conjunto de opções para o parser XML

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
		// verifica a existência da xml extension
		if (!function_exists('xml_parser_create'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'xml'), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setSourceEncoding
	// @desc		Define a codificação de entrada a ser utilizada na interpretação do conteúdo XML
	// @param		encoding string		Codificação de entrada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSourceEncoding($encoding) {
		$this->srcEncoding = $encoding;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setTargetEncoding
	// @desc		Define a codificação de saída a ser utilizada nos dados lidos do XML
	// @param		encoding string		Codificação de saída
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTargetEncoding($encoding) {
		$this->parserOptions[XML_OPTION_TARGET_ENCODING] = $encoding;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setParserOption
	// @desc		Define o valor de uma opção do parser XML
	// @param		option int		Opção
	// @param		value mixed		Valor para a opção
	// @note		Para detalhes sobre as opções disponíveis, consulte a documentação
	//				em http://www.php.net/xml_parser_set_option
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setParserOption($option, $value) {
		$this->parserOptions[$option] = $value;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setNamespaceAwareness
	// @desc		Define se os namespaces devem ou não ser processados pelo parser XML
	// @param		setting bool	"TRUE" Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setNamespaceAwareness($setting=TRUE) {
		$this->namespaceAware = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::setPreserveWhitespace
	// @desc		Define se os espaços em branco contidos no conteúdo XML devem ser preservados
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setPreserveWhitespace($setting) {
		$this->preserveWhitespace = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::parse
	// @desc		Interpreta o conteúdo de um arquivo XML ou uma string XML,
	//				chamando os métodos tratadores de eventos definidos na classe
	// @param		xmlContent mixed	Caminho do arquivo XML ou string XML
	// @param		srcType int			"T_BYFILE" T_BYFILE: arquivo, T_BYVAR: string
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function parse($xmlContent, $srcType=T_BYFILE) {
		// buscar conteúdo, quando $srcType==T_BYFILE
		if ($srcType == T_BYFILE)
			$xmlContent = FileSystem::getContents($xmlContent);
		// remover espaços em branco
		if (!$this->preserveWhitespace)
			$xmlContent = eregi_replace(">[[:space:]]+<", "><", $xmlContent);
		// criação do parser
		$parser = XmlParser::createParser(
			$xmlContent, $this->srcEncoding,
			$this->parserOptions, $this->namespaceAware
		);
		// configuração dos tratadores de eventos
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
	// @desc		Método que trata o evento de início de elemento (nodo XML)
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
	// @desc		Método que trata o evento de início de elemento, para os
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
	// @desc		Método que trata o evento de fim de elemento (nodo XML)
	// @param		parser resource		Parser XML
	// @param		name string			Nome do elemento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function endElement($parser, $name) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::characterData
	// @desc		Método que trata as seções CDATA (character data) do conteúdo XML
	// @param		parser resource		Parser XML
	// @param		data string			Character data, na forma de string
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function characterData($parser, $data) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::startNamespace
	// @desc		Método que recebe a notificação do início da declaração de um namespace no conteúdo XML
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
	// @desc		Método que recebe a notificação do final da declaração de um namespace no conteúdo XML
	// @param		parser resource		Parser XML
	// @param		prefix string		Prefixo do namespace
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function endNamespace($parser, $prefix) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::notationDecl
	// @desc		Recebe a notificação de uma declaração de notação no conteúdo XML
	// @param		parser resource		Parser XML
	// @param		notationName string	Nome da notação
	// @param		base string			Base para resolução dos identificadores de sistema (SYSTEM). Atualmente é enviado sempre como uma string vazia
	// @param		systemId string		Identificador de sistema da declaração externa da notação
	// @param		publicId string		Identificador público da declaração externa da notação
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function notationDecl($parser, $notationName, $base, $systemId, $publicId) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::processingInstruction
	// @desc		Recebe a notificação da declaração de uma instrução de processamento (PI)
	// @param		parser resource		Parser XML
	// @param		target string		Alvo da PI (Ex: php)
	// @param		data string			Dados da instrução (Ex: código PHP)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function processingInstruction($parser, $target, $data) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::externalEntityRef
	// @desc		Método que trata uma referência para uma entidade externa no conteúdo XML
	// @param		parser resource			Parser XML
	// @param		openEntityNames string	Lista separada por espaços das entidades que aguardam pela interpretação desta entidade
	// @param		base string				Base de resolução do identificador de sistema da entidade externa (SYSTEM). Atualmente é enviado sempre como uma string vazia
	// @param		systemId string			Identificador de sistema da entidade externa
	// @param		publicId string			Identificador público da entidade externa
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function externalEntityRef($parser, $openEntityNames, $base, $systemId, $publicId) {
	}

	//!-----------------------------------------------------------------
	// @function	AbstractSAXParser::unparsedEntityDecl
	// @desc		Recebe a notificação de uma declaração de entidade externa
	//				associada a uma declaração de notação (NDATA)
	// @param		parser resource		Parser XML
	// @param		entityName string	Nome da entidade
	// @param		base string			Base para resolução do identificador de sistema da entidade
	// @param		systemId string		Identificador de sistema da entidade
	// @param		publicId string		Identificador público da entidade
	// @param		notationName string	Nome da notação associada
	//!-----------------------------------------------------------------
	function unparsedEntityDecl($parser, $entityName, $base, $systemId, $publicId, $notationName) {
	}
}
?>