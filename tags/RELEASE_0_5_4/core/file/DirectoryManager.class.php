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
// $Header: /www/cvsroot/php2go/core/file/DirectoryManager.class.php,v 1.15 2006/04/06 01:14:22 mpont Exp $
// $Date: 2006/04/06 01:14:22 $

//!-----------------------------------------------------------------
import('php2go.file.FileSystem');
import('php2go.file.DirectoryEntry');
//!-----------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DirectoryManager
// @desc		Classe que manipula diret�rios e os arquivos e outros diret�rios
//				contidos em seu conte�do. Permite obter informa��es sobre as entradas
//				de um diret�rio e totaliza��es de tamanho total em disco
// @package		php2go.file
// @extends		FileSystem
// @author		Marcos Pont
// @version		$Revision: 1.15 $
//!-----------------------------------------------------------------
class DirectoryManager extends FileSystem
{
	var $currentHandle = NULL;			// @var currentHandle resource		"NULL" Ponteiro para o diret�rio ativo
	var $currentPath;					// @var currentPath string			Caminho completo para o diret�rio ativo
	var $currentAttrs;					// @var currentAttrs array			Vetor de atributos do diret�rio ativo
	var $throwErrors = TRUE;			// @var throwErrors bool			"TRUE" Indica se erros na leitura ou abertura de diret�rios devem ser reportados

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::DirectoryManager
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function DirectoryManager() {
		parent::FileSystem();
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::__destruct
	// @desc		Destrutor da classe, libera os handles obtidos para
	//				a leitura de diret�rios
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		$handles = DirectoryManager::getHandles();
		if (!empty($handles)) {
			foreach($handles as $path => $handle) {
				if (TypeUtils::isResource($handle))
					@closedir($handle);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::&getHandles
	// @desc		Busca os handles de diret�rio associados ao objeto
	// @access		public
	// @return		array Vetor contendo os handles de diret�rio ativos
	// @note		Este m�todo armazena os handles de diret�rios em uma vari�vel est�tica
	//!-----------------------------------------------------------------
	function &getHandles() {
		static $handles;
		if (!isset($handles)) {
			$handles = array();
		}
		return $handles;
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::getCurrentHandle
	// @desc		Retorna o handle ativo na classe
	// @access		public
	// @return		resource Handle ativo ou FALSE se o objeto n�o possui handle ativo
	//				ou ele for inv�lido
	//!-----------------------------------------------------------------
	function getCurrentHandle() {
		return isset($this->currentHandle) && TypeUtils::isResource($this->currentHandle) ? $this->currentHandle : FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::&getParentDirectory
	// @desc		Retorna uma outra inst�ncia da classe apontando para o
	//				diret�rio superior em rela��o ao atual
	// @access		public
	// @return		DirectoryManager object
	//!-----------------------------------------------------------------
	function &getParentDirectory() {
		$result = NULL;
		$parent = $this->getParentPath();
		if (!empty($parent)) {
			$Mgr =& new DirectoryManager();
			if ($Mgr->open($this->getParentPath()))
				$result =& $Mgr;
		}
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::getParentPath
	// @desc		Retorna o caminho do diret�rio superior ao atual, se existente
	// @access		public
	// @return		string Caminho do diret�rio superior
	//!-----------------------------------------------------------------
	function getParentPath() {
		$fullPath = StringUtils::left($this->currentAttrs['path'], -1);
		if (!isset($this->currentHandle) || !TypeUtils::isResource($this->currentHandle) || TypeUtils::isFalse(strpos($fullPath, '/'))) {
			return '';
		} else {
			return dirname($fullPath) . '/';
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::open
	// @desc		Abre um diret�rio indicado em $directoryPath para leitura
	// @access		public
	// @param		directoryPath string	Caminho do diret�rio a ser aberto
	// @return		bool
	//!-----------------------------------------------------------------
	function open($directoryPath) {
		$attrs = FileSystem::getFileAttributes($directoryPath);
		if (!$attrs) {
			if ($this->throwErrors)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_DIR', $directoryPath), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// corrige caminho do diret�rio
		$directoryPath = $attrs['path'];
		// busca os handles de diret�rio
		$handles = &$this->getHandles();
		if (!isset($handles[$directoryPath]) || !TypeUtils::isResource($handles[$directoryPath])) {
			$handles[$directoryPath] = @opendir($directoryPath);
			if (TypeUtils::isFalse($handles[$directoryPath])) {
				if ($this->throwErrors)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_OPEN_DIR', $directoryPath), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
		}
		if ($attrs) {
			$this->currentHandle = &$handles[$directoryPath];
			$this->currentPath = $directoryPath;
			$this->currentAttrs =& $attrs;
		}
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::isOpen
	// @desc		Verifica se existe um diret�rio atualmente aberto sendo
	//				manipulado pela classe DirectoryManager
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isOpen() {
		return (isset($this->currentHandle) && !TypeUtils::isResource($this->currentHandle));
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::changeDirectory
	// @desc		Alterna entre os diret�rios abertos no objeto, a partir do
	//				caminho completo que foi utilizado para abri-los
	// @access		public
	// @param		directoryPath string	Nome do arquivo solicitado
	// @return		bool
	//!-----------------------------------------------------------------
	function changeDirectory($directoryPath) {
		$handles =& $this->getHandles();
		if (!isset($handles[$directoryPath]) || !TypeUtils::isResource($handles[$directoryPath])) {
			return FALSE;
		} else {
			$this->currentHandle =& $handles[$directoryPath];
			$this->currentPath = $directoryPath;
			$this->currentAttrs = FileSystem::getFileAttributes($directoryPath);
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::read
	// @desc		L� a pr�xima entrada do diret�rio atualmente aberto
	// @access		public
	// @param		includeRegExp string	"" Filtro a ser aplicado no nome do arquivo
	// @return		DirectoryEntry object Objeto DirectoryEntry que representa a pr�xima entrada encontrada
	//!-----------------------------------------------------------------
	function read($includeRegExp='') {
		if (!isset($this->currentHandle) || !TypeUtils::isResource($this->currentHandle)) {
			return FALSE;
		} else {
			if (!$entry = @readdir($this->currentHandle)) {
				return FALSE;
			} else {
				// filtro autom�tico para ignorar as entradas '.' e '..'
				if (ereg("^\.{1,2}", $entry)) {
					return $this->read($includeRegExp);
				} elseif (!empty($includeRegExp)) {
					if (preg_match('/' . $includeRegExp . '/', $entry))
						return new DirectoryEntry($this->currentPath, $entry);
					else
						return $this->read($includeRegExp);
				} else {
					return new DirectoryEntry($this->currentPath, $entry);
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::getFileNames
	// @desc		Monta uma lista contendo apenas os nomes dos arquivos regulares
	//				contidos no diret�rio ativo
	// @access		public
	// @param		includeRegExp string	"" Filtro a ser aplicado nos nomes dos arquivos
	// @param		sort bool				"TRUE" Ordenar ou n�o a lista de arquivos resultantes
	// @return		array Lista de nomes de arquivos ou FALSE se o diret�rio n�o possui arquivos regulares
	// @see			DirectoryManager::getFiles
	// @see			DirectoryManager::getDirectories
	//!-----------------------------------------------------------------
	function getFileNames($includeRegExp='', $sort=TRUE) {
		$files = array();
		if (!isset($this->currentHandle) || !TypeUtils::isResource($this->currentHandle)) {
			return FALSE;
		} else {
			$this->rewind();
			while ($entry = $this->_readSimple($includeRegExp)) {
				if (@is_file($this->currentPath . $entry))
					$files[] = $entry;
			}
			if ($sort)
				sort($files, SORT_STRING);
			return $files;
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::getFiles
	// @desc		Retorna a lista de arquivos regulares contidos no diret�rio, onde cada
	//				entrada da lista � um objeto do tipo DirectoryEntry
	// @access		public
	// @param		includeRegExp string	"" Filtro a ser aplicado nos nomes dos arquivos
	// @param		sort bool				"TRUE" Ordenar ou n�o a lista de arquivos resultante
	// @return		array Lista de objetos do tipo DirectoryEntry ou FALSE se o diret�rio n�o possui arquivos regulares
	// @see			DirectoryManager::getFileNames
	// @see			DirectoryManager::getDirectories
	//!-----------------------------------------------------------------
	function getFiles($includeRegExp='', $sort=TRUE) {
		$files = array();
		if (!isset($this->currentHandle) || !TypeUtils::isResource($this->currentHandle)) {
			return FALSE;
		} else {
			$this->rewind();
			while ($entry = $this->read($includeRegExp)) {
				if ($entry->isFile())
					$files[] = $entry;
			}
			if ($sort)
				usort($files, array($this, '_sortDirectoryEntries'));
			return $files;
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::getDirectories
	// @desc		Retorna um vetor contendo todos os subdiret�rios contidos no diret�rio atual
	// @access		public
	// @return		array Vetor de objetos do tipo DirectoryEntry ou FALSE se o
	//				o diret�rio n�o possui subdiret�rios
	// @see			DirectoryManager::getFileNames
	// @see			DirectoryManager::getFiles
	//!-----------------------------------------------------------------
	function getDirectories() {
		$directories = array();
		if (!isset($this->currentHandle) || !TypeUtils::isResource($this->currentHandle)) {
			return FALSE;
		} else {
			$this->rewind();
			while ($entry = $this->read()) {
				if ($entry->isDirectory()) {
					$directories[] = $entry;
				}
			}
			sort($directories);
			return $directories;
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::getSize
	// @desc		Calcula o tamanho total do diret�rio
	// @access		public
	// @param		mode string	"" 	Modo como o resultado deve ser apresentado (K, M, G, T)
	// @param		precision int	"2" N�mero de casas decimais no resultado
	// @param		deep bool		"FALSE" Somar o tamanho dos subdiret�rios ao resultado
	// @return		string Tamanho do diret�rio, no formato e precis�o definidos
	// @see			Number::formatByteAmount
	//!-----------------------------------------------------------------
	function getSize($mode='', $precision=2, $deep=FALSE) {
		$size = $this->_getTotalSize($deep);
		return Number::formatByteAmount($size, $mode, $precision);
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::&getContentTree
	// @desc		Monta uma �rvore de arquivos e subdiret�rios a partir do diret�rio atual
	// @return		DirectoryEntry object Diret�rio atual, contendo arquivos e subdiret�rios na forma de uma �rvore
	// @access		public
	//!-----------------------------------------------------------------
	function &getContentTree() {
		$result = NULL;
		if (isset($this->currentHandle) && TypeUtils::isResource($this->currentHandle)) {
			$result = new DirectoryEntry($this->getParentPath(), $this->currentAttrs['lastDir']);
			$this->rewind();
			$this->_getChildren($result);
		}
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::rewind
	// @desc		Volta o ponteiro do handle do diret�rio para o in�cio
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function rewind() {
		if (!isset($this->currentHandle) || !TypeUtils::isResource($this->currentHandle)) {
			return FALSE;
		} else {
			return @rewinddir($this->currentHandle);
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::close
	// @desc		Fecha o handle do diret�rio atual
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function close() {
		if (!isset($this->currentHandle) || !TypeUtils::isResource($this->currentHandle)) {
			return FALSE;
		} else {
			// Remove do vetor de handles
			$handles = &$this->getHandles();
			unset($handles[$this->currentPath]);
			// Reseta o valor das propriedades do objeto
			$handle = $this->currentHandle;
			unset($this->currentHandle);
			unset($this->currentPath);
			unset($this->currentAttrs);
			return @closedir($handle);
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::_readSimple
	// @desc		Este m�todo alternativo de leitura � utilizado pelo m�todo
	//				getFileNames, que retorna apenas os nomes das entradas dos
	//				arquivos e n�o objetos do tipo DirectoryEntry
	// @access		private
	// @param		includeRegExp string	"" Filtro a ser aplicado nos nomes dos arquivos
	// @return		mixed Nome do pr�ximo arquivo no diret�rio ou FALSE em caso de falha
	//!-----------------------------------------------------------------
	function _readSimple($includeRegExp='') {
		if (!isset($this->currentHandle) || !TypeUtils::isResource($this->currentHandle)) {
			return FALSE;
		} else {
			if (!$entry = @readdir($this->currentHandle)) {
				return FALSE;
			} else {
				// filtro autom�tico para ignorar as entradas '.' e '..'
				if (ereg("^\.{1,2}", $entry)) {
					return $this->_readSimple($includeRegExp);
				} elseif (!empty($includeRegExp)) {
					if (preg_match('/' . $includeRegExp . '/', $entry))
						return $entry;
					else
						return $this->_readSimple($includeRegExp);
				} else {
					return $entry;
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::_getChildren
	// @desc		M�todo recursivo para buscar a �rvore de subdiret�rios
	//				abaixo do diret�rio ativo no objeto
	// @access		private
	// @param		&node DirectoryEntry object	Objeto DirectoryEntry do qual devem ser buscados os filhos
	// @return		void
	//!-----------------------------------------------------------------
	function _getChildren(&$node) {
		$oldPath = $node->getFullName();
		while ($entry = $this->read()) {
			$child = &$node->addChild($entry);
			if ($child->isDirectory()) {
				$this->open($child->getFullName());
				$this->_getChildren($child);
				$this->close();
				$this->changeDirectory($oldPath);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::_getTotalSize
	// @desc		M�todo recursivo para contar o tamanho total em bytes de um diret�rio
	// @access		private
	// @param		deep bool		"FALSE" Se TRUE, o total deve incluir o total dos subdiret�rios
	// @return		int Total em bytes de um diret�rio
	//!-----------------------------------------------------------------
	function _getTotalSize($deep=FALSE) {
		$size = 0;
		if (!isset($this->currentHandle) || !TypeUtils::isResource($this->currentHandle)) {
			return FALSE;
		} else {
			$this->rewind();
			$oldPath = $this->currentPath;
			while ($entry = $this->read()) {
				$size += $entry->getSize();
				if ($deep && $entry->isDirectory()) {
					$this->open($entry->getFullName());
					$size += $this->_getTotalSize($deep);
					$this->close();
					$this->changeDirectory($oldPath);
				}
			}
		}
		return $size;
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryManager::_sortDirectoryEntries
	// @desc		Utilizado para ordenar a lista de arquivos retornada pelo
	//				m�todo getFiles
	// @access		public
	// @param		a DirectoryEntry object		Lado esquerdo da compara��o
	// @param		b DirectoryEntry object		Lado esquerdo da compara��o
	// @return		int
	//!-----------------------------------------------------------------
	function _sortDirectoryEntries($a, $b) {
		$an = $a->getName();
		$bn = $b->getName();
	 	if ($an == $bn) {
			return 0;
		}
		return ($an < $bn) ? -1 : 1;
	}
}
?>