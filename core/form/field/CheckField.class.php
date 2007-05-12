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
 * Builds a CHECKBOX input
 *
 * @package form
 * @subpackage field
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class CheckField extends FormField
{
	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return CheckField
	 */
	function CheckField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'CHECKBOX';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$captionString = '';
		if (!empty($this->attributes['CAPTION'])) {
			$caption = @$this->attributes['CAPTION'];
			if ($caption == 'empty')
				$caption = '';
			elseif (!$caption)
				$caption = $this->name;
			if ($this->accessKey && $this->_Form->accessKeyHighlight) {
				$pos = strpos(strtoupper($caption), strtoupper($this->accessKey));
				if ($pos !== FALSE)
					$caption = substr($caption, 0, $pos) . '<u>' . $caption[$pos] . '</u>' . substr($caption, $pos+1);
			}
			$captionString = sprintf("&nbsp;<label for=\"%s\" id=\"%s\"%s>%s</label>",
				$this->id, $this->id . "_label", $this->_Form->getLabelStyle(), $caption
			);
		}
		print sprintf("<input type=\"checkbox\" id=\"%s\" name=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s>%s",
			$this->id, $this->name, $this->attributes['CAPTION'], $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
			$this->attributes['STYLE'], $this->attributes['DISABLED'], $this->attributes['CHECKED'], $this->attributes['DATASRC'],
			$this->attributes['DATAFLD'], $this->attributes['SCRIPT'], $captionString);
		print sprintf("<input type=\"hidden\" id=\"%s\" name=\"%s\" value=\"%s\">",
				"V_{$this->id}", "V_{$this->name}",
				(empty($this->attributes['DISABLED']) ? (empty($this->value) ? 'F' : $this->value) : '')
		);
	}

	/**
	 * Overrides parent class implementation to read the
	 * field's label from the CAPTION attribute, when the LABEL
	 * attribute is missing
	 *
	 * @return string
	 */
	function getLabel() {
		if (empty($this->label) || $this->label == 'empty') {
			if ($this->attributes['CAPTION'] != '' && $this->attributes['CAPTION'] != 'empty')
				return $this->attributes['CAPTION'];
			return '';
		}
		return $this->label;
	}

	/**
	 * Overrides parent class implementation to build human-readable
	 * representation of the value based on the language entries
	 *
	 * In english, it would return:
	 * # checked: %field_name is checked
	 * # unchecked: %field_name is not checked
	 *
	 * @return string
	 */
	function getDisplayValue() {
		$descriptions = PHP2Go::getLangVal('CHECKBOX_DESCRIPTIONS');
		return sprintf($descriptions[$this->value], $this->attributes['CAPTION']);
	}

	/**
	 * Return search value and search settings for this component
	 *
	 * @return array
	 */
	function getSearchData() {
		$search = array_merge($this->searchDefaults, $this->search);
		if ($this->_Form->isPosted()) {
			$search['VALUE'] = $this->getValue();
			$search['DISPLAYVALUE'] = $this->getDisplayValue();
		}
		return $search;
	}

	/**
	 * Normalize checked and unchecked values
	 *
	 * Checkbox inputs in PHP2Go are submitted with the value "on", and
	 * then converted to T or F (when unchecked). When loaded from a
	 * database and published to the form, checkbox values can be T or 1
	 * (checked) and F or 0 (unchecked).
	 *
	 * @param string $value Checkbox value
	 */
	function setValue($value) {
		if (!$this->dataBind)
			$this->onDataBind();
		// translate the field's value
		$value = (string)$value;
		switch ($value) {
			case 'T' :
			case 'on' :
			case '1' :
				$value = 'T';
				break;
			case 'F' :
			case '0' :
				$value = 'F';
				break;
			default :
				$value = 'F';
				break;
		}
		// store the new value on the superglobals
		$method = '_' . HttpRequest::method();
		$$method["V_{$this->name}"] = $_REQUEST["V_{$this->name}"] = $value;
		// set CHECKED attribute
		$this->attributes['CHECKED'] = ($value == 'T' ? ' checked' : '');
		$this->value = $value;
	}

	/**
	 * Set the caption of the checkbox
	 *
	 * @param string $caption Caption
	 */
	function setCaption($caption) {
		if ($caption)
			$this->attributes['CAPTION'] = resolveI18nEntry($caption);
	}

	/**
	 * Set the checkbox as checked or unchecked
	 *
	 * @param bool $setting Checked/unchecked
	 */
	function setChecked($setting=TRUE) {
		$setting = (bool)$setting;
		if ($setting) {
			$this->attributes['CHECKED'] = ' checked';
			$this->value = 'T';
		} else {
			$this->attributes['CHECKED'] = '';
			$this->value = 'F';
		}
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// caption
		$this->setCaption(@$attrs['CAPTION']);
		// empty label
		if (!isset($attrs['LABEL']))
			$this->setLabel('empty');
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		if (empty($this->value) || $this->value == @$this->attributes['DEFAULT']) {
			// try to define the component's value from the associated hidden field
			$hiddenValue = HttpRequest::getVar('V_' . $this->name, 'all', 'ROSGPCE');
			if ($hiddenValue)
				$this->setValue($hiddenValue);
		}
	}
}
?>