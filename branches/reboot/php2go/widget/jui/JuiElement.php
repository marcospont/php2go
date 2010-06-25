<?php

abstract class JuiElement extends WidgetElement
{
	public function setDisabled($disabled) {
		$this->params['disabled'] = (bool)$disabled;
	}

	public function preInit() {
		parent::preInit();
		$this->view->jQuery()->enableUi();
	}

	protected function getSetupParams() {
		$params = array_merge($this->params, $this->jsListeners);
		if (!empty($params))
			return $params;
		return Js::emptyObject();
	}
}