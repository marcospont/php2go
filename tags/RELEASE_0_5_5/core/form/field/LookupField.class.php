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

import('php2go.form.field.DbField');

/**
 * Select input whose options are loaded from a data source
 *
 * @package form
 * @subpackage field
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class LookupField extends DbField
{
	/**
	 * Option count
	 *
	 * @var int
	 * @access private
	 */
	var $optionCount = 0;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return LookupField
	 */
	function LookupField(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->htmlType = 'SELECT';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$name = ($this->attributes['MULTIPLE'] && substr($this->name, -2) != '[]' ? $this->name . '[]' : $this->name);
		print sprintf("\n<select id=\"%s\" name=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s%s>\n",
			$this->id, $name, $this->label, $this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'],
			$this->attributes['TABINDEX'], $this->attributes['STYLE'], ($this->attributes['MULTIPLE'] ? ' multiple' : ''),
			$this->attributes['SIZE'], $this->attributes['WIDTH'], $this->attributes['DISABLED'],
			$this->attributes['DATASRC'], $this->attributes['DATAFLD']
		);
		if (!$this->attributes['NOFIRST'])
			print sprintf("<option value=\"\">%s</option>\n", $this->attributes['FIRST']);
		if ($this->_Rs->recordCount() > 0) {
			$this->optionCount = $this->_Rs->recordCount();
			$hasValue = ((is_array($this->value) && !empty($this->value)) || $this->value != '');
			$arrayValue = (is_array($this->value));
			if ($this->isGrouping) {
				$groupVal = '';
				while (list($key, $display, $group, $groupDisplay) = $this->_Rs->fetchRow()) {
					if (strcasecmp($group, $groupVal)) {
						if ($groupVal != '')
							print "</optgroup>\n";
						print sprintf("<optgroup label=\"%s\">\n", $groupDisplay);
					}
					if ($hasValue) {
						if ($arrayValue)
							$optionSelected = (in_array($key, $this->value) ? ' selected' : '');
						else
							$optionSelected = (!strcasecmp($key, $this->value) ? ' selected' : '');
					} else {
						$optionSelected = '';
					}
					print sprintf("<option value=\"%s\"%s>%s</option>\n", $key, $optionSelected, $display);
					$groupVal = $group;
				}
				print "</optgroup>\n";
			} else {
				while (list($key, $display) = $this->_Rs->fetchRow()) {
					if ($hasValue) {
						if ($arrayValue)
							$optionSelected = (in_array($key, $this->value) ? ' selected' : '');
						else
							$optionSelected = (!strcasecmp($key, $this->value) ? ' selected' : '');
					} else {
						$optionSelected = '';
					}
					print sprintf("<option value=\"%s\"%s>%s</option>\n", $key, $optionSelected, $display);
				}
			}
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
		if (isset($this->value) && !TypeUtils::isInstanceOf($this->_Rs, 'ADORecordSet_empty')) {
			$arrayValue = is_array($this->value);
			while (list($value, $caption) = $this->_Rs->fetchRow()) {
				if ($arrayValue) {
					if (in_array($value, $this->value))
						$display[] = $caption;
				} else {
					if ($value == $this->value) {
						$display = $caption;
						break;
					}
				}
			}
			$this->_Rs->moveFirst();
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
	 * Get option count
	 *
	 * @return int
	 */
	function getOptionCount() {
		return $this->optionCount;
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
		$this->setMultiple($this->attributes['MULTIPLE'] || resolveBooleanChoice(@$attrs['MULTIPLE']));
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
	}

	/**
	 * Configures component's dynamic properties
	 */
	function onDataBind() {
		parent::onDataBind();
		if (!isset($this->_Rs))
			parent::processDbQuery(ADODB_FETCH_NUM);
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