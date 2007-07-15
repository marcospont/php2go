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
 * Color selection tool
 *
 * Builds a color selection tool based on a 228 colors palette. Supports
 * flat mode (renders the palette together with a text input) and popup
 * mode (renders text input only, and palette opens by clicking on an
 * image button).
 *
 * @package form
 * @subpackage field
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ColorPickerField extends FormField
{
	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return ColorPickerField
	 */
	function ColorPickerField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		if ($this->attributes['MODE'] == 'flat') {
			$options = "{mode:\"flat\",container:\"{$this->id}_container\"}";
			print sprintf(
				"<input id=\"%s\" name=\"%s\" type=\"hidden\" value=\"%s\" title=\"%s\"%s%s%s%s />" .
				"<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td id=\"%s_container\"></td></tr></table>" .
				"<script type=\"text/javascript\">new ColorPickerField(\"%s\", %s);</script>",
				$this->id, $this->name, $this->value, $this->label, $this->attributes['SCRIPT'],
				$this->attributes['TABINDEX'], $this->attributes['ACCESSKEY'], $this->attributes['DISABLED'],
				$this->id, $this->id, $options
			);
		} else {
			$ua =& UserAgent::getInstance();
			$options = "{mode:\"popup\",trigger:\"{$this->id}_button\"}";
			print sprintf(
				"<input id=\"%s\" name=\"%s\" type=\"text\" value=\"%s\" size=\"8\" maxlength=\"7\" title=\"%s\"%s%s%s%s%s%s%s />" .
				"<button id=\"%s_button\" type=\"button\" %s style=\"cursor:pointer;%s;background:transparent;border:none;vertical-align:text-bottom\"><img src=\"%s\" border=\"0\" alt=\"\" /></button>" .
				"<script type=\"text/javascript\">new ColorPickerField(\"%s\", %s);</script>",
				$this->id, $this->name, $this->value, $this->label, $this->attributes['SCRIPT'], $this->attributes['TABINDEX'], $this->attributes['ACCESSKEY'],
				$this->attributes['DISABLED'], $this->attributes['STYLE'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->id,
				$this->attributes['TABINDEX'], ($ua->matchBrowser('opera') ? "padding-left:4px;padding-right:0" : "width:20px"),
				PHP2GO_ICON_PATH . 'colorpicker.gif', $this->id, $options
			);
		}
	}

	/**
	 * Set color selection mode
	 *
	 * @param string $mode FLAT or POPUP
	 */
	function setMode($mode) {
		$mode = strtolower(strval($mode));
		$this->attributes['MODE'] = ($mode == 'popup' ? 'popup' : 'flat');
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// mode
		$this->setMode(@$attrs['MODE']);
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->importStyle(PHP2GO_CSS_PATH . "colorpicker.css");
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "form/colorpickerfield.js");
	}
}
?>