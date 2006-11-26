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
// $Header: /www/cvsroot/php2go/p2gLib.php,v 1.42 2006/10/26 04:43:19 mpont Exp $
// $Date: 2006/10/26 04:43:19 $
// $Revision: 1.42 $

//!------------------------------------------------------------------
// @function	import
// @desc		Importa o(s) arquivo(s) correspondente(s) ao caminho fornecido
// @param		path string			Caminho para a(s) classe(s) ou arquivo(s)
// @param		extension string	"class.php" Extensão
// @param		isClass bool		"TRUE" O caminho representa uma classe ou conjunto de classes
// @return		bool
//!------------------------------------------------------------------
function import($modulePath, $extension='class.php', $isClass=TRUE) {
	$Loader =& ClassLoader::getInstance();
	return $Loader->importPath($modulePath, $extension, $isClass);
}

//!------------------------------------------------------------------
// @function	importFile
// @desc		Importa um arquivo simples, a partir de seu caminho completo
// @param		filePath string	Caminho do arquivo
// @return		bool
//!------------------------------------------------------------------
function importFile($filePath) {
	$Loader =& ClassLoader::getInstance();
	return $Loader->loadFile($filePath);
}

//!------------------------------------------------------------------
// @function	includeFile
// @desc		Inclui um arquivo a partir de seu caminho completo
// @param		filePath string	Caminho do arquivo
// @param		return bool		"FALSE" Indica se o conteúdo da inclusão deve ser retornado
// @note		Ao contrário da função importFile, a função includeFile replica
//				arquivos já carregados (utiliza a função include do PHP)
// @return		bool
//!------------------------------------------------------------------
function includeFile($filePath, $return=FALSE) {
	if ($return === TRUE) {
		return (include($filePath));
	} else {
		if (!@include($filePath)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
}

//!--------------------------------------------------------------
// @function	classForPath
// @desc		Importa e retorna o nome de uma classe a partir de seu caminho
//				no padrão PHP2Go (pontos como separadores)
// @param		path string		Caminho da classe
// @return		string Nome da classe ou FALSE se não for possível importá-la
// @note		Exemplo: para o caminho myproject.person.Person, deve retornar Person
//!--------------------------------------------------------------
function classForPath($path) {
	import($path);
	$className = basename(str_replace('.', '/', $path));
	if (class_exists($className))
		return $className;
	return FALSE;
}

//!------------------------------------------------------------------
// @function	__autoload
// @desc		Função chamada automaticamente pelo PHP5 no momento em que
//				uma classe não encontrada é instanciada. Esta função utiliza
//				internamente o método loadFile da classe ClassLoader para
//				incluir a classe desejada
// @param		className string	Nome da classe
// @return		void
//!------------------------------------------------------------------
function __autoload($className) {
	$Lang =& LanguageBase::getInstance();
	$Loader =& ClassLoader::getInstance();
	$fileName = (isset($Loader->importClassCache[$className]) ? $Loader->importClassCache[$className] : $className . '.class.php');
	if (!$Loader->loadFile($fileName)) {
		trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_MODULE'), $className), E_USER_ERROR);
	}
}

//!--------------------------------------------------------------
// @function	getPhp2GoOffset
// @desc		Calcula o caminho que deve ser seguido para chegar à raiz
//				do PHP2Go a partir da pasta atual
// @return		string Caminho calculado
//!--------------------------------------------------------------
function getPhp2GoOffset() {
	// host + port from p2g absolute url
	$matches = array();
	preg_match("~(https?://)([^/]+)/?(.*)?~", substr(PHP2GO_ABSOLUTE_PATH, 0, -1), $matches);
	$p2gSrvName = @$matches[2];
	// host + port from application url
	$appSrvName = @$_SERVER['SERVER_NAME'] . (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '');
	if (!empty($p2gSrvName) && $p2gSrvName != $appSrvName)
		return FALSE;
	$path1 = strval(@$matches[3]);
	$path2 = substr($_SERVER['PHP_SELF'], 1);
	$matches1 = NULL;
	$matches2 = NULL;
	$equal = TRUE;
	$back = '';
	$forward = '';
	while ($path1 != '' || $path2 != '') {
		$matches1 = array();
		$matches2 = array();
		$res1 = preg_match("~^([^/]+)(/)?(.*)?$~", $path1, $matches1);
		$res2 = preg_match("~^([^/]+)(/)?(.*)?$~", $path2, $matches2);
		if (@$matches1[1] != @$matches2[1] || !$equal) {
			if (@$matches2[2] == '/')
				$back .= '../';
			if (@$matches1[1] != '')
				$forward .= @$matches1[1] . @$matches1[2];
			$equal = FALSE;
		}
		$path1 = @$matches1[3];
		$path2 = @$matches2[3];
	}
	$finalPath = $back . $forward;
	if (substr($finalPath, -1) != '/')
		$finalPath .= '/';
	return $finalPath;
}

//!-----------------------------------------------
// @function	jsrsDispatch
// @desc		Configura tratadores de eventos JSRS, utilizando
//				a classe ServiceJSRS. Função criada para manter compatiblidade
//				com a bibiliteca Jsrs.lib.php, que foi removida do PHP2Go
// @param		handlersList string	Lista de tratadores, separada por espaços simples
// @return		void
//!-----------------------------------------------
function jsrsDispatch($handlersList) {
	import('php2go.util.service.ServiceJSRS');
	$Service = new ServiceJSRS();
	$handlersList = trim((string)$handlersList);
	$handlers = explode(' ', $handlersList);
	foreach ($handlers as $handler)
		$Service->registerHandler($handler);
	$Service->handleRequest();
}

//!-----------------------------------------------
// @function	destroyPHP2GoObjects
// @desc		Destrói os objetos criados cujos destrutores foram
//				registrados no escopo global e executa as funções
//				de shutdown registradas pelo usuário
// @return		void
// @note		Esta função é registrada como shutdown_function
//				na inicialização global definida em p2gConfig.php
//!-----------------------------------------------
function destroyPHP2GoObjects() {
	global $PHP2Go_destructor_list, $PHP2Go_shutdown_funcs;
	import('php2go.util.TypeUtils');
	if (is_array($PHP2Go_destructor_list) && !empty($PHP2Go_destructor_list)) {
		foreach($PHP2Go_destructor_list as $destructor) {
			$object =& $destructor[0];
			$method = $destructor[1];
			$object->$method();
			unset($object);
		}
	}
	if (is_array($PHP2Go_shutdown_funcs) && !empty($PHP2Go_shutdown_funcs)) {
		foreach($PHP2Go_shutdown_funcs as $function) {
			if (sizeof($function) == 3) {
				$object =& $function[0];
				$method = $function[1];
				$args = implode(',', $function[2]);
				eval("\$object->$method($args);");
			} else {
				call_user_func_array($function[0], $function[1]);
			}
		}
	}
}

//!-----------------------------------------------
// @function	__
// @desc		Função especial para resolução de entradas
//				de linguagem, permitindo um conjunto arbitrário
//				de valores de substituição
// @return		string
//!-----------------------------------------------
function __() {
	$args = func_get_args();
	if (sizeof($args) < 1) {
		return NULL;
	} else {
		$base = array_shift($args);
		return PHP2Go::getLangVal($base, $args, FALSE);
	}
}

//!-----------------------------------------------
// @function	__v
// @desc		Função utilizada pela engine de templates para
//				imprimir valores de variáveis. Componentes
//				serão impressos através de seus métodos display()
// @param		&val mixed	Valor da variável
// @return		void
//!-----------------------------------------------
function __v(&$val) {
	if (is_object($val) && is_subclass_of($val, (IS_PHP5 ? 'Component' : 'component')))
		$val->display();
	else
		print $val;
}

//!-----------------------------------------------
// @function	println
// @desc		Função utilitária para exibição de uma string
//				seguida de uma quebra de linha
// @param		str string	String a ser exibida
// @param		nl string	"&lt;BR&gt;" Quebra de linha
// @return		void
//!-----------------------------------------------
function println($str, $nl='<br>') {
	echo $str . $nl;
}

//!-----------------------------------------------
// @function	dumpVariable
// @desc		Imprime a descrição de uma variável utilizando
//				pré-formatação, para melhor visualização
// @param		var mixed	Variável
// @return		void
//!-----------------------------------------------
function dumpVariable($var) {
	print '<pre>';
	var_dump($var);
	print '</pre>';
}

//!-----------------------------------------------
// @function	dumpArray
// @desc		Retorna ou imprime a forma apresentável
//				da primeira dimensão de um array. Útil
//				para a validação de arrays associativos
//				(hash tables) ou unidimensionais utilizados
//				nas aplicações
// @param		arr array	Variável a ser exibida
// @param		return bool	"TRUE" Retornar ou imprimir o conteúdo do array
// @param		stringLimit int "200" Limite de caracteres para entradas do tipo string
// @return		string Retorna o conteúdo do array, se o parâmetro $return for TRUE
//!-----------------------------------------------
function dumpArray($arr, $return=TRUE, $stringLimit=200, $deep=FALSE) {
	$r = array();
	foreach ($arr as $k => $v) {
		if (is_string($v)) {
			$r[] = $k . "=>'" . (strlen($v) > $stringLimit ? substr($v, 0, $stringLimit) . "...(" . strlen($v) . ")" : $v) . "'";
		} elseif ($deep && (is_array($v) || is_object($v))) {
			(is_object($v)) && ($v = get_object_vars($v));
			$r[] = $k . '=>' . dumpArray($v, TRUE, $stringLimit, TRUE);
		} else {
			$r[] = $k . '=>' . $v;
		}
	}
	if ($return)
		return "[" . implode(", ", $r) . "]";
	print "[" . implode(", ", $r) . "]";
	return TRUE;
}

//!-----------------------------------------------
// @function	exportVariable
// @desc		Retorna a descrição de uma variável, utilizando
//				ou não pré-formatação
// @param		var mixed		Variável
// @param		formatted bool	"FALSE" Utilizar pré-formatação
// @return		string Conteúdo exportado da variável
//!-----------------------------------------------
function exportVariable($var, $formatted=FALSE) {
	if (is_object($var) && !System::isPHP5() && method_exists($var, '__tostring'))
		$export = $var->__toString();
	else
		$export = var_export($var, TRUE);
	if ($formatted)
		return '<pre>' . $export . '</pre>';
	else
		return $export;
}

//!-----------------------------------------------
// @function	consumeArray
// @desc		Retorna o valor de uma chave de um array associativo,
//				retirando-a do array em caso de sucesso
// @param		&array array	Array
// @param		key string		Chave pesquisada
// @return		mixed
//!-----------------------------------------------
function consumeArray(&$array, $key) {
	if (is_array($array)) {
		if (array_key_exists($key, $array)) {
			$return = $array[$key];
			unset($array[$key]);
			return $return;
		}
	}
	return NULL;
}

//!-----------------------------------------------
// @function	findArrayPath
// @desc		Procura por um valor em um array multidimensional
//				utilizando um "caminho" de chaves, utilizando um separador
// @param		arr array			Array multidimensional
// @param		path string			Caminho de pesquisa
// @param		separator string	"." Separador de níveis no caminho fornecido
// @param		fallback mixed		"NULL" Valor retornado caso o caminho não seja encontrado
// @return		mixed Valor encontrado ou valor do parâmetro $fallback
//!-----------------------------------------------
function findArrayPath($arr, $path, $separator='.', $fallback=NULL) {
	if (!is_array($arr))
		return $fallback;
	$parts = explode($separator, $path);
	if (sizeof($parts) == 1) {
		return (isset($arr[$path]) ? $arr[$path] : $fallback);
	} else {
		$i = 0;
		$base = $arr;
		$size = sizeof($parts);
		while ($i < $size) {
			if (!isset($base[$parts[$i]]))
				return $fallback;
			else
				$base = $base[$parts[$i]];
			if ($i < ($size-1) && !is_array($base))
				return $fallback;
			$i++;
		}
		return $base;
	}
}

//!-----------------------------------------------
// @function	highlightPHP
// @desc		Atalho para as funções de syntax highlight
//				do PHP. highlight_string é executada quando
//				$type==T_BYVAR. highlight_file é executada
//				quando $type==T_BYFILE
// @param		code string		Trecho de código no formato string ou caminho para um arquivo
// @param		type int		"T_BYVAR" T_BYVAR: parâmetro do tipo string, T_BYFILE: parâmetro do tipo arquivo
// @return		string Código com highlight de sintaxe
//!-----------------------------------------------
function highlightPHP($code, $type=T_BYVAR) {
	if ($type == T_BYFILE)
		return highlight_file($code, TRUE);
	else
		return highlight_string((string)$code, TRUE);
}

//!-----------------------------------------------------------------
// @function	resolveBooleanChoice
// @desc		Define o valor das escolhas booleanas utilizadas em atributos
//				das especificações XML de formulários e relatórios. O valor T
//				é mapeado para true, F para false e outros valores para null
// @param		value mixed			"NULL" Valor do atributo
// @return		mixed
//!-----------------------------------------------------------------
function resolveBooleanChoice($value=NULL) {
	if (TypeUtils::isNull($value))
		return NULL;
	elseif (trim($value) == "T")
		return TRUE;
	elseif (trim($value) == "F")
		return FALSE;
	return NULL;
}

//!-----------------------------------------------------------------
// @function	resolveI18nEntry
// @desc		Método utilizado para resolver entradas de linguagem
//				referenciadas em atributos nas especificações XML
//				de formulários e relatórios
// @param		value string	Valor do atributo
// @return		mixed
//!-----------------------------------------------------------------
function resolveI18nEntry($value) {
	if (!empty($value) && preg_match(PHP2GO_I18N_PATTERN, $value, $matches))
		return PHP2Go::getLangVal($matches[1]);
	return $value;
}
?>