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
// $Header: /www/cvsroot/php2go/core/auth/Auth.class.php,v 1.28 2006/11/25 12:16:52 mpont Exp $
// $Date: 2006/11/25 12:16:52 $

//------------------------------------------------------------------
import('php2go.auth.User');
import('php2go.net.HttpRequest');
import('php2go.text.StringUtils');
import('php2go.util.Callback');
//------------------------------------------------------------------

// @const AUTH_DEFAULT_LOGIN_FIELD "username"
// Nome padrão para a variável POST do nome de usuário
define('AUTH_DEFAULT_LOGIN_FIELD', 'username');
// @const AUTH_DEFAULT_PASSWORD_FIELD "password"
// Nome padrão para a variável POST que carrega a senha do usuário
define('AUTH_DEFAULT_PASSWORD_FIELD', 'password');
// @const AUTH_DEFAULT_EXPIRY_TIME
// Tempo padrão, em segundos, para a expiração da sessão
define('AUTH_DEFAULT_EXPIRY_TIME', 600);
// @const AUTH_DEFAULT_IDLE_TIME
// Tempo padrão, em segundos, de ociosidade da sessão
define('AUTH_DEFAULT_IDLE_TIME', 60);
// @const AUTH_STATE_INVALID "1"
// Estado de autenticação inválido, não existe um usuário autenticado
define('AUTH_STATE_INVALID', 1);
// @const AUTH_STATE_LOGIN "2"
// Estado de login, existe uma solicitação de autenticação enviada na requisição
define('AUTH_STATE_LOGIN', 2);
// @const AUTH_STATE_ERROR "3"
// Estado de erro, a solicitação de autenticação não foi realizada com sucesso
define('AUTH_STATE_ERROR', 3);
// @const AUTH_STATE_EXPIRED "4"
// O usuário logado teve sua sessão expirada
define('AUTH_STATE_EXPIRED', 4);
// @const AUTH_STATE_IDLED "5"
// O usuário logado manteve sua sessão ociosa por muito tempo
define('AUTH_STATE_IDLED', 5);
// @const AUTH_STATE_VALID "6"
// Existe uma sessão de usuário válida, não expirada e não ociosa
define('AUTH_STATE_VALID', 6);

//!-----------------------------------------------------------------
// @class		Auth
// @desc		Classe base para implementação de operações de autenticação
//				de usuários e criação de sessão com persistência de dados, controle
//				de expiração e ociosidade
// @package		php2go.auth
// @uses		HttpRequest
// @uses		SessionObject
// @uses		StringUtils
// @uses		TypeUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.28 $
// @note		A classe Auth é abstrata e não deve ser instanciada diretamente.
//				Devem ser criadas instâncias das classes que definem diferentes
//				tipos de autenticação
//!-----------------------------------------------------------------
class Auth extends PHP2Go
{
	var $authState;					// @var authState int							Estado de autenticação
	var $loginFunction;				// @var loginFunction Callback object			Função ou método responsável por exibir a tela de login ou redirecionar para o script que constroi o mesmo
	var $loginCallback;				// @var loginCallback Callback object			Função ou método a ser executado quando o login é efetuado com sucesso
	var $errorCallback;				// @var errorCallback Callback object			Função ou método a ser executado quando o login falha
	var $logoutCallback;			// @var logoutCallback Callback object			Função ou método a ser executado quando o logout é efetuado
	var $expiryCallback;			// @var expiryCallback Callback object			Função ou método para tratar expiração da sessão
	var $idlenessCallback;			// @var idlenessCallback Callback object		Função ou método que trata uma sessão que excede o tempo de ociosidade
	var $validSessionCallback;		// @var validSessionCallback Callback object	Função ou método executado quando existe uma sessão válida, já persistida na sessão
	var $loginFieldName;			// @var loginFieldName string					Nome da variável que contém o nome de usuário
	var $passwordFieldName;			// @var passwordFieldName string				Nome da variável que contém a senha
	var $sessionKeyName;			// @var sessionKeyName string					Nome para a variável de sessão que deve ser criada
	var $expiryTime;				// @var expiryTime int							Tempo de expiração da sessão, em segundos
	var $idleTime;					// @var idleTime int							Tempo que a sessão pode permanecer ociosa, em segundos
	var $initialized = FALSE;		// @var initialized bool						"FALSE" Indica se o autenticador já foi inicializado através do método init
	var $User = NULL;				// @var User User object						Instância da classe User (ou de uma subclasse) utilizada para persistir dados do usuário na sessão
	var $_login;					// @var login string							Nome de usuário para autenticação
	var $_password;					// @var password string							Senha para autenticação

