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
import('php2go.template.Template');

/**
 * Default width for the editor's area
 */
define('EDITOR_DEFAULT_WIDTH', 500);
/**
 * Default height for the editor's area
 */
define('EDITOR_DEFAULT_HEIGHT', 200);

/**
 * Builds a WYSIWYG HTML editor
 *
 * This form component is a WYSIWYG HTML editor based on an
 * editable IFRAME. It offers several formatting tools, and submits
 * the HTML value in a hidden field.
 *
 * @package form
 * @subpackage field
 * @uses Template
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class EditorField extends FormField
{
	/**
	 * Indicates if component is in read-only mode
	 *
	 * @var bool
	 * @access private
	 */
	var $readOnly = FALSE;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return EditorField
	 */
	function EditorField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'IFRAME';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'editorfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('labelStyle', $this->_Form->getLabelStyle());
		$Tpl->assign('inputStyle', $this->attributes['STYLE']);
		$options = array();
		$options[] = "'readOnly':" . ($this->disabled || $this->attributes['READONLY'] || $this->_Form->readonly ? 'true' : 'false');
		$options[] = "'resizeMode':'{$this->attributes['RESIZEMODE']}'";
		if (isset($this->attributes['STYLESHEET']))
			$options[] = "'styleSheet':'" . $this->attributes['STYLESHEET'] . "'";
		$Tpl->assign('options', '{' . join(',', $options) . '}');
		$Tpl->assign('iconPath', PHP2GO_ICON_PATH);
		$Tpl->assign('resizeMode', $this->attributes['RESIZEMODE']);
		$Tpl->assign('width', (isset($this->attributes['WIDTH']) ? $this->attributes['WIDTH'] : EDITOR_DEFAULT_WIDTH));
		$Tpl->assign('height', (isset($this->attributes['HEIGHT']) ? $this->attributes['HEIGHT'] : EDITOR_DEFAULT_HEIGHT));
		$Tpl->assign('hiddenField', sprintf("<input type=\"hidden\" id=\"%s\" name=\"%s\" value=\"%s\" title=\"%s\"%s%s%s />",
				$this->id, $this->name, htmlspecialchars($this->value), $this->label, $this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->attributes['SCRIPT']));
		$Tpl->assign(PHP2Go::getLangVal('EDITOR_VARS'));
		$Tpl->assign('fontNames', array(
			'arial,helvetica,sans-serif' => 'Arial',
			'arial black,avant garde' => 'Arial Black',
			'book antiqua,palatino' => 'Book Antiqua',
			'comic sans ms,sand' => 'Comic Sans',
			'courier new,courier' => 'Courier New',
			'georgia,palatino' => 'Georgia',
			'helvetica' => 'Helvetica',
			'impact,chicago' => 'Impact',
			'symbol' => 'Symbol',
			'tahoma,arial,helvetica,sans-serif' => 'Tahoma',
			'terminal,monaco' => 'Terminal',
			'times new roman,times' => 'Times',
			'trebuchet ms,geneva' => 'Trebuchet',
			'verdana,geneva' => 'Verdana',
			'webdings' => 'Webdings',
			'wingdings,zapf dingbats' => 'Wingdings'
		));
		$Tpl->assign('emoticons', array(
			'smiley', 'lol', 'surprise', 'blink', 'sad', 'confused', 'disappointed',
			'cry', 'shame', 'glasses', 'angry', 'angel', 'devil', 'creekingteeth',
			'nerd', 'sarcastic', 'secret', 'party', 'thumbup', 'thumbdown', 'boy',
			'girl', 'hug', 'heart', 'brokenheart', 'kiss', 'gift', 'flower',
			'bulb', 'coffee', 'beer', 'cake', 'gift', 'camera', 'phone',
			'moon', 'star', 'email', 'clock',  'plate', 'pizza', 'ball',
			'computer', 'car', 'plane', 'umbrella', 'island', 'storm', 'money'
		));
		$Tpl->display();
	}

	/**
	 * Define the editor IFRAME as the control to be activated
	 * when the component's label is clicked
	 *
	 * @return string
	 */
	function getFocusId() {
		return "{$this->id}_iframe";
	}

	/**
	 * Enable/disable read-only mode
	 *
	 * @param bool $setting Enable/disable
	 */
	function setReadonly($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['READONLY'] = " readonly=\"readonly\"";
			$this->readOnly = TRUE;
		} else {
			$this->attributes['READONLY'] = "";
			$this->readOnly = FALSE;
		}
	}

	/**
	 * Set width of the editor area
	 *
	 * @param int $width Width, in pixels
	 */
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = $width;
	}

	/**
	 * Set height of the editor area
	 *
	 * @param int $height Height, in pixels
	 */
	function setHeight($height) {
		if (TypeUtils::isInteger($height))
			$this->attributes['HEIGHT'] = $height;
	}

	/**
	 * Set a CSS file to be used on the editor's document
	 *
	 * @param string $stylesheet URL of the CSS file
	 */
	function setStylesheet($stylesheet) {
		if (!empty($stylesheet))
			$this->attributes['STYLESHEET'] = $stylesheet;
	}

	/**
	 * Set resize mode of the editor
	 *
	 * @param string $resizable HORIZONTAL, VERTICAL, BOTH or NONE
	 */
	function setResizeMode($resizable) {
		$expr = "/^(horizontal|vertical|both|none)$/i";
		$val = trim($resizable);
		if (preg_match($expr, $val))
			$this->attributes['RESIZEMODE'] = $val;
		else
			$this->attributes['RESIZEMODE'] = 'none';
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// readonly
		$readOnly = (resolveBooleanChoice(@$attrs['READONLY']) || $this->_Form->readonly);
		if ($readOnly)
			$this->setReadonly();
		// width
		$this->setWidth(@$attrs['WIDTH']);
		// height
		$this->setHeight(@$attrs['HEIGHT']);
		// stylesheet file
		$this->setStylesheet(@$attrs['STYLESHEET']);
		// resize mode
		$this->setResizeMode(@$attrs['RESIZEMODE']);
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/editorfield.js');
		$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'colorpicker.css');
		$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'editorfield.css');
		if ($this->readOnly === NULL) {
			if ($this->_Form->readonly)
				$this->setReadonly();
			else
				$this->setReadonly(FALSE);
		}
	}
}
?>