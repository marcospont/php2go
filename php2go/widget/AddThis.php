<?php

class AddThis extends WidgetElement
{
	const BUTTON = 'button';
	const TOOLBOX = 'toolbox';
	const SCRIPT_URL = 'http://s7.addthis.com/js/250/addthis_widget.js';
	const LINK_URL = 'http://www.addthis.com/bookmark.php?v=250';

	private static $locales = array(
		'en', 'af', 'sq', 'ar', 'az', 'eu', 'be', 'bn', 'bs', 'bg', 'ca', 'zh', 'hr', 'cs', 'da', 'nl', 'et', 'fo', 'fi', 'fr', 'gl',
		'de', 'el', 'he', 'hi', 'hu', 'is', 'id', 'ga', 'it', 'ja', 'ko', 'lv', 'lt', 'lb', 'mk', 'ml', 'mn', 'ms', 'nb', 'no', 'oc',
		'fa', 'pl', 'pt', 'ro', 'ru', 'se', 'sr', 'sk', 'sl', 'sw', 'es', 'su', 'sv', 'tl', 'ta', 'te', 'th', 'tr', 'uk', 'ur', 'vi', 'cy'
	);
	protected $type = self::BUTTON;
	protected $buttonImage;
	protected $buttonText;
	protected $toolboxSeparator = '|';
	protected $toolboxServices = array();
	protected $uiOptions = array();
	protected $shareOptions = array();

	public function setType($type) {
		if (in_array($type, array(self::BUTTON, self::TOOLBOX)))
			$this->type = $type;
		else
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid UI type: %s.', array($type)));
	}

	public function setButtonImage($buttonImage) {
		$this->buttonImage = $buttonImage;
	}

	public function setButtonText($buttonText) {
		$this->buttonText = $buttonText;
	}

	public function setToolboxSeparator($toolboxSeparator) {
		$this->toolboxSeparator = $toolboxSeparator;
	}

	public function setToolboxServices(array $toolboxServices) {
		foreach ($toolboxServices as $id => $options) {
			if (is_numeric($id) && is_string($options)) {
				$this->toolboxServices[] = array(
					'id' => $options
				);
			} elseif (is_string($id) && is_array($options)) {
				$this->toolboxServices[] = array_merge($options, array('id' => $id));
			} else {
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid toolbox services specification.'));
			}
		}
	}

	public function setUiOptions(array $uiOptions) {
		$this->uiOptions = array_merge($this->uiOptions, $uiOptions);
	}

	public function setShareOptions(array $shareOptions) {
		$this->shareOptions = array_merge($this->shareOptions, $shareOptions);
	}

	public function preInit() {
		parent::preInit();
		$this->uiOptions['ui_language'] = $this->getLocale();
	}

	public function init() {
		$this->view->head()->addScriptFile(self::SCRIPT_URL);
	}

	public function run() {
		$uiOptions = (!empty($this->uiOptions) ? Json::encode($this->uiOptions, array('ensureUTF8' => true)) : Js::emptyObject());
		$shareOptions = (!empty($this->shareOptions) ? Json::encode($this->shareOptions, array('ensureUTF8' => true)) : Js::emptyObject());
		if ($this->type == self::BUTTON) {
			echo '<a ' . $this->renderAttrs() . '>';
			if (isset($this->buttonImage))
				echo $this->view->html()->image($this->buttonImage);
			if (isset($this->buttonText))
				echo $this->view->escape($this->buttonText);
			echo '</a>';
			$this->view->jQuery()->addOnLoad('addthis.button("#' . $this->getId() . '", ' . $uiOptions . ', ' . $shareOptions . ');');
		} else {
			echo '<div ' . $this->renderAttrs() . '>';
			foreach ($this->toolboxServices as $service) {
				if ($service['id'] == 'separator') {
					echo '<span class="addthis_separator">' . $this->toolboxSeparator . '</span>';
				} else {
					echo '<a class="addthis_button_' . $service['id'] . '"' .
						(isset($service['title']) ? ' title="' . $service['title'] . '"' : '') .
						(isset($this->shareOptions['url']) ? ' addthis:url="' . $this->shareOptions['url'] . '"' : '') .
						(isset($this->shareOptions['title']) ? ' addthis:title="' . $this->shareOptions['title'] . '"' : '') .
						(isset($this->shareOptions['description']) ? ' addthis:description="' . $this->shareOptions['description'] . '"' : '') .
					'>';
					if (isset($service['image']))
						echo $this->view->html()->image($service['image']);
					if (isset($service['label']))
						echo $this->view->escape($service['label']);
					echo '</a>';
				}
			}
			echo '</div>';
			$this->view->jQuery()->addOnLoad('addthis.toolbox("#' . $this->getId() . '", ' . $uiOptions . ', ' . $shareOptions . ');');
		}
	}

	protected function renderAttrs() {
		$class = ($this->type == self::BUTTON ? 'addthis_button' : 'addthis_toolbox');
		$toolboxClass = ($this->type == self::TOOLBOX ? ' addthis_default_style' : '');
		$this->attrs['class'] = $class . (isset($this->attrs['class']) ? ' ' . implode(' ', (array)$this->attrs['class']) : $toolboxClass);
		return parent::renderAttrs();
	}

	protected function getLocale() {
		$locale = substr(Php2Go::app()->getLocale(), 0, 2);
 		if (in_array($locale, self::$locales))
			return $locale;
 		else
 			return 'en';
	}
}