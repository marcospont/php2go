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
// $Header: /www/cvsroot/php2go/core/datetime/Date.class.php,v 1.30 2006/09/28 02:58:54 mpont Exp $
// $Date: 2006/09/28 02:58:54 $

// @const DATE_FORMAT_LOCAL "1"
// Corresponde ao formato de data armazenado na configuração (d/m/Y ou Y/m/d)
define('DATE_FORMAT_LOCAL', 1);
// @const DATE_FORMAT_RFC822 "2"
// Corresponde ao formato de data definido no RFC 822
define('DATE_FORMAT_RFC822', 2);
// @const DATE_FORMAT_ISO8601 "3"
// Corresponde ao formato de data ISO8601
define('DATE_FORMAT_ISO8601', 3);
// @const DATE_FORMAT_CUSTOM "4"
// Formato de data customizado
define('DATE_FORMAT_CUSTOM', 4);

//!-----------------------------------------------------------------
// @class		Date
// @desc		Classe para manipulação e realização de cálculos com datas
// @package		php2go.datetime
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.30 $
// @static
//!-----------------------------------------------------------------
class Date extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	Date::isValid
	// @desc		Verifica se uma determinada data é válida
	// @access		public
	// @param		date string	Data a ser validada
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isValid($date) {
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
		} else if (Date::isUsDate($date, $regs) || Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
		} else {
			return FALSE;
		}
		if ($year < 0 || $year > 9999) {
			return FALSE;
		} else {
			return (checkdate($month, $day, $year));
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::isValidTZ
	// @desc		Verifica se um valor de time zone é válido
	// @access		public
	// @param		tz string	Valor de time zone
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isValidTZ($tz) {
		return preg_match("/^(((\+|\-)[0-9]{2}\:[0-9]{2})|(UT|GMT|EST|EDT|CST|CDT|MST|MDT|PST|PDT)|([A-IK-Y]{1}))$/", $tz);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::isEuroDate
	// @desc		Verifica se uma data está no formato europeu dd[/-.]mm[/-.]YYYY[ HH:mm:ss]
	// @param		date string	Data a ser verificada
	// @param		&regs array	Vetor para onde retornam os valores destacados de dia, mês e ano
	// @access		public		
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function isEuroDate($date, &$regs) {
		$date = trim($date);
		if (ereg("^([0-9]{1,2})(/|\-|\.)([0-9]{1,2})(/|\-|\.)([0-9]{4})([[:space:]]([0-9]{1,2}):([0-9]{1,2}):?([0-9]{1,2})?)?$", $date, $matches)) {
			$regs = array(
				$matches[0], 
				$matches[1], $matches[3], $matches[5], 
				$matches[7], $matches[8], $matches[9]
			);
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::isUsDate
	// @desc		Verifica se uma data está no formato americano YYYY[/-.]mm[/-.]dd[ HH:mm:ss]
	// @param		date string	Data a ser verificada
	// @param		&regs array	Vetor para onde retornam os valores destacados de dia, mês e ano
	// @access		public		
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------	
	function isUsDate($date, &$regs) {
		$date = trim($date);
		if (ereg("^([0-9]{4})(/|\-|\.)([0-9]{1,2})(/|\-|\.)([0-9]{1,2})([[:space:]]([0-9]{1,2}):([0-9]{1,2}):?([0-9]{1,2})?)?$", $date, $matches)) {
			$regs = array(
				$matches[0], 
				$matches[1], $matches[3], $matches[5], 
				$matches[7], $matches[8], $matches[9]
			);
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::isSqlDate
	// @desc		Verifica se uma data está no formato SQL YYYY-mm-dd
	// @param		date string	Data a ser verificada
	// @param		&regs array	Vetor para onde retornam os valores destacados de dia, mês e ano
	// @access		public		
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function isSqlDate($date, &$regs) {
		$date = trim($date);
		if (ereg("^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})([[:space:]]([0-9]{1,2}):([0-9]{1,2}):?([0-9]{1,2})?)?$", $date, $matches)) {
			$regs = array(
				$matches[0], 
				$matches[1], $matches[2], $matches[3], 
				$matches[5], $matches[6], $matches[7]
			);
			return TRUE;			
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::isFuture
	// @desc		Verifica se uma data é posterior à data atual
	// @access		public	
	// @param		date string	Data a ser verificada
	// @return		bool
	// @see			Date::pastDate
	// @see			Date::futureDate
	// @see			Date::isPast
	// @static	
	//!-----------------------------------------------------------------
	function isFuture($date) {
		$daysFrom = Date::dateToDays($date);
		$daysTo = Date::dateToDays();
		return ($daysFrom > $daysTo);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::isPast
	// @desc		Verifica se uma data é anterior à data atual
	// @access		public	
	// @param		date string	Data a ser verificada
	// @return		bool
	// @see			Date::pastDate
	// @see			Date::futureDate
	// @see			Date::isFuture
	// @static	
	//!-----------------------------------------------------------------
	function isPast($date) {
		$daysFrom = Date::dateToDays($date);
		$daysTo = Date::dateToDays();
		return ($daysTo > $daysFrom);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::tomorrow
	// @desc		Calcula o dia seguinte em relação à data atual
	// @access		public
	// @return		string	Data calculada
	// @static
	//!-----------------------------------------------------------------
	function tomorrow() {
		return Date::nextDay();
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::nextDay
	// @desc		Calcula a data imediatamente posterior a uma determinada data
	// @access		public
	// @param		date string	"NULL" Data base
	// @return		string	Dia seguinte calculado
	// @static
	//!-----------------------------------------------------------------
	function nextDay($date=NULL) {
		if (TypeUtils::isNull($date)) {
			$date = Date::localDate();
		}
		return Date::futureDate($date, 1);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::futureDate
	// @desc		Calcula uma data no futuro, a partir de um número de dias, meses e anos
	// @access		public	
	// @param		date string	Data original
	// @param		days int	"0" Número de dias no futuro
	// @param		monts int	"0" Número de meses no futuro
	// @param		years int	"0" Número de anos no futuro
	// @return		string Data calculada no formato original
	// @see 		Date::pastDate
	// @see			Date::isFuture
	// @see			Date::isPast
	// @static	
	//!-----------------------------------------------------------------
	function futureDate($date, $days = 0, $months = 0, $years = 0) {
		// Captura o formato e os elementos da data base
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
			$dateFormat = "EURO";
		} else if (Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
			$dateFormat = "SQL";
		} else if (Date::isUsDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
			$dateFormat = "US";
		} else {			
			return NULL;
		}
		// Calcula o número de dias da data original
		$daysFrom = Date::dateToDays($date);
		$daysInc = 0;
		// Adiciona os anos
		$years = TypeUtils::parseInteger($years);
		for ($i = 1; $i <= $years; $i++) {
			$year++;
			$daysInc += (Date::isLeapYear($year)) ? 366 : 365;
		}
		// Adiciona os meses de acordo com o número de dias em cada um
		$months = TypeUtils::parseInteger($months);
		for ($i = 1; $i <= $months; $i++) {
			$mTemp = $i % 12 - 1;
			$yTemp = TypeUtils::parseInteger($i / 12);
			if (($month + $mTemp) > 12) {
				$yTemp++;
				$mTemp = ($month + $mTemp) - 12;
			} else {
				$mTemp = $month + $mTemp;
			}
			$daysInc += Date::daysInMonth($mTemp, $year + $yTemp);
		}
		// Adiciona os dias
		$daysInc += TypeUtils::parseInteger($days);
		// Retorna a data calculada no formato original
		return Date::daysToDate($daysFrom + $daysInc, $dateFormat);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::yesterday
	// @desc		Calcula o dia anterior em relação à data atual
	// @access		public
	// @return		string	Data calculada
	// @static
	//!-----------------------------------------------------------------
	function yesterday() {
		return Date::prevDay();
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::prevDay
	// @desc		Calcula a data imediatamente anterior a uma determinada data
	// @access		public
	// @param		date string	"NULL" Data base
	// @return		string	Dia anterior calculado
	// @static
	//!-----------------------------------------------------------------
	function prevDay($date=NULL) {
		if (TypeUtils::isNull($date)) {
			$date = Date::localDate();
		}
		return Date::pastDate($date, 1);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::pastDate
	// @desc		Calcula uma data no passado, a partir de um número de dias, meses e anos
	// @param		date string	Data original
	// @param		days int	"0" Número de dias no passado
	// @param		monts int	"0" Número de meses no passado
	// @param		years int	"0" Número de anos no passado
	// @return		string Data calculada no formato original
	// @access		public		
	// @see			Date::futureDate
	// @see			Date::isFuture
	// @see			Date::isPast
	// @static	
	//!-----------------------------------------------------------------
	function pastDate($date, $days=0, $months=0, $years=0) {
		// Captura o formato e os elementos da data base
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
			$dateFormat = 'EURO';
		} else if (Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
			$dateFormat = 'SQL';
		} else if (Date::isUsDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
			$dateFormat = 'US';
		} else {
			return NULL;
		}
		// Calcula o número de dias da data original
		$daysFrom = Date::dateToDays($date);
		$daysDec = 0;
		// Adiciona os anos
		for ($i = 1; $i <= $years; $i++) {
			$s = (Date::isLeapYear($year)) ? 366 : 365;
			$daysDec += (Date::isLeapYear($year)) ? 366 : 365;
			$year--;			
		}		
		// Adiciona os meses de acordo com os dias de cada mês
		for ($i = 1; $i <= $months; $i++) {
			$mTemp = $i % 12;
			$yTemp = TypeUtils::parseInteger($i / 12);
			if (($month - $mTemp) <= 0) {
				$yTemp++;
				$mTemp = 12 + ($month - $mTemp);
			} else {
				$mTemp = $month - $mTemp;
			}
			$daysDec += Date::daysInMonth($mTemp, $year - $yTemp);
		}
		// Adiciona os dias
		$daysDec += $days;
		// Retorna a data calculada no formato original
		return Date::daysToDate($daysFrom - $daysDec, $dateFormat);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::getDiff
	// @desc		Calcula a diferença em dias entre duas datas
	// @param		dateM string	Data 1
	// @param		dateS string	Data 2
	// @param		unsigned bool	"TRUE" Retorna um resultado sempre positivo (offset em dias) ou com sinal (diferença simples)
	// @return 		int Diferença em dias
	// @access		public		
	// @static
	//!-----------------------------------------------------------------
	function getDiff($dateM, $dateS, $unsigned=TRUE) {
		// Calcula o número de dias da diferença
		$daysS = Date::dateToDays($dateS);
		$daysM = Date::dateToDays($dateM);
		return ($unsigned? abs($daysS - $daysM) : ($daysS - $daysM));
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::getTZDiff
	// @desc		Calcula o número de segundos correspondente ao diferencial
	//				de um fuso horário, ou time zone
	// @param		tz string	Time zone
	// @return		int Número de segundos correspondente
	// @access		public	
	// @static
	//!-----------------------------------------------------------------
	function getTZDiff($tz) {
		$tz = TypeUtils::parseString($tz);
		if (Date::isValidTZ($tz)) {			
			if ($tz == 'Z' || $tz == 'UT' || $tz == 'GMT') {
				return 0;
			} elseif ($tz[0] == '+' || $tz[0] == '-') {
				$offset = (substr($tz, 1, 2) * 3600) + (substr($tz, -2) * 60);
				return ($tz[0] == '-' ? -1*$offset : $offset);
			} elseif (ereg("^[A-IK-Y]{1}$", $tz)) {
				if (ord($tz) > ord("M"))
					return ((ord($tz) - ord("M")) * 3600);
				else
					return ((ord("A") - ord($tz) - 1) * 3600);
			} else {
				switch ($tz) {
					case 'EDT' : return (-4*3600);
					case 'EST' :
					case 'CDT' : return (-5*3600);
					case 'CST' :
					case 'MDT' : return (-6*3600);
					case 'MST' :
					case 'PDT' : return (-7*3600);
					case 'PST' : return (-8*3600);
				}
			}
		}
		return 0;
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::parseFieldExpression
	// @desc		Interpreta expressões utilizadas na definição XML dos formulários
	//				para aplicar nos campos e/ou nas regras de validação valores de data dinâmicos
	// @param		expr string	Expressão informada no arquivo XML
	// @return		string Data correspondente
	// @note		As expressões aceitas devem ser construídas no formato TODAY (+|-) (999) (D|M|Y)
	// @access		public	
	// @static
	//!-----------------------------------------------------------------
	function parseFieldExpression($expr) {
		$matches = array();
		if (eregi("today((\+|\-)([0-9]+)(d|m|y))?", $expr, $matches)) {
			if ($matches[1]) {
				if ($matches[2] == '+')
					$date = ($matches[4] == 'D' ? Date::futureDate(Date::localDate(), $matches[3]) : ($matches[4] == 'M' ? Date::futureDate(Date::localDate(), 0, $matches[3]) : Date::futureDate(Date::localDate(), 0, 0, $matches[3])));
				else
					$date = ($matches[4] == 'D' ? Date::pastDate(Date::localDate(), $matches[3]) : ($matches[4] == 'M' ? Date::pastDate(Date::localDate(), 0, $matches[3]) : Date::pastDate(Date::localDate(), 0, 0, $matches[3])));

			} else {
				$date = Date::localDate();				
			}
			return $date;
		}
		return $expr;
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::dayOfWeek
	// @desc 		Retorna o dia da semana para uma data
	// @param		date string	Data a ser processada
	// @param		text bool	"TRUE" Se verdadeiro, retornará o nome do dia da semana
	// @param		abbr bool	"FALSE" Se verdadeiro, retornará a inicial do dia da semana
	// @return		mixed Nome ou número do dia da semana (baseado em zero)
	// @access		public		
	// @static	
	//!-----------------------------------------------------------------
	function dayOfWeek($date, $text=TRUE, $abbr=FALSE) {
		// Captura os elementos da data base de acordo com o formato
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
		} else if (Date::isUsDate($date, $regs) || Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
		} else {
			return NULL;
		}
		// Cálculo do dia da semana
		if ($month > 2) {
			$month -= 2;
		} else {
			$month += 10;
			$year--;
		}
		$dow = (floor((13 * $month - 1) / 5) + $day + ($year % 100) + floor(($year % 100) / 4) + floor(($year / 100) / 4) - 2 * floor($year / 100) + 77);
		$dow = (($dow - 7 * floor($dow / 7)));
		// Exibição do resultado, de acordo com os parâmetros fornecidos
		$LanguageBase =& LanguageBase::getInstance();
		if ($abbr)
			$daysOfWeek = $LanguageBase->getLanguageValue('DAYS_OF_WEEK_ABBR');
		else
			$daysOfWeek = $LanguageBase->getLanguageValue('DAYS_OF_WEEK');
		if ($text && $daysOfWeek[$dow]) {
			return $daysOfWeek[$dow];
		} else {
			return $dow;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::daysInMonth
	// @desc		Retorna o número de dias de um mês de acordo com o mês e o ano
	// @param		month int	"NULL" Mês
	// @param		year int	"NULL" Ano
	// @return		int Número de dias do mês solicitado
	// @access		public		
	// @static	
	//!-----------------------------------------------------------------
	function daysInMonth($month=NULL, $year=NULL) {
		if (TypeUtils::isNull($year)) 
			$year = date("Y");
		if (TypeUtils::isNull($month)) 
			$month = date("m");
		if ($month == 2) {
			return (Date::isLeapYear($year) ? 29 : 28);
		} elseif (in_array($month, array(4, 6, 9, 11))) {
			return 30;
		} else {
			return 31;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::isLeapYear
	// @desc		Verifica se um determinado ano é bissexto
	// @param		year int	"NULL" Ano para ser verificado
	// @access		public		
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function isLeapYear($year=NULL) {
		if (TypeUtils::isNull($year))
			$year = date("Y");
		if (strlen($year) != 4 || preg_match("/\D/", $year))
			return NULL;
		return ((($year % 4) == 0 && ($year % 100) != 0) || ($year % 400) == 0);
	}	
	
	//!-----------------------------------------------------------------
	// @function	Date::fromEuroToSqlDate
	// @desc		Converte uma data no padrão europeu (dd/mm/YYYY)
	// 				para o padrão SQL (YYYY-mm-dd)
	// @param		date string	Data a ser convertida
	// @param		preserveTime bool "FALSE" Preservar porção de hora, se ela existir na data fornecida
	// @return		string Data convertida ou a original se o padrão de entrada estiver incorreto
	// @access		public	
	// @static	
	//!-----------------------------------------------------------------
	function fromEuroToSqlDate($date, $preserveTime=FALSE) {
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			$res = "$regs[3]-$regs[2]-$regs[1]";
			if ($preserveTime && $regs[4] !== FALSE && $regs[5] !== FALSE) {
				$res .= " $regs[4]:$regs[5]";
				if ($regs[6] !== FALSE)
					$res .= ":$regs[6]";
			}
			return $res;
		} else {
			return $date;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::fromEuroToUsDate
	// @desc		Converte uma data no padrão europeu (dd/mm/YYYY)
	// 				para o padrão americano (YYYY/mm/dd)
	// @param		date string	Data a ser convertida
	// @param		preserveTime bool "FALSE" Preservar porção de hora, se ela existir na data fornecida	
	// @return		string Data convertida ou a original se o padrão de entrada estiver incorreto
	// @access		public		
	// @static	
	//!-----------------------------------------------------------------
	function fromEuroToUsDate($date, $preserveTime=FALSE) {
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {			
			$res = "$regs[3]/$regs[2]/$regs[1]";
			if ($preserveTime && $regs[4] !== FALSE && $regs[5] !== FALSE) {
				$res .= " $regs[4]:$regs[5]";
				if ($regs[6] !== FALSE)
					$res .= ":$regs[6]";
			}
			return $res;			
		} else {
			return $date;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::fromUsToSqlDate
	// @desc		Converte uma data no padrão americano (YYYY/mm/dd)
	// 				para o padrão SQL (YYYY-mm-dd)
	// @param		date string	Data a ser convertida
	// @return		string Data convertida ou a original se o padrão de entrada estiver incorreto
	// @access		public		
	// @static	
	//!-----------------------------------------------------------------
	function fromUsToSqlDate($date) {
		$regs = array();
		if (Date::isUsDate($date, $regs)) {
			return str_replace("/", "-", $date);
		} else {
			return $date;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::fromUsToEuroDate
	// @desc		Converte uma data no padrão americano (YYYY/mm/dd)
	// 				para o padrão europeu (dd/mm/YYYY)
	// @param		date string	Data a ser convertida
	// @param		preserveTime bool "FALSE" Preservar porção de hora, se ela existir na data fornecida	
	// @return		string Data convertida ou a original se o padrão de entrada estiver incorreto
	// @access		public		
	// @static	
	//!-----------------------------------------------------------------
	function fromUsToEuroDate($date, $preserveTime=FALSE) {
		$regs = array();
		if (Date::isUsDate($date, $regs)) {
			$res = "$regs[3]/$regs[2]/$regs[1]";
			if ($preserveTime && $regs[4] !== FALSE && $regs[5] !== FALSE) {
				$res .= " $regs[4]:$regs[5]";
				if ($regs[6] !== FALSE)
					$res .= ":$regs[6]";
			}
			return $res;			
		} else {
			return $date;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::fromSqlToEuroDate
	// @desc		Converte uma data no padrão SQL (YYYY-mm-dd)
	// 				para o padrão europeu (dd/mm/YYYY)
	// @param		date string	Data a ser convertida
	// @param		preserveTime bool "FALSE" Preservar porção de hora, se ela existir na data fornecida	
	// @return		string Data convertida ou a original se o padrão de entrada estiver incorreto
	// @access		public		
	// @static	
	//!-----------------------------------------------------------------
	function fromSqlToEuroDate($date, $preserveTime=FALSE) {
		$regs = array();
		if (Date::isSqlDate($date, $regs)) {
			$res = "$regs[3]/$regs[2]/$regs[1]";
			if ($preserveTime && $regs[4] !== FALSE && $regs[5] !== FALSE) {
				$res .= " $regs[4]:$regs[5]";
				if ($regs[6] !== FALSE)
					$res .= ":$regs[6]";
			}
			return $res;			
		} else {
			return $date;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::fromSqlToUsDate
	// @desc		Converte uma data no padrão SQL (YYYY-mm-dd)
	// 				para o padrão americano (YYYY/mm/dd)
	// @param		date string	Data a ser convertida
	// @return		string Data convertida ou a original se o padrão de entrada estiver incorreto
	// @access		public		
	// @static	
	//!-----------------------------------------------------------------
	function fromSqlToUsDate($date) {
		$regs = array();
		if (Date::isSqlDate($date, $regs)) {
			return str_replace("-", "/", $date);
		} else {
			return $date;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::fromUnixToDosDate
	// @desc		Converte um timestamp Unix em uma data/hora no
	// 				formato DOS com 4 bytes
	// @param		unixTimestamp int	"0" Timestamp UNIX para a conversão
	// @return		string Data e hora no formato DOS
	// @access		public		
	// @static	
	//!-----------------------------------------------------------------
	function fromUnixToDosDate($unixTimestamp=0) {
		$timeData = ($unixTimestamp) ? getdate($unixTimestamp) : getdate();
		if ($timeData['year'] < 1980) {
			$timeData['year'] = 1980;
			$timeData['mon'] = 1;
			$timeData['mday'] = 1;
			$timeData['hours'] = 0;
			$timeData['minutes'] = 0;
			$timeData['seconds'] = 0;
		}
		return ((($timeData['year'] - 1980) << 25) |
			($timeData['mon'] << 21) |
			($timeData['mday'] << 16) |
			($timeData['hours'] << 11) |
			($timeData['minutes'] << 5) |
			($timeData['seconds'] << 1));
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::dateToTime
	// @desc		Converte uma data nos formatos EURO, US ou SQL em
	//				um timestamp UNIX
	// @param		date string	Data
	// @access		public
	// @return		int
	// @static
	//!-----------------------------------------------------------------
	function dateToTime($date) {
		$date = Date::fromEuroToUsDate($date, TRUE);
		return strtotime($date);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::dateToDays
	// @desc		Converte uma data para o correspondente em número de dias
	// @param		date string	"NULL" Data base para o cálculo
	// @return		int	Data convertida em número de dias
	// @access		public	
	// @static
	//!-----------------------------------------------------------------
	function dateToDays($date=NULL) {		
		if (TypeUtils::isNull($date))
			$date = Date::localDate();
		$regs = array();
		if (Date::isEuroDate($date, $regs)) {
			list(, $day, $month, $year) = $regs;
		} else if (Date::isUsDate($date, $regs) || Date::isSqlDate($date, $regs)) {
			list(, $year, $month, $day) = $regs;
		} else {
			return -1;
		}		
        $century = (int) substr($year,0,2);
        $year = (int) substr($year,2,2);
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }
        return (floor((146097 * $century) / 4 ) + floor(( 1461 * $year) / 4 ) + floor(( 153 * $month + 2) / 5 ) + $day + 1721119);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::daysToDate
	// @desc		Converte um número de dias em uma data
	// @param		days int	Número de dias
	// @param		dateType string	Tipo da data a ser retornada (EURO, US ou SQL)
	// @return		string	Data correspondente
	// @access		public	
	// @static
	//!-----------------------------------------------------------------
	function daysToDate($days, $dateType) {
		if (!TypeUtils::isInteger($days) || !in_array(strtolower($dateType), array('euro', 'us', 'sql'))) {
			return NULL;
		}
        $days -= 1721119;
        $century = floor(( 4 * $days - 1) / 146097);
        $days = floor(4 * $days - 1 - 146097 * $century);
        $day = floor($days / 4);
        $year = floor(( 4 * $day +  3) / 1461);
        $day = floor(4 * $day +  3 - 1461 * $year);
        $day = floor(($day +  4) / 4);
        $month = floor(( 5 * $day - 3) / 153);
        $day = floor(5 * $day - 3 - 153 * $month);
        $day = floor(($day +  5) /  5);
        if ($month < 10) {
            $month +=3;
        } else {
            $month -=9;
            if ($year++ == 99) {
                $year = 0;
                $century++;
            }
        }
        $century = sprintf('%02d', $century);
        $year = sprintf('%02d', $year);
        $month = sprintf('%02d', $month);
        $day = sprintf('%02d', $day);
        if (strtolower($dateType) == 'euro') {
        	return ("$day/$month/$century$year");
        } else if (strtolower($dateType) == 'us') {
        	return ("$century$year/$month/$day");
        } else {
        	return ("$century$year-$month-$day");
        }		
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::monthName
	// @desc		Retorna o nome completo do mês, para um 
	//				determinado valor de timestamp
	// @param		ts int	"0" Timestamp de referência
	// @note		Se este valor for omitido, o timestamp do servidor será utilizado em seu lugar
	// @return		string Nome do mês para o timestamp fornecido
	// @access		public	
	// @static	
	//!-----------------------------------------------------------------
	function monthName($ts=0) {
		$Lang =& LanguageBase::getInstance();
		$date = ($ts <= 0 ? time() : intval($ts));
		$month = date('n', $date);
		$monthNames = $Lang->getLanguageValue('MONTHS_OF_YEAR');
		return $monthNames[$month-1];
	}	
	
	//!-----------------------------------------------------------------
	// @function	Date::localDate
	// @desc		Retorna a data local de acordo com o formato
	// @access		public
	// @param		ts int	"0" Timestamp opcional para geração da data local
	// @return		string Data local, a partir do timestamp atual ou um determinado
	// @static	
	//!-----------------------------------------------------------------
	function localDate($ts=0) {
		$Conf =& Conf::getInstance();
		$dateFormat = $Conf->getConfig('LOCAL_DATE_FORMAT');
		if ($ts > 0) {
			if ($dateFormat) {
				return date($dateFormat . ' H:i:s', $ts);
			} else {
				return date("d/m/Y H:i:s", $ts);
			}
		} else {
			if ($dateFormat) {
				return date($dateFormat);
			} else {
				return date("d/m/Y");
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::formatDate
	// @desc		Formata um valor de data a partir dos valores de dia, mês e ano
	// @access		public
	// @param		day int				Valor do dia na data
	// @param		month int			Valor do mês na data
	// @param		year int			Valor do ano na data, com 4 dígitos
	// @param		fmtType int			"DATE_FORMAT_LOCAL" Tipo de formato de data (vide constantes da classe)
	// @param		fmtString string	"" Descrição do formato (quando $fmtType==DATE_FORMAT_CUSTOM)
	// @return		string Data formatada
	// @static	
	//!-----------------------------------------------------------------
	function formatDate($day, $month, $year, $fmtType=DATE_FORMAT_LOCAL, $fmtStr='') {
		$day = TypeUtils::parseString(str_repeat('0', (2 - strlen($day))) . $day);
		$month = TypeUtils::parseString(str_repeat('0', (2 - strlen($month))) . $month);
		$year = TypeUtils::parseString(str_repeat('0', (4 - strlen($year))) . $year);
		$tsDate = mktime(0, 0, 0, $month, $day, $year);
		return Date::formatTime($tsDate, $fmtType, $fmtStr);
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::formatTime
	// @desc		Formata um valor de unix timestamp
	// @access		public
	// @param		time int			Unix timestamp
	// @param		fmtType int			"DATE_FORMAT_LOCAL" Tipo de formato de data (vide constantes da classse)
	// @param		fmtString string	"" Descrição do formato (quando $fmtType==DATE_FORMAT_CUSTOM)
	// @return		string Timestamp formatado
	//!-----------------------------------------------------------------
	function formatTime($time=NULL, $fmtType=DATE_FORMAT_LOCAL, $fmtStr='') {
		if (empty($time))
			$time = time();
		if (!TypeUtils::isInteger($time) || $time < 0 || $time > LONG_MAX)
			return $time;
		if ($fmtType == DATE_FORMAT_LOCAL) {			
			return Date::localDate($time);
		} elseif ($fmtType == DATE_FORMAT_RFC822) {
			$tz = PHP2Go::getConfigVal('LOCAL_TIME_ZONE', FALSE);
			if (!empty($tz)) {
				$date = date('D, d M Y H:i:s', $time) . ' ' . $tz;
				return $date;
			} else {
				return date('r', $time);
			}
		} elseif ($fmtType == DATE_FORMAT_ISO8601) {
			$date = gmdate("Y-m-d\TH:i:sO", $time);
			$tz = PHP2Go::getConfigVal('LOCAL_TIME_ZONE', FALSE);
			if (!empty($tz)) {
				return str_replace('+00:00', $tz, $date);
			} else {
				return $date;
			}	
		} elseif ($fmtType == DATE_FORMAT_CUSTOM && !empty($fmtStr)) {
			return date($fmtStr, $time);
		} else {
			return $time;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Date::printDate
	// @desc		Imprime a data atual do sistema formatada
	// @param		city bool		"TRUE" Flag para exibir o nome do município
	// @param		country bool	"TRUE" Flag para exibir o nome do país
	// @param		dow bool		"TRUE" Flag para exibir o dia da semana
	// @return		string Data formatada de acordo com as opções
	// @static	
	//!-----------------------------------------------------------------
	function printDate($city=TRUE, $country=TRUE, $dow=TRUE) {
		$Conf =& Conf::getInstance();
		$Lang =& LanguageBase::getInstance();
		$date = "";
		// Insere o nome da cidade local
		if ($city) {
			$cityName = $Conf->getConfig('CITY');
			if (TypeUtils::isFalse($cityName)) {
				trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_FIND_CFG_VAL'), "CITY"), E_USER_WARNING);
			} else if (!empty($cityName)) {
				$date .= $cityName;
			}			
		}
		// Insere o nome do país local
		if ($country) {
			$countryName = $Conf->getConfig('COUNTRY');
			if (TypeUtils::isFalse($countryName)) {
				trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_FIND_CFG_VAL'), "COUNTRY"), E_USER_WARNING);
			} else if (!empty($countryName)) {
				if (!empty($cityName)) 
					$date .= "/";
				$date .= $countryName . ", ";
			}
		} else if (!empty($cityName)) {
			$date .= ", ";
		}
		// Insere o dia da semana
		if ($dow) {
			$daysOfWeek = $Lang->getLanguageValue('DAYS_OF_WEEK');
			$dayOfWeek = date('w');
			$date .= $daysOfWeek[$dayOfWeek] . ", ";
		}
		// Insere a data atual
		$date .= Date::localDate();
		return $date;
	}	
}
?>