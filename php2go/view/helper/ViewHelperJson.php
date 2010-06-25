<?php

class ViewHelperJson extends ViewHelper
{
	public function json($value, array $options=array()) {
		return Json::encode($value, $options);
	}
}