<?php

abstract class ActiveRecordBehavior extends ModelBehavior
{
	public function events() {
		return array_merge(parent::events(), array(
			'onLoad' => 'onLoad',
			'onBeforeSave' => 'onBeforeSave',
			'onAfterSave' => 'onAfterSave',
			'onBeforeInsert' => 'onBeforeInsert',
			'onAfterInsert' => 'onAfterInsert',
			'onBeforeUpdate' => 'onBeforeUpdate',
			'onAfterUpdate' => 'onAfterUpdate',
			'onBeforeDelete' => 'onBeforeDelete',
			'onAfterDelete' => 'onAfterDelete'
		));
	}
	
	public function onLoad(Event $event) {		
	}
	
	public function onBeforeSave(Event $event) {		
	}
	
	public function onAfterSave(Event $event) {		
	}
	
	public function onBeforeInsert(Event $event) {		
	}
	
	public function onAfterInsert(Event $event) {		
	}
	
	public function onBeforeUpdate(Event $event) {		
	}
	
	public function onAfterUpdate(Event $event) {		
	}
	
	public function onBeforeDelete(Event $event) {		
	}
	
	public function onAfterDelete(Event $event) {		
	}
}