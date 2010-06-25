<?php

class LoggerEvent extends Event
{
	private static $startTime;
	protected $data;
	protected $source;
	
	public function __construct(array $data) {
		$this->data = $data;
	}
	
	public function __get($name) {
		switch ($name) {
			case 'time' :
				return $this->getTime();
			case 'file' :
			case 'line' :
			case 'class' :
			case 'function' :
				if (isset($this->data[$name])) {
					return $this->data[$name];
				} else {
					$source = $this->getSource();
					return $source[$name];
				}
			default :
				return (isset($this->data[$name]) ? $this->data[$name] : null);
		}
	}
	
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}
	
	public static function getStartTime() {
		if (self::$startTime === null)
			self::$startTime = microtime(true);
		return self::$startTime;
	}
	
	private function getTime() {
		$time = $this->data['timestamp'];
		$startTime = self::getStartTime();
		return number_format(($time - $startTime) * 1000, 0, '', '');
	}
	
	private function getSource() {
		if ($this->source === null) {
			$source = array();
			$trace = debug_backtrace();
			$prevItem = null;			
			$item = array_pop($trace);
			while ($item !== null) {
				if (isset($item['class'])) {
					$className = strtolower($item['class']);
					if (!empty($className) && ($className == 'logger' || strtolower(get_parent_class($className)) == 'logger')) {
						$source['line'] = $item['line'];
						$source['file'] = $item['file'];
						break;
					}
				}
				$prevItem = $item;
				$item = array_pop($trace);
			}
			$source['class'] = isset($prevItem['class']) ? $prevItem['class'] : 'main';
			if (isset($prevItem['function']) && $prevItem['function'] !== 'include' && $prevItem['function'] !== 'include_once' && $prevItem['function'] !== 'require' && $prevItem['function'] !== 'require_once')
				$source['function'] = $prevItem['function'];
			else
				$source['function'] = 'main';
			$this->source = $source;
		}
		return $this->source;
	}
}

LoggerEvent::getStartTime();