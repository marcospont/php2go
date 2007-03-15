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

import('php2go.util.Environment');

/**
 * Provides information about the server and its operating system
 *
 * @package util
 * @uses Environment
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class System extends PHP2Go
{
	/**
	 * Check if server's operating system is MS Windows
	 *
	 * @return bool
	 * @static
	 */
	function isWindows() {
		return (substr(PHP_OS, 0, 3) == 'WIN');
	}

	/**
	 * Check if running PHP version is greater or equal than 5.0.0
	 *
	 * @return bool
	 * @static
	 */
	function isPHP5() {
		return IS_PHP5;
	}

	/**
	 * Check if register_globals is turned on
	 *
	 * @return bool
	 * @static
	 */
	function isGlobalsOn() {
		return (bool)System::getIni("register_globals");
	}

	/**
	 * Get the description of the server's operating system
	 *
	 * @return string
	 * @static
	 */
	function getOs() {
		return PHP_OS;
	}

	/**
	 * Get the name of the server computer
	 *
	 * @return string
	 * @static
	 */
	function getSystemName() {
		return Environment::get('COMPUTERNAME');
	}

	/**
	 * Get the name of interface between PHP and the web server
	 *
	 * @return string
	 * @static
	 */
	function getServerAPIName() {
		return php_sapi_name();
	}

	/**
	 * Get the value of a PHP initialization variable
	 *
	 * @param string $key Variable name
	 * @return mixed
	 * @static
	 */
	function getIni($key) {
		return ini_get($key);
	}

	/**
	 * Set the value of a PHP initialization variable
	 *
	 * @param string $key Variable name
	 * @param mixed $value Variable value
	 * @return bool
	 * @static
	 */
	function setIni($key, $value) {
		return @ini_set(TypeUtils::parseString($key), $value);
	}

	/**
	 * Get the path to the server's temp dir
	 *
	 * @return string
	 * @static
	 */
	function getTempDir() {
		if (System::isWindows())
			if (Environment::has('TEMP'))
				return Environment::get('TEMP');
			else if (Environment::has('TMP'))
				return Environment::get('TMP');
			else if (Environment::has('windir'))
				return Environment::get('windir') . '\temp';
			else
				return Environment::get('SystemRoot') . '\temp';
		else if (Environment::has('TMPDIR'))
			return Environment::get('TMPDIR');
		else
			return '/tmp';
	}

	/**
	 * Tries to load a PHP extension if not loaded
	 *
	 * This method used the {@link dl()} function to
	 * load the extension library, when not loaded by
	 * default when PHP initializes.
	 *
	 * @param string $extensionName Extension name
	 * @return bool
	 * @static
	 */
	function loadExtension($extensionName) {
		$extensionMap = array(
			'HP-UX' => '.sl',
			'AIX' => '.a',
			'OSX' => '.bundle',
			'LINUX' => '.so'
		);
		if (!extension_loaded($extensionName)) {
            if (System::getIni('enable_dl') != 1 || System::getIni('safe_mode') == 1) {
                return FALSE;
            }
			$osName = System::getOs();
			if (System::isWindows()) {
				$resourceName = $extensionName . '.dll';
			} else if (isset($extensionMap[strtoupper($osName)])) {
				$resourceName = $extensionName . $extensionMap[$osName];
			} else {
				$resourceName = $extensionName . '.so';
			}
			return @dl('php_' . $resourceName) || @dl($resourceName);
		}
		return TRUE;
	}

	/**
	 * Get server's time, expressed in seconds and microseconds
	 *
	 * @return float
	 * @static
	 */
	function getMicrotime() {
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>