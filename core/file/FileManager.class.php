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

import('php2go.file.FileSystem');

/**
 * Read mode
 */
define('FILE_MANAGER_READ', 'r');
/**
 * Binary read mode
 */
define('FILE_MANAGER_READ_BINARY', 'rb');
/**
 * Write mode
 */
define('FILE_MANAGER_WRITE', 'w');
/**
 * Binary write mode
 */
define('FILE_MANAGER_WRITE_BINARY',	'wb');
/**
 * Append mode
 */
define('FILE_MANAGER_APPEND', 'a');
/**
 * Binary append mode
 */
define('FILE_MANAGER_APPEND_BINARY', 'ab');
/**
 * Default block size when reading files
 */
define('FILE_MANAGER_DEFAULT_BLOCK', 512);

/**
 * File reader and writer
 *
 * Performs operations on files: create, read, write, append,
 * delete, change mode, change owner, get/set attributes.
 *
 * @package file
 * @uses FileSystem
 * @author Marcos Pont
 * @version $Revision$
 */
class FileManager extends PHP2Go
{
	/**
	 * Attributes of the current opened file
	 *
	 * @var array
	 */
	var $currentAttrs;

	/**
	 * Path to the current opened file
	 *
	 * @var string
	 */
	var $currentPath;

	/**
	 * Current file handle
	 *
	 * @var resource
	 * @access private
	 */
	var $currentFile;

	/**
	 * Current open mode (read, write, append, ...)
	 *
	 * @var string
	 * @access private
	 */
	var $currentMode;

	/**
	 * Whether to throw errors
	 *
	 * @var bool
	 */
	var $throwErrors = TRUE;

