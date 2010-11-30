<?php

class ViewHelperHead extends ViewHelper
{
	protected $title = array();
	protected $titleSeparator = ' - ';
	protected $meta = array();
	protected $libraries = array();
	protected $scriptFiles = array();
	protected $styleSheets = array();
	protected $links = array();
	protected $script = array();
	protected $style = array();
	protected $hasScript = false;
	protected $hasStyle = false;
	private $xhtml;

	public function __construct(View $view) {
		parent::__construct($view);
		$this->script = array(array(
			'source' => array()
		));
		$this->style = array(array(
			'source' => array()
		));
	}

	public function __call($name, $args) {
		if (preg_match('/^(meta|httpequiv|scriptfile|script|stylesheet|style|alternatelink|link)$/i', $name)) {
			$method = 'add' . $name;
			return call_user_func_array(array($this, $method), $args);
		}
		return parent::__call($name, $args);
	}

	public function setTitle($title) {
		$this->title = array($title);
		return $this;
	}

	public function appendTitle($title) {
		$this->title[] = $title;
		return $this;
	}

	public function setTitleSeparator($separator) {
		$this->titleSeparator = $separator;
		return $this;
	}

	public function setMeta($meta) {
		if (is_array($meta)) {
			foreach ($meta as $item) {
				if (count($item) >= 2)
					$this->addMeta($item[0], $item[1], (isset($item[2]) ? $item[2] : 'name'), (is_array(@$item[3]) ? $item[3] : array()));
				else
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid meta specification.'));
			}
			return $this;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid meta specification.'));
		}
	}

	public function addMeta($name, $value, $type='name', array $attrs=array()) {
		if (!in_array($type, array('name', 'http-equiv')))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid meta type: "%s".', array($type)));
		$attrs['type'] = $type;
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		$this->meta[] = $attrs;
		return $this;
	}

	public function appendMeta($name, $value, $type='name') {
		foreach ($this->meta as &$meta) {
			if ($meta['type'] == $type && $meta['name'] == $name) {
				$meta['value'] .= $value;
				break;
			}
		}
		return $this;
	}

	public function setHttpEquiv($httpEquiv) {
		if (is_array($httpEquiv)) {
			foreach ($httpEquiv as $item) {
				if (count($item) >= 2)
					$this->addHttpEquiv($item[0], $item[1], (is_array(@$item[2]) ? $item[2] : array()));
				else
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid http-equiv specification.'));
			}
			return $this;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid http-equiv specification.'));
		}
	}

	public function addHttpEquiv($name, $value, array $attrs=array()) {
		$this->addMeta($name, $value, 'http-equiv', $attrs);
		return $this;
	}

	public function setLibraries($libs) {
		if (is_array($libs)) {
			foreach ($libs as $lib)
				$this->addLibrary($lib);
			return $this;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid libraries specification.'));
		}
	}

	public function hasLibrary($lib) {
		return (isset($this->libraries[$lib]));
	}

	public function addLibrary($lib) {
		if (!isset($this->libraries[$lib])) {
			$library = $this->view->app->getLibrary($lib);
			$dependencies = Util::consumeArray($library, 'dependencies', array());
			foreach ($dependencies as $dependency)
				$this->addLibrary($dependency);
			$this->libraries[$lib] = $library;
		}
		return $this;
	}

	public function setScriptFiles($files) {
		if (is_array($files)) {
			foreach ($files as $file)
				$this->addScriptFile($file[0], (is_array(@$file[1]) ? $file[1] : array()), (isset($file[2]) ? $file[2] : 1));
			return $this;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid script files specification.'));
		}
	}

	public function addScriptFile($url, array $attrs=array(), $priority=1) {
		if (!isset($this->scriptFiles[$url])) {
			$attrs['src'] = $this->view->url($url);
			$this->scriptFiles[$url] = array(
				'attrs' => $attrs,
				'priority' => max(array($priority, 0))
			);
		}
		return $this;
	}

	public function addScript($content, array $attrs=array()) {
		if (empty($attrs) || (count($attrs) == 1 && @$attrs['type'] == 'text/javascript')) {
			$this->script[0]['source'][] = $content;
		} else {
			$attrs['source'] = $content;
			$this->script[] = $attrs;
		}
		$this->hasScript = true;
		return $this;
	}

	public function setStylesheets($styleSheets) {
		if (is_array($styleSheets)) {
			foreach ($styleSheets as $styleSheet)
				$this->addStylesheet($styleSheet[0], (is_array(@$styleSheet[1]) ? $styleSheet[1] : array()), (isset($styleSheet[2]) ? $styleSheet[1] : 1));
			return $this;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid stylesheets specification.'));
		}
	}

