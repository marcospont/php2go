<?php

	// $Header: /www/cvsroot/php2go/examples/helloworld.example.php,v 1.1 2006/06/15 01:34:14 mpont Exp $
	// $Revision: 1.1 $
	// $Date: 2006/06/15 01:34:14 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.base.Document');
	
	/**
	 * This is the "Hello World!" example. 
	 * 
	 * An instance of the php2go.base.Document is created, 
	 * and a string is assigned to the "main" slot.
	 * 
	 * Finally, the HTML document is printed out.
	 */
	
	$doc = new Document('resources/basicLayout.example.tpl');
	$doc->assign('main', 'Hello World!');
	$doc->display();

?>