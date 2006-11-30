<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import("php2go.util.AbstractList");
import("php2go.net.HttpRequest");

/**
 * Report simple search processor
 *
 * This class parses the search filters from the request and builds
 * the condition clause that must be appended to the report's original
 * condition clause (from the XML specification).
 *
 * It also handles masked search filters: before building the condition
 * clause, callback functions can be executed to transform values of
 * specific masks (like, for instance, DATE).
 *
 * @package data
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ReportSimpleSearch extends AbstractList
{
	/**
	 * Search fields parsed from the request
	 *
	 * @var string
	 */
	var $fields = '';

	/**
	 * Search operators parsed from the request
	 *
	 * @var string
	 */
	var $operators = '';

	/**
	 * Search values (terms) parsed from the request
	 *
	 * @var string
	 */
	var $values = '';

	/**
	 * Search main operator parsed from the request
	 *
	 * @var string
	 */
	var $mainOperator = '';

	/**
	 * Indicates a search was submitted and its arguments were found in the request
	 *
	 * @var bool
	 */
	var $searchSent;

	/**
	 * Holds a query string containing all search fields
	 *
	 * This is used to build links to navigate to other pages or
	 * sort report, in order to keep track of all search arguments
	 *
	 * @var string
	 * @access private
	 */
	var $urlString = '';

	/**
	 * Regexp used to validate the mask of data filters
	 *
	 * @var string
	 * @access private
	 */
	var $masksRegExp;

	/**
	 * Mask transformation functions
	 *
	 * @var array
	 * @access private
	 */
	var $maskFunctions;

	/**
	 * Class constructor
	 *
	 * @return ReportSimpleSearch
	 */
	function ReportSimpleSearch() {
		parent::AbstractList();
		$this->maskFunctions = array();
		$this->searchSent = FALSE;
	}

	/**
	 * Get search fields
	 *
	 * @return string
	 */
	function getFields() {
		return $this->fields;
	}

	/**
	 * Get search operators
	 *
	 * @return string
	 */
	function getOperators() {
		return $this->operators;
	}

	/**
	 * Get search values (terms)
	 *
	 * @return string
	 */
	function getValues() {
		return $this->values;
	}

	/**
	 * Get search main operator
	 *
	 * @return string
	 */
	function getMainOperator() {
		return $this->mainOperator;
	}

	/**
	 * Get all parsed search arguments in the form of a query string
	 *
	 * @return string
	 */
	function getUrlString() {
		return $this->urlString;
	}

	/**
	 * Get an SQL condition clause based on the parsed search arguments
	 *
	 * If there are no search arguments in the request, an empty string is returned.
	 *
	 * @return string
	 */
	function getSearchClause() {
		// parse search arguments from the request
		$this->_checkRequest();
		if (!$this->searchSent)
			return '';
		// split fields list, operators list and values list
		$fieldList = explode('|', $this->fields);
		$operatorList = explode('|', $this->operators);
		$valueList = explode('|', $this->values);
		// check if arguments are complete
		if (sizeof($fieldList) == sizeof($operatorList) && sizeof($operatorList) == sizeof($valueList)) {
			// build condition clause
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

	/**
	 * Register a new data filter
	 *
	 * @param array $filterData Filter data
	 */
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

	/**
	 * Register a callback function to transform data filters of a given mask
	 *
	 * @param string $mask Mask
	 * @param string $callback Function name, class/method or object/method
	 * @return bool
	 */
	function addMaskFunction($mask, $callback) {
		$mask = strtoupper($mask);
		if ($mask == 'STRING' || preg_match(PHP2GO_MASK_PATTERN, $mask)) {
			$maskName = strtoupper($mask);
			$this->maskFunctions[$maskName] =& new Callback($callback);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Parses search arguments from the request
	 *
	 * @access private
	 */
	function _checkRequest() {
		$this->urlString = "";
		// search fields
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
		// search operators
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
		// search values (terms)
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
		// search main operator
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

	/**
	 * Process the value of a search term
	 *
	 * Execute the callback function associated with the filter mask, if any
	 *
	 * @param string $field Search field name
	 * @param string $value Search term
	 * @return string
	 */
	function _checkMask($field, $value) {
		$index = $this->_containsField($field);
		if ($index !== FALSE) {
			$filter = $this->get($index);
			if (isset($this->maskFunctions[$filter['mask']])) {
				$fn = $this->maskFunctions[$filter['mask']];
				return $fn->invoke($value);
			} else
				return $value;
		} else
			return $value;
	}

	/**
	 * Check if a filter exists
	 *
	 * @param string $field Field name
	 * @return bool
	 */
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