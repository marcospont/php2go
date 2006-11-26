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
// $Header: /www/cvsroot/php2go/core/xml/XmlParser.class.php,v 1.24 2006/07/12 08:13:48 mpont Exp $
// $Date: 2006/07/12 08:13:48 $

//------------------------------------------------------------------
import('php2go.file.FileSystem');
import('php2go.xml.XmlNode');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		XmlParser
// @desc		Esta classe é uma camada de abstração sobre o parser
//				EXPAT nativo do PHP, que constrói uma árvore de objetos
//				XmlNode representando o conteúdo do arquivo e insere esta
//				em um objeco XmlDocument
// @package		php2go.xml
// @extends 	PHP2Go
// @uses 		XmlNode
// @uses		FileSystem
// @author 		Marcos Pont
// @version		$Revision: 1.24 $
// @note		Exemplo de uso:
//				<pre>
//
//				$Doc =& new XmlDocument();
//				$Doc->parseXml('file.xml');
//				$Root =& $Doc->getRoot();
//				if ($Root->hasChildren()) {
//				&nbsp;&nbsp;&nbsp;$count = $Root->getChildrenCount();
//				&nbsp;&nbsp;&nbsp;for ($i=0; $i<$count; $i++) {
//				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$Child = $Root->getChild($i);
//				&nbsp;&nbsp;&nbsp;}
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class XmlParser extends PHP2Go
{
	var $parser; 					// @var parser resource						Objeto xml_parser criado
	var $data; 						// @var data string							Dados XML a serem interpretados pelo parser
	var $vals; 						// @var vals array							Vetor de valores capturados do arquivo XML
	var $index;						// @var index array							Vetor de índices do conteúdo XML interpretado
	var $includedFiles = array();	// @var includedFiles array					"array()" Vetor de arquivos já incluídos - entidades externas
	var $namespaceAware = FALSE;	// @var namespaceAware bool					"FALSE" Indica se os namespaces devem ser processados pelo parser
	var $Document = NULL;			// @var Document XmlDocument object			"NULL" Documento onde o conteúdo parseado é inserido

	//!-----------------------------------------------------------------
	// @function	XmlParser::XmlParser
	// @desc		Construtor da classe
	// @param		&Document XmlDocument object	Documento onde os nodos criados são inseridos
	// @param		namespace bool					"FALSE" Processar ou não os namespaces
	// @access		public
	//!-----------------------------------------------------------------
	function XmlParser(&$Document, $namespace=FALSE) {
		parent::PHP2Go();
		// verifica a existência da xml extension
		if (!function_exists('xml_parser_create'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'xml'), E_USER_ERROR, __FILE__, __LINE__);
		$this->Document =& $Document;
		$this->namespaceAware = (bool)$namespace;
	}

	//!-----------------------------------------------------------------
	// @function	XmlParser::createParser
	// @desc		Método estático para a criação de um parser XML baseado em um conjunto de parâmetros
	// @param		&xmlSource string	Conteúdo XML
	// @param		srcEncoding string 	"NULL" Codificação a ser utilizada pelo parser
	// @param		optionFlags array	Conjunto de opções para o parser (http://www.php.net/xml_parser_set_option)
	// @param		namespace bool		"FALSE" Criar o parser com suporte a namespaces
	// @return		resource Parser XML criado
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function createParser(&$xmlSource, $srcEncoding=NULL, $optionFlags=array(), $namespace=FALSE) {
		$validEncodings = array('iso-8859-1', 'us-ascii', 'utf-8');
		// source encoding não fornecido
		if (empty($srcEncoding)) {
			// php4 - buscar codificação dos atributos do root XML
			$matches = array();
			if (preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/m', $xmlSource, $matches))
				$srcEncoding = strtolower($matches[1]);
			else
				$srcEncoding = 'utf-8';
		}
		// se não for uma das codificações válidas para a xml entension
		if (!empty($srcEncoding) && !in_array(strtolower($srcEncoding), $validEncodings)) {
			// conversão usando iconv
			if (function_exists('iconv')) {
				$xmlSource = iconv($srcEncoding, 'utf-8', $xmlSource);
				$srcEncoding = 'utf-8';
			}
			// conversão usando mbstring
			elseif (function_exists('mb_convert_encoding')) {
				$xmlSource = mb_convert_encoding($xmlSource, 'utf-8', $srcEncoding);
				$srcEncoding = 'utf-8';
			}
			// não foi possível converter a partir do srcEncoding fornecido
			else {
				$srcEncoding = NULL;
			}
		}
		// cria o parser
		if ($namespace)
			$parser = (TypeUtils::isNull($srcEncoding, TRUE) ? xml_parser_create_ns() : xml_parser_create_ns($srcEncoding));
		else
			$parser = (TypeUtils::isNull($srcEncoding, TRUE) ? xml_parser_create() : xml_parser_create($srcEncoding));
		// seta as opções
		foreach ($optionFlags as $code => $value)
			xml_parser_set_option($parser, $code, $value);
		return $parser;
	}

	//!-----------------------------------------------------------------
	// @function	XmlParser::parse
	// @desc		Interpreta o conteúdo de um arquivo ou string XML
	// @param		xmlContent string		Caminho completo do arquivo ou string XML
	// @param		srcType int				"T_BYFILE" T_BYFILE para arquivos e T_BYVAR para variáveis
	// @param		srcEncoding string		"NULL" Codificação a ser utilizada na interpretação dos dados
	// @param		trgEncoding string		"NULL" Codificação a ser utilizada na montagem da árvore XML
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function parse($xmlContent, $srcType=T_BYFILE, $srcEncoding=NULL, $trgEncoding=NULL) {
		if ($srcType == T_BYFILE) {
			$this->includedFiles[] = $xmlContent;
			$this->data = FileSystem::getContents($xmlContent);
		} else {
			$this->data = $xmlContent;
		}
		$this->data = eregi_replace(">[[:space:]]+<", "><", $this->data);
		return ($this->_parseExternalEntities($this->data) && $this->_parseXmlString($srcEncoding, $trgEncoding));
	}

	//!-----------------------------------------------------------------
	// @function	XmlParser::_parseExternalEntities
	// @desc		Procura por definições de entidades externas no cabeçalho
	//				do documento, inserindo o conteúdo das mesmas no arquivo
	//				principal
	// @param		&data string		Dados a serem processados
	// @note		Este método é recursivo para definições de novas entidades
	//				nos arquivos incluídos. O encontro de uma referência circular
	//				(arquivo já utilizado) irá parar o processamento
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _parseExternalEntities(&$data) {
		$matches = array();
		if (preg_match_all('/<!ENTITY[ ](%[ ])?([a-zA-Z0-9_]+)[ ]SYSTEM[ ]\"([^\"]+)\"/', $data, $matches, PREG_SET_ORDER)) {
			$sizeMatches = sizeof($matches);
			for ($i=0; $i<$sizeMatches; $i++) {
				if (!in_array($matches[$i][3], $this->includedFiles)) {
					if ($fileData = FileSystem::getContents($matches[$i][3])) {
						$this->_parseExternalEntities($fileData);
						$data = ereg_replace("(&|%){$matches[$i][2]};", $fileData, $data);
					} else {
						return FALSE;
					}
				} else
					$data = eregi_replace("(&|%){$matches[$i][2]};", "", $data);
			}
		}
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	XmlParser::_parseXmlString
	// @desc 		Inicializa o parser XML, setando suas opções de
	// 				configuração e executa a função de interpretação
	// 				do parser, armazenando os resultados em uma estrutura
	// 				de árvore
	// @param		srcEncoding string		Codificação para o parser
	// @param		trgEncoding string		Codificação para a função de montagem da árvore (xml_parse_into_struct)
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _parseXmlString($srcEncoding, $trgEncoding) {
		$i = 0;
		$parserOptions = array();
		if (!empty($trgEncoding))
			$parserOptions[XML_OPTION_TARGET_ENCODING] = $trgEncoding;
		$this->parser = XmlParser::createParser(
			$this->data, $srcEncoding,
			$parserOptions, $this->namespaceAware
		);
		if (!@xml_parse_into_struct($this->parser, $this->data, &$this->vals, &$this->index)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_XML_PARSE', array(xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser))), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		xml_parser_free($this->parser);
		$cData = '';
		$this->Document->DocumentElement = new XmlNode(
			$this->vals[$i]['tag'],
			@$this->vals[$i]['attributes'],
			$this->_getChildren($this->vals, $i, $cData),
			@$this->vals[$i]['value']
		);
		$this->Document->DocumentElement->value .= $cData;
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	XmlParser::_getChildren
	// @desc 		Função recursiva para a montagem da árvore XML
	// @param 		vals array	Vetor de valores do arquivo
	// @param 		&i int		Índice atual do vetor de valores
	// @return		array Vetor de filhos de um determinado nodo
	// @access 		private
	//!-----------------------------------------------------------------
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