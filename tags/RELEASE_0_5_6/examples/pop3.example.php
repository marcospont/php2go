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
	import('php2go.net.Pop3');

	println('<b>PHP2Go Examples</b> : php2go.net.Pop3<br>');

	// create an instance of the Pop3 class
	$pop = new Pop3();
	// enable debug to see the client/server messages
	$pop->debug = TRUE;
	// connect to a POP3 host (default port = 110)
	$pop->connect('your.pop.host');
	// send authentication data
	$pop->authenticate('username', 'password');

	// get the message count
	$count = $pop->getMessagesCount();
	println('<b>Total number os messages in the POP3 server:</b> ' . $count);
	flush();

	// get the headers of each message
	for ($i=1; $i<=$count; $i++) {
		$h = $pop->getMessageHeaders($i, TRUE);
		println('<hr>Date: ' . $h['Date'] . '<br>Subject: ' . $h['Subject'] . '<br>From: ' . $h['From']);
		flush();
	}

	// close the connection
	$pop->quit();

?>