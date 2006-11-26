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
// $Header: /www/cvsroot/php2go/core/validation/MaxValidator.class.php,v 1.10 2006/04/05 23:43:18 mpont Exp $
// $Date: 2006/04/05 23:43:18 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		MaxValidator
// @desc		Classe que valida valores em relação a um limite máximo
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.10 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = 10;
//				if (Validator::validate('php2go.validation.MaxValidator', $value, array('max'=>5))) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class MaxValidator extends Validator
{
	var $max;			// @var max int	Limite máximo
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	MaxValidator::MaxValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	// @note		Conjunto de parâmetros:
	//				max => Limite máximo
	//!-----------------------------------------------------------------
	function MaxValidator($params = NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['max']))
				$this->max = $params['max'];
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];				
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	MaxValidator::execute
	// @desc		Executa a validação de um valor em relação ao limite máximo
	// @access		public
	// @param		value mixed	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		$value = TypeUtils::parseFloat($value);
		if (!isset($this->max)) {
			$result = TRUE;
		} else {
			$result = ($value <= $this->max);
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_VALUE_LOET', array($this->fieldLabel, $this->max));
		return $result;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	MaxValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>