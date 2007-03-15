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

import('php2go.file.FileManager');
import('php2go.file.DirectoryManager');
import('php2go.net.HttpResponse');

/**
 * Base class to handle with archives and file compression
 *
 * This class is the base of classes that read/write archives
 * and compressed files. It contains all common operations
 * shared by child classes.
 *
 * @package file
 * @uses FileManager
 * @uses DirectoryManager
 * @uses HttpResponse
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 * @abstract
 */
class FileCompress extends PHP2Go
{
	/**
	 * Default create mode
	 *
	 * @var int
	 */
	var $defaultMode	= 0644;

	/**
	 * Current working dir
	 *
	 * @var string
	 */
	var $currentDir		= './';

	/**
	 * Debug flag
	 *
	 * @var bool
	 */
	var $debug = FALSE;

	/**
	 * Whether to recurse into subdirectories
	 *
	 * @var bool
	 * @access private
	 */
	var $recurseDir		= TRUE;

	/**
	 * Whether to overwrite existing files when extracting archives
	 *
	 * @var bool
	 * @access private
	 */
	var $overwriteFile	= TRUE;

	/**
	 * Whether to register the full file paths when creating archives
	 *
	 * @var bool
	 * @access private
	 */
	var $storePaths		= TRUE;

	/**
	 * Class constructor
	 *
	 * @param string $cwd Working directory
	 * @return FileCompress
	 */
	function FileCompress($cwd='') {
		parent::PHP2Go();
		if ($this->isA('FileCompress', FALSE)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'FileCompress'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$this->currentDir = empty($cwd) ? getcwd() : $cwd;
			$this->currentDir = ereg("/\|/$", $this->currentDir) ? substr($this->currentDir, 0, -1) . '/' : $this->currentDir . '/';
		}
	}

	/**
	 * Get a singleton of one of the archiving/compressing classes
	 *
	 * @param string $type Class type: "tar", "gz" or "zip"
	 * @param string $cwd Working directory
	 * @return FileCompress
	 * @static
	 */
	function &getInstance($type, $cwd='') {
		static $instances;
		$type = strtolower(trim($type));
		$className = ucfirst($type) . 'File';
		if (!isset($instances)) {
			if (import('php2go.file.' . $className))
				$instances = array($type => new $className($cwd));
		} elseif (!isset($instances[$type])) {
			if (import('php2go.file.' . $className))
				$instances = array($type => new $className($cwd));
		}
		return $instances[$type];
	}

	/**
	 * Get a singleton of the {@link FileManager} class
	 *
	 * @return FileManager
	 * @static
	 */
	function &getFileManager() {
		static $Mgr;
		if (!isset($Mgr)) {
			$Mgr =& new FileManager();
			$Mgr->throwErrors = FALSE;
		}
		return $Mgr;
	}

	/**
	 * Set default create mode
	 *
	 * @param int $mode Create mode
	 */
	function setDefaultMode($mode) {
		$this->defaultMode = $mode;
	}

	/**
	 * Enable/disable directory recursion
	 *
	 * @param bool $recurse Enable/disable
	 */
	function setDirectoryRecursion($recurse) {
		$this->recurseDir = TypeUtils::toBoolean($recurse);
	}

	/**
	 * Check if directory recursion is enabled
	 *
	 * @return bool
	 */
	function isRecursionEnabled() {
		return $this->recurseDir;
	}

	/**
	 * Enable/disable overwrite of existing files when extracting archives
	 *
	 * @param bool $overwrite Enable/disable
	 */
	function setFileOverwrite($overwrite) {
		$this->overwriteFile = TypeUtils::toBoolean($overwrite);
	}

	/**
	 * Check if overwrite is enabled
	 *
	 * @return bool
	 */
	function isOverwriteEnabled() {
		return $this->overwriteFile;
	}

	/**
	 * Enable/disable storage of full paths when creating archives
	 *
	 * @param bool $store Enable/disable
	 */
	function setPathStorage($store) {
		$this->storePaths = TypeUtils::toBoolean($store);
	}

	/**
	 * Check if path storage is enabled
	 *
	 * @return bool
	 */
	function isPathStorageEnabled() {
		return $this->storePaths;
	}

	/**
	 * Adds a list of directories (and its contents)
	 *
	 * @param array $dirList List of directories
	 * @return bool
	 */
	function addDirectories($dirList) {
		if ($this->isA('GzFile') || !($dirList)) {
			return FALSE;
		}
		$fileList = array();
		$actualDir = getcwd();
		@chdir($this->currentDir);
		foreach($dirList as $file) {
			if (@is_dir($file)) {
				if ($dirFiles = $this->_parseDirectory($file)) {
					$fileList = array_merge($fileList, $dirFiles);
				}
			} elseif (file_exists($file)) {
				$fileList[] = $file;
			}
		}
		@chdir($actualDir);
		$this->addFiles($fileList);
		return TRUE;
	}

	/**
	 * Adds a list of files
	 *
	 * @param array $fileList List of file names
	 * @return bool
	 */
	function addFiles($fileList) {
		if ($this->isA('GzFile') || !is_array($fileList)) {
			return FALSE;
		}
		$actualDir = getcwd();
		@chdir($this->currentDir);
		foreach ($fileList as $currentFile) {
			$this->addFile($currentFile);
		}
		@chdir($actualDir);
		return TRUE;
	}

	/**
	 * Must be implemented in the child classes
	 *
	 * @param string $filePath File path
	 * @abstract
	 */
	function addFile($filePath) {
	}

	/**
	 * Must be implemented in the child classes
	 *
	 * @param string $data Data
	 * @param string $fileName File name
	 * @param array $attrs File attributes
	 * @abstract
	 */
	function addData($data, $fileName, $attrs) {
	}

