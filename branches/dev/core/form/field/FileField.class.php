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
 * Builds file inputs
 *
 * @package form
 * @subpackage field
 * @uses Validator
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FileField extends FormField
{
	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return FileField
	 */
	function FileField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'FILE';
		$this->searchable = FALSE;
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && parent::onPreRender());
		print sprintf("<input type=\"file\" id=\"%s\" name=\"%s\" size=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s>",
			$this->id, $this->name, $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'],
			$this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'], $this->attributes['STYLE'],
			$this->attributes['READONLY'], $this->attributes['DISABLED'], $this->attributes['DATASRC'],
			$this->attributes['DATAFLD']
		);
	}

	/**
	 * The value produced by a FileField component is read
	 * from the $_FILES superglobal array
	 *
	 * @return array
	 */
	function getValue() {
		if (empty($_FILES) || !isset($_FILES[$this->getName()]))
			return '';
		return $_FILES[$this->getName()]['name'];
	}

	/**
	 * Set input size
	 *
	 * @param int $size Input size
	 */
	function setSize($size) {
		$this->attributes['SIZE'] = TypeUtils::parseInteger($size);
	}

	/**
	 * Set maximum size for the uploaded file
	 *
	 * Accepts integer or string values: 2000000, 200K, 4M
	 *
	 * @param int|string $maxSize Maximum file size
	 */
	function setMaxFileSize($maxSize) {
		if (!empty($maxSize))
			$this->attributes['MAXFILESIZE'] = $maxSize;
	}

	/**
	 * Set allowed mime types for the uploaded file
	 *
	 * @param string $types Comma separated list of mime types
	 */
	function setAllowedTypes($types) {
		if (!empty($types)) {
			$types = explode(',', TypeUtils::parseString($types));
			$this->attributes['ALLOWEDTYPES'] = $types;
		}
	}

	/**
	 * Set a callback function that must be used to save the uploaded file
	 *
	 * @param string $function Function name, class/method or object/method
	 */
	function setSaveFunction($function) {
		if (!empty($function))
			$this->attributes['SAVEFUNCTION'] = $function;
		else
			$this->attributes['SAVEFUNCTION'] = NULL;
	}

	/**
	 * Set save path for the uploaded file
	 *
	 * @param string $path Save path
	 */
	function setSavePath($path) {
		$this->attributes['SAVEPATH'] = $path;
	}

	/**
	 * Set save name for the uploaded file
	 *
	 * When declared in the XML specification, this attribute
	 * accepts variables in the pattern ~var~.
	 *
	 * @param string $name Save name
	 * @todo Understand strftime and other custom placeholders
	 */
	function setSaveName($name) {
		if (!empty($name))
			$this->attributes['SAVENAME'] = $name;
		else
			$this->attributes['SAVENAME'] = '';
	}

	/**
	 * Set save mode for the uploaded file
	 *
	 * @param int $mode Save mode
	 */
	function setSaveMode($mode) {
		if (!empty($mode)) {
			$mode = ereg_replace("[^0-9]+", "", TypeUtils::parseString($mode));
			eval("\$this->attributes['SAVEMODE'] = {$mode};");
		}
	}

	/**
	 * Enable/disable overwrite of existing files
	 *
	 * @param bool $overwrite Enable/disable
	 */
	function setOverwrite($overwrite) {
		$this->attributes['OVERWRITE'] = TypeUtils::toBoolean($overwrite);
	}

	/**
	 * Validates the file input
	 *
	 * If the ONVALIDATE attribute is set to TRUE, processes the
	 * uploaded file and validates its integrity using the
	 * {@link UploadValidator}.
	 *
	 * @uses Validator::validate()
	 * @return bool
	 */
	function isValid() {
		$result = parent::isValid();
		if ($this->attributes['ONVALIDATE'] === TRUE) {
			$attrs = $this->attributes;
			$attrs['FIELDNAME'] = $this->getName();
			$result &= Validator::validate('php2go.validation.UploadValidator', $attrs);
			$Uploader =& FileUpload::getInstance();
			if ($handler = $Uploader->getHandlerByName($this->getName()))
				parent::setSubmittedValue($handler);
		}
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
		// notify the parent form
		$this->_Form->hasUpload = TRUE;
		// input size
		// 1) from the SIZE attribute
		if (isset($attrs['SIZE']) && TypeUtils::isInteger($attrs['SIZE']))
			$this->setSize($attrs['SIZE']);
		// 2) from the LENGTH attribute
		elseif (isset($attrs['LENGTH']) && TypeUtils::isInteger($attrs['LENGTH']))
			$this->setSize($attrs['LENGTH']);
		// 3) default size
		else
			$this->setSize(15);
		// maximum file size
		$this->setMaxFileSize(@$attrs['MAXFILESIZE']);
		// allowed mime types
		$this->setAllowedTypes(@$attrs['ALLOWEDTYPES']);
		// save callback function
		$this->setSaveFunction(@$attrs['SAVEFUNCTION']);
		// save path
		$this->setSavePath(@$attrs['SAVEPATH']);
		// save name
		$this->setSaveName(@$attrs['SAVENAME']);
		// save mode
		$this->setSaveMode(@$attrs['SAVEMODE']);
		// files overwrite
		if (isset($attrs['OVERWRITE']))
			$this->setOverwrite(resolveBooleanChoice($attrs['OVERWRITE']));
		// whether uploaded file must be processed upon validation
		if (isset($attrs['UPLOADONVALIDATE']))
			$this->attributes['ONVALIDATE'] = resolveBooleanChoice($attrs['UPLOADONVALIDATE']);
		else
			$this->attributes['ONVALIDATE'] = TRUE;
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @uses Form::resolveVariables()
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		if (preg_match("/~[^~]+~/", $this->attributes['SAVENAME']))
			$this->attributes['SAVENAME'] = $this->_Form->resolveVariables($this->attributes['SAVENAME']);
	}
}
?>