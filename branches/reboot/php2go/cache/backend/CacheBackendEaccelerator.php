<?php

class CacheBackendEaccelerator extends CacheBackend
{
	public function __construct() {
		if (!extension_loaded('eaccelerator'))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" extension is not available.', array('eaccelerator')));
	}
	
	public function load($id) {
		$item = eaccelerator_get($id);
		if (is_array($item) && isset($item[0]))
			return $item[0];
		return false;
	}
	
	public function contains($id) {
		$item = eaccelerator_get($id);
		return (is_array($item) && isset($item[0]));
	}
	
	public function save($data, $id, $lifetime=false) {
		$time = time();
		$lifetime = ($lifetime === false ? $this->lifetime : $lifetime);
		$flag = ($this->compression ? MEMCACHE_COMPRESSED : 0);
		$metaData = array(
			'time' => $time,
			'expire' => ($lifetime === null ? 9999999999 : $time + $lifetime)
		);
		return eaccelerator_put($id, array($data, $metaData), ($lifetime === null ? 0 : $lifetime));		
	}
	
	public function touch($id, $lifetime) {
		$item = eaccelerator_get($id);
		if (is_array($item)) {
			$data = $item[0];
			$metaData = $item[1];
			$metaData['time'] = time();
			$metaData['expire'] += $lifetime;
			if ($metaData['expire'] <= $metaData['time'])
				return false;
			return eaccelerator_put($id, array($data, $metaData), $metaData['expire'] - $metaData['time']);			
		}
		return false;
	}
	
	public function delete($id) {
		return eaccelerator_rm($id);
	}
	
	public function clean($mode=Cache::CLEANING_MODE_ALL, $param=null) {
		switch ($mode) {
			case Cache::CLEANING_MODE_ALL :
				eaccelerator_gc();
				$keys = eaccelerator_list_keys();
				foreach ($keys as $key)
					eaccelerator_rm($key['name']);
				return true;
			case Cache::CLEANING_MODE_EXPIRED :
				eaccelerator_gc();
				return true;
			case Cache::CLEANING_MODE_PATTERN :
				$keys = eaccelerator_list_keys();
				foreach ($keys as $key) {
					if (strpos($key['name'], $param))
						eaccelerator_rm($key['name']);
				}
				return true;
		}
	}
	
	public function getIds() {
		$ids = array();
		foreach (eaccelerator_list_keys() as $key)
			$ids[] = $key['name'];
		return $ids;
	}
	
	public function getFeatures() {
		return array(
			'autoCleaning' => true,
			'list' => true,
			'usage' => false
		);
	}
}