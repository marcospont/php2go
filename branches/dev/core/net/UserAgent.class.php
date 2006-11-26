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
// $Header: /www/cvsroot/php2go/core/net/UserAgent.class.php,v 1.5 2006/04/05 23:43:25 mpont Exp $
// $Date: 2006/04/05 23:43:25 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		UserAgent
// @desc		Esta classe é responsável por capturar informações sobre o agente
//				do usuário: informações sobre o navegador, sobre o sistema operacional
//				utilizado, sobre funcionalidades do navegador, além de armazenar os tipos
//				MIME, as linguages, as codificações e os charsets aceitos pelo cliente.
//				A classe ainda contém métodos que facilitam a comparação do browser/OS do
//				cliente contra uma lista de valores
// @package		php2go.net
// @extends		PHP2Go
// @uses		Environment
// @uses		HttpRequest
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.5 $
//!-----------------------------------------------------------------
class UserAgent extends PHP2Go
{
	var $userAgent;			// @var userAgent string		Valor original do user agent
	var $identifier;		// @var identifier string		String de identificação do user agent, capturada do início da string original
	var $fullVersion;		// @var fullVersion string		Informação completa de versão
	var $majorVersion;		// @var majorVersion string		Parte principal da versão do cliente
	var $minorVersion;		// @var minorVersion string		Parte secundária da versão do cliente
	var $verlet;			// @var verlet string			Informação adicional da versão do cliente
	var $browser;			// @var browser array			Conjunto de flags boolenos contendo categorias/tipos de navegador
	var $browserName;		// @var browserName string		Abreviação do nome do browser
	var $browserFullName;	// @var browserFullName string	Nome completo do browser
	var $os;				// @var os array				Conjunto de flags booleanos contendo categorias/tipos de sistemas operacionais
	var $osName;			// @var osName string			Abreviação do nome do sistema operacional
	var $osFullName;		// @var osFullName string		Nome completo do sistema operacional
	var $features;			// @var features array			Conjunto de features, com seu respectivo valor baseado no user agent atual
	var $mimeTypes;			// @var mimeTypes array			Conjunto de tipos mime aceitos
	var $language;			// @var language array			Conjunto de linguagens aceitas
	var $encoding;			// @var encoding array			Conjunto de codificações aceitas
	var $charset;			// @var charset array			Conjunto de charsets aceitos
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::UserAgent
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function UserAgent() {
		parent::PHP2Go();
		$this->_initializeProperties();
		$this->_detectProperties();
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::&getInstance
	// @desc		Retorna uma instância única da classe UserAgent, com as informações de detecção
	// @access		public
	// @return		UserAgent object
	// @note		É recomendado que o acesso a esta classe seja feito sempre através do método
	//				getInstance, a fim de evitar que o mecanismo de detecção seja executado mais de
	//				uma vez para um mesmo script
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new UserAgent();
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::getBrowserName
	// @desc		Retorna uma abreviação do nome do browser cliente
	// @access		public
	// @return		string Nome do browser
	//!-----------------------------------------------------------------
	function getBrowserName() {
		return $this->browserName;
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::getBrowserFullName
	// @desc		Retorna o nome completo do browser cliente
	// @access		public
	// @return		string Nome completo do browser
	//!-----------------------------------------------------------------
	function getBrowserFullName() {
		return $this->browserFullName;
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::getOSName
	// @desc		Retorna uma abreviação do nome do sistema operacional do cliente
	// @access		public
	// @return		string Nome do sist. operacional
	//!-----------------------------------------------------------------
	function getOSName() {
		return $this->osName;
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::getFullOSName
	// @desc		Retorna o nome completo do sistema operacional do cliente
	// @access		public
	// @return		string Nome completo do sist. operacional
	//!-----------------------------------------------------------------
	function getOSFullName() {
		return $this->osFullName;
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::getFeature
	// @desc		Retorna o valor de uma determinada funcionalidade exigida do browser cliente
	// @access		public
	// @param		feature string	Nome da funcionalidade
	// @return		mixed Valor da funcionalidade (ou flag indicando que está ativa)
	// @note		Funcionalidades disponíveis:<br>
	//				* javascript (retorna a versão JavaScript no browser cliente)<br>
	//				* dom (verifica se a implementação do modelo DOM está habilitada)<br>
	//				* dhtml (verifica se há suporte a DHTML)<br>
	//				* gecko (retorna a data de compilação da engine Gecko no browser cliente)
	//!-----------------------------------------------------------------
	function getFeature($feature) {
		return (isset($this->features[$feature]) ? $this->features[$feature] : FALSE);
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::matchBrowser
	// @desc		Fornecido um flag da lista de flags booleanos de browser, este método
	//				retorna o valor para este flag, se ele existir
	// @access		public
	// @param		identifier string	Nome do flag
	// @return		bool
	//!-----------------------------------------------------------------
	function matchBrowser($identifier) {
		$identifier = strtolower($identifier);
		return (isset($this->browser[$identifier]) ? $this->browser[$identifier] : FALSE);
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::matchBrowserList
	// @desc		A partir de uma lista de flags, realiza verificação para todos contra
	//				a tabela de flags booleanos de browser, retornando aquele que primeiro 
	//				possuir uma valor verdadeiro
	// @access		public
	// @param		list array	Lista de flags
	// @return		mixed Valor do flag verdadeiro, ou FALSE se nenhum for verdadeiro
	//!-----------------------------------------------------------------
	function matchBrowserList($list) {
		$list = (array)$list;
		foreach ($list as $entry) {
			if (!empty($this->browser[strtolower($entry)]))
				return $entry;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::matchOS
	// @desc		Dado um flag de sistema operacional, retorna o valor para este flag
	// @access		public
	// @param		identifier string 	Flag de sistema operacional
	// @return		bool
	//!-----------------------------------------------------------------
	function matchOS($identifier) {
		$identifier = strtolower($identifier);
		return (isset($this->os[$identifier]) ? $this->os[$identifier] : FALSE);
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::matchOSList
	// @desc		A partir de uma lista de flags, realiza verificação para todos contra
	//				a tabela de flags booleanos de sist. operacional, retornando aquele que
	//				primeiro possuir um valor verdadeiro
	// @access		public
	// @param		list array	Lista de flags
	// @return		mixed Valor do flag verdadeiro, ou FALSE se nenhum for verdadeiro
	//!-----------------------------------------------------------------
	function matchOSList($list) {
		$list = (array)$list;
		foreach ($list as $entry) {
			if (!empty($this->os[strtolower($entry)]))
				return $entry;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	UserAgent::matchMimeType
	// @desc		Verifica se um determinado tipo mime é aceito para a requisição atual
	// @access		public
	// @param		mime string	Tipo mime
	// @return		bool
	//!-----------------------------------------------------------------
	function matchMimeType($mime) {
		$mimestr = implode('|', $this->mimeTypes);
		return preg_match("/{$mime}/i", $mimestr);
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::matchLanguage
	// @desc		Verifica se uma determinada linguagem é aceita
	// @access		public
	// @param		lang string Código da linguagem
	// @return		bool
	//!-----------------------------------------------------------------
	function matchLanguage($lang) {
		$langstr = implode('|', $this->language);
		return preg_match("/{$lang}/i", $langstr);
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::matchEncoding
	// @desc		Verifica se um determinado tipo de codificação é aceito
	// @access		public
	// @param		encoding string Tipo de codificação
	// @return		bool
	//!-----------------------------------------------------------------
	function matchEncoding($encoding) {
		$encodingstr = implode('|', $this->encoding);
		return preg_match("/{$encoding}/", $encodingstr);
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::matchCharset
	// @desc		Verifica se um determinado conjunto de caracteres de codificação é aceito
	// @access		public
	// @param		charset string	Charset
	// @return		bool
	//!-----------------------------------------------------------------
	function matchCharset($charset) {
		$charsetstr = implode('|', $charset);
		return preg_match("/{$charset}/", $charsetstr);
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::matchAcceptList
	// @desc		Valida uma lista de valores contra uma das tabelas "accept" da requisição atual
	// @access		public
	// @param		list array			Lista de valores
	// @param		acceptType string	Nome da tabela (mimeTypes, language, encoding ou charset)
	// @return		mixed Primeiro valor aceito, ou FALSE se nenhum for aceito
	//!-----------------------------------------------------------------
	function matchAcceptList($list, $acceptType) {
		if (in_array($acceptType, array('mimeTypes', 'language', 'encoding', 'charset'))) {
			$str = implode('|', $this->{$acceptType});
			$list = (array)$list;
			foreach ($list as $entry) {
				if (preg_match("/{$entry}/i", $str))
					return $entry;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::toString
	// @desc		Monta uma representação string do user agent
	// @access		public
	// @return		string Representação string, com um resumo das informações coletadas
	//!-----------------------------------------------------------------
	function toString() {
		$features = array();
		foreach ($this->features as $name => $value)
			$features[] = $name . '=' . (TypeUtils::isBoolean($value) ? intval($value) : $value);
		$features = implode(', ', $features);
		return sprintf("Browser: %s\nOS: %s\nFeatures: %s\nAccept-Types: %s\nAccept-Languages: %s\nAccept-Encoding: %s\nAccept-Charset: %s",
				$this->browserFullName, $this->osFullName, $features, 
				implode(', ', $this->mimeTypes), implode(', ', $this->language),
				implode(', ', $this->encoding), implode(', ', $this->charset)
		);
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_initializeProperties
	// @desc		Inicializa as propriedades da classe
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initializeProperties() {
		$this->userAgent = HttpRequest::userAgent();
		$this->identifier = 'unknown';
		$this->fullVersion = 0;
		$this->majorVersion = 0;
		$this->minorVersion = 0;
		$this->verlet = '';
		$this->browser = array();
		$this->browserName = '';
		$this->os = array();
		$this->osName = '';
		$this->features = array(
			'javascript' => FALSE,
			'dhtml' => FALSE,
			'dom' => FALSE,
			'gecko'=> FALSE
		);
		$this->mimeTypes = array();
		$this->language = array();
		$this->encoding = array();
		$this->charset = array();
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_detectProperties
	// @desc		Método principal de detecção das propriedades do cliente
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _detectProperties() {
		$agent = strtolower($this->userAgent);
		if (preg_match(";^([[:alnum:]]+)[ /\(]*[[:alpha:]]*([0-9]*)(\.[0-9a-z\.]*);", $agent, $matches)) {
			if (isset($matches[1]))
				$this->identifier = $matches[1];
			if (isset($matches[2]))
				$this->majorVersion = $matches[2];
			if (isset($matches[3])) {
				if (preg_match("/([\.0-9]+)([\.a-z0-9]+)?/i", $matches[3], $verMatches)) {
					if (isset($verMatches[1]))
						$this->minorVersion = substr($verMatches[1], 1);
					if (isset($verMatches[2]))
						$this->verlet = $verMatches[2];
				}
			}
			$this->fullVersion = "{$this->majorVersion}.{$this->minorVersion}{$this->verlet}";
		}
		$this->_detectBrowserFlags($agent);
        $this->_detectOSFlags($agent);
        $this->_detectFeatures($agent);
		$this->_detectAccept();	
		$this->_detectBrowserName();
		$this->_detectOSName();
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_detectBrowserFlags
	// @desc		Monta a tabela de flags de browser
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _detectBrowserFlags($agent) {
        $this->browser['ns'] = ($this->_match('mozilla', $agent) && $this->_dontMatch('spoofer|compatible|hotjava|opera|webtv', $agent));        
        $this->browser['ns2'] = ($this->browser['ns'] && $this->majorVersion == 2);
        $this->browser['ns3'] = ($this->browser['ns'] && $this->majorVersion == 3);
        $this->browser['ns4'] = ($this->browser['ns'] && $this->majorVersion == 4);
        $this->browser['ns4+'] = ($this->browser['ns'] && $this->majorVersion >= 4);
        $this->browser['nav'] = ($this->browser['ns'] && $this->majorVersion < 5);
        $this->browser['ns6'] = ($this->browser['ns'] && $this->majorVersion == 5);
        $this->browser['ns6+'] = ($this->browser['ns'] && $this->majorVersion >= 5);
        $this->browser['galeon'] = $this->_match('galeon', $agent);
        $this->browser['konqueror'] = $this->_match('konqueror|safari', $agent);
        $this->browser['nautilus'] = $this->_match('nautilus', $agent);
        $this->browser['safari'] = $this->_match('konqueror|safari', $agent);
        $this->browser['text'] = $this->_match('links|lynx|w3m', $agent);        
        $this->browser['gecko'] = ($this->_match('gecko', $agent) && !$this->browser['konqueror']);
        $this->browser['firefox'] = ($this->browser['gecko'] && $this->_match("firefox|firebird", $agent));
        $this->browser['firefox0x'] = ($this->browser['firefox'] && $this->_match("firebird/0.|firefox/0.", $agent));
        $this->browser['firefox1x'] = ($this->browser['firefox'] && $this->_match("firefox/1.", $agent));
        $this->browser['ie'] = ($this->_match('msie', $agent) && $this->_dontMatch('opera', $agent));
        $this->browser['ie3'] = ($this->browser['ie'] && $this->majorVersion == 3);
        $this->browser['ie4'] = ($this->browser['ie'] && $this->majorVersion == 4 && $this->_match('msie 4', $agent));
        $this->browser['ie4+'] = ($this->browser['ie'] && !$this->browser['ie3']);
        $this->browser['ie5'] = ($this->browser['ie4+'] && $this->_match('msie 5.0', $agent));
        $this->browser['ie55'] = ($this->browser['ie4+'] && $this->_match('msie 5.5', $agent));
        $this->browser['ie5+'] = ($this->browser['ie4+'] && !$this->browser['ie3'] && !$this->browser['ie4']);
        $this->browser['ie55+'] = ($this->browser['ie5+'] && !$this->browser['ie5']);           
        $this->browser['ie6'] = $this->_match('msie 6', $agent);
        $this->browser['ie6+'] = ($this->browser['ie5+'] && !$this->browser['ie5'] && !$this->browser['ie5_5']);
        $this->browser['myie'] = ($this->browser['ie'] && $this->_match('myie', $agent));
        $this->browser['opera'] = $this->_match('opera', $agent);
        $this->browser['opera2'] = $this->_match('opera[ /]2', $agent);
        $this->browser['opera3'] = $this->_match('opera[ /]3', $agent);
        $this->browser['opera4'] = $this->_match('opera[ /]4', $agent);
        $this->browser['opera5'] = $this->_match('opera[ /]5', $agent);
        $this->browser['opera6'] = $this->_match('opera[ /]6', $agent);
        $this->browser['opera7'] = $this->_match('opera[ /]7', $agent);
        $this->browser['opera5+'] = ($this->browser['opera'] && $this->_dontMatch('opera[ /][234]', $agent));
        $this->browser['opera6+'] = ($this->browser['opera'] && $this->_dontMatch('opera[ /][2345]', $agent));
        $this->browser['opera7+'] = ($this->browser['opera'] && $this->_dontMatch('opera[ /][23456]', $agent));
        $this->browser['aol'] = $this->_match('aol', $agent);
        $this->browser['aol3'] = ($this->browser['aol'] && $this->browser['ie3']);
        $this->browser['aol4'] = ($this->browser['aol'] && $this->browser['ie4']);
        $this->browser['aol5'] = $this->_match('aol 5', $agent);
        $this->browser['aol6'] = $this->_match('aol 6', $agent);
        $this->browser['aol7'] = $this->_match('aol7|aol 7', $agent);
        $this->browser['aol8'] = $this->_match('aol8|aol 8', $agent);
        $this->browser['webtv'] = $this->_match('webtv', $agent);
        $this->browser['aoltv'] = $this->_match('tvnavigator|navio|navio_aoltv', $agent);
        $this->browser['hotjava'] = $this->_match('hotjava', $agent);
        $this->browser['hotjava3'] = ($this->browser['hotjava'] && $this->majorVersion == 3);
        $this->browser['hotjava3+'] = ($this->browser['hotjava'] && $this->majorVersion >= 3);
        $this->browser['avant'] = $this->_match('avant browser|avantbrowser', $agent);
        $this->browser['k-meleon'] = $this->_match('k-meleon', $agent);
        $this->browser['crazy'] = $this->_match('crazy browser', $agent);
        $this->browser['epiphany'] = $this->_match('epiphany', $agent);
		$this->browser['netgem'] = $this->_match('netgem', $agent);        
        $this->browser['webdav'] = ($agent == 'microsoft data access internet publishing provider dav' || $agent == 'microsoft data access internet publishing provider protocol discovery');
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_detectOSFlags
	// @desc		Monta a tabela de flags de sistema operacional
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _detectOSFlags($agent) {
        $this->os['win'] = $this->_match('win|16bit', $agent);
        $this->os['win16'] = $this->_match('win16|16bit|windows 3.1|windows 16-bit', $agent);
        $this->os['win31'] = $this->_match('win16|windows 3.1|windows 16-bit', $agent);
        $this->os['win95'] = $this->_match('wi(n|ndows)[ -]?95', $agent);
        $this->os['win98'] = $this->_match('wi(n|ndows)[ -]?98', $agent);
        $this->os['wince'] = $this->_match('wi(n|ndows)[ -]?ce', $agent);
        $this->os['winme'] = $this->_match('win 9x 4.90|wi(n|ndows)[ -]?me', $agent);
        $this->os['win2k'] = $this->_match('wi(n|ndows)[ -]?(2000|nt 5.0)', $agent);
        $this->os['winxp'] = $this->_match('wi(n|ndows)[ -]?(xp|nt 5.1)', $agent);
        $this->os['win2003'] = $this->_match('win(n|ndows)[ -]?(2003|nt 5.2)', $agent);
        $this->os['winnt'] = $this->_match('wi(n|ndows)[ -]?nt', $agent);
        $this->os['win32'] = ($this->os['win95'] || $this->os['win98'] || $this->os['winnt'] || $this->_match('win32', $agent) || $this->_match('32bit', $agent));
        $this->os['aix'] = $this->_match('aix', $agent);
        $this->os['aix1'] = $this->_match('aix[ ]?1', $agent);
        $this->os['aix2'] = $this->_match('aix[ ]?2', $agent);
        $this->os['aix3'] = $this->_match('aix[ ]?3', $agent);
        $this->os['aix4'] = $this->_match('aix[ ]?4', $agent);
        $this->os['amiga'] = $this->_match('amiga[ ]?os', $agent);
        $this->os['beos'] = $this->_match('beos', $agent);
        $this->os['freebsd'] = $this->_match('free[ -]?bsd', $agent);
        $this->os['hpux'] = $this->_match('hp[ -]?ux', $agent);
        $this->os['hpux9'] = ($this->os['hpux'] && $this->_match('09.', $agent));
        $this->os['hpux10'] = ($this->os['hpux'] && $this->_match('10.', $agent));
        $this->os['irix'] = $this->_match('irix', $agent);
        $this->os['irix5'] = $this->_match('irix[ ]?5', $agent);
        $this->os['irix6'] = $this->_match('irix[ ]?6', $agent);
        $this->os['linux'] = $this->_match('linux|mdk for [0-9.]+', $agent);
        $this->os['mac'] = $this->_match('mac', $agent);
        $this->os['macosx'] = $this->_match('mac[ ]?os[ ]?x', $agent);
        $this->os['macppc'] = $this->_match('mac(_power|intosh.+p)pc', $agent);
        $this->os['mac68k'] = ($this->os['mac'] && $this->_match('68k|68000', $agent));
        $this->os['netbsd'] = $this->_match('net[ \-]?bsd', $agent);
        $this->os['os2'] = $this->_match('warp[ /]?[0-9.]+|os[ /]?2', $agent);
        $this->os['openbsd'] = $this->_match('open[ -]?bsd', $agent);
        $this->os['openvms'] = $this->_match('vax|open[ -]?vms', $agent);
        $this->os['palmos'] = $this->_match('palm[ -]?(source|os)', $agent);
        $this->os['photon'] = $this->_match('photon', $agent);
        $this->os['sco'] = $this->_match('sco|unix_sv', $agent);
        $this->os['sinix'] = $this->_match('sinix', $agent);
        $this->os['sun'] = $this->_match('sun[ -]?os', $agent);
        $this->os['sun4'] = $this->_match('sun[ -]?os[ ]?4', $agent);
        $this->os['sun5'] = $this->_match('sun[ -]?os[ ]?5', $agent);
        $this->os['suni86'] = ($this->os['sun'] && $this->_match('i86', $agent));
        $this->os['unixware'] = $this->_match('unixware|unix_system_v', $agent);
        $this->os['bsd'] = $this->_match('bsd', $agent);
        $this->os['unix'] = ($this->_match('x11|unix', $agent) || $this->os['aix'] || $this->os['hpux'] || $this->os['irix'] || $this->os['linux'] || $this->os['bsd'] || $this->os['sco'] || $this->os['sinix']);		
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_detectFeatures
	// @desc		Monta a tabela de features do browser cliente
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _detectFeatures($agent) {
        if ($this->browser['ns2'] || $this->browser['ie3'])
        	$this->features['javascript'] = '1.0';
        elseif ($this->browser['ns3'] || ($this->browser['opera'] && !$this->browser['opera5+']))
        	$this->features['javascript'] = '1.1';
        elseif (($this->browser['ns4'] && $this->fullVersion <= 4.05) || $this->browser['ie4'])
        	$this->features['javascript'] = '1.2';
        elseif (($this->browser['ns4'] && $this->fullVersion > 4.05) || $this->browser['ie5+'] || $this->browser['opera5+'] || $this->browser['hotjava3+'])
        	$this->features['javascript'] = '1.3';
        elseif (($this->browser['ie5'] && $this->os['mac']) || $this->browser['konqueror'])
        	$this->features['javascript'] = '1.4';
        elseif ($this->browser['ns6+'] || $this->browser['gecko'] || $this->browser['netgem'])
        	$this->features['javascript'] = '1.5';
        if ($this->browser['gecko']) {
        	if (preg_match(';gecko/([\d]+)\b;i', $agent, $matches))
        		$this->features['gecko'] = $matches[1];
        }
        if ($this->browser['ns6+'] || $this->browser['opera5+'] || $this->browser['konqueror'] || $this->browser['netgem'])
        	$this->features['dom'] = TRUE;
        if ($this->browser['ie4+'] || $this->browser['ns4+'] || $this->browser['opera5+'] || $this->browser['konqueror'] || $this->browser['netgem'])
        	$this->features['dhtml'] = TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_detectAccept
	// @desc		Monta as tabelas "accept" da requisição
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _detectAccept() {		
		$Negotiator =& LocaleNegotiator::getInstance();
		$this->language = $Negotiator->getSupportedLanguages();
		if (empty($this->language))
			$this->language = array('en');
		$this->charset = $Negotiator->getSupportedCharsets();		
		if (empty($this->charset))
			$this->charset = array('*');		
        $this->mimeTypes = TypeUtils::toArray(preg_split(';[\s,]+;', Environment::get('HTTP_ACCEPT'), -1, PREG_SPLIT_NO_EMPTY));
		$this->encoding = TypeUtils::toArray(preg_split(';[\s,]+;', Environment::get('HTTP_ACCEPT_ENCODING'), -1, PREG_SPLIT_NO_EMPTY));
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_detectBrowserName
	// @desc		Detecta o nome completo do browser
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _detectBrowserName() {
		$vstr = array(
			'aol' => 'AOL Browser',
			'myie' => 'MyIE',
			'konqueror' => 'Konqueror/Safari',
			'galeon' => 'Galeon',
			'nautilus' => 'Nautilus',
			'hotjava' => 'HotJava',
			'avant' => 'Avant Browser',
			'k-meleon' => 'K-meleon',
			'crazy' => 'Crazy Browser',
			'epiphany' => 'Epiphany',
			'netgem' => 'Netgem/iPlayer',
			'text' => 'Text based Browser',
			'webdav' => 'WebDAV',
			'opera7+' => 'Opera 7.x',
			'opera6+' => 'Opera 6.x',
			'opera5+' => 'Opera 5.x',
			'opera4' => 'Opera 4.x',
			'opera' => 'Opera',			
			'ie6+' => 'Microsoft Internet Explorer 6.x',
			'ie5+' => 'Microsoft Internet Explorer 5.x',
			'ie4+' => 'Microsoft Internet Explorer 4.x',
			'ie' => 'Microsoft Internet Explorer',
			'nav' => 'Netscape Navigator',
			'firefox' => 'Mozilla Firefox',
			'ns6+' => 'Mozilla/Netscape 6.x',
			'ns4' => 'Netscape 4.x',
			'ns' => 'Netscape'
		);
		$result = $this->matchBrowserList(array_keys($vstr));
		if ($result) {
			$this->browserName = $result;
			$this->browserFullName = $vstr[$result];
		} else {
			$this->browserName = strtolower($this->identifier);
			$this->browserFullName = $this->identifier;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_detectOSName
	// @desc		Detecta o nome completo do sistema operacional do cliente
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------	
	function _detectOSName() {
		$vstr = array(
			'win2003' => 'Microsoft Windows 2003',
			'winxp' => 'Microsoft Windows XP',
			'win2k' => 'Microsoft Windows 2000',
			'winme' => 'Microsoft Windows Millenium',
			'wince' => 'Microsoft Windows CE',
			'win98' => 'Microsoft Windows 98',
			'win95' => 'Microsoft Windows 95',
			'win16' => 'Microsoft Windows 3.x/16 bits',
			'winnt' => 'Microsoft Windows NT',
			'aix' => 'AIX',
			'amiga' => 'AmigaOS',
			'freebsd' => 'FreeBSD',
			'hpux' => 'HP UX',
			'irix' => 'Irix',
			'linux' => 'Linux',
			'macppc' => 'MacOS PPC',			
			'macosx' => 'MacOS X',
			'mac' => 'MacOS',
			'netbsd' => 'NetBSD',
			'openbsd' => 'OpenBSD',
			'sun' => 'SunOS',
			'unix' => 'Unix'
		);
		$result = $this->matchOSList(array_keys($vstr));
		if ($result) {
			$this->osName = $result;
			$this->osFullName = $vstr[$result];
		} else {
			$this->osName = 'unknown';
			$this->osFullName = 'Unkown/Other';
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_match
	// @desc		Método utilitário para teste de padrões na string original do user agent
	// @access		private
	// @param		pattern string	Padrão
	// @param		value string	Valor de teste
	// @return		bool
	//!-----------------------------------------------------------------
	function _match($pattern, $value) {
		return ((bool)preg_match(":{$pattern}:", $value));
	}
	
	//!-----------------------------------------------------------------
	// @function	UserAgent::_dontMatch
	// @desc		Método utilitário para teste de padrões na string original do user agent
	// @access		private
	// @param		pattern string	Padrão
	// @param		value string	Valor de teste
	// @return		bool
	//!-----------------------------------------------------------------
	function _dontMatch($pattern, $value) {
		return (!(bool)preg_match(":{$pattern}:", $value));
	}
}
?>