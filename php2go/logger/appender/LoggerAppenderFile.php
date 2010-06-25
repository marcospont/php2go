<?php

class LoggerAppenderFile extends LoggerAppender
{
	protected $append = true;
	protected $filePath;
	protected $fp;

	public function getAppend() {
		return $this->append;
	}

	public function setAppend($append) {
		$this->append = (bool)$append;
	}

	public function getFilePath() {
		return $this->filePath;
	}

	public function setFilePath($path) {
		$this->filePath = $path;
	}

	public function write(LoggerEvent $event) {
		$this->open();
		if ($this->fp) {
			if (@flock($this->fp, LOCK_EX)) {
				fwrite($this->fp, $this->formatter->format($event) . PHP_EOL);
				flock($this->fp, LOCK_UN);
			}
		}
	}

	public function close() {
		if ($this->fp) {
			fclose($this->fp);
			$this->fp = null;
		}
	}

	protected function open() {
		if ($this->fp === null) {
			$filePath = $this->filePath;
			if (!empty($filePath)) {
				if (!is_file($filePath)) {
					$dirPath = dirname($filePath);
					if (!is_dir($dirPath))
						@mkdir($dirPath, 0666);
				}
				$this->fp = @fopen($filePath, ($this->append ? 'a' : 'w'));
			}
		}
	}
}