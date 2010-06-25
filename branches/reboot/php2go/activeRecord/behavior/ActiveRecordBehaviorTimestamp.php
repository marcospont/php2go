<?php

class ActiveRecordBehaviorTimestamp extends ActiveRecordBehavior
{
	protected $insert = array();
	protected $update = array();
	
	public function setInsert($attrs) {
		if (is_string($attrs))
			$attrs = explode(',', $attrs);
		elseif (!is_array($attrs))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid attributes specification.'));
		$this->insert = $attrs;
	}
	
	public function setUpdate($attrs) {
		if (is_string($attrs))
			$attrs = explode(',', $attrs);
		elseif (!is_array($attrs))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid attributes specification.'));
		$this->update = $attrs;
	}
	
	public function onBeforeInsert(Event $event) {
		foreach ($this->insert as $attr)
			$this->owner->{$attr} = date('Y-m-d H:i:s');
	}
	
	public function onBeforeUpdate(Event $event) {
		foreach ($this->update as $attr)
			$this->owner->{$attr} = date('Y-m-d H:i:s');
	}	
}