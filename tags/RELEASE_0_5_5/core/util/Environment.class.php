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

/**
 * Reads and writes environment variables
 *
 * @package util
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Environment extends PHP2Go
{
	/**
	 * Checks if a given variable exists
	 *
	 * @param string $key Variable key
	 * @return bool
	 * @static
	 */
	function has($key) {
		if (isset($_SERVER[$key])) {
			return TRUE;
		}
		return TypeUtils::toBoolean(@getenv($key));
	}

	/**
	 * Gets the value of an environment variable
	 *
	 * Returns FALSE if the variable doesn't exist.
	 *
	 * @param string $key Variable key
	 * @return mixed
	 * @static
	 */
	function get($key) {
		if (isset($_SERVER[$key])) {
			return $_SERVER[$key];
		}
		if (@getenv($key))
			return getenv($key);
		else
			return NULL;
	}

	/**
	 * Sets or creates an environment variable
	 *
	 * This method won't work when safe_mode is enabled.
	 *
	 * @link http://www.php.net/putenv
	 * @param string $key Name
	 * @param mixed $value Value
	 * @static
	 */
	function set($key, $value) {
		if (System::getIni('safe_mode') != '1')
			@putenv("$key=$value");
	}
}
?>