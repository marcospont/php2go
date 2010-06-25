<?php

class DbStatementColumn
{
	public $name;
	public $type;
	public $length;

	public function __construct(array $attrs) {
		foreach ($attrs as $k=>$v)
			$this->{$k} = $v;
	}
}