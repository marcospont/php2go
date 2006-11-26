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
// $Header: /www/cvsroot/php2go/core/auth/Authorizer.class.php,v 1.5 2006/05/07 15:19:04 mpont Exp $
// $Date: 2006/05/07 15:19:04 $

//------------------------------------------------------------------
import('php2go.auth.User');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		Authorizer
// @desc		Esta classe consiste em um conjunto de métodos abstratos que definem
//				uma estrutura de controle de autorização sobre as aplicações. Utilizando
//				a entrada de configuração AUTH.AUTHORIZER_PATH, deve se definir uma classe
//				de autorização, extendendo a classe Authorizer, onde estes e outros métodos
//				destinados a aplicar controle de acesso nas aplicações podem ser implementados
// @package		php2go.auth
// @extends		PHP2Go
// @uses		User
// @version		$Revision: 1.5 $
// @author		Marcos Pont
//!-----------------------------------------------------------------
class Authorizer extends PHP2Go
{
	var $User = NULL;	// @var User User object	Instância da classe User - contém o usuário ativo na aplicação

	//!-----------------------------------------------------------------
	// @function	Authorizer::Authorizer
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Authorizer() {
		parent::PHP2Go();
		$this->User =& User::getInstance();
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::&getInstance
	// @desc		Retorna uma instância única da classe de autorização,
	//				ou controle de acesso, da aplicação
	// @note		** SEMPRE ** utilize o método getInstance para utilizar a classe Authorizer.
	//				Dentro dele, existe uma rotina que identifica que uma classe de autorização especializada
	//				foi definida nas configurações da aplicação, e devolve uma instância desta classe ao invés
	//				da classe base de autorização
	// @return		Authorizer object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			// busca o caminho da classe de autorização definida na configuração
			if ($authorizerClassPath = PHP2Go::getConfigVal('AUTH.AUTHORIZER_PATH', FALSE, FALSE)) {
				if ($authorizerClass = classForPath($authorizerClassPath)) {
					$instance = new $authorizerClass();
					if (!TypeUtils::isInstanceOf($instance, 'Authorizer'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHORIZER', $authorizerClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHORIZER_PATH', $authorizerClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			}
			// usa o container padrão (php2go.auth.User)
			else {
				$instance = new Authorizer();
			}
		}
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeUri
	// @desc		Método abstrato de verificação de acesso a uma URI (Uniform Resource Identifier)
	// @param		uri string	URI a ser verificada
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeUri($uri) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeAction
	// @desc		Método abstrato de verificação de acesso a uma ação, a partir
	//				de seu nome, ID ou código
	// @note		Pode ser utilizado por aplicações baseadas em ações armazenadas
	//				em arquivos, bancos de dados ou outras fontes externas
	// @param		action mixed	Representa a ação a ser verificada
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeAction($action) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeObjectAction
	// @desc		Este método pode ser utilizado para implementar um teste de autorização
	//				sobre uma ação (ou uma operação) em um objeto
	// @param		object mixed	Objeto
	// @param		action mixed	Identificador da ação
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeObjectAction($object, $action) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeModule
	// @desc		Método de autorização que pode ser utilizado para verificar
	//				o acesso a um módulo da aplicação
	// @param		module mixed	Nome ou conjunto de informações sobre o módulo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeModule($module) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeModuleAction
	// @desc		Método abstrato para validação de acesso a uma determinada
	//				ação dentro de um módulo da aplicação. Ex: "produtos" + "editar"
	// @param		module mixed	Nome ou informações sobre o módulo
	// @param		action mixed	Nome ou informações sobre a ação
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeModuleAction($module, $action) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeFormSection
	// @desc		Para as seções condicionais cuja função de avaliação de visibilidade
	//				não for definida, este método será executado. Ou seja: com este
	//				método é possível centralizar todas as consultas por acesso a seções
	//				condicionais de formulários
	// @param		Section FormSection object	Seção de formulário
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeFormSection($Section) {
		return TRUE;
	}
}
?>