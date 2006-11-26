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
// $Header: /www/cvsroot/php2go/core/data/adapter/DataSetArray.class.php,v 1.4 2006/04/05 23:43:22 mpont Exp $
// $Date: 2006/04/05 23:43:22 $

//-----------------------------------------
import('php2go.data.adapter.DataAdapter');
import('php2go.util.AbstractList');
//-----------------------------------------

//!-----------------------------------------------------------------
// @class		DataSetArray
// @desc		Implementa um adaptador capaz de construir um DataSet baseado em
//				um array bidimensional.<br><br>
//				Este array deve ser indexado numericamente e cada entrada deste array representa
//				um registro, podendo ser um array, um objeto ou um valor escalar. Porйm, para fins
//				de determinaзгo dos nomes das colunas do registro, somente arrays associativos e objetos 
//				poderгo ser interpretados como um conjunto de colunas=>valores
// @package		php2go.data.adapter
// @extends		DataAdapter
// @uses		AbstractList
// @uses		ListIterator
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class DataSetArray extends DataAdapter
{
	var $Iterator;		// @var Iterator ListIterator object	Objeto Iterator para navegaзгo nos registros do conjunto
	var $fields;		// @var fields array					Armazena as colunas do registro apontado pelo cursor
	var $recordType;	// @var recordType string				Tipo de dado de cada registro do DataSet
	var $eof = TRUE;	// @var eof bool						"FALSE" Indica se o final do conjunto de dados foi alcanзado
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::DataSetArray
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"array()" Vetor de parвmetros de inicializaзгo	
	//!-----------------------------------------------------------------
	function DataSetArray($params=array()) {
		parent::DataAdapter($params);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::load
	// @desc		Carrega os dados do dataset a partir de um array
	// @access		public
	// @param		arr array	Array contendo os dados
	// @note		O array deve ser bidimensional e indexado numericamente
	// @return		bool	
	//!-----------------------------------------------------------------
	function load($arr) {
		if (TypeUtils::isArray($arr)) {
			$content = $arr;
			if (empty($content)) {
				$this->recordCount = 0;
			} else {
				$this->recordCount = sizeof($content);
				$DataList = new AbstractList($content);
				$this->Iterator =& $DataList->iterator();
				$this->fields = $this->Iterator->next();
				$this->_setFieldProperties();
				$this->eof = FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::loadSubSet
	// @desc		Carrega para o dataset um subconjunto dos dados armazenados no array original,
	//				baseado no deslocamento e tamanho fornecidos
	// @access		public
	// @param		offset int		Deslocamento a partir do inнcio do conjunto (baseado em zero)
	// @param		size int		Tamanho do subconjunto	
	// @param		arr array		Array contendo os dados
	// @note		O array deve ser bidimensional e indexado numericamente
	// @return		bool	
	//!-----------------------------------------------------------------
	function loadSubSet($offset, $size, $arr) {
		if (TypeUtils::isArray($arr)) {
			$content = $arr;
			if (empty($content)) {
				$this->recordCount = 0;
			} else {
				$subSet = array_slice($content, $offset, $size);
				if (sizeof($subSet) > 0) {
					$this->recordCount = sizeof($subSet);
					$this->totalRecordCount = sizeof($content);					
					$DataList = new AbstractList($subSet);
					$this->Iterator =& $DataList->iterator();
					$this->fields = $this->Iterator->next();
					$this->_setFieldProperties();
					$this->eof = FALSE;
				} else {
					$this->recordCount = 0;
				}
			}
			return TRUE;
		}
		return FALSE;
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::getField
	// @desc		Retorna o valor de um determinado campo a partir de seu нndice ou de seu nome
	// @access		public
	// @param		fieldId mixed	
	//!-----------------------------------------------------------------
	function getField($fieldId) {
		if (!TypeUtils::isNull($this->Iterator)) {
			if ($this->recordType == 'object')
				return (array_key_exists($fieldId, get_object_vars($this->fields)) ? $this->fields->{$fieldId} : NULL);
			else
				return (array_key_exists($fieldId, $this->fields) ? $this->fields[$fieldId] : NULL);
		}			
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::getAbsolutePosition
	// @desc		Retorna a posiзгo atual do cursor de registros
	// @access		public
	// @return		int Posiзгo atual do cursor
	//!-----------------------------------------------------------------
	function getAbsolutePosition() {
		if (!TypeUtils::isNull($this->Iterator))
			return $this->Iterator->getCurrentIndex();
		return 0;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::current
	// @desc		Retorna o registro apontado pela posiзгo atual do cursor
	// @access		public
	// @return		array Vetor contendo o registro atual
	//!-----------------------------------------------------------------
	function current() {
		if (!TypeUtils::isNull($this->Iterator))
			return $this->fields;
		return array();
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::fetch
	// @desc		Retorna um vetor contendo o registro atual
	// @access		public
	// @return		array Vetor contendo o registro atual
	//!-----------------------------------------------------------------
	function fetch() {
		if (!TypeUtils::isNull($this->Iterator) && !$this->eof()) {
			$dataArray = $this->fields;
			$this->moveNext();
			return $dataArray;
		}
		return array();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::fetchInto
	// @desc		Copia para o vetor passado no parвmetro $dataArray o
	//				conteъdo do registro atual
	// @access		public
	// @param		&dataArray array	Vetor para armazenamento do registro
	// @return		bool
	//!-----------------------------------------------------------------
	function fetchInto(&$dataArray) {
		if (!TypeUtils::isNull($this->Iterator) && !$this->eof()) {
			$dataArray = $this->fields;
			$this->moveNext();
			return TRUE;
		}
		return FALSE;
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::eof
	// @desc		Verifica se o final do conjunto de resultados foi alcanзado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function eof() {
		if (!TypeUtils::isNull($this->Iterator))
			return $this->eof;
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::move
	// @desc		Move o cursor para uma determinada posiзгo
	// @access		public
	// @param		recordNumber int	Nъmero do registro
	// @return		bool
	//!-----------------------------------------------------------------
	function move($recordNumber) {
		if (!TypeUtils::isNull($this->Iterator) && TypeUtils::isInteger($recordNumber)) {
			if ($this->Iterator->moveToIndex($recordNumber)) {
				$this->fields = $this->Iterator->next();
				$this->eof = FALSE;
				return TRUE;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::moveNext
	// @desc		Move o cursor para a prуxima posiзгo, se existente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function moveNext() {
		if (!TypeUtils::isNull($this->Iterator) && $this->Iterator->hasNext()) {
			$this->fields = $this->Iterator->next();
			return TRUE;
		}
		$this->eof = TRUE;
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::movePrevious
	// @desc		Move o cursor para a posiзгo anterior, se existente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function movePrevious() {
		if (!TypeUtils::isNull($this->Iterator) && $this->getAbsolutePosition() > 0) {
			$this->fields = $this->Iterator->previous();
			if ($this->eof())
				$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetArray::_setFieldProperties
	// @desc		Define os campos, nomes de campos e quantidade de campos,
	//				dependendo do tipo de cada entrada do array correspondente
	//				a um registro do DataSet
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _setFieldProperties() {
		$this->recordType = TypeUtils::getType($this->fields);
		if ($this->recordType == 'array') {
			$this->fieldNames = array_keys($this->fields);
			$this->fieldCount = sizeof($this->fieldNames);
		} elseif ($this->recordType == 'object') {
			$this->fieldNames = array_keys(get_object_vars($this->fields));
			$this->fieldCount = sizeof($this->fieldNames);
		} else {
			$this->fieldNames = array(0);
			$this->fieldCount = 1;
		}
	}
}
?>