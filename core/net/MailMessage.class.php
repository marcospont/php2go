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
// $Header: /www/cvsroot/php2go/core/net/MailMessage.class.php,v 1.29 2006/10/11 22:05:02 mpont Exp $
// $Date: 2006/10/11 22:05:02 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
import('php2go.net.MailPart');
import('php2go.net.MailTransport');
import('php2go.net.MimeType');
import('php2go.template.Template');
//------------------------------------------------------------------

// @const	MAIL_RECIPIENT_TO	"1"
// Constante para permitir referência ao tipo de recipiente "To:"
define('MAIL_RECIPIENT_TO', 1);
// @const	MAIL_RECIPIENT_CC	"2"
// Permite referência ao tipo de recipiente "Cc:"
define('MAIL_RECIPIENT_CC', 2);
// @const	MAIL_RECIPIENT_BCC	"3"
// Referencia-se ao tipo de recipiente "Bcc:"
define('MAIL_RECIPIENT_BCC', 3);
// @const	MAIL_RECIPIENT_REPLYTO	"4"
// Referencia-se ao tipo de recipiente "Reply-to:"
define('MAIL_RECIPIENT_REPLYTO', 4);

//!-----------------------------------------------------------------
// @class		MailMessage
// @desc		Classe que permite construir uma mensagem de e-mail
//				MIME, de acordo com a especificação RFC 822. Constrói
//				cabeçalhos e corpo da mensagem, adiciona arquivos anexos
//				e imagens embebidas em HTML.
// @package		php2go.net
// @extends		PHP2Go
// @uses		Environment
// @uses		HttpRequest
// @uses		MailPart
// @uses		MailTransport
// @uses		MimeType
// @uses		StringUtils
// @uses		Template
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.29 $
// @note		Exemplo de uso:
//				<pre>
//
//				$Msg = new MailMessage();
//				$Msg->setSubject("foo");
//				$Msg->setFrom("john@foo.com", "John");
//				$Msg->addTo("paul@bar.org", "Paul");
//				$Msg->addCC("mary@baz.org", "Mary");
//				$Msg->setHtmlBody("
//					&lt;html&gt;&lt;body&gt;
//						&lt;table&gt;&lt;tr&gt;&lt;td&gt;
//							This is HTML mail!
//						&lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;
//					&lt;/body&gt;&lt;/html&gt;");
//				$Msg->build();
//				$Transport =& $Msg->getTransport();
//				$Transport->setType(MAIL_TRANSPORT_SMTP, array('server'=>'foo.org', 'port'=>25));
//				$Transport->send();
//
//				</pre>
//!-----------------------------------------------------------------
class MailMessage extends PHP2Go
{
	var $from;							// @var from string						Endereço do remetente
	var $fromName;						// @var fromName string					Nome do remetente
	var $subject = '';					// @var subject string					Assunto da mensagem
	var $to = array();					// @var to array						"array()" Vetor de destinatários do tipo To
	var $cc = array();					// @var cc array						"array()" Vetor de destinatários do tipo Cc
	var $bcc = array();					// @var bcc array						"array()" Vetor de destinatários do tipo Bcc
	var $replyto = array();				// @var replyto array					"array()" Vetor de endereços de reply
	var $confirmReading;				// @var confirmReading string			Endereço para envio de confirmação de leitura
	var $headers = array();				// @var headers array					"array()" Vetor de cabeçalhos
	var $customHeaders = array();		// @var customHeaders array				"array()" Vetor de headers adicionados pelo usuário
	var $msHeaders = FALSE;				// @var msHeaders bool					"FALSE" Indica se os cabeçalhos Microsoft devem ser incluídos na mensagem
	var $textBody;						// @var textBody string					Corpo de texto da mensagem
	var $htmlBody;						// @var htmlBody string					Corpo HTML da mensagem
	var $wordWrap;						// @var wordWrap int					Tamanho da linha da mensagem
	var $attachments = array();			// @var attachments array				"array()" Vetor de arquivos anexos da mensagem
	var $embeddedFiles = array();		// @var embeddedFiles array				"array()" Vetor de arquivos embebidos no corpo da mensagem
	var $hostName;						// @var hostName string					Nome do host local
	var $mailType;						// @var mailType string					Tipo da mensagem
	var $charset;						// @var charset string					Charset da mensagem
	var $contentType;					// @var contentType string				Tipo do conteúdo da mensagem
	var $contentEncoding;				// @var contentEncoding string			Tipo de codificação da mensagem
	var $priority;						// @var priority int					Prioridade da mensagem
	var $body;							// @var body string						Conteúdo do corpo da mensagem
	var $lineEnd;						// @var lineEnd string					Caractere(s) de final de linha
	var $xMailer;						// @var xMailer string					Nome do X-Mailer
	var $uniqueId;						// @var uniqueId string					String randômica que representa o ID da mensagem
	var $built;							// @var built bool						Indica que a mensagem já foi construída com o método MailMessage::build

