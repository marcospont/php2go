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

import('php2go.datetime.Date');
import('php2go.net.HttpRequest');
import('php2go.net.MailPart');
import('php2go.net.MailTransport');
import('php2go.net.MimeType');
import('php2go.template.Template');
import('php2go.text.StringUtils');

/**
 * Recipient type "To"
 */
define('MAIL_RECIPIENT_TO', 1);
/**
 * Recipient type "Cc"
 */
define('MAIL_RECIPIENT_CC', 2);
/**
 * Recipient type "Bcc"
 */
define('MAIL_RECIPIENT_BCC', 3);
/**
 * Recipient type "Reply-to"
 */
define('MAIL_RECIPIENT_REPLYTO', 4);

/**
 * Builds MIME mail messages, according to RFC822
 *
 * Example:
 * <code>
 * $msg = new MailMessage();
 * $msg->setSubject('Hello there!');
 * $msg->setFrom('john@foo.com', 'John');
 * $msg->setConfirmReading();
 * $msg->useMicrosoftHeaders(TRUE);
 * $msg->addTo('paul@bar.org', 'Paul');
 * $msg->addCc('mary@baz.org', 'Mary');
 * $msg->addBcc('anna@xpto.com', 'Anna');
 * $msg->setHtmlBody('
 *   <html><body>
 *     <table><tr><td>
 *       This is HTML mail!
 *     </td></tr></table>
 *   </body></html>');
 * $msg->build();
 * $transp =& $msg->getTransport();
 * $transp->setType(MAIL_TRANSPORT_SMTP, array('server'=>'foo.com', 'port'=>25));
 * if ($transp->send()) {
 *   print 'Message successfully sent!';
 * } else {
 *   print 'Error sending message: ' . $transp->getErrorMessage();
 * }
 * </code>
 *
 * @package net
 * @uses Environment
 * @uses HttpRequest
 * @uses MailPart
 * @uses MailTransport
 * @uses MimeType
 * @uses StringUtils
 * @uses Template
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class MailMessage extends PHP2Go
{
	/**
	 * Message charset
	 *
	 * @var string
	 */
	var $charset;

	/**
	 * Message content type
	 *
	 * @var string
	 */
	var $contentType;

	/**
	 * Message content encoding
	 *
	 * @var string
	 */
	var $contentEncoding;

	/**
	 * Message subject
	 *
	 * @var string
	 */
	var $subject = '';

	/**
	 * Sender's address
	 *
	 * @var string
	 */
	var $from;

	/**
	 * Sender's name
	 *
	 * @var string
	 */
	var $fromName;

	/**
	 * List of "To" recipients
	 *
	 * @var array
	 */
	var $to = array();

	/**
	 * List of "Cc" recipients
	 *
	 * @var array
	 */
	var $cc = array();

	/**
	 * List of "Bcc" recipients
	 *
	 * @var array
	 */
	var $bcc = array();

	/**
	 * List of "Reply-to" recipients
	 *
	 * @var array
	 */
	var $replyto = array();

	/**
	 * Return receipt address
	 *
	 * @var string
	 */
	var $confirmReading;

	/**
	 * Message headers
	 *
	 * @var array
	 */
	var $headers = array();

	/**
	 * Customized headers
	 *
	 * @var array
	 */
	var $customHeaders = array();

	/**
	 * Whether Microsoft headers should be sent
	 *
	 * @var bool
	 */
	var $msHeaders = FALSE;

	/**
	 * Message priority
	 *
	 * @var int
	 */
	var $priority;

	/**
	 * Message text body
	 *
	 * @var string
	 */
	var $textBody;

	/**
	 * Message HTML body
	 *
	 * @var string
	 */
	var $htmlBody;

	/**
	 * Line wrap
	 *
	 * @var int
	 */
	var $wordWrap;

	/**
	 * Message attachments
	 *
	 * @var array
	 */
	var $attachments = array();

	/**
	 * Message embedded files
	 *
	 * @var array
	 */
	var $embeddedFiles = array();

	/**
	 * Message host name
	 *
	 * @var string
	 */
	var $hostName;

	/**
	 * Detected mail type
	 *
	 * The mail type can assume one of the following values:
	 * plan, related, mixed or alternative. This type depends
	 * on the contents of the message (attachments, embedded
	 * files, HTML body, ...).
	 *
	 * @var string
	 * @access private
	 */
	var $mailType;

	/**
	 * Final message body, containing
	 * all headers and parts
	 *
	 * @var string
	 * @access private
	 */
	var $body;

	/**
	 * Line end characters
	 *
	 * @var string
	 * @access private
	 */
	var $lineEnd;

	/**
	 * Message X-Mailer
	 *
	 * @var string
	 * @access private
	 */
	var $xMailer;

	/**
	 * Indicates that the message has already been built
	 *
	 * @var bool
	 * @access private
	 */
	var $built;

	/**
	 * Class constructor
	 *
	 * @return MailMessage
	 */
	function MailMessage() {
		parent::PHP2Go();
		$this->charset = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->contentType = 'text/plain';
		$this->contentEncoding = '8bit';
		$this->from = 'root@localhost';
		$this->fromName = '';
		$this->confirmReading = '';
		$this->subject = '';
		$this->priority = 3;
		$this->body = '';
		$this->textBody = '';
		$this->htmlBody = '';
		$this->wordWrap = 50;
		$this->lineEnd = "\n";
		$this->hostName = Environment::has('SERVER_NAME') ? Environment::get('SERVER_NAME') : 'localhost.localdomain';
		$this->xMailer = "PHP2Go Mail Transporter";
		$this->uniqueId = md5(uniqid(time()));
		$this->built = FALSE;
	}

	/**
	 * Get message's charset
	 *
	 * @return string
	 */
	function getCharset() {
		return $this->charset;
	}

	/**
	 * Set message's charset
	 *
	 * @param string $charset
	 */
	function setCharset($charset) {
		$this->charset = $charset;
	}

	/**
	 * Get message's content type
	 *
	 * Content type can't be overriden because it's determined,
	 * according to the message parts.
	 *
	 * @return string
	 */
	function getContentType() {
		return $this->contentType;
	}

	/**
	 * Get message's content encoding
	 *
	 * @return string
	 */
	function getEncoding() {
		return $this->contentEncoding;
	}

	/**
	 * Set message's content encoding
	 *
	 * @param string $encoding Encoding
	 */
	function setEncoding($encoding) {
		$this->contentEncoding = $encoding;
	}

	/**
	 * Get sender's address
	 *
	 * @return string
	 */
	function getFrom() {
		return $this->from;
	}

	/**
	 * Get sender's name
	 *
	 * @return string
	 */
	function getFromName() {
		return $this->fromName;
	}

	/**
	 * Set message sender
	 *
	 * @param string $address Sender's email
	 * @param string $name Sender's name
	 */
	function setFrom($address, $name='') {
		$this->from = $address;
		if (!empty($name)) {
			$this->fromName = $name;
		}
	}

	/**
	 * Get message's subject
	 *
	 * @return string
	 */
	function getSubject() {
		return $this->subject;
	}

	/**
	 * Set message's subject
	 *
	 * @param string $subject New subject
	 */
	function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * Get the recipients of a given type
	 *
	 * Available recipient types: {@link MAIL_RECIPIENT_TO},
	 * {@link MAIL_RECIPIENT_CC}, {@link MAIL_RECIPIENT_BCC}
	 * and {@link MAIL_RECIPIENT_REPLYTO}.
	 *
	 * @param int $recipientType Recipient type
	 * @return array|NULL
	 */
	function getRecipients($recipientType) {
		switch($recipientType) {
			case MAIL_RECIPIENT_TO :
				return $this->to;
			case MAIL_RECIPIENT_CC :
				return $this->cc;
			case MAIL_RECIPIENT_BCC :
				return $this->bcc;
			case MAIL_RECIPIENT_REPLYTO :
				return $this->replyto;
			default :
				return NULL;
		}
	}

	/**
	 * Check if the message has recipients of a given type
	 *
	 * @param int $recipientType Recipient type
	 * @return bool
	 */
	function hasRecipients($recipientType) {
		switch($recipientType) {
			case MAIL_RECIPIENT_TO :
				return (count($this->to) > 0);
			case MAIL_RECIPIENT_CC :
				return (count($this->cc) > 0);
			case MAIL_RECIPIENT_BCC :
				return (count($this->bcc) > 0);
			case MAIL_RECIPIENT_REPLYTO :
				return (count($this->replyto) > 0);
			default :
				return FALSE;
		}
	}

	/**
	 * Add a "To" recipient
	 *
	 * @param string $address E-mail address
	 * @param string $name Name
	 */
	function addTo($address, $name = '') {
		$this->addRecipient(MAIL_RECIPIENT_TO, $address, $name);
	}

	/**
	 * Add a "Cc" recipient
	 *
	 * @param string $address E-mail address
	 * @param string $name Name
	 */
	function addCc($address, $name = '') {
		$this->addRecipient(MAIL_RECIPIENT_CC, $address, $name);
	}

	/**
	 * Add a "Bcc" recipient
	 *
	 * @param string $address E-mail address
	 * @param string $name Name
	 */
	function addBcc($address, $name = '') {
		$this->addRecipient(MAIL_RECIPIENT_BCC, $address, $name);
	}

	/**
	 * Add a "Reply-to" recipient
	 *
	 * @param string $address E-mail address
	 * @param string $name Name
	 */
	function addReplyTo($address, $name = '') {
		$this->addRecipient(MAIL_RECIPIENT_REPLYTO, $address, $name);
	}

	/**
	 * Generic method to add recipients
	 *
	 * Available recipient types: {@link MAIL_RECIPIENT_TO},
	 * {@link MAIL_RECIPIENT_CC}, {@link MAIL_RECIPIENT_BCC}
	 * and {@link MAIL_RECIPIENT_REPLYTO}.
	 *
	 * @param int $recipientType Type
	 * @param string $address E-mail address
	 * @param string $name Name
	 */
	function addRecipient($recipientType, $address, $name) {
		switch ($recipientType) {
			case MAIL_RECIPIENT_TO :
				array_push($this->to, array($address, $name));
				break;
			case MAIL_RECIPIENT_CC :
				array_push($this->cc, array($address, $name));
				break;
			case MAIL_RECIPIENT_BCC :
				array_push($this->bcc, array($address, $name));
				break;
			case MAIL_RECIPIENT_REPLYTO :
				array_push($this->replyto, array($address, $name));
				break;
		}
	}

	/**
	 * Add a set of recipients of a given type
	 *
	 * Available recipient types: {@link MAIL_RECIPIENT_TO},
	 * {@link MAIL_RECIPIENT_CC}, {@link MAIL_RECIPIENT_BCC}
	 * and {@link MAIL_RECIPIENT_REPLYTO}.
	 *
	 * @param int $recipientType Recipient type
	 * @param array $recipients Recipients
	 */
	function addRecipientList($recipientType, $recipients) {
		if (is_array($recipients)) {
			foreach($recipients as $recipient) {
				$recipient = (array)$recipient;
				$this->addRecipient($recipientType, $recipient[0], (isset($recipient[1])) ? $recipient[1] : '');
			}
		}
	}

	/**
	 * Clears the recipients of a given type
	 *
	 * @param int $recipientType Recipient type
	 */
	function clearRecipients($recipientType) {
		switch ($recipientType) {
			case MAIL_RECIPIENT_TO :
				$this->to = array();
				break;
			case MAIL_RECIPIENT_CC :
				$this->cc = array();
				break;
			case MAIL_RECIPIENT_BCC :
				$this->bcc = array();
				break;
			case MAIL_RECIPIENT_REPLYTO :
				$this->replyto = array();
				break;
		}
	}

	/**
	 * Clears all recipients
	 */
	function clearAllRecipients() {
		$this->to = array();
		$this->cc = array();
		$this->bcc = array();
		$this->replyto = array();
	}

	/**
	 * Set the return receipt address
	 *
	 * If $confirmAddress is missing, the sender address will be used.
	 *
	 * @param string $confirmAddress E-mail address
	 */
	function setConfirmReading($confirmAddress='') {
		if ($confirmAddress != '')
			$this->confirmReading = $confirmAddress;
		elseif (isset($this->from))
			$this->confirmReading = $this->from;
	}

	/**
	 * Remove one of the default message headers
	 *
	 * @param string $name Header name
	 */
	function removeHeader($name) {
		if (isset($this->headers[trim($name)]))
			unset($this->headers[trim($name)]);
	}

	/**
	 * Add a custom header
	 *
	 * @param string $name Name
	 * @param mixed $value Value
	 */
	function addCustomHeader($name, $value) {
		$this->customHeaders[] = array($name, $value);
	}

	/**
	 * Remove a custom header
	 *
	 * @param string $name Header name
	 */
	function removeCustomHeader($name) {
		$keys = array_keys($this->customHeaders);
		foreach ($keys as $hName) {
			if (trim($hName) == trim($name)) {
				unset($this->customHeaders[$hName]);
				break;
			}
		}
	}

	/**
	 * Enable/disable Microsoft headers
	 *
	 * @param bool $setting Enable/disable
	 */
	function useMicrosoftHeaders($setting=TRUE) {
		$this->msHeaders = TypeUtils::toBoolean($setting);
	}

	/**
	 * Set message's priority
	 *
	 * Priority values: 1 (high), 3 (medium) and 5 (low).
	 *
	 * @param int $priority Priority
	 */
	function setPriority($priority) {
		if ($priority == 1 || $priority == 3 || $priority == 5)
			$this->priority = $priority;
	}

	/**
	 * Check if the message has any attachments
	 *
	 * @return bool
	 */
	function hasAttachments() {
		return sizeof($this->attachments) > 0;
	}

	/**
	 * Adds an attachment
	 *
	 * Example:
	 * <code>
	 * $msg->addAttachment('files/terms_and_conditions.pdf');
	 * </code>
	 *
	 * @param string $fileName File path
	 * @param string $encoding Content encoding. Defaults to base64
	 * @param string $mimeType MIME type. Auto detected from file extension if missing
	 */
	function addAttachment($fileName, $encoding='base64', $mimeType='') {
		$Part = new MailPart();
		$Part->setContentType(empty($mimeType) ? MimeType::getFromFileName($fileName) : $mimeType);
		$Part->setEncoding($encoding);
		$Part->setDisposition('attachment');
		$Part->setFileName($fileName);
		$Part->encodeContent();
		$this->attachments[] =& $Part;
	}

	/**
	 * Removes all attachments
	 */
	function clearAttachments() {
		$this->attachments = array();
	}

	/**
	 * Check if the message has any embedded files
	 *
	 * @return bool
	 */
	function hasEmbeddedFiles() {
		return sizeof($this->embeddedFiles) > 0;
	}

	/**
	 * Adds an embedded file
	 *
	 * An embedded file is a message part whose content is
	 * embedded in the message's body. Example: images.
	 *
	 * Example:
	 * <code>
	 * $msg->setHtmlBody('<html><body><img src='cid:logo' border=0><br>Hello World!</body></html>');
	 * $msg->addEmbeddedFile('images/logo.gif', 'logo');
	 * </code>
	 *
	 * @param string $fileName File path
	 * @param string $cid Content ID
	 * @param string $encoding Content encoding. Defaults to base64
	 * @param string $mimeType MIME type. Auto detected from file extension if missing
	 */
	function addEmbeddedFile($fileName, $cid, $encoding='base64', $mimeType='') {
		$Part = new MailPart();
		$Part->setContentType(empty($mimeType) ? MimeType::getFromFileName($fileName) : $mimeType);
		$Part->setContentId($cid);
		$Part->setEncoding($encoding);
		$Part->setDisposition('inline');
		$Part->setFileName($fileName);
		$Part->encodeContent();
		$this->embeddedFiles[] =& $Part;
	}

	/**
	 * Removes all embedded files
	 */
	function clearEmbeddedFiles() {
		$this->embeddedFiles = array();
	}

	/**
	 * Set the text body of the message
	 *
	 * When the message uses text and HTML parts, in most e-mail
	 * clients, the HTML part will take precedence. However,
	 * text/plain parts are useful when messages are being sent
	 * to recipients without visual clients or in scenarios
	 * where bandwidth should be saved.
	 *
	 * When {@link setHtmlBody} is called and the message doesn't
	 * have a text body, the text body is populated with the HTML
	 * body without tags and unnecessary spaces.
	 *
	 * @param string $textBody Text body
	 */
	function setTextBody($textBody) {
		$this->textBody = $textBody;
	}

	/**
	 * Set the HTML body of the message
	 *
	 * @param string $htmlBody HTML body
	 */
	function setHtmlBody($htmlBody) {
		$this->htmlBody = $htmlBody;
		$this->contentType = 'text/html';
	}

	/**
	 * Set the HTML body of the message based
	 * on an HTML template
	 *
	 * @param string $templateFile Template file
	 * @param array $templateVars Template variables
	 * @param array $templateIncludes Template includes
	 */
	function setHtmlTemplate($templateFile, $templateVars=array(), $templateIncludes=array()) {
		$Template = new Template($templateFile);
		if (TypeUtils::isHashArray($templateIncludes) && !empty($templateIncludes)) {
			foreach ($templateIncludes as $blockName => $value) {
				$Template->includeAssign($blockName, $value);
			}
		}
		$Template->parse();
		if (TypeUtils::isArray($templateVars) && !empty($templateVars)) {
			$Template->assign($templateVars);
		}
		$this->setHtmlBody($Template->getContent());
	}

	/**
	 * Set the word wrap for the message's body
	 *
	 * @param int $wrap Word wrap
	 */
	function setWordWrap($wrap) {
		if ($wrap >= 1)
			$this->wordWrap = $wrap;
	}

	/**
	 * Set message's hostname
	 *
	 * The hostname defaults to the SERVER_NAME server variable, when available.
	 * Otherwise, 'localhost.localdomain' is used.
	 *
	 * @param string $hostName
	 */
	function setHostName($hostName) {
		$this->hostName = $hostName;
	}

	/**
	 * Reset all message's properties
	 */
	function reset() {
		$this->body = '';
		$this->textBody = '';
		$this->htmlBody = '';
		$this->headers = array();
		$this->customHeaders = array();
		$this->clearAllRecipients();
		$this->clearAttachments();
		$this->clearEmbeddedFiles();
		$this->built = FALSE;
	}

	/**
	 * Prepares the message to be sent
	 */
	function build() {
		$this->_defineMessageType();
		$this->_buildHeaders();
		$this->_buildBody();
		$this->built = TRUE;
	}

	/**
	 * Create an instance of the MailTransport class,
	 * to be used to send the message
	 *
	 * @return MailTransport
	 */
	function &getTransport() {
		static $Transport;
		if (!isset($Transport))
			$Transport = new MailTransport($this);
		else
			$Transport->setMessage($this);
		return $Transport;
	}

	/**
	 * Define the message type based on the use of HTML,
	 * attachments and embedded files
	 *
	 * @access private
	 */
	function _defineMessageType() {
		if (!$this->hasAttachments() && !$this->hasEmbeddedFiles() && empty($this->htmlBody)) {
			$this->mailType = 'plain';
		} else {
			if (empty($this->textBody)) {
				$matches = array();
				if (eregi("<body[^>]+>(.*)<\/body>", $this->htmlBody, $matches)) {
					$tmp = eregi_replace("<style.*></style>", "", $matches[1]);
				} else {
					$tmp = eregi_replace("<style.*></style>", "", $this->htmlBody);
				}
				$tmp = trim(strip_tags(eregi_replace("<br>", "\n", $tmp)), "\x00..\x2f\x7f..\xff");
				$this->textBody = ereg_replace("[[:blank:]]{1,}", " ", ereg_replace("\x0D\x0A", "\x0A", $tmp));
			}
			if ($this->hasAttachments() || $this->hasEmbeddedFiles()) {
				if ($this->hasEmbeddedFiles()) {
					$this->contentType = 'multipart/related';
					$this->mailType = 'related';
				} else {
					$this->contentType = 'multipart/mixed';
					$this->mailType = 'mixed';
				}
			}
			if (!empty($this->textBody) && !$this->hasAttachments() && !$this->hasEmbeddedFiles()) {
				$this->contentType = 'multipart/alternative';
				$this->mailType = 'alternative';
			}
		}
	}

	/**
	 * Build message headers
	 *
	 * @uses Date::formatTime()
	 * @access private
	 */
	function _buildHeaders() {
		$this->_buildReceived();
		$this->_addHeader('Date', Date::formatTime(time(), DATE_FORMAT_RFC822) . $this->lineEnd);
		$uniqueId = md5(uniqid(time()));
		$this->_addHeader('Message-ID', '<' . $uniqueId . '@' . $this->hostName . '>' . $this->lineEnd);
		$this->_addHeader('From', $this->_formatAddress(array(trim($this->from), trim($this->fromName))) . $this->lineEnd);
		if ($this->hasRecipients(MAIL_RECIPIENT_TO))
			$this->_addHeader('To', $this->_buildAddressList(MAIL_RECIPIENT_TO));
		elseif (!$this->hasRecipients(MAIL_RECIPIENT_CC))
			$this->_addHeader('To', 'undisclosed-recipients:;' . $this->lineEnd);
		if ($this->hasRecipients(MAIL_RECIPIENT_CC))
			$this->_addHeader('Cc', $this->_buildAddressList(MAIL_RECIPIENT_CC));
		if ($this->hasRecipients(MAIL_RECIPIENT_REPLYTO))
			$this->_addHeader('Reply-to', $this->_buildAddressList(MAIL_RECIPIENT_REPLYTO));
		$this->_addHeader('Subject', $this->_encodeHeader(trim($this->subject)) . $this->lineEnd);
		$this->_addHeader('MIME-Version', '1.0' . $this->lineEnd);
		$this->_addHeader('X-Priority', $this->priority . $this->lineEnd);
		$this->_addHeader('X-Mailer', $this->xMailer . ' (version ' . PHP2GO_VERSION . ')' . $this->lineEnd);
		$this->_addHeader('Return-Path', trim($this->from) . $this->lineEnd);
		if (!empty($this->confirmReading))
			$this->_addHeader('Disposition-Notification-To', '<' . trim($this->confirmReading) . '>' . $this->lineEnd);
		if (!empty($this->customHeaders))
			for ($i=0; $i<sizeof($this->customHeaders); $i++)
				$this->_addHeader(trim($this->customHeaders[$i][0]), $this->_encodeHeader(trim($this->customHeaders[$i][1])) . $this->lineEnd);
		if ($this->msHeaders) {
			if ($this->priority == 1)
				$msPriority = 'High';
			elseif ($this->priority == 5)
				$msPriority = 'Low';
			else
				$msPriority = 'Medium';
			$this->_addHeader('X-MSMail-Priority', $msPriority . $this->lineEnd);
			$this->_addHeader('Importante', $msPriority . $this->lineEnd);
		}
		switch($this->mailType) {
			case 'plain' :
				$this->_addHeader('Content-Transfer-Encoding', $this->contentEncoding . $this->lineEnd);
				$this->_addHeader('Content-Type', $this->contentType . "; charset=\"" . $this->charset . "\"");
				break;
			case 'related' :
				$this->_addHeader('Content-Type', $this->contentType . ';' . $this->lineEnd . "\ttype=\"text/html\";" . $this->lineEnd . "\tboundary=\"" . $this->_getMimeBoundary('rel') . "\"" . $this->lineEnd);
				break;
			case 'mixed' :
				$this->_addHeader('Content-Type', $this->contentType . ';' . $this->lineEnd . "\tboundary=\"" . $this->_getMimeBoundary('mix') . "\"" . $this->lineEnd);
				break;
			case 'alternative' :
				$this->_addHeader('Content-Type', $this->contentType . ';' . $this->lineEnd . "\tboundary=\"" . $this->_getMimeBoundary('alt') . "\"" . $this->lineEnd);
				break;
			default :
				return;
		}
	}

	/**
	 * Build the message body
	 *
	 * Based on the {@link textBody} and {@link htmlBody} properties, creates
	 * and builds text/plain and text/html parts, when applicable.
	 *
	 * Builds and renders all mail parts representing attachments and
	 * embedded files.
	 *
	 * @uses StringUtils::wrap()
	 * @access private
	 */
	function _buildBody() {
		$this->body = '';
		if (!empty($this->htmlBody) && $this->wordWrap > 0)
			$this->htmlBody = StringUtils::wrap($this->htmlBody, $this->wordWrap, $this->lineEnd);
		if (!empty($this->textBody) && $this->wordWrap > 0)
			$this->textBody = StringUtils::wrap($this->textBody, $this->wordWrap, $this->lineEnd);
		if ($this->mailType == 'plain') {
			$this->body .= $this->lineEnd . $this->lineEnd . StringUtils::encode($this->textBody, $this->contentEncoding);
		} else {
			$this->body .= sprintf("%sThis is a multi-part message in MIME format.%s", $this->lineEnd, $this->lineEnd . $this->lineEnd);
			// mixed/relative types
			if ($this->mailType == 'mixed') {
				// mixed
				$this->body .= sprintf("--%s%s", $this->_getMimeBoundary('mix'), $this->lineEnd);
				// alternative
				$this->body .= sprintf("Content-Type: %s;%s\tboundary=\"%s\"%s", 'multipart/alternative', $this->lineEnd, $this->_getMimeBoundary('alt'), $this->lineEnd . $this->lineEnd);
			} else if ($this->mailType == 'related') {
				// related
				$this->body .= sprintf("--%s%s", $this->_getMimeBoundary('rel'), $this->lineEnd);
				// alternative
				$this->body .= sprintf("Content-Type: %s;%s\tboundary=\"%s\"%s", 'multipart/alternative', $this->lineEnd, $this->_getMimeBoundary('alt'), $this->lineEnd . $this->lineEnd);
			}
			// text part
			if (!empty($this->textBody)) {
				$TextBody = new MailPart();
				$TextBody->setBoundaryId($this->_getMimeBoundary('alt'));
				$TextBody->setCharset($this->getCharset());
				$TextBody->setEncoding($this->contentEncoding);
				$TextBody->setContent($this->textBody);
				$TextBody->encodeContent();
				$this->body .= $TextBody->buildSource();
				$this->body .= $this->lineEnd . $this->lineEnd;
			}
			// html part
			if (!empty($this->htmlBody)) {
				$HtmlBody = new MailPart();
				$HtmlBody->setBoundaryId($this->_getMimeBoundary('alt'));
				$HtmlBody->setCharset($this->getCharset());
				$HtmlBody->setContentType('text/html');
				$HtmlBody->setEncoding($this->contentEncoding);
				if ($this->hasEmbeddedFiles())
					$this->htmlBody = eregi_replace("\'([ ]?cid[ ]?:.+)\'", "\"\\1\"", $this->htmlBody);
				$HtmlBody->setContent($this->htmlBody);
				$HtmlBody->encodeContent();
				$this->body .= $HtmlBody->buildSource();
				$this->body .= $this->lineEnd . $this->lineEnd;
			}
			// alternative boundary
			$this->body .= sprintf("%s--%s--%s", $this->lineEnd, $this->_getMimeBoundary('alt'), $this->lineEnd . $this->lineEnd);
			// attachments
			if ($this->hasAttachments()) {
				foreach($this->attachments as $attachment) {
					if ($this->mailType == 'mixed')
						$attachment->setBoundaryId($this->_getMimeBoundary('mix'));
					else
						$attachment->setBoundaryId($this->_getMimeBoundary('rel'));
					$this->body .= $attachment->buildSource();
				}
			}
			// embedded files
			if ($this->hasEmbeddedFiles()) {
				foreach($this->embeddedFiles as $embedded) {
					$embedded->setBoundaryId($this->_getMimeBoundary('rel'));
					$this->body .= $embedded->buildSource();
				}
			}
			if ($this->mailType == 'mixed') {
				// mixed boundary
				$this->body .= sprintf("%s--%s--%s", $this->lineEnd, $this->_getMimeBoundary('mix'), $this->lineEnd . $this->lineEnd);
			} else if ($this->mailType == 'related') {
				// related boundary
				$this->body .= sprintf("%s--%s--%s", $this->lineEnd, $this->_getMimeBoundary('rel'), $this->lineEnd . $this->lineEnd);
			}
		}
	}

	/**
	 * Register a message header
	 *
	 * @param string $name Name
	 * @param string $value Value
	 * @access private
	 */
	function _addHeader($name, $value) {
		$this->headers[$name] = $value;
	}

	/**
	 * Serialize all recipients of a given type to build a message header
	 *
	 * @param int $recipientType Recipient type
	 * @access private
	 * @return string
	 */
	function _buildAddressList($recipientType) {
		switch($recipientType) {
			case MAIL_RECIPIENT_TO :
				$list =& $this->to;
				break;
			case MAIL_RECIPIENT_CC :
				$list =& $this->cc;
				break;
			case MAIL_RECIPIENT_BCC :
				$list =& $this->bcc;
				break;
			case MAIL_RECIPIENT_REPLYTO :
				$list =& $this->replyto;
				break;
			default :
				return '';
		}
		$addressList = $this->_formatAddress($list[0]);
		$listSize = sizeof($list);
		if ($listSize > 1) {
			for ($i=1; $i<$listSize; $i++)
				$addressList .= sprintf(", %s", $this->_formatAddress($list[$i]));
		}
		$addressList .= $this->lineEnd;
		return $addressList;
	}

	/**
	 * Builds the 'Received' header
	 *
	 * @uses Date::formatTime()
	 * @uses HttpRequest::protocol()
	 * @uses HttpRequest::remoteAddress()
	 * @access private
	 */
	function _buildReceived() {
		if (Environment::get('SERVER_NAME')) {
			$protocol = HttpRequest::protocol();
			$remote = Environment::get('HTTP_HOST');
			if ($remote == '')
				$remote = 'PHP2Go';
			$remote .= ' ([' . HttpRequest::remoteAddress() . '])';
		} else {
			$protocol = 'local';
			$remote = Environment::has('USER') ? Environment::get('USER') : 'PHP2Go';
		}
        $str = sprintf(
			"from %s %s\tby %s with %s (%s);%s\t%s%s",
			$remote, $this->lineEnd, $this->hostName, $protocol, $this->xMailer,
			$this->lineEnd, Date::formatTime(time(), DATE_FORMAT_RFC822), $this->lineEnd
		);
		$this->_addHeader('Received', $str);
	}

	/**
	 * Formats a recipient address
	 *
	 * @param array $address Array containing e-mail address and name
	 * @access private
	 * @return string
	 */
	function _formatAddress($address) {
		if (!isset($address[1]) || empty($address[1]))
			$formatted = $address[0];
		else
			$formatted = sprintf('%s %s', $this->_encodeHeader($address[1], 'phrase'), '<' . $address[0] . '>');
        return $formatted;
	}

	/**
	 * Encodes a message header
	 *
	 * @author Brent R. Matzelle <bmatzelle@yahoo.com>
	 * @uses StringUtils::wrap()
	 * @param string $content Header value
	 * @param string $type Encoding type
	 * @return string Encoded header
	 * @access private
	 */
	function _encodeHeader($content, $type='text') {
		$search = 0;
		$matches = array();
		switch (strtolower($type)) {
			case 'phrase':
				if (preg_match_all('/[\200-\377]/', $content, $matches) == 0) {
					$encoded = addcslashes($content, '\000-\037\177');
					$encoded = preg_replace('/([\"])/', '\\"', $encoded);
					if ($content == $encoded && preg_match_all('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $content, $matches) == 0)
						return ($encoded);
					else
						return "\"$encoded\"";
				}
				$search = preg_match_all('/[^\040\041\043-\133\135-\176]/', $content, $matches);
				break;
			case 'comment':
				$search = preg_match_all('/[()"]/', $content, $matches);
				break;
			case 'text':
			default:
				$search += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $content, $matches);
				break;
		}
		if ($search == 0)
			return ($content);
		$maxLength = 68 - strlen($this->getCharset());
		if (strlen($content)/3 < $search) {
			$encoding = 'B';
			$encoded = base64_encode($content);
			$maxLength -= $maxLength % 4;
			$encoded = trim(chunk_split($encoded, $maxLength, "\n"));
		} else {
			$encoding = 'Q';
			$encoded = $this->_encodeQuoted($content, $type);
			$encoded = StringUtils::wrap($encoded, $maxLength);
			$encoded = str_replace("=" . $this->lineEnd, "\n", trim($encoded));
		}
		$encoded = preg_replace('/^(.*)$/m', " =?" . $this->getCharset() . "?$encoding?\\1?=", $encoded);
		$encoded = trim(str_replace("\n", $this->lineEnd, $encoded));
		return($encoded);
	}

	/**
	 * Encodes a string using the quoted-printable encoding
	 *
	 * @author Brent R. Matzelle <bmatzelle@yahoo.com>
	 * @param string $content Input string
	 * @param string $type Message type: 'phrase', 'comment' or 'text'
	 * @return string Encoded string
	 * @access private
	 */
	function _encodeQuoted($content, $type='text') {
		$encoded = preg_replace("[\r\n]", '', $content);
		switch (strtolower($type)) {
			case 'phrase' :
				$encoded = preg_replace("/([^A-Za-z0-9!*+\/ -])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
				break;
			case 'comment' :
				$encoded = preg_replace("/([\(\)\"])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
				break;
			case 'text' :
			default :
				$encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e', "'='.sprintf('%02X', ord('\\1'))", $encoded);
				break;
		}
		$encoded = str_replace(' ', '_', $encoded);
		return $encoded;
	}

	/**
	 * Get a MIME boundary
	 *
	 * @param string $type Type
	 * @access private
	 * @return string
	 */
	function _getMimeBoundary($type) {
		static $alt;
		static $rel;
		static $mix;
		if (isset($$type)) {
			return $$type;
		} else if ($type == 'alt' || $type == 'rel' || $type == 'mix') {
			$$type = '----=_NextPart' . date( 'YmdHis' ) . '_' . mt_rand(10000, 99999);
			return $$type;
		} else {
			return FALSE;
		}
	}
}
?>