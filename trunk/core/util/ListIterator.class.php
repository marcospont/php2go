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
// $Header: /www/cvsroot/php2go/core/util/ListIterator.class.php,v 1.10 2006/05/07 15:23:54 mpont Exp $
// $Date: 2006/05/07 15:23:54 $

//!-----------------------------------------------------------------
// @class		ListIterator
// @desc		Implementa itera��o em listas permitindo a captura dos
//				elementos da mesma em ambas as dire��es e obter a posi��o
//				atual do cursor
// @package		php2go.util
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.10 $
//!-----------------------------------------------------------------
class ListIterator extends PHP2Go
{
	var $current;	// @var current int					Posi��o atual do cursor
	var $_List;		// @var _List AbstractList object	Lista abstrata da qual esta classe retorna os elementos

	//!-----------------------------------------------------------------
	// @function	ListIterator::ListIterator
	// @desc		Construtor da classe
	// @param		List AbstractList object Inst�ncia da classe AbstractList
	// @access		public	
	//!-----------------------------------------------------------------
	function ListIterator($List) {
		parent::PHP2Go();
		if (!TypeUtils::isObject($List) || !TypeUtils::isInstanceOf($List, 'AbstractList')) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'AbstractList'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$this->current = -1;
			$this->_List = $List;
		}
	}

	//!-----------------------------------------------------------------
	// @function	ListIterator::getCurrentIndex
	// @desc		Busca a posi��o atual do cursor na lista de elementos
	// @access		public
	// @return		int Posi��o atual do cursor
	//!-----------------------------------------------------------------
	function getCurrentIndex() {
		return $this->current;
	}

	//!-----------------------------------------------------------------
	// @function	ListIterator::moveToIndex
	// @desc		Muda a posi��o atual do cursor na lista de elementos
	// @access		public
	// @param		index int 	Nova posi��o para o cursor
	// @return		bool
	//!-----------------------------------------------------------------
	function moveToIndex($index) {
		if ($index >= 0 && $index < $this->_List->size()) {
			$this->current = $index - 1;
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	ListIterator::current
	// @desc		Retorna o elemento atual da lista
	// @access		public
	// @return		mixed Valor do elemento
	//!-----------------------------------------------------------------
	function current() {
		return ($this->getCurrentIndex() >= 0) ? $this->_List->get($this->current + 1) : NULL;
	}

	//!-----------------------------------------------------------------
	// @function	ListIterator::hasNext
	// @desc		Verifica se a lista possui mais elementos quando percorrida na ordem crescente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasNext() {
		return ($this->current < ($this->_List->size() - 1));
	}

	//!-----------------------------------------------------------------
	// @function	ListIterator::next
	// @desc		Busca o pr�ximo elemento da lista
	// @access		public
	// @return		mixed Pr�ximo elemento ou FALSE se a lista atingiu o final
	//!-----------------------------------------------------------------
	function next() {
		if ($this->hasNext()) {
			return $this->_List->get(++$this->current);
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	ListIterator::nextIndex
	// @desc		Busca o �ndice retornado pela pr�xima execu��o do m�todo next()
	// @access		public
	// @return		int Valor do �ndice
	//!-----------------------------------------------------------------
	function nextIndex() {
		return $this->current + 1;
	}

	//!-----------------------------------------------------------------
	// @function	ListIterator::hasPrevious
	// @desc		Verifica se a lista possui mais elementos quando percorrida na ordem decrescente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasPrevious() {
		return ($this->current > 0);
	}

	//!-----------------------------------------------------------------
	// @function	ListIterator::previous
	// @desc		Busca o elemento anterior na lista
	// @access		public
	// @return		mixed Elemento anterior ou FALSE se a lista atingiu o in�cio
	//!-----------------------------------------------------------------
	function previous() {
		if ($this->hasPrevious()) {
			return $this->_List->get(--$this->current);
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	ListIterator::previousIndex
	// @desc		Busca o �ndice retornado pela pr�xima execu��o do m�todo previous()
	// @access		public
	// @return		int Valor do �ndice
	//!-----------------------------------------------------------------
	function previousIndex() {
		return $this->current - 1;
	}
}
?>