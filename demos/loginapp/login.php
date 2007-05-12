<?php

	require_once('config/config.php');	
	import('php2go.auth.Auth');
	
	/**
	 * In this script, we handle the login/logout process, using the class MyAuth,
	 * an extension of AuthDb that includes methods to perform the authentication operations
	 * like building the login view and creating and destroying the user session
	 */
	$auth =& Auth::getInstance();
	if ($auth->isValid()) {
		if (HttpRequest::get('logoff') != NULL)
			$auth->logout();
		else
			HttpResponse::redirect(new Url('index.php'));
	}

?>