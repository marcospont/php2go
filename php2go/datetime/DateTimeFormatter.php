<?php

abstract class DateTimeFormatter
{
	private static $formats = array();
	private static $formatters = array(
		'G' => 'formatEra',
		'GG' => 'formatEra',
		'GGG' => 'formatEra',
		'GGGG' => 'formatEra',
		'GGGGG' => 'formatEra',	
		'y' => 'formatYear',
		'yy' => 'formatYear',
		'yyy' => 'formatYear',
		'yyyy' => 'formatYear',
		'yyyyy' => 'formatYear',
		'Y' => 'formatYear8601',
		'YY' => 'formatYear8601',
		'YYY' => 'formatYear8601',
		'YYYY' => 'formatYear8601',
		'YYYYY' => 'formatYear8601',
		'l' => 'formatLeapYear',		
		'M' => 'formatMonth',	
		'MM' => 'formatMonth',
		'MMM' => 'formatMonth',
		'MMMM' => 'formatMonth',
		'MMMMM' => 'formatMonth',
		'w' => 'formatWeekInYear',
		'ww' => 'formatWeekInYear',	
		'W' => 'formatWeekInMonth',
		'WW' => 'formatWeekInMonth',
		'D' => 'formatDayInYear',
		'DD' => 'formatDayInYear',
		'DDD' => 'formatDayInYear',
		'd' => 'formatDay',
		'dd' => 'formatDay',
		'SS' => 'formatDaySuffix',
		'ddd' => 'formatDaysInMonth',		
		'F' => 'formatWeekDayInMonth',
		'E' => 'formatWeekDayName',
		'EE' => 'formatWeekDayName',
		'EEE' => 'formatWeekDayName',
		'EEEE' => 'formatWeekDayName',
		'EEEEE' => 'formatWeekDayName',
		'e' => 'formatWeekDay',
		'ee' => 'formatWeekDay',
		'a' => 'formatDayPeriod',
		'B' => 'formatSwatch',
		'h' => 'formatHour12',
		'hh' => 'formatHour12',
		'H' => 'formatHour24',
		'HH' => 'formatHour24',
		'm' => 'formatMinute',
		'mm' => 'formatMinute',
		's' => 'formatSecond',
		'ss' => 'formatSecond',
		'I' => 'formatDST',
		'z' => 'formatTimeZone',
		'zz' => 'formatTimeZone',
		'zzz' => 'formatTimeZone',
		'zzzz' => 'formatTimeZone',
		'Z' => 'formatTimeZoneDiff',
		'ZZ' => 'formatTimeZoneDiff',
		'ZZZ' => 'formatTimeZoneDiff',
		'ZZZZ' => 'formatTimeZoneDiff',
		'c' => 'formatIso8601',
		'r' => 'formatRFC2822',
		'U' => 'formatTimestamp'
	);
	private static $locale;
	
	public static function format($date, $format, $locale=null, $gmt=false) {
		return self::formatIso($date, DateTimeUtil::convertPhpToIsoFormat($format), $locale=null, $gmt=false);
	}
	
	public static function formatIso($date, $format, $locale=null, $gmt=false) {
		// define timestamp
		if (is_string($date)) {
			if (is_numeric($date)) {
				$timestamp = (int)$date;
				($gmt) && ($timestamp += DateTimeUtil::getTimezoneOffset());
			} else {
				$timestamp = DateTimeUtil::getTime($date, $gmt);
			}
		} else {
			$timestamp = $date;
			($gmt) && ($timestamp += DateTimeUtil::getTimezoneOffset());
		}		
		// define locale
		if ($locale === null)
			self::$locale = Php2Go::app()->getLocale();
		else
			self::$locale = Locale::findLocale($locale);
		// get date parts
		$date = DateTimeUtil::getDate($timestamp);
		// read and convert tokens		
		$tokens = self::parseFormat($format);		
		foreach($tokens as &$token) {
			if (is_array($token))
				$token = call_user_func(array(__CLASS__, $token[0]), $token[1], $date, $gmt);
		}
		return implode('',$tokens);
		
	}
	
