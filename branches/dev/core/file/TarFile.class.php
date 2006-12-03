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

import('php2go.file.FileCompress');
import('php2go.file.GzFile');
import('php2go.text.StringUtils');

/**
 * Used to identify the USTAR format
 */
define('TAR_MAGIC', 'ustar');
/**
 * TAR block size
 */
define('TAR_BLOCK_SIZE', 512);
/**
 * Maximum size for file names
 */
define('TAR_FILENAME_MAXLENGTH', 99);
/**
 * Maximum size for file paths
 */
define('TAR_FILEPATH_MAXLENGTH', 154);
/**
 * Maximum value for user and group IDs
 */
define('TAR_MAX_UID_GID', 2097151);

/**
 * Reads and writes TAR archives
 *
 * Creates TAR (tape archive) files from multiple files or directories.
 * Allow saving or downloading the file either in .tar or .tar.gz formats.
 * It can also extract tar archives (even gzipped ones).
 *
 * @package file
 * @uses FileManager
 * @uses GzFile
 * @uses StringUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TarFile extends FileCompress
{
	/**
	 * TAR stream
	 *
	 * @var string
	 * @access private
	 */
	var $tarData = '';

	/**
	 * Global attributes for all files in the archive
	 *
	 * @var array
	 * @access private
	 */
	var $globalAttributes;

	/**
	 * Default attributes for all files in the archive
	 *
	 * @var array
	 * @access private
	 */
	var $defaultAttributes = array (
		'mode' => 0,
		'uid' => 0,
		'gid' => 0,
		'time' => 0,
		'type' => 0,
		'link' => '',
		'path' => ''
	);

	/**
	 * Class constructor
	 *
	 * @param string $cwd Starting working dir
	 * @return TarFile
	 */
	function TarFile($cwd = '') {
		parent::FileCompress($cwd);
		$this->defaultAttributes['mode'] = decoct(0x8000 | 0x0100 | 0x0080 | 0x0020 | 0x0004);
		$this->defaultAttributes['time'] = time();
	}

	/**
	 * Set default attributes for all files in the archive
	 *
	 * When adding files in the archive, the provided attributes
	 * will be merged with the default ones.
	 *
	 * Accepted attributes: mode, uid, gid, time
	 *
	 * @param array $attrs Default attributes
	 */
	function setDefaultAttributes($attrs) {
		if (is_array($attrs)) {
			if (isset($attrs['mode'])) $this->defaultAttributes['mode'] = $attrs['mode'];
			if (isset($attrs['uid']) && $attrs['uid'] <= TAR_MAX_UID_GID) $this->defaultAttributes['uid'] = $attrs['uid'];
			if (isset($attrs['gid']) && $attrs['gid'] <= TAR_MAX_UID_GID) $this->defaultAttributes['gid'] = $attrs['gid'];
			if (isset($attrs['time'])) $this->defaultAttributes['time'] = $attrs['time'];
		}
	}

	/**
	 * Set global attributes for all files in the archive
	 *
	 * Global attributes will be used when adding a file in
	 * the archive even when the same attributes are provided.
	 *
	 * Accepted attributes: mode, time
	 *
	 * @param array $attrs Global attributes
	 */
	function setGlobalAttributes($attrs) {
		if (is_array($attrs)) {
			if (isset($attrs['mode']))
				$this->globalAttributes['mode'] = $attrs['mode'];
			if (isset($attrs['time']))
				$this->globalAttributes['time'] = $attrs['time'];
		}
	}

	/**
	 * Adds a file in the TAR stream
	 *
	 * @param string $path File path
	 * @return bool
	 */
	function addFile($path) {
		$filePath = '';
		// check if file path should be stored
		if ($this->isPathStorageEnabled())
			$fileName = preg_replace('/^(\.{1,2}(\/|\\\))+/', '', $path);
		else
			$fileName = (StringUtils::match($path, '/') ? substr($path, strrpos($path, '/') + 1) : $path);
		// check if file name exceeds maximum length
		if (strlen($fileName) > TAR_FILENAME_MAXLENGTH) {
			if (($pos = strrpos($fileName, '/')) !== FALSE) {
				$filePath = StringUtils::left($fileName, $pos+1);
				$fileName = substr($fileName, $pos);
			}
			// check if file path exceeds maximum size
			if (strlen($fileName) > TAR_FILENAME_MAXLENGTH || strlen($filePath) > TAR_FILEPATH_MAXLENGTH)
				return FALSE;
		}
		// open requested file
		$Mgr =& FileCompress::getFileManager();
		if (!$Mgr->open($path, FILE_MANAGER_READ_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			// build attributes
			$fileAttrs = $Mgr->getAttributes();
			$attrs = array(
				'mode' => decoct($fileAttrs['mode']),
				'uid' => $fileAttrs['userId'],
				'gid' => $fileAttrs['groupId'],
				'time' => $fileAttrs['mTime'],
				'type' => ($fileAttrs['isDir'] ? 5 : ($fileAttrs['isFile'] ? 0 : ($fileAttrs['isLink'] ? 1 : 9))),
				'link' => ($fileAttrs['isLink'] ? $fileAttrs['linkTarget'] : ''),
				'path' => $filePath
			);
			// add file
			$this->addData($Mgr->readFile(), $fileName, $attrs);
			$Mgr->close();
		}
		return TRUE;
	}

	/**
	 * Adds data in the TAR stream
	 *
	 * Accepted attributes: mode, uid, gid, time, type,
	 * type (0-regular, 1-link, 5-dir), link, path, size
	 *
	 * @param string $data Data
	 * @param string $fileName File name
	 * @param array $attrs File attributes
	 * @return bool
	 */
	function addData($data, $fileName, $attrs) {
		if (strlen($fileName) > TAR_FILENAME_MAXLENGTH || (isset($attrs['path']) && strlen($attrs['path'] > TAR_FILEPATH_MAXLENGTH)))
			return FALSE;
		// build file attributes
		$fileAttrs = array(
			'mode' => (isset($this->globalAttributes['mode']) ? $this->globalAttributes['mode'] : (isset($attrs['mode']) ? $attrs['mode'] : $this->defaultAttributes['mode'])),
			'uid' => (isset($attrs['uid']) && $attrs['uid'] <= TAR_MAX_UID_GID ? $attrs['uid'] : $this->defaultAttributes['uid']),
			'gid' => (isset($attrs['gid']) && $attrs['gid'] <= TAR_MAX_UID_GID ? $attrs['gid'] : $this->defaultAttributes['gid']),
			'time' => (isset($this->globalAttributes['time']) ? $this->globalAttributes['time'] : (isset($attrs['time']) ? $attrs['time'] : $this->defaultAttributes['time'])),
			'link' => (isset($attrs['link']) ? $attrs['link'] : $this->defaultAttributes['link']),
			'path' => (isset($attrs['path']) ? $attrs['path'] : $this->defaultAttributes['path']),
			'size' => (isset($attrs['size']) ? $attrs['size'] : strlen($data))
		);
		// configure file name
		if ($this->isPathStorageEnabled()) {
			$fileName = preg_replace('/^(\.{1,2}(\/|\\\))+/', '', $fileName);
		} else {
			$fileName = (StringUtils::match($fileName, '/') ? substr($fileName, strrpos($fileName, '/')+1) : $fileName);
			$fileAttrs['path'] = '';
		}
		// build start/end blocks and calculate checksum
		$startBlock = $this->_buildBlockStart($fileName, $fileAttrs);
		$endBlock = $this->_buildBlockEnd($fileAttrs);
		$checkSum = $this->_buildChecksum($startBlock, $endBlock);
		// the size of a file in a TAR stream must be divisible by 512 bytes
		if (($fileAttrs['size'] % TAR_BLOCK_SIZE) > 0)
			$data .= str_repeat("\0", TAR_BLOCK_SIZE - ($fileAttrs['size'] % TAR_BLOCK_SIZE));
		/**
		 * format of a TAR entry
		 * 0-147 (148) Start of block 0
		 * 148-155 (8) Checksum
		 * 156-499 (344) End of block 0
		 * 500-511 (12) Empty
		 * 512-... (512*N) N file blocks
		 */
		$this->tarData .= $startBlock . $checkSum . $endBlock . pack("a12", '') . $data;
		return TRUE;
	}

	/**
	 * Get the TAR stream
	 *
	 * @return string
	 */
	function getData() {
		return $this->tarData . pack("a512", '');
	}

	/**
	 * Extract files from a TAR stream
	 *
	 * @param string $data TAR data
	 * @return array Array of extracted files
	 */
	function extractData($data) {
		$position = 0;
		$blockCount = (strlen($data) / TAR_BLOCK_SIZE) - 1;
		parent::debug($blockCount . ' blocks');
		$returnData = array();
		while ($position < $blockCount) {
			parent::debug('file at position ' . $position);
			$fileHeader = substr($data, TAR_BLOCK_SIZE * $position, TAR_BLOCK_SIZE);
			if (ord($fileHeader[0]) == 0)
				break;
			$file = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12time/a8checksum/a1type/a100linkname/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155path", $fileHeader);
			$file['filename'] = trim($file['filename']);
			$file['mode'] = octdec(trim($file['mode']));
			$file['uid'] = octdec(trim($file['uid']));
			$file['gid'] = octdec(trim($file['gid']));
			$file['size'] = octdec(trim($file['size']));
			$file['time'] = octdec(trim($file['time']));
			$file['checksum'] = trim($file['checksum']);
			$file['type'] = trim($file['type']);
			$file['linkname'] = trim($file['linkname']);
			$file['path'] = trim($file['path']);
			if (!$this->isPathStorageEnabled())
				$file['filename'] = substr($file['filename'], strrpos($file['filename'], '/') + 1);
			if ($file['checksum'] != trim($this->_buildChecksum($fileHeader, $fileHeader)))
				break;
			$size = ceil($file['size'] / TAR_BLOCK_SIZE);
			parent::debug("file has $size blocks => initial block : " . ($position+1) . "  - final block : " . ($position+1+$size));
			parent::debug('header : ' . exportVariable($file, TRUE));
			$file['data'] = substr($data, TAR_BLOCK_SIZE * (++$position), $file['size']);

			$position += $size;
			$returnData[] = $file;
		}
		parent::debug(sizeof($returnData) . ' files + folders');
		return $returnData;
	}

	/**
	 * Extract a TAR archive compressed with gzip
	 *
	 * @param string $fileName File path
	 * @return array|NULL
	 */
	function extractGzip($fileName) {
		if (file_exists($fileName) && $fp = @gzopen($fileName, 'rb')) {
			$buffer = '';
			while (!gzeof($fp))
				$buffer .= gzread($fp, 1024);
			gzclose($fp);
			return $this->extractData($buffer);
		}
		return NULL;
	}

	/**
	 * Compress using gzip and save the TAR stream
	 *
	 * @param string $tarName File name
	 * @param int $mode Save mode
	 */
	function saveGzip($tarName, $mode = NULL) {
		$Gz =& FileCompress::getInstance('gz');
		$Gz->addData($this->getData(), $tarName, array('time' => time()));
		if (!StringUtils::endsWith($tarName, '.gz') && !StringUtils::endsWith($tarName, '.gzip') && !StringUtils::endsWith($tarName, '.tgz'))
			$tarName .= '.gz';
		$Gz->saveFile($tarName, $mode);
	}

	/**
	 * Compress using gzip and download the TAR stream
	 *
	 * @param string $tarName File name
	 */
	function downloadGzip($tarName) {
		$Gz =& FileCompress::getInstance('gz');
		$Gz->addData($this->getData(), $tarName, array('time' => time()));
		if (!StringUtils::endsWith($tarName, '.gz') && !StringUtils::endsWith($tarName, '.gzip') && !StringUtils::endsWith($tarName, '.tgz'))
			$tarName .= '.gz';
		$Gz->downloadFile($tarName);
	}

	/**
	 * Build the start of a TAR block
	 *
	 * @param string $fileName File name
	 * @param array $fileAttrs File attributes
	 * @return string TAR block
	 * @access private
	 */
	function _buildBlockStart($fileName, $fileAttrs) {
		$fUid = sprintf("%6s", decoct($fileAttrs['uid']));
		$fGid = sprintf("%6s", decoct($fileAttrs['gid']));
		$fSize = sprintf("%11s ", decoct($fileAttrs['size']));
		$fTime = sprintf("%11s ", decoct($fileAttrs['time']));
		/**
		 * format of the start part of a TAR block
		 * 0-99 (100) File name
		 * 100-107 (8) File mode (octal)
		 * 108-115 (8) Owner ID (octal)
		 * 116-123 (8) Group ID (octal)
		 * 124-135 (12) File size (octal)
		 * 136-147 (12) Modify time (octal)
		 */
		return pack("a100a8a8a8a12a12", $fileName, $fileAttrs['mode'], $fUid, $fGid, $fSize, $fTime);
	}

	/**
	 * Build the end of a TAR block
	 *
	 * @param array $fileAttrs File attributes
	 * @return string TAR block
	 * @access private
	 */
	function _buildBlockEnd($fileAttrs) {
		/**
		 * format of the end part of a TAR block
		 * 156 (1) File type
		 * 157-256 (100) Link path
		 * 257-262 (6) Magic string "ustar"
		 * 263-264 (2) TAR version
		 * 265-296 (32) Owner name
		 * 297-328 (32) Group name
		 * 329-336 (8) Major device ID
		 * 337-344 (8) Minor devide ID
		 * 345-499 (155) File path
		 */
		return pack("a1a100a6a2a32a32a8a8a155", $fileAttrs['type'], $fileAttrs['link'], TAR_MAGIC, "00", "Unknown", "Unknown", "", "", $fileAttrs['path']);
	}

	/**
	 * Build the checksum of the first TAR block
	 *
	 * @param string $blockStart Block start
	 * @param string $blockEnd Block end
	 * @return string Checksum
	 * @access private
	 */
	function _buildChecksum($blockStart, $blockEnd) {
		$checksum = 0;
		for ($i = 0; $i < 148; $i++) {
			$checksum += ord(substr($blockStart, $i, 1));
		}
		for ($i = 148; $i < 156; $i++) {
			$checksum += ord(' ');
		}
		$init = ($blockStart == $blockEnd) ? 156 : 0;
		for ($i = 156, $j = $init; $i < 512; $i++, $j++) {
			$checksum += ord(substr($blockEnd, $j, 1));
		}
		return pack("a8", sprintf("%6s", decoct($checksum)));
	}
}
?>