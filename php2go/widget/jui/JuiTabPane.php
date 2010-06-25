<?php

class JuiTabPane extends WidgetCapture
{
	protected $url;
	protected $label;
	protected $content;
	
	public function getUrl() {
		return $this->url;
	}
	
	public function setUrl($url) {
		$this->url = $this->view->url($url);
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function setLabel($label) {
		$this->label = $label;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
	protected function capture($content) {
		if ($this->content === null)
			$this->content = $content;
		$this->parent->addPane($this);
	}
}