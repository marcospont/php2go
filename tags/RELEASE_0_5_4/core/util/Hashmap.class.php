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
// $Header: /www/cvsroot/php2go/core/util/Hashmap.class.php,v 1.6 2006/05/07 15:23:53 mpont Exp $
// $Date: 2006/05/07 15:23:53 $

//!-----------------------------------------------------------------
// @class		Hashmap
// @desc		Esta classe implementa uma tabela hash, que mapeia chaves para valores.
//				As chaves devem ser nуo nulas e nуo vazias, mas os valores aceitam qualquer informaчуo.
// @package		php2go.util
// @extends		PHP2Go
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class Hashmap extends PHP2Go
{
	var $elements = array();	// @var elements array		Vetor de pares chave=>valor da tabela

	//!-----------------------------------------------------------------
	// @function	Hashmap::Hashmap
	// @desc		Construtor da classe
	// @access		public
	// @param		arr array		"array()" Array associativo para inicializaчуo do objeto
	//!-----------------------------------------------------------------
	function Hashmap($arr = array()) {
		parent::PHP2Go();
		$this->putAll($arr);
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::clear
	// @desc		Remove todos os elementos da tabela
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clear() {
		$this->elements = array();
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::get
	// @desc		Retorna o valor de uma chave da tabela
	// @access		public
	// @param		key string		Nome da chave
	// @param		fallback mixed	"NULL" Valor a ser retornado quando a chave nуo for encontrada
	// @return		mixed Valor da chave ou o valor do parтmetro $fallback
	//!-----------------------------------------------------------------
	function get($key, $fallback=NULL) {
		$key = TypeUtils::parseString($key);
		return (array_key_exists($key, $this->elements) ? $this->elements[$key] : $fallback);
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::assertGet
	// @desc		Executa a operaчуo get forчando o valor buscado para um
	//				determinado tipo. Se o valor nуo se enquadrar no tipo solicitado,
	//				o mщtodo retornarс o valor do parтmetro $fallback, indicando um erro
	// @access		public
	// @param		key string		Nome da chave
	// @param		type string		Tipo desejado
	// @param		fallback mixed	"NULL" Valor de fallback, para chaves nуo encontradas ou nуo compatэveis com o tipo desejado
	// @return		mixed Valor buscado, ou valor de fallback
	//!-----------------------------------------------------------------
	function assertGet($key, $type, $fallback=NULL) {
		$value = $this->get($key, $fallback);
		if ($value !== $fallback) {
			switch ($type) {
				case 'string' : return (TypeUtils::isString($value) ? $value : $fallback);
				case 'integer' : return (TypeUtils::isInteger($value) ? $value : $fallback);
				case 'float' :
				case 'double' : return (TypeUtils::isFloat($value) ? $value : $fallback);
				case 'array' : return (TypeUtils::isArray($value) ? $value : $fallback);
				case 'hash' : return (TypeUtils::isHashArray($value) ? $value : $fallback);
				case 'resource' : return (TypeUtils::isResource($value) ? $value : $fallback);
				case 'boolean' :
				case 'bool' : return (TypeUtils::isBoolean($value) ? $value : $fallback);
				case 'object' : return (TypeUtils::isObject($value) ? $value : $fallback);
				case 'null' : return (TypeUtils::isNull($value) ? $value : $fallback);
				default : return $value;
			}
		}
		return $fallback;
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::assertType
	// @desc		Verifica a existъncia de uma chave na tabela, e verifica
	//				se ela se enquadra em um determinado tipo de dado
	// @access		public
	// @param		key string		Nome da chave
	// @param		type string		Tipo
	// @return		bool
	// @note		Tipos aceitos: string, integer, float, double, array, hash,
	//				resource, boolean, object e null
	//!-----------------------------------------------------------------
	function assertType($key, $type) {
		$value = $this->get($key);
		$type = strtolower($type);
		switch ($type) {
			case 'string' : return TypeUtils::isString($value);
			case 'integer' : return TypeUtils::isInteger($value);
			case 'float' :
			case 'double' : return TypeUtils::isFloat($value);
			case 'array' : return TypeUtils::isArray($value);
			case 'hash' : return TypeUtils::isHashArray($value);
			case 'resource' : return TypeUtils::isResource($value);
			case 'boolean' :
			case 'bool' : return TypeUtils::isBoolean($value);
			case 'object' : return TypeUtils::isObject($value);
			case 'null' : return TypeUtils::isNull($value);
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::keys
	// @desc		Retorna o array das chaves da tabela
	// @access		public
	// @return		array
	// @see			Hashmap::values
	//!-----------------------------------------------------------------
	function keys() {
		return array_keys($this->elements);
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::values
	// @desc		Retorna o array de valores da tabela
	// @access		public
	// @return		array
	// @see			Hashmap::keys
	//!-----------------------------------------------------------------
	function values() {
		return array_values($this->elements);
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::containsKey
	// @desc		Verifica a existъncia de uma chave
	// @access		public
	// @param		key string		Nome da chave
	// @return		bool
	// @see			Hashmap::containsValue
	//!-----------------------------------------------------------------
	function containsKey($key) {
		return (array_key_exists($key, $this->elements));
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::containsValue
	// @desc		Verifica a existъncia (ao menos uma vez) de um valor na tabela
	// @access		public
	// @param		value mixed		Valor a ser buscado
	// @param		strict bool		"FALSE" Verifica equivalъncia de tipos
	// @return		bool
	// @see			Hashmap::containsKey
	//!-----------------------------------------------------------------
	function containsValue($value, $strict=FALSE) {
		return (array_search($value, $this->elements, $strict));
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::put
	// @desc		Insere ou altera uma chave da tabela
	// @access		public
	// @param		key string		Nome da chave
	// @param		value mixed		Valor da chave
	// @return		bool
	//!-----------------------------------------------------------------
	function put($key, $value) {
		$key = TypeUtils::parseString($key);
		if (!empty($key)) {
			$this->elements[$key] = $value;
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::putRef
	// @desc		Implementaчуo especial do mщtodo put que recebe o valor da chave
	//				por referъncia - ideal para o armazenamento de objetos e valores
	//				que serуo posteriormente alterados
	// @access		public
	// @param		key string		Nome da chave
	// @param		&value mixed	Valor a ser inserido
	// @return		bool
	//!-----------------------------------------------------------------
	function putRef($key, &$value) {
		$key = TypeUtils::parseString($key);
		if (!empty($key)) {
			$this->elements[$key] =& $value;
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::putAll
	// @desc		Adiciona ou atualiza uma coleчуo de chaves na tabela
	// @param		collection mixed	Uma outra instтncia da classe Hashmap ou um array associativo
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function putAll($collection) {
		$result = TRUE;
		if (TypeUtils::isInstanceOf($collection, 'Hashmap'))
			$collection = $collection->toArray();
		if (TypeUtils::isHashArray($collection)) {
			foreach ($collection as $key => $value)
				$result = ($result && $this->put($key, $value));
		}
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::merge
	// @desc		Une a coleчуo atual de chaves com outra, impedindo sobrescrita
	// @param		collection mixed	Instтncia de Hashmap ou array associativo
	// @param		recursive bool		"FALSE" Unir as tabelas recursivamente
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function merge($collection, $recursive=FALSE) {
		if (TypeUtils::isInstanceOf($collection, 'Hashmap'))
			$collection = $collection->toArray();
		if (TypeUtils::isHashArray($collection)) {
			if ($recursive)
				$this->elements = array_merge_recursive($collection, $this->elements);
			else
				$this->elements = array_merge($collection, $this->elements);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::swap
	// @desc		Inverte os valores de duas chaves da tabela
	// @access		public
	// @param		a string			Nome da primeira chave
	// @param		b string			Nome da segunda chave
	// @return		bool
	// @note		Este mщtodo retornarс TRUE apenas se as duas chaves jс existirem na tabela
	//!-----------------------------------------------------------------
	function swap($a, $b) {
		if ($this->containsKey($a) && $this->containsKey($b)) {
			$tmp = $this->get($a);
			$this->put($a, $this->get($b));
			$this->put($b, $tmp);
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::remove
	// @desc		Remove uma determinada chave da tabela
	// @access		public
	// @param		key string		Nome da chave
	// @return		bool
	//!-----------------------------------------------------------------
	function remove($key) {
		if ($this->containsKey($key)) {
			unset($this->elements[$key]);
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::sort
	// @desc		Ordena a tabela pelas chaves, de forma ascendente
	// @access		public
	// @param		flags int		Flags de ordenaчуo
	// @return		void
	// @note		Para maiores informaчѕes sobre o parтmetro $flags, consulte
	//				a documentaчуo da funчуo sort em http://www.php.net/sort
	// @see			Hashmap::reverseSort
	// @see			Hashmap::customSort
	//!-----------------------------------------------------------------
	function sort($flags=SORT_REGULAR) {
		sort($this->elements, $flags);
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::reverseSort
	// @desc		Ordena a tabela pelas chaves, de forma descendente
	// @access		public
	// @param		flags int		Flags de ordenaчуo
	// @return		void
	// @see			Hashmap::sort
	// @see			Hashmap::customSort
	//!-----------------------------------------------------------------
	function reverseSort($flags=SORT_REGULAR) {
		rsort($this->elements, $flags);
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::customSort
	// @desc		Ordena a tabela utilizando uma funчуo de comparaчуo definida pelo usuсrio
	// @access		public
	// @param		callback mixed	Funчуo de comparaчуo
	// @return		void
	//!-----------------------------------------------------------------
	function customSort($callback) {
		usort($this->elements, $callback);
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::size
	// @desc		Retorna o tamanho da tabela
	// @access		public
	// @return		int Tamanho da tabela
	//!-----------------------------------------------------------------
	function size() {
		return sizeof($this->elements);
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::isEmpty
	// @desc		Verifica se a tabela estс vazia
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isEmpty() {
		return ($this->size() == 0);
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::toArray
	// @desc		Retorna a tabela na forma de um array associativo
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function toArray() {
		return $this->elements;
	}

	//!-----------------------------------------------------------------
	// @function	Hashmap::toString
	// @desc		Monta uma representaчуo string da tabela
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function toString() {
		return sprintf("Hashmap object{\n%s\n}", dumpArray($this->elements));
	}
}

?>