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

	require_once('../config/config.php');

	import('php2go.auth.AuthDb');
	import('php2go.base.Document');
	import('php2go.net.HttpRequest');
	import('php2go.net.HttpResponse');
	import('php2go.net.Url');
	import('php2go.text.StringUtils');

 	/**
	 * This example class was created to demonstrate how to encapsulate Auth configuration and
	 * Auth callbacks (event handlers) inside a child class. Using this approach, you could use
	 * this child class in all application, without the need of duplicating code.
	 * In this example, we extend php2go.auth.AuthDb because we want to authenticate the user
	 * against a database table (e.g. users)
	 */
	class MyAuthenticator extends AuthDb
	{
		/**
		 * Holds the Document instance
		 *
		 * @var MyHTMLPage
		 */
		var $doc = NULL;

		function MyAuthenticator() {
			/**
			 * call parent class ctor
			 */
			parent::AuthDb();
			/**
			 * define callbacks (event handlers)
			 */
			parent::setLoginCallback(array($this, 'onLogin'));
			parent::setLogoutCallback(array($this, 'onLogout'));
			parent::setErrorCallback(array($this, 'onError'));
			parent::setExpiryCallback(array($this, 'onExpire'));
			parent::setIdlenessCallback(array($this, 'onIdle'));
			parent::setValidSessionCallback(array($this, 'onValidSession'));
			/**
			 * set the table columns that must be included in the login query.
			 * all returned columns will be automatically persisted in the
			 * session scope and can be retrieved through the global User
			 * instance: $user =& User::getInstance();
			 */
			parent::setDbFields('*');
			/**
			 * set the name of the table to be used by the login query
			 */
			parent::setTableName('users');
			/**
			 * define an extra condition clause (the initial "AND" must not be included)
			 */
			parent::setExtraClause('active = ?', array(1));
			/**
			 * set the name of the request parameteres that will contain username and
			 * password (login credentials)
			 */
			parent::setLoginFieldName('username');
			parent::setPasswordFieldName('password');
			/**
			 * define crypt function that must be used to verify the user password.
			 * if omitted, no crypt will be used (plain text comparison).
			 * the second parameter indicates we want to call a db function instead of a php function.
			 */
			parent::setCryptFunction('md5', true);
		}

		function authenticate() {
			/**
			 * Here, we're extending parent class authenticate method
			 * in order to match username and password against static values.
			 * Remove this method from your authenticator class in order
			 * to authenticate user against the database.
			 *
			 * IMPORTANT: If authenticate returns an array of values, these
			 * values will be also persisted in the session scope. Returning
			 * FALSE means that authentication has failed
			 */
			if ($this->_login == 'admin' && $this->_password == 'admin') {
				return array(
					'name' => 'Administrator'
				);
			} else {
				return FALSE;
			}
			//return parent::authenticate();
		}

		/**
		 * VALID SESSION CALLBACK
		 * This callback is executed when the authenticator detects that there's a valid user stored in the session scope.
		 * When this event is triggered, we're sure that the user is not idled or expired.
		 * IMPORTANT: the User instance MUST be taken by reference so that the changes can have effect
		 */
		function onValidSession(&$currentUser) {
			/**
			 * Initialize last_visited_uri and hit_count variables in the
			 * User object stored in the session scope
			 */
			$currentUser->setPropertyValue('last_visited_uri', HttpRequest::uri());
			$currentUser->setPropertyValue('hit_count', $currentUser->getPropertyValue('hit_count', FALSE)+1);
		}

		/**
		 * LOGIN CALLBACK
		 * The login callback is called when the authentication method succeeds. Receives as parameter a reference to
		 * the User object that has just logged in. The most common behaviour of this callback is redirect to a secure page.
		 * However, other behaviours are accepted, such as printing a message or drawing the page without performing any
		 * redirection
		 * IMPORTANT: the User object MUST be taken by reference so that the changes can have effect
		 */
		function onLogin(&$newUser) {
			/**
			 * Initialize last_visited_uri and hit_count variables in the
			 * User object stored in the session scope
			 */
			$newUser->setPropertyValue('last_visited_uri', HttpRequest::uri());
			$newUser->setPropertyValue('hit_count', $newUser->getPropertyValue('hit_count', FALSE)+1);
			$this->doc->successMsg = "User " . $newUser->getUsername() . " was logged in successfully";
		}

		/**
		 * ERROR CALLBACK
		 * This callback is called when the authentication method fails. This callback receives as parameter an instance of the
		 * class User containing the failed username. Normally, this callback should send the user back to the login page or
		 * redraw the login form displaying some error message
		 */
		function onError($errorUser) {
			$this->doc->loginErrorMsg = 'Username ' . $errorUser->getUsername() . ' or password don\'t match!';
		}

		/**
		 * LOGOUT CALLBACK
		 * Here we define the callback function that will be called when the method logout() in Auth class is called.
		 * This callback receives as parameter the User object representing the user that logged out.
		 * Normally, this kind of callback function must send the user back to the login page or to the home page
		 */
		function onLogout($lastUser) {
			/**
			 * destroy all session contents and redirect
			 * back to the login page
			 */
			$Session =& SessionManager::getInstance();
			$Session->destroy();
			HttpResponse::redirect(new Url(HttpRequest::basePath()));
		}

		/**
		 * EXPIRENESS CALLBACK
		 * When the session is expired, the Auth class automatically destroys the user session, and calls the expireness
		 * callback if it's defined. This callback receives as parameter an User object that represents the user that was logged in.
		 * Some possible behaviours of this callback are redirect to the login page or call the login function providing some
		 * error message to be displayed
		 */
		function onExpire($lastUser) {
			$this->doc->loginErrorMsg = "The session of the user ".$lastUser->getUsername()." has expired!";
		}

		/**
		 * IDLENESS CALLBACK
		 * If the time between one request and another is greater than the defined idle time, the user session will be destroyed
		 * and the idleness callback will be called, if it's defined. This callback receives as parameter an User object that represents
		 * the user that was logged in. The suggested behaviours for this callback are the same suggested to the expireness callback
		 */
		function onIdle($lastUser) {
			$this->doc->loginErrorMsg = "The session of the user ".$lastUser->getUsername()." has been idle for a long time!";
		}
	}

	/**
	 * Interface example class used to demonstrate how to integrade
	 * an specialized authenticator with an instance of the Document
	 * class
	 */
	class MyHTMLPage extends Document
	{
		/**
		 * Reference to the authenticator object
		 *
		 * @var MyAuthenticator
		 */
		var $auth = NULL;
		/**
		 * Login error message
		 *
		 * @var string
		 */
		var $loginErrorMsg = '';
		/**
		 * Success message
		 *
		 * @var string
		 */
		var $successMsg = '';

		/**
		 * Class construtor
		 *
		 * @param string $layout Layout template
		 * @return MyHTMLPage
		 */
		function MyHTMLPage($layout) {
			/**
			 * call parent class ctor
			 */
			parent::Document($layout);
			parent::setTitle('PHP2Go Examples - php2go.auth.AuthDb');
			/**
			 * instantiate and initialize authenticator,
			 * so that HTML pages built with this class
			 * will require a valid user to be displayed
			 */
			$this->auth = new MyAuthenticator();
			$this->auth->doc =& $this;
			$this->auth->init();
			/**
			 * handle logoff requests
			 */
			if (isset($_REQUEST['logout']))
				$this->auth->logout();
			/**
			 * add a stylesheet file
			 */
			parent::addStyle('../common/examples.css');
		}
	}

	$page = new MyHTMLPage('../common/basic.tpl');
	$main =& $page->createElement('main', 'index.tpl');
	$main->assign('loginErrorMsg', $page->loginErrorMsg);
	$main->assign('successMsg', $page->successMsg);
	$page->display();

?>