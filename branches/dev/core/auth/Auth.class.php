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

import('php2go.auth.User');
import('php2go.net.HttpRequest');
import('php2go.text.StringUtils');
import('php2go.util.Callback');

/**
 * Default login field name
 */
define('AUTH_DEFAULT_LOGIN_FIELD', 'username');
/**
 * Default password field name
 */
define('AUTH_DEFAULT_PASSWORD_FIELD', 'password');
/**
 * Default expiry time for the user session
 */
define('AUTH_DEFAULT_EXPIRY_TIME', 600);
/**
 * Default maximum idle time for the user session
 */
define('AUTH_DEFAULT_IDLE_TIME', 60);
/**
 * Means that a valid user session isn't present
 */
define('AUTH_STATE_INVALID', 1);
/**
 * Means that a user has just been logged in
 */
define('AUTH_STATE_LOGIN', 2);
/**
 * Means that an authentication attempt has just failed
 */
define('AUTH_STATE_ERROR', 3);
/**
 * Means that the user session has expired
 */
define('AUTH_STATE_EXPIRED', 4);
/**
 * Means that the maximum idle time was reached
 */
define('AUTH_STATE_IDLED', 5);
/**
 * Means that a valid (non expired and non idled) user session is present
 */
define('AUTH_STATE_VALID', 6);

/**
 * Base authentication class
 *
 * This is the base class for authentication operations. It's its job to control
 * authentication through a set of states, create and destroy user sessions.
 *
 * Auth is an abstract class, thus it can't be instantiated directly. Instead, the
 * applications must use one of Auth's child classes, each one defining one kind
 * of storage for auth credentials.
 *
 * The {@link getInstance} method, included in this class, must be used
 * everywhere authentication is needed, because it will return a single instance
 * of the application's authenticator (one of Auth's child classes or a custom
 * authenticator created by the developer).
 *
 * The recommended way to create an initialize the authenticator is:
 * <code>
 * $auth =& Auth::getInstance();
 * $auth->init();
 * </code>
 *
 * @package auth
 * @uses Callback
 * @uses HttpRequest
 * @uses SessionObject
 * @uses StringUtils
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 * @abstract
 */
class Auth extends PHP2Go
{
	/**
	 * Authentication state
	 *
	 * @var int
	 */
	var $authState;

	/**
	 * Callback that handles {@link AUTH_STATE_INVALID} authentication state
	 *
	 * @var object Callback
	 */
	var $loginFunction;

	/**
	 * Callback executed right after a user session is created ({@link AUTH_STATE_LOGIN})
	 *
	 * @var object Callback
	 */
	var $loginCallback;

	/**
	 * Callback executed right after an authentication attempt has failed
	 *
	 * @var object Callback
	 */
	var $errorCallback;

	/**
	 * Callback executed right after the user session is destroyed
	 *
	 * @var object Callback
	 */
	var $logoutCallback;

	/**
	 * Callback to handle user session expiration
	 *
	 * @var object Callback
	 */
	var $expiryCallback;

	/**
	 * Callback to handle user session idleness
	 *
	 * @var object Callback
	 */
	var $idlenessCallback;

	/**
	 * Callback to handle valid, non expired and non idled user sessions
	 *
	 * @var object Callback
	 */
	var $validSessionCallback;

	/**
	 * Username field to be used when fetching auth vars from request
	 *
	 * @var string
	 */
	var $loginFieldName;

	/**
	 * Password field to be used when fetching auth vars from request
	 *
	 * @var string
	 */
	var $passwordFieldName;

	/**
	 * Name to the session variable that must be created
	 *
	 * @var string
	 */
	var $sessionKeyName;

	/**
	 * Expiry time for the user session, in seconds
	 *
	 * @var int
	 */
	var $expiryTime;

	/**
	 * Idle time for the user session, in seconds
	 *
	 * @var int
	 */
	var $idleTime;

	/**
	 * Indicates if the authentication process
	 * has already been initialized through the
	 * {@link init} method
	 *
	 * @var bool
	 */
	var $initialized = FALSE;

	/**
	 * Internal reference to the object holding
	 * logged user's data
	 *
	 * @var object User
	 * @access private
	 */
	var $User = NULL;

