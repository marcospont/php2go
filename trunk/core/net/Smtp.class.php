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
// $Header: /www/cvsroot/php2go/core/net/Smtp.class.php,v 1.15 2006/04/05 23:43:24 mpont Exp $
// $Date: 2006/04/05 23:43:24 $

//------------------------------------------------------------------
import('php2go.net.SocketClient');
//------------------------------------------------------------------

// @const SMTP_DEFAULT_PORT "25"
// Porta padrão para conexão em servidores SMTP
define('SMTP_DEFAULT_PORT', 25);
// @const SMTP_DEFAULT_TIMEOUT "5"
// Timeout padrão para conexão SMTP
define('SMTP_DEFAULT_TIMEOUT', 5);
// @const SMTP_CRLF "\r\n"
// Define a quebra de linha utilizada na comunicação com o servidor SMTP
define('SMTP_CRLF', "\r\n");

//!-----------------------------------------------------------------
// @class		Smtp
// @desc		Esta classe define métodos que possibilitam a conexão
//				a um servidor SMTP. É compatível com o RFC 821, implementando
//				todas as funções SMTP definidas no RFC, exceto a função TURN
// @package		php2go.net
// @extends		SocketClient
// @author		Marcos Pont
// @version		$Revision: 1.15 $
// @note		Para maiores informações, consulte o conteúdo dos RFCs 
//				821 e 822 na Internet
//!-----------------------------------------------------------------
class Smtp extends SocketClient
{
	var $debug = FALSE;				// @var debug bool				"FALSE" Indica se mensagens de debug devem ser geradas juntamente com a execução dos comandos
	var $authenticated = FALSE;		// @var authenticated bool		"FALSE" Indica se houve uma autenticação com sucesso
	var $maxDataLength = 998;		// @var maxDataLength int		"998" Tamanho máximo para uma linha de mensagem, segundo o RFC 821

