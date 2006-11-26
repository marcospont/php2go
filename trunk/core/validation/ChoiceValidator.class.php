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
// $Header: /www/cvsroot/php2go/core/validation/ChoiceValidator.class.php,v 1.8 2006/04/05 23:43:18 mpont Exp $
// $Date: 2006/04/05 23:43:18 $

//!-----------------------------------------------------------------
// @class		ChoiceValidator
// @desc		Classe que valida a escolha de um valor em uma lista de valores
// @package		php2go.validation
// @extends		Validator
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.8 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = 1;
//				$options = array(1, 2, 3);
//				if (Validator::validate('php2go.validation.ChoiceValidator', $value, array('options'=>$options))) {
//				&nbsp;&nbsp;&nbsp;print 'choice ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class ChoiceValidator extends Validator
{
	var $options;	// @var options	array		Vetor de opções de escolha
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	ChoiceValidator::ChoiceValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	// @note		Conjunto de parâmetros:
	//				options => Vetor de opções válidas
	//!-----------------------------------------------------------------
	function ChoiceValidator($params = NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['options']) && TypeUtils::isArray($params['options']) && !TypeUtils::isHashArray($params['options'])) {
				$this->options = $params['options'];
			}
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];			
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	ChoiceValidator::execute
	// @desc		Executa a validação de um valor com relação a uma lista de opções disponíveis
	// @access		public
	// @param		value mixed		Valor a ser validado
	// @return		bool	
	//!-----------------------------------------------------------------
	function execute($value) {
		if (!isset($this->options))
			return FALSE;
		$result = (in_array($value, $this->options));
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2GO::getLangVal('ERR_FORM_FIELD_CHOICE', array($this->fieldLabel, implode(',', $this->options)));
		return $result;		
	}
	
	//!-----------------------------------------------------------------
	// @function	ChoiceValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>