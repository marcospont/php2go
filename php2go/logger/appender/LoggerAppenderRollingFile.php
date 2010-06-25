<?php

class LoggerAppenderRollingFile extends LoggerAppenderFile
{
	protected $maxFileSize = 10485760;
	protected $maxFiles = 5;
	
	public function getMaxFileSize() {
		return $this->maxFileSize;
	}
	
	public function setMaxFileSize($maxFileSize) {
		$this->maxFileSize = Util::fromByteString($maxFileSize, null);
		if ($this->maxFileSize === null)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid file size.'));
	}
	
	public function getMaxFiles() {
		return $this->maxFiles;
	}
	
	public function setMaxFiles($maxFiles) {
		$this->maxFiles = $maxFiles;
	}
	
	public function write(LoggerEvent $event) {
		parent::write($event);
		if ($this->fp && ftell($this->fp) > $this->maxFileSize)
			$this->rollOver();
	}
	
	protected function rollOver() {
		if ($this->maxFiles > 0) {
			$fileName = $this->filePath . '.' . $this->maxFiles;
			if (is_writeable($fileName))
				unlink($fileName);
			for ($i=$this->maxFiles-1; $i>=1; $i--) {
				$fileName = $this->filePath . '.' . $i;
				if (is_readable($fileName)) {
					$target = $this->filePath . '.' . ($i + 1);
					rename($fileName, $target);
				}
			}
			$this->close();
			rename($this->filePath, $this->filePath . '.1');				
		} else {
			ftruncate($this->fp, 0);
		}
	}
}