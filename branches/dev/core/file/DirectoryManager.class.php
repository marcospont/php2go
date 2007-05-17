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
import('php2go.file.DirectoryEntry');

/**
 * Manipulates file system folders
 *
 * This class is able to read the contents of folders in the file system.
 * It can handle multiple folders at the same time.
 *
 * @package file
 * @uses FileSystem
 * @uses Number
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DirectoryManager extends PHP2Go
{
	/**
	 * Current opened directory handle
	 *
	 * @var resource
	 * @access private
	 */
	var $currentHandle = NULL;

	/**
	 * Current opened path
	 *
	 * @var string
	 */
	var $currentPath;

	/**
	 * Attributes of current opened directory
	 *
	 * @var array
	 */
	var $currentAttrs;

	/**
	 * Whether to throw errors when directories can't be opened
	 *
	 * @var bool
	 */
	var $throwErrors = TRUE;

	/**
	 * Class constructor
	 *
	 * @return DirectoryManager
	 */
	function DirectoryManager() {
		parent::PHP2Go();
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 *
	 * Closes all opened directory handles.
	 */
	function __destruct() {
		$handles = $this->getHandles();
		if (!empty($handles)) {
			foreach($handles as $path => $handle) {
				if (is_resource($handle))
					@closedir($handle);
			}
		}
	}

	/**
	 * Get all directory handles
	 *
	 * @return array
	 */
	function &getHandles() {
		static $handles;
		if (!isset($handles)) {
			$handles = array();
		}
		return $handles;
	}

	/**
	 * Get current opened directory handle
	 *
	 * Returns FALSE if there's not a valid opened handle.
	 *
	 * @return resource|bool
	 */
	function getCurrentHandle() {
		return isset($this->currentHandle) && is_resource($this->currentHandle) ? $this->currentHandle : FALSE;
	}

	/**
	 * Create a {@link DirectoryManager} instance pointing to
	 * the parent directory of the opened directory
	 *
	 * @return DirectoryManager
	 */
	function &getParentDirectory() {
		$result = NULL;
		$parent = $this->getParentPath();
		if (!empty($parent)) {
			$Mgr = new DirectoryManager();
			if ($Mgr->open($this->getParentPath()))
				$result =& $Mgr;
		}
		return $result;
	}

	/**
	 * Get current opened directory's parent path
	 *
	 * @uses FileSystem::getStandardPath()
	 * @return string
	 */
	function getParentPath() {
		if (is_resource($this->currentHandle)) {
			$fullPath = substr($this->currentAttrs['path'], 0, -1);
			$parentPath = FileSystem::getStandardPath(dirname($fullPath));
			if (substr($parentPath, -1) != '/')
				$parentPath .= '/';
			return $parentPath;
		}
		return '';
	}

	/**
	 * Opens a directory
	 *
	 * @param string $directoryPath Directory path
	 * @uses FileSystem::getFileAttributes()
	 * @return bool
	 */
	function open($directoryPath) {
		$attrs = FileSystem::getFileAttributes($directoryPath);
		if (!$attrs) {
			if ($this->throwErrors)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_DIR', $directoryPath), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// fix directory path
		$directoryPath = $attrs['path'];
		// check if the directory is already opened
		$handles =& $this->getHandles();
		if (!isset($handles[$directoryPath]) || !is_resource($handles[$directoryPath])) {
			$handle = @opendir($directoryPath);
			if ($handle === FALSE) {
				if ($this->throwErrors)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_OPEN_DIR', $directoryPath), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			$handles[$directoryPath] =& $handle;
		}
		$this->currentHandle =& $handles[$directoryPath];
		$this->currentPath = $directoryPath;
		$this->currentAttrs =& $attrs;
		return TRUE;
	}

	/**
	 * Check if there's an opened directory
	 *
	 * @return bool
	 */
	function isOpen() {
		return (is_resource($this->currentHandle));
	}

	/**
	 * Alternate between 2 opened directories
	 *
	 * The $directoryPath argument must be the
	 * same path used to open the directory.
	 *
	 * @param string $directoryPath Directory path
	 * @uses FileSystem::getFileAttributes()
	 * @return bool
	 */
	function changeDirectory($directoryPath) {
		$handles =& $this->getHandles();
		if (is_resource($handles[$directoryPath])) {
			$this->currentHandle =& $handles[$directoryPath];
			$this->currentPath = $directoryPath;
			$this->currentAttrs = FileSystem::getFileAttributes($directoryPath);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Read the next entry from the opened directory
	 *
	 * Example:
	 * <code>
	 * $dir = new DirectoryManager();
	 * $dir->open("photo/");
	 * /* get JPEG images only {@*}
	 * while ($entry = $dir->read("\.jpg")) {
	 *   print $entry->getFullName() . '<br />';
	 * }
	 * $dir->close();
	 * </code>
	 *
	 * Returns FALSE after the last entry was read.
	 *
	 * @param string $includeRegExp Include regular expression
	 * @return DirectoryEntry|bool
	 */
	function read($includeRegExp='') {
		if (is_resource($this->currentHandle)) {
			if ($entry = @readdir($this->currentHandle)) {
				// ignore "." and ".." entries
				if (preg_match("/^\.{1,2}/", $entry)) {
					return $this->read($includeRegExp);
				} elseif (!empty($includeRegExp)) {
					if (preg_match('/' . $includeRegExp . '/', $entry))
						return new DirectoryEntry($this->currentPath, $entry);
					else
						return $this->read($includeRegExp);
				} else {
					return new DirectoryEntry($this->currentPath, $entry);
				}
			}
		}
		return FALSE;
	}

	/**
	 * Get a list of all regular files included in the
	 * current opened directory
	 *
	 * Returns FALSE when there's no opened directory.
	 *
	 * @uses DirectoryManager::_readSimple()
	 * @param string $includeRegExp Include regular expression
	 * @param bool $sort Whether result must be sorted alphabetically
	 * @return array|bool
	 */
	function getFileNames($includeRegExp='', $sort=TRUE) {
		if (is_resource($this->currentHandle)) {
			$files = array();
			$this->rewind();
			while ($entry = $this->_readSimple($includeRegExp)) {
				if (@is_file($this->currentPath . $entry))
					$files[] = $entry;
			}
			if ($sort)
				sort($files, SORT_STRING);
			return $files;
		}
		return FALSE;
	}

	/**
	 * Get an array of {@link DirectoryEntry} instances, containing
	 * all regular files included in the current opened directory
	 *
	 * Returns FALSE when there's no opened directory.
	 *
	 * @param string $includeRegExp Include regular expression
	 * @param bool $sort Whether results should be sorted
	 * @return array
	 */
	function getFiles($includeRegExp='', $sort=TRUE) {
		if (is_resource($this->currentHandle)) {
			$files = array();
			$this->rewind();
			while ($entry = $this->read($includeRegExp)) {
				if ($entry->isFile())
					$files[] = $entry;
			}
			if ($sort)
				usort($files, array($this, '_sortDirectoryEntries'));
			return $files;
		}
		return FALSE;
	}

	/**
	 * Get an array of {@link DirectoryEntry} instances, containing
	 * all directories included in the current opened directory
	 *
	 * Returns FALSE if there's no opened directory.
	 *
	 * @return array
	 */
	function getDirectories() {
		if (is_resource($this->currentHandle)) {
			$directories = array();
			$this->rewind();
			while ($entry = $this->read()) {
				if ($entry->isDirectory()) {
					$directories[] = $entry;
				}
			}
			sort($directories);
			return $directories;
		}
		return FALSE;
	}

	/**
	 * Calculates the total size of the directory
	 *
	 * @uses Number::formatByteAmount()
	 * @param string $mode Display mode: K, M, G or T
	 * @param int $precision Precision
	 * @param bool $deep Recurse into subdirectories
	 * @return string
	 */
	function getSize($mode='', $precision=2, $deep=FALSE) {
		$size = $this->_getTotalSize($deep);
		return Number::formatByteAmount($size, $mode, $precision);
	}

	/**
	 * Builds a tree of files and subdirectories using the
	 * current opened directory as root node
	 *
	 * Returns a {@link DirectoryEntry} instance, containing all
	 * files and subdirectories as <b>child nodes</b>.
	 *
	 * @uses DirectoryManager::_getChildren()
	 * @return DirectoryEntry
	 */
	function &getContentTree() {
		$result = NULL;
		if (is_resource($this->currentHandle)) {
			$result = new DirectoryEntry($this->getParentPath(), $this->currentAttrs['lastDir']);
			$this->rewind();
			$this->_getChildren($result);
		}
		return $result;
	}

	/**
	 * Rewinds the internal cursor to the first position
	 *
	 * @return bool
	 */
	function rewind() {
		return @rewinddir($this->currentHandle);
	}

	/**
	 * Closes the current opened directory
	 *
	 * @return bool
	 */
	function close() {
		if (is_resource($this->currentHandle)) {
			// remove from the internal set of handles
			$handles = &$this->getHandles();
			unset($handles[$this->currentPath]);
			// reset class properties
			$handle = $this->currentHandle;
			unset($this->currentHandle);
			unset($this->currentPath);
			unset($this->currentAttrs);
			return @closedir($handle);
		}
		return FALSE;
	}

	/**
	 * Use to read all contents of a file system tree, capturing file
	 * and directory names only
	 *
	 * @param string $includeRegExp Include regular expression
	 * @access private
	 * @return array
	 */
	function _readSimple($includeRegExp='') {
		if (is_resource($this->currentHandle)) {
			if ($entry = @readdir($this->currentHandle)) {
				// ignore "." and ".." entries
				if (ereg("^\.{1,2}", $entry)) {
					return $this->_readSimple($includeRegExp);
				} elseif (!empty($includeRegExp)) {
					if (preg_match('/' . $includeRegExp . '/', $entry))
						return $entry;
					else
						return $this->_readSimple($includeRegExp);
				} else {
					return $entry;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Recursive method to collect child nodes of a given directory entry
	 *
	 * @param DirectoryEntry $node Directory entry
	 * @access private
	 */
	function _getChildren(&$node) {
		$oldPath = $node->getFullName();
		while ($entry = $this->read()) {
			$child =& $node->addChild($entry);
			if ($child->isDirectory()) {
				$this->open($child->getFullName());
				$this->_getChildren($child);
				$this->close();
				$this->changeDirectory($oldPath);
			}
		}
	}

	/**
	 * Counts the total size of a directory in bytes
	 *
	 * @param bool $deep Recurse into subdirectories
	 * @access private
	 * @return int
	 */
	function _getTotalSize($deep=FALSE) {
		$size = 0;
		if (is_resource($this->currentHandle)) {
			$this->rewind();
			$oldPath = $this->currentPath;
			while ($entry = $this->read()) {
				$size += $entry->getSize();
				if ($deep && $entry->isDirectory()) {
					$this->open($entry->getFullName());
					$size += $this->_getTotalSize($deep);
					$this->close();
					$this->changeDirectory($oldPath);
				}
			}
		}
		return $size;
	}

	/**
	 * Used to sort directory entries
	 *
	 * @param DirectoryEntry $a First entry
	 * @param DirectoryEntry $b Second entry
	 * @access private
	 * @return int
	 */
	function _sortDirectoryEntries($a, $b) {
		$an = $a->getName();
		$bn = $b->getName();
	 	if ($an == $bn)
			return 0;
		return ($an < $bn) ? -1 : 1;
	}
}
?>