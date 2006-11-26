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
// $Header: /www/cvsroot/php2go/core/file/GzFile.class.php,v 1.13 2006/03/15 04:43:23 mpont Exp $
// $Date: 2006/03/15 04:43:23 $

//------------------------------------------------------------------
import('php2go.file.FileCompress');
import('php2go.net.HttpResponse');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class 		GzFile
// @desc 		Permite a compactação de arquivos utilizando o formato GZIP (GNU Zip)
// @package		php2go.file
// @extends 	FileCompress
// @uses 		FileManager
// @uses		HttpResponse
// @uses		StringUtils
// @author 		Marcos Pont
// @version		$Revision: 1.13 $
//!-----------------------------------------------------------------
class GzFile extends FileCompress
{
	var $gzData;	// @var gzData string	Dados do arquivo .gz
	var $level = 9;	// @var level int		Nivel de compressão a ser utilizado

	//!-----------------------------------------------------------------
	// @function 	GzFile::GzFile
	// @desc 		Executa o construtor da classe superior (FileCompress)
	// @access 		public
	//!-----------------------------------------------------------------
	function GzFile() {
		parent::FileCompress();
		$this->gzData = "";
	}

	//!-----------------------------------------------------------------
	// @function 	GzFile::setCompressionLevel
	// @desc 		Configura o nível do algoritmo de compactação
	// @access 		public
	// @param 		level int			Nível de comptactação
	// @return		void
	//!-----------------------------------------------------------------
	function setCompressionLevel($level) {
		$this->level = $level <= 9 ? max(1, (int)$level): 9;
	}

	//!-----------------------------------------------------------------
	// @function 	GzFile::addData
	// @desc 		Adiciona dados binários de um arquivo ao .gz
	// @access 		public
	// @param 		data string		Dados de um arquivo
	// @param 		fileName string   "NULL" Nome do arquivo
	// @param 		fileAttrs array	"array()" Atributos do arquivo
	// @return		void
	// @note 		Conjunto de atributos: time e comment
	//!-----------------------------------------------------------------
	function addData($data, $fileName = NULL, $fileAttrs = array()) {
		$fileFlags = bindec('000' . (isset($fileAttrs['comment']) ? '1' : '0') . ($fileName != NULL ? '1' : '0') . '000');
		$this->gzData = $this->_buildFileHeader($fileFlags, $fileAttrs['time']);
		if ($fileName != NULL)
			$this->gzData .= $fileName . "\0";
		if (isset($fileAttrs['comment']))
			$this->gzData .= $fileAttrs['comment'] . "\0";
		$this->gzData .= gzdeflate($data, $this->level);
		$this->gzData .= pack("VV", crc32($data), strlen($data));
	}

	//!-----------------------------------------------------------------
	// @function 	GzFile::addFile
	// @desc 		Adiciona um arquivo ao .gz
	// @access 		public
	// @param 		fileName string	Nome do arquivo
	// @param		comment string	"NULL" Comentário para o arquivo
	// @return		bool
	//!-----------------------------------------------------------------
	function addFile($fileName, $comment = NULL) {
		$Mgr =& FileCompress::getFileManager();
		if (!$Mgr->open($fileName, FILE_MANAGER_READ_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$fileName = (StringUtils::match($fileName, '/') ? substr($fileName, strrpos($fileName, '/') + 1) : $fileName);
			$attrs['time'] = $Mgr->getAttribute('mTime');
			if (!TypeUtils::isNull($comment))
				$attrs['comment'] = $comment;
			$this->addData($Mgr->readFile(), $fileName, $attrs);
			$Mgr->close();
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	GzFile::extractData
	// @desc 		Extrai o conteúdo do arquivo compactado
	// @access 		public
	// @param 		data string		Conteúdo do arquivo .gz
	// @return 		array Vetor contendo os dados do arquivo ou false em caso de erros
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function 	GzFile::getData
	// @desc 		Retorna o conteúdo do arquivo .gz construído
	// @access 		public
	// @return 		string Conteúdo do arquivo GZIP
	//!-----------------------------------------------------------------
	function getData() {
		return $this->gzData;
	}

	//!-----------------------------------------------------------------
	// @function 	GzFile::downloadFile
	// @desc 		Imprime os headers de formato de arquivo para permitir
	// 				o download do arquivo .gz montado
	// @access 		public
	// @param 		fileName string	Nome do arquivo a ser enviado ao usuário
	// @return		void
	//!-----------------------------------------------------------------
	function downloadFile($fileName) {
		if (!HttpResponse::headersSent()) {
			HttpResponse::download($fileName, strlen($this->getData()));
			print $this->getData();
		}
	}

	//!-----------------------------------------------------------------
	// @function 	GzFile::_buildFileHeader
	// @desc 		Constrói o cabeçalho de um arquivo no formato GZIP
	// @access 		private
	// @param 		flags int			Flags de formatação do arquivo
	// @param 		mtime int			"0" Timestamp de modificação do arquivo
	// @return		string Cabeçalho do arquivo GZIP em formato binário
	//!-----------------------------------------------------------------
	function _buildFileHeader($flags, $mtime = 0) {
		if (!$mtime) $mtime = time();
		// Formato do cabeçalho de arquivo :
		// POS    TAM       DESC
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