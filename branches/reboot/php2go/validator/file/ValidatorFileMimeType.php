<?php

class ValidatorFileMimeType extends Validator
{
	protected $allow;
	protected $deny;
	
	public function __construct() {
		$this->defaultMessages = array(
			'disallowed' => __(PHP2GO_LANG_DOMAIN, '"{file}" is not one of the allowed types: {mimeTypes}.'),
			'denied' => __(PHP2GO_LANG_DOMAIN, 'The type of "{file}" is not allowed.')
		);		
	}
	
	protected function validateOptions() {
		if ($this->allow === null && $this->deny === null)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));		
	}
	
	public function validate($value) {
		if ($value instanceof UploadFile) {
			$name = $value->getName();
			$mimeType = $value->getMimeType();
		} else {
			$name = basename($value);
			$mimeType = FileUtil::getMimeType($value);
		}
		$allow = (is_array($this->allow) ? $this->allow : preg_split('/[\s,]+/', $this->allow, -1, PREG_SPLIT_NO_EMPTY));			
		$deny = (is_array($this->deny) ? $this->deny : preg_split('/[\s,]+/', $this->deny, -1, PREG_SPLIT_NO_EMPTY));
		if (!empty($mimeType) && !in_array($mimeType, $allow)) {
			$this->setError($this->resolveMessage('disallowed'), array('file' => $name, 'mimeType' => $mimeType, 'mimeTypes' => implode(',', $allow)));
			return false;
		}
		if (!empty($mimeType) && in_array($mimeType, $deny)) {
			$this->setError($this->resolveMessage('denied'), array('file' => $name, 'mimeType' => $mimeType));
			return false;
		}
		return true;		
	}
}