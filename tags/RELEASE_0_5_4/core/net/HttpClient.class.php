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
// $Header: /www/cvsroot/php2go/core/net/HttpClient.class.php,v 1.21 2006/04/05 23:43:24 mpont Exp $
// $Date: 2006/04/05 23:43:24 $

//------------------------------------------------------------------
import('php2go.file.FileManager');
import('php2go.net.HttpCookie');
import('php2go.net.SocketClient');
import('php2go.net.Url');
import('php2go.net.MimeType');
import('php2go.net.httpConstants', 'php', FALSE);
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		HttpClient
// @desc		Esta classe implementa um cliente HTTP, que permite a abertura de conex�es
//				HTTP nas vers�es 1.0 e 1.1 e o envio de comandos GET, POST, TRACE e DELETE
// @package		php2go.net
// @uses		FileManager
// @uses		FileSystem
// @uses		HttpCookie
// @uses		MimeType
// @uses		TypeUtils
// @uses		Url
// @extends		SocketClient
// @author		Marcos Pont
// @version		$Revision: 1.21 $
// @note		A classe tem total compatibilidade com o RFC 2616 (Hypertext Transfer Protocol)
// @note		Suporta comandos POST com m�ltiplas partes ou dados de formul�rio, uso de
//				proxy com autentica��o, envio e interpreta��o de cookies e conex�es Keep-Alive
//!-----------------------------------------------------------------
class HttpClient extends SocketClient
{
	var $httpHost;					// @var httpHost string			Endere�o ou IP do host HTTP utilizado na conex�o
	var $httpPort;					// @var httpPort int			Porta utilizada na conex�o HTTP
	var $httpVersion;				// @var httpVersion string		Vers�o do protocolo HTTP utilizada
	var $userAgent;					// @var userAgent string		Descri��o do agente enviada nas requisi��es
	var $referer;					// @var referer string			Armazena a URI do referente de uma requisi��o
	var $keepAlive;					// @var keepAlive bool			Indica se a conex�o deve ser do tipo Keep-Alive
	var $useAuth;					// @var useAuth bool			Indica se a autentica��o est� habilitada
	var $authUser;					// @var authUser string			Nome de usu�rio para autentica��o
	var $authPass;					// @var authPass string			Senha para autentica��o
	var $useProxy;					// @var useProxy bool			Indica se o uso de proxy est� habilitado
	var $proxyHost;					// @var proxyHost string		Endere�o ou IP do servidor proxy a ser utilizado
	var $proxyPort;					// @var proxyPort int			Porta para conex�o no servidor proxy
	var $proxyUser;					// @var proxyUser string		Nome de usu�rio para autentica��o no servidor proxy
	var $proxyPass;					// @var proxyPass string		Senha para autentica��o no servidor proxy
	var $followRedirects;			// @var followRedirects bool	Indica se a conex�o deve seguir comandos de redirecionamento
	var $debug = FALSE;				// @var debug bool				"FALSE" Controle de debug da classe
	var $currentMethod = NULL;		// @var currentMethod string	"NULL" �ltimo m�todo executado na conex�o HTTP
	var $requestHeaders = array();	// @var requestHeaders array	"array()" Vetor de cabe�alhos de requisi��o
	var $requestBody = '';			// @var requestBody string		"" Corpo da requisi��o, inclui os dados de formul�rios e/ou partes da requisi��o
	var $responseHeaders = array();	// @var responseHeaders array	"array()" Vetor de cabe�alhos de resposta
	var $responseBody = '';			// @var responseBody string		"" Corpo da resposta
	var $cookieHeaders = array();	// @var cookieHeaders array		"array()" Vetor de cabe�alhos Set-Cookie
	var $cookies = array();			// @var cookies array			"array()" Vetor de cookies nos cabe�alhos de resposta

