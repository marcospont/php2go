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
// $Header: /www/cvsroot/php2go/core/validation/FloatValidator.class.php,v 1.7 2006/06/15 00:29:12 mpont Exp $
// $Date: 2006/06/15 00:29:12 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FloatValidator
// @desc		Classe que valida valores decimais com ou sem sinal,
//				com validação de precisão opcional
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.7 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = 10.1;
//				if (Validator::validate('php2go.validation.FloatValidator', $value)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class FloatValidator extends Validator
{
	var $limiters;		// @var limiters array			Limites para a parte inteira e decimal do número
	var $decimalPoint;	// @var decimalPoint string		Ponto decimal a ser utilizado na validação. Utiliza o valor da tabela de localização, por padrão
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	FloatValidator::FloatValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	//				Conjunto de parâmetros:
	//				limiters => Vetor com o tamanho da parte inteira e o tamanho da parte decimal
	//!-----------------------------------------------------------------
	function FloatValidator($params = NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['limiters']) && TypeUtils::isArray($params['limiters']) && sizeof($params['limiters']) == 2) {
				$this->limiters = $params['limiters'];
				$this->limiters[0] = max(1, $this->limiters[0]);
				$this->limiters[1] = max(1, $this->limiters[1]);
			}
			if (isset($params['decimalPoint'])) {
				$this->decimalPoint = $params['decimalPoint'];
			} else {
				$locale = localeconv();
				$this->decimalPoint = $locale['decimal_point'];
			}
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];			
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FloatValidator::execute
	// @desc		Executa a validação de números decimais para um valor
	// @access		public
	// @param		&value mixed	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute(&$value) {
		if ($value[0] == $this->decimalPoint)
			$value = '0' . $value;
		if ($value[0] == '-' && $value[1] == $this->decimalPoint)
			$value = '-0' . substr($value, 1);
		if (isset($this->limiters)) {
			// completa o valor com as casas decimais necessárias
			// para a precisão numérica do campo
			$p = strpos($value, $this->decimalPoint); 
			if ($p === FALSE) {
				$value .= $this->decimalPoint . str_repeat('0', $this->limiters[1]);
			} else {
				$p++;
				$ss = substr($value, $p);
				if (strlen($ss) < $this->limiters[1])
					$value .= str_repeat('0', $this->limiters[1]-strlen($ss));
			}
			// valida a precisão numérica
			$result = preg_match("/^\-?[0-9]{1," . $this->limiters[0] . "}(" . preg_quote($this->decimalPoint, '/') . "[0-9]{1," . $this->limiters[1] . "})?$/", $value);
		} else {
			// validação simples de número com ponto flutuante, sem precisão definida
			$result = preg_match("/^\-?[0-9]+(" . preg_quote($this->decimalPoint, '/') . "[0-9]+)?$/", $value);
		}
		if ($result === FALSE && isset($this->fieldLabel)) {			
			if (isset($this->limiters)) {
				$this->errorMessage = str_replace("\\n", "<br>", PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_FLOAT', array($this->fieldLabel, $this->limiters[0], $this->limiters[1])));
			} else {
				$maskLabels = PHP2Go::getLangVal('FORM_MASKS');
				$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['FLOAT']));
			}
		}
		return $result;
	}	
	
	//!-----------------------------------------------------------------
	// @function	FloatValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>