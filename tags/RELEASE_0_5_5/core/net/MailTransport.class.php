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

import('php2go.net.Smtp');

/**
 * Mail transport using the native {@link mail()} function
 */
define('MAIL_TRANSPORT_MAIL', 1);
/**
 * Mail transport using sendmail
 */
define('MAIL_TRANSPORT_SENDMAIL', 2);
/**
 * Mail transport using a manual SMTP connection
 */
define('MAIL_TRANSPORT_SMTP', 3);

/**
 * Processes and sends a MIME mail message
 *
 * @package net
 * @uses Smtp
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class MailTransport extends PHP2Go
{
	/**
	 * Transport type
	 *
	 * @var int
	 */
	var $type;

	/**
	 * Transport parameters
	 *
	 * @var array
	 */
	var $params;

	/**
	 * Transport error message
	 *
	 * @var string
	 */
	var $errorMessage;

	/**
	 * The message being sent
	 *
	 * @var object MailMessage
	 * @access private
	 */
	var $_Message;

	/**
	 * SMTP client
	 *
	 * @var object Smtp
	 * @access private
	 */
	var $_Smtp;

	/**
	 * Class constructor
	 *
	 * @param MailMessage &$MailMessage Message to be sent
	 * @return MailTransport
	 */
	function MailTransport(&$MailMessage) {
		parent::PHP2Go();
		if (!TypeUtils::isObject($MailMessage) || !is_a($MailMessage, 'mailmessage'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'MailMessage'), E_USER_ERROR, __FILE__, __LINE__);
		$this->errors = array();
		$this->_Message = $MailMessage;
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
	function __destruct() {
		if (isset($this->_Smtp))
			$this->_Smtp->quit();
	}

	/**
	 * Get transport type
	 *
	 * @return int
	 */
	function getType() {
		return $this->type;
	}

	/**
	 * Check if transport type matches a given $type
	 *
	 * @param int $type Transport type
	 * @return bool
	 */
	function isType($type) {
		return ($this->type == $type);
	}

	/**
	 * Set transport type
	 *
	 * Acceptable parameters for each transport type:
	 * # mail: none
	 * # sendmail: sendmail (string, path to the sendmail executable)
	 * # smtp: server, port, debug (bool), timeout (SMTP timeout), username, password, helo
	 *
	 * @param int $type Type
	 * @param array $params Parameters
	 * @return bool
	 */
	function setType($type, $params=array()) {
		if (!TypeUtils::isInteger($type) || $type < MAIL_TRANSPORT_MAIL || $type > MAIL_TRANSPORT_SMTP) {
			return FALSE;
		} elseif ($this->_validateParams($type, $params)) {
			$this->params = $params;
			$this->type = $type;
			return TRUE;
		} else {
			$typeName = ($type == MAIL_TRANSPORT_MAIL) ? 'mail' : ($type == MAIL_TRANSPORT_SENDMAIL) ? 'sendmail' : 'smtp';
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MAIL_INCOMPLETE_PARAMS', $typeName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
	}

	/**
	 * Get last error message
	 *
	 * @return string
	 */
	function getErrorMessage() {
		return $this->errorMessage;
	}

	/**
	 * Set another message to be sent by the class
	 *
	 * @param MailMessage &$MailMessage Mail message
	 */
	function setMessage(&$MailMessage) {
		if (!TypeUtils::isObject($MailMessage) || !is_a($MailMessage, 'mailmessage'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'MailMessage'), E_USER_ERROR, __FILE__, __LINE__);
		$this->_Message =& $MailMessage;
	}

	/**
	 * Send the message using the configured transport type
	 *
	 * @param bool $shutdown Whether to free allocated resources
	 * @return bool
	 */
	function send($shutdown=TRUE) {
		unset($this->errorMessage);
		if ($this->_validateMessage()) {
			switch($this->type) {
				case MAIL_TRANSPORT_MAIL :
					return $this->_mailSend();
				case MAIL_TRANSPORT_SENDMAIL :
					return $this->_sendmailSend();
				case MAIL_TRANSPORT_SMTP :
					return $this->_smtpSend($shutdown);
				default :
					return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * Sends the message using the native {@link mail()} function
	 *
	 * @access private
	 * @return bool
	 */
	function _mailSend() {
		$this->_Message->removeHeader('To');
		// get the subject, and remove the Subject header
		$subject = $this->_Message->headers['Subject'];
		$this->_Message->removeHeader('Subject');
		// requires at least one "To" recipient
		if (!$this->_Message->hasRecipients(MAIL_RECIPIENT_TO)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MAIL_EMPTY_RCPT'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// recipients
		$recipients = $this->_Message->to[0][0];
		for ($i=1; $i<sizeof($this->_Message->to); $i++) {
			$recipients .= sprintf(",%s", $this->_Message->to[$i][0]);
		}
		// mail() function
		$parameters = sprintf("-oi -f %s", $this->_Message->getFrom());
		if (!mail($recipients, $subject, $this->_Message->body, $this->_getMessageHeaders(), $parameters)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_EXECUTE_COMMAND', 'mail()'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * Sends the message using the local sendmail installation
	 *
	 * @access private
	 * @return bool
	 */
	function _sendmailSend() {
		$sendmailString = sprintf("%s -oi -f %s -F %s -t", $this->params['sendmail'], $this->_Message->getFrom(), $this->_Message->getFromName());
		if (!@$sendmail = popen($sendmailString, "w")) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_EXECUTE_COMMAND', $sendmailString), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			fputs($sendmail, $this->_getMessageHeaders());
			fputs($sendmail, $this->_Message->body);
			$result = pclose($sendmail) >> 8 & 0xFF;
			if ($result != 0) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_EXECUTE_COMMAND', $sendmailString), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	/**
	 * Sends the message using a manual SMTP connection
	 *
	 * @param bool $shutdown Shutdown flag
	 * @return bool
	 */
	function _smtpSend($shutdown) {
		$result = TRUE;
		if (!is_object($this->_Smtp)) {
			$this->_Smtp = new Smtp();
			$this->_Smtp->debug = (isset($this->params['debug']) ? TypeUtils::toBoolean($this->params['debug']) : FALSE);
		}
		// open connection
		if (!$this->_Smtp->isConnected()) {
			if (!isset($this->params['port']))
				$this->params['port'] = SMTP_DEFAULT_PORT;
			$result = $this->_Smtp->connect($this->params['server'], $this->params['port']);
		}
		// helo
		if ($result) {
			if (isset($this->params['helo']))
				$this->_Smtp->helo($this->params['helo']);
			else
				$this->_Smtp->helo();
		}
		// authentication
		if ($result && isset($this->params['username']) && isset($this->params['password'])) {
			$this->_Smtp->authenticate($this->params['username'], $this->params['password']);
		}
		// sender
		$result = $result && $this->_Smtp->mail($this->_Message->getFrom());
		// recipients
		$to = $this->_Message->getRecipients(MAIL_RECIPIENT_TO);
		foreach ($to as $recipient)
			$result = $result && $this->_Smtp->recipient($recipient[0]);
		$cc = $this->_Message->getRecipients(MAIL_RECIPIENT_CC);
		foreach ($cc as $recipient)
			$result = $result && $this->_Smtp->recipient($recipient[0]);
		$bcc = $this->_Message->getRecipients(MAIL_RECIPIENT_BCC);
		foreach ($bcc as $recipient)
			$result = $result && $this->_Smtp->recipient($recipient[0]);
		// message data
		$result = $result && $this->_Smtp->data($this->_getMessageHeaders() . $this->_Message->body);
		// close connection if necessary
		if ($shutdown)
			$result = $result && $this->_Smtp->quit();
		// get send errors
		if (!$result && $this->errorMessage = $this->_Smtp->getLastError())
			PHP2Go::raiseError("SMTP ERROR: {$this->errorMessage}", E_USER_WARNING, __FILE__, __LINE__);
		return $result;
	}

	/**
	 * Validate the message before sending
	 *
	 * @access private
	 * @return bool
	 */
	function _validateMessage() {
		if (!$this->_Message->built)
			$this->_Message->build();
		if (!$this->_Message->hasRecipients(MAIL_RECIPIENT_TO) && !$this->_Message->hasRecipients(MAIL_RECIPIENT_CC) && !$this->_Message->hasRecipients(MAIL_RECIPIENT_BCC)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MAIL_EMPTY_RCPT'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Valdiate transport parameters
	 *
	 * @param int $type Transport type
	 * @param array &$params Transport parameters
	 * @access private
	 * @return bool
	 */
	function _validateParams($type, &$params) {
		switch($type) {
			case MAIL_TRANSPORT_MAIL :
				return TRUE;
			case MAIL_TRANSPORT_SENDMAIL :
				return (isset($params['sendmail']));
			case MAIL_TRANSPORT_SMTP :
				return (isset($params['server']));
			default :
				return FALSE;
		}

	}

	/**
	 * Serializes all message headers
	 *
	 * @access private
	 * @return string
	 */
	function _getMessageHeaders() {
		$headers = '';
		foreach($this->_Message->headers as $name => $value) {
			$headers .= sprintf("%s: %s", $name, $value);
		}
		return $headers;
	}
}
?>