<?php

final class Flash
{
	const SESSION_VAR = '__flash__';
	
	public static function get($key) {
		$session = Php2Go::app()->getSession();
		$value = $session->get($key);
		if ($value) {
			$session->remove($key);
			return $value;
		}
		return null;
	}
	
	public static function set($key, $value) {
		$session = Php2Go::app()->getSession();
		$session->set($key, $value);
	}
}