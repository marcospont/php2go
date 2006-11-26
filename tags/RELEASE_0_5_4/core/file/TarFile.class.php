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
// $Header: /www/cvsroot/php2go/core/file/TarFile.class.php,v 1.14 2006/05/07 15:07:10 mpont Exp $
// $Date: 2006/05/07 15:07:10 $

//------------------------------------------------------------------
import('php2go.file.FileCompress');
import('php2go.file.GzFile');
import('php2go.net.HttpResponse');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

// @const TAR_MAGIC "ustar"
// Constante utilizada para indicar o formato TAR dos arquivos criados
define('TAR_MAGIC', 'ustar');
// @const TAR_BLOCK_SIZE "512"
// Tamanho em bytes de um bloco de dados no formato TAR
define('TAR_BLOCK_SIZE', 512);
// @const TAR_FILENAME_MAXLENGTH "99"
// Tamanho máximo para nomes de arquivos no formato TAR
define('TAR_FILENAME_MAXLENGTH', 99);
// @const TAR_FILEPATH_MAXLENGTH "154"
// Tamanho máximo para caminhos/prefixos de arquivos no formato TAR
define('TAR_FILEPATH_MAXLENGTH', 154);
// @const TAR_MAX_UID_GID "2097151"
// Valor máximo para os valores de ID de usuário e ID de grupo
define('TAR_MAX_UID_GID', 2097151);

//!-----------------------------------------------------------------
// @class		TarFile
// @desc		Permite a inserção de vários arquivos e diretórios para
// 				a criação de um único arquivo binário no formato TAR
// @extends 	FileCompress
// @package		php2go.file
// @uses 		FileManager
// @uses		GzFile
// @uses		HttpResponse
// @uses		StringUtils
// @author 		Marcos Pont
// @version		$Revision: 1.14 $
//!-----------------------------------------------------------------
class TarFile extends FileCompress
{
	var $tarData = ''; 			// @var tarData string			"" Conteúdo do arquivo TAR
	var $useZip = FALSE; 		// @var useZip bool				"FALSE" Indica se está sendo utilizada compactação
	var $globalAttributes; 		// @var globalAttributes array	Vetor de atributos globais para todos os arquivos inseridos
	var $defaultAttributes = 	// @var defaultAttributes array	Vetor de atributos padrão para os arquivos
	array (
		'mode' => 0,
		'uid' => 0,
		'gid' => 0,
		'time' => 0,
		'type' => 0,
		'link' => '',
		'path' => ''
	);

