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

/**
 * Default expiry time, in seconds
 */
define('COOKIE_DEFAULT_EXPIRY_TIME', 86400);

/**
 * Abstraction of an HTTP cookie
 *
 * @package net
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class HttpCookie extends PHP2Go
{
	/**
	 * Cookie name
	 *
	 * @var string
	 * @access private
	 */
	var $name;

	/**
	 * Cookie value
	 *
	 * @var mixed
	 * @access private
	 */
	var $value;

	/**
	 * Cookie domain
	 *
	 * @var string
	 * @access private
	 */
	var $domain;

	/**
	 * Cookie path
	 *
	 * @var string
	 * @access private
	 */
	var $path = '/';

	/**
	 * Expiry timestamp
	 *
	 * @var int
	 * @access private
	 */
	var $expires;

	/**
	 * Secure flag
	 *
	 * @var bool
	 * @access private
	 */
	var $secure = FALSE;

	/**
	 * Class constructor
	 *
	 * @return HttpCookie
	 */
	function HttpCookie() {
		parent::PHP2Go();
	}

	/**
	 * Set all cookie properties
	 *
	 * @param string $name Name
	 * @param mixed $value Value
	 * @param string $domain Domain
	 * @param string $path Path
	 * @param int $expires Expiry time, in seconds
	 * @param bool $secure Secure flag
	 */
	function set($name, $value, $domain=NULL, $path='/', $expires=COOKIE_DEFAULT_EXPIRY_TIME, $secure=FALSE) {
		$this->setName($name);
		$this->setValue($value);
		if ($domain)
			$this->setDomain($domain);
		if ($path)
			$this->setPath($path);
		$this->setExpiryTime($expires);
		$this->secure = $secure;
	}

	/**
	 * Parses cookie properties from an HTTP header
	 *
	 * @param string $cookieString Header value
	 * @param string $host Cookie host
	 * @return bool
	 */
	function parseFromHeader($cookieString, $host) {
		$matches = array();
		eregi("^([^=]+)[ ]?=[ ]?([^;]+)(;[ ]?domain=([^;]+))?(;[ ]?expires=([^;]+))?(;[ ]?path=([^;]+))?(;[ ]?secure)?;?", $cookieString, $matches);
		if ($matches[1] && $matches[2]) {
			$name = $matches[1];
			$value = rtrim($matches[2]);
			if ($matches[4])
				$domain = rtrim($matches[4]);
			else
				$domain = $host;
			if ($matches[6])
				$expires = rtrim($matches[6]);
			else
				$expires = '';
			if ($matches[8])
				$path = rtrim($matches[8]);
			else
				$path = '/';
			$secure = (bool)$matches[9];
			$this->set($name, $value, $domain, $path, $expires, $secure);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get cookie's name
	 *
	 * @return string
	 */
	function getName() {
		return isset($this->name) ? $this->name : NULL;
	}

	/**
	 * Set cookie's name
	 *
	 * @param string $name New name
	 */
	function setName($name) {
		$this->name = $name;
	}

	/**
	 * Get cookie's value
	 *
	 * @return mixed
	 */
	function getValue() {
		return isset($this->value) ? $this->value : NULL;
	}

	/**
	 * Set cookie's value
	 *
	 * @param mixed $value New value
	 */
	function setValue($value) {
		$this->value = $value;
	}

	/**
	 * Get cookie's domain
	 *
	 * @return string
	 */
	function getDomain() {
		return isset($this->domain) ? $this->domain : NULL;
	}

	/**
	 * Check if the cookie's domain matches the passed $domain
	 *
	 * @param string $domain Input domain
	 * @return bool
	 */
	function isDomain($domain) {
		return (isset($this->domain) && preg_match("'.*" . preg_quote($this->domain) . "$'i", $domain));
	}

	/**
	 * Set the domain for which the cookie is valid
	 *
	 * @param string $domain New domain
	 */
	function setDomain($domain) {
		if (!empty($domain))
			$this->domain = $domain;
	}

	/**
	 * Get the cookie's path on the origin server
	 *
	 * @return string
	 */
	function getPath() {
		return isset($this->path) ? $this->path : NULL;
	}

	/**
	 * Check if the cookie's path matches the passed $path
	 *
	 * @param string $path Input path
	 * @return bool
	 */
	function isPath($path) {
		return (isset($this->path) && preg_match("'^" . preg_quote($this->path) . ".*'i", $path));
	}

	/**
	 * Set the path (subset of URLs) on the origin server
	 * to which the cookie applies
	 *
	 * @param string $path New path
	 */
	function setPath($path) {
		if (!empty($path))
			$this->path = $path;
	}

	/**
	 * Get cokie's expiry time
	 *
	 * The value returned is a UNIX timestamp.
	 *
	 * @return int
	 */
	function getExpiryTime() {
		return isset($this->expires) ? $this->expires : NULL;
	}

	/**
	 * Check if the cookie is expired
	 *
	 * @return bool
	 */
	function isExpired() {
		$now = time();
		return (isset($this->expires) && $this->expires <= $now);
	}

	/**
	 * Set the cookie's expiry time
	 *
	 * The $expires argument should be an offset (positive
	 * or negative) to be added to the current UNIX timestamp.
	 *
	 * @param int $expires Expiry offset
	 */
	function setExpiryTime($expires=NULL) {
		$now = time();
		if (trim($expires) != '') {
			if (TypeUtils::isInteger($expires))
				$this->expires = $now + $expires;
			else
				$this->expires = $now + COOKIE_DEFAULT_EXPIRY_TIME;
		} else
			$this->expires = $now + COOKIE_DEFAULT_EXPIRY_TIME;
	}

	/**
	 * Check if the cookie is secure
	 *
	 * @return bool
	 */
	function isSecure() {
		return isset($this->secure) ? $this->secure : NULL;
	}

	/**
	 * Set/unset secure flag
	 *
	 * @param bool $setting Secure flag
	 */
	function setSecure($setting) {
		$this->secure = (bool)$setting;
	}
}
?>