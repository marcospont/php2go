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
// $Header: /www/cvsroot/php2go/core/validation/AlphaCharsValidator.class.php,v 1.7 2006/04/05 23:43:18 mpont Exp $
// $Date: 2006/04/05 23:43:18 $

//------------------------------------------------------------------
import('php2go.validation.RegexValidator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		AlphaCharsValidator
// @desc		Classe que valida se uma string possui apenas caracteres alfanum�ricos
// @package		php2go.validation
// @extends		Validator
// @uses		RegexValidator
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.7 $
// @note		O padr�o inicial de valida��o da classe aceita apenas letras min�sculas
//				e mai�sculas de A a Z. Para adicionar caracteres na express�o, utilize um
//				dos par�metros aceitos pelo validador: space, number, punctuation, acclower 
//				e accupper, para aceitar, respectivamente, espa�os, n�meros, caracteres de
//				pontua��o e caracteres acentuados min�sculos e mai�sculos
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$wrongValues = array();
//				$values = array('mystring', 'anotherstring', 'wrong value');
//				if (Validator::validateMultiple('php2go.validation.AlphaCharsValidator', $values, NULL, $wrongValues)) {
//				&nbsp;&nbsp;&nbsp;print 'alpha chars ok';
//				} else {
//				&nbsp;&nbsp;&nbsp;print 'wrong values: '; var_dump($wrongValues);
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class AlphaCharsValidator extends Validator
{
	var $pattern;		// @var pattern string			Padr�o de express�o regular para valida��o
	var $fieldLabel;	// @var fieldLabel string		R�tulo do campo que est� sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	AlphaCharsValidator::AlphaCharsValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Par�metros para o validador
	// @note		Conjunto de par�metros:
	//				space		=> Forne�a TRUE para aceitar espa�os na valida��o
	//				number		=> TRUE para aceitar n�meros
	//				punctuation	=> TRUE para aceitar paracteres de pontua��o
	//				acclower	=> TRUE se caracteres acentuados min�sculos devem ser aceitos
	//				accupper 	=> TRUE se caracteres acentuados mai�sculos devem ser aceitos
	//!-----------------------------------------------------------------	
	function AlphaCharsValidator($params = NULL) {
		parent::Validator();
		$this->pattern = "a-zA-Z";
		if (TypeUtils::isArray($params)) {
			// habilita espa�os no padr�o de valida��o
			if (isset($params['space']) && $params['space'] == TRUE)
				$this->pattern .= "[:space:]";
			// permite algarismos
			if (isset($params['number']) && $params['number'] == TRUE)
				$this->pattern .= "0-9";
			// permite caracteres de pontua��o
			if (isset($params['punctuation']) && $params['punctuation'] == TRUE)
				$this->pattern .= "\.,;\:&\"'\?\!\(\)";
			// permite caracteres acentuados min�sculos
			if (isset($params['acclower']) && $params['acclower'] == TRUE) 
				$this->pattern .= "�-����-��-����-����-��-�";
			// permite caracteres acentuados mai�sculos
			if (isset($params['accupper']) && $params['accupper'] == TRUE)
				$this->pattern .= "�-����-��-����-����-���";
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AlphaCharsValidator::execute
	// @desc		Valida se um determinado valor possui apenas caracteres alfanum�ricos
	// @access		public
	// @param		value string	String a ser validada
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		$RegexValidator =& new RegexValidator(array(
			'pattern' => "^[{$this->pattern}]+$",
			'type' => "POSIX"
		));
		$result = $RegexValidator->execute($value);
		if ($result === FALSE && isset($this->fieldLabel))			
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_ALPHANUM', $this->fieldLabel);
		return $result;
	}
	
	//!-----------------------------------------------------------------
	// @function	AlphaCharsValidator::getError
	// @desc		Retorna a mensagem de erro resultante da valida��o
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>