	//!-----------------------------------------------------------------
	// @function	Auth::Auth
	// @desc		Construtor da classe
	// @param		sessionName string	"NULL" Nome da sessão de usuário
	// @access		public
	//!-----------------------------------------------------------------
	function Auth($sessionName=NULL) {
		parent::PHP2Go();
		if ($this->isA('Auth', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Auth'), E_USER_ERROR, __FILE__, __LINE__);
		$this->authState = AUTH_STATE_INVALID;
		$this->loginFieldName = AUTH_DEFAULT_LOGIN_FIELD;
		$this->passwordFieldName = AUTH_DEFAULT_PASSWORD_FIELD;
		$this->expiryTime = TypeUtils::ifFalse(PHP2Go::getConfigVal('AUTH.EXPIRY_TIME', FALSE), AUTH_DEFAULT_EXPIRY_TIME);
		$this->idleTime = TypeUtils::ifFalse(PHP2Go::getConfigVal('AUTH.IDLE_TIME', FALSE), AUTH_DEFAULT_IDLE_TIME);
		$this->User =& User::getInstance($sessionName);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::&getInstance
	// @desc		Constrói/retorna o singleton da classe de autenticação,
	//				utilizando a classe de autenticação definida na configuração,
	//				ou php2go.auth.AuthDb por padrão
	// @param		sessionName string	"NULL" Nome da sessão de usuário
	// @return		Auth object
	// @access		public
	//!-----------------------------------------------------------------
	function &getInstance($sessionName=NULL) {
		static $instances;
		$sessionName = TypeUtils::ifNull(
			TypeUtils::ifNull(
				$sessionName, PHP2Go::getConfigVal('USER.SESSION_NAME', FALSE)
			), 'PHP2GO_USER'
		);
		if (!isset($instances))
			$instances = array();
		if (!isset($instances[$sessionName])) {
			// busca o autenticador customizado definido na configuração
			if ($authClassPath = PHP2Go::getConfigVal('AUTH.AUTHENTICATOR_PATH', FALSE, FALSE)) {
				if ($authClass = classForPath($authClassPath)) {
					$instances[$sessionName] = new $authClass($sessionName);
					if (!TypeUtils::isInstanceOf($instances[$sessionName], 'Auth'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHENTICATOR', $authClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHENTICATOR_PATH', $authClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			}
			// usa o autenticador padrão - php2go.auth.AuthDb
			else {
				import('php2go.auth.AuthDb');
				$instances[$sessionName] = new AuthDb($sessionName);
			}
		}
		return $instances[$sessionName];
	}

	//!-----------------------------------------------------------------
	// @function	Auth::&getCurrentUser
	// @desc		Retorna uma instância da classe User representando o usuário do sistema
	// @return		User object Usuário ativo
	// @access		public
	//!-----------------------------------------------------------------
	function &getCurrentUser() {
		return $this->User;
	}

	//!-----------------------------------------------------------------
	// @function	Auth::getCurrentUsername
	// @desc		Retorna o nome do usuário ativo
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getCurrentUsername() {
		return $this->User->getUsername();
	}

	//!-----------------------------------------------------------------
	// @function	Auth::getAuthState
	// @desc		Retorna o estado atual de autenticação
	// @note		As constantes AUTH_STATE_* presentes nesta classe
	// 				constituem os valores de retorno possíveis deste método
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getAuthState() {
		return $this->authState;
	}

	//!-----------------------------------------------------------------
	// @function	Auth::getElapsedTime
	// @desc		Busca o número de segundos desde o início da sessão
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getElapsedTime() {
		return $this->User->getElapsedTime();
	}

	//!-----------------------------------------------------------------
	// @function	Auth::isValid
	// @desc		Verifica se existe um usuário autenticado no presente momento
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		return $this->User->isAuthenticated();
	}

	//!-----------------------------------------------------------------
	// @function	Auth::getExpiryTime
	// @desc		Retorna o tempo de expiração configurado na classe
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getExpiryTime() {
		return $this->expiryTime;
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setExpiryTime
	// @desc		Configura o tempo de expiração da sessão
	// @param		seconds int	Tempo de expiração, em segundos
	// @note		Utilize $seconds == 0 para desabilitar o controle de expiração
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setExpiryTime($seconds) {
		$this->expiryTime = TypeUtils::parseIntegerPositive($seconds);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::isExpired
	// @desc		Verifica se o tempo máximo de persistência da sessão foi excedido
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isExpired() {
		if ($this->expiryTime > 0 && $this->User->isAuthenticated()) {
			$elapsedTime = $this->User->getElapsedTime();
			if ($elapsedTime >= $this->expiryTime)
				return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Auth::getIdleTime
	// @desc		Retorna o tempo máximo de ociosidade permitido
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getIdleTime() {
		return $this->idleTime;
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setIdleTime
	// @desc		Define o tempo máximo de ociosidade da sessão
	// @param		seconds int	Tempo de ociosidade, em segundos
	// @note		Utilize $seconds == 0 para desabilitar o controle de ociosidade
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setIdleTime($seconds) {
		$this->idleTime = TypeUtils::parseIntegerPositive($seconds);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::isIdled
	// @desc		Verifica se o tempo máximo de ociosidade foi excedido
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isIdled() {
		if ($this->idleTime > 0 && $this->User->isAuthenticated()) {
			$lastIdleTime = $this->User->getLastIdleTime();
			if ($lastIdleTime >= $this->idleTime)
				return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setLoginFieldName
	// @desc		Define o nome da variável que contém o nome de usuário
	// @param		loginFieldName string		Nome da variável
	// @note		A classe define como padrão a variável 'username', que é buscada
	//				no vetor $_POST para a execução do login
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLoginFieldName($loginFieldName) {
		if (trim($loginFieldName) != '')
			$this->loginFieldName = trim($loginFieldName);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setPasswordFieldName
	// @desc		Define o nome da variável que contém o senha do usuário
	// @param		passwordFieldName string	Nome da variável
	// @note		O nome padrão para esta variável é 'password'. Ela é buscada
	//				no vetor $_POST para a execução do login
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setPasswordFieldName($passwordFieldName) {
		if (trim($passwordFieldName) != '')
			$this->passwordFieldName = $passwordFieldName;
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setLoginFunction
	// @desc		Define a função ou método que será responsável por gerar o formulário
	//				de autenticação de usuários
	// @param		loginFunction mixed		Nome da função ou vetor contendo objeto/método
	// @param		replace bool			"TRUE" Substituir handler atual, se existente
	// @note		Se este tratador de evento for executado após o encerramento de uma sessão
	//				por expiração ou inativação, ele receberá como parâmetro o usuário que estava logado.
	//				Em caso contrário (sessão inexistente), não será enviado nenhum parâmetro
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLoginFunction($loginFunction, $replace=TRUE) {
		if ($replace || !isset($this->loginFunction))
			$this->loginFunction = new Callback($loginFunction);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setLoginCallback
	// @desc		Define a função ou método que será chamada após o login ter sido efetuado com sucesso
	// @param		loginCallback mixed		Nome da função ou vetor contendo objeto/método
	// @param		replace bool			"TRUE" Substituir handler atual, se existente
	// @note		Esta função receberá como parâmetro uma instância da classe User
	//				representando o usuário que acaba de autenticar-se
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLoginCallback($loginCallback, $replace=TRUE) {
		if ($replace || !isset($this->loginFunction))
			$this->loginCallback = new Callback($loginCallback);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setErrorCallback
	// @desc		Define a função ou método que irá tratar a falha no login
	// @param		errorCallback mixed		Nome da função ou vetor contendo objeto/método
	// @param		replace bool			"TRUE" Substituir handler atual, se existente
	// @note		Esta função receberá como parâmetro uma instância da classe User
	//				representando o usuário que realizou a tentativa de login
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setErrorCallback($errorCallback, $replace=TRUE) {
		if ($replace || !isset($this->errorCallback))
			$this->errorCallback = new Callback($errorCallback);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setLogoutCallback
	// @desc		Define a função ou método que será chamado após o logout
	// @param		logoutCallback mixed	Nome da função ou vetor contendo objeto/método
	// @param		replace bool			"TRUE" Substituir handler atual, se existente
	// @note		Esta função receberá como parâmetro uma instância da classe User
	//				representando a sessão de usuário que foi destruída
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLogoutCallback($logoutCallback, $replace=TRUE) {
		if ($replace || !isset($this->logoutCallback))
			$this->logoutCallback = new Callback($logoutCallback);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setExpiryCallback
	// @desc		Define uma função ou método para tratar a expiração da sessão
	// @param		expiryCallback mixed	Nome da função ou vetor contendo objeto/método
	// @param		replace bool			"TRUE" Substituir handler atual, se existente
	// @note		Esta função receberá como parâmetro uma instância da classe User
	//				representando a sessão de usuário expirada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setExpiryCallback($expiryCallback, $replace=TRUE) {
		if ($replace || !isset($this->expiryCallback))
			$this->expiryCallback = new Callback($expiryCallback);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setIdlenessCallback
	// @desc		Define uma função ou método para tratar tempo ocioso excedido
	// @param		idlenessCallback mixed	Nome da função ou vetor contendo objeto/método
	// @param		replace bool			"TRUE" Substituir handler atual, se existente
	// @note		Esta função receberá como parâmetro uma instância da classe User
	//				representando a sessão de usuário inativa
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setIdlenessCallback($idlenessCallback, $replace=TRUE) {
		if ($replace || !isset($this->idlenessCallback))
			$this->idlenessCallback = new Callback($idlenessCallback);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::setValidSessionCallback
	// @desc		Define uma função para tratar a existência de uma sessão válida. Pode ser utilizada
	//				para atualizar as propriedades do usuário logado a cada requisição realizada com sessão válida
	// @param		validSessionCallback mixed	Nome da função ou vetor contendo objeto/método
	// @note		Esta função receberá como parâmetro uma instância da classe User representando o usuário logado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setValidSessionCallback($validSessionCallback) {
		$this->validSessionCallback = new Callback($validSessionCallback);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::init
	// @desc		Inicializa as operações de autenticação
	// @note		Este método pode executar as seguintes operações:<br><br>
	//				- se a sessão não for válida, irá executar a função do usuário para construção do login<br>
	//				- se existirem parâmetros de autenticação, irá tentar a autenticação, chamando as callbacks de sucesso ou falha conforme o resultado<br>
	//				- se existir uma sessão válida porém com tempo expirado, irá executar a callback de expiração ou reconstruir o login (loginFunction)<br>
	//				- se existir uma sessão válida porém ociosa, irá executar a callback de ociosidade ou reconstruir o login (loginFunction)<br>
	//				- do contrário, a sessão é válida e o tempo de ociosidade é zerado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function init() {
		if (!$this->initialized) {
			$this->_fetchAuthVars();
			// sessão inválida
			if (!$this->isValid()) {
				// se existem as variáveis de autenticação
				if (isset($this->_login) && isset($this->_password))
					$this->login();
				// chamada da função do usuário para construir o form de autenticação
				elseif (isset($this->loginFunction))
					$this->loginFunction->invoke();
			}
			// sessão válida, porém expirada
			elseif ($this->isExpired()) {
				$this->authState = AUTH_STATE_EXPIRED;
				$this->_handleExpiredSession();
			}
			// sessão válida, porém inativa por muito tempo entre 2 requisições
			elseif ($this->isIdled()) {
				$this->authState = AUTH_STATE_IDLED;
				$this->_handleIdleSession();
			}
			// sessão válida, não inativa e não expirada
			else {
				$this->authState = AUTH_STATE_VALID;
				if (isset($this->validSessionCallback))
					$this->validSessionCallback->invokeByRef($this->User);
			}
			$this->initialized = TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Auth::login
	// @desc		Método de autenticação. Verifica a autenticação do usuário
	//				se os parâmetros login e password forem encontrados
	// @access		protected
	// @note		Se a sessão estiver ativa, será encerrada
	// @return		void
	//!-----------------------------------------------------------------
	function login() {
		// executa o método de autenticação, implementado nas classes filhas
		$result = $this->authenticate();
		// o login falhou
		if ($result === FALSE) {
			$this->authState = AUTH_STATE_ERROR;
			if (isset($this->errorCallback)) {
				$user = $this->User;
				$user->logout();
				$user->setUsername($this->_login);
				$this->errorCallback->invoke($user);
			}
		}
		// o login teve sucesso
		else {
			$this->authState = AUTH_STATE_LOGIN;
			$this->User->authenticate($this->_login, (TypeUtils::isHashArray($result) ? $result : array()));
			// renovar o ID de sessão
			$regenId = PHP2Go::getConfigVal('AUTH.REGENID_ON_LOGIN', FALSE);
			if ($regenId === TRUE) {
				session_regenerate_id();
				if (!version_compare(phpversion(),"4.3.3",">="))
					setcookie(session_name(), session_id(), ini_get("session.cookie_lifetime"), "/");
			}
			// login callback
			if (isset($this->loginCallback))
				$this->loginCallback->invokeByRef($this->User);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Auth::logout
	// @desc		Encerra a sessão atual, se ela existir
	// @param		rebuildLogin bool	"FALSE" Indica se a opção de novo login deve ser oferecida
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function logout($rebuildLogin=FALSE) {
		$lastUser = $this->getCurrentUser();
		$lastUser->unregister();		
		if ($this->User->isAuthenticated())
			$this->User->logout();
		// invalidar a sessão
		$destroy = PHP2Go::getConfigVal('AUTH.DESTROY_ON_LOGOUT', FALSE);
		if ($destroy === TRUE)
			$this->User->destroy();
		// callbacks
		if (isset($this->logoutCallback))
			$this->logoutCallback->invoke($lastUser);
		if ($rebuildLogin && isset($this->loginFunction))
			$this->loginFunction->invoke($lastUser);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::authenticate
	// @desc		Este método deve ser implementado nas classes filhas
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function authenticate() {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Auth::_fetchAuthVars
	// @desc		Busca do vetor de variáveis POST os dados de autenticação
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _fetchAuthVars() {
		$login = HttpRequest::post($this->loginFieldName);
		if ($login)
			$this->_login = $login;
		$password = HttpRequest::post($this->passwordFieldName);
		if ($password)
			$this->_password = $password;
	}

	//!-----------------------------------------------------------------
	// @function	Auth::_handleExpiredSession
	// @desc		Trata uma sessão cujo tempo de persistência expirou
	// @note		Encerra e chama a callback de expiração, se existir.
	//				Do contrário, encerra e reconstrói o login
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _handleExpiredSession() {
		if (isset($this->expiryCallback)) {
			$lastUser = $this->getCurrentUser();
			$lastUser->registered = FALSE;
			$this->User->logout();
			$this->expiryCallback->invoke($lastUser);
		} else {
			$this->logout(TRUE);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Auth::_handleIdleSession
	// @desc		Trata uma sessão cujo tempo de ociosidade foi excedido
	// @access		private
	// @return		void
	// @note		Encerra e chama a callback de ociosidade, se existir.
	//				Do contrário, encerra e reconstrói o login
	//!-----------------------------------------------------------------
	function _handleIdleSession() {
		if (isset($this->idlenessCallback)) {
			$lastUser = $this->getCurrentUser();
			$lastUser->registered = FALSE;
			$this->User->logout();
			$this->idlenessCallback->invoke($lastUser);
		} else {
			$this->logout(TRUE);
		}
	}
}
?>