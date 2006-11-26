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
// $Header: /www/cvsroot/php2go/core/util/Number.class.php,v 1.16 2006/04/05 23:43:24 mpont Exp $
// $Date: 2006/04/05 23:43:24 $

//!-----------------------------------------------------------------
// @class		Number
// @desc		Esta classe contém métodos de manipulação, transformação
//				e conversão de formato de números inteiros, decimais,
//				binários, romanos, fracionários, etc...
// @package		php2go.util
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.16 $
// @static
//!-----------------------------------------------------------------
class Number extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	Number::numberConversion
	// @desc		Converte um número de uma base para outra
	// @access		public
	// @param		source mixed		Parâmetro de entrada
	// @param		baseIn int		Base de entrada
	// @param		baseOut int		Base de saída
	// @param		targetLen int		"0" Tamanho para o valor de saída
	// @return		mixed Número convertido ou NULL em caso de erros
	// @note		As bases aceitas vão de 2 a 36
	// @static
	//!-----------------------------------------------------------------
	function numberConversion($source, $baseIn, $baseOut, $targetLen = 0) {
		if (!TypeUtils::parseInteger($baseIn) || !TypeUtils::parseInteger($baseOut) || $baseIn < 2 || $baseIn > 36 || $baseOut < 2 || $baseOut > 36) {
			return NULL;
		}
		// Cálculo do operando de entrada na base 10
		if ($baseIn != 10) {
			$source = strtolower($source);
			$sourceLen = strlen($source);
			$decimalNumber = 0;
			for ($i = 0; $i < $sourceLen; $i++) {
				$operand = substr($source, $i, 1);
				// Operando maior do que a base de entrada
				if ($operand >= $baseIn) {
					return NULL;
				}
				// Operando alfabético
				if ((ord($operand) >= 97) && (ord($operand) <= 122)) {
					$operand = ord($operand) - 87;
				}
				$decimalNumber += $operand * pow($baseIn, $i);
			}
		} else {
			$decimalNumber = $source;
		}
		// Tamanho máximo representável
		if ($decimalNumber > 214748367) {
			return NULL;
		}
		settype($decimalNumber, "double");
		settype($baseOut, "double");
		// Cálculo do resultado na base de saída
		if ($baseOut != 10) {
			$return = "";
			while ($decimalNumber > 0) {
				// Busca o resto da divisão do número pela base de saída
				$remainder = (TypeUtils::parseFloat($decimalNumber) % $baseOut);
				// Resto numérico
				if ($remainder < 10) {
					$return = $remainder . $return;
					// Resto alfabético
				} else {
					$return = chr($remainder + 87) . $return;
				}
				$decimalNumber = floor($decimalNumber / $baseOut);
			}
		} else {
			$return = $decimalNumber;
		}
		if ($targetLen > strlen($return)) {
			return str_pad(strtoupper($return), $targetLen, "0", STR_PAD_LEFT);
		} else {
			return strtoupper($return);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Number::fromHexToBin
	// @desc		Conversão de números hexadecimais para binários
	// @access		public
	// @param		number mixed		Número a ser convertido
	// @return		string Número convertido para binários
	// @static
	//!-----------------------------------------------------------------
	function fromHexToBin($number) {
		$numberLen = strlen(TypeUtils::parseString($number));
		$bin = "";
		for ($i=0, $s=strlen($number); $i<$s; $i+=2) {
			$bin .= decbin(hexdec(substr($number, $i, 2)));
		}
		return $bin;
	}

	//!-----------------------------------------------------------------
	// @function	Number::fromDecimalToCurrency
	// @desc		Converte números inteiros ou decimais em formato moeda
	// @access		public
	// @param		number mixed			Número decimal ou inteiro a ser convertido
	// @param		currencySign string		"NULL" Símbolo da moeda desejada
	// @param		decSep string			"NULL" Separador dos centavos
	// @param		thousSep string			"NULL" Separador dos milhares
	// @param		precision int			"NULL" Precisão do valor final
	// @param		currencySignPos string	"NULL" Posição do símbolo da moeda'
	// @return 		string Número convertido para preço/valor de moeda
	// @note		Se os parâmetros $currencySign, $decSep, $thousSep, $precision e
	//				$currencySignPos não forem fornecidos, serão buscados na tabela de
	//				localização ativa (http://www.php.net/localeconv)
	// @static
	//!-----------------------------------------------------------------
	function fromDecimalToCurrency($number, $currencySign=NULL, $decSep=NULL, $thousSep=NULL, $precision=NULL, $currencySignPos=NULL) {
		$locale = localeconv();
		$currencySign = (TypeUtils::isNull($currencySign, TRUE) ? $locale['currency_symbol'] : (empty($currencySign) ? '' : $currencySign));
		if (empty($decSep))
			$decSep = $locale['mon_decimal_point'];
		if (empty($thousSep))
			$thousSep = $locale['mon_thousands_sep'];
		if (empty($precision))
			$precision = $locale['frac_digits'];
		$number = TypeUtils::parseFloat(trim($number));
		if (TypeUtils::isFloat($number)) {
			if (!empty($currencySign)) {
				$x = ($number < 0 ? 'n' : 'p');
				if (empty($currencySignPos))
					$currencySignPos = ($locale["{$x}_cs_precedes"] ? 'left' : 'right');
				$currencySignSpace = ($locale["{$x}_sep_by_space"] ? ' ' : '');
				return ($currencySignPos == 'left' ? $currencySign . $currencySignSpace . number_format($number, $precision, $decSep, $thousSep) : number_format($number, $precision, $decSep, $thousSep) . $currencySignSpace . $currencySign);
			} else {
				return number_format($number, $precision, $decSep, $thousSep);
			}
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function 	Number::fromDecimalToFraction
	// @desc		Converte um número decimal em seu correspondente
	// 				fracionário. Ex: 10.5 -> 10 1/2
	// @access		public
	// @param		number float		Número a ser convertido
	// @return 		string Correspondente fracionário do número ou FALSE caso
	// 				o parâmetro passado não seja um número decimal válido
	// @note 		1.0 ou 2.0 será tomado como inteiro e retornará FALSE
	// @static
	//!-----------------------------------------------------------------
	function fromDecimalToFraction($number) {
		$locale = localeconv();
		$isF = TypeUtils::isFloat($number);
		if (!$isF) {
			$number = str_replace(',', '.', (string)$number);
			$isF = TypeUtils::isFloat($number);
			if (!$isF)
				return FALSE;
		}
		$number = (string)$number;
		if (strpos($number, $locale['decimal_point']) === FALSE)
			$number .= $locale['decimal_point'] . '0';
		list($intpart, $numerator) = explode($locale['decimal_point'], $number);
		$denominator = '1' . str_repeat('0', strlen ($numerator));
		$gcd = Number::gcd($numerator, $denominator);
		$numerator /= $gcd;
		$denominator /= $gcd;
		return ($intpart) ? sprintf("%d <sup>%d</sup>/<sub>%d</sub>", $intpart, $numerator, $denominator) : sprintf("<sup>%d</sup>/<sub>%d</sub>", $numerator, $denominator);
	}

	//!-----------------------------------------------------------------
	// @function	Number::fromArabicToRoman
	// @desc		Realiza a conversão de números arábicos inteiros
	// 				positivos para números romandos
	// @access		public
	// @param		arabic int		Número arábico a ser convertido
	// @return		string Número correspondente em romanos ou FALSE se for um
	// 				número negativo ou inválido
	// @see			Number::fromRomanToArabic
	// @static
	//!-----------------------------------------------------------------
	function fromArabicToRoman($arabic) {
		$roman = '';
		$convBase = array(10 => array('X', 'C', 'M'),
			5 => array('V', 'L', 'D'),
			1 => array('I', 'X', 'C'));
		if ($arabic < 0) {
			return FALSE;
		} else {
			// converte e normaliza o número
			$arabic = (int) $arabic;
			$digit = (int) ($arabic / 1000);
			$arabic -= $digit * 1000;
			while ($digit > 0) {
				$roman .= 'M';
				$digit--;
			}
			// concatena os valores em romano convertidos dígito a dígito
			for ($i = 2; $i >= 0; $i--) {
				// busca o dígito da casa atual no número arábico
				$power = pow(10, $i);
				$digit = (int) ($arabic / $power);
				$arabic -= $digit * $power;
				// constrói dígitos especiais (dígitos 4 e 9)
				if (($digit == 9) || ($digit == 4)) {
					$roman .= $convBase[1][$i] .= $convBase[$digit + 1][$i];
				}
				// constrói os outros dígitos
				else {
					if ($digit >= 5) {
						$roman .= $convBase[5][$i];
						$digit -= 5;
					}
					while ($digit > 0) {
						$roman .= $convBase[1][$i];
						$digit--;
					}
				}
			}
			return $roman;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Number::fromRomanToArabic
	// @desc		Realiza a conversão de números romanos em números arábicos
	// @access		public
	// @param		roman string		Número no formato romano
	// @return 		int Correspondente número arábico ou FALSE se o número
	// 				romano for inválido e/ou mal construído
	// @see			Number::fromArabicToRoman
	// @static
	//!-----------------------------------------------------------------
	function fromRomanToArabic($roman) {
		$rValues = array(array(1, 'I'), array(5, 'V'),
			array(10, 'X'), array(50, 'L'),
			array(100, 'C'), array(500, 'D'),
			array(1000, 'M'), array(0, 0));
		$rRegExp = '/^(I|V|X|L|C|D|M)/i';
		if (!preg_match($rRegExp, $roman)) {
			return FALSE;
		} else {
			$rLen = strlen($roman)-1;
			$state = 0;
			$index = 0;
			$arabic = 0;
			while ($rLen >= 0) {
				$i = 0;
				while ($rValues[$i][0] > 0) {
					if (strtoupper($roman[$rLen]) == $rValues[$i][1]) {
						if ($state > $rValues[$i][0]) {
							if (($index - $i) != 2) {
								return FALSE;
							} else {
								$arabic -= $rValues[$i][0];
							}
						} else {
							$arabic += $rValues[$i][0];
							$state = $rValues[$i][0];
							$index = $i;
						}
						break;
					}
					$i++;
				}
				$rLen--;
			}
			return $arabic;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Number::formatByteAmount
	// @desc		Formata uma quantidade de bytes
	// @access		public
	// @param		size int		Quantidade em bytes
	// @param		mode string	"" Modo como o resultado deve ser apresentado
	// @param		precision int	"2" Número de casas decimais no resultado
	// @return		string Quantidade formatada
	// @static
	//!-----------------------------------------------------------------
	function formatByteAmount($size, $mode = '', $precision = 2) {
		$locale = localeconv();
		$decSep = $locale['decimal_point'];
		$thousSep = $locale['thousands_sep'];
		$precision = TypeUtils::parseInteger($precision);
		switch($mode) {
			case 'K' : return number_format(($size / 1024), $precision, $decSep, $thousSep) . 'K';
			case 'M' : return number_format(($size / 1024 / 1024), $precision, $decSep, $thousSep) . 'M';
			case 'G' : return number_format(($size / 1024 / 1024 / 1024), $precision, $decSep, $thousSep) . 'G';
			case 'T' : return number_format(($size / 1024 / 1024 / 1024 / 1024), $precision, $decSep, $thousSep) . 'T';
			default  : return $size;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Number::modulus10
	// @desc		Implementação do algoritmo módulo 10 para a validação
	//				de uma seqüência de números
	// @access		public
	// @param		number string		Seqüência numérica
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function modulus10($number) {
		$number = strrev($number);
		$numberLength = strlen($number);
		$currentNumber = $firstNumber = $secondNumber = NULL;
		$sum = 0;
		for ($i=0; $i<$numberLength; $i++) {
			$currentNumber = substr($number, $i, 1);
			if ($i % 2 == 1)
				$currentNumber *= 2;
			if ($currentNumber > 9) {
				$firstNumber = $currentNumber % 10;
				$secondNumber = ($currentNumber - $firstNumber) / 10;
				$currentNumber = $firstNumber + $secondNumber;
			}
			$sum += $currentNumber;
		}
		return (($sum % 10) == 0);
	}

	//!-----------------------------------------------------------------
	// @function 	Number::modulus11
	// @desc 		Cálculo do dígito verificador de um seqüência
	// 				numérica segundo o padrão de verificação módulo 11
	// @access		public
	// @param 		number mixed		Seqüência Numérica
	// @param 		base int			Peso máximo para construção do produtório
	// @return		int Dígito verificador para a seqüência numérica ou
	// 				false se a seqüência fornecida não for válida
	// @static
	//!-----------------------------------------------------------------
	function modulus11($number, $base = 9) {
		if (!TypeUtils::isInteger($number)) {
			return FALSE;
		}
		$sum = 0;
		$factor = 2;
		$strSize = strlen(TypeUtils::parseString($number)) - 1;
		for ($i = $strSize; $i >= 0; $i--) {
			$sum += ($number[$i] * $factor);
			$factor = ($factor == $base) ? 2 : $factor++;
		}
		$result = 11 - ($sum % 11);
		return ($result == 10) ? 1 : $result;
	}

	//!-----------------------------------------------------------------
	// @function	Number::gcd
	// @desc		Cálculo do maior divisor comum entre dois números
	// @access		public
	// @param		a int		Primeiro número
	// @param		b int		Segundo número
	// @return		int Maior Divisor Comum de dois operandos
	// @static
	//!-----------------------------------------------------------------
	function gcd($a, $b) {
		while ($b != 0) {
			$remainder = $a % $b;
			$a = $b;
			$b = $remainder;
		}
		return abs($a);
	}

	//!-----------------------------------------------------------------
	// @function	Number::randomize
	// @desc		Gera um número randômico em um determinado intervalo
	// @access		public
	// @param		rangeMin int	Início do intervalo
	// @param		rangeMax int	Fim do intervalo
	// @return		int Número escolhido ou NULL em caso de erros
	// @static
	//!-----------------------------------------------------------------
	function randomize($rangeMin, $rangeMax) {
		if ($rangeMax > $rangeMin && is_numeric($rangeMin) && is_numeric($rangeMax)) {
			return rand($rangeMin, $rangeMax);
		} else {
			return NULL;
		}
	}
}
?>