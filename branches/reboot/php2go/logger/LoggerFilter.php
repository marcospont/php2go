<?php

abstract class LoggerFilter extends Component
{
	private static $filters = array(
		Logger::FILTER_PRIORITY,
		Logger::FILTER_PRIORITY_RANGE,
		Logger::FILTER_CATEGORY,
		Logger::FILTER_MESSAGE
	);

	public static function factory($filter) {
		if (is_array($filter)) {
			$type = @$filter[0];
			if (in_array($type, self::$filters)) {
				$count = count($filter) - 1;
				switch ($type) {
					case Logger::FILTER_PRIORITY :
						if ($count == 1)
							return new LoggerFilterPriority(@$filter[1]);
						elseif ($count == 2)
							return new LoggerFilterPriority(@$filter[1], @$filter[2]);
						break;
					case Logger::FILTER_PRIORITY_RANGE :
						if ($count == 2)
							return new LoggerFilterPriorityRange(@$filter[1], @$filter[2]);
						break;
					case Logger::FILTER_CATEGORY :
					case Logger::FILTER_MESSAGE :
						if ($count == 1)
							return new LoggerFilterProperty($type, @$filter[1]);
				}
			} else {
				$config = array(
					'class' => $type,
					'parent' => 'LoggerFilter',
					'options' => $filter
				);
				return Php2Go::newInstance($config);
			}
		}
		throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid logger filter specification.'));
	}

	abstract public function accept(LoggerEvent $event);
}