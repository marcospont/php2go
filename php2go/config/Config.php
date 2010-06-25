<?php

class Config extends HashMap
{
	public static function fromFile($path, $readOnly=true) {
		if (($real = realpath($path)) === false && !is_readable($real))
			return array();
		$data = include($real);
		if (!is_array($data))
			return array();
		return new Config($data, $readOnly);
	}
}