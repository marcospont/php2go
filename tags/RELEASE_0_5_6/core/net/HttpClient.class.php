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

import('php2go.net.httpConstants', 'php', FALSE);
import('php2go.net.HttpCookie');
import('php2go.net.MimeType');
import('php2go.net.SocketClient');
import('php2go.net.Url');

/**
 * HTTP client class
 *
 * This class is RCF2616 compliant, works with HTTP 1.0 and HTTP 1.1 and performs
 * GET, POST, TRACE and DELETE requests. Supports posting of form data and/or files,
 * use of proxy servers, automatic parsing of location redirects, sending and parsing
 * cookies and Keep-Alive connections.
 *
 * Example:
 * <code>
 * $http = new HttpClient();
 * $http->setFollowRedirects(TRUE);
 * $http->setUserAgent('MyUserAgent (compatible; MyBrowser; Linux)');
 * $http->setHost('myhost.org');
 * $postVars = array(
 *   'name' => 'John Doe',
 *   'e_mail' => 'john@foo.org',
 *   'phone' => '6666666',
 *   'message' => 'The quick brown fox jumps over the lazy dog',
 *   'contact' => 'foo@bar.baz.org'
 * );
 * /* POST example {@*}
 * if ($http->doPost('page.php', $postVars) == HTTP_STATUS_OK) {
 *   print '<pre>' . $http->getResponseBody() . '</pre>';
 * }
 * /* multipart POST example (post vars and upload files) {@*}
 * $uploadFiles = array(
 *   array(
 *     'name' => 'file',
 *     'file' => 'file.txt',
 *     'data' => file_get_contents('file.txt')
 *   )
 * );
 * if ($http->doMultipartPost('page.php', $postVars, $uploadFiles) == HTTP_STATUS_OK) {
 *   print '<pre>' . $http->getResponseBody() . '</pre>';
 * }
 * </code>
 *
 * @package net
 * @uses HttpCookie
 * @uses MimeType
 * @uses Url
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class HttpClient extends SocketClient
{
	/**
	 * Current host
	 *
	 * @var string
	 */
	var $httpHost;

	/**
	 * Current port
	 *
	 * @var int
	 */
	var $httpPort;

	/**
	 * Current HTTP version
	 *
	 * @var string
	 */
	var $httpVersion;

	/**
	 * User agent to be sent through HTTP connections
	 *
	 * @var string
	 */
	var $userAgent;

	/**
	 * Referer to be sent through HTTP connections
	 *
	 * @var string
	 */
	var $referer;

	/**
	 * Enable keep alive mode for opened connections
	 *
	 * @var bool
	 */
	var $keepAlive;

	/**
	 * Whether to use authentication
	 *
	 * @var bool
	 */
	var $useAuth;

	/**
	 * Auth username
	 *
	 * @var string
	 */
	var $authUser;

	/**
	 * Auth password
	 *
	 * @var string
	 */
	var $authPass;

	/**
	 * Whether to connect through a proxy server
	 *
	 * @var bool
	 */
	var $useProxy;

	/**
	 * Proxy host
	 *
	 * @var string
	 */
	var $proxyHost;

	/**
	 * Proxy port
	 *
	 * @var int
	 */
	var $proxyPort;

	/**
	 * Proxy user
	 *
	 * @var string
	 */
	var $proxyUser;

	/**
	 * Proxy password
	 *
	 * @var string
	 */
	var $proxyPass;

	/**
	 * Whether to follow redirections
	 *
	 * @var bool
	 */
	var $followRedirects;

	/**
	 * Debug flag
	 *
	 * @var bool
	 */
	var $debug = FALSE;

	/**
	 * Last HTTP method used
	 *
	 * @var string
	 * @access private
	 */
	var $currentMethod = NULL;

	/**
	 * Request headers
	 *
	 * @var array
	 * @access private
	 */
	var $requestHeaders = array();

	/**
	 * Request body
	 *
	 * @var string
	 */
	var $requestBody = '';

	/**
	 * Response headers
	 *
	 * @var array
	 * @access private
	 */
	var $responseHeaders = array();

	/**
	 * Response body
	 *
	 * @var string
	 * @access private
	 */
	var $responseBody = '';

	/**
	 * Incoming/Outgoing cookies
	 *
	 * @var array
	 * @access private
	 */
	var $cookies = array();

	/**
	 * Class constructor
	 *
	 * @return HttpClient
	 */
	function HttpClient() {
		parent::SocketClient();
		parent::setTimeout(HTTP_DEFAULT_TIMEOUT);
		parent::setBufferSize(4096);
		parent::setLineEnd(HTTP_CRLF);
		$this->httpPort = HTTP_DEFAULT_PORT;
		$this->httpVersion = '1.1';
		$this->userAgent = 'PHP2Go Http Client ' . PHP2GO_VERSION . ' (compatible; MSIE 6.0; Linux)';
		$this->keepAlive = FALSE;
		$this->useAuth = FALSE;
		$this->useProxy = FALSE;
		$this->followRedirects = FALSE;
	}

	/**
	 * Set the connection host
	 *
	 * @param string $host HTTP host or IP address
	 */
	function setHost($host) {
		$this->httpHost = $host;
	}

	/**
	 * Set the connection port
	 *
	 * @param int $port Connection port
	 */
	function setPort($port) {
		$this->httpPort = $port;
	}

	/**
	 * Set the HTTP version
	 *
	 * @param string $version '1.0' or '1.1'
	 */
	function setHttpVersion($version) {
		if (in_array($version, array('1.0', '1.1')))
			$this->httpVersion = $version;
	}

	/**
	 * Set connection's user agent
	 *
	 * @param string $userAgent User agent
	 */
	function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
	}

	/**
	 * Set connection's referer
	 *
	 * @param string $referer Referer
	 */
	function setReferer($referer) {
		$this->referer = $referer;
	}

	/**
	 * Set authentication credentials to the
	 * next opened connection
	 *
	 * @param string $userName Username
	 * @param string $password Password
	 * @return bool
	 */
	function setAuth($userName, $password) {
		if (trim($userName) != '' && trim($password) != '') {
			$this->useAuth = TRUE;
			$this->authUser = trim($userName);
			$this->authPass = trim($password);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Set proxy server and proxy credentials to be
	 * used by the next opened connection
	 *
	 * @param string $host Proxy host or IP address
	 * @param string $port Proxy port
	 * @param string $userName Proxy username
	 * @param string $password Proxy password
	 * @return bool
	 */
	function setProxy($host, $port, $userName='', $password='') {
		if (trim($host) != '' && TypeUtils::isInteger($port)) {
			$this->keepAlive = FALSE;
			$this->useProxy = TRUE;
			$this->proxyHost = trim($host);
			$this->proxyPort = $port;
			if (trim($userName) != '' && trim($password) != '') {
				$this->proxyUser = trim($userName);
				$this->proxyPass = trim($password);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Enable/disable keep alive mode on
	 * the current HTTP connection
	 *
	 * @param bool $setting Enable/disable
	 */
	function setKeepAlive($setting=TRUE) {
		if (!$this->useProxy)
			$this->keepAlive = (bool)$setting;
	}

	/**
	 * Enable/disable automatic following of "redirect" headers
	 *
	 * @param bool $setting Enable/disable
	 */
	function setFollowRedirects($setting=TRUE) {
		$this->followRedirects = (bool)$setting;
	}

	/**
	 * Get a request header
	 *
	 * @param string $name Header name
	 * @return mixed
	 */
	function getRequestHeader($name) {
		$formattedName = $this->_formatHeaderName($name);
		if (isset($this->requestHeaders[$formattedName]))
			return $this->requestHeaders[$formattedName];
		else
			return NULL;
	}

	/**
	 * Set a request header
	 *
	 * @param string $name Header name
	 * @param string $value Header value
	 */
	function setRequestHeader($name, $value) {
		if (trim($name) != '' && trim($value) != '') {
			$formattedName = $this->_formatHeaderName(trim($name));
			$this->requestHeaders[$formattedName] = $value;
		}
	}

	/**
	 * Removes a request header
	 *
	 * @param string $name Header name
	 * @return bool
	 */
	function removeRequestHeader($name) {
		if (trim($name) != '') {
			$formattedName = $this->_formatHeaderName(trim($name));
			if (isset($this->requestHeaders[$formattedName])) {
				unset($this->requestHeaders[$formattedName]);
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Get the response status
	 *
	 * @return int Response status
	 */
	function getStatus() {
		$status = $this->getResponseHeader('Status');
		if (!is_null($status))
			return intval($status);
		else
			return NULL;
	}

	/**
	 * Get a response header by name
	 *
	 * @param string $name Header name
	 * @return mixed
	 */
	function getResponseHeader($name) {
		$formattedName = $this->_formatHeaderName($name);
		if (isset($this->responseHeaders[$formattedName]))
			return $this->responseHeaders[$formattedName];
		else
			return NULL;
	}

	/**
	 * Get all response headers
	 *
	 * @return array|NULL
	 */
	function getResponseHeaders() {
		return (!empty($this->responseHeaders)) ? $this->responseHeaders : NULL;
	}

	/**
	 * Get the response body
	 *
	 * @return string
	 */
	function getResponseBody() {
		return $this->responseBody;
	}

	/**
	 * Reset the class properties, and prepare it
	 * for a new HTTP connection
	 */
	function reset() {
		$this->httpPort = HTTP_DEFAULT_PORT;
		$this->httpVersion = '1.1';
		$this->userAgent = 'PHP2Go Http Client ' . PHP2GO_VERSION . ' (compatible; MSIE 6.0; Linux)';
		$this->referer = NULL;
		$this->keepAlive = FALSE;
		$this->useAuth = FALSE;
		$this->useProxy = FALSE;
		$this->followRedirects = FALSE;
		$this->_resetRequest();
		$this->_resetResponse();
	}

	/**
	 * Performs a HEAD request
	 *
	 * @param string $uri Request URI
	 * @return int Response status
	 */
	function doHead($uri) {
		if (is_null($uri) || empty($uri))
			$uri = '/';
		// reopen connection if necessary
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// set default headers
		$this->_setDefaultHeaders($uri);
		// send the command to the server
		$this->currentMethod = 'HEAD';
		$data = sprintf("%s %s HTTP/%s%s%s%s", $this->currentMethod, $uri, $this->httpVersion, HTTP_CRLF, $this->_assembleRequestHeaders(), HTTP_CRLF);
		$this->_sendCommand($data);
		$this->_getResponse();
		$this->_ensureConnectionRelease();
		if ($this->_processUseProxyResponse())
			return $this->doHead($uri);
		return $this->getStatus();
	}

	/**
	 * Performs a GET request
	 *
	 * @param string $uri Request URI
	 * @return int Response status
	 */
	function doGet($uri) {
		if (is_null($uri) || empty($uri))
			$uri = '/';
		// reopen connection if necessary
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// set default headers
		$this->_setDefaultHeaders($uri);
		// send the command to the server
		$this->currentMethod = 'GET';
		$data = sprintf("%s %s HTTP/%s%s%s%s", $this->currentMethod, $uri, $this->httpVersion, HTTP_CRLF, $this->_assembleRequestHeaders(), HTTP_CRLF);
		$this->_sendCommand($data);
		$this->_getResponse();
		$this->_ensureConnectionRelease();
		$this->_processRedirectResponse();
		if ($this->_processUseProxyResponse())
			return $this->doGet($uri);
		return $this->getStatus();
	}

	/**
	 * Performs a POST request
	 *
	 * @param string $uri URI
	 * @param array $parameters Hash array of parameters
	 * @return int Response status
	 * @uses _assembleParameters
	 * @uses _sendPost
	 */
	function doPost($uri, $parameters) {
		// reopen connection if necessary
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// set default headers
		$this->_setDefaultHeaders($uri);
		// build the request body
		$body = $this->_assembleParameters($parameters) . HTTP_CRLF . HTTP_CRLF;
		$this->requestBody = $body;
		// set extra headers
		$this->setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		$this->setRequestHeader('Content-Length', strlen($body));
		// send the command to the server
		$this->_sendPost($uri);
		$this->_processRedirectResponse();
		if ($this->_processUseProxyResponse())
			return $this->doPost($uri, $parameters);
		return $this->getStatus();
	}

	/**
	 * Performs a multipart POST request
	 *
	 * @param string $uri URI
	 * @param array $parameters Hash array of parameters
	 * @param array $files Array of upload files
	 * @return int Response status
	 * @uses _assembleMultipartData
	 * @uses _sendPost
	 */
	function doMultipartPost($uri, $parameters, $files = NULL) {
		// reopen the connection if necessary
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// set default headers
		$this->_setDefaultHeaders($uri);
		// build the request body
		$boundary = '----=_NextPart' . date( 'YmdHis' ) . '_' . rand(10000, 99999);
		$body = $this->_assembleMultipartData($boundary, $parameters, $files) . HTTP_CRLF . HTTP_CRLF;
		$this->requestBody = $body;
		// set extra headers
		$this->setRequestHeader('Content-Type', 'multipart/form-data; boundary=' . $boundary);
		$this->setRequestHeader('Content-Length', strlen($body));
		// send the command to the server
		$this->_sendPost($uri);
		$this->_processRedirectResponse();
		if ($this->_processUseProxyResponse())
			return $this->doMultipartPost($uri, $parameters, $files);
		return $this->getStatus();
	}

	/**
	 * Send a POST request containing XML
	 *
	 * @param string $uri URI
	 * @param string $xmlData XML data or file name
	 * @param bool $byFile Whether to parse $xmlData as XML data (FALSE) or XML file name (TRUE)
	 * @param string $charset Request charset
	 * @return int Response status
	 * @uses _sendPost
	 */
	function doXmlPost($uri, $xmlData, $byFile=FALSE, $charset=NULL) {
		if (empty($charset))
			$charset = 'iso-8859-1';
		// reopen the connection if necessary
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// set default headers
		$this->_setDefaultHeaders($uri);
		// build the request body
		if ($byFile) {
			$body = @file_get_contents($xmlData);
			if (!$body)
				return FALSE;
		} else {
			$body = $xmlData;
		}
		$this->requestBody = $body;
		// set extra headers
		$this->setRequestHeader('Content-Type', 'text/xml; charset=' . $charset);
		$this->setRequestHeader('Content-Length', strlen($body));
		// send command to the server
		$this->_sendPost($uri);
		$this->_processRedirectResponse();
		if ($this->_processUseProxyResponse())
			return $this->doXmlPost($uri, $xmlData, $byFile, $charset);
		return $this->getStatus();
	}

	/**
	 * Performs an HTTP DELETE request
	 *
	 * @param string $uri URI
	 * @return int Response status
	 */
	function doDelete($uri) {
		// reopen the connection if necessary
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// set default headers
		$this->_setDefaultHeaders($uri);
		// send command to the server
		$data = sprintf("DELETE %s HTTP/%s%s%s%s", $uri, $this->httpVersion, HTTP_CRLF, $this->_assembleRequestHeaders(), HTTP_CRLF);
		$this->_sendCommand($data);
		$this->_getResponse();
		$this->_ensureConnectionRelease();
		return $this->getStatus();
	}

	/**
	 * Opens a connection with the HTTP host
	 *
	 * @uses SocketClient::connect()
	 * @access private
	 * @return bool
	 */
	function _connect() {
		if (!isset($this->httpHost)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_HTTP_MISSING_HOST');
			return FALSE;
		}
		if ($this->useProxy)
			return parent::connect($this->proxyHost, $this->proxyPort, NULL, HTTP_DEFAULT_TIMEOUT);
		else
			return parent::connect($this->httpHost, $this->httpPort, NULL, HTTP_DEFAULT_TIMEOUT);
	}

	/**
	 * Send a POST command to the HTTP server
	 *
	 * @param string $uri Target URI
	 */
	function _sendPost($uri) {
		if (is_null($uri) || empty($uri))
			$uri = '/';
		$this->currentMethod = 'POST';
		$data = sprintf("%s %s HTTP/%s%s%s%s", $this->currentMethod, $uri, $this->httpVersion, HTTP_CRLF, $this->_assembleRequestHeaders(), HTTP_CRLF);
		$this->_sendCommand($data);
		usleep(10);
		$this->_sendCommand($this->requestBody);
		$this->_getResponse();
		$this->_ensureConnectionRelease();
	}

	/**
	 * Send a command to the HTTP server
	 *
	 * @param string $data Command data
	 * @access private
	 */
	function _sendCommand($data) {
		parent::write($data);
		if ($this->debug)
			print('HTTP DEBUG --- FROM CLIENT : ' . nl2br($data));
	}

	/**
	 * Defines the default HTTP headers before performing a request
	 *
	 * Default request headers are: Host, Proxy-Connection and Proxy-Authorization
	 * (when using a proxy server), Connection, Pragma, Cache-Control, Authorization
	 * (when using authorization), User-Agent, Referer and Cookie
	 *
	 * @param string $uri Request URI
	 * @access private
	 */
	function _setDefaultHeaders(&$uri) {
		if ($this->useProxy) {
			$this->setRequestHeader('Host', $this->httpHost . ':' . $this->httpPort);
			$this->setRequestHeader('Proxy-Connection', ($this->keepAlive ? 'Keep-Alive' : 'Close'));
			if (isset($this->proxyUser))
				$this->setRequestHeader('Proxy-Authorization', 'Basic ' . base64_encode($this->proxyUser . ':' . $this->proxyPass));
			$uri = 'http://' . $this->httpHost . ':' . $this->httpPort . $uri;
		} else {
			$this->setRequestHeader('Host', $this->httpHost);
			$this->setRequestHeader('Connection', ($this->keepAlive ? 'Keep-Alive' : 'Close'));
			$this->setRequestHeader('Pragma', 'no-cache');
			$this->setRequestHeader('Cache-Control', 'no-cache');
		}
		if ($this->useAuth)
			$this->setRequestHeader('Authorization', 'Basic ' . base64_encode($this->authUser . ':' . $this->authPass));
		$cookies = $this->_getCookies($this->httpHost, $this->_getCurrentPath($uri));
		$this->setRequestHeader('User-Agent', $this->userAgent);
		$this->setRequestHeader('Accept', '*/*');
		$this->setRequestHeader('Referer', (isset($this->referer) ? $this->referer : ''));
		$this->setRequestHeader('Cookie', $cookies);
	}

	/**
	 * Serializes the headers of an HTTP request
	 *
	 * @access private
	 * @return string
	 */
	function _assembleRequestHeaders() {
		$headerString = '';
		foreach($this->requestHeaders as $name => $value) {
			$headerString .= sprintf("%s: %s%s", $name, $value, HTTP_CRLF);
		}
		return $headerString;
	}

	/**
	 * Builds the body of a POST request
	 *
	 * Serializes an N-dimension array of request parameters into
	 * an HTTP request body.
	 *
	 * @param array $parameters Hash array of parameters
	 * @param string $paramName Used by recursive calls, when parameter values are arrays
	 * @access private
	 * @return string
	 */
	function _assembleParameters($parameters, $paramName='') {
		$paramsString = '';
		foreach ($parameters as $key => $value)
			if (!is_array($value)) {
				if (trim($paramName) != '')
					$paramsString .= sprintf("&%s[%s]=%s", $paramName, $key, urlencode($value));
				else
					$paramsString .= sprintf("&%s=%s", $key, urlencode($value));
			} else {
				$paramsString .= '&' . $this->_assembleFormData($parameters[$key], $key);
			}
		return substr($paramsString, 1);
	}

	/**
	 * Builds the body of a multipart POST request
	 *
	 * @param string $boundary Part boundary
	 * @param array $formData Hash array of form variables
	 * @param array $formFiles Array of upload files
	 * @return string
	 */
	function _assembleMultipartData($boundary, $formData, $formFiles = NULL) {
		$boundary = '--' . $boundary;
		$formString = '';
		if (is_array($formData)) {
			foreach ($formData as $name => $data) {
				$formString .= sprintf("%s%sContent-Disposition: form-data; name=\"%s\"%s%s%s%s", $boundary, HTTP_CRLF, $name, HTTP_CRLF, HTTP_CRLF, $data, HTTP_CRLF);
			}
		}
		if (is_array($formFiles)) {
			foreach ($formFiles as $data) {
				if (!isset($data['type'])) {
					$data['type'] = MimeType::getFromFileName($data['file']);
				}
				$formString .= sprintf("%s%sContent-Disposition: form-data; name=\"%s\"; filename=\"%s\"%sContent-Type: %s%s%s%s%s", $boundary, HTTP_CRLF, $data['name'], $data['file'], HTTP_CRLF, $data['type'], HTTP_CRLF, HTTP_CRLF, $data['data'], HTTP_CRLF);
			}
		}
		$formString .= $boundary . '--' . HTTP_CRLF;
		return $formString;
	}

	/**
	 * Reads an HTTP response
	 *
	 * @param bool $getBody Whether to read response body or just the headers
	 * @access private
	 */
	function _getResponse($getBody=TRUE) {
		// initialize request and response data
		$this->_resetRequest();
		$this->_resetResponse();
		while (1) {
			// read lines from the socket until a line containing only CRLF is found
			$rawHeaders = '';
			while (($line = parent::readLine()) != HTTP_CRLF || $rawHeaders == '')
				if ($line != HTTP_CRLF) $rawHeaders .= $line;
			// parse response headers
			$this->_parseResponseHeaders($rawHeaders);
			// handle 'Continue' status
			if ($this->getStatus() != HTTP_STATUS_CONTINUE)
				break;
			parent::writeLine();
		}
		if ($this->debug)
			print('HTTP DEBUG --- FROM SERVER : ' . $rawHeaders . '<br>');
		if ($getBody) {
			$body = '';
			if (strtolower($this->getResponseHeader('Transfer-Encoding')) != 'chunked' && !$this->keepAlive)
				$body = parent::readAllContents();
			else if (!is_null($this->getResponseHeader('Content-Length'))) {
				$contentLength = intval($this->getResponseHeader('Content-Length'));
				$body = parent::read($contentLength);
			} else if (!is_null($this->getResponseHeader('Transfer-Encoding')))
				if ($this->getResponseHeader('Transfer-Encoding') == 'chunked') {
					$chunkSize = intval(hexdec(parent::readLine()));
					while ($chunkSize > 0) {
						$body .= parent::read($chunkSize);
						parent::read(strlen(HTTP_CRLF));
						$chunkSize = intval(hexdec(parent::readLine()));
					}
				}
			$this->responseBody = $body;
		}
	}

	/**
	 * Parse all response headers from the raw headers string
	 *
	 * @param string $headers Headers string
	 * @access private
	 */
	function _parseResponseHeaders($headers) {
		$headers = preg_replace("/^" . HTTP_CRLF . "/", '', $headers);
		$headersArray = explode(HTTP_CRLF, $headers);
		$matches = NULL;
		if (preg_match("'HTTP/(\d\.\d)\s+(\d+).*'i", $headersArray[0], $matches)) {
			if ($matches[1])
				$this->responseHeaders['Protocol-Version'] = $matches[1];
			if ($matches[2])
				$this->responseHeaders['Status'] = $matches[2];
		}
		array_shift($headersArray);
		foreach($headersArray as $headerValue) {
			if (ereg("([^:]+):(.*)", $headerValue, $matches)) {
				$key = $matches[1];
				$value = trim($matches[2]);
				if (strtoupper($key) == 'SET-COOKIE') {
					if ($Cookie = $this->_parseCookie($value))
						$this->cookies[$Cookie->getName()] = $Cookie;
				} elseif (!empty($value)) {
					$this->responseHeaders[$this->_formatHeaderName($key)] = $value;
				}
			}
		}
	}

	/**
	 * Create an {@link HttpCookie} object from a Set-Cookie header
	 *
	 * @param string $cookieString Cookie header
	 * @return HttpCookie
	 * @access private
	 */
	function _parseCookie($cookieString) {
		$Cookie = new HttpCookie();
		$Cookie->parseFromHeader($cookieString, $this->httpHost);
		return $Cookie;
	}

	/**
	 * Build the Set-Cookie request header from the cookies
	 * parsed from the last response
	 *
	 * @param string $domain Cookies domain
	 * @param string $path Cookies path
	 * @return string Header value
	 * @access private
	 */
	function _getCookies($domain, $path) {
		$cookieString = '';
		foreach($this->cookies as $cookieName => $Cookie) {
			if (!$Cookie->isExpired()) {
				if ($Cookie->isDomain($domain) && $Cookie->isPath($path))
					$cookieString = $Cookie->getName() . '=' . $Cookie->getValue() . '; ';
			} else
				unset($this->cookies[$cookieName]);
		}
		return $cookieString;
	}

	/**
	 * Check if connection should be closed, according to class
	 * settings and response headers
	 *
	 * @access private
	 */
	function _ensureConnectionRelease() {
		// closes the connection when keep alive is disabled
		if (parent::isConnected() && !$this->keepAlive)
			parent::close();
		// closes the connection if Connection header is equal to 'close'
		if (!is_null($this->getResponseHeader('Connection')))
			if ($this->keepAlive && strtolower($this->getResponseHeader('Connection')) == 'close') {
				$this->keepAlive = FALSE;
				parent::close();
			}
	}

	/**
	 * Handles a "redirect" HTTP status
	 *
	 * Opens a connection with the URL indicated by the "Location" response header.
	 *
	 * @access private
	 */
	function _processRedirectResponse() {
		// check if redirects should be followed
		if ($this->followRedirects && in_array($this->getStatus(), array(HTTP_STATUS_MOVED_PERMANENTLY, HTTP_STATUS_FOUND, HTTP_STATUS_SEE_OTHER))) {
			// get the Location response header
			$uri = $this->getResponseHeader('Location');
			if (!is_null($uri) && !empty($uri)) {
				$Url = new Url($uri);
				$redirectHost = $Url->getHost();
				$redirectPort = TypeUtils::ifNull($Url->getPort(), HTTP_DEFAULT_PORT);
				$redirectFile = TypeUtils::ifNull($Url->getPath(), '/');
				$redirectQueryString = TypeUtils::ifNull($Url->getQueryString(), '');
				if (!empty($redirectQueryString))
					$redirectQueryString = '?' . $redirectQueryString;
				// update class properties if host or port had changed
				if ($redirectHost != $this->httpHost || $redirectPort != $this->httpPort) {
					$this->httpHost = $redirectHost;
					$this->httpPort = $redirectPort;
					if (!$this->useProxy)
						parent::close();
				}
				usleep(100);
				$this->doGet($redirectFile . $redirectQueryString);
			}
		}
	}

	/**
	 * Process an "use proxy" HTTP status
	 *
	 * Connects to the proxy URL indicated by the "Location" header.
	 *
	 * @access private
	 * @return bool
	 */
	function _processUseProxyResponse() {
		if ($this->getStatus() == HTTP_STATUS_USE_PROXY) {
			parent::close();
			$Url = new Url($this->getResponseHeader('Location'));
			$proxyHost = $Url->getHost();
			$proxyPort = TypeUtils::ifNull($Url->getPort(), HTTP_DEFAULT_PORT);
			$this->setProxy($proxyHost, $proxyPort);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Reset request data
	 *
	 * @access private
	 */
	function _resetRequest() {
		$this->requestHeaders = array();
		$this->cookieHeaders = array();
		$this->cookies = array();
	}

	/**
	 * Reset response data
	 *
	 * @access private
	 */
	function _resetResponse() {
		$this->responseHeaders = array();
		$this->responseBody = '';
	}

	/**
	 * Get the path part of an URI
	 *
	 * @param string $uri URI
	 * @access private
	 * @return string
	 */
	function _getCurrentPath($uri) {
		$uriParts = explode('/', $uri);
		array_pop($uriParts);
		$currentPath = implode('/', $uriParts) . '/';
		return ($currentPath != '') ? $currentPath : '/';
	}

	/**
	 * Normalize an HTTP header name
	 *
	 * @param string $headerName Header name
	 * @access private
	 * @return string
	 */
	function _formatHeaderName($headerName) {
		$formatted = ucwords(str_replace('-', ' ', strtolower($headerName)));
		return str_replace(' ', '-', $formatted);
	}
}
?>