	/**
	 * Username fetched from request
	 *
	 * @var string
	 * @access private
	 */
	var $_login;

	/**
	 * Password fetched from request
	 *
	 * @var string
	 * @access private
	 */
	var $_password;

	/**
	 * Class constructor
	 *
	 * @param string $sessionName Session name
	 * @return Auth
	 * @see getInstance
	 */
	function Auth($sessionName=NULL) {
		parent::PHP2Go();
		if ($this->isA('Auth', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Auth'), E_USER_ERROR, __FILE__, __LINE__);
		$this->authState = AUTH_STATE_INVALID;
		$this->loginFieldName = AUTH_DEFAULT_LOGIN_FIELD;
		$this->passwordFieldName = AUTH_DEFAULT_PASSWORD_FIELD;
		$this->expiryTime = TypeUtils::ifFalse(PHP2Go::getConfigVal('AUTH.EXPIRY_TIME', FALSE), AUTH_DEFAULT_EXPIRY_TIME);
		$this->idleTime = TypeUtils::ifFalse(PHP2Go::getConfigVal('AUTH.IDLE_TIME', FALSE), AUTH_DEFAULT_IDLE_TIME);
		$this->User =& User::getInstance($sessionName);
	}

	/**
	 * Builds/returns the singleton of the application authenticator
	 *
	 * Uses the class defined in the AUTH.AUTHENTICATOR_PATH config
	 * setting, or {@link AuthDb} by default. This is the recommended
	 * way to create/retrieve an authenticator
	 *
	 * The $sessionName parameter allows to define multiple authentication
	 * areas in the application. Each will store its information in a different
	 * session variable, and thus will create separated user sessions
	 *
	 * @param string $sessionName Session name
	 * @return Auth Singleton of the authenticator
	 * @static
	 */
	function &getInstance($sessionName=NULL) {
		static $instances;
		$sessionName = TypeUtils::ifNull(
			TypeUtils::ifNull(
				$sessionName, PHP2Go::getConfigVal('USER.SESSION_NAME', FALSE)
			), 'PHP2GO_USER'
		);
		if (!isset($instances))
			$instances = array();
		if (!isset($instances[$sessionName])) {
			// search the custom authenticator defined in the global configuration
			if ($authClassPath = PHP2Go::getConfigVal('AUTH.AUTHENTICATOR_PATH', FALSE, FALSE)) {
				if ($authClass = classForPath($authClassPath)) {
					$instances[$sessionName] = new $authClass($sessionName);
					if (!TypeUtils::isInstanceOf($instances[$sessionName], 'Auth'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHENTICATOR', $authClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHENTICATOR_PATH', $authClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			}
			// use the default authenticator: php2go.auth.AuthDb
			else {
				import('php2go.auth.AuthDb');
				$instances[$sessionName] = new AuthDb($sessionName);
			}
		}
		return $instances[$sessionName];
	}

	/**
	 * Get a reference to the {@link User} instance, which contains
	 * information about the current logged user
	 *
	 * @return User
	 */
	function &getCurrentUser() {
		return $this->User;
	}

	/**
	 * Get the username of the current logged user
	 *
	 * @return string
	 */
	function getCurrentUsername() {
		return $this->User->getUsername();
	}

	/**
	 * Get the current authentication state
	 *
	 * Before calling {@link init}, this method will always return
	 * {@link AUTH_STATE_INVALID}. To get correct results, always call this
	 * after initializing the authenticator
	 *
	 * @return int
	 */
	function getAuthState() {
		return $this->authState;
	}

	/**
	 * Get the elapsed time (in seconds), since the user has logged in
	 *
	 * @return int
	 */
	function getElapsedTime() {
		return $this->User->getElapsedTime();
	}

	/**
	 * Check if a valid user session is present
	 *
	 * @return bool
	 */
	function isValid() {
		return $this->User->isAuthenticated();
	}

	/**
	 * Get the expiry time for user sessions
	 *
	 * @return int
	 */
	function getExpiryTime() {
		return $this->expiryTime;
	}

	/**
	 * Set the expiry time for user sessions
	 *
	 * The value must be an amount of seconds. Use 0 to disable
	 * user session expiration
	 *
	 * @param int $seconds
	 */
	function setExpiryTime($seconds) {
		$this->expiryTime = TypeUtils::parseIntegerPositive($seconds);
	}

	/**
	 * Check if the user session is expired
	 *
	 * @return bool
	 */
	function isExpired() {
		if ($this->expiryTime > 0 && $this->User->isAuthenticated()) {
			$elapsedTime = $this->User->getElapsedTime();
			if ($elapsedTime >= $this->expiryTime)
				return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get the maximum idle time allowed for user sessions
	 *
	 * @return int
	 */
	function getIdleTime() {
		return $this->idleTime;
	}

	/**
	 * Set the maximum idle time allowed for user sessions
	 *
	 * The value must be an amount of seconds. Use 0 to disable idleness control
	 *
	 * @param int $seconds
	 */
	function setIdleTime($seconds) {
		$this->idleTime = TypeUtils::parseIntegerPositive($seconds);
	}

	/**
	 * Check if the user session is idled
	 *
	 * @return bool
	 */
	function isIdled() {
		if ($this->idleTime > 0 && $this->User->isAuthenticated()) {
			$lastIdleTime = $this->User->getLastIdleTime();
			if ($lastIdleTime >= $this->idleTime)
				return TRUE;
		}
		return FALSE;
	}

	/**
	 * Set the request field that must be used to fetch the username,
	 * when the login credentials are submitted
	 *
	 * When building your login form, the 'name' attribute of your username
	 * input must match this setting. Besides, when using database authentication
	 * through {@link AuthDb}, this will be the field name used to build
	 * the login query (where username = 'request_username')
	 *
	 * The login field defaults to {@link AUTH_DEFAULT_LOGIN_FIELD}
	 *
	 * @param string $loginFieldName
	 */
	function setLoginFieldName($loginFieldName) {
		if (trim($loginFieldName) != '')
			$this->loginFieldName = trim($loginFieldName);
	}

	/**
	 * Set the request field that must be used to fetch the password,
	 * when the login credentials are submitted
	 *
	 * When building your login form, the 'name' attribute of your password
	 * input must match this setting. Besides, when authenticating against
	 * a database through {@link AuthDb}, this will be the password field name
	 * of the login query
	 *
	 * The password field defaults to {@link AUTH_DEFAULT_PASSWORD_FIELD}
	 *
	 * @param string $passwordFieldName
	 */
	function setPasswordFieldName($passwordFieldName) {
		if (trim($passwordFieldName) != '')
			$this->passwordFieldName = $passwordFieldName;
	}

	/**
	 * Set the callback to handle an invalid authentication state
	 *
	 * Tipically, invalid session callbacks should show a login screen,
	 * by displaying a form or redirecting to another page
	 *
	 * $loginFunction must be a valid callback: a procedural function,
	 * a class::method pair or an $object,method array.
	 *
	 * @param mixed $loginFunction Invalid session callback
	 * @param bool $replace Whether to replace the existent callback
	 */
	function setLoginFunction($loginFunction, $replace=TRUE) {
		if ($replace || !isset($this->loginFunction))
			$this->loginFunction = new Callback($loginFunction);
	}

	/**
	 * Set the callback the must be called when a new user session is created
	 *
	 * A typical behaviour of a login callback is redirecting to a secure page.
	 * This callback will receive a single parameter which is a reference to
	 * a {@link User} object. This object contains information about the user
	 * that has just been logged in.
	 *
	 * $loginCallback must be a valid callback: a procedural function,
	 * a class::method pair or an $object,method array.
	 *
	 * @param mixed $loginCallback Login callback
	 * @param bool $replace Whether to replace the existent callback
	 */
	function setLoginCallback($loginCallback, $replace=TRUE) {
		if ($replace || !isset($this->loginFunction))
			$this->loginCallback = new Callback($loginCallback);
	}

	/**
	 * Set the authentication failure callback
	 *
	 * This callback will receive a {@link User} instance as parameter. This
	 * object contains information about the user that attempted to authenticate.
	 *
	 * $errorCallback must be a valid callback (procedural function, class::method
	 * or array($object, method))
	 *
	 * @param mixed $errorCallback Authentication failure callback
	 * @param bool $replace Whether to replace the existent callback
	 */
	function setErrorCallback($errorCallback, $replace=TRUE) {
		if ($replace || !isset($this->errorCallback))
			$this->errorCallback = new Callback($errorCallback);
	}

	/**
	 * Set the logout callback
	 *
	 * This callback is executed inside {@link logout}, and receives
	 * an {@link User} instance as parameter. This object contains information
	 * about the user that has logged out.
	 *
	 * $logoutCallback must be a valid callback (procedural function, class::method
	 * or array($object, method))
	 *
	 * @param mixed $logoutCallback Logout callback
	 * @param bool $replace Whether to replace the existent callback
	 */
	function setLogoutCallback($logoutCallback, $replace=TRUE) {
		if ($replace || !isset($this->logoutCallback))
			$this->logoutCallback = new Callback($logoutCallback);
	}

	/**
	 * Set the expired session callback
	 *
	 * Receives an {@link User} instance as parameter. When called, the
	 * user session has already been destroyed.
	 *
	 * $expiryCallback must be a valid callback (procedural function, class::method
	 * or array($object, method)
	 *
	 * @param mixed $expiryCallback Expiry callback
	 * @param bool $replace Whether to replace the existent callback
	 */
	function setExpiryCallback($expiryCallback, $replace=TRUE) {
		if ($replace || !isset($this->expiryCallback))
			$this->expiryCallback = new Callback($expiryCallback);
	}

	/**
	 * Set the idled session callback
	 *
	 * Receives an {@link User} instance as parameter. When called, the
	 * user session has already been destroyed.
	 *
	 * $idlenessCallback must be a valid callback (procedural function, class::method
	 * or array($object, method)
	 *
	 * @param mixed $idlenessCallback Idleness callback
	 * @param bool $replace Whether to replace the existent callback
	 */
	function setIdlenessCallback($idlenessCallback, $replace=TRUE) {
		if ($replace || !isset($this->idlenessCallback))
			$this->idlenessCallback = new Callback($idlenessCallback);
	}

	/**
	 * Callback executed when the authenticator detects a valid,
	 * non expired and non idled user session
	 *
	 * Receives a reference to the logged user (a {@link User} instance).
	 * So, it could be used to add or modify user properties.
	 *
	 * $validSessionCallback must be a valid callback (procedural function, class::method
	 * or array($object, method)
	 *
	 * @param mixed $validSessionCallback Valid session callback
	 */
	function setValidSessionCallback($validSessionCallback) {
		$this->validSessionCallback = new Callback($validSessionCallback);
	}

	/**
	 * Initialize the authenticator
	 *
	 * This method detects and updates the {@link $authState}. According
	 * to the calculated state, it will create, modify or destroy the user
	 * session and execute the proper callback:
	 * # if the user session is invalid, tries to fetch the login credentials from the request
	 * # if the credentials are present, query the authenticator. If the user is valid, create
	 *   the user session and execute the login callback. Otherwise, execute the authentication
	 *   failure callback
	 * # if the session is valid and expired, destroy it and execute the expiry callback
	 * # if the session is valid and idled, destroy it and execute the idleness callback
	 * # otherwise, execute the valid session callback
	 */
	function init() {
		if (!$this->initialized) {
			$this->_fetchAuthVars();
			// invalid session
			if (!$this->isValid()) {
				// if the login credentials were found in the request
				if (isset($this->_login) && isset($this->_password))
					$this->login();
				// call invalid session callback
				elseif (isset($this->loginFunction))
					$this->loginFunction->invoke();
			}
			// valid but expired session
			elseif ($this->isExpired()) {
				$this->authState = AUTH_STATE_EXPIRED;
				$this->_handleExpiredSession();
			}
			// valid but idled session
			elseif ($this->isIdled()) {
				$this->authState = AUTH_STATE_IDLED;
				$this->_handleIdleSession();
			}
			// valid, non expired and non idled session
			else {
				$this->authState = AUTH_STATE_VALID;
				if (isset($this->validSessionCallback))
					$this->validSessionCallback->invokeByRef($this->User);
			}
			$this->initialized = TRUE;
		}
	}

	/**
	 * Internal method that tries to authenticate the user against
	 * the storage layer, using the credentials fetched from the request
	 *
	 * Calls {@link authenticate}, which is implemented by each child
	 * authenticator. If the produced result is successful, a new user session
	 * is created, and the login callback is executed. Otherwise, the authentication
	 * failure callback will be called.
	 *
	 * When the configuration setting AUTH.REGENID_ON_LOGIN is set to true,
	 * the session ID will be generated right after the new session is created
	 *
	 * @access protected
	 * @uses authenticate
	 */
	function login() {
		// executes the authentication method, implemented inside child classes
		$result = $this->authenticate();
		// failure
		if ($result === FALSE) {
			$this->authState = AUTH_STATE_ERROR;
			if (isset($this->errorCallback)) {
				$user = $this->User;
				$user->logout();
				$user->setUsername($this->_login);
				$this->errorCallback->invoke($user);
			}
		}
		// success
		else {
			$this->authState = AUTH_STATE_LOGIN;
			$this->User->authenticate($this->_login, (TypeUtils::isHashArray($result) ? $result : array()));
			// regenerate session ID
			$regenId = PHP2Go::getConfigVal('AUTH.REGENID_ON_LOGIN', FALSE);
			if ($regenId === TRUE) {
				session_regenerate_id();
				if (!version_compare(phpversion(),"4.3.3",">="))
					setcookie(session_name(), session_id(), ini_get("session.cookie_lifetime"), "/");
			}
			// login callback
			if (isset($this->loginCallback))
				$this->loginCallback->invokeByRef($this->User);
		}
	}

	/**
	 * Destroys the current user session, if existent
	 *
	 * Must be called manually by the developer inside the routine
	 * that handles the logout request. A brief example follows:
	 * <code>
	 * $auth =& Auth::getInstance();
	 * $auth->logout();
	 * </code>
	 *
	 * After destroying the user session, the method will call the
	 * defined {@link $logoutCallback}. When $rebuildLogin is set
	 * to true, it will also call {@link $loginFunction}.
	 *
	 * When the configuration setting AUTH.DESTROY_ON_LOGOUT is set
	 * to true, all the session variables will also be unset and destroyed.
	 *
	 * @param bool $rebuildLogin Whether to call the invalid session callback
	 */
	function logout($rebuildLogin=FALSE) {
		$lastUser = $this->getCurrentUser();
		$lastUser->unregister();
		if ($this->User->isAuthenticated())
			$this->User->logout();
		// invalidar a sessão
		$destroy = PHP2Go::getConfigVal('AUTH.DESTROY_ON_LOGOUT', FALSE);
		if ($destroy === TRUE)
			$this->User->destroy();
		// callbacks
		if (isset($this->logoutCallback))
			$this->logoutCallback->invoke($lastUser);
		if ($rebuildLogin && isset($this->loginFunction))
			$this->loginFunction->invoke($lastUser);
	}

	/**
	 * Abstract method that verifies if the fetched credentials point to a valid user
	 *
	 * This method is implemented in each authenticator (Auth's child classes). Anything
	 * different from false will signify a successful login attempt.
	 *
	 * @return mixed
	 * @access protected
	 * @abstract
	 */
	function authenticate() {
		return TRUE;
	}

	/**
	 * Fetches username and password values from the request
	 *
	 * @access private
	 */
	function _fetchAuthVars() {
		$login = HttpRequest::post($this->loginFieldName);
		if ($login)
			$this->_login = $login;
		$password = HttpRequest::post($this->passwordFieldName);
		if ($password)
			$this->_password = $password;
	}

	/**
	 * Handles an expired user session
	 *
	 * @access private
	 */
	function _handleExpiredSession() {
		if (isset($this->expiryCallback)) {
			$lastUser = $this->getCurrentUser();
			$lastUser->registered = FALSE;
			$this->User->logout();
			$this->expiryCallback->invoke($lastUser);
		} else {
			$this->logout(TRUE);
		}
	}

	/**
	 * Handles an idled user session
	 *
	 * @access private
	 */
	function _handleIdleSession() {
		if (isset($this->idlenessCallback)) {
			$lastUser = $this->getCurrentUser();
			$lastUser->registered = FALSE;
			$this->User->logout();
			$this->idlenessCallback->invoke($lastUser);
		} else {
			$this->logout(TRUE);
		}
	}
}
?>