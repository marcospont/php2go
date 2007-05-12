<?php

/**
 * Perform the necessary imports
 */
import('php2go.auth.AuthDb');
import('php2go.base.Document');
import('php2go.form.FormBasic');
import('php2go.net.HttpRequest');
import('php2go.net.HttpResponse');
import('php2go.net.Url');
	
class MyAuth extends AuthDb {
	
	/**
	 * This property will be used to build the login form view
	 */
	var $doc = NULL;
	
	function MyAuth() {
		
		/**
		 * Call the parent ctor
		 */		
		parent::AuthDb();		
		
		/**
		 * Define the table where the user data is stored
		 */
		parent::setTableName('users');
		
		/**
		 * Define the name of the request parameters sent by the login form,
		 * containing username and password for authentication purposes
		 */
		parent::setLoginFieldName('login');
		parent::setPasswordFieldName('password');
		
		/**
		 * Define that all the table fields must be copied to the session object
		 */
		parent::setDbFields('*');
		
		/**
		 * Define the crypt function that is used to compare the passwords
		 * By default, the framework compares plain text strings
		 */
		parent::setCryptFunction('md5');
		
		/**
		 * Define the method that will handle the invalid session state
		 */
		parent::setLoginFunction(array($this, 'onInvalidSession'));
		
		/**
		 * Define the methods that will handle success, logout and error in the authentication process
		 */
		parent::setLoginCallback(array($this, 'onLogin'));
		parent::setLogoutCallback(array($this, 'onLogout'));
		parent::setErrorCallback(array($this, 'onError'));
		
		/**
		 * Define the methods that will handle an expired or idled session
		 */
		parent::setExpiryCallback(array($this, 'onExpire'));
		parent::setIdlenessCallback(array($this, 'onIdle'));
		
		/**
		 * Initialize the authentication process
		 */
		parent::init();		
	}

	function onInvalidSession($error=NULL) {
		/**
		 * The MyAuth class is used inside MyDoc class. So, when we're loading any PHP script that needs authentication
		 * and the session is invalid, we must redirect to our login page. However, if the current script (basePath) is
		 * the login page, we can just generate and print the login form
		 */
		if (strpos(HttpRequest::basePath(), 'login.php') === FALSE) {
			$loginUrl = new Url('login.php');
			if (!empty($error) && TypeUtils::isInteger($error))
				$loginUrl->addParameter('error', $error);
			HttpResponse::redirect($loginUrl);
		} else {
		
			/**
			 * Here we detect the error message to be displayed
			 * 1) when this event handler is called from the login page, the error comes as a parameter ($error)
			 * 2) when the session reaches expired state or idle state, the login page is loaded with a GET parameter containing the error code
			 */
			$error = TypeUtils::ifNull($error, TypeUtils::ifNull(HttpRequest::get('error'), 0));
			switch ($error) {
				case 1 : $errorMessage = "Username or password invalid"; break;
				case 2 : $errorMessage = "Your session <b>expired</b>.<br>You must authenticate again."; break;
				case 3 : $errorMessage = "Session was <b>idle</b> for a long time.<br>You must authenticate again."; break;
			}
			 
			/**
			 * This method will generate a view to the user containing the login form
			 * So, we must create an instance of Document class, to generate an HTML document
			 */
			$doc =& new Document(TEMPLATE_PATH . 'simple_layout.tpl');
			$doc->addStyle(CSS_PATH . 'loginapp.css');
			$doc->setCache(FALSE);
			$doc->setCompression();
			
			/**
			 * Create a form containing username and password fields
			 */
			$form =& new FormBasic(XML_PATH . 'login.xml', 'loginform', $doc);
			$form->setFormAlign('center');
			$form->setFormWidth(250);
			$form->setInputStyle('input');
			$form->setLabelStyle('label');
			$form->setButtonStyle('button');
			$form->setErrorStyle('error', FORM_ERROR_FLOW, '');
			$form->setErrorDisplayOptions(FORM_CLIENT_ERROR_DHTML);
			
			/**
			 * Here we assign the login error, if it's passed to this method
			 */
			if (isset($errorMessage))
				$form->addErrors($errorMessage);			
			
			/**
			 * Attach this form in the main slot of the page (defined in simple_layout.tpl)
			 */
			$doc->elements['main'] = $form->getContent();
			
			/**
			 * Request focus on the "login" field, in the "login" form
			 */
			$doc->setFocus('loginform', 'login');
			
			/**
			 * Display the view (HTML document)
			 */
			$doc->display();
		}
	}
	
	function onLogin() {
		HttpResponse::redirect(new Url('index.php'));
	}
	
	function onError() {
		$this->onInvalidSession(1);
	}
	
	function onLogout() {
		HttpResponse::redirect(new Url('login.php'));
	}
	
	function onExpire() {
		$this->onInvalidSession(2);
	}
	
	function onIdle() {
		$this->onInvalidSession(3);
	}
}
?>