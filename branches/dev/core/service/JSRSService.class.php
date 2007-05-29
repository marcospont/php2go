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

import('php2go.service.AbstractService');

/**
 * Handles JSRS requests
 *
 * JSRS is an acronym for Javascript Remote Scripting. It's a library that
 * is able to perform calls to PHP functions from inside a Javascript code
 * block. Internally, the request is performed through a hidden IFRAME
 * element.
 *
 * This class handles these requests. Parses the request, calls the requested
 * function or method and prints its results.
 *
 * @package service
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class JSRSService extends AbstractService
{
	/**
	 * JSRS context ID
	 *
	 * @var int
	 * @access private
	 */
	var $contextId = NULL;

	/**
	 * Class constructor
	 *
	 * @return JSRSService
	 */
	function JSRSService() {
		parent::AbstractService();
	}

	/**
	 * Get the singleton of the JSRSService class
	 *
	 * @return JSRSService
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new JSRSService();
		return $instance;
	}

	/**
	 * Utility method to convert an array into a string with separators
	 *
	 * @param array $arr Input array
	 * @param string $lineSep Lines separator
	 * @param string $colSep Columns separator
	 * @return string
	 * @static
	 */
	function arrayToString($arr, $lineSep='|', $colSep='~') {
		$arr = (array)$arr;
		if (empty($arr))
			return '';
		$lines = array();
		foreach ($arr as $line) {
			$lines[] = (is_array($line) ? implode($colSep, $line) : $line);
		}
		return implode($lineSep, $lines);
	}

	/**
	 * Verifies if JSRS debug flag is present in the request
	 *
	 * @return bool
	 * @static
	 */
	function debugEnabled() {
		$source = ($_SERVER['REQUEST_METHOD'] == 'GET' ? $_GET : $_POST);
		return (!empty($source['C']) && @$source['D'] == 1);
	}

	/**
	 * Tries to parse context ID, handler ID and handler arguments from the request
	 *
	 * @access protected
	 * @return bool
	 */
	function acceptRequest() {
		$source = ($_SERVER['REQUEST_METHOD'] == 'GET' ? $_GET : $_POST);
		$context = (array_key_exists('C', $source) ? $source['C'] : NULL);
		$handler = (array_key_exists('F', $source) ? $source['F'] : NULL);
		$args = array();
		$i = 0;
		while (array_key_exists('P' . $i, $source)) {
			$argument = $source['P' . $i];
			$args[] = substr($argument, 1, -1);
			$i++;
		}
		if (!empty($context)) {
			$this->contextId = $context;
			$this->handlerId = $handler;
			$this->handlerParams = $args;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Builds and prints the response produced by the JSRS handler
	 *
	 * @param string $response Response contents
	 * @access protected
	 */
	function sendResponse($response) {
		// force string type
		if (!empty($response))
			$response = strval($response);
		else
			$response = '';
		print "<html><head></head><body onload=\"p=document.layers?parentLayer:window.parent;p.jsrsLoaded('" . $this->contextId . "');\">jsrsPayload:<br />" . "<form name=\"jsrs_Form\"><textarea name=\"jsrs_Payload\">" . $this->_escapeResponse($response) . "</textarea></form></body></html>";
		exit;
	}

	/**
	 * Executed when JSRS handler ID is not found in the request
	 *
	 * @access protected
	 */
	function onMissingHandler() {
		$msg = PHP2Go::getLangVal('ERR_SERVICE_MISSING_HANDLER', SERVICE_TYPE_JSRS);
		$cleanMsg = 'jsrsError: ' . preg_replace("/\"/", "\\\"", preg_replace("/\'/", "\\'", $msg));
		print "<html><head></head><body " . "onload=\"p=document.layers?parentLayer:window.parent;p.jsrsError('" . $this->contextId . "','" . $msg . "');\">" . $cleanMsg . "</body></html>";
		exit;
	}

	/**
	 * Executed when JSRS handler ID is invalid
	 *
	 * @access protected
	 */
	function onInvalidHandler() {
		$msg = PHP2Go::getLangVal('ERR_SERVICE_INVALID_HANDLER', array(SERVICE_TYPE_JSRS, $this->handlerId));
		$cleanMsg = 'jsrsError: ' . preg_replace("/\"/", "\\\"", preg_replace("/\'/", "\\'", $msg));
		print "<html><head></head><body " . "onload=\"p=document.layers?parentLayer:window.parent;p.jsrsError('" . $this->contextId . "','" . $msg . "');\">" . $cleanMsg . "</body></html>";
		exit;
	}

	/**
	 * Handles errors when error handling is enabled
	 *
	 * @param int $code Error code
	 * @param string $message Error message
	 * @param string $file File path
	 * @param int $line Line number
	 * @param array $vars Local scope variables
	 */
	function onError($code, $message, $file, $line, $vars) {
		if ($code != E_STRICT && error_reporting() != 0) {
			$error = new PHP2GoError();
			$error->setType($code);
			if (!$error->isIgnoreError($message) && $error->isUserError($code)) {
				$msg = $error->getTypeDesc() . ': ' . $message;
				$cleanMsg = 'jsrsError: ' . preg_replace("/\"/", "\\\"", preg_replace("/\'/", "\\'", $message));
				print "<html><head></head><body " . "onload=\"p=document.layers?parentLayer:window.parent;p.jsrsError('" . $this->contextId . "','" . $msg . "');\">" . $cleanMsg . "</body></html>";
				exit;
			}
		}
	}

	/**
	 * Escape some special chars inside a response
	 *
	 * @param string $response Response
	 * @access private
	 * @return string
	 */
	function _escapeResponse($response) {
		$tmp = ereg_replace("&", "&amp;", $response);
		return ereg_replace("\/" , "\\/", $tmp);
	}
}
?>