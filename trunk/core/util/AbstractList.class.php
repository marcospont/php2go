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
// $Header: /www/cvsroot/php2go/core/util/AbstractList.class.php,v 1.11 2006/05/07 15:09:54 mpont Exp $
// $Date: 2006/05/07 15:09:54 $

//------------------------------------------------------------------
import('php2go.util.ListIterator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		AbstractList
// @desc		Esta classe implementa uma lista de objetos no PHP,
//				indexados por um valor inteiro iniciando em zero
// @package		php2go.util
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.11 $
//!-----------------------------------------------------------------
class AbstractList extends PHP2Go
{
	var $elements;			// @var elements array		Lista de objetos da classe
	var $modCount = 0;		// @var modCount int		"0" Número de modificações estruturais e de movimentação de objetos

	//!-----------------------------------------------------------------
	// @function	AbstractList::AbstractList
	// @desc		Construtor da classe, permite a inicialização do objeto
	//				com um vetor de objetos
	// @access		public
	// @param		arr array		"array()" Vetor para inicialização
	//!-----------------------------------------------------------------
	function AbstractList($arr = array()) {
		parent::PHP2Go();
		$this->elements = array();
		if (TypeUtils::isArray($arr) && !empty($arr)) {
			$this->addAll($arr);
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::add
	// @desc		Adiciona um objeto à lista, indicando ou não índice
	// @access		public
	// @param		object mixed	Objeto a ser adicionado
	// @param		index int		"-1" Índice opcional
	// @return		bool
	// @see			AbstractList::addAll
	// @note		Se o objeto já existir, será duplicado
	// @note		Se o índice já possuir algum valor, realoca para a
	//				direita todos os elementos subseqüentes
	// @note		O índice indicado deve ser positivo e menor ou igual
	//				ao tamanho atual da lista
	//!-----------------------------------------------------------------
	function add($object, $index=-1) {
		if ($index != -1 && TypeUtils::isInteger($index)) {
			if ($index < 0 || $index > $this->size()) {
				return FALSE;
			} else {
				if (isset($this->elements[$index])) {
					$size = $this->size();
					for ($i=$size; $i>$index; $i--) {
						$this->elements[$i] = $this->elements[$i-1];
						$this->modCount++;
					}
				}
				$this->elements[$index] = $object;
				$this->modCount++;
				return TRUE;
			}
		} else if ($index == -1) {
			$this->elements[] = $object;
			$this->modCount++;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::addAll
	// @desc		Adiciona uma coleção de elementos à lista
	// @access		public
	// @param		collection array	Vetor a ser adicionado
	// @param		index int			"-1" Índice inicial opcional
	// @return		int Número de elementos corretamente inseridos
	// @see			AbstractList::add
	// @note		O índice inicial deve ser positivo e menor ou igual ao tamanho atual da lista
	// @note		Os índices atuais da coleção serão ignorados
	//!-----------------------------------------------------------------
	function addAll($collection, $index=-1) {
		$added = 0;
		if (TypeUtils::isArray($collection)) {
			if ($index != -1 && TypeUtils::isInteger($index)) {
				$initial = $index;
				foreach($collection as $element) {
					$added += TypeUtils::parseInteger($this->add($element, $initial++));
				}
			} else if ($index == -1) {
				foreach($collection as $element) {
					$added += TypeUtils::parseInteger($this->add($element));
				}
			}
		}
		return $added;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::clear
	// @desc		Limpa todos os elementos da lista
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clear() {
		$this->elements = array();
		$this->modCount++;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::&get
	// @desc		Busca um elemento da lista pelo seu índice
	// @access		public
	// @param		index int		Índice buscado
	// @return		mixed Objeto no índice solicitado ou FALSE caso não seja encontrado
	//!-----------------------------------------------------------------
	function &get($index) {
		$return = FALSE;
		if (TypeUtils::isInteger($index)) {
			if (isset($this->elements[$index])) {
				$return = $this->elements[$index];
			}
		}
		return $return;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::&iterator
	// @desc		Obtém um objeto ListIterator para iterar sobre os elementos da lista
	// @access		public
	// @return		ListIterator object
	//!-----------------------------------------------------------------
	function &iterator() {
		$iterator = new ListIterator($this);
		return $iterator;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::contains
	// @desc		Verifica se um determinado objeto pertence à lista
	// @access		public
	// @param		object mixed	Objeto procurado
	// @return		bool
	// @see			AbstractList::containsAll
	//!-----------------------------------------------------------------
	function contains($object) {
		return ($this->indexOf($object) != -1);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::containsAll
	// @desc		Verifica se todos os elementos de uma coleção pertencem à lista
	// @access		public
	// @param		collection array	Vetor de elementos
	// @return		bool
	// @see			AbstractList::contains
	//!-----------------------------------------------------------------
	function containsAll($collection) {
		if (TypeUtils::isArray($collection) && !empty($collection)) {
			foreach($collection as $element) {
				if (!$this->contains($element)) return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::indexOf
	// @desc		Busca o próximo índice de um determinado objeto na lista
	// @access		public
	// @param		object mixed	Objeto procurado
	// @return		int Índice do objeto ou -1 se ele não existir
	// @see			AbstractList::lastIndexOf
	//!-----------------------------------------------------------------
	function indexOf($object) {
		reset($this->elements);
		while (list($key, $value) = each($this->elements)) {
			if ($value === $object) return $key;
		}
		return -1;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::lastIndexOf
	// @desc		Busca o último índice de um objeto na lista
	// @access		public
	// @param		object mixed	Objeto procurado
	// @return		int Último índice ou -1 se não encontrado
	// @see			AbstractList::indexOf
	//!-----------------------------------------------------------------
	function lastIndexOf($object) {
		$index = -1;
		reset($this->elements);
		while (list($key, $value) = each($this->elements)) {
			if ($value == $object) $index = $key;
		}
		return $index;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::lastIndex
	// @desc		Busca o último índice da lista
	// @access		public
	// @return		int Último índice ou -1 se a lista estiver vazia
	//!-----------------------------------------------------------------
	function lastIndex() {
		return $this->isEmpty() ? -1 : $this->size() - 1;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::remove
	// @desc		Remove um item através de seu índice
	// @access		public
	// @param		index int	Índice a ser removido
	// @return		bool
	// @see			AbstractList::removeAll
	// @see			AbstractList::removeRange
	//!-----------------------------------------------------------------
	function remove($index) {
		if (isset($this->elements[$index])) {
			$newList = array();
			$size = $this->size();
			for ($i=0; $i<$size; $i++) {
				if ($i != $index) $newList[] = $this->get($i);
				else $this->modCount++;
			}
			$this->elements = $newList;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::removeAll
	// @desc		Remove todos os itens da lista. É um sinônimo para AbstractList::clear
	// @access		public
	// @return		void
	// @see			AbstractList::remove
	// @see			AbstractList::removeRange
	//!-----------------------------------------------------------------
	function removeAll() {
		$this->clear();
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::removeRange
	// @desc		Remove N itens da lista compreendidos entre dois limites
	// @access		public
	// @param		fromIndex int	Índice inicial
	// @param		toIndex int	Índice final
	// @return		int Número de itens removidos
	// @see			AbstractList::remove
	// @see			AbstractList::removeAll
	// @note		Os parâmetros limitadores devem ser inteiros positivos,
	//				sendo que o final deve ser menor do que o tamanho da lista
	//!-----------------------------------------------------------------
	function removeRange($fromIndex, $toIndex) {
		$removed = 0;
		$size = $this->size();
		if (TypeUtils::isInteger($fromIndex) && TypeUtils::isInteger($toIndex) && $fromIndex >= 0 && $toIndex < $size) {
			$newList = array();
			for ($i=0; $i<$size; $i++) {
				if ($i < $fromIndex || $i > $toIndex) {
					$newList[] = $this->get($i);
				} else {
					$this->modCount++;
					$removed++;
				}
			}
			$this->elements = $newList;
		}
		return $removed;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::set
	// @desc		Atribui um novo valor a um índice da lista
	// @access		public
	// @param		index int		Índice da lista
	// @param		object mixed	Novo valor para o índice
	// @return		bool
	//!-----------------------------------------------------------------
	function set($index, $object) {
		$size = $this->size();
		if ($index < $size) {
			$this->elements[$index] = $object;
			$this->modCount++;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::subList
	// @desc		Cria uma sublista a partir da lista atual
	// @access		public
	// @param		fromIndex int	Índice inicial
	// @param		toIndex (itn)	Índice final
	// @return		array Lista criada
	// @note		Os limitadores deve estar dentro dos limites atuais da lista
	//!-----------------------------------------------------------------
	function subList($fromIndex, $toIndex) {
		$subList = array();
		$size = $this->size();
		if (TypeUtils::isInteger($fromIndex) && TypeUtils::isInteger($toIndex) && $fromIndex >= 0 && $toIndex < $size) {
			for ($i=$fromIndex; $i<$size && $i<=$toIndex; $i++) {
				if (isset($this->elements[$i])) {
					$subList[] = $this->get($i);
				}
			}
		}
		return $subList;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::size
	// @desc		Busca o tamanho atual da lista
	// @access		public
	// @return		int Tamanho da lista
	//!-----------------------------------------------------------------
	function size() {
		return sizeof($this->elements);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::isEmpty
	// @desc		Verifica se a lista está vazia
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isEmpty() {
		return ($this->size() == 0);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::toArray
	// @desc		Retorna a representação array da lista
	// @access		public
	// @return		array Vetor com os elementos da lista
	//!-----------------------------------------------------------------
	function toArray() {
		return $this->elements;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::toString
	// @desc		Exibe a representação string da lista
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function toString() {
		return sprintf("AbstractList object{\n%s\n}", dumpArray($this->elements));
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::getModCount
	// @desc		Busca o número de modificações estruturais e de movimentação da lista
	// @access		public
	// @return		int Número de modificações
	//!-----------------------------------------------------------------
	function getModCount() {
		return $this->modCount;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractList::modifiedSince
	// @desc		Verifica se a lista foi modificada a partir de um limite
	// @access		public
	// @param		count int	"0"	Número de modificações
	// @return		bool
	// @note		Este método pode ser utilizado para comparar o valor
	//				capturado em AbstractList::getModCount com o valor atual
	//				após a execução de uma operação
	//!-----------------------------------------------------------------
	function modifiedSince($count=0) {
		return $this->modCount > $count;
	}
}
?>