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

import('php2go.util.Callback');

/**
 * JSRS service type
 */
define('SERVICE_TYPE_JSRS', 'JSRS');
/**
 * AJAX service type
 */
define('SERVICE_TYPE_AJAX', 'AJAX');

/**
 * Base service class
 *
 * This is the base class for {@link JSRSService} and {@link AjaxService},
 * which are classes that handle calls performed from Javascript code.
 *
 * @package service
 * @uses Callback
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AbstractService extends PHP2Go
{
	/**
	 * Registered handlers
	 *
	 * @var array
	 * @access private
	 */
	var $handlers = array();
	/**
	 * Requested handler ID
	 *
	 * @var string
	 * @access private
	 */
	var $handlerId = NULL;
	/**
	 * Request parameters
	 *
	 * @var array
	 * @access private
	 */
	var $handlerParams = array();
	/**
	 * Fallback handler
	 *
	 * @var object Callback
	 * @access private
	 */
	var $fallbackHandler = NULL;
	/**
	 * Error handling flag
	 *
	 * @var bool
	 * @access private
	 */
	var $errorHandling = FALSE;

	/**
	 * Class constructor
	 *
	 * @return AbstractService
	 */
	function AbstractService() {
		parent::PHP2Go();
		if ($this->isA('AbstractService', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'AbstractService'), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Enables/disables error handling while calling service handlers
	 *
	 * @param bool $setting Flag value
	 */
	function setErrorHandling($setting) {
		$this->errorHandling = (bool)$setting;
	}

	/**
	 * Registers a new handler
	 *
	 * The handler can be a procedural function, a class/method or
	 * an object/method pair (the last two represented by an array).
	 *
	 * @param string|array $handler Handler spec
	 * @param string $alias Handler alias
	 */
	function registerHandler($handler, $alias=NULL) {
		$callback = new Callback();
		$callback->throwErrors = FALSE;
		$callback->setFunction($handler);
		if ($callback->isValid()) {
			if (empty($alias))
				$alias = ($callback->isType(CALLBACK_FUNCTION) ? $handler : $handler[1]);
			$this->handlers[$alias] = $callback;
		}
	}

	/**
	 * Register methods of an object as handlers
	 *
	 * A method prefix can be defined, so that only methods
	 * that respect this pattern will be recognized.
	 *
	 * Example:
	 * <code>
	 * class MyHandler
	 * {
	 *   function thisIsNotAHandler() { }
	 *   function serviceDoThis() { }
	 *   function serviceDoThat() { }
	 * }
	 * $handler = new MyHandler();
	 * $service->registerObject($handler, 'service');
	 * </code>
	 *
	 * @param object &$object Object
	 * @param string $methodPrefix Method prefix
	 */
	function registerObject(&$object, $methodPrefix='') {
		if (is_object($object)) {
			$methods = get_class_methods(get_class($object));
			foreach ($methods as $method) {
				// protect special methods
				if ($method == get_class($object) ||
					$method == '__construct' || $method == '__destruct' ||
					$method == '__call' || $method == '__set' || $method == '__get' ||
					$method == '__sleep' || $method == '__wakeup'
				) {
					continue;
				}
				// method is accepted?
				if (empty($methodPrefix) || preg_match("/^{$methodPrefix}([A-Z])/", $method)) {
					$func = array();
					$func[0] =& $object;
					$func[1] = $method;
					$callback = new Callback();
					$callback->setFunction($func);
					$alias = preg_replace("/^{$methodPrefix}([A-Z])/e", "strtolower('$1')", $method);
					$this->handlers[$alias] = $callback;
				}
			}
		}
	}

	/**
	 * Register a handler to be called when
	 * an invalid handler ID is requested
	 *
	 * @param string|array $handler Handler spec
	 * @param string $alias Handler alias
	 */
	function setFallbackHandler($handler, $alias=NULL) {
		$callback = new Callback();
		$callback->throwErrors = FALSE;
		$callback->setFunction($handler);
		if ($callback->isValid()) {
			if (empty($alias))
				$alias = ($callback->isType(CALLBACK_FUNCTION) ? $handler : $handler[1]);
			$this->fallbackHandler = $callback;
		}
	}

	/**
	 * Triggers request handling and processing
	 *
	 * Parses the request parameters. If the request is a valid request,
	 * this method tries to find a handler to handle the incoming request.
	 */
	function handleRequest() {
		if ($this->acceptRequest()) {
			// missing handler
			if (empty($this->handlerId)) {
				$this->onMissingHandler();
			}
			// valid handler
			elseif (array_key_exists($this->handlerId, $this->handlers)) {
				if ($this->errorHandling)
					set_error_handler(array($this, 'onError'));
				$callback =& $this->handlers[$this->handlerId];
				$response = $callback->invoke($this->handlerParams);
				if ($this->errorHandling)
					restore_error_handler();
				$this->sendResponse($response);
			}
			// fallback handler
			elseif ($this->fallbackHandler) {
				if ($this->errorHandling)
					set_error_handler(array($this, 'onError'));
				$response = $this->fallbackHandler->invoke(array($this->handlerId, $this->handlerParams), TRUE);
				if ($this->errorHandling)
					restore_error_handler();
				$this->sendResponse($response);
			}
			// invalid handler
			else {
				$this->onInvalidHandler();
			}
		}
 	}

 	/**
 	 * Must be implemented by child classes
 	 *
 	 * @access protected
 	 * @return bool
 	 */
 	function acceptRequest() {
		return FALSE;
	}

 	/**
 	 * Must be implemented by child classes
 	 *
 	 * @param mixed $response Response
 	 * @access protected
 	 * @return bool
 	 */
	function sendResponse($response) {
	}

	/**#@+
	 * Must be implemented by child classes
     *
     * @abstract
     */
	function onMissingHandler() {
	}
	function onInvalidHandler() {
	}
	function onError() {
	}
	/**#@-*/
}

?>