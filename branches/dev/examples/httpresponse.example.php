<?php

	// $Header: /www/cvsroot/php2go/examples/httpresponse.example.php,v 1.4 2006/10/15 20:38:55 mpont Exp $
	// $Revision: 1.4 $
	// $Date: 2006/10/15 20:38:55 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

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