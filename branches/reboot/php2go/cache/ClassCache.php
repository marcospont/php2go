<?php

class ClassCache extends CacheProxy
{
	const ID_PREFIX = 'function-';

	protected $instance;
	protected $class;
	protected $methods = array();

	public function loadOptions(array $options) {
		$this->setInstance(Util::consumeArray($options, 'instance'));
		$this->setMethods(Util::consumeArray($options, 'methods', array()));
		parent::loadOptions($options);
	}

	public function setInstance($instance) {
		if (!is_object($instance))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Instance must be an object.'));
		$this->instance = $instance;
		$this->class = get_class($instance);
	}

	public function setMethods($methods) {
		if (is_string($methods))
			$methods = explode(',', $methods);
		if (!is_array($methods))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Methods must be an array or a comma separated string.'));
		$this->methods = $methods;
	}

	public function __call($name, $args) {
		if ($this->instance && method_exists($this->instance, $name)) {
			if (in_array($name, $this->methods)) {
				if (($data = $this->getCache()->load($this->getId($name, $args))) && isset($data[0]) && isset($data[1])) {
					$output = $data[0];
					$return = $data[1];
				} else {
					ob_start();
					ob_implicit_flush(false);
					$return = call_user_func_array(array($this->instance, $name), $args);
					$output = ob_get_clean();
					$this->save(array($output, $return), $this->getId($name, $args));
				}
				echo $output;
				return $return;
			} else {
				return call_user_func_array(array($this->instance, $name), $args);
			}
		} else {
			return parent::__call($name, $args);
		}
	}

	protected function onCreateCache() {
		$this->getCache()->setAutoSerialization(true);
		$this->getCache()->setIdPrefix(self::ID_PREFIX);
	}

	protected function getId($method, array $params) {
		$id = strtolower($this->class) . '-' . $method;
		if (!empty($params))
			$id .= '-' . md5(serialize($params));
		return $id;
	}
}