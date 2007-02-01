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

/**
 * Utility class used to parse and load template config variables
 *
 * PHP2Go's template engine is able to load variables from configuration
 * files written in the .ini format. This class is used to parse these
 * files, extracting sections and variables from it.
 *
 * For each script execution, the framework will use one single instance
 * of this class.
 *
 * @package template
 * @uses CacheManager
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net
 * @version $Revision$
 */
class TemplateConfigFile extends PHP2Go
{
	/**
	 * Parsed config variables
	 *
	 * @var array
	 * @access private
	 */
	var $data = array();

	/**
	 * Parsing settings
	 *
	 * @var array
	 * @access private
	 */
	var $options = array();

	/**
	 * Owner template
	 *
	 * @var object Template
	 * @access private
	 */
	var $_Template = NULL;

	/**
	 * Class constructor
	 *
	 * @param Template &$Template Owner template
	 * @param array $options Options
	 * @return TemplateConfigFile
	 */
	function TemplateConfigFile(&$Template, $options=array()) {
		parent::PHP2Go();
		if (!TypeUtils::isInstanceOf($Template, 'Template'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Template'), E_USER_ERROR, __FILE__, __LINE__);
		$this->_Template =& $Template;
		$options = (array)$options;
		$this->options['booleanize'] = TypeUtils::ifNull(@$options['booleanize'], TRUE);
		$this->options['caseSensitive'] = TypeUtils::ifNull(@$options['caseSensitive'], TRUE);
		$this->options['baseDir'] = TypeUtils::ifNull(@$options['baseDir'], '');
	}

	/**
	 * Get all variables or a section from a file
	 *
	 * @param string $filePath Config file path
	 * @param string $section Section name
	 * @return array
	 */
	function get($filePath, $section=NULL) {
		$fullPath = $this->options['baseDir'] . $filePath;
		if (!isset($this->data[$fullPath]))
			$this->loadFile($filePath);
		if (isset($this->data[$fullPath])) {
			if (!empty($section)) {
				if (isset($this->data[$fullPath]['sections'][$section]))
					return $this->data[$fullPath]['sections'][$section]['vars'];
				return array();
			}
			return (empty($this->data[$fullPath]['sections']) ? $this->data[$fullPath]['vars'] : $this->data[$fullPath]['sections']);
		}
		return array();
	}

	/**
	 * Loads a new configuration file
	 *
	 * @param string $filePath File path
	 */
	function loadFile($filePath) {
		$filePath = $this->options['baseDir'] . $filePath;
		if ($this->_Template->cacheOptions['enabled']) {
			import('php2go.cache.CacheManager');
			$Cache = CacheManager::factory('file');
			$cacheId = realpath($filePath);
			if ($this->_Template->cacheOptions['useMTime']) {
				$Cache->Storage->setLastValidTime(filemtime($filePath));
			} elseif ($this->_Template->cacheOptions['lifeTime']) {
				$Cache->Storage->setLifeTime($this->_Template->cacheOptions['lifeTime']);
			}
			if ($this->_Template->cacheOptions['baseDir'])
				$Cache->Storage->setBaseDir($this->_Template->cacheOptions['baseDir']);
			$data = $Cache->load($cacheId, $this->_Template->cacheOptions['group']);
			if ($data) {
				$this->data[$filePath] = $data;
			} else {
				$fileContents = @file_get_contents($filePath);
				if ($fileContents !== FALSE) {
					$this->data[$filePath] = $this->_parseContents($fileContents);
					$Cache->save($this->data[$filePath], $cacheId, $this->_Template->cacheOptions['group']);
					$Cache->save($this->data[$filePath], $cacheId, $this->_Template->cacheOptions['group']);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
				}
			}
		} else {
			$fileContents = @file_get_contents($filePath);
			if ($fileContents !== FALSE) {
				$this->data[$filePath] = $this->_parseContents($fileContents);
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
			}
		}
	}

	/**
	 * Parse contents of a configuration file
	 *
	 * @param string $contents File contents
	 * @access private
	 * @return string
	 */
	function _parseContents($contents) {
		$contents = (string)$contents;
		$contents = preg_replace('~\r\n?~', "\n", $contents);
		$result = array(
			'sections' => array(),
			'vars' => array()
		);
		$vars =& $result['vars'];
		// split file lines
		$matches = array();
		preg_match_all('~^.*\n?~m', $contents, $matches);
		$lines = $matches[0];
		for ($i=0,$s=sizeof($lines); $i<$s; $i++) {
			if (empty($lines[$i]))
				continue;
			if ($lines[$i][0] == '[' && preg_match('~^\s*\[([^\]]+)\]~', $lines[$i], $matches)) {
				$sectionName = $matches[1];
				if (!$this->options['caseSensitive'])
					$sectionName = strtoupper($sectionName);
				if (!isset($result['sections'][$sectionName]))
					$result['sections'][$sectionName] = array(
						'vars' => array()
					);
				$vars =& $result['sections'][$sectionName]['vars'];
				continue;
			}
			if (preg_match('~^\s*(\w+)\s*=\s*(.*)~s', $lines[$i], $matches)) {
				$varName = trim($matches[1]);
				if (!$this->options['caseSensitive'])
					$varName = strtoupper($varName);
				if (strpos($matches[2], '"""') === 0) {
					$lines[$i] = substr($matches[2], 3);
					$varValue = '';
					while ($i < $s) {
						if (($pos = strpos($lines[$i], '"""')) === FALSE) {
							$varValue .= $lines[$i++];
						} else {
							$varValue .= substr($lines[$i], 0, $pos);
							break;
						}
					}
					$booleanize = FALSE;
				} else {
					if (defined(trim($matches[2]))) {
						$varValue = constant(trim($matches[2]));
						$booleanize = FALSE;
					} else {
						$varValue = preg_replace('~^([\'"])(.*)\1$~', '\2', rtrim($matches[2]));
						$booleanize = $this->options['booleanize'];
					}
				}
				if ($booleanize) {
					if (preg_match('~^(on|true|yes|t)$~i', $varValue, $matches))
						$varValue = TRUE;
					if (preg_match('~^(off|false|no|f)$~i', $varValue, $matches))
						$varValue = FALSE;
				}
				if (!isset($vars[$varName])) {
					$vars[$varName] = $varValue;
				} else {
					$vars[$varName] = (array)$vars[$varName];
					$vars[$varName][] = $varValue;
				}
			}
		}
		return $result;
	}
}
?>