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

import('php2go.util.Callback');

/**
 * Handle JSRS requests
 *
 * JSRS is an acronym for Javascript Remote Scripting, a library used to
 * perform calls to PHP functions from inside a Javascript code block.
 * Internally, the request is performed through a hidden IFRAME element.
 *
 * This class is used to handle these requests, parsing and calling the
 * requested function, printing its results.
 *
 * @package util
 * @subpackage service
 * @uses Callback
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ServiceJSRS extends PHP2Go
{
	/**
	 * Registered handlers
	 *
	 * @var array
	 * @access private
	 */
	var $handlers = array();

	/**
	 * Data parsed from the request
	 *
	 * @var array
	 * @access private
	 */
	var $request = array();

	/**
	 * Class constructor
	 *
	 * @return ServiceJSRS
	 */
	function ServiceJSRS() {
		parent::PHP2Go();
	}

	/**
	 * Get the singleton of the ServiceJSRS class
	 *
	 * @return ServiceJSRS
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new ServiceJSRS();
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
	 * Registers a new JSRS handler
	 *
	 * The handler can be a procedural function, a class/method or
	 * an object/method pair (the last two represented by an array).
	 *
	 * @param string|array $handler Handler spec
	 * @param string $alias Handler alias
	 */
	function registerHandler($handler, $alias=NULL) {
		$Callback = new Callback();
		$Callback->throwErrors = FALSE;
		$Callback->setFunction($handler);
		$valid = $Callback->isValid();
		if ($valid) {
			if (empty($alias))
				$alias = ($Callback->isType(CALLBACK_FUNCTION) ? $handler : $handler[1]);
			$this->handlers[$alias] = $Callback;
		}
	}

	/**
	 * Triggers the handling of the request
	 *
	 * Parses the request parameters. If the request is a valid JSRS request,
	 * this method tries to find a handler to the the requested function.
	 *
	 * @uses _parseRequest()
	 * @uses _buildResponse()
	 * @uses _buildErrorResponse()
	 */
	function handleRequest() {
		if ($this->_parseRequest()) {
			if (empty($this->request['handlerId'])) {
				$this->_buildErrorResponse(PHP2Go::getLangVal('ERR_JSRS_MISSING_HANDLER'));
			} elseif (array_key_exists($this->request['handlerId'], $this->handlers)) {
				$handler =& $this->handlers[$this->request['handlerId']];
				$result = $handler->invoke($this->request['handlerArgs'], TRUE);
				if (!empty($result))
					$this->_buildResponse((string)$result);
				else
					$this->_buildResponse('');
			} else {
				$this->_buildErrorResponse(PHP2Go::getLangVal('ERR_JSRS_INVALID_HANDLER', $this->request['handlerId']));
			}
		}
	}

	/**
	 * Parses context ID, handler ID and handler arguments from the request
	 *
	 * @access private
	 * @return bool
	 */
	function _parseRequest() {
		$context = (isset($_REQUEST['C']) ? $_REQUEST['C'] : '');
		$handler = (isset($_REQUEST['F']) ? $_REQUEST['F'] : '');
		$args = array();
		$i = 0;
		while (isset($_REQUEST['P'.$i])) {
			$argument = $_REQUEST['P'.$i];
			$args[] = substr($argument, 1, strlen($argument)-2);
			$i++;
		}
		if (!empty($context)) {
			$this->request = array(
				'contextId' => $context,
				'handlerId' => $handler,
				'handlerArgs' => $args
			);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Builds the HTML code of a JSRS response
	 *
	 * @param string $payload Payload produced by the PHP function
	 * @access private
	 */
	function _buildResponse($payload) {
		print ("<html><head></head><body onload=\"p=document.layers?parentLayer:window.parent;p.jsrsLoaded('" . $this->request['contextId'] . "');\">jsrsPayload:<br>" . "<form name=\"jsrs_Form\"><textarea name=\"jsrs_Payload\">" . $this->_escapeResponse($payload) . "</textarea></form></body></html>");
		exit();
	}

	/**
	 * Builds an error response
	 *
	 * @param string $str Error message
	 * @access private
	 */
	function _buildErrorResponse($str) {
		$cleanStr = ereg_replace("\'", "\\'", $str);
		$cleanStr = "jsrsError: " . ereg_replace("\"", "\\\"", $cleanStr);
		print ("<html><head></head><body " . "onload=\"p=document.layers?parentLayer:window.parent;p.jsrsError('" . $this->request['contextId'] . "','" . $str . "');\">" . $cleanStr . "</body></html>");
		exit();
	}

	/**
	 * Escape some special chars inside a response payload
	 *
	 * @param string $payload Response payload
	 * @access private
	 * @return string
	 */
	function _escapeResponse($payload) {
		$tmp = ereg_replace("&", "&amp;", $payload);
		return ereg_replace("\/" , "\\/", $tmp);
	}
}
?>