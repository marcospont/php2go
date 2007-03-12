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
 * Loader for class and library files
 *
 * PHP2Go uses the "dot path" syntax to identify a class or library.
 * The first element of the path must point to a include path key,
 * (php2go, the framework default, or a user-defined include path
 * key, defined in the configuration entry INCLUDE_PATH). The
 * following path elements are traduced into a relative path in
 * the file system.
 *
 * In the path 'php2go.base.Document':
 * # php2go is the include path key (internally points to PHP2GO_ROOT . "core/")
 * # base.Document is the relative path starting from the include path key root
 * # when the file extension is missing, .class.php is used
 * # the resolved path would be PHP2GO_ROOT . "core/base/Document.class.php"
 *
 * ClassLoader has the responsability of traducing paths in the "dot"
 * pattern to real paths pointing to folders, classes, libraries
 * or helper files.
 *
 * Each processed path is stored in an internal cache, so that it won't
 * be processed twice.
 *
 * Under PHP4, files and classes are included in the exact moment they're requested.
 * Under PHP5, files are included right away and classes are included on demand,
 * through an __autoload interceptor.
 *
 * @package php2go
 * @uses Conf
 * @uses LanguageBase
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ClassLoader
{
	/**
	 * Holds dot paths already processed
	 *
	 * @var array
	 * @access private
	 */
	var $importCache = array();

	/**
	 * Holds class dot paths already processed
	 *
	 * @var array
	 * @access private
	 */
	var $importClassCache = array();

	/**
	 * Class constructor
	 *
	 * @return ClassLoader
	 */
	function ClassLoader() {
	}

	/**
	 * Get the singleton of the ClassLoader class
	 *
	 * @return ClassLoader
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new ClassLoader();
		return $instance;
	}

	/**
	 * Import the file(s) pointed by the given path
	 *
	 * Translates a dot path to a path in the file system pointing
	 * to a single file or a set of files, when the wildcard * is
	 * present.
	 *
	 * @param string $path Dot path
	 * @param string $extension File(s) extension
	 * @param bool $isClass Indicates that a class or classes are being imported
	 * @return bool Operation result
	 * @see import()
	 */
	function importPath($path, $extension='class.php', $isClass=TRUE) {
		if (isset($this->importCache[$path]))
			return TRUE;
		$Lang =& LanguageBase::getInstance();
		if ($translated = $this->_translateDotPath($path)) {
			$this->_registerIncludePath($translated['path']);
			$result = ($isClass && IS_PHP5 ? TRUE : ($translated['file'] == '*' ? $this->loadDirectory($translated['path']) : $this->loadFile($translated['file'] . '.' . $extension)));
			if ($result) {
				$this->importCache[$path] = TRUE;
				if ($translated['file'] != '*')
					$this->importClassCache[$translated['file']] = $translated['file'] . '.' . $extension;
				return TRUE;
			}
		}
		trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_MODULE'), $path), E_USER_ERROR);
		return FALSE;
	}

	/**
	 * Include a file given its path in the file system
	 *
	 * @param string $filePath File path
	 * @return bool Operation result
	 */
	function loadFile($filePath) {
		return ($this->_isReadable($filePath) ? (include_once($filePath)) : FALSE);
	}

	/**
	 * Include all files inside a given directory
	 *
	 * Operation will stop when the first error is found.
	 *
	 * @param string $directoryPath Directory path
	 * @return bool Operation result
	 */
	function loadDirectory($directoryPath) {
		$Lang =& LanguageBase::getInstance();
		$directoryPath = rtrim($directoryPath, "\\/");
		$handle = @dir($directoryPath);
		if ($handle) {
			while ($file = $handle->read()) {
				if ($file == '.' || $file == '..' || is_dir($directoryPath . PHP2GO_DIRECTORY_SEPARATOR . $file))
					continue;
				if (!include_once($directoryPath . PHP2GO_DIRECTORY_SEPARATOR . $file)) {
					trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_DIR_MODULE'), $file, $directoryPath), E_USER_ERROR);
					return FALSE;
				}
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Translate a dot path into a file system path
	 *
	 * Determines the real file system path of the file or files. The
	 * first path element must match one of the defined include path
	 * keys. The other elements are translated into a relative file
	 * system path.
	 *
	 * @param string $dotPath Path
	 * @return bool Operation result
	 * @access private
	 */
	function _translateDotPath($dotPath) {
		$matches = array();
		if (preg_match("/^([^\.]+)\.(.*\.)?([^\.]+)/i", $dotPath, $matches)) {
			if ($matches[1] == PHP2GO_INCLUDE_KEY) {
				$basePath = PHP2GO_ROOT . 'core/';
			} else {
				$Conf =& Conf::getInstance();
				$includePaths = $Conf->getConfig('INCLUDE_PATH');
				if (array_key_exists($matches[1], (array)$includePaths)) {
					$basePath = $includePaths[$matches[1]];
				} else {
					return FALSE;
				}
			}
			$basePath .= ($matches[2] ? strtr($matches[2], '.', PHP2GO_DIRECTORY_SEPARATOR) : '');
			return array(
				'path' => $basePath,
				'file' => $matches[3]
			);
		} elseif ($dotPath != '*') {
			return array(
				'path' => getcwd(),
				'file' => $dotPath
			);
		} else {
			return FALSE;
		}
	}

	/**
	 * Check if a given file path is readable
	 *
	 * The method also tries to find the file using include_path.
	 *
	 * @param string $filePath File path
	 * @access private
	 * @return bool
	 */
	function _isReadable($filePath) {
		$handle = @fopen($filePath, 'r', TRUE);
		if (is_resource($handle)) {
			fclose($handle);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Register a new entry in PHP's include_path directive
	 *
	 * @param string $includePath New include path
	 * @access private
	 */
	function _registerIncludePath($includePath) {
		$ps = PATH_SEPARATOR;
		$includePath = strtr($includePath, '\\', '/');
		$curIncPath = ini_get('include_path');
		if (!preg_match("~{$includePath}(?:{$ps}|$)~", $curIncPath))
			ini_set('include_path', $curIncPath . $ps . $includePath);
	}
}
?>