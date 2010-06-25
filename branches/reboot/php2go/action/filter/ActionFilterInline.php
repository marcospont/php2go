<?php

class ActionFilterInline implements ActionFilterInterface
{
	private $method;
	
	public static function factory(Controller $controller, $method) {
		$method = 'filter' . ucfirst($method);
		if (!method_exists($controller, $method))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The "%s" controller does not have the "%s" method.', array($controller->getId(), $method)));
		return new ActionFilterInline($method);
	}
	
	public function __construct($method) {
		$this->method = $method;
	}
	
	public function run(ActionFilterChain $chain) {
		$chain->controller->{$this->method}($chain);
	}
}