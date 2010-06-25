<?php

class LoggerFilterPriorityRange extends LoggerFilter
{
	protected $highest;
	protected $lowest;

	public function __construct($highest, $lowest) {
		if (!is_int($highest) || !is_int($lowest) || $highest >= $lowest)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid log priority range.'));
		$this->highest = $highest;
		$this->lowest = $lowest;
	}

	public function accept(LoggerEvent $event) {
		return (-$event->getPriority() <= -$this->highest && -$event->priority >= -$this->lowest);
	}
}