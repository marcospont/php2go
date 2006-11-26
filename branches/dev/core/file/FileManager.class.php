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
// $Header: /www/cvsroot/php2go/core/file/FileManager.class.php,v 1.17 2006/10/26 04:27:28 mpont Exp $
// $Date: 2006/10/26 04:27:28 $

//-----------------------------------------
import('php2go.file.FileSystem');
//-----------------------------------------

// @const FILE_MANAGER_READ "r"
// Definiчуo de modo de leitura normal
define('FILE_MANAGER_READ', 'r');
// @const FILE_MANAGER_READ_BINARY "rb"
// Definiчуo de modo de leitura binсrio
define('FILE_MANAGER_READ_BINARY', 'rb');
// @const FILE_MANAGER_WRITE "w"
// Constante que define modo de escrita simples
define('FILE_MANAGER_WRITE', 'w');
// @const FILE_MANAGER_WRITE_BINARY	"wb"
// Define o modo de escrita binсrio
define('FILE_MANAGER_WRITE_BINARY',	'wb');
// @const FILE_MANAGER_APPEND "a"
// Definiчуo para modo de concatenaчуo
define('FILE_MANAGER_APPEND', 'a');
// @const FILE_MANAGER_APPEND_BINARY "ab"
// Definiчуo para o modo de concatenaчуo binсrio
define('FILE_MANAGER_APPEND_BINARY', 'ab');
// @const FILE_MANAGER_DEFAULT_BLOCK "512"
// Quantidade padrуo de bytes a serem lidos de um arquivo a cada vez
define('FILE_MANAGER_DEFAULT_BLOCK', 512);

//!-----------------------------------------------------------------
// @class		FileManager
// @desc		Esta classe implementa as funчѕes de manipulaчуo de
//				arquivos no servidor, como criaчуo, leitura, escrita
//				e concatenaчуo
// @package		php2go.file
// @extends		FileSystem
// @author		Marcos Pont
// @version		$Revision: 1.17 $
//!-----------------------------------------------------------------
class FileManager extends FileSystem
{
	var $currentFile;			// @var currentFile resource	Variсvel que armazena o ponteiro do arquivo atualmente aberto
	var $currentPath;			// @var currentPath string		Caminho do arquivo atualmente aberto
	var $currentMode;			// @var currentMode int			Modo de abertura do arquivo ativo
	var $currentAttrs;			// @var currentAttrs array		Vetor de atributos do arquivo aberto
	var $throwErrors = TRUE;	// @var throwErrors bool		"TRUE" Indica se os erros capturados devem ser tratados

