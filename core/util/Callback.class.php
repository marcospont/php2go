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
 * Dynamic method (object + method name)
 */
define('CALLBACK_DYNAMIC_METHOD', 1);
/**
 * Static method (class name + method name)
 */
define('CALLBACK_STATIC_METHOD', 2);
/**
 * Procedural function
 */
define('CALLBACK_FUNCTION', 3);

/**
 * Common interface to different types of callable functions and methods
 *
 * Examples:
 * <code>
 * /* creating a callback from a procedural function {@*}
 * $func = new Callback('strtoupper');
 * $upper = $func->invoke('hello world');
 *
 * /* creating a callback from a static method {@*}
 * $method = new Callback('MyClass::myMethod');
 * /* invoke it with 3 arguments {@*}
 * $method->invoke(array($a, $b, $c), TRUE);
 *
 * /* creating a callback from an object's method {@*}
 * $obj = new MyClass();
 * $callback = new Callback(array($obj, 'myMethod'));
 * $callback->invoke($arg);
 * </code>
 *
 * @package util
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Callback extends PHP2Go
{
	/**
	 * Current callback
	 *
	 * @var mixed
	 */
	var $function = NULL;

	/**
	 * Current callback type
	 *
	 * @var int
	 */
	var $type;

	/**
	 * Indicates if the current callback is valid
	 *
	 * @var bool
	 */
	var $valid = FALSE;

	/**
	 * Throw an error when an invalid callback is detected
	 *
	 * @var bool
	 */
	var $throwErrors = TRUE;

	/**
	 * Class constructor
	 *
	 * @param string|array $function Callback specification
	 * @return Callback
	 */
	function Callback($function=NULL) {
		parent::PHP2Go();
		if (!TypeUtils::isNull($function)) {
			$this->function = $function;
			$this->_parseFunction();
		}
	}

	/**
	 * Get the singleton of the Callback class
	 *
	 * @return Callback
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new Callback();
		return $instance;
	}

	/**
	 * Get the type of the current callback
	 *
	 * @return int
	 */
	function getType() {
		return $this->type;
	}

	/**
	 * Checks if the current callback is of a given type
	 *
	 * @param int $type Type
	 * @return bool
	 */
	function isType($type) {
		return ($this->type == $type);
	}

	/**
	 * Checks if the current callback is valid
	 *
	 * @return bool
	 */
	function isValid() {
		return $this->valid;
	}

	/**
	 * Changes or defines the callback
	 *
	 * @param string|array $function Callback
	 */
	function setFunction($function) {
		$this->function = $function;
		$this->_parseFunction();
	}

	/**
	 * Enable/disable errors when invalid callbacks are detected
	 *
	 * @param bool $setting Boolean value
	 */
	function setThrowErrors($setting) {
		$this->throwErrors = (bool)$setting;
	}

	/**
	 * Invoke the callback with the given arguments
	 *
	 * The $args parameter will be used as argument
	 * of the function or method. To invoke the callback
	 * with N arguments, $args must be an array and
	 * $nargs must be TRUE.
	 *
	 * @param mixed $args Callback arguments
	 * @param bool $nargs Multiple arguments flag
	 * @return mixed Callback return
	 */
	function invoke($args=NULL, $nargs=FALSE) {
		if ($this->isValid())
			return (TypeUtils::isNull($args) ? call_user_func($this->function) : ($nargs && TypeUtils::isArray($args) ? call_user_func_array($this->function, $args) : call_user_func($this->function, $args)));
		return NULL;
	}

	/**
	 * Invoke the callback with a single parameter by reference
	 *
	 * @param mixed &$argument Callback argument
	 * @return mixed Callback return
	 */
	function invokeByRef(&$argument) {
		if ($this->isValid()) {
			$params = array();
			$params[] =& $argument;
			return call_user_func_array($this->function, $params);
		}
		return NULL;
	}

	/**
	 * Builds a string representation of the object
	 *
	 * @return string
	 */
	function __toString() {
		if (isset($this->function))
			return (is_array($this->function) ? (is_object($this->function[0]) ? get_class($this->function[0]) . '=>' . $this->function[1] : $this->function[0] . '=>' . $this->function[1]) : $this->function);
		return NULL;
	}

	/**
	 * Defines type and validity of the current callback
	 *
	 * @access private
	 */
	function _parseFunction() {
		if (TypeUtils::isArray($this->function) && sizeof($this->function) == 2) {
			if (TypeUtils::isObject($this->function[0])) {
				$this->type = CALLBACK_DYNAMIC_METHOD;
				$this->valid = (method_exists($this->function[0], $this->function[1]));
			} else {
				if (!IS_PHP5) {
					$this->function[0] = strtolower($this->function[0]);
					$this->function[1] = strtolower($this->function[1]);
				}
				$this->type = CALLBACK_STATIC_METHOD;
				$this->valid = (in_array($this->function[1], TypeUtils::toArray(get_class_methods($this->function[0]))));
			}
		} else {
			$tmp = (!System::isPHP5() ? strtolower($this->function) : $this->function);
			if (strpos($tmp, '::') !== FALSE) {
				$this->type = CALLBACK_STATIC_METHOD;
				$this->function = explode('::', $tmp);
				$this->valid = (in_array($this->function[1], TypeUtils::toArray(get_class_methods($this->function[0]))));
			} else {
				$this->type = CALLBACK_FUNCTION;
				$this->valid = (function_exists($this->function));
			}
		}
		if (!$this->valid && $this->throwErrors)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_CALLBACK', $this->__toString()), E_USER_ERROR, __FILE__, __LINE__);
	}
}