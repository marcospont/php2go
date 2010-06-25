<?php

abstract class LoggerAppender extends Component
{
	private static $appenders = array(
		Logger::APPENDER_CONSOLE,
		Logger::APPENDER_DATE_FILE,
		Logger::APPENDER_ECHO,
		Logger::APPENDER_FILE,
		Logger::APPENDER_MAIL,
		Logger::APPENDER_ROLLING_FILE,
		Logger::APPENDER_SYSLOG
	);
	protected $formatter;
	protected $filters = array();

	public static function factory($options) {
		if (is_string($options)) {
			$type = $options;
			$options = array();
		} elseif (is_array($options) && isset($options['type'])) {
			$type = Util::consumeArray($options, 'type');
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid logger appender specification.'));
		}
		$config = array('options' => $options);
		if (in_array($type, self::$appenders)) {
			$config['class'] = 'LoggerAppender' . ucfirst($type);
		} else {
			$config['class'] = $type;
			$config['parent'] = 'LoggerAppender';
		}
		return Php2Go::newInstance($config);
	}

	public function __construct() {
		$this->formatter = new LoggerFormatterSimple();
	}

	public function getFormatter() {
		return $this->formatter;
	}

	public function setFormatter($formatter) {
		if (!$formatter instanceof LoggerFormatter)
			$formatter = LoggerFormatter::factory($formatter);
		$this->formatter = $formatter;
	}

	public function setFilters(array $filters) {
		foreach ($filters as $filter)
			$this->addFilter($filter);
		return $this;
	}

	public function addFilter($filter) {
		if (!$filter instanceof LoggerFilter)
			$filter = LoggerFilter::factory($filter);
		$this->filters[] = $filter;
		return $this;
	}

	public function clearFilters() {
		$this->filters = array();
		return $this;
	}

	public function append(LoggerEvent $event) {
		foreach ($this->filters as $filter) {
			if (!$filter->accept($event))
				return;
		}
		$this->write($event);
		return $this;
	}

	abstract protected function write(LoggerEvent $event);

	public function close() {
	}
}