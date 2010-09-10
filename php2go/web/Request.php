<?php

class Request extends Component
{
	private $host;
	private $url;
	private $baseUrl;
	private $scriptUrl;
	private $scriptFile;
	private $pathInfo;
	private $requestUri;
	private $headers;
	private $rawBody;
	private $params = array();
	private $cookies;

	public function __construct() {
		$this->normalize();
	}

	public function __get($name) {
		switch (true) {
			case (array_key_exists($name, $this->params)) :
				return $this->params[$name];
			case (array_key_exists($name, $_GET)) :
				return $_GET[$name];
			case (array_key_exists($name, $_POST)) :
				return $_POST[$name];
			case (array_key_exists($name, $_COOKIE)) :
				return new Cookie($name, $_COOKIE[$param]);
			case (array_key_exists($name, $_SERVER)) :
				return $_SERVER[$name];
			case (array_key_exists($name, $_ENV)) :
				return $_ENV[$name];
			default :
				return parent::__get($name);
		}
	}

	public function getParam($param, $fallback=null) {
		return (array_key_exists($param, $this->params) ? $this->params[$param] : $fallback);
	}

	public function setParam($param, $value) {
		$this->params[$name] = $value;
	}

	public function getQuery($param=null, $fallback=null) {
		if ($param === null)
			return $_GET;
		return (array_key_exists($param, $_GET) ? $_GET[$param] : $fallback);
	}

	public function getPost($param=null, $fallback=null) {
		if ($param === null)
			return $_POST;
		return (array_key_exists($param, $_POST) ? $_POST[$param] : $fallback);
	}

	public function getCookie($param=null, $fallback=null) {
		if (!isset($this->cookies))
			$this->cookies = new CookieCollection();
		if ($param === null)
			return $this->cookies;
		return $this->cookies->itemAt($param, $fallback);
	}

	public function getServer($param=null, $fallback=null) {
		if ($param === null)
			return $_SERVER;
		return (array_key_exists($param, $_SERVER) ? $_SERVER[$param] : $fallback);
	}

	public function getEnv($param=null, $fallback=null) {
		if ($param === null)
			return $_ENV;
		return (array_key_exists($param, $_ENV) ? $_ENV[$param] : $fallback);
	}

	public function getHeader($name, $fallback=null) {
		$headers = $this->getHeaders();
		if (isset($headers[$name]))
			return $headers[$name];
		return $fallback;
	}

	public function getHeaders() {
		if (!$this->headers) {
			if (function_exists('apache_request_headers'))
				$this->headers = apache_request_headers();
			$this->headers = array();
			foreach ($_SERVER as $key => $value) {
				if (substr($key, 0, 5) == 'HTTP_') {
					$name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
					$this->headers[$name] = $value;
				}
			}
		}
		return $this->headers;
	}

