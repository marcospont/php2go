<?php

	// $Header: /www/cvsroot/php2go/examples/pop3.example.php,v 1.11 2006/06/09 04:38:46 mpont Exp $
	// $Revision: 1.11 $
	// $Date: 2006/06/09 04:38:46 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.net.Pop3');

	println('<b>PHP2Go Examples</b> : php2go.net.Pop3<br>');

	// create an instance of the Pop3 class
	$pop = new Pop3();
	// enable debug to see the client/server messages
	$pop->debug = TRUE;
	// connect to a POP3 host (default port = 110)
	$pop->connect('your.pop3.host');
	// send authentication data
	$pop->login('username', 'password');

	// get the message count
	$count = $pop->getMsgCount();
	println('<b>Total number os messages in the POP3 server:</b> ' . $count);

	// get the headers of each message
	for ($i=1; $i<=$count; $i++) {
		$h = $pop->getMessageHeaders($i, TRUE);
		println('Date: ' . $h['Date'] . '<br>Subject: ' . $h['Subject'] . '<br>From: ' . htmlspecialchars($h['From']));
	}

	//close the connection
	$pop->close();

?>