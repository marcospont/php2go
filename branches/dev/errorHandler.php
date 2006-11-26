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
// $Header: /www/cvsroot/php2go/errorHandler.php,v 1.19 2006/03/15 23:12:15 mpont Exp $
// $Date: 2006/03/15 23:12:15 $
// $Revision: 1.19 $

//!------------------------------------------------------------------
// @function	php2GoErrorHandler
// @desc		Substitui o tratador de erros tradicional do PHP
// @param		errorCode int			C�digo do erro
// @param		errorMessage string		Mensagem de erro
// @param		fileName string			Nome do arquivo
// @param		lineNumber string		Linha do arquivo
// @param		vars array				Escopo da fun��o onde o erro ocorreu
// @return		void
// @note		Se a fun��o PHP2Go::raiseError for utilizada, � poss�vel 
//				sobrescrever os valores de filename e linenum pelos valores 
//				corretos com rela��o ao ponto onde a fun��o � executada
//!------------------------------------------------------------------
function php2GoErrorHandler ($errorCode, $errorMessage, $fileName, $lineNumber, $vars) {	
	if ($errorCode != E_STRICT && error_reporting() != 0) {
		$ThrownError = Registry::get('PHP2Go_error');
		if ($ThrownError) {
			Registry::remove('PHP2Go_error');
			$ThrownError->handle();
		} else {
			$Error = new PHP2GoError();
			if (!$Error->isIgnoreError($errorMessage)) {
				$Error->setType($errorCode);			
				$Error->setMessage($errorMessage);	
				$Error->setFile($fileName);
				$Error->setLine($lineNumber);
				$Error->handle();
			}
		}
	}
}

//!------------------------------------------------------------------
// @function	php2GoAssertionHandler
// @desc		Tratador padr�o para erros na avalia��o de express�es
// @param		fileName string			Arquivo onde a asser��o foi feita
// @param		lineNumber int			Linha do arquivo
// @param		expressionCode string	C�digo da express�o
// @return		void
//!------------------------------------------------------------------
function php2GoAssertionHandler ($fileName, $lineNumber, $expressionCode) {
	import('php2go.base.Php2GoError');
	$userFile = Registry::get('PHP2Go_assertion_file');
	if (!TypeUtils::isNull($userFile)) {
		$fileName = $userFile;
		Registry::remove('PHP2Go_assertion_file');
	}
	$userLine = Registry::get('PHP2Go_assertion_line');
	if (!TypeUtils::isNull($userLine)) {
		$lineNumber = $userLine;
		Registry::remove('PHP2Go_assertion_line');
	}
	$Error = new PHP2GoError();
	$Error->setType(E_USER_WARNING);
	$Error->setMessage(PHP2Go::getLangVal('ERR_ASSERTION_MESSAGE'), $expressionCode);
	$Error->setFile($fileName);
	$Error->setLine($lineNumber);
	$Error->handle();
}

//!------------------------------------------------------------------
// @function	dbErrorHandler
// @desc		Tratador de erros para a bilioteca ADODB
// @param		dbms string				Tipo de banco de dados utilizado
// @param		function string			Fun��o do banco que foi executada e gerou erro
// @param		errorCode int			N�mero do erro de banco
// @param		errorMessage string		Mensagem de erro do banco
// @param		p1 mixed				Par�metro 1 do comando de banco de dados
// @param		p2 mixed				Par�metro 2 do comando de banco de dados
// @param		connection object		Conex�o com o banco ativa no momento do erro
// @return		void
//!------------------------------------------------------------------
function dbErrorHandler($dbms, $function, $errorCode, $errorMessage, $p1=FALSE, $p2=FALSE, &$connection) {
	if (error_reporting() != 0) {
		switch ($function) {
			case 'EXECUTE': 
				$extra = "$function (" . (TypeUtils::isArray($p1) ? exportVariable($p1) : "\"" . $p1 . "\"") . ($p2 ? "," . (TypeUtils::isArray($p2) ? exportVariable($p2) : "\"" . $p2 . "\"") . ")|" : ")"); 
				break;
			case 'PCONNECT':
			case 'CONNECT' : 
				$extra = "$function ($p1, '****', '****', $p2)";
				break;
			default: 
				$extra = "$function (" . (TypeUtils::isArray($p1) ? exportVariable($p1) : "\"{$p1}\"") . ($p2 ? "," . (TypeUtils::isArray($p2) ? exportVariable($p2) : "\"{$p2}\"") . ")" : ")");
				break;
		}
		$Error = new PHP2GoError();
		$Error->setType(E_DATABASE_ERROR);
		$Error->setMessage("$dbms [$errorCode] : $errorMessage", $extra);
		$Error->handle();
	}
}

//!------------------------------------------------------------------
// @function	setupError
// @desc		Imprime uma mensagem de erro nas configura��es
//				principais do sistema
// @param		msg string	Mensagem de Erro
// @return		void
//!------------------------------------------------------------------
function setupError($msg) {
	die ("
		<br>
		<table border='1' bordercolor='#ff0000' cellpadding='0' cellspacing='0' width='100%'>
			<tr><td>
				<table border='0' cellpadding='6' cellspacing='0' width='100%'>
					<tr bgcolor='#EEEEEE'><td valign='top' style='font-family:Sans-Serif;font-size:12px'><b><font color='#ff0000'>SETUP ERROR</font></b><br>".$msg."<br></td></tr>
				</table>
			</td></tr>
		</table>
	");
}
?>