<?php

class TranslatorAdapterArray extends TranslatorAdapterFile
{
	protected $fileSuffix = '.php';
	
	protected function loadFile($path) {
		return include($path);
	}
}