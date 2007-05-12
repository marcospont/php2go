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
 * Framework's custom error handler
 *
 * @param int $errorCode Error code
 * @param string $errorMessage Error message
 * @param string $fileName File path
 * @param int $lineNumber Line number
 * @param array $vars Local variables
 */
function php2GoErrorHandler ($errorCode, $errorMessage, $fileName, $lineNumber, $vars) {
	if ($errorCode != E_STRICT && error_reporting() != 0) {
		$ThrownError = Registry::get('PHP2Go_error');
		if ($ThrownError) {
			Registry::remove('PHP2Go_error');
			$ThrownError->handle();
		} else {
			$Error = new PHP2GoError();
			if (!$Error->isIgnoreError($errorMessage)) {
				$Error->setType($errorCode);
				$Error->setMessage($errorMessage);
				$Error->setFile($fileName);
				$Error->setLine($lineNumber);
				$Error->handle();
			}
		}
	}
}

/**
 * Custom handler for assertion errors
 *
 * @param string $fileName File path
 * @param int $lineNumber Line number
 * @param string $expressionCode Failed expression
 */
function php2GoAssertionHandler ($fileName, $lineNumber, $expressionCode) {
	import('php2go.base.Php2GoError');
	$userFile = Registry::get('PHP2Go_assertion_file');
	if (!is_null($userFile)) {
		$fileName = $userFile;
		Registry::remove('PHP2Go_assertion_file');
	}
	$userLine = Registry::get('PHP2Go_assertion_line');
	if (!is_null($userLine)) {
		$lineNumber = $userLine;
		Registry::remove('PHP2Go_assertion_line');
	}
	$Error = new PHP2GoError();
	$Error->setType(E_USER_WARNING);
	$Error->setMessage(PHP2Go::getLangVal('ERR_ASSERTION_MESSAGE'), $expressionCode);
	$Error->setFile($fileName);
	$Error->setLine($lineNumber);
	$Error->handle();
}

/**
 * Default handler for database errors
 *
 * @param string $dbms Driver name
 * @param string $function DB function
 * @param int $errorCode Error code
 * @param string $errorMessage Error message
 * @param mixed $p1 First parameter of the database command
 * @param mixed $p2 Second parameter of the database command
 * @param ADOConnection $connection Active database connection
 */
function dbErrorHandler($dbms, $function, $errorCode, $errorMessage, $p1=FALSE, $p2=FALSE, &$connection) {
	if (error_reporting() != 0) {
		switch ($function) {
			case 'EXECUTE':
				$extra = "$function (" . (is_array($p1) ? exportVariable($p1) : "\"" . $p1 . "\"") . ($p2 ? "," . (is_array($p2) ? exportVariable($p2) : "\"" . $p2 . "\"") . ")|" : ")");
				break;
			case 'PCONNECT':
			case 'CONNECT' :
				$extra = "$function ($p1, '****', '****', $p2)";
				break;
			default:
				$extra = "$function (" . (is_array($p1) ? exportVariable($p1) : "\"{$p1}\"") . ($p2 ? "," . (is_array($p2) ? exportVariable($p2) : "\"{$p2}\"") . ")" : ")");
				break;
		}
		$Error = new PHP2GoError();
		$Error->setType(E_DATABASE_ERROR);
		$Error->setMessage("$dbms [$errorCode] : $errorMessage", $extra);
		$Error->handle();
	}
}

/**
 * Displays a setup error
 *
 * @param string $msg Error message
 */
function setupError($msg) {
	die ("
		<br>
		<table border='1' bordercolor='#ff0000' cellpadding='0' cellspacing='0' width='100%'>
			<tr><td>
				<table border='0' cellpadding='6' cellspacing='0' width='100%'>
					<tr bgcolor='#EEEEEE'><td valign='top' style='font-family:Sans-Serif;font-size:12px'><b><font color='#ff0000'>SETUP ERROR</font></b><br>".$msg."<br></td></tr>
				</table>
			</td></tr>
		</table>
	");
}
?>