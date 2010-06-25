<?php

abstract class TranslatorAdapterFile extends TranslatorAdapter
{
	protected $basePath;
	protected $fileSuffix = '';
	
	public function getBasePath() {
		if ($this->basePath === null)
			$this->basePath = Php2Go::app()->getBasePath() . DS . 'messages';
		return $this->basePath;
	}
	
	public function setBasePath($path) {
		if (($path = realpath($path)) === false || !is_dir($path))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Translator base path must be a valid directory.'));
		$this->basePath = $path;
	}

	protected function isAvailable($locale, $domain=null) {
		$basePath = $this->getBasePath();
		if ($domain)
			return (is_file($basePath . DS . $locale . DS . $domain . $this->fileSuffix));
		return (is_dir($basePath . DS . $locale));
	}
	
	protected function loadTranslations($locale, $domain) {				
		$key = $locale . '.' . $domain;
		if (!isset($this->translations[$key])) {
			if ($this->isAvailable($locale, $domain)) {
				$this->translations[$key] = $this->loadFile($this->getBasePath() . DS . $locale . DS . $domain . $this->fileSuffix);
				return $this->translations[$key];
			}
			return array();
		}
		return $this->translations[$key];
	}

	abstract protected function loadFile($path);
}