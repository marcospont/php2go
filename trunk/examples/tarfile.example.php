<?php

	// $Header: /www/cvsroot/php2go/examples/tarfile.example.php,v 1.4 2006/04/05 23:43:20 mpont Exp $
	// $Revision: 1.4 $
	// $Date: 2006/04/05 23:43:20 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.file.TarFile');
	
	echo
		'<b>PHP2Go Examples</b> : php2go.file.TarFile<br><br>';
		
	/**
	 * create the instance using the factory method getInstance
	 */
	$tar =& FileCompress::getInstance('tar');
	$tar->debug = TRUE;
	
	/**
	 * add some files in the tarball
	 */
	$tar->addFile('forms.example.xml');
	$tar->addFile('reports.example.xml');
	
	/**
	 * save the file with and without gzip compression
	 */
	$tar->saveFile('tmp/test.tar', 0777);
	$tar->saveGzip('tmp/test.tar.gz', 0777);
		
	/**
	 * extract the archived and compressed data
	 */
	$tar->saveExtractedFiles($tar->extractGzip('tmp/test.tar.gz'), 0777, 'tmp');

?>