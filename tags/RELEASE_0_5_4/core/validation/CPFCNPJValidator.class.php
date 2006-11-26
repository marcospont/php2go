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
// $Header: /www/cvsroot/php2go/core/validation/CPFCNPJValidator.class.php,v 1.6 2006/06/15 00:29:12 mpont Exp $
// $Date: 2006/06/15 00:29:12 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		CPFCNPJValidator
// @desc		Classe que valida números de CPF/CNPJ (código de pessoa física
//				e código de pessoa jurídica, respectivamente)
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.6 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = '39388330599';
//				if (Validator::validate('php2go.validation.CPFCNPJValidator', $value)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class CPFCNPJValidator extends Validator
{
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	CPFCNPJValidator::CPFCNPJValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	//!-----------------------------------------------------------------
	function CPFCNPJValidator($params = NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];			
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	CPFCNPJValidator::execute
	// @desc		Verifica se um valor é um CPF/CNPJ válido
	// @access		public
	// @param		value mixed	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		$value = ereg_replace("[^0-9]", "", $value);
		if (empty($value)) {
			$result = TRUE;
		} elseif (strlen($value) == 14) {
			$s1 = (
				($value[0] * 5) + ($value[1] * 4) + ($value[2] * 3) +
				($value[3] * 2) + ($value[4] * 9) + ($value[5] * 8) +
				($value[6] * 7) + ($value[7] * 6) + ($value[8] * 5) +
				($value[9] * 4) + ($value[10] * 3) + ($value[11] * 2)
			);
			$r = $s1 % 11;
			$d1 = ($r < 2 ? 0 : (11 - $r));
			$s2 = (
				($value[0] * 6) + ($value[1] * 5) + ($value[2] * 4) +
				($value[3] * 3) + ($value[4] * 2) + ($value[5] * 9) +
				($value[6] * 8) + ($value[7] * 7) + ($value[8] * 6) +
				($value[9] * 5) + ($value[10] * 4) + ($value[11] * 3) +
				($value[12] * 2)
			);
			$r = ($s2 % 11);
			$d2 = ($r < 2 ? 0 : (11 - $r));
			$result = ($value[12] == $d1 && $value[13] == $d2);
		} elseif (strlen($value) == 11) {
			$s1 = (
				($value[0] * 10) + ($value[1] * 9) + ($value[2] * 8) +
				($value[3] * 7) + ($value[4] * 6) + ($value[5] * 5) +
				($value[6] * 4) + ($value[7] * 3) + ($value[8] * 2)
			);
			$r = ($s1 % 11);
			$d1 = ($r < 2 ? 0 : (11 - $r));
			$s2 = (
				($value[0] * 11) + ($value[1] * 10) + ($value[2] * 9) +
				($value[3] * 8) + ($value[4] * 7) + ($value[5] * 6) +
				($value[6] * 5) + ($value[7] * 4) + ($value[8] * 3) +
				($value[9] * 2)
			);
			$r = ($s2 % 11);
			$d2 = ($r < 2 ? 0 : (11 - $r));
			$result = ($value[9] == $d1 && $value[10] == $d2);
		} else {
			$result = FALSE;
		}		
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');			
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['CPFCNPJ']));
		}
		return $result;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	CPFCNPJValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>