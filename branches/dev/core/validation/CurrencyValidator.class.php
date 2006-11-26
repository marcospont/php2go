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
// $Header: /www/cvsroot/php2go/core/validation/CurrencyValidator.class.php,v 1.6 2006/06/15 00:29:12 mpont Exp $
// $Date: 2006/06/15 00:29:12 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		CurrencyValidator
// @desc		Classe que valida valores de moeda (decimal com separadores), com ou sem sinal
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.6 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = '10.000,00';
//				if (Validator::validate('php2go.validation.CurrencyValidator', $value)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class CurrencyValidator extends Validator
{
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	CurrencyValidator::CurrencyValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	//!-----------------------------------------------------------------
	function CurrencyValidator($params = NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];			
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	CurrencyValidator::execute
	// @desc		Executa a validação de moeda para um valor
	// @access		public
	// @param		&value mixed	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute(&$value) {
		if (strlen($value) == 1) {
			$result = FALSE;
		} else {
			if (strlen($value) == 2) {
				if ($value[0] == '-')
					$value = "-0,0" . $value[1];
				else
					$value = "0," . $value;
			}
			if (strlen($value) == 3 && $value[0] == '-')
				$value = "-0," . substr($value, 1);
			$strRegExp = '';
			$size = strlen($value);
			if ($value[0] == '-')
				$size--;
			$start = ($size - 3) % 4;
			$rest = $size - $start - 3;
			if ($size == 0) {
				$result = TRUE;
			} else {
				$strRegExp = "/^\-?[0-9]{" . $start . "}";
				for ($i=0; $i<$rest; $i=$i+4)
					$strRegExp .= "\.[0-9]{3}";
				$strRegExp .= "\,[0-9]{2}$/";
				$result = TypeUtils::toBoolean(preg_match($strRegExp, $value));
			}
		}
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');			
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['CURRENCY']));
		}
		return $result;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	CurrencyValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>