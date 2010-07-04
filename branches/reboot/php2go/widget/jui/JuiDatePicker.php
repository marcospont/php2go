<?php

class JuiDatePicker extends JuiInput
{
	private static $localeSet = false;
	private static $locales = array(
		'af', 'ar', 'az', 'bg', 'bs', 'ca', 'cs', 'da', 'de',
		'el', 'en_GB', 'eo', 'es', 'et', 'eu', 'fa', 'fi',
		'fo', 'fr_CH', 'fr', 'he', 'hr', 'hu', 'hy', 'id',
		'is', 'it', 'ja', 'ko', 'lt', 'lv', 'ms', 'nl',
		'no', 'pl', 'pt_BR', 'ro', 'ru', 'sk', 'sl',
		'sq', 'sr_SR', 'sr', 'sv', 'ta', 'th', 'tr',
		'uk', 'vi', 'zh_CN', 'zh_HK', 'zh_TW'
	);

	public function setAltField($altField) {
		$this->params['altField'] = $altField;
	}

	public function setAltFormat($altFormat) {
		$this->params['altFormat'] = $altFormat;
	}

	public function setAppendText($appendText) {
		$this->params['appendText'] = $appendText;
	}

	public function setAutoSize($autoSize) {
		$this->params['autoSize'] = (bool)$autoSize;
	}

	public function setButtonImage($buttonImage) {
		$this->params['buttonImage'] = $buttonImage;
	}

	public function setButtonText($buttonText) {
		$this->params['buttonText'] = $buttonText;
	}

	public function setChangeMonth($changeMonth) {
		$this->params['changeMonth'] = (bool)$changeMonth;
	}

	public function setChangeYear($changeYear) {
		$this->params['changeYear'] = (bool)$changeYear;
	}

	public function setDefaultDate($defaultDate) {
		$this->params['defaultDate'] = $defaultDate;
	}

	public function setGotoCurrent($goToCurrent) {
		$this->params['gotoCurrent'] = (bool)$goToCurrent;
	}

	public function setHideIfNoPrevNext($hideIfNoPrevNext) {
		$this->params['hideIfNoPrevNext'] = (bool)$hideIfNoPrevNext;
	}

	public function setMaxDate($maxDate) {
		$this->params['maxDate'] = $maxDate;
	}

	public function setMinDate($minDate) {
		$this->params['minDate'] = $minDate;
	}

	public function setNumberOfMonths($numberOfMonths) {
		$this->params['numberOfMonths'] = (int)$numberOfMonths;
	}

	public function setSelectOtherMonths($selectOtherMonths) {
		$this->params['selectOtherMonths'] = (bool)$selectOtherMonths;
	}

	public function setShowAnim($showAnim) {
		$this->params['showAnim'] = $showAnim;
	}

	public function setShowAnimDuration($showAnimDuration) {
		$this->params['duration'] = $showAnimDuration;
	}

	public function setShowAnimOptions(array $showAnimOptions) {
		$this->params['showOptions'] = $showAnimOptions;
	}

	public function setShowButtonPanel($showButtonPanel) {
		$this->params['showButtonPanel'] = (bool)$showButtonPanel;
	}

	public function setShowCurrentAtPos($showCurrentAtPos) {
		$this->params['showCurrentAtPos'] = $showCurrentAtPos;
	}

	public function setShowOtherMonths($showOtherMonths) {
		$this->params['showOtherMonths'] = (bool)$showOtherMonths;
	}

	public function setShowWeek($showWeek) {
		$this->params['showWeek'] = (bool)$showWeek;
	}

	public function setStepMonths($stepMonths) {
		$this->params['stepMonths'] = (int)$stepMonths;
	}

	public function setYearRange($yearRange) {
		$this->params['yearRange'] = (is_array($yearRange) ? implode(':', $yearRange) : $yearRange);
	}

	public function preInit() {
		parent::preInit();
		$this->view->head()->addLibrary('jquery-ui-datepicker');
		$this->registerJsEvents(array(
			'beforeShow' => array('input', 'inst'),
			'beforeShowDay' => array('date'),
			'onChangeMonthYear' => array('year', 'month', 'inst'),
			'onClose' => array('dateText', 'inst'),
			'onSelect' => array('dateText', 'inst')
		));
		if (!self::$localeSet && ($locale = $this->getLocale()) !== null) {
			$this->view->jQuery()->addOnLoad('$.datepicker.setDefaults($.datepicker.regional["' . $locale . '"]);');
			self::$localeSet = true;
		}
	}

	public function run() {
		$attrs = array_merge($this->attrs, array('mask' => 'date'));
		if ($this->hasModel())
			echo $this->view->model()->text($this->model, $this->modelAttr, $attrs);
		else
			echo $this->view->form()->text($this->name, $this->value, $attrs);
		$this->view->jQuery()->addCallById($this->getId(),
			'datepicker', array($this->getSetupParams()),
			'parents', array(),
			'find', array('.ui-datepicker-trigger'),
			'css', array('background', 'none'),
			'css', array('border', 'none'),
			'css', array('cursor', 'pointer')
		);
	}

	protected function getDefaultParams() {
		return array(
			'buttonImage' => $this->view->asset(
				$this->view->jQuery()->getUiPath() . DS . 'images' . DS . 'calendar.gif'
			),
			'changeMonth' => true,
			'changeYear' => true,
			'maskPlaceholder' => '_',
			'showAnim' => '',
			'showButtonPanel' => true,
			'showOn' => 'button'
		);
	}

	protected function getMask() {
		$format = Php2Go::app()->getLocale()->getDateInputFormat();
		return preg_replace('/[dmy]/i', '9', $format);
	}

	protected function getLocale() {
		$locale = Php2Go::app()->getLocale();
 		if (in_array($locale, self::$locales))
			return str_replace('_', '-', $locale);
		elseif (in_array(substr($locale, 0, 2), self::$locales))
			return substr($locale, 0, 2);
		return null;
	}
}