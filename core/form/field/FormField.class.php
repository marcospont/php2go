<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
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
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.datetime.Date');
import('php2go.util.HtmlUtils');

/**
 * Base class for all form components
 *
 * @package form
 * @subpackage field
 * @uses Date
 * @uses HttpRequest
 * @uses HtmlUtils
 * @uses Validator
 * @abstract
 */
class FormField extends Component
{
	/**
	 * Field ID
	 *
	 * @var string
	 */
	var $id;

	/**
	 * Field name
	 *
	 * @var string
	 */
	var $name;

	/**
	 * Name used to identify the form component
	 * when configuring client validation
	 *
	 * @var string
	 */
	var $validationName;

	/**
	 * Field label
	 *
	 * @var string
	 */
	var $label;

	/**
	 * Field access key
	 *
	 * @var string
	 */
	var $accessKey;

	/**
	 * Field value
	 *
	 * @var mixed
	 */
	var $value = '';

	/**
	 * Tag name in the XML specification
	 *
	 * @var string
	 */
	var $fieldTag;

	/**
	 * HTML input type
	 *
	 * @var string
	 */
	var $htmlType;

	/**
	 * Whether the field is required
	 *
	 * @var bool
	 */
	var $required = FALSE;

	/**
	 * Whether the field is disabled
	 *
	 * @var bool
	 */
	var $disabled = NULL;

	/**
	 * Whether the component is child of another component
	 *
	 * @var bool
	 */
	var $child = FALSE;

	/**
	 * Indicates a composite form component
	 *
	 * @var bool
	 */
	var $composite = FALSE;

	/**
	 * Indicates if this component can be used on search forms
	 *
	 * @var bool
	 */
	var $searchable = TRUE;

	/**
	 * Field rules
	 *
	 * @var array
	 * @access protected
	 */
	var $rules = array();

	/**
	 * Field JS listeners
	 *
	 * @var array
	 * @access protected
	 */
	var $listeners = array();

	/**
	 * Custom events handled by the component
	 *
	 * @var array
	 * @access protected
	 */
	var $customEvents = array();

	/**
	 * Custom event listeners
	 *
	 * @var array
	 * @access protected
	 */
	var $customListeners = array();

	/**
	 * Custom search settings
	 *
	 * @var array
	 * @access protected
	 */
	var $search = array();

	/**
	 * Default search settings
	 *
	 * @var array
	 * @access protected
	 */
	var $searchDefaults = array();

	/**
	 * Indicates the data bind phase was already executed
	 *
	 * @var bool
	 * @access private
	 */
	var $dataBind = FALSE;

	/**
	 * Reference to the parent form
	 *
	 * @var object Form
	 */
	var $_Form = NULL;