	public function addStylesheet($url, array $attrs=array(), $priority=1) {
		if (!isset($this->styleSheets[$url])) {
			$attrs['rel'] = 'stylesheet';
			$attrs['type'] = 'text/css';
			$attrs['href'] = $this->view->url($url);
			if (!isset($attrs['media']))
				$attrs['media'] = 'screen';
			elseif (is_array($attrs['media']))
				$attrs['media'] = implode(',', $attrs['media']);
			$this->styleSheets[$url] = array(
				'attrs' => $attrs,
				'priority' => max(array($priority, 0))
			);
		}
		return $this;
	}

	public function addStyle($content, array $attrs=array()) {
		if (empty($attrs) || (count($attrs) == 1 && @$attrs['media'] == 'screen')) {
			$this->style[0]['source'][] = $content;
		} else {
			if (!isset($attrs['media']))
				$attrs['media'] = 'screen';
			elseif (is_array($attrs['media']))
				$attrs['media'] = implode(',', $attrs['media']);
			$attrs['source'] = $content;
			$this->style[] = $attrs;
		}
		$this->hasStyle = true;
		return $this;
	}

	public function setAlternateLinks($links) {
		if (is_array($links)) {
			foreach ($links as $link) {
				if (count($link) >= 3)
					$this->addAlternateLink($link[0], $link[1], $link[2], (is_array(@$link[3]) ? $link[3] : array()));
				else
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid alternate link specification.'));
			}
			return $this;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid alternate links specification.'));
		}
	}

	public function addAlternateLink($url, $type, $title, array $attrs=array()) {
		$attrs['rel'] = 'alternate';
		$attrs['href'] = $this->view->url($url);
		$attrs['type'] = $type;
		$attrs['title'] = $title;
		$this->links[] = $attrs;
		return $this;
	}

	public function setLinks($links) {
		if (is_array($links)) {
			foreach ($links as $link) {
				if (is_array($link))
					$this->addLink($link);
				else
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid link specification.'));
			}
			return $this;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid links specification.'));
		}
	}

	public function addLink(array $attrs=array()) {
		if (!empty($attrs)) {
			if (isset($attrs['href']))
				$attrs['href'] = $this->view->url($attrs['href']);
			$this->links[] = $attrs;
		}
		return $this;
	}

	public function toString() {
		$this->xhtml = $this->view->doctype()->isXhtml();
		$this->registerLibraries();
		return
			$this->renderTitle() .
			$this->renderMeta() .
			$this->renderLinks() .
			$this->renderStylesheets() .
			$this->renderScriptFiles() .
			$this->renderScripts() .
			$this->renderStyles();
	}

	private function registerLibraries() {
		if (!empty($this->libraries)) {
			$assetManager = $this->view->app->getAssetManager();
			foreach ($this->libraries as $library) {
				if (isset($library['assetPath'])) {
					$publishAll = (isset($library['publishAll']) && $library['publishAll']);
					if ($publishAll)
						$assetUrl = $assetManager->publish($library['assetPath']);
					foreach ($library['files'] as $file) {
						if ($publishAll)
							$url = $assetUrl . '/' . $file;
						else
							$url = $assetManager->publish($library['assetPath'] . '/' . $file);
						if (preg_match('/\.js(\.php)?$/', basename($url)))
							$this->addScriptFile($url, array(), 0);
						elseif (preg_match('/\.css(\.php)?$/', basename($url)))
							$this->addStylesheet($url, array(), 0);
					}
				} else {
					foreach ($library['files'] as $file) {
						if (preg_match('/\.js(\.php)?$/', basename($file)))
							$this->addScriptFile($file, array(), 0);
						elseif (preg_match('/\.css(\.php)?$/', basename($file)))
							$this->addStylesheet($file, array(), 0);
					}
				}
			}
		}
	}

	private function renderTitle() {
		if (!empty($this->title))
			return '<title>' . $this->escape(implode($this->titleSeparator, $this->title)) . '</title>' . PHP_EOL;
		return '';
	}

	private function renderMeta() {
		if (!empty($this->meta)) {
			$html = '';
			$modifiers = array('lang', 'scheme');
			foreach ($this->arrangeMeta() as $type => $items) {
				foreach ($items as $meta) {
					$html .= sprintf('<meta %s="%s" content="%s"', $type, $this->escape($meta['name']), $this->escape($meta['value']));
					foreach ($modifiers as $modifier) {
						if (isset($meta[$modifier]))
							$html .= sprintf(' %s="%s"', $modifier, $meta[$modifier]);
					}
					$html .= ($this->xhtml ? ' />' : '>') . PHP_EOL;
				}
			}
			return $html;
		}
		return '';
	}

