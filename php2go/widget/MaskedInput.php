<?php

class MaskedInput extends WidgetInput
{
	protected $mask;
	protected $definitions = array();

	public function setMask($mask) {
		$this->mask = $this->resolveMask($mask);
	}

	public function setCompleted($completed) {
		$this->params['completed'] = Js::callback($completed);
	}

	public function setPlaceholder($placeholder) {
		$this->params['placeholder'] = $placeholder;
	}

	public function setDefinitions(array $definitions) {
		$this->definitions = $definitions;
	}

	public function preInit() {
		parent::preInit();
		$this->view->head()->addLibrary('jquery-maskedinput');
	}

	public function run() {
		if ($this->hasModel())
			echo $this->view->model()->text($this->model, $this->modelAttr, $this->attrs);
		else
			echo $this->view->form()->text($this->name, $this->value, $this->attrs);
		$this->view->jQuery()
			->addOnLoad($this->renderDefinitions())
			->addCallById($this->getId(),
				'mask', array($this->mask, $this->params)
			);
	}

	protected function getDefaultParams() {
		return array(
			'placeholder' => '_'
		);
	}

	protected function renderDefinitions() {
		if (!empty($this->definitions)) {
			$buf = array();
			foreach ($this->definitions as $from => $to)
				$buf[] = '$.mask.definitions["' . $from . '"] = "' . Js::escape($to) . '";';
			return implode(PHP_EOL, $buf);
		}
		return '';
	}

	protected function resolveMask($mask) {
		$locale = Php2Go::app()->getLocale();
		switch ($mask) {
			case 'date' :
				$format = $locale->getDateFormat('medium');
				return strtr($format, 'dMy', '999');
		}
		return $mask;
	}
}