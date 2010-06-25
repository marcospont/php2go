<?php

class LoggerAppenderEcho extends LoggerAppender
{
	protected $eol = PHP_EOL;

	public function getEol() {
		return $this->eol;
	}

	public function setEol($eol) {
		$this->eol = $eol;
	}

	public function write(LoggerEvent $event) {
		echo $this->formatter->format($event) . $this->eol;
	}
}