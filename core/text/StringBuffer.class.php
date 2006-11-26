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
// @desc		A classe StringBuffer implementa uma seq��ncia mut�vel
//				de caracteres. Os m�todos implementam opera��es de inser��o,
//				dele��o, concatena��o e manipula��o sobre a seq��ncia armazenada
// @package		php2go.text
// @extends		PHP2Go
// @uses		StringUtils
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.9 $
//!-----------------------------------------------------------------
class StringBuffer extends PHP2Go
{
	var $string = "";	// @var string string		Cont�m o buffer de caracteres
	var $capacity;		// @var capacity int		Armazena a capacidade atual do buffer

	//!-----------------------------------------------------------------
	// @function	StringBuffer::StringBuffer
	// @desc		Construtor da classe. Aceita como par�metros uma string
	//				de inicializa��o e uma capacidade inicial
	// @access		public
	// @param		initStr string		"" String com a qual o buffer deve ser inicializado
	// @param		initCapacity int	"NULL" Capacidade inicial para o buffer
	// @note		Se n�o for fornecida uma string e uma capacidade iniciais,
	//				ser� criado um buffer vazio com capacidade inicial de 16 caracteres
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
	// @desc		Busca a capacidade atual, em n�mero de caracteres, do buffer
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
	// @desc		Os caracteres alocados entre as posi��es $srcBegin e
	//				$srcEnd do buffer s�o copiados para a string $dst. Opcionalmente,
	//				pode ser informada uma posi��o na string $dst onde os caracteres
	//				devem ser copiados (par�metro $dstBegin)
	// @access		public
	// @param		srcBegin int	�ndice inicial do buffer a ser copiado
	// @param		srcEnd int		�ndice final do buffer a ser copiado
	// @param		dst string		String destino
	// @param		dstBegin int	"NULL" Posi��o na string destino
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
	// @desc		O caractere espec�fico da seq��ncia de caracteres representada
	//				pelo buffer ocupante da posi��o indicada em $index � retornado
	// @access		public
	// @param		index int	�ndice dentro da seq��ncia de caracteres, com in�cio em zero
	// @return		mixed	Caractere da posi��o $index ou NULL em caso de erros
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
	// @desc		Retorna o �ndice dentro do buffer atual da primeira ocorr�ncia
	//				da string informada no par�metro $str
	// @access		public
	// @param		str string		String a ser buscada dentro do buffer
	// @param		fromIndex int	"NULL" �ndice a partir do qual deve ser feita a busca dentro do buffer
	// @return		int	Retorna a posi��o da primeira ocorr�ncia de $str
	//				dentro do buffer ou -1 se n�o for encontrado
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
	// @desc		Retorna o �ndice dentro do buffer da �ltima ocorr�ncia
	//				do par�metro $str
	// @access		public
	// @param		str string		String a ser buscada dentro do buffer
	// @param		fromIndex int	"NULL" �ndice a partir do qual a busca deve ser realizada
	// @return		int	Retorna o �ndice da �ltima ocorr�ncia de $str no
	//				buffer ou -1 se n�o encontrado
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
	// @desc		Retorna uma nova string que cont�m uma subseq��ncia de
	//				caracteres contidas no buffer atual. A string inicia no
	//				�ndice especificado em $start e vai at� o fim do buffer
	// @access		public
	// @param		start int	�ndice inicial para a nova string
	// @return		string	Subseq��ncia resultante
	//!-----------------------------------------------------------------
	function subString($start) {
		if (TypeUtils::isInteger($start) && $start >= 0 && $start < $this->length()) {
			return substr($this->string, $start);
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::subSequence
	// @desc		Constr�i uma subseq��ncia de caracteres baseada nos �ndices
	//				informados em $start e $end
	// @access		public
	// @param		start int	�ndice inicial para a nova seq��ncia
	// @param		end int		�ndice final para a nova seq��ncia
	// @return		string	Subseq��ncia criada
	//!-----------------------------------------------------------------
	function subSequence($start, $end) {
		if (TypeUtils::isInteger($start) && TypeUtils::isInteger($end) && $start >= 0 && $end >= $start) {
			return substr($this->string, $start, ($end-$start+1));
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::setCharAt
	// @desc		Altera o valor do caractere cujo �ndice � igual ao valor
	//				do par�metro $index
	// @access		public
	// @param		index int	�ndice dentro da seq��ncia de caracteres, com in�cio em zero
	// @param		ch string	O novo caractere para a posi��o
	// @return		void
	//!-----------------------------------------------------------------
	function setCharAt($index, $ch) {
		if ($index >= 0 && $index < $this->length() && strlen($ch) == 1)
			$this->string{$index} = $ch;
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::setLength
	// @desc		Define um novo comprimento para o buffer de caracteres.
	//				O buffer � alterado para representar uma nova seq��ncia de
	//				caracteres cujo comprimento � informado no par�metro $newLength
	// @access		public
	// @param		newLength int	Novo comprimento para o buffer
	// @return		void
	// @note		Se $newLength for maior do que o comprimento atual do buffer,
	//				um n�mero suficiente de caracteres nulos "\x00" ser� inserido
	//				para completar o tamanho necess�rio. Se for menor, a seq��ncia
	//				armazenada no buffer ser� truncada
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
	// @desc		Concatena a representa��o string de uma vari�vel ao conte�do
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
	// @desc		Insere a representa��o string do par�metro $insertValue
	//				no buffer, na posi��o indicada em $index
	// @access		public
	// @param		index int			�ndice onde a string deve ser inserida no buffer
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
	//				dos �ndices inicial e final
	// @access		public
	// @param		start int	�ndice inicial
	// @param		end int		�ndice final
	// @return		void
	//!-----------------------------------------------------------------
	function delete($start, $end) {
		if (TypeUtils::isInteger($start) && TypeUtils::isInteger($end) && $start >= 0 && $end >= $start) {
			$this->string = $this->subSequence(0, $start-1) . $this->subString($end);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::deleteCharAt
	// @desc		Remove o caractere que ocupa a posi��o indicada em $index,
	//				diminuindo o tamanho do buffer em uma unidade
	// @access		public
	// @param		index int	�ndice a ser removido
	// @return		void
	// @note		O �ndice deve ser maior ou igual a zero e inferior ao tamanho
	//				atual do buffer
	//!-----------------------------------------------------------------
	function deleteCharAt($index) {
		if (TypeUtils::isInteger($index) && $index >= 0 && $index < $this->length()) {
			$this->delete($index, $index+1);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::ensureCapacity
	// @desc		Verifica se a capacidade do buffer � ao menos igual ao
	//				m�nimo informado no par�metro $minimum
	// @access		public
	// @param		minimum int	Tamanho m�nimo exigido para o buffer
	// @return		void
	// @note		Se o valor de $minimum n�o for um n�mero inteiro ou for
	//				negativo, nenhuma a��o � tomada. Do contr�rio, a nova capacidade
	//				do buffer ser� o maior valor entre $minimum e o dobro da
	//				capacidade atual mais dois
	//!-----------------------------------------------------------------
	function ensureCapacity($minimum) {
		if (TypeUtils::isInteger($minimum) && $minimum > 0) {
			$this->capacity = max($minimum, ($this->capacity()*2)+2);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::reverse
	// @desc		A seq��ncia de caracteres contida no buffer � substitu�da
	//				pelo inverso da mesma seq��ncia
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function reverse() {
		$this->string = strrev($this->string);
	}

	//!-----------------------------------------------------------------
	// @function	StringBuffer::toString
	// @desc		Retorna o conte�do atual do buffer, na forma de uma string
	// @access		public
	// @return		string	Conte�do do buffer
	//!-----------------------------------------------------------------
	function toString() {
		return $this->string;
	}
}
?>