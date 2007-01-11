<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

	require_once('config.example.php');
	import('php2go.net.HttpCookie');
	import('php2go.net.HttpRequest');
	import('php2go.net.HttpResponse');

	$Cookie = new HttpCookie();
	if (HttpRequest::cookie('test') == NULL) {
		if (HttpRequest::get('op') == 'set') {
			$Cookie->set('test', 'mycookie', HttpRequest::serverName(), '/', 20);
			HttpResponse::addCookie($Cookie);
			$op = 'The cookie was added. Reload the page to verify.<br><br>';
		}
	} else {
		if (HttpRequest::get('op') == 'unset') {
			$Cookie->set('test', 'mycookie', HttpRequest::serverName(), '/', -20);
			HttpResponse::addCookie($Cookie);
			$op = 'The cookie was removed. Reload the page to verify.<br><br>';
		}
	}

	println('<b>PHP2Go Examples</b> : php2go.net.HttpResponse<br>');
	if (isset($op))
		print($op);

	println('Request Cookies Dump:');
	dumpVariable($_COOKIE);

	println('<a href=\'' . HttpRequest::basePath() . '?op=set\'>Add Cookie</a>');
	println('<a href=\'' . HttpRequest::basePath() . '?op=unset\'>Remove Cookie</a>');
	println('<a href=\'' . HttpRequest::basePath() . '\'>Reload Page</a>');

?>