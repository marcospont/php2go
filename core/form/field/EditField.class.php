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

import('php2go.form.field.EditableField');
import('php2go.datetime.Date');

/**
 * Builds text inputs
 *
 * @package form
 * @subpackage field
 * @uses Date
 * @uses HtmlUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class EditField extends EditableField
{
	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return EditField
	 */
	function EditField(&$Form, $child=FALSE) {
		parent::EditableField($Form, $child);
		$this->htmlType = 'TEXT';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		print sprintf("<input type=\"text\" id=\"%s\" name=\"%s\" value=\"%s\" maxlength=\"%s\" size=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s%s />%s%s",
			$this->id, $this->name, $this->value, $this->attributes['LENGTH'], $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'],
			$this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'], $this->attributes['ALIGN'], $this->attributes['STYLE'],
			$this->attributes['READONLY'], $this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'],
			$this->attributes['AUTOCOMPLETE'], $this->attributes['CALENDAR'], $this->attributes['CALCULATOR']
		);
	}

	/**
	 * Set text align
	 *
	 * @param string $align LEFT, CENTER or RIGHT
	 */
	function setAlign($align) {
		$align = strtolower($align);
		if (!empty($align))
			$this->attributes['ALIGN'] = " style=\"text-align:" . trim($align) . "\"";
		else
			$this->attributes['ALIGN'] = "";
	}

	/**
	 * Enable/disable capitalization of the component's value
	 *
	 * This transformation is executed upon submission.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setCapitalize($setting=TRUE) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['CAPITALIZE'] = "T";
		else
			$this->attributes['CAPITALIZE'] = "F";
	}


	/**
	 * Enable/disable removal of trailing whitespaces on the component's value
	 *
	 * This transformation is executed upon submission.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setAutoTrim($setting=TRUE) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['AUTOTRIM'] = "T";
		else
			$this->attributes['AUTOTRIM'] = "F";
	}

	/**
	 * Override parent class implementation to apply value transformations
	 *
	 * @uses StringUtils::capitalize()
	 * @return bool
	 */
	function isValid() {
		if ($this->attributes['CAPITALIZE'] == "T")
			$this->value = StringUtils::capitalize($this->value);
		if ($this->attributes['AUTOTRIM'] == "T")
			$this->value = trim($this->value);
		return parent::isValid();
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// calculator
		$this->attributes['CALCULATOR'] = resolveBooleanChoice(@$attrs['CALCULATOR']);
		// align
		$this->setAlign(@$attrs['ALIGN']);
		// capitalize
		$this->setCapitalize(resolveBooleanChoice(@$attrs['CAPITALIZE']));
		// autotrim
		$this->setAutoTrim(resolveBooleanChoice(@$attrs['AUTOTRIM']));
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @uses Date::parseFieldExpression()
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		// force string type
		if (is_array($this->value))
			$this->value = '';
		// date expressions
		if ($this->mask == 'DATE' && !empty($this->value)) {
			if ($expr = Date::parseFieldExpression($this->value))
				parent::setValue($expr);
		}
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		// add value transformation scripts
		if ($this->attributes['CAPITALIZE'] == 'T')
			$this->_Form->beforeValidateCode .= sprintf("\t\tfrm.elements['%s'].value = frm.elements['%s'].value.capitalize();\n", $this->name, $this->name);
		if ($this->attributes['AUTOTRIM'] == 'T')
			$this->_Form->beforeValidateCode .= sprintf("\t\tfrm.elements['%s'].value = frm.elements['%s'].value.trim();\n", $this->name, $this->name);
		$btnDisabled = ($this->attributes['READONLY'] != '' || $this->attributes['DISABLED'] != '' || $this->_Form->readonly ? " disabled=\"disabled\"" : "");
		// date picker button (for DATE mask)
		if ($this->mask == 'DATE') {
			$settings = PHP2Go::getConfigVal('DATE_FORMAT_SETTINGS');
			$this->_Form->Document->importStyle(PHP2GO_JAVASCRIPT_PATH . "vendor/jscalendar/calendar-system.css");
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/jscalendar/calendar_stripped.js");
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/jscalendar/calendar-setup_stripped.js");
			$this->_Form->Document->addScriptCode(sprintf("\tCalendar.setup( {\n\t\tinputField:\"%s\", ifFormat:\"%s\", button:\"%s\", singleClick:true, align:\"Bl\", cache:true, showOthers:true, weekNumbers:false\n\t} );",
				$this->id, $settings['calendarFormat'], $this->id . '_calendar'
			), 'Javascript', SCRIPT_END);
			$ua =& UserAgent::getInstance();
			$this->attributes['CALENDAR'] = sprintf("<button id=\"%s\" type=\"button\" %s style=\"cursor:pointer;%s;background:transparent;border:none;vertical-align:text-bottom\"%s><img src=\"%s\" border=\"0\" alt=\"\" /></button>",
					$this->id . '_calendar',
					HtmlUtils::statusBar(PHP2Go::getLangVal('CALENDAR_LINK_TITLE')),
					($ua->matchBrowser('opera') ? "padding-left:4px;padding-right:0" : "width:20px"),
					$this->attributes['TABINDEX'],
					$this->_Form->icons['calendar']
			);
		} else {
			$this->attributes['CALENDAR'] = '';
		}
		// calculator button (FLOAT, INTEGER and CURRENCY masks and CALCULATOR=T)
		if ($this->attributes['CALCULATOR'] && ($this->mask == 'INTEGER' || $this->mask == 'FLOAT' || $this->mask == 'CURRENCY' || $this->mask == '')) {
			$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'calculator.css');
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'widgets/calculator.js');
			$this->_Form->Document->addScriptCode(sprintf("\tCalculator.setup({trigger:'%s_calculator',target:'%s',align:'bottom'});", $this->id, $this->id), 'Javascript', SCRIPT_END);
			$this->attributes['CALCULATOR'] = sprintf("<button id=\"%s_calculator\" type=\"button\" %s style=\"cursor:pointer;padding-left:3px;background:transparent;border:none;vertical-align:text-bottom\"%s><img name=\"%s_calculator_img\" src=\"%s\" border=\"0\" alt=\"\" /></button>",
					$this->id, HtmlUtils::statusBar(PHP2Go::getLangVal('CALCULATOR_LINK_TITLE')),
					$this->attributes['TABINDEX'], $this->id, $this->_Form->icons['calculator']
			);
		} else {
			$this->attributes['CALCULATOR'] = '';
		}
	}
}
?>