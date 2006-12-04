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

/**
 * Build groups of checkboxes or radio buttons loaded from a data source
 *
 * @package form
 * @subpackage field
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 * @abstract
 */
class DbGroupField extends DbField
{
	/**
	 * Option count
	 *
	 * @var int
	 * @access private
	 */
	var $optionCount = 0;

	/**
	 * Builds the component's HTML code
	 *
	 * @see DbCheckGroup::renderGroup()
	 * @see DbRadioField::renderGroup()
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$group = $this->renderGroup();
		$elements =& $group['group'];
		print $group['prepend'];
		print sprintf("\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"%s>\n  <tr>", $this->attributes['TABLEWIDTH']);
		for ($i=0,$s=sizeof($elements); $i<$s; $i++) {
			print sprintf("\n    <td style=\"width:15px;height:15px;\">%s</td>", $elements[$i]['input']);
			print sprintf("\n    <td><label for=\"%s_%s\" id=\"%s_label\"%s%s>%s</label></td>",
				$elements[$i]['id'], $i, $elements[$i]['id'], $elements[$i]['alt'], $this->_Form->getLabelStyle(), $elements[$i]['caption']
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
	 * Define the ID of the first group member as the
	 * target when the component's label is clicked
	 *
	 * @return string
	 */
	function getFocusId() {
		return "{$this->id}_0";
	}

	/**
	 * Traverse through group members in order to build
	 * the human-readable representation of the component's value
	 *
	 * @return string
	 */
	function getDisplayValue() {
		$display = NULL;
		if (isset($this->value)) {
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
			$this->attributes['TABLEWIDTH'] = " width=\"" . $tableWidth . "\"";
		else
			$this->attributes['TABLEWIDTH'] = "";
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
		// data source is mandatory
		if (empty($this->dataSource))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_DBGROUPFIELD_DATASOURCE', array($this->fieldTag, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		parent::processDbQuery(ADODB_FETCH_NUM);
		$this->optionCount = $this->_Rs->recordCount();
		if ($this->optionCount == 0)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_DBGROUPFIELD_RESULTS', array($this->fieldTag, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
	}
}
?>