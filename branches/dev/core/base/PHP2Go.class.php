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
// $Header: /www/cvsroot/php2go/core/base/PHP2Go.class.php,v 1.32 2006/10/26 04:26:50 mpont Exp $
// $Date: 2006/10/26 04:26:50 $

$GLOBALS['PHP2Go_destructor_list'] = array();
$GLOBALS['PHP2Go_shutdown_funcs'] = array();

//!-----------------------------------------------------------------
// @class 		PHP2Go
// @desc 		Objeto principal, do qual todos são subordinados. É uma emulação
//				da classe java.lang.Object, e possui métodos genéricos para execução
//				de operações de comparação e serialização dos objetos criados. A classe
//				PHP2Go também provê acesso à base de linguagem e à tabela de configuração
//				ativa, além de ser a via de acesso para a classe de tratamento de erros
// @package		php2go.base
// @uses 		PHP2GoError
// @uses		TypeUtils
// @author 		Marcos Pont
// @version		$Revision: 1.32 $
//!-----------------------------------------------------------------
class PHP2Go
{
	//!-----------------------------------------------------------------
	// @function 	PHP2Go::PHP2Go
	// @desc		Construtor da classe
	// @access 		public
	//!-----------------------------------------------------------------
	function PHP2Go() {
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::getObjectName
	// @desc 		Retorna o nome da classe
	// @return 		string Retorna o nome do objeto atual
	// @access 		public
	// @deprecated
	//!-----------------------------------------------------------------
	function getObjectName() {
		return get_class($this);
	}

	//!-----------------------------------------------------------------
	// @function	PHP2Go::getClassName
	// @desc		Retorna o nome da classe do objeto
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getClassName() {
		return get_class($this);
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::getParentName
	// @desc		Retorna o nome da classe superior à atual, se existir
	// @return 		string Nome da classe pai ou vazio se não possuir
	// @access 		public
	//!-----------------------------------------------------------------
	function getParentName() {
		return get_parent_class($this);
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::isA
	// @desc 		Verifica se o objeto é uma instância de uma determinada classe
	// @param 		className string	Nome da classe
	// @param		recurse bool		"TRUE"	Buscar também nas classes superiores, se existirem
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isA($className, $recurse=TRUE) {
		$thisClass = get_class($this);
		$otherClass = (System::isPHP5() ? $className : strtolower($className));
		if ($recurse)
			return ($thisClass == $otherClass || is_subclass_of($this, $otherClass));
		return ($thisClass == $otherClass);
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::isSubclassOf
	// @desc 		Verifica se o objeto atual é descendente da classe
	// 				indicada no parâmetro $className
	// @param 		className string	Nome da classe superior
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isSubclassOf($className) {
		return is_subclass_of($this, $className);
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::equals
	// @desc 		Verifica se dois objetos são iguais
	// @param 		object (object)		Objeto para comparação
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function equals($object) {
		if (is_object($object) && (serialize($this) == serialize($object)))
			return TRUE;
		else
			return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::store
	// @desc 		Serializa o objeto em um arquivo armazenado no servidor
	// @param		path string		"" Caminho onde o arquivo deve ser gravado. Por padrão, utiliza o tempdir do servidor
	// @return 		string Nome do arquivo gerado se houver sucesso na operação ou FALSE em caso contrário
	// @see 		PHP2Go::retrieve()
	// @access 		public
	//!-----------------------------------------------------------------
	function store($path='') {
		$filePath = ($path != '' ? tempnam($path, 'php2go_') : tempnam(System::getTempDir(), 'php2go_'));
		$objData = serialize($this);
		if ($filePath != "" && $objData) {
			if ($fp = @fopen($filePath, "wb")) {
				fwrite($fp, $objData);
				@fclose($fp);
				return $filePath;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::retrieve
	// @desc 		Recupera o objeto serializado indicado pelo nome
	// 				de arquivo 'objFile'
	// @param 		objFile string	Nome do arquivo onde o objeto foi serializado
	// @return 		string O objeto serializado ou dispara um erro caso o
	// 				arquivo não possa ser aberto para leitura
	// @access 		public
	//!-----------------------------------------------------------------
	function retrieve($objFile) {
		$ptr = @fopen($objFile, 'rb');
		if ($ptr) {
			$objData = fread($ptr, filesize($objFile));
			return unserialize($objData);
		}
		PHP2Go::raiseError($this->getLangVal('ERR_CANT_FIND_SERIALIZATION_FILE', $objFile), E_USER_ERROR, __FILE__, __LINE__);
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	PHP2Go::cloneObject
	// @desc		Cria uma cópia do objeto
	// @access		public
	// @return		object
	//!-----------------------------------------------------------------
	function cloneObject() {
		return $this;
	}

	//!-----------------------------------------------------------------
	// @function	PHP2Go::hashCode
	// @desc		Retorna uma representação hash do objeto atual
	// @return		string Código hexadecimal do hash code do objeto
	//!-----------------------------------------------------------------
	function hashCode() {
		return bin2hex(mhash(MHASH_CRC32, serialize($this)));
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::__toString
	// @desc 		Constrói uma representação string do objeto
	// @access 		public
	// @return 		string
	//!-----------------------------------------------------------------
	function __toString() {
		ob_start();
		var_dump($this);
		return ob_get_clean();
	}

	//!-----------------------------------------------------------------
	// @function	PHP2Go::logError
	// @desc		Grava em um arquivo de log uma mensagem de erro
	// @access		public
	// @param		logFile string		Caminho completo do arquivo de log
	// @param 		msg string			Mensagem do Erro
	// @param 		type int			"E_USER_ERROR" Tipo do erro, segundo a especificação de erros do PHP
	// @param 		userFile string		"" Arquivo onde o erro ocorreu
	// @param 		userLine string		"NULL" Linha onde o erro ocorreu
	// @param		extra string		"" Mensagem detalhada ou complementar do erro
	// @return		void
	// @note 		Se os parâmetros $userFile e $userLine forem omitidos,
	// 				o erro será reportado como tendo ocorrido no arquivo
	// 				da classe PHP2GoError, onde a função error_log é
	// 				executada
	// @static
	//!-----------------------------------------------------------------
	function logError($logFile, $msg, $type=E_USER_ERROR, $userFile='', $userLine=NULL, $extra='') {
		$Error =& PHP2GoError::getInstance();
		if (isset($this))
			$Error->setObject($this);
		$Error->setMessage($msg, $extra);
		$Error->setType($type);
		$Error->setFile($userFile);
		$Error->setLine($userLine);
		$Error->log($logFile);
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::raiseError
	// @desc		Dispara um erro ocorrido na aplicação
	// @access 		public
	// @param 		msg string			Mensagem do Erro
	// @param 		type int			"E_USER_ERROR" Tipo do erro, segundo a especificação de erros do PHP
	// @param 		userFile string		"" Arquivo onde o erro ocorreu
	// @param 		userLine int		"NULL" Linha onde o erro ocorreu
	// @param		extra string		"" Mensagem detalhada ou complementar do erro
	// @return 		void
	// @note 		Se os parâmetros $userFile e $userLine forem omitidos,
	// 				o erro será reportado como tendo ocorrido no arquivo
	// 				da classe PHP2GoError, onde a função trigger_error é
	// 				executada
	// @static
	//!-----------------------------------------------------------------
	function raiseError($msg, $type=E_USER_ERROR, $userFile='', $userLine=NULL, $extra='') {
		$Error = new PHP2GoError();
		if (isset($this))
			$Error->setObject($this);
		$Error->setMessage($msg, $extra);
		$Error->setType($type);
		$Error->setFile($userFile);
		$Error->setLine($userLine);
		$Error->raise();
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::getConfigVal
	// @desc 		Verifica se uma variável está corretamente setada
	// 				nas configurações do usuário e retorna o seu valor
	//				se ela existir
	// @access 		public
	// @param 		variable string		Variável solicitada
	// @param 		throwError bool		"TRUE" Flag para exibir o erro em caso de variável não encontrada
	// @param		acceptEmpty bool	"TRUE" Flag que indica se valores vazios de configuração devem ser aceitos
	// @return 		string Valor da variável ou dispara erro se não encontrar
	// @note		Se a variável $throwError estiver setada para TRUE,
	//				exibe um erro se a variável buscada não for encontrada
	//!-----------------------------------------------------------------
	function getConfigVal($variable, $throwError = TRUE, $acceptEmpty = TRUE) {
		$Conf =& Conf::getInstance();
		$value = $Conf->getConfig($variable);
		if ($value == FALSE) {
			if ($throwError) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_CFG_VAL', $variable), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			return "";
		} elseif (empty($value) && !$acceptEmpty) {
			if ($throwError) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_CFG_VAL', $variable), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			return $value;
		} else {
			return $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::getLangVal
	// @desc 		Busca um valor na tabela de linguagem ativa
	// @access 		public
	// @param 		entryName string	Nome da entrada do dicionário
	// @param 		bindVars array		"NULL" Variável ou conjunto de variáveis de substituição para a mensagem
	// @param 		throwError bool		"TRUE" Flag para disparar erro em caso de valor não encontrado
	// @return 		string Mensagem ou valor do dicionário
	// @note 		Se o valor de $throwError for FALSE, a função retorna
	// 				uma string vazia ao invés de disparar um erro
	// @see 		PHP2Go::getConfigVal
	//!-----------------------------------------------------------------
	function getLangVal($entryName, $bindVars=NULL, $throwError=TRUE) {
		$Lang =& LanguageBase::getInstance();
		$value = $Lang->getLanguageValue($entryName, $bindVars);
		if ($value === NULL) {
			$value = '';
			if ($throwError)
				PHP2Go::raiseError("Can't find language entry <b>'{$entryName}'</b>", E_USER_ERROR, __FILE__, __LINE__);
		}
		return $value;
	}

	//!-----------------------------------------------------------------
	// @function 	PHP2Go::registerDestructor
	// @desc 		Registra um método destrutor para um objeto
	// @param 		&object string		Objeto
	// @param 		methodName string	Destrutor
	// @access 		public
	// @return 		void
	// @static
	//!-----------------------------------------------------------------
	function registerDestructor(&$object, $methodName) {
		if (!System::isPHP5() || $methodName != '__destruct') {
			global $PHP2Go_destructor_list;
			if (is_object($object) && method_exists($object, $methodName)) {
				$newItem[0] =& $object;
				$newItem[1] = $methodName;
				$PHP2Go_destructor_list[] =& $newItem;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	PHP2Go::hasDestructor
	// @desc		Verifica se um método destrutor já foi registrado
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function hasDestructor($methodName) {
		global $PHP2Go_destructor_list;
		foreach($PHP2Go_destructor_list as $destructor) {
			if ($destructor[1] == $methodName)
				return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	PHP2Go::registerShutdownFunc
	// @desc		Registra uma função ou método de objeto na tabela de
	//				funções a serem executadas no término do script
	// @param		function mixed	Nome da função ou vetor (objeto,método) a ser inserido na tabela de funções
	// @param		args array		"array()" Vetor de argumentos para a função ou método
	// @return 		void
	// @static
	//!-----------------------------------------------------------------
	function registerShutdownFunc($function, $args=array()) {
		global $PHP2Go_shutdown_funcs;
		if (is_array($function) && sizeof($function) == 2) {
			if (is_object($function[0]) && method_exists($function[0], $function[1])) {
				$newItem[0] = &$function[0];
				$newItem[1] = $function[1];
				$newItem[2] = $args;
				$PHP2Go_shutdown_funcs[] = $newItem;
			} else {
				$PHP2Go_shutdown_funcs[] = array($function, $args);
			}
		} else {
			$PHP2Go_shutdown_funcs[] = array($function, $args);
		}
	}

	//!-----------------------------------------------------------------
	// @function	PHP2Go::generateUniqueID
	// @desc		Método utilitário para a geração de um ID único
	// @access		public
	// @return		string ID único
	// @static
	//!-----------------------------------------------------------------
	function generateUniqueId($prefix='php2go_') {
		static $uniqueId;
		static $uniqueIdPrefix;
		if ((string)$prefix == "") {
			if (!isset($uniqueId))
				$uniqueId = 1;
			else
				$uniqueId++;
			return $uniqueId;
		} else {
			if (!isset($uniqueIdPrefix))
				$uniqueIdPrefix = array();
			if (!isset($uniqueIdPrefix[$prefix]))
				$uniqueIdPrefix[$prefix] = 1;
			else
				$uniqueIdPrefix[$prefix]++;
			return $prefix . $uniqueIdPrefix[$prefix];
		}
	}
}
?>