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

import('php2go.util.HtmlUtils');

/**
 * Builds form buttons
 *
 * Generates the HTML code of a form button, based on the settings
 * defined in the form XML specification. Supports 5 types of buttons:
 * submit, reset, clear, button and back. Supports buttons with text
 * or images (including swap image).
 *
 * @package form
 * @uses FormEventListener
 * @uses HtmlUtils
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FormButton extends Component
{
	/**
	 * Button ID
	 *
	 * @var string
	 */
	var $id;

	/**
	 * Button name
	 *
	 * @var string
	 */
	var $name;

	/**
	 * Button value
	 *
	 * @var string
	 */
	var $value = '';

	/**
	 * Whether the button is disabled
	 *
	 * @var bool
	 */
	var $disabled = NULL;

	/**
	 * Button event listeners
	 *
	 * @var array
	 * @access protected
	 */
	var $listeners = array();

	/**
	 * Indicates the data bind phase was already executed
	 *
	 * @var bool
	 * @access private
	 */
	var $dataBind = FALSE;

	/**
	 * Parent form
	 *
	 * @var object Form
	 * @access private
	 */
	var $_Form = NULL;

	/**
	 * Class constructor
	 *
	 * @param Form &$Form Parent form
	 * @return FormButton
	 */
	function FormButton(&$Form) {
		parent::Component();
		$this->_Form =& $Form;
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Builds the button's HTML code
	 */
	function display() {
		(!$this->preRendered) && ($this->onPreRender());
		if ($this->attributes['IMG'] != '') {
			if ($this->attributes['TYPE'] == 'SUBMIT') {
				// builds an IMAGE input
				print sprintf("<input id=\"%s\" name=\"%s\" type=\"image\" value=\"%s\" src=\"%s\"%s%s%s%s%s%s />",
					$this->id, $this->name, $this->value, $this->attributes['IMG'], $this->attributes['ALTHTML'], $this->attributes['SCRIPT'],
					$this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'], $this->attributes['DISABLED'], $this->attributes['STYLE']
				);
			} else {
				$btnScript = ($this->disabled ? " onclick=\"return false;\"" : $this->attributes['SCRIPT']);
				// image dimensions
				$size = @getimagesize($this->attributes['IMG']);
				if (!empty($size)) {
					$width = $size[0];
					$height = $size[1];
				} else {
					$width = 0;
					$height = 0;
				}
				// builds an anchor containing the button image
				print sprintf("<a id=\"%s\" name=\"%s\" style=\"cursor:pointer;\" %s%s%s%s>%s</a>",
					$this->id, $this->name, HtmlUtils::statusBar($this->attributes['ALT']), $btnScript, $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
					HtmlUtils::image($this->attributes['IMG'], '', $width, $height, -1, -1, '', "{$this->name}_img", $this->attributes['SWPIMG'])
				);
			}
		} else {
			print sprintf("<input id=\"%s\" name=\"%s\" type=\"%s\" value=\"%s\"%s%s%s%s%s%s />",
				$this->id, $this->name, strtolower($this->attributes['TYPE']), $this->value, $this->attributes['ALTHTML'],
				$this->attributes['SCRIPT'], $this->attributes['STYLE'], $this->attributes['ACCESSKEY'],
				$this->attributes['TABINDEX'], $this->attributes['DISABLED']
			);
		}
	}

	/**
	 * Get the button's ID
	 *
	 * @return string
	 */
	function getId() {
		return $this->id;
	}

	/**
	 * Set the button's ID
	 *
	 * @param string $id New ID
	 */
	function setId($id) {
		if (!empty($id))
			$this->id = $id;
		else
			$this->id = PHP2Go::generateUniqueId(parent::getClassName());
	}

	/**
	 * Get the button's name
	 *
	 * @return string
	 */
	function getName() {
		return $this->name;
	}

	/**
	 * Set the button's name
	 *
	 * @param string $name New name
	 */
	function setName($name) {
		if (!empty($name))
			$this->name = $name;
		else
			$this->name = $this->id;
		Form::verifyButtonName($this->_Form->formName, $this->name);
	}

	/**
	 * Get the button's value
	 *
	 * @return string
	 */
	function getValue() {
		return $this->value;
	}

	/**
	 * Set the button's value
	 *
	 * @param string $value New value
	 */
	function setValue($value) {
		if (!empty($value))
			$this->value = resolveI18nEntry($value);
		elseif (!empty($this->name))
			$this->value = ucfirst($this->name);
	}

	/**
	 * Get the button's parent form
	 *
	 * @return Form
	 */
	function &getOwnerForm() {
		return $this->_Form;
	}

	/**
	 * Set the button's image
	 *
	 * @param string $img Image URL
	 * @param string $swpImg Swap image URL
	 */
	function setImage($img, $swpImg='') {
		$this->attributes['IMG'] = trim(strval($img));
		if ($swpImg && trim($swpImg) != '')
			$this->attributes['SWPIMG'] = $swpImg;
		else
			$this->attributes['SWPIMG'] = '';
	}

	/**
	 * Set the button's CSS class
	 *
	 * To define a single CSS class for all buttons in
	 * a form, use {@link Form::setButtonStyle()} or declare
	 * it in the XML file (//form/style[@button]). To define
	 * a global CSS configuration for all forms, use the
	 * FORMS entry of the global configuration settings.
	 *
	 * @param string $style CSS class
	 */
	function setStyle($style) {
		$style = trim($style);
		if ($style == 'empty')
			$this->attributes['STYLE'] = '';
		elseif ($style != '')
			$this->attributes['STYLE'] = " class=\"{$style}\"";
		else
			$this->attributes['STYLE'] = $this->_Form->getButtonStyle();
	}

	/**
	 * Set the button's access key
	 *
	 * @param string $accessKey Access key
	 */
	function setAccessKey($accessKey) {
		if (trim($accessKey) != '')
			$this->attributes['ACCESSKEY'] = " accesskey=\"$accessKey\"";
		else
			$this->attributes['ACCESSKEY'] = '';
	}

	/**
	 * Set the button's tab index
	 *
	 * @param int $tabIndex Tab index
	 */
	function setTabIndex($tabIndex) {
		if (TypeUtils::isInteger($tabIndex))
			$this->attributes['TABINDEX'] = " tabindex=\"$tabIndex\"";
		else
			$this->attributes['TABINDEX'] = '';
	}

	/**
	 * Set the button's alternative text
	 *
	 * @param string $altText Alt text
	 */
	function setAltText($altText) {
		if (!empty($altText)) {
			$this->attributes['ALTHTML'] = " alt=\"$altText\"";
			$this->attributes['ALT'] = trim($altText);
		} else {
			$this->attributes['ALTHTML'] = "";
			$this->attributes['ALT'] = "";
		}
	}

	/**
	 * Disables or enables the button
	 *
	 * @param bool $setting Disable/enable
	 */
	function setDisabled($setting=TRUE) {
		if ($setting) {
			$this->attributes['DISABLED'] = " disabled=\"disabled\"";
			$this->disabled = TRUE;
		} else {
			$this->attributes['DISABLED'] = "";
			$this->disabled = FALSE;
		}
	}

	/**
	 * Adds a new event listener
	 *
	 * @param FormEventListener $Listener New listener
	 * @param bool $pushStart Whether to add the listener before all existent ones
	 */
	function addEventListener($Listener, $pushStart=FALSE) {
		$Listener->setOwner($this);
		if ($Listener->isValid()) {
			if ($pushStart) {
				$first = -1;
				for ($i=0,$s=sizeof($this->listeners); $i<$s; $i++) {
					if ($this->listeners[$i]->eventName == $Listener->eventName) {
						$first = $i;
						break;
					}
				}
				if ($first == 0) {
					array_unshift($this->listeners, $Listener);
				} elseif ($first > 0) {
					for ($i=$first, $s=sizeof($this->listeners); $i<$s; $i++)
						$this->listeners[$i+1] = $this->listeners[$i];
					$this->listeners[$first] = $Listener;
				} else {
					$this->listeners[] = $Listener;
				}
			} else {
				$this->listeners[] = $Listener;
			}
		}
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children=array()) {
		// id
		$this->setId(TypeUtils::ifNull(@$attrs['ID'], @$attrs['NAME']));
		// name
		$this->setName(@$attrs['NAME']);
		// value
		$this->setValue(@$attrs['VALUE']);
		// type
		$this->attributes['TYPE'] = (isset($attrs['TYPE']) && preg_match('/submit|reset|clear|back|button/i', $attrs['TYPE']) ? strtoupper($attrs['TYPE']) : 'BUTTON');
		// image and swap image
		$this->setImage(@$attrs['IMG'], @$attrs['SWPIMG']);
		// when the button is type=SUBMIT and image-based,
		// the submitted value of the button will be pair
		// of coordinates the user clicked
		if ($this->_Form->isPosted()) {
			if ($this->attributes['TYPE'] == 'SUBMIT' && $this->attributes['IMG'] != '') {
				$x = HttpRequest::getVar($this->name . '_x', $this->_Form->formMethod);
				$y = HttpRequest::getVar($this->name . '_y', $this->_Form->formMethod);
				if (!is_null($x) && !is_null($y))
					$this->_Form->submittedValues[$this->name] = array('x' => $x, 'y' => $y);
			} else {
				$submittedValue = HttpRequest::getVar($this->name, $this->_Form->formMethod);
				if (!is_null($submittedValue))
					$this->_Form->submittedValues[$this->name] = $submittedValue;
			}
		}
		// CSS class
		$this->setStyle(@$attrs['STYLE']);
		// access key
		$this->setAccessKey(@$attrs['ACCESSKEY']);
		// tab index
		$this->setTabIndex(@$attrs['TABINDEX']);
		// alt text
		$this->setAltText(@$attrs['ALT']);
		// disabled
		$disabled = (resolveBooleanChoice(@$attrs['DISABLED']) || $this->_Form->readonly);
		if ($disabled)
			$this->setDisabled();
		// register an event listener when type=CLEAR, calling Form.clear()
		if ($this->attributes['TYPE'] == 'CLEAR') {
			$this->attributes['TYPE'] = 'BUTTON';
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onClick', sprintf("Form.clear('%s')", $this->_Form->formName)));
		}
		// register an event listener when type=RESET and images are used, calling form.reset()
		if ($this->attributes['TYPE'] == 'RESET' && $this->attributes['IMG'] != '')
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onClick', sprintf("Form.reset('%s')", $this->_Form->formName)));
		// register event listeners to handle with image swapping
		if ($this->attributes['TYPE'] == 'SUBMIT' && $this->attributes['IMG'] != '' && $this->attributes['SWPIMG'] != '') {
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onLoad', sprintf("var %s_swp=new Image();%s_swp.src='%s'", $this->name, $this->name, $this->attributes['SWPIMG'])));
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onMouseOver', sprintf("this.src='%s'", $this->attributes['SWPIMG'])));
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onMouseOut', sprintf("this.src='%s'", $this->attributes['IMG'])));
		}
		if (isset($children['LISTENER'])) {
			$listeners = TypeUtils::toArray($children['LISTENER']);
			foreach ($listeners as $listenerNode)
				$this->addEventListener(FormEventListener::fromNode($listenerNode));
		}
	}

	/**
	 * Configure button's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		for ($i=0,$s=sizeof($this->listeners); $i<$s; $i++) {
			$Listener =& $this->listeners[$i];
			$Listener->onDataBind();
		}
		$this->dataBind = TRUE;
	}

	/**
	 * Prepare the button to be rendered
	 */
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			if (!$this->dataBind)
				$this->onDataBind();
			// register an event listener when type=BACK
			if ($this->attributes['TYPE'] == 'BACK') {
				$this->attributes['TYPE'] = 'BUTTON';
				if (empty($this->_Form->backUrl))
					$action = "history.back()";
				else
					$action = sprintf("window.location.href='%s'", htmlentities($this->_Form->backUrl));
				$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onClick', $action), TRUE);
			}
			// normalized disable state
			if ($this->disabled === NULL) {
				if ($this->_Form->readonly)
					$this->setDisabled();
				else
					$this->setDisabled(FALSE);
			}
			$this->renderListeners();
		}
	}

	/**
	 * Render button's event listeners
	 *
	 * Collects all event listeners, join their calls and save
	 * them in the SCRIPT attribute.
	 *
	 * @access protected
	 */
	function renderListeners() {
		$script = '';
		$events = array();
		foreach ($this->listeners as $listener) {
			$eventName = $listener->eventName;
			if (!isset($events[$eventName]))
				$events[$eventName] = array();
			$code = $listener->getScriptCode();
			if (!empty($code))
				$events[$eventName][] = $code;
		}
		foreach ($events as $event => $action) {
			if (!empty($action)) {
				$action = implode(';', $action);
				$script .= " " . strtolower($event) . "=\"" . str_replace('\"', '\'', $action) . ";\"";
			}
		}
		$this->attributes['SCRIPT'] = $script;
	}
}
?>