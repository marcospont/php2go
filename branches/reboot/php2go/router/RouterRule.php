<?php

class RouterRule
{
	private static $defaultRequirements = array(
		'module' => '[a-zA-Z]\w*',
		'controller' => '[a-zA-Z]\w*',
		'action' => '[a-zA-Z]\w*',
		'id' => '\d+',
		'year' => '[12][0-9]{3}',
		'month' => '0[1-9]|1[012]',
		'day' => '0[1-9]|[12][0-9]|3[01]'
	);
	private $router;	
	private $route;
	private $routeRegex;
	private $routeRefs = array();
	private $template;	
	private $regex;	
	private $params = array();
	private $defaults = array();
	private $requirements = array();
	private $partial = false;
	private $suffix;
	
	public function __construct(Router $router, $template, $route) {
		$this->router = $router;
		$this->template = ltrim($template, '/');
		if (is_string($route)) {
			$this->route = trim($route, '/');
		} elseif (is_array($route) && isset($route[0])) {
			$this->route = array_shift($route);
			if (isset($route['defaults']))
				$this->defaults = $route['defaults'];
			if (isset($route['requirements']))
				$this->requirements = $route['requirements'];
		}
		if (empty($this->route))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid router rule specification.'));
		$this->suffix = $this->parseSuffix($this->template);
		$this->compile();
	}
	
	public function parse($pathInfo) {
		$suffix = $this->parseSuffix($pathInfo);
		if ($suffix !== $this->suffix)
			return false;
		$this->compile();
		if ($route = $this->match($pathInfo))
			return $route;
		return false;
	}
	
	public function create($route, array $params, $ampersand='&') {
		$this->compile();
		$trans = array('*' => '');
		if ($route != $this->route) {
			$matches = array();
			if ($this->routeRegex !== null && preg_match($this->routeRegex, $route, $matches)) {
				foreach ($this->routeRefs as $key => $name)
					$trans[$name] = $matches[$key];	
			} else {
				return false;
			}
		}
		foreach ($this->params as $name) {
			if (isset($params[$name])) {
				$trans[":{$name}"] = urlencode($params[$name]);
				unset($params[$name]);
			} elseif (isset($this->defaults[$name])) {
				$trans[":{$name}"] = urlencode($this->defaults[$name]);
			} else {
				return false;
			}
		}
		$url = strtr($this->template, $trans);
		if (!empty($params)) {
			if ($this->partial || $this->router->appendParams) {
				$url .= '/' . Util::buildPathInfo($params, '/', '/');
				$url .= $this->suffix;
			} else {
				$url .= $this->suffix;
				$url .= '?' . Util::buildPathInfo($params, '=', $ampersand);
			}
		}
		return $url;	
	}
	
	private function compile() {
		if (!$this->regex) {
			// detect route references
			$routeRegexParts = array();
			$parts = explode('/', $this->route);
			if (count($parts) > 3)
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid router rule specification.'));
			foreach ($parts as $part) {
				if ($part[0] == ':')
					$this->routeRefs[substr($part, 1)] = $part;
			}
			// build regex
			$regexParts = array();
			$parts = explode('/', $this->template);
			foreach ($parts as $part) {				
				$q = null;
				$m = array();
				$part = trim($part);
				$param = (strpos($part, ':') !== false);
				if ($param && preg_match('/^:([^:]+)$/', $part, $m)) {
					if (!isset($this->routeRefs[$m[1]]) && array_key_exists($m[1], $this->defaults))
						$q = '?';
					$regex = (isset($this->requirements[$m[1]]) ? $this->requirements[$m[1]] : (isset(self::$defaultRequirements[$m[1]]) ? self::$defaultRequirements[$m[1]] : '[^\/]+'));
					$regexParts[] = '(?:/(?P<' . $m[1] . '>' . $regex . ')' . $q . ')' . $q;
					if (isset($this->routeRefs[$m[1]]))
						$routeRegexParts[$this->routeRefs[$m[1]]] = '(?P<' . $m[1]  . '>' . $regex . ')';
					else
						$this->params[] = $m[1];
				} elseif ($param && preg_match_all('/(?!\\\\):([a-z_0-9]+)/i', $part, $m)) {
					$count = count($m[1]);
					foreach ($m[1] as $i => $name) {
						$q = null;
						$pos = strpos($part, ':' . $name);
						$before = substr($part, 0, $pos);
						$part = substr($part, $pos + strlen($name) + 1);
						$after = null;
						if ($i + 1 == $count && $part)
							$after = preg_quote($part);
						if ($i == 0)
							$before = '/' . $before;
						$before = preg_quote($before, '#');
						if (!isset($this->routeRefs[$name]) && array_key_exists($name, $this->defaults))
							$q = '?';
						$regex = (isset($this->requirements[$name]) ? $this->requirements[$name] : (isset(self::$defaultRequirements[$name]) ? self::$defaultRequirements[$name] : '[^\/]+'));
						$regexParts[] = '(?:' . $before . '(?P<' . $name . '>' . $regex . ')' . $q . $after . ')' . $q;
						if (isset($this->routeRefs[$name]))
							$routeRegexParts[$this->routeRefs[$name]] = '(?P<' . $name . '>' . $regex . ')';
						else
							$this->params[] = $name;
					}
				} elseif ($part == '*') {
					$regexParts[] = '(?:/(.*)?)?';
					$this->partial = true;
				} else {
					$regexParts[] = '/' . $part;
				}
			}
			$this->regex = implode('', $regexParts);
			// build route regex
			if (!empty($this->routeRefs))
				$this->routeRegex = '#^' . strtr($this->route, $routeRegexParts) . '$#';
		}
	}
	
	private function match($pathInfo) {
		$matches = array();
		if (preg_match('#^' . $this->regex . '$#', $pathInfo, $matches)) {
			// collect route references
			$routeTrans = array();
			foreach ($this->routeRefs as $key => $name) {
				$routeTrans[$this->routeRefs[$key]] = $matches[$key];
				unset($matches[$name]);
			}
			// inject params
			$params = $this->defaults;			
			foreach ($this->params as $name) {
				if (!empty($matches[$name]))
					$params[$name] = $matches[$name];
			}
			if ($this->partial) {
				$key = count($this->routeRefs) + count($this->params) + 1;				
				if (isset($matches[$key]))
					$params = array_merge($params, Util::parsePathInfo($matches[$key]));
			}
			foreach ($params as $name => $value) {
				if (!isset($this->routeRefs[$name]))
					$_GET[$name] = $value;
			}
			return strtr($this->route, $routeTrans);
		}
		return false;
	}		
	
	private function parseSuffix(&$url) {
		$matches = array();
		if (preg_match('/\.[a-z]+$/', $url, $matches)) {
			$url = substr($url, 0, -strlen($matches[0]));
			return $matches[0];
		}
		return null;
	}
}