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

import('php2go.form.field.FormField');

/**
 * Select input whose options are defined statically in the XML specification
 *
 * @package form
 * @subpackage field
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ComboField extends FormField
{
	/**
	 * Option count
	 *
	 * @var int
	 * @access private
	 */
	var $optionCount = 0;

	/**
	 * Select options
	 *
	 * @var array
	 * @access private
	 */
	var $optionAttributes = array();

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return ComboField
	 */
	function ComboField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'SELECT';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && parent::onPreRender());
		$name = ($this->attributes['MULTIPLE'] && substr($this->name, -2) != '[]' ? $this->name . '[]' : $this->name);
		print sprintf("\n<select id=\"%s\" name=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s%s>\n",
				$this->id, $name, $this->label, $this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
				$this->attributes['STYLE'], ($this->attributes['MULTIPLE'] ? ' multiple' : ''), $this->attributes['SIZE'],
				$this->attributes['WIDTH'], $this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD']
		);
		if (!$this->attributes['NOFIRST'])
			print sprintf("\t<option value=\"\">%s</option>\n", $this->attributes['FIRST']);
		$hasValue = ((is_array($this->value) && !empty($this->value)) || $this->value != '');
		$arrayValue = (is_array($this->value));
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {
			$key = $this->optionAttributes[$i]['VALUE'];
			if ($hasValue) {
				if ($arrayValue)
					$optionSelected = in_array($key, $this->value) ? ' selected' : '';
				else
					$optionSelected = !strcasecmp($key, $this->value) ? ' selected' : '';
			} else {
				$optionSelected = '';
			}
			print sprintf("\t<option value=\"%s\"%s%s>%s</option>\n",
					$key, (!empty($this->optionAttributes[$i]['ALT']) ? " title=\"{$this->optionAttributes[$i]['ALT']}\"" : ''),
					$optionSelected, $this->optionAttributes[$i]['CAPTION']
			);
		}
		print "</select>";
	}

	/**
	 * Enable multiple choice when field's name ends with "[]"
	 *
	 * @param string $newName New name
	 */
	function setName($newName) {
		if (preg_match("/\[\]$/", $newName))
			$this->setMultiple();
		parent::setName($newName);
	}

	/**
	 * Traverse through the select options in order to
	 * build the human-readable representation of the
	 * field's value
	 *
	 * @return string
	 */
	function getDisplayValue() {
		$display = NULL;
		$value = $this->value;
		$arrayValue = is_array($value);
		foreach ($this->optionAttributes as $index => $data) {
			if (!$arrayValue && $data['VALUE'] == $value) {
				$display = $data['CAPTION'];
				break;
			}
			if ($arrayValue && in_array($data['VALUE'], $value))
				$display[] = $data['CAPTION'];
		}
		return (is_array($display) ? '(' . implode(', ', $display) . ')' : $display);
	}

	/**
	 * Set a caption to the first (unselectable) option
	 *
	 * @param string $first Caption
	 */
	function setFirstOption($first) {
		$this->attributes['FIRST'] = ($first ? resolveI18nEntry($first) : '');
	}

	/**
	 * Enable/disable the first (unselectable) option
	 *
	 * The first and unselectable option is enabled by default.
	 *
	 * @param bool $setting Enable/disable
	 */
	function disableFirstOption($setting=TRUE) {
		$this->attributes['NOFIRST'] = (bool)$setting;
		if ($this->attributes['NOFIRST'])
			$this->attributes['FIRST'] = '';
	}

	/**
	 * Enable/disable multiple selection
	 *
	 * @param bool $setting Enable/disable
	 */
	function setMultiple($setting=TRUE) {
		$this->attributes['MULTIPLE'] = (bool)$setting;
		$this->searchDefaults['OPERATOR'] = ($this->attributes['MULTIPLE'] ? 'IN' : 'EQ');
	}

	/**
	 * Set number of visible options, when multiple selection is enabled
	 *
	 * @param int $size Visible options
	 */
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = " size=\"{$size}\"";
		else
			$this->attributes['SIZE'] = '';
	}

	/**
	 * Set width in pixels for the select input
	 *
	 * @param int $width Width
	 */
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = " style=\"width:{$width}px\"";
		else
			$this->attributes['WIDTH'] = "";
	}

	/**
	 * Get select options
	 *
	 * @return array
	 */
	function getOptions() {
		return $this->optionAttributes;
	}

	/**
	 * Get option count
	 *
	 * @return int
	 */
	function getOptionCount() {
		return $this->optionCount;
	}

	/**
	 * Adds a new select option
	 *
	 * @param string $value Option value
	 * @param string $caption Option caption
	 * @param string $alt Option alt text
	 * @param int $index Index where option should be added (zero-based)
	 * @return bool
	 */
	function addOption($value, $caption, $alt="", $index=NULL) {
		$currentCount = $this->getOptionCount();
		if ($index > $currentCount || $index < 0) {
			return FALSE;
		} else {
			$newOption = array();
			$value = trim(strval($value));
			if ($value == '')
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_COMBOOPTION_VALUE', array(($currentCount-1), $this->name)), E_USER_ERROR, __FILE__, __LINE__);
			else
				$newOption['VALUE'] = $value;
			if (!$caption || trim($caption) == '')
				$newOption['CAPTION'] = $newOption['VALUE'];
			else
				$newOption['CAPTION'] = trim($caption);
			$newOption['ALT'] = trim($alt);
			if ($index == $currentCount || !TypeUtils::isInteger($index)) {
				$this->optionAttributes[$currentCount] = $newOption;
			} else {
				for ($i=$currentCount; $i>$index; $i--) {
					$this->optionAttributes[$i] = $this->optionAttributes[$i-1];
				}
				$this->optionAttributes[$index] = $newOption;
			}
			$this->optionCount++;
			return TRUE;
		}
	}

	/**
	 * Remove a given option
	 *
	 * @param int $index Index to remove
	 * @return bool
	 */
	function removeOption($index) {
		$currentCount = $this->getOptionCount();
		if ($currentCount == 1 || !TypeUtils::isInteger($index) || $index >= $currentCount || $index < 0) {
			return FALSE;
		} else {
			for ($i=$index; $i<($currentCount-1); $i++) {
				$this->optionAttributes[$i] = $this->optionAttributes[$i+1];
			}
			unset($this->optionAttributes[$currentCount-1]);
			$this->optionCount--;
			return TRUE;
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
		// multiple choice
		$this->setMultiple(($this->attributes['MULTIPLE'] || resolveBooleanChoice(@$attrs['MULTIPLE'])));
		// visible options
		$size = @$attrs['SIZE'];
		(!$size && $this->attributes['MULTIPLE']) && ($size = 2);
		($size) && ($attrs['NOFIRST'] = 'T');
		$this->setSize($size);
		// first option's caption
		$this->setFirstOption(@$attrs['FIRST']);
		// first option enabled or disabled
		$this->disableFirstOption(resolveBooleanChoice(@$attrs['NOFIRST']));
		// width
		$this->setWidth(@$attrs['WIDTH']);
		// select options
		if (isset($children['OPTION'])) {
			$options = TypeUtils::toArray($children['OPTION']);
			for ($i=0, $s=sizeof($options); $i<$s; $i++)
				$this->addOption($options[$i]->getAttribute('VALUE'), $options[$i]->getAttribute('CAPTION'), $options[$i]->getAttribute('ALT'));
		}
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		if ($this->attributes['MULTIPLE'] && substr($this->validationName, -2) != '[]')
			$this->validationName .= '[]';
		parent::onPreRender();
		if ($this->attributes['MULTIPLE'] && !$this->attributes['SIZE'])
			$this->attributes['SIZE'] = " size=\"2\"";
	}
}
?>