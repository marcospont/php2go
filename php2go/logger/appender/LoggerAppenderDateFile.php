<?php

class LoggerAppenderDateFile extends LoggerAppenderFile
{
	protected $datePattern = 'dmY';
	
	public function getDatePattern() {
		return $this->datePattern;
	}
	
	public function setDatePattern($pattern) {
		$this->datePattern = $pattern;
	}

	protected function open() {
		if ($this->fp === null) {
			if ($this->filePath !== null)
				$this->filePath = sprintf($this->filePath, date($this->datePattern));
			parent::open();
		}
	}
}