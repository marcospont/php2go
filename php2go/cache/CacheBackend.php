<?php

abstract class CacheBackend extends Component
{
	private static $backends = array(
		Cache::BACKEND_APC,
		Cache::BACKEND_EACCELERATOR,
		Cache::BACKEND_FILE,
		Cache::BACKEND_MEMCACHE
	);
	protected $lifetime = 3600;

	public static function factory($options) {
		if (is_string($options)) {
			$type = $options;
			$options = array();
		} elseif (is_array($options)) {
			$type = Util::consumeArray($options, 'type', Cache::BACKEND_FILE);
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid cache backend configuration.'));
		}
		$config = array('options' => $options);
		if (in_array($type, self::$backends)) {
			$config['class'] = 'CacheBackend' . ucfirst($type);
		} else {
			$config['class'] = $type;
			$config['parent'] = 'CacheBackend';
		}
		return Php2Go::newInstance($config);
	}

	public function getLifetime() {
		return $this->lifetime;
	}

	public function setLifetime($lifetime) {
		$this->lifetime = ($lifetime !== null ? max(0, $lifetime) : $lifetime);
	}

	abstract public function load($id);

	abstract public function contains($id);

	abstract public function save($data, $id, $lifetime=false);

	abstract public function touch($id, $lifetime);

	abstract public function delete($id);

	abstract public function clean($mode=Cache::CLEANING_MODE_ALL, $param=null);

	public function getIds() {
		return array();
	}

	public function getUsage() {
		return 0;
	}

	abstract public function getFeatures();

	public function hasFeature($feature) {
		$features = $this->getFeatures();
		return (@$features[$feature] === true);
	}
}