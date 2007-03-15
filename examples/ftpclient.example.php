<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

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