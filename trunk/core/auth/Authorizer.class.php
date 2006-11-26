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
// @desc		Esta classe consiste em um conjunto de m�todos abstratos que definem
//				uma estrutura de controle de autoriza��o sobre as aplica��es. Utilizando
//				a entrada de configura��o AUTH.AUTHORIZER_PATH, deve se definir uma classe
//				de autoriza��o, extendendo a classe Authorizer, onde estes e outros m�todos
//				destinados a aplicar controle de acesso nas aplica��es podem ser implementados
// @package		php2go.auth
// @extends		PHP2Go
// @uses		User
// @version		$Revision: 1.5 $
// @author		Marcos Pont
//!-----------------------------------------------------------------
class Authorizer extends PHP2Go
{
	var $User = NULL;	// @var User User object	Inst�ncia da classe User - cont�m o usu�rio ativo na aplica��o

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
	// @desc		Retorna uma inst�ncia �nica da classe de autoriza��o,
	//				ou controle de acesso, da aplica��o
	// @note		** SEMPRE ** utilize o m�todo getInstance para utilizar a classe Authorizer.
	//				Dentro dele, existe uma rotina que identifica que uma classe de autoriza��o especializada
	//				foi definida nas configura��es da aplica��o, e devolve uma inst�ncia desta classe ao inv�s
	//				da classe base de autoriza��o
	// @return		Authorizer object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			// busca o caminho da classe de autoriza��o definida na configura��o
			if ($authorizerClassPath = PHP2Go::getConfigVal('AUTH.AUTHORIZER_PATH', FALSE, FALSE)) {
				if ($authorizerClass = classForPath($authorizerClassPath)) {
					$instance = new $authorizerClass();
					if (!TypeUtils::isInstanceOf($instance, 'Authorizer'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHORIZER', $authorizerClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHORIZER_PATH', $authorizerClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			}
			// usa o container padr�o (php2go.auth.User)
			else {
				$instance = new Authorizer();
			}
		}
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeUri
	// @desc		M�todo abstrato de verifica��o de acesso a uma URI (Uniform Resource Identifier)
	// @param		uri string	URI a ser verificada
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeUri($uri) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeAction
	// @desc		M�todo abstrato de verifica��o de acesso a uma a��o, a partir
	//				de seu nome, ID ou c�digo
	// @note		Pode ser utilizado por aplica��es baseadas em a��es armazenadas
	//				em arquivos, bancos de dados ou outras fontes externas
	// @param		action mixed	Representa a a��o a ser verificada
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeAction($action) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeObjectAction
	// @desc		Este m�todo pode ser utilizado para implementar um teste de autoriza��o
	//				sobre uma a��o (ou uma opera��o) em um objeto
	// @param		object mixed	Objeto
	// @param		action mixed	Identificador da a��o
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeObjectAction($object, $action) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeModule
	// @desc		M�todo de autoriza��o que pode ser utilizado para verificar
	//				o acesso a um m�dulo da aplica��o
	// @param		module mixed	Nome ou conjunto de informa��es sobre o m�dulo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeModule($module) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeModuleAction
	// @desc		M�todo abstrato para valida��o de acesso a uma determinada
	//				a��o dentro de um m�dulo da aplica��o. Ex: "produtos" + "editar"
	// @param		module mixed	Nome ou informa��es sobre o m�dulo
	// @param		action mixed	Nome ou informa��es sobre a a��o
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeModuleAction($module, $action) {
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Authorizer::authorizeFormSection
	// @desc		Para as se��es condicionais cuja fun��o de avalia��o de visibilidade
	//				n�o for definida, este m�todo ser� executado. Ou seja: com este
	//				m�todo � poss�vel centralizar todas as consultas por acesso a se��es
	//				condicionais de formul�rios
	// @param		Section FormSection object	Se��o de formul�rio
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authorizeFormSection($Section) {
		return TRUE;
	}
}
?>