<?php

abstract class TranslatorAdapter
{
	private static $adapters = array(
		Translator::ADAPTER_ARRAY,
		Translator::ADAPTER_INI
	);
	protected $translations;

	public static function factory($options) {
		if (is_string($options)) {
			$adapter = $options;
			$options = array();
		} elseif (is_array($options)) {
			$adapter = Util::consumeArray($options, 'type', Translator::ADAPTER_ARRAY);
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid translator adapter configuration.'));
		}
		$config = array('options' => $options);
		if (in_array($type, self::$adapters)) {
			$config['class'] = 'TranslatorAdapter' . ucfirst($type);
		} else {
			$config['class'] = $type;
			$config['parent'] = 'TranslatorAdapter';
		}
		return Php2Go::newInstance($config);
	}

	public function translate($domain, $key, $locale) {
		$keys = $this->loadTranslations($locale, $domain);
		if (isset($keys[$key]) && !empty($keys[$key]))
			return $keys[$key];
		Php2Go::app()->raiseEvent('onMissingTranslation', new MissingTranslationEvent($this, $locale, $domain, $key));
		return $key;
	}

	abstract protected function isAvailable($locale, $domain=null);

	abstract protected function loadTranslations($locale, $domain);
}