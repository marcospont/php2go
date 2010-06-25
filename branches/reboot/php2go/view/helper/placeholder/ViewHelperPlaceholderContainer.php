<?php

class ViewHelperPlaceholderContainer extends ArrayObject
{
	protected $captureLock = false;
	protected $captureKey;
	protected $captureType;

	public function __construct() {
		parent::__construct(array(), parent::ARRAY_AS_PROPS);
	}

	public function prepend($content) {
		$content = $this->getArrayCopy();
		array_unshift($content, $content);
		$this->exchangeArray($content);
	}

	public function set($content) {
		$this->exchangeArray(array($content));
	}

	public function begin($type=ViewHelperPlaceholder::APPEND, $key=null) {
		if ($this->captureLock)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Placeholder captures can not be nested.'));
		$this->captureLock = true;
		$this->captureType = $type;
		$this->captureKey = $key;
		ob_start();
		ob_implicit_flush(false);
	}

	public function end() {
		$content = ob_get_clean();
		$key = $this->captureKey;
		switch ($this->captureType) {
			case ViewHelperPlaceholder::SET :
				if ($key !== null)
					$this->{$this->captureKey} = $content;
				else
					$this->exchangeArray(array($content));
				break;
			case ViewHelperPlaceholder::PREPEND :
				if ($key !== null) {
					if (empty($this->{$key}))
						$this->{$key} = $content;
					else
						$this->{$key} = $content . $this->{$key};
				} else {
					$this->prepend($content);
				}
				break;
			case ViewHelperPlaceholder::APPEND :
				if ($key !== null) {
					if (empty($this->{$key}))
						$this->{$key} = $content;
					else
						$this->{$key} .= $content;
				} else {
					$keys = array_keys($this->getArrayCopy());
					if (count($keys) == 0)
						$nextIndex = 0;
					else
						$nextIndex = max($keys) + 1;
					$this[$nextIndex] = $content;
				}
				break;
		}
		$this->captureLock = false;
		$this->captureType = null;
		$this->captureKey = null;
	}

	public function __toString() {
		$content = $this->getArrayCopy();
		return implode('', $content);
	}
}