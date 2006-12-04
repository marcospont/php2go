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
import('php2go.graph.CaptchaImage');

/**
 * Text field with CAPTCHA security image
 *
 * Displays a text input and a CAPTCHA security image. The
 * word inside the image must be entered in the text input so
 * that the field can be validated.
 *
 * @package form
 * @subpackage field
 * @uses CaptchaImage
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class CaptchaField extends FormField
{
	/**
	 * Used to generate and display the CAPTCHA image
	 *
	 * @var object CaptchaImage
	 * @access private
	 */
	var $Captcha = NULL;

	/**
	 * Image save path
	 *
	 * @var string
	 * @access private
	 */
	var $imagePath;

	/**
	 * Image type
	 *
	 * @var int
	 * @access private
	 */
	var $imageType;

	/**
	 * Whether the component is read-only
	 *
	 * @var bool
	 * @access private
	 */
	var $readOnly = NULL;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return CaptchaField
	 */
	function CaptchaField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'TEXT';
		$this->searchable = FALSE;
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && parent::onPreRender());
		print sprintf("%s&nbsp;&nbsp;<input type=\"text\" id=\"%s\" name=\"%s\" value=\"\" maxlength=\"%s\" size=\"%s\" title=\"%s\" autocomplete=\"OFF\"%s%s%s%s%s%s>",
			(isset($this->imageType) ? $this->Captcha->buildHTML($this->imagePath, $this->imageType) : $this->Captcha->buildHTML($this->imagePath)),
			$this->id, $this->name, $this->attributes['LENGTH'], $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'],
			$this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['READONLY'], $this->attributes['DISABLED']
		);
	}

	/**
	 * Set the size of the text input
	 *
	 * @param int $size Text input size
	 */
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = $size;
	}

	/**
	 * Set maxlength of the text input
	 *
	 * @param int $length Text input maxlength
	 */
	function setLength($length) {
		if (TypeUtils::isInteger($length))
			$this->attributes['LENGTH'] = $length;
	}

	/**
	 * Enable/disable read-only mode for this component
	 *
	 * @param bool $setting Enable/disable
	 */
	function setReadonly($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['READONLY'] = " readonly";
			$this->readOnly = TRUE;
		} else {
			$this->attributes['READONLY'] = "";
			$this->readOnly = FALSE;
		}
	}

	/**
	 * Validates the submitted captcha string
	 *
	 * @uses CaptchaImage::verify()
	 * @return bool
	 */
	function isValid() {
		$result = parent::isValid();
		$verify = $this->Captcha->verify($this->value);
		if (!$verify)
			Validator::addError(PHP2Go::getLangVal('ERR_FORM_CAPTCHA', $this->label));
		$result &= $verify;
		return TypeUtils::toBoolean($result);
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// initialize the CAPTCHA image
		$this->Captcha = new CaptchaImage($this->id . '_captcha');
		// input size and maxlength
		if (isset($attrs['SIZE']))
			$this->setSize($attrs['SIZE']);
		elseif (isset($attrs['LENGTH']))
			$this->setSize($attrs['LENGTH']);
		else
			$this->setSize($this->Captcha->textLength);
		// CAPTCHA string length
		if ($attrs['LENGTH']) {
			$this->setLength($attrs['LENGTH']);
			$this->Captcha->setTextLength($attrs['LENGTH']);
		} else {
			$this->setLength($this->attributes['SIZE']);
		}
		// read-only
		$readOnly = (resolveBooleanChoice(@$attrs['READONLY']) || $this->_Form->readonly);
		if ($readOnly)
			$this->setReadonly();
		// image dimensions
		if ($attrs['WIDTH'])
			$this->Captcha->setWidth($attrs['WIDTH']);
		if ($attrs['HEIGHT'])
			$this->Captcha->setHeight($attrs['HEIGHT']);
		// noise level
		if ($attrs['NOISELEVEL'])
			$this->Captcha->setNoiseLevel($attrs['NOISELEVEL']);
		// font properties
		if ($attrs['FONTSIZE'])
			$this->Captcha->setFontSize($attrs['FONTSIZE']);
		if ($attrs['FONTSHADOW'])
			$this->Captcha->setFontShadow($attrs['FONTSHADOW']);
		if ($attrs['FONTANGLE'])
			$this->Captcha->setFontAngle($attrs['FONTANGLE']);
		// image save path
		if ($attrs['IMAGEPATH'])
			$this->imagePath = $attrs['IMAGEPATH'];
		// image type
		$type = @constant(@$attrs['IMAGETYPE']);
		if (!TypeUtils::isNull($type, TRUE))
			$this->imageType = $type;
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		// revalida a propriedade "readonly"
		if ($this->readOnly === NULL) {
			if ($this->_Form->readonly)
				$this->setReadonly();
			else
				$this->setReadonly(FALSE);
		}
	}
}
?>