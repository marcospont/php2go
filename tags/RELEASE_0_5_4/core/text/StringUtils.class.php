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
// @desc		Classe que contém funções utilitárias para manipulação
//				de strings. As funções extendem a funcionalidade já oferecida
//				pelo PHP (agrupando e tornando mais prática a utilização)
//				e incluem novas ferramentas não implementadas
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
	// @desc		Retira caracteres brancos à esquerda, à direita e
	// 				todos os espaços em branco duplos
	// @param		str string		String a ser formatada
	// @return 		string Valor do string formatado sem espaços inúteis
	// @see 		StringUtils::stripBlank
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function allTrim($str) {
		return StringUtils::stripBlank(trim($str));
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::stripBlank
	// @desc 		Transforma caracteres brancos ocupando 2 ou mais posições
	// 				em um caractere de espaço simples (ord 32)
	// @param 		str string		String a ser formatada
	// @param		replace string	" " String de substituição
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
	// @param 		chars int		"0" Número de caracteres solicitados
	// @return 		string Quantidade solicitada ou toda a string se o parâmetro
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
	// @param 		chars int		"0" Número de caracteres solicitados
	// @return 		string Quantidade solicitada ou toda a string se o parâmetro
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
	// @desc 		Retorna uma porção interna de uma string indicada
	// 				pelo delimitador de início `startAt` e pelo número
	// 				de caracteres `chars`
	// @param 		str string		String original
	// @param 		startAt int		"1" Posição inicial
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
	// @desc		Retorna o caractere na posição $index do string $str
	// @param		str string	String a ser consultado
	// @param		index int	Índice do caractere buscado
	// @return		string Valor do caractere ou vazio para índices inválidos
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
	// 				em consideração o caso (maiúsculas/minúsculas)
	// @param		str string			String base para a busca
	// @param 		sValue string		Valor a ser buscado
	// @param 		caseSensitive bool	"TRUE" Indica se a busca considera ou não letras maiúsculas/minúsculas
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
	// @desc 		Verifica se o início de uma string corresponde
	// 				ao valor do parâmetro 'slice'
	// @param 		str string			String a ser testada
	// @param 		slice string		Porção de string
	// @param 		caseSensitive bool	"TRUE" Indica se a busca considera ou não letras maiúsculas/minúsculas
	// @param 		ignSpaces bool		"TRUE" Retirar espaços em branco à esquerda para realizar a busca
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
	// 				ao valor do parâmetro 'slice'
	// @param 		str string			String a ser testada
	// @param 		slice string		Porção de string
	// @param 		caseSensitive bool	"TRUE" Indica se a busca considera ou não letras maiúsculas/minúsculas
	// @param 		ignSpaces bool		"TRUE" Retirar espaços em branco à direita para realizar a busca
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
	// @desc 		Verifica se uma string é composta apenas por letras maiúsculas
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
	// @desc 		Verifica se uma string é composta apenas por letras minúsculas
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
	// @desc		Retorna TRUE se a string $value passada por parâmetro for vazia
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
	// @desc		Substitui a string por um valor de substituição caso ela seja vazia
	// @param		value string		Valor original
	// @param		replacement string	Valor de substituição
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
	// @desc 		Insere um valor na posição indicada de uma string
	// @param 		str string		String original
	// @param 		insValue string	"" Valor a ser inserido
	// @param 		insPos int		"0" Posição para inserir a string
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
	// @desc		Wrapper para a função str_replace, utilizado como
	//				modifier de variáveis de template
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
	// @desc		Wrapper para a função preg_replace, utilizado como
	//				modifier de variáveis de template
	// @param		str string			String original
	// @param		pattern string		Pattern no padrão PCRE
	// @param		replacement string	Valor de substituição
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
	//				em pedaços separados por $sep
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
	// @desc		Cria uma string a partir da união dos elementos de um array
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
	// @desc		Codifica uma string de acordo com um padrão
	// @param		str string			String a ser codificada
	// @param		encodeType string	Tipo de codificação
	// @param		params array		"NULL" Vetor de parâmetros ou argumentos para a codificação
	// @return		string String codificada
	// @note		Parâmetros disponíveis por tipo de codificação:
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
	// @desc		Decodifica uma string, utilizando um padrão
	// @param		str string			String a ser decodificada
	// @param		encodeType string	Tipo de codificação atual da string
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
	// @desc		Recebe 1+(2*n) parâmetros. Para cada dupla A e B, a partir do segundo
	//				parâmetro, compara A com o primeiro parâmetro. Se forem iguais
	//				o retorno da função será B. Se nenhuma dupla possuir valor igual
	//				ao primeiro parâmetro, o mesmo é retornado
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
	// 				indicado no parâmetro 'filterType'
	// @param 		str string			String a ser filtrada
	// @param 		filterType string	"alphanum" Tipo de caractere a ser filtrado
	// @return 		string String sem todas as ocorrências do tipo de caractere solicitado
	// @note 		O tipo de filtro aceita os valores alpha (alfanuméricos),
	// 				alphalower (alfanuméricos minúsculos), alphaupper (alfanuméricos maiúsculos),
	// 				num (números), alphanum (alfanuméricos e números) e htmlentities (elementos html)
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
	// @desc 		Aplica conversões em um texto de acordo com um
	// 				dos padrões de conversão definidos
	// @param 		str string				Texto a ser processado
	// @param 		conversionType string		"html" Tipo de conversão para o texto
	// @note 		O tipo de conversão aceita os valores:
	// 				html (conversão de caracteres especiais HTML),
	// 				htmlall (conversão de todos os caracteres para HTML),
	// 				url (codificação de url) ou
	// 				quotes (adiciona barras às haspas simples que não possuem barra)
	// @return 		strign Texto convertido segundo o padrão solicitado
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
					"/\t/" => "\\t" // tabulações
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
	// 				localizados nas posições 192-223 e 224-225 para seus
	// 				correspondentes caracteres 'normais' ->  áéíÁÈÒÖ para aeiAEOO
	// @param 		str string		String base para a substituição
	// @return 		string Valor da string normalizado
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function normalize($str) {
		$ts = array("/[À-Å]/", "/Æ/", "/Ç/", "/[È-Ë]/", "/[Ì-Ï]/", "/Ð/", "/Ñ/", "/[Ò-ÖØ]/", "/×/", "/[Ù-Ü]/", "/Ý/", "/ß/", "/[à-å]/", "/æ/", "/ç/", "/[è-ë]/", "/[ì-ï]/", "/ð/", "/ñ/", "/[ò-öø]/", "/÷/", "/[ù-ü]/", "/[ý-ÿ]/");
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
	// @desc		Remove a porção de string a partir da última ocorrência
	//				do parâmetro $cutOff em $string
	// @param		string string		String original
	// @param		cutOff string		Token para busca e remoção
	// @param		caseSensitive bool	"TRUE" Sensível ou não ao caso
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
	// @desc 		Cria indentação em um texto utilizando o caractere
	// 				$iChar repetido $nChars vezes
	// @param 		str string		Texto a ser indentado
	// @param		nChars int		Tamanho da indentação
	// @param		iChar string	" " Caractere(s) para a indentação
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
	// @param 		forceBreak bool		"TRUE" Forçar quebra em palavras longas
	// @return		string String truncada ou a original se não exceder o tamanho
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
	// @param 		char string		" " Caracter ou caracteres para inserção
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
	// @desc		Reformata uma string ajustando-a para ter um número fixo
	//				de colunas, utilizando a quebra de linha 'breakString'
	//				fornecida
	// @param		str string	Texto original
	// @param		num int		Posição de quebra de linha
	// @param		breakString string	"\n" String ou caractere para quebra de linha
	// @return		string Texto formatado
	// @see 		StringUtils::wrap
	// @see 		StringUtils::addLineNumbers
	// @note		Ao contrário do método wrap, este método se
	//				aplica a uma só linha (é suposto que não existem quebras
	//				na variável $str fornecida)
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
	// @desc 		Quebra em múltiplas linhas um texto com 'num'
	// 				caracteres por linha (ou quebras de linha já
	// 				existentes) utilizando a quebra de linha 'breakString'
	// 				fornecida
	// @param 		str string			Texto original
	// @param 		num int				Posição de quebra de linha
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
	// @desc 		Adiciona numeração às linhas de um texto
	// @param 		&str string				Texto original
	// @param 		start int				"1" Numeração inicial, padrão é 1
	// @param 		indent int				"3" Identação mínima da numeração, padrão é 3
	// @param 		afterNumberChar string	":" Caractere a ser utilizado após a numeração, padrão é ':'
	// @return 		string Texto com numeração de linhas
	// @see 		StringUtils::wrap
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function addLineNumbers(&$str, $start = 1, $indent = 3, $afterNumberChar = ':', $glue='\n') {
		// divide a string em linhas de um array
		$line = explode("\n", $str);
		$size = sizeof($line);
		// calcula a largura máxima da numeração de acordo com o número de linhas
		$width = strlen((string)($start + $size -1));
		$indent = max($width, $indent);
		// gera a numeração de linhas da string
		for ($i = 0; $i < $size; $i++) {
			$line[$i] = str_pad((string)($i + $start), $indent, ' ', STR_PAD_LEFT) . $afterNumberChar . ' ' . trim($line[$i]);
		}
		return implode($glue, $line);
	}

	//!-----------------------------------------------------------------
	// @function 	StringUtils::countChars
	// @desc 		Realiza contagem de caracteres em um texto
	// @param 		str string			Texto a ser processado
	// @param 		includeSpaces bool	"FALSE" Incluir espaços, quebras de linha e tabulações na contagem
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
	// @desc		Conta o número de frases em um texto
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
	// @desc		Conta o número de parágrafos em um texto
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
	// @desc		Gera uma string randômica de tamanho $size, incluindo
	//				letras minúsculas, maiúsculas (opcional) e dígitos (opcional)
	// @param		size int	Tamanho desejado para a string
	// @param		upper bool	"TRUE" Incluir letras maiúsculas A-Z
	// @param		digit bool	"TRUE" Incluir dígitos
	// @return		string Texto randômico com tamanho e parametrização definidos
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