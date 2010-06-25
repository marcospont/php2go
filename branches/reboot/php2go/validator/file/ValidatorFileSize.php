<?php

class ValidatorFileSize extends Validator
{
	protected $min;
	protected $max;
	protected $useByteString = true;
	
	public function __construct() {
		$this->defaultMessages = array(
			'tooSmall' => __(PHP2GO_LANG_DOMAIN, '"{file}" is smaller than the minimum allowed size: {min}.'),
			'tooBig' => __(PHP2GO_LANG_DOMAIN, '"{file}" is bigger than the maximum allowed size: {max}.')
		);		
	}
	
	protected function validateOptions() {
		if ($this->min === null && $this->max === null)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));
	}
	
	public function validate($value) {
		if ($value instanceof UploadFile) {
			$name = $value->getName();
			$size = $value->getSize();
		} else {
			$name = basename($value);
			$size = @filesize($value);
		}
		$min = Util::fromByteString($this->min, $this->min);
		$max = Util::fromByteString($this->max, $this->max);
		if ($min !== null && $size < $min) {
			$this->setError($this->resolveMessage('tooSmall'), array(
				'file' => $name,
				'min' => ($this->useByteString ? Util::toByteString($min) : $min), 
				'size' => ($this->useByteString ? Util::toByteString($size) : $size)
			));
			return false;
		} elseif ($max !== null && $size > $max) {
			$this->setError($this->resolveMessage('tooBig'), array(
				'file' => $name,
				'max' => ($this->useByteString ? Util::toByteString($max) : $max), 
				'size' => ($this->useByteString ? Util::toByteString($size) : $size)
			));
			return false;
		}
		return true;
	}
}