	private static function parseFormat($format) {
		if (isset(self::$formats[$format]))
			return self::$formats[$format];
		$tokens = array();
		$len = strlen($format);
		$isLiteral = false;
		$literal = '';
		for ($i=0; $i<$len; ++$i) {
			$c = $format[$i];
			if ($c === "'") {
				if ($i < $n-1 && $format[$i+1] === "'") {
					$tokens[] = "'";
					$i++;
				} elseif ($isLiteral) {
					$tokens[] = $literal;
					$literal = '';
					$isLiteral = false;
				} else {
					$isLiteral = true;
					$literal = '';
				}
			} elseif ($isLiteral) {
				$literal .= $c;
			} else {
				for ($j=$i+1; $j<$len; ++$j) {
					if ($format[$j] !== $c)
						break;
				}
				$p = str_repeat($c, $j-$i);
				if (isset(self::$formatters[$p]))
					$tokens[]= array(self::$formatters[$p], $p);
				else
					$tokens[] = $p;
				$i = $j-1;
			}
		}
		if ($literal !== '')
			$tokens[] = $literal;
		return self::$formats[$format] = $tokens;
	}
	
	private static function formatEra($format, $date) {
		$method = ($date['year'] > 0 ? 'getDC' : 'getAC');
		switch($format) {
			case 'G' :
			case 'GG' :
			case 'GGG' :
				return self::$locale->{$method}('abbreviated');
			case 'GGGG' :
				return self::$locale->{$method}('wide');
			case 'GGGGG' :
				return self::$locale->{$method}('narrow');
		}
	}	
	
	private static function formatYear($format, $date) {
		return str_pad($date['year'], strlen($format), '0', STR_PAD_LEFT);
	}
	
	private static function formatYear8601($format, $date) {
		$year = $date['year'];
		$weekNumber = DateTimeUtil::getWeekNumber($date['year'], $date['mon'], $date['mday']);
		if ($weekNumber > 50 && $date['mon'] == 1)
			$year--;
		return str_pad($year, strlen($format), '0', STR_PAD_LEFT);
	}
	
	private static function formatLeapYear($format, $date) {
		return (DateTimeUtil::isLeapYear($date['year']) ? '1' : '0');
	}

	private static function formatMonth($format, $date) {
		$month = $date['mon'];
		switch ($format) {
			case 'M' :
				return $month;
			case 'MM' :
				return ($month < 10 ? '0' . $month : $month);
			case 'MMM' :
				return self::$locale->getMonth($month, 'abbreviated');
			case 'MMMM' :
				return self::$locale->getMonth($month, 'wide');
			case 'MMMMM' :
				return self::$locale->getMonth($month,'narrow');
		}
	}
	
	private static function formatWeekInYear($format, $date) {
		$weekNumber = DateTimeUtil::getWeekNumber($date['year'], $date['mon'], $date['mday']);
		if ($format === 'w')
			return $weekNumber;
		else
			return ($weekNumber < 0 ? $weekNumber . '0' : $weekNumber);
	}
	
	private static function formatWeekInMonth($format, $date) {
		$weekNumber = DateTimeUtil::getWeekNumber($date['year'], $date['mon'], $date['mday']);
		$firstWeekNumber = DateTimeUtil::getWeekNumber($date['year'], $date['mon'], 1);
		return ($weekNumber - $firstWeekNumber);
	}	

	private static function formatDay($format, $date) {
		$day = $date['mday'];
		switch ($format) {
			case 'd' :
				return $day;
			case 'dd' :
				return ($day < 10 ? '0' . $day : $day);
		}
	}
	
	private static function formatDaySuffix($format, $date) {
		if (self::$locale->getLanguage() == 'en') {
			if (($date['mday'] % 10) == 1)
				return 'st';
			elseif (($date['mday'] % 10) == 0 && $date['mday'] != 12)
				return 'nd';
			elseif (($date['mday'] % 10) == 3)
				return 'rd';
			else
				return 'th';			
		}
		return '';
	}
	
