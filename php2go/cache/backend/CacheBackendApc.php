<?php

class CacheBackendApc extends CacheBackend
{
	public function __construct() {
		if (!extension_loaded('apc'))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" extension is not available.', array('apc')));
	}
	
	public function load($id) {
		$item = apc_fetch($id);
		if (is_array($item) && isset($item[0]))
			return $item[0];
		return false;
	}
	
	public function contains($id) {
		$item = apc_fetch($id);
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
		return apc_store($id, array($data, $metaData), ($lifetime === null ? 0 : $lifetime));
	}
	
	public function touch($id, $lifetime) {
		$item = apc_fetch($id);
		if (is_array($item)) {
			$data = $item[0];
			$metaData = $item[1];
			$metaData['time'] = time();
			$metaData['expire'] += $lifetime;
			if ($metaData['expire'] <= $metaData['time'])
				return false;
			return apc_store($id, array($data, $metaData), $metaData['expire'] - $metaData['time']);			
		}
		return false;
	}
	
	public function delete($id) {
		return apc_delete($id);
	}
	
	public function clean($mode=Cache::CLEANING_MODE_ALL, $param=null) {
		switch ($mode) {
			case Cache::CLEANING_MODE_ALL :
				return apc_clear_cache('user');
			case Cache::CLEANING_MODE_EXPIRED :
			case Cache::CLEANING_MODE_PATTERN :
				$info = apc_cache_info('user', false);
				foreach ($info['cache_list'] as $entry) {
					if ($mode == Cache::CLEANING_MODE_PATTERN) {
						if (strpos($entry['info'], $param) === 0)
							apc_delete($entry['info']);
					} else {
						$item = apc_fetch($entry['info']);
						if (is_array($item) && time() > $item[1]['expire'])
							apc_delete($entry['info']);
					}
				}
				return true;				
		}
	}
	
	public function getIds() {
		$ids = array();
		$info = apc_cache_info('user', false);
		foreach ($info['cache_list'] as $entry)
			$ids[] = $entry['info'];
		return $ids;
	}
	
	public function getUsage() {
		$mem = apc_sma_info(true);
		$total = $mem['num_seg'] * $mem['seg_size'];
		$used = $total - $mem['avail_mem'];
		if ($total == 0)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Backend usage could not be calculated.'));
		if ($used > $total)
			return 100;
		return (round(100 * ($used / $total), 2));
	}
	
	public function getFeatures() {
		return array(
			'autoCleaning' => true,
			'list' => true,
			'usage' => true
		);
	}	
}