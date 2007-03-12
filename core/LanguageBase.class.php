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
 * Language table manager
 * 
 * When PHP2Go initializes, the default language table of the framework
 * is loaded into the singleton of the LanguageBase class. That's the
 * way the core classes retrieve internationalized messages.
 * 
 * By adding a LANGUAGE.MESSAGES_PATH in the global configuration settings,
 * you're able to create your own language domains, which are also accessible
 * from this class. The only difference is that when using custom domains, 
 * the domain must be provided together with the name of the language entry.
 * <code>
 * $lang =& LanguageBase::getInstance();
 * $lang->getLanguageValue('MY_DOMAIN:MY_KEY');
 * $lang->getLanguageValue('MY_DOMAIN:PATH.TO.MY_KEY');
 * </code>
 * 
 * @package php2go
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class LanguageBase
{
	/**
	 * Loaded language entries
	 *
	 * @var array
	 * @access private
	 */
	var $languageBase;

	/**
	 * Class constructor
	 * 
	 * Shouldn't be called directly. Always use {@link getInstance} to read,
	 * add or modify language entries.
	 *
	 * @return LanguageBase
	 */
	function LanguageBase() {
		$this->languageBase = array();
	}

	/**
	 * Get the singleton of the LanguageBase class
	 *
	 * @return LanguageBase
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new LanguageBase();
		return $instance;
	}

	/**
	 * Clear all loaded language entries
	 */
	function clearLanguageBase() {
		$this->languageBase = array();
	}

	/**
	 * Add/replace a set of language entries in a given language domain
	 * 
	 * @param array $languageTable Set of language entries
	 * @param string $domain Domain
	 */
	function loadLanguageTableByValue($languageTable, $domain) {
		$languageTable = (array)$languageTable;
		if (isset($this->languageBase[$domain])) {
			$temp = $this->languageBase[$domain];
			$this->languageBase[$domain] = array_merge($temp, $languageTable);
		} else
			$this->languageBase[$domain] = $languageTable;
	}

	/**
	 * Loads language entries from a file
	 * 
	 * This method is called inside {@link Init} class to load 
	 * internationalized messages used by the framework into the
	 * 'PHP2GO' language domain.
	 *
	 * @param string $languageFile Language file
	 * @param string $domain Domain that must receive the new entries
	 */
	function loadLanguageTableByFile($languageFile, $domain) {
		$this->loadLanguageTableByValue(includeFile($languageFile, TRUE), $domain);
	}

	/**
	 * Resolve a given language entry
	 * 
	 * The $params attribute expects a single substitution
	 * variable or an array of substitution variables. This variables
	 * are used to replace printf placeholders inside the message.
	 *
	 * @param string $key Language entry key
	 * @param mixed $params Substitution arguments
	 * @return string|NULL Resolved message or NULL when not found
	 */
	function getLanguageValue($key, $params=NULL) {
		$key = trim($key);
		if (($pos = strpos($key, ':')) !== FALSE) {
			$domain = substr($key, 0, $pos);
			$key = substr($key, $pos+1);
		} else {
			$domain = 'PHP2GO';
		}
		// load the language domain if the domain wasn't already loaded
		if ($domain != 'PHP2GO' && !isset($this->languageBase[$domain]))
			$this->_loadLanguageDomain($domain);
		// search for the language key (accepts multidimensional search)
		$value = (strpos($key, '.') !== FALSE ? findArrayPath(@$this->languageBase[$domain], $key) : @$this->languageBase[$domain][$key]);
		if ($value) {
			if ($params !== NULL)
				return (is_array($params) ? vsprintf($value, $params) : sprintf($value, $params));
			else
				return $value;
		}
		return NULL;
	}

	/**
	 * Loads a given language domain
	 * 
	 * Based on the messages path (LANGUAGE.MESSAGES_PATH) and on the
	 * active language code (LANGUAGE_CODE), determine the path of
	 * the language domain file in the file system. Tries to read that
	 * file, and load its contents into the language table in case of
	 * success.
	 *
	 * @param string $domain Domain name
	 * @access private
	 */
	function _loadLanguageDomain($domain) {
		$Conf =& Conf::getInstance();
		$code = $Conf->getConfig('LANGUAGE_CODE');
		$path = $Conf->getConfig('LANGUAGE.MESSAGES_PATH');
		if (!empty($path)) {
			$path = rtrim($path, '/\\') . '/';
			$filename = $path . $code . '/' . $domain . '.php';
			if (file_exists($filename)) {
				$table = includeFile($filename, TRUE);
				if (is_array($table)) {
					$this->loadLanguageTableByValue($table, $domain);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_LANGDOMAIN_FILE', array($domain, $code)), E_USER_ERROR, __FILE__, __LINE__);
				}
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_LANGDOMAIN_FILE', array($domain, $code)), E_USER_ERROR, __FILE__, __LINE__);
			}
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CONFIG_ENTRY_NOT_FOUND', 'LANGUAGE/MESSAGES_PATH'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
}
?>