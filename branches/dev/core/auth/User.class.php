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

import('php2go.session.SessionObject');

/**
 * Base user container class
 *
 * The User class is the base container for user sessions in PHP2Go. It is used
 * by {@link Auth} class (or one of its child classes) when creating, modifying
 * and controlling a user session.
 *
 * When session is initialized, user data is read from the session scope and
 * transformed in an instance of this class.
 *
 * Whenever you need to access the user information, just call {@link getInstance}
 * and you'll get a singleton of the user container. Doing it by reference also allows
 * you to modify user session in a simple and easy way.
 * <code>
 * /* reading user info {@*}
 * $user =& User::getInstance();
 * if ($user->isRegistered()) {
 *   print $user->getUsername();
 *   print $user->getLastAccess('d/m/Y H:i:s');
 * }
 * /**
 *  * modifying user info: adding or changing properties
 *  * all changes are saved in the session scope when script shuts down
 *  {@*}
 * $user =& User::getInstance();
 * $user->createProperty('new_prop', $value);
 * $user->setPropertyValue('last_seen_uri', HttpRequest::uri());
 * </code>
 *
 * @package auth
 * @uses System
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class User extends SessionObject
{
	/**
	 * Name used to log in
	 *
	 * @var string
	 */
	var $username = NULL;

	/**
	 * Active role
	 *
	 * This is not populated by PHP2Go. However, it could be used to hold the
	 * role (or roles) assigned to the user.
	 *
	 * @var mixed
	 */
	var $activeRole = NULL;

	/**
	 * Unix timestamp user has logged in
	 *
	 * @var int
	 */
	var $loginTime = NULL;

	/**
	 * Unix timestamp of last request
	 *
	 * @var int
	 */
	var $lastAccess = NULL;

	/**
	 * Class constructor
	 *
	 * Calling this constructor directly is <b>highly unrecommended</b>. You should
	 * always call {@link getInstance} wherever you need to access the user information.
	 *
	 * @param string $sessionName Session name
	 * @return User
	 */
	function User($sessionName=NULL) {
		parent::SessionObject($sessionName);
	}

	/**
	 * Builds/returns the singleton of the user container
	 *
	 * You can create or custom user container, by defining a User child class
	 * and registering its path in the global configuration setting USER.CONTAINER_PATH.
	 * By doing this, all calls to {@link getInstance} will return a reference
	 * to a singleton of your class, instead of the default one.
	 *
	 * The $sessionName parameter allows multiple session scopes in the same
	 * application. Each different session name should be able to point to
	 * a different user information in the session scope.
	 *
	 * @param string $sessionName Session name
	 * @return User Singleton of the user container
	 * @static
	 */
	function &getInstance($sessionName=NULL) {
		$instances =& User::getInstances();
		$sessionName = TypeUtils::ifNull(
			TypeUtils::ifNull(
				$sessionName, PHP2Go::getConfigVal('USER.SESSION_NAME', FALSE)
			), 'PHP2GO_USER'
		);
		if (!isset($instances[$sessionName])) {
			// tries to load the custom container using the configuration settings
			if ($userClassPath = PHP2Go::getConfigVal('USER.CONTAINER_PATH', FALSE, FALSE)) {
				if ($userClass = classForPath($userClassPath)) {
					$instances[$sessionName] = new $userClass($sessionName);
					if (!TypeUtils::isInstanceOf($instances[$sessionName], 'User'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_USERCONTAINER', $userClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_USERCONTAINER_PATH', $userClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			}
			// user the default user container
			else {
				$instances[$sessionName] = new User($sessionName);
			}
		}
		return $instances[$sessionName];
	}

	/**
	 * Get the initialized user instances
	 *
	 * Normally, applications will use a single user session. In this case,
	 * this method would return an array with a single entry. In an application
	 * with multiple sessions, this method would return all sessions that
	 * were already initialized (both active and inactive)
	 *
	 * @return array
	 */
	function &getInstances() {
		static $instances;
		if (!isset($instances) && !is_array($instances))
			$instances = array();
		return $instances;
	}

	/**
	 * Static method automatically called when the script shuts down
	 *
	 * Updates the last access timestamp and publish the
	 * user properties in the session scope.
	 *
	 * Invalid user sessions aren't updated.
	 *
	 * @static
	 */
	function shutdown() {
		$instances =& User::getInstances();
		foreach ($instances as $name => $User) {
			if (TypeUtils::isInstanceOf($User, 'User') && $User->isAuthenticated()) {
				$User->lastAccess = System::getMicrotime();
				$User->update();
			}
		}
	}

	/**
	 * Authenticates a user
	 *
	 * Transforms this instance in a valid user container. The
	 * user's {@link $loginTime}, {@link $lastAccess}, {@link $username}
	 * and {@link $properties} are initialized.
	 *
	 * This method is used by {@link Auth::login()} when a successful
	 * login attempt is made.
	 *
	 * @param string $username Username
	 * @param array $properties User properties that should be persisted in the session scope.
	 */
	function authenticate($username, $properties=array()) {
		$this->username = $username;
		$this->loginTime = System::getMicrotime();
		$this->lastAccess = System::getMicrotime();
		foreach ((array)$properties as $name => $value)
			parent::createProperty($name, $value);
		parent::createTimeCounter('userTimeStamp');
		parent::register();
	}

	/**
	 * Destroys the user session
	 *
	 * @return bool If the user session was successfully destroyed
	 */
	function logout() {
		$result = parent::unregister();
		if ($result) {
			$this->username = NULL;
			$this->loginTime = NULL;
			$this->lastAccess = NULL;
			$this->timeCounters = array();
		}
		return $result;
	}

	/**
	 * Check if the user session is valid
	 *
	 * @return bool
	 */
	function isAuthenticated() {
		return $this->registered;
	}

	/**
	 * Abstract method that should be implemented in a child class
	 *
	 * Allows to verify if the user is assigned to a given role.
	 *
	 * The {@link $activeRole} property and the {@link isInRole}, {@link getActiveRole} and {@link setActiveRole}
	 * methods could be used to implement RABC (role-based access control) in your applications
	 *
	 * @param mixed $role
	 * @return bool
	 * @abstract
	 */
	function isInRole($role) {
		return FALSE;
	}

	/**
	 * Get the user name
	 *
	 * Returns the username provided when user was logged in.
	 *
	 * @return string Username
	 */
	function getUsername() {
		return $this->username;
	}

	/**
	 * Defines/updates the user's username
	 *
	 * @param string $username New username
	 */
	function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * Abstract method that could be used to retrieve the
	 * role (or roles) assigned to the user
	 *
	 * @return mixed
	 * @abstract
	 */
	function getActiveRole() {
		return $this->activeRole;
	}

	/**
	 * Abstract method that could be used to set/change the
	 * user's active role (or roles)
	 *
	 * @param mixed $role
	 */
	function setActiveRole($role) {
		$this->activeRole = $role;
	}

	/**
	 * Get the timestamp the user has logged in
	 *
	 * @param string $fmt Format, using {@link date()} syntax
	 * @return int|string Unix timestamp or formatted date string
	 */
	function getLoginTime($fmt=NULL) {
		if ($this->registered)
			return (empty($fmt) ? $this->loginTime : date($fmt, $this->loginTime));
		return NULL;
	}

	/**
	 * Get the timestamp of the user's last access
	 *
	 * @param string $fmt Format, using {@link date()} syntax
	 * @return int|string Unix timestamp or formatted date string
	 */
	function getLastAccess($fmt=NULL) {
		if ($this->registered)
			return (empty($fmt) ? $this->lastAccess : date($fmt, $this->lastAccess));
		return NULL;
	}

	/**
	 * Get the number of seconds since the user session was created
	 *
	 * @return int
	 */
	function getElapsedTime() {
		if ($this->registered) {
			$Counter =& parent::getTimeCounter('userTimeStamp');
			return $Counter->getElapsedTime();
		}
		return 0;
	}

	/**
	 * Get the time spent since the last user request
	 *
	 * The value returned by this method is used by {@link Auth}
	 * to control user session idleness.
	 *
	 * @return float
	 */
	function getLastIdleTime() {
		return (System::getMicrotime() - $this->lastAccess);
	}

	/**
	 * Overrides parent class implementation so that fetching
	 * inexistent properties return NULL instead of throwing
	 * an application error
	 *
	 * @param string $name Property name
	 * @return mixed
	 */
	function getPropertyValue($name) {
		if ($this->registered) {
			$property = parent::getPropertyValue($name, FALSE);
			if ($property !== FALSE)
				return $property;
		}
		return NULL;
	}

	/**
	 * Builds a string representation of the user object
	 *
	 * @return string
	 */
	function __toString() {
		return sprintf("User object {\nUsername: %s\nAuthenticated: %d\nProperties: %s}",
			$this->username, ($this->registered ? 1 : 0), dumpArray($this->properties)
		);
	}
}

/**
 * Register shutdown function
 */
PHP2Go::registerShutdownFunc(array('User', 'shutdown'));

?>