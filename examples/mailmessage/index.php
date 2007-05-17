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

	require_once('../config/config.php');
	import('php2go.net.MailMessage');

	echo '<b>PHP2Go Example</b> : php2go.net.MailMessage, php2go.net.MailTransport<br /><br />';

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
	$mail->addEmbeddedFile('logo.jpg', 'image', 'base64');

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
		print "delivery error:<br />";
		print $transport->getErrorMessage();
	}

?>