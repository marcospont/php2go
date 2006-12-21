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

import('php2go.net.SocketClient');

/**
 * Initial state (not connected, inactive)
 */
define("POP3_OFF_STATE", 0);
/**
 * POP3 authentication state
 */
define("POP3_AUTH_STATE", 1);
/**
 * POP3 transaction state
 */
define("POP3_TRANS_STATE", 2);
/**
 * POP3 update state
 */
define("POP3_UPDATE_STATE", 3);
/**
 * Default POP3 port
 */
define("POP3_DEFAULT_PORT", 110);
/**
 * Default connection timeout
 */
define("POP3_DEFAULT_TIMEOUT", 60);
/**
 * Default line end character(s)
 */
define("POP3_CRLF", "\r\n");

/**
 * POP3 client class
 *
 * Implementation of a POP3 client, which connects to a POP server
 * and reads mail messages. The client was built according to the
 * RFC1939, support all POP commands and state sequences.
 *
 * Example:
 * <code>
 * $pop = new Pop3();
 * $pop->connect('my.pop.host');
 * $pop->login('foo', 'bar');
 * $count = $pop->getMessagesCount();
 * for ($i=1; $i<$count; $i++) {
 *   print $pop->getMessageHeaders($i);
 *   print $pop->getMessageBody($i);
 * }
 * $pop->quit();
 * </code>
 *
 * @package net
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Pop3 extends SocketClient
{
	/**
	 * Connection state
	 *
	 * @var int
	 */
	var $state = POP3_OFF_STATE;

	/**
	 * Debug flag
	 *
	 * @var bool
	 */
	var $debug = FALSE;

	/**
	 * Banner sent by the POP server
	 *
	 * @var string
	 */
	var $banner;

	/**
	 * Total number of messages in the mailbox
	 *
	 * @var int
	 */
	var $msgCount;

	/**
	 * Total mailbox size, in bytes
	 *
	 * @var int
	 */
	var $boxSize;

	/**
	 * Holds messages already fetched in the current connection
	 *
	 * @var array
	 */
	var $_msgCache = array();

	/**
	 * Class constructor
	 *
	 * @return Pop3
	 */
	function Pop3() {
		parent::SocketClient();
		parent::setBufferSize(512);
		parent::setLineEnd(POP3_CRLF);
		$this->msgCount = NULL;
		$this->boxSize = NULL;
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 *
	 * Quits the POP connection if active.
	 */
	function __destruct() {
		if ($this->state != POP3_OFF_STATE)
			$this->quit();
		unset($this);
	}

	/**
	 * Connects to a given POP3 host
	 *
	 * @param string $host Host name or IP address
	 * @param int $port Port
	 * @param int $timeout Timeout
	 * @return bool
	 */
	function connect($host, $port=POP3_DEFAULT_PORT, $timeout=POP3_DEFAULT_TIMEOUT) {
		// close a previously opened connection
		if ($this->state != POP3_OFF_STATE)
			$this->quit();
		// create and connect the socket
		if (!parent::connect($host, $port, NULL, $timeout)) {
			$this->state = POP3_OFF_STATE;
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_POP3_CONNECTION', array_unshift(parent::getLastError(), $host)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			// wait for server's response
			if ($response = $this->_readResponse()) {
				if ($this->debug)
					print('POP3 DEBUG --- FROM SERVER : ' . $response . '<br>');
				if (ereg("<([^>+])>", $response, $matches))
					$this->banner = $matches[1];
				$this->state = POP3_AUTH_STATE;
				return TRUE;
			} else {
				$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_CONNECTION', array($host, "---", $response));
				return FALSE;
			}
		}
	}

	/**
	 * Authenticates in the POP3 server
	 *
	 * @param string $userName Username
	 * @param string $password Password
	 * @param bool $apop Whether to use the APOP command
	 * @return bool
	 */
	function authenticate($userName, $password, $apop = FALSE) {
		if ($this->state == POP3_AUTH_STATE) {
			if ($apop && $this->apop($userName, $password))
				return TRUE;
			if ($this->user($userName) && $this->pass($password))
				return TRUE;
		}
		$this->quit();
		return FALSE;
	}

	/**
	 * Get the banner returned by the server upon connect
	 *
	 * @return string
	 */
	function getServerBanner() {
		if (isset($this->banner))
			return $this->banner;
		else
			return '';
	}

	/**
	 * Get messages count
	 *
	 * You should be connected to execute this method.
	 *
	 * @return int
	 */
	function getMessagesCount() {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		else {
			if(!TypeUtils::isInteger($this->msgCount))
				$this->stat();
			return $this->msgCount;
		}
	}

	/**
	 * Get mailbox size in bytes
	 *
	 * You should be connected to execute this method.
	 *
	 * @return int
	 */
	function getMailboxSize() {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		else {
			if(!TypeUtils::isInteger($this->boxSize))
				$this->stat();
			return $this->boxSize;
		}
	}

	/**
	 * Get all available messages
	 *
	 * Returns a hash array where the keys are the
	 * message IDs and the values are arrays containing
	 * two keys: uniqueId and size.
	 *
	 * @return array
	 */
	function getAllMessages() {
		$messageList = array();
		$uidl = $this->uidl();
		$list = $this->mList();
		if ($uidl && $list) {
			for($i=0; $i<sizeof($uidl); $i++) {
				$messageList[$uidl[$i][0]] = array(
					'uniqueId' => $uidl[$i][1],
					'size' => isset($list[$i]) ? $list[$i][1] : 0
				);
			}
		}
		return $messageList;
	}

	/**
	 * Get the contents of a given message
	 *
	 * You should be connected to execute this method.
	 *
	 * @param string $msgId Message ID
	 * @return string
	 */
	function getMessage($msgId) {
		if (isset($this->_msgCache[$msgId]))
			return $this->_msgCache[$msgId];
		return $this->retr($msgId);
	}

	/**
	 * Get the headers of a given message
	 *
	 * You should be connected to execute this method.
	 * Returns FALSE when the headers can't be read.
	 *
	 * @param string $msgId Message ID
	 * @param bool $parse Whether to parse the raw headers and return them as a hash array
	 * @return string|array|bool
	 */
	function getMessageHeaders($msgId, $parse=FALSE) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		if ($headers = $this->top($msgId, 0)) {
			if ($parse)
				return $this->_parseHeaders($headers);
			return $headers;
		}
		return FALSE;
	}

	/**
	 * Get the body of a given message
	 *
	 * You should be connected to execute this method.
	 * Returns FALSE when the message body can't be read.
	 *
	 * @param string $msgId Message ID
	 * @return string|bool
	 */
	function getMessageBody($msgId) {
		if ($content = $this->getMessage($msgId)) {
			$pos = strpos($content, POP3_CRLF . POP3_CRLF);
			if ($pos !== FALSE)
				return substr($content, $pos+4);
		}
		return FALSE;
	}

	/**
	 * Deletes all messages of the mailbox
	 *
	 * @return int Number of deleted messages
	 */
	function deleteAllMessages() {
		$deleted = 0;
		if ($list = $this->mList())
			foreach($list as $values)
				$deleted += TypeUtils::parseInteger($this->dele($values[0]));
		return $deleted;
	}

	/**
	 * Delete a message from the mailbox
	 *
	 * @param string $msgId Message ID
	 * @return bool
	 */
	function deleteMessage($msgId) {
		return $this->dele($msgId);
	}

	/**
	 * Sends the USER command
	 *
	 * @param string $userName Username
	 * @access protected
	 * @return bool
	 */
	function user($userName) {
		if ($this->state != POP3_AUTH_STATE)
			return FALSE;
		$responseMessage = NULL;
		$data = sprintf("USER %s%s", $userName, POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('USER', $responseMessage));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Sends the PASS command
	 *
	 * This command must be executed right after the USER command.
	 *
	 * @param string $password Password
	 * @access protected
	 * @return bool
	 */
	function pass($password) {
		if ($this->state != POP3_AUTH_STATE)
			return FALSE;
		$responseMessage = NULL;
		$data = sprintf("PASS %s%s", $password, POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_AUTHENTICATE');
			return FALSE;
		}
		$this->state = POP3_TRANS_STATE;
		return TRUE;
	}

	/**
	 * Sends the APOP command
	 *
	 * If the POP server doesn't accept the APOP command, or doesn't
	 * have a banner, FALSE is returned. If the authentication returns
	 * success, TRUE is returned.
	 *
	 * @param string $userName Username
	 * @param string $password Password
	 * @access protected
	 * @return bool
	 */
	function apop($userName, $password) {
		if ($this->state != POP3_AUTH_STATE)
			return FALSE;
		if (!isset($this->banner) || empty($this->banner)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_APOP');
			return FALSE;
		}
		$responseMessage = NULL;
		$data = sprintf("APOP %s %s%s", $userName, md5($this->banner . $password), POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_AUTHENTICATE');
			return FALSE;
		}
		$this->state = POP3_TRANS_STATE;
		return TRUE;
	}

	/**
	 * Sends the STAT command
	 *
	 * The STAT command collects information about the user's mailbox.
	 * Thus, it populates the {@link msgCount} and {@link boxSize}
	 * properties.
	 *
	 * @access protected
	 * @return bool
	 */
	function stat() {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$data = sprintf("STAT%s", POP3_CRLF);
		$responseMessage = NULL;
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('STAT', $responseMessage));
			return FALSE;
		} else {
			if (ereg("([0-9]+)[ ]([0-9]+)", $responseMessage, $matches)) {
				$this->msgCount = TypeUtils::parseIntegerPositive($matches[1]);
				$this->boxSize = TypeUtils::parseIntegerPositive($matches[2]);
			}
			return TRUE;
		}
	}

	/**
	 * Sends the RETR command
	 *
	 * The RETR command retrieves the contents of a message given its ID.
	 *
	 * @param string $msgId Message ID
	 * @return string Message contents
	 * @access protected
	 */
	function retr($msgId) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;
		$data = sprintf("RETR %s%s", $msgId, POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('STAT', $responseMessage));
			return FALSE;
		} else {
			$msgData = $this->_readAll();
			return $msgData;
		}
	}

	/**
	 * Sends the TOP command
	 *
	 * The TOP command requests the headers of the
	 * message and the first N lines of the
	 * message body.
	 *
	 * @param string $msgId Message ID
	 * @param int $numLines Number of body lines
	 * @return string Message headers and body
	 * @access protected
	 */
	function top($msgId, $numLines=0) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;
		$data = sprintf("TOP %s %d%s", $msgId, TypeUtils::parseIntegerPositive($numLines), POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('TOP', $responseMessage));
			return FALSE;
		}
		return $this->_readAll();
	}

	/**
	 * Sends the DELE command
	 *
	 * Deletes a message given its ID. Delete messages
	 * will only be purged after the connection is closed.
	 *
	 * @param string $msgId Message ID
	 * @access protected
	 * @return bool
	 */
	function dele($msgId) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;
		$data = sprintf("DELE %s%s", $msgId, POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('DELE', $responseMessage));
			return FALSE;
		}
		if (isset($this->_msgCache[$msgId]))
			unset($this->_msgCache[$msgId]);
		return TRUE;
	}

	/**
	 * Sends a LIST command
	 *
	 * Collects ID and size of a specific message
	 * ID or of all messages in the mailbox.
	 *
	 * @param string $msgId Optional message ID
	 * @access protected
	 * @return array
	 */
	function mList($msgId=NULL) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;
		if (TypeUtils::isNull($msgId)) {
			$data = sprintf("LIST%s", POP3_CRLF);
			if ($this->_sendData($data, $responseMessage)) {
				$lines = explode(POP3_CRLF, $this->_readAll());
				$return = array();
				foreach($lines as $line) {
					if (ereg("([0-9]+)[ ]([0-9]+)", $line, $matches)) {
						$return[] = array($matches[1], $matches[2]);
					}
				}
				return $return;
			}
		} else {
			$data = sprintf("LIST %s%s", $msgId, POP3_CRLF);
			if ($this->_sendData($data, $responseMessage)) {
				if (ereg("([0-9]+)[ ]([0-9]+)", $responseMessage, $matches))
					return array($matches[1], $matches[2]);
				else
					return FALSE;
			}
		}
		$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('LIST', $responseMessage));
		return FALSE;
	}

	/**
	 * Sends the UIDL command
	 *
	 * Collects number and unique-ID of a given message
	 * ID or of all messages in the mailbox.
	 *
	 * @param string $msgId Message ID
	 * @access protected
	 * @return array
	 */
	function uidl($msgId=NULL) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;
		if (TypeUtils::isNull($msgId)) {
			$data = sprintf("UIDL%s", POP3_CRLF);
			if ($this->_sendData($data, $responseMessage)) {
				$lines = explode(POP3_CRLF, $this->_readAll());
				$return = array();
				foreach($lines as $line) {
					if (ereg("([0-9]+)[ ](.+)", $line, $matches)) {
						$return[] = array($matches[1], $matches[2]);
					}
				}
				return $return;
			}
		} else {
			$data = sprintf("UIDL %s%s", $msgId, POP3_CRLF);
			if ($this->_sendData($data, $responseMessage)) {
				if (ereg("([0-9]+)[ ](.+)", $responseMessage, $matches))
					return array($matches[1], $matches[2]);
				else
					return FALSE;
			}
		}
		$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('UIDL', $responseMessage));
		return FALSE;
	}

	/**
	 * Sends an RSET command
	 *
	 * All delete marks are undone and the connection is closed.
	 *
	 * @return bool
	 */
	function rset() {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;
		$data = sprintf("RSET%s", POP3_CRLF);
		if (!$retVal = $this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('RSET', $responseMessage));
			return $retVal;
		}
		$this->quit();
		return $retVal;
	}

	/**
	 * Sends a NOOP command
	 *
	 * @return mixed Return value
	 */
	function noop() {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;
		$data = sprintf("NOOP%s", POP3_CRLF);
		if (!$retVal = $this->_sendData($data, $responseMessage))
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('NOOP', $responseMessage));
		return $retVal;
	}

	/**
	 * Sends a QUIT command
	 *
	 * @return bool
	 */
	function quit() {
		$this->_msgCache = array();
		if ($this->state == POP3_TRANS_STATE)
			$this->state = POP3_UPDATE_STATE;
		else
			$this->state = POP3_OFF_STATE;
		$responseMessage = NULL;
		$data = sprintf("QUIT%s", POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage))
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('QUIT', $responseMessage));
		parent::close();
		return TRUE;
	}

	/**
	 * Internal method used to send information through
	 * the socket connection
	 *
	 * Searches for "+OK" in the start of the response message
	 * to flag the command as successful.
	 *
	 * @param string $data Command data
	 * @param string &$responseMessage Used to catch the response data
	 * @access private
	 * @return bool
	 */
	function _sendData($data, &$responseMessage) {
		$this->resetError();
		if (parent::write($data) && $responseMessage = $this->_readResponse()) {
			if ($this->debug) {
				print('POP3 DEBUG --- FROM CLIENT : ' . htmlspecialchars($data) . '<br>');
				print('POP3 DEBUG --- FROM SERVER : ' . htmlspecialchars($responseMessage) . '<br>');
			}
			if (ereg("^\+OK", $responseMessage)) {
				$responseMessage = trim(substr($responseMessage, 3));
				return TRUE;
			} else {
				$responseMessage = trim(substr($responseMessage, 4));
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * Read a line from the POP3 connection
	 *
	 * Returns FALSE when EOF is reached or when the connection is inactive.
	 *
	 * @access private
	 * @return string|FALSE
	 */
	function _readResponse() {
		return parent::readLine();
	}

    /**
     * Read lines from the POP3 connection until a line
     * containing just one period char is found
     *
     * @access private
     * @return string
     */
	function _readAll() {
        $data = '';
        $line = parent::readLine();
        while ($line !== FALSE && trim($line) != '.') {
			if (StringUtils::left($line, 2) == '..')
				$line = substr($line, 1);
			$data .= trim($line) . POP3_CRLF;
			$line = parent::readLine();
		}
		return StringUtils::left($data, -2);
    }

	/**
	 * Parse the headers of a message
	 *
	 * @param string $rawHeaders Raw message headers
	 * @access private
	 * @return array
	 */
	function _parseHeaders($rawHeaders) {
		$headers = array();
		$matches = array();
		$headerList = explode(POP3_CRLF, $rawHeaders);
		foreach ($headerList as $headerItem) {
			if (preg_match("/^([a-zA-Z_\-]+)\:(.*)/", $headerItem, $matches)) {
				$headerName = trim($matches[1]);
				$headerValue = trim($matches[2]);
				if (isset($headers[$headerName])) {
					if (is_array($headers[$headerName]))
						$headers[$headerName][] = $headerValue;
					else
						$headers[$headerName] = array($headers[$headerName], $headerValue);
				} else {
					$headers[$headerName] = $headerValue;
				}
			} else {
				if (is_array($headers[$headerName]))
					$headers[$headerName][sizeof($headers[$headerName])-1] .= POP3_CRLF . trim($headerItem);
				else
					$headers[$headerName] .= POP3_CRLF . trim($headerItem);
			}
		}
		return $headers;
	}
}
?>