<?php

Php2Go::import('php2go.logger.appender.*');
Php2Go::import('php2go.logger.filter.*');
Php2Go::import('php2go.logger.formatter.*');

class Logger extends Component
{
	const EMERG = 0;
	const ALERT = 1;
	const CRIT = 2;
	const ERR = 3;
	const WARN = 4;
	const NOTICE = 5;
	const INFO = 6;
	const DEBUG = 7;
	const APPENDER_CONSOLE = 'console';
	const APPENDER_DATE_FILE = 'dateFile';
	const APPENDER_ECHO = 'echo';
	const APPENDER_FILE = 'file';
	const APPENDER_MAIL = 'mail';
	const APPENDER_ROLLING_FILE = 'rollingFile';
	const APPENDER_SYSLOG = 'syslog';
	const FILTER_PRIORITY = 'priority';
	const FILTER_PRIORITY_RANGE = 'priorityRange';
	const FILTER_CATEGORY = 'category';
	const FILTER_MESSAGE = 'message';
	const FORMATTER_SIMPLE = 'simple';
	const FORMATTER_PATTERN = 'pattern';

	protected $priorities = array(
		self::EMERG  => 'EMERG',
		self::ALERT => 'ALERT',
		self::CRIT => 'CRIT',
		self::ERR => 'ERR',
		self::WARN => 'WARN',
		self::NOTICE => 'NOTICE',
		self::INFO => 'INFO',
		self::DEBUG => 'DEBUG'
	);
	protected $appenders = array();
	protected $filters = array();
	protected $eventExtras = array();
	
	public static function instance() {
		return Php2Go::app()->getLogger();
	}

	public function __destruct() {
		foreach ($this->appenders as $appender)
			$appender->close();
	}

	public function __call($name, $args) {
		$name = strtoupper($name);
		if (($priority = array_search($name, $this->priorities)) !== false) {
			if (empty($args))
				throw new LoggerException(__(PHP2GO_LANG_DOMAIN, 'Missing log message.'));
			$message = $args[0];
			$category = (isset($args[1]) ? $args[1] : null);
			$extras = (isset($args[2]) && is_array($args[2]) ? $args[2] : array());
			return $this->log($message, $priority, $category, $extras);
		} else {
			return parent::__call($name, $args);
		}
	}
	
	public function assertLog($assertion, $message, $category=null, $extras=null) {
		if ($assertion == false)
			$this->log($message, Logger::ERR, $category, $extras);
	}

	public function log($message, $priority, $category=null, $extras=null) {
		if (empty($this->appenders))
			throw new LoggerException(__(PHP2GO_LANG_DOMAIN, 'No log appenders registered.'));
		if (!isset($this->priorities[$priority]))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid log priority.'));
		$event = new LoggerEvent(array_merge(array(
			'timestamp' => microtime(true),
			'message' => $message,
			'priority' => $priority,
			'priorityName' => $this->priorities[$priority],
			'category' => $category
		), $this->eventExtras));
		if (is_array($extras)) {
			foreach ($extras as $key => $value)
				$event->{$key} = $value;
		}
		foreach ($this->appenders as $appender)
			$appender->append($event);
	}
	
	public function getPriorities() {
		return $this->priorities;
	}
	
	public function getPriorityName($priority) {
		return (isset($this->priorities[$priority]) ? $this->priorities[$priority] : null);
	}

	public function setPriorities(array $priorities) {
		foreach ($priorities as $name => $priority)
			$this->addPriority($name, $priority);
	}

	public function addPriority($name, $priority) {
		$name = strtoupper($name);
		if (!is_int($priority) || $priority < 0)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid log priority: %s.', array($priority)));
		if (isset($this->priorities[$priority]) || array_search($name, $this->priorities) !== false)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Log priorities can not be overwritten.'));
		$this->priorities[$priority] = $name;
	}

	public function getAppenders() {
		return $this->appenders;
	}

	public function setAppenders(array $appenders) {
		$this->appenders = array();
		foreach ($appenders as $appender)
			$this->addAppender($appender);
	}

	public function addAppender($appender) {
		if (!$appender instanceof LoggerAppender)
			$appender = LoggerAppender::factory($appender);
		$this->appenders[] = $appender;
	}
	
	public function clearAppenders() {
		foreach ($this->appenders as $appender)
			$appender->close();
		$this->appenders = array();
	}

	public function setFilters(array $filters) {
		$this->filters = array();
		foreach ($filters as $filter)
			$this->addFilter($filter);
	}

	public function addFilter($filter) {
		if (!$filter instanceof LoggerFilter)
			$filter = LoggerFilter::factory($filter);
		$this->filters[] = $filter;
	}
	
	public function clearFilters() {
		$this->filters = array();
	}

	public function setEventExtras(array $extras) {
		$this->eventExtras = array();
		foreach ($extras as $key => $value)
			$this->addEventExtra($key, $value);
	}

	public function addEventExtra($key, $value) {
		$this->eventExtras[$key] = $value;
	}
}

class LoggerException extends Exception
{
}