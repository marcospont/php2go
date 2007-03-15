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

/**
 * Abstract implementation of a node
 *
 * The AbstractNode class contains methods to manipulate nodes: create,
 * destroy, manage attributes and children. The implementation is highly
 * based on the DOM Level 2
 *
 * @package base
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AbstractNode extends PHP2Go
{
	/**
	 * Node ID
	 *
	 * Defaults to the value returned by an unique ID generator.
	 *
	 * @var string
	 */
	var $id;

	/**
	 * Node name
	 *
	 * @var string
	 */
	var $name;

	/**
	 * Node attributes
	 *
	 * @var array
	 */
	var $attrs;

	/**
	 * Node children
	 *
	 * @var array
	 */
	var $children;

	/**
	 * Children count
	 *
	 * @var int
	 */
	var $childrenCount = 0;

	/**
	 * Reference to the parent node
	 *
	 * @var object AbstractNode
	 */
	var $parentNode = NULL;

	/**
	 * Reference to the node's first child
	 *
	 * @var object AbstractNode
	 */
	var $firstChild = NULL;

	/**
	 * Reference to the node's last child
	 *
	 * @var object AbstractNode
	 */
	var $lastChild = NULL;

	/**
	 * Reference to the node's previous sibling
	 *
	 * @var object AbstractNode
	 */
	var $previousSibling = NULL;

	/**
	 * Reference to the node's next sibling
	 *
	 * @var object AbstractNode
	 */
	var $nextSibling = NULL;

	/**
	 * Hash containing IDs of the child nodes
	 *
	 * @var string
	 * @access private
	 */
	var $hashIndex;

	/**
	 * Class constructor
	 *
	 * @param string $nodeName Node name
	 * @param array $nodeAttrs Node attributes
	 * @param array $nodeChildren Node children
	 * @return AbstractNode
	 */
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

	/**
	 * Get the node ID
	 *
	 * @return string
	 */
	function getId() {
		return $this->id;
	}

	/**
	 * Get the node name
	 *
	 * @return string
	 */
	function getName() {
		return $this->name;
	}

	/**
	 * Set the node name
	 *
	 * @param string $newName New node name
	 */
	function setName($newName) {
		$this->name = $newName;
	}

	/**
	 * Check if the node has any attributes
	 *
	 * @return bool
	 */
	function hasAttributes() {
		return (is_array($this->attrs) && !empty($this->attrs));
	}

	/**
	 * Check if the node has a given attribute
	 *
	 * @param string $name Attribute name
	 * @return bool
	 */
	function hasAttribute($name) {
		return (is_array($this->attrs) && array_key_exists($name, $this->attrs));
	}

	/**
	 * Get all node attributes
	 *
	 * The attribute set is returned by reference, so that changes are
	 * automatically applied in the node object.
	 *
	 * @return array
	 */
	function &getAttributes() {
		return $this->attrs;
	}

	/**
	 * Get a reference to a given node attribute
	 *
	 * If the attribute doesn't exist, this method returns false.
	 *
	 * @param string $attribute Attribute name
	 * @return mixed
	 */
	function &getAttribute($attribute) {
		$false = FALSE;
		if ($this->hasAttribute($attribute))
			return $this->attrs[$attribute];
		return $false;
	}

	/**
	 * Add/replace a set of attributes in the node
	 *
	 * The new set of attributes is merged with the current ones,
	 * using {@link array_merge()}.
	 *
	 * @param array $attributes Attributes array
	 */
	function addAttributes($attributes) {
		if (TypeUtils::isHashArray($attributes))
			$this->attrs = array_merge($this->attrs, $attributes);
	}

	/**
	 * Create or modify an attribute
	 *
	 * @param string $attribute Attribute name
	 * @param mixed $value Attribute value
	 */
	function setAttribute($attribute, $value) {
		$this->attrs[$attribute] = $value;
	}

	/**
	 * Remove a given attribute
	 *
	 * @param string $attribute Attribute name
	 */
	function removeAttribute($attribute) {
		unset($this->attrs[$attribute]);
	}

	/**
	 * Get this node's parent node
	 *
	 * @return AbstractNode|NULL
	 */
	function &getParentNode() {
		return $this->parentNode;
	}

	/**
	 * Set this node's parent node
	 *
	 * @param AbstractNode &$Node New parent node
	 */
	function setParentNode(&$Node) {
		$this->parentNode =& $Node;
	}

	/**
	 * Check if the node has any child nodes
	 *
	 * @return bool
	 */
	function hasChildren() {
		return ($this->childrenCount > 0);
	}

	/**
	 * Get this node's children count
	 *
	 * @return int
	 */
	function getChildrenCount() {
		return $this->childrenCount;
	}

	/**
	 * Get all child nodes of this node
	 *
	 * The array of child nodes is returned by reference.
	 * If the are no child nodes, an empty array is returned.
	 *
	 * @return array
	 */
	function &getChildNodes() {
		$result = array();
		if ($this->childrenCount > 0)
			$result =& $this->children;
		return $result;
	}

	/**
	 * Get this node's first child
	 *
	 * @return AbstractNode|NULL
	 */
	function &getFirstChild() {
		$result = NULL;
		if ($this->childrenCount > 0)
			$result =& $this->firstChild;
		return $result;
	}

	/**
	 * Get this node's last child
	 *
	 * @return AbstractNode|NULL
	 */
	function &getLastChild() {
		$result = NULL;
		if ($this->childrenCount > 0)
			$result =& $this->lastChild;
		return $result;
	}

	/**
	 * Get this node's previous sibling
	 *
	 * @return AbstractNode|NULL
	 */
	function &getPreviousSibling() {
		return $this->previousSibling;
	}

	/**
	 * Get this node's next sibling
	 *
	 * @return AbstractNode|NULL
	 */
	function &getNextSibling() {
		return $this->nextSibling;
	}

	/**
	 * Get the child node whose index is $index
	 *
	 * @param int $index Child index
	 * @return AbstractNode|NULL
	 */
	function &getChild($index) {
		$result = FALSE;
		if (isset($this->children[$index]))
			$result =& $this->children[$index];
		return $result;
	}

	/**
	 * Get the index of a given node inside this node's children
	 *
	 * If the given node is not a child of this node, -1 is returned.
	 *
	 * @param AbstractNode $Node Node to be used in the search
	 * @access protected
	 * @return int
	 */
	function getNodeIndex($Node) {
		if ($this->childrenCount > 0) {
			$result = array_search($Node->getId(), $this->hashIndex);
			if (!TypeUtils::isFalse($result))
				return $result;
		}
		return -1;
	}

	/**
	 * Add a new child node
	 *
	 * @param AbstractNode $childNode Child node
	 * @return AbstractNode Node after being added in the tree
	 */
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

	/**
	 * Add a set of child nodes
	 *
	 * @param AbstractNode $Child,... Variable list of child nodes as arguments
	 */
	function addChildList() {
		$args = func_get_args();
		if (func_num_args() > 0) {
			foreach($args as $Child) {
				$this->addChild($Child);
			}
		}
	}

	/**
	 * Remove a child node given its index
	 *
	 * Updates child nodes related with the removed node, by fixing
	 * {@link previousSibling} and {@link nextSibling} references.
	 *
	 * @param int $index Node index
	 * @return bool If the node was successfully removed
	 */
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

	/**
	 * Create a clone of the node
	 *
	 * @param bool $deep Recurse into child nodes
	 * @return AbstractNode Node's clone
	 */
	function cloneNode($deep=TRUE) {
		$Clone =& $this->createClone();
		if ($deep) {
			for ($i=0; $i<$this->children; $i++)
				$Clone->addChild($this->children[$i]);
		}
		return $Clone;
	}

	/**
	 * Method used to create a clone of the node
	 *
	 * This is used by {@link cloneNode} and is overriden
	 * by child classes, like {@link XmlNode}.
	 *
	 * @return AbstractNode
	 */
	function &createClone() {
		$Clone = new AbstractNode($this->name, $this->attrs, NULL);
		return $Clone;
	}
}
?>