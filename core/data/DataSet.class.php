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
// $Header: /www/cvsroot/php2go/core/data/DataSet.class.php,v 1.13 2006/10/26 04:39:09 mpont Exp $
// $Date: 2006/10/26 04:39:09 $

//!-----------------------------------------------------------------
// @class		DataSet
// @desc		A classe DataSet � uma interface para a constru��o de conjuntos
//				de dados atrav�s dos quais � poss�vel navegar utilizando um ponteiro,
//				permitindo a cria��o de itera��es sobre estes dados.<br>
//				Para tal, existe um conjunto de <b>adaptadores de dados</b> capazes
//				de montar e manipular um DataSet partindo de diversas fontes: banco de dados,
//				arquivo CSV, arquivo XML ou arrays.
// @package		php2go.data
// @extends		Component
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.13 $
//!-----------------------------------------------------------------
class DataSet extends Component
{
	var $adapter = NULL;	// @var adapter mixed			"NULL" Objeto que representa o adaptador de dados da classe
	var $adapterType;		// @var adapterType string		Tipo do adaptador de dados (db, csv, xml, array)

	//!-----------------------------------------------------------------
	// @function	DataSet::DataSet
	// @desc		Construtor da classe
	// @access		public
	// @param		type string		Tipo de adaptador (db, csv, xml, array)
	// @param		params array	"array()" Par�metros de inicializa��o do adaptador
	//!-----------------------------------------------------------------
	function DataSet($type, $params=array()) {
		parent::Component();
		$type = ucfirst(strtolower($type));
		$adapterClass = 'DataSet' . $type;
		if (!empty($type) && import("php2go.data.adapter.{$adapterClass}")) {
			$this->adapter = new $adapterClass($params);
			$this->adapterType = $type;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATASET_INVALID_TYPE', $type), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::&factory
	// @desc		M�todo factory: pode ser utilizado para construir
	//				diferentes tipos de dataset a partir dos par�metros
	//				$type (tipo de adaptador) e $params (argumentos para
	//				cria��o do dataset)
	// @param		type string		Tipo de adaptador (db, csv, xml, array)
	// @param		params array	"array()" Par�metros de inicializa��o do adaptador
	// @note		Par�metros do tipo "db": debug (bool), connectionId (string)<br>
	//				Par�metros do tipo "xml": nenhum<br>
	//				Par�metros do tipo "csv": nenhum<br>
	//				Par�metros do tipo "array": nenhum
	// @return		DataSet object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function &factory($type, $params=array()) {
		$type = strtolower($type);
		$params = (array)$params;
		$instance = new DataSet($type, $params);
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::&getInstance
	// @desc		Retorna uma inst�ncia �nica de um determinado tipo de DataSet
	// @param		type string		Tipo de adaptador (db, csv, xml, array)
	// @param		params array	"array()" Par�metros de inicializa��o do adaptador
	// @note		Par�metros do tipo "db": debug (bool), connectionId (string)<br>
	//				Par�metros do tipo "xml": nenhum<br>
	//				Par�metros do tipo "csv": nenhum<br>
	//				Par�metros do tipo "array": nenhum
	// @return		DataSet object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function &getInstance($type, $params=array()) {
		static $instances;
		if (!isset($instances))
			$instances = array();
		$type = strtolower($type);
		$params = (array)$params;
		$hash = $type . serialize(ksort($params));
		if (!isset($instances[$hash]))
			$instances[$hash] = new DataSet($type, $params);
		return $instances[$hash];
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::load
	// @desc		Este m�todo recebe uma quantidade vari�vel de par�metros
	//				dependendo do adaptador de dados utilizado. A partir dos par�metros
	//				recebidos, o m�todo load() interno ao adaptador � executado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function load() {
		$args = func_get_args();
		@call_user_func_array(array(&$this->adapter, 'load'), $args);
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::loadSubSet
	// @desc		Este m�todo recebe uma quantidade vari�vel de par�metros
	//				dependendo do adaptador de dados utilizado. A partir dos par�metros
	//				fornecidos, o m�todo loadSubSet() interno ao adaptador � executado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadSubSet() {
		$args = func_get_args();
		@call_user_func_array(array(&$this->adapter, 'loadSubSet'), $args);
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::getFieldCount
	// @desc		Busca o n�mero de colunas/campos do DataSet criado
	// @return		int N�mero de campos
	// @see			DataSet::getRecordCount
	// @access		public
	//!-----------------------------------------------------------------
	function getFieldCount() {
		return $this->adapter->getFieldCount();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::getFieldNames
	// @desc		Monta um vetor contendo os nomes dos campos do DataSet
	// @return		array Vetor de campos do conjunto de dados
	// @see			DataSet::getFieldNames
	// @access		public
	//!-----------------------------------------------------------------
	function getFieldNames() {
		return $this->adapter->getFieldNames();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::getFieldName
	// @desc		Busca o nome de um determinado campo do conjunto de dados,
	//				a partir de seu �ndice
	// @param		i int	�ndice do campo
	// @return		string Nome do campo buscado
	// @access		public
	//!-----------------------------------------------------------------
	function getFieldName($i) {
		$fieldNames = $this->adapter->getFieldNames();
		return (isset($fieldNames[$i]) ? $fieldNames[$i] : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::getField
	// @desc		Busca o valor de um campo na posi��o atual do cursor
	//				atrav�s de seu �ndice ou de seu nome
	// @param		fieldId mixed	�ndice ou nome do campo buscado
	// @return		mixed	Valor do campo no registro atual
	// @access		public
	//!-----------------------------------------------------------------
	function getField($fieldId) {
		return $this->adapter->getField($fieldId);
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::getRecordCount
	// @desc		Retorna o n�mero de registros do DataSet
	// @access		public
	// @return		int N�mero de registros
	// @see			DataSet::getFieldCount
	//!-----------------------------------------------------------------
	function getRecordCount() {
		return $this->adapter->getRecordCount();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::getAbsolutePosition
	// @desc		Retorna a posi��o atual do cursor
	// @return		int Posi��o atual do cursor
	// @access		public
	//!-----------------------------------------------------------------
	function getAbsolutePosition() {
		return $this->adapter->getAbsolutePosition();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::current
	// @desc		Busca o registro apontado pela posi��o atual do cursor
	// @return		array Vetor contendo dados do registro atual
	// @access		public
	//!-----------------------------------------------------------------
	function current() {
		return $this->adapter->current();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::fetch
	// @desc		Retorna um vetor contendo a linha atual, ou FALSE se
	//				o final do DataSet for atingido
	// @return		mixed Vetor contendo o registro atual ou FALSE
	// @see			DataSet::fetchInto
	// @access		public
	//!-----------------------------------------------------------------
	function fetch() {
		return $this->adapter->fetch();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::fetchInto
	// @desc		Armazena o conte�do do registro atual no vetor passado
	//				atrav�s do par�metro $dataArray. Retorna FALSE se o final
	//				do conjunto de resultados foi atingido
	// @param		&dataArray array	Vetor para armazenamento do registro
	// @see			DataSet::fetch
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function fetchInto(&$dataArray) {
		return $this->adapter->fetchInto($dataArray);
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::eof
	// @desc		Verifica se o final do conjunto de resultados foi atingido
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function eof() {
		return $this->adapter->eof();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::move
	// @desc		Move o ponteiro para um determinado n�mero de registro
	// @param		recordNumber int	N�mero do registro
	// @access		public
	// @return		bool
	// @see			DataSet::movePrevious
	// @see			DataSet::moveNext
	// @see			DataSet::moveFirst
	// @see			DataSet::moveLast
	//!-----------------------------------------------------------------
	function move($recordNumber) {
		return $this->adapter->move($recordNumber);
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::moveNext
	// @desc		Move o ponteiro para o pr�ximo registro
	// @access		public
	// @return		bool
	// @note		Retorna FALSE se o final do DataSet foi atingido
	// @see			DataSet::move
	// @see			DataSet::movePrevious
	// @see			DataSet::moveFirst
	// @see			DataSet::moveLast
	//!-----------------------------------------------------------------
	function moveNext() {
		return $this->adapter->moveNext();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::movePrevious
	// @desc		Move o ponteiro para o registro anterior
	// @access		public
	// @return		bool
	// @note		Retorna FALSE se o in�cio do DataSet foi alcan�ado
	// @see			DataSet::move
	// @see			DataSet::moveNext
	// @see			DataSet::moveFirst
	// @see			DataSet::moveLast
	//!-----------------------------------------------------------------
	function movePrevious() {
		return $this->adapter->movePrevious();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::moveFirst
	// @desc		Move o ponteiro para o primeiro registro
	// @access		public
	// @return		bool
	// @see			DataSet::move
	// @see			DataSet::moveNext
	// @see			DataSet::movePrevious
	// @see			DataSet::moveLast
	//!-----------------------------------------------------------------
	function moveFirst() {
		return $this->adapter->moveFirst();
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::moveLast
	// @desc		Move o ponteiro para o �ltimo registro
	// @access		public
	// @return		bool
	// @see			DataSet::move
	// @see			DataSet::moveNext
	// @see			DataSet::movePrevious
	// @see			DataSet::moveFirst
	//!-----------------------------------------------------------------
	function moveLast() {
		return $this->adapter->moveLast();
	}
}
?>