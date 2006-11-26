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
// $Header: /www/cvsroot/php2go/core/text/StringUtils.class.php,v 1.26 2006/11/02 19:16:01 mpont Exp $
// $Date: 2006/11/02 19:16:01 $

//------------------------------------------------------------------
import('php2go.util.Number');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		StringUtils
// @desc		Classe que cont�m fun��es utilit�rias para manipula��o
//				de strings. As fun��es extendem a funcionalidade j� oferecida
//				pelo PHP (agrupando e tornando mais pr�tica a utiliza��o)
//				e incluem novas ferramentas n�o implementadas
// @package		php2go.text
// @extends		PHP2Go
// @uses		Number
// @author		Marcos Pont
// @version		$Revision: 1.26 $
// @static
//!-----------------------------------------------------------------
class StringUtils extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	StringUtils::allTrim
	// @desc		Retira caracteres brancos � esquerda, � direita e
	// 				todos os espa�os em branco duplos
	// @param		str string		String a ser formatada
	// @return 		string Valor do string formatado sem espa�os in�teis
	// @see 		StringUtils::stripBlank
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function allTrim($str) {
		return StringUtils::stripBlank(trim($str));
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::stripBlank
	// @desc 		Transforma caracteres brancos ocupando 2 ou mais posi��es
	// 				em um caractere de espa�o simples (ord 32)
	// @param 		str string		String a ser formatada
	// @param		replace string	" " String de substitui��o
	// @return 		string Novo valor da string
	// @see 		StringUtils::allTrim
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function stripBlank($str, $replace=' ') {
		return ereg_replace("[[:blank:]]{1,}", $replace, $str);
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::left
	// @desc 		Retorna a quantidade indicada de caracteres de
	// 				uma string fornecida a partir da esquerda
	// @param 		str string		String a ser utilizada
	// @param 		chars int		"0" N�mero de caracteres solicitados
	// @return 		string Quantidade solicitada ou toda a string se o par�metro
	// 				chars fornecido for igual a ""
	// @see 		StringUtils::right
	// @see 		StringUtils::mid
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function left($str, $chars = 0) {
		if (!TypeUtils::isInteger($chars)) {
			return $str;
		} else if ($chars == 0) {
			return '';
		} else {
			return substr($str, 0, $chars);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::right
	// @desc 		Retorna a quantidade indicada de caracteres de
	// 				uma string fornecida a partir da direita
	// @param 		str string		String a ser utilizada
	// @param 		chars int		"0" N�mero de caracteres solicitados
	// @return 		string Quantidade solicitada ou toda a string se o par�metro
	// 				chars fornecido for igual a ""
	// @see 		StringUtils::left
	// @see 		StringUtils::mid
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function right($str, $chars=0) {
		if (!TypeUtils::isInteger($chars)) {
			return $str;
		} else if ($chars == 0) {
			return '';
		} else {
			return substr($str, strlen($str) - $chars, strlen($str)-1);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::mid
	// @desc 		Retorna uma por��o interna de uma string indicada
	// 				pelo delimitador de in�cio `startAt` e pelo n�mero
	// 				de caracteres `chars`
	// @param 		str string		String original
	// @param 		startAt int		"1" Posi��o inicial
	// @param 		chars int		"0" Quantidade de caracteres solicitados
	// @return 		string Novo valor da string
	// @see 		StringUtils::left
	// @see 		StringUtils::right
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function mid($str, $startAt=1, $chars=0) {
		if (!TypeUtils::isInteger($chars)) {
			return $str;
		} else if ($str == '' || $chars == 0) {
			return '';
		} else if (($startAt + $chars) > strlen($str)) {
			return $str;
		} else {
			if ($startAt == 0) $startAt = 1;
			return substr($str, $startAt-1, $chars);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::charAt
	// @desc		Retorna o caractere na posi��o $index do string $str
	// @param		str string	String a ser consultado
	// @param		index int	�ndice do caractere buscado
	// @return		string Valor do caractere ou vazio para �ndices inv�lidos
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function charAt($str, $index) {
		if (!TypeUtils::isInteger($index)) {
			return '';
		} else if ($str == '' || $index < 0 || $index >= strlen($str)) {
			return '';
		} else {
			$strTranslated = strval($str);
			return $strTranslated{$index};
		}
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::match
	// @desc 		Busca por um valor dentro de um texto, levando
	// 				em considera��o o caso (mai�sculas/min�sculas)
	// @param		str string			String base para a busca
	// @param 		sValue string		Valor a ser buscado
	// @param 		caseSensitive bool	"TRUE" Indica se a busca considera ou n�o letras mai�sculas/min�sculas
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function match($str, $sValue, $caseSensitive=TRUE) {
		if (!$caseSensitive) $sValue = strtolower($sValue);
		if (strlen($sValue) == 0) {
			return FALSE;
		} else {
			$pos = strpos($str, $sValue);
			return ($pos !== FALSE);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::startsWith
	// @desc 		Verifica se o in�cio de uma string corresponde
	// 				ao valor do par�metro 'slice'
	// @param 		str string			String a ser testada
	// @param 		slice string		Por��o de string
	// @param 		caseSensitive bool	"TRUE" Indica se a busca considera ou n�o letras mai�sculas/min�sculas
	// @param 		ignSpaces bool		"TRUE" Retirar espa�os em branco � esquerda para realizar a busca
	// @see 		StringUtils::endsWith
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function startsWith($str, $slice, $caseSensitive=TRUE, $ignSpaces=TRUE) {
		if (!$caseSensitive) {
			$strUsed = ($ignSpaces) ? ltrim(strtolower($str)) : strtolower($str);
			$sliceUsed = strtolower($slice);
		} else {
			$strUsed = ($ignSpaces) ? ltrim($str) : $str;
			$sliceUsed = $slice;
		}
		return (StringUtils::left($strUsed, strlen($sliceUsed)) == $sliceUsed);
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::endsWith
	// @desc 		Verifica se o final de uma string corresponde
	// 				ao valor do par�metro 'slice'
	// @param 		str string			String a ser testada
	// @param 		slice string		Por��o de string
	// @param 		caseSensitive bool	"TRUE" Indica se a busca considera ou n�o letras mai�sculas/min�sculas
	// @param 		ignSpaces bool		"TRUE" Retirar espa�os em branco � direita para realizar a busca
	// @see 		StringUtils::startsWith
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function endsWith($str, $slice, $caseSensitive=TRUE, $ignSpaces=TRUE) {
		if (!$caseSensitive) {
			$strUsed = ($ignSpaces) ? rtrim(strtolower($str)) : strtolower($str);
			$sliceUsed = strtolower($slice);
		} else {
			$strUsed = ($ignSpaces) ? rtrim($str) : $str;
			$sliceUsed = $slice;
		}
		return (StringUtils::right($strUsed, strlen($sliceUsed)) == $sliceUsed);
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::isAllUpper
	// @desc 		Verifica se uma string � composta apenas por letras mai�sculas
	// @param 		str string		String a ser verificada
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isAllUpper($str) {
		return (preg_match("/[a-z]/", $str) !== FALSE);
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::isAllLower
	// @desc 		Verifica se uma string � composta apenas por letras min�sculas
	// @param 		str string		String a ser verificada
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isAllLower($str) {
		return (preg_match("/[A-Z]/", $str) !== FALSE);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::isEmpty
	// @desc		Retorna TRUE se a string $value passada por par�metro for vazia
	// @param		value string	String a ser testada
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isEmpty($value) {
		$value = strval($value);
		return (empty($value) && strlen($value) == 0);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::ifEmpty
	// @desc		Substitui a string por um valor de substitui��o caso ela seja vazia
	// @param		value string		Valor original
	// @param		replacement string	Valor de substitui��o
	// @access		public
	// @return		string
	// @static
	//!-----------------------------------------------------------------
	function ifEmpty($value, $replacement) {
		return (empty($value) ? $replacement : $value);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::concat
	// @desc		Concatena um valor no final da string
	// @param		str string		String original
	// @param		concat string	Valor a ser concatenado
	// @return		string Novo valor da string
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function concat($str, $concat) {
		return $str . $concat;
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::surround
	// @desc		Insere prefixo e sufixo em uma determinada string
	// @param		str string		String original
	// @param		prefix string	Prefixo
	// @param		suffix string	Sufixo
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function surround($str, $prefix, $suffix) {
		return $prefix . $str . $suffix;
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::insert
	// @desc 		Insere um valor na posi��o indicada de uma string
	// @param 		str string		String original
	// @param 		insValue string	"" Valor a ser inserido
	// @param 		insPos int		"0" Posi��o para inserir a string
	// @return 		string Novo valor da string
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function insert($str, $insValue = '', $insPos = 0) {
		if (($insValue == '') || ($insPos < 0) || ($insPos > strlen($str))) {
			return $str;
		} else if ($insPos == 0) {
			return $insValue . $str;
		} else if ($insPos == strlen($str)) {
			return $str . $insValue;
		} else {
			return StringUtils::left($str, $insPos) . $insValue . StringUtils::right($str, $insPos, strlen($str) - $insPos);
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::replace
	// @desc		Wrapper para a fun��o str_replace, utilizado como
	//				modifier de vari�veis de template
	// @param		str string	String original
	// @param		from string	Valor buscado
	// @param		to string	Valor a substituir
	// @access		public
	// @return		string
	// @static
	//!-----------------------------------------------------------------
	function replace($str, $from, $to) {
		return str_replace($from, $to, $str);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::regexReplace
	// @desc		Wrapper para a fun��o preg_replace, utilizado como
	//				modifier de vari�veis de template
	// @param		str string			String original
	// @param		pattern string		Pattern no padr�o PCRE
	// @param		replacement string	Valor de substitui��o
	// @access		public
	// @return		string
	// @static
	//!-----------------------------------------------------------------
	function regexReplace($str, $pattern, $replacement) {
		if (empty($pattern))
			return $str;
		$matches = array();
    	if (preg_match('!\W(\w+)$!s', $pattern, $matches) && (strpos($matches[1], 'e') !== FALSE))
			$pattern = substr($pattern, 0, -strlen($matches[1])) . str_replace('e', '', $matches[1]);
		return preg_replace($pattern, $replacement, $str);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::explode
	// @desc		Cria um array a partir da quebra da string $str
	//				em peda�os separados por $sep
	// @access		public
	// @return		array
	// @static
	//!-----------------------------------------------------------------
	function explode($str, $sep) {
		$arr = explode($sep, $str);
		return $arr;
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::implode
	// @desc		Cria uma string a partir da uni�o dos elementos de um array
	// @param		values array	Array de valores
	// @param		glue string		String utilizada para unir os valores
	// @return		string
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function implode($values, $glue) {
		return implode($glue, (array)$values);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::encode
	// @desc		Codifica uma string de acordo com um padr�o
	// @param		str string			String a ser codificada
	// @param		encodeType string	Tipo de codifica��o
	// @param		params array		"NULL" Vetor de par�metros ou argumentos para a codifica��o
	// @return		string String codificada
	// @note		Par�metros dispon�veis por tipo de codifica��o:
	//				- 7bit: {nl}
	//				- 8bit: {nl}
	//				- quoted-printable: {charset}
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function encode($str, $encodeType, $params=NULL) {
		switch(strtolower($encodeType)) {
			case 'base64' :
				$encoded = chunk_split(base64_encode($str));
				break;
			case 'utf8' :
				$encoded = utf8_encode($str);
				break;
			case '7bit' :
			case '8bit' :
				$nl = TypeUtils::ifNull($params['nl'], "\n");
				$str = str_replace(array("\r\n", "\r"), array("\n", "\n"), $str);
				$encoded = str_replace("\n", $nl, $str);
				if (!StringUtils::endsWith($encoded, $nl))
					$encoded .= $nl;
				break;
			case 'quoted-printable' :
				static $qpChars;
				if (!isset($qpChars))
					$qpChars = array_merge(array(64, 61, 46), range(0, 31), range(127, 255));
				$charset = TypeUtils::ifNull($params['charset'], 'iso-8859-1');
				$replace = array(' ' => '_');
				foreach ($qpChars as $char)
					$replace[chr($char)] = '=' . strtoupper(dechex($char));
				return sprintf("=?%s?Q?%s=", $charset, strtr($str, $replace));
			default:
				$encoded = $str;
				break;
		}
		return $encoded;
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::decode
	// @desc		Decodifica uma string, utilizando um padr�o
	// @param		str string			String a ser decodificada
	// @param		encodeType string	Tipo de codifica��o atual da string
	// @return		string Valor decodificado
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function decode($str, $encodeType) {
		switch(strtolower($encodeType)) {
			case 'base64' :
				$decoded = base64_decode($str);
				break;
			case 'utf8' :
				$decoded = utf8_decode($str);
				break;
			case 'quoted-printable' :
				$decoded = quoted_printable_decode($str);
				break;
			default :
				$decoded = $str;
				break;
		}
		return $decoded;
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::map
	// @desc		Recebe 1+(2*n) par�metros. Para cada dupla A e B, a partir do segundo
	//				par�metro, compara A com o primeiro par�metro. Se forem iguais
	//				o retorno da fun��o ser� B. Se nenhuma dupla possuir valor igual
	//				ao primeiro par�metro, o mesmo � retornado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function map() {
		$argc = func_num_args();
		$argv = func_get_args();
		if ($argc == 0)
			return NULL;
		$base = $argv[0];
		for ($i=1,$s=sizeof($argv); $i<$s; $i+=2) {
			if (array_key_exists($i+1, $argv)) {
				if ($base == $argv[$i])
					return $argv[$i+1];
			} else {
				return $argv[$i];
			}
		}
		return $base;
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::filter
	// @desc 		Filtra uma string retirando o tipo de caractere
	// 				indicado no par�metro 'filterType'
	// @param 		str string			String a ser filtrada
	// @param 		filterType string	"alphanum" Tipo de caractere a ser filtrado
	// @return 		string String sem todas as ocorr�ncias do tipo de caractere solicitado
	// @note 		O tipo de filtro aceita os valores alpha (alfanum�ricos),
	// 				alphalower (alfanum�ricos min�sculos), alphaupper (alfanum�ricos mai�sculos),
	// 				num (n�meros), alphanum (alfanum�ricos e n�meros) e htmlentities (elementos html)
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function filter($str, $filterType='alphanum', $replaceStr='') {
		$replaceStr = strval($replaceStr);
		switch ($filterType) {
			case 'alpha' :
				return (ereg_replace("[^a-zA-Z]", $replaceStr, $str));
			case 'alphalower' :
				return (ereg_replace("[^a-z]", $replaceStr, $str));
			case 'alphaupper' :
				return (ereg_replace("[^A-Z]", $replaceStr, $str));
			case 'num' :
				return (ereg_replace("[^0-9]", $replaceStr, $str));
			case 'alphanum' :
				return (ereg_replace("[^0-9a-zA-Z]", $replaceStr, $str));
			case 'htmlentities' :
				return (ereg_replace("&[[:alnum:]]{0,};", $replaceStr, $str));
			case 'blank' :
				return (ereg_replace("[[:blank:]]{1,}", $replaceStr, $str));
			default :
				return $str;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::escape
	// @desc 		Aplica convers�es em um texto de acordo com um
	// 				dos padr�es de convers�o definidos
	// @param 		str string				Texto a ser processado
	// @param 		conversionType string		"html" Tipo de convers�o para o texto
	// @note 		O tipo de convers�o aceita os valores:
	// 				html (convers�o de caracteres especiais HTML),
	// 				htmlall (convers�o de todos os caracteres para HTML),
	// 				url (codifica��o de url) ou
	// 				quotes (adiciona barras �s haspas simples que n�o possuem barra)
	// @return 		strign Texto convertido segundo o padr�o solicitado
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function escape($str, $conversionType='html') {
		switch ($conversionType) {
			case 'html':
				return htmlspecialchars($str, ENT_QUOTES);
			case 'htmlall' :
				return htmlentities($str, ENT_QUOTES);
			case 'url' :
				return rawurlencode($str);
			case 'quotes' :
				return preg_replace("%(?<!\\\\)'%", "\\'", $str);
			case 'javascript' :
				$expressions = array(
					"/(<scr)(ipt)/i" => "$1\"+\"$2", // quebrar tags "<script"
					'/\\\\/' => '\\\\', // backslashes
					'/\'/' => "\'", // single quotes
					'/"/' => '\\"', // double quotes
					"/\r/"=>'\\r', // caractere CR
					"/\n/"=>'\\n', // caractere LF
					"/\t/" => "\\t" // tabula��es
				);
				$str = str_replace("\\", "\\\\", $str);
				$str = preg_replace(array_keys($expressions), array_values($expressions), $str);
				return $str;
			case 'mail' :
				return str_replace(array('@', '.'), array(' at ', ' dot '), $str);
			default :
				return $str;
		}
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::camelize
	// @desc		Converte uma string para o formato "camel case"
	// @param		str string	String a ser convertida
	// @access		public
	// @return		string
	// @static
	//!-----------------------------------------------------------------
	function camelize($str) {
		return preg_replace("/[_|\s]([a-z0-9])/e", "strtoupper('\\1')", strtolower($str));
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::capitalize
	// @desc		Capitaliza todas as palavras contidas em uma string
	// @param		str string		String base
	// @return		string String com todas as palavras capitalizadas
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function capitalize($str) {
		if (!empty($str)) {
			$w = preg_split("/\s+/", $str);
			for ($i=0, $s=sizeof($w); $i<$s; $i++) {
				if (empty($w[$i]))
					continue;
				$f = strtoupper($w[$i][0]);
				$r = strtolower(substr($w[$i], 1));
				$w[$i] = $f . $r;
			}
			return implode(' ', $w);
		}
		return $str;
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::normalize
	// @desc 		Normaliza uma string substituindo caracteres ASCII
	// 				localizados nas posi��es 192-223 e 224-225 para seus
	// 				correspondentes caracteres 'normais' ->  ������� para aeiAEOO
	// @param 		str string		String base para a substitui��o
	// @return 		string Valor da string normalizado
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function normalize($str) {
		$ts = array("/[�-�]/", "/�/", "/�/", "/[�-�]/", "/[�-�]/", "/�/", "/�/", "/[�-��]/", "/�/", "/[�-�]/", "/�/", "/�/", "/[�-�]/", "/�/", "/�/", "/[�-�]/", "/[�-�]/", "/�/", "/�/", "/[�-��]/", "/�/", "/[�-�]/", "/[�-�]/");
		$tn = array("A", "AE", "C", "E", "I", "D", "N", "O", "X", "U", "Y", "ss", "a", "ae", "c", "e", "i", "d", "n", "o", "x", "u", "y");
		return preg_replace($ts, $tn, $str);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::cutBefore
	// @desc		Retira de uma string os caracteres antes de um determinado token
	// @param		string string	String original
	// @param		token string	Token para pesquisa
	// @return		string Valor processado
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function cutBefore($string, $token, $caseSensitive=TRUE) {
		if (StringUtils::match($caseSensitive ? $string : strtolower($string), $token, $caseSensitive)) {
			return stristr($string, $token);
		}
		return $string;
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::cutLastOcurrence
	// @desc		Remove a por��o de string a partir da �ltima ocorr�ncia
	//				do par�metro $cutOff em $string
	// @param		string string		String original
	// @param		cutOff string		Token para busca e remo��o
	// @param		caseSensitive bool	"TRUE" Sens�vel ou n�o ao caso
	// @return		string Valor processado
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function cutLastOcurrence($string, $cutOff, $caseSensitive=TRUE) {
		if (!StringUtils::match($caseSensitive ? $string : strtolower($string), $cutOff, $caseSensitive))
			return $string;
		else
			return strrev(substr(stristr(strrev($string), strrev($cutOff)),strlen($cutOff)));
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::indent
	// @desc 		Cria indenta��o em um texto utilizando o caractere
	// 				$iChar repetido $nChars vezes
	// @param 		str string		Texto a ser indentado
	// @param		nChars int		Tamanho da indenta��o
	// @param		iChar string	" " Caractere(s) para a indenta��o
	// @return		string String indentada
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function indent($str, $nChars, $iChar=' ') {
		if (!TypeUtils::isInteger($nChars) || $nChars < 1) {
			$nChars = 1;
		}
		return preg_replace('!^!m', str_repeat($iChar, $nChars), $str);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::truncate
	// @desc		Trunca um texto para o tamanho indicado por $length,
	// 				sufixando o resultado com o valor indicado em $truncSufix
	// @param 		str string			Texto a ser truncado
	// @param 		length int			Tamanho desejado do resultado
	// @param 		truncSufix string	"..." Sufixo para o resultado
	// @param 		forceBreak bool		"TRUE" For�ar quebra em palavras longas
	// @return		string String truncada ou a original se n�o exceder o tamanho
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function truncate($str, $length, $truncSufix='...', $forceBreak=TRUE) {
		if (!TypeUtils::isInteger($length) || $length < 1) {
			return '';
		} else {
			if (strlen($str) > $length) {
				$length -= strlen($truncSufix);
        		if (!$forceBreak)
            		$str = preg_replace('/\s+?(\S+)?$/', '', substr($str, 0, $length+1));
				return substr($str, 0, $length) . $truncSufix;
			} else {
				return $str;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::insertChar
	// @desc 		Insere o caractere $char entre cada dupla de caracteres
	// 				encontrados no texto $str
	// @param 		str string		Texto original
	// @param 		char string		" " Caracter ou caracteres para inser��o
	// @param 		stripEmpty bool	"TRUE" Ignorar caracteres vazios
	// @return		string String processada
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function insertChar($str, $char = ' ', $stripEmpty = TRUE) {
		if ($stripEmpty) {
			$strChars = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$strChars = preg_split('//', $str, -1);
		}
		return implode($char, $strChars);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::wrapLine
	// @desc		Reformata uma string ajustando-a para ter um n�mero fixo
	//				de colunas, utilizando a quebra de linha 'breakString'
	//				fornecida
	// @param		str string	Texto original
	// @param		num int		Posi��o de quebra de linha
	// @param		breakString string	"\n" String ou caractere para quebra de linha
	// @return		string Texto formatado
	// @see 		StringUtils::wrap
	// @see 		StringUtils::addLineNumbers
	// @note		Ao contr�rio do m�todo wrap, este m�todo se
	//				aplica a uma s� linha (� suposto que n�o existem quebras
	//				na vari�vel $str fornecida)
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function wrapLine($str, $num, $breakString="\n") {
		$line = '';
		$processed = '';
		$token = strtok($str, ' ');
		while($token) {
			if (strlen($line) + strlen($token) < ($num + 2)) {
				$line .= " $token";
			} else {
				$processed .= "$line$breakString";
				$line = $token;
			}
			$token = strtok(' ');
		}
		$processed .= $line;
		$processed = trim($processed);
		return $processed;
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::wrap
	// @desc 		Quebra em m�ltiplas linhas um texto com 'num'
	// 				caracteres por linha (ou quebras de linha j�
	// 				existentes) utilizando a quebra de linha 'breakString'
	// 				fornecida
	// @param 		str string			Texto original
	// @param 		num int				Posi��o de quebra de linha
	// @param 		breakString string	"\n" String ou caractere para quebra de linha
	// @return 		string Texto formatado com as novas quebras de linha
	// @see 		StringUtils::wrapLine
	// @see 		StringUtils::addLineNumbers
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function wrap($str, $num, $breakString="\n") {
		$str = ereg_replace("([^\r\n])\r\n([^\r\n])", "\\1 \\2", $str);
		$str = ereg_replace("[\r\n]*\r\n[\r\n]*", "\r\n\r\n", $str);
		$str = ereg_replace("[ ]* [ ]*", ' ', $str);
		$str = stripslashes($str);
		$processed = '';
		$paragraphs = explode("\n", $str);
		for ($i=0; $i<sizeof($paragraphs); $i++) {
			$processed .= StringUtils::wrapLine($paragraphs[$i], $num, $breakString) . $breakString;
		}
		$processed = trim($processed);
		return $processed;
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::addLineNumbers
	// @desc 		Adiciona numera��o �s linhas de um texto
	// @param 		&str string				Texto original
	// @param 		start int				"1" Numera��o inicial, padr�o � 1
	// @param 		indent int				"3" Identa��o m�nima da numera��o, padr�o � 3
	// @param 		afterNumberChar string	":" Caractere a ser utilizado ap�s a numera��o, padr�o � ':'
	// @return 		string Texto com numera��o de linhas
	// @see 		StringUtils::wrap
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function addLineNumbers(&$str, $start = 1, $indent = 3, $afterNumberChar = ':', $glue='\n') {
		// divide a string em linhas de um array
		$line = explode("\n", $str);
		$size = sizeof($line);
		// calcula a largura m�xima da numera��o de acordo com o n�mero de linhas
		$width = strlen((string)($start + $size -1));
		$indent = max($width, $indent);
		// gera a numera��o de linhas da string
		for ($i = 0; $i < $size; $i++) {
			$line[$i] = str_pad((string)($i + $start), $indent, ' ', STR_PAD_LEFT) . $afterNumberChar . ' ' . trim($line[$i]);
		}
		return implode($glue, $line);
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::countChars
	// @desc 		Realiza contagem de caracteres em um texto
	// @param 		str string			Texto a ser processado
	// @param 		includeSpaces bool	"FALSE" Incluir espa�os, quebras de linha e tabula��es na contagem
	// @access		public
	// @return 		int
	// @static
	//!-----------------------------------------------------------------
	function countChars($str, $includeSpaces = FALSE) {
		if ($includeSpaces) {
			return strlen($str);
		} else {
			$match = array();
			return preg_match_all('/[^\s]/', $str, $match);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::countWords
	// @desc 		Realiza contagem de palavras em um texto
	// @param 		str string	Texto a ser processado
	// @access		public
	// @return 		int
	// @static
	//!-----------------------------------------------------------------
	function countWords($str) {
		return str_word_count($str);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::countSentences
	// @desc		Conta o n�mero de frases em um texto
	// @param		str string	Texto a ser processado
	// @access		public
	// @return		int
	// @static
	//!-----------------------------------------------------------------
	function countSentences($str) {
		$matches = array();
		return preg_match_all('/[^\s]\.(?!\w)/', $str, $matches);
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::countParagraphs
	// @desc		Conta o n�mero de par�grafos em um texto
	// @param		str string	Texto a ser processado
	// @access		public
	// @return		int
	// @static
	//!-----------------------------------------------------------------
	function countParagraphs($str) {
		return count(preg_split('/[\r\n]+/', $str));
	}

	//!-----------------------------------------------------------------
	// @function	StringUtils::randomString
	// @desc		Gera uma string rand�mica de tamanho $size, incluindo
	//				letras min�sculas, mai�sculas (opcional) e d�gitos (opcional)
	// @param		size int	Tamanho desejado para a string
	// @param		upper bool	"TRUE" Incluir letras mai�sculas A-Z
	// @param		digit bool	"TRUE" Incluir d�gitos
	// @return		string Texto rand�mico com tamanho e parametriza��o definidos
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function randomString($size, $upper=TRUE, $digit=TRUE) {
		$pSize = max(1, $size);
		$start = $digit ? 48 : 65;
		$end = 122;
		$result = '';
		while (strlen($result) < $size) {
			$random = Number::randomize($start, $end);
			if (($digit && $random >= 48 && $random <= 57) ||
				($upper && $random >= 65 && $random <= 90) ||
				($random >= 97 && $random <= 122)) {
				$result .= chr($random);
			}
		}
		return $result;
	}
}
?>