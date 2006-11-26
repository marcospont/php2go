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
// @desc		A classe User � a base de armazenamento dos dados do usu�rio logado
//				em uma aplica��o. � utilizada pela classe Auth (ou uma de suas classes
//				filhas) nas fun��es de cria��o, atualiza��o e controle de uma sess�o
//				de usu�rio. Uma sess�o v�lida de usu�rio significa que uma inst�ncia da
//				classe User est� gravada no escopo de sess�o do PHP
// @package		php2go.auth
// @extends		SessionObject
// @uses		System
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.12 $
// @note		Exemplo de utiliza��o:<br>
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
	var $username = NULL;		// @var username string		"NULL" Nome do usu�rio
	var $activeRole = NULL;		// @var activeRole mixed	"NULL" Perfil (ou perfis) ativos para o usu�rio
	var $loginTime = NULL;		// @var loginTime int		"NULL" Timestamp da cria��o da sess�o
	var $lastAccess = NULL;		// @var lastAccess int		"NULL" Timestamp do �ltimo acesso do usu�rio
	
	//!-----------------------------------------------------------------
	// @function	User::User
	// @desc		Construtor da classe
	// @param		sessionName string	"NULL" Nome da vari�vel de sess�o
	// @note		**SEMPRE** utilize o m�todo User::getInstance para criar/alterar
	//				inst�ncias da classe User. Desta forma, os dados do usu�rio ser�o
	//				automaticamente atualizados na sess�o a cada encerramento de execu��o
	// @access		public	
	//!-----------------------------------------------------------------
	function User($sessionName=NULL) {
		parent::SessionObject($sessionName);		
	}
	
	//!-----------------------------------------------------------------
	// @function	User::&getInstance
	// @desc		Constr�i/retorna o singleton da classe User, ou da classe
	//				filha definida no vetor de configura��es do sistema
	// @param		sessionName string	"NULL" Nome da vari�vel de sess�o
	// @note		O par�metro $sessionName permite criar m�ltiplos escopos de sess�o
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
			// busca o container definido na configura��o
			if ($userClassPath = PHP2Go::getConfigVal('USER.CONTAINER_PATH', FALSE, FALSE)) {
				if ($userClass = classForPath($userClassPath)) {					
					$instances[$sessionName] = new $userClass($sessionName);
					if (!TypeUtils::isInstanceOf($instances[$sessionName], 'User'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_USERCONTAINER', $userClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_USERCONTAINER_PATH', $userClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			} 
			// usa o container padr�o (php2go.auth.User)
			else {				
				$instances[$sessionName] = new User($sessionName);
			}
		}
		return $instances[$sessionName];
	}
	
	//!-----------------------------------------------------------------
	// @function	User::&getInstances
	// @desc		Retorna a(s) inst�ncia(s) de usu�rio ativas
	// @note		Normalmente, aplica��es utilizam apenas um escopo de
	//				sess�o para usu�rios. Logo, este m�todo retornaria
	//				um array contendo apenas uma inst�ncia de usu�rio.
	//				Por�m, aplica��es com m�ltiplos escopos de sess�o podem
	//				fazer com que este m�todo retorne mais de uma inst�ncia
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
	// @desc		M�todo est�tico chamado no encerramento de cada execu��o
	//				para atualizar o timestamp de �ltimo acesso e publicar
	//				altera��es realizadas nos usu�rios ativos na sess�o
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
	// @desc		M�todo que autentica e torna v�lido o objeto User, definindo o
	//				nome e inicializando as propriedades do usu�rio
	// @access		public
	// @param		username string		Nome do usu�rio
	// @param		properties array	"array()" Propriedades do usu�rio a serem gravadas na sess�o
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
	// @desc		Encerra a sess�o do usu�rio, resetando os dados de autentica��o
	// @access		public
	// @return		bool
	// @note		Se este m�todo retornar FALSE, significa que n�o 
	//				foi poss�vel remover a sess�o do usu�rio
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
	// @desc		Verifica se o usu�rio est� autenticado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isAuthenticated() {
		return $this->registered;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::isInRole
	// @desc		M�todo abstrato que deve ser implementado em uma classe extendida,
	//				verificando se o usu�rio pertence a um determinado perfil
	// @note		A propriedade activeRole e os m�todos isInRole, getActiveRole 
	//				e setActiveRole podem ser utilizados para implementar regras de RBAC 
	//				(role based authentication control) nas aplica��es	
	// @param		role string		Nome do perfil
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isInRole($role) {
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getUsername
	// @desc		Retorna o nome do usu�rio
	// @access		public
	// @return		string Nome do usu�rio
	// @note		Se o usu�rio n�o est� autenticado, este m�todo retorna NULL
	//!-----------------------------------------------------------------
	function getUsername() {
		return $this->username;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::setUsername
	// @desc		Define/altera o nome do usu�rio na sess�o
	// @access		public
	// @param		username string		Nome para o usu�rio
	// @return		void
	//!-----------------------------------------------------------------
	function setUsername($username) {
		$this->username = $username;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getActiveRole
	// @desc		Retorna o(s) perfil(is) ativo(s) para o usu�rio
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
	// @desc		Define o(s) perfil(s) ativo(s) para o usu�rio
	// @param		role mixed		Perfil ou conjunto de perfis
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setActiveRole($role) {
		$this->activeRole = $role;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getLoginTime
	// @desc		Retorna o timestamp de cria��o da sess�o
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
	// @desc		Retorna o timestamp do �ltimo acesso do usu�rio
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
	// @desc		Retorna o n�mero de segundos desde a cria��o da sess�o do usu�rio
	// @access		public
	// @return		int N�mero de segundos
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
	// @desc		Retorna o tempo decorrido desde a �ltima requisi��o do usu�rio
	// @access		public
	// @return		int Tempo decorrido, em segundos
	// @note		O valor de retorno deste m�todo � utilizado em testes de tempo m�ximo de inatividade
	//!-----------------------------------------------------------------
	function getLastIdleTime() {
		return (System::getMicrotime() - $this->lastAccess);
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getPropertyValue
	// @desc		Sobrescreve a implementa��o do m�todo getPropertyValue da
	//				classe SessionObject para que consultas por propriedades
	//				n�o existentes retornem NULL
	// @access		public
	// @param		name string		Nome da propriedade
	// @return		mixed Valor da propriedade ou NULL se ela n�o existir
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
	// @desc		Constr�i a representa��o string do usu�rio
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