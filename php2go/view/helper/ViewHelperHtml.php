<?php

class ViewHelperHtml extends ViewHelper
{
	protected $closingBracket;

	public function tag($tag, array $attrs=array(), $content=null) {
		$result = '<' . $tag;
		if (!empty($attrs))
			$result .= $this->renderAttrs($attrs);
		if ($content !== null)
			return $result . '>' . 	$content . '</' . $tag . '>';
		else
			return $result . $this->getClosingBracket();
	}

	public function openTag($tag, array $attrs=array()) {
		$result = '<' . $tag;
		if (!empty($attrs))
			$result .= $this->renderAttrs($attrs);
		$result .= '>';
		return $result;
	}

	public function closeTag($tag) {
		return '</' . $tag . '>';
	}

	public function cdata($content) {
		return '<![CDATA[' . $content . ']]>';
	}

	public function link($url=null, $content, array $attrs=array()) {
		$url = $this->view->url($url);
		if (($post = Util::consumeArray($attrs, 'post')) && strpos($url, 'javascript:') === false) {
			$this->view->head()->addLibrary('php2go');
			if (($confirm = Util::consumeArray($attrs, 'confirm')))
				$handler = 'if (confirm("' . Js::escape($confirm) . '")) { php2go.post(this, "' . $url . '"); }';
			else
				$handler = 'php2go.post(this, "' . $url . '");';
			$attrs['onclick'] = @$attrs['onclick'] . $handler;
			$attrs['href'] = '#';
		} else {
			$attrs['href'] = $url;
		}
		return $this->tag('a', $attrs, $content);
	}

	public function mailto($email, $content=null, array $attrs=array()) {
		if (!empty($email)) {
			if (empty($content))
				$content = $email;
			if (($encode = Util::consumeArray($attrs, 'encode'))) {
				switch ($encode) {
					case 'js' :
						$encoded = '';
						$js = 'document.write(\'' . $this->link('mailto:' . $email, $content, $attrs) . '\');';
						for ($i=0; $i<strlen($js); $i++)
							$encoded .= '%' . bin2hex($js[$i]);
						return '<script type="text/javascript">eval(unescape("' . $encoded . '"));</script>';
					case 'hex' :
						if (preg_match('/^(.*)(\?.*)$/', $email))
							throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Hex encoding does not work with extra parameters.'));
						$encodedContent = '';
						for ($i=0; $i<strlen($content); $i++)
							$encodedContent .= '&#x' . bin2hex($content[$i]);
						$encodedEmail = '&#109;&#97;&#105;&#108;&#116;&#111;&#58;';
						for ($i=0; $i<strlen($email); $i++) {
							if (preg_match('/\w/', $email[$i]))
								$encodedEmail .= '&#x' . bin2hex($email[$i]) . ';';
							else
								$encodedEmail .= $email[$i];
						}
						return '<a href="' . $encodedEmail . '"' . $this->renderAttrs($attrs) . '>' . $encodedContent . '</a>';
					default :
						throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid e-mail encoding type.'));
				}
			} else {
				return $this->link('mailto:' . $email, $content, $attrs);
			}
		}
	}

	public function button($content, array $attrs=array()) {
		return $this->tag('button', $attrs, $content);
	}

	public function buttonTo($url, $content, array $attrs=array()) {
		$url = $this->view->url($url);
		if (strpos($url, 'javascript:') === false) {
			if (($post = Util::consumeArray($attrs, 'post'))) {
				$this->view->head()->addLibrary('php2go');
				if (($confirm = Util::consumeArray($attrs, 'confirm')))
					$handler = 'if (confirm("' . Js::escape($confirm) . '")) { php2go.post(this, "' . $url . '"); }';
				else
					$handler = 'php2go.post(this, "' . $url . '");';
				$attrs['onclick'] = @$attrs['onclick'] . $handler;
			} else {
				$attrs['onclick'] = @$attrs['onclick'] . 'document.location.href="' . Js::escape($this->view->url($url)) . '";';
			}
		} else {
			$attrs['onclick'] = @$attrs['onclick'] . $url;
		}
		$attrs['type'] = 'button';
		return $this->button($content, $attrs);
	}

	public function image($src, array $attrs=array()) {
		$attrs['src'] = $this->view->url($src);
		if (!isset($attrs['alt']))
			$attrs['alt'] = '';
		return $this->tag('img', $attrs);
	}

