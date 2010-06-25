<?php

class FilterCallback extends Filter
{
	protected $callback;
	protected $args;
	
	public function __construct($callback, array $args=array()) {
		if (!is_callable($callback))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid callback.'));
		$this->callback = $callback;
		$this->args = $args;
	}
	
	public function filter($value) {
		return call_user_func_array($this->callback, array_merge(array($value), $this->args));
	}
}