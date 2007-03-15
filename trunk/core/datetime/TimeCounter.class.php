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
 * Simple implementation of a chronometer
 * 
 * Implements a chronometer based on the server microtime values.
 * Once started, the time counter can be stopped and/or restarted.
 * The elapsed time can be calculated at any time and could be
 * formatted in hours or minutes.
 * 
 * @package datetime
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TimeCounter extends PHP2Go 
{
	/**
	 * Start microtime
	 *
	 * @var float
	 * @access private
	 */
	var $begin;
	
	/**
	 * End microtime
	 *
	 * @var float
	 * @access private
	 */
	var $end;
	
	/**
	 * Indicates if the chronometer is active
	 *
	 * @var bool
	 * @access private
	 */
	var $active;
	
	/**
	 * Timestamp diff for 01/01/1970
	 *
	 * @var int
	 * @access private
	 */
	var $zeroOffset = 7200;

	/**
	 * Class constructor
	 * 
	 * By default, the chronometer starts using current timestamp as
	 * start point. However, you can use a custom start point through
	 * the $begin argument.
	 *
	 * @param int $begin Start timestamp
	 * @return TimeCounter
	 */
	function TimeCounter($begin=0) {
		parent::PHP2Go();
		if (!$begin) {
			list($usec, $sec) = explode(" ",microtime()); 
			$this->begin = (float)$usec + (float)$sec;
		} else {
			$this->begin = $begin;
		} 
		$this->active = TRUE;
	} 

	/**
	 * Stops the chronometer
	 * 
	 * Current server microtime will be used if $end is missing.
	 *
	 * @param int $end Optional end time
	 * @return bool
	 */
	function stop($end=0) {
		if ($end == 0) {
			list($usec, $sec) = explode(" ",microtime()); 
			$this->end = (float)$usec + (float)$sec;
		} else if ($end >= $this->begin) {
			$this->end = $end;
		} else {
			return FALSE;
		} 
		$this->active = FALSE;
		return TRUE;
	} 

	/**
	 * Restart the chronometer
	 * 
	 * Optionally, you can provide a new start time through $begin parameter.
	 *
	 * @param int $begin New start time
	 */
	function restart($begin=0) {
		$this->active = FALSE;
		unset($this->end);
		if (!$begin) {
			list($usec, $sec) = explode(" ",microtime()); 
			$this->begin = (float)$usec + (float)$sec;
		} else {
			$this->begin = $begin;
		} 
		$this->active = TRUE;
	} 

	/**
	 * Get elapsed time since last time chronometer was started
	 *
	 * @return float
	 * @see getInterval
	 */
	function getElapsedTime() {
		list($usec, $sec) = explode(" ",microtime()); 
		$now = (float)$usec + (float)$sec;			
		$interval = $now - $this->begin;
		return $interval;
	}

	/**
	 * Get diff between end and start times
	 * 
	 * You must call {@link stop} before calling this method.
	 *
	 * @return float
	 * @see getElapsedTime
	 */
	function getInterval() {
		if (!isset($this->end))
			return NULL;
		return ($this->end - $this->begin);
	}
	
	/**
	 * Get diff between end and start times, expressed in <b>minutes/seconds</b>
	 * 
	 * You must call {@link stop} before calling this method.
	 *
	 * @param bool $returnAsArray Return hours and seconds as an array
	 * @return string|array
	 */
	function getMinutes($returnAsArray=FALSE) {
		if (!isset($this->end))
			return NULL;
		$interval = $this->getInterval();
		$hours = floor($interval / 3600);
		$calcTime = date("i:s", ($interval + $this->zeroOffset));
		list($minutes, $seconds) = explode(':', $calcTime);
		if ($returnAsArray)
			return array($minutes + ($hours * 60), $seconds);
		else
			return ($minutes + ($hours * 60)) . "m" . $seconds . "s";
	} 

	/**
	 * Get diff between end and start times, expressed in <b>hours/minutes/seconds</b>
	 * 
	 * You must call {@link stop} before calling this method.
	 *
	 * @param bool $returnAsArray Return hours and seconds as an array
	 * @return string|array
	 */
	function getHours($returnAsArray=FALSE) {
		if (!isset($this->end))
			return NULL;
		$interval = $this->getInterval();
		$days = floor($interval / 86400);
		$calcTime = date("H:i:s", ($interval + $this->zeroOffset));
		list($hours, $minutes, $seconds) = explode(':', $calcTime);
		if ($returnAsArray) {
			return (array($hours + ($days * 24), $minutes, $seconds));
		} else {
			return ($hours + ($days * 24)) . "h" . $minutes . "m" . $seconds . "s";
		} 
	}
} 
?>