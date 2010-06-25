<?php

class ErrorEvent extends Event
{
	public $code;
	public $message;
	public $file;
	public $line;
	
	public function __construct($sender, $code, $message, $file, $line) {
		parent::__construct($sender);
		$this->code = $code;
		$this->message = $message;
		$this->file = $file;
		$this->line = $line;
	}
}