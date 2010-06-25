<?php

abstract class LocaleNumberFormatter
{
	private static $formats = array();
	
	public static function formatInteger($value) {
		return self::format($value, 0);
	}
	
	public static function formatFloat($value) {
		return self::format($value);
	}
	
	public static function format($value, $format=null, $precision=null) {
		if ($format === null) {
			$locale = Php2Go::app()->getLocale();
			$format = $locale->getDecimalFormat();
		} elseif (Locale::isLocale($format)) {
			$locale = ($format instanceof Locale ? $format : new Locale($format));
			$format = $locale->getDecimalFormat();
		} elseif (is_string($format)) {
			$locale = Php2Go::app()->getLocale();
		} elseif (is_int($format) && $format >= 0 && $format <= 30) {
			$precision = $format;
			$locale = Php2Go::app()->getLocale();
			$format = $locale->getDecimalFormat();			
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid number format.'));
		}
		if (strpos($format, '0') === false)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid number format.'));
		if ($precision !== null) {
			if (is_numeric($precision) && $precision >= 0 && $precision <= 0)
				$value = round($value, $precision);
			else
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid precision.'));
		}		
		return self::formatNumber($value, self::parseFormat($format, $locale), $locale);		
	}
	
	private static function formatNumber($value, array $format, Locale $locale) {
		$symbols = $locale->getNumberSymbols();
		$negative = ($value < 0);
		$value = abs($value * $format['multiplier']);
		if ($format['precision'] !== null)
			$value = round($value, $format['precision']);
		$value = "$value";
		if (($pos= strpos($value, '.'))!==false) {
			$integer = substr($value, 0, $pos);
			$decimal = substr($value, $pos+1);
		} else {
			$integer = $value;
			$decimal = '';
		}
		if ($format['precision'] > strlen($decimal))
			$decimal = str_pad($decimal, $format['precision'], '0');
		if (strlen($decimal) > 0)
			$decimal = $symbols['decimal'] . $decimal;
		$integer = str_pad($integer, $format['integerDigits'], '0', STR_PAD_LEFT);
		if ($format['groupSize1'] > 0 && strlen($integer) > $format['groupSize1']) {
			$str1 = substr($integer, 0, -$format['groupSize1']);
			$str2 = substr($integer, -$format['groupSize1']);
			$size = $format['groupSize2'] > 0 ? $format['groupSize2'] : $format['groupSize1'];
			$str1 = str_pad($str1, (int)((strlen($str1) + $size - 1) / $size) * $size, ' ', STR_PAD_LEFT);
			$integer = ltrim(implode($symbols['group'], str_split($str1, $size))) . $symbols['group'] . $str2;
		}
		if ($negative)
			$number = $format['negativePrefix']. $integer . $decimal . $format['negativeSuffix'];
		else
			$number = $format['positivePrefix'] . $integer . $decimal . $format['positiveSuffix'];
		return strtr($number, array('%' => $symbols['percentSign'], '‰' => $symbols['perMille']));		
	}
	
	private static function parseFormat($format, Locale $locale) {
		if (isset(self::$formats[$format]))
			return self::$formats[$format];
		$result = array();
		$matches = array();
		$symbols = $locale->getNumberSymbols();
		// split positive and negative patterns
		$formats = explode(';', $format);
		$result['positivePrefix'] = $result['positiveSuffix'] = $result['negativePrefix'] = $result['negativeSuffix'] = '';
		if (preg_match('/^(.*?)[#,\.0]+(.*?)$/', $formats[0], $matches)) {
			$result['positivePrefix'] = $matches[1];
			$result['positiveSuffix'] = $matches[2];
		}
		if (isset($formats[1]) && preg_match('/^(.*?)[#,\.0]+(.*?)$/', $formats[1], $matches)) {
			$result['negativePrefix'] = $matches[1];
			$result['negativeSuffix'] = $matches[2];
		} else {
			$result['negativePrefix'] = $symbols['minusSign'] . $result['positivePrefix'];
			$result['negativeSuffix'] = $result['positiveSuffix'];
		}
		$fmt = $formats[0];
		// multiplier
		if (strpos($fmt, '%') !== false)
			$result['multiplier'] = 100;
		else if (strpos($fmt, '‰') !== false)
			$result['multiplier'] = 1000;
		else
			$result['multiplier'] = 1;
		// decimal part
		if (($pos = strpos($fmt, '.')) !== false) {
			if (($pos2 = strrpos($fmt, '0')) > $pos)
				$result['precision'] = $pos2 - $pos;
			elseif (substr($fmt, $pos+1, 3) == '###')
				$result['precision'] = null;
			$fmt = substr($fmt, 0, $pos);
		} else {
			$result['precision'] = 0;
		}
		// integer part
		$int = str_replace(',', '', $fmt);
		if (($pos = strpos($int, '0')) !== false)
			$result['integerDigits'] = strrpos($int, '0') - $pos + 1;
		else
			$result['integerDigits'] = 0;
		// group sizes
		$groups = str_replace('#','0', $fmt);
		if (($pos = strrpos($groups, ',')) !== false) {
			$result['groupSize1'] = strrpos($groups, '0') - $pos;
			if (($pos2= strrpos(substr($groups, 0, $pos), ',')) !== false)
				$result['groupSize2'] = $pos - $pos2-1;
			else
				$result['groupSize2'] = 0;
		} else {
			$result['groupSize1'] = $result['groupSize2'] = 0;
		}
		return self::$formats[$format] = $result;
	}	
}