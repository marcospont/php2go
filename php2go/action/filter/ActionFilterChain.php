<?php

class ActionFilterChain
{
	public $controller;
	public $action;
	private $filters = array();
	private $index = 0;

	public static function factory(Controller $controller, Action $action, array $filters) {
		$matches = array();
		$chain = new ActionFilterChain($controller, $action);
		$actionId = $action->getId();
		foreach ($filters as $filter) {
			if (is_string($filter)) {
				if (($pos = strpos($filter, '+')) !== false || ($pos = strpos($filter, '-')) !== false) {
					$match = (preg_match("/\b{$actionId}\b/i", substr($filter, $pos+1)) > 0);
					if (($filter[$pos] == '+') == $match)
						$chain->addFilter(ActionFilterInline::factory($controller, trim(substr($filter, 0, $pos))));
				} else {
					$chain->addFilter(ActionFilterInline::factory($controller, trim($filter)));
				}
			} elseif (is_array($filter) && isset($filter[0])) {
				$class = array_shift($filter);
				if (($pos = strpos($class, '+')) !== false || ($pos = strpos($class, '-')) !== false) {
					$match = (preg_match("/\b{$actionId}\b/i", substr($class, $pos+1)) > 0);
					if (($class[$pos] == '+') == $match)
						$class = trim(substr($class, 0, $pos));
					else
						continue;
				} else {
					$class = trim($class);
				}
				$chain->addFilter(Php2Go::newInstance(array(
					'class' => $class,
					'parent' => 'ActionFilterInterface',
					'options' => $filter
				)));
			} elseif ($filter instanceof ActionFilterInterface) {
				$chain->addFilter($filter);
			} else {
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid filter specification.'));
			}
		}
		return $chain;
	}

	public function __construct(Controller $controller, Action $action) {
		$this->controller = $controller;
		$this->action = $action;
	}

	public function addFilter(ActionFilterInterface $filter) {
		$this->filters[] = $filter;
	}

	public function getFilters() {
		return $this->filters;
	}

	public function run() {
		if (isset($this->filters[$this->index])) {
			$filter = $this->filters[$this->index++];
			$filter->run($this);
		} else {
			$this->controller->runAction($this->action);
		}
	}
}