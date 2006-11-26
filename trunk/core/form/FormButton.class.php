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
// $Header: /www/cvsroot/php2go/core/form/FormButton.class.php,v 1.36 2006/11/19 18:30:38 mpont Exp $
// $Date: 2006/11/19 18:30:38 $

//------------------------------------------------------------------
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormButton
// @desc		Armazena informa��es sobre um bot�o inclu�do em um
// 				formul�rio. A partir das configura��es criadas para
// 				o bot�o, esta classe cria o c�digo HTML do mesmo
// @package		php2go.form
// @extends		Component
// @uses		FormEventListener
// @uses		HtmlUtils
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.36 $
//!-----------------------------------------------------------------
class FormButton extends Component
{
	var $id;					// @var id string			ID do bot�o
	var $name;					// @var name string			Nome do bot�o
	var $value = '';			// @var value string		"" Caption/texto do bot�o
	var $listeners = array();	// @var listeners array		"array()" Tratadores de eventos associados ao bot�o
	var $disabled = NULL;		// @var disabled bool		"NULL" Status do bot�o
	var $_Form = NULL;			// @var _Form Form object	"NULL" Objeto Form no qual o bot�o ser� inclu�do

	//!-----------------------------------------------------------------
	// @function	FormButton::FormButton
	// @desc		Inicializa as propriedades do bot�o e executa a
	// 				fun��o de constru��o do c�digo HTML
	// @param 		&Form Form object		Objeto Form onde o bot�o ser� inclu�do
	// @access		public
	//!-----------------------------------------------------------------
	function FormButton(&$Form) {
		parent::Component();
		$this->_Form =& $Form;
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::__destruct
	// @desc		Destrutor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function __destruct() {
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::getId
	// @desc		Retorna o ID do bot�o
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getId() {
		return $this->id;
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::setId
	// @desc		Define o ID do bot�o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setId($id) {
		if (!empty($id))
			$this->id = $id;
		else
			$this->id = PHP2Go::generateUniqueId(parent::getClassName());
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::getName
	// @desc		Consulta o nome do bot�o
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::setName
	// @desc		Define o nome do bot�o (atributos NAME e ID da tag INPUT)
	// @param		name string		Nome para o bot�o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setName($name) {
		if (!empty($name))
			$this->name = $name;
		else
			$this->name = $this->id;
		Form::verifyButtonName($this->_Form->formName, $this->name);
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::getValue
	// @desc		Consulta o valor (caption) do bot�o
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getValue() {
		return $this->value;
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::setValue
	// @desc		Define valor para o atributo VALUE do bot�o
	// @param		value string	Valor para o bot�o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setValue($value) {
		if (!empty($value))
			$this->value = resolveI18nEntry($value);
		elseif (!empty($this->name))
			$this->value = ucfirst($this->name);
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::&getOwnerForm
	// @desc		Retorna o formul�rio no qual o bot�o est� inserido
	// @return		Form object
	// @access		public
	//!-----------------------------------------------------------------
	function &getOwnerForm() {
		return $this->_Form;
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::display
	// @desc		Monta o conte�do HTML do bot�o
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered) && ($this->onPreRender());
		if ($this->attributes['IMG'] != '') {
			if ($this->attributes['TYPE'] == 'SUBMIT') {
				// c�digo HTML do bot�o, utilizando o type "IMAGE"
				// com este tipo de bot�o, � poss�vel mapear as coordenadas x e y do clique
				print sprintf("<input id=\"%s\" name=\"%s\" type=\"image\" value=\"%s\" src=\"%s\" border=\"0\"%s%s%s%s%s%s>",
					$this->id, $this->name, $this->value, $this->attributes['IMG'], $this->attributes['ALTHTML'], $this->attributes['SCRIPT'],
					$this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'], $this->attributes['DISABLED'], $this->attributes['STYLE']
				);
			} else {
				$btnScript = ($this->disabled ? " onClick=\"return false;\"" : $this->attributes['SCRIPT']);
				// dimens�es da imagem
				$size = @getimagesize($this->attributes['IMG']);
				if (!empty($size)) {
					$width = $size[0];
					$height = $size[1];
				} else {
					$width = 0;
					$height = 0;
				}
				// c�digo HTML do bot�o, utilizando a tag A com a imagem dentro
				print sprintf("<a id=\"%s\" name=\"%s\" style=\"cursor:pointer;\" %s%s%s%s>%s</a>",
					$this->id, $this->name, HtmlUtils::statusBar($this->attributes['ALT']), $btnScript, $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
					HtmlUtils::image($this->attributes['IMG'], '', $width, $height, -1, -1, '', "{$this->name}_img", $this->attributes['SWPIMG'])
				);
			}
		} else {
			print sprintf("<input id=\"%s\" name=\"%s\" type=\"%s\" value=\"%s\"%s%s%s%s%s%s>",
				$this->id, $this->name, strtolower($this->attributes['TYPE']), $this->value, $this->attributes['ALTHTML'],
				$this->attributes['SCRIPT'], $this->attributes['STYLE'], $this->attributes['ACCESSKEY'],
				$this->attributes['TABINDEX'], $this->attributes['DISABLED']
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::setImage
	// @desc		Define a imagem associada ao bot�o
	// @param		img string	Caminho da imagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setImage($img, $swpImg='') {
		$this->attributes['IMG'] = trim(TypeUtils::parseString($img));
		if ($swpImg && trim($swpImg) != '')
			$this->attributes['SWPIMG'] = $swpImg;
		else
			$this->attributes['SWPIMG'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::setStyle
	// @desc		Altera o valor do estilo do bot�o
	// @param		style string	Estilo para o bot�o
	// @note		Este m�todo permite customizar o estilo de um determinado
	//				bot�o em rela��o � configura��o global definida para todo
	//				o formul�rio
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setStyle($style) {
		$style = trim($style);
		if ($style == 'empty')
			$this->attributes['STYLE'] = '';
		elseif ($style != '')
			$this->attributes['STYLE'] = " class=\"{$style}\"";
		else
			$this->attributes['STYLE'] = $this->_Form->getButtonStyle();
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::setAccessKey
	// @desc		Define a tecla de atalho do campo
	// @param		accessKey string	Tecla de atalho
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAccessKey($accessKey) {
		if (trim($accessKey) != '')
			$this->attributes['ACCESSKEY'] = " accesskey=\"$accessKey\"";
		else
			$this->attributes['ACCESSKEY'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::setTabIndex
	// @desc		Define o �ndice de tab order do bot�o
	// @param		tabIndex int	�ndice para o bot�o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTabIndex($tabIndex) {
		if (TypeUtils::isInteger($tabIndex))
			$this->attributes['TABINDEX'] = " tabindex=\"$tabIndex\"";
		else
			$this->attributes['TABINDEX'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::setAltText
	// @desc		Define o texto alternativo do bot�o
	// @param		altText string	Texto alternativo para o bot�o
	// @note		Este atributo apenas tem efeito quando o bot�o utiliza uma imagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAltText($altText) {
		if (!empty($altText)) {
			$this->attributes['ALTHTML'] = " alt=\"$altText\"";
			$this->attributes['ALT'] = trim($altText);
		} else {
			$this->attributes['ALTHTML'] = "";
			$this->attributes['ALT'] = "";
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::setDisabled
	// @desc		Define o estado do bot�o (habilitado ou desabilitado)
	// @param		setting bool	"TRUE" Estado do bot�o (TRUE=desabilitado)
	// @access		public
	//!-----------------------------------------------------------------
	function setDisabled($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['DISABLED'] = " disabled";
			$this->disabled = TRUE;
		} else {
			$this->attributes['DISABLED'] = "";
			$this->disabled = FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::addEventListener
	// @desc		Adiciona um novo tratador de eventos no bot�o
	// @param		Listener FormEventListener object	Tratador de evento
	// @param		pushStart boolean	Adicionar antes de todos os handlers do mesmo tipo existentes
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addEventListener($Listener, $pushStart=FALSE) {
		$Listener->setOwner($this);
		if ($Listener->isValid()) {
			if ($pushStart) {
				$first = -1;
				for ($i=0,$s=sizeof($this->listeners); $i<$s; $i++) {
					if ($this->listeners[$i]->eventName == $Listener->eventName) {
						$first = $i;
						break;
					}
				}
				if ($first == 0) {
					array_unshift($this->listeners, $Listener);
				} elseif ($first > 0) {
					for ($i=$first, $s=sizeof($this->listeners); $i<$s; $i++)
						$this->listeners[$i+1] = $this->listeners[$i];
					$this->listeners[$first] = $Listener;
				} else {
					$this->listeners[] = $Listener;
				}
			} else {
				$this->listeners[] = $Listener;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::onLoadNode
	// @desc		Processa as informa��es provenientes da especifica��o XML do bot�o
	// @param		attrs array		Atributos do nodo XML
	// @param		children array	"array()" Vetor associativo de filhos do nodo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children=array()) {
		// id
		$this->setId(TypeUtils::ifNull(@$attrs['ID'], @$attrs['NAME']));
		// nome
		$this->setName(@$attrs['NAME']);
		// valor
		$this->setValue(@$attrs['VALUE']);
		// tipo
		$this->attributes['TYPE'] = (isset($attrs['TYPE']) && preg_match('/submit|reset|clear|back|button/i', $attrs['TYPE']) ? strtoupper($attrs['TYPE']) : 'BUTTON');
		// imagem e imagem de swap
		$this->setImage(@$attrs['IMG'], @$attrs['SWPIMG']);
		// define valor submetido, se o formul�rio foi postado
		// captura coordenadas para bot�es que utilizam imagem
		if ($this->_Form->isPosted()) {
			if ($this->attributes['TYPE'] == 'SUBMIT' && $this->attributes['IMG'] != '') {
				$x = HttpRequest::getVar($this->name . '_x', $this->_Form->formMethod);
				$y = HttpRequest::getVar($this->name . '_y', $this->_Form->formMethod);
				if (!TypeUtils::isNull($x) && !TypeUtils::isNull($y))
					$this->_Form->submittedValues[$this->name] = array('x' => $x, 'y' => $y);
			} else {
				$submittedValue = HttpRequest::getVar($this->name, $this->_Form->formMethod);
				if (!TypeUtils::isNull($submittedValue))
					$this->_Form->submittedValues[$this->name] = $submittedValue;
			}
		}
		// estilo CSS
		$this->setStyle(@$attrs['STYLE']);
		// access key
		$this->setAccessKey(@$attrs['ACCESSKEY']);
		// tab index
		$this->setTabIndex(@$attrs['TABINDEX']);
		// texto alternativo
		$this->setAltText(@$attrs['ALT']);
		// status
		$disabled = (resolveBooleanChoice(@$attrs['DISABLED']) || $this->_Form->readonly);
		if ($disabled)
			$this->setDisabled();
		// adiciona um listener para bot�es do tipo CLEAR (limpar todos os campos do formul�rio)
		if ($this->attributes['TYPE'] == 'CLEAR') {
			$this->attributes['TYPE'] = 'BUTTON';
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onClick', sprintf("Form.clear('%s')", $this->_Form->formName)));
		}
		// adiciona uma chamada para document.form.reset() para bot�es do tipo RESET usando imagem
		if ($this->attributes['TYPE'] == 'RESET' && $this->attributes['IMG'] != '')
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onClick', sprintf("Form.reset('%s')", $this->_Form->formName)));
		// adiciona event listeners para bot�es submit com imagem e imagem de swap
		if ($this->attributes['TYPE'] == 'SUBMIT' && $this->attributes['IMG'] != '' && $this->attributes['SWPIMG'] != '') {
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onLoad', sprintf("var %s_swp=new Image();%s_swp.src='%s'", $this->name, $this->name, $this->attributes['SWPIMG'])));
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onMouseOver', sprintf("this.src='%s'", $this->attributes['SWPIMG'])));
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onMouseOut', sprintf("this.src='%s'", $this->attributes['IMG'])));
		}
		// adiciona um listener para bot�es do tipo BACK (voltar � p�gina anterior)
		if ($this->attributes['TYPE'] == 'BACK') {
			$this->attributes['TYPE'] = 'BUTTON';
			if (empty($this->_Form->backUrl))
				$action = "history.back()";
			else
				$action = sprintf("window.location.href='%s'", $this->_Form->backUrl);
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onClick', $action));
		}
		if (isset($children['LISTENER'])) {
			$listeners = TypeUtils::toArray($children['LISTENER']);
			foreach ($listeners as $listenerNode)
				$this->addEventListener(FormEventListener::fromNode($listenerNode));
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::onPreRender
	// @desc		Aplica configura��es ao bot�o antes que seu c�digo
	//				HTML final seja gerado pelo m�todo getCode
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			// revalida a propriedade "disabled"
			if ($this->disabled === NULL) {
				if ($this->_Form->readonly)
					$this->setDisabled();
				else
					$this->setDisabled(FALSE);
			}
			$this->renderListeners();
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::renderListeners
	// @desc		A partir dos tratadores de eventos armazenados na classe,
	//				constr�i a string com as declara��es dos eventos para inclus�o
	//				no c�digo HTML final do bot�o
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function renderListeners() {
		$script = '';
		$events = array();
		foreach ($this->listeners as $listener) {
			$eventName = $listener->eventName;
			if (!isset($events[$eventName]))
				$events[$eventName] = array();
			$code = $listener->getScriptCode();
			if (!empty($code))
				$events[$eventName][] = $code;
		}
		foreach ($events as $event => $action) {
			if (!empty($action)) {
				$action = implode(';', $action);
				$script .= " {$event}=\"" . str_replace('\"', '\'', $action) . ";\"";
			}
		}
		$this->attributes['SCRIPT'] = $script;
	}
}
?>