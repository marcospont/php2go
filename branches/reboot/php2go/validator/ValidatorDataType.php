<?php

class ValidatorDataType extends Validator
{
	private static $dataTypes = array('integer', 'decimal', 'date', 'dateTime', 'time');
	protected $dataType;
	protected $format;
	protected $localized = false;

	public function __construct() {
		$this->defaultMessages = array(
			'integer' => __(PHP2GO_LANG_DOMAIN, 'Value is not a valid integer number.'),
			'decimal' => __(PHP2GO_LANG_DOMAIN, 'Value is not a valid decimal number.'),
			'date' => __(PHP2GO_LANG_DOMAIN, 'Value is not a valid date.'),
			'datetime' => __(PHP2GO_LANG_DOMAIN, 'Value is not a valid date time.'),
			'time' => __(PHP2GO_LANG_DOMAIN, 'Value is not a valid time.')
		);
		$this->defaultModelMessages = array(
			'integer' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid integer number.'),
			'decimal' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid decimal number.'),
			'date' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid date.'),
			'datetime' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid datetime.'),
			'time' => __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid time.')
		);
	}

	public function loadOptions(array $options) {
		parent::loadOptions($options);
		if ($this->format === null) {
			$locale = Php2Go::app()->getLocale();
			switch ($this->dataType) {
				case 'date' :
					$this->format = $locale->getDateInputFormat();
					break;
				case 'datetime' :
					$this->format = $locale->getDateTimeInputFormat();
					break;
				case 'time' :
					$this->format = $locale->getTimeInputFormat();
					break;
			}
		} else {
			$this->format = DateTimeUtil::convertPhpToIsoFormat($this->format);
		}
	}

	protected function validateOptions() {
		if ($this->dataType === null || !in_array($this->dataType, self::$dataTypes))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));
	}

	public function validate($value) {
		$value = (string)$value;
		if (!$this->isType($value, $this->localized)) {
			$this->setError($this->resolveMessage($this->dataType));
			return false;
		}
		return true;
	}

	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		$localized = ($this->localized || ($model instanceof ActiveRecord && $model->getAttributeFormat($attr) == $this->dataType));
		if (!$this->isType($value, $localized))
			$this->addModelError($model, $attr, $this->resolveModelMessage($this->dataType));
	}

	protected function isType($value, $localized) {
		switch ($this->dataType) {
			case 'integer' :
				return ($localized ? LocaleNumber::isInteger($value) : preg_match('/^-?[0-9]+$/', $value));
			case 'decimal' :
				return ($localized ? LocaleNumber::isNumber($value) : preg_match('/^-?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?$/', $value));
			case 'date' :
			case 'dateTime' :
			case 'time' :
				return DateTimeUtil::isDate($value, $this->format, 'iso');
		}
	}
}