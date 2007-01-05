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

import('php2go.form.field.DbField');
import('php2go.form.field.LookupField');
import('php2go.template.Template');
import('php2go.util.service.ServiceJSRS');

/**
 * Default size of the search text input
 */
define('EDITSEARCH_DEFAULT_SIZE', 10);
/**
 * Default width of the select input used to show search results
 */
define('EDITSEARCH_DEFAULT_LOOKUP_WIDTH', 250);

/**
 * JSRS based search tool based on a set of filters, a search input and a select input
 *
 * Based on a set of filters defined in the XML specification, this class builds
 * a search tool that uses a JSRS request to get results from a data source and
 * populate a select input ({@link LookupField} child component).
 *
 * @package form
 * @subpackage field
 * @uses LookupField
 * @uses ServiceJSRS
 * @uses Template
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class EditSearchField extends DbField
{
	/**
	 * Data filters
	 *
	 * @var array
	 * @access private
	 */
	var $filters = array();

	/**
	 * LookupField component used to display search results
	 *
	 * @var object LookupField
	 */
	var $_LookupField;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return EditSearchField
	 */
	function EditSearchField(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->composite = TRUE;
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	/**
	 * Builds component's HTML code
	 *
	 * @uses StringUtils::isEmpty()
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'editsearchfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('frm', $this->_Form->formName);
		$Tpl->assign('label', $this->label);
		$Tpl->assign('labelStyle', $this->_Form->getLabelStyle());
		$Tpl->assign('buttonStyle', $this->_Form->getButtonStyle());
		$comboValue = $this->_LookupField->getValue();
		$Tpl->assign('value', (!StringUtils::isEmpty($comboValue) ? "'{$comboValue}'" : 'null'));
		$masks = array();
		$requestFilter = TypeUtils::parseString(HttpRequest::getVar($this->name . '_filters'));
		$filters = sprintf("<select id=\"%s_filters\" name=\"%s_filters\" title=\"%s\"%s%s%s%s>",
			$this->id, $this->name, $this->label, $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['DISABLED']
		);
		foreach ($this->filters as $value => $data) {
			$filters .= sprintf("<option value=\"%s\"%s>%s</option>", $value, ($value == $requestFilter ? ' selected' : ''), $data[0]);
			$masks[] = "'{$data[2]}'";
		}
		$filters .= "</select>";
		$Tpl->assign('filters', $filters);
		$Tpl->assign('masks', implode(',', $masks));
		$Tpl->assign('search', sprintf("<input type=\"text\" id=\"%s_search\" name=\"%s_search\" value=\"%s\" maxlength=\"%s\" size=\"%s\" title=\"%s\"%s%s%s%s%s>&nbsp;",
			$this->id, $this->name, strval(HttpRequest::getVar($this->name . '_search')), $this->attributes['LENGTH'], $this->attributes['SIZE'], $this->label,
			$this->attributes['SCRIPT'], $this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['DISABLED'], $this->attributes['AUTOCOMPLETE']
		));
		$Tpl->assign('tabIndex', $this->attributes['TABINDEX']);
		$Tpl->assign('disabled', $this->attributes['DISABLED']);
		$Tpl->assign('btnImg', $this->attributes['BTNIMG']);
		$Tpl->assign('btnValue', $this->attributes['BTNVALUE']);
		$Tpl->assignByRef('results', $this->_LookupField);
		$Tpl->assign('resultsName', $this->_LookupField->getName());
		$Tpl->assign('idx', ($this->_LookupField->attributes['NOFIRST'] == 'T' ? '0' : '1'));
		$Tpl->assign('url', $this->attributes['URL']);
		$Tpl->assign('autoTrim', ($this->attributes['AUTOTRIM'] ? 'true' : 'false'));
		$Tpl->assign('autoDispatch', ($this->attributes['AUTODISPATCH'] ? 'true' : 'false'));
		$Tpl->assign('debug', ($this->attributes['DEBUG'] ? 'true' : 'false'));
		$Tpl->display();
	}

	/**
	 * The value of an EditSearchField component maps directly
	 * to the value of the internal {@link LookupField} child component
	 *
	 * @uses LookupField::getValue()
	 * @return mixed
	 */
	function getValue() {
		return $this->_LookupField->getValue();
	}

	/**
	 * The human-readable value of an EditSearchField component
	 * is produced by the internal {@link LookupField} child component
	 *
	 * @uses LookupField::getDisplayValue()
	 * @return string
	 */
	function getDisplayValue() {
		return $this->_LookupField->getDisplayValue();
	}

	/**
	 * Define the search input as the control to be activated
	 * when the component receives focus
	 *
	 * @return string
	 */
	function getFocusId() {
		return "{$this->id}_filters";
	}

	/**
	 * Get the internal LookupField
	 *
	 * @return LookupField
	 */
	function &getLookupField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_LookupField, 'LookupField'))
			$result =& $this->_LookupField;
		return $result;
	}

	/**
	 * Set search input size
	 *
	 * @param int $size Search input size
	 */
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = $size;
	}

	/**
	 * Set search input maxlength
	 *
	 * @param int $length Maxlength
	 */
	function setLength($length) {
		if (TypeUtils::isInteger($length))
			$this->attributes['LENGTH'] = $length;
	}

	/**
	 * Set search URL
	 *
	 * Defaults to $_SERVER['REQUEST_URI'].
	 *
	 * @param string $url Search URL
	 */
	function setUrl($url) {
		if (!empty($url))
			$this->attributes['URL'] = $url;
	}

	/**
	 * Enable/disable the autocomplete feature of the browser on the search input
	 *
	 * @param bool $setting Enable/disable
	 */
	function setAutoComplete($setting) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['AUTOCOMPLETE'] = " autocomplete=\"ON\"";
		else if (TypeUtils::isFalse($setting))
			$this->attributes['AUTOCOMPLETE'] = " autocomplete=\"OFF\"";
		else
			$this->attributes['AUTOCOMPLETE'] = "";
	}

	/**
	 * Enable/disable removal of trailing whitespaces from the search input upon search
	 *
	 * Defaults to FALSE.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setAutoTrim($setting=TRUE) {
		$this->attributes['AUTOTRIM'] = TypeUtils::toBoolean($setting);
	}

	/**
	 * Enable/disable search auto dispatch if filter and search term inputs are filled
	 *
	 * Defaults to FALSE.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setAutoDispatch($setting=TRUE) {
		$this->attributes['AUTODISPATCH'] = TypeUtils::toBoolean($setting);
	}

	/**
	 * Set search button value
	 *
	 * @param string $value Button value
	 */
	function setButtonValue($value) {
		if ($value)
			$this->attributes['BTNVALUE'] = resolveI18nEntry($value);
		else
			$this->attributes['BTNVALUE'] = PHP2Go::getLangVal('DEFAULT_BTN_VALUE');
	}

	/**
	 * Set search button image
	 *
	 * @param string $img Button image
	 */
	function setButtonImage($img) {
		if ($img)
			$this->attributes['BTNIMG'] = trim($img);
		else
			$this->attributes['BTNIMG'] = '';
	}

	/**
	 * Enable/disable debug mode
	 *
	 * When debug is enabled, the hidden iframe used to
	 * perform JSRS requests becomes visible (top of the screen
	 * on IE, bottom of the screen on Firefox and Opera).
	 *
	 * @param bool $setting Enable/disable
	 */
	function setDebug($setting) {
		$this->attributes['DEBUG'] = TypeUtils::toBoolean($setting);
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @uses ServiceJSRS::registerHandler()
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		if (!empty($this->dataSource) &&  isset($children['DATAFILTER']) && isset($children['LOOKUPFIELD']) &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'], 'XmlNode')
		) {
			// data filters
			$filters = TypeUtils::toArray($children['DATAFILTER']);
			foreach ($filters as $filterNode) {
				$id = $filterNode->getAttribute('ID');
				$label = $filterNode->getAttribute('LABEL');
				$expression = $filterNode->getAttribute('EXPRESSION');
				$mask = TypeUtils::ifFalse($filterNode->getAttribute('MASK'), 'STRING');
				if (empty($id) || empty($label) || empty($expression) || substr_count($expression, '%s') != 1)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EDITSEARCH_INVALID_DATAFILTER', (empty($id) ? '?' : $id)), E_USER_ERROR, __FILE__, __LINE__);
				if ($mask != 'STRING' && !preg_match(PHP2GO_MASK_PATTERN, $mask))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EDITSEARCH_INVALID_DATAFILTER_MASK', $id), E_USER_ERROR, __FILE__, __LINE__);
				if ($mask == 'DATE')
					$mask .= '-' . PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
				if (isset($this->filters[$id]))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EDITSEARCH_DUPLICATED_DATAFILTER', $id), E_USER_ERROR, __FILE__, __LINE__);
				$this->filters[$id] = array($label, $expression, $mask);
			}
			// initialize JSRS handler
			$Service =& ServiceJSRS::getInstance();
			$Service->registerHandler(array($this, 'performSearch'), strtolower($this->id) . 'PerformSearch');
			$this->_Form->postbackFields[] = $this->id;
			// search input size
			if (isset($attrs['SIZE']))
				$this->setSize($attrs['SIZE']);
			elseif (isset($attrs['LENGTH']))
				$this->setSize($attrs['LENGTH']);
			else
				$this->setSize(EDITSEARCH_DEFAULT_SIZE);
			// search input maxlength
			if ($attrs['LENGTH'])
				$this->setLength($attrs['LENGTH']);
			else
				$this->setLength($this->attributes['SIZE']);
			// search URL
			$this->setUrl(@$attrs['URL']);
			// autocomplete
			$this->setAutoComplete(resolveBooleanChoice(@$attrs['AUTOCOMPLETE']));
			// autotrim
			$this->setAutoTrim(resolveBooleanChoice(@$attrs['AUTOTRIM']));
			// autodispatch
			$this->setAutoDispatch(resolveBooleanChoice(@$attrs['AUTODISPATCH']));
			// button value and image
			$this->setButtonValue(@$attrs['BTNVALUE']);
			$this->setButtonImage(@$attrs['BTNIMG']);
			// JSRS debug
			$this->setDebug(resolveBooleanChoice(@$attrs['DEBUG']));
			// child LookupField
			$lookupAttrs =& $children['LOOKUPFIELD']->getAttributes();
			if (!isset($lookupAttrs['WIDTH']))
				$lookupAttrs['WIDTH'] = EDITSEARCH_DEFAULT_LOOKUP_WIDTH;
			$this->_LookupField = new LookupField($this->_Form, TRUE);
			$this->_LookupField->onLoadNode($lookupAttrs, $children['LOOKUPFIELD']->getChildrenTagsArray());
			$this->_Form->fields[$this->_LookupField->getName()] =& $this->_LookupField;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_EDITSEARCH_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @uses Form::resolveVariables()
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		$this->attributes['URL'] = $this->_Form->resolveVariables($this->attributes['URL']);
		if ($this->_Form->isPosted) {
			$lastFilter = HttpRequest::getVar($this->id . '_lastfilter', $this->_Form->formMethod);
			$lastSearch = HttpRequest::getVar($this->id . '_lastsearch', $this->_Form->formMethod);
			if ($lastFilter !== NULL && $lastSearch !== NULL) {
				$clause = sprintf($this->filters[$lastFilter][1], $lastSearch);
				if (empty($this->dataSource['CLAUSE']))
					$this->dataSource['CLAUSE'] = $clause;
				else
					$this->dataSource['CLAUSE'] = "({$this->dataSource['CLAUSE']}) AND {$clause}";
				@parent::processDbQuery(ADODB_FETCH_NUM);
				$this->_LookupField->setRecordSet($this->_Rs);
			}
		}
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/editsearchfield.js');
		$this->_LookupField->onDataBind();
		$this->_LookupField->setRequired(FALSE);
		$this->_LookupField->setDisabled($this->disabled);
		$this->_LookupField->onPreRender();
	}

	/**
	 * Responds to the JSRS search request
	 *
	 * Receive filter name and search term, and use them
	 * to filter the component's data source, returning
	 * the search results in a string based on separators
	 * for lines and columns.
	 *
	 * @param string $filter Filter name
	 * @param string $term Search term
	 * @return string
	 */
	function performSearch($filter, $term) {
		if (isset($this->filters[$filter])) {
			// builds the condition clause
			$clause = sprintf($this->filters[$filter][1], $term);
			if (empty($this->dataSource['CLAUSE']))
				$this->dataSource['CLAUSE'] = $clause;
			else
				$this->dataSource['CLAUSE'] = "({$this->dataSource['CLAUSE']}) AND {$clause}";
			// execute the query
			@parent::processDbQuery(ADODB_FETCH_NUM, ServiceJSRS::debugEnabled());
			// build the results string
			if ($this->_Rs->RecordCount() > 0) {
				$lines = array();
				while (!$this->_Rs->EOF) {
					$lines[] = @$this->_Rs->fields[0] . '~' . @$this->_Rs->fields[1];
					$this->_Rs->MoveNext();
				}
				return implode('|', $lines);
			}
		}
		return '';
	}
}
?>