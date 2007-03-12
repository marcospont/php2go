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