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

/**
 * Provides information about the user agent
 *
 * Provides information about the user's browser, operating system, accepted
 * languages, accepted MIME types, accepted encodings and browser features.
 *
 * @package net
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class UserAgent extends PHP2Go
{
	/**
	 * Full user agent
	 *
	 * @var string
	 */
	var $userAgent;

	/**
	 * User agent's identifier
	 *
	 * @var string
	 */
	var $identifier;

	/**
	 * Full browser version
	 *
	 * @var string
	 */
	var $fullVersion;

	/**
	 * Browser's major version
	 *
	 * @var int
	 */
	var $majorVersion;

	/**
	 * Brower's minor version
	 *
	 * @var int
	 */
	var $minorVersion;

	/**
	 * Extra information about brower's version
	 *
	 * @var mixed
	 */
	var $verlet;

	/**
	 * Browser's flags
	 *
	 * @var array
	 */
	var $browser;

	/**
	 * Browser's name
	 *
	 * @var string
	 */
	var $browserName;

	/**
	 * Browser's full name
	 *
	 * @var string
	 */
	var $browserFullName;

	/**
	 * User's operating system
	 *
	 * @var string
	 */
	var $os;

	/**
	 * User's operating system name
	 *
	 * @var string
	 */
	var $osName;

	/**
	 * User's operating system full name
	 *
	 * @var string
	 */
	var $osFullName;

	/**
	 * Set of browser features
	 *
	 * @var array
	 */
	var $features;

	/**
	 * Accepted MIME types
	 *
	 * @var array
	 */
	var $mimeTypes;

	/**
	 * Accepted language codes
	 *
	 * @var array
	 */
	var $language;

	/**
	 * Accepted types of content encoding
	 *
	 * @var array
	 */
	var $encoding;

	/**
	 * Accepted charsets
	 *
	 * @var array
	 */
	var $charset;

	/**
	 * Class constructor
	 *
	 * Shouldn't be called directly. Prefer calling {@link getInstance}.
	 *
	 * @return UserAgent
	 */
	function UserAgent() {
		parent::PHP2Go();
		$this->_initializeProperties();
		$this->_detectProperties();
	}

	/**
	 * Get the singleton of the UserAgent class
	 *
	 * Use this method when you need to read information
	 * about the request's user agent
	 *
	 * @return UserAgent
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new UserAgent();
		return $instance;
	}

	/**
	 * Get client browser's name
	 *
	 * @return string
	 */
	function getBrowserName() {
		return $this->browserName;
	}

	/**
	 * Get client browser's full name
	 *
	 * @return string
	 */
	function getBrowserFullName() {
		return $this->browserFullName;
	}

	/**
	 * Get the name of the user's operating system
	 *
	 * @return string
	 */
	function getOSName() {
		return $this->osName;
	}

	/**
	 * Get the full name of the user's operating system
	 *
	 * @return string
	 */
	function getOSFullName() {
		return $this->osFullName;
	}

	/**
	 * Get information about a given browser feature
	 *
	 * Supports the following properties:
	 * # javascript (returns the Javascript version bundled with the browser)
	 * # dom (check if browser supports the DOM model)
	 * # dhtml (check if browser supports DHTML)
	 * # gecko (get compilation date of the internal Gecko engine)
	 *
	 * @param string $feature Feature name
	 * @return mixed
	 */
	function getFeature($feature) {
		return (isset($this->features[$feature]) ? $this->features[$feature] : FALSE);
	}

	/**
	 * Tests the user's browser against a given pattern
	 *
	 * Available browser patterns:
	 * ns, ns2, ns3, ns4, ns4+, nav, ns6, ns6+, galeon, konqueror,
	 * nautilus, safari, text, gecko, firefox, firefox0x, firefox1x,
	 * firefox2x, ie, ie3, ie4, ie4+, ie5, ie55, ie55+, ie6, ie6+, ie7,
	 * myie, opera, opera2, opera3, opera4, opera5, opera6, opera7, opera8,
	 * opera9, opera5+, opera6+, opera7+, opera8+, opera9+, aol, aol3, aol4,
	 * aol5, aol6, aol7, aol8, webtv, aoltv, hotjava, hotjava3, hotjava3+,
	 * avant, k-meleon, crazy, epiphany, netgem, webdav.
	 *
	 * Examples:
	 * <code>
	 * $agent =& UserAgent::getInstance();
	 * $isIE = $agent->matchBrowser('ie');
	 * $isOpera7 = $agent->matchBrowser('opera7');
	 * </code>
	 *
	 * @param string $identifier Browser identifier
	 * @return unknown
	 */
	function matchBrowser($identifier) {
		$identifier = strtolower($identifier);
		return (isset($this->browser[$identifier]) ? $this->browser[$identifier] : FALSE);
	}

	/**
	 * Tests the user's browser against a list of patterns
	 *
	 * Returns first accepted pattern, otherwise returns FALSE.
	 *
	 * @param array $list Patterns list
	 * @return mixed
	 */
	function matchBrowserList($list) {
		$list = (array)$list;
		foreach ($list as $entry) {
			if (!empty($this->browser[strtolower($entry)]))
				return $entry;
		}
		return FALSE;
	}

	/**
	 * Tests the user's OS name against a given pattern
	 *
	 * Available OS patterns:
	 * win, win16, win31, win95, win98, wince, winme, win2k, winxp, win2003,
	 * winnt, win32, aix, aix1, aix2, aix3, aix4, amiga, beos, freebsd, hpux,
	 * hpux9, hpux10, irix, irix5, irix6, linux, mac, macosx, macppc, mac68k,
	 * netbsd, os3, openbsd, openvms, palmos, photon, sco, sinix, sun, sun4,
	 * sun5, suni86, unixware, bsd, unix.
	 *
	 * Examples:
	 * <code>
	 * $agent =& UserAgent::getInstance();
	 * $isXP = $agent->matchOS('winxp');
	 * $isLinux = $agent->matchOS('linux');
	 * </code>
	 *
	 * @param string $identifier OS identifier
	 * @return bool
	 */
	function matchOS($identifier) {
		$identifier = strtolower($identifier);
		return (isset($this->os[$identifier]) ? $this->os[$identifier] : FALSE);
	}

	/**
	 * Tests the user's OS name against a list of patterns
	 *
	 * Returns first accepted pattern, otherwise returns FALSE.
	 *
	 * @param array $list Patterns list
	 * @return mixed
	 */
	function matchOSList($list) {
		$list = (array)$list;
		foreach ($list as $entry) {
			if (!empty($this->os[strtolower($entry)]))
				return $entry;
		}
		return FALSE;
	}

	/**
	 * Check if a given MIME type is accepted
	 *
	 * @param string $mime MIME type
	 * @return bool
	 */
	function matchMimeType($mime) {
		$mimestr = implode('|', $this->mimeTypes);
		return preg_match("/{$mime}/i", $mimestr);
	}

	/**
	 * Check if a given language code is accepted
	 *
	 * @param string $lang Language code
	 * @return bool
	 */
	function matchLanguage($lang) {
		$langstr = implode('|', $this->language);
		return preg_match("/{$lang}/i", $langstr);
	}

	/**
	 * Check if a given type of content encoding is accepted
	 *
	 * @param string $encoding Encoding type
	 * @return bool
	 */
	function matchEncoding($encoding) {
		$encodingstr = implode('|', $this->encoding);
		return preg_match("/{$encoding}/", $encodingstr);
	}

	/**
	 * Check if a given charset is accepted
	 *
	 * @param string $charset Charset
	 * @return bool
	 */
	function matchCharset($charset) {
		$charsetstr = implode('|', $charset);
		return preg_match("/{$charset}/", $charsetstr);
	}

	/**
	 * Validates a list of values against one of the "accept" tables
	 *
	 * Returns the first accepted value, otherwise returns FALSE.
	 *
	 * @param array $list List of values
	 * @param string $acceptType Accept type: 'mimeTypes', 'language', 'encoding' or 'charset'
	 * @return mixed
	 */
	function matchAcceptList($list, $acceptType) {
		if (in_array($acceptType, array('mimeTypes', 'language', 'encoding', 'charset'))) {
			$str = implode('|', $this->{$acceptType});
			$list = (array)$list;
			foreach ($list as $entry) {
				if (preg_match("/{$entry}/i", $str))
					return $entry;
			}
		}
		return FALSE;
	}

	/**
	 * Builds a string representation of the UserAgent object
	 *
	 * @return string
	 */
	function __toString() {
		$features = array();
		foreach ($this->features as $name => $value)
			$features[] = $name . '=' . (TypeUtils::isBoolean($value) ? intval($value) : $value);
		$features = implode(', ', $features);
		return sprintf("Browser: %s\nOS: %s\nFeatures: %s\nAccept-Types: %s\nAccept-Languages: %s\nAccept-Encoding: %s\nAccept-Charset: %s",
				$this->browserFullName, $this->osFullName, $features,
				implode(', ', $this->mimeTypes), implode(', ', $this->language),
				implode(', ', $this->encoding), implode(', ', $this->charset)
		);
	}

	/**
	 * Initializes class properties
	 *
	 * @access private
	 */
	function _initializeProperties() {
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		$this->identifier = 'unknown';
		$this->fullVersion = 0;
		$this->majorVersion = 0;
		$this->minorVersion = 0;
		$this->verlet = '';
		$this->browser = array();
		$this->browserName = '';
		$this->os = array();
		$this->osName = '';
		$this->features = array(
			'javascript' => FALSE,
			'dhtml' => FALSE,
			'dom' => FALSE,
			'gecko'=> FALSE
		);
		$this->mimeTypes = array();
		$this->language = array();
		$this->encoding = array();
		$this->charset = array();
	}

	/**
	 * Detects browser properties
	 *
	 * @access private
	 */
	function _detectProperties() {
		$agent = strtolower($this->userAgent);
		if (preg_match(";^([[:alnum:]]+)[ /\(]*[[:alpha:]]*([0-9]*)(\.[0-9a-z\.]*);", $agent, $matches)) {
			if (isset($matches[1]))
				$this->identifier = $matches[1];
			if (isset($matches[2]))
				$this->majorVersion = $matches[2];
			if (isset($matches[3])) {
				if (preg_match("/([\.0-9]+)([\.a-z0-9]+)?/i", $matches[3], $verMatches)) {
					if (isset($verMatches[1]))
						$this->minorVersion = substr($verMatches[1], 1);
					if (isset($verMatches[2]))
						$this->verlet = $verMatches[2];
				}
			}
			$this->fullVersion = "{$this->majorVersion}.{$this->minorVersion}{$this->verlet}";
		}
		$this->_detectBrowserFlags($agent);
        $this->_detectOSFlags($agent);
        $this->_detectFeatures($agent);
		$this->_detectAccept();
		$this->_detectBrowserName();
		$this->_detectOSName();
	}

	/**
	 * Detects boolean values for all browser patterns
	 *
	 * @param string $agent Original user agent
	 * @access private
	 */
	function _detectBrowserFlags($agent) {
        $this->browser['ns'] = ($this->_match('mozilla', $agent) && $this->_dontMatch('spoofer|compatible|hotjava|opera|webtv', $agent));
        $this->browser['ns2'] = ($this->browser['ns'] && $this->majorVersion == 2);
        $this->browser['ns3'] = ($this->browser['ns'] && $this->majorVersion == 3);
        $this->browser['ns4'] = ($this->browser['ns'] && $this->majorVersion == 4);
        $this->browser['ns4+'] = ($this->browser['ns'] && $this->majorVersion >= 4);
        $this->browser['nav'] = ($this->browser['ns'] && $this->majorVersion < 5);
        $this->browser['ns6'] = ($this->browser['ns'] && $this->majorVersion == 5);
        $this->browser['ns6+'] = ($this->browser['ns'] && $this->majorVersion >= 5);
        $this->browser['galeon'] = $this->_match('galeon', $agent);
        $this->browser['konqueror'] = $this->_match('konqueror|safari', $agent);
        $this->browser['nautilus'] = $this->_match('nautilus', $agent);
        $this->browser['safari'] = $this->_match('konqueror|safari', $agent);
        $this->browser['text'] = $this->_match('links|lynx|w3m', $agent);
        $this->browser['gecko'] = ($this->_match('gecko', $agent) && !$this->browser['konqueror']);
        $this->browser['firefox'] = ($this->browser['gecko'] && $this->_match("firefox|firebird", $agent));
        $this->browser['firefox0x'] = ($this->browser['firefox'] && $this->_match("firebird/0.|firefox/0.", $agent));
        $this->browser['firefox1x'] = ($this->browser['firefox'] && $this->_match("firefox/1.", $agent));
        $this->browser['firefox2x'] = ($this->browser['firefox'] && $this->_match("firefox/2.", $agent));
        $this->browser['ie'] = ($this->_match('msie', $agent) && $this->_dontMatch('opera', $agent));
        $this->browser['ie3'] = ($this->browser['ie'] && $this->majorVersion == 3);
        $this->browser['ie4'] = ($this->browser['ie'] && $this->majorVersion == 4 && $this->_match('msie 4', $agent));
        $this->browser['ie4+'] = ($this->browser['ie'] && !$this->browser['ie3']);
        $this->browser['ie5'] = ($this->browser['ie4+'] && $this->_match('msie 5.0', $agent));
        $this->browser['ie55'] = ($this->browser['ie4+'] && $this->_match('msie 5.5', $agent));
        $this->browser['ie5+'] = ($this->browser['ie4+'] && !$this->browser['ie3'] && !$this->browser['ie4']);
        $this->browser['ie55+'] = ($this->browser['ie5+'] && !$this->browser['ie5']);
        $this->browser['ie6'] = $this->_match('msie 6', $agent);
        $this->browser['ie6+'] = ($this->browser['ie5+'] && !$this->browser['ie5'] && !$this->browser['ie5_5']);
        $this->browser['ie7'] = $this->_match('msie 7', $agent);
        $this->browser['myie'] = ($this->browser['ie'] && $this->_match('myie', $agent));
        $this->browser['opera'] = $this->_match('opera', $agent);
        $this->browser['opera2'] = $this->_match('opera[ /]2', $agent);
        $this->browser['opera3'] = $this->_match('opera[ /]3', $agent);
        $this->browser['opera4'] = $this->_match('opera[ /]4', $agent);
        $this->browser['opera5'] = $this->_match('opera[ /]5', $agent);
        $this->browser['opera6'] = $this->_match('opera[ /]6', $agent);
        $this->browser['opera7'] = $this->_match('opera[ /]7', $agent);
        $this->browser['opera8'] = $this->_match('opera[ /]8', $agent);
        $this->browser['opera9'] = $this->_match('opera[ /]9', $agent);
        $this->browser['opera5+'] = ($this->browser['opera'] && $this->_dontMatch('opera[ /][234]', $agent));
        $this->browser['opera6+'] = ($this->browser['opera'] && $this->_dontMatch('opera[ /][2345]', $agent));
        $this->browser['opera7+'] = ($this->browser['opera'] && $this->_dontMatch('opera[ /][23456]', $agent));
        $this->browser['opera8+'] = ($this->browser['opera'] && $this->_dontMatch('opera[ /][234567]', $agent));
        $this->browser['opera9+'] = ($this->browser['opera'] && $this->_dontMatch('opera[ /][2345678]', $agent));
        $this->browser['aol'] = $this->_match('aol', $agent);
        $this->browser['aol3'] = ($this->browser['aol'] && $this->browser['ie3']);
        $this->browser['aol4'] = ($this->browser['aol'] && $this->browser['ie4']);
        $this->browser['aol5'] = $this->_match('aol 5', $agent);
        $this->browser['aol6'] = $this->_match('aol 6', $agent);
        $this->browser['aol7'] = $this->_match('aol7|aol 7', $agent);
        $this->browser['aol8'] = $this->_match('aol8|aol 8', $agent);
        $this->browser['webtv'] = $this->_match('webtv', $agent);
        $this->browser['aoltv'] = $this->_match('tvnavigator|navio|navio_aoltv', $agent);
        $this->browser['hotjava'] = $this->_match('hotjava', $agent);
        $this->browser['hotjava3'] = ($this->browser['hotjava'] && $this->majorVersion == 3);
        $this->browser['hotjava3+'] = ($this->browser['hotjava'] && $this->majorVersion >= 3);
        $this->browser['avant'] = $this->_match('avant browser|avantbrowser', $agent);
        $this->browser['k-meleon'] = $this->_match('k-meleon', $agent);
        $this->browser['crazy'] = $this->_match('crazy browser', $agent);
        $this->browser['epiphany'] = $this->_match('epiphany', $agent);
		$this->browser['netgem'] = $this->_match('netgem', $agent);
        $this->browser['webdav'] = ($agent == 'microsoft data access internet publishing provider dav' || $agent == 'microsoft data access internet publishing provider protocol discovery');
	}

	/**
	 * Detects boolean values for all OS patterns
	 *
	 * @param string $agent Original user agent
	 * @access private
	 */
	function _detectOSFlags($agent) {
        $this->os['win'] = $this->_match('win|16bit', $agent);
        $this->os['win16'] = $this->_match('win16|16bit|windows 3.1|windows 16-bit', $agent);
        $this->os['win31'] = $this->_match('win16|windows 3.1|windows 16-bit', $agent);
        $this->os['win95'] = $this->_match('wi(n|ndows)[ -]?95', $agent);
        $this->os['win98'] = $this->_match('wi(n|ndows)[ -]?98', $agent);
        $this->os['wince'] = $this->_match('wi(n|ndows)[ -]?ce', $agent);
        $this->os['winme'] = $this->_match('win 9x 4.90|wi(n|ndows)[ -]?me', $agent);
        $this->os['win2k'] = $this->_match('wi(n|ndows)[ -]?(2000|nt 5.0)', $agent);
        $this->os['winxp'] = $this->_match('wi(n|ndows)[ -]?(xp|nt 5.1)', $agent);
        $this->os['win2003'] = $this->_match('win(n|ndows)[ -]?(2003|nt 5.2)', $agent);
		$this->os['winvista'] = $this->_match('win(n|ndows)[ -]}(vista|nt 6.0)', $agent);
        $this->os['winnt'] = $this->_match('wi(n|ndows)[ -]?nt', $agent);
        $this->os['win32'] = ($this->os['win95'] || $this->os['win98'] || $this->os['winnt'] || $this->_match('win32', $agent) || $this->_match('32bit', $agent));
        $this->os['aix'] = $this->_match('aix', $agent);
        $this->os['aix1'] = $this->_match('aix[ ]?1', $agent);
        $this->os['aix2'] = $this->_match('aix[ ]?2', $agent);
        $this->os['aix3'] = $this->_match('aix[ ]?3', $agent);
        $this->os['aix4'] = $this->_match('aix[ ]?4', $agent);
        $this->os['amiga'] = $this->_match('amiga[ ]?os', $agent);
        $this->os['beos'] = $this->_match('beos', $agent);
        $this->os['freebsd'] = $this->_match('free[ -]?bsd', $agent);
        $this->os['hpux'] = $this->_match('hp[ -]?ux', $agent);
        $this->os['hpux9'] = ($this->os['hpux'] && $this->_match('09.', $agent));
        $this->os['hpux10'] = ($this->os['hpux'] && $this->_match('10.', $agent));
        $this->os['irix'] = $this->_match('irix', $agent);
        $this->os['irix5'] = $this->_match('irix[ ]?5', $agent);
        $this->os['irix6'] = $this->_match('irix[ ]?6', $agent);
        $this->os['linux'] = $this->_match('linux|mdk for [0-9.]+', $agent);
        $this->os['mac'] = $this->_match('mac', $agent);
        $this->os['macosx'] = $this->_match('mac[ ]?os[ ]?x', $agent);
        $this->os['macppc'] = $this->_match('mac(_power|intosh.+p)pc', $agent);
        $this->os['mac68k'] = ($this->os['mac'] && $this->_match('68k|68000', $agent));
        $this->os['netbsd'] = $this->_match('net[ \-]?bsd', $agent);
        $this->os['os2'] = $this->_match('warp[ /]?[0-9.]+|os[ /]?2', $agent);
        $this->os['openbsd'] = $this->_match('open[ -]?bsd', $agent);
        $this->os['openvms'] = $this->_match('vax|open[ -]?vms', $agent);
        $this->os['palmos'] = $this->_match('palm[ -]?(source|os)', $agent);
        $this->os['photon'] = $this->_match('photon', $agent);
        $this->os['sco'] = $this->_match('sco|unix_sv', $agent);
        $this->os['sinix'] = $this->_match('sinix', $agent);
        $this->os['sun'] = $this->_match('sun[ -]?os', $agent);
        $this->os['sun4'] = $this->_match('sun[ -]?os[ ]?4', $agent);
        $this->os['sun5'] = $this->_match('sun[ -]?os[ ]?5', $agent);
        $this->os['suni86'] = ($this->os['sun'] && $this->_match('i86', $agent));
        $this->os['unixware'] = $this->_match('unixware|unix_system_v', $agent);
        $this->os['bsd'] = $this->_match('bsd', $agent);
        $this->os['unix'] = ($this->_match('x11|unix', $agent) || $this->os['aix'] || $this->os['hpux'] || $this->os['irix'] || $this->os['linux'] || $this->os['bsd'] || $this->os['sco'] || $this->os['sinix']);
	}

	/**
	 * Detects browser features
	 *
	 * @param string $agent Original user agent
	 * @access private
	 */
	function _detectFeatures($agent) {
        if ($this->browser['ns2'] || $this->browser['ie3'])
        	$this->features['javascript'] = '1.0';
        elseif ($this->browser['ns3'] || ($this->browser['opera'] && !$this->browser['opera5+']))
        	$this->features['javascript'] = '1.1';
        elseif (($this->browser['ns4'] && $this->fullVersion <= 4.05) || $this->browser['ie4'])
        	$this->features['javascript'] = '1.2';
        elseif (($this->browser['ns4'] && $this->fullVersion > 4.05) || $this->browser['ie5+'] || $this->browser['opera5+'] || $this->browser['hotjava3+'])
        	$this->features['javascript'] = '1.3';
        elseif (($this->browser['ie5'] && $this->os['mac']) || $this->browser['konqueror'])
        	$this->features['javascript'] = '1.4';
        elseif ($this->browser['ns6+'] || $this->browser['gecko'] || $this->browser['netgem'])
        	$this->features['javascript'] = '1.5';
        if ($this->browser['gecko']) {
        	if (preg_match(';gecko/([\d]+)\b;i', $agent, $matches))
        		$this->features['gecko'] = $matches[1];
        }
        if ($this->browser['ns6+'] || $this->browser['opera5+'] || $this->browser['konqueror'] || $this->browser['netgem'])
        	$this->features['dom'] = TRUE;
        if ($this->browser['ie4+'] || $this->browser['ns4+'] || $this->browser['opera5+'] || $this->browser['konqueror'] || $this->browser['netgem'])
        	$this->features['dhtml'] = TRUE;
	}

	/**
	 * Detects accept tables
	 *
	 * @access private
	 */
	function _detectAccept() {
		$Negotiator =& LocaleNegotiator::getInstance();
		$this->language = $Negotiator->getSupportedLanguages();
		if (empty($this->language))
			$this->language = array('en');
		$this->charset = $Negotiator->getSupportedCharsets();
		if (empty($this->charset))
			$this->charset = array('*');
        $this->mimeTypes = TypeUtils::toArray(preg_split(';[\s,]+;', $_SERVER['HTTP_ACCEPT'], -1, PREG_SPLIT_NO_EMPTY));
		$this->encoding = TypeUtils::toArray(preg_split(';[\s,]+;', $_SERVER['HTTP_ACCEPT_ENCODING'], -1, PREG_SPLIT_NO_EMPTY));
	}

	/**
	 * Detects full browser name
	 *
	 * @access private
	 */
	function _detectBrowserName() {
		$vstr = array(
			'aol' => 'AOL Browser',
			'myie' => 'MyIE',
			'konqueror' => 'Konqueror/Safari',
			'galeon' => 'Galeon',
			'nautilus' => 'Nautilus',
			'hotjava' => 'HotJava',
			'avant' => 'Avant Browser',
			'k-meleon' => 'K-meleon',
			'crazy' => 'Crazy Browser',
			'epiphany' => 'Epiphany',
			'netgem' => 'Netgem/iPlayer',
			'text' => 'Text based Browser',
			'webdav' => 'WebDAV',
			'opera9+' => 'Opera 9.x',
			'opera8+' => 'Opera 8.x',
			'opera7+' => 'Opera 7.x',
			'opera6+' => 'Opera 6.x',
			'opera5+' => 'Opera 5.x',
			'opera4' => 'Opera 4.x',
			'opera' => 'Opera',
			'ie7' => 'Microsoft Internet Explorer 7.x',
			'ie6+' => 'Microsoft Internet Explorer 6.x',
			'ie5+' => 'Microsoft Internet Explorer 5.x',
			'ie4+' => 'Microsoft Internet Explorer 4.x',
			'ie' => 'Microsoft Internet Explorer',
			'nav' => 'Netscape Navigator',
			'firefox2x' => 'Mozilla Firefox 2.x',
			'firefox1x' => 'Mozilla Firefox 1.x',
			'firefox0x' => 'Mozilla Firefox 0.x',
			'ns6+' => 'Mozilla/Netscape 6.x',
			'ns4' => 'Netscape 4.x',
			'ns' => 'Netscape'
		);
		$result = $this->matchBrowserList(array_keys($vstr));
		if ($result) {
			$this->browserName = $result;
			$this->browserFullName = $vstr[$result];
		} else {
			$this->browserName = strtolower($this->identifier);
			$this->browserFullName = $this->identifier;
		}
	}

	/**
	 * Detects full OS name
	 *
	 * @access private
	 */
	function _detectOSName() {
		$vstr = array(
			'winvista' => 'Microsoft Windows Vista',
			'win2003' => 'Microsoft Windows 2003',
			'winxp' => 'Microsoft Windows XP',
			'win2k' => 'Microsoft Windows 2000',
			'winme' => 'Microsoft Windows Millenium',
			'wince' => 'Microsoft Windows CE',
			'win98' => 'Microsoft Windows 98',
			'win95' => 'Microsoft Windows 95',
			'win16' => 'Microsoft Windows 3.x/16 bits',
			'winnt' => 'Microsoft Windows NT',
			'aix' => 'AIX',
			'amiga' => 'AmigaOS',
			'freebsd' => 'FreeBSD',
			'hpux' => 'HP UX',
			'irix' => 'Irix',
			'linux' => 'Linux',
			'macppc' => 'MacOS PPC',
			'macosx' => 'MacOS X',
			'mac' => 'MacOS',
			'netbsd' => 'NetBSD',
			'openbsd' => 'OpenBSD',
			'sun' => 'SunOS',
			'unix' => 'Unix'
		);
		$result = $this->matchOSList(array_keys($vstr));
		if ($result) {
			$this->osName = $result;
			$this->osFullName = $vstr[$result];
		} else {
			$this->osName = 'unknown';
			$this->osFullName = 'Unkown/Other';
		}
	}

	/**
	 * Utility method, used to check if an input
	 * string is <b>accepted</b> by a given pattern
	 *
	 * @param string $pattern Pattern
	 * @param string $value Input string
	 * @access private
	 * @return bool
	 */
	function _match($pattern, $value) {
		return ((bool)preg_match(":{$pattern}:", $value));
	}

	/**
	 * Utility method, used to check if an input
	 * string is <b>rejected</b> by a given pattern
	 *
	 * @param string $pattern Pattern
	 * @param string $value Input string
	 * @access private
	 * @return bool
	 */
	function _dontMatch($pattern, $value) {
		return (!(bool)preg_match(":{$pattern}:", $value));
	}
}
?>