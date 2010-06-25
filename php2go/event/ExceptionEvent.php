<?php

class ExceptionEvent extends Event 
{
	public $exception;
	
	public function __construct($sender, Exception $exception) {
		parent::__construct($sender);
		$this->exception = $exception;
	}
}