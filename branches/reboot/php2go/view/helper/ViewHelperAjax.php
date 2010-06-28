<?php

class ViewHelperAjax extends ViewHelper
{
	public function ajax(array $options) {
		$options = array_merge(array(
			'url' => Js::identifier('location.href'),
			'cache' => false
		), $options);
		if (isset($options['beforeSend']))
			$options['beforeSend'] = Js::callback($options['beforeSend'], array('request'));
		if (isset($options['complete']))
			$options['complete'] = Js::callback($options['complete'], array('request', 'textStatus'));
		if (($confirm = Util::consumeArray($options, 'confirm')))
			$confirm = 'confirm("' . Js::escape($confirm) . '")';
		if (isset($options['dataFilter']))
			$options['dataFilter'] = Js::callback($options['dataFilter'], array('response', 'type'));
		if (isset($options['error']))
			$options['error'] = Js::callback($options['error'], array('request', 'textStatus', 'error'));
		if (isset($options['success']))
			$options['success'] = Js::callback($options['success'], array('response', 'textStatus', 'request'));
		if (isset($options['update'])) {
			if (!isset($options['success']))
				$options['success'] = Js::func('$("#' . $options['update'] . '").html(html);', array('html'));
			unset($options['update']);
		}
		if (isset($options['replace'])) {
			if (!isset($options['success']))
				$options['success'] = Js::func('$("#' . $options['update'] . '").replaceWith(html);', array('html'));
			unset($options['replace']);
		}
		$ajax = '$.ajax(' . Js::encode($options) . ');' . PHP_EOL;
		return ($confirm ? 'if (' . $confirm . ') {' . PHP_EOL . $ajax . '}' . PHP_EOL : $ajax);
	}

	public function link($url, $content, array $options=array(), array $attrs=array()) {
		if (!isset($attrs['id']))
			$attrs['id'] = Util::id('AjaxLink');
		$this->view->html()->event($attrs['id'], 'click', $this->ajax(array_merge($options, array(
			'url' => $this->view->url($url)
		))));
		return $this->view->html()->link('#', $content, $attrs);
	}

	public function button($url, $content, array $options=array(), array $attrs=array()) {
		$this->defineId($attrs, 'AjaxButton');
		$this->view->html()->event($attrs['id'], 'click', $this->ajax(array_merge($options, array(
			'url' => $this->view->url($url)
		))));
		$attrs['type'] = 'button';
		return $this->view->html()->button($content, $attrs);
	}
}