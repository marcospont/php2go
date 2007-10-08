<?php

	require_once('../config/config.php');
	import('php2go.util.json.JSONEncoder');

	header("Content-Type: application/json");
	print JSONEncoder::encode(array(
		'files' => array(
			'index.php',
			'list.xml',
			'list.tpl',
			'form.xml',
			'form.tpl'
		)
	));

?>