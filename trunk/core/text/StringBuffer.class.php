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
// $Header: /www/cvsroot/php2go/core/text/StringBuffer.class.php,v 1.9 2006/03/15 04:44:42 mpont Exp $
// $Date: 2006/03/15 04:44:42 $

//------------------------------------------------------------------
import('php2go.text.StringUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		StringBuffer
// @desc		A classe StringBuffer implementa uma seqüência mutável
//				de caracteres. Os métodos implementam operações de inserção,
//				deleção, concatenação e manipulação sobre a seqüência armazenada
// @package		php2go.text
// @extends		PHP2Go
// @uses		StringUtils
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.9 $
//!-----------------------------------------------------------------
class StringBuffer extends PHP2Go
{
	var $string = "";	// @var string string		Contém o buffer de caracteres
	var $capacity;		// @var capacity int		Armazena a capacidade atual do buffer

	//!-----------------------------------------------------------------
	// @function	StringBuffer::StringBuffer
	// @desc		Construtor da classe. Aceita como parâmetros uma string
	//				de inicialização e uma capacidade inicial
	// @access		public
	// @param		initStr string		"" String com a qual o buffer deve ser inicializado
	// @param		initCapacity int	"NULL" Capacidade inicial para o buffer
	// @note		Se não for fornecida uma string e uma capacidade iniciais,
	//				será criado um buffer vazio com capacidade inicial de 16 caracteres
	//!-----------------------------------------------------------------
	function StringBuffer($initStr="", $initCapacity=NULL) {
		parent::PHP2Go();
		$this->capacity = (TypeUtils::parseInteger($initCapacity) > 0) ? $initCapacity : 16;
		if (!empty($initStr)) {
			$this->string = $initStr;
			if (strlen($this->string) > $this->capacity) {
				$this->capacity = strlen($this->string);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::capacity
	// @desc		Busca a capacidade atual, em número de caracteres, do buffer
	// @access		public
	// @return		int	Capacidade atual do buffer
	//!-----------------------------------------------------------------
	function capacity() {
		return $this->capacity;
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::length
	// @desc		Consulta o tamanho atual ocupado pelo buffer de caracteres
	// @access		public
	// @return		int	Tamanho atual do buffer
	//!-----------------------------------------------------------------
	function length() {
		return strlen($this->string);
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::getChars
	// @desc		Os caracteres alocados entre as posições $srcBegin e
	//				$srcEnd do buffer são copiados para a string $dst. Opcionalmente,
	//				pode ser informada uma posição na string $dst onde os caracteres
	//				devem ser copiados (parâmetro $dstBegin)
	// @access		public
	// @param		srcBegin int	Índice inicial do buffer a ser copiado
	// @param		srcEnd int		Índice final do buffer a ser copiado
	// @param		dst string		String destino
	// @param		dstBegin int	"NULL" Posição na string destino
	// @return		void
	//!-----------------------------------------------------------------
	function getChars($srcBegin, $srcEnd, &$dst, $dstBegin=NULL) {
		if (TypeUtils::isInteger($srcBegin) && TypeUtils::isInteger($srcEnd) && $srcBegin >= 0 && $srcEnd >= $srcBegin) {
			$chars = $this->subSequence($srcBegin, $srcEnd);
			$dstBuffer = new StringBuffer($dst);
			$dstBuffer->insert(TypeUtils::ifNull($dstBegin, 0), $chars);
			$dst = $dstBuffer->toString();
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::charAt
	// @desc		O caractere específico da seqüência de caracteres representada
	//				pelo buffer ocupante da posição indicada em $index é retornado
	// @access		public
	// @param		index int	Índice dentro da seqüência de caracteres, com início em zero
	// @return		mixed	Caractere da posição $index ou NULL em caso de erros
	//!-----------------------------------------------------------------
	function charAt($index) {
		if ($index < 0 || $index >= $this->length()) {
			return NULL;
		} else {
			return $this->string{$index};
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::indexOf
	// @desc		Retorna o índice dentro do buffer atual da primeira ocorrência
	//				da string informada no parâmetro $str
	// @access		public
	// @param		str string		String a ser buscada dentro do buffer
	// @param		fromIndex int	"NULL" Índice a partir do qual deve ser feita a busca dentro do buffer
	// @return		int	Retorna a posição da primeira ocorrência de $str
	//				dentro do buffer ou -1 se não for encontrado
	//!-----------------------------------------------------------------
	function indexOf($str, $fromIndex=NULL) {
		if (!TypeUtils::isNull($fromIndex)) {
			if (TypeUtils::isInteger($fromIndex) && $fromIndex >= 0 && $fromIndex < $this->length()) {
				$searchBase = $this->subString($fromIndex);
				$offset = $fromIndex;
			} else {
				$searchBase = $this->string;
				$offset = 0;
			}
		} else {
			$searchBase = $this->string;
			$offset = 0;
		}
		if (StringUtils::match($searchBase, TypeUtils::parseString($str))) {
			return $offset + strpos($searchBase, $str);
		} else {
			return -1;
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::lastIndexOf
	// @desc		Retorna o índice dentro do buffer da última ocorrência
	//				do parâmetro $str
	// @access		public
	// @param		str string		String a ser buscada dentro do buffer
	// @param		fromIndex int	"NULL" Índice a partir do qual a busca deve ser realizada
	// @return		int	Retorna o índice da última ocorrência de $str no
	//				buffer ou -1 se não encontrado
	//!-----------------------------------------------------------------
	function lastIndexOf($str, $fromIndex=NULL) {
		if (!TypeUtils::isNull($fromIndex)) {
			if (TypeUtils::isInteger($fromIndex) && $fromIndex >= 0 && $fromIndex < $this->length()) {
				$searchBase = $this->subString($fromIndex);
				$offset = $fromIndex;
			} else {
				$searchBase = $this->string;
				$offset = 0;
			}
		} else {
			$searchBase = $this->string;
			$offset = 0;
		}
		if (StringUtils::match($searchBase, TypeUtils::parseString($str))) {
			return $offset + strrpos($searchBase, $str);
		} else {
			return -1;
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::subString
	// @desc		Retorna uma nova string que contém uma subseqüência de
	//				caracteres contidas no buffer atual. A string inicia no
	//				índice especificado em $start e vai até o fim do buffer
	// @access		public
	// @param		start int	Índice inicial para a nova string
	// @return		string	Subseqüência resultante
	//!-----------------------------------------------------------------
	function subString($start) {
		if (TypeUtils::isInteger($start) && $start >= 0 && $start < $this->length()) {
			return substr($this->string, $start);
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::subSequence
	// @desc		Constrói uma subseqüência de caracteres baseada nos índices
	//				informados em $start e $end
	// @access		public
	// @param		start int	Índice inicial para a nova seqüência
	// @param		end int		Índice final para a nova seqüência
	// @return		string	Subseqüência criada
	//!-----------------------------------------------------------------
	function subSequence($start, $end) {
		if (TypeUtils::isInteger($start) && TypeUtils::isInteger($end) && $start >= 0 && $end >= $start) {
			return substr($this->string, $start, ($end-$start+1));
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::setCharAt
	// @desc		Altera o valor do caractere cujo índice é igual ao valor
	//				do parâmetro $index
	// @access		public
	// @param		index int	Índice dentro da seqüência de caracteres, com início em zero
	// @param		ch string	O novo caractere para a posição
	// @return		void
	//!-----------------------------------------------------------------
	function setCharAt($index, $ch) {
		if ($index >= 0 && $index < $this->length() && strlen($ch) == 1)
			$this->string{$index} = $ch;
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::setLength
	// @desc		Define um novo comprimento para o buffer de caracteres.
	//				O buffer é alterado para representar uma nova seqüência de
	//				caracteres cujo comprimento é informado no parâmetro $newLength
	// @access		public
	// @param		newLength int	Novo comprimento para o buffer
	// @return		void
	// @note		Se $newLength for maior do que o comprimento atual do buffer,
	//				um número suficiente de caracteres nulos "\x00" será inserido
	//				para completar o tamanho necessário. Se for menor, a seqüência
	//				armazenada no buffer será truncada
	//!-----------------------------------------------------------------
	function setLength($newLength) {
		if (TypeUtils::isInteger($newLength) && $newLength > 0) {
			if ($newLength > $this->length()) {
				$this->string = str_pad($this->string, $newLength, "\x00", STR_PAD_RIGHT);
				if ($newLength > $this->capacity()) {
					$this->capacity = $newLength;
				}
			} else {
				$this->string = StringUtils::left($this->string, $newLength);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::append
	// @desc		Concatena a representação string de uma variável ao conteúdo
	//				do buffer, atualizando a capacidade do mesmo se ela for excedida
	// @access		public
	// @param		appendValue mixed	Valor a ser concatenado ao buffer
	// @return		void
	//!-----------------------------------------------------------------
	function append($appendValue) {
		if (TypeUtils::isObject($appendValue)) {
			$this->string .= $appendValue->toString();
		} else if (TypeUtils::isArray($appendValue) || TypeUtils::isResource($appendValue) || TypeUtils::isBoolean($appendValue)) {
			$this->string .= var_export($appendValue, TRUE);
		} else {
			$this->string .= TypeUtils::parseString($appendValue);
		}
		if (strlen($this->string) > $this->capacity) {
			$this->capacity = strlen($this->string);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::insert
	// @desc		Insere a representação string do parâmetro $insertValue
	//				no buffer, na posição indicada em $index
	// @access		public
	// @param		index int			Índice onde a string deve ser inserida no buffer
	// @param		insertValue string	Valor a ser inserido no buffer
	// @return		void
	//!-----------------------------------------------------------------
	function insert($index, $insertValue) {
		if (TypeUtils::isInteger($index) && $index >= 0 && $index <= $this->length()) {
			if (TypeUtils::isObject($insertValue)) {
				$insertValue = $insertValue->toString();
			} else if (TypeUtils::isArray($insertValue) || TypeUtils::isResource($insertValue) || TypeUtils::isBoolean($insertValue)) {
				$insertValue = var_export($insertValue, TRUE);
			} else {
				$insertValue = TypeUtils::parseString($insertValue);
			}
			$this->string = $this->subSequence(0, $index-1) . $insertValue . $this->subString($index);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::delete
	// @desc		Remove um conjunto de caracteres do buffer atual, a partir
	//				dos índices inicial e final
	// @access		public
	// @param		start int	Índice inicial
	// @param		end int		Índice final
	// @return		void
	//!-----------------------------------------------------------------
	function delete($start, $end) {
		if (TypeUtils::isInteger($start) && TypeUtils::isInteger($end) && $start >= 0 && $end >= $start) {
			$this->string = $this->subSequence(0, $start-1) . $this->subString($end);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::deleteCharAt
	// @desc		Remove o caractere que ocupa a posição indicada em $index,
	//				diminuindo o tamanho do buffer em uma unidade
	// @access		public
	// @param		index int	Índice a ser removido
	// @return		void
	// @note		O índice deve ser maior ou igual a zero e inferior ao tamanho
	//				atual do buffer
	//!-----------------------------------------------------------------
	function deleteCharAt($index) {
		if (TypeUtils::isInteger($index) && $index >= 0 && $index < $this->length()) {
			$this->delete($index, $index+1);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::ensureCapacity
	// @desc		Verifica se a capacidade do buffer é ao menos igual ao
	//				mínimo informado no parâmetro $minimum
	// @access		public
	// @param		minimum int	Tamanho mínimo exigido para o buffer
	// @return		void
	// @note		Se o valor de $minimum não for um número inteiro ou for
	//				negativo, nenhuma ação é tomada. Do contrário, a nova capacidade
	//				do buffer será o maior valor entre $minimum e o dobro da
	//				capacidade atual mais dois
	//!-----------------------------------------------------------------
	function ensureCapacity($minimum) {
		if (TypeUtils::isInteger($minimum) && $minimum > 0) {
			$this->capacity = max($minimum, ($this->capacity()*2)+2);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::reverse
	// @desc		A seqüência de caracteres contida no buffer é substituída
	//				pelo inverso da mesma seqüência
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function reverse() {
		$this->string = strrev($this->string);
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::toString
	// @desc		Retorna o conteúdo atual do buffer, na forma de uma string
	// @access		public
	// @return		string	Conteúdo do buffer
	//!-----------------------------------------------------------------
	function toString() {
		return $this->string;
	}
}
?>