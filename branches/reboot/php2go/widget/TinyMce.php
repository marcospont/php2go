<?php

class TinyMce extends WidgetInput
{
	const DEFAULT_WIDTH = '600px';
	const DEFAULT_HEIGHT = '300px';

	private static $locales = array(
	    'ar', 'az', 'be', 'bg', 'bn', 'br', 'bs', 'ca',
	    'ch', 'cs', 'cy', 'da', 'de', 'dv', 'el', 'en',
	    'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fr', 'fr_LU',
	    'gl', 'gu', 'he', 'hi', 'hr', 'hu', 'hy', 'ia',
	    'id', 'ii', 'is', 'it', 'ja', 'ka', 'kl', 'km',
	    'ko', 'lt', 'lv', 'mk', 'ml', 'mn', 'ms', 'my',
	    'nb', 'nl', 'nn', 'no', 'pl', 'ps', 'pt', 'ro',
	    'ru', 'se', 'si', 'sk', 'sl', 'sq', 'sr', 'sv',
	    'ta', 'te', 'th', 'tn', 'tr', 'tt', 'uk', 'ur',
	    'vi', 'zh', 'zh_CN', 'zh_TW', 'zu'
	);
	private static $localeAliases = array(
		'fr_LU' => 'lb'
	);
	protected $locale;
	protected $compression = true;
	protected $width = self::DEFAULT_WIDTH;
	protected $height = self::DEFAULT_HEIGHT;
	protected $template = 'advanced';
	protected $contentCss;
	protected $plugins = array();
	protected $readOnly = false;
	protected $customParams = array();

	public function setCompression($compression) {
		$this->compression = (bool)$compression;
	}

	public function setWidth($width) {
		if (is_numeric($width))
			$this->width = intval($width) . 'px';
		elseif (preg_match('/[0-9]+(px|%)/', $width))
			$this->width = $width;
		else
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid width: "%s".', array($width)));
	}

	public function setHeight($height) {
		if (is_numeric($height))
			$this->height = intval($height) . 'px';
		elseif (preg_match('/[0-9]+(px|%)/', $height))
			$this->height = $height;
		else
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid height: "%s".', array($height)));
	}

	public function setTemplate($template) {
		if (!in_array($template, array('advanced', 'full')))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Available templates are "advanced" and "full".'));
		$this->template = $template;
	}

	public function setContentCss($css) {
		$this->contentCss = $this->view->url($css);
	}

	public function setPlugins($plugins) {
		if (!is_array($plugins))
			$plugins = explode(',', (string)$plugins);
		$this->plugins = $plugins;
	}

	public function setReadOnly($readOnly) {
		$this->readOnly = (bool)$readOnly;
	}

	public function setCustomParams(array $params) {
		$this->customParams = $params;
	}

	public function init() {
		parent::init();
		if ($this->compression)
			$this->view->head()->addLibrary('tinymce-gz');
		else
			$this->view->head()->addLibrary('tinymce');
		$this->applySettings();
		$this->applyTemplate();
		$this->applyCustomParams();
		if ($this->compression) {
			$this->view->head()->addScript('tinyMCE_GZ.init(' . Js::encode(array(
				'plugins' => (isset($this->params['plugins']) ? $this->params['plugins'] : ''),
				'themes' => $this->params['theme'],
				'languages' => $this->getLocale(),
				'disk_cache' => true,
				'debug' => true
			)) . ');');
		}
		$this->view->jQuery()->addCallById($this->getId(),
			'tinymce', array($this->params)
		);
	}

	public function run() {
		if ($this->hasModel())
			echo $this->view->model()->textarea($this->model, $this->modelAttr, $this->attrs);
		else
			echo $this->view->form()->textarea($this->name, $this->value, $this->attrs);
	}

	public function applySettings() {
		if (!isset($this->attrs['style']))
			$this->attrs['style'] = '';
		$this->attrs['style'] = 'width:' . $this->width . ';height:' . $this->height . ';' . $this->attrs['style'];
		$this->params['mode'] = 'exact';
		$this->params['elements'] = $this->getId();
		$this->params['language'] = $this->getLocale();
		if ($this->contentCss)
			$this->params['content_css'] = $this->contentCss;
		$this->params['readonly'] = $this->readOnly;
		$this->params['relative_urls'] = false;
	}

