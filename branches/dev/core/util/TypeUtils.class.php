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
 * Collection of methods to deal with variables and data types (casting, conversion, sanity check)
 *
 * @package util
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TypeUtils extends PHP2Go
{
	/**
	 * Get the type of a given value
	 *
	 * @param mixed $value Value
	 * @return string Type
	 * @static
	 */
	function getType($value) {
		return gettype($value);
	}

	/**
	 * Determines if a given value is float
	 *
	 * If $strict is set to FALSE, a string value that is written
	 * in a valid float format, will be accepted and converted to
	 * float.
	 *
	 * @param mixed &$value Value to be tested
	 * @param bool $strict Strong or weak test
	 * @return bool
	 * @static
	 */
	function isFloat(&$value, $strict=FALSE) {
		$locale = localeconv();
		$dp = $locale['decimal_point'];
		$exp = "/^\-?[0-9]+(\\" . $dp . "[0-9]+)?$/";
		if (preg_match($exp, $value)) {
			if (!$strict && !is_float($value)) {
				$value = TypeUtils::parseFloat($value);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Converts a given value to float
	 *
	 * @param mixed $value Value
	 * @return float
	 * @static
	 */
	function parseFloat($value) {
		if (is_string($value)) {
			$locale = localeconv();
			if ($locale['decimal_point'] != '.') {
				$value = str_replace($locale['decimal_point'], '.', $value);
			}
		}
		return floatval($value);
	}

	/**
	 * Converts the given value to a positive floating point number
	 *
	 * @param mixed $value Value
	 * @return float
	 * @static
	 */
	function parseFloatPositive($value) {
		return abs(floatval($value));
	}

	/**
	 * Checks if a given value is integer
	 *
	 * If $strict is set to FALSE, a string written as a valid
	 * integer number will be accepted and converted to int.
	 *
	 * @param mixed &$value Value to be tested
	 * @param bool $strict Strong or weak test
	 * @return bool
	 * @static
	 */
	function isInteger(&$value, $strict=FALSE) {
		$exp = "/^\-?[0-9]+$/";
		if (preg_match($exp, $value)) {
			if (!$strict && !is_int($value)) {
				$value = TypeUtils::parseInteger($value);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Converts a given value to integer
	 *
	 * @param mixed $value Input value
	 * @return int
	 * @static
	 */
	function parseInteger($value) {
		return intval($value);
	}

	/**
	 * Converts a given value into a positive integer number
	 *
	 * @param mixed $value Input value
	 * @return int
	 * @static
	 */
	function parseIntegerPositive($value) {
		return abs(intval($value));
	}

	/**
	 * Checks if a given value is a string
	 *
	 * @param mixed $value Value to be tested
	 * @return bool
	 * @static
	 */
	function isString($value) {
		return is_string($value);
	}

	/**
	 * Converts a given value into a string
	 *
	 * @param mixed $value Input value
	 * @return string
	 * @static
	 */
	function parseString($value) {
		return strval($value);
	}

	/**
	 * Checks if a given value is an array
	 *
	 * @param mixed $value Value to be tested
	 * @return bool
	 * @static
	 */
	function isArray($value) {
		return is_array($value);
	}

	/**
	 * Checks if a given value is a hash array
	 *
	 * Even having numeric keys, an array that
	 * contains gaps in the indexes sequence can be
	 * considered a hash array. Non-integer keys,
	 * obviously, also represent hash arrays.
	 *
	 * @param mixed $value Value to be tested
	 * @return bool
	 * @static
	 */
	function isHashArray($value) {
		if (is_array($value) && sizeof($value)) {
			$i = 0;
			$keys = array_keys($value);
			foreach ($keys as $k=>$v) {
				if ($v !== $i) {
					return TRUE;
				}
				$i++;
			}
		}
		return FALSE;
	}

	/**
	 * Converts a given value to array
	 *
	 * @param mixed $value Input value
	 * @return array
	 * @static
	 */
	function toArray($value) {
		return is_array($value) ? $value : array($value);
	}

	/**
	 * Checks if a given value is an object
	 *
	 * @param mixed $value Value to be tested
	 * @return bool
	 * @static
	 */
	function isObject($value) {
		return is_object($value);
	}

	/**
	 * Checks if an object is instance of a given class
	 *
	 * The $className argument is case-sensitive. Under PHP4, it will
	 * be converted to lowercase before the comparison.
	 *
	 * @param object $object Object
	 * @param string $className Class name
	 * @param string $recurse Whether parent classes should be considered
	 * @return bool
	 * @static
	 */
	function isInstanceOf($object, $className, $recurse=TRUE) {
		if (!is_object($object))
			return FALSE;
		$objClass = get_class($object);
		$otherClass = (System::isPHP5() ? $className : strtolower($className));
		if ($recurse)
			return ($objClass == $otherClass || is_subclass_of($object, $otherClass));
		return ($objClass == $otherClass);
	}

	/**
	 * Checks if a given value is a resource
	 *
	 * Returns the resource type in case of success.
	 *
	 * @param mixed $value Value to be tested
	 * @return mixed
	 * @static
	 */
	function isResource($value) {
		if (is_resource($value))
			return get_resource_type($value);
		return FALSE;
	}

	/**
	 * Checks if a value is NULL
	 *
	 * When $strict is set to FALSE, a simple value
	 * comparison is made. Otherwise, the
	 * datatype will also be considered.
	 *
	 * @param mixed $value Value to be tested
	 * @param bool $strict Strong or weak test
	 * @return bool
	 * @static
	 */
	function isNull($value, $strict=FALSE) {
		return ($strict) ? (NULL === $value) : (NULL == $value);
	}

	/**
	 * Returns a "default" value when a given value is NULL
	 *
	 * @param mixed $value Value to be tested
	 * @param mixed $default Default value, when $value is NULL
	 * @return mixed
	 * @static
	 */
	function ifNull($value, $default=NULL) {
		if ($value === NULL)
			return $default;
		return $value;
	}

	/**
	 * Checks if a value is boolean
	 *
	 * @param mixed $value Value to be tested
	 * @return bool
	 * @static
	 */
	function isBoolean($value) {
		return ($value === TRUE || $value === FALSE);
	}

	/**
	 * Checks if a value is TRUE
	 *
	 * @param mixed $value Value to be tested
	 * @return bool
	 * @static
	 */
	function isTrue($value) {
		return ($value === TRUE);
	}

	/**
	 * Checks if a value is FALSE
	 *
	 * @param mixed $value Value to be tested
	 * @return bool
	 * @static
	 */
	function isFalse($value) {
		return ($value === FALSE);
	}

	/**
	 * Returns a "default" value when a given value is FALSE
	 *
	 * @param mixed $value Value to be tested
	 * @param mixed $default Default value, when $value is FALSE
	 * @return mixed
	 * @static
	 */
	function ifFalse($value, $default=FALSE) {
		if ($value === FALSE)
			return $default;
		return $value;
	}

	/**
	 * Converts a value to boolean
	 *
	 * @param mixed $value Input value
	 * @return bool
	 * @static
	 */
	function toBoolean($value) {
		return (bool)$value;
	}

	/**
	 * Checks if a value is empty
	 *
	 * @param mixed $value Value to be tested
	 * @return bool
	 * @static
	 */
	function isEmpty($value) {
		$result = empty($value);
		return $result;
	}
}
?>