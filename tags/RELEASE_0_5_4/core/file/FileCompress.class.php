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
// $Header: /www/cvsroot/php2go/core/file/FileCompress.class.php,v 1.12 2006/06/18 18:44:59 mpont Exp $
// $Date: 2006/06/18 18:44:59 $

//-----------------------------------------
import('php2go.file.FileManager');
import('php2go.file.DirectoryManager');
//-----------------------------------------

//!-----------------------------------------------------------------
// @class		FileCompress
// @desc		Esta classe agrupa outras três classes responsáveis por
//				tarefas de concatenação e compressão de arquivos, utilizando
//				os padrões TAR, GZIP e ZIP
// @package		php2go.file
// @extends		PHP2Go
// @uses		FileManager
// @uses		DirectoryManager
// @author		Marcos Pont
// @version		$Revision: 1.12 $
//!-----------------------------------------------------------------
class FileCompress extends PHP2Go
{
	var $defaultMode	= 0644;		// @var	defaultMode string	"0644" Modo padrão para criação de arquivos
	var $currentDir		= './';		// @var	currentDir string	"./" Diretório inicial
	var $recurseDir		= TRUE;		// @var	recurseDir bool		"TRUE" Aplicar recursão em subdiretórios
	var $overwriteFile	= TRUE;		// @var	overwriteFile bool	"TRUE" Sobrescrever arquivos existentes ao gravar resultados
	var $storePaths		= TRUE;		// @var	storePaths bool		"TRUE" Inserir caminhos completos dos arquivos
	var $debug = FALSE;				// @var debug bool			"FALSE" Habilita debug na classe

