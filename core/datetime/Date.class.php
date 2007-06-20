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
 * Date format defined in the global configuration settings
 */
define('DATE_FORMAT_LOCAL', 1);
/**
 * Date format according to RFC822
 */
define('DATE_FORMAT_RFC822', 2);
/**
 * Date format according to ISO8601
 */
define('DATE_FORMAT_ISO8601', 3);
/**
 * Custom date format
 */
define('DATE_FORMAT_CUSTOM', 4);

/**
 * Collection of static methods to handle with dates
 *
 * This class contains a collection of methods to perform transformations
 * and calculations on dates. Most of the methods expect a date written
 * in one of the following formats:
 * # EURO : d/m/Y
 * # US : Y/m/d
 * # SQL : Y-m-d
 *
 * @package datetime
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Date extends PHP2Go
{
	/**
	 * Check if a given date is a valid date
	 *
	 * @param string $date Date
	 * @return bool
	 * @static
	 */
	function isValid($date) {
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
		} else if (Date::isUsDate($date, $regs) || Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
		} else {
			return FALSE;
		}
		if ($year < 0 || $year > 9999) {
			return FALSE;
		} else {
			return (checkdate($month, $day, $year));
		}
	}

	/**
	 * Check if the given parameter is a valid timezone
	 *
	 * @param string $tz Timezone
	 * @return bool
	 * @static
	 */
	function isValidTZ($tz) {
		return preg_match("/^(((\+|\-)[0-9]{2}\:[0-9]{2})|(UT|GMT|EST|EDT|CST|CDT|MST|MDT|PST|PDT)|([A-IK-Y]{1}))$/", $tz);
	}

	/**
	 * Check if a given date is in the <b>EURO</b> format
	 *
	 * The parsed date parts (day, month, year, hour, minute, second)
	 * can be returned through the second argument, passed by reference.
	 *
	 * @param string $date Date
	 * @param array &$regs Used to return the parsed date parts
	 * @return bool
	 * @static
	 */
	function isEuroDate($date, &$regs) {
		$date = trim($date);
		if (ereg("^([0-9]{1,2})(/|\-|\.)([0-9]{1,2})(/|\-|\.)([0-9]{4})([[:space:]]([0-9]{1,2}):([0-9]{1,2}):?([0-9]{1,2})?)?$", $date, $matches)) {
			$regs = array(
				$matches[0],
				$matches[1], $matches[3], $matches[5],
				$matches[7], $matches[8], $matches[9]
			);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Check if a given date is in the <b>US</b> format
	 *
	 * The parsed date parts (day, month, year, hour, minute, second)
	 * can be returned through the second argument, passed by reference.
	 *
	 * @param string $date Date
	 * @param array &$regs Used to return the parsed date parts
	 * @return bool
	 * @static
	 */
	function isUsDate($date, &$regs) {
		$date = trim($date);
		if (ereg("^([0-9]{4})(/|\-|\.)([0-9]{1,2})(/|\-|\.)([0-9]{1,2})([[:space:]]([0-9]{1,2}):([0-9]{1,2}):?([0-9]{1,2})?)?$", $date, $matches)) {
			$regs = array(
				$matches[0],
				$matches[1], $matches[3], $matches[5],
				$matches[7], $matches[8], $matches[9]
			);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Check if a given date is in the <b>SQL</b> format
	 *
	 * The parsed date parts (day, month, year, hour, minute, second)
	 * can be returned through the second argument, passed by reference.
	 *
	 * @param string $date Date
	 * @param array &$regs Used to return the parsed date parts
	 * @return bool
	 * @static
	 */
	function isSqlDate($date, &$regs) {
		$date = trim($date);
		if (ereg("^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})([[:space:]]([0-9]{1,2}):([0-9]{1,2}):?([0-9]{1,2})?)?$", $date, $matches)) {
			$regs = array(
				$matches[0],
				$matches[1], $matches[2], $matches[3],
				$matches[5], $matches[6], $matches[7]
			);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Check if a given date is in the future
	 *
	 * @param string $date Date
	 * @return bool
	 * @static
	 */
	function isFuture($date) {
		$daysFrom = Date::dateToDays($date);
		$daysTo = Date::dateToDays();
		return ($daysFrom > $daysTo);
	}

	/**
	 * Check if a given date is in the past
	 *
	 * @param string $date Date
	 * @return bool
	 * @static
	 */
	function isPast($date) {
		$daysFrom = Date::dateToDays($date);
		$daysTo = Date::dateToDays();
		return ($daysTo > $daysFrom);
	}

	/**
	 * Get tomorrow's date
	 *
	 * @return string
	 * @static
	 */
	function tomorrow() {
		return Date::nextDay();
	}

	/**
	 * Get the next date after a given date
	 *
	 * If $date is missing, today's date will be used.
	 * The output date will be in the same format of the input date.
	 *
	 * @param string $date Date
	 * @return string Next date
	 * @static
	 */
	function nextDay($date=NULL) {
		if (is_null($date)) {
			$date = Date::localDate();
		}
		return Date::futureDate($date, 1);
	}

	/**
	 * Build a future date
	 *
	 * The output date will be in the same format of the input date.
	 *
	 * @param string $date Date
	 * @param int $days Number of days to add
	 * @param int $months Number of months to add
	 * @param int $years Number of years to add
	 * @return string Result date
	 * @static
	 */
	function futureDate($date, $days = 0, $months = 0, $years = 0) {
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
			$dateFormat = "EURO";
		} else if (Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
			$dateFormat = "SQL";
		} else if (Date::isUsDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
			$dateFormat = "US";
		} else {
			return NULL;
		}
		$daysFrom = Date::dateToDays($date);
		$daysInc = 0;
		$years = intval($years);
		for ($i = 1; $i <= $years; $i++) {
			$year++;
			$daysInc += (Date::isLeapYear($year)) ? 366 : 365;
		}
		$months = intval($months);
		for ($i = 1; $i <= $months; $i++) {
			$mTemp = $i % 12 - 1;
			$yTemp = intval($i / 12);
			if (($month + $mTemp) > 12) {
				$yTemp++;
				$mTemp = ($month + $mTemp) - 12;
			} else {
				$mTemp = $month + $mTemp;
			}
			$daysInc += Date::daysInMonth($mTemp, $year + $yTemp);
		}
		$daysInc += intval($days);
		return Date::daysToDate($daysFrom + $daysInc, $dateFormat);
	}

	/**
	 * Get yesterday's date
	 *
	 * @return string
	 * @static
	 */
	function yesterday() {
		return Date::prevDay();
	}

	/**
	 * Get the previous date of a given date
	 *
	 * If $date is missing, today's date will be used.
	 * The output date will be in the same format of the input date.
	 *
	 * @param string $date Date
	 * @return string Previous date
	 * @static
	 */
	function prevDay($date=NULL) {
		if (is_null($date)) {
			$date = Date::localDate();
		}
		return Date::pastDate($date, 1);
	}

	/**
	 * Calculate a date in the past
	 *
	 * The output date will be in the same format of the input date.
	 *
	 * @param string $date Date
	 * @param int $days Number of days to subtract
	 * @param int $months Number of months to subtract
	 * @param int $years Number of years to subtract
	 * @return date Past date
	 * @static
	 */
	function pastDate($date, $days=0, $months=0, $years=0) {
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
			$dateFormat = 'EURO';
		} else if (Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
			$dateFormat = 'SQL';
		} else if (Date::isUsDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
			$dateFormat = 'US';
		} else {
			return NULL;
		}
		$daysFrom = Date::dateToDays($date);
		$daysDec = 0;
		for ($i = 1; $i <= $years; $i++) {
			$s = (Date::isLeapYear($year)) ? 366 : 365;
			$daysDec += (Date::isLeapYear($year)) ? 366 : 365;
			$year--;
		}
		for ($i = 1; $i <= $months; $i++) {
			$mTemp = $i % 12;
			$yTemp = intval($i / 12);
			if (($month - $mTemp) <= 0) {
				$yTemp++;
				$mTemp = 12 + ($month - $mTemp);
			} else {
				$mTemp = $month - $mTemp;
			}
			$daysDec += Date::daysInMonth($mTemp, $year - $yTemp);
		}
		$daysDec += $days;
		return Date::daysToDate($daysFrom - $daysDec, $dateFormat);
	}

	/**
	 * Calculate the difference in days between 2 dates
	 *
	 * <code>
	 * /* this will return 6935 {@*}
	 * $diff = Date::getDiff('03/05/1980', '29/04/1999');
	 * /* this will return 365 {@*}
	 * $diff = Date::getDiff('01/01/2006', '01/01/2005');
	 * /* this will return -365, because we want it to return a signed int {@*}
	 * $diff = Date::getDiff('01/01/2006', '01/01/2005', FALSE);
	 * </code>
	 *
	 * @param string $dateM First date
	 * @param string $dateS Second date
	 * @param bool $unsigned Whether to return an unsigned value
	 * @return int Diff, in days
	 * @static
	 */
	function getDiff($dateM, $dateS, $unsigned=TRUE) {
		$daysS = Date::dateToDays($dateS);
		$daysM = Date::dateToDays($dateM);
		return ($unsigned? abs($daysS - $daysM) : ($daysS - $daysM));
	}

	/**
	 * Get the difference in seconds of a timezone, from GMT
	 *
	 * Examples:
	 * # GMT : returns 0
	 * # +0300 : returns 10800
	 * # -0300 : returns -10800
	 *
	 * @param string $tz Timezone string
	 * @return int
	 * @static
	 */
	function getTZDiff($tz) {
		$tz = strval($tz);
		if (Date::isValidTZ($tz)) {
			if ($tz == 'Z' || $tz == 'UT' || $tz == 'GMT') {
				return 0;
			} elseif ($tz[0] == '+' || $tz[0] == '-') {
				$offset = (substr($tz, 1, 2) * 3600) + (substr($tz, -2) * 60);
				return ($tz[0] == '-' ? -1*$offset : $offset);
			} elseif (ereg("^[A-IK-Y]{1}$", $tz)) {
				if (ord($tz) > ord("M"))
					return ((ord($tz) - ord("M")) * 3600);
				else
					return ((ord("A") - ord($tz) - 1) * 3600);
			} else {
				switch ($tz) {
					case 'EDT' : return (-4*3600);
					case 'EST' :
					case 'CDT' : return (-5*3600);
					case 'CST' :
					case 'MDT' : return (-6*3600);
					case 'MST' :
					case 'PDT' : return (-7*3600);
					case 'PST' : return (-8*3600);
				}
			}
		}
		return 0;
	}

	/**
	 * Parse date expressions used at the forms XML specification
	 *
	 * Examples:
	 * # TODAY+10D (today plus 10 days)
	 * # TODAY-18Y (today minus 18 years)
	 *
	 * @param string $expr Date expression
	 * @return string Result date
	 * @static
	 */
	function parseFieldExpression($expr) {
		$matches = array();
		if (eregi("today((\+|\-)([0-9]+)(d|m|y))?", $expr, $matches)) {
			if ($matches[1]) {
				if ($matches[2] == '+')
					$date = ($matches[4] == 'D' ? Date::futureDate(Date::localDate(), $matches[3]) : ($matches[4] == 'M' ? Date::futureDate(Date::localDate(), 0, $matches[3]) : Date::futureDate(Date::localDate(), 0, 0, $matches[3])));
				else
					$date = ($matches[4] == 'D' ? Date::pastDate(Date::localDate(), $matches[3]) : ($matches[4] == 'M' ? Date::pastDate(Date::localDate(), 0, $matches[3]) : Date::pastDate(Date::localDate(), 0, 0, $matches[3])));

			} else {
				$date = Date::localDate();
			}
			return $date;
		}
		return $expr;
	}

	/**
	 * Get the day of week of a given date
	 *
	 * @param string $date Date
	 * @param bool $text If TRUE, the day name is returned
	 * @param bool $abbr If TRUE, the abbreviation of the day name is returned
	 * @return int|string Day number/name
	 * @static
	 */
	function dayOfWeek($date, $text=TRUE, $abbr=FALSE) {
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
		} else if (Date::isUsDate($date, $regs) || Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
		} else {
			return NULL;
		}
		if ($month > 2) {
			$month -= 2;
		} else {
			$month += 10;
			$year--;
		}
		$dow = (floor((13 * $month - 1) / 5) + $day + ($year % 100) + floor(($year % 100) / 4) + floor(($year / 100) / 4) - 2 * floor($year / 100) + 77);
		$dow = (($dow - 7 * floor($dow / 7)));
		$LanguageBase =& LanguageBase::getInstance();
		if ($abbr)
			$daysOfWeek = $LanguageBase->getLanguageValue('DAYS_OF_WEEK_ABBR');
		else
			$daysOfWeek = $LanguageBase->getLanguageValue('DAYS_OF_WEEK');
		if ($text && $daysOfWeek[$dow]) {
			return $daysOfWeek[$dow];
		} else {
			return $dow;
		}
	}

	/**
	 * Get the number of days of a given month/year
	 *
	 * @param int $month Month
	 * @param int $year Year
	 * @return int Number of days
	 * @static
	 */
	function daysInMonth($month=NULL, $year=NULL) {
		if (is_null($year))
			$year = date("Y");
		if (is_null($month))
			$month = date("m");
		if ($month == 2) {
			return (Date::isLeapYear($year) ? 29 : 28);
		} elseif (in_array($month, array(4, 6, 9, 11))) {
			return 30;
		} else {
			return 31;
		}
	}

	/**
	 * Check if a given year is leap
	 *
	 * @param int $year Year
	 * @return bool
	 * @static
	 */
	function isLeapYear($year=NULL) {
		if (is_null($year))
			$year = date("Y");
		if (strlen($year) != 4 || preg_match("/\D/", $year))
			return NULL;
		return ((($year % 4) == 0 && ($year % 100) != 0) || ($year % 400) == 0);
	}

	/**
	 * Transforms a date from <b>SQL</b> format to local date format
	 *
	 * The local date format is defined in the global configuration settings.
	 *
	 * @param string $date Input date
	 * @param bool $preserveTime Preserve time values (hour, minute, second)
	 * @return string
	 * @static
	 */
	function fromSqlDate($date, $preserveTime=FALSE) {
		$format = PHP2Go::getConfigVal('LOCAL_DATE_FORMAT');
		switch ($format) {
			case 'd/m/Y' :
				return Date::fromSqlToEuroDate($date, $preserveTime);
			case 'Y/m/d' :
				return Date::fromSqlToUsDate($date, $preserveTime);
		}
		return $date;
	}

	/**
	 * Transforms a date from local date format to <b>SQL</b> format
	 *
	 * Local date format is read from the global configuration settings.
	 *
	 * @param string $date Input date
	 * @param bool $preserveTime Preserve time values (hour, minute, second)
	 * @return string
	 * @static
	 */
	function toSqlDate($date, $preserveTime=FALSE) {
		$format = PHP2Go::getConfigVal('LOCAL_DATE_FORMAT');
		switch ($format) {
			case 'd/m/Y' :
				return Date::fromEuroToSqlDate($date, $preserveTime);
			case 'Y/m/d' :
				return Date::fromUsToSqlDate($date, $preserveTime);
		}
		return $date;
	}

	/**
	 * Transforms a date from <b>EURO</b> to <b>SQL</b> format
	 *
	 * The transformation will only be performed if the input date
	 * respects the input format (in this case, <b>EURO</b>).
	 *
	 * @param string $date Input date
	 * @param bool $preserveTime Preserve time values (hour, minute, second)
	 * @return string New date
	 * @static
	 */
	function fromEuroToSqlDate($date, $preserveTime=FALSE) {
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			$res = "$regs[3]-$regs[2]-$regs[1]";
			if ($preserveTime && $regs[4] !== FALSE && $regs[5] !== FALSE) {
				$res .= " $regs[4]:$regs[5]";
				if ($regs[6] !== FALSE)
					$res .= ":$regs[6]";
			}
			return $res;
		} else {
			return $date;
		}
	}

	/**
	 * Transforms a date from <b>EURO</b> to <b>US</b> format
	 *
	 * The transformation will only be performed if the input date
	 * respects the input format (in this case, <b>EURO</b>).
	 *
	 * @param string $date Input date
	 * @param bool $preserveTime Preserve time values (hour, minute, second)
	 * @return string New date
	 * @static
	 */
	function fromEuroToUsDate($date, $preserveTime=FALSE) {
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			$res = "$regs[3]/$regs[2]/$regs[1]";
			if ($preserveTime && $regs[4] !== FALSE && $regs[5] !== FALSE) {
				$res .= " $regs[4]:$regs[5]";
				if ($regs[6] !== FALSE)
					$res .= ":$regs[6]";
			}
			return $res;
		} else {
			return $date;
		}
	}

	/**
	 * Transforms a date from <b>US</b> to <b>SQL</b> format
	 *
	 * The transformation will only be performed if the input date
	 * respects the input format (in this case, <b>US</b>).
	 *
	 * @param string $date Input date
	 * @param bool $preserveTime Preserve time values (hour, minute, second)
	 * @return string New date
	 * @static
	 */
	function fromUsToSqlDate($date) {
		$regs = array();
		if (Date::isUsDate($date, $regs)) {
			return str_replace("/", "-", $date);
		} else {
			return $date;
		}
	}

	/**
	 * Transforms a date from <b>US</b> to <b>EURO</b> format
	 *
	 * The transformation will only be performed if the input date
	 * respects the input format (in this case, <b>US</b>).
	 *
	 * @param string $date Input date
	 * @param bool $preserveTime Preserve time values (hour, minute, second)
	 * @return string New date
	 * @static
	 */
	function fromUsToEuroDate($date, $preserveTime=FALSE) {
		$regs = array();
		if (Date::isUsDate($date, $regs)) {
			$res = "$regs[3]/$regs[2]/$regs[1]";
			if ($preserveTime && $regs[4] !== FALSE && $regs[5] !== FALSE) {
				$res .= " $regs[4]:$regs[5]";
				if ($regs[6] !== FALSE)
					$res .= ":$regs[6]";
			}
			return $res;
		} else {
			return $date;
		}
	}

	/**
	 * Transforms a date from <b>SQL</b> to <b>EURO</b> format
	 *
	 * The transformation will only be performed if the input date
	 * respects the input format (in this case, <b>SQL</b>).
	 *
	 * @param string $date Input date
	 * @param bool $preserveTime Preserve time values (hour, minute, second)
	 * @return string New date
	 * @static
	 */
	function fromSqlToEuroDate($date, $preserveTime=FALSE) {
		$regs = array();
		if (Date::isSqlDate($date, $regs)) {
			$res = "$regs[3]/$regs[2]/$regs[1]";
			if ($preserveTime && $regs[4] !== FALSE && $regs[5] !== FALSE) {
				$res .= " $regs[4]:$regs[5]";
				if ($regs[6] !== FALSE)
					$res .= ":$regs[6]";
			}
			return $res;
		} else {
			return $date;
		}
	}

	/**
	 * Transforms a date from <b>SQL</b> to <b>US</b> format
	 *
	 * The transformation will only be performed if the input date
	 * respects the input format (in this case, <b>SQL</b>).
	 *
	 * @param string $date Input date
	 * @param bool $preserveTime Preserve time values (hour, minute, second)
	 * @return string New date
	 * @static
	 */
	function fromSqlToUsDate($date) {
		$regs = array();
		if (Date::isSqlDate($date, $regs)) {
			return str_replace("-", "/", $date);
		} else {
			return $date;
		}
	}

	/**
	 * Converts a UNIX timestamp into a 4-byte DOS date
	 *
	 * Current timestamp will be used if $ts is missing.
	 *
	 * @param int $ts Timestamp
	 * @return int DOS date
	 * @static
	 */
	function fromUnixToDosDate($ts=0) {
		$timeData = ($ts) ? getdate($ts) : getdate();
		if ($timeData['year'] < 1980) {
			$timeData['year'] = 1980;
			$timeData['mon'] = 1;
			$timeData['mday'] = 1;
			$timeData['hours'] = 0;
			$timeData['minutes'] = 0;
			$timeData['seconds'] = 0;
		}
		return ((($timeData['year'] - 1980) << 25) |
			($timeData['mon'] << 21) |
			($timeData['mday'] << 16) |
			($timeData['hours'] << 11) |
			($timeData['minutes'] << 5) |
			($timeData['seconds'] << 1));
	}

	/**
	 * Converts a date into a UNIX timestamp
	 *
	 * @param string $date Input date
	 * @return int Timestamp
	 * @static
	 */
	function dateToTime($date) {
		$date = Date::fromEuroToUsDate($date, TRUE);
		return strtotime($date);
	}

	/**
	 * Converts a date string into a day count
	 *
	 * Current date will be used if $date is missing.
	 *
	 * @param string $date Input date
	 * @return int Day count
	 * @static
	 */
	function dateToDays($date=NULL) {
		if (is_null($date))
			$date = Date::localDate();
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
		} else if (Date::isUsDate($date, $regs) || Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
		} else {
			return -1;
		}
        $century = (int) substr($year,0,2);
        $year = (int) substr($year,2,2);
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }
        return (floor((146097 * $century) / 4 ) + floor(( 1461 * $year) / 4 ) + floor(( 153 * $month + 2) / 5 ) + $day + 1721119);
	}

	/**
	 * Converts a day count into a date string
	 *
	 * @param int $days Day count
	 * @param string $dateType Date format (EURO, US or SQL)
	 * @return string Date string
	 * @static
	 */
	function daysToDate($days, $dateType) {
		if (!TypeUtils::isInteger($days) || !in_array(strtolower($dateType), array('euro', 'us', 'sql'))) {
			return NULL;
		}
        $days -= 1721119;
        $century = floor(( 4 * $days - 1) / 146097);
        $days = floor(4 * $days - 1 - 146097 * $century);
        $day = floor($days / 4);
        $year = floor(( 4 * $day +  3) / 1461);
        $day = floor(4 * $day +  3 - 1461 * $year);
        $day = floor(($day +  4) / 4);
        $month = floor(( 5 * $day - 3) / 153);
        $day = floor(5 * $day - 3 - 153 * $month);
        $day = floor(($day +  5) /  5);
        if ($month < 10) {
            $month +=3;
        } else {
            $month -=9;
            if ($year++ == 99) {
                $year = 0;
                $century++;
            }
        }
        $century = sprintf('%02d', $century);
        $year = sprintf('%02d', $year);
        $month = sprintf('%02d', $month);
        $day = sprintf('%02d', $day);
        if (strtolower($dateType) == 'euro') {
        	return ("$day/$month/$century$year");
        } else if (strtolower($dateType) == 'us') {
        	return ("$century$year/$month/$day");
        } else {
        	return ("$century$year-$month-$day");
        }
	}

	/**
	 * Get the month name of a given timestamp
	 *
	 * Current timestamp will be used if $ts is missing.
	 *
	 * @param int $ts Timestamp
	 * @return string Month name
	 * @static
	 */
	function monthName($ts=0) {
		$Lang =& LanguageBase::getInstance();
		$date = ($ts <= 0 ? time() : intval($ts));
		$month = date('n', $date);
		$monthNames = $Lang->getLanguageValue('MONTHS_OF_YEAR');
		return $monthNames[$month-1];
	}

	/**
	 * Transform a UNIX timestamp into a date string, using the
	 * date format set in the global configuration settings
	 *
	 * Current timestamp will be used if $ts is missing.
	 *
	 * @param int $ts Timestamp
	 * @return string Date string
	 * @static
	 */
	function localDate($ts=0) {
		$Conf =& Conf::getInstance();
		$dateFormat = $Conf->getConfig('LOCAL_DATE_FORMAT');
		if ($ts > 0) {
			if ($dateFormat) {
				return date($dateFormat . ' H:i:s', $ts);
			} else {
				return date("d/m/Y H:i:s", $ts);
			}
		} else {
			if ($dateFormat) {
				return date($dateFormat);
			} else {
				return date("d/m/Y");
			}
		}
	}

	/**
	 * Build a date string from day, month and year values
	 *
	 * Examples:
	 * <code>
	 * /* prints Sat, 01 Jan 2005 00:00:00 GMT {@*}
	 * print Date::formatDate(1, 1, 2005, DATE_FORMAT_RFC822);
	 * /* prints 2005-01-01T00:00:00+0000 {@*}
	 * print Date::formatDate(1, 1, 2005, DATE_FORMAT_ISO8601);
	 * /* prints 01/01/05 {@*}
	 * print Date::formatDate(1, 1, 2005, DATE_FORMAT_CUSTOM, 'd/m/y');
	 * </code>
	 *
	 * @param int $day Day
	 * @param int $month Month
	 * @param int $year Year (with 4 digits)
	 * @param int $fmtType Date format
	 * @param int $fmtStr Custom date format
	 * @return string Formatted date
	 * @see formatTime
	 * @static
	 */
	function formatDate($day, $month, $year, $fmtType=DATE_FORMAT_LOCAL, $fmtStr='') {
		$day = strval(str_repeat('0', (2 - strlen($day))) . $day);
		$month = strval(str_repeat('0', (2 - strlen($month))) . $month);
		$year = strval(str_repeat('0', (4 - strlen($year))) . $year);
		$tsDate = mktime(0, 0, 0, $month, $day, $year);
		return Date::formatTime($tsDate, $fmtType, $fmtStr);
	}

	/**
	 * Build a date string from a UNIX timestamp
	 *
	 * This method behaves like {@link formatDate}.
	 *
	 * @param int $time UNIX timestamp
	 * @param string $fmtType Date format
	 * @param string $fmtStr Custom date format
	 * @return string Date string
	 * @static
	 */
	function formatTime($time=NULL, $fmtType=DATE_FORMAT_LOCAL, $fmtStr='') {
		if (empty($time))
			$time = time();
		if (!TypeUtils::isInteger($time) || $time < 0 || $time > LONG_MAX)
			return $time;
		if ($fmtType == DATE_FORMAT_LOCAL) {
			return Date::localDate($time);
		} elseif ($fmtType == DATE_FORMAT_RFC822) {
			$tz = PHP2Go::getConfigVal('LOCAL_TIME_ZONE', FALSE);
			if (!empty($tz)) {
				$date = date('D, d M Y H:i:s', $time) . ' ' . $tz;
				return $date;
			} else {
				return date('r', $time);
			}
		} elseif ($fmtType == DATE_FORMAT_ISO8601) {
			$date = gmdate("Y-m-d\TH:i:sO", $time);
			$tz = PHP2Go::getConfigVal('LOCAL_TIME_ZONE', FALSE);
			if (!empty($tz)) {
				return str_replace('+00:00', $tz, $date);
			} else {
				return $date;
			}
		} elseif ($fmtType == DATE_FORMAT_CUSTOM && !empty($fmtStr)) {
			return date($fmtStr, $time);
		} else {
			return $time;
		}
	}

	/**
	 * Get current system date
	 *
	 * @param bool $city Prepend date with CITY configuration setting, if available
	 * @param bool $country Prepend date with COUNTRY configuration setting, if available
	 * @param bool $dow Prepend date with week day
	 * @return string Date string
	 * @static
	 */
	function printDate($city=TRUE, $country=TRUE, $dow=TRUE) {
		$Conf =& Conf::getInstance();
		$Lang =& LanguageBase::getInstance();
		$date = "";
		if ($city) {
			$cityName = $Conf->getConfig('CITY');
			if ($cityName === FALSE) {
				trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_FIND_CFG_VAL'), "CITY"), E_USER_WARNING);
			} elseif (!empty($cityName)) {
				$date .= $cityName;
			}
		}
		if ($country) {
			$countryName = $Conf->getConfig('COUNTRY');
			if ($countryName === FALSE) {
				trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_FIND_CFG_VAL'), "COUNTRY"), E_USER_WARNING);
			} elseif (!empty($countryName)) {
				if (!empty($cityName))
					$date .= "/";
				$date .= $countryName . ", ";
			}
		} else if (!empty($cityName)) {
			$date .= ", ";
		}
		if ($dow) {
			$daysOfWeek = $Lang->getLanguageValue('DAYS_OF_WEEK');
			$dayOfWeek = date('w');
			$date .= $daysOfWeek[$dayOfWeek] . ", ";
		}
		$date .= Date::localDate();
		return $date;
	}
}
?>