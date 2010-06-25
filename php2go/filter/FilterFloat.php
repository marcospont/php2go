<?php

class FilterFloat extends Filter
{
	public function filter($value) {
		return (float)((string)$value);
	}
}