	/**
	 * Class constructor
	 *
	 * @return FileManager
	 */
	function FileManager() {
		parent::PHP2Go();
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 *
	 * Closes all opened file handles and release all file locks.
	 */
	function __destruct() {
		$this->closeAll();
		$locks = FileManager::getLocks();
		if (!empty($locks)) {
			foreach($locks as $pointer) {
				@flock($pointer, LOCK_UN);
			}
		}
	}

	/**
	 * Get all file handles
	 *
	 * @return array
	 */
	function &getPointers() {
		static $pointers;
		if (!isset($pointers)) {
			$pointers = array();
		}
		return $pointers;
	}

	/**
	 * Get all file locks
	 *
	 * @return array
	 */
	function &getLocks() {
		static $locks;
		if (!isset($locks)) {
			$locks = array();
		}
		return $locks;
	}

	/**
	 * Get the current opened file handle
	 *
	 * Returns FALSE if there's no opened file.
	 *
	 * @return resource|bool
	 */
	function getCurrentPointer() {
		return isset($this->currentFile) && is_resource($this->currentFile) ? $this->currentFile : FALSE;
	}

	/**
	 * Opens a file
	 *
	 * Examples:
	 * <code>
	 * $fm = new FileManager();
	 * /* opens data.xml using read mode {@*}
	 * $fm->open('data.xml', FILE_MANAGER_READ);
	 * /* opens messages.log using binary read mode and requires a shared lock {@*}
	 * $fm->open('messages.log', FILE_MANAGER_READ_BINARY, LOCK_SH);
	 * /* opens servers.txt using write mode and requires an exclusive lock {@*}
	 * $fm->open('servers.txt', FILE_MANAGER_WRITE, LOCK_EX);
	 * </code>
	 *
	 * @uses FileSystem::getFileAttributes()
	 * @param string $filePath File path
	 * @param string $mode Open mode
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 * @return bool
	 */
	function open($filePath, $mode=FILE_MANAGER_READ_BINARY, $lockType=FALSE) {
		$attrs = FileSystem::getFileAttributes($filePath);
		$dirAttrs = FileSystem::getFileAttributes(dirname($filePath));
		$pointers =& $this->getPointers();
		if (!isset($pointers[$filePath][$mode]) || !is_resource($pointers[$filePath][$mode])) {
			// validate read mode
			if (preg_match('/^rb?$/', $mode) && !preg_match('/^(http|https|ftp|php):\/\//i', $filePath) && !$attrs) {
				if ($this->throwErrors)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			// validate write/append mode
			if (preg_match('/^(w|a)b?$/', $mode)) {
				if (!FileSystem::exists($filePath)) {
					if (!$dirAttrs['isWriteable']) {
						if ($this->throwErrors)
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_CREATE_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
				} else {
					if (!$attrs['isWriteable']) {
						if ($this->throwErrors)
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
				}
			}
			// create the file handle
			$pointers[$filePath][$mode] = @fopen($filePath, $mode);
			if ($pointers[$filePath][$mode] === FALSE) {
				if ($this->throwErrors)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_OPEN_FILE', array($filePath, $mode)), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
		}
  		if ($lockType == LOCK_SH || $lockType == LOCK_EX) {
   			$this->lock($pointers[$filePath][$mode], $lockType);
		}
  		$this->currentFile =& $pointers[$filePath][$mode];
  		$this->currentPath = $filePath;
		$this->currentMode = $mode;
  		$this->currentAttrs =& $attrs;
  		return TRUE;
	}

	/**
	 * Alternate between 2 opened files
	 *
	 * The $filePath argument must be the
	 * same used to open the file.
	 *
	 * @param string $filePath File path
	 * @param string $mode Mode used to open the file
	 * @uses FileSystem::getFileAttributes()
	 * @return bool
	 */
	function changeFile($filePath, $mode) {
		$pointers =& $this->getPointers();
		if (!isset($pointers[$filePath][$mode]) || !is_resource($pointers[$filePath][$mode])) {
			return FALSE;
		} else {
			$this->currentFile =& $pointers[$filePath][$mode];
			$this->currentPath = $filePath;
			$this->currentMode = $mode;
			$this->currentAttrs = FileSystem::getFileAttributes($filePath);
			return TRUE;
		}
	}

	/**
	 * Get position of the current file pointer
	 *
	 * Returns FALSE if there's no opened file.
	 *
	 * @return int|bool
	 */
	function getCurrentPosition() {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			$fp =& $this->currentFile;
			return ftell($fp);
		}
	}

	/**
	 * Get an attribute of the opened file
	 *
	 * Returns FALSE when there's no opened file and
	 * when the attribute name is invalid.
	 *
	 * @param string $attributeName Attribute name
	 * @return mixed Attribute value
	 */
	function getAttribute($attributeName) {
		if (is_resource($this->currentFile))
			return (array_key_exists($attributeName, $this->currentAttrs) ? $this->currentAttrs[$attributeName] : FALSE);
		return FALSE;
	}

	/**
	 * Get all attributes of the opened file
	 *
	 * Returns FALSE when there's no opened file.
	 *
	 * @return array|bool
	 */
	function getAttributes() {
		if (is_resource($this->currentFile))
			return $this->currentAttrs;
		return FALSE;
	}

	/**
	 * Reads a block from the current file
	 *
	 * Allows to request shared lock on the file before reading.
	 * Returns FALSE when end of file is reached.
	 *
	 * @param int $size Block size
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 * @return string|bool
	 */
	function read($size=FILE_MANAGER_DEFAULT_BLOCK, $lockType=FALSE) {
		if (is_resource($this->currentFile)) {
			$fp =& $this->currentFile;
			if ($lockType == LOCK_SH || $lockType == LOCK_EX) {
				$this->lock($fp, $lockType);
			}
			return (feof($fp) ? FALSE : fread($fp, max(intval($size), 1)));
		}
		return FALSE;
	}

	/**
	 * Reads a char from the opened file
	 *
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 * @return string|bool
	 */
	function readChar($lockType=FALSE) {
		return $this->read(1, $lockType);
	}

	/**
	 * Reads a line from the current file
	 *
	 * Read bytes from the file until a line break is found
	 * or until end of file is reached.
	 *
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 * @return string|bool
	 */
	function readLine($lockType=FALSE) {
		if (is_resource($this->currentFile)) {
			$fp =& $this->currentFile;
			if ($lockType == LOCK_SH || $lockType == LOCK_EX)
				$this->lock($fp, $lockType);
			return (feof($fp) ? FALSE : fgets($fp));
		}
		return FALSE;
	}

	/**
	 * Read all contents of the current file
	 *
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 * @return string|bool
	 */
	function readFile($lockType=FALSE) {
		if (is_resource($this->currentFile))
			return $this->read($this->currentAttrs['size'], $lockType);
		return FALSE;
	}

	/**
	 * Get an array of the contents of a given file
	 *
	 * @param string $filePath File path
	 * @return array|bool
	 */
	function readArray($filePath) {
		if (file_exists($filePath))
			return file($filePath);
		if ($this->throwErrors)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
		return FALSE;
	}

	/**
	 * Write a string in the current file
	 *
	 * @param string $string Input string
	 * @param int $size Total bytes to write
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 * @return bool
	 */
	function write($string, $size=0, $lockType=FALSE) {
		if (is_resource($this->currentFile)) {
			$string = (string)$string;
			$fp =& $this->currentFile;
			if ($lockType == LOCK_SH || $lockType == LOCK_EX)
				$this->lock($fp, $lockType);
			return (@fwrite($fp, $string, max($size, strlen($string))));
		}
		return FALSE;
	}

	/**
	 * Write a char in the current file
	 *
	 * @param string $char Char to write
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 * @return bool
	 */
	function writeChar($char, $lockType=FALSE) {
		$char = (string)$char;
		if (strlen($char) > 0)
			return $this->write($char[0], 1, $lockType);
		return FALSE;
	}

	/**
	 * Write a line in the current file
	 *
	 * @param string $string Input string
	 * @param string $endLine Line end delimiter
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 * @return bool
	 */
	function writeLine($string, $endLine="\n", $lockType=FALSE) {
		$string = (string)$string;
		$string .= $endLine;
		return $this->write($string, strlen($string), $lockType);
	}

	/**
	 * Replace a search term in a given set of files
	 *
	 * @param string $search Value to search
	 * @param string $replace Replacement string
	 * @param array $files Files to search
	 */
	function replaceInFiles($search, $replace, $files) {
		$files = (array)$files;
		foreach ($files as $file) {
			if (file_exists($file) && is_readable($file)) {
				$content = file_get_contents($file);
				$result = preg_replace('/' . preg_quote($search, "/") . '/', '', $content);
				if (function_exists('file_put_contents')) {
					$result = @file_put_contents($file, $result);
					if ($result === FALSE)
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $file), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					$this->open($file, FILE_MANAGER_WRITE_BINARY);
					$this->write($result);
					$this->close();
				}
			}
		}
	}

	/**
	 * Rewind the current file's pointer to the start
	 *
	 * @return bool
	 */
	function rewind() {
		if (is_resource($this->currentFile)) {
			$fp =& $this->currentFile;
			return @rewind($fp);
		}
		return FALSE;
	}

	/**
	 * Seek a given position in the current file
	 *
	 * @param int $offset Offset
	 * @return bool
	 */
	function seek($offset) {
		if (is_resource($this->currentFile)) {
			$fp =& $this->currentFile;
			$result = @fseek($fp, $offset);
			return ($result === 0 ? TRUE : FALSE);
		}
		return FALSE;
	}

	/**
	 * Truncate the current file to a given size
	 *
	 * @param int $size New size
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 * @return bool
	 */
	function truncate($size, $lockType=FALSE) {
		if (is_resource($this->currentFile)) {
			$fp =& $this->currentFile;
			$truncSize = abs(intval($size));
			if ($lockType == LOCK_SH || $lockType == LOCK_EX)
				$this->lock($fp, $lockType);
			return @ftruncate($fp, $truncSize);
		}
		return FALSE;
	}

	/**
	 * Change the mode of the current file
	 *
	 * @param int $newMode New mode
	 * @return bool
	 */
	function changeMode($newMode) {
		if (isset($this->currentPath))
			return chmod($this->currentPath, $newMode);
		return FALSE;
	}

	/**
	 * Change the last modified time of the current file
	 *
	 * If $time is missing, current UNIX timestamp will be used.
	 *
	 * @uses FileSystem::touch()
	 * @param int $time New modified time
	 */
	function touch($time=NULL) {
		if (isset($this->currentPath)) {
			($time == NULL) && ($time = time());
			FileSystem::touch($this->currentPath, $time);
		}
	}

	/**
	 * Lock a given file
	 *
	 * @param resource &$filePointer File handle
	 * @param int $lockType Lock type (LOCK_SH or LOCK_EX)
	 */
	function lock(&$filePointer, $lockType) {
		$locks = FileManager::getLocks();
		if (@flock($filePointer, $lockType))
			$locks[] =& $filePointer;
	}

	/**
	 * Unlock a given file
	 *
	 * @param string $filePath File path
	 * @param int $mode Mode used to open the file
	 * @return bool
	 */
	function unlock($filePath, $mode) {
		$locks =& $this->getLocks();
		$pointers =& $this->getPointers();
		if (!isset($pointers[$filePath][$mode]) || !is_resource($pointers[$filePath][$mode])) {
			return FALSE;
		} else {
			$fp =& $pointers[$filePath][$mode];
			return @flock($fp, LOCK_UN);
		}
	}

	/**
	 * Close the current opened file
	 *
	 * @return bool
	 */
	function close() {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			// remove from internal set of handles
			$pointers =& $this->getPointers();
			unset($pointers[$this->currentPath][$this->currentMode]);
			// reset class properties
			$fp = $this->currentFile;
			unset($this->currentFile);
			unset($this->currentPath);
			unset($this->currentMode);
			unset($this->currentAttrs);
			return @fclose($fp);
		}
	}

	/**
	 * Close all opened files
	 */
	function closeAll() {
		$pointers = &$this->getPointers();
		if (!empty($pointers)) {
			foreach($pointers as $path => $value) {
				foreach($value as $mode => $pointer) {
					if (is_resource($pointer) && get_resource_type($pointer) != 'Unknown')
						@fclose($pointer);
				}
			}
		}
		$pointers = array();
	}
}
?>