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
 * Builds groups of checkboxes or radio buttons with options declared in the XML file
 *
 * @package form
 * @subpackage field
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class GroupField extends FormField
{
	/**
	 * Option count
	 *
	 * @var int
	 * @access private
	 */
	var $optionCount = 0;

	/**
	 * Group options (members and their attributes)
	 *
	 * @var array
	 * @access private
	 */
	var $optionAttributes = array();

	/**
	 * Group event listeners
	 *
	 * @var array
	 * @access private
	 */
	var $optionListeners = array();

	/**
	 * Builds the component's HTML code
	 *
	 * @see CheckGroup::renderGroup()
	 * @see RadioField::renderGroup()
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$group = $this->renderGroup();
		$elements =& $group['group'];
		print $group['prepend'];
		print sprintf("\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"%s>\n  <tr>", $this->attributes['TABLEWIDTH']);
		for ($i=0,$s=sizeof($elements); $i<$s; $i++) {
			print sprintf("\n    <td style=\"width:15px;height:15px;\">%s</td>", $elements[$i]['input']);
			print sprintf("\n    <td><label for=\"%s_%s\" id=\"%s_%s_label\"%s%s>%s</label></td>",
				$elements[$i]['id'], $i, $elements[$i]['id'], $i, $elements[$i]['alt'], $this->_Form->getLabelStyle(), $elements[$i]['caption']
			);
			if ((($i+1) % $this->attributes['COLS']) == 0 && $i<($s-1))
				print "\n  </tr><tr>";
		}
		$diff = ($i % $this->attributes['COLS']);
		if ($diff && $this->attributes['COLS'] > 1) {
			for ($i=$diff; $i<$this->attributes['COLS']; $i++)
				print "\n    <td colspan=\"2\"></td>";
		}
		print "\n  </tr>\n</table>";
		print $group['append'];
	}

	/**
	 * Must be implemented by child classes
	 *
	 * @return array
	 * @abstract
	 */
	function renderGroup() {
		return array();
	}

	/**
	 * Define the first group member as the control that should be
	 * activated when the component's label is clicked
	 *
	 * @return string
	 */
	function getFocusId() {
		return "{$this->id}_0";
	}

	/**
	 * Traverse group members in order to build a human-readable
	 * representation of the component's value
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
	 * Get group options
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
	 * Adds a new option in the group
	 *
	 * @param string $value Option value
	 * @param string $caption Option caption
	 * @param string $alt Option alt text
	 * @param bool $disabled Whether the option should be disabled
	 * @param string $accessKey Option access key
	 * @param int $index Index where the option should be inserted
	 * @return bool
	 */
	function addOption($value, $caption, $alt='', $disabled=FALSE, $accessKey=NULL, $index=NULL) {
		if ($index <= $this->optionCount && $index >= 0) {
			$newOption = array();
			if (trim($value) == '') {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_OPTION_VALUE', array($index, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			// attributes of the new option
			$newOption['VALUE'] = $value;
			$newOption['CAPTION'] = (empty($caption) ? $newOption['VALUE'] : $caption);
			$newOption['ALT'] = $alt;
			$newOption['ACCESSKEY'] = $accessKey;
			if ($disabled || $this->_Form->readonly)
				$newOption['DISABLED'] = " disabled=\"disabled\"";
			else
				$newOption['DISABLED'] = (isset($this->attributes['DISABLED']) ? $this->attributes['DISABLED'] : '');
			// insert at last position
			if (!TypeUtils::isInteger($index) || $index == $this->optionCount) {
				$this->optionAttributes[$this->optionCount] = $newOption;
				$this->optionListeners[$this->optionCount] = array();
			// insert at a given position
			} else {
				for ($i=$this->optionCount; $i>$index; $i--) {
					$this->optionAttributes[$i] = $this->optionAttributes[$i-1];
					$this->optionListeners[$i] = $this->optionListeners[$i-1];
				}
				$this->optionAttributes[$index] = $newOption;
				$this->optionListeners[$index] = array();
			}
			$this->optionCount++;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Remove a given option from the group
	 *
	 * @param int $index Option index
	 * @return bool
	 */
	function removeOption($index) {
		// invalid index
		if ($this->optionCount == 1 || !TypeUtils::isInteger($index) || $index < 0 || $index >= $this->optionCount)
			return FALSE;
		// reallocate other options
		for ($i=$index; $i<($this->optionCount-1); $i++) {
			$this->optionAttributes[$i] = $this->optionAttributes[$i+1];
			$this->optionListeners[$i] = $this->optionListeners[$i+1];
		}
		unset($this->optionAttributes[$this->optionCount-1]);
		unset($this->optionListeners[$this->optionCount-1]);
		$this->optionCount--;
		return TRUE;
	}

	/**
	 * Set the number of inputs per line
	 *
	 * @param int $cols Inputs per line
	 */
	function setCols($cols) {
		$this->attributes['COLS'] = max(1, $cols);
	}

	/**
	 * Set component's table width in pixels
	 *
	 * @param int $tableWidth Width in pixels
	 */
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " width=\"{$tableWidth}\"";
		else
			$this->attributes['TABLEWIDTH'] = "";
	}

	/**
	 * Enable/disable a given group option or all options
	 *
	 * @param bool $setting Enable/disable
	 * @param int $index Option index
	 * @return bool
	 */
	function setDisabled($setting=TRUE, $index=NULL) {
		if ($index === NULL) {
			parent::setDisabled($setting);
			return TRUE;
		} else {
			// invalid index
			if (!TypeUtils::isInteger($index) || $index < 0 || $index >= $this->optionCount)
				return FALSE;
			$this->optionAttributes[$index]['DISABLED'] = ($setting ? " disabled=\"disabled\"" : '');
			return TRUE;
		}
	}

	/**
	 * Adds a new event listener
	 *
	 * When $index is missing, the listener is bound
	 * with all group options.
	 *
	 * @param FormEventListener $Listener Event listener
	 * @param int $index Associate the listener with a given option only
	 */
	function addEventListener($Listener, $index=NULL) {
		if ($index === NULL) {
			parent::addEventListener($Listener);
		} elseif ($index < $this->optionCount && $index >= 0) {
			$Listener->setOwner($this, $index);
			if ($Listener->isValid())
				$this->optionListeners[$index][] =& $Listener;
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
		// inputs per line
		$this->setCols(@$attrs['COLS']);
		// table width
		$this->setTableWidth(@$attrs['TABLEWIDTH']);
		// options
		if (isset($children['OPTION'])) {
			$options = TypeUtils::toArray($children['OPTION']);
			for ($i=0,$s=sizeof($options); $i<$s; $i++) {
				$this->addOption($options[$i]->getAttribute('VALUE'), $options[$i]->getAttribute('CAPTION'), $options[$i]->getAttribute('ALT'), ($options[$i]->getAttribute('DISABLED') == 'T'), $options[$i]->getAttribute('ACCESSKEY'));
				// individual listeners per option
				$optChildren = $options[$i]->getChildrenTagsArray();
				if (isset($optChildren['LISTENER'])) {
					$listener = TypeUtils::toArray($optChildren['LISTENER']);
					foreach ($listener as $listenerNode)
						$this->addEventListener(FormEventListener::fromNode($listenerNode), $i);
				}
			}
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_GROUPFIELD_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		if ($this->_Form->readonly) {
			for ($i=0; $i<$this->optionCount; $i++)
				$this->optionAttributes[$i]['DISABLED'] = " disabled=\"disabled\"";
		}
	}

	/**
	 * Override parent class implementation to render global listeners
	 * (associated with all group options) and individual listeners
	 *
	 * @access protected
	 */
	function renderListeners() {
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {
			$script = '';
			$optionEvents = array();
			foreach ($this->listeners as $globalListener) {
				$eventName = $globalListener->eventName;
				if (!isset($optionEvents[$eventName]))
					$optionEvents[$eventName] = array();
				$optionEvents[$eventName][] = $globalListener->getScriptCode($i);
			}
			// individual listeners
			reset($this->optionListeners[$i]);
			foreach ($this->optionListeners[$i] as $optionListener) {
				$eventName = $optionListener->eventName;
				if (!isset($optionEvents[$eventName]))
					$optionEvents[$eventName] = array();
				$optionEvents[$eventName][] = $optionListener->getScriptCode();
			}
			foreach ($optionEvents as $name => $actions)
				$this->optionAttributes[$i]['SCRIPT'] .= " " . strtolower($name) . "=\"" . str_replace('\"', '\'', implode(';', $actions)) . ";\"";
		}
	}
}
?>