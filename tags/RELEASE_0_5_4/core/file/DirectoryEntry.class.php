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
// $Header: /www/cvsroot/php2go/core/file/DirectoryEntry.class.php,v 1.8 2006/03/15 04:43:23 mpont Exp $
// $Date: 2006/03/15 04:43:23 $

//!-----------------------------------------------------------------
import('php2go.base.AbstractNode');
//!-----------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DirectoryEntry
// @desc		Classe que manipula um arquivo inclu�do no conte�do
//				de um diret�rio, resultante de uma execu��o do m�todo
//				DirectoryManager::read()
// @package		php2go.file
// @extends		AbstractNode
// @author		Marcos Pont
// @version		$Revision: 1.8 $
//!-----------------------------------------------------------------
class DirectoryEntry extends AbstractNode
{
	var $path;		// @var path string		Caminho do diret�rio

	//!-----------------------------------------------------------------
	// @function	DirectoryEntry::DirectoryEntry
	// @desc		Construtor da classe, a partir do caminho do diret�rio e do nome da entrada
	// @access		public
	// @param		path string		Caminho do diret�rio
	// @param		entryName string	Nome da entrada (arquivo ou diret�rio)
	//!-----------------------------------------------------------------
	function DirectoryEntry($path, $entryName) {
		parent::AbstractNode($entryName, array(), NULL);
		$this->path = $path;
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryEntry::getPath
	// @desc		Retorna o caminho do diret�rio onde o arquivo se encontra
	// @access		public
	// @return		string Caminho do diret�rio
	//!-----------------------------------------------------------------
	function getPath() {
		return $this->path;
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryEntry::getFullName
	// @desc		Retorna o caminho completo da entrada, incluindo caminho do diret�rio e nome da entrada
	// @access		public
	// @return		string Caminho completo do arquivo ou diret�rio no servidor
	//!-----------------------------------------------------------------
	function getFullName() {
		if ($this->isDirectory())
			return $this->path . $this->getName() . '/';
		else
			return $this->path . $this->getName();
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryEntry::getSize
	// @desc		Retorna, em bytes, o tamanho da entrada no disco
	// @access		public
	// @return		int Tamanho do arquivo ou diret�rio
	//!-----------------------------------------------------------------
	function getSize() {
		if (empty($this->attrs))
			$this->_getAttributes();
		return AbstractNode::getAttribute('size');
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryEntry::isFile
	// @desc		Verifica se a entrada � um arquivo regular
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isFile() {
		if (empty($this->attrs))
			$this->_getAttributes();
		return AbstractNode::getAttribute('isFile');
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryEntry::isDirectory
	// @desc		Verifica se a entrada � um diret�rio
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isDirectory() {
		if (empty($this->attrs))
			$this->_getAttributes();
		return AbstractNode::getAttribute('isDir');
	}

	//!-----------------------------------------------------------------
	// @function	DirectoryEntry::_getAttributes
	// @desc		Busca os atributos do arquivo ou diret�rio
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _getAttributes() {
		$this->attrs = FileSystem::getFileAttributes($this->path . $this->getName());
	}
}
?>