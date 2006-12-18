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

import('php2go.net.HttpRequest');
import('php2go.util.HtmlUtils');

/**
 * Builds and manages URLs (Uniform Reference Locator)
 *
 * @package net
 * @uses HtmlUtils
 * @uses HttpRequest
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Url extends PHP2Go
{
	/**
	 * Protocol
	 *
	 * @var string
	 */
	var $protocol;

	/**
	 * Authentication username
	 *
	 * @var string
	 */
	var $user;

	/**
	 * Authentication password
	 *
	 * @var string
	 */
	var $pass;

	/**
	 * Host
	 *
	 * @var string
	 */
	var $host;

	/**
	 * Port
	 *
	 * @var int
	 */
	var $port;

	/**
	 * Path
	 *
	 * @var string
	 */
	var $path;

	/**
	 * File name
	 *
	 * @var string
	 */
	var $file;

	/**
	 * Parameters (query string)
	 *
	 * @var string
	 */
	var $parameters;

	/**
	 * Fragment
	 *
	 * @var string
	 */
	var $fragment;

	/**
	 * Class constructor
	 *
	 * @param string $url URL string
	 * @return Url
	 */
	function Url($url='') {
		parent::PHP2Go();
		if ($url != '') {
			$this->set($url);
		}
	}

	/**
	 * Populates the URL object from a given URL string
	 *
	 * @param string $url URL
	 */
	function set($url) {
		$this->_parse($url);
	}

	/**
	 * Populates the object using the current requested URI
	 */
	function setFromCurrent() {
		$this->set(HttpRequest::uri());
	}

	/**
	 * Get URL protocol
	 *
	 * @return string
	 */
	function getProtocol() {
		return (isset($this->protocol) && !empty($this->protocol) ? $this->protocol : NULL);
	}

	/**
	 * Get URL scheme
	 *
	 * @return string
	 */
	function getScheme() {
		$protocol = $this->getProtocol();
		if ($protocol !== NULL)
			return strtolower($protocol) . '://';
		else
			return NULL;
	}

	/**
	 * Get authentication data
	 *
	 * Returns username:password, if both are present. Returns
	 * username only, if password is not set. Returns NULL when
	 * both username and password are not set.
	 *
	 * @return string
	 */
	function getAuth() {
		return (isset($this->user) ? (isset($this->pass) ? "{$this->user}:{$this->pass}" : $this->user) : NULL);
	}

	/**
	 * Get authentication username
	 *
	 * @return string
	 */
	function getUser() {
		return (isset($this->user) && !empty($this->user) ? $this->user : NULL);
	}

	/**
	 * Get authentication password
	 *
	 * @return string
	 */
	function getPass() {
		return (isset($this->pass) && !empty($this->pass) ? $this->pass : NULL);
	}

	/**
	 * Get URL host
	 *
	 * When the host is an IP address, the method tries to resolve the
	 * hostname using {@link gethostbyaddr()}.
	 *
	 * @return string
	 */
	function getHost() {
		if (!isset($this->host) || empty($this->host))
			return NULL;
		$matches = array();
		if (preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/", $this->host, $matches)) {
			return gethostbyaddr($this->host);
		} else {
			return strtolower($this->host);
		}
	}

	/**
	 * Get URL port
	 *
	 * @return int
	 */
	function getPort() {
		return (isset($this->port) && !empty($this->port) ? $this->port : NULL);
	}

	/**
	 * Get URL path
	 *
	 * @return string
	 */
	function getPath() {
		return (isset($this->path) && !empty($this->path) ? $this->path : NULL);
	}

	/**
	 * Get URL file name
	 *
	 * @return string
	 */
	function getFile() {
		return (isset($this->file) && !empty($this->file) ? $this->file : NULL);
	}

	/**
	 * Get URL query string
	 *
	 * @param bool $prefix Add ? prefix if the query string is not empty
	 * @return string
	 */
	function getQueryString($prefix=FALSE) {
		return (isset($this->parameters) && !empty($this->parameters) ? ($prefix ? '?' . $this->parameters : $this->parameters) : NULL);
	}

	/**
	 * Get an array containing the query string parameters
	 *
	 * @return array
	 */
	function getQueryStringArray() {
		$queryString = $this->getQueryString();
		if ($queryString !== NULL) {
			parse_str($queryString, $result);
			return $result;
		}
		return NULL;
	}

	/**
	 * Adds a parameter in the URL
	 *
	 * @param string $name Name
	 * @param mixed $value Value
	 */
	function addParameter($name, $value) {
		$query = $this->getQueryString();
		if ($query !== NULL) {
			$result = '';
			parse_str($query, $params);
			$params[$name] = $value;
			foreach ($params as $name => $value)
				$result .= ($result == '' ? "$name=$value" : "&$name=$value");
			$this->parameters = $result;
		} else {
			$this->parameters = "$name=$value";
		}
	}

	/**
	 * Removes a parameter from the URL query string
	 *
	 * @param string $name Name
	 */
	function removeParameter($name) {
		$query = $this->getQueryStringArray();
		if ($query !== NULL) {
			unset($query[$name]);
			$tmp = array();
			foreach ($query as $k => $v)
				$tmp[] = "$k=$v";
			$this->parameters = implode("&", $tmp);
		}
	}

	/**
	 * Get URL fragment
	 *
	 * @return string
	 */
	function getFragment() {
		return (isset($this->fragment) && !empty($this->fragment) ? $this->fragment : NULL);
	}

	/**
	 * Get the full URL string
	 *
	 * @return string
	 */
	function getUrl() {
		return sprintf("%s%s%s%s%s%s%s",
			(isset($this->protocol) && !empty($this->protocol) ? "{$this->protocol}://" : ''),
			(isset($this->user) ? (isset($this->pass) ? "{$this->user}:{$this->pass}@" : "{$this->user}@") : ''),
			strtolower($this->host),
			(isset($this->port) && !empty($this->port) ? ":{$this->port}" : ''),
			$this->path,
			(isset($this->parameters) && !empty($this->parameters) ? "?{$this->parameters}" : ''),
			(isset($this->fragment) && !empty($this->fragment) ? "#{$this->fragment}" : '')
		);
	}

	/**
	 * Builds an anchor based on the current URL
	 *
	 * @param string $caption Link caption
	 * @param string $statusBarText Status bar text
	 * @param string $cssClass CSS class
	 * @return string
	 */
	function getAnchor($caption, $statusBarText='', $cssClass='') {
		return HtmlUtils::anchor($this->getUrl(), $caption, $statusBarText, $cssClass);
	}

	/**
	 * Reset all URL properties
	 */
	function reset() {
		unset($this->protocol);
		unset($this->user);
		unset($this->pass);
		unset($this->host);
		unset($this->port);
		unset($this->path);
		unset($this->file);
		unset($this->parameters);
		unset($this->fragment);
	}

	/**
	 * Encodes the URL parameters (query string) and return
	 * the full encoded URL
	 *
	 * @param string $url Input URL. Defaults to the URL being modified by the class
	 * @param string $varName Variable that should hold the hash of encoded variables
	 * @return string Encoded URL
	 */
	function encode($url=NULL, $varName='p2gvar') {
		if (empty($url))
			$url = $this->getUrl();
		$matches = array();
		if (preg_match("/([^?#]+\??)?([^#]+)?(.*)/", $url, $matches)) {
			if ($matches[2] !== FALSE) {
				$paramString = base64_encode(urlencode($matches[2]));
				$returnUrl = strval($matches[1]) . $varName . '=' . $paramString . strval($matches[3]);
			} else {
				$returnUrl = $url;
			}
		}
		return $returnUrl;
	}

	/**
	 * Decodes an URL encoded with {@link encode}
	 *
	 * @param string $url Input URL. Defaults to the URL being modified by the class
	 * @param bool $returnAsArray Whether to return just an array of decoded arguments or the full URL string
	 * @return string|array
	 */
	function decode($url=NULL, $returnAsArray=FALSE) {
		if (empty($url))
			$url = $this->getUrl();
		$matches = array();
		preg_match("/([^?#]+\??)?([^#]+)?(.*)/", $url, $matches);
		if ($matches[2] !== FALSE) {
			parse_str($matches[2], $vars);
			if (list(, $value) = each($vars)) {
				$paramString = urldecode(base64_decode($value));
				if ($returnAsArray) {
					parse_str($paramString, $varsArray);
					return $varsArray;
				} else {
					return strval($matches[1]) . $paramString . strval($matches[3]);
				}
			}
		}
		return FALSE;
	}

	/**
	 * Parse URL elements from a given URL string, populating
	 * the object's properties
	 *
	 * @param string $url URL string
	 * @access private
	 */
	function _parse($url) {
        if (preg_match('!^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?!', $url, $matches)) {
			if (isset($matches[1]))
				$this->protocol = $matches[2];
			if (isset($matches[3]) && isset($matches[4])) {
				$atPos = strpos($matches[4], '@');
				if ($atPos !== FALSE) {
					$auth = substr($matches[4], 0, $atPos);
					$dotPos = strpos($auth, ':');
					if ($dotPos !== FALSE) {
						$authParts = explode(':', $auth);
						$this->user = $authParts[0];
						$this->pass = $authParts[1];
					} else {
						$this->user = $auth;
					}
					$matches[4] = substr($matches[4], $atPos+1);
				}
				$portPos = strrpos($matches[4], ':');
				if ($portPos !== FALSE) {
					$this->port = TypeUtils::parseIntegerPositive(substr($matches[4], $portPos+1));
					if (!$this->port) {
						$this->port = NULL;
					}
				}
				$this->host = $portPos ? substr($matches[4], 0, $portPos) : $matches[4];
			}
			if (isset($matches[5])) {
				$this->path = $matches[5];
				$slashPos = strrpos(substr($this->path, 1), '/');
				if ($slashPos !== FALSE) {
					$this->file = substr($this->path, $slashPos + 2);
				}
			}
			$this->path = $matches[5] ? $matches[5] : '';
            if (isset($matches[6]) && $matches[6] != '')
				$this->parameters = $matches[7];
            if (isset($matches[8]) && $matches[8] != '')
				$this->fragment = $matches[9];
        }
	}
}
?>