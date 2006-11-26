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
// $Header: /www/cvsroot/php2go/core/net/HttpCookie.class.php,v 1.15 2006/02/28 21:55:58 mpont Exp $
// $Date: 2006/02/28 21:55:58 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
//------------------------------------------------------------------

// @const COOKIE_DEFAULT_EXPIRY_TIME "86400"
// Tempo padrão de expiração do cookie, em segundos
define('COOKIE_DEFAULT_EXPIRY_TIME', 86400);

//!-----------------------------------------------------------------
// @class		HttpCookie
// @desc		Esta classe permite a construção de cookies HTTP ou a
//				interpretação de cookies recebidos em cabeçalhos de resposta
//				de métodos HTTP
// @package		php2go.net
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.15 $
//!-----------------------------------------------------------------
class HttpCookie extends PHP2Go
{
	var $name;			// @var name string			Nome do cookie	
	var $value;			// @var value string		Valor do cookie
	var $domain;		// @var domain string		Domínio ao qual o cookie está associado
	var $path;			// @var path string			Caminho do cookie
	var $expires;		// @var expires string		Data de expiração do cookie	
	var $secure;		// @var secure bool			Indica se o cookie é seguro
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::HttpCookie
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function HttpCookie() {
		parent::PHP2Go();
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::getName
	// @desc		Busca o nome do cookie
	// @access		public
	// @return		string Nome do cookie
	//!-----------------------------------------------------------------
	function getName() {
		return isset($this->name) ? $this->name : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::getValue
	// @desc		Busca o valor do cookie
	// @access		public
	// @return		string Valor do cookie
	//!-----------------------------------------------------------------
	function getValue() {
		return isset($this->value) ? $this->value : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::getDomain
	// @desc		Busca o domínio associado ao cookie
	// @access		public
	// @return		string Domínio do cookie
	//!-----------------------------------------------------------------
	function getDomain() {
		return isset($this->domain) ? $this->domain : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::isDomain
	// @desc		Verifica se o cookie está associado a um determinado domínio
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isDomain($domain) {
		return (isset($this->domain) && preg_match("'.*" . preg_quote($this->domain) . "$'i", $domain));
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::getPath
	// @desc		Busca o caminho ao qual o cookie está associado
	// @access		public
	// @return		string Caminho associado ao cookie
	//!-----------------------------------------------------------------
	function getPath() {
		return isset($this->path) ? $this->path : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::isPath
	// @desc		Verifica se o cookie está associado a um determinado caminho
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isPath($path) {
		return (isset($this->path) && preg_match("'^" . preg_quote($this->path) . ".*'i", $path));
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::getExpiryDate
	// @desc		Busca a data de expiração do cookie
	// @access		public
	// @return		string Data de expiração, no formato compatível com o RFC 2616
	//!-----------------------------------------------------------------
	function getExpiryDate() {
		return isset($this->expires) ? $this->expires : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::isExpired
	// @desc		Verifica se o cookie expirou
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isExpired() {
		$now = time();
		return (isset($this->expires) && $this->expires <= $now);
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::isSecure
	// @desc		Verifica se o cookie é seguro
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isSecure() {
		return isset($this->secure) ? $this->secure : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::set
	// @desc		Seta valores para as propriedades do cookie
	// @access		public
	// @param		name string		Nome para o cookie
	// @param		value mixed		Valor
	// @param		domain string	"NULL" Domínio associado
	// @param		path string		"/" Caminho associado
	// @param		expires string	"86400" Data de expiração
	// @param		secure bool		"FALSE" Indica se o cookie é seguro
	// @return		void
	//!-----------------------------------------------------------------
	function set($name, $value, $domain = NULL, $path = '/', $expires = COOKIE_DEFAULT_EXPIRY_TIME, $secure=FALSE) {
		$this->setName($name);
		$this->setValue($value);
		$this->setDomain($domain);
		$this->setPath($path);
		$this->setExpiryTime($expires);
		$this->secure = $secure;
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::setName
	// @desc		Define um nome para o cookie
	// @access		public
	// @param		name string		Nome para o cookie
	// @return		void
	//!-----------------------------------------------------------------
	function setName($name) {
		$this->name = trim($name);
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::setValue
	// @desc		Define o valor do cookie
	// @access		public
	// @param		value mixed		Valor do cookie
	// @return		void
	//!-----------------------------------------------------------------
	function setValue($value) {
		$this->value = $value;
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::setDomain
	// @desc		Seta o domínio ao qual o cookie está relacionado
	// @access		public
	// @param		domain string	Domínio do cookie
	// @return		void
	//!-----------------------------------------------------------------
	function setDomain($domain) {
		$this->domain = (TypeUtils::isNull($domain) ? HttpRequest::serverName() : $domain);
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::setPath
	// @desc		Define o caminho do cookie no domínio
	// @access		public
	// @param		path string		Caminho para o cookie
	// @return		void
	//!-----------------------------------------------------------------
	function setPath($path) {
		$this->path = trim($path);
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::setExpiryTime
	// @desc		Define uma data de expiração para o cookie atual
	// @access		public
	// @param		expires int		Offset em segundos para expiração do cookie (valores positivos ou negativos)
	// @return		void
	// @note		Se o parâmetro expires for omitido, será utilizado o
	//				valor atual de data e hora mais o valor da constante
	//				COOKIE_DEFAULT_EXPIRY_TIME
	//!-----------------------------------------------------------------
	function setExpiryTime($expires = NULL) {		
		$now = time();
		if (trim($expires) != '') {			
			if (TypeUtils::isInteger($expires))
				$this->expires = $now + $expires;
			else
				$this->expires = $now + COOKIE_DEFAULT_EXPIRY_TIME;
		} else
			$this->expires = $now + COOKIE_DEFAULT_EXPIRY_TIME;		
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpCookie::parseFromHeader
	// @desc		Interpreta as propriedades de um cookie a partir de
	//				um cabeçalho HTTP do tipo SET-COOKIE
	// @access		public
	// @param		cookieString string	Valor do cabeçalho
	// @param		host string			Host a ser utilizado caso o cookie não possua um domínio definido
	// @return		bool	Retorna FALSE caso os dados do cookie não possam ser interpretados
	//!-----------------------------------------------------------------
	function parseFromHeader($cookieString, $host) {
		$matches = array();
		eregi("^([^=]+)[ ]?=[ ]?([^;]+)(;[ ]?domain=([^;]+))?(;[ ]?expires=([^;]+))?(;[ ]?path=([^;]+))?(;[ ]?secure)?;?", $cookieString, $matches);
		if ($matches[1] && $matches[2]) {
			$name = $matches[1];
			$value = rtrim($matches[2]);
			if ($matches[4])
				$domain = rtrim($matches[4]);
			else
				$domain = $host;
			if ($matches[6])
				$expires = rtrim($matches[6]);
			else
				$expires = '';
			if ($matches[8])
				$path = rtrim($matches[8]);
			else
				$path = '/';
			$secure = TypeUtils::toBoolean($matches[9]);
			$this->set($name, $value, $domain, $path, $expires, $secure);
			return TRUE;
		}
		return FALSE;		
	}
	
}
?>