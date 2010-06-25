<?php

abstract class LoggerFormatter extends Component
{
	private static $formatters = array(
		Logger::FORMATTER_SIMPLE,
		Logger::FORMATTER_PATTERN
	);

	public static function factory($options) {
		if (is_string($options)) {
			$type = $options;
			$options = array();
		} elseif (is_array($options) && isset($options['type'])) {
			$type = Util::consumeArray($options, 'type');
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid logger formatter specification.'));
		}
		$config = array('options' => $options);
		if (in_array($type, self::$formatters)) {
			$config['class'] = 'LoggerFormatter' . ucfirst($type);
		} else {
			$config['class'] = $type;
			$config['parent'] = 'LoggerFormatter';
		}
		return Php2Go::newInstance($config);
	}

	abstract public function format(LoggerEvent $event);
}