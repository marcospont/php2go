<?php

	require_once('config.example.php');
	import('php2go.cache.CacheManager');
	
	println('<b>PHP2Go Examples</b> : php2go.cache.CacheManager<br>');
	
	// create the cache manager
	$cache = CacheManager::factory('file');
	
	// enable debug
	$cache->Storage->setDebug();
	
	// define lifetime in seconds
	$cache->Storage->setLifeTime(10);
	
	// define base directory
	$cache->Storage->setBaseDir(PHP2GO_CACHE_PATH);
		
	// query the cache engine for a given object ID
	if (!$data = $cache->load('testObject')) {
		// object doesn't exists or is invalid: send it to cache
		$data = array(1, 2, 3);
		$cache->save($data, 'testObject');
	}
	
	println('Last cache status: ' . $cache->getLastStatus());
	dumpVariable($data);

?>