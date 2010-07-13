<?php

class ImageCropper extends WidgetElement
{
	protected $params = array();

	public function setAddClass($addClass) {
		$this->params['addClass'] = $addClass;
	}

	public function setAllowMove($allowMove) {
		$this->params['allowMove'] = (bool)$allowMove;
	}

	public function setAllowResize($allowResize) {
		$this->params['allowResize'] = (bool)$allowResize;
	}

	public function setAllowSelect($allowSelect) {
		$this->params['allowSelect'] = (bool)$allowSelect;
	}

	public function setAnimationDelay($animationDelay) {
		$this->params['animationDelay'] = (int)$animationDelay;
	}

	public function setAspectRatio($aspectRatio) {
		$this->params['aspectRatio'] = $aspectRatio;
	}

	public function setBaseClass($baseClass) {
		$this->params['baseClass'] = $baseClass;
	}

	public function setBgColor($bgColor) {
		$this->params['bgColor'] = $bgColor;
	}

	public function setBgOpacity($bgOpacity) {
		$this->params['bgOpacity'] = (float)$bgOpacity;
	}

	public function setBorderOpacity($borderOpacity) {
		$this->params['borderOpacity'] = (float)$borderOpacity;
	}

	public function setBoundary($boundary) {
		$this->params['boundary'] = (int)$boundary;
	}

	public function setBoxHeight($boxHeight) {
		$this->params['boxHeight'] = (int)$boxHeight;
	}

	public function setBoxWidth($boxWidth) {
		$this->params['boxWidth'] = (int)$boxWidth;
	}

	public function setCornerHandles($cornerHandles) {
		$this->params['cornerHandles'] = (bool)$cornerHandles;
	}

	public function setDisabled($disabled) {
		$this->params['disabled'] = (bool)$disabled;
	}

	public function setDragEdges($dragEdges) {
		$this->params['dragEdges'] = (bool)$dragEdges;
	}

	public function setDrawBorders($drawBorders) {
		$this->params['drawBorders'] = (bool)$drawBorders;
	}

	public function setEdgeMargin($edgeMargin) {
		$this->params['edgeMargin'] = (int)$edgeMargin;
	}

	public function setHandleOpactiy($handleOpacity) {
		$this->params['handleOpacity'] = (float)$handleOpacity;
	}

	public function setHandleOffset($handleOffset) {
		$this->params['handleOffset'] = (int)$handleOffset;
	}

	public function setHandlePad($handlePad) {
		$this->params['handlePad'] = (int)$handlePad;
	}

	public function setHandleSize($handleSize) {
		$this->params['handleSize'] = $handleSize;
	}

	public function setKeySupport($keySupport) {
		$this->params['keySupport'] = (bool)$keySupport;
	}

	public function setMinSelect(array $minSelect) {
		$this->params['minSelect'] = $minSelect;
	}

	public function setMinSize(array $minSize) {
		$this->params['minSize'] = $minSize;
	}

	public function setMaxSize(array $maxSize) {
		$this->params['maxSize'] = $maxSize;
	}

	public function setOuterImage($outerImage) {
		$this->params['outerImage'] = $outerImage;
	}

	public function setSelection(array $selection) {
		$this->params['setSelect'] = $selection;
	}

	public function setSideHandles($sideHandles) {
		$this->params['sideHandles'] = (bool)$sideHandles;
	}

	public function setSrc($src) {
		$this->attrs['src'] = $src;
	}

	public function setSwingSpeed($swingSpeed) {
		$this->params['swingSpeed'] = (int)$swingSpeed;
	}

	public function setTrueSize(array $trueSize) {
		$this->params['trueSize'] = $trueSize;
	}

	public function preInit() {
		parent::preInit();
		$this->view->head()->addLibrary('jquery-jcrop');
	}

	public function run() {
		$this->view->jQuery()->addCallById($this->getId(),
			'Jcrop', array((!empty($this->params) ? $this->params : Js::emptyObject()))
		);
		echo $this->view->html()->tag('img', $this->attrs);
	}
}