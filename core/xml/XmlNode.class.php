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
// $Header: /www/cvsroot/php2go/core/xml/XmlNode.class.php,v 1.17 2006/10/29 17:26:22 mpont Exp $
// $Date: 2006/10/29 17:26:22 $

//!-----------------------------------------------------------------
import('php2go.base.AbstractNode');
//!-----------------------------------------------------------------

// @const	XML_NODE_DEFAULT_NAME		"node"
// Nome padrão para nodos inseridos através de uma consulta SQL
define('XML_NODE_DEFAULT_NAME',			'node');

//!-----------------------------------------------------------------
// @class		XmlNode
// @desc		Classe que cria e manipula um nodo de uma árvore XML,
//				extendendo os métodos implementados na classe superior,
//				AbstractNode
// @package		php2go.xml
// @extends 	AbstractNode
// @uses		Db
// @uses		TypeUtils
// @author 		Marcos Pont
// @version		$Revision: 1.17 $
//!-----------------------------------------------------------------
class XmlNode extends AbstractNode
{
	var $prefix;			// @var prefix string						Prefixo de namespace do nome do nodo
	var $localName;			// @var localName string					Parte local do nome do nodo, quando existe um prefixo de namespace
	var $value; 			// @var value mixed							Valor CDATA do nodo XML
	var $ownerDocument;		// @var ownerDocument XmlDocument object	Documento onde o nodo está inserido

