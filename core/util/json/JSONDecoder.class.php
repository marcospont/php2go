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
 * JSON object start
 */
define('JSON_LEFT_BRACE', 1);
/**
 * JSON object end
 */
define('JSON_RIGHT_BRACE', 2);
/**
 * JSON array start
 */
define('JSON_LEFT_BRACKET', 3);
/**
 * JSON array end
 */
define('JSON_RIGHT_BRACKET', 4);
/**
 * Array or object members separator
 */
define('JSON_COMMA', 5);
/**
 * Separates property name and property value inside JSON objects
 */
define('JSON_COLON', 6);
/**
 * JSON data (strings, numbers)
 */
define('JSON_DATA', 7);
/**
 * Token type used to detect property names of JSON objects
 */
define('JSON_CONTINUE', 8);

/**
 * Unserializes PHP values from JSON strings
 *
 * JSON stands for Javascript Object Notation. It's a lightweight
 * data transfer format, widely used to interchange information between
 * client and server side by the most common AJAX libraries.
 *
 * @package util
 * @subpackage json
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class JSONDecoder extends PHP2Go
{
	/**
	 * Current JSON string
	 *
	 * @var string
	 * @access private
	 */
	var $src = NULL;

	/**
	 * Length of the JSON string
	 *
	 * @var int
	 * @access private
	 */
	var $length = 0;

	/**
	 * Last token
	 *
	 * @var string
	 * @access private
	 */
	var $token = NULL;

	/**
	 * Last token's type
	 *
	 * @var int
	 * @access private
	 */
	var $tokenType = FALSE;

	/**
	 * Current offset in the JSON string
	 *
	 * @var int
	 * @access private
	 */
	var $pos = 0;

	/**
	 * Determines how JSON objects should be decoded
	 *
	 * @var bool
	 * @access private
	 */
	var $looseType;

	/**
	 * Class constructor
	 *
	 * @param bool $looseType Parse JSON objects as arrays (TRUE) or objects (FALSE)
	 * @return JSONDecoder
	 */
	function JSONDecoder($looseType=FALSE) {
		parent::PHP2Go();
		$this->looseType = (bool)$looseType;
	}

	/**
	 * Shortcut method to decode a JSON string
	 *
	 * @param string $str JSON string
	 * @param bool $looseType Parse JSON objects as arrays (TRUE) or objects (FALSE)
	 * @return mixed Decoded value
	 * @static
	 */
	function decode($str, $looseType=FALSE) {
		$decode = new JSONDecoder($looseType);
		return $decode->decodeValue($str);
	}

	/**
	 * Decodes a given JSON string
	 *
	 * @param string $str JSON string
	 * @return mixed Decoded value
	 */
	function decodeValue($str) {
		// remove comments from the JSON string
		$str = preg_replace(array(
			'~^\s*//(.+)$#~',
			'~^\s*/\*(.+)\*/~Us',
			'~/\*(.+)\*/\s*$~Us'
		), '', $str);
		$this->src = $str;
		$this->length = strlen($str);
		$this->pos = 0;
		$this->_getToken();
		return $this->_decodeToken();
	}

	/**
	 * Processes the last token read
	 *
	 * @return mixed Token value
	 * @access private
	 */
	function _decodeToken() {
		if ($this->tokenType == JSON_DATA) {
			$token = $this->token;
			$this->_getToken();
			return $token;
		} elseif ($this->tokenType == JSON_LEFT_BRACE) {
			return $this->_decodeObject();
		} elseif ($this->tokenType == JSON_LEFT_BRACKET) {
			return $this->_decodeArray();
		} else {
			return NULL;
		}
	}

	/**
	 * Decodes an object
	 *
	 * @access private
	 * @return object
	 */
	function _decodeObject() {
		$properties = array();
		$tokenType = $this->_getToken();
		while ($tokenType && $tokenType != JSON_RIGHT_BRACE) {
			if ($tokenType == JSON_CONTINUE) {
				$key = '';
				while ($tokenType == JSON_CONTINUE) {
					$key .= $this->token;
					$tokenType = $this->_getToken();
				}
			} elseif (is_string($this->token)) {
				$key = $this->token;
				$tokenType = $this->_getToken();
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_INVALID_PROPNAME', $this->token), E_USER_ERROR, __FILE__, __LINE__);
			}
			if ($tokenType != JSON_COLON)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_MISSING_COLON', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
			$tokenType = $this->_getToken();
			if ($tokenType && $tokenType != JSON_CONTINUE && $tokenType != JSON_RIGHT_BRACE) {
				$properties[$key] = $this->_decodeToken();
				$tokenType = $this->tokenType;
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_INVALID_PROPVALUE', $this->token), E_USER_ERROR, __FILE__, __LINE__);
			}
			if ($tokenType == JSON_RIGHT_BRACE) {
				break;
			}
			if ($tokenType != JSON_COMMA)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_MISSING_COMMA', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
			$tokenType = $this->_getToken();
		}
		if ($tokenType == JSON_RIGHT_BRACE) {
			$this->_getToken();
		}
		if ($this->looseType) {
			return $properties;
		} else {
			$obj = new stdClass();
			foreach ($properties as $key => $value)
				$obj->{$key} = $value;
			return $obj;
		}
	}

	/**
	 * Decodes an array
	 *
	 * @access private
	 * @return array
	 */
	function _decodeArray() {
		$items = array();
		$tokenType = $this->_getToken();
		while ($tokenType && $tokenType != JSON_RIGHT_BRACKET) {
			$items[] = $this->_decodeToken();
			$tokenType = $this->tokenType;
			if ($tokenType == JSON_RIGHT_BRACKET) {
				break;
			}
			if ($tokenType != JSON_COMMA)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_MISSING_COMMA', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
			$tokenType = $this->_getToken();
		}
		if ($tokenType == JSON_RIGHT_BRACKET) {
			$this->_getToken();
		}
		return $items;
	}

	/**
	 * Reads the next token from the JSON string
	 *
	 * Returns the token type, or FALSE when the end of
	 * the string is reached.
	 *
	 * @return int|bool
	 * @access private
	 */
	function _getToken() {
		$this->token = '';
		$this->_skipWhitespace();
		if ($this->pos >= $this->length) {
			$this->tokenType = FALSE;
			return FALSE;
		}
		$str =& $this->src;
		$start = $i = $this->pos;
		switch ($str[$i]) {
			case '{' :
				$this->tokenType = JSON_LEFT_BRACE;
				$this->token = $str[$i];
				$i++;
				break;
			case '}' :
				$this->tokenType = JSON_RIGHT_BRACE;
				$this->token = $str[$i];
				$i++;
				break;
			case '[' :
				$this->tokenType = JSON_LEFT_BRACKET;
				$this->token = $str[$i];
				$i++;
				break;
			case ']' :
				$this->tokenType = JSON_RIGHT_BRACKET;
				$this->token = $str[$i];
				$i++;
				break;
			case ',' :
				$this->tokenType = JSON_COMMA;
				$this->token = $str[$i];
				$i++;
				break;
			case ':' :
				$this->tokenType = JSON_COLON;
				$this->token = $str[$i];
				$i++;
				break;
			case '/' :
				$i++;
				if ($i < $this->length) {
					if ($str[$i] == '/') {
						$i++;
						$char = NULL;
						while ($i < $this->length && $char != "\n") {
							$char = $str[$i];
							$i++;
						}
						$this->pos = $i;
						return $this->_getToken();
					}
					if ($str[$i] == '*') {
						$i++;
						$char = NULL;
						$found = FALSE;
						while ($i < $this->length) {
							$char = $str[$i];
							$i++;
							if ($char == '*' && @$str[$i] == '/') {
								$i++;
								break;
							}
						}
						$this->pos = $i;
						return $this->_getToken();
					}
				}
			case '"' :
			case '\'' :
				$delimiter = $str[$i];
				$buf = '';
				while ($i < ($this->length-1)) {
					$i++;
					$char = $str[$i];
					if ($char == '\\') {
						$i++;
						if ($i >= $this->length)
							break;
						$char = $str[$i];
						switch ($char) {
							case $delimiter : $buf .= $delimiter; break;
							case '\\' : $buf .= '\\'; break;
							case '/' : $buf .= '/'; break;
							case 'b' : $buf .= chr(8); break;
							case 't' : $buf .= chr(9); break;
							case 'n' : $buf .= chr(10); break;
							case 'f' : $buf .= chr(12); break;
							case 'r' : $buf .= chr(13); break;
							default : PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_ESCAPE_SEQUENCE', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
						}
					} elseif ($char == $delimiter) {
						$i++;
						break;
					} else {
						$buf .= $char;
					}
				}
				if ($char != $delimiter)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_UNTERMINATED_STRING', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
				$this->tokenType = JSON_DATA;
				$this->token = $buf;
				break;
			default :
				if ($this->tokenType != JSON_CONTINUE) {
					$matches = array();
					preg_match('/true|false|null/', $str, $matches, PREG_OFFSET_CAPTURE, $this->pos);
					if (preg_match('/true|false|null/', $str, $matches, PREG_OFFSET_CAPTURE, $this->pos) && $matches[0][1] == $this->pos) {
						$this->tokenType = JSON_DATA;
						$this->token = ($matches[0][0] == "true" ? TRUE : ($matches[0][0] == "false" ? FALSE : NULL));
						$i += strlen($matches[0][0]);
					} elseif (preg_match('/-?([0-9])+(\.[0-9]*)?((e|E)((-|\+)?)[0-9]+)?/s', $str, $matches, PREG_OFFSET_CAPTURE, $this->pos) && $matches[0][1] == $this->pos) {
						$this->tokenType = JSON_DATA;
						$int = intval($matches[0][0]);
						$float = doubleval($matches[0][0]);
						$this->token = ($int == $float ? $int : $float);
						$i += strlen($matches[0][0]);
					} else {
						$this->tokenType = JSON_CONTINUE;
						$this->token = $str[$i];
						$i++;
					}
				} else {
					$this->tokenType = JSON_CONTINUE;
					$this->token = $str[$i];
					$i++;
				}
				break;
		}
		$this->pos = $i;
		return $this->tokenType;
	}

	/**
	 * Ignores whitespace chars before the next token
	 *
	 * @access private
	 */
	function _skipWhitespace() {
		$matches = array();
		$result = preg_match("/(\s|\t|\n|\r|\f|\b)*/", $this->src, $matches, PREG_OFFSET_CAPTURE, $this->pos);
		if ($result && $matches[0][1] == $this->pos)
			$this->pos += strlen($matches[0][0]);
	}
}
?>