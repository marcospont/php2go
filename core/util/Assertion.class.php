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
 * Simple interface to assert expressions
 *
 * @package util
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Assertion extends PHP2Go
{
	/**
	 * Whether evaluation is enabled
	 *
	 * @var int
	 * @access private
	 */
	var $active = 1;

	/**
	 * Whether an assertion failure should throw a warning
	 *
	 * @var int
	 * @access private
	 */
	var $warning = 0;

	/**
	 * Whether the assertion evaluation should be silent
	 *
	 * @var int
	 * @access private
	 */
	var $quiet = 1;

	/**
	 * Whether an assertion failure should abort the script execution
	 *
	 * @var int
	 * @access private
	 */
	var $bail = 0;

	/**
	 * Callback that should handle assertion failures
	 *
	 * @var mixed
	 * @access private
	 */
	var $callback = NULL;

	/**
	 * Class constructor
	 *
	 * @return Assertion
	 */
	function Assertion() {
		parent::PHP2Go();
		$this->_setOption(ASSERT_ACTIVE, $this->active);
		$this->_setOption(ASSERT_WARNING, $this->warning);
		$this->_setOption(ASSERT_QUIET_EVAL, $this->quiet);
		$this->_setOption(ASSERT_BAIL, $this->bail);
		$this->_setOption(ASSERT_CALLBACK, 'php2GoAssertionHandler');
	}

	/**
	 * Deactivates assertion evaluation
	 */
	function deactivate() {
		$this->active = 0;
		$this->_setOption(ASSERT_ACTIVE, $this->active);
	}

	/**
	 * Activates assertion evaluation
	 */
	function activate() {
		$this->active = 1;
		$this->_setOption(ASSERT_ACTIVE, $this->active);
	}

	/**
	 * Enables throwing of warnings upon assertion failures
	 */
	function enableWarning() {
		$this->warning = 1;
		$this->_setOption(ASSERT_WARNING, $this->warning);
	}

	/**
	 * Enables script aborting upon assertion failures
	 */
	function enableBail() {
		$this->bail = 1;
		$this->_setOption(ASSERT_BAIL, $this->bail);
	}

	/**
	 * Define a callback to handle assertion failures
	 *
	 * The callback function can be a string (function name),
	 * or an array (class/method or object/method).
	 *
	 * The function signature must accept 3 arguments: $file,
	 * $line and $code.
	 *
	 * @param mixed $callback Callback function
	 */
	function setCallback($callback) {
		$this->callback = $callback;
		$this->_setOption(ASSERT_CALLBACK, $callback);
	}

	/**
	 * Asserts a given expression
	 *
	 * @param bool $expression Boolean expression
	 * @param string $file File name
	 * @param int $line Line number
	 * @return bool
	 */
	function evaluate($expression, $file='', $line=0) {
		if ($file != '')
			Registry::set('PHP2Go_assertion_file', $file);
		if ($line != 0)
			Registry::set('PHP2Go_assertion_line', $line);
		return assert($expression);
	}

	/**
	 * Defines an assertion option
	 *
	 * @param string $option Option name
	 * @param mixed $value Option value
	 * @access private
	 */
	function _setOption($option, $value) {
		assert_options($option, $value);
	}
}
?>
