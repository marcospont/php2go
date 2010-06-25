<?php

class TranslatorAdapterIni extends TranslatorAdapterFile
{
	protected $fileSuffix = '.ini';
	
	protected function loadFile($path) {
		return parse_ini_file($path, false);
	}
}