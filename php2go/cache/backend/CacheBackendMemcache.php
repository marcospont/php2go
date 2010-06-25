<?php

class CacheBackendMemcache extends CacheBackend
{
	private static $defaultServer = array(
		'host' => '127.0.0.1',
		'port' => 11211,
		'persistent' => true,
		'weight' => 1,
		'timeout' => 1,
		'retryInterval' => 15,
		'status' => true,
		'failureCallback' => null
	);
	private $servers = array();
	private $compression;
	private $memcache;
	
	public function __construct(array $options=array()) {
		if (!extension_loaded('memcache'))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" extension is not available.', array('memcache')));
		parent::__construct($options);
	}
	
	public function setLifetime($lifetime) {
		parent::setLifetime($lifetime);
		if ($lifetime > 2592000)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Maximum lifetime for memcache backend is 2592000.'));
	}
	
	public function getServers() {
		return $this->servers;
	}
	
	public function setServers($servers) {
		if (!$this->memcache) {
			if (!is_array($servers))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Memcache servers configuration must be an array.'));
			foreach ($servers as $server) {
				if (!is_array($server))
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Memcache servers configuration must be an array of arrays.'));
				$this->servers[] = array_merge(self::$defaultServer, $server);					
			}
		} else {
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Memcache servers must be set before any cache operation.'));
		}
	}
	
	public function getCompression() {
		return $this->compression;
	}
	
	public function setCompression($compression) {
		$this->compression = !!$compression;
	}
	
	public function load($id) {
		$this->connect();
		$item = $this->memcache->get($id);
		if (is_array($item) && isset($item[0]))
			return $item[0];
		return false;
	}
	
	public function contains($id) {
		$this->connect();
		$item = $this->memcache->get($id);
		return (is_array($item) && isset($item[0]));
	}
	
	public function save($data, $id, $lifetime=false) {
		$this->connect();
		$time = time();
		$lifetime = ($lifetime === false ? $this->lifetime : $lifetime);
		$flag = ($this->compression ? MEMCACHE_COMPRESSED : 0);
		$metaData = array(
			'time' => $time,
			'expire' => ($lifetime === null ? 9999999999 : $time + $lifetime)
		);
		return @$this->memcache->set($id, array($data, $metaData), $flag, ($lifetime === null ? 0 : $lifetime));
	}
	
	public function touch($id, $lifetime) {
		$this->connect();
		$flag = ($this->compression ? MEMCACHE_COMPRESSED : 0);
		$item = $this->memcache->get($id);
		if (is_array($item)) {
			$data = $item[0];
			$metaData = $item[1];
			$metaData['time'] = time();
			$metaData['expire'] += $lifetime;
			if ($metaData['expire'] <= $metaData['time'])
				return false;
			if (!($result = $this->memcache->replace($id, array($data, $metaData), $flag, $metaData['expire'] - $metaData['time'])))
				$result = $this->memcache->set($id, array($data, $metaData), $flag, $metaData['expire'] - $metaData['time']);
			return $result;
		}
		return false;
	}
	
	public function delete($id) {
		$this->connect();
		return $this->memcache->delete($id, 0);
	}
	
	public function clean($mode=Cache::CLEANING_MODE_ALL, $param=null) {
		$this->connect();
		switch ($mode) {
			case Cache::CLEANING_MODE_ALL :				
				return $this->memcache->flush();
			case Cache::CLEANING_MODE_EXPIRED :
			case Cache::CLEANING_MODE_PATTERN :
				$allSlabs = $this->memcache->getExtendedStats('slabs');
				foreach ($allSlabs as $server => $slabs) {
					foreach (array_keys($slabs) as $slabId) {
						$dump = $this->memcache->getExtendedStats('cachedump', intval($slabId));
						foreach ($dump as $server => $entries) {
							if ($entries) {
								foreach ($entries as $id => $detail) {
									if ($mode == Cache::CLEANING_MODE_EXPIRED && time() > $detail[1])
										$this->memcache->delete($id, 0);
									elseif ($mode == Cache::CLEANING_MODE_PATTERN && strpos($id, $param) === 0)
										$this->memcache->delete($id, 0);
								}
							}
						}
					}
				}
				return true;
		}
	}
	
	public function getIds() {
		$this->connect();
		$ids = array();
		$allSlabs = $this->memcache->getExtendedStats('slabs');
		foreach ($allSlabs as $server => $slabs) {
			foreach (array_keys($slabs) as $slabId) {
				$dump = $this->memcache->getExtendedStats('cachedump', intval($slabId));
				foreach ($dump as $server => $entries) {
					if ($entries) {
						foreach (array_keys($entries) as $id)
							$ids[] = $id;
					}
				}
			}
		}
		return $ids;
	}
	
	public function getUsage() {
		$total = null;
		$used = null;
		$this->connect();
		$stats = $this->memcache->getExtendedStats();
		foreach ($stats as $server => $data) {
			if ($data === false)
				continue;
			$maxBytes = $data['limit_maxbytes'];
			$bytes = $data['bytes'];
			if ($bytes > $maxBytes)
				$bytes = $maxBytes;
			$total += $maxBytes;
			$used += $bytes;
		}
		if ($total === null)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Backend usage could not be calculated.'));
		return (round(100 * ($used / $total), 2));
	}
	
	public function getFeatures() {
		return array(
			'autoCleaning' => false,
			'list' => true,
			'usage' => true
		);
	}
	
	private function connect() {
		if (!$this->memcache) {
			$this->memcache = new Memcache;
			if (empty($this->servers))
				$this->servers[] = self::$defaultServer;
			foreach ($this->servers as $server)
				$this->memcache->addServer($server['host'], $server['port'], $server['persistent'], $server['weight'], $server['timeout'], $server['retryInterval'], $server['status'], $server['failureCallback']);
		}
	}
}