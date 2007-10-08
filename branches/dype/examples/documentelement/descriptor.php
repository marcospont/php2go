<?php

	require_once('../config/config.php');
	import('php2go.util.json.JSONEncoder');

	header("Content-Type: application/json");
	print JSONEncoder::encode(array(
		'files' => array(
			'index.php',
			'menu.html',
			'include1.tpl',
			'include2.tpl'
		)
	));

?>