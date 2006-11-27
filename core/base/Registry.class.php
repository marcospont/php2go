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
 * Implementation of a registry of variables
 * 
 * This is an utility class that exposes a simple API to handle variables
 * stored in a registry. This registry is initialized with the contents
 * of the superglobal $GLOBALS array.
 * 
 * The scope of the variables handled by this class is the executing script.
 * This means that Registry is not a persistent storage layer for variables:
 * it is a simple wrapper to get/set variables and avoid using global variables
 * inside the application's code.
 * 
 * @package base
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Registry extends PHP2Go
{
	/**
	 * Registry entries
	 *
	 * @var array
	 */
	var $entries;

	/**
	 * Class constructor
	 * 
	 * It's not recommended to call this constructor directly. It is used
	 * by {@link Registry::getInstance()} to create the singleton of the class.
	 *
	 * @return Registry
	 */
	function Registry() {
		parent::PHP2Go();
		$this->entries =& $GLOBALS;
	}

	/**
	 * Get the singleton of the Registry class
	 * 
	 * @return Registry
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance =& new Registry();
		return $instance;
	}

	/**
	 * Read an entry from the registry
	 * 
	 * <code>
	 * $value = Registry::get('variable');
	 * if ($value) {
	 *   /* do something {@*}
	 * }
	 * </code>
	 *
	 * @param string $variable Variable name
	 * @uses getInstance
	 * @return mixed
	 * @static
	 */
	function get($variable) {
		$Registry =& Registry::getInstance();
		if (array_key_exists($variable, $Registry->entries))
			return $Registry->entries[$variable];
		else
			return NULL;
	}

	/**
	 * Add/modify an entry in the registry
	 * 
	 * <code>
	 * Registry::set(Registry::get('variable')+1);
	 * Registry::set('my_var', $myValue);
	 * </code>
	 *
	 * @param string $variable Variable name
	 * @param mixed $value Variable value
	 * @uses getInstance
	 * @static
	 */
	function set($variable, $value) {
		$Registry =& Registry::getInstance();
		$Registry->entries[$variable] = $value;
	}

	/**
	 * Remove an entry from the registry
	 *
	 * @param string $variable Variable name
	 * @return bool Operation result
	 * @uses getInstance
	 * @static
	 */
	function remove($variable) {
		$Registry =& Registry::getInstance();
		if (array_key_exists($variable, $Registry->entries)) {
			unset($Registry->entries[$variable]);
			return TRUE;
		}
		return FALSE;
	}
}
?>