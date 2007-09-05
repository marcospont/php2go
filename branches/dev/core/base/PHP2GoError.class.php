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
 * Database error type, thrown from {@link dbErrorHandler()}
 */
define('E_DATABASE_ERROR', 0);
/**
 * Define missing error level constants
 */
if (!defined('E_STRICT'))
	define('E_STRICT', 2048);
if (!defined('E_RECOVERABLE_ERROR'))
	define('E_RECOVERABLE_ERROR', 4096);

/**
 * Base error class
 *
 * This class is used by the framework as a wrapper to throw and log
 * application errors.
 *
 * During the initialization, PHP2Go defines the function
 * {@link php2goErrorHandler()} as the error handler for
 * warnings, notices and user errors. Inside this function,
 * PHP2GoError class is used to create an error object, save
 * it in the error log file and raise it through {@link trigger_error()}.
 *
 * @package base
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class PHP2GoError extends PHP2Go
{
	/**
	 * Class that generated the error
	 *
	 * @var object
	 */
	var $object;

	/**
	 * Error message
	 *
	 * @var string
	 */
	var $msg = '';

	/**
	 * Extra/detailed error message
	 *
	 * @var string
	 */
	var $extra = '';

	/**
	 * Error type
	 *
	 * @var int
	 */
	var $type = E_USER_ERROR;

	/**
	 * String description of the error type, loaded
	 * from the active language table
	 *
	 * @var string
	 */
	var $typeDesc;

	/**
	 * Error file path
	 *
	 * @var string
	 */
	var $file;

	/**
	 * Error line number
	 *
	 * @var int
	 */
	var $line;

	/**
	 * Format for log entry dates
	 *
	 * @var string
	 */
	var $dateFormat = '%b %d %H:%M:%S';

	/**
	 * Set of error messages that should be ignored
	 *
	 * @var array
	 */
	var $ignoreErrors = array ('UNDEFINED INDEX', 'USE OF UNDEFINED CONSTANT');

	/**
	 * Class constructor
	 *
	 * @return PHP2GoError
	 */
	function PHP2GoError() {
		parent::PHP2Go();
	}

	/**
	 * Get a singleton of the PHP2GoError class
	 *
	 * @return PHP2GoError
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new PHP2GoError();
		return $instance;
	}

	/**
	 * Set the class where the error happened
	 *
	 * @param object $object
	 */
	function setObject($object) {
		$this->object = get_class($object);
	}

	/**
	 * Set the error message
	 *
	 * @param string $message Message
	 * @param mixed $extra Detailed message, or extra information about the error
	 */
	function setMessage($message, $extra='') {
		$this->msg = $message;
		if (!empty($extra))
			$this->extra = $extra;
	}

	/**
	 * Get the text representation of the error type
	 *
	 * @return string
	 */
	function getTypeDesc() {
		return $this->typeDesc;
	}

	/**
	 * Set the error type
	 *
	 * @param int $type Error type
	 */
	function setType($type) {
		$this->type = $type;
		$errorTypes = PHP2Go::getLangVal('ERR_TYPES');
		$this->typeDesc = ($type == E_DATABASE_ERROR ? PHP2Go::getLangVal('ERR_DATABASE') : $errorTypes[$type]);
	}

	/**
	 * Set the path to the file where the error happened
	 *
	 * @param string $fileName File path
	 */
	function setFile($fileName='') {
		if (trim($fileName) != '')
			$this->file = $fileName;
	}

	/**
	 * Set the line number where the error happened
	 *
	 * @param int $lineNumber Error line number
	 */
	function setLine($lineNumber=NULL) {
		if (!is_null($lineNumber))
			$this->line = $lineNumber;
	}

	/**
	 * Check if errors should be captured, according to the
	 * global configuration settings
	 *
	 * @return bool
	 */
	function captureErrors() {
		return (PHP2Go::getConfigVal('CAPTURE_ERRORS', FALSE) === TRUE);
	}

	/**
	 * Check if errors should be logged, according to the
	 * global configuration settings
	 *
	 * @return bool
	 */
	function logErrors() {
		return (PHP2Go::getConfigVal('LOG_ERRORS', FALSE) === TRUE);
	}

	/**
	 * Check if errors should be displayed, according to the
	 * global configuration settings
	 *
	 * @return bool
	 */
	function showErrors() {
		return (PHP2Go::getConfigVal('SHOW_ERRORS', FALSE) === TRUE);
	}

	/**
	 * Check if debug trace should be displayed/logged along with
	 * the error message
	 *
	 * @return bool
	 */
	function debugTrace() {
		return (PHP2Go::getConfigVal('DEBUG_TRACE', FALSE) === TRUE);
	}

	/**
	 * Check if a given error message should be ignored
	 *
	 * @param string $errorMessage Error message
	 * @return bool
	 */
	function isIgnoreError($errorMessage) {
		$userIgnoreErrors = PHP2Go::getConfigVal('IGNORE_ERRORS', FALSE);
		$ignoreErrors = (!is_array($userIgnoreErrors) ? $this->ignoreErrors : array_merge($this->ignoreErrors, $userIgnoreErrors));
		for ($i = 0; $i < sizeof($ignoreErrors); $i++) {
			if (preg_match("/{$ignoreErrors[$i]}/i", $errorMessage))
				return TRUE;
		}
		return FALSE;
	}

	/**
	 * Check if a given error code is E_USER_* (user error codes)
	 *
	 * @param int $errorCode Error code
	 * @return bool
	 */
	function isUserError($errorCode) {
		return ($errorCode == E_USER_ERROR || $errorCode == E_USER_WARNING || $errorCode == E_USER_NOTICE);
	}

	/**
	 * Raise the error, if capturing errors is enabled
	 */
	function raise() {
		if ($this->captureErrors()) {
			Registry::set('PHP2Go_error', $this);
			trigger_error($this->msg, $this->type);
		}
	}

	/**
	 * Log the error using the given $logFile
	 *
	 * The log file path can contain {@link strftime()} placeholders.
	 *
	 * @param string $logFile Log file path
	 */
	function log($logFile) {
		if ($this->captureErrors()) {
			$this->_logError(array(
				'CODE' => $this->type,
				'TYPE' => $this->typeDesc,
				'MESSAGE' => $this->_formatMessage(),
				'EXTRA' => $this->extra,
				'FILE' => (isset($this->file) ? $this->file : __FILE__),
				'LINE' => (isset($this->line) ? $this->line : __LINE__),
				'TRACE' => $this->_getStackTrace(),
				'REQUEST' => $this->_getRequestData()
			), strftime($logFile));
		}
	}

	/**
	 * Handle the error by logging and/or displaying according
	 * with the global configuration settings
	 *
	 * This method is called inside {@link php2goErrorHandler()}
	 * to handle manually thrown errors or normal warnings/notices
	 * captured by the error handler.
	 */
	function handle() {
		if ($this->type != E_STRICT) {
			// build the error information
			$errorData = array(
				'CODE' => $this->type,
				'TYPE' => $this->typeDesc,
				'MESSAGE' => $this->msg,
				'EXTRA' => $this->extra,
				'FILE' => (isset($this->file) ? $this->file : __FILE__),
				'LINE' => (isset($this->line) ? $this->line : __LINE__),
				'TRACE' => $this->_getStackTrace(),
				'REQUEST' => $this->_getRequestData()
			);
			// display the error, if displaying errors is enabled
			if ($this->showErrors())
				$this->_displayError($errorData);
			// log the error
			$logFile = ($this->type == E_DATABASE_ERROR ? PHP2Go::getConfigVal('DB_ERROR_LOG_FILE', FALSE) : PHP2Go::getConfigVal('ERROR_LOG_FILE', FALSE));
			$this->_logError($errorData, strftime($logFile));
			// abort the script for user errors or database errors
			if ($this->type == E_USER_ERROR || $this->type == E_DATABASE_ERROR)
				exit;
		}
	}

	/**
	 * Display HTML information about the error
	 *
	 * @param array $errData Error data
	 * @access private
	 */
	function _displayError($errData) {
		$extra = @$errData['EXTRA'];
		$location = ($errData['FILE'] != '' && $errData['LINE'] != '' ? "<br />on {$errData['FILE']}, {$errData['LINE']}" : '');
		$stackTrace = '';
		if (!empty($errData['TRACE'])) {
			$stackTrace .= "<br /><b>STACK TRACE</b><pre>";
			foreach ($errData['TRACE'] as $element)
				$stackTrace .= "\tat {$element['FUNCTION']}(" . htmlspecialchars($element['ARGS']) . ")\n\t\ton {$element['FILE']}, {$element['LINE']}\n";
			$stackTrace .= "</pre>";
		}
		print ("
			<table cellpadding=\"6\" cellspacing=\"0\" style=\"border:1px solid red;background-color:#efefef;width:auto;\">
				<tr><td>
					<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\">
						<tr><td width=\"20\" valign=\"top\" rowspan=\"4\"><img src=\"" . PHP2Go::getConfigVal('ABSOLUTE_URI', FALSE) . "resources/icon/error.gif\" hspace=\"3\" /></td><td style=\"font-family:Arial;font-weight:bold;font-size:14px;color:red;\">PHP2Go - {$errData['TYPE']}</td></tr>
						<tr><td style=\"font-family:Arial;font-size:12px;\"><b>{$errData['MESSAGE']}</b></td></tr>
						<tr><td style=\"font-family:Arial;font-size:12px;\">{$extra}{$location}</td></tr>
						<tr><td style=\"font-family:Arial;font-size:12px;\">{$stackTrace}</td></tr>
					</table>
				</td></tr>
			</table>
		");
	}

	/**
	 * Build a log message from a given set of error properties
	 * and save it in a given log file
	 *
	 * No action is performed if logging errors is disabled in
	 * the global configuration settings.
	 *
	 * @param array $errData Error data
	 * @param string $logFile Log file path
	 */
	function _logError($errData, $logFile) {
		if ($this->logErrors()) {
			$nl = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? "\r\n" : "\n");
			$logString = "[" . strftime($this->dateFormat) . "]" . $nl;
			$logString .= "\tTYPE=\"{$errData['TYPE']}\"" . $nl;
			$logString .= "\tCODE={$errData['CODE']}" . $nl;
			$logString .= "\tMESSAGE=\"" . str_replace(array('<br>','<br />',"\r","\n","\""), array(' ',' ',' ',' ','\''), $errData['MESSAGE']) . "\"" . $nl;
			if (!empty($errData['EXTRA']))
				$logString .= "\tEXTRA=\"" . str_replace(array('<br>','<br />',"\r","\n","\""), array(' ',' ',' ',' ','\''), $errData['EXTRA']) . "\"" . $nl;
			$logString .= "\tFILE=\"{$errData['FILE']}\"" . $nl;
			$logString .= "\tLINE={$errData['LINE']}" . $nl;
			if (!empty($errData['TRACE'])) {
				$i=0;
				foreach ($errData['TRACE'] as $element) {
					$logString .= "\tTRACE{$i}=\"" . $element['FUNCTION'] . '(' . $element['ARGS'] . ')' . (!empty($element['FILE']) ? ' (' . $element['FILE'] . ', ' . $element['LINE'] . ')' : '') . "\"" . $nl;
					$i++;
				}
			}
			if (!empty($errData['REQUEST'])) {
				foreach ($errData['REQUEST'] as $key=>$value) {
					if (is_array($value))
						$logString .= "\t{$key}=\"" . dumpArray($value) . "\"" . $nl;
					else
						$logString .= "\t{$key}=\"{$value}\"" . $nl;
				}
			}
			if ($logFile != '' && @touch($logFile))
				@error_log($logString, 3, $logFile);
			else
				@error_log($logString, 0);
		}
	}

	/**
	 * Format the error message
	 *
	 * @return string
	 * @access private
	 */
	function _formatMessage() {
		if (!isset($this->object))
			return PHP2Go::getLangVal('ERR_SCRIPT_MESSAGE', $this->msg);
		return PHP2Go::getLangVal('ERR_OBJ_MESSAGE', array($this->object, $this->msg));
	}

	/**
	 * Get the stack trace of the error, for
	 * display or log purposes
	 *
	 * @return array Error stack trace
	 * @access private
	 */
	function _getStackTrace() {
		if ($this->debugTrace()) {
			$trace = debug_backtrace();
			$result = array();
			for ($i=0, $size=sizeof($trace); $i<$size; $i++) {
				$qualifiedName = (isset($trace[$i]['class']) ? $trace[$i]['class'] . $trace[$i]['type'] : '') . $trace[$i]['function'];
				if (strtolower($qualifiedName) == 'php2goerror->_getstacktrace')
					continue;
				// function or method name
				$element['FUNCTION'] = $qualifiedName;
				// function or method parameters
				if (sizeof($trace[$i]['args']) > 0) {
					$pars = array();
					for ($j=0; $j<sizeof($trace[$i]['args']); $j++) {
						if (is_string($trace[$i]['args'][$j])) {
							$arg = preg_replace("/<br( \/)?>/", " ", $trace[$i]['args'][$j]);
							$arg = preg_replace("/\s{2,}/", " ", preg_replace("/[\r\t\n]{1,}/", "", $arg));
							$pars[] = "'" . (strlen($arg) > 200 ? substr($arg, 0, 200) . "...'(" . strlen($arg) . ")" : $arg . "'");
						} elseif (is_object($trace[$i]['args'][$j])) {
							$pars[] = get_class($trace[$i]['args'][$j]) . " object";
						} elseif (is_bool($trace[$i]['args'][$j])) {
							$pars[] = ($trace[$i]['args'][$j] == TRUE ? "TRUE" : "FALSE");
						} elseif (is_null($trace[$i]['args'][$j])) {
							$pars[] = "NULL";
						} elseif (is_array($trace[$i]['args'][$j])) {
							$pars[] = dumpArray($trace[$i]['args'][$j]);
						} elseif (is_resource($trace[$i]['args'][$j])) {
							$pars[] = get_resource_type($trace[$i]['args'][$j]);
						} else {
							$pars[] = $trace[$i]['args'][$j];
						}
					}
					$element['ARGS'] = implode(',', $pars);
				} else {
					$element['ARGS'] = '';
				}
				// error file path
				$element['FILE'] = @$trace[$i]['file'];
				// error line number
				$element['LINE'] = @$trace[$i]['line'];
				$result[] = $element;
			}
			return $result;
		}
		return FALSE;
	}

	/**
	 * Collect information about the request to save in the log
	 * file along with the error data
	 *
	 * @return array
	 * @access private
	 */
	function _getRequestData() {
		$info = array();
		if (isset($_SERVER)) {
			$method = $_SERVER['REQUEST_METHOD'];
			if ($method == 'GET') {
				$info['METHOD'] = 'GET';
				$info['AGENT'] = $_SERVER['HTTP_USER_AGENT'];
				if (isset($_SERVER['HTTP_REFERER']))
					$info['REFERER'] = $_SERVER['HTTP_REFERER'];
				if (!empty($_GET)) {
					$info['PARAMS'] = array();
					foreach ($_GET as $key=>$value) {
						if (is_array($value))
							$info['PARAMS'][$key] = dumpArray($value);
						else
							$info['PARAMS'][$key] = $value;
					}
				}
			} elseif ($method == 'POST') {
				$info['METHOD'] = 'POST';
				$info['URI'] = $_SERVER['REQUEST_URI'];
				$info['AGENT'] = $_SERVER['HTTP_USER_AGENT'];
				if (isset($_SERVER['HTTP_REFERER']))
					$info['REFERER'] = $_SERVER['HTTP_REFERER'];
				if (!empty($_POST)) {
					$info['PARAMS'] = array();
					foreach ($_POST as $key=>$value) {
						if (is_array($value))
							$info['PARAMS'][$key] = dumpArray($value);
						else
							$info['PARAMS'][$key] = $value;
					}
				}
			}
			// cookies
			if (!empty($_COOKIE))
				$info['COOKIES'] = $_COOKIE;
			// session variables
			if (!empty($_SESSION)) {
				$info['SESSION'] = array();
				foreach ($_SESSION as $key=>$value) {
					if (is_scalar($value)) {
						$info['SESSION'][$key] = $value;
					} elseif (is_array($value)) {
						$info['SESSION'][$key] = dumpArray($value);
					} else {
						$exported = preg_replace("/\s{2,}/", " ", preg_replace("/[\r\t\n]{1,}/", "", exportVariable($value)));
						$info['SESSION'][$key] = $exported;
					}
				}
			}
		}
		return $info;
	}
}
?>