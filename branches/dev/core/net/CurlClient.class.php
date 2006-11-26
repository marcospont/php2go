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
// $Header: /www/cvsroot/php2go/core/net/CurlClient.class.php,v 1.17 2006/05/07 15:21:49 mpont Exp $
// $Date: 2006/05/07 15:21:49 $

//------------------------------------------------------------------
import('php2go.file.FileSystem');
import('php2go.net.httpConstants', 'php', FALSE);
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		CurlClient
// @desc		Esta classe implementa uma camada de abstra��o sobre as fun��es
//				da extens�o CURL do PHP. A biblioteca CURL permite conex�es e comunica��o
//				com v�rios tipos diferentes de servidor utilizando tipos diferentes de
//				protocolos, como HTTP, HTTPS, FTP, Gopher, Telnet, Dict, LDAP, etc...
// @package		php2go.net
// @extends		PHP2Go
// @uses		FileSystem
// @uses		System
// @author		Marcos Pont
// @version		$Revision: 1.17 $
// @note		Para maiores informa��es sobre a extens�o CURL ou sobre a biblioteca,
//				visite a documenta��o do PHP: http://www.php.net/curl
//!-----------------------------------------------------------------
class CurlClient extends PHP2Go
{
	var $url;					// @var url string					URL a ser utilizada na transfer�ncia
	var $returnValue = FALSE;	// @var returnValue string			"FALSE" Valor de retorno da transfer�ncia
	var $returnFile;			// @var returnFile string			Nome de arquivo para grava��o do conte�do do retorno
	var $errorNumber;			// @var errorNumber int				C�digo de erro retornado pela opera��o de transfer�ncia
	var $errorString;			// @var errorString string			Mensagem de erro retornada na opera��o de transfer�ncia
	var $session;				// @var session resource			Armazena o ponteiro para a sess�o CURL ativa
	var $sessionActive = FALSE;	// @var sessionActive bool			"FALSE" Indica que existe uma sess�o CURL ativa
	var $_fileHandle = NULL;

