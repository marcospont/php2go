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
// $Header: /www/cvsroot/php2go/core/validation/WordValidator.class.php,v 1.1 2006/06/15 01:27:54 mpont Exp $
// $Date: 2006/06/15 01:27:54 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		WordValidator
// @desc		Classe que valida valores "word boundary",
//				aceitando caracteres 0-9, a-z, A-Z, _, . e -
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.1 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = 'my_word';
//				if (Validator::validate('php2go.validation.WordValidator', $value)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class WordValidator extends Validator
{
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro

	//!-----------------------------------------------------------------
	// @function	WordValidator::WordValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	//!-----------------------------------------------------------------
	function WordValidator($params = NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];
		}
	}

	//!-----------------------------------------------------------------
	// @function	WordValidator::execute
	// @desc		Verifica se um valor é um nome de usuário válido
	// @access		public
	// @param		value mixed	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		$result = TypeUtils::toBoolean(preg_match("/^\w+((-\w+)|(\.\w+))*$/", $value));
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['WORD']));
		}
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	WordValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}
}
?>