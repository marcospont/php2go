<?php

abstract class WidgetCapture extends WidgetElement
{
	public function init() {
		ob_start();
		ob_implicit_flush(false);
	}
	
	public function run() {
		$this->capture(ob_get_clean());
	}
	
	abstract protected function capture($content);
}