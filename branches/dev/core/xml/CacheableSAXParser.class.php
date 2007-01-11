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

import('php2go.cache.CacheManager');
import('php2go.xml.AbstractSAXParser');

/**
 * Extends base SAX parser by adding cache control
 *
 * Using CacheableSAXParser, you can define your event handlers and parse the
 * XML string normally. Besides, the content parsed from the XML string can be
 * cached, so that parsing won't execute again until the original XML source
 * is newer than the cached values.
 *
 * @package xml
 * @uses CacheManager
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class CacheableSAXParser extends AbstractSAXParser
{
	/**
	 * Cache options
	 *
	 * @var array
	 * @access private
	 */
	var $cacheOptions = array();

	/**
	 * Class constructor
	 *
	 * @return CacheableSAXParser
	 */
	function CacheableSAXParser() {
		parent::AbstractSAXParser();
		$this->cacheOptions['group'] = 'php2goSAXParser';
		$this->cacheOptions['lifeTime'] = NULL;
		$this->cacheOptions['useMTime'] = TRUE;
	}

	/**
	 * Set cache directory
	 *
	 * @param string $dir Directory path
	 */
	function setCacheDir($dir) {
		$this->cacheOptions['baseDir'] = $dir;
	}

	/**
	 * Set cache group
	 *
	 * @param string $group Group ID
	 */
	function setCacheGroup($group) {
		$this->cacheOptions['group'] = $group;
	}

	/**
	 * Set lifetime for the cached data
	 *
	 * @param int $lifeTime Lifetime, in seconds
	 */
	function setCacheLifeTime($lifeTime) {
		$this->cacheOptions['lifeTime'] = $lifeTime;
		$this->cacheOptions['useMTime'] = FALSE;
	}

	/**
	 * Enable/disable cache control based on the original XML source's timestamp
	 *
	 * @param bool $setting Flag value
	 */
	function setUseFileMTime($setting) {
		$this->cacheOptions['useMTime'] = (bool)$setting;
		if ($this->cacheOptions['useMTime'])
			$this->cacheOptions['lifeTime'] = NULL;
	}

	/**
	 * Must be implemented by child classes
	 *
	 * This method should be used to restore to the
	 * object data read from the cache storage layer.
	 *
	 * @param mixed $data Data loaded from cache
	 * @abstract
	 */
	function loadCacheData($data) {
	}

	/**
	 * Must be implemented by child classes
	 *
	 * Should be used to return all information read
	 * from XML that should be cached.
	 *
	 * This method is executed only when the XML string
	 * was parsed successfully.
	 *
	 * @return array
	 * @abstract
	 */
	function getCacheData() {
		return array();
	}

	/**
	 * Overrides parent class implementation by adding cache control
	 *
	 * Ask cache manager if it contains a cached copy of the XML
	 * source. If cache is valid and not stale, data is restored
	 * from the cache layer. Otherwise, XML is parsed and saved
	 * in the cache for further use.
	 *
	 * @param string $xmlContent XML contents or file path
	 * @param int $srcType Source type ({@link T_BYFILE} or {@link T_BYVAR})
	 * @return bool
	 */
	function parse($xmlContent, $srcType=T_BYFILE) {
		$Cache = CacheManager::factory('file');
		if ($this->cacheOptions['baseDir'])
			$Cache->Storage->setBaseDir($this->cacheOptions['baseDir']);
		if ($srcType == T_BYFILE) {
			$cacheId = realpath($xmlContent);
			if ($this->cacheOptions['useMTime'])
				$Cache->Storage->setLastValidTime(@filemtime($xmlContent));
			elseif ($this->cacheOptions['lifeTime'] > 0)
				$Cache->Storage->setLifeTime($this->cacheOptions['lifeTime']);
		} else {
			$cacheId = dechex(crc32($xmlContent));
			($this->cacheOptions['lifeTime'] > 0) && ($Cache->Storage->setLifeTime($this->cacheOptions['lifeTime']));
		}
		$cacheData = $Cache->load($cacheId, $this->cacheOptions['group']);
		if ($cacheData !== FALSE) {
			$this->loadCacheData($cacheData);
			return TRUE;
		} else {
			$result = parent::parse($xmlContent, $srcType);
			if ($result)
				$Cache->save($this->getCacheData(), $cacheId, $this->cacheOptions['group']);
			return $result;
		}
	}
}
?>