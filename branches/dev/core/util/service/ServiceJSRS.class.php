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
// $Header: /www/cvsroot/php2go/core/util/service/ServiceJSRS.class.php,v 1.6 2006/10/11 22:48:07 mpont Exp $
// $Date: 2006/10/11 22:48:07 $

//-----------------------------------------
import('php2go.util.Callback');
//-----------------------------------------

//!-----------------------------------------------------------------
// @class		ServiceJSRS
// @desc		A classe ServiceJSRS é responsável por configurar e executar
//				tratadores de eventos JSRS (JavaScript Remote Scripting). Através
//				dela, é possível registrar tratadores (funções, métodos estáticos
//				de classe ou métodos de objetos) e devolver para o cliente o
//				retorno produzido pelo tratador
// @package		php2go.util.service
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class ServiceJSRS extends PHP2Go
{
	var $handlers = array();	// @var handlers array	"array()" Conjunto de handlers registrados e válidos
	var $request = array();		// @var request array	"array()" Armazena a última requisição tratada
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::ServiceJSRS
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function ServiceJSRS() {
		parent::PHP2Go();
	}
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::&getInstance
	// @desc		Retorna um singleton da classe ServiceJSRS
	// @return		ServiceJSRS object
	// @access		public	
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new ServiceJSRS();
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::registerHandler
	// @desc		Registra um novo tratador de evento
	// @param		handler mixed	Tratador
	// @param		alias string	"NULL" Alias para o tratador
	// @note		O tratador deve ser um callback válido: função procedural,
	//				método estático de classe ou um array contendo objeto+método
	// @note		O parâmetro $alias pode ser utilizado para definir um alias que
	//				será utilizado na definição XML do formulário para referenciar o tratador
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function registerHandler($handler, $alias=NULL) {
		$Callback = new Callback();
		$Callback->throwErrors = FALSE;
		$Callback->setFunction($handler);
		$valid = $Callback->isValid();
		if ($valid) {
			if (empty($alias))
				$alias = ($Callback->isType(CALLBACK_FUNCTION) ? $handler : $handler[1]);
			$this->handlers[$alias] = $Callback;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::handleRequest
	// @desc		Verifica se existe uma chamada de evento na requisição,
	//				buscando o tratador apropriado se ele existir e retornando
	//				o resultado do tratador como resposta da requisição
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function handleRequest() {
		if ($this->_parseRequest()) {
			if (empty($this->request['handlerId'])) {
				$this->_buildErrorResponse(PHP2Go::getLangVal('ERR_JSRS_MISSING_HANDLER'));
			} elseif (array_key_exists($this->request['handlerId'], $this->handlers)) {
				$handler =& $this->handlers[$this->request['handlerId']];
				$result = $handler->invoke($this->request['handlerArgs'], TRUE);
				if (!empty($result))
					$this->_buildResponse((string)$result);
				else
					$this->_buildResponse('');
			} else {
				$this->_buildErrorResponse(PHP2Go::getLangVal('ERR_JSRS_INVALID_HANDLER', $this->request['handlerId']));
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::arrayToString
	// @desc		Transforma um array de uma ou duas dimensões em uma string
	//				que possa ser devolvida como retorno de uma função JSRS
	// @access		public
	// @param		arr array		Array a ser processado
	// @param		lineSep string	"|" Separador de linhas
	// @param		colSep string	"~" Separador de colunas
	// @return		string Array convertido em string
	// @static
	//!-----------------------------------------------------------------
	function arrayToString($arr, $lineSep='|', $colSep='~') {
		$arr = (array)$arr;
		if (empty($arr))
			return '';
		$lines = array();
		foreach ($arr as $line) {
			$lines[] = (TypeUtils::isArray($line) ? implode($colSep, $line) : $line);
		}
		return implode($lineSep, $lines);
	}
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::debugEnabled
	// @desc		Verifica se o debug está habilitado na requisição JSRS
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function debugEnabled() {
		return (!empty($_REQUEST['C']) && @$_REQUEST['D'] == 1);
	}
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::_parseRequest
	// @desc		Detecta se existe ID de contexto, nome de tratador e conjunto
	//				de argumentos na requisição
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _parseRequest() {
		$context = (isset($_REQUEST['C']) ? $_REQUEST['C'] : '');
		$handler = (isset($_REQUEST['F']) ? $_REQUEST['F'] : '');
		$args = array();
		$i = 0;
		while (isset($_REQUEST['P'.$i])) {
			$argument = $_REQUEST['P'.$i];
			$args[] = substr($argument, 1, strlen($argument)-2);
			$i++;
		}
		if (!empty($context)) {
			$this->request = array(
				'contextId' => $context,
				'handlerId' => $handler,
				'handlerArgs' => $args
			);
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::_buildResponse
	// @desc		Constrói e exibe o código HTML da resposta da requisição,
	//				contendo o retorno produzido pelo tratador do evento
	// @param		payload string	Retorno do tratador
	// @access		private	
	// @return		void
	//!-----------------------------------------------------------------
	function _buildResponse($payload) {
		print ("<html><head></head><body onload=\"p=document.layers?parentLayer:window.parent;p.jsrsLoaded('" . $this->request['contextId'] . "');\">jsrsPayload:<br>" . "<form name=\"jsrs_Form\"><textarea name=\"jsrs_Payload\">" . $this->_escapeResponse($payload) . "</textarea></form></body></html>");
		exit();		
	}
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::_buildErrorResponse
	// @desc		Produz uma resposta contendo a descrição de um erro encontrado
	//				no processamento do evento
	// @param		str string	Mensagem de erro
	// @access		private	
	// @return		void
	//!-----------------------------------------------------------------
	function _buildErrorResponse($str) {
		$cleanStr = ereg_replace("\'", "\\'", $str);
		$cleanStr = "jsrsError: " . ereg_replace("\"", "\\\"", $cleanStr);
		print ("<html><head></head><body " . "onload=\"p=document.layers?parentLayer:window.parent;p.jsrsError('" . $this->request['contextId'] . "','" . $str . "');\">" . $cleanStr . "</body></html>");
		exit();		
	}
	
	//!-----------------------------------------------------------------
	// @function	ServiceJSRS::_escapeResponse
	// @desc		Formata o código HTML de retorno do tratador para converter
	//				caracteres especiais e barras invertidas
	// @access		private
	// @param		code string	Código HTML de retorno
	//!-----------------------------------------------------------------		
	function _escapeResponse($code) {
		$tmp = ereg_replace("&", "&amp;", $code);
		return ereg_replace("\/" , "\\/", $tmp);
	}
}
?>