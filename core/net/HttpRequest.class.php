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

import('php2go.net.UserAgent');
import('php2go.session.SessionManager');

/**
 * Collection of methods to handle with the incoming HTTP request
 *
 * Collects information about request parameters, client browser, context
 * variables, environment variables, system variables, ...
 *
 * @package net
 * @uses Environment
 * @uses Registry
 * @uses SessionManager
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class HttpRequest extends PHP2Go
{
	/**
	 * Get all request parameters
	 *
	 * Returns a reference to the superglobal $_REQUEST.
	 *
	 * @return array
	 * @static
	 */
	function &request() {
		$request =& $_REQUEST;
		return $request;
	}

	/**
	 * Read a GET parameter
	 *
	 * If $paramName is missing, the full superglobal
	 * $_GET is returned.
	 *
	 * @uses HttpRequest::fetchVar()
	 * @param string $paramName Parameter name
	 * @return mixed
	 * @static
	 */
	function get($paramName='') {
		$paramName = trim($paramName);
		if ($paramName != '')
			return HttpRequest::fetchVar($paramName, 'GET');
		return (array)$_GET;
	}

	/**
	 * Read a POST parameter
	 *
	 * If $paramName is missing, the full superglobal
	 * $_POST is returned.
	 *
	 * @uses HttpRequest::fetchVar()
	 * @param string $paramName Parameter name
	 * @return mixed
	 * @static
	 */
	function post($paramName='') {
		$paramName = trim($paramName);
		if ($paramName != '')
			return HttpRequest::fetchVar($paramName, 'POST');
		return (array)$_POST;
	}

	/**
	 * Get raw post contents
	 *
	 * @return string
	 * @static
	 */
	function rawPost() {
		global $HTTP_RAW_POST_DATA;
		if (isset($HTTP_RAW_POST_DATA))
			return $HTTP_RAW_POST_DATA;
		return file_get_contents('php://input');
	}

	/**
	 * Read a cookie value
	 *
	 * If $paramName is missing, the full superglobal
	 * $_COOKIE is returned.
	 *
	 * @uses HttpRequest::fetchVar()
	 * @param string $paramName Cookie name
	 * @return mixed
	 * @static
	 */
	function cookie($paramName='') {
		$paramName = trim($paramName);
		if ($paramName != '')
			return HttpRequest::fetchVar($paramName, 'COOKIE');
		return (array)$_COOKIE;
	}

	/**
	 * Read a session variable
	 *
	 * If $varName is missing, the full superglobal
	 * $_SESSION is returned.
	 *
	 * @uses HttpRequest::fetchVar()
	 * @param string $varName Variable name
	 * @return mixed
	 * @static
	 */
	function session($varName='') {
		$varName = trim($varName);
		if ($varName != '')
			return HttpRequest::fetchVar($varName, 'SESSION');
		return (array)$_SESSION;
	}

	/**
	 * Get a request parameter by name
	 *
	 * The $where argument can be used to specify what should
	 * be the base where to seek for the parameter.
	 *
	 * If $where is missing, the parameter will be searched in
	 * all possible sources: 'get', 'post', 'cookie', 'session',
	 * 'env', 'object' and 'reg'.
	 *
	 * The 'object' source means the objects persisted in the
	 * session scope. The 'reg' source means the singleton of
	 * the {@link Registry} class, which is initialized with
	 * the value of $GLOBALS.
	 *
	 * The $searchOrder argument can be used to define a search
	 * order, when $where == 'all'.
	 *
	 * @uses HttpRequest::fetchVar()
	 * @uses SessionManager::getObjectProperty()
	 * @uses Registry::get()
	 * @param string $variableName Parameter name
	 * @param string $where Search source
	 * @param string $searchOrder Search order, when $where == 'all'
	 * @return mixed
	 * @static
	 */
	function getVar($variableName, $where='all', $searchOrder='EGPCSOR') {
		$return = NULL;
		if (strtoupper($where) == 'ALL') {
            for ($i=0; $i<strlen($searchOrder); ++$i) {
                switch ($searchOrder{$i}) {
					case 'E' :
						$value = Environment::get($variableName);
						if ($value !== NULL)
							return $value;
						break;
					case 'G' :
						$value = HttpRequest::fetchVar($variableName, 'GET');
						if ($value !== NULL)
							return $value;
						break;
					case 'P' :
						$value = HttpRequest::fetchVar($variableName, 'POST');
						if ($value !== NULL)
							return $value;
						break;
					case 'C' :
						$value = HttpRequest::fetchVar($variableName, 'COOKIE');
						if ($value !== NULL)
							return $value;
						break;
					case 'S' :
						$value = HttpRequest::fetchVar($variableName, 'SESSION');
						if ($value !== NULL)
							return $value;
						break;
					case 'O' :
						$value = SessionManager::getObjectProperty($variableName);
						if ($value !== NULL)
							return $value;
						break;
					case 'R' :
						$value = Registry::get($variableName);
						if ($value !== NULL)
							return $value;
						break;
				}
            }
		} else {
			$where = strtoupper($where);
			if ($where == 'REG')
				$return = Registry::get($variableName);
			elseif ($where == 'OBJECT')
				$return = SessionManager::getObjectProperty($variableName);
			else
				$return = HttpRequest::fetchVar($variableName, $where);
		}
		return $return;
	}

	/**
	 * Internal method used to fetch parameters
	 * from one of the super globals: $_GET, $_POST,
	 * $_COOKIE and $_SESSION
	 *
	 * The $variableName arguments accepts names of
	 * entries of arrays with 2 or 3 dimensions:
	 * <code>
	 * $var = HttpRequest::fetchVar('array[key]', 'POST');
	 * $var2 = HttpRequest::fetchVar('data_grid[2][name]', 'POST');
	 * </code>
	 *
	 * @param string $variableName Parameter name
	 * @param string $where Source
	 * @return mixed Parameter value
	 * @static
	 */
	function fetchVar($variableName, $where) {
		$arrayContent = array();
		eval("\$arrayContent =& \$_$where;");
		$arrayContent = (array)$arrayContent;
		if (preg_match("/([^\[]+)\[([^\]]+)\](\[([^\]]+)\])?/", $variableName, $matches)) {
			if (isset($arrayContent[$matches[1]]) && is_array($arrayContent[$matches[1]])) {
				$value = @$arrayContent[$matches[1]][$matches[2]];
				if (isset($matches[3]) && is_array($value))
					$value = @$value[$matches[4]];
				return $value;
			}
		} elseif (array_key_exists($variableName, $arrayContent)) {
			return $arrayContent[$variableName];
		}
		return NULL;
	}

	/**
	 * Get request headers
	 *
	 * @return array
	 * @static
	 */
	function getHeaders() {
		return apache_request_headers();
	}

	/**
	 * Get request method
	 *
	 * @return string
	 * @static
	 */
	function method() {
		return Environment::get('REQUEST_METHOD');
	}

	/**
	 * Check if the method of the request is GET
	 *
	 * @return bool
	 * @static
	 */
	function isGet() {
		$method = Environment::get('REQUEST_METHOD');
		return ($method == 'GET');
	}

	/**
	 * Check if the method of the request is POST
	 *
	 * @return bool
	 * @static
	 */
	function isPost() {
		$method = Environment::get('REQUEST_METHOD');
		return ($method == 'POST');
	}

	/**
	 * Check whether this is an AJAX request
	 *
	 * @return bool
	 * @static
	 */
	function isAjax() {
		$headers = HttpRequest::getHeaders();
		return (array_key_exists('X-Requested-With', $headers));
	}

	/**
	 * Get request protocol
	 *
	 * @return string
	 * @static
	 */
	function protocol() {
		if (HttpRequest::isSecure())
			return 'https';
		else
			return 'http';
	}

	/**
	 * Check if the request uses HTTPS
	 *
	 * @return bool
	 * @static
	 */
	function isSecure() {
		return (strtolower(Environment::get('HTTPS')) == 'on' || Environment::has('SSL_PROTOCOL_VERSION'));
	}

	/**
	 * Get the server's hostname
	 *
	 * Returns hostname and port (when different from default ports)
	 *
	 * @return string
	 * @static
	 */
	function serverHostName() {
		$port = Environment::has('SERVER_PORT') ? Environment::get('SERVER_PORT') : '80';
		$protocol = HttpRequest::protocol();
		if (($protocol == 'http' && $port != '80') || ($protocol == 'https' && $port != '443'))
			$port = ":{$port}";
		else
			$port = '';
		return Environment::get('HTTP_HOST') . $port;
	}

	/**
	 * Get the server's name
	 *
	 * @return string
	 * @static
	 */
	function serverName() {
		return Environment::get('SERVER_NAME');
	}

	/**
	 * Get the full path of the current script
	 *
	 * @return string
	 * @static
	 */
	function scriptName() {
		return Environment::get('SCRIPT_NAME');
	}

	/**
	 * Get information about the running script
	 *
	 * Returns a hash array with the following keys:
	 * # path : full path
	 * # base : script's base directory
	 * # file : script's filename
	 *
	 * @return array
	 * @static
	 */
	function scriptInfo() {
		$scriptName = HttpRequest::scriptName();
		$scriptFile = basename($scriptName);
		$scriptBase = substr($scriptName, 0, strlen($scriptName) - strlen($scriptFile));
		return array(
			'path' => $scriptName,
			'base' => $scriptBase,
			'file' => $scriptFile
		);
	}

	/**
	 * Return the base path of the current script
	 *
	 * @return string
	 * @static
	 */
	function basePath() {
		return Environment::get('PHP_SELF');
	}

	/**
	 * Get the request's query string
	 *
	 * @return string
	 * @static
	 */
	function queryString() {
		return Environment::get('QUERY_STRING');
	}

	/**
	 * Get the requested URL
	 *
	 * Returns protocol, port (when different from the default ports),
	 * server name and script's base path.
	 *
	 * @return string
	 * @static
	 */
	function url() {
		$protocol = HttpRequest::protocol();
		$port = Environment::get('SERVER_PORT');
		if (($protocol == 'http' && $port != '80') || ($protocol == 'https' && $port != '443'))
			$base = "{$protocol}://" . HttpRequest::serverName() . ":{$port}";
		else
			$base = "{$protocol}://" . HttpRequest::serverName();
		return $base . HttpRequest::basePath();
	}

	/**
	 * Get the requested URI
	 *
	 * When $full is set to TRUE, returns protocol, port (when different
	 * from the default ports), server name, script path and query string.
	 * Otherwise, returns script path and query string only.
	 *
	 * @param bool $full Whether to return full information
	 * @return string
	 * @static
	 */
	function uri($full=TRUE) {
		if ($full) {
			$protocol = HttpRequest::protocol();
			$port = Environment::get('SERVER_PORT');
			if (($protocol == 'http' && $port != '80') || ($protocol == 'https' && $port != '443'))
				$base = "{$protocol}://" . HttpRequest::serverName() . ":{$port}";
			else
				$base = "{$protocol}://" . HttpRequest::serverName();
		} else {
			$base = '';
		}
		if (!$requestUri = Environment::get('REQUEST_URI')) {
			if ($queryString = Environment::get('QUERY_STRING'))
				return $base . HttpRequest::scriptName() . '?' . $queryString;
			else
				return NULL;
		}
		$requestUri = preg_replace('#[?|&]' . session_name() . '=[^&|?]*#', '', $requestUri);
		return $base . $requestUri;
	}

	/**
	 * Get the request's referer URI
	 *
	 * @return string
	 * @static
	 */
	function referer() {
		return Environment::get('HTTP_REFERER');
	}

	/**
	 * Get client's user agent
	 *
	 * @return string
	 * @static
	 */
	function userAgent() {
		return Environment::get('HTTP_USER_AGENT');
	}

	/**
	 * Get the client's IP address
	 *
	 * @return string
	 * @static
	 */
	function remoteAddress() {
		if (Environment::has('X_FORWARDED_FOR'))
			return array_pop(explode(',', Environment::get('X_FORWARDED_FOR')));
		return Environment::get('REMOTE_ADDR');
	}

	/**
	 * Get the client's hostname
	 *
	 * When the client's hostname can't be resolved, the
	 * IP address is returned.
	 *
	 * @uses gethostbyaddr()
	 * @return string
	 * @static
	 */
	function remoteHost() {
		return @gethostbyaddr(HttpRequest::remoteAddress());
	}
}
?>