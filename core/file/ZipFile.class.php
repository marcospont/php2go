<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/core/file/ZipFile.class.php,v 1.14 2006/05/07 15:06:10 mpont Exp $
// $Date: 2006/05/07 15:06:10 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
import('php2go.file.FileCompress');
import('php2go.file.FileManager');
import('php2go.net.HttpResponse');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		ZipFile
// @desc		Esta classe permite compactar um ou v�rios arquivos
// 				e diret�rios utilizando o formato .zip e extrair arquivos
// 				comptactados
// @package		php2go.file
// @extends 	FileCompress
// @uses 		FileManager
// @uses		StringUtils
// @uses		System
// @author 		Marcos Pont
// @version		$Revision: 1.14 $
//!-----------------------------------------------------------------
class ZipFile extends FileCompress
{
	var $defaultTime; 			// @var defaultTime int			Timestamp padr�o
	var $globalTime; 			// @var globalTime int			Timestamp geral para todos os arquivos
	var $level; 				// @var level int				N�vel utilizado na compress�o dos arquivos
	var $lastOffset; 			// @var lastOffset int			Ponteiro para o arquivo no diret�rio central
	var $zipData = array(); 	// @var zipData array			"array()" Vetor contendo os arquivos
	var $centralData = array(); // @var centralData array		"array()" Diret�rio central reunindo informa��es sobre os arquivos
	var $centralDataEof; 		// @var centralDataEof string	Seq��ncia final do diret�rio central