	//!-----------------------------------------------------------------
	// @function	MailMessage::MailMessage
	// @desc		Construtor da classe. Executa o construtor da classe
	//				pai e inicializa as propriedades da classe com seus
	//				valores padrão
	// @access		public
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::getCharset
	// @desc		Retorna o charset definido para a mensagem
	// @return		string Charset da mensagem
	// @access		public
	//!-----------------------------------------------------------------
	function getCharset() {
		return $this->charset;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setCharset
	// @desc		Configura o charset da mensagem
	// @param		charset string	Valor para o charset
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCharset($charset) {
		$this->charset = $charset;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::getContentType
	// @desc		Retorna o tipo MIME da mensagem
	// @return		string Tipo MIME da mensagem
	// @access		public
	//!-----------------------------------------------------------------
	function getContentType() {
		return $this->contentType;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::getEncoding
	// @desc		Retorna o tipo de codificação da mensagem
	// @return		string Tipo de codificação
	// @access		public
	//!-----------------------------------------------------------------
	function getEncoding() {
		return $this->contentEncoding;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setEncoding
	// @desc		Atribui um tipo de codificação para o conteúdo da mensagem
	// @param		encoding string	Tipo de codificação
	// @note		O padrão da classe para o conteúdo de texto da mensagem
	//				é 8bit. Este valor pode ser alterado para 7bit ou quoted-printable
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setEncoding($encoding) {
		$this->contentEncoding = $encoding;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::getFrom
	// @desc		Retorna o endereço do remetente da mensagem
	// @return		string Endereço do remetente
	// @access		public
	//!-----------------------------------------------------------------
	function getFrom() {
		return $this->from;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::getFromName
	// @desc		Retorna o nome do remetente da mensagem
	// @return		string Nome do remetente
	// @access		public
	//!-----------------------------------------------------------------
	function getFromName() {
		return $this->fromName;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setFrom
	// @desc		Configura o endereço e o nome do remetente da mensagem
	// @param		address string	Endereço do remetente
	// @param		name string		"" Nome do remetente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFrom($address, $name='') {
		$this->from = $address;
		if (!empty($name)) {
			$this->fromName = $name;
		}
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::getSubject
	// @desc		Retorna o assunto da mensagem
	// @return		string Assunto da mensagem
	// @access		public
	//!-----------------------------------------------------------------
	function getSubject() {
		return $this->subject;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setSubject
	// @desc		Seta o assunto da mensagem para um determinado valor
	// @param		subject string	Assunto da mensagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSubject($subject) {
		$this->subject = $subject;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::getPriority
	// @desc		Retorna a prioridade da mensagem
	// @return		string Prioridade da mensagem
	// @see			MailMessage::setPriority
	// @access		public
	//!-----------------------------------------------------------------
	function getPriority() {
		return $this->priority;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::getHostName
	// @desc		Retorna o nome do host onde a mensagem é construída
	// @return		string Nome do host local
	// @access		public
	//!-----------------------------------------------------------------
	function getHostName() {
		return $this->hostName;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setHostName
	// @desc		Define o nome ou endereço do host local
	// @param		hostName string	Nome para o host local
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setHostName($hostName) {
		$this->hostName = $hostName;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::getRecipients
	// @desc		Busca os destinatários da mensagem de um determinado tipo
	// @param		recipientType int		Tipo de recipiente
	// @return		array Vetor contendo os destinatários ou FALSE caso o tipo seja inválido
	// @note		O parâmetro $recipientType aceita os valores definidos
	//				nas constantes da classe: MAIL_RECIPIENT_TO, MAIL_RECIPIENT_CC,
	//				MAIL_RECIPIENT_BCC e MAIL_RECIPIENT_REPLYTO
	// @see			MailMessage::hasRecipients
	// @access		public
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::hasRecipients
	// @desc		Verifica se a mensagem possui destinatários de um determinado tipo
	// @param		recipientType int		Tipo de recipiente
	// @see			MailMessage::getRecipients
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::addTo
	// @desc		Adiciona um ou mais destinatários do tipo "To:"
	// @param		address string	Endereço do destinatário
	// @param		name string		"" Nome do destinatário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addTo($address, $name = '') {
		$this->addRecipient(MAIL_RECIPIENT_TO, $address, $name);
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::addCC
	// @desc		Adiciona um ou mais destinatários do tipo "Cc:"
	// @param		address string	Endereço do destinatário
	// @param		name string		"" Nome do destinatário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addCc($address, $name = '') {
		$this->addRecipient(MAIL_RECIPIENT_CC, $address, $name);
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::addBCC
	// @desc		Adiciona um ou mais destinatários do tipo "Bcc:"
	// @param		address string	Endereço do destinatário
	// @param		name string		"" Nome do destinatário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addBcc($address, $name = '') {
		$this->addRecipient(MAIL_RECIPIENT_BCC, $address, $name);
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::addReplyTo
	// @desc		Adiciona um ou mais destinatários do tipo "Reply-to:"
	// @param		address string	Endereço do destinatário de resposta
	// @param		name string		"" Nome do destinatário de resposta
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addReplyTo($address, $name = '') {
		$this->addRecipient(MAIL_RECIPIENT_REPLYTO, $address, $name);
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::addRecipient
	// @desc		Método genérico para inclusão de um ou mais destinatários
	//				a partir de seu tipo, endereços e nomes
	// @param		recipientType int	Tipo de destinatário (vide constantes da classe)
	// @param		address string	Endereço do destinatário
	// @param		name string		"" Nome do destinatário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::addRecipientList
	// @desc		Inclui um vetor de destinatários de um determinado tipo
	// @param		recipientType int	Tipo de destinatário (vide constantes da classe)
	// @param		recipients array	Vetor de destinatários
	// @note		Cada posição do vetor deve conter outro vetor com uma ou duas
	//				posições, sendo que a primeira será interpretada como sendo o
	//				endereço e a segunda (opcional) o nome
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function addRecipientList($recipientType, $recipients) {
		if (!TypeUtils::isArray($recipients))
			return FALSE;
		foreach($recipients as $recipient) {
			if (sizeof($recipient) < 1)
				continue;
			$this->addRecipient($recipientType, $recipient[0], (isset($recipient[1])) ? $recipient[1] : '');
		}
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::clearRecipients
	// @desc		Limpa a lista de destinatários de um determinado tipo
	// @param		recipientType int	Tipo de destinatário (vide constantes da classe)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::clearAllRecipients
	// @desc		Limpa todas as listas de destinatários de mensagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clearAllRecipients() {
		$this->to = array();
		$this->cc = array();
		$this->bcc = array();
		$this->replyto = array();
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::hasAttachments
	// @desc		Verifica se a mensagem possui arquivos anexos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasAttachments() {
		return sizeof($this->attachments) > 0;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::addAttachment
	// @desc		Adiciona um arquivo armazenado no servidor como anexo da mensagem atual
	// @param		fileName string	Caminho completo e nome do arquivo
	// @param		encoding string	"base64" Tipo de codificação a ser aplicada no arquivo
	// @param		mimeType string	"" Tipo MIME do arquivo
	// @note		Os tipos de codificações implementados são: 7bit, 8bit, base64 e
	//				quoted-printable
	// @note		Se não for fornecido um tipo MIME, a classe tentará buscar
	//				a partir da extensão do arquivo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addAttachment($fileName, $encoding='base64', $mimeType='') {
		$Part = new MailPart();
		$Part->setContentType(empty($mimeType) ? MimeType::getFromFileName($fileName) : $mimeType);
		$Part->setEncoding($encoding);
		$Part->setDisposition('attachment');
		$Part->setFileName($fileName);
		$Part->encodeContent();
		$this->attachments[] =& $Part;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::clearAttachments
	// @desc		Remove todos os arquivos anexos já incluídos na mensagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clearAttachments() {
		$this->attachments = array();
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::hasEmbeddedFiles
	// @desc		Verifica se a mensagem possui arquivos embebidos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasEmbeddedFiles() {
		return sizeof($this->embeddedFiles) > 0;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::addEmbeddedFile
	// @desc		Adiciona um arquivo como anexo da mensagem, indicando que o mesmo
	//				está embebido no corpo da mensagem
	// @param		fileName string	Caminho completo e nome do arquivo
	// @param		cid string		ID do elemento referenciado no corpo da mensagem
	// @param		encoding string	"base64" Tipo de codificação a ser aplicada no arquivo
	// @param		mimeType string	"" Tipo MIME do arquivo
	// @note		Os tipos de codificações implementados são: 7bit, 8bit, base64 e
	//				quoted-printable
	// @note		Se não for fornecido um tipo MIME, a classe tentará buscar
	//				a partir da extensão do arquivo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::clearEmbeddedFiles
	// @desc		Remove todos os arquivos embebidos já incluídos na mensagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clearEmbeddedFiles() {
		$this->embeddedFiles = array();
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::addCustomHeader
	// @desc		Adiciona um cabeçalho adicional à mensagem
	// @param		name string	Nome do cabeçalho
	// @param		value string	Valor do cabeçalho
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addCustomHeader($name, $value) {
		$this->customHeaders[] = array($name, $value);
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::removeCustomHeader
	// @desc		Remove um cabeçalho adicional inserido
	// @param		name string	Nome do cabeçalho
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function removeCustomHeader($name) {
		$keys = array_keys($this->customHeaders);
		foreach ($keys as $hName) {
			if (trim($hName) == trim($name)) {
				unset($this->customHeaders[$hName]);
				break;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::removeHeader
	// @desc		Remove um dos cabeçalhos padrão da mensagem
	// @param		name string	Nome do cabeçalho
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function removeHeader($name) {
		if (isset($this->headers[trim($name)]))
			unset($this->headers[trim($name)]);
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::useMicrosoftHeaders
	// @desc		Habilita ou desabilita a inclusão dos cabeçalhos Microsoft
	// @param		setting bool	"TRUE" Flag para habilitar ou desabilitar os cabeçalhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function useMicrosoftHeaders($setting = TRUE) {
		$this->msHeaders = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setPriority
	// @desc		Seta a prioridade da mensagem
	// @param		priority int	Prioridade da mensagem
	// @note		Valores possíveis para prioridade: 1 = alta, 3 = normal, 5 = baixa
	// @see			MailMessage::getPriority
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setPriority($priority) {
		if ($priority == 1 || $priority == 3 || $priority == 5)
			$this->priority = $priority;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setConfirmReading
	// @desc		Configura a mensagem para solicitar confirmação de leitura para
	//				um determinado endereço. Se o parâmetro $confirmAddress for deixado
	//				em branco, o método irá utilizar o e-mail do remetente da mensagem
	// @param		confirmAddress string		"" Endereço para confirmação
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setConfirmReading($confirmAddress='') {
		if ($confirmAddress != '')
			$this->confirmReading = $confirmAddress;
		elseif (isset($this->from))
			$this->confirmReading = $this->from;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setTextBody
	// @desc		Seta o corpo de texto da mensagem
	// @param		textBody string	Corpo de texto para a mensagem
	// @note		Caso não seja fornecido um textBody para a mensagem
	//				utilizando este método, será utilizada uma
	//				versão somente texto a partir do HTML fornecido em
	//				MailMessage::setHtmlBody
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTextBody($textBody) {
		$this->textBody = $textBody;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setHtmlBody
	// @desc		Seta o corpo de texto HTML da mensagem
	// @param		htmlBody string	Corpo HTML para a mensagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setHtmlBody($htmlBody) {
		$this->htmlBody = $htmlBody;
		$this->contentType = 'text/html';
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::setHtmlTemplate
	// @desc		Seta o corpo HTML da mensagem a partir de um template, incluindo
	//				as variáveis que devem ser substituídas no mesmo
	// @param		templateFile string		Caminho completo do template a ser utilizado
	// @param		templateVars array		"array()" Variáveis de substituição para o template
	// @param		templateIncludes array	"array()" Vetor de inclusões para o arquivo template
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::setWordWrap
	// @desc		Seta o número de caracteres para quebra de linha automática
	//				no corpo da mensagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setWordWrap($wrap) {
		if ($wrap >= 1)
			$this->wordWrap = $wrap;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::&getTransport
	// @desc		Instancia um objeto MailTransport responsável pelo envio da mensagem
	// @return		MailTransport object
	// @note		Mesmo que sejam executadas várias chamadas deste método,
	//				será utilizada sempre a mesma instância do objeto de transporte
	// @access		public
	//!-----------------------------------------------------------------
	function &getTransport() {
		static $Transport;
		if (!isset($Transport))
			$Transport = new MailTransport($this);
		else
			$Transport->setMessage($this);
		return $Transport;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::reset
	// @desc		Reseta todas as informações referentes à mensagem: recipientes, cabeçalhos,
	//				corpo da mensagem, anexos e embedded files. Utilizando este método,
	//				uma mesma instância pode ser utilizada para enviar mensagens diferentes
	//				para destinos diferentes
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::build
	// @desc		Monta os cabeçalhos e o corpo da mensagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function build() {
		$this->_defineMessageType();
		$this->_buildHeaders();
		$this->_buildBody();
		$this->built = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::formatAddress
	// @desc		Formata um endereço contido nos destinatários da mensagem
	// @param		address array		Vetor contendo endereço e nome do destinatário
	// @return		string String com endereço e nome formatados para inclusão em um dos cabeçalhos
	// @access		public	
	//!-----------------------------------------------------------------
	function formatAddress($address) {
		if (!isset($address[1]) || empty($address[1]))
			$formatted = $address[0];
		else
			$formatted = sprintf('%s %s', $this->_encodeHeader($address[1], 'phrase'), '<' . $address[0] . '>');
        return $formatted;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::_defineMessageType
	// @desc		Define o tipo da mensagem de acordo com a utilização
	//				de HTML, arquivos anexos e imagens embebidas
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::_buildHeaders
	// @desc		Constrói os headers da mensagem, contendo destinatários,
	//				assunto e outros parâmetros de configuração
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildHeaders() {
		// cabeçalho Received
		$this->_buildReceived();
		// cabeçalho Date
		$this->_addHeader('Date', $this->_getDate() . $this->lineEnd);
		// cabeçalho Message-ID
		$this->_addHeader('Message-ID', '<' . $this->uniqueId . '@' . $this->getHostName() . '>' . $this->lineEnd);
		// cabeçalho From
		$this->_addHeader('From', $this->formatAddress(array(trim($this->from), trim($this->fromName))) . $this->lineEnd);
		// cabeçalho To
		if ($this->hasRecipients(MAIL_RECIPIENT_TO))
			$this->_addHeader('To', $this->_buildAddressList(MAIL_RECIPIENT_TO));
		elseif (!$this->hasRecipients(MAIL_RECIPIENT_CC))
			$this->_addHeader('To', 'undisclosed-recipients:;' . $this->lineEnd);
		// cabeçalho Cc
		if ($this->hasRecipients(MAIL_RECIPIENT_CC))
			$this->_addHeader('Cc', $this->_buildAddressList(MAIL_RECIPIENT_CC));
		// cabeçalho Reply-to
		if ($this->hasRecipients(MAIL_RECIPIENT_REPLYTO))
			$this->_addHeader('Reply-to', $this->_buildAddressList(MAIL_RECIPIENT_REPLYTO));
		// cabeçalho Subject
		$this->_addHeader('Subject', $this->_encodeHeader(trim($this->subject)) . $this->lineEnd);
		// cabeçalho MIME-Version
		$this->_addHeader('MIME-Version', '1.0' . $this->lineEnd);
		// cabeçalho X-Priority
		$this->_addHeader('X-Priority', $this->getPriority() . $this->lineEnd);
		// cabeçalho X-Mailer
		$this->_addHeader('X-Mailer', $this->xMailer . ' (version ' . PHP2GO_VERSION . ')' . $this->lineEnd);
		// cabeçalho Return-Path
		$this->_addHeader('Return-Path', trim($this->from) . $this->lineEnd);
		// cabeçalho Disposition-Notification-To
		if (!empty($this->confirmReading))
			$this->_addHeader('Disposition-Notification-To', '<' . trim($this->confirmReading) . '>' . $this->lineEnd);
		// cabeçalhos customizados pelo usuário
		if (!empty($this->customHeaders))
			for ($i=0; $i<sizeof($this->customHeaders); $i++)
				$this->_addHeader(trim($this->customHeaders[$i][0]), $this->_encodeHeader(trim($this->customHeaders[$i][1])) . $this->lineEnd);
		// cabeçalhos da Microsoft
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
		// cabeçalhos Content-Type e Content-Transfer-Encoding
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::_buildBody
	// @desc		Constrói o corpo da mensagem a partir dos valores armazenados
	//				em $textBody e $htmlBody e a partir do tipo de mensagem
	//				já definido
	// @note		Para cada parte envolvida nesta mensagem, o método cria
	//				uma instância da classe MailPart, que constrói o cabeçalho
	//				e o conteúdo do elemento para inclusão no corpo principal
	//				da mensagem
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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
			// mail type mixed e relative : delimitador externo
			if ($this->mailType == 'mixed') {
				// separador mixed
				$this->body .= sprintf("--%s%s", $this->_getMimeBoundary('mix'), $this->lineEnd);
				// header alternative
				$this->body .= sprintf("Content-Type: %s;%s\tboundary=\"%s\"%s", 'multipart/alternative', $this->lineEnd, $this->_getMimeBoundary('alt'), $this->lineEnd . $this->lineEnd);
			} else if ($this->mailType == 'related') {
				// separador related
				$this->body .= sprintf("--%s%s", $this->_getMimeBoundary('rel'), $this->lineEnd);
				// header alternative
				$this->body .= sprintf("Content-Type: %s;%s\tboundary=\"%s\"%s", 'multipart/alternative', $this->lineEnd, $this->_getMimeBoundary('alt'), $this->lineEnd . $this->lineEnd);
			}
			// corpo de texto
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
			// corpo html
			if (!empty($this->htmlBody)) {
				$HtmlBody = new MailPart();
				$HtmlBody->setBoundaryId($this->_getMimeBoundary('alt'));
				$HtmlBody->setCharset($this->getCharset());
				$HtmlBody->setContentType('text/html');
				$HtmlBody->setEncoding($this->contentEncoding);
				// alterar cid com haspas simples
				if ($this->hasEmbeddedFiles())
					$this->htmlBody = eregi_replace("\'([ ]?cid[ ]?:.+)\'", "\"\\1\"", $this->htmlBody);
				$HtmlBody->setContent($this->htmlBody);
				$HtmlBody->encodeContent();
				$this->body .= $HtmlBody->buildSource();
				$this->body .= $this->lineEnd . $this->lineEnd;
			}
			// terminador boundary alternative
			$this->body .= sprintf("%s--%s--%s", $this->lineEnd, $this->_getMimeBoundary('alt'), $this->lineEnd . $this->lineEnd);
			// arquivos anexos
			if ($this->hasAttachments())
				foreach($this->attachments as $attachment) {
					if ($this->mailType == 'mixed')
						$attachment->setBoundaryId($this->_getMimeBoundary('mix'));
					else
						$attachment->setBoundaryId($this->_getMimeBoundary('rel'));
					$this->body .= $attachment->buildSource();
				}
			// arquivos embebidos
			if ($this->hasEmbeddedFiles()) {
				foreach($this->embeddedFiles as $embedded) {
					$embedded->setBoundaryId($this->_getMimeBoundary('rel'));
					$this->body .= $embedded->buildSource();
				}
			}
			// mail type mixed e relative : delimitador externo
			if ($this->mailType == 'mixed') {
				// terminador boundary mixed
				$this->body .= sprintf("%s--%s--%s", $this->lineEnd, $this->_getMimeBoundary('mix'), $this->lineEnd . $this->lineEnd);
			} else if ($this->mailType == 'related') {
				// terminador boundary related
				$this->body .= sprintf("%s--%s--%s", $this->lineEnd, $this->_getMimeBoundary('rel'), $this->lineEnd . $this->lineEnd);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::_addHeader
	// @desc		Inclui um elemento no vetor de cabeçalhos da mensagem
	// @param		name string	Nome do cabeçalho
	// @param		value string	Valor do cabeçalho
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _addHeader($name, $value) {
		$this->headers[$name] = $value;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::_buildAddressList
	// @desc		Constrói uma lista de endereços a partir de um dos
	//				vetores de destinatários da mensagem
	// @param		recipientType int	Tipo de destinatário (vide constantes da classe)
	// @return		string	Lista de remetentes do tipo passado por parâmetro
	// @access		private
	//!-----------------------------------------------------------------
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
		$addressList = $this->formatAddress($list[0]);
		$listSize = sizeof($list);
		if ($listSize > 1) {
			for ($i=1; $i<$listSize; $i++)
				$addressList .= sprintf(", %s", $this->formatAddress($list[$i]));
		}
		$addressList .= $this->lineEnd;
		return $addressList;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::_received
	// @desc		Constrói o cabeçalho "Received" para permitir rastreamento
	//				da mensagem da origem até o seu destino
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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
			$remote, $this->lineEnd, $this->getHostName(),
			$protocol, $this->xMailer, $this->lineEnd, $this->_getDate(), $this->lineEnd
		);
		$this->_addHeader('Received', $str);
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::_encodeHeader
	// @desc		Codifica o conteúdo de um header da mensagem para adaptá-lo
	//				aos padrões de codificação exigidos
	// @param		content string	Conteúdo de um cabeçalho da mensagem
	// @param		type string		Tipo do conteúdo: phrase, comment ou text
	// @author		Brent R. Matzelle <bmatzelle@yahoo.com>
	// @return		string Valor do cabeçalho codificado
	// @access		private
	//!-----------------------------------------------------------------
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
				// retira caracteres ASCII altos e caracteres de controle
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::_encodeQuoted
	// @desc		Codifica uma string para o modo de codificação Q (quoted)
	// @param		content string	Conteúdo a ser codificado
	// @param		type string		Tipo do conteúdo: phrase, comment ou text
	// @author		Brent R. Matzelle <bmatzelle@yahoo.com>
	// @return		string String codificada
	// @access		private
	//!-----------------------------------------------------------------
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
				// substitui todos os caracteres ASCII altos, caracteres de controle e underlines
				$encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e', "'='.sprintf('%02X', ord('\\1'))", $encoded);
				break;
		}
		// substitui todos os espaços em branco por underlines
		$encoded = str_replace(' ', '_', $encoded);
		return $encoded;
	}

	//!-----------------------------------------------------------------
	// @function	MailMessage::_getMimeBoundary
	// @desc		Armazena em variáveis estáticas os delimitadores de partes
	//				da mensagem (boundaries), retornando-os para inclusão
	//				nos cabeçalhos e no corpo
	// @return		string	Valor do limitador MIME
	// @access		private
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailMessage::_getDate
	// @desc		Constrói a definição da data segundo o formato definido no RFC 822
	// @return		string Data formatada para inclusão nos cabeçalhos da mensagem
	// @access		private
	//!-----------------------------------------------------------------
	function _getDate() {
        $timeZone = date("Z");
        $timeZoneSig = ($timeZone < 0) ? "-" : "+";
        $timeZone = TypeUtils::parseIntegerPositive($timeZone);
        $timeZone = ($timeZone/3600)*100 + ($timeZone%3600)/60;
        return sprintf("%s %s%04d", date("D, j M Y H:i:s"), $timeZoneSig, $timeZone);
	}
}
?>