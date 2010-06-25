<?php

class ViewHelperModel extends ViewHelper
{
	protected $form;

	public function __construct(View $view) {
		parent::__construct($view);
		$this->form = $this->view->form();
	}

	public function label(Model $model, $attr, array $attrs=array()) {
		if (isset($attrs['for']))
			$for = Util::consumeArray($attrs, 'for');
		else
			$for = $this->getIdByModelAttr($model, $attr);
		if (isset($attrs['content']))
			$content = Util::consumeArray($attrs, 'content');
		else
			$content = $model->getAttributeLabel($attr);
		$attrs['required'] = $model->isAttributeRequired($attr);
		$attrs['error'] = $model->hasErrors($attr);
		return $this->form->label($for, $content, $attrs);
	}

	public function text(Model $model, $attr, array $attrs=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		$attrs['error'] = $model->hasErrors($attr);
		return $this->form->text($name, $model->{$attr}, $attrs);
	}

	public function textArea(Model $model, $attr, array $attrs=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		$attrs['error'] = $model->hasErrors($attr);
		return $this->form->textArea($name, $model->{$attr}, $attrs);
	}

	public function password(Model $model, $attr, array $attrs=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		$attrs['error'] = $model->hasErrors($attr);
		return $this->form->password($name, null, $attrs);
	}

	public function hidden(Model $model, $attr, array $attrs=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		return $this->form->hidden($name, $model->{$attr}, $attrs);
	}

	public function file(Model $model, $attr, array $attrs=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		$attrs['error'] = $model->hasErrors($attr);
		return $this->form->file($name, $attrs);
	}

	public function checkBox(Model $model, $attr, array $attrs=array()) {
		$checkedValue = (isset($attrs['checkedValue']) ? $attrs['checkedValue'] : '1');
		$name = $this->defineName($model, $attr, $attrs);
		$attrs['error'] = $model->hasErrors($attr);
		return $this->form->checkBox($name, ($model->{$attr} == $checkedValue), $attrs);
	}

	public function checkBoxGroup(Model $model, $attr, array $options, array $attrs=array(), array $labelAttrs=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		$attrs['error'] = $model->hasErrors($attr);
		return $this->form->checkBoxGroup($name, $model->{$attr}, $options, $attrs, $labelAttrs);
	}

	public function radioGroup(Model $model, $attr, array $options, array $attrs=array(), array $labelAttrs=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		$attrs['error'] = $model->hasErrors($attr);
		return $this->form->radioGroup($name, $model->{$attr}, $options, $attrs, $labelAttrs);
	}

	public function select(Model $model, $attr, array $options=array(), array $attrs=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		if (!array_key_exists('disableEmpty', $attrs) && $model->isAttributeRequired($attr))
			$attrs['disableEmpty'] = true;
		$attrs['error'] = $model->hasErrors($attr);
		return $this->form->select($name, $model->{$attr}, $options, $attrs);
	}

	public function errorSummary(Model $model, $headerTemplate='<p>%s</p>', $headerText=null) {
		$items = array();
		foreach ($model->getErrors() as $attr => $errors) {
			if ($attr === 0) {
				foreach ($errors as $error)
					$items[] = '<p>' . $this->view->escape($error) . '</p>';
			} else {
				$items[] = '<p>' . $this->view->escape($errors[0]) . '</p>';
			}
		}
		if (!empty($items)) {
			if ($headerText === null)
				$headerText = __(PHP2GO_LANG_DOMAIN, 'The following errors were found:');
			return
				sprintf($headerTemplate, $headerText) .
				implode('', $items) . PHP_EOL;
		}
		return '';
	}

	public function error(Model $model, $attr, array $attrs=array(), $tagName='div') {
		if (($errors = $model->getErrors($attr))) {
			return $this->view->html()->tag($tagName, $attrs, $this->view->escape($errors[0]));
		}
		return '';
	}

	protected function defineName(Model $model, &$attr, array $attrs) {
		if (isset($attrs['name']))
			return $attrs['name'];
		return $this->getNameByModelAttr($model, $attr);
	}
}