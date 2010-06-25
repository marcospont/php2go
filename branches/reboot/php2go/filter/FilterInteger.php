<?php

class FilterInteger extends Filter
{
	public function filter($value) {
		return (int)((string)$value);
	}
}