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

import('php2go.validation.AbstractValidator');

/**
 * Interface to all framework's validators
 *
 * The Validator class contains static methods to test a value or
 * a list of values against a validator, based just on the validator
 * class path, the validator arguments and the value(s) to be validated.
 *
 * Examples:
 * <code>
 * /* simple value {@*}
 * $value = 'foo@bar.com';
 * if (Validator::validate('php2go.validation.EmailValidator', $value)) {
 *   print 'valid e-mail!';
 * } else {
 *   print 'bad e-mail syntax!';
 * }
 *
 * /* multiple values {@*}
 * $values = array(1.24, 2.01);
 * $wrongValues = array();
 * if (Validator::validateMultiple(
 *     'php2go.validation.MaxValidation', $values,
 *     array('max'=>2), $wrongValues)
 * ) {
 *   print 'values ok';
 * } else {
 * 	 print 'max exceeded! => wrong values: ' . var_dump($wrongValues);
 * }
 * </code>
 *
 * @package validation
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Validator extends PHP2Go
{
	/**
	 * Error stack
	 *
	 * @var array
	 */
	var $errorStack = array();

	/**
	 * Get the singleton of the Validator class
	 *
	 * @return Validator
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new Validator();
		return $instance;
	}

	/**
	 * Validates a value against a given validator
	 *
	 * The $path argument must contain the path to the validator
	 * class, using "dot path" notation.
	 *
	 * @param string $path Validator class path
	 * @param mixed &$value Value to be validated
	 * @param array $params Validator arguments
	 * @param string $userMessage User defined error message
	 * @return bool
	 * @static
	 */
	function validate($path, &$value, $params=NULL, $userMessage=NULL) {
		$validatorClass = basename(str_replace('.', '/', $path));
		// imports the validator
		if (!import($path))
			return FALSE;
		// verify if the class exists
		if (!class_exists($validatorClass)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_INSTANTIATE_VALIDATOR', $validatorClass), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$Validator = new $validatorClass($params);
		// verify if the execute method exists
		if (!method_exists($Validator, 'execute')) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_VALIDATOR', array($validatorClass, $validatorClass)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// run the validation
		$result = (bool)$Validator->execute($value);
		if ($result === FALSE) {
			// custom message
			if (!$userMessage !== NULL) {
				Validator::addError($userMessage);
			// getError method
			} elseif (method_exists($Validator, 'getError')) {
				$errMsg = $Validator->getError();
				if (!empty($errMsg))
					Validator::addError($errMsg);
			}
		}
		return $result;
	}

	/**
	 * Used by the forms API to perform validations on form fields
	 *
	 * The FormField instance is passed by reference, along with the
	 * validator path and arguments. Some validators might change
	 * the value of the field.
	 *
	 * @param FormField &$Field Form field
	 * @param string $path Validator path
	 * @param array $params Validator arguments
	 * @param string $userMessage Customized error message
	 * @return bool
	 * @static
	 */
	function validateField(&$Field, $path, $params=NULL, $userMessage=NULL) {
		if (TypeUtils::isInstanceOf($Field, 'FormField')) {
			// rule validation
			if ($path == 'php2go.validation.RuleValidator') {
				$name = $Field->getName();
				return Validator::validate($path, $name, $params, $userMessage);
			} else {
				$value = $Field->getValue();
				$params['fieldLabel'] = $Field->getLabel();
				$result = Validator::validate($path, $value, $params, $userMessage);
				$currentValue = $Field->getValue();
				if ($result !== FALSE) {
					if (is_array($value) && $value === $currentValue)
						$Field->setValue($value);
					if (is_string($value) && strcmp(strval($value), strval($currentValue)))
						$Field->setValue($value);
					$Field->setValue($value);
				}
				return $result;
			}
		}
		return FALSE;
	}

	/**
	 * Runs a validation on multiple values
	 *
	 * The failed values are collected and returned through the
	 * 4th parameter, $wrongValues, that sould be passed by reference.
	 *
	 * Returns TRUE only when the validation returned TRUE for all values.
	 *
	 * @param string $path Validator path
	 * @param array $value Values to be validated
	 * @param array $params Validator arguments
	 * @param array $wrongValues Used to return the wrong values
	 * @return bool
	 * @static
	 */
	function validateMultiple($path, $value, $params=NULL, &$wrongValues) {
		$wrongValues = array();
		$validatorClass = basename(str_replace('.', '/', $path));
		$value = (array)$value;
		// imports the validator
		if (!import($path))
			return FALSE;
		// verifies if the validator class exists
		if (!class_exists($validatorClass)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_INSTANTIATE_VALIDATOR', $validatorClass), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// verifies if the execute method exists
		$Validator = new $validatorClass($params);
		if (!method_exists($Validator, 'execute')) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_VALIDATOR', array($validatorClass, $validatorClass)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$result = TRUE;
		$hasErrorGetter = method_exists($Validator, 'getError');
		foreach ($value as $k=>$v) {
			if (!$Validator->execute($v)) {
				$wrongValues[$k] = $v;
				if ($hasErrorGetter) {
					$errMsg = $Validator->getError();
					if (!empty($errMsg))
						Validator::addError($errMsg);
				}
				$result = FALSE;
			}
		}
		return $result;
	}

	/**
	 * Registers a validator error
	 *
	 * @param string $msg Error message
	 * @static
	 */
	function addError($msg) {
		$Validator =& Validator::getInstance();
		$Validator->errorStack[] = $msg;
	}

	/**
	 * Gets the collected error messages
	 *
	 * @return array
	 * @static
	 */
	function getErrors() {
		$Validator =& Validator::getInstance();
		return $Validator->errorStack;
	}

	/**
	 * Clears all validation errors
	 *
	 * @static
	 */
	function clearErrors() {
		$Validator =& Validator::getInstance();
		$Validator->errorStack = array();
	}
}
?>