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
// $Header: /www/cvsroot/php2go/core/validation/CreditCardValidator.class.php,v 1.12 2006/04/05 23:43:18 mpont Exp $
// $Date: 2006/04/05 23:43:18 $

//------------------------------------------------------------------
import('php2go.util.Number');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

// @const CREDIT_CARD_TYPE_MC "1"
// Definição para o cartão de crédito Mastercard
define("CREDIT_CARD_TYPE_MC", 1);
// @const CREDIT_CARD_TYPE_VS "2"
// Definição para o cartão de crédito VISA
define("CREDIT_CARD_TYPE_VS", 2);
// @const CREDIT_CARD_TYPE_AX "3"
// Definição para o cartão de crédito American Express
define("CREDIT_CARD_TYPE_AX", 3);
// @const CREDIT_CARD_TYPE_DC "4"
// Definição para o cartão de crédito Diners Club
define("CREDIT_CARD_TYPE_DC", 4);
// @const CREDIT_CARD_TYPE_DS "5"
// Definição para o cartão de crédito Discovery
define("CREDIT_CARD_TYPE_DS", 5);
// @const CREDIT_CARD_TYPE_JC "6"
// Definição para o cartão de crédito JCB
define("CREDIT_CARD_TYPE_JC", 6);

//!-----------------------------------------------------------------
// @class		CreditCardValidator
// @desc		Classe que valida números de cartão de crédito
// @package		php2go.validation
// @uses		Number
// @uses		StringUtils
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.12 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$ccNumber = '1234567890123';
//				if (Validator::validate('php2go.validation.CreditCardValidator', $ccNumber, array('name'=>'Credit Card XX', 'type' => 'visa', 'expiryMonth' => 12, 'expiryYear' => 2005))) {
//				&nbsp;&nbsp;&nbsp;print 'credit card number ok';
//				}
//
//				</pre>
// @note		Os tipos de cartão de crédito suportados são: Mastercard,
//				Visa, American Express, Diners Club, Discovery e JCB
//!-----------------------------------------------------------------
class CreditCardValidator extends Validator
{	
	var $name;				// @var name string			Nome do cartão
	var $type;				// @var type int			Tipo do cartão
	var $ccNames = array();	// @var ccNames array		Nomes dos cartões de crédito
	var $number;			// @var number string		Número do cartão de crédito
	var $expiryMonth;		// @var expiryMonth int		Mês de expiração
	var $expiryYear;		// @var expiryYear int		Ano de expiração
	var $fieldLabel;		// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;		// @var errorMessage string		Mensagem de erro	

