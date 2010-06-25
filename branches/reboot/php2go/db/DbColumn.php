<?php

class DbColumn
{
	public $name;
	public $position;
	public $type;
	public $default;
	public $nullable;
	public $binary;
	public $unsigned;
	public $length;
	public $scale;
	public $enums;
	public $primary;
	public $primaryPosition;
	public $identity;
	
	public function __construct(array $attrs) {
		foreach ($attrs as $k=>$v)
			$this->{$k} = $v;
	}
}