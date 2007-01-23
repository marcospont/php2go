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

import('php2go.cache.storage.AbstractCache');

/**
 * Cache storage layer based on files
 *
 * @package cache
 * @subpackage storage
 * @uses System
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FileCache extends AbstractCache
{
	/**
	 * Base directory
	 *
	 * Defaults to the OS temp dir.
	 *
	 * @var string
	 * @access private
	 */
	var $baseDir;

	/**
	 * Automatic serialize/unserialize cache objects
	 *
	 * @var bool
	 * @access private
	 */
	var $autoSerialize = TRUE;

	/**
	 * Should files be locked for read/write operations?
	 *
	 * @var bool
	 * @access private
	 */
	var $lockFiles = FALSE;

	/**
	 * Whether to build obfuscated file names for cache objects
	 *
	 * @var bool
	 * @access private
	 */
	var $obfuscateFileNames = TRUE;

	/**
	 * Class constructor
	 *
	 * @return FileCache
	 */
	function FileCache() {
		parent::AbstractCache();
		$this->baseDir = System::getTempDir() . '/';
	}

	/**
	 * Set cache base directory
	 *
	 * @param string $dir Base dir
	 */
	function setBaseDir($dir) {
		$dir = str_replace("\\", "/", $dir);
		$this->baseDir = (!preg_match("~\/$~", $dir) ? $dir . '/' : $dir);
	}

	/**
	 * Enable/disable file locks on read/write operations
	 *
	 * @param bool $setting Enable/disable
	 */
	function setFileLocking($setting) {
		$this->lockFiles = (bool)$setting;
	}

	/**
	 * Enable/disable automatic serialize/unserialize calls
	 * when reading or writing cache objects
	 *
	 * @param bool $setting Enable/disable
	 */
	function setAutoSerialize($setting) {
		$this->autoSerialize = (bool)$setting;
	}

	/**
	 * Enable/disable obfuscation when build cache file names
	 *
	 * @param bool $setting Enable/disable
	 */
	function setObfuscateFileNames($setting) {
		$this->obfuscateFileNames = (bool)$setting;
	}

	/**
	 * Reads a given cache object (in a given cache group) from the file system
	 *
	 * When using {@link lastValidTime}, the cache file is considered stale
	 * if older than this property value. When using {@link lifeTime}, the
	 * last modified date of the cache file mustn't be older than current
	 * timestamp minus the lifetime value.
	 *
	 * The object is considered stale if the cache file doesn't exist, can't
	 * be read or contains an invalid checksum (when checksum control is enabled).
	 *
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @param bool $force Ignore expiration control
	 * @return mixed Object data or FALSE in case of error
	 */
	function read($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		$fileName = $this->_getFileName($id, $group);
		$exists = file_exists($fileName);
		if ($exists) {
			clearstatcache();
			$mtime = filemtime($fileName);
			$this->_debug('File cache comparison: ' . date('d/m/Y H:i:s', $mtime) . ' <= ' . date('d/m/Y H:i:s', $this->lastValidTime));
			if ($mtime > $this->lastValidTime || $force) {
				$fp = @fopen($fileName, 'rb');
				if ($this->lockFiles)
					@flock($fp, LOCK_SH);
				if ($fp !== FALSE) {
					$size = filesize($fileName);
					if ($this->checksum) {
						$savedChecksum = fread($fp, $this->checksumLength);
						$savedData = fread($fp, $size-$this->checksumLength);
						$checksum = $this->_getChecksum($savedData);
						if ($this->lockFiles)
							@flock($fp, LOCK_UN);
						fclose($fp);
						if ($savedChecksum != $checksum) {
							$this->_debug('Checksum error');
							@touch($fileName, time()-abs($this->lifeTime*2));
							$this->currentStatus = CACHE_MISS;
							return FALSE;
						}
					} else {
						$savedData = fread($fp, $size);
						if ($this->lockFiles)
							@flock($fp, LOCK_UN);
						fclose($fp);
					}
					$this->currentStatus = CACHE_HIT;
					$this->_debug('File cache hit');
					if ($this->autoSerialize)
						$savedData = unserialize($savedData);
					return $savedData;
				} else {
					$this->_debug('Cache file read error');
				}
			} else {
				@unlink($fileName);
				$this->currentStatus = CACHE_STALE;
				$this->_debug('Cache file is stale');
			}
		} else {
			$this->_debug('Cache file doesn\'t exist');
		}
		$this->currentStatus = CACHE_MISS;
		return FALSE;
	}

	/**
	 * Saves a cache object (using a given cache group) in the file system
	 *
	 * The operation returns FALSE when the file can't be opened in write mode.
	 *
	 * @param mixed $data Object data
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @return bool
	 */
	function write($data, $id, $group=CACHE_MANAGER_GROUP) {
		$fp = @fopen($this->_getFileName($id, $group), 'wb');
		if ($this->lockFiles)
			@flock($fp, LOCK_EX);
		if ($fp !== FALSE) {
			if ($this->autoSerialize)
				$data = serialize($data);
			if ($this->checksum) {
				$checksum = $this->_getChecksum($data);
				fwrite($fp, $checksum, $this->checksumLength);
			}
			fwrite($fp, $data);
			if ($this->lockFiles)
				@flock($fp, LOCK_UN);
			$this->_debug('Cache file written successfully');
			fclose($fp);
			return TRUE;
		}
		$this->_debug('File write error');
		return FALSE;
	}

	/**
	 * Removes a cache object from the file system
	 *
	 * @param string $id Object ID
	 * @param string $group Object group
	 * @return bool Returns FALSE if file doesn't exist
	 */
	function remove($id, $group=CACHE_MANAGER_GROUP) {
		$fileName = $this->_getFileName($id, $group);
		if (!@unlink($fileName)) {
			if (!file_exists($fileName))
				return FALSE;
			return @touch($fileName, time()-abs($this->lifeTime*2));
		}
	}

	/**
	 * Clear all cache object from a given group
	 *
	 * If a group ID is not provided, all contents of the cache
	 * base directory are removed.
	 *
	 * @param string $group Cache group
	 * @return bool Returns FALSE if one of the files can't be removed
	 */
	function clear($group=NULL) {
		parent::clear($group);
		$res = TRUE;
		$dir = @dir($this->baseDir);
		$pattern = preg_quote((!empty($group) ? ($this->obfuscateFileNames ? 'cache_' . md5($group) : 'cache_' . $group) : 'cache_'), '/');
		clearstatcache();
		while ($entry = $dir->read()) {
			if ($entry == '.' || $entry == '..' || is_dir($entry))
				continue;
			if (empty($group)) {
				if (preg_match($pattern, $entry) && filemtime($this->baseDir . $entry) <= $this->lastValidTime)
					$res = @unlink($this->baseDir . $entry);
			} else {
				if (preg_match($pattern, $entry))
					$res = @unlink($this->baseDir . $entry);
			}
			if (!$res)
				break;
		}
		return $res;
	}

	/**
	 * Build a file path from a given pair of $id and $group
	 *
	 * @param string $id ID
	 * @param string $group Group
	 * @return string File path
	 * @access private
	 */
	function _getFileName($id, $group) {
		$id = preg_replace("/[^0-9a-zA-Z_\.\-\:]+/", '', $id);
		$group = preg_replace("/[^0-9a-zA-Z_\.\-\:]+/", '', $group);
		if ($this->obfuscateFileNames)
			return $this->baseDir . 'cache_' . md5($group) . '_' . md5($id);
		else
			return $this->baseDir . 'cache_' . $group . '_' . $id;
	}
}
?>