	//!-----------------------------------------------------------------
	// @function	FileCompress::FileCompress
	// @desc		Construtor da classe. Valida se existe uma classe extendida
	//				sendo instanciada (FileCompress é abstrata)
	// @param		cwd string		"" Diretório inicial de trabalho
	// @note		Se o diretório inicial for omitido, o diretório atual será utilizado como padrão
	// @access		public
	//!-----------------------------------------------------------------
	function FileCompress($cwd='') {
		parent::PHP2Go();
		if ($this->isA('FileCompress', FALSE)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'FileCompress'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$this->currentDir = empty($cwd) ? getcwd() : $cwd;
			$this->currentDir = ereg("/\|/$", $this->currentDir) ? StringUtils::left($this->currentDir, -1) . '/' : $this->currentDir . '/';
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::&getInstance
	// @desc		Método "factory" para retornar uma instância única de um determinado
	//				tipo de classe de compressão de arquivos. O parâmetro $type
	//				deve conter um dos valores : zip, gz, tar
	// @access		public
	// @param		type string			Tipo de componente de compressão de arquivos
	// @param		cwd string			"" Parâmetro que será passado ao construtor do objeto criado
	// @return		mixed Retorna a instância já armazenada do objeto ou uma nova se ela não existir
	//!-----------------------------------------------------------------
	function &getInstance($type, $cwd='') {
		static $instances;
		$type = strtolower(trim($type));
		$className = ucfirst($type) . 'File';
		if (!isset($instances)) {
			if (import('php2go.file.' . $className))
				$instances = array($type => new $className($cwd));
		} elseif (!isset($instances[$type])) {
			if (import('php2go.file.' . $className))
				$instances = array($type => new $className($cwd));
		}
		return $instances[$type];
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::&getFileManager
	// @desc		Retorna uma instância única da classe de manipulação de arquivos
	// @access		public
	// @return		FileManager object	Instância do objeto de manipulação de arquivos
	// @static
	//!-----------------------------------------------------------------
	function &getFileManager() {
		static $Mgr;
		if (!isset($Mgr)) {
			$Mgr =& new FileManager();
			$Mgr->throwErrors = FALSE;
		}
		return $Mgr;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::setDefaultMode
	// @desc		Configura o modo padrão de criação de arquivos
	// @access		public
	// @param		mode string		Modo para criação de arquivos
	// @return		void
	// @see			FileCompress::setDirectoryRecursion
	// @see			FileCompress::setFileOverwrite
	// @see			FileCompress::setPathStorage
	//!-----------------------------------------------------------------
	function setDefaultMode($mode) {
		$this->defaultMode = $mode;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::setDirectoryRecursion
	// @desc		Habilita ou desabilita a recursão em subdiretórios
	// @access		public
	// @param		recurse bool		Flag para desabilitar/habilitar recursão
	// @return		void
	// @see			FileCompress::setDefaultMode
	// @see			FileCompress::setFileOverwrite
	// @see			FileCompress::setPathStorage
	//!-----------------------------------------------------------------
	function setDirectoryRecursion($recurse) {
		$this->recurseDir = TypeUtils::toBoolean($recurse);
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::setFileOverwrite
	// @desc		Habilita ou desabilita a sobrescrita de arquivos já existentes na gravação
	// @access		public
	// @param		overwrite bool	Habilitar ou desabilitar esta característica
	// @return		void
	// @see			FileCompress::setDefaultMode
	// @see			FileCompress::setDirectoryRecursion
	// @see			FileCompress::setPathStorage
	//!-----------------------------------------------------------------
	function setFileOverwrite($overwrite) {
		$this->overwriteFile = TypeUtils::toBoolean($overwrite);
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::setPathStorage
	// @desc		Habilita ou desabilita a inserção dos caminhos completos
	//				junto aos nomes dos arquivos concatenados ou compactados
	// @access		public
	// @param		store bool		Inserir ou não caminhos completos
	// @return		void
	// @see			FileCompress::setDefaultMode
	// @see			FileCompress::setDirectoryRecursion
	// @see			FileCompress::setFileOverwrite
	//!-----------------------------------------------------------------
	function setPathStorage($store) {
		$this->storePaths = TypeUtils::toBoolean($store);
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::isRecursionEnabled
	// @desc		Verifica se a recursão de subdiretórios está habilitada
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isRecursionEnabled() {
		return $this->recurseDir;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::isOverwriteEnabled
	// @desc		Checa se os arquivos já existentes devem ser sobrescritos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isOverwriteEnabled() {
		return $this->overwriteFile;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::isPathStorageEnabled
	// @desc		Verifica se devem ser utilizados caminhos completos nos nomes de arquivos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isPathStorageEnabled() {
		return $this->storePaths;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::addDirectories
	// @desc		Adiciona uma lista de diretórios ou arquivos ao arquivo
	//				que está sendo montado
	// @param		dirList array		Vetor contendo arquivos e/ou diretórios
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function addDirectories($dirList) {
		if ($this->isA('GzFile') || !($dirList)) {
			return FALSE;
		}
		$fileList = array();
		$actualDir = getcwd();
		@chdir($this->currentDir);
		foreach($dirList as $file) {
			if (@is_dir($file)) {
				if ($dirFiles = $this->_parseDirectory($file)) {
					$fileList = array_merge($fileList, $dirFiles);
				}
			} else if (FileSystem::exists($file)) {
				$fileList[] = $file;
			}
		}
		@chdir($actualDir);
		$this->addFiles($fileList);
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::addFiles
	// @desc		Adiciona uma lista de arquivos a um arquivo concatenado ou compactado
	// @param		fileList array	Vetor de arquivos a serem adicionados
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function addFiles($fileList) {
		if ($this->isA('GzFile') || !TypeUtils::isArray($fileList)) {
			return FALSE;
		}
		$actualDir = getcwd();
		@chdir($this->currentDir);
		foreach ($fileList as $currentFile) {
			$this->addFile($currentFile);
		}
		@chdir($actualDir);
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::addFile
	// @desc		Inclusão de um arquivo ao arquivo compactado ou concatenado
	// @note		Este método é abstrato na classe FileCompress, e é implementado
	//				em cada uma das classes filhas (GzFile, TarFile, ZipFile)
	// @param		filePath string		Caminho completo do arquivo
	// @return		void
	//!-----------------------------------------------------------------
	function addFile($filePath) {
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::extractFile
	// @desc		Abre e descompacta um arquivo utilizando a função de extração
	//				de cada uma das classes filhas
	// @access		public
	// @param		fileName string	Arquivo com dados compactados ou arquivos agrupados
	// @return		mixed Retorna um vetor contendo informações do(s) arquivo(s) extraídos ou FALSE em caso de erros
	// @see			FileCompress::saveExtractedFiles
	// @note		Para extrair o conteúdo de um arquivo e gravar o resultado em disco, utilize
	//				este método em conjunto com o método saveExtractedFiles
	//!-----------------------------------------------------------------
	function extractFile($fileName) {
		$Mgr =& FileCompress::getFileManager();
		if (!$Mgr->open($fileName, FILE_MANAGER_READ_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$result = $this->extractData($Mgr->readFile());
			$Mgr->close();
			return $result;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::extractData
	// @desc		Extrai dados de um arquivo concatenado ou compactado
	// @note		Este método é abstrato na classe FileCompress, e é implementado
	//				em cada uma das classes filhas (GzFile, TarFile, ZipFile)
	// @param		content string		Conteúdo do arquivo
	// @return		void
	//!-----------------------------------------------------------------
	function extractData($content) {
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::getData
	// @desc		Método abstrato que retorna o buffer de dados. Cada
	//				uma das classes filhas deve implementar este método
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getData() {
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::saveExtractedFiles
	// @desc		Salva em disco os arquivos extraídos, criando os diretórios necessários
	// @access		public
	// @param		files array		Vetor com dados dos arquivos extraídos, resultante da chamada dos métodos extractFile e extractData
	// @return		FALSE em caso de erro; a lista de arquivos extraídos (caminhos completos) em caso contrário
	//!-----------------------------------------------------------------
	function saveExtractedFiles($files, $createMode, $target=NULL) {
		$cwd = getcwd();
		$fileSet = array();
		$lastDir = NULL;
		$Mgr =& FileCompress::getFileManager();
		// verifica se os arquivos são válidos
		if (!TypeUtils::isArray($files))
			return FALSE;
		// aponta para o diretório alvo, se existir
		if (!TypeUtils::isNull($target) && $Mgr->exists($target))
			chdir($target);
		// processa um ou mais arquivos extraídos
		foreach ($files as $file) {
			$path = (isset($file['path']) ? $file['path'] . $file['filename'] : $file['filename']);
			// separa o nome do arquivo em nome e caminho
			if (StringUtils::match($path, '/')) {
				$name = substr($path, strrpos($path, '/')+1);
				$path = substr($path, 0, strrpos($path, '/'));
				if ($path != $lastDir) {
					if (FileSystem::exists($path) || FileSystem::createPath($path)) {
						if ($name != '' && (isset($file['type']) && $file['type'] == 5)) {
							@touch($path, (isset($file['time']) ? $file['time'] : time()));
							@chmod($path, (isset($file['mode']) ? $file['mode'] : $createMode));
						}
						$lastDir = $path;
					} else {
						chdir($cwd);
						return FALSE;
					}
				}
				chdir($lastDir);
			} else {
				$name = $path;
			}
			// grava o conteúdo do arquivo utilizando o nome e o modo originais
			if ($name != '' && (!isset($file['type']) || $file['type'] != 5)) {
				// adiciona à lista de arquivos descompactados
				$fileSet[] = (!TypeUtils::isNull($target) && $Mgr->exists($target) ? $target : '') . TypeUtils::parseString($lastDir) . $name;
				// grava o arquivo descompactado
				if (!$Mgr->open($name, FILE_MANAGER_WRITE_BINARY)) {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $path), E_USER_ERROR, __FILE__, __LINE__);
					chdir($cwd);
					return FALSE;
				} else {
					$Mgr->write($file['data']);
					$Mgr->changeMode(isset($file['mode']) ? $file['mode'] : $createMode);
					if (isset($file['time']))
						$Mgr->touch($file['time']);
					$Mgr->close();
				}
			}
			// retorna ao ponto original
			chdir($cwd);
			if (!TypeUtils::isNull($target) && $Mgr->exists($target))
				chdir($target);
		}
		chdir($cwd);
		return $fileSet;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::saveFile
	// @desc		Salva o arquivo gerado pelas classes extendidas
	// @access		public
	// @param		fileName string	Nome para o arquivo
	// @param		mode string		"NULL" Modo de criação para o arquivo,
	//									se não for fornecido utiliza o padrão 0644
	// @return		bool
	//!-----------------------------------------------------------------
	function saveFile($fileName, $mode=NULL) {
		$Mgr =& FileCompress::getFileManager();
		if (!$this->isOverwriteEnabled() && $Mgr->exists($fileName))
			return FALSE;
		else if ($Mgr->exists($fileName))
			@unlink($fileName);
		if (!$Mgr->open($fileName, FILE_MANAGER_WRITE_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$Mgr->write($this->getData());
			$Mgr->changeMode(($mode == NULL ? $this->defaultMode : $mode));
			$Mgr->close();
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::debug
	// @desc		Imprime informação de debug
	// @access		private
	// @param		str string			Mensagem de debug
	// @return		void
	//!-----------------------------------------------------------------
	function debug($str) {
		$type = strtoupper($this->getObjectName());
		if ($this->debug) {
			print $type . ' DEBUG : ' . $str . '<br>';
			flush();
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::_parseDirectory
	// @desc		Função recursiva que busca todos os nomes de arquivos
	//				concatenados ao caminho a partir de um diretório raiz
	// @access		private
	// @param		directory string  Diretório raiz
	// @return		array Vetor contendo os arquivos encontrados
	// @note		A recursão presente neste método só será executada se
	//				a propriedade recurseDir estiver habilitada
	//!-----------------------------------------------------------------
	function _parseDirectory($directory) {
		$fileList = array();
		$_DirectoryManager = new DirectoryManager();
		if (!$_DirectoryManager->open($this->currentDir . $directory)) {
			return $fileList;
		} else {
			while ($entry = $_DirectoryManager->read()) {
				if ($this->isRecursionEnabled() && $entry->isDirectory()) {
					$dirFiles = $this->_parseDirectory($directory . '/' . $entry->getName());
					$fileList = array_merge($fileList, $dirFiles);
				} else {
					$fileList[] = $directory . '/' . $entry->getName();
				}
			}
			$_DirectoryManager->close();
		}
		return $fileList;
	}
}
?>