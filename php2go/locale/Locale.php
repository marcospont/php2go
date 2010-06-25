<?php

class Locale extends Component
{
	private static $dataPath;
	protected $locale;
	protected $data;
	
	private static function getDataPath() {
		if (self::$dataPath === null)
			self::$dataPath = dirname(__FILE__) . DS . 'data';
		return self::$dataPath;
	}
	
	public static function findLocale($locale, $strict=false) {
		if ($locale instanceof Locale)
			return $locale;
		elseif (is_string($locale) && Locale::isLocale($locale))
			return new Locale($locale);
		throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid locale.'));
	}
	
	public static function isLocale($locale, $strict=false) {
		if ($locale instanceof Locale)
			return true;
		if ($locale === null && !is_string($locale))
			return false;
		$locale = str_replace('-', '_', $locale);
		if (is_file(self::getDataPath() . DS . $locale . '.php')) {
			return true;
		} elseif (!$strict) {
			$locale = explode('_', $locale);
			return (is_file(self::getDataPath() . DS . $locale . '.php'));
		}
		return false;
	}
	
	public function __construct($locale) {
		$this->setLocale($locale);
	}
	
	public function setLocale($locale) {
		$locale = str_replace('-', '_', $locale);
		if ($locale != $this->locale) {
			while (true) {
				if (self::isLocale($locale, true)) {
					$this->locale = $locale;
					break;
				} elseif (($pos = strpos($locale, '_')) !== false) {
					$locale = substr($locale, 0, $pos);
				} else {
					$this->locale = 'root';
					break;
				}
			}
			$this->data = null;
		}
	}
	
	public function getLanguage() {
		$locale = explode('_', $this->locale);
		return $locale[0];
	}
	
	public function getTerritory() {
		$data = $this->getData();
		if (!empty($data['territory']))
			return $data['territory'];
		return null;
	}
	
	public function getOrientation() {
		$data = $this->getData();
		return $data['orientation'];
	}
	
	public function getMonths($width='wide') {
		$data = $this->getData();		
		if (isset($data['dates']['months'][$width]))
			return array_map('ucfirst', $data['dates']['months'][$width]);
		return array();
	}
	
	public function getMonth($value, $width='wide') {
		$data = $this->getData();		
		if (isset($data['dates']['months'][$width])) {
			if (isset($data['dates']['months'][$width][$value]))
				return ucfirst($data['dates']['months'][$width][$value]);
		}
		return null;
	}
	
	public function getWeekDays($width='wide') {
		$data = $this->getData();		
		if (isset($data['dates']['weekDays'][$width]))
			return array_map('ucfirst', $data['dates']['weekDays'][$width]);
		return array();
	}
	
	public function getWeekDay($value, $width='wide') {
		$data = $this->getData();		
		if (isset($data['dates']['weekDays'][$width])) {
			if (isset($data['dates']['weekDays'][$width][$value]))
				return ucfirst($data['dates']['weekDays'][$width][$value]);
		}
		return null;
	}
	
	public function getQuarters($width='wide') {
		$data = $this->getData();		
		if (isset($data['dates']['quarters'][$width]))
			return array_map('ucfirst', $data['dates']['quarters'][$width]);
		return array();
	}
	
	public function getQuarter($value, $width='wide') {
		$data = $this->getData();		
		if (isset($data['dates']['quarters'][$width])) {
			if (isset($data['dates']['quarters'][$width][$value]))
				return ucfirst($data['dates']['quarters'][$width][$value]);
		}
		return null;
	}
	
	public function getDayPeriods($width='wide') {
		$data = $this->getData();		
		if (isset($data['dates']['dayPeriods'][$width]))
			return $data['dates']['dayPeriods'][$width];
		return array();
	}
	
	public function getDayPeriod($value, $width='wide') {
		$data = $this->getData();		
		if (isset($data['dates']['dayPeriods'][$width])) {
			if (isset($data['dates']['dayPeriods'][$width][$value]))
				return $data['dates']['dayPeriods'][$width][$value];
		}
		return null;
	}
	
