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
 * Negotiates locale and charset based on HTTP headers
 * 
 * This class parses the Accept-Language and Accept-Charset headers in order
 * to determine what are the user settings and use it when auto detecting
 * localization settings.
 * 
 * When PHP2Go loads, and LANGUAGE.AUTO_DETECT is set to true, or CHARSET is
 * set to 'auto', this class is used to determine the new values for these
 * settings, based on the client browser preferences.
 *
 * @package php2go
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class LocaleNegotiator
{
	/**
	 * Set of supported languages
	 *
	 * @var array
	 * @access private
	 */
	var $languages;
	
	/**
	 * Set of supported charsets
	 *
	 * @var array
	 * @access private
	 */
	var $charsets;
	
	/**
	 * Class constructor
	 * 
	 * Loads supported languages and charsets from the HTTP_ACCEPT_LANGUAGE
	 * and HTTP_ACCEPT_CHARSET headers.
	 *
	 * @return LocaleNegotiator
	 */
	function LocaleNegotiator() {
		$this->languages = $this->_parseHeader(@$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$this->charsets = $this->_parseHeader(@$_SERVER['HTTP_ACCEPT_CHARSET']);		
	}
	
	/**
	 * Get the singleton of the LocaleNegotiator class
	 *
	 * @return LocaleNegotiator
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new LocaleNegotiator();
		return $instance;
	}
	
	/**
	 * Get supported language codes
	 * 
	 * Each item of the returned array contains a language code, 
	 * along with the quality factor (if lower than 1.0).
	 *
	 * @return array
	 */
	function getSupportedLanguages() {
		$tmp = array();
		foreach ($this->languages as $k => $v)
			$tmp[] = $k . ($v == 1.0 ? '' : ';' . $v);
		return $tmp;
	}
	
	/**
	 * Get supported charset encodings
	 * 
	 * Each item of the returned array contains a charset code,
	 * along with the quality factor (if lower than 1.0).
	 *
	 * @return array
	 */
	function getSupportedCharsets() {
		$tmp = array();
		foreach ($this->charsets as $k => $v)
			$tmp[] = $k . ($v == 1.0 ? '' : ';' . $v);
		return $tmp;
	}
	
	/**
	 * Negotiate the language code
	 * 
	 * This method expects a set of language codes supported
	 * by the application and a default language code to be used
	 * when negotiation fails.
	 * 
	 * The set of application language codes is compared with
	 * the languages parsed from the request.
	 *
	 * @param array $available Language codes supported by the application
	 * @param string $default Default language code
	 * @return string Language code
	 */
	function negotiateLanguage($available, $default) {
		$available = (array)$available;
		foreach ($this->languages as $lang => $level) {
			$result = $this->_compare($lang, $available);
			if ($result)
				return $result;
		}
		return $default;		
	}
	
	/**
	 * Negotiate the charset encoding
	 * 
	 * Matches a set of charset codes supported by the application
	 * against the ones parsed from the HTTP headers
	 *
	 * @param array $available Available charset codes
	 * @param string $default Default charset code, to be used when negotiation fails
	 * @return string Charset
	 */
	function negotiateCharset($available, $default) {
		$available = (array)$available;
		foreach ($this->charsets as $charset => $level) {
			$result = $this->_compare($charset, $available);
			if ($result)
				return $result;
		}
		return $default;
	}
	
	/**
	 * Internal method that parses Accept-* headers
	 *
	 * @param string $str Raw header
	 * @return array Parsed header
	 */
	function _parseHeader($str) {
		$result = array();
		$str = (string)$str;
		if ($token = strtok($str, ', ')) {
			do {
				$pos = strpos($token, ';');
				if ($pos !== FALSE) {
					$name = substr($token, 0, $pos);
					$level = (float)substr($token, $pos+3);
				} else {
					$name = $token;
					$level = 1.0;
				}
				$result[$name] = $level;
			} while ($token = strtok(', '));
			asort($result, SORT_NUMERIC);
			return array_reverse($result);
		}
		return $result;
    }
    
    /**
     * Search for a value inside an array, using binary safe 
     * and case insensitive comparison
     *
     * @param string $value Search value
     * @param array $array Search base
     * @return string|FALSE Array entry in case of success. FALSE in case of error
     */
    function _compare($value, $array) {
    	$l = strlen($value);
    	foreach ($array as $comp) {
    		if (strncasecmp($value, $comp, $l) === 0)
    			return $comp;
    	}
    	return FALSE;
    }
}
?>