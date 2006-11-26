<?php

	// $Header: /www/cvsroot/php2go/examples/ftpclient.example.php,v 1.5 2006/06/09 04:38:45 mpont Exp $
	// $Revision: 1.5 $
	// $Date: 2006/06/09 04:38:45 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.net.FtpClient');

	println('<b>PHP2Go Examples</b> : php2go.net.FtpClient<br>');

	// create an instance of the FtpClient class
	$ftp = new FtpClient();

	// set timeout of the FTP connection
	$ftp->setTimeout(10);

	// enable passive mode
	$ftp->togglePassiveMode(TRUE);

	// set host name
	$ftp->setServer('ftp.debian.org', FTP_DEFAULT_PORT);

	// connect
	if ($ftp->connect())
	{
		// set user info (will not be used in this example)
		//$ftp->setUserInfo('username', 'password');

		// login operation (first parameter as "TRUE" means anonymous login)
		if ($ftp->login(TRUE))
		{
			// print current dir
			println('Current Directory: ' . $ftp->getCurrentDir());
			// print server system information
			println('Server system type: ' . $ftp->getSysType());

			// change directory
			$ftp->changeDir('debian');

			// retrieve the file list containing only the file names
			println('<hr>');
			println('<b>Retrieve the file names of a remote directory</b>');
			$list = $ftp->fileList();
			foreach ($list as $entry)
				print $entry . '<br>';

			// retrieve the details of the files inside a remote directory
			println('<hr>');
			println('<b>Retrieve the details of each file included in a remote directory</b>');
			$list = $ftp->rawList('', TRUE);
			foreach ($list as $entry) {
				println("Name: {$entry['name']} - Size: {$entry['size']} bytes - Permissions: {$entry['attr']}");
			}
		} else {
			println('FtpClient: Authentication failed');
		}
		// quit the connection
		$ftp->quit();
	} else {
		println('FtpClient: Connection problem');
	}

?>