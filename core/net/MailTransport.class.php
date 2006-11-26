<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/core/net/MailTransport.class.php,v 1.19 2006/10/11 22:05:23 mpont Exp $
// $Date: 2006/10/11 22:05:23 $

//------------------------------------------------------------------
import('php2go.net.Smtp');
//------------------------------------------------------------------

// @const	MAIL_TRANSPORT_MAIL		"1"
// Representa o envio de mensagens utilizando mail() padrão PHP
define('MAIL_TRANSPORT_MAIL', 1);
// @const	MAIL_TRANSPORT_SENDMAIL	"2"
// Representa o envio de mensagens utilizando o programa sendmail
define('MAIL_TRANSPORT_SENDMAIL', 2);
// @const	MAIL_TRANSPORT_SMTP		"3"
// Representa o envio de mensagens utilizando um servidor SMTP
define('MAIL_TRANSPORT_SMTP', 3);

//!-----------------------------------------------------------------
// @class		MailTransport
// @desc		Classe responsável por processar o envio de uma mensagem
//				MIME construída pela classe MailMessage utilizando um dos 
//				métodos disponíveis: mail padrão PHP, sendmail ou SMTP
// @package		php2go.net
// @uses		Smtp
// @uses		TypeUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.19 $
//!-----------------------------------------------------------------
class MailTransport extends PHP2Go
{
	var $type;			// @var type int					Método utilizado para envio da mensagem
	var $params;		// @var params array				Vetor associativo de parâmetros para o envio da mensagem
	var $errorMessage;	// @var errorMessage string			Mensagem de erro encontrada no envio de uma mensagem
	var $_Message;		// @var _Message MailMessage object	Objeto MailMessage representando a mensagem já construída
	var $_Smtp;			// @var _Smtp Smtp object			Objeto de conexão com o servidor SMTP, para tipo de envio MAIL_TRANSPORT_SMTP
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::MailTransport
	// @desc		Construtor da classe, executado por padrão dentro do
	//				método MailMessage::getTransport. Valida a instância
	//				da classe MailMessage recebida
	// @access		public
	// @param		&MailMessage MailMessage object		Representa a mensagem a ser enviada
	//!-----------------------------------------------------------------
	function MailTransport(&$MailMessage) {
		parent::PHP2Go();
		if (!TypeUtils::isObject($MailMessage) || !is_a($MailMessage, 'mailmessage'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'MailMessage'), E_USER_ERROR, __FILE__, __LINE__);
		$this->errors = array();
		$this->_Message = $MailMessage;	
		parent::registerDestructor($this, '__destruct');
	}
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::_destruct
	// @desc		Destrutor da classe. Encerra a conexão com o servidor SMTP,
	//				se ela existir e estiver ativa
	// @access		public
	//!-----------------------------------------------------------------
	function __destruct() {
		if (isset($this->_Smtp))
			$this->_Smtp->quit();
	}
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::getType
	// @desc		Retorna o tipo de envio definido
	// @access		public	
	// @return		int
	//!-----------------------------------------------------------------
	function getType() {
		return $this->type;
	}
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::isType
	// @desc		Verifica se a classe está configurada para um determinado tipo de envio
	// @param		type int	Tipo de envio
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function isType($type) {
		return ($this->type == $type);
	}
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::setType
	// @desc		Configura o método de envio da mensagem
	// @param		type int		Tipo solicitado
	// @param		params array	"array()" Vetor de parâmetros para configuração do envio da mensagem
	// @note		Os tipos válidos são: MAIL_TRANSPORT_MAIL, MAIL_TRANSPORT_SENDMAIL e MAIL_TRANSPORT_SMTP
	// @note		Os parâmetros permitem a customização dos métodos de envio da mensagem
	// @note		Os parâmetros aceitos são: (os marcados com * são obrigatórios)
	//				MAIL : nenhum parâmetro
	//				SENDMAIL : sendmail*
	//				SMTP : server*, port, debug (TRUE|FALSE), timeout, username, password, helo	
	// @access		public	
	// @return		bool	
	//!-----------------------------------------------------------------
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
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::getErrorMessage
	// @desc		Retorna a mensagem de erro encontrada no momento do envio de uma mensagem
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getErrorMessage() {
		return $this->errorMessage;
	}
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::setMessage
	// @desc		Configura a classe para enviar outra mensagem
	// @param		&Message MailMessage object		Nova mensagem a ser enviada
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setMessage(&$MailMessage) {
		if (!TypeUtils::isObject($MailMessage) || !is_a($MailMessage, 'mailmessage'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'MailMessage'), E_USER_ERROR, __FILE__, __LINE__);
		$this->_Message =& $MailMessage;
	}
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::send
	// @desc		Envia a mensagem utilizando o método escolhido
	// @param		shutdown bool		"TRUE" Indica que os recursos criados (conexão SMTP) devem ser liberados ao final deste método
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
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
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::_mailSend
	// @desc		Envia a mensagem utilizando a função mail(), nativa no PHP
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _mailSend() {
		$this->_Message->removeHeader('To');		
		// armazena o subject antes de remove-lo
		$subject = $this->_Message->headers['Subject'];
		$this->_Message->removeHeader('Subject');
		// exige pelo menos um recipiente do tipo To
		if (!$this->_Message->hasRecipients(MAIL_RECIPIENT_TO)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MAIL_EMPTY_RCPT'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// destinatários
		$recipients = $this->_Message->to[0][0];
		for ($i=1; $i<sizeof($this->_Message->to); $i++) {
			$recipients .= sprintf(",%s", $this->_Message->to[$i][0]);
		}
		// execução da função mail()
		$parameters = sprintf("-oi -f %s", $this->_Message->getFrom());
		if (!mail($recipients, $subject, $this->_Message->body, $this->_getMessageHeaders(), $parameters)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_EXECUTE_COMMAND', 'mail()'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::_sendmailSend
	// @desc		Processa o envio da mensagem utilizando o sendmail instalado no servidor
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
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
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::_smtpSend
	// @desc		Processa o envio da mensagem utilizando um servidor SMTP
	// @param		shutdown bool	Encerrar ou não a conexão SMTP ao final deste método
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _smtpSend($shutdown) {
		$result = TRUE;
		// instância da conexão SMTP
		if (!is_object($this->_Smtp)) {
			$this->_Smtp = new Smtp();
			$this->_Smtp->debug = (isset($this->params['debug']) ? TypeUtils::toBoolean($this->params['debug']) : FALSE);
		}
		// abertura da conexão
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
		// autenticação
		if ($result && isset($this->params['username']) && isset($this->params['password'])) {
			$this->_Smtp->authenticate($this->params['username'], $this->params['password']);
		}
		// remetente
		$result = $result && $this->_Smtp->mail($this->_Message->getFrom());
		// destinatários
		$to = $this->_Message->getRecipients(MAIL_RECIPIENT_TO);
		foreach ($to as $recipient) 
			$result = $result && $this->_Smtp->recipient($recipient[0]);
		$cc = $this->_Message->getRecipients(MAIL_RECIPIENT_CC);
		foreach ($cc as $recipient) 
			$result = $result && $this->_Smtp->recipient($recipient[0]);
		$bcc = $this->_Message->getRecipients(MAIL_RECIPIENT_BCC);
		foreach ($bcc as $recipient) 
			$result = $result && $this->_Smtp->recipient($recipient[0]);
		// dados da mensagem		
		$result = $result && $this->_Smtp->data($this->_getMessageHeaders() . $this->_Message->body);
		// encerra a conexão se necessário
		if ($shutdown)
			$result = $result && $this->_Smtp->quit();		
		// erros no envio
		if (!$result && $this->errorMessage = $this->_Smtp->getLastError())
			PHP2Go::raiseError("SMTP ERROR: {$this->errorMessage}", E_USER_WARNING, __FILE__, __LINE__);
		return $result;
	}
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::_validateMessage
	// @desc		Valida se a mensagem pode ser enviada, de acordo com
	//				o estado atual de sua configuração
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _validateMessage() {
		if (!$this->_Message->built) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MAIL_MESSAGE_NOT_BUILT'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if (!$this->_Message->hasRecipients(MAIL_RECIPIENT_TO) && !$this->_Message->hasRecipients(MAIL_RECIPIENT_CC) && !$this->_Message->hasRecipients(MAIL_RECIPIENT_BCC)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MAIL_EMPTY_RCPT'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::_validateParams
	// @desc		Valida os parâmetros fornecidos para o envio da mensagem
	//				de acordo com o método escolhido
	// @param		type int		Tipo de envio
	// @param		&params array	Parâmetros para envio da mensagem
	// @access		private	
	// @return		bool
	//!-----------------------------------------------------------------
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
	
	//!-----------------------------------------------------------------
	// @function	MailTransport::_getMessageHeaders
	// @desc		Busca os cabeçalhos da mensagem, a partir do objeto _Message
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _getMessageHeaders() {
		$headers = '';
		foreach($this->_Message->headers as $name => $value) {
			$headers .= sprintf("%s: %s", $name, $value);
		}
		return $headers;
	}
}
?>