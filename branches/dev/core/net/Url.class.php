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
// $Header: /www/cvsroot/php2go/core/net/Url.class.php,v 1.17 2006/02/28 21:55:59 mpont Exp $
// $Date: 2006/02/28 21:55:59 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		Url
// @desc		Classe que cont�m m�todos utilit�rios para a constru��o
//				e a manipula��o de URLs
// @package		php2go.net
// @extends		PHP2Go
// @uses		HtmlUtils
// @uses		HttpRequest
// @uses		StringUtils
// @author		Marcos Pont
// @version		$Revision: 1.17 $
// @note		O formato completo de uma URL interpretada por esta classe �:
//				[protocolo]://[usu�rio]:[senha]@[dom�nio]:[porta]/[caminho]/[arquivo]?[par�metros]#[fragmento]
//!-----------------------------------------------------------------
class Url extends PHP2Go
{
	var $protocol;			// @var protocol string			Protocolo da URL
	var $auth;				// @var auth string				Dados de autentica��o
	var $user;				// @var user string				Nome do usu�rio na autentica��o
	var $pass;				// @var pass string				Senha de autentica��o
	var $host;				// @var host string				Nome de dom�nio ou IP do host da URL
	var $port;				// @var port int				N�mero da porta presente na URL
	var $path;				// @var path string				Caminho completo, a partir da raiz do dom�nio
	var $file;				// @var file string				Nome do arquivo da URL
	var $parameters;		// @var parameters string		String de par�metros da URL
	var $fragment;			// @var fragment string			Fragmento da URL		

