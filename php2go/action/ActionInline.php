<?php

class ActionInline extends Action
{
	public function run() {
		$method = 'action' . $this->getId();
		$this->controller->{$method}();
	}
}