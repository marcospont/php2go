<?php

	// $Header: /www/cvsroot/php2go/examples/mailmessage.example.php,v 1.10 2006/04/05 23:43:20 mpont Exp $
	// $Revision: 1.10 $
	// $Date: 2006/04/05 23:43:20 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.net.MailMessage');

	echo '<b>PHP2Go Example</b> : php2go.net.MailMessage<br><br>';

	/**
	 * Class instance
	 */
	$mail = new MailMessage();

	/**
	 * define subject and recipients
	 */
	$mail->setSubject("Foo Bar Baz");
	$mail->setFrom("zxcvb@foo.net", "Sender");
	$mail->addTo("qwerty@foo.com", "Recipient 1");
	$mail->addCC("asdf@baz.org", "Recipient 2");

	/**
	 * define message HTML body -> this will set the type of the message to "multipart"
	 */
	$mail->setHtmlBody("
	<html>
		<body>
			<table><tr><td>
				<img src=\"cid:image\" alt=\"\" border=\"0\"/>
				This is HTML Mail!
			</td></tr></table>
		<body>
	</html>
	");

	/**
	 * add an embedded image, that must be included in the code using something like <img src="cid:image"/>
	 * the 4th parameter of this method is the file mime type. if omitted, it will be retrieved using the file extension
	 */
	$mail->addEmbeddedFile('resources/p2g_logo1.jpg', 'image', 'base64');

	/**
	 * build the message headers and body
	 */
	$mail->build();

	/*
	 * fetch an instance of the mail transporter
	 */
	$transport =& $mail->getTransport();

	/**
	 * configure transport using an SMTP server
	 * >>> attention : allowed options : server, port, user, password, debug
	 */
	$transport->setType(MAIL_TRANSPORT_SMTP, array(
		'server' => 'localhost',
		'port' => 25,
		//'username' => 'user',
		//'password' => 'password',
		'debug' => TRUE
	));

	/**
	 * configure transport using PHP mail() function
	 */
	//$transport->setType(MAIL_TRANSPORT_MAIL);

	/**
	 * configure transport using UNIX sendmail
	 * >>> attention : allowed options : sendmail (sendmail executable path)
	 */
	//$transport->setType(MAIL_TRANSPORT_SENDMAIL, array('sendmail' => '/usr/sbin/sendmail'));

	/**
	 * send the message
	 * using the @ operator, you'll be able to hide the error reporting even if any
	 * kind of delivery error is found
	 */
	if (@$transport->send()) {
		print "sent ok";
	} else {
		print "delivery error:<br>";
		print $transport->getErrorMessage();
	}

?>