	//!-----------------------------------------------------------------
	// @function	ZipFile::ZipFile
	// @desc		Executa o construtor da classe superior (FileCompress)
	// 				e configura as propriedades da classe
	// @access		public
	// @param		cwd string	"" Diret�rio inicial de trabalho
	//!-----------------------------------------------------------------
	function ZipFile($cwd = '') {
		parent::FileCompress($cwd);
		if (!System::loadExtension('zlib'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'zlib'), E_USER_ERROR, __FILE__, __LINE__);
		$this->defaultTime = time();
		$this->centralDataEof = "\x50\x4b\x05\x06\x00\x00\x00\x00";
		$this->lastOffset = 0;
		$this->level = 9;
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::setGlobalTime
	// @desc		Seta o timestamp que deve ser associado a todos os arquivos
	// @access		public
	// @param		time int		Timestamp para todos os arquivos
	// @return		void
	//!-----------------------------------------------------------------
	function setGlobalTime($time) {
		$this->globalTime = $time;
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::setCompressionLevel
	// @desc		Configura o n�vel do algoritmo de compacta��o
	// @access		public
	// @param		level int		N�vel de comptacta��o
	// @return		void
	//!-----------------------------------------------------------------
	function setCompressionLevel($level) {
		$this->level = $level <= 9 ? max(1, TypeUtils::parseIntegerPositive($level)): 9;
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::addData
	// @desc		Adiciona dados ao arquivo ZIP
	// @access		public
	// @param		data string		Dados de um arquivo
	// @param 		fileName string	Nome do arquivo
	// @param 		attrs array		Atributos do arquivo
	// @return		void
	//!-----------------------------------------------------------------
	function addData($data, $fileName, $attrs) {
		// monta a data/hora de modifica��o do arquivo
		$time = (isset($this->globalTime) ? $this->globalTime : (TypeUtils::isArray($attrs) && isset($attrs['time']) ? $attrs['time'] : $this->defaultTime));
		$decTime = dechex(Date::fromUnixToDosDate($time));
		$hexTime = '\x' . $decTime[6] . $decTime[7] . '\x' . $decTime[4] . $decTime[5] . '\x' . $decTime[2] . $decTime[3] . '\x' . $decTime[0] . $decTime[1];
		eval('$hexTime = "' . $hexTime . '";');
		// configura o nome do arquivo de acordo com a configura��o de armazenamento de caminhos
		if ($this->isPathStorageEnabled())
			$fileName = preg_replace("/^(\.{1,2}(\/|\\\))+/", "", $fileName);
		else
			$fileName = StringUtils::match($fileName, '/') ? substr($fileName, strrpos($fileName, '/') + 1) : $fileName;
		// compacta os dados, calculando os tamanhos e o CRC 32
		$uncompressed = strlen($data);
		$crc = crc32($data);
		$zipData = gzcompress($data, $this->level);
		$zipData = substr($zipData, 2, strlen($zipData)-6);
		$compressed = strlen($zipData);
		// monta o cabe�alho e o descritor do arquivo
		$fileHeader = $this->_buildLocalFileHeader($hexTime, $uncompressed, $crc, $compressed, $fileName);
		$fileDesc = $this->_buildFileDescriptor($crc, $compressed, $uncompressed);
		// adiciona o arquivo
		$this->zipData[] = $fileHeader . $zipData . $fileDesc;
		// adiciona uma entrada no diret�rio central
		$offset = strlen(implode('', $this->zipData));
		$centralHeader = $this->_buildCentralDirectoryHeader($hexTime, $uncompressed, $crc, $compressed, $fileName, $offset);
		$this->lastOffset = $offset;
		$this->centralData[] = $centralHeader;
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::addFile
	// @desc		Adiciona o conte�do de um arquivo ao ZIP, a partir de seu caminho completo
	// @access		public
	// @param		fileName string	Caminho completo do arquivo
	// @return		void
	//!-----------------------------------------------------------------
	function addFile($fileName) {
		// configura o nome do arquivo, de acordo com a configura��o de armazenamento de caminhos
		if ($this->isPathStorageEnabled())
			$fileName = preg_replace("/^(\.{1,2}(\/|\\\))+/", "", $fileName);
		else
			$fileName = StringUtils::match($fileName, '/') ? substr($fileName, strrpos($fileName, '/') + 1) : $fileName;
		// abre o arquivo para leitura
		$Mgr =& FileCompress::getFileManager();
		if (!$Mgr->open($fileName, FILE_MANAGER_READ_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			// busca o mtime do arquivo
			$attrs['time'] = $Mgr->getAttribute('mTime');
			// adiciona seu conte�do no ZIP
			$this->addData($Mgr->readFile(), $fileName, $attrs);
			$Mgr->close();
		}
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::extractData
	// @desc		Extrai o conte�do inserido em um arquivo .zip
	// @access		public
	// @param		data string		Conte�do bin�rio de um arquivo .zip
	// @return		array Vetor contendo os arquivos extra�dos e seus atributos
	// 				ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function extractData($data) {
		$returnData = array();
		if (($centralInfo = $this->_readCentralDirectory($data)) !== FALSE) {
			FileCompress::debug('central directory found - data : ' . exportVariable($centralInfo, TRUE));
			$positionZip = 0;
			$positionCtrl = $centralInfo['offset'];
			for ($i=0, $size=$centralInfo['entries']; $i<$size; $i++) {
				if ($fileCentralHeader = $this->_readCentralFileHeader($data, $positionCtrl)) {
					FileCompress::debug('file found - central header : ' . exportVariable($fileCentralHeader, TRUE));
					$positionZip = $fileCentralHeader['offset'];
					if ($fileLocalHeader = $this->_readLocalFileHeader($data, $positionZip)) {
						FileCompress::debug('file found - local header : ' . exportVariable($fileLocalHeader, TRUE));
						$zipOffset = $positionZip;
						$zipData = substr($data, $zipOffset, $fileLocalHeader['compressed_size']);
						$fileData = gzinflate($zipData);
						if ($fileLocalHeader['crc'] != crc32($fileData)) {
							FileCompress::debug($fileLocalHeader['filename'] . ' : CRC error');
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
						FileCompress::debug('error reading local file header');
						break;
					}
				} else {
					FileCompress::debug('error reading central file header');
					break;
				}
			}
		} else {
			FileCompress::debug('invalid archive : central directory not found');
		}
		FileCompress::debug(sizeof($returnData) . ' files found');
		return $returnData;
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::getData
	// @desc		Retorna o conte�do do arquivo .zip constru�do
	// @access		public
	// @return		string Conte�do do arquivo ZIP
	//!-----------------------------------------------------------------
	function getData() {
		$zipData = implode('', $this->zipData);
		$ctrlData = implode('', $this->centralData);
		return $zipData . $ctrlData . $this->centralDataEof . pack("vvVV", sizeof($this->centralData), sizeof($this->centralData), strlen($ctrlData), strlen($zipData)) . "\x00\x00";
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::downloadFile
	// @desc		Imprime os headers de formato de arquivo para permitir
	// 				o download do arquivo .zip montado
	// @access		public
	// @param		fileName string	Nome do arquivo a ser enviado ao usu�rio
	// @return		void
	//!-----------------------------------------------------------------
	function downloadFile($fileName) {
		if (!HttpResponse::headersSent()) {
			HttpResponse::download($fileName, strlen($this->getData()));
			print $this->getData();
		}
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::_buildLocalFileHeader
	// @desc		Constr�i o cabe�alho de um arquivo
	// @access		private
	// @param		hexTime string	Data e hora de modifica��o
	// @param 		uncompressed int	Tamanho original do arquivo
	// @param 		crc int			Cyclic Redundancy Check do arquivo
	// @param 		compressed int	Tamanho compactado do arquivo
	// @param 		fileName string	Nome do arquivo
	// @return 		string Conte�do do cabe�alho do arquivo
	//!-----------------------------------------------------------------
	function _buildLocalFileHeader($hexTime, $uncompressed, $crc, $compressed, $fileName) {
		$fHeader = "\x50\x4b\x03\x04";
		$fHeader .= "\x14\x00";
		$fHeader .= "\x00\x00";
		$fHeader .= "\x08\x00";
		$fHeader .= $hexTime;
		/**
		 * cabe�alho local de arquivo
		 * 0-3 (4) Assinatura do cabe�alho do arquivo
		 * 4-5 (2) Vers�o exigida para extra��o do arquivo
		 * 6-7 (2) Flag general purpose
		 * 8-9 (2) M�todo de compress�o
		 * 10-11 (2) Mod time
		 * 12-13 (2) Mod date
		 * 14-17 (4) CRC do arquivo
		 * 18-21 (4) Tamanho do arquivo compactado
		 * 22-25 (4) Tamanho original
		 * 26-27 (2) Tamanho do nome do arquivo
		 * 28-29 (2) Extra
		 * 30-... (N) N bytes contendo o nome do arquivo
		 */
		return $fHeader . pack("VVVvv", $crc, $compressed, $uncompressed, strlen($fileName), 0) . $fileName;
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::_buildFileDescriptor
	// @desc 		Constr�i o descritor de um arquivo
	// @access 		private
	// @param 		crc int			Cyclic Redundancy Check do arquivo
	// @param 		compressed int	Tamanho compactado
	// @param 		uncompressed int	Tamanho original
	// @return 		string Descritor do arquivo
	//!-----------------------------------------------------------------
	function _buildFileDescriptor($crc, $compressed, $uncompressed) {
		/**
		 * descritor de arquivo
		 * 0-3 (4) CRC do arquivo
		 * 4-7 (4) Tamanho compactado
		 * 8-11 (4) Tamanho original
		 */
		return pack("VVV", $crc, $compressed, $uncompressed);
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::_buildCentralDirectoryHeader
	// @desc 		Constr�i as informa��es de um arquivo inseridas
	// 				no diret�rio central do arquivo .zip
	// @access 		private
	// @param 		hexTime string	Data e hora de modifica��o
	// @param 		uncompressed int	Tamanho original
	// @param 		crc int			CRC do arquivo
	// @param 		compressed int	Tamanho compactado
	// @param 		fileName string	Nome do arquivo
	// @param 		offset int		Posi��o inicial do arquivo
	// @return 		string Seq��ncia de caracteres com as informa��es do arquivo
	// 				a serem inseridas no diret�rio central
	//!-----------------------------------------------------------------
	function _buildCentralDirectoryHeader($hexTime, $uncompressed, $crc, $compressed, $fileName, $offset) {
		$cHeader = "\x50\x4b\x01\x02";
		$cHeader .= "\x00\x00";
		$cHeader .= "\x14\x00";
		$cHeader .= "\x00\x00";
		$cHeader .= "\x08\x00";
		$cHeader .= $hexTime;
		/**
		 * cabe�alho do diret�rio central
		 * 0-3 (4) Assinatura do cabe�alho do diret�rio central
		 * 4-5 (2) Vers�o utilizada na compacta��o
		 * 6-7 (2) Vers�o necess�ria para extra��o
		 * 8-9 (2) Flag general purpose
		 * 10-11 (2) M�todo de compress�o
		 * 12-13 (2) Mod time
		 * 14-15 (2) Mod date
		 * 16-19 (2) CRC 32
		 * 20-23 (4) Tamanho compactado
		 * 24-27 (4) Tamanho original
		 * 28-29 (2) Tamanho do nome do arquivo
		 * 30-31 (2) Extra
		 * 32-33 (2) Tamanho do coment�rio do arquivo
		 * 34-35 (2) N�mero do disco inicial
		 * 36-37 (2) Atributos internos do arquivo
		 * 38-41 (4) Atributos externos do arquivo
		 * 42-45 (4) Offset relativo do cabe�alho local
		 * 46-... (N) N bytes contendo o nome do arquivo
		 */
		return $cHeader . pack("VVVvvvvvVV", $crc, $compressed, $uncompressed, strlen($fileName), 0, 0, 0, 0, 32, $this->lastOffset) . $fileName;
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::_readCentralDirectory
	// @desc		Procura os dados do diret�rio central no arquivo ZIP
	// @access		private
	// @param		data string		Dados do arquivo ZIP
	// @return		mixed Vetor contendo os dados do diret�rio central ou FALSE se
	//				n�o foi poss�vel ler as informa��es (formato inv�lido)
	//!-----------------------------------------------------------------
	function _readCentralDirectory($data) {
		$centralInfo = NULL;
		$size = strlen($data);
		// primeira tentativa : arquivo sem coment�rios, central directory nos �ltimos 22 bytes
		if ($size > 26) {
			$pos = $size - 18;
			$binHeader = substr($data, $pos-4);
			$arrHeader = unpack("Vid", substr($binHeader, 0, 4));
			if ($arrHeader['id'] == 0x06054b50)
				$centralInfo = unpack("vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size", substr($binHeader, 4));
		}
		// segunda tentativa : tamanho m�ximo do central directory
		if (TypeUtils::isNull($centralInfo)) {
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
		// falha no conte�do do central directory
		if (($pos + 18 + $centralInfo["comment_size"]) != $size)
			return FALSE;
		if ($centralInfo['comment_size'] != 0)
			$centralInfo['comment'] = substr($data, $pos + 18, $centralInfo['comment_size']);
		else
			$centralInfo['comment'] = '';
		return $centralInfo;
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::_readCentralFileHeader
	// @desc		L� as informa��es do header central de um arquivo
	// @access		private
	// @param		data string		Dados do arquivo ZIP
	// @param		&pos int		Posi��o atual de leitura
	// @return		mixed Dados do arquivo ou FALSE em caso de erros
	//!-----------------------------------------------------------------
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
		if (StringUtils::startsWith($arrHeader['filename'], '/'))
			$arrHeader['external'] = 0x41FF0010;
		$arrHeader['dir'] = (($arrHeader['external']&0x00000010)==0x00000010);
		return $arrHeader;
	}

	//!-----------------------------------------------------------------
	// @function	ZipFile::_readLocalFileHeader
	// @desc		L� o cabe�alho local de um arquivo contido no archive
	// @access		private
	// @param		data string		Dados do arquivo ZIP
	// @param		&pos int		Posi��o inicial a ser lida
	// @return		mixed Vetor com dados de um arquivo ou FALSE em caso de erros
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	ZipFile::_recoverUnixDate
	// @desc		Traduz um data/hora no formato DOS associada a um
	// 				arquivo compactado para a correspondente data/hora Unix
	// @access		private
	// @param		dosDate string	Data no formato DOS
	// @param		dosTime string	Hora no formato DOS
	// @return		string Data/hora convertida
	//!-----------------------------------------------------------------
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