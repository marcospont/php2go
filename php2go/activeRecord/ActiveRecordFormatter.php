<?php

class ActiveRecordFormatter extends Component
{
	private static $formatTypes = array(
		'integer', 'decimal', 'date', 'dateTime', 'time'
	);
	protected $locale;
	protected $messages = array();
	protected $model;
	protected $formats = array();
	protected $formatErrors = array();

	public function __construct(ActiveRecord $model) {
		$this->locale = Php2Go::app()->getLocale();
		$this->messages = array(
			'integer' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid integer number.'),
			'decimal' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid decimal number.'),
			'date' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid date.'),
			'dateTime' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid datetime.'),
			'time' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid time.')
		);
		$this->model = $model;
		$this->setFormats($model->formats());
	}

	public function getFormats() {
		return $this->formats;
	}

	protected function setFormats(array $formats) {
		foreach ($formats as $attr => $type) {
			if (!$this->model->hasAttribute($attr))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The "%s" does not have the "%s" attribute.', array(get_class($this->model), $attr)));
			if (!in_array($type, self::$formatTypes))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid format type: "%s"', array($type)));
			$this->formats[$attr] = $type;
		}
	}

	public function getFormatErrors() {
		return $this->formatErrors;
	}

	public function getFormat($name) {
		return (isset($this->formats[$name]) ? $this->formats[$name] : null);
	}

	public function formatGet($name, $value) {
		if (!isset($this->formatErrors[$name]))
			return $this->{$this->formats[$name] . 'Get'}($name, $value);
		return $value;
	}

	public function formatSet($name, $value) {
		try {
			$value = $this->{$this->formats[$name] . 'Set'}($name, $value);
			unset($this->formatErrors[$name]);
		} catch (Exception $e) {
			$this->formatErrors[$name] = Util::buildMessage($this->messages[$this->formats[$name]], array('attribute' => $this->model->getAttributeLabel($name)));
		}
		return $value;
	}

	protected function integerGet($name, $value) {
		if (preg_match('/^-?[0-9]+$/', $value))
			return LocaleNumberFormatter::formatInteger($value);
		return $value;
	}

	protected function integerSet($name, $value) {
		return LocaleNumber::getInteger($value);
	}

	protected function decimalGet($name, $value) {
		if (preg_match('/^-?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?$/', $value))
			return LocaleNumberFormatter::format($value);
		return $value;
	}

	protected function decimalSet($name, $value) {
		return LocaleNumber::getNumber($value);
	}

	protected function dateGet($name, $value) {
		if (preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $value))
			return new LocaleDate($value);
		return $value;
	}

	protected function dateSet($name, $value) {
		return date('Y-m-d', DateTimeParser::parseIso($value, $this->locale->getDateInputFormat()));
	}

	protected function dateTimeGet($name, $value) {
		if (preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $value))
			return new LocaleDateTime($value);
		return $value;
	}

	protected function dateTimeSet($name, $value) {
		return date('Y-m-d H:i:s', DateTimeParser::parseIso($value, $this->locale->getDateTimeInputFormat()));
	}

	protected function timeGet($name, $value) {
		if (preg_match('/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $value))
			return DateTimeFormatter::formatIso($value, $this->locale->getTimeInputFormat());
		return $value;
	}

	protected function timeSet($name, $value) {
		return date('H:i:s', DateTimeParser::parseIso($value, $this->locale->getTimeInputFormat()));
	}
}