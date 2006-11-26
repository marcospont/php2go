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
// $Header: /www/cvsroot/php2go/core/validation/MinValidator.class.php,v 1.10 2006/04/05 23:43:18 mpont Exp $
// $Date: 2006/04/05 23:43:18 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		MinValidator
// @desc		Classe que valida valores em relação a um limite mínimo
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.10 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = 10;
//				if (Validator::validate('php2go.validation.MinValidator', $value, array('min'=>5))) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class MinValidator extends Validator
{
	var $min;			// @var min int					Limite mínimo
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	MinValidator::MinValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	// @note		Conjunto de parâmetros:
	//				min => Limite mínimo	
	//!-----------------------------------------------------------------
	function MinValidator($params = NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['min']))
				$this->min = $params['min'];
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];			
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	MinValidator::execute
	// @desc		Executa a validação de um valor em relação ao limite mínimo
	// @access		public
	// @param		value mixed	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {		
		if (!isset($this->min)) {
			$result = TRUE;
		} else {
			$value = TypeUtils::parseFloat($value);
			$result = ($value >= $this->min);
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_VALUE_GOET', array($this->fieldLabel, $this->min));
		return $result;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	MinValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>