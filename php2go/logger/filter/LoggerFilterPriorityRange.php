<?php

class LoggerFilterPriorityRange extends LoggerFilter
{
	protected $lowest;
	protected $highest;

	public function __construct($lowest, $highest) {
		if (!is_int($lowest) || !is_int($highest) || $lowest >= $highest)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid log priority range.'));
		$this->lowest = $lowest;
		$this->highest = $highest;
	}

	public function accept(LoggerEvent $event) {
		return ($event->priority >= $this->lowest && $event->priority <= $this->highest);
	}
}