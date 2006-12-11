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

import('php2go.net.httpConstants', 'php', FALSE);
import('php2go.net.MimeType');
import('php2go.net.Url');

/**
 * Collection of methods to handle with the HTTP response
 *
 * @package net
 * @uses Environment
 * @uses HttpRequest
 * @uses MimeType
 * @uses TypeUtils
 * @uses Url
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class HttpResponse extends PHP2Go
{
	/**
	 * Verify if response headers have already been sent
	 *
	 * @return bool
	 * @static
	 */
	function headersSent() {
		return headers_sent();
	}

	/**
	 * Send a response header
	 *
	 * @param string $name Header name
	 * @param mixed $value Header value
	 * @param bool $replace Whether to replace existent one
	 * @static
	 */
	function addHeader($name, $value='', $replace=TRUE) {
		if (empty($value) && strlen($value) == 0)
			@header("$name", TypeUtils::toBoolean($replace));
		else
			@header("$name: $value", TypeUtils::toBoolean($replace));
	}

	/**
	 * Send an array of response headers
	 *
	 * @param array $headers Response headers
	 * @static
	 */
	function addHeaders($headers) {
		foreach((array)$headers as $name => $value)
			HttpResponse::addHeader($name, $value);
	}

	/**
	 * Send a cookie
	 *
	 * @param HttpCookie $Cookie HTTP cookie
	 * @static
	 */
	function addCookie($Cookie) {
		if (TypeUtils::isInstanceOf($Cookie, 'HttpCookie')) {
			setcookie($Cookie->getName(), $Cookie->getValue(),
					$Cookie->getExpiryTime(), $Cookie->getPath(),
					$Cookie->getDomain(), ($Cookie->isSecure() ? 1 : 0));
		}
	}

	/**
	 * Redirect to a given location
	 *
	 * The $location parameter can either be a string
	 * or an instance of the Url class.
	 *
	 * @param string|Url $location Target URL
	 * @static
	 */
	function redirect($location) {
		if (TypeUtils::isInstanceOf($location, 'Url'))
			$location = $location->getUrl();
		else
			$location = (string)$location;
		HttpResponse::setStatus(HTTP_STATUS_MOVED_PERMANENTLY);
		HttpResponse::addHeader('Location', $location);
		HttpResponse::addHeader('Connection', 'close');
	}

	/**
	 * Set response status
	 *
	 * @param int $code Status code
	 * @param string $httpVersion HTTP version
	 * @static
	 */
	function setStatus($code, $httpVersion='1.0') {
		HttpResponse::addHeader("HTTP/$httpVersion $code");
	}

	/**
	 * Send the proper response headers to download a file
	 *
	 * MIME type and content disposition can be defined
	 * through the 3rd and 4th arguments. By default, MIME
	 * type is determined based on the file extension, and
	 * the content disposition is set to 'attachment'.
	 *
	 * Examples:
	 * <code>
	 * HttpResponse::download('data.xls', filesize('data.xls'));
	 * readfile('data.xls');
	 *
	 * HttpResponse::download('file.xxx', filesize('file.xxx'), 'application/force-download', 'attachment');
	 * readfile('file.xxx');
	 * </code>
	 *
	 * @param string $fileName File name
	 * @param int $length File content length
	 * @param string $mimeType MIME type
	 * @param string $contentDisp Content disposition
	 * @static
	 */
	function download($fileName, $length=0, $mimeType='', $contentDisp='') {
		if (empty($mimeType))
			$mimeType = MimeType::getFromFileName($fileName);
		if (empty($contentDisp))
			$contentDisp = 'attachment';
		$headers = array();
		$headers['Content-Type'] = $mimeType;
		$headers['Content-Disposition'] = "{$contentDisp}; filename=\"{$fileName}\"";
		if ($length)
			$headers['Content-Length'] = $length;
		$headers['Last-Modified'] = gmdate("D, d M Y H:i:s") . ' GMT';
		$headers['Expires'] = '0';
		$Agent =& UserAgent::getInstance();
		if ($Agent->matchBrowser('ie')) {
			$isSecure = (strtolower(Environment::get('HTTPS')) == 'on' || Environment::has('SSL_PROTOCOL_VERSION'));
			$headers['Pragma'] = 'public';
			$headers['Cache-Control'] = ($isSecure ? 'max-age=0' : 'must-revalidate, post-check=0, pre-check=0');
		} else {
			$headers['Pragma'] = 'no-cache';
		}
		HttpResponse::addHeaders($headers);
    }
}
?>