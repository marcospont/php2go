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
// $Header: /www/cvsroot/php2go/core/data/ReportSimpleSearch.class.php,v 1.19 2006/10/11 22:48:36 mpont Exp $
// $Date: 2006/10/11 22:48:36 $

//------------------------------------------------------------------
import("php2go.util.AbstractList");
import("php2go.net.HttpRequest");
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		ReportSimpleSearch
// @desc		Implementa uma lista de filtros aplic�veis a um relat�rio
//				ou consulta ao banco de dados. Constr�i a express�o que
//				deve ser adicionada a uma consulta a partir dos filtros
//				submetidos por uma busca
// @package		php2go.data
// @extends		AbstractList
// @author		Marcos Pont
// @version		$Revision: 1.19 $
//!-----------------------------------------------------------------
class ReportSimpleSearch extends AbstractList
{
	var $fields = '';			// @var fields string			"" Lista de campos de pesquisa submetidos
	var $operators = '';		// @var operators string		"" Lista de operadores de pesquisa submetidos
	var $values = '';			// @var values string			"" Lista de valores de pesquisa
	var $mainOperator = '';		// @var mainOperator string		"" Operador principal da express�o de pesquisa
	var $urlString = '';		// @var urlString string		"" Dados da �ltima busca realizada no formato url encode
	var $masksRegExp;			// @var masksRegExp string		Express�o regular para validar m�scaras de valor de pesquisa
	var $maskFunctions;			// @var maskFunctions array		Fun��es de usu�rio para valores que pertencem a determinadas m�scaras
	var $searchSent;			// @var searchSent bool			Indica se uma busca foi submetida

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::ReportSimpleSearch
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function ReportSimpleSearch() {
		parent::AbstractList();
		$this->maskFunctions = array();
		$this->searchSent = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::getFields
	// @desc		Busca o(s) campo(s) de pesquisa submetido(s)
	// @acess		public
	// @return		string Campo(s) de pesquisa
	// @see			ReportSimpleSearch::getOperators
	// @see			ReportSimpleSearch::getValues
	// @see			ReportSimpleSearch::getMainOperator
	//!-----------------------------------------------------------------
	function getFields() {
		return $this->fields;
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::getOperators
	// @desc		Busca o(s) operador(es) de pesquisa submetido(s)
	// @access		public
	// @return		string Operado(es) de pesquisa
	// @see			ReportSimpleSearch::getFields
	// @see			ReportSimpleSearch::getValues
	// @see			ReportSimpleSearch::getMainOperator
	//!-----------------------------------------------------------------
	function getOperators() {
		return $this->operators;
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::getValues
	// @desc		Busca o(s) valor(es) de pesquisa submetido(s)
	// @access		public
	// @return		string Valor(es) de pesquisa
	// @see			ReportSimpleSearch::getFields
	// @see			ReportSimpleSearch::getOperators
	// @see			ReportSimpleSearch::getMainOperator
	//!-----------------------------------------------------------------
	function getValues() {
		return $this->values;
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::getMainOperator
	// @desc		Busca o operador principal da express�o de busca
	// @access		public
	// @return		string Operador principal (AND ou OR)
	// @see			ReportSimpleSearch::getFields
	// @see			ReportSimpleSearch::getOperators
	// @see			ReportSimpleSearch::getValues
	//!-----------------------------------------------------------------
	function getMainOperator() {
		return $this->mainOperator;
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::getUrlString
	// @desc		Retorna os dados da �ltima pesquisa no formato url encode
	// @access		public
	// @return		string Dados da �ltima pesquisa
	//!-----------------------------------------------------------------
	function getUrlString() {
		return $this->urlString;
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::getSearchClause
	// @desc		Verifica se uma express�o de busca foi submetida na
	//				requisi��o atual e constr�i a partir dela a express�o
	//				correspondente em SQL para filtragem dos dados
	// @access		public
	// @return		string Cl�usula para a consulta SQL, que ser� vazia caso n�o
	//				n�o exista express�o de busca submetida na requisi��o
	//!-----------------------------------------------------------------
	function getSearchClause() {
		// verifica se uma express�o de busca foi postada
		$this->_checkRequest();
		if (!$this->searchSent)
			return '';
		// busca campos, operadores e valores de busca
		$fieldList = explode('|', $this->fields);
		$operatorList = explode('|', $this->operators);
		$valueList = explode('|', $this->values);
		// verifica se os dados est�o completos
		if (sizeof($fieldList) == sizeof($operatorList) && sizeof($operatorList) == sizeof($valueList)) {
			// constr�i a express�o de busca a partir dos filtros submetidos
			$clause = '';
			for ($i = 0; $i < sizeof($fieldList); $i++) {
				$clause .= '(' . $fieldList[$i];
				$valueList[$i] = $this->_checkMask($fieldList[$i], $valueList[$i]);
				switch ($operatorList[$i]) {
					case "LIKE"	:
						$clause .= " LIKE '%" . $valueList[$i] . "%')";
						break;
					case "LIKEI" :
						$clause .= " LIKE '" . $valueList[$i] . "%')";
						break;
					case "LIKEF" :
						$clause .= " LIKE '%" . $valueList[$i] . "')";
						break;
					case "NOT LIKE" :
						$clause .= " NOT LIKE '%" . $valueList[$i] . "%')";
						break;
					default :
						// inclus�o da cl�usula depende do tipo de valor informado
						if ($index = $this->_containsField($fieldList[$i])) {
							$filter = $this->get($index);
							if ($filter['mask'] == 'integer' || $filter['mask'] == 'float') {
								$clause .= $operatorList[$i] . $valueList[$i];
							} else {
								$clause .= $operatorList[$i] . "'" . $valueList[$i] . "'";
							}
						} else {
							$clause .= $operatorList[$i] . ( ( !TypeUtils::isInteger($valueList[$i]) && !TypeUtils::isFloat($valueList[$i]) ) ? "'" . $valueList[$i] . "'" : $valueList[$i] );
						}
						$clause .= ')';
				}
				if ($i < (sizeof($fieldList)-1)) $clause .= ' ' . $this->mainOperator . ' ';
			}
			$clause = sizeof($fieldList) > 1 ? '(' . $clause . ')' : $clause;
			return (!empty($clause) ? $clause : NULL);
		}
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::addMaskFunction
	// @desc		Associa uma fun��o de usu�rio a uma determinada m�scara
	// @access		public
	// @param		mask string		Nome da m�scara
	// @param		callback mixed	Nome de fun��o, classe::m�todo ou vetor objeto+m�todo
	// @return		bool
	// @note		Ao associar uma fun��o ou um m�todo � mascara 'date', por exemplo,
	//				os valores cuja m�scara for 'date' ser�o processados pela fun��o
	//				ou m�todo ao serem submetidos
	//!-----------------------------------------------------------------
	function addMaskFunction($mask, $callback) {
		$mask = strtoupper($mask);
		if ($mask == 'STRING' || preg_match(PHP2GO_MASK_PATTERN, $mask)) {
			$maskName = strtoupper($mask);
			$this->maskFunctions[$maskName] =& new Callback($callback);
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::addFilter
	// @desc		Adiciona uma nova op��o de filtro
	// @param		filterData array		Dados do filtro
	// @access		public
	// @return		void
	// @note		O vetor $filterData deve conter os campos LABEL (r�tulo
	//				do filtro), FIELD (nome do filtro) e MASK (m�scara do filtro).
	//				Adicionalmente, o campo INDEX associa o filtro com uma determinada
	//				coluna de uma consulta ou relat�rio
	//!-----------------------------------------------------------------
	function addFilter($filterData) {
		if (!isset($filterData['LABEL']) || !isset($filterData['FIELD']) || !isset($filterData['MASK']))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_SEARCH_PARS_MALFORMED'), E_USER_ERROR, __FILE__, __LINE__);
		$filterData['MASK'] = strtoupper($filterData['MASK']);
		if ($filterData['MASK'] != 'STRING' && !preg_match(PHP2GO_MASK_PATTERN, $filterData['MASK']))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_SEARCH_INVALID_MASK', array($filterData['MASK'], $this->size()+1)), E_USER_ERROR, __FILE__, __LINE__);
		$newFilter = array(
			'label' => trim($filterData['LABEL']),
			'field' => trim($filterData['FIELD']),
			'mask' => trim($filterData['MASK']),
			'index' => isset($filterData['INDEX']) ? $filterData['INDEX'] : -1
		);
		parent::add($newFilter);
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::_checkRequest
	// @desc		Verifica se a requisi��o atual cont�m valores de pesquisa
	//				submetidos via GET ou POST
	// @access		private
	// @return		void
	// @note		Os nomes dos campos s�o search_fields, search_operators,
	//				search_values e search_main_op
	// @note		Se os quatro valores forem preenchidos, a propriedade
	//				searchSent ser� modificada para TRUE
	//!-----------------------------------------------------------------
	function _checkRequest() {
		$this->urlString = "";
		// Campos de busca
		$pFields = HttpRequest::post('search_fields');
		$gFields = HttpRequest::get('search_fields');
		if ($pFields !== NULL) {
			$this->fields = $pFields;
			$this->urlString .= "&search_fields=" . $pFields;
		} else if ($gFields !== NULL) {
			$gFields = urldecode($gFields);
			$this->fields = str_replace("\\'", "'", $gFields);
			$this->urlString .= "&search_fields=" . urlencode(str_replace("\\'", "'", $gFields));
		}
		// Operadores de busca
		$pOperators = HttpRequest::post('search_operators');
		$gOperators = HttpRequest::get('search_operators');
		if ($pOperators !== NULL) {
			$this->operators = $pOperators;
			$this->urlString .= "&search_operators=" . $pOperators;
		} else if ($gOperators !== NULL) {
			$gOperators = urldecode($gOperators);
			$this->operators = $gOperators;
			$this->urlString .= "&search_operators=" . urlencode($gOperators);
		}
		// Valores de busca
		$pValues = HttpRequest::post('search_values');
		$gValues = HttpRequest::get('search_values');
		if ($pValues !== NULL) {
			$this->values = $pValues;
			$this->urlString .= "&search_values=" . $pValues;
		} else if ($gValues !== NULL) {
			$gValues = urldecode($gValues);
			$this->values = $gValues;
			$this->urlString .= "&search_values=" . urlencode($gValues);
		}
		// Operador principal
		$pMain = HttpRequest::post('search_main_op');
		$gMain = HttpRequest::get('search_main_op');
		if ($pMain !== NULL) {
			$this->mainOperator = $pMain;
			$this->urlString .= "&search_main_op=" . $pMain;
		} else if ($gMain !== NULL) {
			$gMain = urldecode($gMain);
			$this->mainOperator = $gMain;
			$this->urlString .= "&search_main_op=" . $gMain;
		}
		$this->searchSent = (!empty($this->fields) && !empty($this->operators) && trim($this->values) != '' && !empty($this->mainOperator));
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::_checkMask
	// @desc		Verifica se existe fun��o de usu�rio a ser executada
	//				para a m�scara de um campo de pesquisa
	// @access		private
	// @param		field string	Nome do campo
	// @param		value mixed	Valor do campo
	// @return		mixed Valor processado pela fun��o, se ela existir, ou o mesmo valor em caso contr�rio
	//!-----------------------------------------------------------------
	function _checkMask($field, $value) {
		$index = $this->_containsField($field);
		if (!TypeUtils::isFalse($index)) {
			$filter = $this->get($index);
			if (isset($this->maskFunctions[$filter['mask']])) {
				$fn = $this->maskFunctions[$filter['mask']];
				return $fn->invoke($value);
			} else
				return $value;
		} else
			return $value;
	}

	//!-----------------------------------------------------------------
	// @function	ReportSimpleSearch::_containsField
	// @desc		Verifica se a lista de filtros cont�m um determinado campo
	// @access		private
	// @param		field string	Nome do campo
	// @return		int �ndice do campo ou FALSE se ele n�o existir
	//!-----------------------------------------------------------------
	function _containsField($field) {
		$Iterator = parent::iterator();
		while ($filter = $Iterator->next()) {
			if ($filter['field'] == trim($field)) {
				return $Iterator->getCurrentIndex();
			}
		}
		return FALSE;
	}
}
?>