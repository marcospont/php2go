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

import('php2go.base.AbstractNode');

/**
 * Represents an entry of a file system directory
 *
 * This class is used by {@link DirectoryManager} to represent entries
 * read from file system directories.
 *
 * @package file
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DirectoryEntry extends AbstractNode
{
	/**
	 * Parent folder's path
	 *
	 * @var string
	 */
	var $path;

	/**
	 * Class constructor
	 *
	 * @param string $path Parent folder's path
	 * @param string $entryName Entry name
	 * @return DirectoryEntry
	 */
	function DirectoryEntry($path, $entryName) {
		parent::AbstractNode($entryName, array(), NULL);
		$this->path = $path;
	}

	/**
	 * Get parent folder's path
	 *
	 * @return string
	 */
	function getPath() {
		return $this->path;
	}

	/**
	 * Get full entry path
	 *
	 * @return string
	 */
	function getFullName() {
		if ($this->isDirectory())
			return $this->path . $this->name . '/';
		else
			return $this->path . $this->name;
	}

	/**
	 * Get entry size in bytes
	 *
	 * @return int
	 */
	function getSize() {
		if (empty($this->attrs))
			$this->_getAttributes();
		return parent::getAttribute('size');
	}

	/**
	 * Check if the entry is a regular file
	 *
	 * @return bool
	 */
	function isFile() {
		if (empty($this->attrs))
			$this->_getAttributes();
		return parent::getAttribute('isFile');
	}

	/**
	 * Check if the entry is a folder
	 *
	 * @return bool
	 */
	function isDirectory() {
		if (empty($this->attrs))
			$this->_getAttributes();
		return parent::getAttribute('isDir');
	}

	/**
	 * Implements the data set row interface
	 *
	 * @return array
	 */
	function toArray() {
		if (empty($this->attrs))
			$this->_getAttributes();
		return $this->attrs;
	}

	/**
	 * Determine entry's attributes
	 *
	 * @access private
	 */
	function _getAttributes() {
		$this->attrs = FileSystem::getFileAttributes($this->path . $this->getName());
	}
}
?>