	//!-----------------------------------------------------------------
	// @function	XmlNode::XmlNode
	// @desc		Construtor do objeto XmlNode
	// @access		public
	// @param		nodeTag string		Tag do nodo
	// @param		nodeAttrs array		Vetor de atributos do nodo
	// @param 		nodeChildren array	"NULL" Vetor de filhos do nodo
	// @param 		nodeValue mixed		"NULL" Valor CDATA do nodo XML
	//!-----------------------------------------------------------------
	function XmlNode($nodeTag, $nodeAttrs, $nodeChildren = NULL, $nodeValue = NULL) {
		parent::AbstractNode($nodeTag, $nodeAttrs, $nodeChildren);
		if (ereg('[a-zA-Z]+\:[a-zA-Z]+', $nodeTag))
			list($this->prefix, $this->localName) = explode(':', $nodeTag);
		$this->value = $nodeValue;
		$this->ownerDocument = NULL;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::getTag
	// @desc		Busca a tag do nodo XML
	// @access		public
	// @return		string Tag do nodo XML
	//!-----------------------------------------------------------------
	function getTag() {
		return parent::getName();
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::setTag
	// @desc		Altera o valor do nome da tag do nodo atual
	// @access		public
	// @param		newTag string		Novo nome para a tag
	// @return		void
	//!-----------------------------------------------------------------
	function setTag($newTag) {
		parent::setName($newTag);
		if (ereg("[a-zA-Z]+\:[a-zA-Z]+", $newTag))
			list($this->prefix, $this->localName) = explode(':', $newTag);
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::getPrefix
	// @desc		Busca o prefixo de namespace do nodo
	// @access		public
	// @return		string Prefixo de namespace
	//!-----------------------------------------------------------------
	function getPrefix() {
		return $this->prefix;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::setPrefix
	// @desc		Define um valor para o prefixo de namespace do nodo
	// @access		public
	// @param		newPrefix string	Valor para o prefixo
	// @return		void
	//!-----------------------------------------------------------------
	function setPrefix($newPrefix) {
		$this->prefix = $newPrefix;
		parent::setName($this->prefix . ':' . $this->localName);
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::getLocalName
	// @desc		Busca a parte local do nome do nodo
	// @access		public
	// @return		string Nome local
	//!-----------------------------------------------------------------
	function getLocalName() {
		return $this->localName;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::getElementsByTagName
	// @desc		Retorna os elementos (incluindo ele mesmo) cujo
	//				nome seja igual a $tagName
	// @param		tagName string	Nome da tag
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function &getElementsByTagName($tagName) {
		$elements = array();
		$this->getNamedItem($tagName, $elements);
		return $elements;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::getNamedItem
	// @desc		Monta um vetor contendo os elementos identificados pelo nome $name
	// @param		name string		Nome da tag
	// @param		&elements array	Array de elementos encontrados
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function getNamedItem($name, &$elements) {
		if (!is_array($elements))
			$elements = array();
		if ($name == '*' || $this->name == $name)
			$elements[] =& $this;
		if ($this->hasChildren())
			for ($i=0; $i<$this->childrenCount; $i++)
				$this->children[$i]->getNamedItem($name, $elements);
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::getChildrenTagsArray
	// @desc 		Retorna os filhos do nodo listados em um
	// 				vetor associativo indexado pelas TAGS
	// @access 		public
	// @return		array Vetor associativo no formato Children1Tag=>Children1Object,
	// 				Children2Tag=>Children2Object, ...
	// @note		Quando o nodo XML possui filhos cujas tags se repetem,
	//				estes retornarão na forma de um vetor
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	XmlNode::hasData
	// @desc		Verifica se o nodo XML possui um valor de texto ou CDATA
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasData() {
		return isset($this->value) && !TypeUtils::isNull($this->value);
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::getData
	// @desc		Retorna o valor do nodo (texto ou CDATA)
	// @access		public
	// @return		mixed Valor do nodo
	//!-----------------------------------------------------------------
	function getData() {
		return $this->value;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::setData
	// @desc		Atribui um valor à propriedade CDATA do nodo XML
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setData($value) {
		$this->value = $value;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::&getOwnerDocument
	// @desc		Retorna o documento XML onde o nodo está inserido
	// @access		public
	// @return		XmlDocument object
	//!-----------------------------------------------------------------
	function &getOwnerDocument() {
		return $this->ownerDocument;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::setOwnerDocument
	// @desc		Define o documento XML do nodo atual
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setOwnerDocument(&$Document) {
		if (TypeUtils::isInstanceOf($Document, 'XmlDocument'))
			$this->ownerDocument =& $Document;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::&addChild
	// @desc		Sobrescreve o método AbstractNode::addChild para setar
	//				a propriedade ownerDocument do objeto XmlNode
	// @access		public
	// @param		childNode XmlNode object	Nodo a ser inserido
	// @return		XmlNode object Nodo inserido
	// @see			AbstractNode::addChild
	//!-----------------------------------------------------------------
	function &addChild($childNode) {
		$Child =& parent::addChild($childNode);
		$Child->setOwnerDocument($this->ownerDocument);
		return $Child;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::addFromQuery
	// @desc		Adiciona nodos filhos ao nodo atual a partir de uma consulta SQL
	// @access		public
	// @param		queryString string	Código da consulta SQL
	// @param		tagName string		"XML_NODE_DEFAULT_NAME" Nome da tag a ser criada para cada linha de resultado
	// @param		connectionId string	"NULL" ID da conexão a banco de dados a ser utilizada
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	XmlNode::sortChildrenBy
	// @desc		Ordena os filhos do nodo baseado em um determinado atributo
	// @access		public
	// @param		attributeName string	Nome do atributo de ordenação
	// @return		bool
	// @note		Se o atributo não existir em algum dos filhos, a função irá abortar
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	XmlNode::&createClone
	// @desc		Cria um clone no nodo XML
	// @access		public
	// @return		XmlNode object
	//!-----------------------------------------------------------------
	function &createClone() {
		$Clone = new XmlNode($this->name, $this->attrs, NULL, $this->value);
		return $Clone;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::render
	// @desc		A partir dos dados contidos na classe, constrói a representação XML do nodo
	// @access		public
	// @param		lineEnd string	"" Caractere de fim de linha para o conteúdo
	// @param		depth int		"0" Nível do nodo na árvore XML
	// @param		indent string	"" String a ser utilizada para indentação
	// @return		string Conteúdo XML do nodo
	// @note		Se o nodo atual possuir filhos, a função render será chamada recursivamente
	//				para os mesmos, incrementando uma unidade na indentação
	//!-----------------------------------------------------------------
	function render($lineEnd='', $depth=0, $indent='') {
		$cdata = FALSE;
		$content  = str_repeat($indent, $depth) . '<' . $this->getTag() . $this->_renderAttributeString();
		if ($this->hasChildren() || $this->hasData()) {
			$content .= '>';
			// gera a seção CDATA do nodo
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
			// executa a recursão para os filhos do nodo
			for ($i=0; $i<$this->getChildrenCount(); $i++) {
				$content .= $this->children[$i]->render($lineEnd, $depth+1, $indent);
			}
			$content .= (($this->hasChildren() || $cdata) ? str_repeat($indent, $depth) : '') . '</' . $this->getTag() . '>' . $lineEnd;
		} else {
			$content .= '/>' . $lineEnd;
		}
		return $content;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::_renderAttributeString
	// @desc		Constrói a representação XML dos atributos do nodo
	// @access		private
	// @return		string Conteúdo dos atributos no formato atributo="valor"
	//!-----------------------------------------------------------------
	function _renderAttributeString() {
		$buffer = '';
		foreach((array)$this->attrs as $attr => $value)
			$buffer .= " {$attr}=\"" . $this->_prepareValue($value) . "\"";
		return $buffer;
	}

	//!-----------------------------------------------------------------
	// @function	XmlNode::_prepareValue
	// @desc		Prepara o valor de um atributo para exibição, retirando
	//				caracteres especiais e barras (slashes)
	// @access		private
	// @param		value string	Valor de um atributo do nodo
	// @return		string Valor corrigido para montagem do arquivo XML
	//!-----------------------------------------------------------------
	function _prepareValue($value) {
		return str_replace(array('<','>','&'), array('&lt;', '&gt;', '&amp;'), stripslashes($value));
	}
}