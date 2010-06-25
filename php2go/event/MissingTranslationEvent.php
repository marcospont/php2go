<?php

class MissingTranslationEvent extends Event
{
	public $locale;
	public $domain;
	public $key;
	
	public function __construct($sender, $locale, $domain, $key) {
		parent::__construct($sender);
		$this->locale = $locale;
		$this->domain = $domain;
		$this->key = $key;
	}
}