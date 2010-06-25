<?php

class LoggerFormatterPattern extends LoggerFormatter
{
	protected $timestampFormat = 'c';
	protected $pattern = '{timestamp} [{priorityName}] {message}';

	public function getTimestampFormat() {
		return $this->timestampFormat;
	}

	public function setTimestampFormat($format) {
		$this->timestampFormat = $format;
	}

	public function getPattern() {
		return $this->pattern;
	}

	public function setPattern($pattern) {
		$this->pattern = $pattern;
	}

	public function format(LoggerEvent $event) {
		$matches = array();
		$result = $this->pattern;
		if (preg_match_all('/({\w+})/', $this->pattern, $matches)) {
			$trans = array();
			foreach ($matches[0] as $match) {
				$variable = substr($match, 1, -1);
				$value = $event->{$variable};
				if ($value !== null)
					$trans[$match] = $this->formatVariable($variable, $value);
			}
			$result = strtr($result, $trans);
		}
		return $result;
	}

	public function formatVariable($name, $value) {
		switch ($name) {
			case 'timestamp' :
				return date($this->timestampFormat, $value);
			default :
				if ((is_object($value) && !method_exists($value, '__toString')) || is_array($value))
					$value = gettype($value);
				return $value;
		}
	}
}