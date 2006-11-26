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
// $Header: /www/cvsroot/php2go/core/form/FormEventListener.class.php,v 1.14 2006/11/26 00:19:02 mpont Exp $
// $Date: 2006/11/26 00:19:02 $

// @const FORM_EVENT_JS "JS"
// Eventos que executam cѓdigo Javascript
define('FORM_EVENT_JS', 'JS');
// @const FORM_EVENT_JSRS "JSRS"
// Eventos que executam scripts PHP utilizando JSRS
define('FORM_EVENT_JSRS', 'JSRS');
// @const FORM_EVENT_AJAX "AJAX"
// Eventos que executam requests AJAX
define('FORM_EVENT_AJAX', 'AJAX');

//!-----------------------------------------------------------------
// @class		FormEventListener
// @desc		A classe FormEventListener armazena dados dos tratadores de eventos
//				associados a campos e botѕes de formulсrios. Щ responsсvel por gerar
//				o cѓdigo de chamada da(s) rotina(s) Javascript associadas ao evento
// @package		php2go.form
// @extends		PHP2Go
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.14 $
//!-----------------------------------------------------------------
class FormEventListener extends PHP2Go
{
	var $type;				// @var type string				Tipo do tratador
	var $eventName;			// @var eventName string		Nome do evento
	var $action;			// @var action string			Aчуo a ser executada (somente JS)
	var $autoDispatchIf;	// @var autoDispatchIf string	Uma expressуo em Javascript que, se for avaliada para true, irс disparar automaticamente o evento no carregamento da pсgina
	var $functionBody;		// @var functionBody string		Cѓdigo JavaScript a ser executado (somente JS)
	var $custom = FALSE;	// @var custom bool				Indica se o evento щ custom (fora dos eventos DOM) ou nуo
	var $_valid = FALSE;	// @var _valid bool				"FALSE" Indica que as propriedades do listener sуo vсlidas
	var $_Owner = NULL;		// @var _Owner object			"NULL" Campo ou botуo ao qual o listener estс associado
	var $_ownerIndex;		// @var _ownerIndex int			Эndice da opчуo р qual o listener pertence (RadioField, CheckGroup)

	//!-----------------------------------------------------------------
	// @function	FormEventListener::FormEventListener
	// @desc		Construtor da classe
	// @param		type string				Tipo do tratador
	// @param		eventName string		Nome do evento JavaScript
	// @param		action string			"" Cѓdigo Javascript a ser executado
	// @param		autoDispatchIf string	"" Expressуo que define se o evento щ disparado automaticamente ou nуo
	// @param		functionBody string		"" Conjunto de rotinas Javascript a serem executadas
	// @access		public
	//!-----------------------------------------------------------------
	function FormEventListener($type, $eventName, $action='', $autoDispatchIf='', $functionBody='') {
		parent::PHP2Go();
		$this->type = $type;
		$this->eventName = $eventName;
		$this->action = $action;
		$this->autoDispatchIf = $autoDispatchIf;
		$this->functionBody = $functionBody;
	}

