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

import('php2go.net.MailMessage');

/**
 * Build and send MIME messages signed by GnuPG
 *
 * This class works only under Unix systems.
 *
 * @package net
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class SignedMailMessage extends MailMessage
{
	/**
	 * Key pair's owner
	 *
	 * @var string
	 */
	var $keyName;

	/**
	 * Path to the key pair
	 *
	 * @var string
	 */
	var $keyPath;

	/**
	 * Path to the GnuPG software
	 *
	 * @var string
	 */
	var $gnuPgPath = '/usr/bin/gpg';

	/**
	 * Last error message
	 *
	 * @var string
	 */
	var $errorMsg;

	/**
	 * Template of the GnuPG command
	 *
	 * @var string
	 * @access private
	 */
	var $commandTemplate;

	/**
	 * Class constructor
	 *
	 * @return SignedMailMessage
	 */
	function SignedMailMessage() {
		parent::MailMessage();
		if (System::isWindows())
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_RUN_ON_WINDOWS', parent::getClassName()), E_USER_ERROR, __FILE__, __LINE__);
		$this->commandTemplate = "echo \"%s\" | \"%s\" 2>&1 --batch --no-secmem-warning --armor --sign -u \"%s\" --default-key \"%s\" ";
	}

	/**
	 * Set the key pair's owner
	 *
	 * The $keyName argument must be provided in the format "Name <email>",
	 * like the example above:
	 * <code>
	 * $mail->setKeyName("Foo <foo@foo.org>");
	 * </code>
	 *
	 * If setKeyName is not called, the message sender will be used
	 * as the owner of the key pair.
	 *
	 * @param string $keyName Key pair's owner
	 */
	function setKeyName($keyName) {
		$this->keyName = $keyName;
	}

	/**
	 * Set the path to the key pair
	 *
	 * @param string $keyPath Key pair's path
	 */
	function setKeyPath($keyPath) {
		$this->keyPath = $keyPath;
	}

	/**
	 * Set the path to the GnuPG software
	 *
	 * @param string $path GnuPG path
	 */
	function setGnuPGPath($path) {
		$this->gnuPgPath = $path;
	}

	/**
	 * Overrides parent class implementation to sign
	 * the message contents using the GnuPG software
	 * and the provided key pair
	 *
	 * @return bool
	 */
	function build() {
		parent::build();
		if (!isset($this->keyName))
			$this->keyName = isset($this->fromName) ? $this->fromName . " <" . $this->from . ">" : $this->from;
		if (!isset($this->keyPath)) {
			$this->build = FALSE;
			return FALSE;
		} else {
			$this->_buildCommandString();
			if ($this->_signMessage())
				return TRUE;
			else {
				$this->built = FALSE;
				return FALSE;
			}
		}
	}

	/**
	 * Build the command string that signs the message
	 *
	 * @access private
	 * @return string
	 */
	function _buildCommandString() {
		$commandString = sprintf($this->commandTemplate, str_replace("\n", "\r\n", $this->body), $this->gnuPgPath, $this->keyName, $this->keyName);
		if (parent::hasRecipients(MAIL_RECIPIENT_TO))
			for ($i=0; $i<sizeof($this->to); $i++)
				$commandString .= " -r \"" . parent::formatAddress($this->to[$i]) . "\"";
		if (parent::hasRecipients(MAIL_RECIPIENT_CC))
			for ($i=0; $i<sizeof($this->cc); $i++)
				$commandString .= " -r \"" . parent::formatAddress($this->cc[$i]) . "\"";
		if (parent::hasRecipients(MAIL_RECIPIENT_BCC))
			for ($i=0; $i<sizeof($this->bcc); $i++)
				$commandString .= " -r \"" . parent::formatAddress($this->bcc[$i]) . "\"";
		return $commandString;
	}

	/**
	 * Sign the message body already built by the parent class, based
	 * on the arguments provided to the class (key path, key owner,
	 * GnuPG path)
	 *
	 * @access private
	 * @return bool
	 */
	function _signMessage() {
		$oldHome = @getenv('HOME');
		if (substr($this->keyPath, -1) == '/')
			$this->keyPath = substr($this->keyPath, 0, -1);
		putenv("HOME=$this->keyPath");
		$commandString = $this->_buildCommandString();
		$encryptedContent = '';
		exec($commandString, $encryptedContent, $errorCode);
		putenv("HOME=$oldHome");
		$message = implode("\r\n", $encryptedContent);
		if(ereg("-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----",$message)) {
			$this->body = $this->lineEnd . $this->lineEnd . $message;
			return TRUE;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SIGNED_MESSAGE_SIGN', $message), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
	}
}
?>