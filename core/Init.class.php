<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
 * Default configuration file name
 */
define('PHP2GO_CONFIG_FILE_NAME', 'userConfig.php');
/**
 * Default language code
 */
define('PHP2GO_DEFAULT_LANGUAGE', 'en-us');
/**
 * Default charset
 */
define('PHP2GO_DEFAULT_CHARSET', 'iso-8859-1');

/**
 * Initializes the framework
 *
 * This class is used inside the initialization script, and has the
 * responsability of starting up all basic services of the framework:
 * configuration, internationalization and session.
 *
 * @package php2go
 * @uses Conf
 * @uses LanguageBase
 * @uses LanguageNegotiator
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Init
{
	/**
	 * Locale code
	 *
	 * @var string
	 */
	var $locale;

	/**
	 * Charset code
	 *
	 * @var string
	 */
	var $charset;

	/**
	 * Available set of locales
	 *
	 * @var array
	 */
	var $localeTable = array();

	/**
	 * Reference to the singleton of the {@link Conf} class
	 *
	 * @var object Conf
	 * @access private
	 */
	var $_Conf;

	/**
	 * Refernce to the singleton of the {@link LanguageBase} class
	 *
	 * @var object LanguageBase
	 * @access private
	 */
	var $_Lang;

	/**
	 * Reference to the singleton of the {@link LocaleNegotiator} class
	 *
	 * @var object LocaleNegotiator
	 * @access private
	 */
	var $_Negotiator;

	/**
	 * Class constructor
	 *
	 * @return Init
	 */
	function Init() {
		$this->localeTable = array(
			'pt-br' => array(array('pt_BR', 'portuguese', 'pt_BR.iso-8859-1', 'pt_BR.utf-8'), 'brazilian-portuguese', 'pt-br'),
			'en-us' => array(array('en_US', 'en'), 'us-english', 'en'),
			'es' => array(array('es_ES', 'es'), 'spanish', 'es'),
			'cs' => array(array('cs_CZ', 'cz'), 'czech', 'cz'),
			'it' => array(array('it_IT', 'it'), 'italian', 'it'),
			'de-de' => array(array('de_DE', 'de', 'ge'), 'de-german', 'de'),
			'fr-fr' => array(array('fr_FR', 'fr'), 'french', 'fr'),
			'th' => array(array('th_TH'), 'thai', 'th')
		);
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

	/**
	 * Get the singleton of the class
	 *
	 * This method is called inside p2gConfig.php, and launches
	 * the framework's initialization.
	 *
	 * @return Init
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new Init();
		return $instance;
	}

	/**
	 * Change the active locale
	 *
	 * @param string $language Language code
	 * @return bool
	 */
	function setLocale($language) {
		return $this->_applyLocale($language);
	}

	/**
	 * Reset the language settings
	 *
	 * Check if there's a language code (choosen by the user) stored
	 * in the cookies or in the session scope. If it exists, it is removed
	 * and all language/locale configurations are reinitialized based on
	 * the default language settings.
	 *
	 * This method could be useful to remove a language choice right
	 * after the user logs off the application.
	 */
	function resetLocale() {
		if (isset($_SESSION['PHP2GO_LANGUAGE']) || isset($_COOKIE['PHP2GO_LANGUAGE'])) {
			unset($_SESSION['PHP2GO_LANGUAGE']);
			setcookie('PHP2GO_LANGUAGE', @$_COOKIE['PHP2GO_LANGUAGE'], time()-86400, '/');
			unset($_COOKIE['PHP2GO_LANGUAGE']);
			$this->_initLocale();
		}
	}

	/**
	 * Initialize the configuration settings
	 *
	 * @access private
	 */
	function _initConfig() {
		// check if there's an user-defined $P2G_USER_CFG array in the global scope
		global $P2G_USER_CFG;
		if (isset($P2G_USER_CFG) && is_array($P2G_USER_CFG)) {
			$this->_Conf->setConfig($P2G_USER_CFG);
			$P2G_USER_CFG = NULL;
		}
		// check if there's a configuration file in the root of the application's domain
		elseif (@file_exists($_SERVER['DOCUMENT_ROOT'] . '/userConfig.php')) {
			$this->_Conf->loadConfig($_SERVER['DOCUMENT_ROOT'] . '/userConfig.php');
		}
		// try to use the framework's default config file
		elseif (@file_exists(PHP2GO_ROOT . 'userConfig.php')) {
			$this->_Conf->loadConfig(PHP2GO_ROOT . 'userConfig.php');
		}
		else {
			setupError('The default configuration file <b>userConfig.php</b> was not found at <b>' . PHP2GO_ROOT . '</b>');
		}
	}

	/**
	 * Initializes the session
	 *
	 * Configure all session properties based on the global settings: session
	 * name, session lifetime, session save path, ... Start PHP session if
	 * SESSION.AUTO_START is missing or set to true. Define a set of ini
	 * entries for sessions.
	 *
	 * @access private
	 */
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
			// values in the old format
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
		// session cookie name
		if ($conf['NAME'])
			ini_set('session.name', $conf['NAME']);
		ini_set('session.cache_limiter', 'must_revalidate');
		ini_set('session.use_cookies', TRUE);
		ini_set('session.cookie_lifetime', 0);
		// previously started session
		/*
		if (ini_get('session.auto_start') == TRUE) {
			$_SESSION = array();
			if (isset($_COOKIE[session_name()]))
				setcookie(session_name(), '', time()-86400, '/');
			session_unset();
			@session_destroy();
		}*/
		// use only cookies or not
		if ($conf['COOKIES_ONLY']) {
			ini_set('session.use_only_cookies', TRUE);
			ini_set('session.use_trans_sid', FALSE);
			ini_set('url_rewriter.tags', '');
		} else {
			ini_set('session.use_only_cookies', FALSE);
			ini_set('session.use_trans_sid', TRUE);
			ini_set('url_rewriter.tags', 'a=href,frame=src,input=src,form=fakeentry,fieldset=');
		}
		// garbage collection settings
		ini_set('session.gc_probability', 1);
		if (is_int($conf['LIFETIME']))
			ini_set('session.gc_maxlifetime', $conf['LIFETIME']);
		// save path
		if (!empty($conf['SAVE_PATH']) && is_dir($conf['SAVE_PATH']))
			session_save_path($conf['SAVE_PATH']);
		// auto start
		if ($conf['AUTO_START'] !== FALSE)
			@session_start();
	}

	/**
	 * Initialize locale and language settings
	 *
	 * Steps of language code detection:
	 * # use LANGUAGE.REQUEST_PARAM, if present and mapping to a supported language code
	 * # use the previously selected language code stored in the cookies, if present and supported
	 * # use the previously selected language stored in the session scope, if present and supported
	 * # use auto detected language, if auto detection is enabled and detected language is supported
	 * # use language code defined as default, if present in the config settings
	 * # use framework's default language code (en-us)
	 *
	 * @access private
	 */
	function _initLocale() {
		// language
		$conf = $this->_Conf->getConfig('LANGUAGE');
		$jsLangCode = (isset($_REQUEST['locale']) && preg_match("/resources\/javascript\/lang.php$/", @$_SERVER['PHP_SELF']) ? $_REQUEST['locale'] : NULL);
		$userDefined = FALSE;
		if (!empty($conf)) {
			if (is_array($conf)) {
				$default = (isset($conf['DEFAULT']) ? $conf['DEFAULT'] : PHP2GO_DEFAULT_LANGUAGE);
				$param = (!empty($conf['REQUEST_PARAM']) ? $conf['REQUEST_PARAM'] : NULL);
				$supported = (isset($conf['AVAILABLE']) ? (array)$conf['AVAILABLE'] : array_keys($this->localeTable));
				// JS language file generation
				if ($jsLangCode) {
					$language = $jsLangCode;
					$userDefined = FALSE;
				}
				// dynamic language change from GET or POST
				elseif (!empty($param) && !empty($_REQUEST[$param]) && in_array($_REQUEST[$param], $supported)) {
					$language = $_REQUEST[$param];
					$userDefined = TRUE;
				}
				// language code stored in a cookie
				elseif (isset($_COOKIE['PHP2GO_LANGUAGE']) && in_array($_COOKIE['PHP2GO_LANGUAGE'], $supported)) {
					$language = $_COOKIE['PHP2GO_LANGUAGE'];
					$userDefined = TRUE;
				}
				// language code previously stored in the session
				elseif (isset($_SESSION['PHP2GO_LANGUAGE']) && in_array($_SESSION['PHP2GO_LANGUAGE'], $supported)) {
					$language = $_SESSION['PHP2GO_LANGUAGE'];
				}
				// check if auto detection is enabled
				elseif (@$conf['AUTO_DETECT'] == TRUE) {
					$language = $this->_Negotiator->negotiateLanguage($supported, $default);
				}
				// use user-defined default language
				else {
					$language = $default;
				}
			} else {
				$language = (string)$conf;
			}
			// applies the choosen language
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

	/**
	 * Check if the running PHP version satisfies the
	 * minimum requirement of the framework
	 *
	 * @access private
	 */
	function _checkPhpVersion() {
		if (!version_compare(PHP_VERSION, '4.3.0', '>=') == -1)
			setupError($this->_Lang->getLanguageValue('ERR_OLD_PHP_VERSION', array(PHP_VERSION, '4.3.0')));
	}

	/**
	 * Check if the ABSOLUTE_URI configuration setting
	 * is present and represents a valid absolute or relative URL
	 *
	 * @access private
	 */
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

	/**
	 * Validate LOCAL_DATE_FORMAT provided in the configuration settings
	 *
	 * @access private
	 */
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

	/**
	 * Apply a given language code
	 *
	 * @param string $language Language code
	 * @param bool $userDefined Whether this is a user choice
	 * @return bool
	 */
	function _applyLocale($language, $userDefined=FALSE) {
		global $ADODB_LANG;
		if (isset($this->localeTable[$language])) {
			$this->locale = $language;
			// get locale settings
			$locale = $this->localeTable[$language][0];
			$langName = $this->localeTable[$language][1];
			$adodbLang = $this->localeTable[$language][2];
			// defines locale using setlocale()
			if (version_compare(PHP_VERSION, '4.3.0', '>=')) {
				$params = array_merge(array(LC_ALL), $locale);
				call_user_func_array('setlocale', $params);
			} else {
				setlocale(LC_ALL, $locale[0]);
			}
			// set configuration entries
			$this->_Conf->setConfig('LOCALE', $locale[0]);
			$this->_Conf->setConfig('LANGUAGE_CODE', $language);
			$this->_Conf->setConfig('LANGUAGE_NAME', $langName);
			// if language code is user-defined, save it in the session scope
			if ($userDefined) {
				$_SESSION['PHP2GO_LANGUAGE'] = $language;
				setcookie('PHP2GO_LANGUAGE', $language, time()+86400, '/');
			}
			// load framework's language table
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
