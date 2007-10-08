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
 * Iterator implementation
 *
 * The ListIterator class expects an instance of the AbstractList in the
 * constructor. Based on it, the object can iterate through the list members
 * forward and backwards, and jump to a specific list index.
 *
 * @package util
 * @uses AbstractList
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ListIterator extends PHP2Go
{
	/**
	 * Current cursor position
	 *
	 * @var int
	 * @access private
	 */
	var $current;

	/**
	 * List instance
	 *
	 * @var object AbstractList
	 * @access private
	 */
	var $_List;

	/**
	 * Class constructor
	 *
	 * @param AbstractList $List List instance
	 * @return ListIterator
	 */
	function ListIterator($List) {
		parent::PHP2Go();
		if (!TypeUtils::isInstanceOf($List, 'AbstractList')) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'AbstractList'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$this->current = -1;
			$this->_List = $List;
		}
	}

	/**
	 * Gets current cursor position
	 *
	 * @return int
	 */
	function getCurrentIndex() {
		return $this->current;
	}

	/**
	 * Moves to a given list index
	 *
	 * @param int $index Target index
	 * @return bool
	 */
	function moveToIndex($index) {
		if ($index >= 0 && $index < $this->_List->size()) {
			$this->current = $index - 1;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Gets current list element
	 *
	 * @return mixed
	 */
	function current() {
		return ($this->getCurrentIndex() >= 0) ? $this->_List->get($this->current + 1) : NULL;
	}

	/**
	 * Check if there's a next item to be read
	 *
	 * @return bool
	 */
	function hasNext() {
		return ($this->current < ($this->_List->size() - 1));
	}

	/**
	 * Moves cursor to the next position and returns the fetched element.
	 *
	 * Returns FALSE when the cursor is already at the last position.
	 *
	 * @return mixed
	 */
	function next() {
		if ($this->hasNext()) {
			return $this->_List->get(++$this->current);
		} else {
			return FALSE;
		}
	}

	/**
	 * Gets next list index
	 *
	 * @return int
	 */
	function nextIndex() {
		return $this->current + 1;
	}

	/**
	 * Checks if there's a previous item before the current one
	 *
	 * @return bool
	 */
	function hasPrevious() {
		return ($this->current > 0);
	}

	/**
	 * Moves the cursor to the previous index and return the fetched element
	 *
	 * Returns FALSE when the cursor is already at the first position.
	 *
	 * @return unknown
	 */
	function previous() {
		if ($this->hasPrevious()) {
			return $this->_List->get(--$this->current);
		} else {
			return FALSE;
		}
	}

	/**
	 * Gets the previous list index
	 *
	 * @return int
	 */
	function previousIndex() {
		return $this->current - 1;
	}
}
?>