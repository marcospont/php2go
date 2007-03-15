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

/**
 * CURL client class
 *
 * This is class is an abstraction layer over the functions
 * provided by the CURL extension.
 *
 * @package net
 * @uses System
 * @link http://www.php.net/curl
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class CurlClient extends PHP2Go
{
	/**
	 * Remote URL
	 *
	 * @var string
	 */
	var $url;

	/**
	 * Last error code
	 *
	 * @var int
	 */
	var $errorNumber;

	/**
	 * Last error message
	 *
	 * @var string
	 */
	var $errorString;

	/**
	 * CURL session handle
	 *
	 * @var resource
	 * @access private
	 */
	var $session;

	/**
	 * Whether the CURL session was already started
	 *
	 * @var bool
	 * @access private
	 */
	var $sessionActive = FALSE;

	/**
	 * Return file handle
	 *
	 * @var resource
	 * @access private
	 */
	var $returnHandle;

	/**
	 * Upload file handle
	 *
	 * @var resource
	 * @access private
	 */
	var $uploadHandle;

	/**
	 * Class constructor
	 *
	 * @return CurlClient
	 */
	function CurlClient() {
		parent::PHP2Go();
		if (!System::loadExtension('curl'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'curl'));
		$this->init();
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
	function __destruct() {
		$this->close();
	}

	/**
	 * Initializes the CURL session
	 */
	function init() {
		$this->session = curl_init();
		$this->sessionActive = TRUE;
		$this->reset();
	}

	/**
	 * Reset all connection properties
	 */
	function reset() {
		$this->setOption(CURLOPT_POSTFIELDS, NULL);
		$this->setOption(CURLOPT_RETURNTRANSFER, 1);
		$this->setOption(CURLOPT_UPLOAD, 0);
		$this->setOption(CURLOPT_HEADER, 0);
	}

	/**
	 * Get information about the last request
	 *
	 * If $infoName is missing, a hash array will all
	 * information fields is returned.
	 *
	 * @link http://www.php.net/curl_getinfo
	 * @param string $infoName Requested info key
	 * @return string|array
	 */
	function getTransferInfo($infoName='') {
		if (!isset($this->session))
			return FALSE;
		return (!empty($infoName) ? curl_getinfo($this->session, $infoName) : curl_getinfo($this->session));
	}

	/**
	 * Get last error code and message
	 *
	 * Returns an empty string when the last requested produced no errors.
	 *
	 * @return string
	 */
	function getTransferError() {
		if (!isset($this->session) || !isset($this->errorNumber))
			return '';
		return "[{$this->errorNumber}] $this->errorString";
	}

	/**
	 * Configures a CURL option
	 *
	 * @link http://www.php.net/curl_setopt
	 * @param int $optionCode Option code
	 * @param mixed $optionValue Option value
	 */
	function setOption($optionCode, $optionValue) {
		if (!$this->sessionActive)
			$this->init();
		if ($optionCode > 0)
			curl_setopt($this->session, $optionCode, $optionValue);
	}

	/**
	 * Set the target URL
	 *
	 * @param string|Url $url Connection URL
	 */
	function setUrl($url) {
		if (TypeUtils::isInstanceOf($url, 'Url'))
			$this->url = $url->getUrl();
		else
			$this->url = $url;
		$this->setOption(CURLOPT_URL, $this->url);
	}

	/**
	 * Set a referer to the CURL connection
	 *
	 * @param string $referer Referer
	 */
	function setReferer($referer) {
		$this->setOption(CURLOPT_REFERER, $referer);
	}

	/**
	 * Set the user agent to be used
	 *
	 * @param string $userAgent User agent identifier
	 */
	function setUserAgent($userAgent) {
		$this->setOption(CURLOPT_USERAGENT, $userAgent);
	}

	/**
	 * Set connection's POST parameters
	 *
	 * @param array $dataArray Parameters
	 */
	function setPostData($dataArray) {
		if (TypeUtils::isArray($dataArray) && !empty($dataArray)) {
			foreach($dataArray as $key => $value)
				$request[] = "$key=" . urlencode($value);
			$postFields = implode('&', $request);
			$this->setOption(CURLOPT_POSTFIELDS, $postFields);
		}
	}

	/**
	 * Attach a file to be upload through the CURL connection
	 *
	 * @param string $fileName File path
	 */
	function uploadFile($fileName) {
		$fileName = realpath($fileName);
		if (file_exists($fileName)) {
			$this->uploadHandle = @fopen($fileName, 'rb');
			$this->setOption(CURLOPT_UPLOAD, 1);
			$this->setOption(CURLOPT_INFILE, $this->uploadHandle);
			$this->setOption(CURLOPT_INFILESIZE, filesize($fileName));
		}
	}

	/**
	 * Configure a file where the returned response should be saved
	 *
	 * @param string $fileName File path
	 * @return bool
	 */
	function returnToFile($fileName) {
		if (file_exists($fileName) && $this->returnHandle = @fopen($fileName, 'wb')) {
			$this->setOption(CURLOPT_FILE, $this->returnHandle);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Performs a GET request
	 *
	 * Returns TRUE or FALSE when the response was configured to
	 * redirect to a file. Otherwise, returns the response payload.
	 *
	 * @return bool|string
	 */
	function doGet() {
		if (!isset($this->sessionActive))
			$this->init();
		if (!$returnValue = @curl_exec($this->session)) {
			$this->errorNumber = curl_errno($this->session);
			$this->errorString = curl_error($this->session);
		}
		$this->_disposeHandles();
		return $returnValue;
	}

	/**
	 * Performs a POST request
	 *
	 * Returns TRUE or FALSE when the response was configured to
	 * redirect to a file. Otherwise, returns the response payload.
	 *
	 * @return bool|string
	 */
	function doPost() {
		if (!isset($this->sessionActive))
			$this->init();
		//$this->setOption(CURLOPT_POST, 1);
		if (!$returnValue = @curl_exec($this->session)) {
			$this->errorNumber = curl_errno($this->session);
			$this->errorString = curl_error($this->session);
		}
		$this->_disposeHandles();
		return $returnValue;
	}

	/**
	 * Utility method to parse a response payload
	 *
	 * Returns a hash array containing 3 keys:
	 * # code : response code
	 * # headers : hash array of response headers
	 * # body : response body
	 *
	 * @param string $response Response payload
	 * @param string $crlf Line end chars
	 * @return array
	 */
	function parseResponse($response, $crlf="\r\n") {
		$result = array();
		$parts = explode($crlf . $crlf, $response, 2);
		if (sizeof($parts) == 2) {
			list($headers, $body) = $parts;
		} elseif (sizeof($parts) == 1) {
			$headers = $parts[0];
			$body = '';
		} else {
			$headers = '';
			$body = '';
		}
		$headerLines = explode($crlf, $headers);
   		$headerLine = array_shift($headerLines);
		if (preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@', $headerLine, $matches)) {
			$result['code'] = $matches[1];
   		} else {
   			$result['code'] = -1;
   		}
   		$result['headers'] = array();
   		foreach ($headerLines as $headerLine) {
   			list($header, $value) = explode(': ', $headerLine, 2);
   			$result['headers'][$header] = $value;
   		}
   		$result['body'] = $body;
   		return $result;
	}

	/**
	 * Closes the CURL session
	 */
	function close() {
		if ($this->sessionActive) {
			@curl_close($this->session);
			$this->sessionActive = FALSE;
		}
	}

	/**
	 * Dispose return and upload file handles
	 *
	 * @access private
	 */
	function _disposeHandles() {
		if (isset($this->returnHandle)) {
			@fclose($this->returnHandle);
			unset($this->returnHandle);
		}
		if (isset($this->uploadHandle)) {
			@fclose($this->uploadHandle);
			unset($this->uploadHandle);
		}
	}
}
?>