	//!-----------------------------------------------------------------
	// @function	TarFile::TarFile
	// @desc 		Construtor da classe
	// @param 		cwd string		"" Diretório inicial de trabalho
	// @access 		public
	//!-----------------------------------------------------------------
	function TarFile($cwd = '') {
		parent::FileCompress($cwd);
		$this->defaultAttributes['mode'] = decoct(0x8000 | 0x0100 | 0x0080 | 0x0020 | 0x0004);
		$this->defaultAttributes['time'] = time();
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::setDefaultAttributes
	// @desc		Configura os atributos padrão para os arquivos inseridos
	// @param 		attrs array		Vetor associativo de atributos
	// @access 		public
	// @return		void
	// @note 		Os atributos aceitos são mode, user_id, group_id e modify_time
	//!-----------------------------------------------------------------
	function setDefaultAttributes($attrs) {
		if (TypeUtils::isArray($attrs)) {
			if (isset($attrs['mode'])) $this->defaultAttributes['mode'] = $attrs['mode'];
			if (isset($attrs['uid']) && $attrs['uid'] <= TAR_MAX_UID_GID) $this->defaultAttributes['uid'] = $attrs['uid'];
			if (isset($attrs['gid']) && $attrs['gid'] <= TAR_MAX_UID_GID) $this->defaultAttributes['gid'] = $attrs['gid'];
			if (isset($attrs['time'])) $this->defaultAttributes['time'] = $attrs['time'];
		}
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::setGlobalAttibutes
	// @desc 		Seta os atributos globais a serem atribuídos a todos os arquivos
	// @param 		attrs array		Vetor associativo de atributos
	// @access 		public
	// @return		void
	// @note 		Os atributos são mode e time
	//!-----------------------------------------------------------------
	function setGlobalAttributes($attrs) {
		if (TypeUtils::isArray($attrs)) {
			if (isset($attrs['mode']))
				$this->globalAttributes['mode'] = $attrs['mode'];
			if (isset($attrs['time']))
				$this->globalAttributes['time'] = $attrs['time'];
		}
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::addData
	// @desc		Adiciona dados de um arquivo no TAR
	// @param 		data string		Dados de um arquivo
	// @param 		fileName string	Nome do arquivo
	// @param 		attrs array		Atributos do arquivo
	// @access 		public
	// @return		bool
	// @note		O vetor de atributos deve conter as seguintes entradas :
	//				mode, uid, gid, time, type (0-regular, 1-link, 5-dir), link, path, size
	//!-----------------------------------------------------------------
	function addData($data, $fileName, $attrs) {
		if (strlen($fileName) > TAR_FILENAME_MAXLENGTH || (isset($attrs['path']) && strlen($attrs['path'] > TAR_FILEPATH_MAXLENGTH)))
			return FALSE;
		// configura o vetor de atributos para o arquivo no formato TAR
		$fileAttrs = array(
			'mode' => (isset($this->globalAttributes['mode']) ? $this->globalAttributes['mode'] : (isset($attrs['mode']) ? $attrs['mode'] : $this->defaultAttributes['mode'])),
			'uid' => (isset($attrs['uid']) && $attrs['uid'] <= TAR_MAX_UID_GID ? $attrs['uid'] : $this->defaultAttributes['uid']),
			'gid' => (isset($attrs['gid']) && $attrs['gid'] <= TAR_MAX_UID_GID ? $attrs['gid'] : $this->defaultAttributes['gid']),
			'time' => (isset($this->globalAttributes['time']) ? $this->globalAttributes['time'] : (isset($attrs['time']) ? $attrs['time'] : $this->defaultAttributes['time'])),
			'link' => (isset($attrs['link']) ? $attrs['link'] : $this->defaultAttributes['link']),
			'path' => (isset($attrs['path']) ? $attrs['path'] : $this->defaultAttributes['path']),
			'size' => (isset($attrs['size']) ? $attrs['size'] : strlen($data))
		);
		// configura o nome do arquivo
		if ($this->isPathStorageEnabled()) {
			$fileName = preg_replace('/^(\.{1,2}(\/|\\\))+/', '', $fileName);
		} else {
			$fileName = (StringUtils::match($fileName, '/') ? substr($fileName, strrpos($fileName, '/')+1) : $fileName);
			$fileAttrs['path'] = '';
		}
		// constrói os blocos de início e fim de arquivo e o checksum
		$startBlock = $this->_buildBlockStart($fileName, $fileAttrs);
		$endBlock = $this->_buildBlockEnd($fileAttrs);
		$checkSum = $this->_buildChecksum($startBlock, $endBlock);
		// o tamanho de um arquivo no formato TAR deve ser um múltiplo de 512 bytes
		if (($fileAttrs['size'] % TAR_BLOCK_SIZE) > 0)
			$data .= str_repeat("\0", TAR_BLOCK_SIZE - ($fileAttrs['size'] % TAR_BLOCK_SIZE));
		/**
		 * composição de uma entrada de arquivo no formato TAR
		 * 0-147 (148) Início bloco 0
		 * 148-155 (8) Checksum
		 * 156-499 (344) Fim bloco 0
		 * 500-511 (12) Vazio
		 * 512-... (512*N) N blocos do arquivo
		 */
		$this->tarData .= $startBlock . $checkSum . $endBlock . pack("a12", '') . $data;
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::addFile
	// @desc		Adiciona o conteúdo de um arquivo ao TAR a partir de seu caminho e nome
	// @param 		path string	Caminho completo para o arquivo
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function addFile($path) {
		$filePath = '';
		// verifica se o caminho do arquivo deve ser armazenado
		if ($this->isPathStorageEnabled())
			$fileName = preg_replace('/^(\.{1,2}(\/|\\\))+/', '', $path);
		else
			$fileName = (StringUtils::match($path, '/') ? substr($path, strrpos($path, '/') + 1) : $path);
		// verifica se o nome do arquivo excede o tamanho máximo
		if (strlen($fileName) > TAR_FILENAME_MAXLENGTH) {
			if (($pos = strrpos($fileName, '/')) !== FALSE) {
				$filePath = StringUtils::left($fileName, $pos+1);
				$fileName = substr($fileName, $pos);
			}
			// verifica se os tamanhos de nome/caminho não excedem o máximo permitido
			if (strlen($fileName) > TAR_FILENAME_MAXLENGTH || strlen($filePath) > TAR_FILEPATH_MAXLENGTH)
				return FALSE;
		}
		// abre o arquivo solicitado para leitura
		$Mgr =& FileCompress::getFileManager();
		if (!$Mgr->open($path, FILE_MANAGER_READ_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			// monta o vetor de atributos
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
			// adiciona o conteúdo no arquivo TAR
			$this->addData($Mgr->readFile(), $fileName, $attrs);
			$Mgr->close();
		}
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::extractData
	// @desc		Extrai os arquivos e diretórios inseridos em um .tar
	// @access		public
	// @param		data string		Conteúdo binário de um arquivo .tar
	// @return 		array Vetor contendo nomes e atributos dos arquivos incluídos no arquivo TAR
	//!-----------------------------------------------------------------
	function extractData($data) {
		$position = 0;
		$blockCount = (strlen($data) / TAR_BLOCK_SIZE) - 1;
		FileCompress::debug($blockCount . ' blocks');
		$returnData = array();
		while ($position < $blockCount) {
			FileCompress::debug('file at position ' . $position);
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
			FileCompress::debug("file has $size blocks => initial block : " . ($position+1) . "  - final block : " . ($position+1+$size));
			FileCompress::debug('header : ' . exportVariable($file, TRUE));
			$file['data'] = substr($data, TAR_BLOCK_SIZE * (++$position), $file['size']);

			$position += $size;
			$returnData[] = $file;
		}
		FileCompress::debug(sizeof($returnData) . ' files + folders');
		return $returnData;
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::getData
	// @desc		Retorna o conteúdo do arquivo .tar construído
	// @access		public
	// @return		string Conteúdo do arquivo TAR
	//!-----------------------------------------------------------------
	function getData() {
		return $this->tarData . pack("a512", '');
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::downloadFile
	// @desc		Imprime os headers de formato de arquivo para permitir
	// 				o download do arquivo .tar montado
	// @access		public
	// @param		fileName string	Nome do arquivo a ser enviado ao usuário
	// @return		void
	//!-----------------------------------------------------------------
	function downloadFile($fileName) {
		if (!HttpResponse::headersSent()) {
			HttpResponse::download($fileName, strlen($this->getData()));
			print $this->getData();
		}
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::extractGzip
	// @desc		Extrai o conteúdo de um arquivo TAR compactado no formato GZIP
	// @access		public
	// @param		fileName string	Nome do arquivo TAR compactado
	// @return		array Vetor contendo os nomes e os atributos dos arquivos incluídos no arquivo TAR
	//!-----------------------------------------------------------------
	function extractGzip($fileName) {
		if (FileSystem::exists($fileName) && $fp = @gzopen($fileName, 'rb')) {
			$buffer = '';
			while (!gzeof($fp))
				$buffer .= gzread($fp, 1024);
			gzclose($fp);
			return $this->extractData($buffer);
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::downloadGzip
	// @desc 		Cria uma versão compactada no padrão GZIP do arquivo TAR,
	// 				imprimindo os headers apropriados e o seu conteúdo
	// @access 		public
	// @param 		tarName string	Nome para o arquivo TAR
	// @return		void
	// @note 		A extensão .gz será inserida automaticamente por este método
	//!-----------------------------------------------------------------
	function downloadGzip($tarName) {
		$Gz =& FileCompress::getInstance('gz');
		$Gz->addData($this->getData(), $tarName, array('time' => time()));
		if (!StringUtils::endsWith($tarName, '.gz') && !StringUtils::endsWith($tarName, '.gzip') && !StringUtils::endsWith($tarName, '.tgz'))
			$tarName .= '.gz';
		$Gz->downloadFile($tarName);
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::saveGzip
	// @desc 		Cria uma versão compactada no padrão GZIP do arquivo TAR,
	// 				salvando no arquivo indicado em $tarName o conteúdo
	// @access 		public
	// @param 		tarName string	Nome para o arquivo TAR
	// @param		mode int		"NULL" Modo de criação do arquivo tar.gz
	// @return		void
	//!-----------------------------------------------------------------
	function saveGzip($tarName, $mode = NULL) {
		$Gz =& FileCompress::getInstance('gz');
		$Gz->addData($this->getData(), $tarName, array('time' => time()));
		if (!StringUtils::endsWith($tarName, '.gz') && !StringUtils::endsWith($tarName, '.gzip') && !StringUtils::endsWith($tarName, '.tgz'))
			$tarName .= '.gz';
		$Gz->saveFile($tarName, $mode);
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::_buildBlockStart
	// @desc 		Constrói a parte inicial do primeiro bloco do arquivo
	// @access 		private
	// @param 		fileName string	Nome do arquivo
	// @param 		fileAttrs array	Atributos do arquivo
	// @return 		string Conteúdo binário da parte inicial do primeiro bloco
	//!-----------------------------------------------------------------
	function _buildBlockStart($fileName, $fileAttrs) {
		$fUid = sprintf("%6s", decoct($fileAttrs['uid']));
		$fGid = sprintf("%6s", decoct($fileAttrs['gid']));
		$fSize = sprintf("%11s ", decoct($fileAttrs['size']));
		$fTime = sprintf("%11s ", decoct($fileAttrs['time']));
		/**
		 * parte inicial do bloco 0 de um arquivo
		 * 0-99 (100) Nome do arquivo
		 * 100-107 (8) Modo do arquivo, em ASCII octal
		 * 108-115 (8) UID, em ASCII octal
		 * 116-123 (8) GID, em ASCII octal
		 * 124-135 (12) Tamanho do arquivo, em ASCII octal
		 * 136-147 (12) Modify time do arquivo, em ASCII octal
		 */
		return pack("a100a8a8a8a12a12", $fileName, $fileAttrs['mode'], $fUid, $fGid, $fSize, $fTime);
	}

	//!-----------------------------------------------------------------
	// @function 	TarFile::_buildBlockEnd
	// @desc 		Monta a parte final do bloco 0 de um arquivo
	// @access 		private
	// @param 		fileAttrs array	Atributos do arquivo
	// @return 		string Conteúdo binário da parte final do primeiro bloco
	//!-----------------------------------------------------------------
	function _buildBlockEnd($fileAttrs) {
		/**
		 * parte final do bloco 0 de um arquivo
		 * 156 (1) Flag de tipo de arquivo
		 * 157-256 (100) Caminho do link, para links simbólicos
		 * 257-262 (6) MAGIC ("ustar", formato TAR utilizado)
		 * 263-264 (2) Versão
		 * 265-296 (32) Nome do owner do arquivo
		 * 297-328 (32) Nome do grupo do owner
		 * 329-336 (8) Major device ID
		 * 337-344 (8) Minor devide ID
		 * 345-499 (155) Prefixo do nome do arquivo
		 */
		return pack("a1a100a6a2a32a32a8a8a155", $fileAttrs['type'], $fileAttrs['link'], TAR_MAGIC, "00", "Unknown", "Unknown", "", "", $fileAttrs['path']);
	}

	//!-----------------------------------------------------------------
	// @function	TarFile::_buildChecksum
	// @desc 		Calcula a checksum do primeiro bloco de um arquivo
	// @access 		private
	// @param 		blockStart string	String binária da parte inicial do bloco 0
	// @param 		blockEnd string		String binária da parte final do bloco 0
	// @return 		string Checksum em formato binário
	//!-----------------------------------------------------------------
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