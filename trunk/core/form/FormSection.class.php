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

import('php2go.auth.Authorizer');
import('php2go.util.Callback');

/**
 * Represents a form section (group of fields and buttons)
 *
 * @package form
 * @uses Authorizer
 * @uses Callback
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FormSection extends PHP2Go
{
	/**
	 * Section name
	 *
	 * @var string
	 */
	var $name;

	/**
	 * Section ID
	 *
	 * @var string
	 */
	var $id;

	/**
	 * Section attributes
	 *
	 * @var array
	 */
	var $attributes = array();

	/**
	 * Indicates if the section's visibility is conditional
	 *
	 * @var bool
	 */
	var $conditional = FALSE;

	/**
	 * Whether the section is visible
	 *
	 * @var bool
	 */
	var $visible = TRUE;

	/**
	 * Child elements
	 *
	 * @var array
	 */
	var $children = array();

	/**
	 * Child map, organized by child type
	 *
	 * @var array
	 */
	var $childMap = array();

	/**
	 * Parent form
	 *
	 * @var object Form
	 */
	var $_Form = NULL;

	/**
	 * Class constructor
	 *
	 * @param Form &$Form Parent form
	 * @return FormSection
	 */
	function FormSection(&$Form) {
		parent::PHP2Go();
		$this->_Form =& $Form;
	}

	/**
	 * Get the section's ID
	 *
	 * @return string
	 */
	function getId() {
		return $this->id;
	}

	/**
	 * Set the section's ID
	 *
	 * @param string $id New ID
	 */
	function setId($id) {
		if (!empty($id))
			$this->id = $id;
		Form::verifySectionId($this->_Form->formName, $this->id);
	}

	/**
	 * Get the section's name
	 *
	 * @return string
	 */
	function getName() {
		return $this->name;
	}

	/**
	 * Set the section's name
	 *
	 * @param string $name New name
	 */
	function setName($name) {
		if (!empty($name))
			$this->name = resolveI18nEntry($name);
	}

	/**
	 * Check if the section has conditional visibility
	 *
	 * @return bool
	 */
	function isConditional() {
		return $this->conditional;
	}

	/**
	 * Enable/disable conditional visibility on the section
	 *
	 * @param bool $setting Enable/disable
	 * @param string $function Function or method that should be used to evaluate section's visibility
	 */
	function setConditional($setting=TRUE, $function='') {
		if ((bool)$setting == TRUE) {
			$this->conditional = TRUE;
			$this->attributes['INVERT'] = FALSE;
			if (!empty($function)) {
				if ($function[0] == '!') {
					$this->attributes['INVERT'] = TRUE;
					$this->attributes['EVALFUNCTION'] = substr($function, 1);
				} else {
					$this->attributes['EVALFUNCTION'] = $function;
				}
			} else {
				$this->attributes['EVALFUNCTION'] = "{$this->id}_evaluate";
			}
			$this->visible = $this->_defineVisibility();
		} else {
			$this->conditional = FALSE;
			$this->visible = TRUE;
		}
	}

	/**
	 * Check it the section is visible
	 *
	 * @return bool
	 */
	function isVisible() {
		return $this->visible;
	}

	/**
	 * Enable/disable display of a sign on all required fields
	 *
	 * @param bool $setting Enable/disable
	 */
	function setRequiredFlag($setting=TRUE) {
		$this->attributes['REQUIRED_FLAG'] = TypeUtils::toBoolean($setting);
	}

	/**
	 * Set the text of the required field sign
	 *
	 * @param string $text Text
	 */
	function setRequiredText($text) {
		if (!empty($text))
			$this->attributes['REQUIRED_TEXT'] = $text;
	}

	/**
	 * Set the color of the required field sign
	 *
	 * @param string $color RGB color
	 */
	function setRequiredColor($color) {
		if (!empty($color))
			$this->attributes['REQUIRED_COLOR'] = $color;
	}

	/**
	 * Check if the section has any child elements
	 *
	 * @return bool
	 */
	function hasChildren() {
		return (!empty($this->children));
	}

	/**
	 * Get section's child elements
	 *
	 * @return array
	 */
	function getChildren() {
		return $this->children;
	}

	/**
	 * Get a child element by index
	 *
	 * @param int $index Child index
	 * @return FormSection|FormButton|FormField
	 */
	function &getChild($index) {
		$result = NULL;
		if (isset($this->children[$index]))
			$result =& $this->children[$index]['object'];
		return $result;
	}

	/**
	 * Get the type of a child, given its index
	 *
	 * @param int $index Child index
	 * @return string
	 */
	function getChildType($index) {
		if (isset($this->children[$index]))
			return $this->children[$index]['type'];
		else
			return NULL;
	}

	/**
	 * Get a child field by name
	 *
	 * Returns NULL when the section doesn't contain
	 * a child field named $fieldName.
	 *
	 * @param string $fieldName Field name
	 * @return FormField|NULL
	 */
	function &getField($fieldName) {
		$result = NULL;
		$index = (is_array($this->childMap['FIELD']) ? array_search($fieldName, $this->childMap['FIELD']) : FALSE);
		if ($index !== FALSE)
			$result =& $this->getChild($index);
		return $result;
	}

	/**
	 * Get a child section by name
	 *
	 * Returns NULL when the section doesn't contain
	 * a child section whose ID is $sectionId.
	 *
	 * @param string $sectionId Section ID
	 * @return FormSection|NULL
	 */
	function &getSubSection($sectionId) {
		$result = NULL;
		$index = (is_array($this->childMap['SECTION']) ? array_search($sectionId, $this->childMap['SECTION']) : FALSE);
		if ($index !== FALSE)
			$result =& $this->getChild($index);
		return $result;
	}

	/**
	 * Adds a new child element in the section
	 *
	 * @param FormSection|FormField|FormButton|array &$object New child element
	 * @return bool
	 */
	function addChild(&$object) {
		$currentIndex = sizeof($this->children);
		// subsection
		if (TypeUtils::isInstanceOf($object, 'FormSection')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'SECTION';
			$this->childMap['SECTION'][$currentIndex] = $object->getId();
		// field
		} elseif (TypeUtils::isInstanceOf($object, 'FormField')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'FIELD';
			$this->childMap['FIELD'][$currentIndex] = $object->getName();
		// button
		} elseif (TypeUtils::isInstanceOf($object, 'FormButton')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'BUTTON';
			$this->childMap['BUTTON'][$currentIndex] = $object->getName();
		// button group
		} elseif (is_array($object) && is_object($object[0]) && TypeUtils::isInstanceOf($object[0], 'FormButton')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'BUTTONGROUP';
			for ($i=0; $i<sizeof($object); $i++) {
				$this->childMap['BUTTON'][$currentIndex] = $object[$i]->getName();
			}
		// invalid type
		} else {
			return FALSE;
		}
		$this->children[] =& $newChild;
		return TRUE;
	}

	/**
	 * Processes attributes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $parentAttrs Parent node attributes
	 */
	function onLoadNode($attrs, $parentAttrs=array()) {
		// section ID
		$this->setId(TypeUtils::ifNull(@$attrs['ID'], PHP2Go::generateUniqueId(parent::getClassName())));
		// section name
		$this->setName(TypeUtils::ifNull(@$attrs['NAME'], $this->_Form->formName . ' - Section ' . sizeof($this->_Form->sections)));
		// conditional section?
		$this->setConditional(resolveBooleanChoice(@$attrs['CONDITION']), @$attrs['EVALFUNCTION']);
		// required fields sign
		$this->setRequiredFlag(TypeUtils::ifNull(
			resolveBooleanChoice(@$attrs['REQUIRED_FLAG']),
			TypeUtils::ifNull(
				resolveBooleanChoice(@$parentAttrs['REQUIRED_FLAG']),
				$this->_Form->requiredMark
			)
		));
		$this->setRequiredText(TypeUtils::ifNull(
			@$attrs['REQUIRED_TEXT'],
			TypeUtils::ifNull(
				@$parentAttrs['REQUIRED_TEXT'],
				$this->_Form->requiredText
			)
		));
		$this->setRequiredColor(TypeUtils::ifNull(
			@$attrs['REQUIRED_COLOR'],
			TypeUtils::ifNull(
				@$parentAttrs['REQUIRED_COLOR'],
				$this->_Form->requiredColor
			)
		));
	}

	/**
	 * Define the visibility of a form section by calling its
	 * associated evaluate function
	 *
	 * The visibility is defined through the following steps:
	 * # verify if the section has the EVALFUNCTION attribute
	 * # if this attribute is a valid callback, execute it
	 * # otherwise, verify if the function name is different from the default value
	 * # if it is, throw an error
	 * # if not, define section's visibility by calling Authorizer->authorizeFormSection
	 *
	 * @uses Authorizer::authorizeFormSection()
	 * @access private
	 * @return bool
	 */
	function _defineVisibility() {
		$Callback = new Callback();
		$Callback->throwErrors = FALSE;
		$Callback->setFunction($this->attributes['EVALFUNCTION']);
		if (!$Callback->isValid()) {
			if ($this->attributes['EVALFUNCTION'] != "{$this->id}_evaluate")
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_CALLBACK', $Callback->toString()), E_USER_NOTICE, __FILE__, __LINE__);
			$func = array();
			$func[0] =& Authorizer::getInstance();
			$func[1] = 'authorizeFormSection';
			$Callback->setFunction($func);
		}
		return ($this->attributes['INVERT'] ? !$Callback->invoke($this) : $Callback->invoke($this));
	}
}
?>