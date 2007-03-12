<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
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
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
 * Last read operation was a hit
 */
define('CACHE_HIT', 1);
/**
 * Last read operation was a hit, but cache object was stale (expired)
 */
define('CACHE_STALE', 2);
/**
 * Last read operation was a miss (object not found)
 */
define('CACHE_MISS', 3);
/**
 * Last read operation was a hit, and data was found in temp memory
 */
define('CACHE_MEMORY_HIT', 4);

/**
 * Abstract implementation of a cache storage layer
 * 
 * The read, write and delete methods must be implemented by child classes.
 * 
 * @package cache
 * @subpackage storage
 * @author Marcos Pont
 * @version $Revision$
 * @static
 */
class AbstractCache extends PHP2Go
{
	/**
	 * Lifetime in seconds for cache objects
	 *
	 * @var int
	 */
	var $lifeTime = CACHE_MANAGER_LIFETIME;
	
	/**
	 * Maximum valid timestamp for cache objects
	 *
	 * @var int
	 */
	var $lastValidTime;
	
	/**
	 * Result of the last read operation
	 *
	 * @var int
	 */
	var $currentStatus = CACHE_MISS;
	
	/**
	 * Debug flag
	 *
	 * @var bool
	 */
	var $debug = FALSE;
	
	/**
	 * Whether memory cache is enabled
	 *
	 * @var bool
	 */
	var $memoryCache = FALSE;
	
	/**
	 * Memory cache storage area
	 *
	 * @var array
	 * @access private
	 */
	var $memoryTable = array();
	
	/**
	 * Memory cache size
	 *
	 * @var int
	 * @access private
	 */
	var $memoryLimit = 100;
	
	/**
	 * Group ID to be used to save and restore memory cache
	 *
	 * @var string
	 * @access private
	 */
	var $memoryCacheGroup;
	
	/**
	 * Flag indicating memory cache was already been loaded
	 *
	 * @var bool
	 * @access private
	 */
	var $memoryFirstRead = TRUE;
	
	/**
	 * Flag indicating memory cache was already been changed
	 *
	 * @var bool
	 * @access private
	 */
	var $memoryCacheChanged = FALSE;
	
	/**
	 * Whether to use checksum control when reading and writing cache objects
	 *
	 * @var bool
	 * @access private
	 */
	var $checksum = TRUE;
	
	/**
	 * Checksum function
	 *
	 * @var string
	 * @access private
	 */
	var $checksumFunc = 'crc32';
	
	/**
	 * Checksum string length
	 *
	 * @var int
	 * @access private
	 */
	var $checksumLength = 32;
	
	/**
	 * Frequency in which expired cache objects are
	 * automatically removed. When set to 0, this 
	 * feature is disabled.
	 *
	 * @var int
	 * @access private
	 */
	var $autoCleanFrequency = 0;
	
	/**
	 * Registers the number of write operations
	 *
	 * @var int
	 * @access private
	 */
	var $writeCount = 0;

