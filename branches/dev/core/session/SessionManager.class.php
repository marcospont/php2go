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
// $Header: /www/cvsroot/php2go/core/session/SessionManager.class.php,v 1.19 2006/11/25 11:58:50 mpont Exp $
// $Date: 2006/11/25 11:58:50 $

//!-----------------------------------------------------------------
// @class 		SessionManager
// @desc 		Esta classe � respons�vel por manipular vari�veis simples
// 				de sess�o, que preferencialmente possuam valores escalares
// 				ou do tipo array. Gerencia vari�veis de sess�o permitindo
// 				cri�-las, atribuir e recuperar valores
// @package		php2go.session
// @extends 	PHP2Go
// @author 		Marcos Pont
// @version		$Revision: 1.19 $
//!-----------------------------------------------------------------
class SessionManager extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	SessionManager::SessionManager
	// @desc		Construtor da classe
	// @access 		public
	//!-----------------------------------------------------------------
	function SessionManager() {
		parent::PHP2Go();
	}

	//!-----------------------------------------------------------------
	// @function	SessionManager::&getInstance
	// @desc		Retorna una inst�ncia �nica (singleton) da classe SessionManager
	// @return		SessionManager object
	// @access		public	
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new SessionManager();
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	SessionManager::getSessionId
	// @desc		Busca o ID da sess�o atual
	// @return		string ID da sess�o
	// @access		public	
	//!-----------------------------------------------------------------
	function getSessionId() {
		return @session_id();
	}

	//!-----------------------------------------------------------------
	// @function	SessionManager::getSessionName
	// @desc		Obt�m o nome da sess�o atual
	// @return		string Nome da sess�o
	// @access		public	
	//!-----------------------------------------------------------------
	function getSessionName() {
		return @session_name();
	}

	//!-----------------------------------------------------------------
	// @function	SessionManager::getSessionSavePath
	// @desc		Obt�m o caminho onde os dados da sess�o s�o gravados no servidor
	// @return		string Caminho de armazenamento da sess�o
	// @access		public	
	//!-----------------------------------------------------------------
	function getSessionSavePath() {
		return @session_save_path();
	}

	//!-----------------------------------------------------------------
	// @function	SessionManager::getValue
	// @desc 		Busca o valor armazenado para uma vari�vel de sess�o
	// @param		name string	Nome da vari�vel solicitada
	// @return 		mixed Valor da vari�vel de sess�o ou NULL caso ela n�o
	// 				possua valor setado ou armazenado
	// @access 		public	
	//!-----------------------------------------------------------------
	function getValue($name) {
		if ($this->isRegistered($name))
			return $_SESSION[$name];
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	SessionManager::getObjectProperty
	// @desc		M�todo est�tico para a busca de valores de propriedades
	//				armazenados em objetos de sess�o
	// @param		qualifiedName string	String contendo nome do objeto de sess�o e nome da propriedade. Ex: sessao:variavel
	// @return		mixed Valor da propriedade, se existente, ou NULL
	// @access		public	
	// @static
	//!-----------------------------------------------------------------
	function getObjectProperty($qualifiedName) {
		if (ereg("([^\:]+)\:(.+)", $qualifiedName, $matches)) {
			import('php2go.session.SessionObject');
			$Session = new SessionObject($matches[1]);
			if ($Session->isRegistered() && $Session->hasProperty($matches[2]))
				return $Session->getPropertyValue($matches[2]);
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	SessionManager::setValue
	// @desc 		Atribui um valor a uma vari�vel de sess�o
	// @param		name string	Nome da vari�vel
	// @param 		value mixed	Valor a ser atribu�do � vari�vel
	// @return 		bool Retorna TRUE se o valor for setado ou FALSE se seu tipo
	// 				n�o for array/escalar ou se o m�todo for executado
	// 				a partir da classe extendida SessionObject
	// @note		Para atribuir objetos ao valor de uma sess�o, utilize
	//				a classe SessionObject
	// @access 		public
	//!-----------------------------------------------------------------
	function setValue($name, $value) {
		if (!$this->isA('SessionManager') || (!is_scalar($value) && !is_array($value)))
			return FALSE;
		$_SESSION[$name] = $value;
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function 	SessionManager::isRegistered
	// @desc 		Verifica se uma determinada vari�vel est� registrada na sess�o
	// @param		var string	Nome da vari�vel
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isRegistered($var) {
		return (array_key_exists($var, $_SESSION));
	}

	//!-----------------------------------------------------------------
	// @function 	SessionManager::register
	// @desc 		Registra uma vari�vel na sess�o atual com um determinado valor
	// @note		Para armazenar objetos na sess�o, utilize a classe SessionObject
	// @param		name string	Nome da vari�vel
	// @param		value mixed	Valor para a vari�vel
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function register($name, $value) {
		$_SESSION[$name] = $value;
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function 	SessionManager::unregister
	// @desc 		Apaga uma vari�vel da sess�o atual
	// @param		name string	Nome da vari�vel de sess�o
	// @return 		bool Retorna TRUE se a vari�vel estava registrada (sucesso) ou
	// 				FALSE se ela n�o estava (falha)
	// @access 		public
	//!-----------------------------------------------------------------
	function unregister($name) {
		if ($this->isRegistered($name)) {
			unset($_SESSION[$name]);
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	SessionManager::delete
	// @desc		Este m�todo � um alias para SessionManager::unregister
	// @param		name string	Nome da vari�vel de sess�o
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function delete($name) {
		$this->unregister($name);
	}

	//!-----------------------------------------------------------------
	// @function	SessionManager::destroy
	// @desc		Destr�i todas as vari�veis de sess�o registradas
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function destroy() {		
		if (isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time()-86400, '/');
		session_unset();
		$_SESSION = array();
		@session_destroy();
	}
}
?>