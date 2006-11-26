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
// $Header: /www/cvsroot/php2go/core/Init.class.php,v 1.38 2006/11/25 12:12:17 mpont Exp $
// $Date: 2006/11/25 12:12:17 $

// @const PHP2GO_CONFIG_FILE_NAME	"userConfig.php"
// Nome do arquivo padrão de configuração do framework
define('PHP2GO_CONFIG_FILE_NAME', 'userConfig.php');
// @const PHP2GO_DEFAULT_LANGUAGE	"en-us"
// Representa a linguagem padrão a ser utilizada quando não for definido um valor fixo nem habilitada auto detecção
define('PHP2GO_DEFAULT_LANGUAGE', 'en-us');
// @const PHP2GO_DEFAULT_CHARSET	"iso-8859-1"
// Charset padrão a ser utilizado quando requisitado e não fornecido pelo usuário
define('PHP2GO_DEFAULT_CHARSET', 'iso-8859-1');

//!-----------------------------------------------------------------
// @class		Init
// @desc		A classe Init é instanciada dentro do arquivo de definições
//				do framework, a fim de carregar o arquivo de configurações do
//				usuário, o arquivo de entradas de linguagem e realizar validações
//				no ambiente atual
// @author		Marcos Pont
// @version		$Revision: 1.38 $
//!-----------------------------------------------------------------
class Init
{
	var $_Conf;			// @var _Conf Conf object					Contém as configurações do usuário para o framework
	var $_Lang;			// @var _Lang LanguageBase object			Contém a tabela de linguagem utilizada no framework
	var $_Negotiator;	// @var _Negotiator LocaleNegotiator object	Utilizada para detectar parâmetros de internacionalização a partir dos headers enviados pelo browser cliente
	var $locale;		// @var locale string						Locale ativo
	var $charset;		// @var charset string						Charset ativo

	var $localeTable = array(
		'pt-br' => array(array('pt_BR', 'portuguese', 'pt_BR.iso-8859-1', 'pt_BR.utf-8'), 'brazilian-portuguese', 'pt-br'),
		'en-us' => array(array('en_US', 'en'), 'us-english', 'en'),
		'es' => array(array('es_ES', 'es'), 'spanish', 'es'),
		'cs' => array(array('cs_CZ', 'cz'), 'czech', 'cz'),
		'it' => array(array('it_IT', 'it'), 'italian', 'it'),
		'de-de' => array(array('de_DE', 'de', 'ge'), 'de-german', 'de'),
		'fr-fr' => array(array('fr_FR', 'fr'), 'french', 'fr')
	);

	//!-----------------------------------------------------------------
	// @function	Init::Init
	// @desc		Construtor da classe de inicialização do framework
	// @access		public
	//!-----------------------------------------------------------------
	function Init() {
		$this->_Conf =& Conf::getInstance();
		$this->_Lang =& LanguageBase::getInstance();
		$this->_Negotiator =& LocaleNegotiator::getInstance();
		$this->_initConfig();
		$this->_initSession();
		$this->_initLocale();
		$this->_checkPhpVersion();
		$this->_checkAbsoluteUri();
		$this->_checkDateFormat();
	}

	//!-----------------------------------------------------------------
	// @function	Init::&getInstance
	// @desc		Retorna uma instância única (singleton) da classe Init
	// @return		Init object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new Init();
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	Init::setLocale
	// @desc		Altera a tabela de linguagem em relação ao valor original da tabela de configuração
	// @param		language string		Código da linguagem/idioma
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function setLocale($language) {
		return $this->_applyLocale($language);
	}

