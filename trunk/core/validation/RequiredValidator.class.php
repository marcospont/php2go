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
// $Header: /www/cvsroot/php2go/core/validation/RequiredValidator.class.php,v 1.6 2006/05/07 15:15:40 mpont Exp $
// $Date: 2006/05/07 15:15:40 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		RequiredValidator
// @desc		Classe que valida se um valor é não vazio
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.6 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = NULL;
//				if (Validator::validate('php2go.validation.RequiredValidator', $value)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				} else {
//				&nbsp;&nbsp;&nbsp;print 'value is not present';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class RequiredValidator extends Validator
{
	var $fieldClass;	// @var fieldClass string		Nome da classe do campo a ser validado
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro

	//!-----------------------------------------------------------------
	// @function	RequiredValidator::RequiredValidator
	// @desc		Construtor da classe
	// @param		params array	"NULL" Parâmetros para o validador
	// @access		public
	//!-----------------------------------------------------------------
	function RequiredValidator($params=NULL) {
		parent::Validator();
		if (is_array($params)) {
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];
			if (isset($params['fieldClass']))
				$this->fieldClass = $params['fieldClass'];
		}
	}

	//!-----------------------------------------------------------------
	// @function	RequiredValidator::execute
	// @desc		Executa a validação de obrigatoriedade (não vazio) para um valor
	// @param		value mixed	Valor a ser validado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		if (is_array($value)) {
			$result = (!empty($value));
		} elseif ($this->fieldClass == 'checkfield' && $value == 'F') {
			$result = FALSE;
		} else {
			$value = (string)$value;
			$result = (trim($value) != '');
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_REQUIRED', $this->fieldLabel);
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	RequiredValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @return		string Mensagem de erro
	// @access		public
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}
}
?>