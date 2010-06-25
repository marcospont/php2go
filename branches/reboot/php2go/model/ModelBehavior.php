<?php

abstract class ModelBehavior extends Behavior
{
	public function events() {
		return array(
			'onImport' => 'onImport',
			'onBeforeValidate' => 'onBeforeValidate',
			'onAfterValidate' => 'onAfterValidate'
		);
	}
	
	public function onImport(Event $event) {		
	}
	
	public function onBeforeValidate(Event $event) {		
	}
	
	public function onAfterValidate(Event $event) {		
	}
}