	/**
	 * Opens and extracts a file
	 *
	 * Returns an array of extract files or FALSE in case of error.
	 *
	 * @see TarFile::extractData()
	 * @see GzFile::extractData()
	 * @see ZipFile::extractData()
	 * @param string $fileName File path
	 * @return array|bool
	 */
	function extractFile($fileName) {
		$Mgr =& FileCompress::getFileManager();
		if (!$Mgr->open($fileName, FILE_MANAGER_READ_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$result = $this->extractData($Mgr->readFile());
			$Mgr->close();
			return $result;
		}
		return FALSE;
	}

	/**
	 * Must be implemented by child classes
	 *
	 * @return string
	 * @abstract
	 */
	function getData() {
	}

	/**
	 * Must be implemented by child classes
	 *
	 * @param string $content Content to extract
	 * @access protected
	 * @return string
	 * @abstract
	 */
	function extractData($content) {
	}

	/**
	 * Save the generated archive
	 *
	 * @param string $fileName Save path
	 * @param int $mode Save mode
	 * @return bool
	 */
	function saveFile($fileName, $mode=NULL) {
		$Mgr =& FileCompress::getFileManager();
		if (!$this->isOverwriteEnabled() && $Mgr->exists($fileName))
			return FALSE;
		elseif ($Mgr->exists($fileName))
			@unlink($fileName);
		if (!$Mgr->open($fileName, FILE_MANAGER_WRITE_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$Mgr->write($this->getData());
			$Mgr->changeMode(($mode == NULL ? $this->defaultMode : $mode));
			$Mgr->close();
			return TRUE;
		}
	}

	/**
	 * Save files extracted from an archive
	 *
	 * Inexistent directories will be automatically created.
	 * <code>
	 * $zip =& FileCompress::getInstance('zip');
	 * $zip->saveExtractedFiles($zip->extractFile('test.zip'), 0777, 'tmp/');
	 * </code>
	 *
	 * @param array $files List of files
	 * @param int $createMode Create mode
	 * @param string $target Target directory
	 * @return array|bool List of saved files or FALSE in case of error
	 */
	function saveExtractedFiles($files, $createMode, $target=NULL) {
		$cwd = getcwd();
		$fileSet = array();
		$lastDir = NULL;
		$Mgr =& FileCompress::getFileManager();
		if (!is_array($files))
			return FALSE;
		// change to the target directory, if provided
		if (!TypeUtils::isNull($target) && $Mgr->exists($target))
			chdir($target);
		// process extracted files
		foreach ($files as $file) {
			$path = (isset($file['path']) ? $file['path'] . $file['filename'] : $file['filename']);
			// split path and file name
			if (strpos($path, '/') !== FALSE) {
				$name = substr($path, strrpos($path, '/')+1);
				$path = substr($path, 0, strrpos($path, '/'));
				if ($path != $lastDir) {
					if (file_exists($path) || FileSystem::createPath($path)) {
						if ($name != '' && (isset($file['type']) && $file['type'] == 5)) {
							@touch($path, (isset($file['time']) ? $file['time'] : time()));
							@chmod($path, (isset($file['mode']) ? $file['mode'] : $createMode));
						}
						$lastDir = $path;
					} else {
						chdir($cwd);
						return FALSE;
					}
				}
				chdir($lastDir);
			} else {
				$name = $path;
			}
			// save file using original name and mode
			if ($name != '' && (!isset($file['type']) || $file['type'] != 5)) {
				// add to the list of extracted files
				$fileSet[] = (!TypeUtils::isNull($target) && $Mgr->exists($target) ? $target : '') . TypeUtils::parseString($lastDir) . $name;
				// create/replace the file
				if (!$Mgr->open($name, FILE_MANAGER_WRITE_BINARY)) {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $path), E_USER_ERROR, __FILE__, __LINE__);
					chdir($cwd);
					return FALSE;
				} else {
					$Mgr->write($file['data']);
					$Mgr->changeMode(isset($file['mode']) ? $file['mode'] : $createMode);
					if (isset($file['time']))
						$Mgr->touch($file['time']);
					$Mgr->close();
				}
			}
			// return to the original directory
			chdir($cwd);
			if (!TypeUtils::isNull($target) && $Mgr->exists($target))
				chdir($target);
		}
		chdir($cwd);
		return $fileSet;
	}

	/**
	 * Download the generated archive or compressed file
	 *
	 * @param string $fileName File name
	 */
	function downloadFile($fileName) {
		if (!headers_sent()) {
			HttpResponse::download($fileName, strlen($this->getData()));
			print $this->getData();
		}
	}


	/**
	 * Print debug information
	 *
	 * @param string $str Debug message
	 * @access protected
	 */
	function debug($str) {
		$type = strtoupper($this->getObjectName());
		if ($this->debug) {
			print $type . ' DEBUG : ' . $str . '<br>';
			flush();
		}
	}

	/**
	 * Recursive method used to collect all file names
	 * starting from a given directory path
	 *
	 * @param string $directory Directory path
	 * @access private
	 * @return array
	 */
	function _parseDirectory($directory) {
		$fileList = array();
		$_DirectoryManager = new DirectoryManager();
		if (!$_DirectoryManager->open($this->currentDir . $directory)) {
			return $fileList;
		} else {
			while ($entry = $_DirectoryManager->read()) {
				if ($this->isRecursionEnabled() && $entry->isDirectory()) {
					$dirFiles = $this->_parseDirectory($directory . '/' . $entry->getName());
					$fileList = array_merge($fileList, $dirFiles);
				} else {
					$fileList[] = $directory . '/' . $entry->getName();
				}
			}
			$_DirectoryManager->close();
		}
		return $fileList;
	}
}
?>