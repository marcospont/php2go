<?php

class StarRating extends WidgetInput
{
	protected $options = array();

	public function setCallback($callback) {
		$this->params['callback'] = Js::callback($callback, array('ui', 'type', 'value'));
	}

	public function setCancelClass($cancelClass) {
		$this->params['cancelClass'] = $cancelClass;
	}

	public function setCancelDisabledClass($cancelDisabledClass) {
		$this->params['cancelDisabledClass'] = $cancelDisabledClass;
	}

	public function setCancelHoverClass($cancelHoverClass) {
		$this->params['cancelHoverClass'] = $cancelHoverClass;
	}

	public function setCancelShow($cancelShow) {
		$this->params['cancelShow'] = (bool)$cancelShow;
	}

	public function setCancelTitle($cancelTitle) {
		$this->params['cancelTitle'] = $cancelTitle;
	}

	public function setCancelValue($cancelValue) {
		$this->params['cancelValue'] = $cancelValue;
	}

	public function setCaptionEl($captionEl) {
		$this->params['captionEl'] = $this->view->jQuery()->selector('#' . ltrim($captionEl, '#'));
	}

	public function setDisabled($disabled) {
		$this->params['disabled'] = (bool)$disabled;
	}

	public function setDisableValue($disableValue) {
		$this->params['disableValue'] = $disableValue;
	}

	public function setOneVoteOnly($oneVoteOnly) {
		$this->params['oneVoteOnly'] = (bool)$oneVoteOnly;
	}

	public function setOptions(array $options) {
		$this->options = $options;
	}

	public function setShowTitles($showTitles) {
		$this->params['showTitles'] = (bool)$showTitles;
	}

	public function setSplit($split) {
		$this->params['split'] = (int)$split;
	}

	public function setStarClass($starClass) {
		$this->params['starClass'] = $starClass;
	}

	public function setStarDisabledClass($starDisabledClass) {
		$this->params['starDisabledClass'] = $starDisabledClass;
	}

	public function setStarHoverClass($starHoverClass) {
		$this->params['starHoverClass'] = $starHoverClass;
	}

	public function setStarOnClass($starOnClass) {
		$this->params['starOnClass'] = $starOnClass;
	}

	public function setStarWidth($starWidth) {
		$this->params['starWidth'] = (int)$starWidth;
	}

	public function preInit() {
		parent::preInit();
		$this->view->head()->addLibrary('jquery-stars');
	}

	public function run() {
		if (!empty($this->options)) {
			echo '<div' . $this->renderAttrs() . '>';
			$name = ($this->hasModel() ? $this->getNameByModelAttr($this->model, $this->modelAttr) : $this->name);
			$value = ($this->hasModel() ? $this->model->{$this->modelAttr} : $this->value);
			foreach ($this->options as $optValue => $optTitle) {
				echo $this->view->form()->radio($name, $optValue, ($optValue == $value), array('title' => $optTitle));
			}
			echo '</div>' . PHP_EOL;
			$this->view->jQuery()->addCallById($this->getId(),
				'stars', array((!empty($this->params) ? $this->params : Js::emptyObject()))
			);
		}
	}

	public function getDefaultParams() {
		return array(
			'cancelTitle' => __(PHP2GO_LANG_DOMAIN, 'Cancel Rating')
		);
	}
}