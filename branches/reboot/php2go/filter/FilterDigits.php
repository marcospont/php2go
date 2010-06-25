<?php

class FilterDigits extends FilterRegex
{
	public function __construct() {
		parent::__construct((extension_loaded('mbstring') ? '/[^[:digit:]]/' : '/[^0-9]/'));
	}
}