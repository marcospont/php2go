<?php

class LoggerFormatterSimple extends LoggerFormatter
{
	public function format(LoggerEvent $event) {
		return sprintf("%s - %s", $event->priorityName, $event->message);
	}
}