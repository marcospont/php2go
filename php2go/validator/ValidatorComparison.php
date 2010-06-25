<?php

class ValidatorComparison extends Validator
{
	private static $operators = array('eq', 'neq', 'gt', 'goet', 'lt', 'loet');
	private static $dataTypes = array('integer', 'decimal', 'currency', 'string');
	protected $operator = '=';
	protected $peer = null;
	protected $peerAttribute = null;
	protected $dataType = 'string';
	protected $localized = false;
	
	public function __construct() {
		$this->defaultMessages = array(
			'eq' => __(PHP2GO_LANG_DOMAIN, 'Value must be equal to "{peer}".'),
			'neq' => __(PHP2GO_LANG_DOMAIN, '"Value must not be equal to "{peer}".'),
			'lt' => __(PHP2GO_LANG_DOMAIN, 'Value must be less than "{peer}".'),
			'loet' => __(PHP2GO_LANG_DOMAIN, 'Value must be less or equal than "{peer}".'),
			'gt' => __(PHP2GO_LANG_DOMAIN, 'Value must be greater than "{peer}".'),
			'goet' => __(PHP2GO_LANG_DOMAIN, 'Value must be greater or equal than "{peer}".')
		);
		$this->defaultModelMessages = array(
			'eq' => __(PHP2GO_LANG_DOMAIN, '{attribute} must be equal to {peer}.'),
			'neq' => __(PHP2GO_LANG_DOMAIN, '{attribute} must not be equal to {peer}.'),
			'lt' => __(PHP2GO_LANG_DOMAIN, '{attribute} must be less than {peer}.'),
			'loet' => __(PHP2GO_LANG_DOMAIN, '{attribute} must be less or equal than {peer}.'),
			'gt' => __(PHP2GO_LANG_DOMAIN, '{attribute} must be greater than {peer}.'),
			'goet' => __(PHP2GO_LANG_DOMAIN, '{attribute} must be greater or equal than {peer}.')
		);		
	}
	
	protected function validateOptions() {
		$invalidPeer = (!empty($this->attributes) ? ($this->peer === null && $this->peerAttribute === null) : ($this->peer === null));
		$invalidOperator = (!in_array(strtolower($this->operator), self::$operators));
		$invalidDataType = (!in_array(strtolower($this->dataType), self::$dataTypes));
		if ($invalidPeer || $invalidOperator || $invalidDataType)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));
	}
	
	public function validate($value) {
		if (!$this->compare($value, $this->peer, $this->localized)) {
			$this->setError($this->resolveMessage($this->operator), array('peer' => $this->peer));
			return false;
		}
		return true;
	}
	
	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if ($this->peerAttribute !== null) {
			$peer = (string)$model->{$this->peerAttribute};
			$peerLabel = $model->getAttributeLabel($this->peerAttribute);
			$peerLocalized = ($this->localized || ($model instanceof ActiveRecord && $model->getAttributeFormat($this->peerAttribute) == $this->dataType));
		} else {
			$peer = $peerLabel = $this->peer;
			$peerLocalized = $this->localized;
		}
		$localized = ($this->localized || ($model instanceof ActiveRecord && $model->getAttributeFormat($attr) == $this->dataType));
		if (!$this->compare($value, $peer, $localized, $peerLocalized))
			$this->addModelError($model, $attr, $this->resolveModelMessage($this->operator), array('peer' => $peerLabel));
	}
	
	private function compare($value, $peer, $localized, $peerLocalized=null) {
		if ($this->dataType !== null) {
			if ($peerLocalized === null)
				$peerLocalized = $localized;
			switch (strtolower($this->dataType)) {
				case 'integer' :
					if ($localized && LocaleNumber::isInteger($value))
						$value = LocaleNumber::getInteger($value);
					if (!is_numeric($value))
						return false;
					$value = intval($value);
					if ($peerLocalized && LocaleNumber::isInteger($peer))
						$peer = LocaleNumber::getInteger($peer);
					if (!is_numeric($peer))
						return false;
					if (!is_int($peer))
						$peer = intval($peer);
					break;
				case 'decimal' :
				case 'currency' :
					if ($localized && LocaleNumber::isFloat($value))
						$value = LocaleNumber::getFloat($value);
					if (!is_numeric($value))
						return false;
					$value = floatval($value);
					if ($peerLocalized && LocaleNumber::isFloat($peer))
						$peer = LocaleNumber::getFloat($peer);
					if (!is_numeric($peer))
						return false;
					if (!is_int($peer))
						$peer = floatval($peer);
					break;
			}
		}
		switch (strtolower($this->operator)) {
			case 'eq' :
				return ($value == $peer);
			case 'neq' :
				return ($value != $peer);
			case 'lt' :
				return ($value < $peer);
			case 'loet' :
				return ($value <= $peer);
			case 'gt' :
				return ($value > $peer);
			case 'goet' :
				return ($value >= $peer);
		}
	}
}