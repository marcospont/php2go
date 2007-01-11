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
	import('php2go.net.CurlClient');

	echo '<b>PHP2Go Examples</b> : php2go.net.CurlClient<br><br>';

	// create and initialize an instance of the CurlClient class
	$curl = new CurlClient();
	$curl->init();

	// define the URL to be used in this example
	// this method accepts string URLs or php2go.net.Url instances
	$curl->setUrl(PHP2GO_ABSOLUTE_PATH . 'examples/resources/curlclient.request.php');

	println('<b>Perform a simple GET request</b>');
	// sinalize that we want the response headers
	$curl->setOption(CURLOPT_HEADER, 1);
	// perform the request
	$response = $curl->doGet();
	// parse the response, extracting the response code, headers and body
	print 'Response contents: ';
	dumpVariable($curl->parseResponse($response));

	// retrieve information about the last transfer
	println('<hr>');
	println('<b>Retrieve information about the last transfer</b>');
	$info = $curl->getTransferInfo();
	foreach ($info as $name => $value) {
		println("{$name} = {$value}");
	}

	// reset the configuration settings (CURL options)
	// this method must be called when a new transfer will be executed
	$curl->reset();

	println('<hr>');
	println('<b>Perform a POST request, saving the response body in a text file</b>');
	// set the file where the response must be written
	$ok = $curl->returnToFile('resources/curlclient.response.txt');
	// set the array of POST fields
	$curl->setPostData(array('foo' => 1, 'bar' => 2, 'baz' => 3));
	$return = $curl->doPost();
	if ($ok) {
		println('Response body contents:');
		readfile('resources/curlclient.response.txt');
	} else {
		println('It wasn\'t possible to write response to a file. Response body contents:');
		println($return);
	}

?>