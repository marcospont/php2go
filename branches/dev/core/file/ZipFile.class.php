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

import('php2go.datetime.Date');
import('php2go.file.FileCompress');

/**
 * Reads and writes files in the ZIP format
 *
 * Creates ZIP archives with multiple files and folders, and
 * extract archives in the ZIP format.
 *
 * @package file
 * @uses Date
 * @uses FileManager
 * @uses System
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ZipFile extends FileCompress
{
	/**
	 * Default timestamp for all included files
	 *
	 * @var int
	 * @access private
	 */
	var $defaultTime;

	/**
	 * Global timestamp for all included files
	 *
	 * @var array
	 * @access private
	 */
	var $globalTime;

	/**
	 * Compression level
	 *
	 * @var int
	 * @access private
	 */
	var $level;

	/**
	 * ZIP contents
	 *
	 * @var array
	 * @access private
	 */
	var $zipData = array();

	/**
	 * ZIP central directory data
	 *
	 * @var array
	 * @access private
	 */
	var $centralData = array();

	/**
	 * Final sequence of the ZIP central directory
	 *
	 * @var string
	 * @access private
	 */
	var $centralDataEof;

	/**
	 * Use to bookmark a position inside the central directory
	 *
	 * @var int
	 * @access private
	 */
	var $lastOffset;

	/**
	 * Class constructor
	 *
	 * @param string $cwd Initial working dir
	 * @return ZipFile
	 */
	function ZipFile($cwd = '') {
		parent::FileCompress($cwd);
		if (!System::loadExtension('zlib'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'zlib'), E_USER_ERROR, __FILE__, __LINE__);
		$this->defaultTime = time();
		$this->centralDataEof = "\x50\x4b\x05\x06\x00\x00\x00\x00";
		$this->lastOffset = 0;
		$this->level = 9;
	}

	/**
	 * Set default timestamp for all included files
	 *
	 * Default timestamp will be used when a file is
	 * included without an explicit timestamp.
	 *
	 * @param int $time Default timestamp
	 */
	function setDefaultTime($time) {
		$this->defaultTime = $time;
	}

	/**
	 * Set global timestamp for all included files
	 *
	 * The global timestamp will be used even when
	 * the file is added along with an explicit timestamp.
	 *
	 * @param int $time Global timestamp
	 */
	function setGlobalTime($time) {
		$this->globalTime = $time;
	}

	/**
	 * Set compression level
	 *
	 * @param int $level
	 */
	function setCompressionLevel($level) {
		$this->level = $level <= 9 ? max(1, TypeUtils::parseIntegerPositive($level)): 9;
	}

	/**
	 * Adds a file in the ZIP stream
	 *
	 * @param string $fileName File path
	 */
	function addFile($fileName) {
		// check if file path should be stored
		if (parent::isPathStorageEnabled())
			$fileName = preg_replace("/^(\.{1,2}(\/|\\\))+/", "", $fileName);
		else
			$fileName = (strpos($fileName, '/') !== FALSE ? substr($fileName, strrpos($fileName, '/') + 1) : $fileName);
		// open the file
		$Mgr =& FileCompress::getFileManager();
		if (!$Mgr->open($fileName, FILE_MANAGER_READ_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			// get last modified time
			$attrs['time'] = $Mgr->getAttribute('mTime');
			// add file
			$this->addData($Mgr->readFile(), $fileName, $attrs);
			$Mgr->close();
		}
	}

	/**
	 * Adds data in the ZIP stream
	 *
	 * @uses Date::fromUnixToDosDate()
	 * @param string $data Data
	 * @param string $fileName File name
	 * @param array $attrs File attributes
	 */
	function addData($data, $fileName, $attrs) {
		// build last modified time
		$time = (isset($this->globalTime) ? $this->globalTime : (is_array($attrs) && isset($attrs['time']) ? $attrs['time'] : $this->defaultTime));
		$decTime = dechex(Date::fromUnixToDosDate($time));
		$hexTime = '\x' . $decTime[6] . $decTime[7] . '\x' . $decTime[4] . $decTime[5] . '\x' . $decTime[2] . $decTime[3] . '\x' . $decTime[0] . $decTime[1];
		eval('$hexTime = "' . $hexTime . '";');
		// check if file path should be stored
		if (parent::isPathStorageEnabled())
			$fileName = preg_replace("/^(\.{1,2}(\/|\\\))+/", "", $fileName);
		else
			$fileName = (strpos($fileName, '/') !== FALSE ? substr($fileName, strrpos($fileName, '/') + 1) : $fileName);
		// get size, crc32 and compress data
		$uncompressed = strlen($data);
		$crc = crc32($data);
		$zipData = gzcompress($data, $this->level);
		$zipData = substr($zipData, 2, strlen($zipData)-6);
		$compressed = strlen($zipData);
		// build file header and file descriptor
		$fileHeader = $this->_buildLocalFileHeader($hexTime, $uncompressed, $crc, $compressed, $fileName);
		$fileDesc = $this->_buildFileDescriptor($crc, $compressed, $uncompressed);
		// add the file
		$this->zipData[] = $fileHeader . $zipData . $fileDesc;
		// add an entry in the central directory
		$offset = strlen(implode('', $this->zipData));
		$centralHeader = $this->_buildCentralDirectoryHeader($hexTime, $uncompressed, $crc, $compressed, $fileName, $offset);
		$this->lastOffset = $offset;
		$this->centralData[] = $centralHeader;
	}

	/**
	 * Get the ZIP stream
	 *
	 * @return string
	 */
	function getData() {
		$zipData = implode('', $this->zipData);
		$ctrlData = implode('', $this->centralData);
		return $zipData . $ctrlData . $this->centralDataEof . pack("vvVV", sizeof($this->centralData), sizeof($this->centralData), strlen($ctrlData), strlen($zipData)) . "\x00\x00";
	}

	/**
	 * Extract files from a ZIP stream
	 *
	 * @param string $data ZIP data
	 * @return array List of extracted files
	 */
	function extractData($data) {
		$returnData = array();
		if (($centralInfo = $this->_readCentralDirectory($data)) !== FALSE) {
			parent::debug('central directory found - data : ' . exportVariable($centralInfo, TRUE));
			$positionZip = 0;
			$positionCtrl = $centralInfo['offset'];
			for ($i=0, $size=$centralInfo['entries']; $i<$size; $i++) {
				if ($fileCentralHeader = $this->_readCentralFileHeader($data, $positionCtrl)) {
					parent::debug('file found - central header : ' . exportVariable($fileCentralHeader, TRUE));
					$positionZip = $fileCentralHeader['offset'];
					if ($fileLocalHeader = $this->_readLocalFileHeader($data, $positionZip)) {
						parent::debug('file found - local header : ' . exportVariable($fileLocalHeader, TRUE));
						$zipOffset = $positionZip;
						$zipData = substr($data, $zipOffset, $fileLocalHeader['compressed_size']);
						$fileData = gzinflate($zipData);
						if ($fileLocalHeader['crc'] != crc32($fileData)) {
							parent::debug($fileLocalHeader['filename'] . ' : CRC error');
							break;
						}
						$returnData[] = array(
							'filename' => $fileLocalHeader['filename'],
							'size' => $fileLocalHeader['size'],
							'time' => $fileLocalHeader['mtime'],
							'checksum' => $fileLocalHeader['crc'],
							'data' => $fileData
						);
					} else {
						parent::debug('error reading local file header');
						break;
					}
				} else {
					parent::debug('error reading central file header');
					break;
				}
			}
		} else {
			parent::debug('invalid archive : central directory not found');
		}
		parent::debug(sizeof($returnData) . ' files found');
		return $returnData;
	}

	/**
	 * Packs a ZIP file header
	 *
	 * @param string $hexTime Last modified date/time
	 * @param int $uncompressed Uncompressed size
	 * @param int $crc File crc
	 * @param int $compressed Compressed size
	 * @param string $fileName File name
	 * @return string File header block
	 * @access private
	 */
	function _buildLocalFileHeader($hexTime, $uncompressed, $crc, $compressed, $fileName) {
		$fHeader = "\x50\x4b\x03\x04";
		$fHeader .= "\x14\x00";
		$fHeader .= "\x00\x00";
		$fHeader .= "\x08\x00";
		$fHeader .= $hexTime;
		/**
		 * file header block
		 * 0-3 (4) Signature
		 * 4-5 (2) Required ZIP version
		 * 6-7 (2) General purpose flag
		 * 8-9 (2) Compression method
		 * 10-11 (2) Mod time
		 * 12-13 (2) Mod date
		 * 14-17 (4) CRC32
		 * 18-21 (4) Compressed size
		 * 22-25 (4) Uncompressed size
		 * 26-27 (2) File name length
		 * 28-29 (2) Extra
		 * 30-... (N) File name
		 */
		return $fHeader . pack("VVVvv", $crc, $compressed, $uncompressed, strlen($fileName), 0) . $fileName;
	}

	/**
	 * Builds a file descriptor block
	 *
	 * @param int $crc CRC32
	 * @param int $compressed Compressed size
	 * @param int $uncompressed Uncompressed size
	 * @return string Descriptor block
	 * @access private
	 */
	function _buildFileDescriptor($crc, $compressed, $uncompressed) {
		/**
		 * file descriptor block
		 * 0-3 (4) CRC32
		 * 4-7 (4) Compressed size
		 * 8-11 (4) Uncompressed size
		 */
		return pack("VVV", $crc, $compressed, $uncompressed);
	}

	/**
	 * Builds the header of a file in the central directory
	 *
	 * @param string $hexTime Last modified date/time
	 * @param int $uncompressed Uncompressed size
	 * @param int $crc File crc
	 * @param int $compressed Compressed size
	 * @param string $fileName File name
	 * @param int $offset Offset
	 * @return string Central directory header block
	 * @access private
	 */
	function _buildCentralDirectoryHeader($hexTime, $uncompressed, $crc, $compressed, $fileName, $offset) {
		$cHeader = "\x50\x4b\x01\x02";
		$cHeader .= "\x00\x00";
		$cHeader .= "\x14\x00";
		$cHeader .= "\x00\x00";
		$cHeader .= "\x08\x00";
		$cHeader .= $hexTime;
		/**
		 * central directory header
		 * 0-3 (4) Header signature
		 * 4-5 (2) Version used
		 * 6-7 (2) Version required
		 * 8-9 (2) General purpose flag
		 * 10-11 (2) Compression method
		 * 12-13 (2) Mod time
		 * 14-15 (2) Mod date
		 * 16-19 (2) CRC32
		 * 20-23 (4) Compressed size
		 * 24-27 (4) Uncompressed size
		 * 28-29 (2) File name length
		 * 30-31 (2) Extra
		 * 32-33 (2) File comments length
		 * 34-35 (2) Disk number
		 * 36-37 (2) Internal file attrs
		 * 38-41 (4) External file attrs
		 * 42-45 (4) File offset
		 * 46-... (N) File name
		 */
		return $cHeader . pack("VVVvvvvvVV", $crc, $compressed, $uncompressed, strlen($fileName), 0, 0, 0, 0, 32, $this->lastOffset) . $fileName;
	}

	/**
	 * Parse central directory data from a ZIP stream
	 *
	 * @param string $data Substring of a ZIP stream
	 * @return array ZIP file properties
	 * @access private
	 */
	function _readCentralDirectory($data) {
		$centralInfo = NULL;
		$size = strlen($data);
		// first trial : no file comments
		if ($size > 26) {
			$pos = $size - 18;
			$binHeader = substr($data, $pos-4);
			$arrHeader = unpack("Vid", substr($binHeader, 0, 4));
			if ($arrHeader['id'] == 0x06054b50)
				$centralInfo = unpack("vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size", substr($binHeader, 4));
		}
		// second trial : maximum size of a central directory
		if (is_null($centralInfo)) {
			$maximumSize = 65557; // 22 + 0xffff
			if ($maximumSize > $size)
				$maximumSize = $size;
			$pos = $size - $maximumSize;
			$bytes = 0x00000000;
			while ($pos < $size) {
				$byte = $data{$pos};
				$bytes = ($bytes << 8) | ord($byte);
				if ($bytes == 0x504b0506) {
					$pos++;
					break;
				}
				$pos++;
			}
			if ($pos == $size) {
				return FALSE;
			} else {
				$centralInfo = unpack("vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size", substr($data, $pos, 18));
			}
		}
		// invalid central directory
		if (($pos + 18 + $centralInfo["comment_size"]) != $size)
			return FALSE;
		if ($centralInfo['comment_size'] != 0)
			$centralInfo['comment'] = substr($data, $pos + 18, $centralInfo['comment_size']);
		else
			$centralInfo['comment'] = '';
		return $centralInfo;
	}

	/**
	 * Reads file information from a ZIP central directory
	 *
	 * @param string $data ZIP stream
	 * @param int &$pos Offset
	 * @return array Central file header
	 * @access private
	 */
	function _readCentralFileHeader($data, &$pos) {
		$binHeader = substr($data, $pos, 46);
		$arrHeader = unpack("Vid", substr($binHeader, 0, 4));
		if ($arrHeader['id'] != 0x02014b50)
			return FALSE;
		$pos += 46;
		$arrHeader = unpack('vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', substr($binHeader, 4));
		if ($arrHeader['filename_len'] != 0) {
			$arrHeader['filename'] = substr($data, $pos, $arrHeader['filename_len']);
			$pos += $arrHeader['filename_len'];
		} else {
			$arrHeader['filename'] = '';
		}
		if ($arrHeader['extra_len'] != 0) {
			$arrHeader['extra'] = substr($data, $pos, $arrHeader['extra_len']);
			$pos += $arrHeader['extra_len'];
		} else {
			$arrHeader['extra'] = '';
		}
		if ($arrHeader['comment_len'] != 0) {
			$arrHeader['comment'] = substr($data, $pos, $arrHeader['comment_len']);
			$pos += $arrHeader['comment_len'];
		}
		$arrHeader['mtime'] = $this->_recoverUnixDate($arrHeader['mdate'], $arrHeader['mtime']);
		unset($arrHeader['mdate']);
		if ($arrHeader['filename'][0] == '/')
			$arrHeader['external'] = 0x41FF0010;
		$arrHeader['dir'] = (($arrHeader['external']&0x00000010)==0x00000010);
		return $arrHeader;
	}

	/**
	 * Read a local file header from a given ZIP stream
	 *
	 * @param string $data ZIP stream
	 * @param int &$pos Offset
	 * @return Local file header
	 * @access private
	 */
	function _readLocalFileHeader($data, &$pos) {
		$binHeader = substr($data, $pos, 30);
		$arrHeader = unpack("Vid", substr($binHeader, 0, 4));
		if ($arrHeader['id'] != 0x04034b50)
			return FALSE;
		$pos += 30;
		$arrHeader = unpack('vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', substr($binHeader, 4));
		if ($arrHeader['filename_len'] != 0) {
			$arrHeader['filename'] = substr($data, $pos, $arrHeader['filename_len']);
			$pos += $arrHeader['filename_len'];
		} else {
			$arrHeader['filename'] = '';
		}
		if ($arrHeader['extra_len'] != 0) {
			$arrHeader['extra'] = substr($data, $pos, $arrHeader['extra_len']);
			$pos += $arrHeader['extra_len'];
		} else {
			$arrHeader['extra'] = '';
		}
		$arrHeader['mtime'] = $this->_recoverUnixDate($arrHeader['mdate'], $arrHeader['mtime']);
		unset($arrHeader['mdate']);
		return $arrHeader;
	}

	/**
	 * Converts a DOS date into an UNIX timestamp
	 *
	 * @param string $dosDate DOS date
	 * @param string $dosTime DOS time
	 * @return int UNIX timestamp
	 * @access private
	 */
	function _recoverUnixDate($dosDate, $dosTime) {
		if ($dosDate && $dosTime) {
			$unixHour = ($dosTime &0xF800) >> 11;
			$unixMinute = ($dosTime &0x07E0) >> 5;
			$unixSecond = ($dosTime &0x001F) * 2;
			$unixYear = (($dosDate &0xF800) >> 9) + 1980;
			$unixMonth = ($dosDate &0x07E0) >> 5;
			$unixDay = ($dosDate &0x001F);
			return mktime($unixHour, $unixMinute, $unixSecond, $unixMonth, $unixDay, $unixYear);
		} else {
			return time();
		}
	}
}
?>