<?php

	// $Header: /www/cvsroot/php2go/examples/statement.example.php,v 1.4 2006/06/09 04:38:46 mpont Exp $
	// $Revision: 1.4 $
	// $Date: 2006/06/09 04:38:46 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.net.HttpRequest');
	import('php2go.util.Statement');

	println('<b>PHP2Go Example</b> : php2go.util.Statement<br>');

	/**
	 * Create a new Statement class instance
	 */
	$statement = new Statement();

	/**
	 * Set the statement source
	 */
	$stCode = "You are running PHP ~version~, and the name of this script is ~script~";
	println('New Statement code : ' . $stCode);
	$statement->setStatement($stCode);

	/**
	 * Bind statement variables
	 */
	println('Bind variable version = PHP_VERSION');
	$statement->bindByName('version', PHP_VERSION, FALSE);
	println('Bind variable script = HttpRequest::basePath()');
	$statement->bindByName('script', HttpRequest::basePath());

	/**
	 * Print result
	 */
	println('Result : ' . $statement->getResult() . '<br>');

	/**
	 * Setup a new statement
	 */
	$stCode = "Running PHP on ~_SERVER['SERVER_NAME']~, ~_SERVER['SERVER_ADDR']~";
	println('New Statement code : ' . $stCode);
	$statement->setStatement($stCode);

	/**
	 * Bind variables using a magic method that searches
	 * for the placeholder replacements in external scopes
	 */
	println('Bind variables (method that tries to find all the variables in the DATA REPOSITORIES (request, session, registry)');
	$statement->bindVariables(FALSE, 'ROEGP');

	/**
	 * Prints out the statement result
	 */
	println('Result : ' . $statement->getResult());

?>