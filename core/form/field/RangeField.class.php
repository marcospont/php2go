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

/**
 * Builds a pair of inputs representing a range of values
 *
 * The range can be composed by a pair of the following form
 * components: {@link EditField}, {@link ComboField},
 * {@link LookupField} or {@link DatePickerField}.
 *
 * It's also able to configure a caption to involve the
 * pair of inputs (for instance, "Between %s and %s"), and
 * to perform automatically comparison validation on the
 * submitted values.
 *
 * @package form
 * @subpackage field
 * @uses ComboField
 * @uses DatePickerField
 * @uses EditField
 * @uses LookupField
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class RangeField extends FormField
{
	/**
	 * Start range member
	 *
	 * @var object FormField
	 * @access private
	 */
	var $_StartField = NULL;

	/**
	 * End range member
	 *
	 * @var object FormField
	 * @access private
	 */
	var $_EndField = NULL;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return RangeField
	 */
	function RangeField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->composite = TRUE;
		$this->searchDefaults['OPERATOR'] = 'BETWEEN';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		if (TypeUtils::isInstanceOf($this->_StartField, 'DatePickerField')) {
			print sprintf("<table id=\"%s\" cellspacing=\"0\"><tr><td style=\"padding-right:5px\" valign=\"top\">%s</td><td style=\"padding-left:5px\" valign=\"top\">%s</td></tr></table>",
				$this->id, $this->_StartField->getContent(), $this->_EndField->getContent()
			);
		} elseif (isset($this->attributes['SURROUNDTEXT'])) {
			print sprintf("<span id=\"%s\"%s%s>%s</span>",
				$this->id, $this->attributes['STYLE'], $this->attributes['TABINDEX'],
				sprintf($this->attributes['SURROUNDTEXT'], $this->_StartField->getContent(), $this->_EndField->getContent())
			);
		} else {
			print sprintf("<span id=\"%s\"%s>%s&nbsp;%s</span>",
				$this->id, $this->attributes['TABINDEX'], $this->_StartField->getContent(), $this->_EndField->getContent()
			);
		}
	}

	/**
	 * Set the "start" field as the control to be activated
	 * when the component's label is clicked
	 *
	 * @return string
	 */
	function getFocusId() {
		return $this->_StartField->getId();
	}

	/**
	 * Builds an human-readable representation of the component's value
	 *
	 * @return string
	 */
	function getDisplayValue() {
		if (is_array($this->value)) {
			$values = array_values($this->value);
			if (sizeof($values) == 2) {
				$operators = PHP2Go::getLangVal('OPERATORS');
				return sprintf("%s %s %s", $values[0], $operators['AND'], $values[1]);
			}
		}
		return NULL;
	}

	/**
	 * Get the range's start field
	 *
	 * @return FormField
	 */
	function &getStartField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_StartField, 'FormField'))
			$result =& $this->_StartField;
		return $result;
	}

	/**
	 * Get the range's end field
	 *
	 * @return FormField
	 */
	function &getEndField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_EndField, 'FormField'))
			$result =& $this->_EndField;
		return $result;
	}

	/**
	 * Overrides parent class implementation to define search
	 * datatype based on the MASK attribute read from the RangeField
	 * itself or from one of the range members
	 *
	 * @return array
	 */
	function getSearchData() {
		$searchData = parent::getSearchData();
		$mask = $this->attributes['MASK'];
		if (!$mask && TypeUtils::isInstanceOf($this->_StartField, 'EditField')) {
			$bottomMask = $this->_StartField->getMask();
			$topMask = $this->_EndField->getMask();
			if ($bottomMask == $topMask)
			 	$mask = $bottomMask;
		}
		switch ($mask) {
			case 'DATE' :
			case 'DATE-EURO' :
			case 'DATE-US' :
				if ($searchData['DATATYPE'] != 'DATETIME')
					$searchData['DATATYPE'] = 'DATE';
				break;
			case 'INTEGER' :
			case 'FLOAT' :
				$searchData['DATATYPE'] = $mask;
				break;
		}
		return $searchData;
	}

	/**
	 * Set a mask to the range members
	 *
	 * This is only applicable when the range members are
	 * instances of the {@link EditField} form component.
	 *
	 * @param string $mask Mask name
	 */
	function setMask($mask) {
		$matches = array();
		$mask = trim(strtoupper($mask));
		if (!empty($mask)) {
			if (preg_match(PHP2GO_MASK_PATTERN, $mask, $matches))
				$this->attributes['MASK'] = $mask;
			else
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_INVALID_MASK', array($mask, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Set the text that should involve the range inputs
	 *
	 * @param string $text Surround text
	 */
	function setSurroundText($text) {
		if (!empty($text))
			$this->attributes['SURROUNDTEXT'] = resolveI18nEntry($text);
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		$fieldMap = array(
			'EDITFIELD' => 'php2go.form.field.EditField',
			'COMBOFIELD' => 'php2go.form.field.ComboField',
			'LOOKUPFIELD' => 'php2go.form.field.LookupField',
			'DATEPICKERFIELD' => 'php2go.form.field.DatePickerField'
		);
		$fieldNames = array_keys($fieldMap);
		if (!empty($children)) {
			foreach ($children as $key => $value) {
				if (in_array($key, $fieldNames)) {
					if (!is_array($value) || sizeof($value) != 2) {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_RANGEFIELD_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
					} else {
						$start = @$attrs['STARTNAME'];
						$end = @$attrs['ENDNAME'];
						if (!$start || !$end || $start == $end) {
							$start = 'start';
							$end = 'end';
						}
						$this->attributes['STARTNAME'] = $start;
						$this->attributes['ENDNAME'] = $end;
						$value[0]->setAttribute('ID', "{$this->name}_{$start}");
						$value[0]->setAttribute('NAME', "{$this->name}[{$start}]");
						if (!isset($attrs['MASK']) && isset($value[0]->attrs['MASK']))
							$attrs['MASK'] = $value[0]->attrs['MASK'];
						if (!$value[0]->hasAttribute('LABEL'))
							$value[0]->setAttribute('LABEL', $this->label . ' (' . ucfirst(strtolower($start)) . ')');
						$value[1]->setAttribute('ID', "{$this->name}_{$end}");
						$value[1]->setAttribute('NAME', "{$this->name}[{$end}]");
						if (!$value[1]->hasAttribute('LABEL'))
							$value[1]->setAttribute('LABEL', $this->label . ' (' . ucfirst(strtolower($end)) . ')');
						$fieldClass = classForPath($fieldMap[$key]);
						$this->_StartField = new $fieldClass($this->_Form, TRUE);
						$this->_StartField->onLoadNode($value[0]->getAttributes(), $value[0]->getChildrenTagsArray());
						$this->_StartField->setRequired($this->required);
						$this->_Form->fields[$this->_StartField->getName()] =& $this->_StartField;
						$this->_EndField = new $fieldClass($this->_Form, TRUE);
						$this->_EndField->onLoadNode($value[1]->getAttributes(), $value[1]->getChildrenTagsArray());
						$this->_EndField->setRequired($this->required);
						$this->_Form->fields[$this->_EndField->getName()] =& $this->_EndField;
					}
				}
			}
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_RANGEFIELD_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
		// read-only mode
		$this->attributes['READONLY'] = resolveBooleanChoice(@$attrs['READONLY']);
		// range mask
		$this->setMask(@$attrs['MASK']);
		// surround text
		$this->setSurroundText(@$attrs['SURROUNDTEXT']);
		// comparison validation
		$type = resolveBooleanChoice(@$attrs['RULEEQUAL']);
		if (isset($attrs['RULEMESSAGE']))
			$attrs['RULEMESSAGE'] = resolveI18nEntry($attrs['RULEMESSAGE']);
		$mask = TypeUtils::ifNull(@$attrs['MASK'], 'STRING');
		if ($mask == 'DATE-EURO' || $mask == 'DATE-US')
			$mask = 'DATE';
		$this->_EndField->addRule(new FormRule(
			($type ? 'GOET' : 'GT'), $this->_StartField->getName(),
			NULL, $mask, @$attrs['RULEMESSAGE']
		));
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		if (is_array($this->value) && isset($this->value[$this->attributes['STARTNAME']]) && isset($this->value[$this->attributes['ENDNAME']])) {
			$this->_StartField->setValue($this->value[$this->attributes['STARTNAME']]);
			$this->_EndField->setValue($this->value[$this->attributes['ENDNAME']]);
		}
		// read-only and disabled attributes are propagated
		if ($this->disabled) {
			$this->_StartField->setDisabled();
			$this->_EndField->setDisabled();
		}
		if (TypeUtils::isInstanceOf($this->_StartField, 'EditField')) {
			$this->_StartField->setReadonly($this->attributes['READONLY']);
			$this->_EndField->setReadonly($this->attributes['READONLY']);
			if (isset($this->attributes['MASK'])) {
				$this->_StartField->setMask($this->attributes['MASK']);
				$this->_EndField->setMask($this->attributes['MASK']);
			}
		}
		if (TypeUtils::isInstanceOf($this->_StartField, 'DatePickerField')) {
			$this->_StartField->setMultiple(FALSE);
			$this->_EndField->setMultiple(FALSE);
		}
		if (!isset($this->attributes['STYLE']))
			$this->attributes['STYLE'] = $this->_Form->getLabelStyle();
		// propagate the access key to the start range member
		if ($this->accessKey)
			$this->_StartField->setAccessKey($this->accessKey);
		$this->_StartField->onPreRender();
		$this->_EndField->onPreRender();
	}
}
?>