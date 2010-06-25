<?php

class ValidatorFileExtension extends Validator
{
	protected $allow;
	protected $deny;
	protected $caseSensitive = false;
	
	public function __construct() {
		$this->defaultMessages = array(
			'disallowed' => __(PHP2GO_LANG_DOMAIN, '"{file}" is not one of the allowed extensions: {extensions}.'),
			'denied' => __(PHP2GO_LANG_DOMAIN, 'The extension of "{file}" is not allowed.')
		);		
	}
	
	protected function validateOptions() {
		if ($this->allow === null && $this->deny === null)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));		
	}
	
	public function validate($value) {
		if ($value instanceof UploadFile) {
			$name = $value->getName();
			$extension = $value->getExtension();
		} else {
			$name = basename($value);
			$extension = FileUtil::getExtension($value);
		}
		$allow = (is_array($this->allow) ? $this->allow : preg_split('/[\s,]+/', $this->allow, -1, PREG_SPLIT_NO_EMPTY));			
		$deny = (is_array($this->deny) ? $this->deny : preg_split('/[\s,]+/', $this->deny, -1, PREG_SPLIT_NO_EMPTY));
		if (!empty($extension) && (
			($this->caseSensitive && !in_array(strtolower($extension), array_map('strtolower', $allow))) ||
			(!$this->caseSensitive && !in_array($extension, $allow))
		)) {
			$this->setError($this->resolveMessage('disallowed'), array('file' => $name, 'extension' => $extension, 'extensions' => implode(',', $allow)));
			return false;
		}
		if (!empty($extension) && (
			($this->caseSensitive && in_array(strtolower($extension), array_map('strtolower', $deny))) ||
			(!$this->caseSensitive && in_array($extension, $deny))
		)) {
			$this->setError($this->resolveMessage('denied'), array('file' => $name, 'extension' => $extension));
			return false;
		}
		return true;		
	}
}