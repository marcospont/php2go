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

import('php2go.util.Number');

/**
 * Collection of utility methods to handle with strings
 *
 * Basically, this collection of static methods wraps the
 * functions provided by PHP's standard string functions,
 * and adds some new funcionalities.
 *
 * @package text
 * @uses Number
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class StringUtils extends PHP2Go
{
	/**
	 * Removes whitespace chars from left, right and inside a string
	 *
	 * @param string $str Input string
	 * @return string
	 * @static
	 */
	function allTrim($str) {
		return StringUtils::stripBlank(trim($str));
	}

	/**
	 * Replaces 2 or more whitespace chars in a string
	 *
	 * @param string $str Input string
	 * @param string $replace Replacement string
	 * @return string
	 * @static
	 */
	function stripBlank($str, $replace=' ') {
		return ereg_replace("[[:blank:]]{1,}", $replace, $str);
	}

	/**
	 * Get $n chars from the left side of a string
	 *
	 * @param string $str Input string
	 * @param int $n Number of chars
	 * @return string
	 * @static
	 */
	function left($str, $n=0) {
		if (!TypeUtils::isInteger($n))
			return $str;
		if ($n == 0)
			return '';
		return substr($str, 0, $n);
	}

	/**
	 * Get $n chars from the right side of a string
	 *
	 * @param string $str Input string
	 * @param int $n Number of chars
	 * @return string
	 * @static
	 */
	function right($str, $n=0) {
		if (!TypeUtils::isInteger($n))
			return $str;
		if ($n == 0)
			return '';
		return substr($str, strlen($str) - $n, strlen($str)-1);
	}

	/**
	 * Reads a portion of a string, start at $startAt
	 *
	 * The start index is 1-based. Example:
	 * <code>
	 * $sb = new String Buffer('hello world');
	 * /* prints "hello" {@*}
	 * print $sb->mid(1, 5);
	 * </code>
	 *
	 * @param string $str Input string
	 * @param int $startAt Start index
	 * @param int $chars Substring length
	 * @return string
	 * @static
	 */
	function mid($str, $startAt=1, $chars=0) {
		if (!TypeUtils::isInteger($chars))
			return $str;
		if ($str == '' || $chars == 0)
			return '';
		if (($startAt + $chars) > strlen($str))
			return $str;
		if ($startAt == 0) $startAt = 1;
		return substr($str, $startAt-1, $chars);
	}

	/**
	 * Reads a char from a string
	 *
	 * @param string $str Input string
	 * @param int $index Char index
	 * @return string
	 * @static
	 */
	function charAt($str, $index) {
		if (!TypeUtils::isInteger($index))
			return '';
		if ($str == '' || $index < 0 || $index >= strlen($str))
			return '';
		$strTranslated = strval($str);
		return $strTranslated[$index];
	}

	/**
	 * Checks if a value is present in a given string
	 *
	 * @param string $str Input string
	 * @param string $search Search value
	 * @param bool $caseSensitive Whether to do case sensitive search
	 * @return bool
	 * @static
	 */
	function match($str, $search, $caseSensitive=TRUE) {
		if (!$caseSensitive) $search = strtolower($search);
		if (strlen($search) == 0) {
			return FALSE;
		} else {
			$pos = strpos($str, $search);
			return ($pos !== FALSE);
		}
	}

	/**
	 * Checks if a given string starts with a given value
	 *
	 * @param string $str Input string
	 * @param string $slice Comparison value
	 * @param bool $caseSensitive Case sensitive?
	 * @param bool $ignSpaces Ignore initial whitespace chars
	 * @return bool
	 * @static
	 */
	function startsWith($str, $slice, $caseSensitive=TRUE, $ignSpaces=TRUE) {
		if (!$caseSensitive) {
			$strUsed = ($ignSpaces) ? ltrim(strtolower($str)) : strtolower($str);
			$sliceUsed = strtolower($slice);
		} else {
			$strUsed = ($ignSpaces) ? ltrim($str) : $str;
			$sliceUsed = $slice;
		}
		return (StringUtils::left($strUsed, strlen($sliceUsed)) == $sliceUsed);
	}

	/**
	 * Checks if a given string ends with a given value
	 *
	 * @param string $str Input string
	 * @param string $slice Comparison value
	 * @param bool $caseSensitive Case sensitive?
	 * @param bool $ignSpaces Ignore initial whitespace chars
	 * @return bool
	 * @static
	 */
	function endsWith($str, $slice, $caseSensitive=TRUE, $ignSpaces=TRUE) {
		if (!$caseSensitive) {
			$strUsed = ($ignSpaces) ? rtrim(strtolower($str)) : strtolower($str);
			$sliceUsed = strtolower($slice);
		} else {
			$strUsed = ($ignSpaces) ? rtrim($str) : $str;
			$sliceUsed = $slice;
		}
		return (StringUtils::right($strUsed, strlen($sliceUsed)) == $sliceUsed);
	}

	/**
	 * Checks if a string is composed only by uppercase chars
	 *
	 * @param string $str Input string
	 * @return bool
	 * @static
	 */
	function isAllUpper($str) {
		return (preg_match("/[a-z]/", $str) !== FALSE);
	}

	/**
	 * Checks if a string is composed only by lowercase chars
	 *
	 * @param string $str Input string
	 * @return bool
	 * @static
	 */
	function isAllLower($str) {
		return (preg_match("/[A-Z]/", $str) !== FALSE);
	}

	/**
	 * Safely checks if a string is empty
	 *
	 * A string will be considered empty when its length
	 * is 0 and a call to the {@link empty()} function
	 * returns TRUE.
	 *
	 * @param string $str Input string
	 * @return bool
	 * @static
	 */
	function isEmpty($str) {
		$str = strval($str);
		return (empty($str) && strlen($str) == 0);
	}

	/**
	 * Returns a fallback value when a given string is empty
	 *
	 * @param string $str Input string
	 * @param string $replacement Fallback string
	 * @return string
	 * @static
	 */
	function ifEmpty($str, $replacement) {
		return (StringUtils::isEmpty($str) ? $replacement : $str);
	}

	/**
	 * Appends a value in the end of a string
	 *
	 * @param string $str Input string
	 * @param string $concat Concat string
	 * @return string
	 * @static
	 */
	function concat($str, $concat) {
		return $str . $concat;
	}

	/**
	 * Surrounds a string with a prefix and a suffix
	 *
	 * @param string $str Input string
	 * @param string $prefix Prefix
	 * @param string $suffix Suffix
	 * @return string
	 * @static
	 */
	function surround($str, $prefix, $suffix) {
		return $prefix . $str . $suffix;
	}

	/**
	 * Inserts a value in a given position of a string
	 *
	 * @param string $str Input string
	 * @param string $insValue Insert value
	 * @param int $insPos Insert position
	 * @return string
	 * @static
	 */
	function insert($str, $insValue = '', $insPos = 0) {
		if (($insValue == '') || ($insPos < 0) || ($insPos > strlen($str)))
			return $str;
		if ($insPos == 0)
			return $insValue . $str;
		if ($insPos == strlen($str))
			return $str . $insValue;
		return StringUtils::left($str, $insPos) . $insValue . StringUtils::right($str, $insPos, strlen($str) - $insPos);
	}

	/**
	 * Replaces all occurrences of $from by $to in a given string
	 *
	 * @param string $str Input string
	 * @param string $from Search
	 * @param string $to Replace
	 * @return string
	 * @static
	 */
	function replace($str, $from, $to) {
		return str_replace($from, $to, $str);
	}

	/**
	 * Performs a regular expression search and replace on a string
	 *
	 * @param string $str Input string
	 * @param string $pattern PCRE pattern
	 * @param string $replacement Replacement
	 * @return string
	 * @static
	 */
	function regexReplace($str, $pattern, $replacement) {
		if (empty($pattern))
			return $str;
		$matches = array();
    	if (preg_match('!\W(\w+)$!s', $pattern, $matches) && (strpos($matches[1], 'e') !== FALSE))
			$pattern = substr($pattern, 0, -strlen($matches[1])) . str_replace('e', '', $matches[1]);
		return preg_replace($pattern, $replacement, $str);
	}

	/**
	 * Splits the string using $sep as separator
	 *
	 * @param string $str Input string
	 * @param string $sep Separator
	 * @return array Resultant array
	 * @static
	 */
	function explode($str, $sep) {
		$arr = explode($sep, $str);
		return $arr;
	}

	/**
	 * Implodes an array, returning the result as a string
	 *
	 * @param array $values Input array
	 * @param string $glue Glue
	 * @return string
	 * @static
	 */
	function implode($values, $glue) {
		return implode($glue, (array)$values);
	}

	/**
	 * Encodes a given string
	 *
	 * The supported encoding types (and their parameters) are
	 * base64 (none), utf8 (none), 7bit (nl), 8bit (nl) and
	 * quoted-printable (charset).
	 *
	 * Examples:
	 * <code>
	 * StringUtils::encode('encode me', 'base64');
	 * StringUtils::encode('encode me', '7bit');
	 * StringUtils::encode('quoted printable', 'quoted-printable', array('charset' => 'utf-8'));
	 * </code>
	 *
	 * @param string $str Input string
	 * @param string $encodeType Encode type
	 * @param array $params Encoding params
	 * @return string
	 * @static
	 */
	function encode($str, $encodeType, $params=NULL) {
		switch(strtolower($encodeType)) {
			case 'base64' :
				$encoded = chunk_split(base64_encode($str));
				break;
			case 'utf8' :
				$encoded = utf8_encode($str);
				break;
			case '7bit' :
			case '8bit' :
				$nl = TypeUtils::ifNull($params['nl'], "\n");
				$str = str_replace(array("\r\n", "\r"), array("\n", "\n"), $str);
				$encoded = str_replace("\n", $nl, $str);
				if (!StringUtils::endsWith($encoded, $nl))
					$encoded .= $nl;
				break;
			case 'quoted-printable' :
				static $qpChars;
				if (!isset($qpChars))
					$qpChars = array_merge(array(64, 61, 46), range(0, 31), range(127, 255));
				$charset = TypeUtils::ifNull($params['charset'], 'iso-8859-1');
				$replace = array(' ' => '_');
				foreach ($qpChars as $char)
					$replace[chr($char)] = '=' . strtoupper(dechex($char));
				return sprintf("=?%s?Q?%s=", $charset, strtr($str, $replace));
			default:
				$encoded = $str;
				break;
		}
		return $encoded;
	}

	/**
	 * Decodes a given string
	 *
	 * Supported encoding types: base64, utf8 and quoted-printable.
	 *
	 * @param string $str Input string
	 * @param string $encodeType Encoding type
	 * @return string
	 * @static
	 */
	function decode($str, $encodeType) {
		switch(strtolower($encodeType)) {
			case 'base64' :
				$decoded = base64_decode($str);
				break;
			case 'utf8' :
				$decoded = utf8_decode($str);
				break;
			case 'quoted-printable' :
				$decoded = quoted_printable_decode($str);
				break;
			default :
				$decoded = $str;
				break;
		}
		return $decoded;
	}

	/**
	 * This method has the functionality of an if-then-else statement
	 *
	 * Given the function arguments, the method analyzes them in pairs,
	 * starting from the second (the first is the input string). For
	 * each pair A and B, if A is equal to the original string, B is
	 * returned. If none of the pairs matches, the last argument can
	 * be used as an "else".
	 *
	 * Examples:
	 * <code>
	 * $result = StringUtils::map($value, 1, 'yes', 0, 'no', 'unknown');
	 * $result = StringUtils::map($bool, true, 'true', false, 'false');
	 * </code>
	 *
	 * @return mixed
	 * @static
	 */
	function map() {
		$argc = func_num_args();
		$argv = func_get_args();
		if ($argc == 0)
			return NULL;
		$base = $argv[0];
		for ($i=1,$s=sizeof($argv); $i<$s; $i+=2) {
			if (array_key_exists($i+1, $argv)) {
				if ($base == $argv[$i])
					return $argv[$i+1];
			} else {
				return $argv[$i];
			}
		}
		return $base;
	}

	/**
	 * Applies a given filter on a string
	 *
	 * Filter types:
	 * # alpha: removes all alpha chars
	 * # alphalower : removes all lowercase alpha chars
	 * # alphaupper : removes all uppercase alpha chars
	 * # num : removes all numbers
	 * # alphanum : removes all alphanumeric chars
	 * # htmlentities : removes all html entities
	 * # blank : removes all whitespace chars
	 *
	 * @param string $str Input string
	 * @param string $filterType Filter type
	 * @param string $replaceStr Replacement string
	 * @return string
	 * @static
	 */
	function filter($str, $filterType='alphanum', $replaceStr='') {
		$replaceStr = strval($replaceStr);
		switch ($filterType) {
			case 'alpha' :
				return (ereg_replace("[^a-zA-Z]", $replaceStr, $str));
			case 'alphalower' :
				return (ereg_replace("[^a-z]", $replaceStr, $str));
			case 'alphaupper' :
				return (ereg_replace("[^A-Z]", $replaceStr, $str));
			case 'num' :
				return (ereg_replace("[^0-9]", $replaceStr, $str));
			case 'alphanum' :
				return (ereg_replace("[^0-9a-zA-Z]", $replaceStr, $str));
			case 'htmlentities' :
				return (ereg_replace("&[[:alnum:]]{0,};", $replaceStr, $str));
			case 'blank' :
				return (ereg_replace("[[:blank:]]{1,}", $replaceStr, $str));
			default :
				return $str;
		}
	}

	/**
	 * Escapes a string according to a given pattern
	 *
	 * Patterns:
	 * # html: escapes HTML special chars
	 * # htmlall: escapes HTML entities
	 * # url: escapes URL special chars (using {@link rawurlencode()})
	 * # quotes: escapes quotes
	 * # javascript: escapes JS code
	 * # mail: replaces '@' by 'at' and '.' by 'dot'
	 *
	 * @param string $str Input string
	 * @param string $escapeType
	 * @return string
	 * @static
	 */
	function escape($str, $escapeType='html') {
		switch ($escapeType) {
			case 'html':
				return htmlspecialchars($str, ENT_QUOTES);
			case 'htmlall' :
				return htmlentities($str, ENT_QUOTES);
			case 'url' :
				return rawurlencode($str);
			case 'quotes' :
				return preg_replace("%(?<!\\\\)'%", "\\'", $str);
			case 'javascript' :
				$expressions = array(
					"/(<scr)(ipt)/i" => "$1\"+\"$2", // quebrar tags "<script"
					'/\\\\/' => '\\\\', // backslashes
					'/\'/' => "\'", // single quotes
					'/"/' => '\\"', // double quotes
					"/\r/"=>'\\r', // caractere CR
					"/\n/"=>'\\n', // caractere LF
					"/\t/" => "\\t" // tabulações
				);
				$str = str_replace("\\", "\\\\", $str);
				$str = preg_replace(array_keys($expressions), array_values($expressions), $str);
				return $str;
			case 'mail' :
				return str_replace(array('@', '.'), array(' at ', ' dot '), $str);
			default :
				return $str;
		}
	}

	/**
	 * Converts a string to camel case format
	 *
	 * @param string $str Input string
	 * @return string
	 * @static
	 */
	function camelize($str) {
		return preg_replace("/[_|\s]([a-z0-9])/e", "strtoupper('\\1')", strtolower($str));
	}

	/**
	 * Capitalizes all words of a given string
	 *
	 * @param string $str Input string
	 * @return string
	 * @static
	 */
	function capitalize($str) {
		if (!empty($str)) {
			$w = preg_split("/\s+/", $str);
			for ($i=0, $s=sizeof($w); $i<$s; $i++) {
				if (empty($w[$i]))
					continue;
				$f = strtoupper($w[$i][0]);
				$r = strtolower(substr($w[$i], 1));
				$w[$i] = $f . $r;
			}
			return implode(' ', $w);
		}
		return $str;
	}

	/**
	 * Normalizes a given string
	 *
	 * Replaces all chars from positions 192-223 and 224-225 of the
	 * ASCII table by their 'normal' versions: áéíÁÈÒÖ by aeiAEOO.
	 *
	 * @param string $str Input string
	 * @return string
	 * @static
	 */
	function normalize($str) {
		$ts = array("/[À-Å]/", "/Æ/", "/Ç/", "/[È-Ë]/", "/[Ì-Ï]/", "/Ð/", "/Ñ/", "/[Ò-ÖØ]/", "/×/", "/[Ù-Ü]/", "/Ý/", "/ß/", "/[à-å]/", "/æ/", "/ç/", "/[è-ë]/", "/[ì-ï]/", "/ð/", "/ñ/", "/[ò-öø]/", "/÷/", "/[ù-ü]/", "/[ý-ÿ]/");
		$tn = array("A", "AE", "C", "E", "I", "D", "N", "O", "X", "U", "Y", "ss", "a", "ae", "c", "e", "i", "d", "n", "o", "x", "u", "y");
		return preg_replace($ts, $tn, $str);
	}

	/**
	 * Remove all chars in a string before a given token is found
	 *
	 * <code>
	 * /* prints "order by name" {@*}
	 * print StringUtils::cutBefore("select * from table order by name", "order by");
	 * </code>
	 *
	 * @param string $string Input string
	 * @param string $token Token
	 * @param bool $caseSensitive Case sensitive?
	 * @return string
	 * @static
	 */
	function cutBefore($string, $token, $caseSensitive=TRUE) {
		if (StringUtils::match($caseSensitive ? $string : strtolower($string), $token, $caseSensitive))
			return stristr($string, $token);
		return $string;
	}

	/**
	 * Removes all chars after the last ocurrence of $cutOff in $string
	 *
	 * <code>
	 * /* prints "select * from table" {@*}
	 * print StringUtils::cutLastOcurrence("select * from table where active = 1", "where");
	 * </code>
	 *
	 * @param string $string Input string
	 * @param string $cutOff Token
	 * @param bool $caseSensitive Case sensitive?
	 * @return string
	 * @static
	 */
	function cutLastOcurrence($string, $cutOff, $caseSensitive=TRUE) {
		if (!StringUtils::match($caseSensitive ? $string : strtolower($string), $cutOff, $caseSensitive))
			return $string;
		return strrev(substr(stristr(strrev($string), strrev($cutOff)),strlen($cutOff)));
	}

	/**
	 * Indents a given text using $iChar as indent char
	 *
	 * @param string $str Input string
	 * @param int $nChars Number of times to repeat the indent char
	 * @param string $iChar Indent char
	 * @return string
	 * @static
	 */
	function indent($str, $nChars, $iChar=' ') {
		if (!TypeUtils::isInteger($nChars) || $nChars < 1) {
			$nChars = 1;
		}
		return preg_replace('!^!m', str_repeat($iChar, $nChars), $str);
	}

	/**
	 * Truncate a given string to $length
	 *
	 * If the length of the original string is lower than $length,
	 * the original string is returned.
	 *
	 * @param string $str Input string
	 * @param int $length New length
	 * @param int $truncSufix Suffix to be appended in the end of the truncated string
	 * @param bool $forceBreak Force break of long words
	 * @return string
	 * @static
	 */
	function truncate($str, $length, $truncSufix='...', $forceBreak=TRUE) {
		if (!TypeUtils::isInteger($length) || $length < 1) {
			return '';
		} else {
			if (strlen($str) > $length) {
				$length -= strlen($truncSufix);
        		if (!$forceBreak)
            		$str = preg_replace('/\s+?(\S+)?$/', '', substr($str, 0, $length+1));
				return substr($str, 0, $length) . $truncSufix;
			} else {
				return $str;
			}
		}
	}

	/**
	 * Insert a char between every pair of chars of a string
	 *
	 * @param string $str Input string
	 * @param string $char Char to be inserted
	 * @param bool $stripEmpty Strip whitespace chars
	 * @return string
	 * @static
	 */
	function insertChar($str, $char=' ', $stripEmpty = TRUE) {
		if ($stripEmpty) {
			$strChars = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$strChars = preg_split('//', $str, -1);
		}
		return implode($char, $strChars);
	}

	/**
	 * Adds line wraps on a given string
	 *
	 * The number of chars per line is defined by the $num
	 * parameter. The $breakString parameter defines the
	 * character(s) to be used to represent a line break.
	 *
	 * @param string $str Input string
	 * @param int $num Chars per line
	 * @param string $breakString Line break string
	 * @return string
	 * @static
	 */
	function wrapLine($str, $num, $breakString="\n") {
		$line = '';
		$processed = '';
		$token = strtok($str, ' ');
		while($token) {
			if (strlen($line) + strlen($token) < ($num + 2)) {
				$line .= " $token";
			} else {
				$processed .= "$line$breakString";
				$line = $token;
			}
			$token = strtok(' ');
		}
		$processed .= $line;
		$processed = trim($processed);
		return $processed;
	}

	/**
	 * Adds or adjusts line breaks on a string, using $num chars per line
	 *
	 * The $breakString parameter defines the character(s) to be
	 * used to represent a line break.
	 *
	 * @param string $str Input string
	 * @param int $num Chars per line
	 * @param string $breakString Line break string
	 * @return string
	 * @static
	 */
	function wrap($str, $num, $breakString="\n") {
		$str = ereg_replace("([^\r\n])\r\n([^\r\n])", "\\1 \\2", $str);
		$str = ereg_replace("[\r\n]*\r\n[\r\n]*", "\r\n\r\n", $str);
		$str = ereg_replace("[ ]* [ ]*", ' ', $str);
		$str = stripslashes($str);
		$processed = '';
		$paragraphs = explode("\n", $str);
		for ($i=0; $i<sizeof($paragraphs); $i++) {
			$processed .= StringUtils::wrapLine($paragraphs[$i], $num, $breakString) . $breakString;
		}
		$processed = trim($processed);
		return $processed;
	}

	/**
	 * Adds line numbers on a given text
	 *
	 * @param string $str Input text
	 * @param int $start Start numeration
	 * @param int $indent Start indentation
	 * @param string $afterNumberChar Suffix for the line numbers
	 * @param string $glue Line glue
	 * @return string
	 * @static
	 */
	function addLineNumbers(&$str, $start = 1, $indent = 3, $afterNumberChar = ':', $glue="\n") {
		$line = explode("\n", $str);
		$size = sizeof($line);
		$width = strlen((string)($start + $size -1));
		$indent = max($width, $indent);
		for ($i = 0; $i < $size; $i++)
			$line[$i] = str_pad((string)($i + $start), $indent, ' ', STR_PAD_LEFT) . $afterNumberChar . ' ' . trim($line[$i]);
		return implode($glue, $line);
	}

	/**
	 * Counts characters of a given string
	 *
	 * @param string $str Input string
	 * @param bool $includeSpaces Consider whitespace chars when couting
	 * @return int
	 * @static
	 */
	function countChars($str, $includeSpaces = FALSE) {
		if ($includeSpaces) {
			return strlen($str);
		} else {
			$match = array();
			return preg_match_all('/[^\s]/', $str, $match);
		}
	}

	/**
	 * Counts the number of words of a given text
	 *
	 * @param string $str Input text
	 * @return int
	 * @static
	 */
	function countWords($str) {
		return str_word_count($str);
	}

	/**
	 * Counts the number of sentences of a given text
	 *
	 * @param string $str Input text
	 * @return int
	 * @static
	 */
	function countSentences($str) {
		$matches = array();
		return preg_match_all('/[^\s]\.(?!\w)/', $str, $matches);
	}

	/**
	 * Counts the number of paragraphs of a given text
	 *
	 * @param string $str Input text
	 * @return int
	 * @static
	 */
	function countParagraphs($str) {
		return count(preg_split('/[\r\n]+/', $str));
	}

	/**
	 * Generates a random string
	 *
	 * @param int $size Desired string length
	 * @param bool $upper Whether to use uppercase chars
	 * @param bool $digit Whether to use digits
	 * @return string Generated random string
	 * @static
	 */
	function randomString($size, $upper=TRUE, $digit=TRUE) {
		$pSize = max(1, $size);
		$start = $digit ? 48 : 65;
		$end = 122;
		$result = '';
		while (strlen($result) < $size) {
			$random = Number::randomize($start, $end);
			if (($digit && $random >= 48 && $random <= 57) ||
				($upper && $random >= 65 && $random <= 90) ||
				($random >= 97 && $random <= 122)) {
				$result .= chr($random);
			}
		}
		return $result;
	}
}
?>