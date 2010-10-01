<?php

require_once PHP2GO_PATH . '/vendor/recaptcha/recaptchalib.php';

class ReCaptcha extends WidgetInput
{
	private static $themes = array(
		'red', 'white', 'blackglass', 'clean', 'custom'
	);
	private static $locales = array(
		'en', 'nl', 'fr', 'de', 'pt', 'ru', 'es', 'tr'
	);
	protected $publicKey;
	protected $theme = 'red';
	protected $customTranslations = array();
	protected $customThemeWidget = '';
	protected $tabIndex = 0;

	public function setPublicKey($publicKey) {
		if (!empty($publicKey) && is_string($publicKey))
			$this->publicKey = $publicKey;
		else
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid reCAPTCHA public key.'));
	}

	public function setTheme($theme) {
		if (in_array($theme, self::$themes))
			$this->theme = $theme;
		else
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid theme: %s.', array($theme)));
	}

	public function setCustomTranslations(array $translations) {
		$this->customTranslations = $translations;
	}

	public function setCustomThemeWidget($customThemeWidget) {
		if (!empty($customThemeWidget))
			$this->customThemeWidget = $customThemeWidget;
	}

	public function preInit() {
		parent::preInit();
		if (($publicKey = $this->view->app->getOption('reCaptcha.publicKey')) !== null)
			$this->setPublicKey($publicKey);
	}

	public function init() {
		parent::init();
		$options = array(
			'theme' => $this->theme,
			'lang' => $this->getLocale(),
			'custom_theme_widget' => $this->customThemeWidget,
			'custom_translations' => (!empty($this->customTranslations) ? array_walk($this->customTranslations, 'utf8_encode') : Js::emptyObject()),
			'tabindex' => $this->tabIndex
		);
		$this->view->head()->addScript("var RecaptchaOptions = " . Json::encode($options) . ";");
	}

	public function run() {
		echo recaptcha_get_html($this->publicKey);
	}

	protected function getLocale() {
		$locale = substr(Php2Go::app()->getLocale(), 0, 2);
 		if (in_array($locale, self::$locales))
			return $locale;
 		else
 			return 'en';
	}
}