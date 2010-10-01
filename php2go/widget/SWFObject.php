<?php

class SWFObject extends WidgetElement
{
	protected $url;
	protected $width;
	protected $height;
	protected $version = '9';
	protected $expressInstallUrl;
	protected $flashVars = array();
	protected $flashParams = array();
	protected $flashAttrs = array();

	public function setUrl($url) {
		$this->url = $this->view->url($url);
	}

	public function setWidth($width) {
		$this->width = $width;
	}

	public function setHeight($height) {
		$this->height = $height;
	}

	public function setVersion($version) {
		$this->version = $version;
	}

	public function setExpressInstallUrl($url) {
		$this->expressInstallUrl = $url;
	}

	public function setFlashVars(array $vars) {
		$this->flashVars = $vars;
	}

	public function setFlashParams(array $params) {
		$this->flashParams = $params;
	}

	public function setFlashAttrs(array $attrs) {
		$this->flashAttrs = $attrs;
	}

	public function preInit() {
		parent::preInit();
		$this->view->head()->addLibrary('swfobject');
	}

	public function init() {
		echo '<div id="' . $this->getId() . '">' . PHP_EOL;
	}

	public function run() {
		echo '</div>';
		$this->renderEmbed();
	}

	public function renderEmbed() {
		$expressInstallUrl = (isset($this->expressInstallUrl) ? '"' . Js::escape($this->expressInstallUrl) . '"' : 'null');
		$flashVars = (!empty($this->flashVars) ? Js::encode($this->flashVars) : 'null');
		$flashParams = (!empty($this->flashParams) ? Js::encode($this->flashParams) : 'null');
		$flashAttrs = (!empty($this->flashAttrs) ? Js::encode($this->flashAttrs) : 'null');
		$this->view->scriptBuffer()->add('swfobject.embedSWF("' . Js::escape($this->url) . '", "' . Js::escape($this->getId()) . '", "' . $this->width . '", "' . $this->height . '", "' . $this->version . '", ' . $expressInstallUrl . ', ' . $flashVars . ', ' . $flashParams . ', ' . $flashAttrs . ');', 'domReady');
	}
}