<?php

class Router extends Component
{
	protected $baseUrl;
	public $appendParams = true;
	public $showScriptFile = true;
	protected $suffix;
	protected $rules = array();

	public function init() {
		$this->addDefaultRules();
	}

	public function getBaseUrl($absolute=false) {
		if (!$this->baseUrl) {
			$request = Php2Go::app()->getRequest();
			if ($this->showScriptFile)
				$this->baseUrl = $request->getScriptUrl($absolute);
			else
				$this->baseUrl = $request->getBaseUrl($absolute);
		}
		return $this->baseUrl;
	}

	public function setSuffix($suffix) {
		if (!preg_match('/^\.?[a-z]+$/', $suffix))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid URL suffix.'));
		$this->suffix = '.' . ltrim($suffix, '.');
	}

	public function getRules() {
		return $this->rules;
	}

	public function setRules($rules) {
		if (is_array($rules)) {
			$this->rules = array();
			foreach ($rules as $template => $route)
				$this->rules[] = new RouterRule($this, $template, $route);
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid rules specification.'));
		}
	}

	public function parseUrl(Request $request) {
		$pathInfo = urldecode($request->getPathInfo());
		if (!empty($pathInfo)) {
			$pathInfo = '/' . $pathInfo;
			foreach ($this->rules as $rule) {
				if (($result = $rule->parse($pathInfo)) !== false)
					return $result;
			}
		}
		return $pathInfo;
	}

	public function createUrl($route, array $params=array(), $absolute=false, $ampersand='&') {
		foreach ($params as &$param) {
			if ($param === null)
				$param = '';
		}
		if (isset($params['#'])) {
			$anchor = '#' . $params['#'];
			unset($params['#']);
		} else {
			$anchor = '';
		}
		foreach ($this->rules as $rule) {
			if (($url = $rule->create($route, $params, $ampersand))) {
				return $this->getBaseUrl($absolute) . '/' . $url . $anchor;
			}
		}
		return $this->buildUrl($route, $params, $absolute, $ampersand) . $anchor;
	}

	protected function buildUrl($route, array $params=array(), $absolute=false, $ampersand='&') {
		$url = rtrim($this->getBaseUrl($absolute) . '/' . $route, '/');
		if ($this->appendParams) {
			$url = rtrim($url . '/' . Util::buildPathInfo($params, '/', '/'), '/');
			return $url;
		} else {
			$query = Util::buildPathInfo($params, '=', $ampersand);
			return ($query === '' ? $url : $url . '?' . $query);
		}
	}

	protected function addDefaultRules() {
		$this->rules[] = new RouterRule($this, ':module/:controller/:action/:id/*', ':module/:controller/:action');
		$this->rules[] = new RouterRule($this, ':controller/:action/:id/*', ':controller/:action');
	}
}