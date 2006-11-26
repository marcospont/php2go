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
// $Header: /www/cvsroot/php2go/core/validation/IntegerValidator.class.php,v 1.5 2006/06/15 00:29:12 mpont Exp $
// $Date: 2006/06/15 00:29:12 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		IntegerValidator
// @desc		Classe que valida valores inteiros com ou sem sinal
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.5 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = 10;
//				if (Validator::validate('php2go.validation.IntegerValidator', $value)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class IntegerValidator extends Validator
{
	var $fieldLabel;		// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $unsigned = FALSE;	// @var unsigned bool			"FALSE" Se TRUE, valida apenas números inteiros positivos
	var $errorMessage;		// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	IntegerValidator::IntegerValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	//!-----------------------------------------------------------------
	function IntegerValidator($params=NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];
			if (isset($params['unsigned']))
				$this->unsigned = TypeUtils::toBoolean($params['unsigned']);
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	IntegerValidator::execute
	// @desc		Executa a validação de números inteiros para um valor
	// @access		public
	// @param		value mixed	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		$result = TypeUtils::isInteger($value);
		if ($this->unsigned && TypeUtils::parseInteger($value) < 0)
			$result = FALSE;
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');			
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, ($this->unsigned ? $maskLabels['DIGIT'] : $maskLabels['INTEGER'])));
		}
		return $result;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	IntegerValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>