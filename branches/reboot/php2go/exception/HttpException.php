<?php

class HttpException extends Exception
{
	public $statusCode;
	
	public function __construct($status, $message, $code=0) {
		parent::__construct($message, $code);
		$this->statusCode = $status;
	}
}