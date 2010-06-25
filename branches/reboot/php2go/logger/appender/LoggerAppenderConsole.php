<?php

class LoggerAppenderConsole extends LoggerAppender
{
	const STDOUT = 'php://stdout';
	const STDERR = 'php://stderr';

	protected $target = self::STDOUT;
	protected $fp;

	public function getTarget() {
		return $this->target;
	}

	public function setTarget($target) {
		if ($target == self::STDOUT || strtolower($target) == 'stdout')
			$this->target = self::STDOUT;
		elseif ($target == self::STDERR || strtolower($target) == 'stderr')
			$this->target = self::STDERR;
		else
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid target.'));
	}

	public function write(LoggerEvent $event) {
		$this->open();
		if ($this->fp)
			fwrite($this->fp, $this->formatter->format($event) . PHP_EOL);
	}

	public function close() {
		if ($this->fp) {
			fclose($this->fp);
			$this->fp = null;
		}
	}

	protected function open() {
		if ($this->fp === null)
			$this->fp = fopen($this->target, 'w');
	}
}