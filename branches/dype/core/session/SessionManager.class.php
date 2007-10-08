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
 * Manages session variables
 *
 * @package session
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class SessionManager extends PHP2Go
{
	/**
	 * Get the singleton of the SessionManager class
	 *
	 * @return SessionManager
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new SessionManager();
		return $instance;
	}

	/**
	 * Read a property of a {@link SessionObject} instance
	 * stored in the session scope
	 *
	 * The $qualifiedName variable must be composed by the
	 * name of the session object plus the name of the property,
	 * separated by a double colon sign.
	 *
	 * @param string $qualifiedName Qualified name
	 * @return mixed
	 * @static
	 */
	function getObjectProperty($qualifiedName) {
		if (ereg("([^\:]+)\:(.+)", $qualifiedName, $matches)) {
			import('php2go.session.SessionObject');
			$Session = new SessionObject($matches[1]);
			if ($Session->isRegistered() && $Session->hasProperty($matches[2]))
				return $Session->getPropertyValue($matches[2]);
		}
		return NULL;
	}

	/**
	 * Get current session ID
	 *
	 * @return string
	 */
	function getSessionId() {
		return @session_id();
	}

	/**
	 * Get current session cookie name
	 *
	 * @return string
	 */
	function getSessionName() {
		return @session_name();
	}

	/**
	 * Get current session save path
	 *
	 * @return string
	 */
	function getSessionSavePath() {
		return @session_save_path();
	}

	/**
	 * Check if a given variable exists in the session scope
	 *
	 * @param string $var Variable name
	 * @return bool
	 */
	function isRegistered($var) {
		return (array_key_exists($var, $_SESSION));
	}

	/**
	 * Get the value of a session variable
	 *
	 * @param string $name Variable name
	 * @param mixed $fallback Fallback value
	 * @return mixed
	 */
	function getValue($name, $fallback=FALSE) {
		if ($this->isRegistered($name))
			return $_SESSION[$name];
		return $fallback;
	}

	/**
	 * Create or replace a session variable
	 *
	 * @param string $name Variable name
	 * @param mixed $value Variable value
	 * @return mixed Old value, if existent
	 */
	function setValue($name, $value) {
		if (array_key_exists($name, $_SESSION))
			$old = $_SESSION[$name];
		else
			$old = NULL;
		$_SESSION[$name] = $value;
		return $old;
	}

	/**
	 * Create or replace a session variable
	 *
	 * @param string $name Variable name
	 * @param mixed $value Variable value
	 */
	function register($name, $value) {
		$_SESSION[$name] = $value;
	}

	/**
	 * Removes a variable from the session scope
	 *
	 * @param string $name Variable name
	 */
	function unregister($name) {
		if (array_key_exists($name, $_SESSION)) {
			unset($_SESSION[$name]);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Destroys all session variables and kills the current active session
	 *
	 * @static
	 */
	function destroy() {
		if (isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time()-86400, '/');
		session_unset();
		$_SESSION = array();
		@session_destroy();
	}
}
?>