<?php

class ViewHelperFacebook extends ViewHelper
{
	const HTML5 = 'html5';
	const XFBXML = 'xfbxml';
	const IFRAME = 'iframe';
	
	private static $likeDefaults = array(
		'send' => false,
		'layout' => 'standard',
		'width' => 450,
		'height' => 90,
		'showFaces' => true,
		'action' => 'like',
		'colorScheme' => 'light',
		'font' => 'arial',
		'style' => ''
	);
	
	private $appId;
	private $implementation = self::IFRAME;
	private $sdk = false;
	
	public function setAppId($appId) {
		$this->appId = $appId;		
	}
	
	public function setImplementation($implementation) {
		if (!in_array($implementation, array(self::HTML5, self::XFBXML, self::IFRAME)))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid implementation: "%s".', array($implementation)));
		$this->implementation = $implementation;
	}
	
	public function like(array $attrs) {
		if (array_key_exists('url', $attrs)) {
			$attrs = array_merge(self::$likeDefaults, $attrs);
			if (!isset($attrs['id']))
				$attrs['id'] = Util::id(get_class($this) . 'Like');
			$content = $this->enableSDK();
			switch ($this->implementation) {			
				case self::HTML5 :
					$content .= sprintf('<div id="%s" style="%s" class="fb-like" data-href="%s" data-send="%s" data-layout="%s" data-width="%s" data-show-faces="%s" data-action="%s" data-colorscheme="%s" data-font="%s"></div>',
						$attrs['id'], $attrs['style'], $attrs['url'], (!!$attrs['send'] ? 'true' : 'false'), $attrs['layout'], $attrs['width'], 
						(!!$attrs['showFaces'] ? 'true' : 'false'), $attrs['action'], $attrs['colorScheme'], $attrs['font']
					);
					break;
				case self::XFBXML :
					$content .= sprintf('<fb:like id="%s" style="%s" href="%s" send="%s" layout="%s" width="%s" show_faces="%s" action="%s" colorscheme="%s" font="%s"></fb:like>',
						$attrs['id'], $attrs['style'], $attrs['url'], (!!$attrs['send'] ? 'true' : 'false'), $attrs['layout'], $attrs['width'], 
						(!!$attrs['showFaces'] ? 'true' : 'false'), $attrs['action'], $attrs['colorScheme'], $attrs['font']
					);
					break;
				case self::IFRAME :
					$content .= sprintf('<iframe id="%s" style="%s" src="//www.facebook.com/plugins/like.php?href=%s&amp;send=%s&amp;layout=%s&amp;width=%s&amp;show_faces=%s&amp;action=%s&amp;colorscheme=%s&amp;font=%s&amp;height=%s&amp;appId=%s" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:%spx; height:%spx;" allowTransparency="true"></iframe>',
						$attrs['id'], $attrs['style'], urlencode($attrs['url']), (!!$attrs['send'] ? 'true' : 'false'), $attrs['layout'], $attrs['width'], 
						(!!$attrs['showFaces'] ? 'true' : 'false'), $attrs['action'], $attrs['colorScheme'], $attrs['font'],
						$attrs['height'], $this->appId, $attrs['width'], $attrs['height']
					);
			}
			return $content;
		}
		return '';
	}
	
	private function enableSDK() {
		if ($this->implementation != self::IFRAME && !$this->sdk) {
			if (!isset($this->appId))
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" property is required for "%s" helper.', array('appId', get_class($this))));
			$buf = array();
			$buf[] = "(function(d, s, id) {";
			$buf[] = "var js, fjs = d.getElementsByTagName(s)[0];";
			$buf[] = "if (d.getElementById(id)) {return;}";
			$buf[] = "js = d.createElement(s); js.id = id;";
			$buf[] = "js.src = \"//connect.facebook.net/" . Php2Go::app()->getLocale() . "/all.js#xfbml=1&appId={$this->appId}\";";
			$buf[] = "fjs.parentNode.insertBefore(js, fjs);";
			$buf[] = "}(document, 'script', 'facebook-jssdk'));";
			$this->view->scriptBuffer()->add(join("\n", $buf));
			return '<div id="fb-root"></div>';
			$this->sdk = true;
		}
		return '';
	}
}