	//!-----------------------------------------------------------------
	// @function	Init::resetLocale
	// @desc		Verifica se existe um código de linguagem definido pelo usuário
	//				armazenado em um cookie ou na sessão. Se existir, remove as referências
	//				existentes e reinicializa as tabelas de linguagem baseado na configuração
	// @note		Este método pode ser útil para remover uma escolha de linguagem
	//				feita pelo usuário quando ele encerra sua sessão
	// @return		void
	//!-----------------------------------------------------------------
	function resetLocale() {
		if (isset($_SESSION['PHP2GO_LANGUAGE']) || isset($_COOKIE['PHP2GO_LANGUAGE'])) {
			unset($_SESSION['PHP2GO_LANGUAGE']);
			setcookie('PHP2GO_LANGUAGE', @$_COOKIE['PHP2GO_LANGUAGE'], time()-86400, '/');
			unset($_COOKIE['PHP2GO_LANGUAGE']);
			$this->_initLocale();
		}
	}

	//!-----------------------------------------------------------------
	// @function	Init::_initConfig
	// @desc		Este método inicializa o conjunto de configurações setadas
	//				pelo usuário, criando uma instância da classe Conf
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initConfig() {
		// verifica se o vetor de configuração já foi definido pelo usuário
		global $P2G_USER_CFG;
		if (isset($P2G_USER_CFG) && is_array($P2G_USER_CFG)) {
			$this->_Conf->setConfig($P2G_USER_CFG);
			$P2G_USER_CFG = NULL;
		}
		// verifica se existe um arquivo de configuração na raiz do domínio
		elseif (@file_exists($_SERVER['DOCUMENT_ROOT'] . '/userConfig.php')) {
			$this->_Conf->loadConfig($_SERVER['DOCUMENT_ROOT'] . '/userConfig.php');
		}
		// inicializa a partir das configurações padrões do framework
		elseif (@file_exists(PHP2GO_ROOT . 'userConfig.php')) {
			$this->_Conf->loadConfig(PHP2GO_ROOT . 'userConfig.php');
		}
		else {
			setupError('The default configuration file <b>userConfig.php</b> was not found at <b>' . PHP2GO_ROOT . '</b>');
			return FALSE;
		}
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Init::_initSession
	// @desc		Inicializa a sessão de usuário, aplicando as configurações
	//				de nome, tempo de expiração e caminho de serialização
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initSession() {
		// defaults
		$defaults = array(
			'NAME' => 'PHP2GO_SESSID',
			'LIFETIME' => NULL,
			'SAVE_PATH' => NULL,
			'AUTO_START' => TRUE,
			'COOKIES_ONLY' => TRUE
		);
		$conf = $this->_Conf->getConfig('SESSION');
		if (!is_array($conf)) {
			// valores no formato antigo
			$conf = array(
				'NAME' => $this->_Conf->getConfig('SESSION_NAME', 'PHP2GO_SESSID'),
				'LIFETIME' => $this->_Conf->getConfig('SESSION_LIFETIME', NULL),
				'SAVE_PATH' => $this->_Conf->getConfig('SESSION_PATH'),
				'AUTO_START' => $this->_Conf->getConfig('SESSION_AUTO_START', TRUE),
				'COOKIES_ONLY' => TRUE
			);				
		} else {
			$conf = array_merge($defaults, $conf);
		}
		// nome do cookie de sessão
		if ($conf['NAME'])
			ini_set('session.name', $conf['NAME']);
		ini_set('session.cache_limiter', 'must_revalidate');
		ini_set('session.use_cookies', TRUE);
		ini_set('session.cookie_lifetime', 0);		
		// sessão previsamente inicializada
		if (ini_get('session.auto_start') == TRUE) {
			$_SESSION = array();
			if (isset($_COOKIE[session_name()]))
				setcookie(session_name(), '', time()-86400, '/');
			session_unset();
			@session_destroy();
		}
		// utilizar ou não apenas cookies
		if ($conf['COOKIES_ONLY']) {
			ini_set('session.use_only_cookies', TRUE);
			ini_set('session.use_trans_sid', FALSE);
			ini_set('url_rewriter.tags', '');
		} else {
			ini_set('session.use_only_cookies', FALSE);
			ini_set('session.use_trans_sid', TRUE);
			ini_set('url_rewriter.tags', 'a=href,frame=src,input=src,form=fakeentry,fieldset=');
		}
		// configurações de garbage collection
		ini_set('session.gc_probability', 1);
		if (is_int($conf['LIFETIME']))
			ini_set('session.gc_maxlifetime', $lifetime);
		// save path
		if (!empty($conf['SAVE_PATH']) && is_dir($conf['SAVE_PATH']))
			session_save_path($path);
		// auto start
		if ($conf['AUTO_START'] !== FALSE)
			@session_start();
	}

	//!-----------------------------------------------------------------
	// @function	Init::_initLocale
	// @desc		Inicializa as configurações de linguagem e localização a partir
	//				das configurações definidas pelo usuário
	// @note		Implementa alteração dinâmica da linguagem a partir de um parâmetro
	//				da requisição e auto detecção a partir do cabeçalho Accept-Language
	// @note		Consulte o arquivo INSTALL.txt que acompanha o framework para saber mais
	//				sobre as possibilidades da entrada de configuração 'LANGUAGE'
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initLocale() {
		// linguagem
		$conf = $this->_Conf->getConfig('LANGUAGE');
		$jsLangCode = (isset($_REQUEST['locale']) && preg_match("/resources\/javascript\/lang.php$/", @$_SERVER['PHP_SELF']) ? $_REQUEST['jslang'] : NULL);
		$userDefined = FALSE;
		if (!empty($conf)) {
			if (is_array($conf)) {
				$default = (isset($conf['DEFAULT']) ? $conf['DEFAULT'] : PHP2GO_DEFAULT_LANGUAGE);
				$param = (!empty($conf['REQUEST_PARAM']) ? $conf['REQUEST_PARAM'] : NULL);
				$supported = (isset($conf['AVAILABLE']) ? (array)$conf['AVAILABLE'] : array_keys($this->localeTable));
				// geração de arquivo de linguagem Javascript
				if ($jsLangCode) {
					$language = $jsLangCode;
					$userDefined = FALSE;
				}
				// alteração dinâmica de linguagem por GET ou POST
				elseif (!empty($param) && !empty($_REQUEST[$param]) && in_array($_REQUEST[$param], $supported)) {
					$language = $_REQUEST[$param];
					$userDefined = TRUE;
				}
				// linguagem armazenada em um cookie
				elseif (isset($_COOKIE['PHP2GO_LANGUAGE']) && in_array($_COOKIE['PHP2GO_LANGUAGE'], $supported)) {
					$language = $_COOKIE['PHP2GO_LANGUAGE'];
					$userDefined = TRUE;
				}
				// linguagem definida anteriormente armazenada na sessão
				elseif (isset($_SESSION['PHP2GO_LANGUAGE']) && in_array($_SESSION['PHP2GO_LANGUAGE'], $supported)) {
					$language = $_SESSION['PHP2GO_LANGUAGE'];
				}
				// verifica se foi solicitada auto detecção
				elseif (@$conf['AUTO_DETECT'] == TRUE) {
					$language = $this->_Negotiator->negotiateLanguage($supported, $default);
				}
				// utiliza a linguagem padrão definida pelo usuário
				else {
					$language = $default;
				}
			} else {
				$language = (string)$conf;
			}
			// aplica a linguagem definida
			$this->_applyLocale($language, $userDefined);
		} else {
			$this->_applyLocale(PHP2GO_DEFAULT_LANGUAGE);
		}
		// charset
		$userCharset = $this->_Conf->getConfig('CHARSET');
		if ($userCharset == 'auto') {
			$charset = $this->_Negotiator->negotiateCharset(array('iso-8859-1', 'iso-8859-2', 'utf-8'), PHP2GO_DEFAULT_CHARSET);
			$this->charset = $charset;
			$this->_Conf->setConfig('CHARSET', $charset);
			ini_set('default_charset', $charset);
		} elseif (empty($userCharset)) {
			$this->charset = PHP2GO_DEFAULT_CHARSET;
			$this->_Conf->setConfig('CHARSET', PHP2GO_DEFAULT_CHARSET);
			ini_set('default_charset', PHP2GO_DEFAULT_CHARSET);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Init::_checkPhpVersion
	// @desc		Verifica a versão do PHP instalada no sistema operacional do servidor
	// @note		A requisição mínima para utilização do PHP2Go é a versão 4.3.0
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _checkPhpVersion() {
		if (!version_compare(PHP_VERSION, '4.3.0', '>=') == -1)
			setupError($this->_Lang->getLanguageValue('ERR_OLD_PHP_VERSION', array(PHP_VERSION, '4.3.0')));
	}

	//!-----------------------------------------------------------------
	// @function	Init::_checkAbsoluteUri
	// @desc		Verifica se a chave ABSOLUTE_URI do arquivo de configuração
	//				foi definida e se a mesma possui um valor correto
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _checkAbsoluteUri() {
		if (!$uri = $this->_Conf->getConfig('ABSOLUTE_URI')) {
			setupError($this->_Lang->getLanguageValue('ERR_ABSOLUTE_URI_NOT_FOUND'));
		} else {
			$pattern = "/^https?\:\/\/[a-zA-Z0-9\-\.\/\:~]+$/";
			if (!preg_match($pattern, $uri)) {
				setupError(sprintf($this->_Lang->getLanguageValue('ERR_URL_MALFORMED'), "'ABSOLUTE_URI'"));
			} else {
				$uriArr = @parse_url($uri);
				if (empty($uriArr)) {
					setupError(sprintf($this->_Lang->getLanguageValue('ERR_URL_MALFORMED'), "'ABSOLUTE_URI'"));
				} else {
					if (substr($uri, strlen($uri)-1, 1) != '/') {
						$this->_Conf->setConfig('ABSOLUTE_URI', $uri . '/');
					}
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Init::_checkDateFormat
	// @desc		Verifica o formato local de data definido no arquivo de configuração
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _checkDateFormat() {
		$dateFormat = $this->_Conf->getConfig('LOCAL_DATE_FORMAT');
		if ($dateFormat) {
			switch ($dateFormat) {
				case 'd/m/Y' :
					$this->_Conf->setConfig('LOCAL_DATE_TYPE', 'EURO');
					break;
				case 'Y/m/d' :
					$this->_Conf->setConfig('LOCAL_DATE_TYPE', 'US');
					break;
				default :
					$this->_Conf->setConfig('LOCAL_DATE_TYPE', 'EURO');
					break;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Init::_applyLocale
	// @desc		Aplica uma definição/alteração de linguagem nas tabelas
	//				de configuração e linguagem
	// @param		language string		Código da linguagem
	// @param		userDefined bool	"FALSE" Definida pelo usuário?
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _applyLocale($language, $userDefined=FALSE) {
		global $ADODB_LANG;
		if (isset($this->localeTable[$language])) {
			$this->locale = $language;
			// busca cada uma das definições para a linguagem
			$locale = $this->localeTable[$language][0];
			$langName = $this->localeTable[$language][1];
			$adodbLang = $this->localeTable[$language][2];
			// define a localização utilizando a função setlocale
			if (version_compare(PHP_VERSION, '4.3.0', '>=')) {
				$params = array_merge(array(LC_ALL), $locale);
				call_user_func_array('setlocale', $params);
			} else {
				setlocale(LC_ALL, $locale[0]);
			}
			// modifica a configuração
			$this->_Conf->setConfig('LOCALE', $locale[0]);
			$this->_Conf->setConfig('LANGUAGE_CODE', $language);
			$this->_Conf->setConfig('LANGUAGE_NAME', $langName);
			// grava na sessão se foi definida pelo usuário
			if ($userDefined) {
				$_SESSION['PHP2GO_LANGUAGE'] = $language;
				setcookie('PHP2GO_LANGUAGE', $language, time()+86400, '/');
			}
			// carrega a tabela de linguagem do framework
			$this->_Lang->clearLanguageBase();
			$this->_Lang->loadLanguageTableByFile(PHP2GO_ROOT . 'languages/' . $langName . '.inc', 'PHP2GO');
			$ADODB_LANG = $adodbLang;
			return TRUE;
		} else {
			setupError("The language <b>\"{$language}\"</b> is not supported by PHP2Go.");
			return FALSE;
		}
	}
}
?>
