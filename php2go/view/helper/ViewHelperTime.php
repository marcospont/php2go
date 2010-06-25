<?php

class ViewHelperTime extends ViewHelper
{
	public function agoInWords($time, $seconds=false) {
		return $this->distanceInWords($time, time(), $seconds);
	}

	public function distanceInWords($from, $to, $seconds=false) {
		if ($from instanceof DateTime)
			$from = $from->format('U');
		elseif (!is_int($from))
			$from = DateTimeUtil::getTime($from);
		if ($to instanceof DateTime)
			$to = $to->format('U');
		elseif (!is_int($to))
			$to = DateTimeUtil::getTime($to);
		$distanceInSeconds = round(abs($to - $from));
		$distanceInMinutes = round($distanceInSeconds / 60);
		if ($distanceInMinutes <= 1) {
			if (!$seconds) {
				if ($distanceInMinutes == 0)
					return __(PHP2GO_LANG_DOMAIN, 'less than a minute');
				else
					return __(PHP2GO_LANG_DOMAIN, '1 minute');
			} else {
				if ($distanceInSeconds < 5)
					return __(PHP2GO_LANG_DOMAIN, 'less than %s seconds', array('5'));
				if ($distanceInSeconds < 10)
					return __(PHP2GO_LANG_DOMAIN, 'less than %s seconds', array('10'));
				if ($distanceInSeconds < 20)
					return __(PHP2GO_LANG_DOMAIN, 'less than %s seconds', array('20'));
				if ($distanceInSeconds < 40)
					return __(PHP2GO_LANG_DOMAIN, 'half a minute');
				if ($distanceInSeconds < 60)
					return __(PHP2GO_LANG_DOMAIN, 'less than a minute');
				return __(PHP2GO_LANG_DOMAIN, '1 minute');
			}
		}
		if ($distanceInMinutes < 45)
			return __(PHP2GO_LANG_DOMAIN, '%s minutes', array($distanceInMinutes));
		if ($distanceInMinutes < 90)
			return __(PHP2GO_LANG_DOMAIN, 'about an hour');
		if ($distanceInMinutes < 1440)
			return __(PHP2GO_LANG_DOMAIN, 'about %s hours', array(round(floatval($distanceInMinutes)/60.0)));
		if ($distanceInMinutes < 2880)
			return __(PHP2GO_LANG_DOMAIN, 'a day');
		if ($distanceInMinutes < 43200) {
			$days = round(floatval($distanceInMinutes)/1440);
			if ($days >= 6 && $days <= 9)
				return __(PHP2GO_LANG_DOMAIN, 'about a week');
			else
				return __(PHP2GO_LANG_DOMAIN, 'about %s days', array($days));
		}
		if ($distanceInMinutes < 86400)
			return __(PHP2GO_LANG_DOMAIN, 'about a month');
		if ($distanceInMinutes < 525600)
			return __(PHP2GO_LANG_DOMAIN, '%s months', array(round(floatval($distanceInMinutes)/43200)));
		if ($distanceInMinutes < 1051199)
			return __(PHP2GO_LANG_DOMAIN, 'about a year');
		return __(PHP2GO_LANG_DOMAIN, 'over %s years', array(round(floatval($distanceInMinutes)/525600)));
	}
}