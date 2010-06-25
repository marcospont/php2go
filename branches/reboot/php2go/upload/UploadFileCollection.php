<?php

class UploadFileCollection extends ArrayObject
{
	private $uploadKey;
	
	public function __construct(array $files, $uploadKey) {
		parent::__construct($files);
		$this->uploadKey = $uploadKey;
	}
	
	public function getUploadKey() {
		return $this->uploadKey;
	}
	
	public function offsetSet($offset, $value) {
		throw new LogicException(__(PHP2GO_LANG_DOMAIN, 'Adding members on an upload files collection is not allowed.'));
	}
	
	public function offsetUnset($offset) {
		throw new LogicException(__(PHP2GO_LANG_DOMAIN, 'Removing members from an upload files collection is not allowed.'));
	}
}