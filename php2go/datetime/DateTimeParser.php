<?php

abstract class DateTimeParser
{
	public static function parse($value, $format, $timestamp=true) {
		return self::parseIso($value, DateTimeUtil::convertPhpToIsoFormat($format));
	}
	
	public static function parseIso($value, $format, $timestamp=true) {
		$i = 0;
		$result = array();
		$format = utf8_encode($format);
		$len = strlen($value);
		// parse tokens
		$tokens = self::tokenize($format);
		foreach ($tokens as $token) {
			switch ($token) {
				case 'yyyy' :
					if (($result['year'] = self::parseNumber($value, $i, 4, 4)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += 4;
					break;
				case 'yy' :
					if (($result['year'] = self::parseNumber($value, $i, 1, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += strlen($result['year']);
					break;
				case 'MM' :
					if (($result['month'] = self::parseNumber($value, $i, 2, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += 2;
					break;
				case 'M' :
					if (($result['month'] = self::parseNumber($value, $i, 1, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += strlen($result['month']);
					break;
				case 'dd' :
					if (($result['day'] = self::parseNumber($value, $i, 2, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += 2;
					break;
				case 'd' :
					if (($result['day'] = self::parseNumber($value, $i, 1, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += strlen($result['day']);
					break;
				case 'h' :
				case 'H' :
					if (($result['hour'] = self::parseNumber($value, $i, 1, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += strlen($result['hour']);
					break;
				case 'hh' :
				case 'HH' :
					if (($result['hour'] = self::parseNumber($value, $i, 2, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += 2;
					break;
				case 'm' :
					if (($result['minute'] = self::parseNumber($value, $i, 1, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += strlen($result['minute']);
					break;
				case 'mm' :
					if (($result['minute'] = self::parseNumber($value, $i, 2, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += 2;
					break;
				case 's' :
					if (($result['second'] = self::parseNumber($value, $i, 1, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += strlen($result['second']);
					break;
				case 'ss' :
					if (($result['second'] = self::parseNumber($value, $i, 2, 2)) === false)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += 2;
					break;
				default :
					$tn = strlen($token);
					if ($i >= $len || substr($value, $i, $tn) !== $token)
						throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
					$i += $tn;
					break;
			}
		}
		// incomplete parse
		if ($i < $len)
			throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
		// 2-digit year
		if ($result['year'] >= 0 && $result['year'] < 100) {
			if ($result['year'] < 70)
				$result['year'] += 100;
			$result['year'] += 1900;
		}
		// should we return a timestamp?
		if ($timestamp) {
			// set missing parts
	    	if (!isset($result['year']))
	    		$result['year'] = (int)date('Y');
	    	if (!isset($result['month']))
	    		$result['month'] = (int)date('n');
	    	if (!isset($result['day']))
	    		$result['day'] = (int)date('j');
	    	if (!isset($result['hour']))
	    		$result['hour'] = (int)date('H');
	    	if (!isset($result['minute']))
	    		$result['minute'] = (int)date('i');
	    	if (!isset($result['second']))
	    		$result['second'] = (int)date('s');
	    	$timestamp = DateTimeUtil::makeTime($result['hour'], $result['minute'], $result['second'], $result['month'], $result['day'], $result['year']);
	    	// validate parts
	        if ($result['year'] != DateTimeUtil::date('Y', $timestamp))
	            throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
	        if ($result['month'] != DateTimeUtil::date('n', $timestamp))
	            throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
	        if ($result['day'] != DateTimeUtil::date('j', $timestamp))
	            throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
	        if ($result['hour'] != DateTimeUtil::date('G', $timestamp))
	            throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
	        if ($result['minute'] != DateTimeUtil::date('i', $timestamp))
	            throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
	        if ($result['second'] != DateTimeUtil::date('s', $timestamp))
	            throw new DateTimeParserException(__(PHP2GO_LANG_DOMAIN, 'Invalid datetime.'));
			return $timestamp;
		}
		return $result;
	}
	
	private static function tokenize($format) {
		if(!($len = strlen($format)))
			return array();
		$tokens = array();
		for ($c0=$format[0],$start=0,$i=1; $i<$len; ++$i) {
			if (($c = $format[$i]) !== $c0) {
				$tokens[] = substr($format, $start, $i-$start);
				$c0 = $c;
				$start = $i;
			}
		}
		$tokens[] = substr($format, $start, $len-$start);
		return $tokens;		
	}
	
	private static function parseNumber($value, $offset, $min, $max) {
		for ($len=$max; $len>=$min; --$len) {
			$v = substr($value, $offset, $len);
			if (strlen($v) == $len && ctype_digit($v))
				return $v;
		}
		return false;		
	}
}

class DateTimeParserException extends Exception 
{	
}