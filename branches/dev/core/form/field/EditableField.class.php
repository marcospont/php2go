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

import('php2go.form.field.FormField');
import('php2go.text.StringUtils');

/**
 * Default size of editable fields
 */
define('EDITABLE_FIELD_DEFAULT_SIZE', 10);

/**
 * Base class for editable form components
 *
 * @package form
 * @subpackage field
 * @uses StringUtils
 * @uses TypeUtils
 * @uses Validator
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 * @abstract
 */
class EditableField extends FormField
{
	/**
	 * Indicates if component is in read-only mode
	 *
	 * @var bool
	 * @access private
	 */
	var $readOnly = NULL;

	/**
	 * Input minimum length
	 *
	 * @var int
	 * @access private
	 */
	var $minLength;

	/**
	 * Input maximum length
	 *
	 * @var int
	 * @access private
	 */
	var $maxLength;

	/**
	 * Input mask
	 *
	 * @var string
	 * @access private
	 */
	var $mask = '';

	/**
	 * Input mask setup code
	 *
	 * @var string
	 * @access private
	 */
	var $maskSetupScript = '';

	/**
	 * Size limiters used by the component's mask
	 *
	 * @var array
	 * @access private
	 */
	var $limiters;

	/**
	 * Must be implemented by child classes
	 *
	 * @abstract
	 */
	function display() {
	}

	/**
	 * Get component's mask name
	 *
	 * @return string
	 */
	function getMask() {
		return $this->mask;
	}

	/**
	 * Get mask limiters
	 *
	 * @return array
	 */
	function getMaskLimiters() {
		return $this->limiters;
	}

	/**
	 * Set component's mask
	 *
	 * A <b>mask</b> is used to restrict data entered in the
	 * text input, as well as to validate the submitted data
	 * using a set of <b>data type validators</b> in the client
	 * side and in the server side.
	 *
	 * @param string $mask Mask name
	 */
	function setMask($mask) {
		$matches = array();
		$mask = trim(strtoupper($mask));
		if (!empty($mask)) {
			if (preg_match(PHP2GO_MASK_PATTERN, $mask, $matches)) {
				if (isset($matches[6]) && $matches[6] == 'ZIP') {
					$this->mask = $matches[6];
					$this->limiters = array($matches[8], $matches[9]);
					$this->setLength($matches[8] + $matches[9] + 1);
				} elseif (isset($matches[2]) && $matches[2] == 'FLOAT') {
					$this->mask = $matches[2];
					$this->limiters = array($matches[4], $matches[5]);
					$this->setLength($matches[4] + $matches[5] + 2);
				} elseif ($matches[0] == 'DATE') {
					$mask = $this->mask = 'DATE-' . PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
				} elseif ($matches[0] == 'LOGIN') {
					$this->mask = 'WORD';
				} else {
					$this->mask = $matches[0];
				}
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_INVALID_MASK', array($mask, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
			}
			$this->maskSetupScript = "\tInputMask.setup('%s', Mask.fromMaskName('{$mask}'));";
		}
	}

