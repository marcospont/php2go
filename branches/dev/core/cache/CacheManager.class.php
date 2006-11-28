<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.cache.storage.*');

/**
 * Default lifetime for cache objects
 */
define('CACHE_MANAGER_LIFETIME', 3600);
/**
 * Default cache group
 */
define('CACHE_MANAGER_GROUP', 'php2goCache');

/**
 * Implements a cache engine for serializable data
 * 
 * The CacheManager class implements a simple cache engine for
 * any kind of serializable data. Read and write operations are
 * based on object and group IDs defined by the developer.
 * 
 * The read/write operations are executed in a separated storage
 * layer. Currenly, the only supported storage layer is the file
 * system ({@link FileCache} class).
 * 
 * Enabling memory cache, you can speed up subsequent operations
 * on the same object. Enabling checksum, you can improve security 
 * while reading and writing cache objects.
 * 
 * @package cache
 * @uses TypeUtils
 * @author Marcos Pont
 * @version $Revision$
 */
class CacheManager extends PHP2Go
{
	/**
	 * Current cache ID
	 *
	 * @var string
	 */
	var $currentId;
	
	/**
	 * Current cache group
	 *
	 * @var string
	 */
	var $currentGroup;
	
	/**
	 * Storage layer
	 *
	 * @var object AbstractCache
	 */
	var $Storage = NULL;

	/**
	 * Class constructor
	 * 
	 * {@link FileCache} will be used if no storage 
	 * layer is provided.
	 *
	 * @param AbstractCache $Storage Instance of the storage layer
	 * @return CacheManager
	 */
	function CacheManager($Storage=NULL) {
		parent::PHP2Go();
		if (!TypeUtils::isInstanceOf($Storage, 'AbstractCache'))
			$Storage = new FileCache();
		$this->Storage = $Storage;
		$this->Storage->initialize();
	}
	
	/**
	 * Get the singleton of the cache manager
	 * 
	 * When $storage is missing, 'file' will be used.
	 *
	 * @param string $storage Storage type
	 * @return CacheManager
	 * @static
	 */
	function &getInstance($storage=NULL) {
		static $instances = array();
		$storage = TypeUtils::ifNull($storage, 'file');
		if (!isset($instances[$storage])) {
			$instances[$storage] = CacheManager::factory($storage);
		}
		return $instances[$storage];
	}
	
	/**
	 * Creates a new instance of the CacheManager class,
	 * using a given storage type
	 * 
	 * <code>
	 * /* in both cases below, storage will be handled by {@link FileCache} {@*}
	 * $manager = CacheManager::factory('file');
	 * $manager = CacheManager::factory();
	 * </code>
	 * 
	 * @param string $storage Storage type
	 * @return CacheManager
	 * @static
	 */
	function factory($storage) {
		$class = ucfirst(strtolower($storage)) . 'Cache';
		if (class_exists($class)) {
			$Manager = new CacheManager(new $class());
			return $Manager;
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_LOAD_MODULE', $class), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Get status of the last read operation
	 * 
	 * The possible returned values are: {@link CACHE_HIT},
	 * {@link CACHE_MISS}, {@link CACHE_STALE} and {@link CACHE_MEMORY_HIT}.
	 *
	 * @return int
	 */
	function getLastStatus() {
		return $this->Storage->currentStatus;
	}

	/**
	 * Loads an object from the cache, given its ID and group
	 * 
	 * Calls the load() method on the storage layer, which is
	 * responsible to find the cache object and return its contents
	 * in case of success.
	 * 
	 * As cache engine applies lifetime values on stored objects,
	 * the requested ID might be present, but stale (expired). In
	 * this case, this method will return FALSE.
	 * <code>
	 * /* typical cache roundtrip {@*}
	 * $cache =& CacheManager::factory('file');
	 * $id = 'my_cache_id';
	 * if ($data = $cache->load($id)) {
	 *   print "cache hit";
	 * } else {
	 *   if ($cache->getLastStatus() == CACHE_STALE)
	 *     print "cache is stale";
	 *   else
	 *     print "cache miss";
	 *   $data = do_normal_stuff_to_generate_data();
	 *   $cache->save($data, $id);
	 * }
	 * </code>
	 *
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @param bool $force If set to TRUE, expiration control will be ignored
	 * @return mixed
	 */
	function load($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		$this->currentId = $id;
		$this->currentGroup = $group;
		return $this->Storage->load($this->currentId, $this->currentGroup, $force);	
	}

	/**
	 * Adds or updates an object in the cache
	 *
	 * @param mixed $data Object data
	 * @param string $id Object id
	 * @param string $group Object group
	 * @return bool Operation result
	 */
	function save($data, $id=NULL, $group=CACHE_MANAGER_GROUP) {
		if (!empty($id)) {
			$this->currentId = $id;
			$this->currentGroup = $group;			
		}
		return $this->Storage->save($data, $this->currentId, $this->currentGroup);	
	}

	/**
	 * Remove an object from the cache
	 *
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @return bool
	 */
	function remove($id, $group=CACHE_MANAGER_GROUP) {
		$this->currentId = $id;
		$this->currentGroup = $group;
		return $this->Storage->remove($this->currentId, $this->currentGroup);
	}
	
	/**
	 * Clear all objects of a given group, or all cache
	 * objects if $group is missing
	 *
	 * @param string $group Cache group
	 * @return bool
	 */
	function clear($group=NULL) {
		return $this->Storage->clear($group);
	}	
}
?>