	private function renderScriptFiles() {
		if (!empty($this->scriptFiles)) {
			$return = array();
			$files = $this->arrangeByPriority($this->scriptFiles);
			foreach ($files as $priority => $items) {
				foreach ($items as $attrs)
					$return[] = $this->renderScript($attrs);
			}
			return implode(PHP_EOL, $return) . PHP_EOL;
		}
		return '';
	}

	private function renderStylesheets() {
		if (!empty($this->styleSheets)) {
			$return = array();
			$files = $this->arrangeByPriority($this->styleSheets);
			foreach ($files as $priority => $items) {
				foreach ($items as $attrs)
					$return[] = $this->renderLink($attrs);
			}
			return implode(PHP_EOL, $return) . PHP_EOL;
		}
		return '';
	}

	private function renderLinks() {
		if (!empty($this->links)) {
			$return = array();
			foreach ($this->links as $attrs)
				$return[] = $this->renderLink($attrs);
			return implode(PHP_EOL, $return) . PHP_EOL;
		}
		return '';
	}

	private function renderScripts() {
		if ($this->hasScript) {
			$escapeStart = ($this->xhtml ? '//<![CDATA[' : '//<!--');
			$escapeEnd = ($this->xhtml ? '//]]>' : '//-->');
			$return = array();
			foreach ($this->script as $attrs) {
				if (!empty($attrs['source']))
					$return[] = $this->renderScript($attrs, $escapeStart, $escapeEnd);
			}
			return implode(PHP_EOL, $return) . PHP_EOL;
		}
		return '';
	}

	private function renderStyles() {
		if ($this->hasStyle) {
			$escapeStart = '<!--';
			$escapeEnd = '-->';
			$return = array();
			foreach ($this->style as $attrs) {
				if (!empty($attrs['source']))
					$return[] = $this->renderStyle($attrs, $escapeStart, $escapeEnd);
			}
			return implode(PHP_EOL, $return) . PHP_EOL;
		}
		return '';
	}

	private function renderScript(array $attrs, $escapeStart=null, $escapeEnd=null) {
		$type = Util::consumeArray($attrs, 'type', 'text/javascript');
		$source = Util::consumeArray($attrs, 'source');
		$conditional = Util::consumeArray($attrs, 'conditional');
		$html = '<script type="' . $this->escape($type) . '"';
		foreach ($attrs as $name => $value) {
			if ($name == 'defer')
				$value = 'defer';
			$html .= sprintf(' %s="%s"', $name, $this->escape($value));
		}
		$html .= '>';
		if ($source) {
			(is_array($source)) && ($source = implode(PHP_EOL, $source));
			$html .= $escapeStart . PHP_EOL . $source . PHP_EOL . $escapeEnd;
		}
		$html .= '</script>';
		if (is_string($conditional) && !empty($conditional))
			return '<!--[if ' . trim($conditional) . ']>' . $html . '<![endif]-->';
		return $html;
	}

	private function renderStyle(array $attrs, $escapeStart, $escapeEnd) {
		$type = Util::consumeArray($attrs, 'type', 'text/css');
		$source = Util::consumeArray($attrs, 'source');
		$conditional = Util::consumeArray($attrs, 'conditional');
		$html = '<style type="' . $this->escape($type) . '"';
		foreach ($attrs as $name => $value)
			$html .= sprintf(' %s="%s"', $name, $this->escape($value));
		(is_array($source)) && ($source = implode(PHP_EOL, $source));
		$html .= '>';
		$html .= $escapeStart . PHP_EOL . $source . PHP_EOL . $escapeEnd;
		$html .= '</style>';
		if (is_string($conditional) && !empty($conditional))
			return '<!--[if ' . trim($conditional) . ']>' . $html . '<![endif]-->';
		return $html;
	}

	private function renderLink(array $attrs) {
		$conditional = Util::consumeArray($attrs, 'conditional');
		$html = '<link';
		foreach ($attrs as $name => $value)
			$html .= sprintf(' %s="%s"', $name, $this->escape($value));
		$html .= ($this->xhtml ? ' />' : '>');
		if (is_string($conditional) && !empty($conditional))
			return '<!--[if ' . trim($conditional) . ']>' . $html . '<![endif]-->';
		return $html;
	}

	private function escape($value) {
		return htmlspecialchars($value, ENT_COMPAT, $this->view->app->getCharset());
	}

	private function arrangeMeta() {
		$result = array();
		foreach ($this->meta as $meta) {
			$type = Util::consumeArray($meta, 'type');
			if (!isset($result[$type]))
				$result[$type] = array();
			$result[$type][] = $meta;
		}
		krsort($result);
		return $result;
	}

	private function arrangeByPriority($data) {
		$result = array();
		foreach ($data as $value) {
			if (!isset($result[$value['priority']]))
				$result[$value['priority']] = array();
			$result[$value['priority']][] = $value['attrs'];
		}
		ksort($result);
		return $result;
	}
}