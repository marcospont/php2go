<?php

class FilterBoolean extends Filter
{
	const ZERO_STRING = 1;
	const EMPTY_STRING = 2;
	const EMPTY_ARRAY = 4;
	const NUMBER = 8;
	const BOOLEAN = 16;
	const NULL = 32;
	const PHP = 63;
	const FALSE_STRING = 64;
	const YES_STRING = 128;
	const ALL = 255;
	
	protected $flags = 0;
	protected $casting = true;
	protected $locale;

	public function __construct($flags=null, $casting=null) {
		if ($flags !== null)
			$this->flags = $flags;
		else
			$this->flags = self::ALL;
		if ($casting !== null)
			$this->casting = (bool)$casting;
		$this->locale = Php2Go::app()->getLocale();
	}
	
	public function setLocale(Locale $locale) {
		$this->locale = $locale;
	}
	
	public function filter($value) {
		if ($this->flags & self::ZERO_STRING) {
			if (is_string($value) && $value == '0')
				return false;
			if (!$this->casting && is_string($value) && $value == '1')
				return true;
		}
		if ($this->flags & self::EMPTY_STRING) {
			if (is_string($value) && $value == '')
				return false;
		}
		if ($this->flags & self::EMPTY_ARRAY) {
			if (is_array($value) && $value == array())
				return false;
		}
		if ($this->flags & self::NUMBER) {
			if ((is_int($value) && $value == 0) || (is_float($value) && $value == 0.0))
				return false;
			if (!$this->casting && ((is_int($value) && $value == 1) || (is_float($value) && $value == 1.0)))
				return true;
		}
		if ($this->flags & self::BOOLEAN) {
			if (is_bool($value))
				return $value;
		}
		if ($this->flags & self::NULL) {
			if (is_null($value))
				return false;
		}
		if ($this->flags & self::FALSE_STRING) {
			if (is_string($value) && strtolower($value) == 'false')
				return false;
			if (!$this->casting && is_string($value) && strtolower($value) == 'true')
				return true;
		}
		if ($this->flags & self::YES_STRING) {
			if (is_string($value) && $this->isLocalizedMessage('no', $value))
				return false;
			if (!$this->casting && is_string($value) && $this->isLocalizedMessage('yes', $value))
				return true;
		}
		return ($this->casting ? true : $value);
	}
	
	protected function isLocalizedMessage($message, $value) {
		$value = utf8_encode(strtolower($value));
		$message = explode(':', $this->locale->getMessage($message));
		return (!empty($message) && $value == $message[0] || $value == $message[1]);
	}
}