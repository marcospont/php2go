<?php

class SWFObject extends WidgetElement
{
	protected $url;
	protected $width;
	protected $height;
	protected $version = '9';
	protected $expressInstallUrl;
	protected $flashVars = array();

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

	public function setParams(array $params) {
		$this->params = $params;
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
		$id = Util::consumeArray($this->attrs, 'id');
		$result =
			'swfobject.embedSWF(' .
			'"' . Js::escape($this->url) . '", ' .
			'"' . Js::escape($id) . '", ' .
			'"' . $this->width . '", ' .
			'"' . $this->height . '", ' .
			'"' . Js::escape($this->version) . '", ' .
			(isset($this->expressInstallUrl) ? '"' . Js::escape($this->expressInstallUrl) . '", ' : 'false, ') .
			(!empty($this->flashVars) ? Js::encode($this->flashVars) : Js::emptyObject()) . ', ' .
			(!empty($this->params) ? Js::encode($this->params) : Js::emptyObject()) . ', ' .
			(!empty($this->attrs) ? Js::encode($this->attrs) : Js::emptyObject()) . ', ' .
			(isset($this->callback) ? Js::callback($this->callback) : 'null') . ');';
		$this->view->scriptBuffer()->add($result);
	}
}