	public function getAM($width='wide') {
		return $this->getDayPeriod('am', $width);
	}
	
	public function getPM($width='wide') {
		return $this->getDayPeriod('pm', $width);
	}
	
	public function getEras($width='wide') {
		$data = $this->getData();		
		if (isset($data['dates']['eras'][$width]))
			return $data['dates']['eras'][$width];
		return array();
	}
	
	public function getAC($width='wide') {
		$eras = $this->getEras($width);
		return $eras[0];
	}
	
	public function getDC($width='wide') {
		$eras = $this->getEras($width);
		return $eras[1];
	}
	
	public function getDateFormat($width='full') {
		$data = $this->getData();
		if (isset($data['dates']['dateFormats'][$width]))
			return $data['dates']['dateFormats'][$width];
		return null;
	}
	
	public function getDateInputFormat() {
		$data = $this->getData();
		$formats = $data['dates']['dateFormats'];
		$format = (preg_match('/\\\'[^\\\']+\\\'/', $formats['medium']) ? $formats['short'] : $formats['medium']);
		preg_match('/[dmy]+([\.\-\/])/i', $formats['short'], $shortSep);
		return preg_replace(
			array('/\.\s+/', '/\s/', '/[^dmy\.\-\/]+/i', '/\bd\b/i', '/\b(m|m{3,4})\b/i', '/\by{1,2}\b/i'),
			array('.', $shortSep ? $shortSep[1] : '/', '', 'dd', 'MM', 'yyyy'),
			utf8_decode($format)
		);
	}
	
	public function getTimeFormat($width='full') {
		$data = $this->getData();
		if (isset($data['dates']['timeFormats'][$width]))
			return $data['dates']['timeFormats'][$width];
		return null;
	}
	
	public function getTimeInputFormat() {
		return $this->getTimeFormat('medium');
	}
	
	public function getDateTimeFormat($width='full') {
		$data = $this->getData();
		if (($dateFormat = $data['dates']['dateFormats'][$width]) === null)
			return null;
		if (($timeFormat = $data['dates']['timeFormats'][$width]) === null)
			return null;
		return strtr($data['dates']['dateTimeFormats'][$width], array(
			'{1}' => $dateFormat,
			'{0}' => $timeFormat
		));
	}
	
	public function getDateTimeInputFormat() {
		$data = $this->getData();
		return strtr($data['dates']['dateTimeFormats']['full'], array(
			'{1}' => $this->getDateInputFormat(),
			'{0}' => $this->getTimeInputFormat()
		));
	}
	
	public function getDateField($field, $relative=null) {
		$data = $this->getData();
		if (isset($data['dates']['fields'][$field])) {
			if ($relative !== null && isset($data['dates']['fields'][$field]['relative'][$relative]))
				return $data['dates']['fields'][$field]['relative'][$relative];
			return $data['dates']['fields'][$field]['name'];
		}
		return null;
	}
	
	public function getNumberSymbols() {
		$data = $this->getData();
		return $data['numbers']['symbols'];
	}
	
	public function getDecimalFormat() {
		$data = $this->getData();
		return $data['numbers']['decimalFormat'];
	}
	
	public function getScientificFormat() {
		$data = $this->getData();
		return $data['numbers']['scientificFormat'];
	}
	
	public function getPercentFormat() {
		$data = $this->getData();
		return $data['numbers']['percentFormat'];
	}
	
	public function getCurrencyFormat() {
		$data = $this->getData();
		return $data['numbers']['currencyFormat'];
	}
	
	public function getCurrencySymbol($currency) {
		$data = $this->getData();
		if (isset($data['currencies'][$currency]))
			return $data['currencies'][$currency];
		return null;
	}
	
	public function getMessage($message) {
		$data = $this->getData();
		if (isset($data['messages'][$message]))
			return $data['messages'][$message];
		return null;
	}
	
	protected function getData() {
		if ($this->data === null)
			$this->data = include(self::getDataPath() . DS . $this->locale . '.php');
		return $this->data;
	}	
	
	public function __toString() {
		return $this->locale;
	}
}