	//!-----------------------------------------------------------------
	// @function	CreditCardValidator::CreditCardValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	// @note		Conjunto de parâmetros:
	//				name => Nome do cartão
	//				type => Tipo (vide tipos válidos nas notas e constantes da classe)
	//				expiryMonth => Mês de expiração
	//				expiryYear => Ano de expiração
	//!-----------------------------------------------------------------
	function CreditCardValidator($params = NULL) {
		parent::Validator();
		$this->ccNames = array(
			CREDIT_CARD_TYPE_MC => 'Mastercard', 
			CREDIT_CARD_TYPE_VS => 'VISA',
			CREDIT_CARD_TYPE_AX => 'American Express', 
			CREDIT_CARD_TYPE_DC => 'Diners Club',
			CREDIT_CARD_TYPE_DS => 'Discovery', 
			CREDIT_CARD_TYPE_JC => 'JCB'								
		);
		if (TypeUtils::isArray($params)) {
			if (isset($params['name']))
				$this->name = $params['name'];
			if (isset($params['type']))
				$this->_parseType($params['type']);
			if (isset($params['expiryMonth']))
				$this->_parseMonth($params['expiryMonth']);
			if (isset($params['expiryYear']))
				$this->_parseYear($params['expiryYear']);
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];				
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	CreditCardValidator::execute
	// @desc		Executa a validação de um número de cartão de crédito
	// @access		public
	// @param		value string	Número a ser validado
	// @return		bool	
	//!-----------------------------------------------------------------
	function execute($value) {
		$value = TypeUtils::parseString($value);
		$this->_parseNumber($value);
		if (!isset($this->name) || !isset($this->type) || !isset($this->number) || !isset($this->expiryMonth) || !isset($this->expiryYear)) {
			$result = FALSE;
		} else {
			$validRegex = TRUE;			
			switch ($this->type) {
				case CREDIT_CARD_TYPE_MC :
					$validRegex = ereg("^5[1-5][0-9]{14}$", $this->number); 
					break;
				case CREDIT_CARD_TYPE_VS :
					$validRegex = ereg("^4[0-9]{12}([0-9]{3})?$", $this->number);
					break;
				case CREDIT_CARD_TYPE_AX :
					$validRegex = ereg("^3[47][0-9]{13}$", $this->number);
					break;
				case CREDIT_CARD_TYPE_DC :
					$validRegex = ereg("^3(0[0-5]|[68][0-9])[0-9]{11}$", $this->number);
					break;
				case CREDIT_CARD_TYPE_DS :
					$validRegex = ereg("^6011[0-9]{12}$", $this->number);
					break;
				case CREDIT_CARD_TYPE_JC :
					$validRegex = ereg("^(3[0-9]{4}|2131|1800)[0-9]{11}$", $this->number);
					break;
			}
			$result = ($validRegex && Number::modulus10($value));
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_CREDITCARD', array($this->fieldLabel, $this->type));
		return $result;		
	}
	
	//!-----------------------------------------------------------------
	// @function	CreditCardValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
	
	//!-----------------------------------------------------------------
	// @function	CreditCardValidator::_parseType
	// @desc		Extrai o tipo do cartão de crédito a partir do 
	//				parâmetro fornecido pelo usuário
	// @access		private
	// @param		type mixed	Tipo fornecido pelo usuário
	// @return		void
	//!-----------------------------------------------------------------
	function _parseType($type) {
		$type = strtolower(trim(TypeUtils::parseString($type)));
		switch ($type) {
			case 'mc' :
			case 'mastercard' :
			case 'm' :
			case '1' :
				$this->type = CREDIT_CARD_TYPE_MC;
				break;
			case 'vs' :
			case 'visa' :
			case 'v' :
			case '2' :
				$this->type = CREDIT_CARD_TYPE_VS;
				break;
			case 'ax' :
			case 'american express' :
			case 'a' :
			case '3' :
				$this->type = CREDIT_CARD_TYPE_AX;
				break;
			case 'dc' :
			case 'diners' :
			case 'diners club' :
			case '4' :
				$this->type = CREDIT_CARD_TYPE_DC;
				break;
			case 'ds' :
			case 'discover' :
			case '5' :
				$this->type = CREDIT_CARD_TYPE_DS;
				break;
			case 'jcb' :
			case 'j' :
				$this->type = CREDIT_CARD_TYPE_JC;
				break;				
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	CreditCardValidator::_parseNumber
	// @desc		Retira caracteres não numéricos do número de cartão de crédito fornecido
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _parseNumber($number) {
		$number = ereg_replace("[^0-9]", "", $number);
		if (!empty($number))
			$this->number = $number;
	}
	
	//!-----------------------------------------------------------------
	// @function	CreditCardValidator::_parseMonth
	// @desc		Valida o mês de expiração
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _parseMonth($month) {
		if (is_numeric($month) && $month >= 1 && $month <= 12)
			$this->expiryMonth = $month;
	}
	
	//!-----------------------------------------------------------------
	// @function	CreditCardValidator::_parseYear
	// @desc		Valida o ano de expiração
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _parseYear($year) {
		$currentYear = date("Y");
		$yearPrefix = StringUtils::left($currentYear, 2);
		$currentYear = TypeUtils::parseInteger($currentYear);
		if (is_numeric($year)) {
			if ($year < 100)
				$year = TypeUtils::parseInteger($yearPrefix . $year);
			if ($year >= $currentYear && $year <= ($currentYear + 10)) {
				$this->expiryYear = $year;
			}
		}
	}	
}
?>