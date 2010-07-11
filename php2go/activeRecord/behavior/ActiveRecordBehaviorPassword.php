<?php

class ActiveRecordBehaviorPassword extends ActiveRecordBehavior
{
	private static $defaultOptions = array(
		'hashFunction' => 'md5'
	);
	protected $attrs;

	public function setAttrs($attrs) {
		if (is_string($attrs))
			$attrs = explode(',', $attrs);
		elseif (!is_array($attrs))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid attributes specification.'));
		foreach ($attrs as $name => $options) {
			if (is_int($name) && is_string($options))
				$this->attrs[$options] = self::$defaultOptions;
			elseif (is_string($name) && Util::isMap($options))
				$this->attrs[$name] = $options;
			else
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid attributes specification.'));
		}
	}

	public function onLoad(Event $event) {
		foreach (array_keys($this->attrs) as $attr)
			$this->owner->{$attr} = null;
	}

	public function onBeforeSave(Event $event) {
		foreach ($this->attrs as $attr => $options) {
			$value = $this->owner->{$attr};
			if (!Util::isEmpty($value) && isset($options['hashFunction']))
				$this->owner->{$attr} = $options['hashFunction']($value);
		}
		return true;
	}
}