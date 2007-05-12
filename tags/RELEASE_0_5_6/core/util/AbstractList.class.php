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

import('php2go.util.ListIterator');

/**
 * Implements a mutable list of objects indexed by an integer value
 *
 * @package util
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AbstractList extends PHP2Go
{
	/**
	 * List elements
	 *
	 * @var array
	 * @access private
	 */
	var $elements;

	/**
	 * Number of list modifications
	 *
	 * @var int
	 * @access private
	 */
	var $modCount = 0;

	/**
	 * Class constructor
	 *
	 * @param array $arr Allows to initialize the list with an array of objects
	 * @return AbstractList
	 */
	function AbstractList($arr=array()) {
		parent::PHP2Go();
		$this->elements = array();
		if (is_array($arr) && !empty($arr))
			$this->addAll($arr);
	}

	/**
	 * Get the number of modifications since the list was instantiated
	 *
	 * @return int
	 */
	function getModCount() {
		return $this->modCount;
	}

	/**
	 * Adds an object in the list
	 *
	 * If the object already exists in the list, it will be duplicated. If
	 * the index exists, all values starting at this index are shifted to
	 * the right. The add index must be positive and lower or equal than
	 * the list size.
	 *
	 * @param mixed $object Object to add
	 * @param int $index Target index
	 * @return bool
	 */
	function add($object, $index=-1) {
		if ($index != -1 && TypeUtils::isInteger($index)) {
			if ($index < 0 || $index > $this->size()) {
				return FALSE;
			} else {
				if (isset($this->elements[$index])) {
					$size = $this->size();
					for ($i=$size; $i>$index; $i--) {
						$this->elements[$i] = $this->elements[$i-1];
						$this->modCount++;
					}
				}
				$this->elements[$index] = $object;
				$this->modCount++;
				return TRUE;
			}
		} else if ($index == -1) {
			$this->elements[] = $object;
			$this->modCount++;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Adds a collection of elements in the list
	 *
	 * The add index must be positive and lower or equal than the
	 * size of the list. The current indexes of the collection
	 * will be ignored.
	 *
	 * @param array $collection Collection of elements
	 * @param int $index Add index
	 * @return int Number of added elements
	 */
	function addAll($collection, $index=-1) {
		$added = 0;
		if (is_array($collection)) {
			if ($index != -1 && TypeUtils::isInteger($index)) {
				$initial = $index;
				foreach($collection as $element)
					$added += intval($this->add($element, $initial++));
			} else if ($index == -1) {
				foreach($collection as $element)
					$added += intval($this->add($element));
			}
		}
		return $added;
	}

	/**
	 * Clears the list
	 */
	function clear() {
		$this->elements = array();
		$this->modCount++;
	}

	/**
	 * Get an element of the list, given its index
	 *
	 * @param int $index Element's index
	 * @return mixed Element
	 */
	function &get($index) {
		$return = FALSE;
		if (TypeUtils::isInteger($index)) {
			if (isset($this->elements[$index])) {
				$return = $this->elements[$index];
			}
		}
		return $return;
	}

	/**
	 * Returns an iterator for this list
	 *
	 * @return ListIterator
	 */
	function &iterator() {
		$iterator = new ListIterator($this);
		return $iterator;
	}

	/**
	 * Checks if a given object is contained in the list
	 *
	 * @param mixed $object Object
	 * @return bool
	 */
	function contains($object) {
		return ($this->indexOf($object) != -1);
	}

	/**
	 * Checks if all elements of a given collection are contained in the list
	 *
	 * @param array $collection Collection
	 * @return bool
	 */
	function containsAll($collection) {
		if (is_array($collection) && !empty($collection)) {
			foreach($collection as $element) {
				if (!$this->contains($element)) return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get the index of the first occurence of an object in the list
	 *
	 * Returns -1 when the object is not found.
	 *
	 * @param mixed $object Object
	 * @return int Index
	 */
	function indexOf($object) {
		reset($this->elements);
		while (list($key, $value) = each($this->elements)) {
			if ($value === $object) return $key;
		}
		return -1;
	}

	/**
	 * Get the index of the last occurrence of an object in the list
	 *
	 * Returns -1 if the object is not found.
	 *
	 * @param mixed $object Object
	 * @return int Index
	 */
	function lastIndexOf($object) {
		$index = -1;
		reset($this->elements);
		while (list($key, $value) = each($this->elements)) {
			if ($value == $object) $index = $key;
		}
		return $index;
	}

	/**
	 * Get the highest index of the list
	 *
	 * Returns -1 when the list is empty.
	 *
	 * @return int
	 */
	function lastIndex() {
		return $this->isEmpty() ? -1 : $this->size() - 1;
	}

	/**
	 * Removes a list element, given its index
	 *
	 * @param int $index Element's index
	 * @return bool
	 */
	function remove($index) {
		if (isset($this->elements[$index])) {
			$newList = array();
			$size = $this->size();
			for ($i=0; $i<$size; $i++) {
				if ($i != $index) $newList[] = $this->get($i);
				else $this->modCount++;
			}
			$this->elements = $newList;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Removes all elements of the list
	 */
	function removeAll() {
		$this->clear();
	}

	/**
	 * Removes a set of elements given a range of indexes
	 *
	 * @param int $fromIndex Start index
	 * @param int $toIndex End index
	 * @return int Number of removed elements
	 */
	function removeRange($fromIndex, $toIndex) {
		$removed = 0;
		$size = $this->size();
		if (TypeUtils::isInteger($fromIndex) && TypeUtils::isInteger($toIndex) && $fromIndex >= 0 && $toIndex < $size) {
			$newList = array();
			for ($i=0; $i<$size; $i++) {
				if ($i < $fromIndex || $i > $toIndex) {
					$newList[] = $this->get($i);
				} else {
					$this->modCount++;
					$removed++;
				}
			}
			$this->elements = $newList;
		}
		return $removed;
	}

	/**
	 * Redefine a member of the list, given its index
	 *
	 * @param int $index Element's index
	 * @param int $object New value
	 * @return bool
	 */
	function set($index, $object) {
		$size = $this->size();
		if ($index < $size) {
			$this->elements[$index] = $object;
			$this->modCount++;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Builds a subset of elements, given a range of indexes
	 *
	 * @param int $fromIndex Start index
	 * @param int $toIndex End index
	 * @return array Subset of elements
	 */
	function subList($fromIndex, $toIndex) {
		$subList = array();
		$size = $this->size();
		if (TypeUtils::isInteger($fromIndex) && TypeUtils::isInteger($toIndex) && $fromIndex >= 0 && $toIndex < $size) {
			for ($i=$fromIndex; $i<$size && $i<=$toIndex; $i++) {
				if (isset($this->elements[$i])) {
					$subList[] = $this->get($i);
				}
			}
		}
		return $subList;
	}

	/**
	 * Get the current list size
	 *
	 * @return int
	 */
	function size() {
		return sizeof($this->elements);
	}

	/**
	 * Checks if the list is empty
	 *
	 * @return bool
	 */
	function isEmpty() {
		return ($this->size() == 0);
	}

	/**
	 * Builds an array representation of the list
	 *
	 * @return array
	 */
	function toArray() {
		return $this->elements;
	}

	/**
	 * Builds a string representation of the list
	 *
	 * @return string
	 */
	function __toString() {
		return sprintf("AbstractList object{\n%s\n}", dumpArray($this->elements));
	}
}
?>