	public function itemList(array $items, $ordered=false, array $attrs=array(), $escape=true) {
		$result = '';
		foreach ($items as $item) {
			if (is_array($item))
				$result .= '<li>' . $this->itemList($item, $ordered, $attrs, $escape) . '</li>' . PHP_EOL;
			else
				$result .= '<li>' . ($escape ? $this->view->escape($item) : $item) . '</li>' . PHP_EOL;
		}
		$tagName = ($ordered ? 'ol' : 'ul');
		return '<' . $tagName . $this->renderAttrs($attrs) . '>' . PHP_EOL . $result . '</' . $tagName . '>';
	}

	public function definitionList(array $items, array $attrs=array(), $escape=true) {
		$result = '';
		foreach ($items as $term => $definition) {
			$result .= '<dt>' . ($escape ? $this->view->escape($term) : $term) . '</dt>';
			if (is_array($definition))
				$result .= $this->definitionList($definition, $attrs, $escape) . PHP_EOL;
			else
				$result .= '<dd>' . ($escape ? $this->view->escape($definition) : $definition) . '</dd>' . PHP_EOL;
		}
		return '<dl' . $this->renderAttrs($attrs) . '>' . PHP_EOL . $result . '</dl>';
	}

	public function flash($movie, array $attrs=array(), array $params=array(), $content=null) {
		// initialize
		$movie = $this->view->url($movie);
		$closingBracket = $this->getClosingBracket();
		// outer attrs (IE proprietary)
		$outerAttrs = array_merge($attrs, array(
			'classid' => 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'
		));
		$result = '<object' . $this->renderAttrs($outerAttrs) . '>' . PHP_EOL;
		// id needs to be rendered on the outer object
		unset($attrs['id']);
		// outer params (IE proprietary)
		$outerParams = array_merge($params, array(
			'movie' => $movie
		));
		foreach ($outerParams as $name => $options) {
			if (is_string($options))
				$options = array('value' => $options);
			$options = array_merge(array('name' => $name), $options);
			$result .= '<param' . $this->renderAttrs($options) . $closingBracket . PHP_EOL;
		}
		$result .= '<!--[if !IE]>-->' . PHP_EOL;
		// inner object (w3c standards)
		$result .= $this->object($movie, 'application/x-shockwave-flash', $attrs, $params, '<!--<![endif]-->' . PHP_EOL . (!empty($content) ? $content . PHP_EOL : '') . '<!--[if !IE]>-->');
		$result .= '<!--<![endif]-->' . PHP_EOL;
		$result .= '</object>';
		return $result;
	}

	public function quickTime($src, array $attrs=array(), array $params=array(), $content=null) {
		$src = $this->view->url($src);
		$attrs = array_merge(array(
			'classid' => 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B',
			'codebase' => 'http://www.apple.com/qtactivex/qtplugin.cab'
		), $attrs);
		$params = array_merge(array(
			'src' => $src
		), $params);
		return $this->object($src, 'video/quicktime', $attrs, $params, $content);
	}

	public function object($data, $type, array $attrs=array(), array $params=array(), $content=null) {
		$attrs = array_merge(array(
			'data' => $data,
			'type' => $type
		), $attrs);
		$closingBracket = $this->getClosingBracket();
		$paramsResult = '';
		foreach ($params as $name => $options) {
			if (is_string($options))
				$options = array('value' => $options);
			$options = array_merge(array('name' => $name), $options);
			$paramsResult .= '<param' . $this->renderAttrs($options) . $closingBracket . PHP_EOL;
		}
		if (is_array($content))
			$content = implode(PHP_EOL, $content);
		return
			'<object' . $this->renderAttrs($attrs) . '>' . PHP_EOL .
			$paramsResult .
			($content ? $content . PHP_EOL : '') .
			'</object>' . PHP_EOL;
	}

	public function event($id, $eventName, $body) {
		$this->view->jQuery()->addCallById($id,
			'bind', array($eventName, Js::func($body))
		);
	}

	protected function getClosingBracket() {
		if ($this->closingBracket === null)
			$this->closingBracket = ($this->view->doctype()->isXhtml() ? ' />' : '>');
		return $this->closingBracket;
	}
}