	/**
	 * Class constructor
	 *
	 * @return AbstractCache
	 */
	function AbstractCache() {
		parent::PHP2Go();
		if ($this->isA('AbstractCache', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'AbstractCache'), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Class destructor
	 * 
	 * If memory cache is enabled, and has its contents changed,
	 * the entries are saved in the storage layer so that they can
	 * be used again by the next request.
	 */
	function __destruct() {
		if ($this->memoryCache && $this->memoryCacheChanged && !empty($this->memoryTable)) {
			$old = $this->debug;
			$this->debug = FALSE;
			if ($this->write($this->memoryTable, '__memCache', $this->memoryCacheGroup)) {
				$this->debug = $old;
				$this->_debug('Memory state saved');
			}
		}
	}

	/**
	 * Initializes the storage layer
	 */
	function initialize() {
		$this->lastValidTime = (time() - $this->lifeTime);
		PHP2Go::registerDestructor($this, '__destruct');
	}

	/**
	 * Define the lifetime, in seconds, for cache objects
	 *
	 * @param int $time Lifetime
	 * @return int Previous lifetime setting
	 */
	function setLifeTime($time) {
		$oldLifeTime = $this->lifeTime;
		$lifeTime = abs(intval($time));
		if ($lifeTime > 0) {
			$this->lifeTime = $lifeTime;
			$this->lastValidTime = time() - $this->lifeTime;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MUST_BE_POSITIVE', array("\$lifeTime", 'setLifeTime')), E_USER_ERROR, __FILE__, __LINE__);
		}
		return $oldLifeTime;

	}

	/**
	 * Set maximum timestamp for cache objects
	 *
	 * @param int $time UNIX timestamp
	 * @return int Previous last valid timestamp
	 */
	function setLastValidTime($time) {
		$oldValidTime = $this->lastValidTime;
		$this->lifeTime = time() - $time;
		$this->lastValidTime = $time;
		return $oldValidTime;
	}

	/**
	 * Enable/disable debug mode
	 *
	 * @param bool $setting Enable/disable
	 */
	function setDebug($setting=TRUE) {
		$this->debug = (bool)$setting;
	}

	/**
	 * Set memory cache settings
	 *
	 * @param bool $enable Enable/disable memory cache
	 * @param int $limit Memory table size
	 * @param string $group Memory cache group
	 */
	function setMemoryCache($enable, $limit=100, $group=NULL) {
		$this->memoryCache = (bool)$enable;
		if ($this->memoryCache) {
			$limit = abs(intval($limit));
			if ($limit > 0)
				$this->memoryLimit = $limit;
			else
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MUST_BE_POSITIVE', array("\$limit", 'setMemoryCache')), E_USER_ERROR, __FILE__, __LINE__);
			$this->memoryCacheGroup = TypeUtils::ifNull($group, CACHE_MANAGER_GROUP);
		}
	}

	/**
	 * Enable/disable checksum control on read/write operations
	 *
	 * @param bool $setting Enable/disable
	 * @param string $func Checksum function
	 */
	function setReadChecksum($setting, $func=NULL) {
		$this->checksum = (bool)$setting;
		if (!empty($func) && function_exists($func))
			$this->checksumFunc = $func;
	}

	/**
	 * Enable automatic removal of expired cache objects
	 * 
	 * The $frequency argument represents the number of write
	 * operations to wait until then next automatic remove
	 * operation is performed.
	 *
	 * @param int $frequency Auto clean frequency
	 */
	function setAutoClean($frequency) {
		if ((int)$frequency > 0) {
			$this->autoCleanFrequency = $frequency;
		}
	}

	/**
	 * Load an object from the cache storage
	 *
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @param bool $force Ignore expiration control
	 * @return mixed Object data or FALSE when invalid or stale
	 */
	function load($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		if ($this->memoryCache && $data = $this->_readMemory($id, $group))
			return $data;
		return $this->read($id, $group, $force);
	}

	/**
	 * Save an object in the cache storage
	 *
	 * @param mixed $data Object data
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @return bool
	 */
	function save($data, $id, $group=CACHE_MANAGER_GROUP) {
		if ($this->autoCleanFrequency > 0) {
			if ($this->autoCleanFrequency == $this->writeCount) {
				$this->clear($group);
				$this->writeCount = 1;
			}
		}
		if ($this->memoryCache)
			$this->_writeMemory($data, $id, $group);
		return $this->write($data, $id, $group);
	}

	/**
	 * Abstract load method
	 * 
	 * Must be implemented in the child classes in order
	 * to search for the object in the cache storage, verify
	 * if the object is stale and complete, and return its
	 * contents in case of success.
	 *
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @param bool $force Ignore expiration control
	 * @return mixed
	 * @abstract 
	 */
	function read($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		return FALSE;
	}

	/**
	 * Abstract write method
	 * 
	 * Must be implemented in the child classes in order
	 * to add or replace an object in the cache storage.
	 *
	 * @param mixed $data Object data
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @return bool
	 * @abstract 
	 */
	function write($data, $id, $group=CACHE_MANAGER_GROUP) {
		return FALSE;
	}

	/**
	 * Abstract remove method
	 * 
	 * Must be implemented by the child classes.
	 *
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @return bool
	 */
	function remove($id, $group=CACHE_MANAGER_GROUP) {
		return FALSE;
	}

	/**
	 * Abstract clear method
	 *
	 * @param string $group Cache group
	 * @return bool
	 */
	function clear($group=NULL) {
		if ($this->memoryCache)
			$this->memoryTable = array();
		return TRUE;
	}

	/**
	 * Calculate the checksum of a given argument
	 *
	 * @param mixed $data Data
	 * @return string Calculated checksum
	 * @access private
	 */
	function _getChecksum($data) {
		$func = $this->checksumFunc;
		$len = $this->checksumLength;
		switch($func) {
			case 'crc32' :
				return sprintf("% {$len}d", crc32($data));
			case 'md5' :
				return sprintf("% {$len}d", md5($data));
			case 'strlen' :
				return sprintf("% {$len}d", strlen($data));
			default :
				return sprintf("% {$len}d", crc32($data));
		}
	}

	/**
	 * Reads information from the memory table
	 *
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @access private
	 * @return mixed
	 */
	function _readMemory($id, $group) {
		if ($this->memoryFirstRead) {
			$old = $this->debug;
			$this->debug = FALSE;
			$data = $this->read('__memCache', $this->memoryCacheGroup);
			$this->debug = $old;
			if ($data) {
				$this->_debug('Memory state loaded - ' . sizeof($data) . ' entries');
				$this->memoryTable = $data;
			} else {
				$this->_debug('Memory state invalid or inexistent');
			}
			$this->memoryFirstRead = FALSE;
		}
		$key = $group . '-' . $id;
		if (array_key_exists($key, $this->memoryTable)) {
			$this->_debug('Memory cache hit');
			$this->currentStatus = CACHE_MEMORY_HIT;
			return $this->memoryTable[$key];
		}
		$this->_debug('Memory cache miss');
		return FALSE;
	}

	/**
	 * Writes information in the memory table
	 *
	 * @param mixed $data Object data
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @access private
	 */
	function _writeMemory($data, $id, $group) {
		$key = $group . '-' . $id;
		$this->memoryTable[$key] = $data;
		$this->memoryCacheChanged = TRUE;
		if (sizeof($this->memoryTable) > $this->memoryLimit) {
			$this->_debug('Memory limit exceeded');
			list($key, $value) = each($this->memoryTable);
			unset($this->memoryTable[$key]);
		}
	}

	/**
	 * Utility method to display debug messages
	 *
	 * @param string $msg Message
	 * @access private
	 */
	function _debug($msg) {
		if ($this->debug)
			println("CACHE DEBUG (" . parent::getClassName() . ") --- {$msg}");
	}
}
?>