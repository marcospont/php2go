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
 * Reads and writes files in the ".ini" format
 *
 * The Properties class is able to read sections and their values, as well
 * as to create or change existent sections and keys.
 *
 * @package util
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Properties extends PHP2Go
{
	/**
	 * File path
	 *
	 * @var string
	 * @access private
	 */
	var $filename;

	/**
	 * Properties read from the file
	 *
	 * @var array
	 * @access private
	 */
	var $table;

	/**
	 * Whether sections should be processed
	 *
	 * @var bool
	 * @access private
	 */
	var $processSections;

	/**
	 * Whether read/write of keys should be case-sensitive
	 *
	 * @var bool
	 * @access private
	 */
	var $caseSensitive;

	/**
	 * Current section name
	 *
	 * @var string
	 * @access private
	 */
	var $currentSection = NULL;

	/**
	 * Class constructor
	 *
	 * @param string $filename File to be read or written
	 * @param bool $processSections Process sections?
	 * @param bool $caseSensitive Case-sensitive keys?
	 * @return Properties
	 */
	function Properties($filename, $processSections=FALSE, $caseSensitive=FALSE) {
		parent::PHP2Go();
		$this->filename = $filename;
		$this->processSections = (bool)$processSections;
		$this->caseSensitive = (bool)$caseSensitive;
		if (file_exists($filename)) {
			$this->_loadFile();
		} else {
			$this->table = array();
			$this->_createFile();
		}
	}

	/**
	 * Gets the name of the first section
	 *
	 * Returns FALSE when the processing of sections is disabled.
	 *
	 * @return string
	 */
	function getFirstSection() {
		if ($this->processSections) {
			reset($this->table);
			return key($this->table);
		}
		return FALSE;
	}

	/**
	 * Fetches the contents of the first section
	 *
	 * Returns FALSE when the processing of sections is disabled.
	 *
	 * @return mixed
	 */
	function fetchFirstSection() {
		return $this->getSection($this->getFirstSection());
	}

	/**
	 * Gets current section name
	 *
	 * @return string
	 */
	function getCurrentSection() {
		return $this->currentSection;
	}

	/**
	 * Fetches current section
	 *
	 * @return array
	 */
	function fetchCurrentSection() {
		return $this->getSection($this->currentSection);
	}

	/**
	 * Moves the internal pointer to the next section
	 *
	 * Returns the fetched section name, or FALSE when
	 * the end was reached or when processing sections
	 * is disabled.
	 *
	 * @return string Next section's name
	 */
	function getNextSection() {
		if ($this->processSections) {
			if (key($this->table)) {
				$this->currentSection = key($this->table);
				next($this->table);
				return $this->currentSection;
			}
		}
		return FALSE;
	}

	/**
	 * Fetches the next section
	 *
	 * Returns FALSE when there is no next action or
	 * when processing sections is disabled.
	 *
	 * @return mixed
	 */
	function fetchNextSection() {
		return $this->getSection($this->getNextSection());
	}

	/**
	 * Checks if a given section exists
	 *
	 * @param string $section Section name
	 * @return bool
	 */
	function hasSection($section) {
		return ($this->processSections ? ($this->table[$section]) : FALSE);
	}

	/**
	 * Fetches a section by name
	 *
	 * @param string $section Section name
	 * @return mixed
	 */
	function getSection($section) {
		if (!empty($section)) {
			if ($this->processSections) {
				if (!$this->caseSensitive)
					$section = strtoupper($section);
				return (isset($this->table[$section]) ? $this->table[$section] : FALSE);
			}
		}
		return NULL;
	}

	/**
	 * Reads a value, given its key
	 *
	 * If $section is omitted, the current loaded section will
	 * be used. The $fallback value will be used when $key is not found.
	 *
	 * @param string $key Key
	 * @param mixed $fallback Fallback value
	 * @param string $section Optional section name
	 * @return mixed
	 */
	function getValue($key, $fallback=NULL, $section=NULL) {
		if (!$this->caseSensitive) {
			$key = strtoupper($key);
			$section = strtoupper((string)$section);
		}
		if ($this->processSections) {
			$section = (empty($section) ? $this->currentSection : $section);
			if (!empty($section))
				return (isset($this->table[$section][$key]) ? $this->table[$section][$key] : $fallback);
			return $fallback;
		} else {
			return (isset($this->table[$key]) ? $this->table[$key] : $fallback);
		}
	}

	/**
	 * Checks if the value of a key matches a given pattern
	 *
	 * @param string $key Key
	 * @param string $pattern Pattern
	 * @param string $section Optional section name
	 * @return bool
	 */
	function matchValue($key, $pattern, $section=NULL) {
		$tmp = $this->getValue($key, NULL, $section);
		if ($tmp !== NULL)
			return preg_match($pattern, $tmp);
		return FALSE;
	}

	/**
	 * Reads the value of a key as an array
	 *
	 * @param string $key Key
	 * @param string $separator Separator to be used
	 * @param mixed $fallback Fallback value
	 * @param string $section Optional section value
	 * @return mixed
	 */
	function getArray($key, $separator='|', $fallback=array(), $section=NULL) {
		$tmp = $this->getValue($key, $fallback, $section);
		if ($tmp != $fallback)
			return explode($separator, (string)$tmp);
		return $fallback;
	}

	/**
	 * Reads the value of a key as boolean
	 *
	 * @param string $key Key
	 * @param mixed $fallback Fallback value
	 * @param string $trueValue Representation of a true value, for comparison purposes
	 * @param string $section Optional section name
	 * @return bool
	 */
	function getBool($key, $fallback=FALSE, $trueValue='1', $section=NULL) {
		$tmp = $this->getValue($key, $fallback, $section);
		if ($tmp != $fallback)
			return ($tmp == $trueValue);
		return $fallback;
	}

	/**
	 * Adds a new section
	 *
	 * This section will be set as current section, so that
	 * further calls to {@link setValue} will add/change keys
	 * of this section.
	 *
	 * @param string $section Section name
	 * @param bool $overwrite Overwrite current entries, if existent
	 * @param array $entries Section entries
	 */
	function addSection($section, $overwrite=FALSE, $entries=array()) {
		if ($this->processSections) {
			if ($overwrite || !isset($this->table[$section]))
				$this->table[$section] = (array)$entries;
			$this->currentSection = $section;
		}
	}

	/**
	 * Adds or changes a key
	 *
	 * @param string $key Key
	 * @param string $value Key's value
	 * @param string $section Optional section name
	 */
	function setValue($key, $value, $section=NULL) {
		if (!$this->caseSensitive) {
			$key = strtoupper($key);
			$section = strtoupper($section);
		}
		if ($this->processSections) {
			$section = (empty($section) ? $this->currentSection : $section);
			if (!empty($section))
				$this->table[$section][$key] = (string)$value;
		} else {
			$this->table[$key] = (string)$value;
		}
	}

	/**
	 * Adds an array key
	 *
	 * The array will be imploded using the given $glue string.
	 *
	 * @param string $key Key
	 * @param array $value Key's value
	 * @param string $glue Array glue
	 * @param string $section Optional section name
	 */
	function setArray($key, $value, $glue='|', $section=NULL) {
		$value = implode($glue, $value);
		$this->setValue($key, $value, $section);
	}

	/**
	 * Adds a boolean key
	 *
	 * @param string $key Key
	 * @param bool $value Key's value
	 * @param string $section Optional section name
	 */
	function setBool($key, $value, $section=NULL) {
		$value = ($value ? "1" : "0");
		$this->setValue($key, $value, $section);
	}

	/**
	 * Adds a comment
	 *
	 * The comments are always added in the end of sections, or in
	 * the end of the file, when the processing of sections is disabled.
	 *
	 * @param string $comment Comment
	 * @param string $section Optional section name
	 */
	function addComment($comment, $section=NULL) {
		if (!$this->caseSensitive)
			$section = strtoupper((string)$section);
		if ($this->processSections) {
			$section = (empty($section) ? $this->currentSection : $section);
			if (!empty($section)) {
				$size = (isset($this->table[$section]) ? sizeof($this->table[$section]) : 0);
				$this->table[$section][";{$size}"] = $comment;
			}
		} else {
			$size = sizeof($this->table);
			$this->table[";{$size}"] = $comment;
		}
	}

	/**
	 * Renders and returns the file contents
	 *
	 * @param string $lineEnd Line end string
	 * @param string $indent Indentation string
	 * @return string
	 */
	function getContent($lineEnd="\n", $indent='') {
		$buffer = '';
		if ($this->processSections) {
			foreach ($this->table as $section => $keys) {
				if (!$this->caseSensitive)
					$section = strtoupper($section);
				if (is_array($keys)) {
					$buffer .= "[{$section}]" . $lineEnd;
					foreach ($keys as $key => $value) {
						if ($key[0] == ';') {
							$buffer .= $indent . '; ' . $value . $lineEnd;
						} else {
							if (!$this->caseSensitive)
								$key = strtoupper($key);
							$buffer .= $indent . "$key = \"$value\"" . $lineEnd;
						}
					}
					$buffer .=  $lineEnd;
				} else {
					$buffer .= $indent . "$section = \"$keys\"" . $lineEnd;
				}
			}
		} else {
			foreach ($this->table as $key => $value) {
				if ($key[0] == ';')
					$buffer .= $indent . '; ' . $value . $lineEnd;
				else
					$buffer .= $indent . "$key = \"$value\"" . $lineEnd;
			}
		}
		return $buffer;
	}

	/**
	 * Rebuilds the internal table based on the original file
	 */
	function reset() {
		$this->_loadFile(TRUE);
	}

	/**
	 * Renders and saves the file contents
	 *
	 * @param string $lineEnd Line end string
	 * @param string $indent Indentation string
	 * @return bool
	 */
	function save($lineEnd="\n", $indent='') {
		$fp = @fopen($this->filename, 'w');
		if ($fp !== FALSE) {
			flock($fp, LOCK_EX);
			fputs($fp, $this->getContent($lineEnd, $indent));
			flock($fp, LOCK_UN);
			fclose($fp);
			return TRUE;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $file), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
	}

	/**
	 * Used to create the file when it doesn't exist
	 *
	 * @access private
	 */
	function _createFile() {
		$fp = @fopen($this->filename, 'w');
		if ($fp === FALSE)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_CREATE_FILE', $this->filename), E_USER_ERROR, __FILE__, __LINE__);
		ftruncate($fp, 0);
		fclose($fp);
	}

	/**
	 * Used to load the contents of the file
	 *
	 * @uses parse_ini_file()
	 * @param unknown_type $force Force table rebuild
	 * @access private
	 */
	function _loadFile($force=FALSE) {
		if (!isset($this->table) || $force) {
			$tmp = @parse_ini_file($this->filename, $this->processSections);
			if ($tmp !== FALSE) {
				if ($this->processSections) {
					if (is_array($tmp)) {
						foreach ($tmp as $section => $values) {
							if (!$this->caseSensitive)
								$this->table[strtoupper($section)] = (is_array($values) ? array_change_key_case($values, CASE_UPPER) : $values);
							else
								$this->table[$section] = $values;
						}
					} else {
						$this->table = ($this->caseSensitive ? $tmp : array_change_key_case($tmp, CASE_UPPER));
					}
				} else {
					if (!$this->caseSensitive)
						$this->table = array_change_key_case($tmp, CASE_UPPER);
					else
						$this->table = $tmp;
				}
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_PROPERTIES_FILE', $this->filename), E_USER_ERROR, __FILE__, __LINE__);
			}
		}
	}
}
?>