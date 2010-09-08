<?php

class ContentWrapper extends WidgetCapture
{
	protected $layout;

	public function setLayout($layout) {
		$this->layout = $layout;
	}

	public function init() {
		parent::init();
		if (!isset($this->layout))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, '"%s" widget requires the "layout" option.', array(get_class($this))));
	}

	public function capture($content) {
		echo $this->view->renderPartial($this->layout, array(
			'content' => $content
		));
	}
}