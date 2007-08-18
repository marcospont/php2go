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

import('php2go.net.HttpRequest');
import('php2go.service.AbstractService');
import('php2go.service.ajax.AjaxResponse');

/**
 * Handles AJAX remote function/method calls
 *
 * This class works together with the AjaxService Javascript
 * class in order to make possible a PHP function or method
 * call from the Javascript code. The function or method ID
 * is transferred through a special header - X-Handler-ID.
 * All AJAX parameters are passed to the called function or method
 * as an associative array.
 *
 * @package service
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AjaxService extends AbstractService
{
	/**
	 * Detects ajax calls performed using an iframe
	 *
	 * @var bool
	 * @access private
	 */
	var $isIframe = FALSE;
	
	/**
	 * Class constructor
	 *
	 * @return AjaxService
	 */
	function AjaxService() {
		parent::AbstractService();
	}

	/**
	 * Get the singleton of the AjaxService class
	 *
	 * @return AjaxService
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new AjaxService();
		return $instance;
	}

	/**
	 * Searches for a valid handler ID in the request
	 *
	 * @access protected
	 * @return bool
	 */
	function acceptRequest() {
		$this->isIframe = FALSE;
		$method = strtolower(HttpRequest::method());
		$headers = array_change_key_case(HttpRequest::getHeaders(), CASE_LOWER);
		// normal ajax call
		if (array_key_exists('x-requested-with', $headers)) {
			$this->handlerId = @$headers['x-handler-id'];
			$this->handlerParams = $this->_decodeParams(($method == 'post' ? $_POST : $_GET));
			return TRUE;
		// file upload ajax call
		} elseif (is_array($_FILES)) {			
			$params = array_change_key_case($_POST, CASE_LOWER);
			$ajax = consumeArray($params, 'x-requested-with');
			if ($ajax) {
				$this->isIframe = TRUE;
				$this->handlerId = consumeArray($params, 'x-handler-id');
				$this->handlerParams = $this->_decodeParams($params);
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Sends the response back
	 *
	 * @param mixed $response Response
	 * @access protected
	 */
	function sendResponse($response) {
		if (TypeUtils::isInstanceOf($response, 'AjaxResponse')) {
			$response->render($this->isIframe);
		} else {
			$response = (string)$response;
			print $response;
		}
		exit;
	}

	/**
	 * Called when the handler ID is missing in the request
	 *
	 * Sends back an error HTTP status code and an error
	 * message inside the response body.
	 *
	 * @access protected
	 */
	function onMissingHandler() {
		header('HTTP/1.0 500', TRUE, 500);
		print PHP2Go::getLangVal('ERR_SERVICE_MISSING_HANDLER', SERVICE_TYPE_AJAX);
		exit;
	}

	/**
	 * Called when the requested handler is invalid
	 *
	 * Sends back an error HTTP status code and an error
	 * message inside the response body.
	 *
	 * @access protected
	 */
	function onInvalidHandler() {
		header('HTTP/1.0 500', TRUE, 500);
		print PHP2Go::getLangVal('ERR_SERVICE_INVALID_HANDLER', array(SERVICE_TYPE_AJAX, $this->handlerId));
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
				header('HTTP/1.0 500', TRUE, 500);
				print $error->getTypeDesc() . ': ' . $message;
				exit;
			}
		}
	}

	/**
	 * Process all request parameters, decoding
	 * all entries from UTF-8
	 *
	 * @param array $source Parameters
	 * @return array Decoded parameters
	 * @access private
	 */
	function _decodeParams($source) {
		foreach ($source as $key => $value) {
			if (is_array($value))
				$source[$key] = $this->_decodeParams($value);
			else
				$source[$key] = utf8_decode($value);
		}
		return $source;
	}
}
?>