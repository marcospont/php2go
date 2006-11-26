<?php

	// $Header: /www/cvsroot/php2go/examples/httpclient.example.php,v 1.8 2006/06/09 04:38:45 mpont Exp $
	// $Revision: 1.8 $
	// $Date: 2006/06/09 04:38:45 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.file.FileSystem');
	import('php2go.net.HttpClient');

	/**
	 * routine used to generate the response for the POST and multipart POST requests
	 */
	if (isset($_GET['process'])) {
		println('POST VARS');
		dumpVariable($_POST);
		println('FILES');
		dumpVariable($_FILES);
		println('GET VARS');
		dumpVariable($_GET);
		exit;
	}

	$testCase = 1;

	/**
	 * create an instance of HttpClient class;
	 * turn on followRedirects and debug flags
	 * define a custom user agent
	 */
	$http = new HttpClient();
	$http->setFollowRedirects(TRUE);
	$http->setUserAgent('MyUserAgent (compatible; MyBrowser; Linux)');
	$http->debug = TRUE;

	if ($testCase == 1) {

		/**
		 * define the host and perform a simple GET request;
		 * test for the HTTP 502 code (HTTP_STATUS_OK) to print the response body
		 */
		$http->setHost('www.php.net');
		if ($http->doGet('/') == HTTP_STATUS_OK) {
			echo $http->responseBody;
		}

	} elseif ($testCase == 2) {

		/**
		 * set the target host and perform a multipart POST request,
		 * sending a file as parameter (upload file)
		 */
		$http->setHost('www.php2go.com.br');
		$cont = FileSystem::getContents('resources/httpclient.example.txt');
		if ($http->doMultipartPost(
			'/php2go/examples/httpclient.example.php?process=1',
			array(
				'name'=>'John Doe',
				'e_mail'=>'john@foo.org',
				'phone'=>'6666666',
				'message'=>'The quick brown fox jumps over the lazy dog',
				'contact'=>'bar@baz.foo.org'
			),
			array(
				array('name'=>'arquivo','file'=>'resources/httpclient.example.txt','data'=>$cont)
			)) == HTTP_STATUS_OK)
		{
			echo '<pre>' . $http->getResponseBody() . '</pre>';
		}

	} elseif ($testCase == 3) {

		/**
		 * set the target host and perform a POST request,
		 * specifying the hash array containing the POST data
		 */
		$http->setHost('www.php2go.com.br');
		$formData = array(
			'name'=>'John Doe',
			'e_mail'=>'john@foo.org',
			'phone'=>'6666666',
			'message'=>'The quick brown fox jumps over the lazy dog',
			'contact'=>'bar@baz.foo.org'
		);
		if ($http->doPost(
			'/php2go/examples/httpclient.example.php?process=1',
			$formData
		) == HTTP_STATUS_OK) {
			echo '<pre>' . $http->getResponseBody() . '</pre>';
		}

	} elseif ($testCase == 4) {

		/**
		 * perform a GET request using a proxy host
		 */
		/*
		echo '<pre>';
		$http->setHost('www.yahoo.com');
		$http->setProxy('200.168.74.40', 80);
		if ($http->doGet('/') == HTTP_STATUS_OK) {
			var_dump($http->getResponseHeaders());
		} else {
			echo 'Return status: ' . $http->getStatus();
		}
		echo '</pre>';
		*/

	}

?>