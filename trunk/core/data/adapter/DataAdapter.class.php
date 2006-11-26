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
// $Header: /www/cvsroot/php2go/core/data/adapter/DataAdapter.class.php,v 1.6 2006/10/26 04:42:49 mpont Exp $
// $Date: 2006/10/26 04:42:49 $

//!-----------------------------------------------------------------
// @class		DataAdapter
// @desc		A classe DataAdapter й a base para os adaptadores de dados utilizados
//				na classe DataSet e suas classes extendidas. Em suas classes filhas sгo
//				implementadas as funcionalidades de navegaзгo sobre um conjunto de 
//				dados proveniente das diversas fontes: resultados de uma consulta a 
//				banco dados, um arquivo CSV, um arquivo XML ou um array
// @package		php2go.data.adapter
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class DataAdapter extends PHP2Go
{
	var $fieldCount = 0;		// @var fieldCount int			"0" Nъmero de campos do conjunto de resultados
	var $fieldNames = array();	// @var fieldNames array		"array()" Vetor contendo os nomes dos campos do conjunto de dados
	var $params = array();		// @var params array			"array()" Vetor de parвmetros especнficos do provider
	var $recordCount = 0;		// @var recordCount int			"0" Nъmero de linhas do resultado
	var $totalRecordCount = 0;	// @var totalRecordCount int	"0" Utilizado para armazenar o tamanho total do conjunto de dados
	
	//!-----------------------------------------------------------------
	// @function	DataAdapter::DataAdapter
	// @desc		Construtor da classe. Deve ser executado somente pelas
	//				classes filhas
	// @access		public
	// @param		params array	"array()" Vetor de parвmetros de inicializaзгo	
	//!-----------------------------------------------------------------
	function DataAdapter($params=array()) {
		parent::PHP2Go();
		if ($this->isA('DataAdapter', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'DataAdapter'), E_USER_ERROR, __FILE__, __LINE__);
		$this->params = TypeUtils::toArray($params);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataAdapter::getType
	// @desc		Retorna o tipo do adaptador
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getType() {
		$className = parent::getClassName();
		switch (strtolower($className)) {
			case 'datasetdb' :
				return 'db';
			case 'datasetcsv' :
				return 'csv';
			case 'datasetxml' :
				return 'xml';
			case 'datasetarray' :
				return 'array';
			default :
				return NULL;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	DataAdapter::getParameter
	// @desc		Busca o valor de um parвmetro do adapter
	// @param		param string	Nome do parвmetro
	// @return		mixed Valor do parвmetro
	// @access		public		
	//!-----------------------------------------------------------------
	function getParameter($param) {
		return (isset($this->params[$param]) ? $this->params[$param] : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataAdapter::setParameter
	// @desc		Define o valor de um parвmetro especнfico do provider
	// @param		param string	Nome do parвmetro
	// @param		value mixed		Valor para o parвmetro
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setParameter($param, $value) {
		$this->params[$param] = $value;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataAdapter::getFieldCount
	// @desc		Retorna o nъmero de colunas/campos do conjunto de dados
	// @access		public
	// @return		int Nъmero de campos	
	//!-----------------------------------------------------------------
	function getFieldCount() {
		return $this->fieldCount;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataAdapter::getFieldNames
	// @desc		Retorna o vetor contendo os nomes dos campos/colunas do DataSet
	// @access		public
	// @return		array Vetor contendo os nomes dos campos
	//!-----------------------------------------------------------------
	function getFieldNames() {
		return $this->fieldNames;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataAdapter::getRecordCount
	// @desc		Retorna o nъmero total de registros no conjunto de dados atual
	// @access		public
	// @return		int Nъmero de registros	
	// @note		Quando o provedor de dados estiver sendo utilizado para
	//				construзгo de dados paginados, este mйtodo retorna apenas
	//				o total de registros da pбgina
	// @see			PagedDataSet::getTotalRecordCount
	//!-----------------------------------------------------------------
	function getRecordCount() {
		return $this->recordCount;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataAdapter::moveFirst
	// @desc		Move o ponteiro para o primeiro registro do conjunto de dados
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------		
	function moveFirst() {
		return ($this->getAbsolutePosition() == 0 ? TRUE : $this->move(0));
	}
	
	//!-----------------------------------------------------------------
	// @function	DataAdapter::moveFirst
	// @desc		Move o ponteiro para o ъltimo registro do conjunto de dados
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function moveLast() {
		return $this->move($this->getRecordCount() - 1);
	}	
}
?>