<?php

class Event
{
	public $sender = null;
	public $time;
	public $handled = false;
	
	public function __construct($sender) {
		$this->sender = $sender;
		$this->time = microtime(true);
	}	
}