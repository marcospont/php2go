<?php

Php2Go::import('php2go.translator.adapter.*');

class Translator extends Component
{
	const ADAPTER_ARRAY = 'array';
	const ADAPTER_INI = 'ini';

	protected $adapter;
	protected $sourceLanguage;
	protected $defaultDomain = 'main';
	protected $formDomain;
	protected $validatorDomain;

	public function __construct() {
		$this->sourceLanguage = Php2Go::app()->getSourceLanguage();
	}

	public function getAdapter() {
		if ($this->adapter === null)
			$this->setAdapter(new TranslatorAdapterArray());
		return $this->adapter;
	}

	public function setAdapter($adapter) {
		if (!$adapter instanceof TranslatorAdapter)
			$adapter = TranslatorAdapter::factory($adapter);
		$this->adapter = $adapter;
	}

	public function getSourceLanguage() {
		return $this->sourceLanguage;
	}

	public function setSourceLanguage($language) {
		$this->sourceLanguage = $language;
	}

	public function getDefaultDomain() {
		return $this->defaultDomain;
	}

	public function setDefaultDomain($domain) {
		if (!preg_match('/\w+/', $domain))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid translator domain.'));
		$this->defaultDomain = $domain;
	}

	public function getFormDomain() {
		return $this->formDomain;
	}

	public function setFormDomain($domain) {
		if (!preg_match('/\w+/', $domain))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid translator domain.'));
		$this->formDomain = $domain;
	}

	public function getValidatorDomain() {
		return $this->validatorDomain;
	}

	public function setValidatorDomain($domain) {
		if (!preg_match('/\w+/', $domain))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid translator domain.'));
		$this->validatorDomain = $domain;
	}

	public function translate($key, $domain=null, $locale=null) {
		if ($locale === null)
			$locale = Php2Go::app()->getLocale();
		if ($locale != $this->sourceLanguage) {
			if ($domain === null)
				$domain = $this->defaultDomain;
			return $this->getAdapter()->translate($domain, $key, $locale);
		}
		return $key;
	}

	public function translatePath($path, $locale=null) {
		if ($locale === null)
			$locale = Php2Go::app()->getLocale();
		$localizedPath = dirname($path) . DS . $locale . DS . basename($path);
		return (is_file($localizedPath) ? $localizedPath : $path);
	}
}

class TranslatorException extends Exception {
}