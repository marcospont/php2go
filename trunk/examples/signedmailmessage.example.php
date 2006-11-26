<?php

	// $Header: /www/cvsroot/php2go/examples/signedmailmessage.example.php,v 1.6 2004/08/17 16:06:30 mpont Exp $
	// $Revision: 1.6 $
	// $Date: 2004/08/17 16:06:30 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.net.SignedMailMessage');
	
	$signedMessage = new SignedMailMessage();
	$signedMessage->setKeyPath('/www/pathtokeys'); // path to the .gnupg folder
	$signedMessage->setFrom('foo@bar.com', 'John Foo');
	$signedMessage->setSubject('Signed Mail Message');
	$signedMessage->addTo('baz@xpto.com', 'Paul Baz');
	$signedMessage->setTextBody("This is a signed message!");
	if ($signedMessage->build()) {
		$transport =& $signedMessage->getTransport();
		$transport->setType(MAIL_TRANSPORT_SMTP, array('server' => 'my.smtp.server'));
		$transport->send();
	}
	
?>