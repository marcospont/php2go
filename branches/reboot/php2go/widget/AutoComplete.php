<?php

class AutoComplete extends WidgetInput
{
	protected $dataOrUrl;
	protected $textArea = false;

	public function setAutoFill($autoFill) {
		$this->params['autoFill'] = (bool)$autoFill;
	}

	public function setCacheLength($cacheLength) {
		$this->params['cacheLength'] = (int)$cacheLength;
	}

	public function setData(array $data) {
		$this->dataOrUrl = $data;
	}

	public function setDataType($dataType) {
		$this->params['dataType'] = $dataType;
	}

	public function setDelay($delay) {
		$this->params['delay'] = (int)$delay;
	}

	public function setExtraParams(array $extraParams) {
		$this->params['extraParams'] = $extraParams;
	}

	public function setFormatItem($formatItem) {
		$this->params['formatItem'] = Js::callback($formatItem, array('row', 'index', 'length', 'key', 'term'));
	}

	public function setFormatMatch($formatMatch) {
		$this->params['formatMatch'] = Js::callback($formatMatch, array('row', 'index', 'length'));
	}

	public function setFormatResult($formatResult) {
		$this->params['formatResult'] = Js::callback($formatResult, array('row', 'key'));
	}

	public function setHighlight($highlight) {
		$this->params['highlight'] = Js::callback($highlight);
	}

	public function setInputClass($inputClass) {
		$this->params['inputClass'] = $inputClass;
	}

	public function setLoadingClass($loadingClass) {
		$this->params['loadingClass'] = $loadingClass;
	}

	public function setMatchCase($matchCase) {
		$this->params['matchCase'] = (bool)$matchCase;
	}

	public function setMatchContains($matchContains) {
		$this->params['matchContains'] = $matchContains;
	}

	public function setMatchSubset($matchSubset) {
		$this->params['matchSubset'] = (bool)$matchSubset;
	}

	public function setMax($max) {
		$this->params['max'] = (int)$max;
	}

	public function setMinChars($minChars) {
		$this->params['minChars'] = (int)$minChars;
	}

	public function setMultiple($multiple) {
		$this->params['multiple'] = (bool)$multiple;
	}

	public function setMultipleSeparator($multipleSeparator) {
		$this->params['multipleSeparator'] = $multipleSeparator;
	}

	public function setMustMatch($mustMatch) {
		$this->params['mustMatch'] = (bool)$mustMatch;
	}

	public function setParse($parse) {
		$this->params['parse'] = Js::callback($parse, array('data'));
	}

	public function setResultsClass($resultsClass) {
		$this->params['resultsClass'] = $resultsClass;
	}

	public function setScroll($scroll) {
		$this->params['scroll'] = (bool)$scroll;
	}

	public function setScrollHeight($scrollHeight) {
		$this->params['scrollHeight'] = (int)$scrollHeight;
	}

	public function setSelectFirst($selectFirst) {
		$this->params['selectFirst'] = (bool)$selectFirst;
	}

	public function setTextArea($textArea) {
		$this->textArea = (bool)$textArea;
	}

	public function setWidth($width) {
		$this->params['width'] = (int)$width;
	}

	public function setUrl($url) {
		$this->dataOrUrl = $this->view->url($url);
	}

	public function preInit() {
		parent::preInit();
		$this->view->head()->addLibrary('jquery-autocomplete');
	}

	public function run() {
		if ($this->hasModel()) {
			if ($this->textArea)
				echo $this->view->model()->textArea($this->model, $this->modelAttr, $this->attrs);
			else
				echo $this->view->model()->text($this->model, $this->modelAttr, $this->attrs);
		} else {
			if ($this->textArea)
				echo $this->view->form()->textArea($this->name, '', $this->attrs);
			else
				echo $this->view->form()->text($this->name, '', $this->attrs);
		}
		$this->view->jQuery()->addCallById($this->getId(),
			'autocomplete', array($this->dataOrUrl, (!empty($this->params) ? $this->params : Js::emptyObject()))
		);
	}
}