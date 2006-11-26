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
// Porta padr�o de conex�o com o servidor IMAP
define('AUTH_IMAP_PORT', 143);

//!-----------------------------------------------------------------
// @class		AuthIMAP
// @desc		Classe de autentica��o de usu�rios baseada em uma consulta a um
//				servidor pela validade do usu�rio e senha fornecidos
// @package		php2go.auth
// @extends		Auth
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class AuthIMAP extends Auth
{
	var $host = 'localhost';		// @var host string		"localhost" Host do servidor IMAP
	var $port = AUTH_IMAP_PORT;		// @var port int		"AUTH_IMAP_PORT" Porta para conex�o
	var $flags = '';				// @var flags string	"" Flags de configura��o da conex�o
	
	//!-----------------------------------------------------------------
	// @function	AuthIMAP::AuthIMAP
	// @desc		Construtor da classe
	// @param		sessionName string	"NULL" Nome da vari�vel de sess�o
	// @access		public
	//!-----------------------------------------------------------------	
	function AuthIMAP($sessionName=NULL) {
		parent::Auth($sessionName);
		if (!System::loadExtension('imap'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'imap'), E_USER_ERROR, __FILE__, __LINE__);
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthIMAP::setupConnection
	// @desc		Configura dados da conex�o com o servidor IMAP: IP ou endere�o
	//				do host, porta e flags de configura��o
	// @param		host string		IP ou endere�o do servidor IMAP
	// @param		port int		"NULL" Porta a ser utilizada
	// @param		flags string	"NULL" Flags para a conex�o
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
	// @desc		M�todo principal de autentica��o. Verifica a exist�ncia do usu�rio
	//				no servidor IMAP
	// @note		Este m�todo � executado em Auth::login
	// @note		Problemas de conex�o com o servidor (timeout, host inv�lido ou inalcan��vel) 
	//				ser�o transparentes ao usu�rio e resultar�o em falha de autentica��o
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