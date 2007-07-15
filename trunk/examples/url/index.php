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
	import('php2go.net.Url');

	println('<b>PHP2Go Examples</b> : php2go.net.Url<br />');

	/**
	 * URL to be tested
	 */
	$testUrl = 'http://user:pass@www.domain.com:8080/path/internal/script.php?parameter=2#anchor';
	println('Original URL : ' . $testUrl);
	println('<hr />');

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
	println('Build anchor: ' . $url->getAnchor('Click me!'));
	println('<hr />');
	/**
	 * get the normalized URL value
	 */
	println('Final URL: ' . $url->getUrl());
	println('<hr />');
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
	println('<hr />');
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