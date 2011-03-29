<?php

class Response extends Component
{
	protected $status = 200;
	protected $headers = array();
	protected $rawHeaders = array();
	protected $compression = false;
	protected $body = '';
	public $isRedirect = false;
	public $isError = false;
	public $isException = false;

	public function __construct() {
		$this->compression = (@strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false);
		$this->setContentType('text/html; charset=' . Php2Go::app()->getCharset());
	}

	public function getHeaders() {
		return $this->headers;
	}

	public function getRawHeaders() {
		return $this->rawHeaders;
	}

	public function addHeader($name, $value, $replace=true) {
		$this->canSendHeaders(true);
		$name = $this->normalizeHeader($name);
		$value = (string)$value;
		if ($replace) {
			foreach ($this->headers as $key => $header) {
				if ($header['name'] == $key)
					unset($this->headers[$key]);
			}
		}
		$this->headers[] = array(
			'name' => $name,
			'value' => $value,
			'replace' => $replace
		);
		return $this;
	}

	public function setRawHeader($header) {
		$this->canSendHeaders(true);
		if (stripos(trim($header), 'location:') === 0)
			$this->isRedirect = true;
		$this->rawHeaders[] = (string)$value;
		return $this;
	}

	public function clearHeader($name) {
		if (!empty($this->headers)) {
			foreach ($this->headers as $key => $header) {
				if ($header['name'] == $key)
					unset($this->headers[$key]);
			}
		}
		return $this;
	}

	public function clearRawHeader($name) {
		if (!empty($this->rawHeaders)) {
			$key = array_search($name, $this->rawHeaders);
			if ($key !== false)
				unset($this->rawHeaders[$key]);
		}
		return $this;
	}

	public function clearHeaders() {
		$this->headers = array();
		return $this;
	}

	public function clearRawHeaders() {
		$this->rawHeaders = array();
		return $this;
	}

	public function getCompression() {
		return $this->compression;
	}

	public function setCompression($compression) {
		$this->compression = !!$compression;
	}

	public function addCookie(Cookie $cookie) {
		setcookie($cookie->name, $cookie->value, $cookie->expire, $cookie->path, $cookie->domain, $cookie->secure, $cookie->httpOnly);
	}

	public function removeCookie(Cookie $cookie) {
		setcookie($cookie->name, null, 0, $cookie->path, $cookie->domain, $cookie->secure, $cookie->httpOnly);
	}

	public function canSendHeaders($throw=false) {
		$sent = headers_sent($file, $line);
		if ($sent && $throw)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Headers already sent in %s, line %s', array($file, $line)));
		return !$sent;
	}

	public function sendHeaders() {
		if (!empty($this->rawHeaders) || !empty($this->headers) || $this->status == 200)
			$this->canSendHeaders(true);
		elseif ($this->status == 200)
			return;
		$statusSent = false;
		foreach ($this->rawHeaders as $header) {
			if (!$statusSent && $this->status) {
				header($header, true, $this->status);
				$statusSent = true;
			} else {
				header($header);
			}
		}
		foreach ($this->headers as $header) {
			if (!$statusSent && $this->status) {
				header($header['name'] . ': ' . $header['value'], $header['replace'], $this->status);
				$statusSent = true;
			} else {
				header($header['name'] . ': ' . $header['value'], $header['replace']);
			}
		}
		if (!$statusSent)
			header('HTTP/1.1 ' . $this->status, true, $this->status);
		return $this;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setStatus($code) {
		if (!is_int($code) || (100 > $code) || (599 < $code))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid HTTP response code.'));
		$this->isRedirect = ($code >= 300 && $code <= 307);
		$this->status = $code;
		return $this;
	}

	public function getContentType($contentType) {
		foreach ($this->headers as $header) {
			if ($header['name'] == 'Content-Type')
				return $header['value'];
		}
		foreach ($this->rawHeaders as $header) {
			if (stripos(trim($header), 'content-type:') === 0)
				return trim(substr(trim($header), strlen('content-type:')));
		}
		return null;
	}

	public function setContentType($contentType) {
		$this->addHeader('Content-Type', $contentType);
		return $this;
	}

	public function redirect($url, $status=302) {
		$this->addHeader('Location', $url, true);
		$this->setStatus($status);
		$this->sendHeaders();
		Php2Go::app()->stop();
	}

	public function getBody() {
		ob_start();
		$this->sendBody();
		return ob_get_clean();
	}

	public function setBody($content) {
		$this->body = $content;
		return $this;
	}

	public function appendBody($content) {
		$this->body .= $content;
		return $this;
	}

	public function clearBody() {
		$this->body = '';
		return $this;
	}

	public function sendBody() {
		if ($this->compression) {
			$body = preg_replace(
	            array(
	                '/(\x20{2,})/',   // extra-white spaces
	                '/\t/',           // tab
	                '/\n\r/'          // blank lines
	            ),
	            array(' ', '', ''),
	            $this->body
	        );
			echo gzencode($this->body, 9);
		} else {
			echo $this->body;
		}
	}

	public function sendResponse() {
		if ($this->compression)
			$this->addHeader('Content-Encoding', 'gzip');
		$this->sendHeaders();
		if (!$this->isRedirect)
			$this->sendBody();
	}

	public function __toString() {
		ob_start();
		$this->sendResponse();
		return ob_get_clean();
	}

	protected function normalizeHeader($name) {
        $filtered = str_replace(array('-', '_'), ' ', (string)$name);
        $filtered = ucwords(strtolower($filtered));
        $filtered = str_replace(' ', '-', $filtered);
        return $filtered;
	}
}