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
// $Header: /www/cvsroot/php2go/core/net/Pop3.class.php,v 1.15 2006/05/07 15:08:40 mpont Exp $
// $Date: 2006/05/07 15:08:40 $

//------------------------------------------------------------------
import('php2go.net.SocketClient');
//------------------------------------------------------------------

// @const POP3_OFF_STATE "0"
// Define o estado inicial da classe, sem conex�es ativas
define("POP3_OFF_STATE", 0);
// @const POP3_AUTH_STATE "1"
// Representa o AUTHENTICATION STATE do protocolo POP3
define("POP3_AUTH_STATE", 1);
// @const POP3_TRANS_STATE "2"
// Representa o TRANSACTION STATE do protocolo POP3
define("POP3_TRANS_STATE", 2);
// @const POP3_UPDATE_STATE "3"
// Representa o UPDATE STATE do protocolo POP3
define("POP3_UPDATE_STATE", 3);
// @const POP3_DEFAULT_PORT "110"
// Porta padr�o a ser utilizada em conex�es pelo protocolo POP3
define("POP3_DEFAULT_PORT", 110);
// @const POP3_DEFAULT_TIMEOUT "60"
// Timeout padr�o a ser utilizado, em segundos
define("POP3_DEFAULT_TIMEOUT", 60);
// @const POP3_CRLF "\r\n"
// Caractere(s) de final de linha padr�o na classe
define("POP3_CRLF", "\r\n");

//!-----------------------------------------------------------------
// @class		Pop3
// @desc		Esta classe implementa a conex�o com um servidor POP3,
//				buscando informa��es e conte�do de mensagens de correio.
//				� compat�vel com o RFC 1939, implementando todos os seus
//				comandos e a seq��ncia de estados
// @package		php2go.net
// @extends		SocketClient
// @author		Marcos Pont
// @version		$Revision: 1.15 $
// @note		Exemplo de uso:
//				<pre>
//
//				$pop = new Pop3();
//				$pop->connect('localhost');
//				$pop->login('foo', 'bar');
//				$count = $pop->getMsgCount();
//				for ($i=1; $i<=$count; $i++) {
//					print $p->getHeaders($i);
//					print $p->getBody($i);
//				}
//				print_r($p->listMessages());
//
//				</pre>
//!-----------------------------------------------------------------
class Pop3 extends SocketClient
{
	var $debug = FALSE;				// @var debug bool			"FALSE" Indica se mensagens de debug devem ser geradas juntamente com a execu��o dos comandos	
	var $state = POP3_OFF_STATE;	// @var state int			"POP3_OFF_STATE" Estado atual da conex�o com o servidor POP (vide constantes da classe)
	var $banner;					// @var banner string		Banner enviado como servidor em resposta � ativa��o da conex�o
	var $msgCount;					// @var msgCount int		Total de mensagens dispon�veis no servidor POP
	var $boxSize;					// @var boxSize int			Tamanho total da caixa de mensagens, em bytes
	
