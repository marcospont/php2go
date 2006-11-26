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
// $Header: /www/cvsroot/php2go/core/validation/IPAddressValidator.class.php,v 1.12 2006/06/15 00:29:12 mpont Exp $
// $Date: 2006/06/15 00:29:12 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		IPAddressValidator
// @desc		Classe que valida endereços IP (Internet Protocol)
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.12 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = '192.168.1.1';
//				if (Validator::validate('php2go.validation.IPAddressValidator', $value)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
// @note		Esta classe de validação aplica-se apenas a endereços IPv4
//!-----------------------------------------------------------------
class IPAddressValidator extends Validator
{
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	IPAddressValidator::IPAddressValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	//!-----------------------------------------------------------------
	function IPAddressValidator($params = NULL) {	
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];			
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	IPAddressValidator::execute
	// @desc		Executa a validação de um endereço IP
	// @access		public
	// @param		value string	Endereço IP a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		$value = TypeUtils::parseString($value);
		$result = (ereg("^(0|([1-9]{1,2}\.)|(1[0-9]{1,2}\.)|(2[0-4][0-9]\.)|(25[0-5]\.)){3}(0|([1-9]{1,2})|(1[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$", $value));
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');			
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['IP']));
		}
		return $result;
		
	}	
	
	//!-----------------------------------------------------------------
	// @function	IPAddressValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>