	public function applyTemplate() {
		if (isset($this->template)) {
			if ($this->template == 'advanced') {
				$this->params['theme'] = 'advanced';
				$this->params['plugins'] = implode(',', array_merge(
					$this->plugins, array(
						'advhr', 'advimage', 'advlink', 'contextmenu', 'directionality', 'emotions', 'fullscreen',
						'inlinepopups', 'insertdatetime', 'layer', 'media', 'nonbreaking', 'noneditable',
						'paste', 'preview', 'print', 'save', 'searchreplace', 'spellchecker', 'style',
						'tabfocus', 'template', 'visualchars'
					)
				));
				$hasContentCss = (isset($this->params['content_css']));
				$this->params['theme_advanced_toolbar_location'] = 'top';
				$this->params['theme_advanced_toolbar_align'] = 'left';
				$this->params['theme_advanced_path_location'] = 'bottom';
				$this->params['theme_advanced_statusbar_location'] = 'bottom';
				$this->params['theme_advanced_buttons1'] = 'save,newdocument,print,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,removeformat,cleanup,|,spellchecker,|,visualaid,visualchars,|,' . ($hasContentCss ? 'ltr,rtl,|,' : '') . 'code,preview,fullscreen';
				$this->params['theme_advanced_buttons2'] = ($hasContentCss ? 'styleselect,' : '') . 'formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,bold,italic,underline,strikethrough,|,sub,sup' . (!$hasContentCss ? ',|,ltr,rtl' : '');
				$this->params['theme_advanced_buttons3'] = 'justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,indent,outdent,|,hr,advhr,nonbreaking,blockquote,|,link,unlink,anchor,|,image,media,emotions,charmap,|,insertdate,inserttime,|,template';
				$this->params['theme_advanced_resizing'] = true;
				$this->params['theme_advanced_resize_horizontal'] = true;
				$this->params['tabfocus_elements'] = ':prev,:next';
			} else {
				$this->params['theme'] = 'advanced';
				$this->params['plugins'] = implode(',', array_merge(
					$this->plugins, array(
						'advhr', 'advimage', 'advlink', 'contextmenu', 'directionality', 'emotions', 'fullscreen',
						'inlinepopups', 'insertdatetime', 'layer', 'media', 'nonbreaking', 'noneditable',
						'paste', 'preview', 'print', 'save', 'searchreplace', 'spellchecker', 'style',
						'tabfocus', 'table', 'template', 'visualchars', 'xhtmlxtras'
					)
				));
				$hasContentCss = (isset($this->params['content_css']));
				$this->params['theme_advanced_toolbar_location'] = 'top';
				$this->params['theme_advanced_toolbar_align'] = 'left';
				$this->params['theme_advanced_path_location'] = 'bottom';
				$this->params['theme_advanced_statusbar_location'] = 'bottom';
				$this->params['theme_advanced_buttons1'] = 'save,newdocument,print,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,removeformat,cleanup,|,spellchecker,|,visualaid,visualchars,|,' . ($hasContentCss ? 'ltr,rtl,|,' : '') . 'code,preview,fullscreen';
				$this->params['theme_advanced_buttons2'] = ($hasContentCss ? 'styleselect,' : '') . 'formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,bold,italic,underline,strikethrough,|,sub,sup' . (!$hasContentCss ? ',|,ltr,rtl' : '');
				$this->params['theme_advanced_buttons3'] = 'justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,indent,outdent,|,hr,advhr,nonbreaking,blockquote,|,link,unlink,anchor,|,image,media,emotions,charmap,|,insertdate,inserttime,|,template';
				$this->params['theme_advanced_buttons4'] = 'cite,abbr,acronym,|,tablecontrols,|,insertlayer,moveforward,movebackward,absolute,|,styleprops,del,ins,attribs';
				$this->params['theme_advanced_resizing'] = true;
				$this->params['theme_advanced_resize_horizontal'] = true;
				$this->params['tabfocus_elements'] = ':prev,:next';
			}
		} else {
			$this->params['theme'] = 'simple';
			$this->params['plugins'] = implode(',', array_merge($this->plugins, array('tabfocus')));
			$this->params['tabfocus_elements'] = ':prev,:next';
		}
	}

	public function applyCustomParams() {
		foreach ($this->customParams as $name => $value)
			$this->params[$name] = $value;
	}

	protected function getLocale() {
		if ($this->locale === null) {
			$locale = Php2Go::app()->getLocale();
			$language = substr($locale, 0, 2);
	 		if (in_array($language, self::$locales)) {
	 			if (isset(self::$localeAliases[$language]))
	 				$this->locale = self::$localeAliases[$language];
	 			else
 					$this->locale = $language;
	 		} elseif (in_array($locale, self::$locales)) {
				if (isset($locale, self::$localeAliases))
					$this->locale = self::$localeAliases[$locale];
				else
					$this->locale = strtolower(str_replace('_', '-', $locale));
	 		} else {
 				$this->locale = 'en';
	 		}
		}
		return $this->locale;
	}
}