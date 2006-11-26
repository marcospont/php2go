<?php

	// $Header: /www/cvsroot/php2go/examples/callback.example.php,v 1.5 2006/06/09 04:38:44 mpont Exp $
	// $Revision: 1.5 $
	// $Date: 2006/06/09 04:38:44 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once("../p2gConfig.php");
	import('php2go.util.Callback');
	import('php2go.net.*');

	$url = new Url();

	println('<b>PHP2Go Examples</b> : php2go.util.Callback<br>');

	function myDummyFunction($arg1, $arg2) {
		return $arg1 + $arg2;
	}

	$callback = new Callback();

	// validate and invoke a callback using object and method
	println('<b>1) Callback using object instance and method : php2go.net.Url instance + encode method</b>');
	$callback->setFunction(array($url, 'encode'));
	$return = $callback->invoke(array(HttpRequest::basePath() . '?arg=test', 'q'), TRUE);
	println('Return: ' . $return . '<br>');

	// validate and invoke a callback using a static method call
	println('<b>2) Callback using a static method call : php2go.net.HttpRequest + basePath method</b>');
	$callback->setFunction('HttpRequest::basePath');
	$return = $callback->invoke();
	println('Return: ' . $return . '<br>');

	// validate and invoke a callback using a simple function
	println('<b>3) Callback using a simple function call</b>');
	$callback->setFunction('myDummyFunction');
	$return = $callback->invoke(array(1,1), TRUE);
	println('Return: ' . $return . '<br>');

	// force an error without disabling the internal error handling of the class
	println('<b>4) Force an error without disabling the internal error of the class</b>');
	$callback->setFunction('i am not a funcion');
	$return = $callback->invoke();
	println('Return: ' . $return . '<br>');

?>