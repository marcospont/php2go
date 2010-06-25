<?php

class FilterNull extends Filter
{
	const ZERO_STRING = 1;
	const EMPTY_STRING = 2;
	const EMPTY_ARRAY = 4;
	const NUMBER = 8;
	const BOOLEAN = 16;
	const ALL = 31;
	
	protected $flags = 0;

	public function __construct($flags=null) {
		if ($flags !== null)
			$this->flags = $flags;
		else
			$this->flags = self::ALL;
	}
	
	public function filter($value) {
		if ($this->flags & self::ZERO_STRING) {
			if (is_string($value) && $value == '0')
				return null;
		}
		if ($this->flags & self::EMPTY_STRING) {
			if (is_string($value) && $value == '')
				return null;
		}
		if ($this->flags & self::EMPTY_ARRAY) {
			if (is_array($value) && $value == array())
				return null;
		}
		if ($this->flags & self::NUMBER) {
			if ((is_int($value) || is_float($value)) && $value == 0)
				return null;
		}
		if ($this->flags & self::BOOLEAN) {
			if (is_bool($value) && $value == false)
				return null;
		}
		return $value;
	}
}