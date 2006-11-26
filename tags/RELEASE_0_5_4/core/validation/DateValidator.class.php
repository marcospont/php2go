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
// $Header: /www/cvsroot/php2go/core/validation/DateValidator.class.php,v 1.12 2006/06/15 00:29:12 mpont Exp $
// $Date: 2006/06/15 00:29:12 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
import('php2go.datetime.Date');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DateValidator
// @desc		Classe que valida datas ou dados no formato data/hora
// @package		php2go.validation
// @uses		Conf
// @uses		Date
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.12 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$value = '10/12/2012';
//				if (Validator::validate('php2go.validation.DateValidator', $value)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class DateValidator extends Validator
{
	var $type;		// @var type string	Tipo da data a ser validada
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	DateValidator::DateValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	// @note		Conjunto de parâmetros:
	//				type => Tipo da data (US, EURO ou SQL)
	//!-----------------------------------------------------------------
	function DateValidator($params = NULL) {	
		parent::Validator();
		$Conf =& Conf::getInstance();
		if (TypeUtils::isArray($params)) {
			if (isset($params['type']) && in_array(strtoupper($params['type']), array('EURO', 'SQL', 'US'))) {
				$this->type = $params['type'];
			} else {
				$this->type = $Conf->getConfig('LOCAL_DATE_TYPE');
			}
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];			
		} else {
			$this->type = $Conf->getConfig('LOCAL_DATE_TYPE');
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	DateValidator::execute
	// @desc		Executa a validação de uma data
	// @access		public
	// @param		&value string	Data ou data/hora a ser validada
	// @return		bool
	// @note		A validação leva em conta a sintaxe da data ou da data/hora
	//				e o dia em relação ao mês/ano
	//!-----------------------------------------------------------------
	function execute(&$value) {
		$value = TypeUtils::parseString($value);
		$matches = array();
		$type = TypeUtils::parseString($this->type);
		$result = TRUE;
		switch ($type) {
			case 'EURO' : 
				if (Date::isEuroDate($value, $matches)) {
					list(,$day,$month,$year,$hours,$minutes,$seconds) = $matches;
				} else {
					$result = FALSE;
				}
				break;
			case 'SQL' :
				if (Date::isSqlDate($value, $matches)) {
					list(,$year,$month,$day,$hours,$minutes,$seconds) = $matches;
				} else {
					$result = FALSE;
				}
				break;
			case 'US' :
				if (Date::isUsDate($value, $matches)) {
					list(,$year,$month,$day,$hours,$minutes,$seconds) = $matches;
				} else {
					$result = FALSE;
				}
				break;
			default :
				$result = FALSE;
		}
		if ($result) {
			// valida o dia ( >0 && <=31 )
			if (TypeUtils::parseInteger($day) < 1 || TypeUtils::parseInteger($day) > 31)
				$result = FALSE;
			// valida o mês ( >0 && <=12 )
			if (TypeUtils::parseInteger($month) < 1 || TypeUtils::parseInteger($month)  > 12)
				$result = FALSE;
			// valida o dia em relação ao mês e o ano
			$daysInMonth = Date::daysInMonth($month, $year);
			if ($day > $daysInMonth) {
				$result = FALSE;
			} else {
				// valida a hora ( >=0 && <= 24 )
				if ($hours && (TypeUtils::parseInteger($hours) < 0 || TypeUtils::parseInteger($hours) > 24)) {
					$result = FALSE;
				}
				// valida os minutos ( >=0 && <= 59 )
				if ($minutes && (TypeUtils::parseInteger($minutes) < 0 || TypeUtils::parseInteger($minutes) > 59)) {
					$result = FALSE;
				}
				// valida os segundos ( >=0 && <= 59 )
				if ($seconds && (TypeUtils::parseInteger($seconds) < 0 || TypeUtils::parseInteger($seconds) > 59)) {
					$result = FALSE;
				}					
			}
		}
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');			
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['DATE']));
		}
		return $result;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	DateValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>