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
// @desc		Esta classe agrupa outras tr�s classes respons�veis por
//				tarefas de concatena��o e compress�o de arquivos, utilizando
//				os padr�es TAR, GZIP e ZIP
// @package		php2go.file
// @extends		PHP2Go
// @uses		FileManager
// @uses		DirectoryManager
// @author		Marcos Pont
// @version		$Revision: 1.12 $
//!-----------------------------------------------------------------
class FileCompress extends PHP2Go
{
	var $defaultMode	= 0644;		// @var	defaultMode string	"0644" Modo padr�o para cria��o de arquivos
	var $currentDir		= './';		// @var	currentDir string	"./" Diret�rio inicial
	var $recurseDir		= TRUE;		// @var	recurseDir bool		"TRUE" Aplicar recurs�o em subdiret�rios
	var $overwriteFile	= TRUE;		// @var	overwriteFile bool	"TRUE" Sobrescrever arquivos existentes ao gravar resultados
	var $storePaths		= TRUE;		// @var	storePaths bool		"TRUE" Inserir caminhos completos dos arquivos
	var $debug = FALSE;				// @var debug bool			"FALSE" Habilita debug na classe

	//!-----------------------------------------------------------------
	// @function	FileCompress::FileCompress
	// @desc		Construtor da classe. Valida se existe uma classe extendida
	//				sendo instanciada (FileCompress � abstrata)
	// @param		cwd string		"" Diret�rio inicial de trabalho
	// @note		Se o diret�rio inicial for omitido, o diret�rio atual ser� utilizado como padr�o
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
	// @desc		M�todo "factory" para retornar uma inst�ncia �nica de um determinado
	//				tipo de classe de compress�o de arquivos. O par�metro $type
	//				deve conter um dos valores : zip, gz, tar
	// @access		public
	// @param		type string			Tipo de componente de compress�o de arquivos
	// @param		cwd string			"" Par�metro que ser� passado ao construtor do objeto criado
	// @return		mixed Retorna a inst�ncia j� armazenada do objeto ou uma nova se ela n�o existir
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
	// @desc		Retorna uma inst�ncia �nica da classe de manipula��o de arquivos
	// @access		public
	// @return		FileManager object	Inst�ncia do objeto de manipula��o de arquivos
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
	// @desc		Configura o modo padr�o de cria��o de arquivos
	// @access		public
	// @param		mode string		Modo para cria��o de arquivos
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
	// @desc		Habilita ou desabilita a recurs�o em subdiret�rios
	// @access		public
	// @param		recurse bool		Flag para desabilitar/habilitar recurs�o
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
	// @desc		Habilita ou desabilita a sobrescrita de arquivos j� existentes na grava��o
	// @access		public
	// @param		overwrite bool	Habilitar ou desabilitar esta caracter�stica
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
	// @desc		Habilita ou desabilita a inser��o dos caminhos completos
	//				junto aos nomes dos arquivos concatenados ou compactados
	// @access		public
	// @param		store bool		Inserir ou n�o caminhos completos
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
	// @desc		Verifica se a recurs�o de subdiret�rios est� habilitada
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isRecursionEnabled() {
		return $this->recurseDir;
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::isOverwriteEnabled
	// @desc		Checa se os arquivos j� existentes devem ser sobrescritos
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
	// @desc		Adiciona uma lista de diret�rios ou arquivos ao arquivo
	//				que est� sendo montado
	// @param		dirList array		Vetor contendo arquivos e/ou diret�rios
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
	// @desc		Inclus�o de um arquivo ao arquivo compactado ou concatenado
	// @note		Este m�todo � abstrato na classe FileCompress, e � implementado
	//				em cada uma das classes filhas (GzFile, TarFile, ZipFile)
	// @param		filePath string		Caminho completo do arquivo
	// @return		void
	//!-----------------------------------------------------------------
	function addFile($filePath) {
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::extractFile
	// @desc		Abre e descompacta um arquivo utilizando a fun��o de extra��o
	//				de cada uma das classes filhas
	// @access		public
	// @param		fileName string	Arquivo com dados compactados ou arquivos agrupados
	// @return		mixed Retorna um vetor contendo informa��es do(s) arquivo(s) extra�dos ou FALSE em caso de erros
	// @see			FileCompress::saveExtractedFiles
	// @note		Para extrair o conte�do de um arquivo e gravar o resultado em disco, utilize
	//				este m�todo em conjunto com o m�todo saveExtractedFiles
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
	// @note		Este m�todo � abstrato na classe FileCompress, e � implementado
	//				em cada uma das classes filhas (GzFile, TarFile, ZipFile)
	// @param		content string		Conte�do do arquivo
	// @return		void
	//!-----------------------------------------------------------------
	function extractData($content) {
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::getData
	// @desc		M�todo abstrato que retorna o buffer de dados. Cada
	//				uma das classes filhas deve implementar este m�todo
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getData() {
	}

	//!-----------------------------------------------------------------
	// @function	FileCompress::saveExtractedFiles
	// @desc		Salva em disco os arquivos extra�dos, criando os diret�rios necess�rios
	// @access		public
	// @param		files array		Vetor com dados dos arquivos extra�dos, resultante da chamada dos m�todos extractFile e extractData
	// @return		FALSE em caso de erro; a lista de arquivos extra�dos (caminhos completos) em caso contr�rio
	//!-----------------------------------------------------------------
	function saveExtractedFiles($files, $createMode, $target=NULL) {
		$cwd = getcwd();
		$fileSet = array();
		$lastDir = NULL;
		$Mgr =& FileCompress::getFileManager();
		// verifica se os arquivos s�o v�lidos
		if (!TypeUtils::isArray($files))
			return FALSE;
		// aponta para o diret�rio alvo, se existir
		if (!TypeUtils::isNull($target) && $Mgr->exists($target))
			chdir($target);
		// processa um ou mais arquivos extra�dos
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
			// grava o conte�do do arquivo utilizando o nome e o modo originais
			if ($name != '' && (!isset($file['type']) || $file['type'] != 5)) {
				// adiciona � lista de arquivos descompactados
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
	// @param		mode string		"NULL" Modo de cria��o para o arquivo,
	//									se n�o for fornecido utiliza o padr�o 0644
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
	// @desc		Imprime informa��o de debug
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
	// @desc		Fun��o recursiva que busca todos os nomes de arquivos
	//				concatenados ao caminho a partir de um diret�rio raiz
	// @access		private
	// @param		directory string  Diret�rio raiz
	// @return		array Vetor contendo os arquivos encontrados
	// @note		A recurs�o presente neste m�todo s� ser� executada se
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