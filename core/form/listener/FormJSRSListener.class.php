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
// $Header: /www/cvsroot/php2go/core/form/listener/FormJSRSListener.class.php,v 1.2 2006/10/11 22:16:13 mpont Exp $
// $Date: 2006/10/11 22:16:13 $

//!-----------------------------------------------------------------
// @class		FormJSRSListener
// @desc		Um listener JSRS щ um tipo especial de tratador de evento para
//				campos e botѕes de formulсrios, pois monta e executa uma chamada remota
//				de um script PHP, utilizando a biblioteca JSRS (JavaScript Remote Scripting).
//				Por isso, este listener deve ser configurado com parтmetros adicionais, como
//				caminho remoto do script PHP, funчуo remota a ser chamada, parтmetros da funчуo
//				remota, funчуo JS para tratamento do retorno, flag de debug, ...
// @extends		FormEventListener
// @package		php2go.form.listener
// @author		Marcos Pont
// @version		$Revision: 1.2 $
//!-----------------------------------------------------------------
class FormJSRSListener extends FormEventListener
{
	var $remoteFile;		// @var remoteFile string		Arquivo PHP remoto que contщm a funчуo a ser executada
	var $remoteFunction;	// @var remoteFunction string	Nome da funчуo remota a ser executada
	var $callback;			// @var callback string			Funчуo JavaScript que deve tratar o retorno da requisiчуo remota
	var $params;			// @var params string			String da parтmetros para a funчуo remota, definida em Javascript
	var $debug;				// @var debug bool				"FALSE" Habilita/desabilita debug das requisiчѕes

	//!-----------------------------------------------------------------
	// @function	FormJSRSListener::FormJSRSListener
	// @desc		Construtor da classe
	// @param		eventName string		Nome do evento Javascript
	// @param		autoDispatchIf string	"" Expressуo que define se o evento щ disparado automaticamente ou nуo
	// @param		remoteFile string		"" Arquivo remoto
	// @param		remoteFunction string	"" Funчуo remota
	// @param		callback string			"" Funчуo de tratamento do retorno
	// @param		params string			"" Conjunto de parтmetros
	// @param		debug bool				"FALSE" Debug do retorno da funчуo remota
	// @access		public
	//!-----------------------------------------------------------------
	function FormJSRSListener($eventName, $autoDispatchIf='', $remoteFile='', $remoteFunction='', $callback='', $params='', $debug=FALSE) {
		parent::FormEventListener(FORM_EVENT_JSRS, $eventName, '', $autoDispatchIf);
		$this->remoteFile = (!empty($remoteFile) ? $remoteFile : HttpRequest::uri());
		$this->remoteFunction = $remoteFunction;
		$this->callback = $callback;
		$this->params = $params;
		$this->debug = TypeUtils::toBoolean($debug);
	}

	//!-----------------------------------------------------------------
	// @function	FormJSRSListener::getScriptCode
	// @desc		Sobrecarrega o mщtodo da classe superior para
	//				incluir no documento HTML o cliente JSRS
	// @param		targetIndex int		"NULL" Эndice de um grupo de opчѕes
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getScriptCode($targetIndex=NULL) {
		$Form =& $this->_Owner->getOwnerForm();
		$Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'jsrsclient.js');
		$this->action = sprintf("jsrsExecute('%s', %s, '%s', %s%s);window.status=''",
			$this->remoteFile, $this->callback, $this->remoteFunction,
			(empty($this->params) ? 'null' : $this->params),
			($this->debug ? ', true' : '')
		);
		parent::renderAutoDispatch($targetIndex);
		return $this->action;
	}

	//!-----------------------------------------------------------------
	// @function	FormJSRSListener::validate
	// @desc		Valida as propriedades do tratador de eventos
	// @access		protected
	// @return		bool
	//!-----------------------------------------------------------------
	function validate() {
		return (!empty($this->eventName) && !empty($this->remoteFile) && !empty($this->remoteFunction) && !empty($this->callback));
	}

	//!-----------------------------------------------------------------
	// @function	FormJSRSListener::__toString
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
		if (!empty($this->remoteFile))
			$info .= "; {$this->remoteFile}";
		if (!empty($this->remoteFunction))
			$info .= "; {$this->remoteFunction}";
		if (!empty($this->callback))
			$info .= "; {$this->callback}";
		if (!empty($this->params))
			$info .= "; {$this->params}";
		$info .= ']';
		return $info;
	}
}
?>