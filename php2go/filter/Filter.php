<?php

abstract class Filter implements FilterInterface
{
	public function filterArray(array $array) {
		$result = $array;
		foreach ($result as &$value)
			$value = $this->filter($value);
		return $result;
	}
}