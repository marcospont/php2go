<?php

class FilterAlphanum extends FilterRegex
{
	public function __construct($allowWhitespace=false) {
		parent::__construct('/[^a-zA-Z0-9' . ($allowWhitespace ? '\s' : '') . ']/');
	}
}