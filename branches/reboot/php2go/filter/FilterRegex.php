<?php

class FilterRegex extends Filter
{
	protected $pattern;
	protected $replacement = '';
	
	public function __construct($pattern, $replacement=null) {
		$this->pattern = $pattern;
		if ($replacement !== null)
			$this->replacement = $replacement;
	}
	
	public function filter($value) {		
		return preg_replace($this->pattern, $this->replacement, $value);
	}
}