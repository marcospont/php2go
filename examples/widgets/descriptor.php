<?php

	require_once('../config/config.php');
	import('php2go.util.json.JSONEncoder');

	header("Content-Type: application/json");
	print JSONEncoder::encode(array(
		'files' => array(
			'index.php',
			'header.tpl',
			'collapsiblepanel.tpl',
			'container.tpl',
			'datatable.tpl',
			'googlemap.tpl',
			'i25barcode.tpl',
			'slideshow.tpl',
			'tabview.tpl',
			'templatecontainer.tpl',
			'toolbar.tpl'
		)
	));

?>