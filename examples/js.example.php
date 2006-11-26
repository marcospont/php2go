<?php

	// $Header: /www/cvsroot/php2go/examples/js.example.php,v 1.1 2006/10/11 22:54:06 mpont Exp $
	// $Revision: 1.1 $
	// $Date: 2006/10/11 22:54:06 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.base.Document');

	/**
	 * This example was created to demonstrate some of the capabilities
	 * of the PHP2Go Javascript Framework. In the PHP side, we just define
	 * an HTML document with a single slot ($main) and show a template inside it
	 */
	$doc = new Document('resources/basicLayout.example.tpl');
	$doc->addScript(PHP2GO_JAVASCRIPT_PATH . 'form.js');
	$main =& $doc->createElement('main', 'resources/js.example.tpl', T_BYFILE);
	$doc->display();

?>