<?php

class AuthorizationException extends HttpException
{
	public function __construct($message, $code=0) {
		parent::__construct(403, $message, $code);
	}
}