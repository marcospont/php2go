<?php

	// $Header: /www/cvsroot/php2go/examples/url.example.php,v 1.10 2006/06/09 04:38:46 mpont Exp $
	// $Revision: 1.10 $
	// $Date: 2006/06/09 04:38:46 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.net.Url');

	println('<b>PHP2Go Examples</b> : php2go.net.Url<br>');

	/**
	 * URL to be tested
	 */
	$testUrl = 'http://user:pass@www.domain.com:8080/path/internal/script.php?parameter=2#anchor';
	println('Original URL : ' . $testUrl);
	println('<hr>');

	$url = new Url($testUrl);
	//$url = new Url());
	//$url->setFromCurrent());
	/**
	 * URL host
	 */
	println('Host: ' . $url->getHost());
	/**
	 * URL scheme (protocol)
	 */
	println('Scheme: ' . $url->getScheme());
	/**
	 * URL connection port
	 */
	println('Port: ' . $url->getPort());
	/**
	 * authentication data (username and password)
	 */
	println('Auth vars: ' . $url->getAuth());
	/**
	 * path after the domain name
	 */
	println('Path: ' . $url->getPath());
	/**
	 * file name
	 */
	println('File: ' . $url->getFile());
	/**
	 * query string
	 */
	println('Parameters: ' . $url->getQueryString());
	/**
	 * query string as array
	 */
	println('Parameters returned as array: ' . exportVariable($url->getQueryStringArray()));
	/**
	 * fragment or anchor
	 */
	println('Fragment or anchor: ' . $url->getFragment());
	/**
	 * build an anchor pointing to the URL
	 */
	println('Geração de âncora: ' . $url->getAnchor('Click me!'));
	println('<hr>');
	/**
	 * get the normalized URL value
	 */
	println('Final URL: ' . $url->getUrl());
	println('<hr>');
	/**
	 * encode the URL using base64
	 */
	$encode = $url->encode(NULL, 'q');
	println('Encoded URL: ' . $encode);
	/**
	 * catches the parameters from an encoded URL value
	 */
	$encoded = new Url($encode);
	$decode = $encoded->decode();
	println('Decoded URL: ' . $decode);
	println('Decoded parameters returned as array: ' . exportVariable($encoded->decode(NULL, TRUE)));
	println('<hr>');
	/**
	 * adding and removing new parameters to the URL
	 */
	$url->addParameter('param', 'value');
	$url->addParameter('action', 'edit');
	$url->addParameter('goto', 2);
	println('URL with new parameters: ' . $url->getUrl());
	$url->removeParameter('action');
	println('URL parameters after removing one of them: ' . $url->getUrl());

?>