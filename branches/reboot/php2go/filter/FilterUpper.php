<?php

class FilterUpper extends Filter
{
	protected $encoding = null;
	
	public function __construct($encoding=null) {
		if ($encoding !== null) {
			if (!extension_loaded('mbstring'))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The "%s" extension is not available.', array('mbstring')));
			if (!in_array(strtolower($encoding), array_map('strtolower', mb_list_encodings())))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The "%s" encoding is not supported.', array($encoding)));
		}
		$this->encoding = $encoding;
	}
	
	public function filter($value) {
		return ($this->encoding !== null ? mb_strtoupper($value, $this->encoding) : strtoupper($value));
	}
}