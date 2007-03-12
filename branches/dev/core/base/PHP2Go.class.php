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
 * Initializes the stack of object destructors
 * @global array $GLOBALS['PHP2Go_destructor_list']
 */
$GLOBALS['PHP2Go_destructor_list'] = array();
/**
 * Initializes the stack of shutdown functions
 * @global array $GLOBALS['PHP2Go_shutdown_funcs']
 */
$GLOBALS['PHP2Go_shutdown_funcs'] = array();

/**
 * Root class of the hierarchy
 *
 * Main root class, ancestor of almost all classes in the framework.
 * It offers utility methods to handle objects. Besides, it exposes static
 * methods that provide access to configuration settings and language
 * entries and throw/log application errors.
 *
 * The PHP2Go class is imported inside p2gConfig.php.
 *
 * @package base
 * @uses Conf
 * @uses LanguageBase
 * @uses PHP2GoError
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class PHP2Go
{
	/**
	 * Class constructor
	 *
	 * @return PHP2Go
	 */
	function PHP2Go() {
	}

	/**
	 * Get the class name of this object
	 *
	 * @return string
	 * @deprecated Use {@link getClassName} instead
	 */
	function getObjectName() {
		return get_class($this);
	}

	/**
	 * Get the class name of this object
	 *
	 * @return string
	 */
	function getClassName() {
		return get_class($this);
	}

	/**
	 * Get the name of the parent class of this object
	 *
	 * @return string
	 */
	function getParentName() {
		return get_parent_class($this);
	}

	/**
	 * Check if the object is an instance of a given class
	 *
	 * @param string $className Class name
	 * @param bool $recurse Recurse into class ancestors
	 * @return bool
	 */
	function isA($className, $recurse=TRUE) {
		$thisClass = get_class($this);
		$otherClass = (System::isPHP5() ? $className : strtolower($className));
		if ($recurse)
			return ($thisClass == $otherClass || is_subclass_of($this, $otherClass));
		return ($thisClass == $otherClass);
	}

	/**
	 * Check if the object is an instance of a subclass of $className
	 *
	 * @param string $className Class name
	 * @return bool
	 */
	function isSubclassOf($className) {
		return is_subclass_of($this, $className);
	}

	/**
	 * Compare this object with another one
	 *
	 * @param object $object Comparison object
	 * @return bool
	 */
	function equals($object) {
		if (is_object($object) && (serialize($this) == serialize($object)))
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Serialize this object's contents in a file
	 *
	 * @param string $path File path
	 * @return bool Returns the save operation result
	 * @see retrieve
	 */
	function store($path='') {
		$filePath = ($path != '' ? tempnam($path, 'php2go_') : tempnam(System::getTempDir(), 'php2go_'));
		$objData = serialize($this);
		if ($filePath != "" && $objData) {
			if ($fp = @fopen($filePath, "wb")) {
				fwrite($fp, $objData);
				@fclose($fp);
				return $filePath;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * Restore an object from a file
	 *
	 * If the file doesn't exist or can't be read, an error will be thrown.
	 * Returns false in any case of error.
	 *
	 * @param string $objFile File path
	 * @return object|false
	 */
	function retrieve($objFile) {
		$ptr = @fopen($objFile, 'rb');
		if ($ptr) {
			$objData = fread($ptr, filesize($objFile));
			return unserialize($objData);
		}
		PHP2Go::raiseError($this->getLangVal('ERR_CANT_FIND_SERIALIZATION_FILE', $objFile), E_USER_ERROR, __FILE__, __LINE__);
		return FALSE;
	}

	/**
	 * Create a clone of this object
	 *
	 * This method is PHP4 only. When running PHP2Go under PHP5,
	 * please use the {@link clone()} native function.
	 *
	 * @return object
	 */
	function cloneObject() {
		return $this;
	}

	/**
	 * Create a hash code of this object
	 *
	 * @return string
	 */
	function hashCode() {
		return bin2hex(mhash(MHASH_CRC32, serialize($this)));
	}

	/**
	 * Generate a string representation of the object
	 *
	 * @return string
	 */
	function __toString() {
		ob_start();
		var_dump($this);
		return ob_get_clean();
	}

	/**
	 * Store an error message in a log file
	 *
	 * If $userFile and $userLine are missing, the error will be logged
	 * as if it happened inside the PHP2GoError class. To get the correct
	 * results, use the __FILE__ and __LINE__ constants.
	 *
	 * @param string $logFile Log file path
	 * @param string $msg Error message
	 * @param int $type Error type
	 * @param string $userFile Error file
	 * @param int $userLine Error line number
	 * @param string $extra Extra/detailed error message
	 * @uses PHP2GoError
	 * @static
	 */
	function logError($logFile, $msg, $type=E_USER_ERROR, $userFile='', $userLine=NULL, $extra='') {
		$Error =& PHP2GoError::getInstance();
		if (isset($this))
			$Error->setObject($this);
		$Error->setMessage($msg, $extra);
		$Error->setType($type);
		$Error->setFile($userFile);
		$Error->setLine($userLine);
		$Error->log($logFile);
	}

	/**
	 * Raise an application error
	 *
	 * Internally, this will perform a call to {@link trigger_error()}.
	 *
	 * If $userFile and $userLine are missing, the error will be logged
	 * as if it happened inside the PHP2GoError class. To get the correct
	 * results, use the __FILE__ and __LINE__ constants.
	 *
	 * @param string $msg Error message
	 * @param int $type Error type
	 * @param string $userFile Error file
	 * @param int $userLine Error line number
	 * @param string $extra Extra/detailed error message
	 * @uses PHP2GoError
	 * @static
	 */
	function raiseError($msg, $type=E_USER_ERROR, $userFile='', $userLine=NULL, $extra='') {
		$Error = new PHP2GoError();
		if (isset($this))
			$Error->setObject($this);
		$Error->setMessage($msg, $extra);
		$Error->setType($type);
		$Error->setFile($userFile);
		$Error->setLine($userLine);
		$Error->raise();
	}

	/**
	 * Read an entry from the global configuration settings
	 *
	 * The $variable argument could be a simple variable name or
	 * a path in the configuration tree:
	 * <code>
	 * $title = PHP2Go::getConfigVal('TITLE');
	 * $defaultDbConn = PHP2Go::getConfigVal('DATABASE.DEFAULT_CONNECTION');
	 * $tplCacheDir = PHP2Go::getConfigVal('TEMPLATES.CACHE.DIR');
	 * </code>
	 *
	 * @param string $variable Variable name or path
	 * @param bool $throwError Whether to throw an error when the entry is not found
	 * @param bool $acceptEmpty Whether to accept empty values as valid or not
	 * @uses Conf::getConfig()
	 * @return mixed
	 * @static
	 */
	function getConfigVal($variable, $throwError = TRUE, $acceptEmpty = TRUE) {
		$Conf =& Conf::getInstance();
		$value = $Conf->getConfig($variable);
		if ($value == FALSE) {
			if ($throwError) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_CFG_VAL', $variable), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			return "";
		} elseif (empty($value) && !$acceptEmpty) {
			if ($throwError) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_CFG_VAL', $variable), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			return $value;
		} else {
			return $value;
		}
	}

	/**
	 * Read an entry from the global language table
	 *
	 * This method can read entries of the PHP2Go language domain
	 * of entries included in any user-defined domain.
	 * <code>
	 * $value = PHP2Go::getLangVal('LANGUAGE_KEY');
	 * $value = PHP2Go::getLangVal('PATH.TO_THE.LANGUAGE_KEY');
	 * $value = PHP2Go::getLangVal('DOMAIN:KEY.PATH');
	 * </code>
	 *
	 * @param string $entryName Entry name or path
	 * @param string|array $bindVars Bind var(s) for the message
	 * @param bool $throwError Throw an error when the language entry is not found
	 * @uses LanguageBase::getLanguageValue()
	 * @return string|NULL
	 * @static
	 */
	function getLangVal($entryName, $bindVars=NULL, $throwError=TRUE) {
		$Lang =& LanguageBase::getInstance();
		$value = $Lang->getLanguageValue($entryName, $bindVars);
		if ($value === NULL) {
			$value = '';
			if ($throwError)
				PHP2Go::raiseError("Can't find language entry <b>'{$entryName}'</b>", E_USER_ERROR, __FILE__, __LINE__);
		}
		return $value;
	}

	/**
	 * Utility method to generate unique IDs with optional prefixes
	 *
	 * Unique IDs generated by this method are simple sequences that
	 * last until the script shuts down. This is better than calling
	 * {@link uniqid()} several times, which could affect overall
	 * performance.
	 *
	 * @param string $prefix ID prefix
	 * @return int Generated unique ID
	 * @static
	 */
	function generateUniqueId($prefix='php2go_') {
		static $uniqueId;
		static $uniqueIdPrefix;
		if ((string)$prefix == "") {
			if (!isset($uniqueId))
				$uniqueId = 1;
			else
				$uniqueId++;
			return $uniqueId;
		} else {
			if (!isset($uniqueIdPrefix))
				$uniqueIdPrefix = array();
			if (!isset($uniqueIdPrefix[$prefix]))
				$uniqueIdPrefix[$prefix] = 1;
			else
				$uniqueIdPrefix[$prefix]++;
			return $prefix . $uniqueIdPrefix[$prefix];
		}
	}

	/**
	 * Register an object destructor
	 *
	 * @param object &$object Object
	 * @param string $methodName Destructor method name
	 * @static
	 */
	function registerDestructor(&$object, $methodName) {
		if (!System::isPHP5() || $methodName != '__destruct') {
			global $PHP2Go_destructor_list;
			if (is_object($object) && method_exists($object, $methodName)) {
				$newItem[0] =& $object;
				$newItem[1] = $methodName;
				$PHP2Go_destructor_list[] =& $newItem;
			}
		}
	}

	/**
	 * Check if a given destructor has already been registered
	 *
	 * @param string $methodName Method name
	 * @return bool
	 * @static
	 */
	function hasDestructor($methodName) {
		global $PHP2Go_destructor_list;
		foreach($PHP2Go_destructor_list as $destructor) {
			if ($destructor[1] == $methodName)
				return TRUE;
		}
		return FALSE;
	}

	/**
	 * Register a shutdown function
	 *
	 * The $function argument can be a procedural function, an array
	 * of class and method or an array of object an method.
	 *
	 * Shutdown functions are registered in a global stack called $PHP2Go_shutdown_funcs
	 *
	 * @param mixed $function Function definition
	 * @param array $args Function arguments
	 * @static
	 */
	function registerShutdownFunc($function, $args=array()) {
		global $PHP2Go_shutdown_funcs;
		if (is_array($function) && sizeof($function) == 2) {
			if (is_object($function[0]) && method_exists($function[0], $function[1])) {
				$newItem[0] = &$function[0];
				$newItem[1] = $function[1];
				$newItem[2] = $args;
				$PHP2Go_shutdown_funcs[] = $newItem;
			} else {
				$PHP2Go_shutdown_funcs[] = array($function, $args);
			}
		} else {
			$PHP2Go_shutdown_funcs[] = array($function, $args);
		}
	}
}
?>