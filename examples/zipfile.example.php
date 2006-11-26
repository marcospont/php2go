<?php

	// $Header: /www/cvsroot/php2go/examples/zipfile.example.php,v 1.5 2006/06/09 04:38:46 mpont Exp $
	// $Revision: 1.5 $
	// $Date: 2006/06/09 04:38:46 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.file.ZipFile');

	println('<b>PHP2Go Examples</b> : php2go.file.ZipFile<br>');

	/**
	 * creates the instance using the factory method getInstance
	 */
	$zip =& FileCompress::getInstance('zip');
	$zip->debug = TRUE;

	/**
	 * add a file in the ZIP archive using the filename
	 */
	$zip->addFile('forms.example.xml');
	/**
	 * add the contents of a file (string parameter)
	 */
	$zip->addData(FileSystem::getContents('reports.example.xml'), 'reports.example.xml', array('time' => filemtime('reports.example.xml')));

	/**
	 * save the file
	 */
	$zip->saveFile('tmp/test.zip', 0777);

	/**
	 * extract the archived & compressed data
	 */
	$zip->saveExtractedFiles($zip->extractFile('tmp/test.zip'), 0777, 'tmp');

?>