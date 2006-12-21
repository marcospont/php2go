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

import('php2go.datetime.TimeCounter');

/**
 * Represents an object that can be persisted in the session scope
 *
 * A SessionObject instance contains a name, set of properties and
 * a set of time counters. Once registered through the {@link register()}
 * method, the object is saved in the session scope and can be updated
 * when necessary through the {@link update()} method. The serialization
 * and unserialization routines are totally transparent to the developer.
 *
 * @package session
 * @uses TimeCounter
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class SessionObject extends PHP2Go
{
	/**
	 * Object name
	 *
	 * @var string
	 */
	var $name;

	/**
	 * Whether the object is saved in the session
	 *
	 * @var bool
	 */
	var $registered = FALSE;

	/**
	 * Object's properties
	 *
	 * @var array
	 */
	var $properties = array();

	/**
	 * Object's time counters
	 *
	 * @var array
	 */
	var $timeCounters = array();

	/**
	 * Class constructor
	 *
	 * Search the given object name in the session scope. If it's found,
	 * restore all object's properties.
	 *
	 * @param string $name Object name
	 * @return SessionObject
	 */
	function SessionObject($name) {
		parent::PHP2Go();
		if (is_array($_SESSION[$name])) {
			$props = $_SESSION[$name];
			foreach ($props as $name => $value) {
				if ($name == 'timeCounters') {
					foreach ($value as $tName => $tData) {
						$this->timeCounters[$tName] = new TimeCounter($tData['begin']);
						$this->timeCounters[$tName]->active = $tData['active'];
						$this->timeCounters[$tName]->end = $tData['end'];
					}
				} else {
					$this->{$name} = $value;
				}
			}
		} else {
			$this->name = $name;
			$this->registered = FALSE;
		}
	}

	/**
	 * Check if the object is saved in the session
	 *
	 * @return bool
	 */
	function isRegistered() {
		$this->registered = (array_key_exists($this->name, $_SESSION));
		return $this->registered;
	}

	/**
	 * Registers the object in the session scope
	 *
	 * @return bool Success/failure
	 */
	function register() {
		$this->_serialize();
		if (array_key_exists($this->name, $_SESSION)) {
			$this->registered = TRUE;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Removes the object from the session scope
	 *
	 * @return bool Success/failure
	 */
	function unregister() {
		if (array_key_exists($this->name, $_SESSION)) {
			unset($_SESSION[$this->name]);
			$this->registered = FALSE;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Synchronizes the object's properties in the session
	 *
	 * It's recommended to call this method every time
	 * an object property is created, changed or removed,
	 * or when the script shuts down.
	 */
	function update() {
		$this->_serialize();
	}

	/**
	 * Check if a given property exists
	 *
	 * @param string $name Property name
	 * @return bool
	 */
	function hasProperty($name) {
		return (isset($this->properties[$name]));
	}

	/**
	 * Get all object's properties
	 *
	 * @return array
	 */
	function getProperties() {
		return $this->properties;
	}

	/**
	 * Get the value of an object's property
	 *
	 * @param string $name Property name
	 * @param bool $throwError Whether to throw an error when the property is not found
	 * @return mixed
	 */
	function getPropertyValue($name, $throwError=TRUE) {
		if (!isset($this->properties[$name])) {
			if ($throwError)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SESSION_PROPERTY_NOT_FOUND', array($name, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			return $this->properties[$name];
		}
	}

	/**
	 * Get an object's property by reference
	 *
	 * @param string $name Property name
	 * @param bool $throwError Whether to throw an error when the property is not found
	 * @return mixed
	 */
	function &getPropertyValueByRef($name, $throwError=TRUE) {
		$result = FALSE;
		if (isset($this->properties[$name]))
			$result =& $this->properties[$name];
		elseif ($throwError)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SESSION_PROPERTY_NOT_FOUND', array($name, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
		return $result;
	}

	/**
	 * Compare the value of an object's property with another value
	 *
	 * @param string $name Property name
	 * @param mixed $compare Compare value
	 * @param bool $strict Whether to use strict type comparison
	 * @return bool Equal/different
	 */
	function comparePropertyValue($name, $compare, $strict=FALSE) {
		$value = $this->getPropertyValue($name, FALSE);
		return ($strict ? $value === $compare : $value == $compare);
	}

	/**
	 * Creates a new object property, or a set of properties
	 *
	 * @param string $name Property name, or hash array of properties
	 * @param mixed $value Property value
	 */
	function createProperty($name, $value='') {
		if (is_array($name)) {
			foreach($name as $prop => $val)
				$this->properties[$prop] = $val;
		} else {
			$this->properties[$name] = $value;
		}
	}

	/**
	 * Set an object's property, or a set of properties
	 *
	 * @param string $name Property name, or hash array of properties
	 * @param mixed $value Property value
	 */
	function setPropertyValue($name, $value='') {
		if (TypeUtils::isHashArray($name)) {
			foreach ($name as $name => $value) {
				if (!$this->hasProperty($name))
					$this->createProperty($name, $value);
				else
					$this->properties[$name] = $value;
			}
		} else {
			if (!$this->hasProperty($name))
				$this->createProperty($name, $value);
			else
				$this->properties[$name] = $value;
		}
	}

	/**
	 * Deletes an object property
	 *
	 * @param string $name Property name
	 */
	function deleteProperty($name) {
		unset($this->properties[$name]);
	}

	/**
	 * Creates a new time counter on the object
	 *
	 * Time counters can be used to track time elapsed
	 * between actions performed by the application user.
	 *
	 * @param string $name Time counter name
	 * @param int $begin Initial timestamp
	 */
	function createTimeCounter($name, $begin=0) {
		$this->timeCounters[$name] = new TimeCounter($begin);
	}

	/**
	 * Get a time counter by name
	 *
	 * @param string $name Time counter name
	 * @param bool $throwError Whether to throw an error when the time counter is not found
	 * @return TimeCounter|bool
	 */
	function &getTimeCounter($name, $throwError=TRUE) {
		$result = FALSE;
		if (isset($this->timeCounters[$name]))
			$result =& $this->timeCounters[$name];
		elseif ($throwError)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SESSION_TIMECOUNTER_NOT_FOUND', array($name, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
		return $result;
	}

	/**
	 * Serializes the object and save it in the session scope
	 *
	 * @access private
	 */
	function _serialize() {
		$vars = get_object_vars($this);
		$tmp = $vars['timeCounters'];
		foreach ($tmp as $name => $obj) {
			$vars['timeCounters'][$name] = array(
				'begin' => $obj->begin,
				'active' => $obj->active,
				'end' => (isset($obj->end) ? $obj->end : NULL)
			);
		}
		$_SESSION[$this->name] = $vars;
	}
}
?>