	//!-----------------------------------------------------------------
	// @function	FileManager::FileManager
	// @desc		Cria o objeto FileManager de manipulaчуo de arquivos
	// @access		public
	//!-----------------------------------------------------------------
	function FileManager() {
		parent::FileSystem();
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::__destruct
	// @desc		Destrutor do objeto. Fecha todos os ponteiros para
	//				arquivo se estiverem abertos e libera todos os locks
	//				de arquivo obtidos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		$this->closeAll();
		$locks = FileManager::getLocks();
		if (!empty($locks)) {
			foreach($locks as $pointer) {
				@flock($pointer, LOCK_UN);
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::&getPointers
	// @desc		Busca os ponteiros de arquivo associados ao objeto
	// @access		public
	// @return		array Vetor contendo os ponteiros de arquivo atualmente abertos
	// @note		Este mщtodo armazena os ponteiros para arquivo em uma variсvel estсtica
	//!-----------------------------------------------------------------
	function &getPointers() {
		static $pointers;
		if (!isset($pointers)) {
			$pointers = array();
		}
		return $pointers;
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::&getLocks
	// @desc		Busca os locks de arquivo associados ao objeto
	// @access		public
	// @return		array Vetor contendo os locks de arquivo concedidos
	// @note		Este mщtodo armazena os locks em uma variсvel estсtica
	//!-----------------------------------------------------------------
	function &getLocks() {
		static $locks;
		if (!isset($locks)) {
			$locks = array();
		}
		return $locks;
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::getCurrentPointer
	// @desc		Busca o ponteiro de arquivo atualmente ativo
	// @access		public
	// @return		resource Ponteiro do arquivo atualmente aberto ou FALSE se
	//				nуo houver arquivo(s) aberto(s)
	//!-----------------------------------------------------------------
	function getCurrentPointer() {
		return isset($this->currentFile) && is_resource($this->currentFile) ? $this->currentFile : FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::open
	// @desc		Abre o arquivo indicado em $filePath com o modo $mode.
	// 				Permite ativar lock de leitura ou exclusivo no arquivo
	//				aberto
	// @access		public
	// @param		filePath string	Caminho do arquivo
	// @param		mode string		"FILE_MANAGER_READ_BINARY" Modo de abertura do arquivo,
	//									de acordo com a especificaчуo da
	//									funчуo fopen() do PHP, padrуo щ leitura byte a byte
	// @param		lockFile mixed	"FALSE" Se for fornecido, criarс um lock no
	//									arquivo aberto do tipo informado pelo
	//									parтmetro: leitura (LOCK_SH) ou exclusivo (LOCK_EX)
	// @return		bool
	//!-----------------------------------------------------------------
	function open($filePath, $mode = FILE_MANAGER_READ_BINARY, $lockFile = FALSE) {
		$attrs = FileSystem::getFileAttributes($filePath);
		$dirAttrs = FileSystem::getFileAttributes(dirname($filePath));
		// busca os pointers de arquivo
		$pointers = &$this->getPointers();
		if (!isset($pointers[$filePath][$mode]) || !is_resource($pointers[$filePath][$mode])) {
			// valida o modo de leitura
			if ($this->_isRead($mode) && !preg_match('/^(http|https|ftp|php):\/\//i', $filePath) && !$attrs) {
				if ($this->throwErrors) 
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			// valida o modo de escrita ou concatenaчуo
			if ($this->_isWrite($mode) || $this->_isAppend($mode)) {
				if (!FileSystem::exists($filePath)) {
					if (!$dirAttrs['isWriteable']) {
						if ($this->throwErrors) 
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_CREATE_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
				} else {
					if (!$attrs['isWriteable']) {
						if ($this->throwErrors) 
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
				}
			}
			// cria o ponteiro para o arquivo com o modo indicado
			$pointers[$filePath][$mode] = @fopen($filePath, $mode);
			if ($pointers[$filePath][$mode] === FALSE) {
				if ($this->throwErrors) 
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_OPEN_FILE', array($filePath, $mode)), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
		}
  		if ($lockFile == LOCK_SH || $lockFile == LOCK_EX) {
   			$this->lock($pointers[$filePath][$mode], $lockFile);
		}
  		$this->currentFile = &$pointers[$filePath][$mode];
  		$this->currentPath = $filePath;
		$this->currentMode = $mode;
  		$this->currentAttrs = &$attrs;
  		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::changeFile
	// @desc		Alterna entre os arquivos abertos na classe
	// @access		public
	// @param		filePath string	Caminho do arquivo
	// @param		mode string		Modo utilizado na abertura do arquivo
	// @return		bool
	//!-----------------------------------------------------------------
	function changeFile($filePath, $mode) {
		$pointers = &$this->getPointers();
		if (!isset($pointers[$filePath][$mode]) || !is_resource($pointers[$filePath][$mode])) {
			return FALSE;
		} else {
			$this->currentFile =& $pointers[$filePath][$mode];
			$this->currentPath = $filePath;
			$this->currentMode = $mode;
			$this->currentAttrs = FileSystem::getFileAttributes($filePath);
			return TRUE;
		}	
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::getCurrentPosition
	// @desc		Retorna a posiчуo atual do ponteiro de arquivo ativo
	// @access		public
	// @return		int Posiчуo do ponteiro no stream do arquivo ou FALSE se nуo hс ponteiro ativo na classe
	//!-----------------------------------------------------------------
	function getCurrentPosition() {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			$fp =& $this->currentFile;
			return ftell($fp);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::getAttribute
	// @desc		Retorna o valor de um atributo do arquivo
	// @access		public
	// @param		attributeName string	Nome do atributo consultado
	// @return		mixed Valor do atributo ou FALSE em caso de erros
	// @see			FileSystem::getFileAttributes
	// @note		Os atributos possэveis sуo os mesmos retornados pelo
	//				mщtodo getFileAttributes da classe FileSystem
	//!-----------------------------------------------------------------
	function getAttribute($attributeName) {
		if (!isset($this->currentFile) || !is_resource($this->currentFile))
			return FALSE;
		else
			return isset($this->currentAttrs[$attributeName]) ? $this->currentAttrs[$attributeName] : FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::getAttributes
	// @desc		Retorna os atributos do arquivo atualmente ativo, se existir
	// @access		public
	// @return		mixed Vetor de atributos do arquivo ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function getAttributes() {
		if (!isset($this->currentFile) || !is_resource($this->currentFile))
			return FALSE;
		else
			return $this->currentAttrs;
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::read
	// @desc		Lъ uma quantidade de bytes do arquivo atual, permitindo
	//				bloqueс-lo para leitura
	// @access		public
	// @param		size int		"FILE_MANAGER_DEFAULT_BLOCK" Nњmero de bytes a serem lidos do arquivo
	// @param		lockFile mixed	"FALSE" Indica o tipo de bloqueio a ser aplicado no arquivo
	// @return		string Conteњdo lido do arquivo ou FALSE em caso de erros
	// @see			FileManager::readChar
	// @see			FileManager::readLine
	// @see			FileManager::readFile
	//!-----------------------------------------------------------------
	function read($size=FILE_MANAGER_DEFAULT_BLOCK, $lockFile=FALSE) {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			$fp =& $this->currentFile;
			if ($lockFile == LOCK_SH || $lockFile == LOCK_EX) {
				$this->lock($fp, $lockFile);
			}
			return feof($fp) ? FALSE : fread($fp, max(intval($size), 1));
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::readChar
	// @desc		Lъ um byte do arquivo atual
	// @access		public
	// @param		lockFile mixed	"FALSE" Indica o tipo de bloqueio a ser aplicado no arquivo
	// @return		string
	// @see			FileManager::read
	// @see			FileManager::readLine
	// @see			FileManager::readFile
	//!-----------------------------------------------------------------
	function readChar($lockFile = FALSE) {
		return $this->read(1, $lockFile);
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::readLine
	// @desc		Lъ bytes do arquivo atual atщ encontrar uma quebra de linha,
	//				representada pelo caractere \n, ou o final do arquivo
	// @access		public
	// @param		lockFile mixed	"FALSE" Indica o tipo de bloqueio a ser aplicado no arquivo
	// @return		string Linha lida do arquivo ou FALSE em caso de erros
	// @see			FileManager::read
	// @see			FileManager::readChar
	// @see			FileManager::readFile
	//!-----------------------------------------------------------------
	function readLine($lockFile = FALSE) {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			$fp =& $this->currentFile;
			if ($lockFile == LOCK_SH || $lockFile == LOCK_EX) {
				$this->lock($fp, $lockFile);
			}
			return feof($fp) ? FALSE : fgets($fp);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::readFile
	// @desc		Lъ todo o conteњdo do arquivo atual
	// @access		public
	// @param		lockFile mixed	"FALSE" Indica o tipo de bloqueio a ser aplicado no arquivo
	// @return		string Conteњdo lido do arquivo ou FALSE em caso de erros
	// @see			FileManager::read
	// @see			FileManager::readChar
	// @see			FileManager::readLine
	//!-----------------------------------------------------------------
	function readFile($lockFile = FALSE) {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			$fp =& $this->currentFile;
			if ($lockFile == LOCK_SH || $lockFile == LOCK_EX) {
				$this->lock($fp, $lockFile);
			}
			$attrs = FileSystem::getFileAttributes($this->currentPath);
			return $this->read($attrs['size'], $lockFile);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::readArray
	// @desc		Lъ todo o conteњdo de um arquivo e retorna em um vetor,
	//				atravщs da funчуo file() do PHP
	// @access		public
	// @param		filePath string	Caminho do arquivo
	// @return		array Vetor com os dados do arquivo ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function readArray($filePath) {
		if (!FileSystem::exists($filePath)) {
			if ($this->throwErrors) 
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $filePath), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			return file($filePath);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::write
	// @desc		Escreve no arquivo atual o conteњdo de uma string indicando
	//				o nњmero de bytes. Permite gravaчуo (cria o arquivo) ou
	//				escrita (concatenaчуo) a partir do parтmetro $mode
	// @access		public
	// @param		string string		Conteњdo a ser escrito no arquivo
	// @param		size int			"0" Nњmero de bytes de string a serem escritos. Zero significa a string inteira
	// @param		lockFile mixed	"FALSE" Indica o tipo de bloqueio a ser aplicado no arquivo
	// @return		bool
	// @see			FileManager::writeChar
	// @see			FileManager::writeLine
	//!-----------------------------------------------------------------
	function write($string, $size = 0, $lockFile = FALSE) {
		if (!is_scalar($string) || !is_resource($this->currentFile)) {
			return FALSE;
		} else {
			$fp =& $this->currentFile;
			if ($lockFile == LOCK_SH || $lockFile == LOCK_EX) {
				$this->lock($fp, $lockFile);
			}
			return (@fwrite($fp, $string, max($size, strlen($string))));
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::writeChar
	// @desc		Escreve um caractere no arquivo atual
	// @access		public
	// @param		char string		Caractere a ser gravado no arquivo
	// @param		lockFile mixed	"FALSE" Indica o tipo de bloqueio a ser aplicado no arquivo
	// @return		bool
	// @see			FileManager::write
	// @see			FileManager::writeLine
	//!-----------------------------------------------------------------
	function writeChar($char, $lockFile = FALSE) {
		return $this->write($char, 1, $lockFile);
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::writeLine
	// @desc		Escreve uma linha de dados no arquivo atual
	// @access		public
	// @param		string string		Conteњdo a ser escrito no arquivo
	// @param		endLine int			"\n" Caractere a ser utilizado para fim de linha, padrуo щ '\n'
	// @param		lockFile mixed		"FALSE" Indica o tipo de bloqueio a ser aplicado no arquivo
	// @return		bool
	// @see			FileManager::write
	// @see			FileManager::writeChar
	//!-----------------------------------------------------------------
	function writeLine($string, $endLine = "\n", $lockFile = FALSE) {
		$string .= $endLine;
		return $this->write($string, strlen($string), $lockFile);
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::replaceInFiles
	// @desc		Realiza substituiчуo de valor no conteњdo 
	//				de um conjunto de arquivos
	// @access		public
	// @param		search string		Valor de pesquisa
	// @param		replace string		Valor de substituiчуo
	// @param		files array			Conjunto de arquivos de substituiчуo
	// @return		void
	//!-----------------------------------------------------------------
	function replaceInFiles($search, $replace, $files) {
		$files = (array)$files;
		foreach ($files as $file) {
			if (file_exists($file) && is_readable($file)) {
				$content = file_get_contents($file);
				$result = preg_replace('/' . preg_quote($search, "/") . '/', '', $content);
				if (function_exists('file_put_contents')) {
					$result = @file_put_contents($file, $result);
					if ($result === FALSE)
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $file), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					$this->open($file, FILE_MANAGER_WRITE_BINARY);
					$this->write($result);
					$this->close();
				}
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::seek
	// @desc		Procura por uma posiчуo no arquivo atual
	// @access		public
	// @param		offset int	Deslocamento
	// @return		bool
	//!-----------------------------------------------------------------
	function seek($offset) {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			$fp =& $this->currentFile;
			$result = @fseek($fp, $offset);
			return ($result === 0) ? TRUE : FALSE;
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::rewind
	// @desc		Volta o ponteiro do arquivo atual para a posiчуo inicial
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function rewind() {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			$fp =& $this->currentFile;
			return @rewind($fp);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::truncate
	// @desc		Trunca o arquivo atual para um determinado tamanho
	// @access		public
	// @param		size int			Novo tamanho para o arquivo
	// @param		lockFile mixed	"FALSE" Indica o tipo de bloqueio a ser aplicado no arquivo
	// @return		bool
	//!-----------------------------------------------------------------
	function truncate($size, $lockFile = FALSE) {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			$fp =& $this->currentFile;
			$truncSize = abs(intval($size));
			if ($lockFile == LOCK_SH || $lockFile == LOCK_EX) {
				$this->lock($fp, $lockFile);
			}			
			return @ftruncate($fp, $truncSize);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::changeMode
	// @desc		Muda o modo do arquivo ativo
	// @access		public
	// @param		newMode string	Novo modo para o arquivo
	// @return		bool
	//!-----------------------------------------------------------------
	function changeMode($newMode) {
		if (!isset($this->currentPath))
			return FALSE;
		else
			return chmod($this->currentPath, $newMode);
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::touch
	// @desc		Altera a data de modificaчуo do arquivo ativo
	// @access		public
	// @param		time int		"NULL" Nova data de modificaчуo para o arquivo
	// @return		void
	//!-----------------------------------------------------------------
	function touch($time=NULL) {
		if (isset($this->currentPath)) {
			($time == NULL) && ($time = time());
			FileSystem::touch($this->currentPath, $time);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::lock
	// @desc		Cria um bloqueio no arquivo indicado pelo ponteiro
	//				$filePointer, utilizando o modo $lockFile
	// @access		public
	// @param		&filePointer resource	Ponteiro de arquivo
	// @param		lockFile int			Tipo de bloqueio de arquivo
	// @return		void	
	//!-----------------------------------------------------------------
	function lock(&$filePointer, $lockFile) {
		$locks = FileManager::getLocks();
		if (@flock($filePointer, $lockFile))
			$locks[] =& $filePointer;
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::unlock
	// @desc		Retira o bloqueio do arquivo indicado em $filePath,
	//				aberto com o modo $mode. Busca por este arquivo nos
	//				ponteiros de arquivos abertos armazenados na classe
	// @access		public
	// @param		filePath string		Caminho do arquivo a ser desbloqueado
	// @param		mode string			Modo utilizado na abertura do arquivo
	// @return		bool
	//!-----------------------------------------------------------------
	function unlock($filePath, $mode) {
		$locks =& $this->getLocks();
		$pointers =& $this->getPointers();
		if (!isset($pointers[$filePath][$mode]) || !is_resource($pointers[$filePath][$mode])) {
			return FALSE;
		} else {
			$fp =& $pointers[$filePath][$mode];
			return @flock($fp, LOCK_UN);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::close
	// @desc		Destrѓi o ponteiro de arquivo atual
	// @access		public
	// @return		bool Retorna FALSE caso nуo seja possэvel destruir o ponteiro ou
	//				caso o arquivo nуo for encontrado na lista de arquivos abertos
	//!-----------------------------------------------------------------
	function close() {
		if (!is_resource($this->currentFile)) {
			return FALSE;
		} else {
			// remove do vetor de pointers
			$pointers =& $this->getPointers();
			unset($pointers[$this->currentPath][$this->currentMode]);
			// reseta o valor das propriedades do objeto
			$fp = $this->currentFile;
			unset($this->currentFile);
			unset($this->currentPath);
			unset($this->currentMode);
			unset($this->currentAttrs);
			return @fclose($fp);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileManager::closeAll
	// @desc		Libera todos os ponteiros de arquivo armazenados na classe
	// @access		public
	// @return		void
	// @note		Este mщtodo щ executado automaticamente na destruiчуo deste
	//				objeto. Logo, ponteiros para arquivo que sуo deixados abertos
	//				no fim do script serуo fechados
	// @note		A primeira chamada deste mщtodo jс irс liberar *todos* os ponteiros
	//				de arquivo utilizados em todas as instтncias da classe FileManager criadas
	//!-----------------------------------------------------------------
	function closeAll() {
		$pointers = &$this->getPointers();
		if (!empty($pointers)) {			
			foreach($pointers as $path => $value) {
				foreach($value as $mode => $pointer) {
					if (is_resource($pointer) && get_resource_type($pointer) != 'Unknown')
						@fclose($pointer);					
				}
			}			
		}				
		$pointers = array();
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::_isRead
	// @desc		Verifica se um modo de abertura de arquivo corresponde
	//				a um modos de leitura do PHP
	// @access		private
	// @param		mode string	Modo a ser verificado
	// @return		bool
	//!-----------------------------------------------------------------
	function _isRead($mode) {
		return (ereg(FILE_MANAGER_READ."b?\+?", $mode));
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::_isWrite
	// @desc		Verifica se um modo de abertura de arquivo corresponde
	//				a um modos de escrita da especificaчуo do PHP
	// @access		private
	// @param		mode string	Modo a ser verificado
	// @return		bool
	//!-----------------------------------------------------------------
	function _isWrite($mode) {
		return (ereg(FILE_MANAGER_WRITE."b?\+?", $mode));
	}

	//!-----------------------------------------------------------------
	// @function	FileManager::_isAppend
	// @desc		Verifica se um modo de abertura de arquivo щ um dos
	//				modos de concatenaчуo especificados pelo PHP
	// @access		private
	// @param		mode string	Modo a ser verificado
	// @return		bool
	//!-----------------------------------------------------------------
	function _isAppend($mode) {
		return (ereg(FILE_MANAGER_APPEND."b?\+?", $mode));
	}
}
?>