	private static function formatDaysInMonth($format, $date) {
		return DateTimeUtil::getDaysInMonth($date['year'], $date['mon']);
	}

	private static function formatDayInYear($format, $date) {
		if ($format === 'D')
			return $date['yday'];
		elseif ($format === 'DD')
			return ($date['yday'] < 10 ? '0' . $date['yday'] : $date['yday']);
		else
			return ($date['yday'] < 100 ? str_pad($date['yday'], 3, '0', STR_PAD_LEFT) : $date['yday']);
	}
	
	private static function formatWeekDayInMonth($format, $date) {
		return (int)(($date['mday']+6) / 7);
	}

	private static function formatWeekDayName($format, $date) {
		$day = $date['wday'];
		switch($format) {
			case 'E':
			case 'EEEEE':
				$weekDayName = self::$locale->getWeekDay($date['wday'], 'narrow');
				return $weekDayName[0];
			case 'EE':
				return self::$locale->getWeekDay($date['wday'], 'narrow');
			case 'EEE':				
				return self::$locale->getWeekDay($date['wday'], 'abbreviated');
			case 'EEEE':
				return self::$locale->getWeekDay($date['wday'], 'wide');
		}
	}
	
	private static function formatWeekDay($format, $date) {
		if ($format === 'e')
			return $date['wday'];
		else
			return '0' . $date['wday'];
	}

	private static function formatDayPeriod($format, $date) {
		if (intval($date['hours'] / 12))
			return self::$locale->getPM();
		else
			return self::$locale->getAM();
	}
	
	private static function formatSwatch($format, $date, $gmt) {
		return DateTimeUtil::date('B', $date[0], $gmt);
	}

	private static function formatHour12($format, $date) {
		$hour = $date['hours'];
		$hour = ($hour == 12 || $hour == 0 ? 12 : $hour % 12);
		if ($format === 'h')
			return $hour;
		else
			return ($hour < 10 ? '0' . $hour : $hour);
	}

	private static function formatHour24($format, $date) {
		$hour = $date['hours'];
		if ($format === 'H')
			return $hour;
		else
			return ($hour < 10 ? '0' . $hour : $hour);
	}

	private static function formatMinute($format, $date) {
		$minutes = $date['minutes'];
		if ($format === 'm')
			return $minutes;
		else
			return ($minutes < 10 ? '0' . $minutes : $minutes);
	}

	private static function formatSecond($format, $date) {
		$seconds = $date['seconds'];
		if ($format === 's')
			return $seconds;
		else
			return ($seconds < 10 ? '0' . $seconds : $seconds);
	}
	
	private static function formatDST($format, $date, $gmt) {
		return DateTimeUtil::date('I', $date[0], $gmt);
	}
	
	private static function formatTimeZone($format, $date, $gmt) {
		if ($format === 'zzzz')
			return DateTimeUtil::date('e', $date[0], $gmt);
		return DateTimeUtil::date('T', $date[0], $gmt);
	}
	
	private static function formatTimeZoneDiff($format, $date, $gmt) {
		if ($format === 'ZZZZ')
			return DateTimeUtil::date('P', $date[0], $gmt);
		return DateTimeUtil::date('O', $date[0], $gmt);
	}
	
	private static function formatIso8601($format, $date, $gmt) {
		$timestamp = ($gmt ? $date[0] - DateTimeUtil::getTimezoneOffset() : $date[0]);
		return self::formatIso($timestamp, 'yyyy-MM-ddTHH:mm:ssZZZZ', null, $gmt);
	}
	
	private static function formatRFC2822($format, $date, $gmt) {
		$timestamp = ($gmt ? $date[0] - DateTimeUtil::getTimezoneOffset() : $date[0]);
		return self::formatIso($timestamp, 'EEE, dd MMM yyyy HH:mm:ss ZZZ', null, $gmt);
	}
	
	private static function formatTimestamp($format, $date) {
		return $date[0];
	}
}