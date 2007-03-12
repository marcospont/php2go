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
	import('php2go.net.HttpRequest');
	import('php2go.session.SessionObject');
	session_save_path(PHP2GO_ROOT . 'examples/resources/');

	$sess = new SessionObject('MY_SESSION');
	echo '<b>PHP2Go Example</b> : php2go.session.SessionObject<br><br>';
	if (!$sess->isRegistered()) {
		echo 'START...<br><a href=\'' . HttpRequest::basePath() . '\'>reload page</a>';
		$sess->createProperty('PAGE_VIEWS', 1);
		$sess->createTimeCounter('PAGE_TIME');
		$timeCounter = $sess->getTimeCounter('PAGE_TIME');
		$sess->createProperty('URLS', array(array(HttpRequest::basePath(), NULL)));
		$sess->register();
	} else {
		$sess->setPropertyValue('PAGE_VIEWS', $sess->getPropertyValue('PAGE_VIEWS')+1);
		echo 'Page Views : ' . $sess->getPropertyValue('PAGE_VIEWS') . '<br><a href=\'' . HttpRequest::basePath() . '\'>reload page</a><br><br>';
		$timeCounter =& $sess->getTimeCounter('PAGE_TIME');
		$u = $sess->getPropertyValue('URLS');
		$timeCounter->stop();
		$u[sizeof($u)-1][1] = $timeCounter->getMinutes();
		echo 'Visited URLs :<pre>';
		var_dump($u);
		echo '</pre>';
		$timeCounter->restart();
		$u[] = array(HttpRequest::basePath(), NULL);
		$sess->setPropertyValue('URLS', $u);
		$sess->update();
	}

?>