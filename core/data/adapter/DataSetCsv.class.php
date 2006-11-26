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
// $Header: /www/cvsroot/php2go/core/data/adapter/DataSetCsv.class.php,v 1.3 2006/02/28 21:55:51 mpont Exp $
// $Date: 2006/02/28 21:55:51 $

//-----------------------------------------
import('php2go.data.adapter.DataAdapter');
import('php2go.util.AbstractList');
//-----------------------------------------

//!-----------------------------------------------------------------
// @class		DataSetCsv
// @desc		Esta classe funciona como um adaptador capaz de construir
//				e manipular um conjunto de dados proveniente de um arquivo
//				CSV (comma separated values), onde a primeira linha do arquivo
//				define os nomes das colunas e as subseqüentes definem os registros
//				do conjunto
// @package		php2go.data.adapter
// @extends		DataAdapter
// @uses		AbstractList
// @uses		ListIterator
// @uses		FileManager
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class DataSetCsv extends DataAdapter 
{
	var $Iterator;		// @var Iterator ListIterator object	Objeto Iterator para navegação nos registros do conjunto
	var $fields;		// @var fields array					Armazena as colunas do registro apontado pelo cursor
	var $eof = TRUE;	// @var eof bool						"FALSE" Indica se o final do conjunto de dados foi alcançado
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::DataSetCsv
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"array()" Vetor de parâmetros de inicialização
	//!-----------------------------------------------------------------
	function DataSetCsv($params=array()) {
		parent::DataAdapter($params);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::load
	// @desc		Constrói o conjunto de dados a partir do caminho completo
	//				de um arquivo CSV contendo os dados a serem carregados
	// @param		fileName string		Caminho completo do arquivo CSV
	// @note		O CSV deve estar formatado de maneira que a primeira linha
	//				represente o "cabeçalho" do conjunto de dados, contendo os nomes
	//				das colunas, e as outras representem os dados
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function load($fileName) {
		$content = @file($fileName);
		if (!$content)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		if (sizeof($content) > 1) {
			$this->fieldNames = explode(',', str_replace(array("\"", "'"), array('', ''), trim($content[0])));
			$this->fieldCount = sizeof($this->fieldNames);
			array_shift($content);
			$this->recordCount = sizeof($content);
			$DataList = new AbstractList($content);
			$this->Iterator =& $DataList->iterator();
			$this->fields = $this->_buildRecord($this->Iterator->next());
			$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::loadSubSet
	// @desc		Constrói um subconjunto dos dados contidos em um arquivo
	//				CSV a partir de um deslocamento, um tamanho e o caminho
	//				completo do arquivo no servidor
	// @access		public
	// @param		offset int			Deslocamento a partir do início do conjunto (baseado em zero)
	// @param		size int			Tamanho do subconjunto	
	// @param		fileName string		Caminho completo do arquivo CSV
	// @return		bool
	// @note		O CSV deve estar formatado de maneira que a primeira linha
	//				represente o "cabeçalho" do conjunto de dados, contendo os nomes
	//				das colunas, e as outras representem os dados
	//!-----------------------------------------------------------------
	function loadSubSet($offset, $size, $fileName) {
		$content = @file($fileName);
		if (!$content)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		if (sizeof($content) > 1) {
			$this->fieldNames = explode(',', str_replace(array("\"", "'"), array('', ''), $content[0]));
			$this->fieldCount = sizeof($this->fieldNames);
			array_shift($content);
			$subSet = array_slice($content, $offset, $size);
			if (sizeof($subSet) > 0) {
				$DataList = new AbstractList($subSet);
				$this->Iterator =& $DataList->iterator();
				$this->recordCount = sizeof($subSet);
				$this->totalRecordCount = sizeof($content);				
				$this->fields = $this->_buildRecord($this->Iterator->next());
				$this->eof = FALSE;
				return TRUE;
			}
		}
		return FALSE;
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::getField
	// @desc		Retorna o valor de um determinado campo a partir de seu índice ou de seu nome
	// @access		public
	// @param		fieldId mixed	
	//!-----------------------------------------------------------------
	function getField($fieldId) {
		if (!TypeUtils::isNull($this->Iterator))
			return isset($this->fields[$fieldId]) ? $this->fields[$fieldId] : NULL;						
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::getAbsolutePosition
	// @desc		Retorna a posição atual do cursor de registros
	// @access		public
	// @return		int Posição atual do cursor
	//!-----------------------------------------------------------------
	function getAbsolutePosition() {
		if (!TypeUtils::isNull($this->Iterator))
			return $this->Iterator->getCurrentIndex();
		return 0;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::current
	// @desc		Retorna o registro apontado pela posição atual do cursor
	// @access		public
	// @return		array Vetor contendo o registro atual
	//!-----------------------------------------------------------------
	function current() {
		if (!TypeUtils::isNull($this->Iterator))
			return $this->fields;
		return array();
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::fetch
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
	// @function	DataSetCsv::fetchInto
	// @desc		Copia para o vetor passado no parâmetro $dataArray o
	//				conteúdo do registro atual
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
	// @function	DataSetCsv::eof
	// @desc		Verifica se o final do conjunto de resultados foi alcançado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function eof() {
		if (!TypeUtils::isNull($this->Iterator)) {
			return $this->eof;
		}
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::move
	// @desc		Move o cursor para uma determinada posição
	// @access		public
	// @param		recordNumber int	Número do registro
	// @return		bool
	//!-----------------------------------------------------------------
	function move($recordNumber) {
		if (!TypeUtils::isNull($this->Iterator) && TypeUtils::isInteger($recordNumber)) {
			if ($this->Iterator->moveToIndex($recordNumber)) {
				$this->fields = $this->_buildRecord($this->Iterator->next());
				$this->eof = FALSE;
				return TRUE;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::moveNext
	// @desc		Move o cursor para a próxima posição, se existente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function moveNext() {
		if (!TypeUtils::isNull($this->Iterator) && $this->Iterator->hasNext()) {
			$this->fields = $this->_buildRecord($this->Iterator->next());
			return TRUE;
		}
		$this->eof = TRUE;
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::movePrevious
	// @desc		Move o cursor para a posição anterior, se existente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function movePrevious() {
		if (!TypeUtils::isNull($this->Iterator) && $this->getAbsolutePosition() > 0) {
			$this->fields = $this->_buildRecord($this->Iterator->previous());
			if ($this->eof())
				$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetCsv::_buildRecord
	// @desc		Monta um vetor associativo para uma linha de dados,
	//				a partir dos nomes dos campos e dos valores de uma linha
	// @access		private
	// @param		fileLine string		Representa uma linha no arquivo CSV
	// @return		array Registro montado
	//!-----------------------------------------------------------------
	function _buildRecord($fileLine) {
		// retira delimitador de string
		$preparedLineData = ereg_replace("\"|'", "", $fileLine);
		// cria vetor separando a linha por vírgulas
		$lineArray = explode(',', $preparedLineData);
		$resultRecord = array();
		foreach($this->fieldNames as $index => $name) {
			$resultRecord[$name] = isset($lineArray[$index]) ? $lineArray[$index] : '';
		}
		return $resultRecord;
	}
}
?>