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

/**
 * Abstract authorizer
 *
 * This class is composed by a set of abstract methods that define a common
 * authorization interface for applications. The developer should create
 * a class extending Authorizer, implement all possible methods and put
 * its path in the configuration setting AUTH.AUTHORIZER_PATH. Doing this,
 * all subsequent calls to {@link getInstance} will return the user-defined
 * authorizer, instead of the default (and abstract) one.
 *
 * IMPORTANT: always call Authorizer constructor inside your custom class.
 *
 * <code>
 * /* your class definition {@*}
 * class MyAuthorizer extends Authorizer
 * {
 *   function authorizeAction($action) {
 *     $role = $this->User->getActiveRole();
 *     if ($action == 'reports' && $role != 'admin')
 *       return FALSE;
 *     return TRUE;
 *   }
 * }
 * /* adding to the configuration settings {@*}
 * $P2G_USER_CFG['AUTH']['AUTHORIZER_PATH'] = "path.to.MyAuthorizer";
 * /* using it inside your code {@*}
 * $authorizer =& Authorizer::getInstance();
 * if (!$authorizer->authorizeAction('reports')) {
 *   /* do something {@*}
 * }
 * </code>
 *
 * @package php2go.auth
 * @uses User
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Authorizer extends PHP2Go
{
	/**
	 * Holds the current logged user
	 *
	 * @var object User
	 */
	var $User = NULL;

	/**
	 * Class constructor
	 *
	 * Must be called from the child class in order to initialize the {@link $User} property.
	 *
	 * @return Authorizer
	 */
	function Authorizer() {
		parent::PHP2Go();
		$this->User =& User::getInstance();
	}

	/**
	 * Builds/returns the singleton of the application authorizer
	 *
	 * Tries to use the classpath defined in the configuration setting
	 * AUTH.AUTHORIZER_PATH, which should point to a custom class
	 * created by the developer. If this setting is missing, the
	 * abstract Authorizer bundled with PHP2Go will be used.
	 *
	 * IMPORTANT: <b>always</b> use this method when an instance of the
	 * authorizer is needed.
	 *
	 * @return Authorizer
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			// tries to load the custom authorizer using the configuration settings
			if ($authorizerClassPath = PHP2Go::getConfigVal('AUTH.AUTHORIZER_PATH', FALSE, FALSE)) {
				if ($authorizerClass = classForPath($authorizerClassPath)) {
					$instance = new $authorizerClass();
					if (!TypeUtils::isInstanceOf($instance, 'Authorizer'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHORIZER', $authorizerClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHORIZER_PATH', $authorizerClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			}
			// use the default Authorizer
			else {
				$instance = new Authorizer();
			}
		}
		return $instance;
	}

	/**
	 * Abstract method to grant/revoke access to a given URI
	 *
	 * @param string $uri URI
	 * @return bool
	 */
	function authorizeUri($uri) {
		return TRUE;
	}

	/**
	 * Abstract method to grant/revoke access to a given action
	 *
	 * This method could be used to check access to ID-based actions
	 * stored in a database or in the user session.
	 * <code>
	 * function authorizeAction($action) {
	 *   $allowedActions = $this->User->getPropertyValue('actions');
	 *   return (in_array($action, $allowedActions));
	 * }
	 * </code>
	 *
	 * @param mixed $action Action
	 * @return bool
	 */
	function authorizeAction($action) {
		return TRUE;
	}

	/**
	 * Abstract method to grant/revoke access to an object/action pair
	 *
	 * This method should be used to authorize a given $action on a
	 * given $object.
	 *
	 * @param mixed $object Object
	 * @param mixed $action Action
	 * @return bool
	 */
	function authorizeObjectAction($object, $action) {
		return TRUE;
	}

	/**
	 * Abstract method to grant/revoke access to an application module
	 *
	 * @param mixed $module Module
	 * @return bool
	 */
	function authorizeModule($module) {
		return TRUE;
	}

	/**
	 * Abstract method to grant/revoke access to a module/action pair
	 *
	 * @param mixed $module Module
	 * @param mixed $action Action
	 * @return bool
	 */
	function authorizeModuleAction($module, $action) {
		return TRUE;
	}

	/**
	 * Abstract method to authorize form conditional sections
	 *
	 * Conditional form sections expect an attribute called 'evalfunction' that
	 * should point to a function or static method that evaluates its visibility.
	 * When the form engine doesn't find this function, the application authorizer
	 * is loaded and this method gets called.
	 *
	 * So, it's possible to centralize evaluation of conditional sections visibility
	 * in a single point by creating an authorizer and implementing this method.
	 *
	 * @param FormSection $Section Form section being evaluated
	 * @return bool
	 */
	function authorizeFormSection($Section) {
		return TRUE;
	}
}
?>