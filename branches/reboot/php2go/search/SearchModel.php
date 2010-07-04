<?php

abstract class SearchModel extends FormModel
{
	protected static $types = array('string', 'integer', 'decimal', 'date', 'datetime', 'time');
	protected static $operators = array('select', 'containing', 'starting', 'ending', 'eq', 'neq', 'gt', 'goet', 'lt', 'loet');
	protected $filters = array();
	protected $filter;
	protected $db;
	protected $locale;
	protected $messages;

	public function __construct() {
		parent::__construct();
		$this->filters = $this->parseFilters();
		$this->db = Db::instance();
		$this->locale = Php2Go::app()->getLocale();
		$this->messages = array(
			'integer' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid integer number.'),
			'decimal' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid decimal number.'),
			'date' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid date.'),
			'datetime' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid datetime.'),
			'time' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid time.')
		);
	}

	public function __get($name) {
		if (preg_match('/^(\w+)\[(\w+)\]$/', $name, $matches))
			list(, $name, $part) = $matches;
		if (array_key_exists($name, $this->filters)) {
			$value = $this->getAttribute($name);
			if (isset($part) && is_array($value))
				return (isset($value[$part]) ? $value[$part] : null);
			return $value;
		}
		return parent::__get($name);
	}

	public function __set($name, $value) {
		if (!$this->setAttribute($name, $value))
			parent::__set($name, $value);
	}

	public function __isset($name) {
		if (array_key_exists($name, $this->filters) && isset($this->filters[$name]['value']))
			return true;
		return parent::__isset($name);
	}

	public function __unset($name) {
		if (array_key_exists($name, $this->filters))
			$this->filters[$name]['value'] = null;
		else
			parent::__unset($name);
	}

	abstract public function filters();

	public function getAttributeNames() {
		return array_keys($this->filters);
	}

	public function hasAttribute($name) {
		return (array_key_exists($name, $this->filters));
	}

	public function getAttribute($name) {
		if (array_key_exists($name, $this->filters) && isset($this->filters[$name]['value']))
			return $this->filters[$name]['value'];
		return null;
	}

	public function getAttributeLabel($name, $fallback=null) {
		if (preg_match('/^(\w+)\[(\w+)\]$/', $name, $matches)) {
			list(, $name, $part) = $matches;
			if ($part != 'val' && array_key_exists($name, $this->filters)) {
				$key = $part . 'Label';
				if (isset($this->filters[$name][$key]))
					return $this->filters[$name][$key];
				return parent::getAttributeLabel($name, $fallback) . ' (' . ucfirst($part) . ')';
			}
		}
		return parent::getAttributeLabel($name, $fallback);
	}

	public function setAttribute($name, $value) {
		if (array_key_exists($name, $this->filters)) {
			$this->filters[$name]['value'] = $value;
			return true;
		}
		return false;
	}

	public function hasErrors($attr=null) {
		if ($attr !== null && array_key_exists($attr, $this->filters)) {
			if ($this->filters[$attr]['operator'] == 'select')
				return $this->hasErrors($attr . '[val]');
			if ($this->filters[$attr]['interval'])
				return ($this->hasErrors($attr . '[start]') || $this->hasErrors($attr . '[end]'));
		}
		return parent::hasErrors($attr);
	}

	public function isAttributeRequired($attr) {
		if (array_key_exists($attr, $this->filters)) {
			if ($this->filters[$attr]['operator'] == 'select')
				return $this->isAttributeRequired($attr . '[val]');
			if ($this->filters[$attr]['interval'])
				return ($this->isAttributeRequired($attr . '[start]') || $this->isAttributeRequired($attr . '[end]'));
		}
		return parent::isAttributeRequired($attr);
	}

	public function import(array $attrs=array()) {
		foreach ($attrs as $name => $value)
			$this->setAttribute($name, $value);
		$this->raiseEvent('onImport', new Event($this));
	}

	public function process() {
		if ($this->validate())
			return $this->buildFilter();
		return null;
	}

	protected function parseFilters() {
		$filters = $this->filters();
		if (!is_array($filters))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid filters specification.'));
		foreach ($filters as $name => &$attrs) {
			// validate attributes
			if (!is_array($attrs))
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid filter specification.'));
			// validate name
			if (!isset($attrs['name']))
				$attrs['name'] = $name;
			// validate type
			if (!isset($attrs['interval']))
				$attrs['interval'] = false;
			else
				$attrs['interval'] = (bool)$attrs['interval'];
			if (!isset($attrs['type'])) {
				$attrs['type'] = 'string';
			} else {
				if (!in_array($attrs['type'], self::$types))
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid filter type: "%s".', array($attrs['type'])));
			}
			// validate operator
			if (isset($attrs['operator'])) {
				if ($attrs['interval'])
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Interval filters does not support "operator" option.'));
				if (!in_array($attrs['operator'], self::$operators))
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid filter operator: "%s".', array($attrs['operator'])));
				if ($attrs['type'] != 'string' && in_array($attrs['operator'], array('containing', 'starting', 'ending')))
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" search type does not support the "%s" operator.', array($attrs['type'], $attrs['operator'])));
			}
			if (!isset($attrs['operator'])) {
				if ($attrs['interval']) {
					$attrs['operator'] = 'between';
				} else {
					switch ($attrs['type']) {
						case 'string' :
							$attrs['operator'] = 'containing';
							break;
						default :
							$attrs['operator'] = 'eq';
							break;
					}
				}
			}
			// validate callbacks
			foreach (array('callback', 'nameCallback', 'valueCallback') as $attr) {
				if (isset($attrs[$attr]) && !is_callable($attrs[$attr]))
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid callback on "%s" property of filter "%s".', array($attr, $name)));
			}
		}
		return $filters;
	}

