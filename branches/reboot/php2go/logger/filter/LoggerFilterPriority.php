<?php

class LoggerFilterPriority extends LoggerFilter
{
	private static $operators = array(
		'eq' => '=',
		'neq' => '!=',
		'gt' => '>',
		'goet' => '>=',
		'lt' => '<',
		'loet' => '<='
	);
	protected $priority;
	protected $operator;

	public function __construct($priority, $operator=null) {
		if (!is_int($priority))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid log priority.'));
		$this->priority = $priority;
		if ($operator !== null) {
			if (!array_key_exists($operator, self::$operators)) {
				if (!array_key_exists($operator, array_flip(self::$operators)))
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid operator.'));
				else
					$this->operator = $operator;
			} else {
				$this->operator = self::$operators[$operator];
			}
		} else {
			$this->operator = '>=';
		}
	}

	public function accept(LoggerEvent $event) {
		return version_compare(-$event->priority, -$this->priority, $this->operator);
	}
}