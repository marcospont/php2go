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
// @desc		Esta classe contém funções utilitárias que permitem
//				interagir com a requisição HTTP. Detecção de informações
//				sobre o browser do cliente, variáveis de ambiente e de
//				sistema e parâmetros da requisição são algumas das funcionalidades
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
	// @desc		Retorna uma instância única da classe
	// @access		public
	// @return		HttpRequest object	Instância da classe
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
	// @desc		Retorna todo o conteúdo do superglobal $_REQUEST
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function &request() {
		$request =& $_REQUEST;
		return $request;
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::get
	// @desc		Consulta o valor de um parâmetro GET
	// @access		public
	// @param		paramName string		"" Nome do parâmetro solicitado
	// @return		mixed Valor do parâmetro ou NULL
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
	// @desc		Consulta o valor de um parâmetro POST
	// @access		public
	// @param		paramName string		"" Nome do parâmetro solicitado
	// @return		mixed Valor do parâmetro ou NULL
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
	// @param		paramName string		"" Nome do parâmetro solicitado
	// @return		mixed Valor do parâmetro ou NULL
	// @note		Se o nome da chave for omitido, o método retorna o vetor $_COOKIE completo
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
	// @desc		Consulta o valor de uma variável de sessão
	// @access		public
	// @param		paramName string		"" Nome do parâmetro solicitado
	// @return		mixed Valor do parâmetro ou NULL
	// @note		Se o nome da chave for omitido, o método retorna o vetor $_SESSION completo
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
	// @desc		Busca e retorna o valor de uma variável
	// @access		public
	// @param		variableName string		Nome da variável
	// @param		where string				"all" Indica onde a variável deve ser buscada
	// @param		searchOrder string		"" Ordem de busca nos dados da requisição
	// @return		mixed Valor da variável ou NULL se não encontrada
	// @note		O comportamento padrão do método é buscar pela variável em todos
	//				os locais possíveis na requisição, de acordo com a ordem estabelecida
	//				pelo parâmetro de inicialização 'variables_order'. Esta ordem pode ser
	//				sobreposta através do parâmetro $searchOrder
	// @note		Também pode ser realizada uma busca explícita em um dos repositórios:
	//				'reg', 'object', 'get', 'post', 'cookie', 'session', 'env', 'server' e 'request'
	// @note		O repositório req representa uma instância da classe Registry, que mantém o conteúdo
	//				do vetor $GLOBALS em suas propriedades
	// @note		O repositório obj representa os objetos de sessão (php2go.session.SessionObject)
	//				criados. Modo de utilização: session_var:property_name
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
	// @desc		Retorna um vetor contendo todos os headers HTTP da requisição atual
	// @access		public
	// @return		array Vetor de headers da requisição HTTP
	// @static
	//!-----------------------------------------------------------------
	function getHeaders() {
		return apache_request_headers();
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::method
	// @desc		Busca o método utilizado na requisição
	// @access		public
	// @return		string
	// @static
	//!-----------------------------------------------------------------
	function method() {
		return Environment::get('REQUEST_METHOD');
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpRequest::isGet
	// @desc		Verifica se o request method é GET
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
	// @desc		Verifica se o request method é POST
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
	// @desc		Busca o protocolo utilizado na requisição
	// @access		public
	// @return		string Nome do protocolo da requisição: http ou https
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
	// @desc		Verifica se o protocolo HTTP seguro está sendo utilizado nesta requisição
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isSecure() {
		return (strtolower(Environment::get('HTTPS')) == 'on' || Environment::has('SSL_PROTOCOL_VERSION'));
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::serverHostName
	// @desc		Retorna o nome do host da requisição atual, juntamente
	//				com a porta utilizada na requisição
	// @access		public
	// @return		string Nome do host e número da porta (se ela for diferente da porta padrão do protocolo)
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
	// @desc		Constrói um vetor com nome completo, caminho base e
	//				nome de arquivo para o script atual
	// @access		public
	// @return		array Vetor associativo contendo três posições: path - caminho
	//				completo; base - diretório base do script; file - arquivo
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
	// @desc		Busca o caminho base atual, a partir da raiz do domínio
	// @access		public
	// @return		string Caminho base atual
	// @static
	//!-----------------------------------------------------------------
	function basePath() {
		return Environment::get('PHP_SELF');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::queryString
	// @desc		Retorna a string dos parâmetros da requisição
	// @access		public
	// @return		string Parâmetros da requisição
	// @static
	//!-----------------------------------------------------------------
	function queryString() {
		return Environment::get('QUERY_STRING');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::url
	// @desc		Retorna a URL da requisição (protocolo, servidor e caminho)
	// @return		string URL da requisição
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
	// @desc		Retorna a URI (caminho e parâmetros) da requisição,
	//				excluindo apenas o ID da sessão, se estiver presente
	// @param		full bool	"TRUE" Retornar o caminho completo (incluindo protocolo, host e porta)
	// @return		string Caminho completo da requisição
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
	// @desc		Busca informações sobre o browser agente
	// @access		public
	// @return		string Informações do browser agente
	// @see			HttpRequest::getBrowserInfo
	// @see			HttpRequest::getBrowserName
	// @static
	//!-----------------------------------------------------------------
	function userAgent() {
		return Environment::get('HTTP_USER_AGENT');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::referer
	// @desc		Retorna o endereço do referente da requisição atual
	// @access		public
	// @return		string Endereço do referente
	// @static
	//!-----------------------------------------------------------------
	function referer() {
		return Environment::get('HTTP_REFERER');
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::remoteAddress
	// @desc		Busca o endereço IP do cliente
	// @access		public
	// @return		string Endereço IP do cliente
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
	// @desc		Busca o nome do host a partir do endereço remoto do usuário atual
	// @access		public
	// @return		string Nome do host
	// @note		Mantendo o comportamento da função gethostbyaddr, quando o domínio
	//				não pode ser resolvido é retornado o endereço IP
	//!-----------------------------------------------------------------
	function remoteHost() {
		return @gethostbyaddr(HttpRequest::remoteAddress());
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::registerGlobals
	// @desc		Move para o escopo global um conjunto de valores
	// 				buscando-os nas variáveis fornecidas à requisição
	// 				atráves de GET, POST e COOKIE
	// @access		public
	// @return		bool Retorna TRUE se todas as variáveis forem publicadas ou
	// 				FALSE se alguma delas não for encontrada ou se a
	// 				função for executada sem parâmetros
	// @note 		A função não possui um número fixo de parâmetros.
	// 				Deve ser executada com n (onde n > 0) parâmetros
	// 				que representam n variáveis a serem publicadas
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
					// valor não encontrado: retorno global da função passa a ser FALSE
					$check = FALSE;
				}
			}
			return $check;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	HttpRequest::unsetGlobals
	// @desc		Remove do escopo global todas as variáveis, exceto
	//				as variáveis especiais pré-definidas pelo PHP
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
	// @desc		Este método é executado a partir dos métodos get, post,
	//				cookie, session e getVar, para buscar o valor de uma variável
	//				em um dos vetores globais
	// @access		private
	// @param		variableName string		Nome da variável
	// @param		where string			Nome do vetor
	// @return		mixed Valor da variável ou NULL
	// @note		Por ser um método privado, este é o único na classe que
	//				não pode ser executado estaticamente
	// @note		Este método também resolve os valores de variáveis do tipo array,
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