	//!-----------------------------------------------------------------
	// @function	HttpClient::HttpClient
	// @desc		Construtor da classe. Inicializa as propriedades principais do objeto
	// @access		public
	//!-----------------------------------------------------------------
	function HttpClient() {
		parent::SocketClient();
		parent::setTimeout(HTTP_DEFAULT_TIMEOUT);
		parent::setBufferSize(4096);
		parent::setLineEnd(HTTP_CRLF);
		$this->httpPort = HTTP_DEFAULT_PORT;
		$this->httpVersion = '1.1';
		$this->userAgent = 'PHP2Go Http Client ' . PHP2GO_VERSION . ' (compatible; MSIE 6.0; Linux)';
		$this->keepAlive = FALSE;
		$this->useAuth = FALSE;
		$this->useProxy = FALSE;
		$this->followRedirects = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::getRequestHeader
	// @desc		Busca, a partir do nome, o valor de um cabe�alho da requisi��o
	// @access		public
	// @param		name string	Nome do cabe�alho solicitado
	// @return		string Valor do cabe�alho ou NULL se n�o existente
	//!-----------------------------------------------------------------
	function getRequestHeader($name) {
		$formattedName = $this->_formatHeaderName($name);
		if (isset($this->requestHeaders[$formattedName]))
			return $this->requestHeaders[$formattedName];
		else
			return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::getStatus
	// @desc		Consulta o status de uma requisi��o HTTP enviada ao
	//				servidor, retornando um c�digo inteiro (RFC 2616)
	// @access		public
	// @return		int C�digo de status da requisi��o enviada
	// @note		Os c�digos inteiros que s�o enviados como resposta est�o
	//				definidos como constantes na classe
	//!-----------------------------------------------------------------
	function getStatus() {
		$status = $this->getResponseHeader('Status');
		if (!TypeUtils::isNull($status))
			return TypeUtils::parseInteger($status);
		else
			return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::getResponseHeader
	// @desc		Busca o valor de um cabe�alho de resposta a partir de seu nome
	// @access		public
	// @param		name string	Nome do cabe�alho solicitado
	// @return		int Valor do cabe�alho ou NULL se n�o existente
	//!-----------------------------------------------------------------
	function getResponseHeader($name) {
		$formattedName = $this->_formatHeaderName($name);
		if (isset($this->responseHeaders[$formattedName]))
			return $this->responseHeaders[$formattedName];
		else
			return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::getResponseHeaders
	// @desc		Busca o conjunto de cabe�alhos de resposta � �ltima
	//				requisi��o enviada ao servidor HTTP
	// @access		public
	// @return		array Vetor associativo de cabe�alhos de resposta ou NULL se
	//				eles n�o existirem
	//!-----------------------------------------------------------------
	function getResponseHeaders() {
		return (!empty($this->responseHeaders)) ? $this->responseHeaders : NULL;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::getResponseBody
	// @desc		Retorna o corpo da resposta do servidor HTTP
	// @access		public
	// @return		string Corpo da resposta HTTP
	//!-----------------------------------------------------------------
	function getResponseBody() {
		return $this->responseBody;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setHost
	// @desc		Configura o host a ser utilizado na pr�xima conex�o HTTP
	// @access		public
	// @param		host string	Nome ou IP do host
	// @return		void
	//!-----------------------------------------------------------------
	function setHost($host) {
		$this->httpHost = $host;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setPort
	// @desc		Configura a porta a ser utilizada na pr�xima conex�o HTTP
	// @access		public
	// @param		port int		N�mero da porta
	// @return		void
	//!-----------------------------------------------------------------
	function setPort($port) {
		$this->httpPort = $port;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setHttpVersion
	// @desc		Seta a vers�o do protocolo HTTP a ser utilizada
	// @access		public
	// @param		version string	Vers�o do protocolo
	// @return		void
	// @note		Os valores aceitos pela classe s�o "1.0" e "1.1"
	//!-----------------------------------------------------------------
	function setHttpVersion($version) {
		if (in_array($version, array('1.0', '1.1')))
			$this->httpVersion = $version;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setUserAgent
	// @desc		Seta o agente a ser enviado nos cabe�alhos das requisi��es
	// @access		public
	// @param		userAgent string	Descri��o do agente
	// @return		void
	//!-----------------------------------------------------------------
	function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setReferer
	// @desc		Seta o referente da requisi��o
	// @access		public
	// @param		referer string	Referente a ser utilizado
	// @return		void
	//!-----------------------------------------------------------------
	function setReferer($referer) {
		$this->referer = $referer;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setAuth
	// @desc		Configura os dados de autentica��o a serem utilizados
	// @access		public
	// @param		userName string	Nome de usu�rio
	// @param		password string	Senha
	// @return		bool
	//!-----------------------------------------------------------------
	function setAuth($userName, $password) {
		if (trim($userName) != '' && trim($password) != '') {
			$this->useAuth = TRUE;
			$this->authUser = trim($userName);
			$this->authPass = trim($password);
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setProxy
	// @desc		Seta os dados de servidor proxy a serem utilizados
	// @access		public
	// @param		host string		Servidor proxy
	// @param		port int			Porta de conex�o
	// @param		userName string	"" Nome de usu�rio, se o servidor requer autentica��o
	// @param		password string	"" Senha de usu�rio, se o servidor requer autentica��o
	// @return		bool
	//!-----------------------------------------------------------------
	function setProxy($host, $port, $userName = '', $password = '') {
		if (trim($host) != '' && TypeUtils::isInteger($port)) {
			$this->keepAlive = FALSE;
			$this->useProxy = TRUE;
			$this->proxyHost = trim($host);
			$this->proxyPort = $port;
			if (trim($userName) != '' && trim($password) != '') {
				$this->proxyUser = trim($userName);
				$this->proxyPass = trim($password);
			}
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setKeepAlive
	// @desc		Configura o tipo de conex�o a ser utilizado
	// @access		public
	// @param		setting bool		"TRUE" O valor TRUE habilita conex�es do tipo Keep-Alive
	// @return		void
	//!-----------------------------------------------------------------
	function setKeepAlive($setting = TRUE) {
		if (!$this->useProxy)
			$this->keepAlive = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setFollowRedirects
	// @desc		Seta o flag que habilita os redirecionamentos
	// @access		public
	// @param		setting bool		"TRUE" O valor TRUE faz com que o
	//									cliente siga os redirecionamentos
	//									indicados nos cabe�alhos de resposta
	// @return		void
	//!-----------------------------------------------------------------
	function setFollowRedirects($setting = TRUE) {
		$this->followRedirects = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::setRequestHeader
	// @desc		Insere ou altera um cabe�alho na requisi��o
	// @access		public
	// @param		name string		Nome do cabe�alho
	// @param		value mixed		Valor para o cabe�alho
	// @return		void
	//!-----------------------------------------------------------------
	function setRequestHeader($name, $value) {
		if (trim($name) != '' && trim($value) != '') {
			$formattedName = $this->_formatHeaderName(trim($name));
			$this->requestHeaders[$formattedName] = $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::removeRequestHeader
	// @desc		Remove um cabe�alho da requisi��o
	// @access		public
	// @param		name string		Nome do cabe�alho
	// @return		bool
	//!-----------------------------------------------------------------
	function removeRequestHeader($name) {
		if (trim($name) != '') {
			$formattedName = $this->_formatHeaderName(trim($name));
			if (isset($this->requestHeaders[$formattedName])) {
				unset($this->requestHeaders[$formattedName]);
				return TRUE;
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::reset
	// @desc		Reseta as propriedades do cliente, para cria��o de novas
	//				conex�es utilizando as configura��es padr�o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function reset() {
		$this->httpPort = HTTP_DEFAULT_PORT;
		$this->httpVersion = '1.1';
		$this->userAgent = 'PHP2Go Http Client ' . PHP2GO_VERSION . ' (compatible; MSIE 6.0; Linux)';
		$this->referer = NULL;
		$this->keepAlive = FALSE;
		$this->useAuth = FALSE;
		$this->useProxy = FALSE;
		$this->followRedirects = FALSE;
		$this->_resetRequest();
		$this->_resetResponse();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::doHead
	// @desc		Envia o comando HEAD ao servidor HTTP
	// @access		public
	// @param		uri string	URI da requisi��o
	// @return		int Status contido na resposta do servidor
	// @note		Retorna FALSE em caso de problemas na conex�o com o servidor
	//!-----------------------------------------------------------------
	function doHead($uri) {
		// verifica a URI
		if (TypeUtils::isNull($uri) || empty($uri))
			$uri = '/';
		// recria a conex�o ao host se for necess�rio
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// insere os cabe�alhos b�sicos
		$this->_setDefaultHeaders($uri);
		// monta o comando e envia ao servidor
		$this->currentMethod = 'HEAD';
		$data = sprintf("%s %s HTTP/%s%s%s%s", $this->currentMethod, $uri, $this->httpVersion, HTTP_CRLF, $this->_assembleRequestHeaders(), HTTP_CRLF);
		$this->_sendCommand($data);
		$this->_getResponse();
		$this->_ensureConnectionRelease();
		if ($this->_processUseProxyResponse())
			$this->doHead($uri);
		return $this->getStatus();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::doGet
	// @desc		Envia um comando GET ao servidor HTTP, que significa a busca
	//				de todo e qualquer conte�do associado � URI inclu�da na requisi��o
	// @access		public
	// @param		uri string	URI a ser enviada na requisi��o
	// @return		int Status contido na resposta do servidor (ver constantes da classe)
	// @note		Retorna FALSE em caso de problemas na conex�o com o servidor
	//!-----------------------------------------------------------------
	function doGet($uri) {
		// verifica a URI
		if (TypeUtils::isNull($uri) || empty($uri))
			$uri = '/';
		// recria a conex�o ao host se for necess�rio
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// insere os cabe�alhos b�sicos
		$this->_setDefaultHeaders($uri);
		// monta o comando e envia ao servidor
		$this->currentMethod = 'GET';
		$data = sprintf("%s %s HTTP/%s%s%s%s", $this->currentMethod, $uri, $this->httpVersion, HTTP_CRLF, $this->_assembleRequestHeaders(), HTTP_CRLF);
		$this->_sendCommand($data);
		$this->_getResponse();
		$this->_ensureConnectionRelease();
		$this->_processRedirectResponse($uri);
		if ($this->_processUseProxyResponse())
			$this->doGet($uri);
		return $this->getStatus();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::doPost
	// @desc		Envia um comando POST ao servidor HTTP, juntamente com
	//				dados de um formul�rio, no formato www-form-urlencoded
	// @access		public
	// @param		uri string		URI da requisi��o
	// @param		formData array	Vetor de dados do formul�rio
	// @return		int Status contido na resposta do servidor (ver constantes da classe)
	// @note		Retorna FALSE em caso de problemas na conex�o com o servidor
	//!-----------------------------------------------------------------
	function doPost($uri, $formData) {
		// recria a conex�o ao host se for necess�rio
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// insere os cabe�alhos b�sicos
		$this->_setDefaultHeaders($uri);
		// monta o corpo com os dados do formul�rio
		$body = $this->_assembleFormData($formData) . HTTP_CRLF . HTTP_CRLF;
		$this->requestBody = $body;
		// insere cabe�alhos extra
		$this->setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		$this->setRequestHeader('Content-Length', strlen($body));
		// envia o comando e retorna o status da opera��o
		$this->_sendPost($uri);
		$this->_processRedirectResponse($uri);
		if ($this->_processUseProxyResponse())
			$this->doPost($uri, $formData);
		return $this->getStatus();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::doMultipartPost
	// @desc		Envia um comando POST composto por m�ltiplas partes, que
	//				podem conter dados de um formul�rio e/ou arquivos
	// @access		public
	// @param		uri string		URI da requisi��o
	// @param		formData array	Vetor de campos de formul�rio
	// @param		formFiles array	"NULL" Vetor de arquivos para upload
	// @return		int Status contido na resposta do servidor (ver constantes da classe)
	// @note		Retorna FALSE em caso de problemas na conex�o com o servidor
	//!-----------------------------------------------------------------
	function doMultipartPost($uri, $formData, $formFiles = NULL) {
		// recria a conex�o ao host se for necess�rio
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// insere os cabe�alhos b�sicos
		$this->_setDefaultHeaders($uri);
		// monta o corpo com os dados do formul�rio
		$boundary = '----=_NextPart' . date( 'YmdHis' ) . '_' . rand(10000, 99999);
		$body = $this->_assembleMultipartData($boundary, $formData, $formFiles) . HTTP_CRLF . HTTP_CRLF;
		$this->requestBody = $body;
		// insere cabe�alhos extra
		$this->setRequestHeader('Content-Type', 'multipart/form-data; boundary=' . $boundary);
		$this->setRequestHeader('Content-Length', strlen($body));
		// envia o comando e retorna o status da opera��o
		$this->_sendPost($uri);
		$this->_processRedirectResponse($uri);
		if ($this->_processUseProxyResponse())
			$this->doMultipartPost($uri, $formData, $formFiles);
		return $this->getStatus();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::doXmlPost
	// @desc		Envia um comando POST ao servidor contendo dados XML
	// @access		public
	// @param		uri string		URI da requisi��o
	// @param		xmlData string	Dados XML ou nome do arquivo
	// @param		byFile bool		"FALSE" Indica se o par�metro $xmlData representa dados XML ou o nome de um arquivo
	// @param		charset string	"NULL" Charset do conte�do XML enviado
	// @return		int Status contido na resposta do servidor
	// @note		Retorna FALSE em caso de problemas na conex�o com o servidor
	//!-----------------------------------------------------------------
	function doXmlPost($uri, $xmlData, $byFile=FALSE, $charset=NULL) {
		if (empty($charset))
			$charset = PHP2Go::getConfigVal('CHARSET', FALSE);
		// recria a conex�o ao host se for necess�rio
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// insere os cabe�alhos b�sicos
		$this->_setDefaultHeaders($uri);
		// cria o corpo da requisi��o com o conte�do XML
		if ($byFile) {
			$FileManager = new FileManager();
			if ($FileManager->open($xmlData))
				$body = $FileManager->readFile();
			else
				return FALSE;
		} else {
			$body = $xmlData;
		}
		$this->requestBody = $body;
		// insere cabe�alhos extra
		$this->setRequestHeader('Content-Type', 'text/xml; charset=' . $charset);
		$this->setRequestHeader('Content-Length', strlen($body));
		// envia o comando e retorna o status da opera��o
		$this->_sendPost($uri);
		$this->_processRedirectResponse($uri);
		if ($this->_processUseProxyResponse())
			$this->doXmlPost($uri, $xmlData, $byFile, $charset);
		return $this->getStatus();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::doDelete
	// @desc		Envia o comando DELETE, que busca remover um recurso armazenado no servidor
	// @access		public
	// @param		uri string		URI da requisi��o
	// @return		int Status contido na resposta do servidor
	//!-----------------------------------------------------------------
	function doDelete($uri) {
		// recria a conex�o ao host se for necess�rio
		if (($this->keepAlive && !parent::isConnected()) || !$this->keepAlive)
			if (!$this->_connect()) {
				return FALSE;
			}
		// insere os cabe�alhos b�sicos
		$this->_setDefaultHeaders($uri);
		// monta o comando e envia ao servidor
		$data = sprintf("DELETE %s HTTP/%s%s%s%s", $uri, $this->httpVersion, HTTP_CRLF, $this->_assembleRequestHeaders(), HTTP_CRLF);
		$this->_sendCommand($data);
		$this->_getResponse();
		$this->_ensureConnectionRelease();
		return $this->getStatus();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_sendPost
	// @desc		M�todo que envia um comando POST em conjunto com o corpo
	//				da requisi��o, utilizado nos m�todos doPost, doMultipartPost
	//				e doXmlPost
	// @access		private
	// @param		uri string	URI da requisi��o
	// @return		void
	//!-----------------------------------------------------------------
	function _sendPost($uri) {
		// verifica a URI
		if (TypeUtils::isNull($uri) || empty($uri))
			$uri = '/';
		// monta o comando e envia ao servidor
		$this->currentMethod = 'POST';
		$data = sprintf("%s %s HTTP/%s%s%s%s", $this->currentMethod, $uri, $this->httpVersion, HTTP_CRLF, $this->_assembleRequestHeaders(), HTTP_CRLF);
		$this->_sendCommand($data);
		usleep(10);
		$this->_sendCommand($this->requestBody);
		$this->_getResponse();
		$this->_ensureConnectionRelease();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_sendCommand
	// @desc		Envia um comando ao servidor HTTP
	// @access		private
	// @param		data string	Dados do comando
	// @return		void
	//!-----------------------------------------------------------------
	function _sendCommand($data) {
		parent::write($data);
		if ($this->debug)
			print('HTTP DEBUG --- FROM CLIENT : ' . nl2br($data));
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_setDefaultHeaders
	// @desc		Adiciona � requisi��o os cabe�alhos principais, como host,
	//				tipo de conex�o, uso de proxy, controle de cache, agente,
	//				tipo de conte�do autorizado, referente e cookies
	// @access		private
	// @param		&uri string	URI inclu�da na requisi��o
	// @return		void
	//!-----------------------------------------------------------------
	function _setDefaultHeaders(&$uri) {
		if ($this->useProxy) {
			$this->setRequestHeader('Host', $this->httpHost . ':' . $this->httpPort);
			$this->setRequestHeader('Proxy-Connection', ($this->keepAlive ? 'Keep-Alive' : 'Close'));
			if (isset($this->proxyUser))
				$this->setRequestHeader('Proxy-Authorization', 'Basic ' . base64_encode($this->proxyUser . ':' . $this->proxyPass));
			$uri = 'http://' . $this->httpHost . ':' . $this->httpPort . $uri;
		} else {
			$this->setRequestHeader('Host', $this->httpHost);
			$this->setRequestHeader('Connection', ($this->keepAlive ? 'Keep-Alive' : 'Close'));
			$this->setRequestHeader('Pragma', 'no-cache');
			$this->setRequestHeader('Cache-Control', 'no-cache');
		}
		if ($this->useAuth)
			$this->setRequestHeader('Authorization', 'Basic ' . base64_encode($this->authUser . ':' . $this->authPass));
		$cookies = $this->_getCookies($this->httpHost, $this->_getCurrentPath($uri));
		$this->setRequestHeader('User-Agent', $this->userAgent);
		$this->setRequestHeader('Accept', '*/*');
		$this->setRequestHeader('Referer', (isset($this->referer) ? $this->referer : ''));
		$this->setRequestHeader('Cookie', $cookies);
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_assembleRequestHeaders
	// @desc		A partir dos cabe�alhos inclu�dos na requisi��o, monta
	//				uma string com seus nomes e valores
	// @access		private
	// @return		string String no formato header: valor <CRLF> header: valor
	//				para montagem da requisi��o
	//!-----------------------------------------------------------------
	function _assembleRequestHeaders() {
		$headerString = '';
		foreach($this->requestHeaders as $name => $value) {
			$headerString .= sprintf("%s: %s%s", $name, $value, HTTP_CRLF);
		}
		return $headerString;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_assembleFormData
	// @desc		Monta uma string contendo os nomes e valores de um formul�rio
	//				que ser�o enviados juntamente com um comando POST
	// @access		private
	// @param		formData array	Vetor de dados do formul�rio
	// @param		paramName string	"" Utilizado em vari�veis de formul�rio na forma de vetores
	// @return		string String no formato urlencode com as vari�veis do formul�rio
	//!-----------------------------------------------------------------
	function _assembleFormData($formData, $paramName = '') {
		$formString = '';
		foreach ($formData as $key => $value)
			if (!TypeUtils::isArray($value)) {
				if (trim($paramName) != '')
					$formString .= sprintf("&%s[%s]=%s", $paramName, $key, urlencode($value));
				else
					$formString .= sprintf("&%s=%s", $key, urlencode($value));
			} else {
				$formString .= '&' . $this->_assembleFormData($formData[$key], $key);
			}
		return substr($formString, 1);
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_assembleMultipartData
	// @desc		Monta o corpo da requisi��o HTTP contendo dados de vari�veis
	//				de formul�rio e dados de arquivo para upload
	// @access		private
	// @param		boundary string	Delimitador das partes da requisi��o
	// @param		formData array	Vetor de vari�veis de formul�rio
	// @param		formFiles array	"NULL" Vetor de arquivos a serem enviados na requisi��o
	// @return		string String contendo os dados do corpo da requisi��o
	//!-----------------------------------------------------------------
	function _assembleMultipartData($boundary, $formData, $formFiles = NULL) {
		$boundary = '--' . $boundary;
		$formString = '';
		if (TypeUtils::isArray($formData)) {
			foreach ($formData as $name => $data) {
				$formString .= sprintf("%s%sContent-Disposition: form-data; name=\"%s\"%s%s%s%s", $boundary, HTTP_CRLF, $name, HTTP_CRLF, HTTP_CRLF, $data, HTTP_CRLF);
			}
		}
		if (TypeUtils::isArray($formFiles)) {
			foreach ($formFiles as $data) {
				if (!isset($data['type'])) {
					$data['type'] = MimeType::getFromFileName($data['file']);
				}
				$formString .= sprintf("%s%sContent-Disposition: form-data; name=\"%s\"; filename=\"%s\"%sContent-Type: %s%s%s%s%s", $boundary, HTTP_CRLF, $data['name'], $data['file'], HTTP_CRLF, $data['type'], HTTP_CRLF, HTTP_CRLF, $data['data'], HTTP_CRLF);
			}
		}
		$formString .= $boundary . '--' . HTTP_CRLF;
		return $formString;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_getResponse
	// @desc		Busca a resposta � uma requisi��o HTTP enviada
	// @access		private
	// @param		getBody bool	Indica se o corpo da resposta tamb�m deve ser buscado
	// @return		void
	//!-----------------------------------------------------------------
	function _getResponse($getBody = TRUE) {
		// inicializa os dados de requisi��o/resposta
		$this->_resetRequest();
		$this->_resetResponse();
		while (1) {
			// l� linhas do socket at� que seja recebida uma linha contendo apenas CRLF
			$rawHeaders = '';
			while (($line = parent::readLine()) != HTTP_CRLF || $rawHeaders == '')
				if ($line != HTTP_CRLF) $rawHeaders .= $line;
			// interpreta os headers da resposta
			$this->_parseResponseHeaders($rawHeaders);
			// trata o status CONTINUE
			if ($this->getStatus() != HTTP_STATUS_CONTINUE) break;
			parent::writeLine();
		}
		if ($this->debug)
			print('HTTP DEBUG --- FROM SERVER : ' . $rawHeaders . '<br>');
		if ($getBody) {
			$body = '';
			if (strtolower($this->getResponseHeader('Transfer-Encoding')) != 'chunked' && !$this->keepAlive)
				$body = parent::readAllContents();
			else if (!TypeUtils::isNull($this->getResponseHeader('Content-Length'))) {
				$contentLength = TypeUtils::parseInteger($this->getResponseHeader('Content-Length'));
				$body = parent::read($contentLength);
			} else if (!TypeUtils::isNull($this->getResponseHeader('Transfer-Encoding')))
				if ($this->getResponseHeader('Transfer-Encoding') == 'chunked') {
					$chunkSize = TypeUtils::parseInteger(hexdec(parent::readLine()));
					while ($chunkSize > 0) {
						$body .= parent::read($chunkSize);
						parent::read(strlen(HTTP_CRLF));
						$chunkSize = TypeUtils::parseInteger(hexdec(parent::readLine()));
					}
				}
			$this->responseBody = $body;
		}
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_parseResponseHeaders
	// @desc		Este m�todo parseia e armazena os cabe�alhos recebidos
	//				na resposta capturada em HttpClient::_getResponse
	// @access		private
	// @param		headers string	Cabe�alhos recebidos na resposta
	// @return		void
	//!-----------------------------------------------------------------
	function _parseResponseHeaders($headers) {
		$headers = preg_replace("/^" . HTTP_CRLF . "/", '', $headers);
		$headersArray = explode(HTTP_CRLF, $headers);
		$matches = NULL;
		if (preg_match("'HTTP/(\d\.\d)\s+(\d+).*'i", $headersArray[0], $matches)) {
			$this->_setResponseHeader('Protocol-Version', $matches[1]);
			$this->_setResponseHeader('Status', $matches[2]);
		}
		array_shift($headersArray);
		foreach($headersArray as $headerValue) {
			if (ereg("([^:]+):(.*)", $headerValue, $matches)) {
				$key = $matches[1];
				$value = trim($matches[2]);
				if (strtoupper($key) == 'SET-COOKIE') {
					if ($Cookie = $this->_parseCookie($value))
						$this->cookies[$Cookie->getName()] = $Cookie;
				} else
					$this->_setResponseHeader($key, $value);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_setResponseHeader
	// @desc		Insere um cabe�alho de resposta
	// @access		private
	// @param		name string	Nome do cabe�alho
	// @param		value string	Valor do cabe�alho
	// @return		void
	//!-----------------------------------------------------------------
	function _setResponseHeader($name, $value) {
		if (trim($value) != '') {
			$formattedName = $this->_formatHeaderName($name);
			$this->responseHeaders[$formattedName] = $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_parseCookie
	// @desc		Instancia um objeto HttpCookie para interpretar um cabe�alho
	// 				Set-Cookie encontrado na resposta
	// @access		private
	// @param		cookieString string	Nome, valor e dados do cookie
	// @return		HttpCookie object	Objeto Cookie criado
	//!-----------------------------------------------------------------
	function _parseCookie($cookieString) {
		$Cookie = new HttpCookie();
		$Cookie->parseFromHeader($cookieString, $this->httpHost);
		return $Cookie;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_getCookies
	// @desc		Monta os cookies da requisi��o a partir dos que est�o
	//				armazenados na �ltima resposta, que n�o expiraram e que
	//				correspondem ao dom�nio/caminho atuais
	// @access		private
	// @param		domain string		Dom�nio atual
	// @param		path string		Diret�rio corrente
	// @return		string String contendo nomes e valores dos cookies v�lidos
	//!-----------------------------------------------------------------
	function _getCookies($domain, $path) {
		$cookieString = '';
		foreach($this->cookies as $cookieName => $Cookie) {
			if (!$Cookie->isExpired()) {
				if ($Cookie->isDomain($domain) && $Cookie->isPath($path))
					$cookieString = $Cookie->getName() . '=' . $Cookie->getValue() . '; ';
			} else
				unset($this->cookies[$cookieName]);
		}
		return $cookieString;
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_ensureConnectionRelease
	// @desc		Verifica se a conex�o deve ser fechada de acordo com as
	//				configura��es e os cabe�alhos de resposta
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _ensureConnectionRelease() {
		// fecha a conex�o se keepAlive == FALSE
		if (parent::isConnected() && !$this->keepAlive)
			$this->_close();
		// fecha a conex�o se for necess�rio
		if (!TypeUtils::isNull($this->getResponseHeader('Connection')))
			if ($this->keepAlive && strtolower($this->getResponseHeader('Connection')) == 'close') {
				$this->keepAlive = FALSE;
				$this->_close();
			}
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_processRedirectResponse
	// @desc		Verifica se a resposta indica redirecionamento para outra
	//				URI e efetua a opera��o em caso positivo
	// @access		private
	// @param		uri string	Endere�o de destino do redirecionamento
	// @return		void
	// @note		Os redirecionamentos s�o disparados quando o status devolvido
	//				pelo servidor HTTP indica mudan�a tempor�ria ou permanente de
	//				um recurso
	//!-----------------------------------------------------------------
	function _processRedirectResponse($uri) {
		// verifica se a configura��o da classe permite e se o status indica redirecionamento
		if ($this->followRedirects && in_array($this->getStatus(), array(HTTP_STATUS_MOVED_PERMANENTLY, HTTP_STATUS_FOUND, HTTP_STATUS_SEE_OTHER))) {
			// busca o cabe�alho Location na resposta do servidor
			$uri = $this->getResponseHeader('Location');
			if (!TypeUtils::isNull($uri) && !empty($uri)) {
				// instancia um objeto Url para interpretar o caminho de redirecionamento
				$Url = new Url($uri);
				$redirectHost = $Url->getHost();
				$redirectPort = TypeUtils::ifNull($Url->getPort(), HTTP_DEFAULT_PORT);
				$redirectFile = TypeUtils::ifNull($Url->getPath(), '/');
				$redirectQueryString = TypeUtils::ifNull($Url->getQueryString(), '');
				if (!empty($redirectQueryString))
					$redirectQueryString = '?' . $redirectQueryString;
				// atualiza as propriedades da classe caso o host ou a porta sejam modificados
				if ($redirectHost != $this->httpHost || $redirectPort != $this->httpPort) {
					$this->httpHost = $redirectHost;
					$this->httpPort = $redirectPort;
					if (!$this->useProxy)
						$this->_close();
				}
				usleep(100);
				$this->doGet($redirectFile . $redirectQueryString);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_processUseProxyResponse
	// @desc		Trata o indicativo de uso de proxy contido na resposta do servidor
	// @access		private
	// @return		bool Retorna TRUE se o m�todo deve ser executado novamente utilizando um proxy
	//!-----------------------------------------------------------------
	function _processUseProxyResponse() {
		if ($this->getStatus() == HTTP_STATUS_USE_PROXY) {
			$this->_close();
			$Url = new Url($this->getResponseHeader('Location'));
			$proxyHost = $Url->getHost();
			$proxyPort = TypeUtils::ifNull($Url->getPort(), HTTP_DEFAULT_PORT);
			$this->setProxy($proxyHost, $proxyPort);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_connect
	// @desc		Abre uma conex�o com o host HTTP configurado na classe
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _connect() {
		if (!isset($this->httpHost)) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_HTTP_MISSING_HOST');
			return FALSE;
		}
		if ($this->useProxy)
			return parent::connect($this->proxyHost, $this->proxyPort, NULL, HTTP_DEFAULT_TIMEOUT);
		else
			return parent::connect($this->httpHost, $this->httpPort, NULL, HTTP_DEFAULT_TIMEOUT);
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_close
	// @desc		Fecha a conex�o ativa
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _close() {
		parent::close();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_resetRequest
	// @desc		Reseta os dados de requisi��o HTTP da classe
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _resetRequest() {
		$this->requestHeaders = array();
		$this->cookieHeaders = array();
		$this->cookies = array();
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_resetResponse
	// @desc		Reseta os dados de resposta da classe
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _resetResponse() {
		$this->responseHeaders = array();
		$this->responseBody = '';
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_getCurrentPath
	// @desc		Busca o diret�rio corrente na URI da requisi��o
	// @access		private
	// @param		uri string	URI da requisi��o
	// @return		string Diret�rio corrente, utilizado para filtrar os cookies a serem enviados
	//!-----------------------------------------------------------------
	function _getCurrentPath($uri) {
		$uriParts = explode('/', $uri);
		array_pop($uriParts);
		$currentPath = implode('/', $uriParts) . '/';
		return ($currentPath != '') ? $currentPath : '/';
	}

	//!-----------------------------------------------------------------
	// @function	HttpClient::_formatHeaderName
	// @desc		Padroniza a nomenclatura de cabe�alhos inseridos em uma requisi��o
	// @access		private
	// @param		headerName string		Nome do cabe�alho
	// @return		string Nome padronizado
	//!-----------------------------------------------------------------
	function _formatHeaderName($headerName) {
		$formatted = ucwords(str_replace('-', ' ', strtolower($headerName)));
		return str_replace(' ', '-', $formatted);
	}
}
?>