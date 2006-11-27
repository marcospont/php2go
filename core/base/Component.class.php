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

/**
 * Basic renderizable component
 * 
 * The Component class is the base for all renderizable elements that can be
 * included in an HTML document in PHP2Go: templates, forms (Form class and 
 * its child classes, fields, buttons), reports (Report class) and other
 * graphic elements, such as menus (php2go.gui package).
 * 
 * @package base
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 * @abstract 
 */
class Component extends PHP2Go
{
	/**
	 * Component attributes
	 *
	 * @var array
	 */
	var $attributes = array();
	
	/**
	 * Indicates if the pre-render phase has
	 * already been executed
	 *
	 * @var bool
	 */
	var $preRendered = FALSE;

	/**
	 * Class constructor
	 *
	 * @return Component
	 */
	function Component() {
		parent::PHP2Go();
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
	function __destruct() {
		unset($this);
	}

	/**
	 * Get the value of an attribute
	 *
	 * @param string $name Attribute name
	 * @param mixed $fallback Value to be returned when attribute is not found
	 * @return mixed
	 */
	function getAttribute($name, $fallback=FALSE) {
		return (array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $fallback);
	}

	/**
	 * Create or modify an attribute
	 *
	 * @param string $name Attribute name
	 * @param mixed $value Attribute value
	 */
	function setAttribute($name, $value) {
		$this->attributes[$name] = $value;
	}

	/**
	 * Pre-render the component
	 * 
	 * Must be overriden by child classes and explictly called
	 * from inside the child implementations, so that the component
	 * gets correctly flagged as pre-rendered.
	 */
	function onPreRender() {
		$this->preRendered = TRUE;
	}

	/**
	 * Get the output produced by this component
	 *
	 * @return string
	 */
	function getContent() {
		ob_start();
		$this->display();
		return ob_get_clean();
	}

	/**
	 * Abstract method that should print the component's output
	 * 
	 * @abstract
	 */
	function display() {
	}

	/**
	 * Build and return a string representation of the component
	 * 
	 * This is very useful inside the template engine, when components
	 * are used as variables in the pattern {$var} and PHP version
	 * is >= 5.0.0.
	 *
	 * @return string
	 */
	function __toString() {
		ob_start();
		$this->display();
		return ob_get_clean();
	}
}
?>