<?php

abstract class DateTimeUtil
{
	private static $monthTable = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	private static $yearTable = array(
		1970 => 0,            1960 => -315619200,   1950 => -631152000,
		1940 => -946771200,   1930 => -1262304000,  1920 => -1577923200,
		1910 => -1893456000,  1900 => -2208988800,  1890 => -2524521600,
		1880 => -2840140800,  1870 => -3155673600,  1860 => -3471292800,
		1850 => -3786825600,  1840 => -4102444800,  1830 => -4417977600,
		1820 => -4733596800,  1810 => -5049129600,  1800 => -5364662400,
		1790 => -5680195200,  1780 => -5995814400,  1770 => -6311347200,
		1760 => -6626966400,  1750 => -6942499200,  1740 => -7258118400,
		1730 => -7573651200,  1720 => -7889270400,  1710 => -8204803200,
		1700 => -8520336000,  1690 => -8835868800,  1680 => -9151488000,
		1670 => -9467020800,  1660 => -9782640000,  1650 => -10098172800,
		1640 => -10413792000, 1630 => -10729324800, 1620 => -11044944000,
		1610 => -11360476800, 1600 => -11676096000
	);	
	private static $timezone;
	private static $timezoneOffset;
	
	public static function getTimezone() {
		return self::$timezone;
	}
	
	public static function getTimezoneOffset() {
		return self::$timezoneOffset;
	}
	
