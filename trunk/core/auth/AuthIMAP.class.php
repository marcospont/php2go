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
// $Header: /www/cvsroot/php2go/core/auth/AuthIMAP.class.php,v 1.4 2006/07/12 08:02:20 mpont Exp $
// $Date: 2006/07/12 08:02:20 $

//------------------------------------------------------------------
import('php2go.auth.Auth');
//------------------------------------------------------------------

// @const AUTH_IMAP_PORT "143"
// Porta padrão de conexão com o servidor IMAP
define('AUTH_IMAP_PORT', 143);

//!-----------------------------------------------------------------
// @class		AuthIMAP
// @desc		Classe de autenticação de usuários baseada em uma consulta a um
//				servidor pela validade do usuário e senha fornecidos
// @package		php2go.auth
// @extends		Auth
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class AuthIMAP extends Auth
{
	var $host = 'localhost';		// @var host string		"localhost" Host do servidor IMAP
	var $port = AUTH_IMAP_PORT;		// @var port int		"AUTH_IMAP_PORT" Porta para conexão
	var $flags = '';				// @var flags string	"" Flags de configuração da conexão
	
	//!-----------------------------------------------------------------
	// @function	AuthIMAP::AuthIMAP
	// @desc		Construtor da classe
	// @param		sessionName string	"NULL" Nome da variável de sessão
	// @access		public
	//!-----------------------------------------------------------------	
	function AuthIMAP($sessionName=NULL) {
		parent::Auth($sessionName);
		if (!System::loadExtension('imap'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'imap'), E_USER_ERROR, __FILE__, __LINE__);
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthIMAP::setupConnection
	// @desc		Configura dados da conexão com o servidor IMAP: IP ou endereço
	//				do host, porta e flags de configuração
	// @param		host string		IP ou endereço do servidor IMAP
	// @param		port int		"NULL" Porta a ser utilizada
	// @param		flags string	"NULL" Flags para a conexão
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setupConnection($host, $port=NULL, $flags=NULL) {
		$this->host = $host;
		if ((int)$port > 0)
			$this->port = $port;
		$flags = ltrim(trim($flags), '/');
		if (!empty($flags))
			$this->flags = '/' . $flags;
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthIMAP::authenticate
	// @desc		Método principal de autenticação. Verifica a existência do usuário
	//				no servidor IMAP
	// @note		Este método é executado em Auth::login
	// @note		Problemas de conexão com o servidor (timeout, host inválido ou inalcançável) 
	//				serão transparentes ao usuário e resultarão em falha de autenticação
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function authenticate() {		
		$mailbox = '{' . $this->host . ':' . $this->port . $this->flags . '}';
		$conn = @imap_open($mailbox, $this->_login, $this->_password, OP_HALFOPEN);
		if (TypeUtils::isResource($conn)) {
			@imap_close($conn);
			return TRUE;
		}
		return FALSE;
	}	
}
?>