	public function getQueryString() {
		return (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
	}

	public function getUrl() {
		if (!$this->url) {
			if (isset($_SERVER['REQUEST_URI'])) {
				$this->url = $_SERVER['REQUEST_URI'];
			} else {
				$this->url = $this->getScriptUrl();
				if (($pathInfo = $this->getPathInfo()))
					$this->url .= '/' . $pathInfo;
				if (($queryString = $this->getQueryString()))
					$this->url .= '?' . $queryString;
			}
		}
		return $this->url;
	}

	public function getBaseUrl($absolute=false) {
		if (!$this->baseUrl)
			$this->baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
		return ($absolute ? $this->getHost() . $this->baseUrl : $this->baseUrl);
	}

	public function getScriptUrl() {
		if (!$this->scriptUrl) {
			$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
			if (basename($_SERVER['SCRIPT_NAME']) == $scriptName)
				$this->scriptUrl = $_SERVER['SCRIPT_NAME'];
			elseif (basename($_SERVER['PHP_SELF']) == $scriptName)
				$this->scriptUrl = $_SERVER['PHP_SELF'];
			elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) == $scriptName)
				$this->scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
			elseif (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false)
				$this->scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
			elseif (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)
				$this->scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
			else
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'It wasn\'t enable to determine the script URL.'));
		}
		return $this->scriptUrl;
	}

	public function getScriptFile() {
		if (!$this->scriptFile)
			$this->scriptFile = realpath($_SERVER['SCRIPT_FILENAME']);
		return $this->scriptFile;
	}

	public function getScriptPath() {
		return dirname($this->getScriptFile());
	}

	public function getRequestUri() {
		if(!$this->requestUri) {
			if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
				$this->requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
			} elseif (isset($_SERVER['IIS_WasUrlRewritten']) && $_SERVER['IIS_WasUrlRewritten'] == '1' && isset($_SERVER['UNENCODED_URL']) && $_SERVER['UNENCODED_URL'] != '') {
				$this->requestUri = $_SERVER['UNENCODED_URL'];
			} elseif (isset($_SERVER['REQUEST_URI'])) {
				$this->requestUri = $_SERVER['REQUEST_URI'];
				if (strpos($this->requestUri, $_SERVER['HTTP_HOST']) !== false)
					$this->requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->requestUri);
			} elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
				$this->requestUri = $_SERVER['ORIG_PATH_INFO'];
				if (!empty($_SERVER['QUERY_STRING']))
					$this->requestUri .= '?' . $_SERVER['QUERY_STRING'];
			} else {
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'It wasn\'t possible to determine the request URI.'));
			}
		}
		return $this->requestUri;
	}

	public function getPathInfo() {
		if (!$this->pathInfo) {
			$baseUrl = $this->getBaseUrl();
			$scriptUrl = $this->getScriptUrl();
			$requestUri = urldecode($this->getRequestUri());
			if (strpos($requestUri, $scriptUrl) === 0)
				$pathInfo = substr($requestUri, strlen($scriptUrl));
			elseif ($baseUrl === '' || strpos($requestUri, $baseUrl) === 0)
				$pathInfo = substr($requestUri, strlen($baseUrl));
			elseif(strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0)
				$pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
			else
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'It wasn\'t possible to determine the request path info.'));
			if (($pos=strpos($pathInfo,'?')) !== false)
				$pathInfo = substr($pathInfo, 0, $pos);
			$this->pathInfo = trim($pathInfo, '/');
		}
		return $this->pathInfo;
	}

	public function getMethod() {
		return ($_SERVER['REQUEST_METHOD']);
	}

	public function getRawBody() {
		if ($this->rawBody === null) {
            $body = file_get_contents('php://input');
            if (strlen(trim($body)) > 0)
                $this->rawBody = $body;
            else
                $this->rawBody = false;
		}
		return $this->rawBody;
	}

	public function getContentLength() {
		if (isset($_SERVER['CONTENT_LENGTH']))
			return $_SERVER['CONTENT_LENGTH'];
		elseif (!empty($_POST))
			return strlen(serialize($_POST));
		return 0;
	}

	public function getReferer() {
		return (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
	}

	public function getHost() {
		if (!$this->host) {
			$schema = ($this->isSecure() ? 'https' : 'http');
			if (isset($_SERVER['HTTP_HOST'])) {
				$this->host = $schema . '://' . $_SERVER['HTTP_HOST'];
			} else {
				$this->host = $schema . '://' . $_SERVER['SERVER_NAME'];
				$port = $_SERVER['SERVER_PORT'];
				if (($secure && $port != 443) || (!$secure && $port != 80))
					$this->host .= ':' . $port;
			}
		}
		return $this->host;
	}

	public function getUserHost() {
		return (isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null);
	}

	public function getUserAddress($checkProxy=true) {
        if ($checkProxy && array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else if ($checkProxy && array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
        	return (array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : null);
        }
	}

	public function getBrowser($userAgent=null, $asArray=true) {
		return get_browser($userAgent, $asArray);
	}

	public function isSecure() {
		return (isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on'));
	}

	public function isGet() {
		return (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'GET'));
	}

	public function isPost() {
		return (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST'));
	}

	public function isPut() {
		$isPut = (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT'));
		$isOverride = ($this->isPost() && !strcasecmp($this->getHeader('X-HTTP-Method-Override'), 'PUT'));
		return ($isPut || $isOverride);
    }

	public function isDelete() {
		$isDelete = (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE'));
		$isOverride = ($this->isPost() && !strcasecmp($this->getHeader('X-HTTP-Method-Override'), 'DELETE'));
		return ($isDelete || $isOverride);
    }

	public function isAjax() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
	}

	public function isFlash() {
		$agent = $this->getHeader('User-Agent');
		return ($agent ? !!strstr(strtolower($agent), 'flash') : false);
	}

	private function normalize() {
		if (ini_get('magic_quotes_gpc')) {
			if (isset($_GET))
				$_GET = $this->stripSlashes($_GET);
			if (isset($_POST))
				$_POST = $this->stripSlashes($_POST);
			if (isset($_REQUEST))
				$_REQUEST = $this->stripSlashes($_REQUEST);
			if (isset($_COOKIE))
				$_COOKIE = $this->stripSlashes($_COOKIE);
		}
	}

	private function stripSlashes(&$data) {
		return (is_array($data) ? array_map(array($this, 'stripSlashes'), $data) : stripslashes($data));
	}
}