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
// $Header: /www/cvsroot/php2go/core/form/listener/FormAjaxListener.class.php,v 1.4 2006/11/19 18:34:18 mpont Exp $
// $Date: 2006/11/19 18:34:18 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormAjaxListener
// @desc		Esta classe constrѓi funчѕes que criam, configuram e
//				executam requests utilizando as bibliotecas AJAX do
//				framework Javascript incluэdo no PHP2Go. Os parтmetros
//				para a requisiчуo podem ser definidos diretamente na
//				especificaчуo do XML, utilizando elementos do tipo
//				"param" ou diretamente por cѓdigo JS
// @extends		FormEventListener
// @package		php2go.form.listener
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class FormAjaxListener extends FormEventListener
{
	var $url;				// @var url string				URL da requisiчуo
	var $class;				// @var class string			Classe AJAX
	var $formSubmit;		// @var formSubmit bool			Flag indicativo de submissуo de formulсrio
	var $params;				// @var params array				Parтmetros AJAX
	var $paramsFuncBody;		// @var paramsFuncBody string	Funчуo de montagem dos parтmetros AJAX

	//!-----------------------------------------------------------------
	// @function	FormAjaxListener::FormAjaxListener
	// @desc		Construtor da classe
	// @param		eventName string		Nome do evento
	// @param		autoDispatchIf string	"" Expressуo que define se o evento щ disparado automaticamente ou nуo
	// @param		url string				"" URL alvo para o request
	// @param		class string			"" Nome da classe AJAX (default щ AjaxRequest)
	// @param		formSubmit bool			"FALSE" Indica se o request AJAX deve submeter o formulсrio onde щ inserido
	// @param		params array				"array()" Parтmetros AJAX
	// @param		paramsFuncBody string	"" Corpo de uma funчуo JS que retorna os parтmetros AJAX
	// @access		public
	//!-----------------------------------------------------------------
	function FormAjaxListener($eventName, $autoDispatchIf='', $url='', $class='', $formSubmit=FALSE, $params=array(), $paramsFuncBody='') {
		parent::FormEventListener(FORM_EVENT_AJAX, $eventName, '', $autoDispatchIf);
		$this->url = $url;
		$this->class = (!empty($class) ? $class : 'AjaxRequest');
		$this->formSubmit = $formSubmit;
		$this->params = (array)$params;
		$this->paramsFuncBody = $paramsFuncBody;
	}

	//!-----------------------------------------------------------------
	// @function	FormAjaxListener::getScriptCode
	// @desc		Sobrescreve o mщtodo da classe superior para que a funчуo
	//				que cria, configura e executa o request AJAX possa ser montada
	//				e adicionada no final do documento HTML ativo
	// @param		targetIndex int		"NULL" Эndice de um grupo de opчѕes
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getScriptCode($targetIndex=NULL) {
		$Form =& $this->_Owner->getOwnerForm();
		$Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'ajax.js');
		// submissуo de formulrio
		if ($this->formSubmit) {
			$this->url = $Form->formAction;
			$this->params['method'] = $Form->formMethod;
			$this->params['form'] = $Form->formName;
		} elseif (empty($this->url)) {
			$this->url = HttpRequest::uri(FALSE);
		}
		// um listener AJAX em um botуo do tipo SUBMIT deve sobrepor o evento 
		// onsubmit do formulсrio, atravщs do mщtodo Form.ajaxify
		if (TypeUtils::isInstanceOf($this->_Owner, 'FormButton') && $this->_Owner->attributes['TYPE'] == 'SUBMIT') {
			$Form->Document->addScriptCode(
				"\tForm.ajaxify($('{$Form->formName}'), function() {\n" .
				$this->getParamsScript() .
				"\t\tvar request = new {$this->class}('{$this->url}', getParams());\n" .
				"\t\trequest.send();\n" .
				"\t});",
			'Javascript', SCRIPT_END);
			return NULL;
		} else {
			// nome da funчуo
			$funcName = PHP2Go::generateUniqueId(preg_replace("~[^\w]+~", "", $this->_Owner->getName()) . ucfirst($this->eventName));
			if ($this->custom) {
				$params = $this->getParamsScript();
				$ctorArgs = 'args';
			} else {
				$params = preg_replace('/\bthis\b/', 'element', $this->getParamsScript());
				$ctorArgs = 'element, event';
			}
			$Form->Document->addScriptCode(
				"\tfunction {$funcName}({$ctorArgs}) {\n{$params}" .
				"\t\tvar request = new {$this->class}('{$this->url}', getParams());\n" .
				"\t\trequest.send();\n" .
				"\t}",
			'Javascript', SCRIPT_END);
			$this->action = "{$funcName}(this, event)";
			parent::renderAutoDispatch($targetIndex);
			return $this->action;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormAjaxListener::getParamsScript
	// @desc		Monta o cѓdigo Javascript que constrѓi o conjunto de
	//				argumentos a serem enviados para o componente AJAX
	// @access		protected
	// @return		string
	//!-----------------------------------------------------------------
	function getParamsScript() {
		$buf = "\t\tvar getParams = function() {\n";
		if (!empty($this->paramsFuncBody)) {
			$funcBody = rtrim(ltrim($this->paramsFuncBody, "\r\n "));
			preg_match("/^([\t]+)/", $funcBody, $matches);
			$funcBody = (isset($matches[1]) ? preg_replace("/^\t{" . strlen($matches[1]) . "}/m", "\t\t\t\t", $funcBody) : $funcBody);
			$buf .= "\t\t\tvar params = function() {\n{$funcBody}\n\t\t\t}();\n";
		} else {
			$buf .= "\t\t\tvar params = {};\n";
		}
		foreach ($this->params as $name => $value) {
			switch ($name) {
				case 'method' :
				case 'contentType' :
				case 'body' :
				case 'form' :
				case 'throbber' :
					$buf .= "\t\t\tparams.{$name} = '{$value}';\n";
					break;
				case 'async' :
				case 'params' :
				case 'headers' :
				case 'formFields' :
					$buf .= "\t\t\tparams.{$name} = {$value};\n";
					break;
				case 'container' :
					$buf .= "\t\t\tparams.{$name} = " . (preg_match("/{.*}/", $value) ? $value : "'{$value}'") . ";\n";
					break;
				case 'onLoading' :
				case 'onLoaded' :
				case 'onInteractive' :
					$buf .=	"\t\t\tparams.{$name} = function() {\n" .
							"\t\t\t{$value}\n" .
							"\t\t\t}\n";
							break;
				case 'onComplete' :
				case 'onSuccess' :
				case 'onFailure' :
				case 'onJSONResult' :
				case 'onXMLResult' :
					$buf .=	"\t\t\tparams.{$name} = function(response) {\n" .
							"\t\t\t{$value}\n" .
							"\t\t\t}\n";
							break;
				case 'onException' :
					$buf .=	"\t\t\tparams.{$name} = function(e) {\n" .
					$buf .= "\t\t\t{$value}\n" .
							"\t\t\t}\n";
			}
		}
		$buf .= "\t\t\treturn params;\n";
		$buf .= "\t\t}\n";
		return $buf;
	}

	//!-----------------------------------------------------------------
	// @function	FormAjaxListener::validate
	// @desc		Valida as propriedades do tratador de eventos
	// @access		protected
	// @return		bool
	//!-----------------------------------------------------------------
	function validate() {
		if (!empty($this->eventName)) {
			if ($this->class == 'AjaxUpdater')
				return (isset($this->params['container']) || isset($this->params['success']));
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	FormAjaxListener::__toString
	// @desc		Monta informaчѕes do listener, para exibiчуo de mensagens de erro
	// @access		protected
	// @return		string
	//!-----------------------------------------------------------------
	function __toString() {
		$info = $this->_Owner->getName();
		if (isset($this->_ownerIndex))
			$info .= " [option {$this->_ownerIndex}]";
		$info .= " - [{$this->type}";
		if (!empty($this->eventName))
			$info .= "; {$this->eventName}";
		if (!empty($this->url))
			$info .= "; {$this->url}";
		if (!empty($this->class))
			$info .= "; {$this->class}";
		$info .= ']';
		return $info;
	}
}

?>