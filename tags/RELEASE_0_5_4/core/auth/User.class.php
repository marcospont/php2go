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
// $Header: /www/cvsroot/php2go/core/auth/User.class.php,v 1.12 2006/11/25 12:12:32 mpont Exp $
// $Date: 2006/11/25 12:12:32 $

//------------------------------------------------------------------
import('php2go.session.SessionObject');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		User
// @desc		A classe User é a base de armazenamento dos dados do usuário logado
//				em uma aplicação. É utilizada pela classe Auth (ou uma de suas classes
//				filhas) nas funções de criação, atualização e controle de uma sessão
//				de usuário. Uma sessão válida de usuário significa que uma instância da
//				classe User está gravada no escopo de sessão do PHP
// @package		php2go.auth
// @extends		SessionObject
// @uses		System
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.12 $
// @note		Exemplo de utilização:<br>
//				<pre>
//
//				$User =& User::getInstance();
//				if ($User->isAuthenticated()) {
//				&nbsp;&nbsp;&nbsp;print $User->getUsername();
//				&nbsp;&nbsp;&nbsp;print $User->getLastAccess('d/m/Y H:i:s');
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class User extends SessionObject
{
	var $username = NULL;		// @var username string		"NULL" Nome do usuário
	var $activeRole = NULL;		// @var activeRole mixed	"NULL" Perfil (ou perfis) ativos para o usuário
	var $loginTime = NULL;		// @var loginTime int		"NULL" Timestamp da criação da sessão
	var $lastAccess = NULL;		// @var lastAccess int		"NULL" Timestamp do último acesso do usuário
	
	//!-----------------------------------------------------------------
	// @function	User::User
	// @desc		Construtor da classe
	// @param		sessionName string	"NULL" Nome da variável de sessão
	// @note		**SEMPRE** utilize o método User::getInstance para criar/alterar
	//				instâncias da classe User. Desta forma, os dados do usuário serão
	//				automaticamente atualizados na sessão a cada encerramento de execução
	// @access		public	
	//!-----------------------------------------------------------------
	function User($sessionName=NULL) {
		parent::SessionObject($sessionName);		
	}
	
	//!-----------------------------------------------------------------
	// @function	User::&getInstance
	// @desc		Constrói/retorna o singleton da classe User, ou da classe
	//				filha definida no vetor de configurações do sistema
	// @param		sessionName string	"NULL" Nome da variável de sessão
	// @note		O parâmetro $sessionName permite criar múltiplos escopos de sessão
	// @return		User object	
	// @access		public
	//!-----------------------------------------------------------------
	function &getInstance($sessionName=NULL) {
		$instances =& User::getInstances();
		$sessionName = TypeUtils::ifNull(
			TypeUtils::ifNull(
				$sessionName, PHP2Go::getConfigVal('USER.SESSION_NAME', FALSE)
			), 'PHP2GO_USER'
		);
		if (!isset($instances[$sessionName])) {
			// busca o container definido na configuração
			if ($userClassPath = PHP2Go::getConfigVal('USER.CONTAINER_PATH', FALSE, FALSE)) {
				if ($userClass = classForPath($userClassPath)) {					
					$instances[$sessionName] = new $userClass($sessionName);
					if (!TypeUtils::isInstanceOf($instances[$sessionName], 'User'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_USERCONTAINER', $userClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_USERCONTAINER_PATH', $userClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			} 
			// usa o container padrão (php2go.auth.User)
			else {				
				$instances[$sessionName] = new User($sessionName);
			}
		}
		return $instances[$sessionName];
	}
	
	//!-----------------------------------------------------------------
	// @function	User::&getInstances
	// @desc		Retorna a(s) instância(s) de usuário ativas
	// @note		Normalmente, aplicações utilizam apenas um escopo de
	//				sessão para usuários. Logo, este método retornaria
	//				um array contendo apenas uma instância de usuário.
	//				Porém, aplicações com múltiplos escopos de sessão podem
	//				fazer com que este método retorne mais de uma instância
	// @return		array
	// @static
	//!-----------------------------------------------------------------
	function &getInstances() {
		static $instances;
		if (!isset($instances) && !is_array($instances))
			$instances = array();
		return $instances;
	}	
	
	//!-----------------------------------------------------------------
	// @function	User::shutdown
	// @desc		Método estático chamado no encerramento de cada execução
	//				para atualizar o timestamp de último acesso e publicar
	//				alterações realizadas nos usuários ativos na sessão
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function shutdown() {
		$instances =& User::getInstances();
		foreach ($instances as $name => $User) {
			if (TypeUtils::isInstanceOf($User, 'User') && $User->isAuthenticated()) {
				$User->lastAccess = System::getMicrotime();
				$User->update();
			}			
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	User::authenticate
	// @desc		Método que autentica e torna válido o objeto User, definindo o
	//				nome e inicializando as propriedades do usuário
	// @access		public
	// @param		username string		Nome do usuário
	// @param		properties array	"array()" Propriedades do usuário a serem gravadas na sessão
	// @return		void
	//!-----------------------------------------------------------------
	function authenticate($username, $properties=array()) {
		$this->username = $username;
		$this->loginTime = System::getMicrotime();
		$this->lastAccess = System::getMicrotime();
		foreach ((array)$properties as $name => $value)
			parent::createProperty($name, $value);
		parent::createTimeCounter('userTimeStamp');
		parent::register();
	}
	
	//!-----------------------------------------------------------------
	// @function	User::logout
	// @desc		Encerra a sessão do usuário, resetando os dados de autenticação
	// @access		public
	// @return		bool
	// @note		Se este método retornar FALSE, significa que não 
	//				foi possível remover a sessão do usuário
	//!-----------------------------------------------------------------
	function logout() {
		$result = parent::unregister();
		if ($result) {
			$this->username = NULL;
			$this->loginTime = NULL;
			$this->lastAccess = NULL;
			$this->timeCounters = array();		
		}
		return $result;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::isAuthenticated
	// @desc		Verifica se o usuário está autenticado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isAuthenticated() {
		return $this->registered;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::isInRole
	// @desc		Método abstrato que deve ser implementado em uma classe extendida,
	//				verificando se o usuário pertence a um determinado perfil
	// @note		A propriedade activeRole e os métodos isInRole, getActiveRole 
	//				e setActiveRole podem ser utilizados para implementar regras de RBAC 
	//				(role based authentication control) nas aplicações	
	// @param		role string		Nome do perfil
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isInRole($role) {
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getUsername
	// @desc		Retorna o nome do usuário
	// @access		public
	// @return		string Nome do usuário
	// @note		Se o usuário não está autenticado, este método retorna NULL
	//!-----------------------------------------------------------------
	function getUsername() {
		return $this->username;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::setUsername
	// @desc		Define/altera o nome do usuário na sessão
	// @access		public
	// @param		username string		Nome para o usuário
	// @return		void
	//!-----------------------------------------------------------------
	function setUsername($username) {
		$this->username = $username;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getActiveRole
	// @desc		Retorna o(s) perfil(is) ativo(s) para o usuário
	// @see			User::isInRole
	// @see			User::setActiveRole
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getActiveRole() {
		return $this->activeRole;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::setActiveRole
	// @desc		Define o(s) perfil(s) ativo(s) para o usuário
	// @param		role mixed		Perfil ou conjunto de perfis
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setActiveRole($role) {
		$this->activeRole = $role;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getLoginTime
	// @desc		Retorna o timestamp de criação da sessão
	// @access		public
	// @param		fmt string	"NULL" Formato, opcional
	// @return		mixed Timestamp, ou data/hora formatada se for fornecido um formato
	//!-----------------------------------------------------------------
	function getLoginTime($fmt=NULL) {
		if ($this->registered)
			return (empty($fmt) ? $this->loginTime : date($fmt, $this->loginTime));
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getLastAccess
	// @desc		Retorna o timestamp do último acesso do usuário
	// @access		public
	// @param		fmt string	"NULL" Formato, opcional
	// @return		mixed Timestamp, ou data/hora formatada se for fornecido um formato
	//!-----------------------------------------------------------------
	function getLastAccess($fmt=NULL) {
		if ($this->registered)
			return (empty($fmt) ? $this->lastAccess : date($fmt, $this->lastAccess));
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getElapsedTime
	// @desc		Retorna o número de segundos desde a criação da sessão do usuário
	// @access		public
	// @return		int Número de segundos
	//!-----------------------------------------------------------------
	function getElapsedTime() {
		if ($this->registered) {
			$Counter =& parent::getTimeCounter('userTimeStamp');
			return $Counter->getElapsedTime();
		}
		return 0;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getLastIdleTime
	// @desc		Retorna o tempo decorrido desde a última requisição do usuário
	// @access		public
	// @return		int Tempo decorrido, em segundos
	// @note		O valor de retorno deste método é utilizado em testes de tempo máximo de inatividade
	//!-----------------------------------------------------------------
	function getLastIdleTime() {
		return (System::getMicrotime() - $this->lastAccess);
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getPropertyValue
	// @desc		Sobrescreve a implementação do método getPropertyValue da
	//				classe SessionObject para que consultas por propriedades
	//				não existentes retornem NULL
	// @access		public
	// @param		name string		Nome da propriedade
	// @return		mixed Valor da propriedade ou NULL se ela não existir
	//!-----------------------------------------------------------------
	function getPropertyValue($name) {
		if ($this->registered) {
			$property = parent::getPropertyValue($name, FALSE);
			if ($property !== FALSE)
				return $property;
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::toString
	// @desc		Constrói a representação string do usuário
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function toString() {
		return sprintf("User object {\nUsername: %s\nAuthenticated: %d\nProperties: %s}", 
			$this->username, ($this->registered ? 1 : 0), dumpArray($this->properties)
		);
	}
}

// register shutdown function
PHP2Go::registerShutdownFunc(array('User', 'shutdown'));

?>