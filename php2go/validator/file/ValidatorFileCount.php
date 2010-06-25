<?php

class ValidatorFileCount extends Validator
{
	protected $min;
	protected $max;
	
	public function __construct() {
		$this->defaultMessages = array(
			'tooFew' => __(PHP2GO_LANG_DOMAIN, 'Too few files. At least {min} are expected but {count} are given.'),
			'tooMany' => __(PHP2GO_LANG_DOMAIN, 'Too many files. At most {max} are accepted but {count} are given.')
		);		
	}
	
	protected function validateOptions() {
		if ($this->min === null && $this->max === null)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));
	}
	
	public function validate($files) {
		if (is_array($files) || $files instanceof Countable) {
			$count = count($files);
			if ($this->min !== null && $count < $this->min) {
				$this->setError($this->resolveMessage('tooFew'), array('min' => $this->min, 'count' => $count));
				return false;
			} elseif ($this->max !== null && $count > $this->max) {
				$this->setError($this->resolveMessage('tooMany'), array('max' => $this->max, 'count' => $count));
				return false;
			}
			return true;
		}
		$this->setError($this->resolveMessage());
		return false;
	}
}