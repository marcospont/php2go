<?php

Php2Go::import('php2go.cache.backend.*');

class Cache extends Component
{
	const BACKEND_APC = 'apc';
	const BACKEND_EACCELERATOR = 'eaccelerator';	
	const BACKEND_FILE = 'file';
	const BACKEND_MEMCACHE = 'memcache';
	const CLEANING_MODE_ALL = 'all';
	const CLEANING_MODE_EXPIRED = 'expired';
	const CLEANING_MODE_PATTERN = 'pattern';
	
	protected $backend;
	protected $idPrefix = null;
	protected $autoSerialization = false;
	protected $autoCleaningFactor = 100;
	protected $lifetime = 3600;
	protected $lastId = null;
	
	public function getBackend() {
		if ($this->backend === null)
			$this->setBackend(new CacheBackendFile());
		return $this->backend;
	}
	
	public function setBackend($backend) {
		if (!$backend instanceof CacheBackend)
			$backend = CacheBackend::factory($backend);
		$this->backend = $backend;
		$this->backend->setLifetime($this->lifetime);
	}
	
	public function getIdPrefix() {
		return $this->idPrefix;
	}
	
	public function setIdPrefix($prefix) {
		if (empty($prefix) || !is_string($prefix))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The cache ID prefix must be a non empty string.'));
		$this->idPrefix = $prefix;
	}
	
	public function getAutoSerialization() {
		return $this->autoSerialization;
	}
	
	public function setAutoSerialization($autoSerialization) {
		$this->autoSerialization = !!$autoSerialization;
	}
	
	public function getAutoCleaningFactor() {
		return $this->autoCleaningFactor;
	}
	
	public function setAutoCleaningFactor($factor) {
		if (!is_int($factor) || $factor < 0)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The cleaning factor must be a positive number.'));
		$this->autoCleaningFactor = $factor;
	}
	
	public function getLifetime() {
		return $this->lifetime;
	}
	
	public function setLifetime($lifetime) {
		if ($lifetime === null || $lifetime < 0)
			$lifetime = 0;
		$this->lifetime = $lifetime;
		if ($this->backend)
			$this->backend->setLifetime($lifetime);
	}
	
	public function load($id, $unserialize=true) {		
		$this->lastId = $id = $this->cacheId($id);
		$this->validateId($id);
		$data = $this->getBackend()->load($id);
		if ($data !== false) {
			if ($unserialize && $this->autoSerialization)
				return unserialize($data);
			return $data;
		}
		return false;
	}
	
	public function contains($id) {
		$this->lastId = $id = $this->cacheId($id);
		$this->validateId($id);
		return $this->getBackend()->contains($id);
	}
	
	public function save($data, $id=null, $lifetime=false) {
		$id = ($id === null ? $this->lastId : $this->cacheId($id));
		$this->validateId($id);		
		if ($this->autoSerialization)
			$data = serialize($data);
		elseif (!is_string($data))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Cache data must be a string or autoSerialization must be turned on.'));
		$backend = $this->getBackend();
		if ($backend->hasFeature('autoCleaning') && $this->autoCleaningFactor > 0) {
			$rand = rand(1, $this->autoCleaningFactor);
			if ($rand == 1)
				$backend->clean(Cache::CLEANING_MODE_EXPIRED);
		}
		$abort = ignore_user_abort(true);
		$result = $backend->save($data, $id, $lifetime);
		ignore_user_abort($abort);
		if ($result) {
			return true;
		} else {
			$backend->delete($id);
			return false;
		}
	}
	
	public function touch($id, $lifetime) {
		$id = $this->cacheId($id);
		$this->validateId($id);
		return $this->getBackend()->touch($id);
	}
	
	public function delete($id) {
		$this->lastId = $id = $this->cacheId($id);
		$this->validateId($id);
		return $this->getBackend()->delete($id);
	}
	
	public function clean($mode=self::CLEANING_MODE_ALL, $param=null) {
		if (!in_array($mode, array(Cache::CLEANING_MODE_ALL, Cache::CLEANING_MODE_EXPIRED, Cache::CLEANING_MODE_PATTERN)))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid cache cleaning mode: "%s".', array($mode)));
		if ($mode == Cache::CLEANING_MODE_PATTERN) {
			if (empty($param))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Cleaning pattern should not be empty.'));
			$param = $this->idPrefix . $param;
		}
		return $this->getBackend()->clean($mode, $param);
	}
	
	public function getIds() {
		$backend = $this->getBackend();
		if ($backend->hasFeature('list')) {
			$result = $backend->getIds();
			if (!empty($this->idPrefix)) {
				$len = strlen($this->idPrefix);
				for ($i=0,$l=sizeof($result); $i<$l; $i++) {
					if (strpos($result[$i], $this->idPrefix) === 0)
						$result[$i] = substr($result[$i], $len);
				}				
			}
			return $result;
		}
		return array();
	}
	
	public function getUsage() {
		$backend = $this->getBackend();
		if ($backend->hasFeature('usage'))
			return $backend->getUsage();
		return 0;
	}
	
	protected function cacheId($id) {
		if (!empty($this->idPrefix))
			return $this->idPrefix . $id;
		return $id;
	}
	
	protected function validateId($id) {
		if (!is_string($id))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Cache id must be a string.'));
		if (!preg_match('~^[a-zA-Z0-9_\-\.]+$~D', $id))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Cache id must contain only "a-zA-Z0-9_-." characters.'));
	}
}