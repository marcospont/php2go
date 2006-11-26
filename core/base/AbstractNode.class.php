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
// $Header: /www/cvsroot/php2go/core/base/AbstractNode.class.php,v 1.21 2006/06/15 00:30:49 mpont Exp $
// $Date: 2006/06/15 00:30:49 $

//!-----------------------------------------------------------------
// @class		AbstractNode
// @desc		Classe que implementa métodos de construção e manipulação
//				de nodos, criando, destruindo e gerenciando seus atributos
//				e nodos filhos. Baseia-se nos métodos implementados pelo
//				modelo DOM Level 2
// @package		php2go.base
// @extends		PHP2Go
// @version		$Revision: 1.21 $
// @author		Marcos Pont
//!-----------------------------------------------------------------
class AbstractNode extends PHP2Go
{
	var $id;						// @var id string							ID único do nodo
	var $name;						// @var name string							Nome do nodo, com semântica dependente da utilização nas classes extendidas
	var $attrs;						// @var attrs array							Vetor de atributos do nodo
	var $children;					// @var children array						Vetor de filhos do nodo
	var $hashIndex;					// @var hashIndex array						Indexa os IDs de nodos para facilitar a busca
	var $childrenCount = 0;			// @var childrenCount int					"0" Número de filhos do nodo
	var $parentNode = NULL;			// @var parentNode AbstractNode object		"NULL" Nodo pai
	var $firstChild = NULL;			// @var firstChild AbstractNode object		"NULL" Primeiro filho do nodo
	var $lastChild = NULL;			// @var lastChild AbstractNode object		"NULL" Último filho do nodo
	var $previousSibling = NULL;	// @var previousSibling AbstractNode object	"NULL" Nodo anterior na cadeia de nodos do mesmo nível
	var $nextSibling = NULL;		// @var nextSibling AbstractNode object		"NULL" Próximo nodo na cadeia de nodos do mesmo nível

