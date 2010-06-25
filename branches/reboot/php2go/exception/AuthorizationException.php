<?php

class AuthorizationException extends HttpException
{
	public function __construct($message, $code) {
		parent::__construct(403, $message, $code);
	}
}