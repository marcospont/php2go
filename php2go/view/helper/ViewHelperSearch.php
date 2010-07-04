<?php

class ViewHelperSearch extends ViewHelper
{
	protected $form;

	public function __construct(View $view) {
		parent::__construct($view);
		$this->form = $this->view->form();
	}

	public function textFilter(SearchModel $model, $attr, array $textAttrs=array(), array $selectAttrs=array(), array $options=array()) {
		$name = $this->defineName($model, $attr, $textAttrs);
		$value = $model->{$attr};
		$textAttrs['error'] = $model->hasErrors($attr . '[val]');
		$selectOptions = array(
			'containing' => __(PHP2GO_LANG_DOMAIN, 'Containing'),
			'starting' => __(PHP2GO_LANG_DOMAIN, 'Starting with'),
			'ending' => __(PHP2GO_LANG_DOMAIN, 'Ending with'),
			'eq' => __(PHP2GO_LANG_DOMAIN, 'Equal to'),
			'neq' => __(PHP2GO_LANG_DOMAIN, 'Not Equal to')
		);
		$selectAttrs['disableEmpty'] = true;
		$separator = Util::consumeArray($options, 'separator', '&nbsp;');
		return $this->form->select($name . '[op]', (is_array($value) ? $value['op'] : null), $selectOptions, $selectAttrs) . $separator .
			$this->form->text($name . '[val]', (is_array($value) ? $value['val'] : null), $textAttrs);
	}

	public function filter(SearchModel $model, $attr, array $textAttrs=array(), array $selectAttrs=array(), array $options=array()) {
		$name = $this->defineName($model, $attr, $textAttrs);
		$value = $model->{$attr};
		$textAttrs['error']	= $model->hasErrors($attr . '[val]');
		$selectOptions = array(
			'eq' => __(PHP2GO_LANG_DOMAIN, 'Equal to'),
			'neq' => __(PHP2GO_LANG_DOMAIN, 'Not equal to'),
			'gt' => __(PHP2GO_LANG_DOMAIN, 'Greater than'),
			'goet' => __(PHP2GO_LANG_DOMAIN, 'Greater or equal than'),
			'lt' => __(PHP2GO_LANG_DOMAIN, 'Less than'),
			'loet' => __(PHP2GO_LANG_DOMAIN, 'Less or equal than')
		);
		$separator = Util::consumeArray($options, 'separator', '&nbsp;');
		return $this->form->select($name . '[op]', (is_array($value) ? $value['op'] : null), $selectOptions, $selectAttrs) . $separator .
			$this->form->text($name . '[val]', (is_array($value) ? $value['val'] : null), $textAttrs);
	}

	public function interval(SearchModel $model, $attr, array $attrs=array(), array $options=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		$value = $model->{$attr};
		$surround = Util::consumeArray($options, 'surround', '%s&nbsp;%s');
		$startAttrs = array_merge($attrs, array('error' => $model->hasErrors($attr . '[start]')));
		$endAttrs = array_merge($attrs, array('error' => $model->hasErrors($attr . '[end]')));
		return sprintf($surround,
			$this->form->text($name . '[start]', (is_array($value) ? $value['start'] : null), $startAttrs),
			$this->form->text($name . '[end]', (is_array($value) ? $value['end'] : null), $endAttrs)
		);
	}

	public function selectInterval(SearchModel $model, $attr, array $startOpts=array(), array $endOpts=array(), array $attrs=array(), array $options=array()) {
		$name = $this->defineName($model, $attr, $attrs);
		$value = $model->{$attr};
		$surround = Util::consumeArray($options, 'surround', '%s&nbsp;%s');
		$startAttrs = array_merge($attrs, array('error' => $model->hasErrors($attr . '[start]')));
		$endAttrs = array_merge($attrs, array('error' => $model->hasErrors($attr . '[end]')));
		return sprintf($surround,
			$this->form->select($name . '[start]', (is_array($value) ? $value['start'] : null), $startOpts, $startAttrs),
			$this->form->select($name . '[end]', (is_array($value) ? $value['end'] : null), $endOpts, $endAttrs)
		);
	}

	protected function defineName(SearchModel $model, &$attr, array $attrs) {
		if (isset($attrs['name']))
			return $attrs['name'];
		return $this->getNameByModelAttr($model, $attr);
	}
}