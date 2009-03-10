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
 * Utility methods to manipulate numeric values
 *
 * @package util
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Number extends PHP2Go
{
	/**
	 * Converts a number from one base to another
	 *
	 * Accepts bases from 2 to 36.
	 *
	 * @param mixed $source Input value
	 * @param int $baseIn Input base
	 * @param int $baseOut Output base
	 * @param int $targetLen Output value length
	 * @return mixed Converted number
	 * @static
	 */
	function numberConversion($source, $baseIn, $baseOut, $targetLen = 0) {
		if (!intval($baseIn) || !intval($baseOut) || $baseIn < 2 || $baseIn > 36 || $baseOut < 2 || $baseOut > 36)
			return NULL;
		if ($baseIn != 10) {
			$source = strtolower($source);
			$sourceLen = strlen($source);
			$decimalNumber = 0;
			for ($i = 0; $i < $sourceLen; $i++) {
				$operand = substr($source, $i, 1);
				if ($operand >= $baseIn)
					return NULL;
				if ((ord($operand) >= 97) && (ord($operand) <= 122))
					$operand = ord($operand) - 87;
				$decimalNumber += $operand * pow($baseIn, $i);
			}
		} else {
			$decimalNumber = $source;
		}
		if ($decimalNumber > 214748367)
			return NULL;
		settype($decimalNumber, "double");
		settype($baseOut, "double");
		if ($baseOut != 10) {
			$return = "";
			while ($decimalNumber > 0) {
				$remainder = (floatval($decimalNumber) % $baseOut);
				if ($remainder < 10) {
					$return = $remainder . $return;
				} else {
					$return = chr($remainder + 87) . $return;
				}
				$decimalNumber = floor($decimalNumber / $baseOut);
			}
		} else {
			$return = $decimalNumber;
		}
		if ($targetLen > strlen($return)) {
			return str_pad(strtoupper($return), $targetLen, "0", STR_PAD_LEFT);
		} else {
			return strtoupper($return);
		}
	}

	/**
	 * Converts from hexadecimal to binary
	 *
	 * @param string $number Hex number
	 * @return string Binary number
	 * @static
	 */
	function fromHexToBin($number) {
		$bin = "";
		for ($i=0, $s=strlen($number); $i<$s; $i+=2) {
			$bin .= decbin(hexdec(substr($number, $i, 2)));
		}
		return $bin;
	}

	/**
	 * Applies monetary format on a decimal value
	 *
	 * All empty arguments, starting from $currencySign, will be
	 * replaced by current locale settings.
	 *
	 * @link http://www.php.net/localeconv
	 * @param float $number Input value
	 * @param string $currencySign Currency sign
	 * @param string $decSep Decimals separator
	 * @param string $thousSep Thousands separator
	 * @param int $precision Precision
	 * @param string $currencySignPos Currency sign position ('left' or 'right')
	 * @return string Formatted number
	 * @static
	 */
	function fromDecimalToCurrency($number, $currencySign=NULL, $decSep=NULL, $thousSep=NULL, $precision=NULL, $currencySignPos='left') {
		$locale = localeconv();
		$currencySign = (is_null($currencySign) ? $locale['currency_symbol'] : (empty($currencySign) ? '' : $currencySign));
		if (empty($decSep))
			$decSep = $locale['mon_decimal_point'];
		if (empty($thousSep))
			$thousSep = $locale['mon_thousands_sep'];
		if (empty($precision))
			$precision = $locale['frac_digits'];
			
		if ($precision == 127) {
			$precision = 2;
			$thousSep = '.';
			$decSep = ',';
		}
		$number = floatval(trim($number));
		if (TypeUtils::isFloat($number)) {
			if (!empty($currencySign)) {
				$x = ($number < 0 ? 'n' : 'p');
				if (empty($currencySignPos))
					$currencySignPos = ($locale["{$x}_cs_precedes"] ? 'left' : 'right');
				$currencySignSpace = ($locale["{$x}_sep_by_space"] ? ' ' : '');
				return ($currencySignPos == 'left' ? $currencySign . $currencySignSpace . number_format($number, $precision, $decSep, $thousSep) : number_format($number, $precision, $decSep, $thousSep) . $currencySignSpace . $currencySign);
			} else {
				return number_format($number, $precision, $decSep, $thousSep);
			}
		}
		return NULL;
	}

	/**
	 * Converts from decimal to fractionary
	 *
	 * Returns FALSE when $number is integer.
	 *
	 * @param float $number Input number
	 * @return mixed
	 * @static
	 */
	function fromDecimalToFraction($number) {
		$locale = localeconv();
		$isF = TypeUtils::isFloat($number);
		if (!$isF) {
			$number = str_replace(',', '.', (string)$number);
			$isF = TypeUtils::isFloat($number);
			if (!$isF)
				return FALSE;
		}
		$number = (string)$number;
		if (strpos($number, $locale['decimal_point']) === FALSE)
			$number .= $locale['decimal_point'] . '0';
		list($intpart, $numerator) = explode($locale['decimal_point'], $number);
		$denominator = '1' . str_repeat('0', strlen ($numerator));
		$gcd = Number::gcd($numerator, $denominator);
		$numerator /= $gcd;
		$denominator /= $gcd;
		return ($intpart) ? sprintf("%d <sup>%d</sup>/<sub>%d</sub>", $intpart, $numerator, $denominator) : sprintf("<sup>%d</sup>/<sub>%d</sub>", $numerator, $denominator);
	}

	/**
	 * Converts from arabic to roman notation
	 *
	 * @param float $arabic Arabic number
	 * @return string
	 * @static
	 */
	function fromArabicToRoman($arabic) {
		$roman = '';
		$convBase = array(10 => array('X', 'C', 'M'),
			5 => array('V', 'L', 'D'),
			1 => array('I', 'X', 'C'));
		if ($arabic < 0) {
			return FALSE;
		} else {
			$arabic = (int) $arabic;
			$digit = (int) ($arabic / 1000);
			$arabic -= $digit * 1000;
			while ($digit > 0) {
				$roman .= 'M';
				$digit--;
			}
			for ($i = 2; $i >= 0; $i--) {
				$power = pow(10, $i);
				$digit = (int) ($arabic / $power);
				$arabic -= $digit * $power;
				if (($digit == 9) || ($digit == 4)) {
					$roman .= $convBase[1][$i] .= $convBase[$digit + 1][$i];
				} else {
					if ($digit >= 5) {
						$roman .= $convBase[5][$i];
						$digit -= 5;
					}
					while ($digit > 0) {
						$roman .= $convBase[1][$i];
						$digit--;
					}
				}
			}
			return $roman;
		}
	}

	/**
	 * Converts from roman to arabic notation
	 *
	 * @param string $roman Roman number
	 * @return float
	 * @static
	 */
	function fromRomanToArabic($roman) {
		$rValues = array(array(1, 'I'), array(5, 'V'),
			array(10, 'X'), array(50, 'L'),
			array(100, 'C'), array(500, 'D'),
			array(1000, 'M'), array(0, 0));
		$rRegExp = '/^(I|V|X|L|C|D|M)/i';
		if (!preg_match($rRegExp, $roman)) {
			return FALSE;
		} else {
			$rLen = strlen($roman)-1;
			$state = 0;
			$index = 0;
			$arabic = 0;
			while ($rLen >= 0) {
				$i = 0;
				while ($rValues[$i][0] > 0) {
					if (strtoupper($roman[$rLen]) == $rValues[$i][1]) {
						if ($state > $rValues[$i][0]) {
							if (($index - $i) != 2) {
								return FALSE;
							} else {
								$arabic -= $rValues[$i][0];
							}
						} else {
							$arabic += $rValues[$i][0];
							$state = $rValues[$i][0];
							$index = $i;
						}
						break;
					}
					$i++;
				}
				$rLen--;
			}
			return $arabic;
		}
	}

	/**
	 * Builds an human-readable representation of a byte amount
	 *
	 * Examples:
	 * <code>
	 * Number::formatByteAmount(65536, 'K');
	 * Number::formatByteAmount(16*1024*1024, 'M');
	 * </code>
	 *
	 * @param int $amount Byte amount
	 * @param string $mode Mode: K, M, G or T
	 * @param int $precision Precision
	 * @return string
	 * @static
	 */
	function formatByteAmount($amount, $mode = '', $precision = 2) {
		$locale = localeconv();
		$decSep = $locale['decimal_point'];
		$thousSep = $locale['thousands_sep'];
		$precision = intval($precision);
		switch($mode) {
			case 'K' : return number_format(($amount / 1024), $precision, $decSep, $thousSep) . 'K';
			case 'M' : return number_format(($amount / 1024 / 1024), $precision, $decSep, $thousSep) . 'M';
			case 'G' : return number_format(($amount / 1024 / 1024 / 1024), $precision, $decSep, $thousSep) . 'G';
			case 'T' : return number_format(($amount / 1024 / 1024 / 1024 / 1024), $precision, $decSep, $thousSep) . 'T';
			default  : return $amount;
		}
	}

	/**
	 * Get the modulus 10 of a given number
	 *
	 * @param int $number Input number
	 * @return int Modulus 10
	 * @static
	 */
	function modulus10($number) {
		$number = strrev($number);
		$numberLength = strlen($number);
		$currentNumber = $firstNumber = $secondNumber = NULL;
		$sum = 0;
		for ($i=0; $i<$numberLength; $i++) {
			$currentNumber = substr($number, $i, 1);
			if ($i % 2 == 1)
				$currentNumber *= 2;
			if ($currentNumber > 9) {
				$firstNumber = $currentNumber % 10;
				$secondNumber = ($currentNumber - $firstNumber) / 10;
				$currentNumber = $firstNumber + $secondNumber;
			}
			$sum += $currentNumber;
		}
		return (($sum % 10) == 0);
	}

	/**
	 * Get the modulus 11 of a given number
	 *
	 * @param int $number Input number
	 * @param int $base Base
	 * @return int Modulus 11
	 * @static
	 */
	function modulus11($number, $base = 9) {
		if (!TypeUtils::isInteger($number))
			return FALSE;
		$sum = 0;
		$factor = 2;
		$strSize = strlen(strval($number)) - 1;
		for ($i = $strSize; $i >= 0; $i--) {
			$sum += ($number[$i] * $factor);
			$factor = ($factor == $base) ? 2 : $factor++;
		}
		$result = 11 - ($sum % 11);
		return ($result == 10) ? 1 : $result;
	}

	/**
	 * Calculates the greatest common divisor of 2 numbers
	 *
	 * @param int $a First number
	 * @param int $b Second number
	 * @return int Result
	 * @static
	 */
	function gcd($a, $b) {
		while ($b != 0) {
			$remainder = $a % $b;
			$a = $b;
			$b = $remainder;
		}
		return abs($a);
	}

	/**
	 * Generates a random number, given an interval of values
	 *
	 * @param int $rangeMin Interval start
	 * @param int $rangeMax Interval end
	 * @return int Choosen number
	 * @static
	 */
	function randomize($rangeMin, $rangeMax) {
		if ($rangeMax > $rangeMin && is_numeric($rangeMin) && is_numeric($rangeMax)) {
			return rand($rangeMin, $rangeMax);
		} else {
			return NULL;
		}
	}
}
?>