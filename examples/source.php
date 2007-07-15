<?php

	require_once('config/config.php');

	$file = $_GET['file'];
	if (empty($file)) {
		internalError();
	} else {
		$file = preg_replace('/^(\.{1,2}(\/|\\\))+/', '', $file);
		if (!file_exists($file))
			internalError();
		highlight_file($file);
	}

	function internalError() {
		header('HTTP/1.0 500', TRUE, 500);
	}

?>