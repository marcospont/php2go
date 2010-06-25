<?php

class ViewHelperForm extends ViewHelper
{
	protected $afterRequiredLabel = '';
	protected $beforeRequiredLabel = '';
	protected $errorClass;
	protected $requiredClass;
	protected $html;
	protected $translator;
	protected $translatorDomain;

	public function __construct(View $view) {
		parent::__construct($view);
		$this->html = $this->view->html();
		$this->translator = $this->view->app->getTranslator();
		$this->translatorDomain = $this->translator->getFormDomain();
	}

	public function setAfterRequiredLabel($afterRequiredLabel) {
		$this->afterRequiredLabel = $afterRequiredLabel;
	}

	public function setBeforeRequiredLabel($beforeRequiredLabel) {
		$this->beforeRequiredLabel = $beforeRequiredLabel;
	}

	public function setErrorClass($class) {
		$this->errorClass = $class;
	}

	public function setRequiredClass($class) {
		$this->requiredClass = $class;
	}

	public function begin(array $attrs=array()) {
		$attrs['action'] = $this->view->url(@$attrs['action']);
		if (!isset($attrs['method']))
			$attrs['method'] = 'post';
		$this->defineId($attrs);
		return $this->html->openTag('form', $attrs);
	}

	public function end() {
		return '</form>';
	}

	public function beginFieldset(array $attrs=array()) {
		$this->defineId($attrs);
		return $this->html->openTag('fieldset', $attrs);
	}

	public function endFieldset() {
		return '</fieldset>';
	}

	public function label($for, $content, array $attrs=array()) {
		// required
		$content = $this->view->escape($content);
		if (isset($attrs['required'])) {
			if ($attrs['required']) {
				if ($this->requiredClass)
					$this->addClass($attrs, $this->requiredClass);
				$content = $this->beforeRequiredLabel . $content . $this->afterRequiredLabel;
			}
			unset($attrs['required']);
		}
		// error state
		if (isset($attrs['error'])) {
			if ($attrs['error'] && $this->errorClass)
				$this->addClass($attrs, $this->errorClass);
			unset($attrs['error']);
		}
		if ($for !== null)
			$attrs['for'] = $for;
		return $this->html->tag('label', $attrs, $content);
	}

	public function text($name, $value=null, array $attrs=array()) {
		return $this->input('text', $name, $value, $attrs);
	}

	public function textArea($name, $value=null, array $attrs=array()) {
		// error state
		if (isset($attrs['error'])) {
			if ($attrs['error'] && $this->errorClass)
				$this->addClass($attrs, $this->errorClass);
			unset($attrs['error']);
		}
		$attrs['name'] = $name;
		if (!isset($attrs['rows']))
			$attrs['rows'] = 5;
		if (!isset($attrs['cols']))
			$attrs['cols'] = 40;
		$this->defineId($attrs);
		return $this->html->tag('textarea', $attrs, ((isset($attrs['escape']) && !$attrs['escape']) ? $value : $this->view->escape($value)));
	}

	public function password($name, $value=null, array $attrs=array()) {
		return $this->input('password', $name, $value, $attrs);
	}

	public function hidden($name, $value=null, array $attrs=array()) {
		return $this->input('hidden', $name, $value, $attrs);
	}

	public function file($name, array $attrs=array()) {
		unset($attrs['value']);
		return $this->input('file', $name, null, $attrs);
	}

	public function checkBox($name, $checked=false, array $attrs=array()) {
		$value = Util::consumeArray($attrs, 'checkedValue', '1');
		if (!@$attrs['disabled'] && !strstr($name, '[]'))
			$uncheck = $this->hidden($name, Util::consumeArray($attrs, 'uncheckedValue', '0'));
		else
			$uncheck = '';
		return $uncheck . $this->input('checkbox', $name, $value, array_merge($attrs, array('checked' => $checked)));
	}

	public function checkBoxGroup($name, $value, array $options, array $attrs=array(), array $labelAttrs=array()) {
		return $this->inputGroup('checkbox', $name, $value, $options, $attrs, $labelAttrs);
	}

	public function radio($name, $value, $checked=false, array $attrs=array()) {
		return $this->input('radio', $name, $value, array_merge($attrs, array('checked' => $checked)));
	}

	public function radioGroup($name, $value, array $options, array $attrs=array(), array $labelAttrs=array()) {
		return $this->inputGroup('radio', $name, $value, $options, $attrs, $labelAttrs);
	}

	public function select($name, $value, array $options=array(), array $attrs=array()) {
		// error state
		if (isset($attrs['error'])) {
			if ($attrs['error'] && $this->errorClass)
				$this->addClass($attrs, $this->errorClass);
			unset($attrs['error']);
		}
		$attrs['name'] = $name;
		// multiple
		if (isset($attrs['multiple']) && $attrs['multiple'] && substr($attrs['name'], -2) != '[]')
			$attrs['name'] .= '[]';
		$this->defineId($attrs);
		return $this->html->tag('select', $attrs, $this->selectOptions($value, $options, $attrs));
	}

