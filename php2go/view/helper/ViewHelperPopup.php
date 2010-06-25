<?php

class ViewHelperPopup extends ViewHelper
{
	public function popup($url, array $options=array()) {
		if (isset($options['confirm']))
			$confirm = 'confirm("' . Js::escape($options['confirm']) . '")';
		else
			$confirm = null;
		$url = $this->view->url($url);
		$name = Util::consumeArray($options, 'name', '');
		$specs = Util::consumeArray($options, 'specs', '');
		$replace = (isset($options['replace']) && $options['replace'] ? 'true' : 'false');
		$focus = (isset($options['focus']) && $options['focus']);
		$popup = 'var w = window.open("' . Js::escape($url) . '", "' . Js::escape($name) . '", "' . Js::escape($specs) . '", ' . $replace . ');';
		$popup .= ($focus ? 'w.focus();' : '') . PHP_EOL;
		return ($confirm ? 'if (' . $confirm . ') {' . PHP_EOL . $popup . '}' . PHP_EOL : $popup);
	}

	public function link($label, $url, array $options=array(), array $attrs=array()) {
		if (!isset($attrs['id']))
			$attrs['id'] = Util::id('PopupLink');
		$this->view->html()->event($attrs['id'], 'click', $this->popup($url, $options));
		return $this->view->html()->link($label, '#', $attrs);
	}

	public function button($label, $url, array $options=array(), array $attrs=array()) {
		$this->defineId($attrs, 'PopupButton');
		$this->view->html()->event($attrs['id'], 'click', $this->popup($url, $options));
		$attrs['type'] = 'button';
		return $this->view->html()->button($label, $attrs);
	}
}