	/**
	 * Set input size
	 *
	 * @param int $size Input size
	 */
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = $size;
	}

	/**
	 * Set input maximum length
	 *
	 * @param int $length Input maxlength
	 * @deprecated Use {@link setMaxLength}
	 */
	function setLength($length) {
		if (TypeUtils::isInteger($length))
			$this->attributes['LENGTH'] = $length;
	}

	/**
	 * Set input maximum length
	 *
	 * When applied to a {@link MemoField} component, this
	 * property applies client and server validation to
	 * assure submitted value respects the maximum length.
	 *
	 * This property is ignored on {@link EditField} components
	 * that use one of the following masks: CPFCNPJ, DATE, FLOAT,
	 * TIME and ZIP.
	 *
	 * @param int $maxLength Input maxlength
	 */
	function setMaxLength($maxLength) {
		if (TypeUtils::isInteger($maxLength)) {
			if (isset($this->limiters))
				$this->maxLength = max($maxLength, array_sum($this->limiters) + 1);
			else
				$this->maxLength = $maxLength;
		}
	}

	/**
	 * Set input minimum length
	 *
	 * This property is ignored on {@link EditField} components
	 * that use one of the following masks: CPFCNPJ, DATE, FLOAT,
	 * TIME and ZIP.
	 *
	 * @param int $minLength Minimum length
	 */
	function setMinLength($minLength) {
		if (TypeUtils::isInteger($minLength))
			$this->minLength = $minLength;
	}

	/**
	 * Enable/disable the autocomplete feature of the browser
	 *
	 * @param bool $setting Enable/disable
	 */
	function setAutoComplete($setting) {
		if ($setting === TRUE)
			$this->attributes['AUTOCOMPLETE'] = " autocomplete=\"on\"";
		elseif ($setting === FALSE)
			$this->attributes['AUTOCOMPLETE'] = " autocomplete=\"off\"";
		else
			$this->attributes['AUTOCOMPLETE'] = "";
	}

	/**
	 * Enable/disable read-only mode
	 *
	 * @param bool $setting Enable/disable
	 */
	function setReadonly($setting=TRUE) {
		if ($setting) {
			$this->attributes['READONLY'] = " readonly=\"readonly\"";
			$this->readOnly = TRUE;
		} else {
			$this->attributes['READONLY'] = "";
			$this->readOnly = FALSE;
		}
	}

	/**
	 * Enable/disable the conversion of the component's value to uppercase
	 *
	 * This conversion is executed upon submission.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setUpper($setting=TRUE) {
		if ($setting)
			$this->attributes['UPPER'] = "T";
		else
			$this->attributes['UPPER'] = "F";
	}

	/**
	 * Enable/disable the conversion of the component's value to lowercase
	 *
	 * This conversion is executed upon submission.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setLower($setting=TRUE) {
		if ($setting)
			$this->attributes['LOWER'] = "T";
		else
			$this->attributes['LOWER'] = "F";
	}

	/**
	 * Override parent class implementation to validate the component's
	 * value against the <b>maxlength</b>, <b>minlength</b> and <b>data
	 * type</b> validators
	 *
	 * @uses Validator::validateField()
	 * @return bool
	 */
	function isValid() {
		if ($this->attributes['UPPER'] == "T")
			$this->value = strtoupper($this->value);
		if ($this->attributes['LOWER'] == "T")
			$this->value = strtolower($this->value);
		$result = parent::isValid();
		$validators = array();
		if (trim($this->value) != '' && $this->mask != '') {
			switch ($this->mask) {
				case 'CPFCNPJ' :
					$validators[] = array('php2go.validation.CPFCNPJValidator', NULL, NULL);
					break;
				case 'CURRENCY' :
					$validators[] = array('php2go.validation.CurrencyValidator', NULL, NULL);
					break;
				case 'DATE-EURO' :
				case 'DATE-US' :
					$validators[] = array('php2go.validation.DateValidator', NULL, NULL);
					break;
				case 'EMAIL' :
					$validators[] = array('php2go.validation.EmailValidator', NULL, NULL);
					break;
				case 'FLOAT' :
					$validators[] = array('php2go.validation.FloatValidator', (is_array($this->limiters) ? array('limiters' => $this->limiters, 'decimalPoint' => '.') : array('decimalPoint' => '.')), NULL);
					break;
				case 'INTEGER' :
					$validators[] = array('php2go.validation.IntegerValidator', array('unsigned' => FALSE), NULL);
					break;
				case 'DIGIT' :
					$validators[] = array('php2go.validation.IntegerValidator', array('unsigned' => TRUE), NULL);
					break;
				case 'WORD' :
					$validators[] = array('php2go.validation.WordValidator', NULL, NULL);
					break;
				case 'TIME' :
					$validators[] = array('php2go.validation.TimeValidator', NULL, NULL);
					break;
				case 'URL' :
					$validators[] = array('php2go.validation.UrlValidator', NULL, NULL);
					break;
				case 'ZIP' :
					$validators[] = array('php2go.validation.ZipCodeValidator', array('limiters' => $this->limiters), NULL);
					break;
			}
		}
		if (!preg_match('/^(CPFCNPJ$|DATE|FLOAT$|TIME|ZIP$)/', $this->mask)) {
			if (isset($this->minLength))
				$validators[] = array('php2go.validation.MinLengthValidator', array('minlength' => $this->minLength, 'bypassEmpty' => TRUE), NULL);
			if (isset($this->maxLength))
				$validators[] = array('php2go.validation.MaxLengthValidator', array('maxlength' => $this->maxLength, 'bypassEmpty' => TRUE), NULL);
		}
		foreach ($validators as $validator) {
			$result &= Validator::validateField($this, $validator[0], $validator[1], $validator[2]);
		}
		return (bool)$result;
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		if (is_array($this->value))
			$this->value = '';
		// input size
		// 1) from SIZE attribute
		if (isset($attrs['SIZE']) && TypeUtils::isInteger($attrs['SIZE']))
			$this->setSize($attrs['SIZE']);
		// 2) from LENGTH attribute
		elseif (isset($attrs['LENGTH']) && TypeUtils::isInteger($attrs['LENGTH']))
			$this->setSize($attrs['LENGTH']);
		// 3) use default size
		else
			$this->setSize(EDITABLE_FIELD_DEFAULT_SIZE);
		// minimum length
		if (isset($attrs['MINLENGTH']) && TypeUtils::isInteger($attrs['MINLENGTH']))
			$this->setMinLength($attrs['MINLENGTH']);
		// maximum length
		if (isset($attrs['MAXLENGTH']) && TypeUtils::isInteger($attrs['MAXLENGTH']))
			$this->setMaxLength($attrs['MAXLENGTH']);
		// maximum chars allowed
		// 1) from LENGTH attribute
		if (isset($attrs['LENGTH']) && TypeUtils::isInteger($attrs['LENGTH']))
			$this->setLength($attrs['LENGTH']);
		// 2) from MAXLENGTH attribute
		elseif (isset($this->maxLength))
			$this->setLength($this->maxLength);
		// 3) from SIZE attribute
		else
			$this->setLength($this->attributes['SIZE']);
		// mask
		$this->setMask(@$attrs['MASK']);
		// autocomplete
		$this->setAutoComplete(resolveBooleanChoice(@$attrs['AUTOCOMPLETE']));
		// read-only
		$readOnly = (resolveBooleanChoice(@$attrs['READONLY']) || $this->_Form->readonly);
		if ($readOnly)
			$this->setReadonly();
		// upper
		$this->setUpper(resolveBooleanChoice(@$attrs['UPPER']));
		// lower
		$this->setLower(resolveBooleanChoice(@$attrs['LOWER']));
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		if (!is_string($this->value))
			$this->value = strval($this->value);
		switch ($this->mask) {
			case 'DATE-EURO' :
			case 'DATE-US' :
				$this->searchDefaults['OPERATOR'] = 'EQ';
				$this->searchDefaults['DATATYPE'] = 'DATE';
				break;
			case 'FLOAT' :
			case 'INTEGER' :
				$this->searchDefaults['OPERATOR'] = 'EQ';
				$this->searchDefaults['DATATYPE'] = $this->mask;
				break;
		}
	}

	/**
	 * Prepares the component to be rendered
	 *
	 * @uses StringUtils::escape()
	 */
	function onPreRender() {
		parent::onPreRender();
		if ($this->readOnly === NULL) {
			if ($this->_Form->readonly)
				$this->setReadonly();
			else
				$this->setReadonly(FALSE);
		}
		// build mask setup and data type validation script
		if ($this->mask != '') {
			$args = array();
			$args[] = "mask:'{$this->mask}'";
			if ($this->mask == 'FLOAT' && is_array($this->limiters)) {
				$msg = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_FLOAT', array($this->label, $this->limiters[0], $this->limiters[1]));
				$args[] = "msg:\"" . StringUtils::escape($msg, 'javascript') . "\"";
			}
			$this->_Form->validatorCode .= sprintf("\t%sValidator.add('%s', DataTypeValidator, {%s});\n", strtolower($this->_Form->formName), $this->name, implode(',', $args));
			$this->_Form->Document->addScriptCode(sprintf($this->maskSetupScript, $this->id), 'Javascript', SCRIPT_END);
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'inputmask.js');
		}
		// add maxlength and minlength validation script
		if (isset($this->minLength) && TypeUtils::isInteger($this->minLength))
			$this->_Form->validatorCode .= sprintf("\t%sValidator.add('%s', LengthValidator, {rule:'MINLENGTH',limit:%d});\n", strtolower($this->_Form->formName), $this->name, $this->minLength);
		if (isset($this->maxLength) && TypeUtils::isInteger($this->maxLength))
			$this->_Form->validatorCode .= sprintf("\t%sValidator.add('%s', LengthValidator, {rule:'MAXLENGTH',limit:%d});\n", strtolower($this->_Form->formName), $this->name, $this->maxLength);
		// add value transformation scripts
		if ($this->attributes['UPPER'] == 'T')
			$this->_Form->beforeValidateCode .= sprintf("\t\tfrm.elements['%s'].value = frm.elements['%s'].value.toUpperCase();\n", $this->name, $this->name);
		if ($this->attributes['LOWER'] == 'T')
			$this->_Form->beforeValidateCode .= sprintf("\t\tfrm.elements['%s'].value = frm.elements['%s'].value.toLowerCase();\n", $this->name, $this->name);
	}
}
?>