	public function button($label, array $attrs=array()) {
		return $this->input('button', null, $label, $attrs);
	}

	public function submit($label, array $attrs=array()) {
		return $this->input('submit', null, $label, $attrs);
	}

	public function reset($label, array $attrs=array()) {
		return $this->input('reset', null, $label, $attrs);
	}

	public function image($src, array $attrs=array()) {
		$attrs['src'] = $this->view->url($src);
		return $this->input('image', null, null, $attrs);
	}

	protected function input($type, $name, $value=null, array $attrs=array()) {
		// error state
		if (isset($attrs['error'])) {
			if ($attrs['error'] && $this->errorClass)
				$this->addClass($attrs, $this->errorClass);
			unset($attrs['error']);
		}
		$attrs['type'] = $type;
		if ($name !== null)
			$attrs['name'] = $name;
		if ($value !== null)
			$attrs['value'] = $value;
		$this->defineId($attrs);
		return $this->html->tag('input', $attrs);
	}

	protected function inputGroup($type, $name, $value=null, array $options, array $attrs=array(), array $labelAttrs=array()) {
		// error state
		if (isset($attrs['error'])) {
			if ($attrs['error'] && $this->errorClass)
				$this->addClass($attrs, $this->errorClass);
			unset($attrs['error']);
		}
		// validate label attrs
		$labelPlacement = Util::consumeArray($labelAttrs, 'placement', 'append');
		if (!in_array($labelPlacement, array('append', 'prepend')))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid placement: "%s"', array($labelPlacement)));
		// item separator
		$separator = Util::consumeArray($attrs, 'separator', $this->html->tag('br'));
		// ensure value is an array
		$value = (array)$value;
		// normalize input attrs
		$attrs['name'] = $name;
		if ($type == 'checkbox' && substr($attrs['name'], -2) != '[]')
			$attrs['name'] .= '[]';
		$attrs['type'] = $type;
		$this->defineId($attrs);
		// build radio group
		$group = array();
		foreach ($options as $optValue => $optLabel) {
			$optAttrs = $attrs;
			$optAttrs['id'] = $attrs['id'] . '-' . sizeof($group);
			$optAttrs['value'] = $optValue;
			if (in_array($optValue, $value))
				$optAttrs['checked'] = 'checked';
			else
				unset($optAttrs['checked']);
			$group[] = $this->html->openTag('label', array_merge($labelAttrs, array('for' => $optAttrs['id']))) .
						($labelPlacement == 'prepend' ? $this->view->escape($optLabel) : '') .
						$this->html->tag('input', $optAttrs) .
						($labelPlacement == 'append' ? $this->view->escape($optLabel) : '') .
						'</label>';
		}
		return implode($separator, $group);
	}

	protected function selectOptions($value, array $options, array &$attrs) {
		$content = '';
		$value = array_map('strval', (array)$value);
		if (isset($attrs['prompt']))
			$content .= $this->html->tag('option', array('value' => ''), $this->view->escape(Util::consumeArray($attrs, 'prompt')));
		elseif (!($disableEmpty = Util::consumeArray($attrs, 'disableEmpty', false)) && !isset($attrs['multiple']))
			$content .= $this->html->tag('option', array('value' => ''));
		foreach ($options as $key => $label) {
			if (is_array($label)) {
				$content .= $this->html->openTag('optgroup', array('label' => $this->view->escape($key)));
				foreach ($label as $optValue => $optLabel)
					$content .= $this->selectOption($optValue, $optLabel, $value);
				$content .= '</optgroup>';
			} else {
				$content .= $this->selectOption($key, $label, $value);
			}
 		}
 		return $content;
	}

	protected function selectOption($value, $label, $selected) {
		$optAttrs = array('value' => $value);
		if (is_array($label)) {
			if ($label[1])
				$optAttrs['disabled'] = 'disabled';
			if (in_array((string)$value, $selected))
				$optAttrs['selected'] = 'selected';
			return $this->html->tag('option', $optAttrs, $this->view->escape($label[0]));
		} else {
			if (in_array((string)$value, $selected))
				$optAttrs['selected'] = 'selected';
			return $this->html->tag('option', $optAttrs, $this->view->escape($label));
		}
	}

	protected function addClass(&$attrs, $class) {
		if (!isset($attrs['class'])) {
			$attrs['class'] = array($class);
		} else {
			if (!is_array($attrs['class']))
				$attrs['class'] = explode(' ', (string)$attrs['class']);
			if (!in_array($class, $attrs['class']))
				$attrs['class'][] = $class;
		}
	}
}