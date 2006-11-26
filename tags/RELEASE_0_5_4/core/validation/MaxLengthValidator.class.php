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
// $Header: /www/cvsroot/php2go/core/validation/MaxLengthValidator.class.php,v 1.4 2006/04/05 23:43:18 mpont Exp $
// $Date: 2006/04/05 23:43:18 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		MaxLengthValidator
// @desc		Classe que valida o tamanho de um valor em caracteres
//				em relação a um limite máximo
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.4 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = 'abc';
//				if (Validator::validate('php2go.validation.MaxLengthValidator', $value, array('maxlength'=>5))) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class MaxLengthValidator extends Validator
{
	var $maxLength;				// @var maxLength int			Comprimento máximo do valor
	var $bypassEmpty = FALSE;	// @var bypassEmpty bool		Validar ou não valores vazios
	var $fieldLabel;			// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;			// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	MaxLengthValidator::MaxLengthValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	// @note		Conjunto de parâmetros:
	//				maxlength => Comprimento máximo para o valor
	//!-----------------------------------------------------------------
	function MaxLengthValidator($params = NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['maxlength']))
				$this->maxLength = $params['maxlength'];
			if (isset($params['bypassEmpty']))
				$this->bypassEmpty = TypeUtils::toBoolean($params['bypassEmpty']);				
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];				
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	MaxLengthValidator::execute
	// @desc		Executa a validação de tamanho do valor
	// @access		public
	// @param		value mixed	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		if (!isset($this->maxLength)) {
			$result = TRUE;
		} else {
			$str = TypeUtils::parseString($value);
			$result = ($this->bypassEmpty ? (empty($value) || strlen($str) <= $this->maxLength) : (strlen($str) <= $this->maxLength));
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_MAX_LENGTH', array($this->fieldLabel, $this->maxLength));
		return $result;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	MaxLengthValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>