	//!-----------------------------------------------------------------
	// @function	FormEventListener::&fromNode
	// @desc		Este mщtodo factory cria uma instтncia da classe
	//				FormEventListener a partir de um nodo XML do tipo
	//				LISTENER, utilizado para definir tratamento de eventos
	//				para campos e botѕes de formulсrios
	// @param		Node XmlNode object	Nodo da regra na especificaчуo XML
	// @return		FormEventListener object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function &fromNode($Node) {
		$type = trim($Node->getAttribute('TYPE'));
		$eventName = trim($Node->getAttribute('EVENT'));
		$autoDispatchIf = trim($Node->getAttribute('AUTODISPATCHIF'));
		$custom = resolveBooleanChoice($Node->getAttribute('CUSTOM'));
		switch ($type) {
			case FORM_EVENT_JS :
				$action = trim($Node->getAttribute('ACTION'));
				$functionBody = $Node->getData();
				$Listener = new FormEventListener(FORM_EVENT_JS, $eventName, $action, $autoDispatchIf, $functionBody, $custom);
				break;
			case FORM_EVENT_JSRS :
				import('php2go.form.listener.FormJSRSListener');
				$remoteFile = trim($Node->getAttribute('FILE'));
				$remoteFunction = trim($Node->getAttribute('REMOTE'));
				$callback = trim($Node->getAttribute('CALLBACK'));
				$params = trim($Node->getAttribute('PARAMS'));
				$debug = resolveBooleanChoice($Node->getAttribute('DEBUG'));
				$Listener = new FormJSRSListener($eventName, $autoDispatchIf, $remoteFile, $remoteFunction, $callback, $params, $debug, $custom);
				break;
			case FORM_EVENT_AJAX :
				import('php2go.form.listener.FormAjaxListener');
				$url = trim($Node->getAttribute('URL'));
				$class = trim($Node->getAttribute('CLASS'));
				$formSubmit = resolveBooleanChoice($Node->getAttribute('FORMSUBMIT'));
				$args = array();
				$argsBody = $Node->getData();
				$children = $Node->getChildrenTagsArray();
				$params = ($children['PARAM'] ? (is_array($children['PARAM']) ? $children['PARAM'] : array($children['PARAM'])) : array());
				foreach ($params as $idx => $ParamNode) {
					$args[$ParamNode->getAttribute('NAME')] = $ParamNode->getData();
				}
				$Listener = new FormAjaxListener($eventName, $autoDispatchIf, $url, $class, $formSubmit, $args, $argsBody, $custom);
				break;
			default :
				$Listener = NULL;
		}
		return $Listener;
	}

	//!-----------------------------------------------------------------
	// @function	FormEventListener::setOwner
	// @desc		Define o campo ou botуo ao qual o tratador de evento estс associado
	// @param		&Owner object	Campo ou botуo
	// @param		ownerIndex int	"NULL" Эndice da opчуo р qual o listener pertence (RadioField, CheckGroup)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setOwner(&$Owner, $ownerIndex=NULL) {
		$this->_Owner =& $Owner;
		$this->_ownerIndex = $ownerIndex;
		if (TypeUtils::isInstanceOf($Owner, 'FormField') && in_array($this->eventName, $Owner->customEvents))
			$this->custom = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	FormEventListener::getScriptCode
	// @desc		Monta o cѓdigo JavaScript de definiчуo do tratador
	// @param		targetIndex int		"NULL" Эndice de um grupo de opчѕes
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getScriptCode($targetIndex=NULL) {
		$Form =& $this->_Owner->getOwnerForm();
		if (!empty($this->functionBody)) {
			$funcName = PHP2Go::generateUniqueId(preg_replace("~[^\w]+~", "", $this->_Owner->getName()) . ucfirst($this->eventName));
			$funcBody = rtrim(ltrim($this->functionBody, "\r\n"));
			if (preg_match("/^([\t]+)/", $funcBody, $matches))
				$funcBody = preg_replace("/^\t{" . strlen($matches[1]) . "}/m", "\t\t", $funcBody);
			if ($this->custom) {
				$Form->Document->addScriptCode("\tfunction {$funcName}(obj, args) {\n{$funcBody}\n\t}", 'Javascript', SCRIPT_END);
				$this->action = "{$funcName}(this, args)";
			} else {
				$funcBody = preg_replace('/\bthis\b/', 'element', $funcBody);
				$Form->Document->addScriptCode("\tfunction {$funcName}(element, event) {\n{$funcBody}\n\t}", 'Javascript', SCRIPT_END);
				$this->action = "{$funcName}(this, event)";
			}
		} else {
			$this->action = preg_replace("/;\s*$/", '', $this->action);
		}
		$this->renderAutoDispatch($targetIndex);
		return $this->action;
	}

	//!-----------------------------------------------------------------
	// @function	FormEventListener::isValid
	// @desc		Verifica se os dados do tratador de evento sуo vсlidos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if ($this->_valid == TRUE)
			return $this->_valid;
		if (TypeUtils::isInstanceOf($this->_Owner, 'FormField') || TypeUtils::isInstanceOf($this->_Owner, 'FormButton')) {
			$this->_valid = $this->validate();
			if (!$this->_valid)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_WRONG_LISTENER', $this->__toString()), E_USER_ERROR, __FILE__, __LINE__);
			return $this->_valid;
		} else {
			$this->_valid = FALSE;
			return $this->_valid;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormEventListener::validate
	// @desc		Valida as propriedades do tratador de eventos
	// @access		protected
	// @return		bool
	//!-----------------------------------------------------------------
	function validate() {
		return (!empty($this->eventName) && (!empty($this->action) || !empty($this->functionBody)));
	}

	//!-----------------------------------------------------------------
	// @function	FormEventListener::renderAutoDispatch
	// @desc		Gera cѓdigo necessсrio para auto executar o tratador
	//				de evento quando uma determinada condiчуo for satisfeita
	//				no momento do carregamento da pсgina
	// @param		targetIndex int		"NULL" Эndice de um grupo de opчѕes
	// @access		protected
	// @return		string
	//!-----------------------------------------------------------------
	function renderAutoDispatch($targetIndex=NULL) {
		if (!$this->custom && !empty($this->autoDispatchIf)) {
			$Form =& $this->_Owner->getOwnerForm();
			if (isset($this->_ownerIndex))
				$fldRef = sprintf("$('%s_%s')", $this->_Owner->getName(), $this->_ownerIndex);
			elseif ($targetIndex !== NULL)
				$fldRef = sprintf("$('%s_%s')", $this->_Owner->getName(), $targetIndex);
			else
				$fldRef = sprintf("$('%s').elements['%s']", $Form->formName, $this->_Owner->getName());
			$dispatchTest = preg_replace('/\bthis\b/', 'fld', $this->autoDispatchIf);
			$dispatchAction = preg_replace(array('/\bthis\b/', '/, event/'), array('fld', ', null'), $this->action);
			$Form->Document->addOnloadCode(sprintf("var fld = %s; if (%s) { %s; }", $fldRef, $dispatchTest, $dispatchAction));
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormEventListener::__toString
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
		if (!empty($this->action))
			$info .= "; {$this->action}";
		$info .= ']';
		return $info;
	}
}
?>