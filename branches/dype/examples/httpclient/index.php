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

	$testCase = 2;

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
		 * define the host and perform a simple GET request
		 */
		$http->setHost('www.php.net');
		if ($http->doGet('/') == HTTP_STATUS_OK) {
			echo $http->getResponseBody();
		}

	} elseif ($testCase == 2) {

		/**
		 * set the target host and perform a multipart POST request,
		 * sending a file as parameter (upload file)
		 */
		$host = $_SERVER['SERVER_NAME'];
		if ($_SERVER['SERVER_PORT'] != 80)
			$host .= ':' . $_SERVER['SERVER_PORT'];
		$http->setHost($host);
		$cont = FileSystem::getContents('upload.txt');
		if ($http->doMultipartPost(
			$_SERVER['PHP_SELF'] . '?process=1',
			array(
				'name'=>'John Doe',
				'e_mail'=>'john@foo.org',
				'phone'=>'6666666',
				'message'=>'The quick brown fox jumps over the lazy dog',
				'contact'=>'bar@baz.foo.org'
			),
			array(
				array('name'=>'arquivo','file'=>'upload.txt','data'=>$cont)
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
		$postData = array(
			'name'=>'John Doe',
			'e_mail'=>'john@foo.org',
			'phone'=>'6666666',
			'message'=>'The quick brown fox jumps over the lazy dog',
			'contact'=>'bar@baz.foo.org'
		);
		if ($http->doPost(
			'index.php?process=1',
			$postData
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