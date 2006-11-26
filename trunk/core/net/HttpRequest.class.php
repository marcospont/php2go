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
// $Header: /www/cvsroot/php2go/core/net/HttpRequest.class.php,v 1.33 2006/11/25 12:00:11 mpont Exp $
// $Date: 2006/11/25 12:00:11 $

//------------------------------------------------------------------
import('php2go.net.UserAgent');
import('php2go.session.SessionManager');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		HttpRequest
// @desc		Esta classe cont�m fun��es utilit�rias que permitem
//				interagir com a requisi��o HTTP. Detec��o de informa��es
//				sobre o browser do cliente, vari�veis de ambiente e de
//				sistema e par�metros da requisi��o s�o algumas das funcionalidades
//				oferecidas
// @package		php2go.net
// @extends		PHP2Go
// @uses		Environment
// @uses		StringUtils
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.33 $
//!-----------------------------------------------------------------
class HttpRequest extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	HttpRequest::&getInstance
	// @desc		Retorna uma inst�ncia �nica da classe
	// @access		public
	// @return		HttpRequest object	Inst�ncia da classe
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			$instance = new HttpRequest;
		}
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::&request
	// @desc		Retorna todo o conte�do do superglobal $_REQUEST
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function &request() {
		$request =& $_REQUEST;
		return $request;
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::get
	// @desc		Consulta o valor de um par�metro GET
	// @access		public
	// @param		paramName string		"" Nome do par�metro solicitado
	// @return		mixed Valor do par�metro ou NULL
	// @note		Se o nome da chave for omitido ($paramName == ''), retorna o vetor $_GET por completo
	// @static
	//!-----------------------------------------------------------------
	function get($paramName='') {
		if (trim($paramName) != '') {
			$req =& HttpRequest::getInstance();
			return $req->_fetchVar(trim($paramName), 'GET');
		} else
			return TypeUtils::toArray($_GET);
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::post
	// @desc		Consulta o valor de um par�metro POST
	// @access		public
	// @param		paramName string		"" Nome do par�metro solicitado
	// @return		mixed Valor do par�metro ou NULL
	// @note		Se o nome da chave for omitido ($paramName == ''), retorna o vetor $_POST por completo
	// @static
	//!-----------------------------------------------------------------
	function post($paramName='') {
		if (trim($paramName) != '') {
			$req =& HttpRequest::getInstance();
			return $req->_fetchVar(trim($paramName), 'POST');
		} else
			return TypeUtils::toArray($_POST);
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::cookie
	// @desc		Consulta o valor de um cookie
	// @access		public
	// @param		paramName string		"" Nome do par�metro solicitado
	// @return		mixed Valor do par�metro ou NULL
	// @note		Se o nome da chave for omitido, o m�todo retorna o vetor $_COOKIE completo
	// @static
	//!-----------------------------------------------------------------
	function cookie($paramName='') {
		if (trim($paramName) != '') {
			$req =& HttpRequest::getInstance();
			return $req->_fetchVar(trim($paramName), 'COOKIE');
		} else
			return TypeUtils::toArray($_COOKIE);
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::session
	// @desc		Consulta o valor de uma vari�vel de sess�o
	// @access		public
	// @param		paramName string		"" Nome do par�metro solicitado
	// @return		mixed Valor do par�metro ou NULL
	// @note		Se o nome da chave for omitido, o m�todo retorna o vetor $_SESSION completo
	// @static
	//!-----------------------------------------------------------------
	function session($paramName='') {
		if (trim($paramName) != '') {
			$req =& HttpRequest::getInstance();
			return $req->_fetchVar(trim($paramName), 'SESSION');
		} else
			return isset($_SESSION) ? TypeUtils::toArray($_SESSION) : FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::getVar
	// @desc		Busca e retorna o valor de uma vari�vel
	// @access		public
	// @param		variableName string		Nome da vari�vel
	// @param		where string				"all" Indica onde a vari�vel deve ser buscada
	// @param		searchOrder string		"" Ordem de busca nos dados da requisi��o
	// @return		mixed Valor da vari�vel ou NULL se n�o encontrada
	// @note		O comportamento padr�o do m�todo � buscar pela vari�vel em todos
	//				os locais poss�veis na requisi��o, de acordo com a ordem estabelecida
	//				pelo par�metro de inicializa��o 'variables_order'. Esta ordem pode ser
	//				sobreposta atrav�s do par�metro $searchOrder
	// @note		Tamb�m pode ser realizada uma busca expl�cita em um dos reposit�rios:
	//				'reg', 'object', 'get', 'post', 'cookie', 'session', 'env', 'server' e 'request'
	// @note		O reposit�rio req representa uma inst�ncia da classe Registry, que mant�m o conte�do
	//				do vetor $GLOBALS em suas propriedades
	// @note		O reposit�rio obj representa os objetos de sess�o (php2go.session.SessionObject)
	//				criados. Modo de utiliza��o: session_var:property_name
	// @static
	//!-----------------------------------------------------------------
	function getVar($variableName, $where='all', $searchOrder='EGPCSOR') {
		$req =& HttpRequest::getInstance();
		$return = NULL;
		if (strtoupper($where) == 'ALL') {
            for ($i=0; $i<strlen($searchOrder); ++$i) {
                switch ($searchOrder{$i}) {
					case 'E' :
						$value = Environment::get($variableName);
						if (!TypeUtils::isNull($value, TRUE))
							return $value;
						break;
					case 'G' :
						$value = $req->_fetchVar($variableName, 'GET');
						if (!TypeUtils::isNull($value, TRUE))
							return $value;
						break;
					case 'P' :
						$value = $req->_fetchVar($variableName, 'POST');
						if (!TypeUtils::isNull($value, TRUE))
							return $value;
						break;
					case 'C' :
						$value = $req->_fetchVar($variableName, 'COOKIE');
						if (!TypeUtils::isNull($value, TRUE))
							return $value;
						break;
					case 'S' :
						$value = $req->_fetchVar($variableName, 'SESSION');
						if (!TypeUtils::isNull($value, TRUE))
							return $value;
						break;
					case 'O' :
						$value = SessionManager::getObjectProperty($variableName);
						if (!TypeUtils::isNull($value, TRUE))
							return $value;
						break;
					case 'R' :
						$value = Registry::get($variableName);
						if (!TypeUtils::isNull($value, TRUE))
							return $value;
						break;
				}
            }
		} else {
			$where = strtoupper($where);
			if ($where == 'REG')
				$return = Registry::get($variableName);
			elseif ($where == 'OBJECT')
				$return = SessionManager::getObjectProperty($variableName);
			else
				$return = $req->_fetchVar($variableName, $where);
		}
		return $return;
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::getHeaders
	// @desc		Retorna um vetor contendo todos os headers HTTP da requisi��o atual
	// @access		public
	// @return		array Vetor de headers da requisi��o HTTP
	// @static
	//!-----------------------------------------------------------------
	function getHeaders() {
		return apache_request_headers();
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::method
	// @desc		Busca o m�todo utilizado na requisi��o
	// @access		public
	// @return		string
	// @static
	//!-----------------------------------------------------------------
	function method() {
		return Environment::get('REQUEST_METHOD');
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpRequest::isGet
	// @desc		Verifica se o request method � GET
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isGet() {
		$method = Environment::get('REQUEST_METHOD');
		return ($method == 'GET');
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpRequest::isPost
	// @desc		Verifica se o request method � POST
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isPost() {
		$method = Environment::get('REQUEST_METHOD');
		return ($method == 'POST');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::protocol
	// @desc		Busca o protocolo utilizado na requisi��o
	// @access		public
	// @return		string Nome do protocolo da requisi��o: http ou https
	// @static
	//!-----------------------------------------------------------------
	function protocol() {
		if (HttpRequest::isSecure())
			return 'https';
		else
			return 'http';
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::isSecure
	// @desc		Verifica se o protocolo HTTP seguro est� sendo utilizado nesta requisi��o
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isSecure() {
		return (strtolower(Environment::get('HTTPS')) == 'on' || Environment::has('SSL_PROTOCOL_VERSION'));
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::serverHostName
	// @desc		Retorna o nome do host da requisi��o atual, juntamente
	//				com a porta utilizada na requisi��o
	// @access		public
	// @return		string Nome do host e n�mero da porta (se ela for diferente da porta padr�o do protocolo)
	// @static
	//!-----------------------------------------------------------------
	function serverHostName() {
		$port = Environment::has('SERVER_PORT') ? Environment::get('SERVER_PORT') : '80';
		$protocol = HttpRequest::protocol();
		if (($protocol == 'http' && $port != '80') || ($protocol == 'https' && $port != '443'))
			$port = ":{$port}";
		else
			$port = '';
		return Environment::get('HTTP_HOST') . $port;
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::serverName
	// @desc		Retorna o nome do servidor
	// @access		public
	// @return		string Nome do servidor
	// @static
	//!-----------------------------------------------------------------
	function serverName() {
		return Environment::get('SERVER_NAME');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::scriptName
	// @desc		Retorna o caminho completo no servidor do script atual
	// @acess		public
	// @return		string Caminho do script atual
	// @static
	//!-----------------------------------------------------------------
	function scriptName() {
		return Environment::get('SCRIPT_NAME');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::scriptInfo
	// @desc		Constr�i um vetor com nome completo, caminho base e
	//				nome de arquivo para o script atual
	// @access		public
	// @return		array Vetor associativo contendo tr�s posi��es: path - caminho
	//				completo; base - diret�rio base do script; file - arquivo
	// @static
	//!-----------------------------------------------------------------
	function scriptInfo() {
		$scriptName = HttpRequest::scriptName();
		$scriptFile = basename($scriptName);
		$scriptBase = substr($scriptName, 0, strlen($scriptName) - strlen($scriptFile));
		return array(
			'path' => $scriptName,
			'base' => $scriptBase,
			'file' => $scriptFile
		);
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::basePath
	// @desc		Busca o caminho base atual, a partir da raiz do dom�nio
	// @access		public
	// @return		string Caminho base atual
	// @static
	//!-----------------------------------------------------------------
	function basePath() {
		return Environment::get('PHP_SELF');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::queryString
	// @desc		Retorna a string dos par�metros da requisi��o
	// @access		public
	// @return		string Par�metros da requisi��o
	// @static
	//!-----------------------------------------------------------------
	function queryString() {
		return Environment::get('QUERY_STRING');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::url
	// @desc		Retorna a URL da requisi��o (protocolo, servidor e caminho)
	// @return		string URL da requisi��o
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function url() {
		$protocol = HttpRequest::protocol();
		$port = Environment::get('SERVER_PORT');
		if (($protocol == 'http' && $port != '80') || ($protocol == 'https' && $port != '443'))
			$base = "{$protocol}://" . HttpRequest::serverName() . ":{$port}";
		else
			$base = "{$protocol}://" . HttpRequest::serverName();
		return $base . HttpRequest::basePath();
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::uri
	// @desc		Retorna a URI (caminho e par�metros) da requisi��o,
	//				excluindo apenas o ID da sess�o, se estiver presente
	// @param		full bool	"TRUE" Retornar o caminho completo (incluindo protocolo, host e porta)
	// @return		string Caminho completo da requisi��o
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function uri($full=TRUE) {
		if ($full) {
			$protocol = HttpRequest::protocol();
			$port = Environment::get('SERVER_PORT');
			if (($protocol == 'http' && $port != '80') || ($protocol == 'https' && $port != '443'))
				$base = "{$protocol}://" . HttpRequest::serverName() . ":{$port}";
			else
				$base = "{$protocol}://" . HttpRequest::serverName();
		} else {
			$base = '';
		}
		if (!$requestUri = Environment::get('REQUEST_URI')) {
			if ($queryString = Environment::get('QUERY_STRING'))
				return $base . HttpRequest::scriptName() . '?' . $queryString;
			else
				return NULL;
		}
		$requestUri = preg_replace('#[?|&]' . session_name() . '=[^&|?]*#', '', $requestUri);
		return $base . $requestUri;
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::userAgent
	// @desc		Busca informa��es sobre o browser agente
	// @access		public
	// @return		string Informa��es do browser agente
	// @see			HttpRequest::getBrowserInfo
	// @see			HttpRequest::getBrowserName
	// @static
	//!-----------------------------------------------------------------
	function userAgent() {
		return Environment::get('HTTP_USER_AGENT');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::referer
	// @desc		Retorna o endere�o do referente da requisi��o atual
	// @access		public
	// @return		string Endere�o do referente
	// @static
	//!-----------------------------------------------------------------
	function referer() {
		return Environment::get('HTTP_REFERER');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::remoteAddress
	// @desc		Busca o endere�o IP do cliente
	// @access		public
	// @return		string Endere�o IP do cliente
	// @see			HttpRequest::remoteHost
	// @static
	//!-----------------------------------------------------------------
	function remoteAddress() {
		if (Environment::has('X_FORWARDED_FOR'))
			return array_pop(explode(',', Environment::get('X_FORWARDED_FOR')));
		return Environment::get('REMOTE_ADDR');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::remoteHost
	// @desc		Busca o nome do host a partir do endere�o remoto do usu�rio atual
	// @access		public
	// @return		string Nome do host
	// @note		Mantendo o comportamento da fun��o gethostbyaddr, quando o dom�nio
	//				n�o pode ser resolvido � retornado o endere�o IP
	//!-----------------------------------------------------------------
	function remoteHost() {
		return @gethostbyaddr(HttpRequest::remoteAddress());
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::registerGlobals
	// @desc		Move para o escopo global um conjunto de valores
	// 				buscando-os nas vari�veis fornecidas � requisi��o
	// 				atr�ves de GET, POST e COOKIE
	// @access		public
	// @return		bool Retorna TRUE se todas as vari�veis forem publicadas ou
	// 				FALSE se alguma delas n�o for encontrada ou se a
	// 				fun��o for executada sem par�metros
	// @note 		A fun��o n�o possui um n�mero fixo de par�metros.
	// 				Deve ser executada com n (onde n > 0) par�metros
	// 				que representam n vari�veis a serem publicadas
	// 				no escopo global
	// @static
	//!-----------------------------------------------------------------
	function registerGlobals() {
		$quotes = TypeUtils::parseInteger(System::getIni('magic_quotes_gpc'));
		if (!System::isGlobalsOn() || !$quotes) {
			$vars = func_get_args();
			if (sizeof($vars) == 0) return FALSE;
			$check = TRUE;
			foreach($vars as $key) {
				if ($value = HttpRequest::getVar($key)) {
					if (!$quotes)
						$value = (!TypeUtils::isArray($value)) ? addslashes($value) : $value;
					$GLOBALS[$key] = $value;
					unset($value);
				} else {
					// valor n�o encontrado: retorno global da fun��o passa a ser FALSE
					$check = FALSE;
				}
			}
			return $check;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::unsetGlobals
	// @desc		Remove do escopo global todas as vari�veis, exceto
	//				as vari�veis especiais pr�-definidas pelo PHP
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function unsetGlobals() {
		$protectedVars = array('_SERVER', '_POST', '_GET', '_COOKIE', '_ENV', '_REQUEST', 'GLOBALS');
		foreach ($GLOBALS as $variable => $value) {
			if (!in_array($variable, $protectedVars)) {
				unset($GLOBALS[$variable]);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::_fetchVar
	// @desc		Este m�todo � executado a partir dos m�todos get, post,
	//				cookie, session e getVar, para buscar o valor de uma vari�vel
	//				em um dos vetores globais
	// @access		private
	// @param		variableName string		Nome da vari�vel
	// @param		where string			Nome do vetor
	// @return		mixed Valor da vari�vel ou NULL
	// @note		Por ser um m�todo privado, este � o �nico na classe que
	//				n�o pode ser executado estaticamente
	// @note		Este m�todo tamb�m resolve os valores de vari�veis do tipo array,
	//				como por exemplo "myform[myfield]"
	//!-----------------------------------------------------------------
	function _fetchVar($variableName, $where) {
		$arrayContent = array();
		eval("\$arrayContent =& \$_$where;");
		$arrayContent = (array)$arrayContent;
		if (preg_match("/([^\[]+)\[([^\]]+)\](\[([^\]]+)\])?/", $variableName, $matches)) {
			if (isset($arrayContent[$matches[1]]) && is_array($arrayContent[$matches[1]])) {
				$value = @$arrayContent[$matches[1]][$matches[2]];
				if (isset($matches[3]) && is_array($value))
					$value = @$value[$matches[4]];
				return $value;
			}
		} elseif (array_key_exists($variableName, $arrayContent)) {
			return $arrayContent[$variableName];
		}
		return NULL;
	}
}
?>