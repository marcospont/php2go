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

import('php2go.net.Url');
import('php2go.validation.Validator');

/**
 * Builds and processes search forms
 *
 * The forms XML specification contains a node called <b>search</b>, in
 * which is possible to customize the behaviour of a given component when
 * used inside a search form. This class is the one who reads these settings
 * in order to transform the form's submitted values into a query condition
 * clause.
 *
 * For instance: a text input will generate, by default, the following clause:
 * <code>
 * field_name like '%submitted_value%'
 * </code>
 *
 * Once the final condition clause is built, the SearchForm can redirect
 * to a page requested by the developer, saving the search filter in the
 * session scope. Besides, the class contains mechanisms to retain search
 * filters and search field values.
 *
 * @package form
 * @uses Callback
 * @uses Db
 * @uses FormBasic
 * @uses FormTemplate
 * @uses Url
 * @uses TypeUtils
 * @uses Validator
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class SearchForm extends Component
{
	/**
	 * Final condition clause
	 *
	 * @var string
	 */
	var $searchString = '';

	/**
	 * Human-readable representaton of the condition clause
	 *
	 * @var string
	 */
	var $searchDescription = '';

	/**
	 * Holds raw search data collected from the form
	 *
	 * @var array
	 * @access private
	 */
	var $searchRawData = array();

	/**
	 * Main operator
	 *
	 * @var string
	 * @access private
	 */
	var $mainOperator = 'AND';

	/**
	 * Prefix operator
	 *
	 * @var string
	 * @access private
	 */
	var $prefixOperator = '';

	/**
	 * Whether an empty search must be accepted (all form fields are empty)
	 *
	 * @var bool
	 * @access private
	 */
	var $acceptEmptySearch = FALSE;

	/**
	 * Error message for an empty search
	 *
	 * @var string
	 * @access private
	 */
	var $emptySearchMessage;

	/**
	 * Set of form fields to be ignored
	 *
	 * @var array
	 * @access private
	 */
	var $ignoreFields = array();

	/**
	 * Minimum length for fields when using string operators
	 *
	 * @var int
	 * @access private
	 */
	var $stringMinLength;

	/**
	 * Whether auto redirect is enabled
	 *
	 * @var bool
	 * @access private
	 */
	var $autoRedirect = FALSE;

	/**
	 * Auto redirect URL
	 *
	 * @var string
	 * @access private
	 */
	var $redirectUrl;

	/**
	 * Auto redirect parameter
	 *
	 * @var string
	 * @access private
	 */
	var $paramName = 'p2g_search';

	/**
	 * Whether filter must be saved on the session
	 * scope when using auto redirect
	 *
	 * @var bool
	 * @access private
	 */
	var $useSession = FALSE;

	/**
	 * Whether filter must be encoded when
	 * auto redirect is enabled
	 *
	 * @var bool
	 * @access private
	 */
	var $useEncode = FALSE;

	/**
	 * Preserve already saved search filters
	 *
	 * @var bool
	 * @access private
	 */
	var $preserveSession = FALSE;

	/**
	 * Auto save/restore form field values
	 *
	 * @var bool
	 * @access private
	 */
	var $filterPersistence = FALSE;

	/**
	 * DB connection ID (needed to build the condition clause)
	 *
	 * @var string
	 * @access private
	 */
	var $connectionId = NULL;

	/**
	 * Conversion mapping for checkbox fields
	 *
	 * @var array
	 * @access private
	 */
	var $checkboxMapping = array('T' => 1, 'F' => 0);

	/**
	 * Search validators
	 *
	 * @var array
	 * @access private
	 */
	var $validators = array();

	/**
	 * Callback helper object
	 *
	 * @var object
	 * @access private
	 */
	var $callbackObj = NULL;

	/**
	 * SQL callbacks
	 *
	 * @var array
	 * @access private
	 */
	var $sqlCallbacks = array();

	/**
	 * Value callbacks
	 *
	 * @var array
	 * @access private
	 */
	var $valueCallbacks = array();

	/**
	 * Holds the search form validation status
	 *
	 * @var bool
	 * @access private
	 */
	var $valid;

	/**
	 * Search form
	 *
	 * @var object Form
	 */
	var $Form = NULL;

	/**
	 * Class constructor
	 *
	 * If $templateFile is missing, the form will be rendered
	 * using the {@link FormBasic} class. Otherwise, it will
	 * be rendered by {@link FormTemplate}.
	 *
	 * @param string $xmlFile Form XML specification file
	 * @param string $templateFile Template file
	 * @param string $formName Form name
	 * @param Document &$Doc Document instance in which the form will be inserted
	 * @param array $tplIncludes Hash array of template includes
	 * @return SearchForm
	 */
	function SearchForm($xmlFile, $templateFile=NULL, $formName, &$Doc, $tplIncludes=array()) {
		parent::Component();
		if (TypeUtils::isNull($templateFile)) {
			import('php2go.form.FormBasic');
			$this->Form = new FormBasic($xmlFile, $formName, $Doc);
		} else {
			import('php2go.form.FormTemplate');
			$this->Form = new FormTemplate($xmlFile, $templateFile, $formName, $Doc, $tplIncludes);
		}
	}

	/**
	 * Check if the form is posted and valid
	 *
	 * @return bool
	 */
	function isValid() {
		if (!isset($this->valid))
			$this->valid = ($this->Form->isPosted() && $this->Form->isValid());
		if (!$this->valid && $this->useSession && !$this->preserveSession) {
			unset($_SESSION[$this->paramName]);
			unset($_SESSION[$this->paramName . '_description']);
		}
		return $this->valid;
	}

	/**
	 * Get raw search data
	 *
	 * Returns an array containing submitted values and
	 * search settings of all valid search fields.
	 *
	 * @return array
	 */
	function getSearchRawData() {
		return $this->searchRawData;
	}

	/**
	 * Get the condition clause produced by the class
	 *
	 * @return string
	 */
	function getSearchString() {
		return $this->searchString;
	}

	/**
	 * Get the human-readable representation of the condition clause
	 *
	 * @return string
	 */
	function getSearchDescription() {
		return $this->searchDescription;
	}

	/**
	 * Get the main search operator
	 *
	 * @return string
	 */
	function getMainOperator() {
		return $this->mainOperator;
	}

	/**
	 * Set the main search operator
	 *
	 * The main operator will be used to build the
	 * condition clause (all fields are operands
	 * joined into a string using the main operator
	 * as the glue).
	 *
	 * @param string $operator Main operator
	 */
	function setMainOperator($operator) {
		$operator = strtoupper($operator);
		if ($operator == 'OR' || $operator == 'AND')
			$this->mainOperator = $operator;
	}

	/**
	 * Get the search prefix operator
	 *
	 * @return string
	 */
	function getPrefixOperator() {
		return $this->prefixOperator;
	}

	/**
	 * Set the search prefix operator
	 *
	 * Determines the operator that should be prepended in the
	 * condition clause. Useful when the resultant clause must
	 * be appended in an SQL query that already has a condition
	 * clause.
	 *
	 * @param string $operator Prefix operator
	 */
	function setPrefixOperator($operator) {
		$operator = strtoupper($operator);
		if ($operator == 'OR' || $operator == 'AND')
			$this->prefixOperator = $operator;
	}

	/**
	 * Accept/reject empty search requests
	 *
	 * Empty seach request mean a form submission where
	 * all searchable fields are empty.
	 *
	 * @param bool $setting Accept/reject
	 */
	function setAcceptEmptySearch($setting=TRUE) {
		$this->acceptEmptySearch = (bool)$setting;
	}

	/**
	 * Set error message for rejected empty search requests
	 *
	 * @param string $message Error message
	 */
	function setEmptySearchMessage($message) {
		$this->emptySearchMessage = $message;
	}

	/**
	 * Get the field names that should be ignored
	 *
	 * @return array
	 */
	function getIgnoreFields() {
		return $this->ignoreFields;
	}

	/**
	 * Set the field names that should be ignored
	 *
	 * Form fields can also be ignored by the search
	 * engine through the IGNORE attribute of the SEARCH
	 * XML node.
	 *
	 * Members of the $fields array must contain field
	 * names (NAME attribute of the XML specification).
	 * The comparison is case-sensitive.
	 *
	 * @param array $fields Field to ignore
	 */
	function setIgnoreFields($fields) {
		$this->ignoreFields = TypeUtils::toArray($fields);
	}

	/**
	 * Set the minimum length of fields that use
	 * string operators (STARTING, CONTAINING, ENDING)
	 *
	 * @param int $minlength Minlength
	 */
	function setStringMinLength($minlength) {
		if (TypeUtils::isInteger($minlength) && $minlength > 0) {
			$this->stringMinLength = $minlength;
		}
	}

	/**
	 * Enable/disable auto redirect upon search
	 *
	 * Auto redirect enables automatic redirection to a
	 * target URL when the {@link run()} method validates
	 * the submitted search form and builds the condition
	 * clause.
	 *
	 * @param bool $setting Enable/disable
	 * @param string $url Target URL
	 * @param string $paramName Request parameter, or session parameter when $useSession==TRUE
	 * @param bool $useSession Save condition clause in the session scope
	 * @param bool $useEncode Encode condition clause
	 */
	function setAutoRedirect($setting=TRUE, $url, $paramName='p2g_search', $useSession=FALSE, $useEncode=FALSE) {
		$this->autoRedirect = (bool)$setting;
		if ($this->autoRedirect) {
			$this->redirectUrl = $url;
			$this->paramName = $paramName;
			$this->useSession = (bool)$useSession;
			$this->useEncode = (bool)$useEncode;
		}
	}

	/**
	 * Whether search condition clause must be preserved
	 * in the session scope or destroyed every time the
	 * form is displayed
	 *
	 * @param bool $setting Preserve or destroy
	 */
	function setPreserveSession($setting) {
		$this->preserveSession = (bool)$setting;
	}

	/**
	 * Enable/disable automatic save/restore of the form
	 * field values using the session scope
	 *
	 * @param bool $enable Enable/disable
	 */
	function setFilterPersistence($enable) {
		$this->filterPersistence = (bool)$enable;
	}

	/**
	 * Clears search field values saved in the session scope
	 */
	function clearFilterPersistence() {
		if (isset($_SESSION['p2g_filters'][$this->Form->getSignature()]))
			unset($_SESSION['p2g_filters'][$this->Form->getSignature()]);
	}

	/**
	 * Set value mapping for search fields based on a checkbox input
	 *
	 * @param string $trueValue Checked value
	 * @param string $falseValue Unchecked value
	 */
	function setCheckboxMapping($trueValue, $falseValue) {
		$this->checkboxMapping = array(
			'T' => $trueValue,
			'F' => $falseValue
		);
	}

	/**
	 * Set database connection ID to be used by the class
	 *
	 * @param string $id Connection ID
	 */
	function setConnectionId($id) {
		$this->connectionId = $id;
	}

	/**
	 * Set a callback object that should be used to search
	 * for SQL and value callbacks defined in the XML specification
	 *
	 * @param object $obj Callback object
	 */
	function setCallbackObject(&$obj) {
		$this->callbackObj =& $obj;
	}

	/**
	 * Register a search validator
	 *
	 * The execute method of the validator will receive the
	 * raw search data as argument.
	 *
	 * @param string $validator Dot path of the validator
	 * @param array $arguments Validator arguments
	 */
	function addValidator($validator, $arguments=array()) {
		$this->validators[] = array($validator, TypeUtils::toArray($arguments));
	}

	/**
	 * Validates the search form and builds the condition clause
	 *
	 * This method must be called right after the configuration of
	 * the form. Returns FALSE when the form is not posted or contains
	 * errors.
	 *
	 * Redirects to the target page when auto redirect is enabled.
	 *
	 * @uses HttpResponse::redirect()
	 * @uses Validator::validate()
	 * @return bool
	 */
	function run() {
		if ($this->isValid()) {
			if ($this->_buildSearchString()) {
				foreach ($this->validators as $validator) {
					if (!Validator::validate($validator[0], $this->searchRawData, $validator[1]))
						$this->valid = FALSE;
				}
				if (!$this->valid) {
					$this->Form->addErrors(Validator::getErrors());
					return FALSE;
				} else {
					// search fields persistence
					if ($this->filterPersistence) {
						$signature = $this->Form->getSignature();
						$formVars = ($this->Form->formMethod == 'POST' ? $_POST : $_GET);
						$_SESSION['p2g_filters'][$signature] = $formVars;
					} else {
						$this->clearFilterPersistence();
					}
					// auto redirect
					if ($this->autoRedirect) {
						$Url = new Url($this->redirectUrl);
						if ($this->useSession) {
							$_SESSION[$this->paramName] = $this->searchString;
							if (!empty($this->searchDescription))
								$_SESSION[$this->paramName . '_description'] = $this->searchDescription;
						} else {
							if ($this->useEncode)
								$Url->addParameter($this->paramName, base64_encode($this->searchString));
							else
								$Url->addParameter($this->paramName, urlencode($this->searchString));
						}
						HttpResponse::redirect($Url);
					}
				}
				return $this->searchString;
			} else {
				$errorMessage = (isset($this->emptySearchMessage) ? $this->emptySearchMessage : (isset($this->stringMinLength) ? PHP2Go::getLangVal('ERR_SEARCHFORM_INVALID', $this->stringMinLength) : PHP2Go::getLangVal('ERR_SEARCHFORM_EMPTY')));
				$this->Form->addErrors($errorMessage);
				$this->valid = FALSE;
				return FALSE;
			}
		} elseif (!$this->Form->isPosted()) {
			// restore search fields
			if ($this->filterPersistence) {
				$signature = $this->Form->getSignature();
				if (isset($_SESSION['p2g_filters'][$signature])) {
					$filters = (array)$_SESSION['p2g_filters'][$signature];
					foreach ($filters as $name => $value)
						Registry::set($name, $value);
				}
			}
		}
		return FALSE;
	}

	/**
	 * Prepares the form to be rendered
	 */
	function onPreRender() {
		if (!$this->preRendered)
			$this->Form->onPreRender();
	}

	/**
	 * Builds and returns the form's HTML code
	 *
	 * @return string
	 */
	function getContent() {
		$this->onPreRender();
		return $this->Form->getContent();
	}

	/**
	 * Builds and displays the form's HTML code
	 */
	function display() {
		$this->onPreRender();
		$this->Form->display();
	}

	/**
	 * Builds the search clause based on the form's
	 * submitted values and search settings
	 *
	 * Returns FALSE when the built search clause is empty.
	 *
	 * @access private
	 * @return bool
	 */
	function _buildSearchString() {
		$fieldNames = array_keys($this->Form->fields);
		foreach ($fieldNames as $name) {
			$field =& $this->Form->fields[$name];
			$sd = $field->getSearchData();
			if ($field->child || !$field->searchable || $sd['IGNORE'] || in_array($name, $this->ignoreFields) || !$this->_validadeSearchField($sd))
				continue;
			$this->searchRawData[$name] = $sd;
		}
		$result = array();
		$description = array();
		$operators = PHP2Go::getLangVal('OPERATORS');
		$dbConn =& Db::getInstance($this->connectionId);
		foreach ($this->searchRawData as $fldName => $args) {
			// SQL callbacks
			if (isset($args['SQLFUNC'])) {
				$clause = $this->_resolveSqlCallback($args['SQLFUNC'], $args['VALUE']);
			}
			// BETWEEN operator
			elseif ($args['FIELDTYPE'] == 'RANGEFIELD') {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				list($tmp, $bottom) = each($args['VALUE']);
				list($tmp, $top) = each($args['VALUE']);
				$bottom = $this->_resolveValueCallback(@$args['VALUEFUNC'], $bottom);
				$top = $this->_resolveValueCallback(@$args['VALUEFUNC'], $top);
				if ($args['DATATYPE'] == 'STRING') {
					$bottom = $dbConn->quoteString($bottom);
					$top = $dbConn->quoteString($top);
				} elseif ($args['DATATYPE'] == 'DATE') {
					$bottom = $dbConn->date($bottom);
					$top = $dbConn->date($top);
				} elseif ($args['DATATYPE'] == 'DATETIME') {
					// fills the value with hour, minute and second
					$bottom .= " 00:00:00";
					$top .= " 23:59:59";
					$bottom = $dbConn->date($bottom, TRUE);
					$top = $dbConn->date($top, TRUE);
				}
				$clause .= $this->_resolveOperator($args['OPERATOR']) . $bottom . ' and ' . $top;
			}
			// IN and NOTIN operators
			elseif ($args['OPERATOR'] == 'IN' || $args['OPERATOR'] == 'NOTIN') {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				$value = $this->_resolveValueCallback(@$args['VALUEFUNC'], TypeUtils::toArray($args['VALUE']));
				if ($args['DATATYPE'] == 'STRING') {
					foreach ($value as $key => $entry)
						$value[$key] = $dbConn->quoteString($entry);
				} elseif ($args['DATATYPE'] == 'DATE' || $args['DATATYPE'] == 'DATETIME') {
					foreach ($value as $key => $entry)
						$value[$key] = $dbConn->date($entry, ($args['DATATYPE'] == 'DATETIME'));
				}
				$clause .= $this->_resolveOperator($args['OPERATOR']) . '(' . implode(',', $value) . ')';
			}
			// string operators
			elseif ($args['OPERATOR'] == 'STARTING' || $args['OPERATOR'] == 'ENDING' || $args['OPERATOR'] == 'CONTAINING') {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				$value = $this->_resolveValueCallback(@$args['VALUEFUNC'], TypeUtils::parseString($args['VALUE']));
				if ($args['OPERATOR'] == 'ENDING' || $args['OPERATOR'] == 'CONTAINING')
					$value = '%' . $value;
				if ($args['OPERATOR'] == 'STARTING' || $args['OPERATOR'] == 'CONTAINING')
					$value .= '%';
				$value = $dbConn->quoteString($value);
				$clause .= $this->_resolveOperator($args['OPERATOR']) . $value;
			}
			// other operators
			else {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				$value = $this->_resolveValueCallback(@$args['VALUEFUNC'], @$args['VALUE']);
				if ($args['FIELDTYPE'] == 'CHECKFIELD')
					$value = $this->checkboxMapping[$value];
				elseif ($args['DATATYPE'] == 'STRING')
					$value = $dbConn->quoteString($value);
				elseif ($args['DATATYPE'] == 'DATE')
					$value = $dbConn->date($value);
				elseif ($value['DATATYPE'] == 'DATETIME')
					$value = $dbConn->date($value, TRUE);
				$clause .= $this->_resolveOperator($args['OPERATOR']) . $value;
			}
			if (!empty($clause)) {
				$result[] = $clause;
				if ($args['FIELDTYPE'] != 'HIDDENFIELD' && $args['DISPLAYVALUE'] !== NULL)
					$description[] = $args['DISPLAYVALUE'];
			}
		}
		$this->searchString = implode(" {$this->mainOperator} ", $result);
		$this->searchDescription = implode(sprintf(" %s ", $operators[$this->mainOperator]), $description);
		if (!empty($this->prefixOperator) && !empty($this->searchString))
			$this->searchString = ($this->prefixOperator == 'OR' ? " OR ({$this->searchString})" : " AND {$this->searchString}");
		return ($this->acceptEmptySearch || !empty($this->searchString));
	}

	/**
	 * Validates a search field
	 *
	 * # range fields must contain non empty values for both members
	 * # fields using string operators should respect {@link stringMinLength}
	 * # other fields should have a non empty value
	 *
	 * @param array $args Field's search arguments
	 * @access private
	 * @return bool
	 */
	function _validadeSearchField($args) {
		if ($args['FIELDTYPE'] == 'RANGEFIELD') {
			$sv = TypeUtils::toArray($args['VALUE']);
			if (sizeof($sv) != 2)
				return FALSE;
			list($tmp, $bottom) = each($sv);
			list($tmp, $top) = each($sv);
			if (empty($bottom) && strlen($bottom) == 0 && empty($top) && strlen($top) == 0)
				return FALSE;
			return TRUE;
		} else {
			if (in_array($args['OPERATOR'], array('STARTING', 'CONTAINING', 'ENDING')) && isset($this->stringMinLength)) {
				return (!TypeUtils::isNull($args['VALUE']) && strlen($args['VALUE']) >= $this->stringMinLength);
			} elseif (is_array($args['VALUE'])) {
				return (!empty($args['VALUE']));
			} else {
				$str = strval($args['VALUE']);
				return (!empty($str) || strlen($str) > 0);
			};
		}
	}

	/**
	 * Resolve an SQL callback
	 *
	 * SQL callbacks has the ability to build the full clause
	 * for a given search field, given its value. An SQL callback
	 * could be used, for instance, to transform a search field into
	 * a complex clause based on a subquery.
	 *
	 * @param string $callback Callback
	 * @param string $value Search field's value
	 * @return string Search clause
	 * @access private
	 */
	function _resolveSqlCallback($callback, $value) {
		if (empty($callback))
			return FALSE;
		if (!isset($this->sqlCallbacks[$callback])) {
			if (is_object($this->callbackObj) && !function_exists($callback) && method_exists($this->callbackObj, $callback))
				$this->sqlCallbacks[$callback] = new Callback(array($this->callbackObj, $callback));
			else
				$this->sqlCallbacks[$callback] = new Callback($callback);
		}
		return $this->sqlCallbacks[$callback]->invoke($value);
	}

	/**
	 * Resolves a value callback
	 *
	 * Value callbacks can be used to apply transformations
	 * on the literal part of the condition clauses (the values
	 * of the search fields).
	 *
	 * @param string $callback Callback
	 * @param string $value Search field's value
	 * @return string Transformed value
	 * @access private
	 */
	function _resolveValueCallback($callback, $value) {
		if (empty($callback))
			return $value;
		if (!isset($this->valueCallbacks[$callback])) {
			if (is_object($this->callbackObj) && !function_exists($callback) && method_exists($this->callbackObj, $callback))
				$this->valueCallbacks[$callback] = new Callback(array($this->callbackObj, $callback));
			else
				$this->valueCallbacks[$callback] = new Callback($callback);
		}
		return $this->valueCallbacks[$callback]->invoke($value);
	}

	/**
	 * Translate an operator name into a valid ANSI operator
	 *
	 * @param string $op Operator name
	 * @access private
	 * @return string
	 */
	function _resolveOperator($op) {
		switch ($op) {
			case 'EQ' : return ' = ';
			case 'NEQ' : return ' <> ';
			case 'LT' : return ' < ';
			case 'LOET' : return ' <= ';
			case 'GT' : return ' > ';
			case 'GOET' : return ' >= ';
			case 'STARTING' :
			case 'ENDING' :
			case 'CONTAINING' :
				return ' LIKE ';
			case 'IN' :
				return ' IN ';
			case 'NOTIN' :
				return ' NOT IN ';
			case 'BETWEEN' :
				return ' BETWEEN ';
			default :
				return ' = ';
		}
	}
}
?>