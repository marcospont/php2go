<?php

class LoggerFilterProperty extends LoggerFilter
{
	protected $name;
	protected $regexp;

	public function __construct($name, $regexp) {
		if (!is_string($name) || empty($name))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Property name must be a non empty string.'));
		$this->name = $name;
		if (!is_string($regexp) || empty($regexp))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Regular expression must be a non empty string.'));
		$this->regexp = $regexp;
	}

	public function accept(array $event) {
		return (isset($event[$this->name]) && preg_match($this->regexp, $event->{$this->name}) > 0);
	}
}