	//!-----------------------------------------------------------------
	// @function	CurlClient::CurlClient
	// @desc		Construtor do objeto CurlClient
	// @access		public
	//!-----------------------------------------------------------------
	function CurlClient() {
		parent::PHP2Go();
		if (!System::loadExtension('curl'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'curl'));
		$this->init();
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::__destruct
	// @desc		Destrutor do objeto, fecha a sess�o se a mesma for mantida aberta
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		$this->close();
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::init
	// @desc		Inicializa a sess�o CURL
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function init() {
		$this->session = curl_init();
		$this->sessionActive = TRUE;
		$this->reset();
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::reset
	// @desc		Define os valores padr�o para as op��es b�sicas de
	//				configura��o da biblioteca CURL
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function reset() {
		$this->setOption(CURLOPT_POSTFIELDS, NULL);
		$this->setOption(CURLOPT_RETURNTRANSFER, 1);
		$this->setOption(CURLOPT_UPLOAD, 0);
		$this->setOption(CURLOPT_HEADER, 0);
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::getTransferError
	// @desc		Busca os erros retornados para a transfer�ncia, se existirem
	// @access		public
	// @return		string N�mero e mensagem de erro, se existirem
	//!-----------------------------------------------------------------
	function getTransferError() {
		if (!isset($this->session) || !isset($this->errorNumber))
			return '';
		return "[{$this->errorNumber}] $this->errorString";
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::getTransferInfo
	// @desc		Busca informa��es sobre a transfer�ncia CURL atual
	// @access		public
	// @param		infoName string		"" Nome da informa��o desejada
	// @return		string Valor do atributo solicitado ou FALSE em caso de erros
	// @note		Para maiores informa��es sobre as op��es dispon�veis para
	//				o par�metro $infoName, consulte o manual da fun��o curl_getinfo()
	//				em http://www.php.net/curl_getinfo
	//!-----------------------------------------------------------------
	function getTransferInfo($infoName='') {
		if (!isset($this->session))
			return FALSE;
		return (!empty($infoName) ? curl_getinfo($this->session, $infoName) : curl_getinfo($this->session));
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::setUrl
	// @desc		Configura a URL alvo da transfer�ncia
	// @access		public
	// @param		url mixed	URL (string ou inst�ncia da classe URL)
	// @return		void
	//!-----------------------------------------------------------------
	function setUrl($url) {
		if (TypeUtils::isInstanceOf($url, 'Url'))
			$this->url = $url->getUrl();
		else
			$this->url = $url;
		$this->setOption(CURLOPT_URL, $this->url);
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::setReferer
	// @desc		M�todo que permite a configura��o do referente a ser utilizado na transfer�ncia CURL
	// @access		public
	// @param		referer string	Referente a ser utilizado
	// @return		void
	//!-----------------------------------------------------------------
	function setReferer($referer) {
		$this->setOption(CURLOPT_REFERER, $referer);
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::setUserAgent
	// @desc		Define a identifica��o (User-Agent) a ser enviada na requisi��o
	// @access		public
	// @param		userAgent string	Identifica��o
	// @return		void
	//!-----------------------------------------------------------------
	function setUserAgent($userAgent) {
		$this->setOption(CURLOPT_USERAGENT, $userAgent);
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::setPostData
	// @desc		Seta os par�metros POST da transfer�ncia CURL
	// @access		public
	// @param		dataArray array	Vetor associativo de par�metros
	// @return		void
	//!-----------------------------------------------------------------
	function setPostData($dataArray) {
		if (TypeUtils::isArray($dataArray) && !empty($dataArray)) {
			foreach($dataArray as $key => $value)
				$request[] = "$key=" . urlencode($value);
			$postFields = implode('&', $request);
			$this->setOption(CURLOPT_POSTFIELDS, $postFields);
		}
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::setOption
	// @desc		M�todo gen�rico que permite configurar o valor de
	//				qualquer das op��es da transfer�ncia CURL
	// @access		public
	// @param		optionCode int	C�digo da op��o a ser setada
	// @param		optionValue mixed	Valor para a op��o
	// @return		void
	// @note		Para maiores informa��es sobre os valores habilitados para
	//				as op��es (par�metro $optionCode), consulte o manual da
	//				fun��o curl_setopt()
	//!-----------------------------------------------------------------
	function setOption($optionCode, $optionValue) {
		if (!$this->sessionActive)
			$this->init();
		if ($optionCode > 0)
			curl_setopt($this->session, $optionCode, $optionValue);
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::returnToFile
	// @desc		Configura a classe para redirecionar o retorno da transfer�ncia
	//				CURL para um determinado arquivo
	// @access		public
	// @param		fileName string	Nome do arquivo
	// @return		void
	//!-----------------------------------------------------------------
	function returnToFile($fileName) {
		if (FileSystem::exists($fileName) && $this->_fileHandle = @fopen($fileName, 'wb')) {
			$this->setOption(CURLOPT_FILE, $this->_fileHandle);
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::uploadFile
	// @desc		Configura o upload de um arquivo para o servidor remoto
	//				na transfer�ncia CURL
	// @access		public
	// @param		fileName string	Nome do arquivo a ser transferido
	// @return		bool
	//!-----------------------------------------------------------------
	function uploadFile($fileName) {
		$fileName = FileSystem::getAbsolutePath($fileName);
		if (FileSystem::exists($fileName)) {
			$this->_fileHandle = @fopen($fileName, 'rb');
			$this->setOption(CURLOPT_UPLOAD, 1);
			$this->setOption(CURLOPT_INFILE, $this->_fileHandle);
			$this->setOption(CURLOPT_INFILESIZE, filesize($fileName));
		}
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::doGet
	// @desc		Inicializa e executa a transfer�ncia CURL no modo 'GET'
	// @access		public
	// @return		mixed Se o retorno for direcionado para arquivo, retorna
	//				TRUE ou FALSE. Em caso contr�rio, retorna o resultado da transfer�ncia
	//!-----------------------------------------------------------------
	function doGet() {
		if (!isset($this->sessionActive))
			$this->init();
		if (!$this->returnValue = @curl_exec($this->session)) {
			$this->errorNumber = curl_errno($this->session);
			$this->errorString = curl_error($this->session);
		}
		if (isset($this->_fileHandle)) {
			@fclose($this->_fileHandle);
			unset($this->_fileHandle);
		}
		return $this->returnValue;
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::doPost
	// @desc		Inicializa e executa a transfer�ncia CURL no modo 'POST'
	// @access		public
	// @return		mixed Se o retorno for direcionado para arquivo, retorna
	//				TRUE ou FALSE. Em caso contr�rio, retorna o resultado da transfer�ncia
	//!-----------------------------------------------------------------
	function doPost() {
		if (!isset($this->sessionActive))
			$this->init();
		//$this->setOption(CURLOPT_POST, 1);
		if (!$this->returnValue = @curl_exec($this->session)) {
			$this->errorNumber = curl_errno($this->session);
			$this->errorString = curl_error($this->session);
		}
		if (isset($this->_fileHandle)) {
			@fclose($this->_fileHandle);
			unset($this->_fileHandle);
		}
		return $this->returnValue;
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::parseResponse
	// @desc		M�todo utilit�rio para interpretar o valor da resposta
	//				de uma requisi��o, montando um vetor com c�digo, cabe�alhos
	//				e corpo
	// @access		public
	// @param		response string		Conte�do original do retorno da requisi��o
	// @param		crlf string			Caractere(s) de final de linha
	// @return		array C�digo, cabe�alhos (hash array) e corpo da resposta
	//!-----------------------------------------------------------------
	function parseResponse($response, $crlf="\r\n") {
		$result = array();
		$parts = explode($crlf . $crlf, $response, 2);
		if (sizeof($parts) == 2) {
			list($headers, $body) = $parts;
		} elseif (sizeof($parts) == 1) {
			$headers = $parts[0];
			$body = '';
		} else {
			$headers = '';
			$body = '';
		}
		$headerLines = explode($crlf, $headers);
   		$headerLine = array_shift($headerLines);
		if (preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@', $headerLine, $matches)) {
			$result['code'] = $matches[1];
   		} else {
   			$result['code'] = -1;
   		}
   		$result['headers'] = array();
   		foreach ($headerLines as $headerLine) {
   			list($header, $value) = explode(': ', $headerLine, 2);
   			$result['headers'][$header] = $value;
   		}
   		$result['body'] = $body;
   		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	CurlClient::close
	// @desc		Encerra a sess�o CURL
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function close() {
		if ($this->sessionActive) {
			@curl_close($this->session);
			$this->sessionActive = FALSE;
		}
	}
}
?>