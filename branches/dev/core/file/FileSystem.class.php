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

import('php2go.util.Number');

/**
 * Performs operations on the file system
 *
 * This class contains a collection of static methods to obtain
 * information about files and directories, and to perform operations
 * on these files.
 *
 * @package file
 * @uses Number
 * @uses System
 * @author Marcos Pont <mpont@users.sourceforge.net
 * @version $Revision$
 */
class FileSystem extends PHP2Go
{
	/**
	 * Check if a given file path exists
	 *
	 * @param string $path File path
	 * @return bool
	 * @static
	 */
	function exists($path) {
		return (@file_exists($path));
	}

	/**
	 * Read contents of a file
	 *
	 * Returns FALSE in case of errors.
	 *
	 * @param string $path File path
	 * @return string|bool
	 * @static
	 */
	function getContents($path) {
		$contents = @file_get_contents($path);
		if ($contents === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $path), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		return $contents;
	}

	/**
	 * Get last modified date of a given file path
	 *
	 * If $noCache is set to TRUE, the method will call
	 * {@link clearstatcache()}. If the file doesn't exist,
	 * the method will return FALSE.
	 *
	 * @param string $path File path
	 * @param bool $noCache Whether to clear cache first
	 * @return int|bool
	 * @static
	 */
	function lastModified($path, $noCache=TRUE) {
		if ($noCache)
			clearstatcache();
		return (FileSystem::exists($path) ? @filemtime($path) : FALSE);
	}

	/**
	 * Get attributes of a given file path
	 *
	 * Returns an array containing the following properties:
	 * # name : file name
	 * # extension : file extension (when applicable)
	 * # path : absolute path
	 * # type : file type ('fifo', 'char', 'dir', 'block', 'file' or 'unknown')
	 * # mode : octal file permissions
	 * # perms : string file permissions
	 * # size : file size in bytes
	 * # aTime : last access timestamp
	 * # mTime : last modification timestamp
	 * # cTime : creation timestamp
	 * # userId : owner ID
	 * # groupId : group ID
	 * # isFile : whether this is a regular file
	 * # isDir : whether this is a directory
	 * # isLink : whether this is a link
	 * # isReadable : is the file readable?
	 * # isWriteable : is the file writeable?
	 * # isExecutable : is the file executable?
	 * # linkTarget : link target
	 *
	 * @param string $path File path
	 * @return array|bool
	 * @static
	 */
	function getFileAttributes($path) {
		if (!file_exists($path))
			return FALSE;
		$pathInfo = FileSystem::getPathInfo($path);
		$fileAttr = array();
		$fileAttr['name']			= '';
		$fileAttr['extension']		= '';
		$fileAttr['path']			= $pathInfo['fullPath'];
		$fileAttr['lastDir']		= $pathInfo['lastDir'];
		$fileAttr['type']			= filetype($path);
		$fileAttr['mode']			= fileperms($path);
		$fileAttr['perms']			= FileSystem::getFilePermissions($path);
		$fileAttr['size']			= filesize($path);
		$fileAttr['aTime']			= fileatime($path);
		$fileAttr['mTime']			= filemtime($path);
		$fileAttr['cTime']			= filectime($path);
		$fileAttr['userId']			= fileowner($path);
		$fileAttr['groupId']		= filegroup($path);
		$fileAttr['isFile']			= is_file($path);
		$fileAttr['isDir']			= is_dir($path);
		$fileAttr['isLink']			= (is_link($path) || (System::isWindows() && substr($path, -4) == '.lnk'));
		$fileAttr['isReadable']		= is_readable($path);
		$fileAttr['isWriteable']	= is_writeable($path);
		$fileAttr['isExecutable']	= (!System::isWindows() && is_executable($path));
		if ($fileAttr['isFile']) {
			$fileAttr['name']		= $pathInfo['file'];
			$fileAttr['extension']	= FileSystem::getFileExtension($path);
		}
		if ($fileAttr['isLink'])
			$fileAttr['linkTarget'] = readlink($path);
		else
			$fileAttr['linkTarget'] = '';
		clearstatcache();
		return $fileAttr;
	}

	/**
	 * Extract the file name from a given file path
	 *
	 * @param string $path File path
	 * @return string
	 * @static
	 */
	function getFileName($path) {
		return basename($path);
	}

	/**
	 * Extract the extension from a given file path
	 *
	 * @param string $path File path
	 * @return string
	 * @static
	 */
	function getFileExtension($path) {
		$matches = array();
		$fileName = basename($path);
		if (preg_match("~\.([^\.]+)$~", $fileName, $matches)) {
			$extension = $matches[1];
			if ($extension == 'lnk' && System::isWindows())
				return FileSystem::getFileExtension(substr($fileName, 0, -4));
			return $extension;
		}
		return '';
	}

	/**
	 * Extract the permissions from a given file path
	 *
	 * The permissions are returned in string format. Example: "rwxr--r--".
	 * Returns FALSE when the path doesn't exist.
	 *
	 * @param string $path File path
	 * @return string|bool
	 * @static
	 */
	function getFilePermissions($path) {
		if (!file_exists($path))
			return FALSE;
		$mode = fileperms($path);
		$fperms["uread"] = ($mode & 00400) ? 'r' : '-';
		$fperms["uwrite"] = ($mode & 00200) ? 'w' : '-';
		$fperms["uexecute"] = ($mode & 00100) ? 'x' : '-';
		$fperms["gread"] = ($mode & 00040) ? 'r' : '-';
		$fperms["gwrite"] = ($mode & 00020) ? 'w' : '-';
		$fperms["gexecute"] = ($mode & 00010) ? 'x' : '-';
		$fperms["aread"] = ($mode & 00004) ? 'r' : '-';
		$fperms["awrite"] = ($mode & 00002) ? 'w' : '-';
		$fperms["aexecute"] = ($mode & 00001) ? 'x' : '-';
		return implode("", $fperms);
	}