	//!-----------------------------------------------------------------
	// @function	AbstractNode::AbstractNode
	// @desc		Construtor do objeto AbstractNode
	// @param		nodeName string		Nome para o nodo
	// @param		nodeAttrs array		"array()" Atributos do nodo
	// @param		nodeChildren array	"NULL" Vetor de filhos do nodo
	// @access		public
	//!-----------------------------------------------------------------
	function AbstractNode($nodeName, $nodeAttrs=array(), $nodeChildren=NULL) {
		parent::PHP2Go();
		$this->id = PHP2Go::generateUniqueId('Node');
		$this->name = $nodeName;
		$this->attrs = $nodeAttrs;
		if ($nodeChildren) {
			foreach ($nodeChildren as $Child)
				$this->addChild($Child);
		} else {
			$this->children = array();
			$this->hashIndex = array();
		}
		$this->childrenCount = (is_array($nodeChildren) ? sizeof($nodeChildren) : 0);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::getId
	// @desc		Busca o ID do nodo
	// @return		string ID do nodo
	// @access		public
	//!-----------------------------------------------------------------
	function getId() {
		return $this->id;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::getName
	// @desc		Busca o nome do nodo
	// @return		string Nome do nodo
	// @access		public
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::setName
	// @desc		Atribui um novo valor ao nome do nodo atual
	// @param		newName string	Nome nome para o nodo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setName($newName) {
		$this->name = $newName;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::hasAttributes
	// @desc		Verifica se o nodo possui atributos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasAttributes() {
		return (is_array($this->attrs) && !empty($this->attrs));
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::hasAttribute
	// @desc		Verifica se o nodo possui um determinado atributo
	// @param		name string		Nome do atributo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasAttribute($name) {
		return (is_array($this->attrs) && array_key_exists($name, $this->attrs));
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getAttributes
	// @desc		Retorna o vetor de atributos do nodo XML
	// @return		array Vetor de atributos do nodo
	// @access		public
	//!-----------------------------------------------------------------
	function &getAttributes() {
		return $this->attrs;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getAttribute
	// @desc		Busca o valor de um atributo do nodo XML
	// @param		attribute string	Nome do atributo
	// @return		string Valor do atributo ou FALSE se ele não existir
	// @access		public
	//!-----------------------------------------------------------------
	function &getAttribute($attribute) {
		$false = FALSE;
		if ($this->hasAttribute($attribute))
			return $this->attrs[$attribute];
		return $false;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::addAttributes
	// @desc		Adiciona um conjunto de atributos ao nodo
	// @param		attributes array	Vetor de atributos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addAttributes($attributes) {
		if (TypeUtils::isHashArray($attributes))
			$this->attrs = array_merge($this->attrs, $attributes);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::setAttribute
	// @desc		Configura o valor de um atributo do nodo
	// @param		attribute string	Nome do atributo
	// @param		value mixed			Valor para o atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAttribute($attribute, $value) {
		$this->attrs[$attribute] = $value;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::removeAttribute
	// @desc		Remove um atributo do nodo
	// @param		attribute string	Nome do atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function removeAttribute($attribute) {
		unset($this->attrs[$attribute]);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getParentNode
	// @desc		Retorna o nodo pai do nodo atual
	// @return		AbstractNode object
	// @access		public
	//!-----------------------------------------------------------------
	function &getParentNode() {
		return $this->parentNode;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::setParentNode
	// @desc		Define o nodo superior ao nodo atual
	// @param		&Node AbstractNode object
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setParentNode(&$Node) {
		$this->parentNode =& $Node;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::hasChildren
	// @desc		Verifica se o nodo XML possui filhos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasChildren() {
		return ($this->childrenCount > 0);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::getChildrenCount
	// @desc 		Retorna o número de filhos do nodo XML
	// @return		int Número de filhos do nodo
	// @access 		public
	//!-----------------------------------------------------------------
	function getChildrenCount() {
		return $this->childrenCount;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getChildNodes
	// @desc		Retorna o vetor de filhos do nodo
	// @note		Retorna um array vazio caso o nodo não possua nodos filhos
	// @return		array
	// @access		public
	//!-----------------------------------------------------------------
	function &getChildNodes() {
		$result = array();
		if ($this->childrenCount > 0)
			$result =& $this->children;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getFirstChild
	// @desc		Busca o primeiro filho do nodo
	// @return		AbstractNode object
	// @access		public
	//!-----------------------------------------------------------------
	function &getFirstChild() {
		$result = NULL;
		if ($this->childrenCount > 0)
			$result =& $this->firstChild;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getLastChild
	// @desc		Busca o útlimo filho do nodo
	// @return		AbstractNode object
	// @access		public
	//!-----------------------------------------------------------------
	function &getLastChild() {
		$result = NULL;
		if ($this->childrenCount > 0)
			$result =& $this->lastChild;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getPreviousSibling
	// @desc		Retorna o nodo anterior na cadeia de nodos no mesmo nível
	// @return		AbstractNode object
	// @access		public
	//!-----------------------------------------------------------------
	function &getPreviousSibling() {
		return $this->previousSibling;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getNextSibling
	// @desc		Retorna o próximo nodo na cadeia de nodos no mesmo nível
	// @return		AbstractNode object
	// @access		public
	//!-----------------------------------------------------------------
	function &getNextSibling() {
		return $this->nextSibling;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getChild
	// @desc 		Retorna o filho de índice $index do nodo, se existir
	// @param 		index int	Índice do nodo buscado
	// @return	 	AbstractNode object
	// @access		public
	//!-----------------------------------------------------------------
	function &getChild($index) {
		$result = FALSE;
		if (isset($this->children[$index]))
			$result =& $this->children[$index];
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::getNodeIndex
	// @desc		Procura por um determinado nodo nos filhos do nodo atual
	// @param		node AbstractNode object	Nodo buscado
	// @access		protected
	// @return		int
	//!-----------------------------------------------------------------
	function getNodeIndex($Node) {
		if ($this->childrenCount > 0) {
			$result = array_search($Node->getId(), $this->hashIndex);
			if (!TypeUtils::isFalse($result))
				return $result;
		}
		return -1;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&addChild
	// @desc		Adiciona um filho ao nodo XML
	// @param		childNode AbstractNode object	Objeto XmlNode a ser inserido
	// @return		AbstractNode object
	// @access		public
	//!-----------------------------------------------------------------
	function &addChild($childNode) {
		$result = FALSE;
		if (TypeUtils::isInstanceOf($childNode, 'AbstractNode')) {
			if (!$this->hasChildren()) {
				$this->children[0] =& $childNode;
				$this->childrenCount = 1;
				$this->firstChild =& $childNode;
				$childNode->previousSibling = NULL;
				$childNode->nextSibling = NULL;
				$this->hashIndex[0] = $childNode->getId();
			} else {
				$index = $this->getNodeIndex($childNode);
				if (!TypeUtils::isNull($index) && $index != -1)
					$this->removeChild($index);
				$this->children[$this->childrenCount] =& $childNode;
				$this->childrenCount++;
				$this->lastChild->nextSibling =& $childNode;
				$Child->previousSibling =& $this->lastChild;
				$this->hashIndex[$this->childrenCount] = $childNode->getId();
			}
			$this->lastChild =& $childNode;
			$childNode->nextSibling = NULL;
			$childNode->setParentNode($this);
			$result =& $childNode;
		}
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::addChildList
	// @desc		Adiciona uma lista de filhos ao nodo XML
	// @note		Este método recebe N parâmetros, que são interpretados
	//				como N filhos a serem adicionados ao nodo atual
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addChildList() {
		$args = func_get_args();
		if (func_num_args() > 0) {
			foreach($args as $Child) {
				$this->addChild($Child);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::removeChild
	// @desc		Remove um filho do nodo atual, através de seu índice
	// @param		index int		Índice do nodo a ser removido
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function removeChild($index) {
		if (array_key_exists($index, $this->children)) {
			$OldChild =& $this->getChild($index);
			if ($OldChild->previousSibling != NULL && $OldChild->nextSibling != NULL) {
				$OldChild->previousSibling->nextSibling =& $OldChild->nextSibling;
				$OldChild->nextSibling->previousSibling =& $OldChild->previousSibling;
			} elseif ($OldChild->previousSibling == NULL && $OldChild->nextSibling != NULL) {
				$OldChild->nextSibling->previousSibling = NULL;
				$this->firstChild =& $OldChild->nextSibling;
			} elseif ($OldChild->previousSibling != NULL && $OldChild->nextSibling == NULL) {
				$OldChild->previousSibling->nextSibling = NULL;
				$this->lastChild =& $OldChild->previousSibling;
			} else {
				$this->firstChild = NULL;
				$this->lastChild = NULL;
			}
			for ($i=$index; $i<($this->childrenCount-1); $i++) {
				$this->children[$i] = $this->children[$i+1];
				$this->hashIndex[$i] = $this->hashIndex[$i+1];
			}
			$this->childrenCount--;
			if ($this->childrenCount == 0) {
				$this->children = array();
				$this->hashIndex = array();
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::cloneNode
	// @desc		Retorna um clone do nodo
	// @param		deep bool	"TRUE" Se igual a TRUE, retorna os filhos do nodo recursivamente. Do contrário, retorna apenas o nodo atual
	// @return		AbstractNode object
	// @access		public
	//!-----------------------------------------------------------------
	function cloneNode($deep=TRUE) {
		$Clone =& $this->createClone();
		if ($deep) {
			for ($i=0; $i<$this->children; $i++)
				$Clone->addChild($this->children[$i]);
		}
		return $Clone;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&cloneNode
	// @desc		Constrói um clone do objeto atual
	// @access		public
	// @return		AbstractNode object
	//!-----------------------------------------------------------------
	function &createClone() {
		$Clone = new AbstractNode($this->name, $this->attrs, NULL);
		return $Clone;
	}
}
?>