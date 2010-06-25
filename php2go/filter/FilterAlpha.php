<?php

class FilterAlpha extends FilterRegex
{
	public function __construct($allowWhitespace=false) {
		parent::__construct('/[^a-zA-Z' . ($allowWhitespace ? '\s' : '') . ']/');
	}
}