	//!-----------------------------------------------------------------
	// @function	Pop3::Pop3
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Pop3() {
		parent::SocketClient();
		parent::setBufferSize(512);
		parent::setLineEnd(POP3_CRLF);
		$this->msgCount = NULL;		
		$this->boxSize = NULL;
		parent::registerDestructor($this, '__destruct');
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::__destruct
	// @desc		Destrutor da classe, encerra a �ltima conex�o se esta foi mantida aberta
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		if ($this->state != POP3_OFF_STATE)
			$this->quit();
		unset($this);
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::connect
	// @desc		Conecta em um servidor POP
	// @access		public
	// @param		host string	Nome ou endere�o do host
	// @param		port int		"POP3_DEFAULT_PORT" Porta a ser utilizada na conex�o
	// @param		timeout int		"POP3_DEFAULT_TIMEOUT" Timeout para a conex�o
	// @return		bool
	//!-----------------------------------------------------------------
	function connect($host, $port=POP3_DEFAULT_PORT, $timeout=POP3_DEFAULT_TIMEOUT) {
		// fecha a conex�o anterior, se existente
		if ($this->state != POP3_OFF_STATE)
			$this->quit();
		// executa a fun��o de conex�o do socket
		if (!parent::connect($host, $port, NULL, $timeout)) {
			$this->state = POP3_OFF_STATE;
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_POP3_CONNECTION', array_unshift(parent::getLastError(), $host)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			// busca a resposta do servidor � solicita��o de conex�o	
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
	
	//!-----------------------------------------------------------------
	// @function	Pop3::login
	// @desc		Busca realizar a autentica��o no servidor POP3 utilizando
	//				um nome de usu�rio e uma senha
	// @access		public
	// @param		userName string		Nome de usu�rio
	// @param		password string		Senha de usu�rio
	// @param		apop bool			"FALSE" Com o valor do TRUE, este par�metro indica que o servidor implementa o comando APOP
	// @return		bool
	//!-----------------------------------------------------------------	
	function login($userName, $password, $apop = FALSE) {
		if ($this->state == POP3_AUTH_STATE) {
			if ($apop && $this->apop($userName, $password))
				return TRUE;
			if ($this->user($userName) && $this->pass($password))
				return TRUE;
		}
		$this->quit();
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::getServerBanner
	// @desc		Busca o banner retornado pelo servidor no momento da 
	//				autentica��o
	// @access		public
	// @return		string Banner enviado pelo servidor POP
	// @note		se o mesmo n�o implementa o comando APOP, este m�todo 
	//				dever� retornar uma string vazia
	//!-----------------------------------------------------------------
	function getServerBanner() {
		if (isset($this->banner))
			return $this->banner;
		else
			return '';
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::getMsgCount
	// @desc		Busca o total de mensagens dispon�veis no servidor POP
	// @access		public
	// @return		int Total de mensagens, excluindo as marcadas para remo��o
	// @note		Este m�todo retornar� FALSE se o protocolo n�o estiver no
	//				TRANSACTION STATE
	//!-----------------------------------------------------------------
	function getMsgCount() {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		else {
			if(!TypeUtils::isInteger($this->msgCount))
				$this->stat();				
			return $this->msgCount;
		}			
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::getMailboxSize
	// @desc		Busca o tamanho total da caixa de mensagens no servidor
	// @access		public
	// @return		int Tamanho total da caixa de mensagens, em bytes
	// @note		Este m�todo retornar� FALSE se o protocolo n�o estiver no
	//				TRANSACTION STATE	
	//!-----------------------------------------------------------------
	function getMailboxSize() {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		else {
			if(!TypeUtils::isInteger($this->boxSize))
				$this->stat();				
			return $this->boxSize;
		}			
	}	
	
	//!-----------------------------------------------------------------
	// @function	Pop3::getMessage
	// @desc		Busca o conte�do de uma mensagem
	// @access		public
	// @param		msgId int		C�digo da mensagem
	// @return		mixed Conte�do da mensagem ou FALSE
	//!-----------------------------------------------------------------	
	function getMessage($msgId) {
		return $this->retr($msgId);
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::getMessageHeaders
	// @desc		Retorna o conte�do dos cabe�alhos de uma mensagem
	// @access		public
	// @param		msgId int		N�mero da mensagem
	// @param		parse bool	"FALSE" Se TRUE, retorna um vetor com os cabe�alhos parseados
	// @return		mixed Conte�do dos headers, na forma de uma string ou de um
	//				vetor (parse = TRUE). Em caso de erros, retorna FALSE
	//!-----------------------------------------------------------------
	function getMessageHeaders($msgId, $parse = FALSE) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		if ($headers = $this->top($msgId))
			if ($parse)
				return $this->_parseHeaders($headers);
			else
				return $headers;
		else
			return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::getMessageBody
	// @desc		M�todo que busca o corpo de uma mensagem a partir de seu n�mero
	// @access		public
	// @param		msgId int		N�mero da mensagem
	// @return		mixed Corpo da mensagem ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function getMessageBody($msgId) {
		if ($content = $this->getMessage($msgId)) {
			if (StringUtils::match($content, "\r\n\r\n")) {
				$pos = strpos($content, "\r\n\r\n");
				return substr($content, $pos+4);
			}
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::listMessages
	// @desc		Gera uma lista das mensagens dispon�veis no servidor
	//				contendo n�mero, identificador �nico e tamanho
	// @access		public
	// @return		mixed Vetor contendo dados das mensagens ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function listMessages() {
		$messageList = array();
		$uidl = $this->uidl();
		$list = $this->mList();
		if ($uidl && $list) {
			for($i=0; $i<sizeof($uidl); $i++) {
				$messageList[] = array(
					'number' => $uidl[$i][0],
					'unique-id' => $uidl[$i][1],
					'size' => isset($list[$i]) ? $list[$i][1] : 0
				);
			}
		}
		return $messageList;
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::clearMailBox
	// @desc		Marca para dele��o todas as mensagens armazenadas
	// @access		public
	// @return		int N�mero de mensagens deletadas
	//!-----------------------------------------------------------------
	function clearMailBox() {
		$deleted = 0;
		if ($list = $this->mList())
			foreach($list as $values)
				$deleted += TypeUtils::parseInteger($this->dele($values[0]));
		return $deleted;
	}
	
	//------------------------------------------------------------------
	//------------------------------------------------------------------
	// COMANDOS SMTP - RFC 1939
	//------------------------------------------------------------------
	//------------------------------------------------------------------
	
	//!-----------------------------------------------------------------
	// @function	Pop3::user
	// @desc		Envia o comando USER ao servidor POP3. Este comando envia
	//				um nome de usu�rio para autentica��o, que ser� verificado 
	//				pelo servidor
	// @access		public
	// @param		userName string	Nome de usu�rio
	// @return		bool
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Pop3::pass
	// @desc		Envia o comando PASS ao servidor POP3. Este comando dever�
	//				ser executado imediatamente ap�s o comando USER, fornecendo
	//				a senha que corresponde ao USERNAME anteriormente fornecido
	// @access		public
	// @param		pass string	Senha de usu�rio
	// @return		bool
	//!-----------------------------------------------------------------
	function pass($password) {
		if ($this->state != POP3_AUTH_STATE)
			return FALSE;	
		// envia a senha requisitando autentica��o
		$responseMessage = NULL;		
		$data = sprintf("PASS %s%s", $password, POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_AUTHENTICATE');			
			return FALSE;
		}
		$this->state = POP3_TRANS_STATE;
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::apop
	// @desc		Envia o comando APOP ao servidor POP3, que permite realizar
	//				a autentica��o de um usu�rio sem que a senha seja enviada
	//				em claro atrav�s da rede
	// @access		public
	// @param		userName string	Nome de usu�rio
	// @param		password string	Senha de usu�rio
	// @return		bool Retorna FALSE caso o servidor n�o tenha enviado um banner
	//				no momento da conex�o ou caso o comando APOP n�o for aceito.
	//				Se a autentica��o for realizada com sucesso, retorna TRUE
	// @note		Para verificar se o servidor implementa o comando APOP, ative
	//				o debug na classe (Pop3->debug = TRUE) ou execute o comando
	//				Pop3::getServerBanner() ap�s Pop3::connect(). Este m�todo dever�
	//				retornar uma string no formato process-ID.clock@hostname, 
	//				gerada pelo servidor POP no momento da conex�o e que ser� utilizada
	//				posteriormente pelo comando APOP para realizar a autentica��o de
	//				usu�rios
	//!-----------------------------------------------------------------
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
	
	//!-----------------------------------------------------------------
	// @function	Pop3::stat
	// @desc		Envia o comando STAT ao servidor, buscando o total de
	//				mensagens dispon�veis (n�o incluindo as mensagems marcadas
	//				para remo��o) e o total em bytes da caixa de correio
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------	
	function stat() {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;		
		// envia uma solicita��o de status da caixa de mensagens
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
	
	//!-----------------------------------------------------------------
	// @function	Pop3::retr
	// @desc		Envia o comando RETR ao servidor, que solicita o conte�do
	//				de uma mensagem a partir de seu c�digo
	// @access		public
	// @param		msgId int		N�mero da mensagem
	// @return		mixed Conte�do da mensagem ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function retr($msgId) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		// solicita o conte�do de uma mensagem atrav�s de seu ID
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
	
	//!-----------------------------------------------------------------
	// @function	Pop3::top
	// @desc		Envia o comando TOP ao servidor, que solicita um determinado
	//				n�mero de linhas de uma mensagem
	// @access		public
	// @param		msgId int		N�mero da mensagem	
	// @param		numLines int	"0" N�mero de linhas solicitadas
	// @return		mixed Linhas solicitadas ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function top($msgId, $numLines = 0) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		// solicita um determinado n�mero de linhas de uma mensagem
		$responseMessage = NULL;		
		$data = sprintf("TOP %s %d%s", $msgId, TypeUtils::parseIntegerPositive($numLines), POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('TOP', $responseMessage));
			return FALSE;
		}
		return $this->_readAll();
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::dele
	// @desc		Envia o comando DELE ao servidor POP, marcando para
	//				dele��o uma mensagem a partir de seu n�mero
	// @access		public
	// @param		msgId int		N�mero da mensagem
	// @return		bool
	// @note		As mensagens marcadas com o comando DELE somente ser�o 
	//				deletadas ap�s a execu��o do comando QUIT
	//!-----------------------------------------------------------------
	function dele($msgId) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;			
		$data = sprintf("DELE %s%s", $msgId, POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('DELE', $responseMessage));
			return FALSE;
		}
		return TRUE;
	}		
	
	//!-----------------------------------------------------------------
	// @function	Pop3::mList
	// @desc		Envia o comando LIST ao servidor POP
	// @access		public
	// @param		msgId int		"NULL" N�mero da mensagem
	// @return		array Vetor contendo n�mero e tamanho da mensagem solicitada ou de todas as mensagens
	// @note		Se um n�mero de mensagem for fornecido, retorna um vetor contendo n�mero e tamanho 
	//				da mensagem. Do contr�rio, retorna um vetor contendo n�meros e tamanhos de todas as mensagens
	//!-----------------------------------------------------------------
	function mList($msgId = NULL) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;			
		if (TypeUtils::isNull($msgId)) {
			$data = sprintf("LIST%s", POP3_CRLF);
			if ($this->_sendData($data, $responseMessage)) {
				// busca todas as linhas dispon�veis
				$lines = explode("\r\n", $this->_readAll());
				$return = array();
				// monta um vetor com n�mero e tamanho das mensagens
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
				// monta um vetor com n�mero e tamanho da mensagem
				if (ereg("([0-9]+)[ ]([0-9]+)", $responseMessage, $matches))
					return array($matches[1], $matches[2]);
				else
					return FALSE;
			}
		}
		$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('LIST', $responseMessage));
		return FALSE;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	Pop3::uidl
	// @desc		Envia o comando UIDL ao servidor POP
	// @access		public
	// @param		msgId int		"NULL" N�mero da mensagem
	// @return		array Vetor contendo n�mero e identificador da mensagem solicitada ou de todas as mensagens
	// @note		Se um n�mero de mensagem for fornecido, retorna um vetor contendo n�mero e identificador 
	//				�nico. Do contr�rio, retorna um vetor contendo n�meros e identificadores �nicos de todas as mensagens
	//!-----------------------------------------------------------------
	function uidl($msgId = NULL) {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;			
		if (TypeUtils::isNull($msgId)) {
			$data = sprintf("UIDL%s", POP3_CRLF);
			if ($this->_sendData($data, $responseMessage)) {
				// l� todas as linhas dispon�veis, com os dados das mensagens
				$lines = explode("\r\n", $this->_readAll());
				$return = array();
				// monta um vetor com n�mero e unique-id das mensagens
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
				// monta um vetor com n�mero e unique-id da mensagem solicitada
				if (ereg("([0-9]+)[ ](.+)", $responseMessage, $matches))
					return array($matches[1], $matches[2]);
				else
					return FALSE;			
			}
		}
		$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('UIDL', $responseMessage));
		return FALSE;		
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::rset
	// @desc		Envia o comando RSET ao servidor POP, que reseta o status do servidor 
	//				remoto: todas as marcas de dele��o em mensagens s�o desfeitas e a conex�o � fechada
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
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
	
	//!-----------------------------------------------------------------
	// @function	Pop3::noop
	// @desc		Envia o comando NOOP ao servidor POP
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function noop() {
		if ($this->state != POP3_TRANS_STATE)
			return FALSE;
		$responseMessage = NULL;			
		$data = sprintf("NOOP%s", POP3_CRLF);
		if (!$retVal = $this->_sendData($data, $responseMessage))
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('NOOP', $responseMessage));
		return $retVal;
	}	
	
	//!-----------------------------------------------------------------
	// @function	Pop3::quit
	// @desc		Envia o comando QUIT ao servidor, fechando a conex�o
	// @access		public
	// @return		bool
	// @note		Se for executado no TRANSACTION STATE, altera o estado
	//				do protocolo para UPDATE (remo��o das mensagens solicitadas).
	//				Em caso contr�rio, altera para o estado OFF (desconectado)
	//!-----------------------------------------------------------------	
	function quit() {
		// altera o estado atual do protocolo
		if ($this->state == POP3_TRANS_STATE)
			$this->state = POP3_UPDATE_STATE;
		else
			$this->state = POP3_OFF_STATE;
		// envia o comando QUIT
		$responseMessage = NULL;		
		$data = sprintf("QUIT%s", POP3_CRLF);
		if (!$this->_sendData($data, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_POP3_COMMAND', array('QUIT', $responseMessage));
		}
		parent::close();
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Pop3::_sendData
	// @desc		Envia um comando ou requisi��o ao servidor POP, buscando
	//				a respectiva mensagem de resposta
	// @access		private
	// @param		data string				Conte�do do comando ou requisi��o
	// @param		&responseMessage string	Vari�vel por onde retorna a mensagem de resposta
	// @return		bool
	//!-----------------------------------------------------------------
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
	
	//!-----------------------------------------------------------------
	// @function	Pop3::_readResponse
	// @desc		L� uma linha atrav�s do socket conectado ao servidor POP,
	//				buscando uma mensagem de resposta a um comando ou requisi��o
	// @access		private
	// @return		string Linha lida do socket ou FALSE em caso de erros na conex�o
	//!-----------------------------------------------------------------
	function _readResponse() {
		return parent::readLine();
	}
	
    //!-----------------------------------------------------------------
	// @function	Pop3::_readAll
	// @desc		L� v�rias linhas atrav�s do socket ativo, at� encontrar
	//				uma linha contendo apenas um per�odo (fim do conte�do)
	// @access		private
	// @return		string Conte�do lido
	//!-----------------------------------------------------------------
	function _readAll() {		
        $data = '';
		while (($line = parent::readLine()) != '.') {
			if (StringUtils::left($line, 2) == '..')
				$line = substr($line, 1);
			$data .= $line . POP3_CRLF;
		}
		return StringUtils::left($data, -2);
    }	
	
	//!-----------------------------------------------------------------
	// @function	Pop3::_parseHeaders
	// @desc		Monta um vetor associativo dos cabe�alhos de uma mensagem
	// @access		private
	// @param		headers string	Conte�do dos headers de uma mensagem
	// @return		array Vetor associativo de nomes => valores dos cabe�alhos
	//!-----------------------------------------------------------------
	function _parseHeaders($headers) {
		$headers = preg_replace("/\r\n[ \t]+/", ' ', $headers);
		$headerList = explode("\r\n", $headers);
		$headers = array();
		foreach ($headerList as $key => $value) {
			if (StringUtils::match($value, ':')) {
				ereg("([^:]+):(.+)", $value, $matches);
				$name = trim($matches[1]);
				$value = trim($matches[2]);
				// os headers repetidos ser�o retornados na forma de um array
				if (isset($headers[$name]))
					if (TypeUtils::isArray($headers[$name]))
						$headers[$name][] = $value;
					else
						$headers[$name] = array($headers[$name], $value);
				else
					$headers[$name] = $value;
			}
		}
		return $headers;
	}
}
?>