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

import('php2go.util.json.JSONEncoder');

/**
 * Interface control that can be included in page templates
 *
 * A widget is an interface control that can be declared inside a
 * template. There are 2 types of widgets: include widgets, which
 * are just included somewhere in the template, and container widgets,
 * which accept internal content (even other widgets), and must be
 * declared with a "start widget" and an "end widget" declarations.
 *
 * This class is the base of all template widgets available in PHP2Go.
 * If the developer wants to create its own widgets, these widgets must
 * extend this class in order to be valid.
 *
 * @package gui
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Widget extends Component
{
	/**
	 * Body content (container widgets only)
	 *
	 * @var string
	 */
	var $content;

	/**
	 * Event listeners
	 *
	 * @var array
	 */
	var $listeners = array();

	/**
	 * Whether this is a container widget
	 *
	 * @var bool
	 */
	var $isContainer = FALSE;

	/**
	 * Whether this widget produces output or not
	 *
	 * @var bool
	 */
	var $hasOutput = TRUE;

	/**
	 * Set of mandatory attributes
	 *
	 * @var array
	 */
	var $mandatoryAttributes = array();

	/**
	 * Parent widget, when applicable
	 *
	 * @var object Widget
	 */
	var $Parent = NULL;

	/**
	 * Class constructor
	 *
	 * @param array $attrs Attributes
	 * @return Widget
	 */
	function Widget($attrs=array()) {
		parent::PHP2Go();
		$attrs = array_merge($this->getDefaultAttributes(), (array)$attrs);
		$this->loadAttributes($attrs);
	}

	/**
	 * Builds a new widget, given its path and attributes
	 *
	 * @param string $path Widget's dot path
	 * @param array $attrs Widget's attributes
	 * @return Widget
	 * @static
	 */
	function factory($path, $attrs=array()) {
		$widgetClass = classForPath($path);
		$widget = new $widgetClass($attrs);
		return $widget;
	}

	/**
	 * Loads resources needed by a given widget
	 *
	 * This method will check if the widget class contains
	 * a method called "loadResources". If yes, the singleton
	 * of the DocumentHead class is passed to this method, so
	 * that the widget is able to register all scripts, stylesheets
	 * and other resources it needs to run.
	 *
	 * @param string $path Widget's path
	 * @static
	 */
	function preload($path) {
		$widgetClass = classForPath($path);
		$methodName = (IS_PHP5 ? 'loadResources' : 'loadresources');
		if (in_array($methodName, get_class_methods($widgetClass))) {
			$params = array();
			$params[] =& DocumentHead::getInstance();
			call_user_func_array(array($widgetClass, $methodName), $params);
		}
	}

	/**
	 * Return the set of the default values for attributes
	 *
	 * @return array
	 */
	function getDefaultAttributes() {
		return array();
	}

	/**
	 * Load widget attributes
	 *
	 * This method can be overriden in the child classes in order
	 * to transform property values or merge provided attributes
	 * with their default values.
	 *
	 * @access protected
	 * @param array $attrs Attributes
	 */
	function loadAttributes($attrs) {
		$this->attributes = $attrs;
	}

	/**
	 * Adds a Javascript event listener
	 *
	 * @param string $event Event
	 * @param string $code Listener code
	 */
	function addEventListener($event, $code) {
		$event = preg_replace('/^on/i', '', strtolower($event));
		$matches = array();
		$funcBody = rtrim(ltrim($code, "\r\n"));
		if (preg_match("/^([\t]+)/", $funcBody, $matches))
			$funcBody = preg_replace("/^\t{" . strlen($matches[1]) . "}/m", "\t\t", $funcBody);
		if (!isset($this->listeners[$event]))
			$this->listeners[$event] = array();
		$this->listeners[$event][] = $funcBody;
	}

	/**
	 * Set widget's internal content
	 *
	 * @param string $content Content
	 */
	function setContent($content) {
		$this->content = $content;
	}

	/**
	 * Set parent widget
	 *
	 * @param Widget $parent
	 */
	function setParent(&$parent) {
		$this->Parent =& $parent;
	}

	/**
	 * Validates widget's mandatory attributes
	 */
	function validate() {
		foreach ($this->mandatoryAttributes as $attr) {
			if (!$this->hasAttribute($attr))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_WIDGET_MANDATORY_PROPERTY', array($attr, parent::getClassName())), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Abstract method. Should be implemented by child classes
	 *
	 * @abstract
	 */
	function render() {
		print '';
	}

	/**
	 * Used to render and display widget's HTML code
	 */
	function display() {
		$this->validate();
		$this->onPreRender();
		if ($this->hasOutput) {
			print "\n";
			$this->render();
			print "\n";
		}
	}

	/**
	 * Render Javascript code to initialize the widget
	 *
	 * @param array $attrs Attributes
	 * @access private
	 */
	function renderJS($attrs) {
		print "\n<script type=\"text/javascript\">\n";
		print sprintf("Widget.init(\"%s\", %s", parent::getClassName(), JSONEncoder::encode($attrs));
		if (!empty($this->listeners)) {
			print ", function(widget) {\n";
			foreach ($this->listeners as $event => $listeners) {
				for ($i=0; $i<sizeof($listeners); $i++) {
					print sprintf("\twidget.addEventListener(\"%s\", function(args) {\n%s\n\t});\n", $event, $listeners[$i]);
				}
			}
			print "});\n";
		} else {
			print ");\n";
		}
		print "</script>";
	}
}
?>