	protected function buildFilter() {
		$filter = array();
		$locale = Php2Go::app()->getLocale();
		foreach ($this->filters as $name => $data) {
			if (isset($data['value'])) {
				if ($data['interval']) {
					// interval filters
					if (is_array($data['value'])) {
						if (!isset($data['value']['start']) || !isset($data['value']['end']))
							continue;
						list($start, $end) = array_values($data['value']);
						// format values
						if (!Util::isEmpty($start)) {
							try {
								$start = $this->formatValue($data['type'], $start);
							} catch (Exception $e) {
								$this->addError($name, Util::buildMessage($this->messages[$data['type']], array('attribute' => $this->getAttributeLabel($name . '[start]'))));
								continue;
							}
						}
						if (!Util::isEmpty($end)) {
							try {
								$end = $this->formatValue($data['type'], $end);
							} catch (Exception $e) {
								$this->addError($name, Util::buildMessage($this->messages[$data['type']], array('attribute' => $this->getAttributeLabel($name . '[end]'))));
								continue;
							}
						}
						// skip empty
						if (Util::isEmpty($start) || Util::isEmpty($end))
							continue;
						// build filter
						if (isset($data['callback'])) {
							$result = call_user_func_array($data['callback'], array($start, $end));
							if (!Util::isEmpty($result))
								$filter[] = $result;
						} else {
							$start = $this->resolveValueCallback($data, $start);
							$end = $this->resolveValueCallback($data, $end);
							if ($data['type'] != 'integer' && $data['type'] != 'decimal') {
								$start = $this->db->quote($start);
								$end = $this->db->quote($end);
							}
							$filter[] = $this->resolveNameCallback($data, $name) . ' between ' . $start . ' and ' . $end;
						}
					}
				} else {
					// filters with operator selected by the user
					if ($data['operator'] == 'select') {
						if (!is_array($data['value']) || !isset($data['value']['op']) || !isset($data['value']['val']))
							continue;
						$operator = $data['value']['op'];
						$value = $data['value']['val'];
					} else {
						$operator = $data['operator'];
						$value = $data['value'];
					}
					// format value
					if (!Util::isEmpty($value)) {
						try {
							$value = $this->formatValue($data['type'], $value);
						} catch (Exception $e) {
							$this->addError($name, Util::buildMessage($this->messages[$data['type']], array('attribute' => $this->getAttributeLabel($name))));
							continue;
						}
					}
					// skip empty values
					if (Util::isEmpty($value))
						continue;
					// build filter
					if (isset($data['callback'])) {
						$result = call_user_func($data['callback'], $value);
						if (!Util::isEmpty($result))
							$filter[] = $result;
					} else {
						$value = $this->resolveValueCallback($data, $value);
						if ($operator == 'starting' || $operator == 'containing')
							$value = '%' . $value;
						if ($operator == 'ending' || $operator == 'containing')
							$value .= '%';
						if ($data['type'] != 'integer' && $data['type'] != 'decimal')
							$value = $this->db->quote($value);
						$filter[] = $this->resolveNameCallback($data, $name) . $this->resolveOperator($operator) . $value;
					}
				}
			}
		}
		return implode(' and ', $filter);
	}

	protected function formatValue($type, $value) {
		switch ($type) {
			case 'integer' :
				return LocaleNumber::getInteger($value);
				break;
			case 'decimal' :
				return LocaleNumber::getNumber($value);
			case 'date' :
				return date('Y-m-d', DateTimeParser::parseIso($value, $this->locale->getDateInputFormat()));
			case 'datetime' :
				return date('Y-m-d H:i:s', DateTimeParser::parseIso($value, $this->locale->getDateTimeInputFormat()));
			case 'time' :
				return date('H:i:s', DateTimeParser::parseIso($value, $this->locale->getTimeInputFormat()));
			default :
				return $value;
		}
	}

	protected function resolveNameCallback(array $options, $name) {
		if (isset($options['nameCallback']))
			return call_user_func($options['nameCallback'], $name);
		return $name;
	}

	protected function resolveOperator($operator) {
		switch ($operator) {
			case 'eq' :
			case '=' :
				return ' = ';
			case 'neq' :
			case '!=' :
				return ' != ';
			case 'gt' :
			case '>' :
				return ' > ';
			case 'goet' :
			case '>=' :
				return ' >= ';
			case 'lt' :
			case '<' :
				return ' < ';
			case 'loet' :
			case '<=' :
				return ' <= ';
			case 'starting' :
			case 'ending' :
			case 'containing' :
				return ' like ';
		}
	}

	protected function resolveValueCallback(array $options, $value) {
		if (isset($options['valueCallback']))
			return call_user_func($options['valueCallback'], $value);
		return $value;
	}
}