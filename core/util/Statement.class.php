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

import('php2go.net.HttpRequest');

/**
 * Processes variables and PHP blocks inside strings
 *
 * A statement represents a string that can contain references to variables
 * and pieces of PHP code. Based on a variable delimiter, the class parses
 * the declared variables and offers an API to manually assign values to
 * them or search them in the global scope.
 *
 * The default variable syntax is "~variable~". That means that "~" and
 * "~" are the prefix and suffix that identify a variable. A pair of "#"
 * chars inside the delimiters represent a block of PHP code that should
 * be evaluated.
 *
 * Examples:
 * <code>
 * $st = new Statement();
 * $st->setStatement("Hello, this is ~name~. I'm ~age~ years old.");
 * $st->bindByName('name', 'John', FALSE);
 * $st->bindByName('age', 30);
 * $st->displayResult();
 * $st->setStatement("Hi, this is ~name~. Today is ~#date('d/m/Y')#~.");
 * $st->bindByName('name', 'Paul', FALSE);
 * $st->displayResult();
 * </code>
 *
 * @package util
 * @uses HttpRequest
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Statement extends PHP2Go
{
	/**
	 * Statement source
	 *
	 * @var string
	 */
	var $source = '';

	/**
	 * Statement result (after variables binding)
	 *
	 * @var string
	 */
	var $result = '';

	/**
	 * Variables prefix
	 *
	 * @var string
	 */
	var $prefix = '~';

	/**
	 * Variables suffix
	 *
	 * @var string
	 */
	var $suffix = '~';

	/**
	 * Declared variables
	 *
	 * @var array
	 */
	var $variables = array();

	/**
	 * Declared PHP blocks
	 *
	 * @var array
	 */
	var $code = array();

	/**
	 * Whether unassigned variables should be displayed
	 *
	 * @var bool
	 */
	var $showUnassigned = FALSE;

	/**
	 * Is this an already parsed statement?
	 *
	 * @var bool
	 */
	var $prepared = FALSE;

	/**
	 * Class constructor
	 *
	 * @param string $source Statement's source
	 * @return Statement
	 */
	function Statement($source='') {
		parent::PHP2Go();
		if (!empty($source)) {
			$this->source = $source;
			$this->result = $source;
			$this->_parseStatement();
		}
		PHP2Go::registerDestructor($this, '__destruct');
	}

	/**
	 * Resolves all variables declared in a string based on the global scope
	 *
	 * @param string $value Statement's source
	 * @param string $prefix Variables prefix
	 * @param string $suffix Variables suffix
	 * @param bool $showUnassigned Show or hide unassigned variables
	 * @return string
	 * @static
	 */
	function evaluate($value, $prefix='~', $suffix='~', $showUnassigned=TRUE) {
		static $Stmt;
		if (!isset($Stmt))
			$Stmt = new Statement();
		$Stmt->setVariablePattern($prefix, $suffix);
		$Stmt->setStatement($value);
		$Stmt->setShowUnassigned($showUnassigned);
		$Stmt->bindVariables(FALSE);
		return $Stmt->getResult();
	}

	/**
	 * Get the statement's source
	 *
	 * @return string
	 */
	function getStatement() {
		return $this->source;
	}

	/**
	 * Display the statement's source
	 *
	 * @param bool $pre Add PRE tags or not
	 */
	function displayStatement($pre=TRUE) {
		print ($pre ? '<pre>' . $this->source . '</pre>' : $this->source);
	}

	/**
	 * Restarts the object with a new statement
	 *
	 * @param string $source Statement's source
	 */
	function setStatement($source) {
		$this->source = $source;
		$this->result = $source;
		$this->code = array();
		$this->variables = array();
		$this->_parseStatement();
	}

	/**
	 * Loads the statement from a file
	 *
	 * @param string $fileName File path
	 */
	function loadFromFile($fileName) {
		$this->setStatement(file_get_contents($fileName));
	}

	/**
	 * Set the variables pattern
	 *
	 * @param string $prefix Variables prefix
	 * @param string $suffix Variables suffix
	 */
	function setVariablePattern($prefix='', $suffix='') {
		if (!empty($prefix) || !empty($suffix)) {
			$this->prefix = $prefix;
			$this->suffix = $suffix;
		}
	}

	/**
	 * Enable/disable the display of unassigned variables
	 *
	 * @param bool $setting Flag value
	 */
	function setShowUnassigned($setting=TRUE) {
		$this->showUnassigned = (bool)$setting;
	}

	/**
	 * Checks if the instance contains an already parsed statement
	 *
	 * @return bool
	 */
	function isPrepared() {
		return $this->prepared;
	}

	/**
	 * Checks if the statement contains any variables or code blocks
	 *
	 * @return bool
	 */
	function isEmpty() {
		return (empty($this->variables) && empty($this->code));
	}

	/**
	 * Get all declared variables
	 *
	 * @return array
	 */
	function getDefinedVars() {
		return array_keys($this->variables);
	}

	/**
	 * Get the number of declared variables
	 *
	 * @return int
	 */
	function getVariablesCount() {
		return sizeof($this->variables);
	}

	/**
	 * Gets the current value of a variable
	 *
	 * @param string $variable Variable name
	 * @return mixed
	 */
	function getVariableValue($variable) {
		return (array_key_exists($variable, $this->variables) ? $this->variables[$variable]['value'] : NULL);
	}

	/**
	 * Checks if a given variable is declared
	 *
	 * @param string $variable Variable name
	 * @return bool
	 */
	function isDefined($variable) {
		return (array_key_exists($variable, $this->variables));
	}

	/**
	 * Checks if a given variable has a value
	 *
	 * @param string $variable Variable name
	 * @return bool
	 */
	function isBound($variable) {
		return (isset($this->variables[$variable]) && $this->variables[$variable]['value'] !== NULL);
	}

	/**
	 * Check if all statement's variables are bound
	 *
	 * @return bool
	 */
	function isAllBound() {
		if (!$this->prepared)
			return FALSE;
		reset($this->variables);
		foreach ($this->variables as $variable) {
			if ($variable['value'] === NULL)
				return FALSE;
		}
		return TRUE;
	}

	/**
	 * Assigns a value to a given variable
	 *
	 * @param string $variable Variable name
	 * @param mixed $value Value
	 * @param bool $quote Whether double quotes should be added when $value is a string
	 * @return bool
	 */
	function bindByName($variable, $value, $quote=TRUE) {
		if (array_key_exists($variable, $this->variables)) {
			if ($quote && is_string($value))
				$value = "\"" . $value . "\"";
			$this->variables[$variable]['value'] = $value;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Tries to assign value to a variable from the global scope
	 *
	 * The default search order is: ROEGPCS.
	 *
	 * @uses HttpRequest::getVar()
	 * @param string $name Variable name
	 * @param bool $quote Add quotes on string values
	 * @param string $searchOrder Search order
	 * @return bool
	 */
	function bindFromRequest($name, $quote=TRUE, $searchOrder=NULL) {
		if (empty($searchOrder))
			$searchOrder = 'ROEGPCS';
		if (isset($this->variables[$name])) {
			$variable = $this->variables[$name];
			if ($variable['array'] == TRUE) {
				$base = HttpRequest::getVar($variable['base'], 'all', $searchOrder);
				if (is_array($base) && array_key_exists($variable['key'], $base))
					$value = $base[$variable['key']];
				else
					$value = NULL;
			} else {
				$value = HttpRequest::getVar($name, 'all', $searchOrder);
			}
			if ($value !== NULL) {
				$this->variables[$name]['value'] = ($quote && is_string($value) ? "\"" . $value . "\"" : $value);
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Appends a value in a variable
	 *
	 * @param string $variable Variable name
	 * @param mixed $value Value to append
	 * @param bool $quote Add double quotes if $value is a string
	 * @return bool
	 */
	function appendByName($variable, $value, $quote=TRUE) {
		if (array_key_exists($variable, $this->variables)) {
			if ($quote && is_string($value))
				$value = "\"" . $value . "\"";
			$this->variables[$variable]['value'] .= $value;
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Sets a given array of variables
	 *
	 * @param array $array Variables hash array
	 * @param bool $quote Add quotes on string values
	 */
	function bindArray($array, $quote=TRUE) {
		if (is_array($array)) {
			foreach ($array as $name => $value)
				$this->bindByName($name, $value, $quote);
		}
	}

	/**
	 * Tries to assign values to all declared variables based on the global scope
	 *
	 * For each declared variable, {@link bindFromRequest} is called. Inside
	 * it, the variable is searched in the superglobals POST, GET, SESSION, COOKIE
	 * and ENV, besides of being searched in the Registry singleton and in the
	 * session objects. The search order (which repository must be read first,
	 * and so on) is defined by the $searchOrder argument.
	 *
	 * @uses bindFromRequest()
	 * @param bool $quote Add quotes on string values
	 * @param string $searchOrder Search order
	 * @param bool $replace Whether existent values should be replaced
	 * @return int Number of affected variables
	 */
	function bindVariables($quote=TRUE, $searchOrder='ROEGPCS', $replace=TRUE) {
		$affected = 0;
		reset($this->variables);
		foreach ($this->variables as $name => $variable) {
			if ($variable['value'] === NULL || $replace) {
				if ($this->bindFromRequest($name, $quote, $searchOrder))
					$affected++;
			}
		}
		return $affected;
	}

	/**
	 * Prints debug information about all declared variables
	 *
	 * @param bool $pre Add PRE tags
	 */
	function debugVariables($pre=TRUE) {
		$str = '';
		reset($this->variables);
		foreach($this->variables as $name => $variable) {
			if ($variable['value'] === NULL)
				$str .= "<b>Variable:</b> {$name} => <b>*NOT BOUND*</b><br />";
			else
				$str .= "<b>Variable:</b> {$name} => <b>{$variable['value']}</b><br />";
		}
		print ($pre ? '<pre>' . $str . '</pre>' : $str);
	}

	/**
	 * Applies all variable values and returns the processed statement
	 *
	 * @return string
	 */
	function getResult() {
		$this->result = $this->source;
		// substitution of code blocks
		reset($this->code);
		foreach ($this->code as $code) {
			ob_start();
			eval("\$value = " . substr($code, 1, -1) . ";");
			$error = ob_get_clean();
			if (!empty($error))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_STATEMENT_EVAL', substr($code, 1, -1)), E_USER_ERROR, __FILE__, __LINE__);
			$pattern = preg_quote("{$this->prefix}{$code}{$this->suffix}", '/');
			$this->result = preg_replace("/{$pattern}/", $value, $this->result, -1);
		}
		// substitution of variables
		reset($this->variables);
		foreach ($this->variables as $name => $variable) {
			if ($variable['array'] == TRUE) {
				$pattern = $this->prefix . $variable['base'] . "\[\'?" . preg_quote($variable['key'] , '/') . "\'?\]" . $this->suffix;
				if ($variable['value'] !== NULL)
					$this->result = preg_replace("/{$pattern}/", $variable['value'], $this->result, -1);
				// replace variable names by an empty string if showUnassigned==FALSE
				elseif (!$this->showUnassigned)
					$this->result = preg_replace("/{$pattern}/", '', $this->result, -1);
			} else {
				$pattern = preg_quote("{$this->prefix}{$name}{$this->suffix}", '/');
				if ($variable['value'] !== NULL)
					$this->result = preg_replace("/{$pattern}/", $variable['value'], $this->result, -1);
				// replace variable names by an empty string if showUnassigned==FALSE
				elseif (!$this->showUnassigned)
					$this->result = preg_replace("/{$pattern}/", '', $this->result, -1);
			}
		}
		return $this->result;
	}

	/**
	 * Processes the statement and displays it
	 *
	 * @param bool $pre Add PRE tags
	 */
	function displayResult($pre=TRUE) {
		$result = $this->getResult();
		print ($pre ? '<pre>' . $result . '</pre>' : $result);
	}

	/**
	 * Parses code blocks and variables from the statement's source
	 *
	 * @access private
	 */
	function _parseStatement() {
		$this->prepared = TRUE;
		$matches = array();
		$pattern = "/{$this->prefix}(#[^#]+#|[[:alnum:]_\:\[\'\]]+){$this->suffix}/";
		preg_match_all($pattern, $this->source, $matches, PREG_PATTERN_ORDER);
		if (!empty($matches[1])) {
			foreach ($matches[1] as $match) {
				if ($match[0] == '#' && $match{strlen($match)-1} == '#') {
					if (!in_array($match, $this->code))
						$this->code[] = $match;
				} else {
					if (!in_array($match, $this->variables)) {
						if (preg_match("/([[:alnum:]_]+)\[\'?([[:alnum:]_]+)\'?\]/", $match, $parts)) {
							$variable = array(
								'array' => TRUE,
								'base' => $parts[1],
								'key' => $parts[2],
								'value' => NULL
							);
						} else {
							$variable = array(
								'array' => FALSE,
								'base' => NULL,
								'key' => NULL,
								'value' => NULL
							);
						}
						$this->variables[$match] = $variable;
					}
				}
			}
		}
	}
}
?>