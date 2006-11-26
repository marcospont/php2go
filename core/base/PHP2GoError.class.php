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
// $Header: /www/cvsroot/php2go/core/base/PHP2GoError.class.php,v 1.28 2006/10/26 04:27:02 mpont Exp $
// $Date: 2006/10/26 04:27:02 $

// @const E_DATABASE_ERROR "0"
// Define o tipo dos erros de banco de dados, que são disparados a partir da biblioteca ADODB
define('E_DATABASE_ERROR', 0);

if (!defined('E_STRICT'))
	define('E_STRICT', 2048);

//!-----------------------------------------------------------------
// @class		PHP2GoError
// @desc		Controla os erros disparados pelos objetos, reunindo
// 				informações sobre o erro e tratando-os
// @package		php2go.base
// @extends 	PHP2Go
// @author 		Marcos Pont
// @version		$Revision: 1.28 $
//!-----------------------------------------------------------------
class PHP2GoError extends PHP2Go
{
	var $object; 						// @var object object			Objeto onde ocorreu o erro
	var $msg = ''; 						// @var msg string				"" Mensagem de Erro
	var $extra = '';					// @var extra string			"" Informações adicionais do erro
	var $type = E_USER_ERROR; 			// @var type int				"E_USER_ERROR" Tipo do erro capturado
	var $typeDesc; 						// @var	typeDesc string			Descrição do tipo de erro capturado
	var $file; 							// @var file string				Arquivo onde o erro foi disparado, setado pelo usuário
	var $line; 							// @var line int				Linha do arquivo onde o erro ocorreu, setado pelo usuário
	var $dateFormat = '%b %d %H:%M:%S';	// @var dateFormat string		Formato de data utilizado nos logs de erro	
	var $ignoreErrors = 				// @var ignoreErrors array		Vetor de erros a serem ignorados
	array (
		'UNDEFINED INDEX',
		'USE OF UNDEFINED CONSTANT'
	);

	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::PHP2GoError
	// @desc 		Construtor da classe PHP2GoError
	// @access 		public
	//!-----------------------------------------------------------------
	function PHP2GoError() {
		parent::PHP2Go();
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::&getInstance
	// @desc 		Retorna uma instância do tratador de erros atual,
	// 				criando uma se não existir (singleton)
	// @return 		PHP2GoError object Instância da classe PHP2GoError
	// @access 		public	
	// @static	
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new PHP2GoError();
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::setObject
	// @desc 		Configura o objeto onde o erro ocorreu
	// @param 		object object	Objeto onde o erro ocorreu
	// @access 		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setObject($object) {
		$this->object = get_class($object);
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::setMessage
	// @desc 		Configura a mensagem do erro
	// @param 		message string	Mensagem de erro
	// @param		extra string	"" Descrição adicional/complementar do erro
	// @access 		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setMessage($message, $extra='') {
		$this->msg = $message;
		if (!empty($extra))
			$this->extra = $extra; 
	}
	
	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::setType
	// @desc 		Configura o tipo do erro capturado
	// @param 		type int		Tipo do erro, conforme a especificação do PHP
	// @param		typeDesc string	"" Descritivo do tipo de erro
	// @access 		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function setType($type) {
		$this->type = $type;
		$errorTypes = PHP2Go::getLangVal('ERR_TYPES');				
		$this->typeDesc = ($type == E_DATABASE_ERROR ? PHP2Go::getLangVal('ERR_DATABASE') : $errorTypes[$type]);
	}
	
	//!-----------------------------------------------------------------
	// @function	PHP2GoError::setFile
	// @desc		Seta o arquivo onde o erro ocorreu
	// @param		fileName string	"" Nome do arquivo
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function setFile($fileName='') {
		if (trim($fileName) != '')
			$this->file = $fileName;
	}
	
	//!-----------------------------------------------------------------
	// @function	PHP2GoError::setLine
	// @desc		Seta a linha do arquivo onde o erro ocorreu
	// @param		lineNumber int	"NULL" Número da linha
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function setLine($lineNumber=NULL) {
		if (!TypeUtils::isNull($lineNumber))
			$this->line = $lineNumber;
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::captureErrors
	// @desc 		Verifica na configuração se os erros devem ser capturados
	// @access 		public
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function captureErrors() {
		return (TypeUtils::isTrue(PHP2Go::getConfigVal('CAPTURE_ERRORS', FALSE)));
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::logErrors
	// @desc 		Verifica se o log dos erros ocorridos está habilitado
	// @access 		public
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function logErrors() {
		return (TypeUtils::isTrue(PHP2Go::getConfigVal('LOG_ERRORS', FALSE)));
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::showErrors
	// @desc 		Verifica na configuração se os erros encontrados devem ser exibidos
	// @access 		public
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function showErrors() {
		return (TypeUtils::isTrue(PHP2Go::getConfigVal('SHOW_ERRORS', FALSE)));
	}
	
	//!-----------------------------------------------------------------
	// @function	PHP2GoError::debugTrace
	// @desc		Verifica se o backtrace dos erros deve ser capturado
	// @access		public
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function debugTrace() {
		return (TypeUtils::isTrue(PHP2Go::getConfigVal('DEBUG_TRACE', FALSE)));
	}
	
	//!-----------------------------------------------------------------
	// @function	PHP2GoError::isIgnoreError
	// @desc		Verifica se uma determinada mensagem de erro deve ser ignorada
	// @param		errorMessage string	Mensagem do erro encontrado
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function isIgnoreError($errorMessage) {
		$userIgnoreErrors = PHP2Go::getConfigVal('IGNORE_ERRORS', FALSE);
		$ignoreErrors = (!TypeUtils::isArray($userIgnoreErrors) ? $this->ignoreErrors : array_merge($this->ignoreErrors, $userIgnoreErrors));
		for ($i = 0; $i < sizeof($ignoreErrors); $i++) {
			if (eregi($ignoreErrors[$i], $errorMessage) !== FALSE)
				return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	PHP2GoError::isStrictWarning
	// @desc		Verifica se o erro é do tipo E_STRICT, introduzido no
	//				PHP 5 para informar trechos de código que devem ser
	//				alterados para se adaptar à Zend Engine 2
	// @param		errorCode int	Código do erro disparado
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function isStrictWarning($errorCode) {
		return ($errorCode == E_STRICT);
	}
	
	//!-----------------------------------------------------------------
	// @function	PHP2GoError::isUserError
	// @desc		Verifica se o erro disparado é um erro da família E_USER
	// @param		errorCode int	Código do erro disparado
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function isUserError($errorCode) {
		return ($errorCode == E_USER_ERROR || $errorCode == E_USER_WARNING || $errorCode == E_USER_NOTICE);
	}
	
	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::raise
	// @desc 		Dispara o tratador de erros
	// @access 		public
	// @return		void	
	//!-----------------------------------------------------------------
	function raise() {
		if ($this->captureErrors()) {
			Registry::set('PHP2Go_error', $this);
			trigger_error($this->msg, $this->type);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	PHP2GoError::log
	// @desc		Dispara o tratador de erros apenas para gravar em log a exceção ocorrida
	// @param		logFile string		Nome do arquivo de log
	// @note		O nome do arquivo será processado pela função strftime para substituir
	//				valores como dia, mês, ano, hora, minuto, ...
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function log($logFile) {
		if ($this->captureErrors()) {
			$this->_logError(array(
				'CODE' => $this->type,
				'TYPE' => $this->typeDesc,
				'MESSAGE' => $this->_formatMessage(),
				'EXTRA' => $this->extra,
				'FILE' => (isset($this->file) ? $this->file : __FILE__),
				'LINE' => (isset($this->line) ? $this->line : __LINE__),
				'TRACE' => $this->_getStackTrace(),
				'REQUEST' => $this->_getRequestData()
			), strftime($logFile));
		}
	}

	//!-----------------------------------------------------------------
	// @function	PHP2GoError::handle
	// @desc		Trata um erro ocorrido, exibindo e/ou gravando em log de acordo
	//				com a configuração definida pelo usuário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function handle() {
		if ($this->type != E_STRICT) {
			// monta o conjunto de dados do erro
			$errorData = array(
				'CODE' => $this->type,
				'TYPE' => $this->typeDesc,
				'MESSAGE' => $this->msg,
				'EXTRA' => $this->extra,
				'FILE' => (isset($this->file) ? $this->file : __FILE__),
				'LINE' => (isset($this->line) ? $this->line : __LINE__),
				'TRACE' => $this->_getStackTrace(),
				'REQUEST' => $this->_getRequestData()
			);
			// exibe o erro, dependendo da configuração de exibição de erros
			if ($this->showErrors())
				$this->_displayError($errorData);
			// grava o erro no arquivo de log
			$logFile = ($this->type == E_DATABASE_ERROR ? PHP2Go::getConfigVal('DB_ERROR_LOG_FILE', FALSE) : PHP2Go::getConfigVal('ERROR_LOG_FILE', FALSE));				
			$this->_logError($errorData, strftime($logFile));
			// aborta a execução do script para erros de usuário ou erros de banco de dados
			if ($this->type == E_USER_ERROR || $this->type == E_DATABASE_ERROR)
				exit;			
		}
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::_displayError
	// @desc 		Exibe informações sobre um erro ocorrido
	// @param 		errData array	Vetor de dados do erro
	// @access 		private	
	// @return		void	
	//!-----------------------------------------------------------------
	function _displayError($errData) {
		$extra = @$errData['EXTRA'];
		$location = ($errData['FILE'] != '' && $errData['LINE'] != '' ? "<br>on {$errData['FILE']}, {$errData['LINE']}" : '');
		$stackTrace = '';
		if (!empty($errData['TRACE'])) {
			$stackTrace .= "<br><b>STACK TRACE</b><pre>";
			foreach ($errData['TRACE'] as $element)
				$stackTrace .= "\tat {$element['FUNCTION']}({$element['ARGS']})\n\t\ton {$element['FILE']}, {$element['LINE']}\n";
			$stackTrace .= "</pre>";
		}
		print ("
			<table width='100%' cellpadding='6' cellspacing='0' border='1' bordercolor='#ff0000' bgcolor='#efefef'>
				<tr><td>
					<table border='0' cellpadding='2' cellspacing='0' width='100%'>
						<tr><td width='20' valign='top' rowspan='4'><img src='" . PHP2Go::getConfigVal('ABSOLUTE_URI', false) . "resources/icon/error.gif' hspace='3'></td><td style='font-family:Arial;font-weight:bolder;font-size:14px;color:#ff0000'>PHP2Go - {$errData['TYPE']}</td></tr>
					   	<tr><td style='font-family:Arial;font-size:12px'><b>{$errData['MESSAGE']}</b></td></tr>
						<tr><td style='font-family:Arial;font-size:12px'>{$extra}{$location}</td></tr>
					   	<tr><td style='font-family:Arial;font-size:12px'>{$stackTrace}</tr>
					</table>
				</td></tr>
			</table>
		");
	}
	
	//!-----------------------------------------------------------------
	// @function	PHP2GoError::_logError
	// @desc		Responsável por gravar em um arquivo de log um erro ocorrido
	// @param		errData array	Vetor de dados do erro
	// @access		private	
	// @return		void
	//!-----------------------------------------------------------------
	function _logError($errData, $logFile) {
		if ($this->logErrors()) {
			$nl = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? "\r\n" : "\n");
			$logString = "[" . strftime($this->dateFormat) . "]" . $nl;
			$logString .= "\tTYPE=\"{$errData['TYPE']}\"" . $nl;
			$logString .= "\tCODE={$errData['CODE']}" . $nl;
			$logString .= "\tMESSAGE=\"" . str_replace(array('<br>','<br>',"\r","\n","\""), array(' ',' ',' ',' ','\''), $errData['MESSAGE']) . "\"" . $nl;
			if (!empty($errData['EXTRA']))
				$logString .= "\tEXTRA=\"" . str_replace(array('<br>','<br>',"\r","\n","\""), array(' ',' ',' ',' ','\''), $errData['EXTRA']) . "\"" . $nl;
			$logString .= "\tFILE=\"{$errData['FILE']}\"" . $nl;
			$logString .= "\tLINE={$errData['LINE']}" . $nl;
			if (!empty($errData['TRACE'])) {
				$i=0;
				foreach ($errData['TRACE'] as $element) {
					$logString .= "\tTRACE{$i}=\"" . $element['FUNCTION'] . '(' . $element['ARGS'] . ')' . (!empty($element['FILE']) ? ' (' . $element['FILE'] . ', ' . $element['LINE'] . ')' : '') . "\"" . $nl;
					$i++;
				}
			}
			if (!empty($errData['REQUEST'])) {				
				foreach ($errData['REQUEST'] as $key=>$value) {
					if (is_array($value))
						$logString .= "\t{$key}=\"" . dumpArray($value) . "\"" . $nl;
					else
						$logString .= "\t{$key}=\"{$value}\"" . $nl;
				}
			}
			if ($logFile != '' && @touch($logFile))
				@error_log($logString, 3, $logFile);
			else
				@error_log($logString, 0);
		}		
	}	
	
	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::_formatMessage
	// @desc 		Constrói a mensagem descritiva do erro
	// @return	 	string Mensagem formatada (objeto + mensagem)
	// @access 		private	
	//!-----------------------------------------------------------------
	function _formatMessage() {
		if (!isset($this->object))
			return PHP2Go::getLangVal('ERR_SCRIPT_MESSAGE', $this->msg);		
		return PHP2Go::getLangVal('ERR_OBJ_MESSAGE', array($this->object, $this->msg));
	}

	//!-----------------------------------------------------------------
	// @function	PHP2GoError::_getStackTrace
	// @desc		Constrói o stack trace da pilha de execução para um erro encontrado
	// @access		private	
	// @return		void	
	//!-----------------------------------------------------------------
	function _getStackTrace() {		
		if ($this->debugTrace()) {
			$trace = debug_backtrace();
			$result = array();
			for ($i=0, $size=sizeof($trace); $i<$size; $i++) {
				$qualifiedName = (isset($trace[$i]['class']) ? $trace[$i]['class'] . $trace[$i]['type'] : '') . $trace[$i]['function'];
				if (strtolower($qualifiedName) == 'php2goerror->_getstacktrace')
					continue;
				// nome da função ou método
				$element['FUNCTION'] = $qualifiedName;
				// parâmetros da função ou método
				if (sizeof($trace[$i]['args']) > 0) {
					$pars = array();
					for ($j=0; $j<sizeof($trace[$i]['args']); $j++) {
						if (is_string($trace[$i]['args'][$j])) {
							$arg = eregi_replace("<br>", " ", $trace[$i]['args'][$j]);
							$arg = ereg_replace("[[:blank:]]{2,}", " ", ereg_replace("[\r\t\n]{1,}", "", $arg));
							$pars[] = "'" . (strlen($arg) > 200 ? substr($arg, 0, 200) . "...'(" . strlen($arg) . ")" : $arg . "'");
						} elseif (is_object($trace[$i]['args'][$j])) {
							$pars[] = get_class($trace[$i]['args'][$j]) . " object";
						} elseif (is_bool($trace[$i]['args'][$j])) {
							$pars[] = ($trace[$i]['args'][$j] == TRUE ? "TRUE" : "FALSE");
						} elseif (is_null($trace[$i]['args'][$j])) {
							$pars[] = "NULL";
						} elseif (is_array($trace[$i]['args'][$j])) {
							$pars[] = dumpArray($trace[$i]['args'][$j]);
						} elseif (is_resource($trace[$i]['args'][$j])) {
							$pars[] = get_resource_type($trace[$i]['args'][$j]);
						} else {
							$pars[] = $trace[$i]['args'][$j];
						}
					}
					$element['ARGS'] = implode(',', $pars);
				} else {
					$element['ARGS'] = '';
				}
				// caminho completo para o arquivo
				$element['FILE'] = @$trace[$i]['file'];
				// linha do arquivo
				$element['LINE'] = @$trace[$i]['line'];
				$result[] = $element;
			}
			return $result;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function 	PHP2GoError::_getRequestData
	// @desc 		Captura informações sobre a requisição no momento do erro
	// @return 		string Informações sobre o sistema para serem registradas junto à mensagem de erro
	// @access 		private	
	//!-----------------------------------------------------------------
	function _getRequestData() {
		$info = array();
		if (isset($_SERVER)) {
			$method = $_SERVER['REQUEST_METHOD'];
			if ($method == 'GET') {
				$info['METHOD'] = 'GET';
				$info['AGENT'] = $_SERVER['HTTP_USER_AGENT'];
				if (isset($_SERVER['HTTP_REFERER']))
					$info['REFERER'] = $_SERVER['HTTP_REFERER'];
				if (!empty($_GET)) {
					$info['PARAMS'] = array();
					foreach ($_GET as $key=>$value) {
						if (is_array($value))
							$info['PARAMS'][$key] = dumpArray($value);
						else
							$info['PARAMS'][$key] = $value;
					}						
				}
			} elseif ($method == 'POST') {
				$info['METHOD'] = 'POST';
				$info['URI'] = $_SERVER['REQUEST_URI'];
				$info['AGENT'] = $_SERVER['HTTP_USER_AGENT'];
				if (isset($_SERVER['HTTP_REFERER']))
					$info['REFERER'] = $_SERVER['HTTP_REFERER'];
				if (!empty($_POST)) {
					$info['PARAMS'] = array();
					foreach ($_POST as $key=>$value) {
						if (is_array($value))
							$info['PARAMS'][$key] = dumpArray($value);
						else
							$info['PARAMS'][$key] = $value;
					}
				}
			}
			// cookies
			if (!empty($_COOKIE))
				$info['COOKIES'] = $_COOKIE;
			// variáveis de sessão
			if (!empty($_SESSION)) {
				$info['SESSION'] = array();
				foreach ($_SESSION as $key=>$value) {
					if (is_scalar($value)) {
						$info['SESSION'][$key] = $value;
					} elseif (is_array($value)) {
						$info['SESSION'][$key] = dumpArray($value);
					} else {
						$exported = ereg_replace("[[:blank:]]{2,}", " ", ereg_replace("[\r\t\n]{1,}", " ", exportVariable($value)));
						$info['SESSION'][$key] = $exported;
					}
				}
			}
		}
		return $info;
	}	
}
?>