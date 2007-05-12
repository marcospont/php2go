<?php

	require_once('config/config.php');
	import('loginapp.MyDoc');	
	
	$db =& Db::getInstance();
	$tables = $db->getTables();
	if (!in_array('users', $tables)) {
		PHP2Go::raiseError('The table users was not found in your database. Please run create.sql to activate this demo application.', E_USER_ERROR);
	}
	
	/**
	 * In this script, we build a simple page that requires authentication
	 * and shows some user variables.
	 * In the lines above, the element "main" represents the main section of the page. That is the slot
	 * where the main content must be generated
	 */	 
	$doc =& new MyDoc();
	$doc->elements['main'] = "Main Slot";	
	$doc->display();

?>