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
 * Serializes PHP variables using JSON
 *
 * JSON stands for Javascript Object Notation. It's a lightweight
 * data transfer format, widely used to interchange information between
 * client and server side by the most common AJAX libraries.
 *
 * Examples:
 * <code>
 * // prints true
 * print JSONEncoder::encode(TRUE);
 * // prints [1,2,3]
 * print JSONEncoder::encode(array(1, 2, 3));
 * // prints {a: 1, b: "foo", c: true}
 * print JSONEncoder::encode(array('a'=>1, 'b'=>'foo', 'c'=>TRUE));
 * </code>
 *
 * @package util
 * @subpackage json
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class JSONEncoder extends PHP2Go
{
	/**
	 * Whether errors should be thrown or not
	 *
	 * @var bool
	 */
	var $throwErrors = TRUE;

	/**
	 * Holds already visited objects, to avoid cyclic references
	 *
	 * @var array
	 * @access private
	 */
	var $objRef = array();

	/**
	 * Class constructor
	 *
	 * @return JSONEncoder
	 */
	function JSONEncoder() {
		parent::PHP2Go();
	}

	/**
	 * Shortcut method to encode a variable
	 *
	 * @param mixed $value Input value
	 * @param bool $throwErrors Abort execution upon errors or not
	 * @return string Generated JSON string
	 * @static
	 */
	function encode($value, $throwErrors=TRUE) {
		$encoder = new JSONEncoder();
		$encoder->throwErrors = $throwErrors;
		return $encoder->encodeValue($value);
	}

	/**
	 * Prepares a Javascript identifier (variable, constant or function) to be encoded
	 *
	 * @param string $name Variable name
	 * @return object
	 * @static
	 */
	function jsIdentifier($name) {
		$obj = new stdclass;
		$obj->__json__ = $name;
		return $obj;
	}

	/**
	 * Prepares a Javascript function body to be encoded
	 *
	 * @param string $body Function body
	 * @param array $inputArgs Function input arguments
	 * @return object
	 * @static
	 */
	function jsFunction($body, $inputArgs=array()) {
		$obj = new stdclass;
		$tmp = array();
		if (is_array($inputArgs)) {
			$encoder = new JSONEncoder();
			foreach ($inputArgs as $arg)
				$tmp[] = $encoder->encodeValue($arg);
		}
		$obj->__json__ = "function(" . join(',', $tmp) . ") { " . $body . " }";
		return $obj;
	}

	/**
	 * Encodes a given value using JSON syntax
	 *
	 * Values that can't be encoded will produce a
	 * NULL result or throw an error.
	 *
	 * @param mixed $value Input value
	 * @return string Generated JSON string
	 */
	function encodeValue($value) {
		if (is_bool($value)) {
			return ($value ? 'true' : 'false');
		} elseif ($value === NULL) {
			return 'null';
		} elseif (is_numeric($value)) {
			$type = gettype($value);
			$value = ($type == 'integer' ? intval($value) : doubleval($value));
			return str_replace(',', '.', $value);
		} elseif (is_string($value)) {
			return $this->_encodeString($value);
		} elseif (is_array($value)) {
			return $this->_encodeArray($value);
		} elseif (is_object($value)) {
			if ($this->_wasVisited($value)) {
				if ($this->throwErrors)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_CYCLE', get_class($value)), E_USER_ERROR, __FILE__, __LINE__);
				return NULL;
			}
			$this->objRef[] = $value;
			return $this->_encodeObject($value);
		} else {
			if ($this->throwErrors)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_ENCODE', gettype($value)), E_USER_ERROR, __FILE__, __LINE__);
			return NULL;
		}
	}

	/**
	 * Encodes a PHP object
	 *
	 * @param object $obj PHP object
	 * @return string Serialized object
	 * @access private
	 */
	function _encodeObject(&$obj) {
		if (isset($obj->__json__)) {
			return $obj->__json__;
		} else {
			$vars = get_object_vars($obj);
			$items = array();
			foreach ($vars as $name => $value) {
				$encoded = $this->encodeValue($value);
				if ($encoded !== NULL)
					$items[] = '"' . strval($name) . '":' . $encoded;
			}
			return '{' . join(',', $items) . '}';
		}
	}

	/**
	 * Encodes a PHP array
	 *
	 * Hash arrays are mapped to anonymous objects in JSON. A PHP array
	 * is serialized as a JSON array only when all keys are numeric and
	 * no gaps are found.
	 *
	 * @param array $arr PHP array
	 * @return string Serialized array
	 * @access private
	 */
	function _encodeArray(&$arr) {
		$items = array();
		if (TypeUtils::isHashArray($arr)) {
			foreach ($arr as $key => $value) {
				$encoded = $this->encodeValue($value);
				if ($encoded !== NULL)
					$items[] = '"' . strval($key) . '":' . $encoded;
			}
			return '{' . implode(',', $items) . '}';
		} else {
			for ($i=0,$s=sizeof($arr); $i<$s; $i++) {
				$encoded = $this->encodeValue($arr[$i]);
				if ($encoded !== NULL)
					$items[] = $encoded;
			}
			return '[' . implode(',', $items) . ']';
		}
	}

	/**
	 * Encodes a string
	 *
	 * @param string $str PHP string
	 * @access private
	 * @return string
	 */
	function _encodeString(&$str) {
		$result = '';
		$len = strlen($str);
		for ($c=0; $c<$len; $c++) {
			$ord = ord($str[$c]);
			if ($ord == 0x08) {
				$result .= '\b';
			} elseif ($ord == 0x09) {
				$result .= '\t';
			} elseif ($ord == 0x0A) {
				$result .= '\n';
			} elseif ($ord == 0x0C) {
				$result .= '\f';
			} elseif ($ord == 0x0D) {
				$result .= '\r';
			} elseif ($ord == 0x22 || $ord == 0x2F || $ord == 0x5C) {
				$result .= '\\' . $str[$c];
			} else {
				$result .= $str[$c];
			}
		}
		return '"' . $result . '"';
	}

	/**
	 * Checks if an object was already visited
	 *
	 * @param object &$obj PHP object
	 * @access private
	 * @return bool
	 */
	function _wasVisited(&$obj) {
		foreach ($this->objRef as $ref) {
			if ($ref === $obj)
				return TRUE;
		}
		return FALSE;
	}
}
?>