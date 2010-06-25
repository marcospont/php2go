<?php

class FilterInline implements FilterInterface
{
	private $object;
	private $method;
	
	public function __construct($object, $method) {
		$this->object = $object;
		$this->method = 'filter' . ucfirst($method);
		if (!method_exists($this->object, $this->method))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The object "%s" does not have the method "%s".', array(get_class($controller), $method)));
	}
	
	public function filter($value) {
		return $this->object->{$this->method}($value);
	}
}