<?php

class LocaleDateTime extends DateTime
{
	public static function errorHandler($code, $message, $file, $line) {		
		throw new Exception($message);
	}
	
	public function __construct($time) {
		$timezone = DateTimeUtil::getTimezone();
		if (version_compare(PHP_VERSION, '5.3.0') < 0) {
			try {
				set_error_handler(array(__CLASS__, 'errorHandler'));
				parent::__construct($time, new DateTimeZone($timezone));
				restore_error_handler();
			} catch (Exception $e) {
				restore_error_handler();
				throw $e;
			}			
		} else {
			parent::__construct($time, new DateTimeZone($timezone));
		}
	}	
	
	public function __toString() {
		return DateTimeFormatter::formatIso($this->format('U'), Php2Go::app()->getLocale()->getDateTimeInputFormat());
	}
}