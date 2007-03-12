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
 * Read and write global configuration settings
 *
 * This class manages the global configuration settings, defined
 * in the $P2G_USER_CFG array and processed by the framework's
 * initialization script (via {@link Init} class).
 *
 * @package php2go
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Conf
{
	/**
	 * Configuration settings
	 *
	 * @var array
	 * @access private
	 */
	var $config;

	/**
	 * Class constructor
	 *
	 * Shouldn't be called directly. Always use {@link getInstance}
	 * when you need to read, add or modify configuration settings.
	 *
	 * @return Conf
	 */
	function Conf() {
	}

	/**
	 * Get the singleton of the Conf class
	 *
	 * @return Conf
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new Conf;
		return $instance;
	}
	
	/**
	 * Get database connection settings for a given connetion ID
	 *
	 * If the $connectionId argument is missing, the method will
	 * try to use the DATABASE.DEFAULT_CONNECTION setting or the
	 * first key of the DATABASE.CONNECTIONS array.
	 *
	 * @param string $connectionId Connection ID
	 * @return array|FALSE Connection properties or FALSE in case of error
	 * @static
	 */
	function getConnectionParameters($connectionId=NULL) {
		$Conf =& Conf::getInstance();
		$connections = $Conf->getConfig('DATABASE.CONNECTIONS');
		if (is_array($connections)) {
			// a connection ID was requested
			if ($connectionId !== NULL) {
				if (isset($connections[$connectionId]))
					$params = (array)$connections[$connectionId];
			} else {
				// default connection ID
				$connectionId = $Conf->getConfig('DATABASE.DEFAULT_CONNECTION');
				if ($connectionId) {
					if (isset($connections[$connectionId]))
						$params = (array)$connections[$connectionId];
				} else {
					// use first connection of the connections hashmap
					list($connectionId, $value) = each($connections);
					if (is_array($value))
						$params = $value;
				}
			}
			// connection ID not found
			if (!isset($params))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_DATABASE_PARAMETERS', $connectionId), E_USER_ERROR, __FILE__, __LINE__);
			if (array_key_exists('PERSISTENT', $params))
				$params['PERSISTENT'] = (bool)$params['PERSISTENT'];
			$params['ID'] = $connectionId;
		} else {
			$connectionId = 'DEFAULT';
			$params = array(
				'HOST' => TypeUtils::ifFalse($Conf->getConfig('DATABASE_HOST'), ''),
				'USER' => $Conf->getConfig('DATABASE_USER'),
				'PASS' => $Conf->getConfig('DATABASE_PASS'),
				'BASE' => $Conf->getConfig('DATABASE_BASE'),
				'TYPE' => $Conf->getConfig('DATABASE_TYPE'),
				'PERSISTENT' => ($Conf->getConfig('DATABASE_PCONNECTION') === TRUE),
				'AFTERCONNECT' => $Conf->getConfig('DATABASE_AFTERCONNECT'),
				'BEFORECLOSE' => $Conf->getConfig('DATABASE_BEFORECLOSE')
			);
			$params['ID'] = $connectionId;
		}
		if (!empty($params['DSN']) || (!empty($params['USER']) && !empty($params['TYPE'])))
			return $params;
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATABASE_PARAMETERS', $connectionId), E_USER_ERROR, __FILE__, __LINE__);
		return FALSE;
	}	

	/**
	 * Load a new set of configuration entries from a given file
	 *
	 * This method is used by {@link Init} class to initialize global
	 * configuration settings.
	 *
	 * @param string $configModule Configuration file path
	 */
	function loadConfig($configModule) {
		$this->config = includeFile($configModule, TRUE);
	}

	/**
	 * Create or replace a single configuration entry or a set of entries
	 *
	 * <code>
	 * $Conf =& Conf::getInstance();
	 * $Conf->setConfig('LOCALE', 'en-us');
	 * $Conf->setConfig(array('KEY'=>'value', 'ANOTHER_KEY'=>'another_value'));
	 * </code>
	 *
	 * @param string|array $configName Config key or a key=>value hashmap
	 * @param mixed $configValue Value
	 * @todo Allow setting array members by using a dot path
	 */
	function setConfig($configName, $configValue='') {
		if (is_array($configName) && trim($configValue) == '') {
			if (isset($this->config))
				$this->config = array_merge($this->config, $configName);
			else
				$this->config = $configName;
		} else {
			$this->config[$configName] = $configValue;
		}
	}

	/**
	 * Get a configuration setting
	 *
	 * @param string $configName Config key
	 * @param mixed $fallback Return value to be used when the key is not found
	 * @return mixed Config key value or $fallback if the key doesn't exist
	 */
	function getConfig($configName, $fallback=FALSE) {
		return findArrayPath($this->config, $configName, '.', $fallback);
	}

	/**
	 * Get all configuration settings
	 *
	 * @return array
	 */
	function &getAll() {
		return $this->config;
	}
}
?>