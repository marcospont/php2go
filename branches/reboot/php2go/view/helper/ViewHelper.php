<?php

abstract class ViewHelper extends Component
{
	protected $view;
	
	public function __construct(View $view) {
		$this->view = $view;
	}
	
	public function __toString() {
		try {
			if (method_exists($this, 'toString'))
				return $this->toString();
			return get_class($this);
		} catch (Exception $e) {
			Php2Go::app()->handleException($e);
		}
	}
}