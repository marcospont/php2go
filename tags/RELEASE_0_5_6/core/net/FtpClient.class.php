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
 * Default FTP port
 */
define('FTP_DEFAULT_PORT', 21);

/**
 * FTP client class
 *
 * Implementation of a FTP (file transfer protocol) client, based on the
 * functions provided by the <b>ftp</b> extension.
 *
 * Example:
 * <code>
 * $ftp = new FtpClient();
 * $ftp->togglePassiveMode(TRUE);
 * $ftp->setServer('ftp.debian.org', FTP_DEFAULT_PORT);
 * if ($ftp->connect()) {
 *   if ($ftp->login(TRUE)) {
 *     $ftp->changeDir('debian');
 *     $list = $ftp->fileList();
 *     foreach ($list as $entry)
 *       print $entry . '<br/>';
 *     $ftp->quit();
 *   }
 * }
 * </code>
 *
 * @package net
 * @uses System
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FtpClient extends PHP2Go
{
	/**
	 * FTP host
	 *
	 * @var string
	 */
	var $host;

	/**
	 * FTP port
	 *
	 * @var int
	 */
	var $port = FTP_DEFAULT_PORT;

	/**
	 * FTP username
	 *
	 * @var string
	 */
	var $user;

	/**
	 * FTP password
	 *
	 * @var string
	 */
	var $password;

	/**
	 * Current transfer mode
	 *
	 * @var int
	 */
	var $transferMode = FTP_BINARY;

	/**
	 * Connection timeout
	 *
	 * @var int
	 */
	var $timeout;

	/**
	 * Default settings
	 *
	 * Hash array of property=>value the can be
	 * used to apply values for multiple class
	 * properties. Used inside {@link reset}.
	 *
	 * @var array
	 */
	var $defaultSettings = array();

	/**
	 * Current local path
	 *
	 * @var string
	 * @access private
	 */
	var $localPath = '';

	/**
	 * Current remote path
	 *
	 * @var string
	 * @access private
	 */
	var $remotePath = '';

	/**
	 * Holds FTP server systype
	 *
	 * @var string
	 * @access private
	 */
	var $sysType = '';

	/**
	 * FTP connection handle
	 *
	 * @var resource
	 * @access private
	 */
	var $connectionId;

	/**
	 * Indicates if the connection is active
	 *
	 * @var bool
	 * @access private
	 */
	var $connected = FALSE;

	/**
	 * Class constructor
	 *
	 * @return FtpClient
	 */
	function FtpClient() {
		parent::PHP2Go();
		if (!System::loadExtension("ftp"))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', "ftp"), E_USER_ERROR, __FILE__, __LINE__);
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
	function __destruct() {
		$this->quit();
	}

	/**
	 * Set remote host and port
	 *
	 * Should be called before {@link connect}.
	 *
	 * @param string $host FTP host or IP address
	 * @param int $port FTP port
	 */
	function setServer($host, $port=FTP_DEFAULT_PORT) {
		if (!$this->isConnected()) {
			$this->host = $host;
			$this->port = intval($port);
		}
	}

	/**
	 * Set user credentials
	 *
	 * Should be called before {@link login}.
	 *
	 * @param string $user Username
	 * @param string $password Password
	 */
	function setUserInfo($user, $password) {
		if (!$this->isConnected()) {
			$this->user = $user;
			$this->password = $password;
		}
	}

	/**
	 * Set connection timeout, in seconds
	 *
	 * Should  be called before {@link connect}, or
	 * it won't have any effect.
	 *
	 * @param int $timeout Timeout
	 */
	function setTimeout($timeout) {
		if ($timeout > 0)
			$this->timeout = $timeout;
	}

	/**
	 * Change transfer mode
	 *
	 * Default transfer mode is {@link FTP_BINARY}.
	 *
	 * @param int $mode
	 */
	function setTransferMode($mode) {
		if ($mode == FTP_ASCII || $mode == FTP_BINARY)
			$this->transferMode = $mode;
	}

	/**
	 * Opens the FTP connection
	 *
	 * @return bool
	 */
	function connect() {
		if ($this->isConnected())
			$this->quit();
		if (!isset($this->host))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FTP_MISSING_HOST'), E_USER_ERROR, __FILE__, __LINE__);
		$this->connectionId = ftp_connect($this->host, $this->port, $this->timeout);
		if (!is_resource($this->connectionId))
			return FALSE;
		$this->connected = TRUE;
		// define connection timeout
		if (isset($this->timeout))
			ftp_set_option($this->connectionId, FTP_TIMEOUT_SEC, $this->timeout);
		return TRUE;
	}

	/**
	 * Check if the FTP connection is active
	 *
	 * @return bool
	 */
	function isConnected() {
		return ($this->connected && isset($this->connectionId) && is_resource($this->connectionId));
	}

	/**
	 * Send an authentication request
	 *
	 * @param bool $anonymous Whether to use anonymous login
	 * @return bool
	 */
	function login($anonymous=FALSE) {
		if (!$this->isConnected())
			$this->connect();
		if ((!isset($this->user) || !isset($this->password)) && !$anonymous)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FTP_MISSING_USER_OR_PASS'), E_USER_ERROR, __FILE__, __LINE__);
		$authUser = ($anonymous ? 'anonymous' : $this->user);
		$authPass = ($anonymous ? 'anonymous@ftpclient.php2go' : $this->password);
		return ftp_login($this->connectionId, $authUser, $authPass);
	}

	/**
	 * Get current remote path
	 *
	 * Returns FALSE when the FTP connection isn't active.
	 *
	 * @return string|bool
	 */
	function getCurrentDir() {
		if (!$this->isConnected())
			return FALSE;
		if (!empty($this->remotePath))
			return $this->remotePath;
		$path = ftp_pwd($this->connectionId);
		if (!$path) {
			$this->remotePath = null;
			return FALSE;
		} else {
			$this->remotePath = $path;
			return $this->remotePath;
		}
	}

	/**
	 * Get remote server's systype
	 *
	 * Returns FALSE if the FTP connection isn't active.
	 *
	 * @return bool|string
	 */
	function getSysType() {
		if (!$this->isConnected())
			return FALSE;
		if (!empty($this->sysType))
			return $this->sysType;
		$sysType = ftp_systype($this->connectionId);
		if (!$sysType) {
			$this->sysType = NULL;
			return FALSE;
		} else {
			$this->sysType = $sysType;
			return $this->sysType;
		}
	}

	/**
	 * Toggles FTP passive mode
	 *
	 * @param bool $mode Enable/disable
	 * @return bool
	 */
	function togglePassiveMode($mode) {
		return ($this->isConnected() ? ftp_pasv($this->connectionId, (bool)$mode) : FALSE);
	}

	/**
	 * Execute an FTP SITE command
	 *
	 * @param string $command Site command
	 * @return bool
	 */
	function site($command) {
		return ($this->isConnected() ? ftp_site($this->connectionId, $command) : FALSE);
	}

	/**
	 * Changes the remote directory
	 *
	 * @param string $directory Target directory
	 * @return bool
	 */
	function changeDir($directory) {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_chdir($this->connectionId, $directory);
		if ($result)
			$this->remotePath = ftp_pwd($this->connectionId);
		return (bool)$result;
	}

	/**
	 * Changes to the parent directory
	 *
	 * @return bool
	 */
	function changeDirUp() {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_cdup($this->connectionId);
		if ($result)
			$this->remotePath = ftp_pwd($this->connectionId);
		return (bool)$result;
	}

	/**
	 * Creates a directory in the FTP server
	 *
	 * @param string $directory Directory name
	 * @param bool $moveDir Whether to set the new directory as working directory
	 * @return bool
	 */
	function makeDir($directory, $moveDir=FALSE) {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_mkdir($this->connectionId, $directory);
		if ($result && $moveDir)
			return $this->changeDir($directory);
		return $result;
	}

	/**
	 * Removes a directory in the FTP server
	 *
	 * @param string $directory Directory name
	 * @return bool
	 */
	function removeDir($directory) {
		return ($this->isConnected() ? ftp_rmdir($this->connectionId, $directory) : FALSE);
	}

	/**
	 * Removes an FTP directory and all its contents
	 *
	 * Operation will stop upon first error. Contents
	 * removed until that point are not recovered.
	 *
	 * @param string $directory Directory name
	 * @return bool
	 */
	function removeDirRecursive($directory) {
		if ($directory != '') {
			if (!$this->changeDir($directory))
				return FALSE;
			$files = $this->rawList();
			if (is_array($files)) {
				for ($i=0, $s=sizeof($files); $i<$s; $i++) {
					$fileInfo = $files[$i];
					if ($fileInfo['type'] == 'dir') {
						if (!$this->removeDirRecursive($fileInfo['name']))
							return FALSE;
					} elseif (!$this->delete($fileInfo['name'])) {
						return FALSE;
					}
				}
				if ($this->changeDirUp() && $this->removeDir($directory))
					return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Get a list of files of the current remote path or
	 * from a given path and file mask
	 *
	 * Example:
	 * <code>
	 * $ftp = new FtpClient();
	 * $ftp->setServer('host.address');
	 * $ftp->connect();
	 * $ftp->login(TRUE);
	 * $ftp->changeDir('releases');
	 * $list = $ftp->fileList();
	 * $filteredList = $ftp->fileList('*.tar.gz');
	 * $subDirList = $ftp->fileList('old/*.tar.gz');
	 * $ftp->quit();
	 * </code>
	 *
	 * Returns FALSE when the FTP connection is not active or when
	 * the files list can't be retrieved.
	 *
	 * @param string $path Path and/or file mask
	 * @uses _parseDir
	 * @see rawList
	 * @return array|bool
	 */
	function fileList($path='') {
		if (!$this->isConnected())
			return FALSE;
		if ($path != '') {
			list($dir, $fileMask) = $this->_parseDir($path);
			if (!empty($dir))
				$this->changeDir($dir);
		} else {
			$fileMask = '';
		}
		$result = ftp_nlist($this->connectionId, $fileMask);
		if ($result)
			return $result;
		return FALSE;
	}

	/**
	 * Get the raw list of files from the current remote path
	 * or from a given path and file mask
	 *
	 * @param string $path Path and/or file mask
	 * @param bool $parseInfo Whether to parse returned data into an array
	 * @uses _parseDir
	 * @uses _parseRawList
	 * @return string|array
	 */
	function rawList($path='', $parseInfo=TRUE) {
		if (!$this->isConnected())
			return FALSE;
		if ($path != '') {
			list($dir, $fileMask) = $this->_parseDir($path);
			if (!empty($dir))
				$this->changeDir($dir);
		} else {
			$fileMask = '';
		}
		$result = ftp_rawlist($this->connectionId, $fileMask);
		if ($result)
			return ($parseInfo ? $this->_parseRawList($result) : $result);
		return FALSE;
	}

	/**
	 * Downloads a file from the FTP server
	 *
	 * @param string $localFile Local file name
	 * @param string $remoteFile Remote file name
	 * @param int $mode Transfer mode
	 * @param int $resume Resume position
	 * @return bool
	 */
	function get($localFile, $remoteFile, $mode=NULL, $resume=NULL) {
		if (!$this->isConnected())
			return FALSE;
		if (empty($mode) || ($mode != FTP_ASCII && $mode != FTP_BINARY))
			$mode = $this->transferMode;
		$result = ftp_get($this->connectionId, $localFile, $remoteFile, $mode, (TypeUtils::isInteger($resume) && $resume >= 0 ? $resume : 0));
		return ($result == FTP_FINISHED);
	}

	/**
	 * Saves a remote file into a given file pointer
	 *
	 * @param resource $filePointer Target file
	 * @param string $remoteFile Remote file name
	 * @param int $mode Transfer mode
	 * @param int $resume Resume position
	 * @return bool
	 */
	function fileGet($filePointer, $remoteFile, $mode=NULL, $resume=FALSE) {
		if (!$this->isConnected())
			return FALSE;
		if (!is_resource($filePointer))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_RESOURCE', array('$filePointer', '$FtpClient->fileGet')), E_USER_ERROR, __FILE__, __LINE__);
		if (empty($mode) || ($mode != FTP_ASCII && $mode != FTP_BINARY))
			$mode = $this->transferMode;
		$result = ftp_fget($this->connectionId, $filePointer, $remoteFile, $mode, ($resume ? filesize($filePointer) : 0));
		return ($result == FTP_FINISHED);
	}

	/**
	 * Downloads all file and folders starting from
	 * the current remote path or from a given directory
	 *
	 * @param string $directory Start point
	 * @param int $mode Transfer mode
	 * @return bool
	 */
	function nGet($directory='', $mode=NULL) {
		if (!$this->isConnected())
			return FALSE;
		$list = $this->rawList($directory);
		if (empty($list) || !is_array($list) || !$this->changeDir($directory))
			return FALSE;
		foreach ($list as $entry) {
			switch ($entry['type']) {
				case 'dir' :
					if (@mkdir($entry['name']) && chdir($entry['name']) && $this->changeDir($entry['name']) && $this->nGet('', $mode)) {
						chdir('..');
						$this->changeDirUp();
					}
					break;
				case 'file' :
					if (!$this->get($entry['name'], $entry['name'], $mode))
						return FALSE;
					break;
			}
		}
		return TRUE;
	}

	/**
	 * Uploads a file to the FTP server
	 *
	 * @param string $localFile Local file name
	 * @param string $remoteFile Remote file name
	 * @param int $mode Transfer mode
	 * @return int
	 */
	function put($localFile, $remoteFile, $mode=NULL) {
		if (!$this->isConnected())
			return FALSE;
		// change to the target folder, if any
		list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
		if (!empty($changeDir) && !$this->changeDir($changeDir))
			return FALSE;
		if (empty($mode) || ($mode != FTP_ASCII && $mode != FTP_BINARY))
			$mode = $this->transferMode;
		return ftp_put($this->connectionId, $remoteFile, $localFile, $mode);
	}

	/**
	 * Uploads the contents of a file to the FTP server
	 *
	 * @param resource $filePointer File pointer
	 * @param string $remoteFile Remote file name
	 * @param int $mode Transfer mode
	 * @return bool
	 */
	function filePut($filePointer, $remoteFile, $mode=NULL) {
		if (!$this->isConnected())
			return FALSE;
		if (!is_resource($filePointer))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_RESOURCE', array('$filePointer', 'FtpClient::filePut')), E_USER_ERROR, __FILE__, __LINE__);
		// change to the target folder, if any
		list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
		if (!empty($changeDir) && !$this->changeDir($changeDir))
			return FALSE;
		if (empty($mode) || ($mode != FTP_ASCII && $mode != FTP_BINARY))
			$mode = $this->transferMode;
		return ftp_fput($this->connectionId, $remoteFile, $filePointer, $mode);
	}

	/**
	 * Uploads all files and directories starting from the
	 * current remote path or from a given directory
	 *
	 * @param string $directory Start point
	 * @param int $mode Transfer mode
	 * @return bool
	 */
	function nPut($directory='', $mode=NULL) {
		if (!$this->isConnected())
			return FALSE;
		if (!empty($directory)) {
			if (!is_dir($directory))
				return FALSE;
			chdir($directory);
		}
		if ($handle = opendir(getcwd())) {
			while (FALSE !== ($fileName = readdir($handle))) {
				if ($fileName != '.' && $fileName != '..') {
					if (is_dir($fileName)) {
						chdir($fileName);
						if ($this->makeDir($fileName, TRUE) && $this->nPut('', $mode)) {
							chdir('..');
							$this->changeDirUp();
						} else {
							return FALSE;
						}
					} elseif (is_file($fileName)) {
						if (!$this->put($fileName, $fileName, $mode))
							return FALSE;
					}
				}
			}
			closedir($handle);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Renames a file on the FTP server
	 *
	 * @param string $remoteFile Old name
	 * @param string $newName New name
	 * @return bool
	 */
	function rename($remoteFile, $newName) {
		if (!$this->isConnected())
			return FALSE;
		return ftp_rename($this->connectionId, $remoteFile, $newName);
	}

	/**
	 * Deletes a file on the FTP server
	 *
	 * @param string $remoteFile Remote file name
	 * @return bool
	 */
	function delete($remoteFile) {
		if (!$this->isConnected())
			return FALSE;
		return ftp_delete($this->connectionId, $remoteFile);
	}

	/**
	 * Get last modified date of a remote file
	 *
	 * Not all FTP servers support this feature. Besides, it
	 * doesn't work on remote directories.
	 *
	 * Returns FALSE when the FTP connection isn't active or
	 * when the last modified time can't be read.
	 *
	 * @uses Date::formatTime()
	 * @param string $remoteFile Remote file name
	 * @param int $formatDate Date format
	 * @return int|string|bool
	 */
	function fileLastMod($remoteFile, $formatDate=TRUE) {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_mdtm($this->connectionId, $remoteFile);
		if ($result && $result != 1)
			return ($formatDate ? Date::formatTime($result) : $result);
		return FALSE;
	}

	/**
	 * Get the size of a remote file
	 *
	 * @param string $remoteFile Remote file name
	 * @return int|bool
	 */
	function fileSize($remoteFile) {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_size($this->connectionId, $remoteFile);
		if ($result && $result != -1)
			return $result;
		return FALSE;
	}

	/**
	 * Prepares the client for a new connection
	 *
	 * # Closes the current connection, if any
	 * # Reset all class properties
	 * # Apply default settings
	 */
	function restart() {
		if ($this->isConnected())
			$this->quit();
		foreach($this->defaultSettings as $property => $value)
			$this->$property = $value;
		unset($this->host);
		unset($this->user);
		unset($this->password);
		unset($this->connectionId);
	}

	/**
	 * Closes the FTP connection, if it's active
	 *
	 * @return bool
	 */
	function quit() {
		if (!$this->isConnected())
			return FALSE;
		$this->connected = (!ftp_quit($this->connectionId));
		if (!$this->connected)
			unset($this->connectionId);
		return (!$this->connected);
	}

	/**
	 * Parses dir name and/or file mask from $str
	 *
	 * @param string $str Input string
	 * @access private
	 * @return array
	 */
	function _parseDir($str) {
		if (strpos($str, '/') !== FALSE) {
			$slashPos = strrpos($str, '/');
			return array(substr($str, 0, $slashPos + 1), substr($str, $slashPos + 1, strlen($str) - $slashPos));
		} else {
			return array('', $str);
		}
	}

	/**
	 * Parses raw information returned by {@link ftp_rawlist()}
	 * into an array of FTP file entries
	 *
	 * @param string $rawList Raw list
	 * @return array
	 */
	function _parseRawList($rawList) {
		if (is_array($rawList)) {
			$newList = array();
			$fileInfo = array();
			while (list($k) = each($rawList)) {
				$element = split(' {1,}', $rawList[$k], 9);
				if (is_array($element) && sizeof($element) == 9) {
					unset($fileInfo);
					$dateF = PHP2Go::getConfigVal('LOCAL_DATE_FORMAT');
					$year = (FALSE === strpos($element[7], ':') ? $element[7] : date('Y'));
					$month = $element[5];
					$day = (strlen($element[6]) == 2 ? $element[6] : '0' . $element[6]);
					$fileInfo['name'] = $element[8];
					$fileInfo['size'] = intval($element[4]);
					$fileInfo['date'] = ($dateF == 'Y/m/d') ? $year . '/' . $month . '/' . $day : $day . '/' . $month . '/' . $year;
					$fileInfo['attr'] = $element[0];
					$fileInfo['type'] = ($element[0][0] == '-') ? 'file' : 'dir';
					$fileInfo['dirno'] = intval($element[1]);
					$fileInfo['user'] = $element[2];
					$fileInfo['group'] = $element[3];
					$newList[] = $fileInfo;
				}
			}
			return $newList;
		}
		return FALSE;
	}
}
?>