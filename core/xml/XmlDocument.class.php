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
// $Header: /www/cvsroot/php2go/core/xml/XmlDocument.class.php,v 1.14 2006/10/29 17:25:30 mpont Exp $
// $Date: 2006/10/29 17:25:30 $

//------------------------------------------------------------------
import('php2go.xml.XmlNode');
import('php2go.xml.XmlParser');
//------------------------------------------------------------------

// @const	XML_DECLARATION ""
// Declara��o XML contendo vers�o e tipo de codifica��o
define('XML_DECLARATION', "<?xml version=\"%s\" encoding=\"%s\"?>");

//!-----------------------------------------------------------------
// @class		XmlDocument
// @desc		A classe XmlDocument permite a cria��o de um documento XML, formado
//				por uma �rvore de objetos do tipo XmlNode. Com base nas propriedades e
//				m�todos desta classe, � poss�vel construir um documento XML manualmente ou
//				aliment�-lo a partir da interpreta��o de um arquivo ou string XML existente
// @package		php2go.xml
// @extends		PHP2Go
// @uses		TypeUtils
// @uses		XmlNode
// @uses		XmlParser
// @author		Marcos Pont
// @version		$Revision: 1.14 $
// @note		Exemplo de uso:
//				<pre>
//
//				/* Constru��o manual */
//				$Doc =& new XmlDocument();
//				$Doc->DocumentElement =& new XmlNode('ROOT', array('ATTR'=>'VALUE'));
//				$Root =& $Doc->getDocumentElement();
//				$Child =& $Root->addChild(new XmlNode('CHILD', array('ATTR'=>'VALUE')));
//
//				/* Interpreta��o de arquivo XML */
//				$Doc =& new XmlDocument();
//				if ($Doc->parseXml('my.xml', T_BYFILE))
//				&nbsp;&nbsp;&nbsp;$Root =& $Doc->getDocumentElement();
//				&nbsp;&nbsp;&nbsp;for ($i=0; $i<$Root->getChildrenCount(); $i++) {
//				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;echo $Root->children[$i]->getName(), '<br>';
//				&nbsp;&nbsp;&nbsp;}
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class XmlDocument extends PHP2Go
{
	var $xmlDecl = '';				// @var declaration string ""					Declara��o XML
	var $styleSheet = array();		// @var styleSheet array "array()"				Vetor de estilos do documento XML
	var $doctype = array();			// @var doctype array "array()"					Vetor de configura��es de defini��o do documento
	var $namespaceAware = FALSE;	// @var namespaceAware bool						"FALSE" Indica se o documento XML deve ter suporte a namespaces
	var $DocumentElement = NULL;	// @var DocumentElement XmlNode object "NULL"	Elemento inicial da cadeia de nodos do documento

	//!-----------------------------------------------------------------
	// @function	XmlDocument::XmlDocument
	// @desc		Construtor do documento XML
	// @access		public
	//!-----------------------------------------------------------------
	function XmlDocument() {
		parent::PHP2Go();
	}

	//!-----------------------------------------------------------------
	// @function	XmlDocument::&getRoot
	// @desc		Este m�todo � um alias para XmlDocument::getDocumentElement
	// @return		XmlNode object Raiz da �rvore XML do documento
	// @see			XmlDocument::getDocumentElement
	// @access		public
	//!-----------------------------------------------------------------
	function &getRoot() {
		return $this->DocumentElement;
	}

	//!-----------------------------------------------------------------
	// @function	XmlDocument::&getDocumentElement
	// @desc		Retorna o elemento inicial da cadeia de nodos do documento XML
	// @return		XmlNode object A "raiz" ou elemento inicial da �rvore XML
	// @access		public
	//!-----------------------------------------------------------------
	function &getDocumentElement() {
		return $this->DocumentElement;
	}

	//!-----------------------------------------------------------------
	// @function	XmlDocument::addXmlDeclaration
	// @desc		Adiciona declara��o XML ao documento
	// @param		version string		"1.0" Vers�o da especifica��o XML
	// @param		charset string		"NULL" Conjunto de caracteres ou codifica��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addXmlDeclaration($version='1.0', $charset=NULL) {
		if (empty($charset))
			$charset = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->xmlDecl = sprintf(XML_DECLARATION, $version, $charset);
	}

	//!-----------------------------------------------------------------
	// @function	XmlDocument::addStylesheet
	// @desc		Adiciona uma folha de estilos ao documento XML
	// @param		link string		URL ou nome do arquivo de estilo
	// @param		alternate bool	Indica que o estilo � alternativo em rela��o a um estilo principal
	// @param		type string		"text/css" Tipo do arquivo de estilo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addStylesheet($link, $alternate=FALSE, $type='text/css') {
		$this->styleSheet[] = array('href'=>$link, 'type'=>$type, 'alternate'=>(TypeUtils::toBoolean($alternate) ? 'yes' : 'no'));
	}

	//!-----------------------------------------------------------------
	// @function	XmlDocument::setDoctype
	// @desc		Define a fonte e o caminho para a defini��o de tipos e entidades do documento
	// @param		source string	SYSTEM ou PUBLIC
	// @param		id mixed		Caminho ou vetor de caminhos para especifica��es de tipos
	// @param		entries string	"array()" Entradas internas da defini��o do documento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setDoctype($uri, $id=NULL, $entries='') {
		if (!TypeUtils::isNull($id)) {
			$this->doctype['SOURCE'] = 'PUBLIC';
			$this->doctype['ID'] = " \"$id\"";
		} else {
			$this->doctype['SOURCE'] = 'SYSTEM';
			$this->doctype['ID'] = NULL;
		}
		$this->doctype['URI'] = "\"$uri\"";
		$this->doctype['ENTRIES'] = $entries;
	}

	//!-----------------------------------------------------------------
	// @function	XmlDocument::setNamespaceAwareness
	// @desc		Define se o documento XML deve ter suporte a namespaces
	// @param		setting bool	"TRUE" Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setNamespaceAwareness($setting=TRUE) {
		$this->namespaceAware = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	XmlDocument::parseXml
	// @desc		Cria uma inst�ncia do parser XML para um determinado arquivo ou conte�do XML
	// @access		public
	// @param		xmlContent string	Nome completo do arquivo ou valor XML
	// @param		srcType int			"T_BYFILE" T_BYFILE para arquivos e T_BYVAR para strings
	// @param		srcEncoding string	"NULL" Codifica��o a ser usada no parser XML
	// @param		trgEncoding string	"NULL" Codifica��o a ser usada na montagem da �rvore XML
	// @return		bool
	// @note		Se os par�metros de codifica��o forem omitidos, a codifica��o padr�o ser�
	//				utilizada na interpreta��o e nas fun��es de tratamento (callbacks)
	//!-----------------------------------------------------------------
	function parseXml($xmlContent, $srcType=T_BYFILE, $srcEncoding=NULL, $trgEncoding=NULL) {
		$this->DocumentElement = NULL;
		$Parser = new XmlParser($this, $this->namespaceAware);
		return $Parser->parse($xmlContent, $srcType, $srcEncoding, $trgEncoding);
	}

	//!-----------------------------------------------------------------
	// @function	XmlDocument::render
	// @desc		Monta a representa��o string de todo o documento XML
	// @access		public
	// @param		lineEnd string	"" Caractere de fim de linha para o conte�do
	// @param		indent string	"" String a ser usada para indenta��o
	// @return		string Representa��o textual do documento XML
	//!-----------------------------------------------------------------
	function render($lineEnd='', $indent='') {
		$result = '';
		if (!TypeUtils::isNull($this->DocumentElement)) {
			// declara��o XML
			if (!empty($this->xmlDecl))
				$result .= $this->xmlDecl . $lineEnd;
			// coment�rio do generator
			$result .= "<!-- generator=\"PHP2Go Web Development Framework " . PHP2GO_VERSION . "\" -->\n";
			// folhas de estilo
			for ($i=0,$s=sizeof($this->styleSheet); $i<$s; $i++)
				$result .= "<?xml-stylesheet href=\"{$this->styleSheet[$i]['href']}\" type=\"{$this->styleSheet[$i]['type']}\" alternate=\"{$this->styleSheet[$i]['alternate']}\"?>" . $lineEnd;
			// doctype
			if (!empty($this->doctype)) {
				if (!empty($this->doctype['ENTRIES']))
					$result .= sprintf("<!DOCTYPE %s %s%s %s [ %s%s %s]>", $this->DocumentElement->getName(), $this->doctype['SOURCE'], $this->doctype['ID'], $this->doctype['URI'], $lineEnd, $this->doctype['ENTRIES'], $lineEnd) . $lineEnd;
				else
					$result .= sprintf("<!DOCTYPE %s %s%s %s>", $this->DocumentElement->getName(), $this->doctype['SOURCE'], $this->doctype['ID'], $this->doctype['URI']) . $lineEnd;
			}
			// nodos, a partir da raiz
			$result .= $this->DocumentElement->render($lineEnd, 0, $indent);
		}
		return $result;
	}
}
?>