	/**
	 * Class constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return FormField
	 */
	function FormField(&$Form, $child=FALSE) {
		parent::Component();
		if ($this->isA('FormField', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'FormField'), E_USER_ERROR, __FILE__, __LINE__);
		$this->_Form =& $Form;
		$this->child = $child;
		$this->fieldTag = strtoupper(parent::getClassName());
		$this->searchDefaults = array(
			'FIELDTYPE' => $this->fieldTag,
			'OPERATOR' => 'CONTAINING',
			'DATATYPE' => 'STRING',
			'DESCSOURCE' => 'DISPLAY'
		);
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Must be implemented by child classes
	 *
	 * @abstract
	 */
	function display() {
	}

	/**
	 * Get component's ID
	 *
	 * @return string
	 */
	function getId() {
		return $this->id;
	}

	/**
	 * Get the ID of the control that should be activated
	 * when the component's label is clicked
	 *
	 * @return string
	 */
	function getFocusId() {
		return $this->id;
	}

	/**
	 * Set the component's ID
	 *
	 * @param string $id New ID
	 */
	function setId($id) {
		if (!empty($id))
			$this->id = $id;
		else
			$this->id = PHP2Go::generateUniqueId(parent::getClassName());
	}

	/**
	 * Get the component's name
	 *
	 * @return string
	 */
	function getName() {
		return $this->name;
	}

	/**
	 * Set the component's name
	 *
	 * @param string $newName New name
	 */
	function setName($newName) {
		$oldName = $this->name;
		if ($newName != '')
			$this->name = $newName;
		else
			$this->name = $this->id;
		$this->validationName = $this->name;
		$this->searchDefaults['ALIAS'] = $this->name;
		Form::verifyFieldName($this->_Form->formName, $this->name);
		if (!empty($oldName) && isset($this->_Form->fields[$oldName])) {
			unset($this->_Form->fields[$oldName]);
			$this->_Form->fields[$this->name] =& $this;
		}
	}

	/**
	 * Get component's label
	 *
	 * @return string
	 */
	function getLabel() {
		return $this->label;
	}

	/**
	 * Build and return the HTML code of the component's label
	 *
	 * @param bool $reqFlag Whether the required field mark should be displayed
	 * @param string $reqColor Color of the required field mark
	 * @param string $reqText Contents of the required field mark
	 * @return string
	 */
	function getLabelCode($reqFlag, $reqColor, $reqText) {
		$UserAgent =& UserAgent::getInstance();
		$label = $this->label;
		if ($label != 'empty') {
			if ($this->accessKey && $this->_Form->accessKeyHighlight) {
				$pos = strpos(strtoupper($label), strtoupper($this->accessKey));
				if ($pos !== FALSE)
					$label = substr($label, 0, $pos) . '<u>' . $label[$pos] . '</u>' . substr($label, $pos+1);
			}
			$required = ($this->required && !$this->disabled && $reqFlag ? "<span style=\"color:{$reqColor}\">{$reqText}</span>" : '');
			if ($this->htmlType == 'SELECT' && $UserAgent->matchBrowser('ie'))
				return sprintf("<label id=\"%s\" onclick=\"var target=$('%s');if(target && !target.disabled)target.focus();\"%s>%s%s</label>",
						$this->getName() . '_label', $this->getFocusId(),
						$this->_Form->getLabelStyle(), $label, $required
				);
			else
				return sprintf("<label for=\"%s\" id=\"%s\"%s>%s%s</label>",
						$this->getFocusId(), $this->getName() . '_label',
						$this->_Form->getLabelStyle(), $label, $required
				);
		} else {
			return '';
		}
	}

	/**
	 * Set component's label
	 *
	 * @param string $label New label
	 */
	function setLabel($label) {
		$label = trim(strval($label));
		if (strlen($label) > 0)
			$this->label = resolveI18nEntry($label);
		else
			$this->label = $this->name;
	}

	/**
	 * Get component's value
	 *
	 * @return mixed
	 */
	function getValue() {
		return $this->value;
	}

	/**
	 * Get a human-readable representation of the component's value
	 *
	 * @return string
	 */
	function getDisplayValue() {
		return $this->value;
	}

	/**
	 * Get component's search data
	 *
	 * This method is used by {@link SearchForm} class to retrieve
	 * search information about a form field.
	 *
	 * @return array
	 */
	function getSearchData() {
		$search = array_merge($this->searchDefaults, $this->search);
		if ($this->_Form->isPosted()) {
			$operators = PHP2Go::getLangVal('OPERATORS');
			$search['VALUE'] = $this->getValue();
			$display = ($search['DESCSOURCE'] == 'DISPLAY' ? $this->getDisplayValue() : $this->getValue());
			if ($search['DATATYPE'] == 'STRING' && $search['OPERATOR'] != 'BETWEEN')
				$display = "\"{$display}\"";
			$search['DISPLAYVALUE'] = sprintf("%s %s %s", $this->getLabel(), $operators[$search['OPERATOR']], $display);
		}
		return $search;
	}

	/**
	 * Set the component's value
	 *
	 * @param mixed $value New value
	 */
	function setValue($value) {
		if (!$this->dataBind)
			$this->onDataBind();
		$this->value = $value;
	}

	/**
	 * Register the component's value in the set of form submitted values
	 *
	 * @param string $value Submitted value
	 * @access protected
	 */
	function setSubmittedValue($value=NULL) {
		$sv =& $this->_Form->submittedValues;
		$value = TypeUtils::ifNull($value, $this->getValue());
		if (preg_match("/([^\[]+)\[([^\]]+)\]/", $this->name, $matches)) {
			if (!isset($sv[$matches[1]]))
				$sv[$matches[1]] = array();
			$sv[$matches[1]][$matches[2]] = $value;
		} else {
			$sv[$this->name] = $value;
		}
	}

	/**
	 * Get the element name used to declare this
	 * component in the XML specification
	 *
	 * @return string
	 */
	function getFieldTag() {
		return $this->fieldTag;
	}

	/**
	 * Get the component's HTML input type
	 *
	 * @return string
	 */
	function getHtmlType() {
		return $this->htmlType;
	}

	/**
	 * Get the component's parent form
	 *
	 * @return Form
	 */
	function &getOwnerForm() {
		return $this->_Form;
	}

	/**
	 * Builds and returns the HTML code of the component's help tooltip
	 *
	 * @return string
	 */
	function getHelpCode() {
		if ($this->attributes['HELP'] != '') {
			if ($this->_Form->helpOptions['mode'] == FORM_HELP_INLINE) {
				$style = (isset($this->_Form->helpOptions['text_style']) ? " class=\"{$this->_Form->helpOptions['text_style']}\"" : $this->_Form->getLabelStyle());
				return sprintf("<div id=\"%s\"%s>%s</div>",
					$this->getName() . '_help',
					$style, $this->attributes['HELP']);
			} else {
				return sprintf("<img id=\"%s\" src=\"%s\" alt=\"\" border=\"0\"%s />",
					$this->getName() . '_help', $this->_Form->helpOptions['popup_icon'],
					' ' . HtmlUtils::overPopup($this->_Form->Document, $this->attributes['HELP'], $this->_Form->helpOptions['popup_attrs']));
			}
		}
		return '';
	}

	/**
	 * Set component's help message
	 *
	 * @param string $help Help message
	 */
	function setHelp($help) {
		$help = trim($help);
		if ($help != '')
			$this->attributes['HELP'] = resolveI18nEntry($help);
		else
			$this->attributes['HELP'] = '';
	}

	/**
	 * Check if the component is required
	 *
	 * @return bool
	 */
	function isRequired() {
		return $this->required;
	}

	/**
	 * Change the obligatoriness state of the component
	 *
	 * @param bool $setting Set this to TRUE to flag the field as required
	 */
	function setRequired($setting=TRUE) {
		$this->required = (bool)$setting;
	}

	/**
	 * Enable/disable the component
	 *
	 * @param bool $setting Enable/disable
	 */
	function setDisabled($setting=TRUE) {
		if ($setting) {
			$this->attributes['DISABLED'] = " disabled=\"disabled\"";
			$this->disabled = TRUE;
		} else {
			$this->attributes['DISABLED'] = "";
			$this->disabled = FALSE;
		}
	}

	/**
	 * Set component's CSS class
	 *
	 * To define a single CSS class for all fields in
	 * a form, use {@link Form::setInputStyle()} or declare
	 * it in the XML file (//form/style[@input]). To define
	 * a global CSS configuration for all forms, use the
	 * FORMS entry of the global configuration settings.
	 *
	 * @param string $style CSS class
	 */
	function setStyle($style) {
		$style = trim($style);
		if ($style == 'empty') {
			$this->attributes['STYLE'] = '';
			$this->attributes['USERSTYLE'] = '';
		} elseif ($style != '') {
			$this->attributes['STYLE'] = " class=\"{$style}\"";
			$this->attributes['USERSTYLE'] = " class=\"{$style}\"";
		} else {
			$this->attributes['STYLE'] = $this->_Form->getInputStyle();
			$this->attributes['USERSTYLE'] = "";
		}
	}

	/**
	 * Set component's access key
	 *
	 * @param string $accessKey Access key
	 */
	function setAccessKey($accessKey) {
		if (trim($accessKey) != '') {
			$this->attributes['ACCESSKEY'] = " accesskey=\"$accessKey\"";
			$this->accessKey = $accessKey;
		} else {
			$this->attributes['ACCESSKEY'] = '';
			$this->accessKey = '';
		}
	}

	/**
	 * Set component's tab index
	 *
	 * @param int $tabIndex Tab index
	 */
	function setTabIndex($tabIndex) {
		if (TypeUtils::isInteger($tabIndex))
			$this->attributes['TABINDEX'] = " tabindex=\"$tabIndex\"";
		else
			$this->attributes['TABINDEX'] = '';
	}

	/**
	 * Adds a new event listener
	 *
	 * @param FormEventListener $Listener New listener
	 */
	function addEventListener($Listener) {
		$Listener->setOwner($this);
		if ($Listener->isValid())
			$this->listeners[] =& $Listener;
	}

	/**
	 * Adds a new validation rule
	 *
	 * @param FormRule $Rule New rule
	 */
	function addRule($Rule) {
		$Rule->setOwnerField($this);
		if ($Rule->isValid())
			$this->rules[] =& $Rule;
	}

	/**
	 * Validates the component's value
	 *
	 * Check if the component's value is valid by executing
	 * and collecting the results of all registered validators.
	 *
	 * @uses Validator::validateField()
	 * @return bool
	 */
	function isValid() {
		if (!$this->dataBind)
			$this->onDataBind();
		$validators = array();
		if ($this->required && !$this->composite) {
			$params = array();
			$params['fieldClass'] = strtolower($this->fieldTag);
			$validators[] = array('php2go.validation.RequiredValidator', $params, NULL);
		}
		for ($i=0,$s=sizeof($this->rules); $i<$s; $i++) {
			$params = array();
			$params['rule'] =& $this->rules[$i];
			$validators[] = array('php2go.validation.RuleValidator', $params, $this->rules[$i]->getMessage());
		}
		$result = TRUE;
		foreach ($validators as $validator)
			$result &= Validator::validateField($this, $validator[0], $validator[1], $validator[2]);
		return (bool)$result;
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		$isDataBind = $this->_Form->isA('FormDataBind');
		// id
		$this->setId(TypeUtils::ifNull(@$attrs['ID'], @$attrs['NAME']));
		// name
		$this->setName(@$attrs['NAME']);
		// label
		$this->setLabel(@$attrs['LABEL']);
		// store VALUE and DEFAULT attributes for further processing
		if (isset($attrs['VALUE']))
			$this->attributes['VALUE'] = ($attrs['VALUE'] == 'empty' ? '' : $attrs['VALUE']);
		if (isset($attrs['DEFAULT']))
			$this->attributes['DEFAULT'] = ($attrs['DEFAULT'] == 'empty' ? '' : $attrs['DEFAULT']);
		// help message
		$this->setHelp(@$attrs['HELP']);
		// CSS class
		$this->setStyle(@$attrs['STYLE']);
		// access key
		$this->setAccessKey(@$attrs['ACCESSKEY']);
		// tab index
		$this->setTabIndex(@$attrs['TABINDEX']);
		// disabled
		$disabled = (resolveBooleanChoice(@$attrs['DISABLED']) || $isDataBind || $this->_Form->readonly);
		if ($disabled)
			$this->setDisabled();
		// required
		$this->setRequired(resolveBooleanChoice(@$attrs['REQUIRED']));
		// event listeners
		if (isset($children['LISTENER'])) {
			$listeners = TypeUtils::toArray($children['LISTENER']);
			foreach ($listeners as $listenerNode)
				$this->addEventListener(FormEventListener::fromNode($listenerNode));
		}
		// validation rules
		if (isset($children['RULE']) && !$this->composite) {
			$rules = TypeUtils::toArray($children['RULE']);
			foreach ($rules as $ruleNode)
				$this->addRule(FormRule::fromNode($ruleNode));
		}
		// search settings
		if (!$this->child && isset($children['SEARCH'])) {
			$this->search = TypeUtils::toArray(@$children['SEARCH']->getAttributes());
			$this->search['IGNORE'] = resolveBooleanChoice(@$this->search['IGNORE']);
		}
		// data bind attributes
		if ($isDataBind && !$this->composite) {
			$this->attributes['DATASRC'] = " datasrc=\"#" . $this->_Form->getDbName() . "\"";
			$this->attributes['DATAFLD'] = " datafld=\"{$this->name}\"";
		} else {
			$this->attributes['DATASRC'] = '';
			$this->attributes['DATAFLD'] = '';
		}
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * This method defines the component's value, through the following steps:
	 * # check if the form was posted
	 * # if the form is posted, tries to load the value from the request
	 * # if the value is not found, try to use VALUE and DEFAULT attributes of the XML specification
	 * # if the form is not posted, tries to use the VALUE attribute
	 * # if the field hasn't a VALUE attribute, try to load the value from the request
	 * # finally, tries to define the component's value from its DEFAULT attribute
	 *
	 * @uses Form::resolveVariables()
	 * @access protected
	 */
	function onDataBind() {
		$this->dataBind = TRUE;
		if (!$this->composite || $this->isA('RangeField') || $this->isA('DataGrid')) {
			$magicq = (System::getIni('magic_quotes_gpc') == 1);
			if ($this->_Form->isPosted()) {
				// 1) submitted value
				$submittedValue = HttpRequest::getVar(preg_replace("/\[\]$/", '', $this->name), $this->_Form->formMethod);
				if ($submittedValue !== NULL) {
					if (is_string($submittedValue) && $magicq)
						$submittedValue = stripslashes($submittedValue);
					$this->setValue($submittedValue);
					$this->setSubmittedValue();
				}
				// 2) submitted value === NULL means "F" on checkbox inputs
				elseif ($this->isA('CheckField')) {
					$this->setValue('F');
					$this->setSubmittedValue();
				}
				// 3) VALUE attribute
				elseif (isset($this->attributes['VALUE'])) {
					if (preg_match("/~[^~]+~/", $this->attributes['VALUE']))
						$this->setValue($this->_Form->resolveVariables($this->attributes['VALUE']));
					else
						$this->setValue($this->attributes['VALUE']);
				}
				// 4) DEFAULT attribute
				elseif (isset($this->attributes['DEFAULT'])) {
					if (preg_match("/~[^~]+~/", $this->attributes['DEFAULT']))
						$this->setValue($this->_Form->resolveVariables($this->attributes['DEFAULT']));
					else
						$this->setValue($this->attributes['DEFAULT']);
				}
			} else {
				// 1) VALUE attribute
				if (isset($this->attributes['VALUE'])) {
					if (preg_match("/~[^~]+~/", $this->attributes['VALUE']))
						$this->setValue($this->_Form->resolveVariables($this->attributes['VALUE']));
					else
						$this->setValue($this->attributes['VALUE']);
				} else {
					// 2) read from the request
					$requestValue = HttpRequest::getVar(preg_replace("/\[\]$/", '', $this->name), 'all', 'ROSGPCE');
					if ($requestValue !== NULL) {
						if (is_string($requestValue) && $magicq)
							$requestValue = stripslashes($requestValue);
						$this->setValue($requestValue);
					}
					// 3) DEFAULT attribute
					elseif (isset($this->attributes['DEFAULT'])) {
						if (preg_match("/~[^~]+~/", $this->attributes['DEFAULT']))
							$this->setValue($this->_Form->resolveVariables($this->attributes['DEFAULT']));
						else
							$this->setValue($this->attributes['DEFAULT']);
					}
				}
			}
		}
		for ($i=0,$s=sizeof($this->listeners); $i<$s; $i++) {
			$Listener =& $this->listeners[$i];
			$Listener->onDataBind();
		}
		if (!$this->composite) {
			for ($i=0,$s=sizeof($this->rules); $i<$s; $i++) {
				$Rule =& $this->rules[$i];
				$Rule->onDataBind();
			}
		}
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		if (!$this->dataBind)
			$this->onDataBind();
		if ($this->disabled === NULL) {
			if ($this->_Form->readonly)
				$this->setDisabled();
			else
				$this->setDisabled(FALSE);
		}
		if ($this->disabled)
			$this->setRequired(FALSE);
		// add required validation script
		if ($this->required && !$this->isA('RangeField') && !$this->isA('DataGrid'))
			$this->_Form->validatorCode .= sprintf("\t%s_validator.add('%s', RequiredValidator);\n", $this->_Form->formName, $this->validationName);
		// build and register valiadation rules script code
		if (!empty($this->rules)) {
			foreach ($this->rules as $Rule)
				$this->_Form->validatorCode .= $Rule->getScriptCode();
		}
		// render event listeners
		if (@$this->attributes['SCRIPT'] === NULL)
			$this->renderListeners();
	}

	/**
	 * Renders all event listeners configured for this component
	 *
	 * @access protected
	 */
	function renderListeners() {
		$events = array();
		$custom = array();
		$this->attributes['SCRIPT'] = '';
		foreach ($this->listeners as $listener) {
			$eventName = $listener->eventName;
			if ($listener->custom) {
				if (!isset($custom[$eventName]))
					$custom[$eventName] = array();
				$custom[$eventName][] = $listener->getScriptCode();
			} else {
				if (!isset($events[$eventName]))
					$events[$eventName] = array();
				$events[$eventName][] = $listener->getScriptCode();
			}
		}
		foreach ($events as $name => $actions)
			$this->attributes['SCRIPT'] .= " " . strtolower($name) . "=\"" . str_replace('\"', '\'', implode(';', $actions)) . ";\"";
		foreach ($custom as $name => $actions) {
			$actions = implode(';', $actions);
			$this->customListeners[$name] = "function(args) {\n\t" . $actions . ";\n}";
		}
	}

	/**
	 * Extract information from a DATASOURCE node
	 *
	 * A data source is used by components that rely on an
	 * external data source to load data sets
	 *
	 * @param XmlNode &$DataSource DATASOURCE node
	 * @return array Data source settings
	 * @access protected
	 */
	function parseDataSource(&$DataSource) {
		$result = array();
		if (TypeUtils::isInstanceOf($DataSource, 'XmlNode')) {
			$elements = $DataSource->getChildrenTagsArray();
			$connectionId = $DataSource->attrs['CONNECTION'];
			if (!$connectionId)
				$connectionId = NULL;
			$result['CONNECTIONID'] = $connectionId;
			$dataSourceElements = $DataSource->getChildrenTagsArray();
			if (empty($dataSourceElements))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATASOURCE_SYNTAX', $this->name), E_USER_ERROR, __FILE__, __LINE__);
			foreach($dataSourceElements as $name => $node) {
				if ($name == 'PROCEDURE')
					$result['CURSORNAME'] = ($node->hasAttribute('CURSORNAME') ? $node->getAttribute('CURSORNAME') : NULL);
				$result[$name] = $node->value;
			}
			if (!isset($result['PROCEDURE']) && (!isset($result['KEYFIELD']) || !isset($result['LOOKUPTABLE'])))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATASOURCE_SYNTAX', $this->name), E_USER_ERROR, __FILE__, __LINE__);
			if (!isset($result['PROCEDURE'])) {
				if (!isset($result['DISPLAYFIELD']))
					$result['DISPLAYFIELD'] = $result['KEYFIELD'];
				if (!isset($result['CLAUSE']))
					$result['CLAUSE'] = '';
				if (!isset($result['GROUPBY']))
					$result['GROUPBY'] = '';
				if (!isset($result['ORDERBY']))
					$result['ORDERBY'] = '';
				if (!isset($result['GROUPFIELD']))
					$result['GROUPFIELD'] = '';
				if (!isset($result['GROUPDISPLAY']))
					$result['GROUPDISPLAY'] = $result['GROUPFIELD'];
			}
		}
		return $result;
	}
}
?>