	//!-----------------------------------------------------------------
	// @function	Smtp::Smtp
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Smtp(){
		parent::SocketClient();
		parent::setBufferSize(515);
		parent::setLineEnd(SMTP_CRLF);
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	Smtp::__destruct
	// @desc		Destrutor da classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct(){
		unset($this);
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::connect
	// @desc		Conecta em um servidor SMTP
	// @param		host string	Endereço do host SMTP
	// @param		port int	"SMTP_DEFAULT_PORT" Porta a ser utilizada na conexão
	// @param		timeout int	"SMTP_DEFAULT_TIMEOUT" Timeout a ser utilizado
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
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
	
	//------------------------------------------------------------------
	//------------------------------------------------------------------
	// COMANDOS SMTP - RFC 821
	//------------------------------------------------------------------
	//------------------------------------------------------------------
	
	//!-----------------------------------------------------------------
	// @function	Smtp::helo
	// @desc		Envia o comando HELO ao servidor SMTP. Este comando
	//				busca certificar-se de que o cliente e o servidor estão
	//				em um mesmo estado conhecido
	// @param		heloHost string		""	Host para o comando HELO
	// @note		HELO <SP> <DOMAIN> <CRLF>
	//				SUCESSO: 250
	//				ERRO: 500, 501, 504, 421
	// @note		Se o host a ser utilizado no comando helo não for fornecido,
	//				a variável de ambiente SERVER_NAME ou localhost.localdomain
	//				serão enviados em seu lugar
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function helo($heloHost='') {
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
	
	//!-----------------------------------------------------------------
	// @function	Smtp::authenticate
	// @desc		Realiza a autenticação no servidor SMTP utilizando 
	//				um nome de usuário e uma senha
	// @param		username string	Nome de usuário
	// @param		password string	Senha
	// @note		Ambos os valores são enviados utilizando a codificação base64
	// @note		AUTH <SP> LOGIN <CRLF>
	//				INTERMEDIÁRIO: 334
	//				<USERNAME> <CRLF>
	//				INTERMEDIÁRIO: 334
	//				<PASSWORD> <CRLF>
	//				SUCESSO: 235
	//				ERRO: 500, 501, 502, 504, 535
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function authenticate($username, $password) {
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
		$this->authenticated = TRUE;
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::mail
	// @desc		Envia o comando MAIL FROM ao servidor SMTP, iniciando
	//				uma transação de envio de mensagem com o servidor
	// @param		from string		Endereço de origem da mensagem
	// @note		Se o remetente for aceito, o próximo comando a ser 
	//				enviado deverá ser RCPT, seguido de DATA
	// @note		MAIL <SP> FROM: <reverse-path> <CRLF>
	//				SUCESSO: 250
	//				FALHA: 552, 451, 452
	//				ERRO: 500, 501, 421
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function mail($from) {
		$responseCode = NULL;
		$responseMessage = NULL;		
		$data = sprintf("MAIL FROM:%s%s", "<$from>", SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('MAIL', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;		
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::recipient
	// @desc		Envia o comando RCPT TO ao servidor SMTP
	// @param		to string		Endereço(s) de destino da mensagem
	// @note		RCPT <SP> TO: <forward-path> <CRLF>
	//				SUCESSO: 250, 251
	//				FALHA: 550, 551, 552, 553, 450, 451, 452
	//				ERRO: 500, 501, 503, 421
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function recipient($to) {
		$responseCode = NULL;
		$responseMessage = NULL;		
		$data = sprintf("RCPT TO:%s%s", "<$to>", SMTP_CRLF);
		if (!$this->_sendData($data, array(250, 251), $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('RCPT', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::data
	// @desc		Envia o comando DATA ao servidor, seguido dos cabeçalhos
	//				e do corpo da mensagem
	// @note		DATA <CRLF>
	//				INTERMEDIÁRIO: 354
	//				FALHA: 451, 554
	//				ERRO: 500, 501, 503, 421
	//				[dados]
	//				<CRLF> . <CRLF>
	//				SUCESSO: 250
	//				FALHA: 552, 554, 451, 452	
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function data($msgData) {
		// inicialmente, é enviado o comando DATA ao servidor, esperando um reply
		// intermediário de código 354 que indica transmissão habilitada
		$responseCode = NULL;
		$responseMessage = NULL;		
		if (!$this->_sendData('DATA' . SMTP_CRLF, 354, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('DATA', $responseCode, $responseMessage));
			return FALSE;
		}		
		// ok, pronto para enviar o conteúdo da mensagem
		// de acordo com o RFC 822, uma única linha não pode conter mais do que
		// 1000 caracteres, incluindo CR e LF. O conteúdo será quebrado em partes
		// através dos delimitadores CR e LF, a fim de criar porções menores que
		// respeitem o limite imposto
		$msgData = str_replace("\r\n", "\n", $msgData);
		$msgData = str_replace("\r", "\n", $msgData);
		$msgLines = explode("\n", $msgData);		
		// a partir das definições do RFC 822, uma linha que não contenha espaços
		// e seja separada pelo caractere ':' caracteriza um header da mensagem.
		// a separação entre os headers e o corpo da mensagem é feita por uma linha
		// vazia
		$headers = (StringUtils::match($msgLines[0], ':') > 0 && !StringUtils::match($msgLines[0], ' ')) ? TRUE : FALSE;
		while (list(, $line) = each($msgLines)) {
			$buffer = array();
			if ($line == '' && $headers)
				$headers = FALSE;
			// devemos verificar se uma linha da mensagem excede o limite de caracteres,
			// e quebra-la em porções menores se necessário
			while (strlen($line) > $this->maxDataLength) {
				$lastSpacePos = strrpos(StringUtils::left($line, $this->maxDataLength), ' ');
				$buffer[] = StringUtils::left($line, $lastSpacePos);
				$line = substr($line, $lastSpacePos+1);
				// de acordo com o RFC 822, as linhas que contêm headers
				// devem ser precedidas por um caractere LWSP (tab)
				if ($headers) {
					$line = "\t" . $line;
				}
			}
			$buffer[] = $line;
			// envio das linhas ao servidor
			while (list(, $line) = each($buffer)) {
				if (strlen($line) > 0 && StringUtils::left($line, 1) == '.') {
					$line = '.' . $line;
				}
				if ($this->debug)
					print('SMTP DEBUG --- FROM CLIENT : ' . htmlspecialchars($line) . '<br>');
				parent::write($line . SMTP_CRLF);
			}
		}		
		// após o envio de todas as linhas, o envio de dados se encerra por uma linha
		// contendo apenas um período
		if (!$this->_sendData('.' . SMTP_CRLF, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('DATA', $responseCode, $responseMessage));
			return FALSE;
		}		
		return TRUE;		
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::send
	// @desc		Envia o comando SEND FROM ao servidor SMTP, iniciando
	//				uma transação de envio de mensagem
	// @param		from string	Endereço do remetente da mensagem
	// @note		SEND <SP> FROM:< <reverse-path> > <CRLF>
	//				SUCESSO: 250
	//				FALHA: 552, 451, 452
	//				ERRO: 500, 501, 502, 421
	// @note		O comando SEND busca entregar a mensagem a um usuário
	//				cujo terminal está ativo. Se o usuário não estiver ativo,
	//				não será possível incluí-lo através do comando RCPT TO
	// @see			Smtp::sendOrMail
	// @see			Smtp::sendAndMail
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function send($from) {
		$responseCode = NULL;
		$responseMessage = NULL;		
		$data = sprintf("SEND FROM:%s%s", $from, SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('SEND', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;	
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::sendOrMail
	// @desc		Envia o comando SOML ao servidor SMTP
	// @param		from string	Endereço do remetente da mensagem
	// @note		SOML <SP> FROM:< <reverse-path> > <CRLF>
	//				SUCESSO: 250
	//				FALHA: 552, 451, 452
	//				ERRO: 500, 501, 502, 421
	// @note		O comando SOML busca entregar a mensagem a um usuário
	//				cujo terminal está ativo. Se o usuário não estiver ativo,
	//				a mensagem será enviada à sua caixa postal
	// @see			Smtp::send
	// @see			Smtp::sendAndMail	
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function sendOrMail($from) {
		$responseCode = NULL;
		$responseMessage = NULL;		
		$data = sprintf("SOML FROM:%s%s", $from, SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('SOML', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;	
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::sendAndMail
	// @desc		Envia o comando SAML ao servidor SMTP
	// @param		from string	Endereço do remetente da mensagem
	// @note		SAML <SP> FROM:< <reverse-path> > <CRLF>
	//				SUCESSO: 250
	//				FALHA: 552, 451, 452
	//				ERRO: 500, 501, 502, 421
	// @note		O comando SAML busca entregar a mensagem em uma caixa
	//				postal de um usuário. Retorna um erro se não for possível
	//				ter acesso à caixa postal de destino
	// @see			Smtp::send
	// @see			Smtp::sendOrMail
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function sendAndMail($from) {
		$responseCode = NULL;
		$responseMessage = NULL;		
		$data = sprintf("SAML FROM:%s%s", $from, SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('SAML', $responseCode, $responseMessage));
			return FALSE;
		}
		return TRUE;	
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::help
	// @desc		Implementa o comando SMTP HELP, que busca informações de ajuda
	//				em um determinado comando ou palavra chave
	// @param		keyword string	"" Comando ou palavra-chave do qual se busca ajuda
	// @return		mixed Conteúdo retornado pelo comando HELP ou FALSE em caso de erros
	// @note		Se o parâmetro $keyword não for fornecido ao método, será
	//				solicitada ajuda genérica, geralmente listando os comandos
	//				disponíveis no servidor
	// @note		Em caso de sucesso, o retorno do comando HELP é devolvido ao
	//				usuário para que seja tratado
	// @note		HELP <SP> <KEYWORD> <CRLF>
	//				SUCESSO: 211, 214
	//				ERRO: 500, 501, 502, 504, 421
	// @access		public	
	//!-----------------------------------------------------------------
	function help($keyword='') {
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
	
	//!-----------------------------------------------------------------
	// @function	Smtp::expand
	// @desc		Envia o comando EXPN ao servidor SMTP, solicitando que
	//				os endereços dos membros da lista $listName sejam listados
	// @param		listName string		Lista a ser consultada
	// @return		array Vetor de endereços retornados pelo servidor em caso contrário. Retorna FALSE em caso de erros
	// @note		EXPN <SP> <list> <CRLF>
	//				SUCESSO: 250-username <CRLF> 250-username <CRLF> 250 username <CRLF>
	//				FALHA: 550
	//				ERRO: 500, 501, 502, 504, 421
	// @access		public	
	//!-----------------------------------------------------------------
	function expand($listName) {
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
	
	//!-----------------------------------------------------------------
	// @function	Smtp::verify
	// @desc		Envia o comando VRFY ao servidor SMTP, que consulta pela
	//				existência ou conhecimento de um determinado nome ou caixa
	//				postal no servidor
	// @return		mixed FALSE em caso de erros ou a resposta do servidor em caso contrário
	// @note		VRFY <SP> <string> <CRLF>
	//				SUCESSO: 250, 251
	//				FALHA: 550, 551, 553
	//				ERRO: 500, 501, 502, 504, 421
	// @access		public	
	//!-----------------------------------------------------------------
	function verify($name) {
		$responseCode = NULL;
		$responseMessage = NULL;		
		$data = sprintf("VRFY %s%s", $name, SMTP_CRLF);
		if (!$this->_sendData($data, array(250, 251), $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('VRFY', $responseCode, $responseMessage));
			return FALSE;
		}
		return $responseMessage;
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::noop
	// @desc		Envia o comando NOOP ao servidor SMTP
	// @note		NOOP <CRLF>
	//				SUCESSO: 250
	//				ERRO: 500, 421
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function noop() {
		$responseCode = NULL;
		$responseMessage = NULL;		
		$data = sprintf("NOOP%s", SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('NOOP', $responseCode, $responseMessage));
		}
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::reset
	// @desc		Envia o comando RSET ao servidor SMTP, que aborta a
	//				transação ativa, limpando buffers e informações coletadas
	// @note		RSET <CRLF>
	//				SUCESSO: 250
	//				ERRO: 500, 501, 504, 421
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function reset() {
		$responseCode = NULL;
		$responseMessage = NULL;		
		$data = sprintf("RSET%s", SMTP_CRLF);
		if (!$this->_sendData($data, 250, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('RSET', $responseCode, $responseMessage));
		}
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::quit
	// @desc		Envia o comando QUIT ao servidor SMTP, que fecha a conexão
	//				com o servidor e o socket aberto
	// @note		QUIT <CRLF>
	//				SUCESSO: 221
	//				ERRO: 500
	// @access		public
	// @return		bool	
	//!-----------------------------------------------------------------
	function quit() {
		$responseCode = NULL;
		$responseMessage = NULL;		
		$data = sprintf("QUIT%s", SMTP_CRLF);
		if (!$this->_sendData($data, 221, $responseCode, $responseMessage)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SMTP_COMMAND', array('QUIT', $responseCode, $responseMessage));
		}
		parent::close();
		return TRUE;
	}	

	//!-----------------------------------------------------------------
	// @function	Smtp::_sendData
	// @desc		Envia um comando ou requisição ao servidor SMTP
	// @param		data string					Conteúdo do comando ou requisição
	// @param		expected int				Código de resposta esperado (sucesso)
	// @param		&responseCode int			Código retornado na resposta do servidor
	// @param		&responseMessage string		Mensagem de resposta do servidor
	// @access		private	
	// @return		bool
	//!-----------------------------------------------------------------
	function _sendData($data, $expected, &$responseCode, &$responseMessage) {
		$expected = !TypeUtils::isArray($expected) ? array($expected) : $expected;
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
	
	//!-----------------------------------------------------------------
	// @function	Smtp::_readResponse
	// @desc		Lê a resposta do servidor SMTP a um determinado comando,
	//				retornando código e mensagem da resposta
	// @param		&responseCode int		Código de resposta
	// @param		&responseMessage string	Mensagem de resposta
	// @access		private	
	// @return		bool
	//!-----------------------------------------------------------------
	function _readResponse(&$responseCode, &$responseMessage) {
		if ($data = $this->_readData()) {			
			$responseCode = substr($data, 0, 3);
			$responseMessage = substr($data, 4);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Smtp::_readData
	// @desc		Método genérico para leitura de dados a partir da 
	//				conexão SMTP estabelecida
	// @return		string Buffer de dados lidos ou FALSE se a conexão for inválida
	// @access		private	
	//!-----------------------------------------------------------------
	function _readData() {
		if (parent::isConnected()) {
			$buffer = '';
			while ($data = parent::readLine()) {
				$buffer .= $data;
				// a quarta posição contendo um espaço em branco indica que não há mais conteúdo a ser lido
				if (StringUtils::charAt($data, 3) == ' ') {
					break;
				}
			}
			return $buffer;
		} else {
			$this->errorNo = -1;
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;			
		}
	}
}
?>