<?php

class JuiUploader extends JuiElement
{
	protected $baseUrl;
	protected $height = 200;
	protected $messages = array();

	public function setAssumeSuccessTimeout($assumeSuccessTimeout) {
		$this->params['assumeSuccessTimeout'] = (int)$assumeSuccessTimeout;
	}

	public function setDebug($debug) {
		$this->params['debug'] = (bool)$debug;
	}

	public function setDisabled($disabled) {
		$this->params['disabled'] = (bool)$disabled;
	}

	public function setHeight($height) {
		$this->height = $height;
	}

	public function setFileParamName($fileParamName) {
		$this->params['fileParamName'] = $fileParamName;
	}

	public function setFileTypes(array $fileTypes) {
		$this->params['fileTypes'] = $fileTypes;
	}

	public function setFileTypesDescription($fileTypesDescription) {
		$this->params['fileTypesDescription'] = $fileTypesDescription;
	}

	public function setHttpSuccess($httpSuccess) {
		if (is_string($httpSuccess))
			$httpSuccess = explode(',', $httpSuccess);
		$this->params['httpSuccess'] = $httpSuccess;
	}

	public function setMethod($method) {
		$this->params['method'] = strtoupper($method);
	}

	public function setMultiple($multiple) {
		$this->params['multiple'] = (bool)$multiple;
	}

	public function setParams(array $params) {
		$this->params['params'] = $params;
	}

	public function setQueueLimit($queueLimit) {
		$this->params['queueLimit'] = (int)$queueLimit;
	}

	public function setUploadUrl($uploadUrl) {
		$this->params['uploadUrl'] = $this->view->url($uploadUrl);
	}

	public function setUseQueryString($useQueryString) {
		$this->params['useQueryString'] = (bool)$useQueryString;
	}

	public function preInit() {
		parent::preInit();
		$this->messages = array(
			'headerName' => __(PHP2GO_LANG_DOMAIN, 'Name'),
			'headerSize' => __(PHP2GO_LANG_DOMAIN, 'Size'),
			'headerStatus' => __(PHP2GO_LANG_DOMAIN, 'Status'),
			'titleRemove' => __(PHP2GO_LANG_DOMAIN, 'Remove'),
			'buttonAdd' => __(PHP2GO_LANG_DOMAIN, 'Add'),
			'buttonSend' => __(PHP2GO_LANG_DOMAIN, 'Send'),
			'buttonClear' => __(PHP2GO_LANG_DOMAIN, 'Clear')
		);
		if (!$this->view->head()->hasLibrary('jquery-uploader')) {
			$this->view->head()->addLibrary('jquery-uploader');
			$url = Php2Go::app()->getAssetManager()->getPublishedUrl(Php2Go::getPathAlias('php2go.library.jquery.jquery-uploader'));
			$this->view->jQuery()
				->addOnLoad('$.uploader.swfUrl = "' . Js::escape($url . '/swfupload.swf') . '";')
				->addOnLoad('$.uploader.messages = ' . Js::encode(array(
					'swfError' => __(PHP2GO_LANG_DOMAIN, 'JuiUploader component requires at least Flash Player %s.'),
					'httpError' => __(PHP2GO_LANG_DOMAIN, 'Internal server error.'),
					'success' => __(PHP2GO_LANG_DOMAIN, 'File "%s" uploaded successfully.')
				)));
		}
	}

	public function run() {
		$this->view->jQuery()->addCallById($this->getId(),
			'uploader', array($this->getSetupParams())
		);
		$this->render('uploader');
	}

	protected function getSetupParams() {
		if (!isset($this->params['params']))
			$this->params['params'] = array();
		$this->params['params'][Session::getName()] = Session::getId();
		return parent::getSetupParams();
	}
}