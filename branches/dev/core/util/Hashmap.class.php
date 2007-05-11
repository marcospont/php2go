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
 * Implementation of a hash table, that maps keys to values
 *
 * The keys must be not null and not empty, and can't be duplicated.
 * The values can be of any type.
 *
 * @package util
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Hashmap extends PHP2Go
{
	/**
	 * Map elements
	 *
	 * @var array
	 * @access private
	 */
	var $elements = array();

	/**
	 * Class constructor
	 *
	 * @param array $arr Initializes the map with the given array
	 * @return Hashmap
	 */
	function Hashmap($arr = array()) {
		parent::PHP2Go();
		$this->putAll($arr);
	}

	/**
	 * Adds or changes a key-value pair
	 *
	 * @param string $key Key
	 * @param mixed $value Value
	 * @return bool
	 */
	function put($key, $value) {
		$key = strval($key);
		if (!empty($key)) {
			$this->elements[$key] = $value;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Adds or changes a key passing the value by reference
	 *
	 * @param string $key Key
	 * @param mixed &$value Value
	 * @return bool
	 */
	function putRef($key, &$value) {
		$key = strval($key);
		if (!empty($key)) {
			$this->elements[$key] =& $value;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Adds all members of a collection in the map
	 *
	 * @param array $collection Collection
	 * @return bool
	 */
	function putAll($collection) {
		$result = TRUE;
		if (TypeUtils::isInstanceOf($collection, 'Hashmap'))
			$collection = $collection->toArray();
		if (TypeUtils::isHashArray($collection)) {
			foreach ($collection as $key => $value)
				$result = ($result && $this->put($key, $value));
		}
		return $result;
	}

	/**
	 * Clears the map
	 */
	function clear() {
		$this->elements = array();
	}

	/**
	 * Get an element of the map, given its key
	 *
	 * @param string $key Key
	 * @param mixed $fallback Fallback value, to be used when the key is not found
	 * @return mixed
	 */
	function get($key, $fallback=NULL) {
		$key = strval($key);
		return (array_key_exists($key, $this->elements) ? $this->elements[$key] : $fallback);
	}

	/**
	 * Get the value of a given map entry, requiring it to be of a given type
	 *
	 * The fallback value is returned when the key is not found or
	 * when the value is of a different data type.
	 *
	 * Accepted types: string, integer, float, double, array, hash,
	 * resource, boolean, bool, object, null.
	 *
	 * @param string $key Key
	 * @param string $type Required type
	 * @param mixed $fallback Fallback value
	 * @return mixed
	 */
	function assertGet($key, $type, $fallback=NULL) {
		$value = $this->get($key, $fallback);
		if ($value !== $fallback) {
			switch ($type) {
				case 'string' : return (is_string($value) ? $value : $fallback);
				case 'integer' : return (TypeUtils::isInteger($value) ? $value : $fallback);
				case 'float' :
				case 'double' : return (TypeUtils::isFloat($value) ? $value : $fallback);
				case 'array' : return (is_array($value) ? $value : $fallback);
				case 'hash' : return (TypeUtils::isHashArray($value) ? $value : $fallback);
				case 'resource' : return (is_resource($value) ? $value : $fallback);
				case 'boolean' :
				case 'bool' : return (is_bool($value) ? $value : $fallback);
				case 'object' : return (is_object($value) ? $value : $fallback);
				case 'null' : return (is_null($value) ? $value : $fallback);
				default : return $value;
			}
		}
		return $fallback;
	}

	/**
	 * Checks if a key exists, and if its value is of a given type
	 *
	 * Accepted types: string, integer, float, double, array, hash,
	 * resource, boolean, bool, object, null.
	 *
	 * @param string $key Key
	 * @param string $type Required type
	 * @return bool
	 */
	function assertType($key, $type) {
		$value = $this->get($key);
		$type = strtolower($type);
		switch ($type) {
			case 'string' : return is_string($value);
			case 'integer' : return TypeUtils::isInteger($value);
			case 'float' :
			case 'double' : return TypeUtils::isFloat($value);
			case 'array' : return is_array($value);
			case 'hash' : return TypeUtils::isHashArray($value);
			case 'resource' : return is_resource($value);
			case 'boolean' :
			case 'bool' : return is_bool($value);
			case 'object' : return is_object($value);
			case 'null' : return is_null($value);
		}
		return FALSE;
	}

	/**
	 * Get hash keys
	 *
	 * @return array
	 */
	function keys() {
		return array_keys($this->elements);
	}

	/**
	 * Get hash values
	 *
	 * @return array
	 */
	function values() {
		return array_values($this->elements);
	}

	/**
	 * Checks if a given key exists
	 *
	 * @param string $key Key
	 * @return bool
	 */
	function containsKey($key) {
		return (array_key_exists($key, $this->elements));
	}

	/**
	 * Checks if a given value exists
	 *
	 * @param mixed $value Value
	 * @param bool $strict Whether data type must be checked
	 * @return bool
	 */
	function containsValue($value, $strict=FALSE) {
		return (array_search($value, $this->elements, $strict) !== FALSE);
	}

	/**
	 * Merge the hash with another collection
	 *
	 * @param array|Hashmap $collection Collection
	 * @param bool $recursive Recursive merge
	 */
	function merge($collection, $recursive=FALSE) {
		if (TypeUtils::isInstanceOf($collection, 'Hashmap'))
			$collection = $collection->toArray();
		if (TypeUtils::isHashArray($collection)) {
			if ($recursive)
				$this->elements = array_merge_recursive($collection, $this->elements);
			else
				$this->elements = array_merge($collection, $this->elements);
		}
	}

	/**
	 * Swap the values of two map keys
	 *
	 * @param string $a First key
	 * @param string $b Second key
	 * @return bool
	 */
	function swap($a, $b) {
		if ($this->containsKey($a) && $this->containsKey($b)) {
			$tmp = $this->get($a);
			$this->put($a, $this->get($b));
			$this->put($b, $tmp);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Removes a given key from the map
	 *
	 * @param string $key Key
	 * @return bool
	 */
	function remove($key) {
		if ($this->containsKey($key)) {
			unset($this->elements[$key]);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Sorts the map
	 *
	 * @link http://www.php.net/sort
	 * @param int $flags Sort flags
	 */
	function sort($flags=SORT_REGULAR) {
		sort($this->elements, $flags);
	}

	/**
	 * Sorts the map in reverse order (highest to lowest)
	 *
	 * @param int $flags Sort flags
	 */
	function reverseSort($flags=SORT_REGULAR) {
		rsort($this->elements, $flags);
	}

	/**
	 * Sorts the map by values based on an user-defined comparison function
	 *
	 * @param string|array $callback User defined function
	 */
	function customSort($callback) {
		usort($this->elements, $callback);
	}

	/**
	 * Get map size
	 *
	 * @return int
	 */
	function size() {
		return sizeof($this->elements);
	}

	/**
	 * Checks if the map is empty
	 *
	 * @return bool
	 */
	function isEmpty() {
		return ($this->size() == 0);
	}

	/**
	 * Builds an array representation of the hash map
	 *
	 * @return array
	 */
	function toArray() {
		return $this->elements;
	}

	/**
	 * Builds a string representation of the hash map
	 *
	 * @return string
	 */
	function __toString() {
		return sprintf("Hashmap object{\n%s\n}", dumpArray($this->elements));
	}
}

?>