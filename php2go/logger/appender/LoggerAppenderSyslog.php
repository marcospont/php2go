<?php

class LoggerAppenderSyslog extends LoggerAppender
{
	protected $priorities = array(
		Logger::EMERG => LOG_EMERG,
		Logger::ALERT => LOG_ALERT,
		Logger::CRIT => LOG_CRIT,
		Logger::ERR => LOG_ERR,
		Logger::WARN => LOG_WARNING,
		Logger::NOTICE => LOG_NOTICE,
		Logger::INFO => LOG_INFO,
		Logger::DEBUG => LOG_DEBUG
	);
	protected $fallbackPriority = LOG_NOTICE;
	protected $ident;
	protected $option;
	protected $facility;
	protected $dirty = true;
	
	public function __construct() {
		$this->ident = Php2Go::app()->getName();
		$this->option = LOG_PID | LOG_CONS;
		$this->facility = LOG_USER;
	}
	
	public function getIdent() {
		return $this->ident;
	}
	
	public function setIdent($ident) {
		if ($ident !== $this->ident) {
			$this->ident = $ident;
			$this->dirty = true;
		}			
	}
	
	public function getOption() {
		return $this->option;
	}
	
	public function setOption($option) {
		if ($option !== $this->option) {
			$this->option = $option;
			$this->dirty = true;
		}
	}
	
	public function getFacility() {
		return $this->facility;
	}
	
	public function setFacility($facility) {
		if ($facility !== $this->facility) {
			$this->facility = $facility;
			$this->dirty = true;
		}
	}
	
	public function write(LoggerEvent $event) {
		$this->open();
		$priority = (array_key_exists($event->priority, $this->priorities) ? $this->priorities[$event->priority] : $this->fallbackPriority);
		syslog($priority, $event->message);		
	}
	
	public function close() {
		closelog();
	}
	
	protected function open() {
		if ($this->dirty) {
			$this->close();
			openlog($this->ident, $this->option, $this->facility);
			$this->dirty = false;
		}
	}
}