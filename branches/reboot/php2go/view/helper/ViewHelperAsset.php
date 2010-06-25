<?php

class ViewHelperAsset extends ViewHelper
{
	public function asset($path) {
		return Php2Go::app()->getAssetManager()->publish($path);
	}
}