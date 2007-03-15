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

import('php2go.net.SocketClient');

/**
 * SMTP default port
 */
define('SMTP_DEFAULT_PORT', 25);
/**
 * Default connection timeout
 */
define('SMTP_DEFAULT_TIMEOUT', 5);
/**
 * Default line end characters
 */
define('SMTP_CRLF', "\r\n");

/**
 * SMTP client class
 *
 * Implementation of an SMTP client, which connects to a server
 * and sends mail messages. This client was built according to
 * the RFC821, supporting all SMTP commands, except TURN.
 *
 * @package net
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Smtp extends SocketClient
{
	/**
	 * Debug flag
	 *
	 * @var bool
	 */
	var $debug = FALSE;

	/**
	 * Max length of a line in the message, according to RFC821
	 *
	 * @var int
	 */
	var $maxDataLength = 998;

	/**
	 * Class constructor
	 *
	 * @return Smtp
	 */
	function Smtp(){
		parent::SocketClient();
		parent::setBufferSize(515);
		parent::setLineEnd(SMTP_CRLF);
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
	function __destruct(){
		unset($this);
	}

	/**
	 * Connects to a given SMTP host
	 *
	 * When missing, $port defaults to {@link SMTP_DEFAULT_PORT}
	 * and $timeout defaults to {@link SMTP_DEFAULT_TIMEOUT}.
	 *
	 * @param string $host Host name or IP address
	 * @param int $port Port
	 * @param int $timeout Timeout
	 * @return bool
	 */
	function connect($host, $port=SMTP_DEFAULT_PORT, $timeout=SMTP_DEFAULT_TIMEOUT) {
		if (!parent::connect($host, $port, NULL, $timeout)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SMTP_CONNECT', array_unshift(parent::getLastError(), $host)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$greeting = $this->_readData();
			if ($this->debug)
				print('SMTP DEBUG --- FROM SERVER : ' . $greeting . '<br>');
			return TRUE;
		}
	}

	/**
	 * Send a HELO command to the server
	 *
	 * The HELO command is used to certify that both client and
	 * server are in the same known state.
	 *
	 * When $heloHost is missing, $_SERVER['SERVER_NAME'] or
	 * localhost.localdomain will be used.
	 *
	 * @param string $heloHost HELO host
	 * @return bool
	 */
	function helo($heloHost='') {
		/**
		 * Format:
		 * HELO <SP> <DOMAIN> <CRLF>
		 * Success: 250
		 * Failure: 500, 501, 504, 421
		 */
		if (empty($heloHost))
			$heloHost = Environment::has('SERVER_NAME') ? Environment::get('SERVER_NAME') : 'localhost.localdomain';
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("HELO %s%s", $heloHost, SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('HELO', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Authenticates in the SMTP server
	 *
	 * @param string $username Username
	 * @param string $password Password
	 * @return bool
	 */
	function authenticate($username, $password) {
		/**
		 * Format:
		 * AUTO <SP> LOGIN <CRLF>
		 * Intermediary: 334
		 * <USERNAME> <CRLF>
		 * Intermediary: 334
		 * <PASSWORD> <CRLF>
		 * Success: 235
		 * Failure: 500, 501, 502, 504, 535
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("AUTH LOGIN%s", SMTP_CRLF);
		if (!$this->_sendData($data, 334, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('AUTH LOGIN', $responseCode, $responseMessage));
			return FALSE;
		}
		if (!$this->_sendData(base64_encode($username) . SMTP_CRLF, 334, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_AUTHENTICATE') . ' ' . $responseCode . ': ' . $responseMessage;
			return FALSE;
		}
		if (!$this->_sendData(base64_encode($password) . SMTP_CRLF, 235, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_AUTHENTICATE') . ' ' . $responseCode . ': ' . $responseMessage;
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Sends the MAIL FROM command to the server
	 *
	 * Initiates a mail send transaction with the server. If the
	 * sender is accepted, the next command should be RCPT.
	 *
	 * @param string $from Sender address
	 * @return bool
	 */
	function mail($from) {
		/**
		 * Format:
		 * MAIL <SP> FROM: <reverse-path> <CRLF>
		 * Success: 250
		 * Failure: 552, 451, 452, 500, 501, 421
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("MAIL FROM:%s%s", "<$from>", SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('MAIL', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Sends the RCPT TO command to the server
	 *
	 * @param string $to Recipient address
	 * @return bool
	 */
	function recipient($to) {
		/**
		 * Format:
		 * RCPT <SP> TO: <forward-path> <CRLF>
		 * Success: 250, 251
		 * Failure: 550, 551, 552, 553, 450, 451, 452, 500, 501, 503, 421
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("RCPT TO:%s%s", "<$to>", SMTP_CRLF);
		if (!$this->_sendData($data, array(250, 251), $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('RCPT', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Sends the DATA command to the server
	 *
	 * The DATA command is used to send the contents of the message:
	 * headers and body. This command should be preceeded by commands
	 * that define sender and recipient(s) of the message.
	 *
	 * @param string $msgData Message contents
	 * @return bool
	 */
	function data($msgData) {
		/**
		 * Format:
		 * DATA <CRLF>
		 * Intermediary: 354
		 * Failure: 451, 554, 500, 501, 503, 421
		 * [MESSAGE DATA]
		 * <CRLF> . <CRLF>
		 * Success: 250
		 * Failure: 552, 554, 451, 452
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		if (!$this->_sendData('DATA' . SMTP_CRLF, 354, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('DATA', $responseCode, $responseMessage));
			return FALSE;
		}
		/**
		 * Ok, ready to send the message contents
		 * According to RFC822, a single line can be longer than 1000 chars,
		 * including CR and LF. Content will be broken into smaller parts using
		 * the CR and LF delimiters.
		 */
		$msgData = str_replace("\r\n", "\n", $msgData);
		$msgData = str_replace("\r", "\n", $msgData);
		$msgLines = explode("\n", $msgData);
		/**
		 * According to RFC822, a line without spaces in the start and
		 * separated by the ":" sign caracterizes a header field. Message
		 * headers and body are separated by a line containing only CRLF.
		 */
		$headers = (strpos($msgLines[0], ':') !== FALSE > 0 && strpos($msgLines[0], ' ') === FALSE) ? TRUE : FALSE;
		while (list(, $line) = each($msgLines)) {
			$buffer = array();
			if ($line == '' && $headers)
				$headers = FALSE;
			// break the line into pieces if necessary
			while (strlen($line) > $this->maxDataLength) {
				$lastSpacePos = strrpos(substr($line, 0, $this->maxDataLength), ' ');
				$buffer[] = substr($line, 0, $lastSpacePos);
				$line = substr($line, $lastSpacePos+1);
				// header lines must be preceeded by a LWSP char (tab)
				if ($headers) {
					$line = "\t" . $line;
				}
			}
			$buffer[] = $line;
			// send lines to the server
			while (list(, $line) = each($buffer)) {
				if (strlen($line) > 0 && substr($line, 0, 1) == '.') {
					$line = '.' . $line;
				}
				if ($this->debug)
					print('SMTP DEBUG --- FROM CLIENT : ' . htmlspecialchars($line) . '<br>');
				parent::write($line . SMTP_CRLF);
			}
		}
		/**
		 * after all lines were sent, a line containing just a dot and CRLF is
		 * sent, indicating that the end of the message was reached
		 */
		if (!$this->_sendData('.' . SMTP_CRLF, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('DATA', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Sends the SEND FROM command to the server
	 *
	 * Tries to deliver the message to a user whose terminal is active.
	 *
	 * @param string $from Sender address
	 * @return bool
	 */
	function send($from) {
		/**
		 * Format:
		 * SEND <SP> FROM:< <reverse-path> > <CRLF>
		 * Success: 250
		 * Failure: 552, 451, 452, 500, 501, 502, 421
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("SEND FROM:%s%s", $from, SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('SEND', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Sends the SOML FROM command to the server
	 *
	 * Tries to deliver the message to the active
	 * terminal of the user. When this terminal is
	 * not found, the message is redirected to the
	 * recipient's mailbox.
	 *
	 * @param string $from Semder address
	 * @return bool
	 */
	function sendOrMail($from) {
		/**
		 * Format:
		 * SOML <SP> FROM:< <reverse-path> > <CRLF>
		 * Success: 250
		 * Failure: 552, 451, 452, 500, 501, 502, 421
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("SOML FROM:%s%s", $from, SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('SOML', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Sends the SAML FROM command to the server
	 *
	 * Tries to deliver the message in the recipient's mailbox.
	 * Returns an error if it's not possible to access this
	 * mailbox.
	 *
	 * @param string $from Sender address
	 * @return bool
	 */
	function sendAndMail($from) {
		/**
		 * Format:
		 * SAML <SP> FROM:< <reverse-path> > <CRLF>
		 * Success: 250
		 * Error: 552, 451, 452, 500, 501, 502, 421
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("SAML FROM:%s%s", $from, SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('SAML', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Sends a HELP command to the server
	 *
	 * This command asks help information about a
	 * given SMTP command or keyword.
	 *
	 * @param string $keyword Command or keyword
	 * @return string|bool Returned information or FALSE in case of errors
	 */
	function help($keyword='') {
		/**
		 * Format:
		 * HELP <SP> <KEYWORD> <CRLF>
		 * Success: 211, 214
		 * Error: 500, 501, 502, 504, 421
		 */
		if (!empty($keyword))
			$keyword = ' ' . $keyword;
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("HELP %s%s", $keyword, SMTP_CRLF);
		if (!$this->_sendData($data, array(211, 214), $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('HELP', $responseCode, $responseMessage));
			return FALSE;
		}
		return $responseMessage;
	}

	/**
	 * Sends the EXPN command to the server
	 *
	 * Given a list of usernames (comma separated), the EXPN
	 * command resolves the email addresses of these users.
	 *
	 * @param string $listName List name
	 * @return array|bool Array of email addresses or FALSE in case of errors
	 */
	function expand($listName) {
		/**
		 * Format:
		 * EXPR <SP> <LIST> <CRLF>
		 * Success: 250
		 * Failure: 550, 500, 501, 502, 504, 421
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("EXPN %s%s", $listName, SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('EXPN', $responseCode, $responseMessage));
			return FALSE;
		}
		$list = array();
		$responseLines = explode(SMTP_CRLF, $responseMessage);
		while (list(,$data) = each($responseLines)) {
			$list[] = $data;
		}
		return $list;
	}

	/**
	 * Sends the VRFY command to the server
	 *
	 * This command checks if a given username or mailbox
	 * exists in the server.
	 *
	 * @param string $name Username or mailbox name
	 * @return string|bool Server response or FALSE in case of errors
	 */
	function verify($name) {
		/**
		 * Format:
		 * VRFY <SP> <NAME> <CRLF>
		 * Success: 250, 251
		 * Failure: 550, 551, 553, 500, 501, 502, 504, 421
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("VRFY %s%s", $name, SMTP_CRLF);
		if (!$this->_sendData($data, array(250, 251), $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('VRFY', $responseCode, $responseMessage));
			return FALSE;
		}
		return $responseMessage;
	}

	/**
	 * Sends a NOOP command to the server
	 *
	 * @return bool
	 */
	function noop() {
		/**
		 * Format:
		 * NOOP <CRLF>
		 * Success: 250
		 * Failure: 500, 421
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("NOOP%s", SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('NOOP', $responseCode, $responseMessage));
		}
		return TRUE;
	}

	/**
	 * Sends a RSET command to the server
	 *
	 * This command aborts the active transaction, clearing
	 * buffers and collected data.
	 *
	 * @return bool
	 */
	function reset() {
		/**
		 * Format:
		 * RSET <CRLF>
		 * Success: 250
		 * Failure: 500, 501, 504, 421
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("RSET%s", SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('RSET', $responseCode, $responseMessage));
		}
		return TRUE;
	}

	/**
	 * Sends the QUIT command to the server and closes the socket connection
	 *
	 * @return bool
	 */
	function quit() {
		/**
		 * Format:
		 * QUIT <CRLF>
		 * Success: 221
		 * Failure: 500
		 */
		$responseCode = NULL;
		$responseMessage = NULL;
		$data = sprintf("QUIT%s", SMTP_CRLF);
		if (!$this->_sendData($data, 221, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('QUIT', $responseCode, $responseMessage));
		}
		parent::close();
		return TRUE;
	}

	/**
	 * Utility method to send a command through the
	 * SMTP connection and process the returned response
	 *
	 * @param string $data Command data
	 * @param int|array $expected Expected return code(s)
	 * @param int &$responseCode Used to return the response code
	 * @param string &$responseMessage Used to return the response message
	 * @access private
	 * @return bool
	 */
	function _sendData($data, $expected, &$responseCode, &$responseMessage) {
		$expected = !is_array($expected) ? array($expected) : $expected;
		if (parent::write($data) && $this->_readResponse($responseCode, $responseMessage)) {
			if ($this->debug) {
				print('SMTP DEBUG --- FROM CLIENT : ' . htmlspecialchars($data) . '<br>');
				print('SMTP DEBUG --- FROM SERVER : ' . htmlspecialchars($responseCode . ' - ' . $responseMessage) . '<br>');
			}
			if (in_array($responseCode, $expected)) {
				return TRUE;
			} else {
				$responseMessage = htmlspecialchars($responseMessage);
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * Reads a response message from the server
	 *
	 * @param int &$responseCode Used to return the response code
	 * @param string &$responseMessage Used to return the response message
	 * @access private
	 * @return bool
	 */
	function _readResponse(&$responseCode, &$responseMessage) {
		if ($data = $this->_readData()) {
			$responseCode = substr($data, 0, 3);
			$responseMessage = substr($data, 4);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Bufferize contents read from the socket connection
	 * until and end line is found (or socket eof)
	 *
	 * @access private
	 * @return string|bool
	 */
	function _readData() {
		if (parent::isConnected()) {
			$buffer = '';
			while ($data = parent::readLine()) {
				$buffer .= $data;
				if ($data[3] == ' ')
					break;
			}
			return $buffer;
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}
}
?>