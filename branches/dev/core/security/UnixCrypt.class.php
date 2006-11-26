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
// $Header: /www/cvsroot/php2go/core/security/UnixCrypt.class.php,v 1.2 2006/02/28 21:56:00 mpont Exp $
// $Date: 2006/02/28 21:56:00 $

//------------------------------------------------------------------
import('php2go.file.FileManager');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		UnixCrypt
// @desc		Esta classe est�tica funciona como uma camada simples de abstra��o
//				sobre a fun��o crypt da API do PHP, que � uma fun��o de criptografia
//				one-way que utiliza o algoritmo de encripta��o Unix Standard DES-based
// @package		php2go.security
// @extends		PHP2Go
// @uses		FileManager
// @author		Marcos Pont
// @version		$Revision: 1.2 $
// @static
//!-----------------------------------------------------------------
class UnixCrypt extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	UnixCrypt::encrypt
	// @desc		Retorna uma string criptografada para um determinado dado
	// @access		public
	// @param		data string		String a ser criptografada
	// @param		salt string		"NULL" Tipo de codifica��o a ser utilizado
	// @note		Nota especial sobre o tipo de codifica��o:
	//				- Quando o primeiro caractere for '_', o formato DES extendido ser� utilizado
	//				- Quando o in�cio da string estiver no padr�o '$d�gito$', ser� utilizado um formato de criptografia regular
	//				- Se n�o for um dos dois casos acima, o tipo de codifica��o tradicional ser� utilizado (usando a pr�pria string como base para a criptografia)
	// @return		string Valor criptografado	
	// @static	
	//!-----------------------------------------------------------------
	function encrypt($data, $salt=NULL) {
		return crypt($data, $salt);
	}
	
	//!-----------------------------------------------------------------
	// @function	UnixCrypt::verify
	// @desc		Verifica se um determinado valor fornecido � igual a um
	//				valor j� criptografado
	// @access		public
	// @param		encrypted string	String criptografada
	// @param		data string			String n�o criptografada para a compara��o
	// @param		salt string			"NULL" Tipo de codifica��o a ser utilizado
	// @note		Pode ser utilizado para a verifica��o de senhas no padr�o Unix
	// @return		bool	
	// @static
	//!-----------------------------------------------------------------
	function verify($encrypted, $data, $salt=NULL) {
		return (UnixCrypt::encrypt($data, $salt) == $encrypted);
	}
}
?>