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

import('php2go.form.field.EditableField');

/**
 * Default value to the COLS attribute
 */
define('MEMOFIELD_DEFAULT_COLS', 40);
/**
 * Default value to the ROWS attribute
 */
define('MEMOFIELD_DEFAULT_ROWS', 5);

/**
 * Builds textarea inputs
 *
 * @package form
 * @subpackage field
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class MemoField extends EditableField
{
	/**
	 * Whether a char counter must be displayed below the textarea
	 *
	 * @var bool
	 * @access private
	 */
	var $charCountControl = FALSE;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return MemoField
	 */
	function MemoField(&$Form, $child=FALSE) {
		parent::EditableField($Form, $child);
		$this->htmlType = 'TEXTAREA';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		if (isset($this->maxLength) && $this->charCountControl) {
			print sprintf("
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
  <tr><td><textarea id=\"%s\" name=\"%s\" cols=\"%s\" rows=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s>%s</textarea></td></tr>
  <tr><td align=\"right\"><span%s>%s</span>&nbsp;<input type=\"text\" id=\"%s_count\" name=\"%s_count\" size=\"5\" value=\"%s\" disabled%s></td></tr>
</table><script type=\"text/javascript\">new MemoField('%s', %s);</script>",
				$this->id, $this->name, $this->attributes['COLS'], $this->attributes['ROWS'], $this->label,
				$this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'],  $this->attributes['TABINDEX'],
				$this->attributes['STYLE'], $this->attributes['INLINESTYLE'], $this->attributes['READONLY'],
				$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'],
				$this->value, $this->_Form->getLabelStyle(), PHP2Go::getLangVal('MEMO_COUNT_LABEL'), $this->id,
				$this->name,  (max(0, $this->maxLength-strlen($this->value))), $this->attributes['STYLE'],
				$this->id, $this->maxLength
			);
		} else {
			print sprintf("<textarea id=\"%s\" name=\"%s\" cols=\"%s\" rows=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s>%s</textarea>",
				$this->id, $this->name, $this->attributes['COLS'], $this->attributes['ROWS'], $this->label,
				$this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
				$this->attributes['STYLE'], $this->attributes['INLINESTYLE'], $this->attributes['READONLY'],
				$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'],
				$this->value
			);
		}
	}

	/**
	 * Set the number of columns of the textarea
	 *
	 * @param int $cols Number of columns
	 */
	function setCols($cols) {
		$this->attributes['COLS'] = $cols;
	}

	/**
	 * Set the number of rows of the textarea
	 *
	 * @param int $rows Number of rows
	 */
	function setRows($rows) {
		$this->attributes['ROWS'] = $rows;
	}

	/**
	 * Set the textarea's width in pixels
	 *
	 * @param int $width Width
	 */
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = $width;
	}

	/**
	 * Set the textarea's height in pixels
	 *
	 * @param int $height Height
	 */
	function setHeight($height) {
		if (TypeUtils::isInteger($height))
			$this->attributes['HEIGHT'] = $height;
	}

	/**
	 * Enables or disables rendering a char count below the textarea input
	 *
	 * The method also allows to define the maximum
	 * length of the component's value.
	 *
	 * @param bool $setting Enable/disable
	 * @param int $maxLength Maxlength
	 */
	function charCount($setting, $maxLength=NULL) {
		$this->charCountControl = TypeUtils::toBoolean($setting);
		if (TypeUtils::isInteger($maxLength))
			parent::setMaxLength($maxLength);
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// columns
		if (isset($attrs['COLS']) && TypeUtils::isInteger($attrs['COLS']))
			$this->setCols($attrs['COLS']);
		else
			$this->setCols(MEMOFIELD_DEFAULT_COLS);
		// rows
		if (isset($attrs['ROWS']) && TypeUtils::isInteger($attrs['ROWS']))
			$this->setRows($attrs['ROWS']);
		else
			$this->setRows(MEMOFIELD_DEFAULT_ROWS);
		// width in pixels
		$this->setWidth(@$attrs['WIDTH']);
		// height in pixels
		$this->setHeight(@$attrs['HEIGHT']);
		// char counter
		$this->charCount(resolveBooleanChoice(@$attrs['CHARCOUNT']));
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		if (is_array($this->value))
			$this->value = '';
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		if (isset($this->maxLength) && $this->charCountControl)
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/memofield.js');
		$inlineStyle = array();
		if (isset($this->attributes['WIDTH']))
			$inlineStyle[] = "width:{$this->attributes['WIDTH']}px";
		if (isset($this->attributes['HEIGHT']))
			$inlineStyle[] = "height:{$this->attributes['HEIGHT']}px";
		$this->attributes['INLINESTYLE'] = (empty($inlineStyle) ? '' : " style=\"" . join(';', $inlineStyle) . "\"");
	}
}
?>