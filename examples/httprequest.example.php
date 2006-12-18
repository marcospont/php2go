<?php

	// $Header: /www/cvsroot/php2go/examples/httprequest.example.php,v 1.7 2006/04/05 23:43:20 mpont Exp $
	// $Revision: 1.7 $
	// $Date: 2006/04/05 23:43:20 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.net.HttpRequest');
	import('php2go.session.SessionObject');

	$sess =& new SessionManager();

	println('<b>PHP2Go Examples</b> : php2go.net.HttpRequest');
	println('<b>Also using</b> : php2go.net.UserAgent<br>');

	/**
	 * 1) current script (PHP_SELF)
	 * 2) URI of the last request (REQUEST_URI) - you can test this variable providing different GET parameters to this page
	 */
	println('<b>Current Script:</b> ' . HttpRequest::basePath());
	println('<b>Current URI:</b> ' . HttpRequest::uri());
	println('<b>Referer:</b> ' . HttpRequest::referer());
	println('<b>Method:</b> ' . HttpRequest::method());
	println('<b>User agent:</b> ' . HttpRequest::userAgent());
	println('<b>Request headers:</b> ' . exportVariable(HttpRequest::getHeaders(), TRUE));
	println('<b>Server name and script name:</b> ' . HttpRequest::scriptName() . ', running at ' . HttpRequest::serverName());
	println('<b>User IP and host:</b> ' . HttpRequest::remoteAddress() . ' - ' . HttpRequest::remoteHost());

	/**
	 * 3) session variables fetch using HttpRequest::session
	 * in the above lines, a new session variable is created (execution_count)
	 * after that, you can see how the value can be retrieved using HttpRequestt
	 */
	if ($sess->isRegistered('execution_count')) {
		$sess->setValue('execution_count', $sess->getValue('execution_count') + 1);
	} else {
		$sess->register('execution_count', 1);
	}
	println('<b>Execution count:</b> ' . HttpRequest::session('execution_count') . ' <a href=\'javascript:location.reload()\'>Reload</a>');

	/**
	 * 4) generic variable search, using the getVar method
	 */
	println('<b>User IP (using getVar):</b> ' . HttpRequest::getVar('REMOTE_ADDR', 'SERVER'));
	println('<b>Document Root (using getVar):</b> ' . HttpRequest::getVar('DOCUMENT_ROOT', 'all', 'EGP'));

	/**
	 * 5) user agent info, using php2go.net.UserAgent
	 */
	println('<b>Get user agent information</b>');
	$agent =& UserAgent::getInstance();
	println(nl2br($agent->__toString()));
	println('<b>Is IE?</b> ' . TypeUtils::parseInteger($agent->matchBrowser('ie')));
	println('<b>Match against a browser list:</b> ' . $agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')));
	println('<b>Accepts gzip encoding?</b> ' . TypeUtils::parseInteger($agent->matchAcceptList('gzip', 'encoding')));
	println('<b>What is the JavaScript version?</b> ' . $agent->getFeature('javascript'));
	println('<b>Print the browser full name</b> ' . $agent->getBrowserFullName());
	println('<b>Print the OS full name</b> ' . $agent->getOSFullName());

?>