	public static function setTimezone($timezone) {
		if (!@timezone_open($timezone))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, '"%s" is not a valid timezone.', array($timezone)));
		$result = @date_default_timezone_set($timezone);
		if ($result === true) {
			self::$timezone = $timezone;
			self::$timezoneOffset = mktime(0, 0, 0, 1, 2, 1970) - gmmktime(0, 0, 0, 1, 2, 1970);
		}
	}
	
    public static function getDayOfWeek($year, $month, $day) {
    	if ($year > 1901 && $year < 2038)
    		return (int)date('w', mktime(0, 0, 0, $month, $day, $year));
        // gregorian correction
        $correction = 0;
        if ($year < 1582 || ($year == 1582 && ($month < 10 || ($month == 10 && $day < 15))))
        	$correction = 3;
        if ($month > 2) {
        	$month -= 2;
        } else {
        	$month += 10;
        	$year--;
        }
        $day  = floor((13 * $month - 1) / 5) + $day + ($year % 100) + floor(($year % 100) / 4);
        $day += floor(($year / 100) / 4) - 2 * floor($year / 100) + 77 + $correction;
        return (int) ($day - 7 * floor($day / 7));
    }
    
    public static function getDaysInMonth($year, $month) {
		return (self::isLeapYear($year) && $month == 2 ? 29 : self::$monthTable[$month-1]);
    }
    
    public static function getWeekNumber($year, $month, $day) {
    	if ($year > 1901 && $year < 2038)
    		return (int)date('W', mktime(0, 0, 0, $month, $day, $year));
    	$dayOfWeek = self::getDayOfWeek($year, $month, $day);
    	$firstDay = self::getDayOfWeek($year, 1, 1);
    	if ($month == 1 && ($firstDay < 1 || $firstDay > 4) && $day < 4) {
    		$firstDay = self::getDayOfWeek($year - 1, 1, 1);
    		$month = 12;
    		$day = 31;
    	} elseif ($month == 12 && (self::getDayOfWeek($year + 1, 1, 1) < 5 && self::getDayOfWeek($year + 1, 1, 1) > 0)) {
    		return 1;
    	}
    	return intval(
    		(self::getDayOfWeek($year, 1, 1) < 5 && self::getDayOfWeek($year, 1, 1) > 0) + 
    		4 * ($month - 1) + (2 * ($month - 1) + ($day - 1) + $firstDay - $dayOfWeek + 6) * 36 / 256
    	);
    }
    
    public static function isLeapYear($year) {
		if (($year % 4) != 0)
			return false;
		if (($year % 400) == 0)
			return true;
		if ($year > 1582 && ($year % 100) == 0)
			return false;
		return true;    	
    }
    
    public static function isDate($date, $format=null, $formatType='php') {
    	if (!is_string($date) && !is_numeric($date) && !is_array($date))
    		return false;
    	// define format
    	if ($format instanceof Locale) {
    		$locale = $format;
    		$format = $locale->getDateInputFormat();
    	} elseif (Locale::isLocale($format)) {
    		$locale = ($format instanceof Locale ? $format : new Locale($format));
    		$format = $locale->getDateInputFormat();
    	} else {
    		$format = ($formatType == 'php' ? self::convertPhpToIsoFormat($format) : $format);
    	}
    	try {
    		$date = DateTimeParser::parseIso($date, $format, false);
    	} catch (Exception $e) {
    		return false;
    	}
    	// fix date parts
    	if (!isset($date['year']))
    		$date['year'] = 1970;
    	if (!isset($date['month']))
    		$date['month'] = 1;
    	if (!isset($date['day']))
    		$date['day'] = 1;
    	if (!isset($date['hour']))
    		$date['hour'] = 12;
    	if (!isset($date['minute']))
    		$date['minute'] = 0;
    	if (!isset($date['second']))
    		$date['second'] = 0;
    	// build timestamp
    	$timestamp = self::makeTime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
    	// validate date parts
        if ($date['year'] != self::date('Y', $timestamp))
            return false;
        if ($date['month'] != self::date('n', $timestamp))
            return false;
        if ($date['day'] != self::date('j', $timestamp))
            return false;
        if ($date['hour'] != self::date('G', $timestamp))
            return false;
        if ($date['minute'] != self::date('i', $timestamp))
            return false;
        if ($date['second'] != self::date('s', $timestamp))
            return false;
        return true;
    }
    
    public static function gmdate($format, $timestamp=null) {
    	return self::date($format, $timestamp, true);
    }
    
    public static function date($format, $timestamp=null, $gmt=false) {
    	// current timestamp
    	if ($timestamp === null)
    		return ($gmt ? @gmdate($format) : @date($format));
    	$timestamp = (int)$timestamp;
    	// 32-bit integer range
    	if (abs($timestamp) <= 0x7FFFFFFF)
    		return ($gmt ? @gmdate($format, $timestamp) : @date($format, $timestamp));
    	$output = '';
    	$original = $timestamp;
    	// fixes for DST and timezone
    	if ($gmt !== true) {
    		$temp = $timestamp;
    		if ($temp > 0) {
	    		while (abs($temp) > 0x7FFFFFFF)
	    			$temp -= (86400 * 23376);
	    		$dst = date('I', $temp);
	    		if ($dst === 1)
	    			$timestamp += 3600;
	    		$timestamp += date('Z', $temp);
    		}
    	}
    	if ($timestamp < 0 && $gmt !== true)
    		$timestamp -= self::$timezoneOffset;
    	// get parts
    	$date = self::getDate($timestamp);
    	$dayOfWeek = null;
    	$weekNumber = null;
    	$helperTime = null;
    	$len = strlen($format);
    	for ($i=0; $i<$len; $i++) {
    		switch ($format[$i]) {
    			// day
    			case 'd' :
    				$output .= ($date['mday'] < 10 ? '0' . $date['mday'] : $date['mday']);
    				break;
    			case 'D' :
    				if ($dayOfWeek === null)
    					$dayOfWeek = self::getDayOfWeek($date['year'], $date['mon'], $date['mday']);
    				$output .= date('D', 86400 * (3 + $dayOfWeek));
    				break;
    			case 'j' :
    				$output .= $date['mday'];
    				break;
    			case 'l' :
    				if ($dayOfWeek === null)
    					$dayOfWeek = self::getDayOfWeek($date['year'], $date['mon'], $date['mday']);
    				$output .= date('l', 86400 * (3 + $dayOfWeek));
    				break;
    			case 'N' :
    				if ($dayOfWeek === null)
    					$dayOfWeek = self::getDayOfWeek($date['year'], $date['mon'], $date['mday']);
					$output .= ($dayOfWeek == 0 ? 7 : $dayOfWeek);
					break;
    			case 'S' :
    				if (($date['mday'] % 10) == 1)
    					$output .= 'st';
    				elseif (($date['mday'] % 10) == 0 && $date['mday'] != 12)
    					$output .= 'nd';
    				elseif (($date['mday'] % 10) == 3)
    					$output .= 'rd';
    				else
    					$output .= 'th';
    				break;
    			case 'w' :
    				if ($dayOfWeek === null)
    					$dayOfWeek = self::getDayOfWeek($date['year'], $date['mon'], $date['mday']);
					$output .= $dayOfWeek;
					break;
    			case 'z' :
    				$output .= $date['yday'];
    				break;
    			// week
    			case 'W' :
    				if ($weekNumber === null)
    					$weekNumber = self::getWeekNumber($date['year'], $date['mon'], $date['mday']);
    				$output .= $weekNumber;
    				break;
    			// month
    			case 'F' :
    				$output .= date('F', mktime(0, 0, 0, $date['mon'], 2, 1971));
    				break;
    			case 'm' :
    				$output .= ($date['mon'] < 10 ? '0' . $date['mon'] : $date['mon']);
    				break;
    			case 'M' :
    				$output .= date('M', mktime(0, 0, 0, $date['mon'], 2, 1971));
    				break;
    			case 'n' :
    				$output .= $date['mon'];
    				break;
    			case 't' :
    				$output .= self::getDaysInMonth($date['year'], $date['mon']);
    				break;
    			// year
    			case 'L' :
    				$output .= (self::isLeapYear($date['year']) ? '1' : '0');
    				break;
    			case 'o' :
    				if ($weekNumber === null)
    					$weekNumber = self::getWeekNumber($date['year'], $date['mon'], $date['mday']);
					if ($weekNumber > 50 && $date['mon'] == 1)
						$output .= ($date['year'] - 1);
					else
						$output .= $date['year'];
					break;
    			case 'Y' :
    				$output .= $date['year'];
    				break;
    			case 'y' :
    				$output .= substr($date['year'], strlen($date['year'])-2, 2);
    				break;
    			// time
    			case 'a' :
    				$output .= ($date['hours'] >= 12 ? 'am' : 'pm');
    				break;
    			case 'A' :
    				$output .= ($date['hours'] >= 12 ? 'AM' : 'PM');
    				break;
    			case 'B' :
    				$daySeconds = (($date['hours'] * 3600) + ($date['minutes'] * 60) + $date['seconds']);
    				if ($gmt)
    					$daySeconds += 3600;
    				$output .= (int)(($daySeconds % 86400) / 86.4);
    				break;
    			case 'g' :
    				if ($date['hours'] > 12)
    					$hours = $date['hours'] - 12;
    				elseif ($date['hours'] == 0)
    					$hours = 12;
    				else
    					$hours = $date['hours'];
    				$output .= $hours;
    				break;
    			case 'G' :
    				$output .= $date['hours'];
    				break;
    			case 'h' :
    				if ($date['hours'] > 12)
    					$hours = $date['hours'] - 12;
    				elseif ($date['hours'] == 0)
    					$hours = 12;
    				else
    					$hours = $date['hours'];
    				$output .= ($hours < 10 ? '0' . $hours : $hours);
    				break;
    			case 'H' :
    				$output .= ($date['hours'] < 10 ? '0' . $date['hours'] : $date['hours']);
    				break;
    			case 'i' :
    				$output .= ($date['minutes'] < 10 ? '0' . $date['minutes'] : $date['minutes']);
    				break;
    			case 's' :
    				$output .= ($date['seconds'] < 10 ? '0' . $date['seconds'] : $date['seconds']);
    				break;
				// timezone
    			case 'e' :
    				if ($helperTime === null)
    					$helperTime = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], 2000);
    				$output .= ($gmt ? gmdate('e', $helperTime) : date('e', $helperTime));
    				break;
    			case 'I' :
    				if ($helperTime === null)
    					$helperTime = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], 2000);
					$output .= ($gmt ? gmdate('I', $helperTime) : date('I', $helperTime));
					break;
    			case 'O' :
    				$offset = ($gmt ? 0 : self::$timezoneOffset);
					$output .= sprintf('%s%04d', ($offset <= 0) ? '+' : '-', abs($offset) / 36);
					break;
    			case 'P' :
    				$offset = ($gmt ? 0 : self::$timezoneOffset);
    				$gmtStr = sprintf('%s%04d', ($offset <= 0) ? '+' : '-', abs($offset) / 36);
    				$output .= substr($gmtStr, 0, 3) . ':' . substr($gmtStr, 3);
    				break;
    			case 'T' :
    				if ($helperTime === null)
    					$helperTime = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], 2000);
					$output .= ($gmt ? gmdate('T', $helperTime) : date('T', $helperTime));
					break;
    			case 'Z' :
    				$output .= ($gmt ? 0 : -self::$timezoneOffset);
    				break;
    			case 'c' :
    				$offset = ($gmt ? 0 : self::$timezoneOffset);
                    $gmtStr = sprintf('%s%04d', ($offset <= 0) ? '+' : '-', abs($offset) / 36);
                    $gmtStr = substr($gmtStr, 0, 3) . ':' . substr($gmtStr, 3);
                    $output .= 
                    	$date['year'] . '-' . 
                    	($date['mon'] < 10 ? '0' . $date['mon']     : $date['mon']) . '-' . 
                    	($date['mday'] < 10 ? '0' . $date['mday']    : $date['mday']) . 'T' . 
                    	($date['hours'] < 10 ? '0' . $date['hours']   : $date['hours']) . ':' . 
                    	($date['minutes'] < 10 ? '0' . $date['minutes'] : $date['minutes']) . ':' . 
                    	($date['seconds'] < 10 ? '0' . $date['seconds'] : $date['seconds']) . 
                    	$gmtStr;
                    break;
    			case 'r' :
    				$offset = ($gmt ? 0 : self::$timezoneOffset);
    				if ($dayOfWeek === null)
    					$dayOfWeek = self::getDayOfWeek($date['year'], $date['mon'], $date['mday']);    				
                    $gmtStr = sprintf('%s%04d', ($offset <= 0) ? '+' : '-', abs($offset) / 36);
                    $output .= 
                    	gmdate('D', 86400 * (3 + $dayOfWeek)) . ', ' . 
                    	($date['mday'] < 10 ? '0' . $date['mday']    : $date['mday']) . ' ' . 
                    	date('M', mktime(0, 0, 0, $date['mon'], 2, 1971)) . ' ' . 
                    	$date['year'] . ' ' . 
                    	($date['hours'] < 10 ? '0' . $date['hours']   : $date['hours']) . ':' . 
                    	($date['minutes'] < 10 ? '0' . $date['minutes'] : $date['minutes']) . ':' . 
                    	($date['seconds'] < 10 ? '0' . $date['seconds'] : $date['seconds']) . ' ' . 
                    	$gmtStr;
                    break;
    			case 'U' :
    				$output .= $original;
    				break;
    			case '\\' :
    				$i++;
    				if ($i < $len)
    					$output .= $format[$i];
    				break;
    			default :
    				$output .= $format[$i];
    				break;    			
    		}
    	}
    	return (string)$output;
    }
    
    public static function getDate($timestamp=null) {
    	// current timestamp
    	if ($timestamp === null)
    		return getdate();
    	// 32-bit integer range
    	if (abs($timestamp) <= 0x7FFFFFFF)
    		return getdate((int)$timestamp);
    	$original = $timestamp;
    	$day = 0;
    	$month = 0;
    	// gregorian correction
    	if ($timestamp < -12219321600)
    		$timestamp -= 864000;
    	// negative timestamp
    	if ($timestamp < 0) {
    		// year
    		$sec = 0;
    		$act = 1970;
    		foreach (self::$yearTable as $year => $seconds) {
    			if ($timestamp >= $seconds) {
    				$i = $act;
    				break;
    			}
    			$sec = $seconds;
    			$act = $year;
    		}
    		$timestamp -= $sec;
    		if (!isset($i))
    			$i = $act;
    		do {
    			--$i;
    			$save = $timestamp;
    			$leap = self::isLeapYear($i);
    			$timestamp += (31536000 + ($leap ? 86400 : 0));
    			if ($timestamp >= 0) {
    				$year = $i;
    				break;
    			}
    		} while ($timestamp < 0);
    		// seconds per year
    		$secondsPerYear = 86400 * ($leap ? 366 : 365) + $save;
    		// month
    		$timestamp = $save;
    		for ($i = 12; --$i >= 0; ) {
    			$save = $timestamp;
    			$timestamp += ($leap && $i == 1 ? 29 : self::$monthTable[$i]) * 86400;
    			if ($timestamp >= 0) {
    				$month = $i+1;
    				$numDays = ($leap && $i == 1 ? 29 : self::$monthTable[$i]);
    				break;
    			}    			
    		}
    		// day and hours
    		$timestamp = $save;
    		$day = $numDays + ceil(($timestamp + 1) / 86400);
    		$timestamp += ($numDays - $day + 1) * 86400;
    		$hours = floor($timestamp / 3600);
    	} else {
			// year
			for ($i = 1970; ; $i++) {
				$save = $timestamp;
				$leap = self::isLeapYear($i);
				$timestamp -= (31536000 + ($leap ? 86400 : 0));
				if ($timestamp < 0) {
					$year = $i;
					break;
				}
			}
			// seconds per year
			$secondsPerYear = $save;
			// month
			$timestamp = $save;
			for ($i = 0; $i <= 11; $i++) {
				$save = $timestamp;
				$timestamp -= ($leap && $i == 1 ? 29 : self::$monthTable[$i]) * 86400;
				if ($timestamp < 0) {
					$month = $i+1;
					$numDays = ($leap && $i == 1 ? 29 : self::$monthTable[$i]);
					break;
				}
			}
			// day and hours
			$timestamp = $save;
			$day = ceil(($timestamp + 1) / 86400);
			$timestamp = $timestamp - ($numDays - 1) * 86400;
			$hours = floor($timestamp / 3600);
    	}
    	// minutes and seconds
    	$timestamp -= ($hours * 3600);
    	$minutes = floor($timestamp / 60);
    	$seconds = $timestamp - ($minutes * 60);
    	// final result
    	$dayOfWeek = self::getDayOfWeek($year, $month, $day);
    	return array(
    		'seconds' => $seconds,
    		'minutes' => $minutes,
    		'hours' => $hours,
    		'mday' => $day,
    		'wday' => $dayOfWeek,
    		'mon' => $month,
    		'year' => $year,
    		'yday' => floor($secondsPerYear / 86400),
    		'weekday' => gmdate('l', 86400 * (3 + $dayOfWeek)),
    		'month' => gmdate('F', mktime(0, 0, 0, $month, 1, 1971)),
    		0 => $original
    	);
    }
    
    public static function getTime($date, $gmt=false) {
    	$dateTime = new DateTime($date, new DateTimeZone(DateTimeUtil::getTimezone()));
    	$timestamp = $dateTime->format('U');
    	if ($gmt)
    		$timestamp += DateTimeUtil::getTimezoneOffset();
    	return $timestamp;
    }
    
    public static function makeTime($hour, $minute, $second, $month, $day, $year, $gmt=false) {
    	// 32-bit integer range
    	if ($year > 1901 && $year < 2038) {
    		if ($gmt)
    			return @gmmktime($hour, $minute, $second, $month, $day, $year);
    		else
    			return @mktime($hour, $minute, $second, $month, $day, $year);
    	}
    	// add GMT offset
    	if ($gmt !== true)
    		$second += self::$timezoneOffset;
    	// convert to int
    	$day = intval($day);
    	$month = intval($month);
    	$year = intval($year);
    	// fix month if necessary
    	if ($month > 12) {
    		$overlap = floor($month / 12);
    		$year += $overlap;
    		$month -= $overlap * 12;
    	} else {
    		$overlap = ceil((1 - $month) / 12);
    		$year -= $overlap;
    		$month += $overlap * 12;
    	}
    	$time = 0;
    	// after unix epoch
    	if ($year >= 1970) {
    		for ($i = 1970; $i <= $year; $i++) {
    			$leap = self::isLeapYear($i);
    			if ($i < $year) {
    				$time += ($leap ? 366 : 365);
    			} else {
    				for ($j = 0; $j < ($month - 1); $j++)
    					$time += ($leap && $j == 1 ? 29 : self::$monthTable[$j]);
    			}
    		}
    		$time += $day - 1;
    		$time = (($time * 86400) + ($hour * 3600) + ($minute * 60) + $second);
    	} 
    	// before unix epoch
    	else {
    		for ($i = 1969; $i >= $year; $i--) {
    			$leap = self::isLeapYear($i);
    			if ($i > $year) {
    				$time += ($leap ? 366 : 365);
    			} else {
    				for ($j = 11; $j > ($month - 1); $j--)
    					$time += ($leap && $j == 1 ? 29 : self::$monthTable[$j]);
    			}
    		}
    		$time += (self::$monthTable[$month - 1] - $day);
    		$time = -(($time * 86400) + (86400 - (($hour * 3600) + ($minute * 60) + $second)));
    	}
    	return $time;
    }    
    
    public static function convertPhpToIsoFormat($format) {
        $convert = array(
        	'd' => 'dd'  , 'D' => 'EEE'  , 'j' => 'd'   , 'l' => 'EEEE', 'N' => 'eee' , 'S' => 'SS'  ,
			'w' => 'e'   , 'z' => 'D'   , 'W' => 'ww'  , 'F' => 'MMMM', 'm' => 'MM'  , 'M' => 'MMM' ,
			'n' => 'M'   , 't' => 'ddd' , 'L' => 'l'   , 'o' => 'YYYY', 'Y' => 'yyyy', 'y' => 'yy'  ,
			'a' => 'a'   , 'A' => 'a'   , 'B' => 'B'   , 'g' => 'h'   , 'G' => 'H'   , 'h' => 'hh'  ,
			'H' => 'HH'  , 'i' => 'mm'  , 's' => 'ss'  , 'e' => 'zzzz', 'I' => 'I'   , 'O' => 'Z'   ,
			'P' => 'ZZZZ', 'T' => 'z'   , 'Z' => 'X'   , 'c' => 'yyyy-MM-ddTHH:mm:ssZZZZ',
			'r' => 'r'   , 'U' => 'U'
		);
        $values = str_split($format);
        foreach ($values as $key => $value) {
            if (isset($convert[$value]))
                $values[$key] = $convert[$value];
        }
        return join($values);
    }    
    
	/*
    public static function isValidDate($year, $month, $day) {
		return checkdate($month, $day, $year);		
	}
	
	public static function isValidTime($h, $m, $s, $h24=true) {
		if ($h24 && ($h < 0 || $h > 23) || !$h24 && ($h < 1 || $h > 12)) 
			return false;
		if ($m > 59 || $m < 0) 
			return false;
		if ($s > 59 || $s < 0) 
			return false;
		return true;		
	}
	
	public static function makeDateTime($h, $m, $s, $year=false, $month=false, $day=false, $gmt=false) {
		$timestamp = self::makeTimestamp($h, $m, $s, $year, $month, $day);
		return new DateTime("@{$timestamp}");
	}
	
	public static function makeTimestamp($h, $m, $s, $year=false, $month=false, $day=false, $gmt=false) {
		if ($year === false)
			return ($gmt ? gmmktime($h, $m, $s) : mktime($h, $m, $s));
		return ($gmt ? gmmktime($h, $m, $s, $month, $day, $year) : mktime($h, $m, $s, $month, $day, $year));
	}
	*/
}
DateTimeUtil::setTimezone(@date_default_timezone_get());