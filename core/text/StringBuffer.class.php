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

/**
 * Implements a mutable sequence of characters
 *
 * A string buffer can be initialized with a string value and a
 * capacity (length). The methods provided by the class are able
 * to change the buffer, by expanding and collapsing it, reading,
 * changing or inserting characters.
 *
 * @package text
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class StringBuffer extends PHP2Go
{
	/**
	 * Buffer contents
	 *
	 * @var string
	 * @access private
	 */
	var $string = "";

	/**
	 * Buffer capacity
	 *
	 * @var int
	 * @access private
	 */
	var $capacity;

	/**
	 * Class constructor
	 *
	 * If $initStr and $initCapacity are missing, the initial
	 * capacity of the buffer will be 16 chars.
	 *
	 * @param string $initStr Initial buffer contents
	 * @param int $initCapacity Initial capacity
	 * @return StringBuffer
	 */
	function StringBuffer($initStr="", $initCapacity=NULL) {
		parent::PHP2Go();
		$this->capacity = (TypeUtils::parseInteger($initCapacity) > 0) ? $initCapacity : 16;
		if (!empty($initStr)) {
			$this->string = $initStr;
			if (strlen($this->string) > $this->capacity) {
				$this->capacity = strlen($this->string);
			}
		}
	}

	/**
	 * Get current buffer capacity
	 *
	 * @return int
	 */
	function capacity() {
		return $this->capacity;
	}

	/**
	 * Get current buffer length
	 *
	 * @return int
	 */
	function length() {
		return strlen($this->string);
	}

	/**
	 * Get a substring of the buffer and copy to a given variable
	 *
	 * The chars between $srcBegin and $srcEnd positions are
	 * copied to the $dst variable. Optionally, a position in
	 * the target variable can be provided through the $dstBegin
	 * argument.
	 *
	 * @param int $srcBegin Substring start
	 * @param int $srcEnd Substring end
	 * @param string &$dst Target variable
	 * @param int $dstBegin Target position
	 */
	function getChars($srcBegin, $srcEnd, &$dst, $dstBegin=NULL) {
		if (TypeUtils::isInteger($srcBegin) && TypeUtils::isInteger($srcEnd) && $srcBegin >= 0 && $srcEnd >= $srcBegin) {
			$chars = $this->subSequence($srcBegin, $srcEnd);
			$dstBuffer = new StringBuffer($dst);
			$dstBuffer->insert(TypeUtils::ifNull($dstBegin, 0), $chars);
			$dst = $dstBuffer->__toString();
		}
	}

	/**
	 * Read a char from the buffer, given its index
	 *
	 * @param int $index Char index
	 * @return string
	 */
	function charAt($index) {
		if ($index < 0 || $index >= $this->length()) {
			return NULL;
		} else {
			return $this->string{$index};
		}
	}

	/**
	 * Get the index of the first occurence of a substring in the buffer
	 *
	 * Returns -1 when the substring is not found.
	 *
	 * @param string $str Substring
	 * @param int $fromIndex Start index, to be used to perform the search
	 * @return int
	 */
	function indexOf($str, $fromIndex=NULL) {
		if (!TypeUtils::isNull($fromIndex)) {
			if (TypeUtils::isInteger($fromIndex) && $fromIndex >= 0 && $fromIndex < $this->length()) {
				$searchBase = $this->subString($fromIndex);
				$offset = $fromIndex;
			} else {
				$searchBase = $this->string;
				$offset = 0;
			}
		} else {
			$searchBase = $this->string;
			$offset = 0;
		}
		$pos = strpos($searchBase, $str);
		if ($pos !== FALSE)
			return $offset + $pos;
		return -1;
	}

	/**
	 * Get the index of the last occurence of a substring in the buffer
	 *
	 * Returns -1 if the substring is not present in the buffer.
	 *
	 * @param string $str Substring
	 * @param int $fromIndex Start index, to be used to perform the search
	 * @return int
	 */
	function lastIndexOf($str, $fromIndex=NULL) {
		if (!TypeUtils::isNull($fromIndex)) {
			if (TypeUtils::isInteger($fromIndex) && $fromIndex >= 0 && $fromIndex < $this->length()) {
				$searchBase = $this->subString($fromIndex);
				$offset = $fromIndex;
			} else {
				$searchBase = $this->string;
				$offset = 0;
			}
		} else {
			$searchBase = $this->string;
			$offset = 0;
		}
		$pos = strrpos($searchBase, $str);
		if ($pos !== FALSE)
			return $offset + $pos;
		return -1;
	}

	/**
	 * Get a substring from the buffer, starting at $start
	 *
	 * Returns NULL if $start is lower than 0 or greater
	 * than the buffer length.
	 *
	 * @param int $start Start index
	 * @return string
	 */
	function subString($start) {
		if (TypeUtils::isInteger($start) && $start >= 0 && $start < $this->length()) {
			return substr($this->string, $start);
		}
		return NULL;
	}

	/**
	 * Get a substring from the buffer, starting at $start and ending at $end
	 *
	 * Returns NULL if one or both limits are invalid.
	 *
	 * @param int $start Start index
	 * @param int $end End index
	 * @return string
	 */
	function subSequence($start, $end) {
		if (TypeUtils::isInteger($start) && TypeUtils::isInteger($end) && $start >= 0 && $end >= $start) {
			return substr($this->string, $start, ($end-$start+1));
		}
		return NULL;
	}

	/**
	 * Changes a given position of the buffer
	 *
	 * @param int $index Index
	 * @param string $ch Character
	 */
	function setCharAt($index, $ch) {
		if ($index >= 0 && $index < $this->length() && strlen($ch) == 1)
			$this->string{$index} = $ch;
	}

	/**
	 * Changes the length of the buffer
	 *
	 * If the new length is greater than the current length, a sequence
	 * of "\x00" chars is used to fill the buffer. If it is lower, the
	 * buffer content is truncated.
	 *
	 * @param int $newLength New length
	 */
	function setLength($newLength) {
		if (TypeUtils::isInteger($newLength) && $newLength > 0) {
			if ($newLength > $this->length()) {
				$this->string = str_pad($this->string, $newLength, "\x00", STR_PAD_RIGHT);
				if ($newLength > $this->capacity()) {
					$this->capacity = $newLength;
				}
			} else {
				$this->string = substr($this->string, 0, $newLength);
			}
		}
	}

	/**
	 * Appends a value in the end of the buffer
	 *
	 * @param mixed $appendValue Value to append
	 */
	function append($appendValue) {
		if (is_object($appendValue) && method_exists($appendValue, '__toString')) {
			$this->string .= $appendValue->__toString();
		} else if (is_array($appendValue) || is_resource($appendValue) || is_bool($appendValue)) {
			$this->string .= var_export($appendValue, TRUE);
		} else {
			$this->string .= strval($appendValue);
		}
		if (strlen($this->string) > $this->capacity) {
			$this->capacity = strlen($this->string);
		}
	}

	/**
	 * Inserts a value in a given index of the buffer
	 *
	 * @param int $index Insert index
	 * @param mixed $insertValue Insert value
	 */
	function insert($index, $insertValue) {
		if (TypeUtils::isInteger($index) && $index >= 0 && $index <= $this->length()) {
			if (is_object($insertValue) && method_exists($insertValue, '__toString')) {
				$insertValue = $insertValue->__toString();
			} else if (is_array($insertValue) || is_resource($insertValue) || is_bool($insertValue)) {
				$insertValue = var_export($insertValue, TRUE);
			} else {
				$insertValue = strval($insertValue);
			}
			$this->string = $this->subSequence(0, $index-1) . $insertValue . $this->subString($index);
		}
	}

	/**
	 * Removes a substring from the buffer
	 *
	 * @param int $start Start index
	 * @param int $end End index
	 */
	function delete($start, $end) {
		if (TypeUtils::isInteger($start) && TypeUtils::isInteger($end) && $start >= 0 && $end >= $start) {
			$this->string = $this->subSequence(0, $start-1) . $this->subString($end);
		}
	}

	/**
	 * Removes a char from the buffer
	 *
	 * @param int $index Char index
	 */
	function deleteCharAt($index) {
		if (TypeUtils::isInteger($index) && $index >= 0 && $index < $this->length()) {
			$this->delete($index, $index+1);
		}
	}

	/**
	 * Checks if the current buffer capacity is greater than a given lower bound
	 *
	 * If $minimum is an integer value greater than zero, the buffer
	 * capacity will be set as the max value between $minimum and the
	 * double of the current capacity plus 2.
	 *
	 * @param int $minimum Lower bound
	 */
	function ensureCapacity($minimum) {
		if (TypeUtils::isInteger($minimum) && $minimum > 0) {
			$this->capacity = max($minimum, ($this->capacity()*2)+2);
		}
	}

	/**
	 * Inverts the buffer contents
	 */
	function reverse() {
		$this->string = strrev($this->string);
	}

	/**
	 * Converts the buffer into a regular string value
	 *
	 * @return string
	 */
	function __toString() {
		return $this->string;
	}
}
?>