	//!-----------------------------------------------------------------
	// @function	Url::Url
	// @desc		Construtor da classe
	// @param		url string	"" Se for fornecida, a URL ser� parseada pela classe
	// @access		public	
	//!-----------------------------------------------------------------
	function Url($url='') {
		parent::PHP2Go();
		if ($url != '') {
			$this->set($url);			
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::set
	// @desc		Seta a URL do objeto
	// @param		url string	URL a ser processada
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function set($url) {
		$this->_parse($url);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::setFromCurrent
	// @desc		Define as propriedades do objeto a partir da requisi��o atual
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFromCurrent() {
		$this->set(HttpRequest::uri());
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getProtocol
	// @desc		Retorna o protocolo da URL
	// @note		Todos os m�todos de acesso da classe retornam NULL
	//				se os valores n�o est�o definidos
	// @return		string Protocolo da URL
	// @access		public		
	//!-----------------------------------------------------------------	
	function getProtocol() {
		return (isset($this->protocol) && !empty($this->protocol) ? $this->protocol : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getScheme
	// @desc		Retorna o esquema da URL atual, composto pelo nome do
	//				protocolo e pelos caracteres '://'	
	// @return		string Esquema da URL ou NULL se n�o existente
	// @access		public	
	//!-----------------------------------------------------------------
	function getScheme() {
		$protocol = $this->getProtocol();
		if (!TypeUtils::isNull($protocol))
			return strtolower($protocol) . '://';
		else
			return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getAuth
	// @desc		Consulta a string de autentica��o da URL
	// @return		string String de autentica��o
	// @access		public	
	//!-----------------------------------------------------------------	
	function getAuth() {
		return (isset($this->auth) && !empty($this->auth) ? $this->auth : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getUser
	// @desc		Retorna o nome de usu�rio para autentica��o na URL
	// @return		string Nome de usu�rio para autentica��o
	// @access		public	
	//!-----------------------------------------------------------------	
	function getUser() {
		return (isset($this->user) && !empty($this->user) ? $this->user : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getPass
	// @desc		Consulta a senha de autentica��o da URL
	// @return		string Senha de autentica��o
	// @access		public	
	//!-----------------------------------------------------------------
	function getPass() {
		return (isset($this->pass) && !empty($this->pass) ? $this->pass : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getHost
	// @desc		Consulta o nome de dom�nio do host da URL
	// @return		string Dom�nio do host
	// @access		public	
	//!-----------------------------------------------------------------
	function getHost() {
		if (!isset($this->host) || empty($this->host)) 
			return NULL;
		if (ereg("[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}", $this->host)) {
			return gethostbyaddr($this->host);
		} else {
			return strtolower($this->host);
		}
	}
	
	//!-----------------------------------------------------------------	
	// @function	Url::getPort
	// @desc		Busca o n�mero da porta da URL
	// @return		int N�mero da porta
	// @access		public	
	//!-----------------------------------------------------------------	
	function getPort() {
		return (isset($this->port) && !empty($this->port) ? $this->port : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getPath
	// @desc		Busca o caminho a partir da raiz do dom�nio da URL
	// @return		string Caminho da URL ou NULL se n�o existente
	// @access		public	
	//!-----------------------------------------------------------------
	function getPath() {
		return (isset($this->path) && !empty($this->path) ? $this->path : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getFile
	// @desc		Busca o nome do arquivo da URL
	// @return		string Nome do arquivo ou NULL se n�o existente
	// @access		public	
	//!-----------------------------------------------------------------
	function getFile() {
		return (isset($this->file) && !empty($this->file) ? $this->file : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getQueryString
	// @desc		Consulta a string de par�metros fornecida junto � URL
	// @param		prefix bool		"FALSE" Adicionar o prefixo '?' para uma string de par�metros n�o nula
	// @return		string String de par�metros, ou NULL se n�o existem par�metros
	// @access		public	
	//!-----------------------------------------------------------------
	function getQueryString($prefix=FALSE) {
		return (isset($this->parameters) && !empty($this->parameters) ? ($prefix ? '?' . $this->parameters : $this->parameters) : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getQueryStringArray
	// @desc		Retorna um vetor contendo os par�metros da URL
	// @return		array Vetor de par�metros, ou NULL se a URL n�o possui par�metros
	// @access		public	
	//!-----------------------------------------------------------------
	function getQueryStringArray() {
		$queryString = $this->getQueryString();
		if (!TypeUtils::isNull($queryString)) {
			parse_str($queryString, $result);
			return $result;
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::addParameter
	// @desc		Adiciona um par�metro na query string da URL
	// @param		name string			Nome do par�metro
	// @param		value mixed			Valor do par�metro
	// @note		Se o par�metro j� existe, � sobrescrito
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function addParameter($name, $value) {
		$queryString = $this->getQueryString();
		if (!TypeUtils::isNull($queryString)) {
			$result = '';
			parse_str($queryString, $params);			
			$params[$name] = $value;
			foreach ($params as $name => $value)
				$result .= ($result == '' ? "$name=$value" : "&$name=$value");
			$this->parameters = $result;
		} else {
			$this->parameters = "$name=$value";
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::removeParameter
	// @desc		Remove um par�metro da query string da URL
	// @param		name string			Nome do par�metro
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function removeParameter($name) {
		$query = $this->getQueryStringArray();
		if (!TypeUtils::isNull($query)) {
			unset($query[$name]);
			$tmp = array();
			foreach ($query as $k => $v)
				$tmp[] = "$k=$v";
			$this->parameters = implode("&", $tmp);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getFragment
	// @desc		Busca o fragmento da URL, se existente
	// @return		string Nome do fragmento ou NULL se n�o encontrado
	// @access		public	
	//!-----------------------------------------------------------------
	function getFragment() {
		return (isset($this->fragment) && !empty($this->fragment) ? $this->fragment : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	Url::getUrl
	// @desc		Reconstr�i a URL a partir dos valores capturados
	// @return		string URL reconstru�da
	// @access		public	
	//!-----------------------------------------------------------------
	function getUrl() {
		return sprintf("%s%s%s%s%s%s%s",
			(isset($this->protocol) && !empty($this->protocol) ? "{$this->protocol}://" : ''),
			(isset($this->auth) && !empty($this->auth) ? "{$this->user}:{$this->pass}@" : ''),
			strtolower($this->host),
			(isset($this->port) && !empty($this->port) ? ":{$this->port}" : ''),
			$this->path,
			(isset($this->parameters) && !empty($this->parameters) ? "?{$this->parameters}" : ''),
			(isset($this->fragment) && !empty($this->fragment) ? "#{$this->fragment}" : '')
		);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::getAnchor
	// @desc		Executa a fun��o de montagem de um �ncora apontando para a URL atual
	// @param		caption string			Caption do �ncora
	// @param		statusBarText string	"" Texto a ser exibido na barra de status
	// @param		cssClass string			"" Nome de estilo CSS para a �ncora
	// @return		string C�digo da �ncora
	// @access		public	
	//!-----------------------------------------------------------------
	function getAnchor($caption, $statusBarText='', $cssClass='') {
		return HtmlUtils::anchor($this->getUrl(), $caption, $statusBarText, $cssClass);
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::reset
	// @desc		Reseta todos os par�metros da URL
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function reset() {
		unset($this->protocol);
		unset($this->auth);
		unset($this->user);
		unset($this->pass);
		unset($this->host);
		unset($this->port);
		unset($this->path);
		unset($this->file);
		unset($this->parameters);
		unset($this->fragment);
	}

	//!-----------------------------------------------------------------
	// @function	Url::encode
	// @desc		Codifica os par�metros de uma URL para prote��o
	//				de seus nomes e valores
	// @param		url string		"NULL" URL a ser codificada
	// @param		varName string	"p2gvar"	Nome da vari�vel criada
	// @note		A URL padr�o a ser utilizada nesta classe � a pr�pria URL armazenada	
	// @return		string Uma string contendo a vari�vel criada e o conte�do das
	//				var�aveis originadas codificado
	// @access		public
	// @see			Url::decode
	//!-----------------------------------------------------------------
	function encode($url=NULL, $varName='p2gvar') {
		// utiliza como padr�o a URL da classe
		if (TypeUtils::isNull($url))
			$url = $this->getUrl();
		// busca a string de par�metros
		if (ereg("([^?#]+\??)?([^#]+)?(.*)", $url, $matches)) {			
			if (!TypeUtils::isFalse($matches[2])) {
				// codifica os par�metros
				$paramString = base64_encode(urlencode($matches[2]));
				$returnUrl = TypeUtils::parseString($matches[1]) . $varName . '=' . $paramString . TypeUtils::parseString($matches[3]);
			} else {
				$returnUrl = $url;
			}
		}
		return $returnUrl;
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::decode
	// @desc		Decodifica uma string de URL codificada pela fun��o
	//				encodeUrl. Reconstr�i a URL original
	// @param		url string			"NULL" URL a ser decodificada
	// @param		returnAsArray bool	Se for verdadeiro, retorna as vari�veis originais em um array
	// @note		A URL utilizada como padr�o neste m�todo � o valor atual de URL da classe
	// @note		Para utilizar o valor da requisi��o atual, utilize $_SERVER['QUERY_STRING'] 
	//				ou $_SERVER['REQUEST_URI'] como par�metro
	// @return		mixed Conte�do original da URL na forma de uma string ou de um array
	// @access		public
	// @see			Url::encode	
	//!-----------------------------------------------------------------
	function decode($url=NULL, $returnAsArray=FALSE) {
		// utiliza como padr�o a URL da classe
		if (TypeUtils::isNull($url))
			$url = $this->getUrl();
		// busca os par�metros codificados		
		ereg("([^?#]+\??)?([^#]+)?(.*)", $url, $matches);
		if (!TypeUtils::isFalse($matches[2])) {
			parse_str($matches[2], $vars);
			if (list(, $value) = each($vars)) {
				// decodifica o conjunto de par�metros
				$paramString = urldecode(base64_decode($value));
				if ($returnAsArray) {
					parse_str($paramString, $varsArray);
					return $varsArray;
				} else {
					return TypeUtils::parseString($matches[1]) . $paramString . TypeUtils::parseString($matches[3]);
				}
			}			
		}	
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Url::_parse
	// @desc		Captura informa��es sobre uma URL, populando as propriedades
	//				da classe com os valores encontrados
	// @param		url string		URL a ser processada
	// @access		private	
	// @return		void
	//!-----------------------------------------------------------------
	function _parse($url) {
        if (preg_match('!^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?!', $url, $matches)) {
			if (isset($matches[1]))
				$this->protocol = $matches[2];
			if (isset($matches[3]) && isset($matches[4])) {
				$atPos = strpos($matches[4], '@');
				if (!TypeUtils::isFalse($atPos)) {
					$this->auth = StringUtils::left($matches[4], $atPos);
					$dotPos = strpos($this->auth, ':');
					if (!TypeUtils::isFalse($dotPos)) {
						$auth = explode(':', $this->auth);
						$this->user = $auth[0];
						$this->pass = $auth[1];
					} else {
						$this->user = $this->auth;
					}
					$matches[4] = substr($matches[4], $atPos+1);
				}
				$portPos = strrpos($matches[4], ':');
				if (!TypeUtils::isFalse($portPos)) {
					$this->port = TypeUtils::parseIntegerPositive(substr($matches[4], $portPos+1));
					if (!$this->port) {
						$this->port = NULL;
					}
				}
				$this->host = $portPos ? StringUtils::left($matches[4], $portPos) : $matches[4];
			}
			if (isset($matches[5])) {
				$this->path = $matches[5];
				$slashPos = strrpos(substr($this->path, 1), '/');
				if (!TypeUtils::isFalse($slashPos)) {
					$this->file = substr($this->path, $slashPos + 2);
				}
			}
			$this->path = $matches[5] ? $matches[5] : '';
            if (isset($matches[6]) && $matches[6] != '') 
				$this->parameters = $matches[7];
            if (isset($matches[8]) && $matches[8] != '') 
				$this->fragment = $matches[9];
        }
	}
}
?>