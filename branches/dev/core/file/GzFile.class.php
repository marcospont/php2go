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
import('php2go.text.StringUtils');

/**
 * Reads and write GZIP files
 *
 * The class is able to create and extract GZIP files
 * using functions of the <b>zlib</b> extension.
 *
 * @package file
 * @uses FileManager
 * @uses HttpResponse
 * @uses StringUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class GzFile extends FileCompress
{
	/**
	 * GZIP stream
	 *
	 * @var string
	 * @access private
	 */
	var $gzData;

	/**
	 * Compression level
	 *
	 * @var int
	 * @access private
	 */
	var $level = 9;

	/**
	 * Class constructor
	 *
	 * @return GzFile
	 */
	function GzFile() {
		parent::FileCompress();
		$this->gzData = "";
	}

	/**
	 * Set compression level
	 *
	 * @param int $level Compression level
	 */
	function setCompressionLevel($level) {
		$this->level = $level <= 9 ? max(1, (int)$level): 9;
	}

	/**
	 * Adds a file in the GZIP stream
	 *
	 * @param string $fileName File path
	 * @param string $comment File comment
	 * @return bool
	 */
	function addFile($fileName, $comment=NULL) {
		$Mgr =& FileCompress::getFileManager();
		if (!$Mgr->open($fileName, FILE_MANAGER_READ_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$fileName = (StringUtils::match($fileName, '/') ? substr($fileName, strrpos($fileName, '/') + 1) : $fileName);
			$attrs['time'] = $Mgr->getAttribute('mTime');
			if (!is_null($comment))
				$attrs['comment'] = $comment;
			$this->addData($Mgr->readFile(), $fileName, $attrs);
			$Mgr->close();
			return TRUE;
		}
	}

	/**
	 * Adds data in the GZIP stream
	 *
	 * Attributes:
	 * # time : last modified time
	 * # comment : file comment
	 *
	 * @param string $data Data
	 * @param string $fileName File name
	 * @param array $fileAttrs File attributes
	 */
	function addData($data, $fileName=NULL, $fileAttrs=array()) {
		$fileFlags = bindec('000' . (isset($fileAttrs['comment']) ? '1' : '0') . ($fileName != NULL ? '1' : '0') . '000');
		$this->gzData = $this->_buildFileHeader($fileFlags, $fileAttrs['time']);
		if ($fileName != NULL)
			$this->gzData .= $fileName . "\0";
		if (isset($fileAttrs['comment']))
			$this->gzData .= $fileAttrs['comment'] . "\0";
		$this->gzData .= gzdeflate($data, $this->level);
		$this->gzData .= pack("VV", crc32($data), strlen($data));
	}

	/**
	 * Get the GZIP stream
	 *
	 * @return string
	 */
	function getData() {
		return $this->gzData;
	}

	/**
	 * Extract GZIP data
	 *
	 * @param string $data GZIP data
	 * @access protected
	 * @return array Extracted file information
	 */
	function extractData($data) {
		$header = unpack("H2a/H2b/Cflags", StringUtils::left($data, 3));
		$descriptor = unpack("Vcrc/Vsize", StringUtils::right($data, 8));
		if ($header['a'] != "1f" || $header['b'] != "8b")
			return FALSE;
		$hasFileName = TypeUtils::toBoolean(decbin($header['flags']) & 0x8);
		$hasComment = TypeUtils::toBoolean(decbin($header['flags']) & 0x4);
		$offset = 10;
		$file = array();
		$file['filename'] = "";
		$file['comment'] = "";
		while ($hasFileName) {
			$char = substr($data, $offset, 1);
			$offset++;
			if ($char == "\0")
				break;
			$file['filename'] .= $char;
		}
		if ($file['filename'] == "")
			$file['filename'] = uniqid("gzfile_");
		while ($hasComment) {
			$char = substr($data, $offset, 1);
			$offset++;
			if ($char == "\0")
				break;
			$file['comment'] .= $char;
		}
		$file['size'] = $descriptor['size'];
		$file['data'] = gzinflate(substr($data, $offset, strlen($data)-8 - $offset));
		if ($descriptor['crc'] != crc32($file['data']))
			return FALSE;
		return $file;
	}

	/**
	 * Build the GZIP header
	 *
	 * @param int $flags File flags
	 * @param int $mtime File timestamp
	 * @access private
	 * @return string
	 */
	function _buildFileHeader($flags, $mtime = 0) {
		if (!$mtime) $mtime = time();
		// Header format:
		// POS    SIZE      DESC
		// 0      2         Magic header
		// 2      1         Compression Method
		// 3      1         File flags
		// 4      4         File modification time in Unix format
		// 8      1         Extra flags
		// 9      1         OS type
		return pack("C1C1C1C1VC1C1", 0x1f, 0x8b, 8, $flags, $mtime, 2, 0xFF);
	}
}
?>