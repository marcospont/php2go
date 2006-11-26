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
// $Header: /www/cvsroot/php2go/core/auth/AuthPop3.class.php,v 1.4 2006/07/12 08:02:20 mpont Exp $
// $Date: 2006/07/12 08:02:20 $

//------------------------------------------------------------------
import('php2go.auth.Auth');
import('php2go.net.Pop3');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		AuthPop3
// @desc		Classe de autenticaчуo de usuсrios baseada em uma consulta
//				a um servidor utilizando o protocolo POP3
// @package		php2go.auth
// @extends		Auth
// @uses		Pop3
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class AuthPop3 extends Auth
{
	var $host = 'localhost';				// @var host string		"localhost" Nome ou IP do servidor POP3
	var $port = POP3_DEFAULT_PORT;			// @var port int		"POP3_DEFAULT_PORT" Porta para a conexуo
	var $timeout = POP3_DEFAULT_TIMEOUT;	// @var timeout int		"POP3_DEFAULT_TIMEOUT" Timeout para a conexуo
	var $Pop = NULL;						// @var Pop Pop3 object	"NULL" Cliente POP utilizado pela classe para realizar a conexуo
	
	//!-----------------------------------------------------------------
	// @function	AuthPop3::AuthPop3
	// @desc		Construtor da classe
	// @param		sessionName string	"NULL" Nome da variсvel de sessуo
	// @access		public
	//!-----------------------------------------------------------------
	function AuthPop3($sessionName=NULL) {
		parent::Auth($sessionName);
		$this->Pop = new Pop3();
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthPop3::setupConnection
	// @desc		Configura as propriedades da conexуo ao servidor: nome ou IP do servidor,
	//				porta e timeout da conexуo
	// @param		host string		Nome ou IP do servidor POP3
	// @param		port int		Porta a ser utilizada
	// @param		timeout int		Timeout
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setupConnection($host, $port=NULL, $timeout=NULL) {
		$this->host = $host;
		if ((int)$port > 0)
			$this->port = $port;
		if ((int)$timeout > 0)
			$this->timeout = $timeout;
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthPop3::authenticate
	// @desc		Mщtodo que executa a verificaчуo de autenticaчуo do usuсrio
	// @note		Este mщtodo щ executado em Auth::login	
	// @note		Problemas de conexуo com o servidor ou durante a comunicaчуo
	//				atravщs do protocolo PO3 nуo irуo gerar erros pelo PHP, mas retornarуo
	//				como falha de autenticaчуo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authenticate() {
		$result = FALSE;
		if (@$this->Pop->connect($this->host, $this->port, $this->timeout)) {
			$result = @$this->Pop->login($this->_login, $this->_password);
			$this->Pop->close();	
		}
		return $result;
	}	
}
?>