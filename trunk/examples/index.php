<?php

	require_once('config/config.php');
	import('php2go.base.Document');
	import('php2go.file.DirectoryManager');

	$doc = new Document('common/basic.tpl');
	$doc->addScript(PHP2GO_JAVASCRIPT_PATH . 'ajax.js');
	$doc->addScript(PHP2GO_JAVASCRIPT_PATH . 'form.js');
	$doc->addStyle('index.css');
	$doc->setTitle('PHP2Go - Examples Browser');
	$doc->setCache();

	$main = new Template('index.tpl');
	$main->parse();

	$folders = array();
	$dir = new DirectoryManager();
	$dir->open(getcwd());
	while ($entry = $dir->read()) {
		if ($entry->isDirectory() && !preg_match('/^(config|common|locale|sql|tmp)$/', $entry->getName()))
			$folders[] = $entry->getName();
	}

	$main->assign('examples', $folders);

	$doc->assignByRef('main', $main);
	$doc->display();

?>