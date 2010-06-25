<?php

class LoggerAppenderMail extends LoggerAppender
{
	protected $subject;
	protected $from;
	protected $to;
	protected $body = '';
	private $sent = false;

	public function getSubject() {
		return $this->subject;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	public function getFrom() {
		return $this->from;
	}

	public function setFrom($from) {
		$this->from = $from;
	}

	public function getTo() {
		return $this->to;
	}

	public function setTo($to) {
		$this->to = $to;
	}

	public function write(LoggerEvent $event) {
		$this->body .= $this->formatter->format($event) . PHP_EOL;
	}

	public function close() {
		if (!$this->sent) {
			if (!empty($this->body) && $this->subject !== null && $this->from !== null && $this->to !== null) {
				@mail(
					$this->to, $this->subject,
					$this->body, "From: {$this->from}"
				);
			}
			$this->sent = true;
		}
	}
}