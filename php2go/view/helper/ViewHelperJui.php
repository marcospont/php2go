<?php

class ViewHelperJui extends ViewHelper
{
	public function __construct(View $view)  {
		parent::__construct($view);
		$this->view->jQuery()->enableUi();
	}

	public function button($label, array $attrs=array()) {
		$this->setupButton($attrs, 'button');
		return $this->view->html()->button($label, $attrs);
	}

	public function buttonTo($label, $url, array $attrs=array()) {
		$this->setupButton($attrs, 'button');
		return $this->view->html()->buttonTo($label, $url, $attrs);
	}

	public function submitButton($label, array $attrs=array()) {
		$this->setupButton($attrs, 'submit');
		return $this->view->html()->button($label, $attrs);
	}

	public function resetButton($label, array $attrs=array()) {
		$this->setupButton($attrs, 'reset');
		return $this->view->html()->button($label, $attrs);
	}

	public function ajaxButton($label, $url, array $options=array(), array $attrs=array()) {
		$this->defineId($attrs, 'JuiButton');
		$this->view->html()->event($attrs['id'], 'click', $this->view->ajax(array_merge($options, array(
			'url' => $this->view->url($url)
		))));
		return $this->button($label, $attrs);
	}

	protected function setupButton(&$attrs, $type) {
		$this->defineId($attrs, 'JuiButton');
		$this->view->jQuery()->addCallById($attrs['id'], 'button', array(array(
			'icons' => array(
				'primary' => Util::consumeArray($attrs, 'primaryIcon'),
				'secondary' => Util::consumeArray($attrs, 'secondaryIcon')
			)
		)));
		$attrs['type'] = $type;
	}
}