	/**
	 * Touch a given file path
	 *
	 * If the file exists, the last modification date is changed. Otherwise,
	 * the file is created. If $time is missing, the current timestamp is used.
	 *
	 * @param string $fileName File path
	 * @param int $time New modification time
	 * @return bool Success or failure
	 * @static
	 */
	function touch($fileName, $time=0) {
		if (@file_exists($fileName)) {
			return @touch($fileName, (!$time ? time() : $time));
		} else {
			return FALSE;
		}
	}

	/**
	 * Get detailed information about a path
	 *
	 * Returns an array containing the following entries:
	 * # file : file name
	 * # dirs : path in the directory tree
	 * # lastDir : last directory in the path
	 * # root : path root
	 * # realPath : file path
	 * # fullPath : file path + file name
	 *
	 * @param string $path File or dir path
	 * @return array
	 * @static
	 */
	function getPathInfo($path) {
		$ret = array(
			'realPath' => '', 'fullPath' => '',
			'root' => '', 'dirs' => '',
			'lastDir' => '', 'file' => ''
		);
		$path = FileSystem::getAbsolutePath(FileSystem::getStandardPath(trim($path)));
		$dir = is_dir($path);
		if (preg_match("/^((.+\:)?(\/{1,2})?).+/", $path, $matches)) {
			$ret['root'] = $matches[1];
			$path = substr($path, strlen($matches[1]));
		}
		$path  = preg_replace(array(';/\./;', ';[/\\\\]+;', ';^(?:\.)/;', ';/\.$;'), array('/','/','','/'), $path);
		$pathParts = explode('/', $path);
		$totalParts = sizeof($pathParts);
		if ($dir) {
			$ret['lastDir'] = $pathParts[$totalParts-1];
			if (substr($pathParts[$totalParts-1], -1) != '/')
				$pathParts[$totalParts-1] .= '/';
		} else {
			$ret['file'] = $pathParts[$totalParts-1];
			if ($totalParts > 1)
				$ret['lastDir'] = $pathParts[$totalParts-2];
			array_pop($pathParts);
		}
		$ret['dirs'] = implode(PHP2GO_DIRECTORY_SEPARATOR, $pathParts);
		$ret['realPath'] = $ret['root'] . $ret['dirs'];
		$ret['fullPath'] = $ret['realPath'] . $ret['file'];
		return $ret;
	}

	/**
	 * Normalize a given file path
	 *
	 * Returns FALSE when the file doesn't exist.
	 *
	 * @param string $path File or dir path
	 * @return string|bool
	 * @static
	 */
	function getFullPath($path) {
		$pathInfo = FileSystem::getPathInfo($path);
		if (is_array($pathInfo))
			return $pathInfo['fullPath'];
		return FALSE;
	}

	/**
	 * Absolutize a file path
	 *
	 * @param string $path File path
	 * @return string
	 * @static
	 */
	function getAbsolutePath($path) {
		return realpath($path);
	}

	/**
	 * Standarize a file path
	 *
	 * @param string $path File path
	 * @return string
	 * @static
	 */
	function getStandardPath($path) {
		return str_replace("\\", "/", $path);
	}

	/**
	 * Check all components of a given file system
	 * path, creating the necessary directories
	 *
	 * @param string $path Path
	 * @param int $mode Create mode
	 * @return bool Success or failure
	 * @static
	 */
	function createPath($path, $mode=0777) {
		$cwd = getcwd();
		$path = rtrim(ltrim(trim($path), '/'), '/');
		$path = preg_replace('/^(\.{1,2}(\/|\\\))+/', '', $path);
		$path = preg_replace('/\\{1,2}/', '/', $path);
		$parts = explode('/', $path);
		for ($i=0, $size=sizeof($parts); $i<$size; $i++) {
			if (!@file_exists($parts[$i])) {
				if (!@mkdir($parts[$i], $mode)) {
					chdir($cwd);
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_CREATE_FILE', $path), E_USER_WARNING, __FILE__, __LINE__);
					return FALSE;
				}
			}
			chdir($parts[$i]);
		}
		chdir($cwd);
		return TRUE;
	}

	/**
	 * Get formatted total used disk space
	 *
	 * @param string $mode Display mode: K, M, G or T
	 * @param int $precision Precision
	 * @uses disk_total_space()
	 * @uses Number::formatByteAmount()
	 * @return string
	 * @static
	 */
	function getDiskTotalSpace($mode='', $precision=2) {
		$size = disk_total_space('/');
		return Number::formatByteAmount($size, $precision);
	}

	/**
	 * Get formatted total free disk space
	 *
	 * @param string $mode Display mode: K, M, G or T
	 * @param int $precision Precision
	 * @uses disk_free_space()
	 * @uses Number::formatByteAmount()
	 * @return string
	 * @static
	 */
	function getDiskFreeSpace($mode='', $precision=2) {
		$size = disk_free_space('/');
		return Number::formatByteAmount($size, $precision);
	}
}
?>