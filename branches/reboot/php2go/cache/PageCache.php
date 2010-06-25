<?php

class PageCache extends CacheProxy
{
	const ID_PREFIX = 'page-';

	protected $debugHeader = true;
	protected $memorizeHeaders = array('Content-Type');
	protected $patterns = array();
	private $defaultOptions = array(
		'session' => false,
		'user' => false,
		'params' => array(),
		'lifetime' => false
	);
	private $matchOptions = array();
	private $handled = false;

	public function loadOptions(array $options) {
		$this->setDebugHeader(Util::consumeArray($options, 'debugHeader', true));
		$this->setMemorizeHeaders(Util::consumeArray($options, 'memorizeHeaders', array()));
		$this->addPatterns((array)Util::consumeArray($options, 'patterns', array()));
		parent::loadOptions($options);
	}

	public function getDebugHeader() {
		return $this->debugHeader;
	}

	public function setDebugHeader($debugHeader=null) {
		$this->debugHeader = !!$debugHeader;
	}

	public function getMemorizeHeaders() {
		return $this->memorizeHeaders;
	}

	public function setMemorizeHeaders($memorizeHeaders) {
		if (!empty($memorizeHeaders)) {
			if (is_string($memorizeHeaders))
				$memorizeHeaders = explode(',', $memorizeHeaders);
			elseif (!is_array($memorizeHeaders))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The option "%s" must be an array or a list of comma separated values.', array('memorizeHeaders')));
			$this->memorizeHeaders = $memorizeHeaders;
		}
	}

	public function getPatterns() {
		return $this->patterns;
	}

	public function addPatterns(array $patterns) {
		foreach ($patterns as $key => $value) {
			if (is_int($key) && is_array($value))
				$this->addPattern($value);
			elseif (is_string($key) && is_array($value))
				$this->addPattern($key, $value);
			else
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid pattern specification.'));
		}
	}

	public function addPattern($pattern, array $options=array()) {
		$this->patterns[$pattern] = array_merge($this->defaultOptions, $options);
	}

	public function removePattern($pattern) {
		unset($this->patterns[$pattern]);
	}

	public function start() {
		if ($this->matchPattern()) {
			$id = $this->getId();
			$data = $this->load($id);
			if ($data !== false) {
				$content = $data['content'];
				$headers = $data['headers'];
				if (!headers_sent()) {
					foreach ($headers as $name => $value)
						header("{$name}: {$value}");
				}
				if ($this->debugHeader)
					header('X-Page-Cache: hit');
				echo $content;
				exit;
			}
			ob_start(array($this, 'flush'));
			ob_implicit_flush(false);
			return true;
		}
	}

	public function cancel() {
		$this->handled = true;
	}

	public function flush($content) {
		if (!$this->handled) {
			$headers = array();
			$headersList = headers_list();
			foreach ($headersList as $header) {
				$header = explode(':', $header);
				$name = trim($header[0]);
				if (in_array($name, $this->memorizeHeaders))
					$headers[$name] = trim($header[1]);
			}
			$data = array(
				'content' => $content,
				'headers' => $headers
			);
			try {
				if ($this->save($data, null, $this->matchOptions['lifetime']))
					$this->handled = true;
			} catch (Exception $e) { }
		}
		return $content;
	}

	public function deleteRoute($route) {
		$this->clean(Cache::CLEANING_MODE_PATTERN, str_replace('/', '-', $route));
	}

	protected function onCreateCache() {
		$this->setAutoSerialization(true);
		$this->setIdPrefix(self::ID_PREFIX);
	}

	protected function matchPattern() {
		if (!empty($this->patterns) && ($route = Php2Go::app()->getRoute())) {
			$match = null;
			foreach ($this->patterns as $pattern => $patternOptions) {
				if (preg_match('~' . $pattern . '~', $route))
					$match = $pattern;
			}
			if ($match) {
				$this->matchOptions = $this->patterns[$match];
				return true;
			}
		}
		return false;
	}

	protected function getId() {
		$ext = '';
		$route = str_replace('/', '-', Php2Go::app()->getController()->getRoute());
		if (Session::isStarted()) {
			if ($this->matchOptions['session'])
				$ext .= session_id();
			if ($this->matchOptions['user']) {
				$auth = Php2Go::app()->getAuthenticator();
				if ($auth->valid)
					$ext .= $auth->getUser()->getName();
			}
		}
		if (is_string($this->matchOptions['params']))
			$this->matchOptions['params'] = explode(',', $this->matchOptions['params']);
		if (is_array($this->matchOptions['params'])) {
			foreach ($this->matchOptions['params'] as $param) {
				if (isset($_REQUEST[$param]))
					$ext .= $param . '=' . $_REQUEST[$param];
			}
		}
		return $route . (!empty($ext) ? '-' . md5($ext) : '');
	}
}