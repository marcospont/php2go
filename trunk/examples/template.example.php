<?php

	// $Header: /www/cvsroot/php2go/examples/template.example.php,v 1.9 2006/06/18 18:45:00 mpont Exp $
	// $Revision: 1.9 $
	// $Date: 2006/06/18 18:45:00 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.datetime.TimeCounter');
	import('php2go.template.Template');

	/**
	 * document creation, using a simple layout with one single slot : main
	 */
	$doc = new Document('resources/layout.example.tpl');
	$doc->setTitle('PHP2Go Examples - php2go.template.Template');

	/**
	 * new instance of a document element (used to render the main page slot)
	 */
	$el = new DocumentElement();
	$el->put("first execution<hr>\n");

	/**
	 * create a template and set the cache parameters
	 * - check if the cache dir (first parameter) is writeable, or an exception will be thrown
	 * - the default lifetime is 1 hour (default lifetime in php2go.cache.CacheManager)
	 */
	$tpl = new Template('resources/template.example.tpl', T_BYFILE);
	$tpl->setCacheProperties(PHP2GO_CACHE_PATH, 1);
	$tpl->parse();

	/**
	 * create a block and assign values to test variable modifiers
	 */
	$tpl->createBlock('modifiers_block');
	$tpl->assign('simple_string', 'php2go web development framework');
	$tpl->assign('big_string', 'this is a string that must be truncated when it reaches 40 chars');
	$tpl->assign('raw_string', '  string to be trimmed and normalized: בגדיךף');
	$tpl->assign('raw_string_multiline', "multiline string.\nnew lines converted and <htmlentites> escaped.\n			blank spaces are minimized.");
	$tpl->assign('array', array(1, 10, 100, 1000));
	$tpl->assign('timestamp', time());
	$tpl->assign('sql_date', date('Y-m-d'));
	$tpl->assign('text_links', 'Visit the PHP2Go website: http://www.php2go.com.br');
	$tpl->assign('mail', 'mpont@users.sourceforge.net');
	$tpl->assign('number', 1956.456);
	$tpl->assign('file', __FILE__);
	$tpl->assign('img', 'http://www.php2go.com.br/resources/images/p2g_transp_logo_header.gif');

	/**
	 * table modifier: create an HTML table using a simple assign operation
	 */
	$db =& Db::getInstance();
	$tables = $db->getTables();
	if (in_array('client', $tables)) {
		$db->setFetchMode(ADODB_FETCH_ASSOC);
		$tpl->assign('data', $db->getAll('
			select
				NAME as "Name", ADDRESS as "Address", CATEGORY as "Category", ACTIVE as "Active"
			from
				client
		'));
	}

	/**
	 * change the current block so that the next assign operation point to the right place
	 */
	$tpl->setCurrentBlock(TP_ROOTBLOCK);

	/**
	 * assign a simple variable and create a set of blocks using a simple iteration command
	 */
	$tpl->assign('simple_var', '<b>PHP2Go Examples : php2go.template.Template</b>');
	for ($i=0; $i<10; $i++) {
		$tpl->createBlock('example_block');
		$tpl->assign('foo', $i);
		$tpl->assign('bar', chr($i+65));
		$tpl->createAndAssign('inner_block', 'baz', chr($i+65+10));
	}

	/**
	 * add the template content to the buffer of the document element
	 */
	$el->put($tpl->getContent(), T_BYVAR);
	$el->put('<br>second execution (all information about variables is clear)<hr>');

	/**
	 * this method reset all variables and blocks stored in the template object;
	 * the object is changed to the state immediately after the parse operation
	 */
	$tpl->resetTemplate();

	/**
	 * assign variables and create blocks again
	 */
	$tpl->assign('simple_var', '<b>PHP2Go Examples : another variable');
	for ($i=0; $i<5; $i++) {
		$tpl->createAndAssign('example_block', array('foo'=>$i, 'bar'=>chr($i+75)));
	}

	/**
	 * add to the buffer
	 */
	$el->put($tpl->getContent(), T_BYVAR);

	/**
	 * parse the element (page slot) and attach it to the document
	 */
	$el->parse();
	$doc->elements['main'] =& $el;

	$doc->display();

?>