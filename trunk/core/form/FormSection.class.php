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
// $Header: /www/cvsroot/php2go/core/form/FormSection.class.php,v 1.25 2006/10/26 04:59:27 mpont Exp $
// $Date: 2006/10/26 04:59:27 $

//------------------------------------------------------------------
import('php2go.auth.Authorizer');
import('php2go.util.Callback');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class 		FormSection
// @desc 		Armazena informa��es sobre uma se��o de formul�rio, que
// 				consiste em um grupo de campos agrupados em termos de
// 				disposi��o de interface
// @package		php2go.form
// @extends 	PHP2Go
// @author 		Marcos Pont
// @version		$Revision: 1.25 $ 
//!-----------------------------------------------------------------
class FormSection extends PHP2Go
{
	var $name;					// @var name string							Nome da se��o
	var $id;					// @var id string							ID da se��o
	var $attributes = array();	// @var attrs array							"array()" Conjunto de atributos secund�rios da se��o
	var $conditional = FALSE;	// @var conditional bool					"FALSE" Indica a gera��o da se��o no formul�rio � condicional
	var $visible = TRUE;		// @var visible bool						"TRUE" Indica se a se��o deve ou n�o ser exibida
	var $children = array();	// @var children array						"array()" Elementos subordinados � se��o (subse��es, campos, bot�es)	
	var $childMap = array();	// @var childMap array						"array()" Armazena os filhos de diferentes tipos em �reas separadas, para facilitar a busca de um campo ou bot�o para altera��o
	var $_Form = NULL;			// @var _Form Form object					"NULL" Formul�rio no qual a se��o est� inclu�da	
	 
	//!-----------------------------------------------------------------
	// @function 	FormSection::FormSection
	// @desc		Construtor da classe
	// @param 		&Form Form object		Objeto Form ao qual a se��o pertence	
	// @access 		public
	//!-----------------------------------------------------------------
	function FormSection(&$Form) {
		parent::PHP2Go();
		$this->_Form =& $Form;		
	} 
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getId
	// @desc		Retorna o ID definido para a se��o
	// @return		string
	// @access		public
	//!-----------------------------------------------------------------
	function getId() {
		return $this->id;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setId
	// @desc		Define o ID da se��o
	// @param		id string	ID para a se��o
	// @return		void
	//!-----------------------------------------------------------------
	function setId($id) {
		if (!empty($id))
			$this->id = $id;
		Form::verifySectionId($this->_Form->formName, $this->id);
	}
	
	//!-----------------------------------------------------------------
	// @function 	FormSection::getName
	// @desc 		Consulta o nome da se��o atual
	// @return 		string Nome da se��o
	// @access 		public 	
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setName
	// @desc		Define o nome da se��o
	// @param		name string		Nome para a se��o
	// @note		Nos formul�rios constru�dos com as classes FormTemplate e
	//				FormDataBind, o nome da se��o pode ser exibido atr�ves da
	//				defini��o de uma vari�vel simples com o mesmo valor do ID da se��o
	//				mais o prefixo "section". Ex: section_sectionId
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setName($name) {
		if (!empty($name))
			$this->name = resolveI18nEntry($name);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::isConditional
	// @desc		Verifica se a visibilidade da se��o � condicional, ou
	//				seja, dependente de uma fun��o de avalia��o ou controle
	//				de acesso
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isConditional() {
		return $this->conditional;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setConditional
	// @desc		Define a se��o como condicional, associando uma fun��o
	//				de avalia��o respons�vel por determinar sua visibilidade
	//				no formul�rio
	// @param		setting bool	"TRUE" Condicional (TRUE) ou n�o condicional (FALSE)
	// @param		function mixed	"" Fun��o de avalia��o da visibilidade da se��o
	// @return		void
	//!-----------------------------------------------------------------
	function setConditional($setting=TRUE, $function='') {		
		if ((bool)$setting == TRUE) {
			$this->conditional = TRUE;
			$this->attributes['INVERT'] = FALSE;
			if (!empty($function)) {
				if ($function[0] == '!') {
					$this->attributes['INVERT'] = TRUE;
					$this->attributes['EVALFUNCTION'] = substr($function, 1);
				} else {
					$this->attributes['EVALFUNCTION'] = $function;
				}
			} else {
				$this->attributes['EVALFUNCTION'] = "{$this->id}_evaluate";
			}
			$this->visible = $this->_defineVisibility();
		} else {
			$this->conditional = FALSE;
			$this->visible = TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::isVisible
	// @desc		Verifica se a se��o deve ser exibida
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isVisible() {
		return $this->visible;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setRequiredFlag
	// @desc		Define se as marcas de campos obrigat�rios devem ser
	//				exibidas nos elementos desta se��o
	// @param		setting bool	"TRUE" Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setRequiredFlag($setting=TRUE) {
		$this->attributes['REQUIRED_FLAG'] = TypeUtils::toBoolean($setting);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setRequiredText
	// @desc		Define o texto/marca indicativa de campo obrigat�rio para esta se��o
	// @param		text string		Texto
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setRequiredText($text) {
		if (!empty($text))
			$this->attributes['REQUIRED_TEXT'] = $text;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setRequiredColor
	// @desc		Define a cor do texto indicativo de campos obrigat�rios
	// @param		color string	Nome da cor ou string RGB
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setRequiredColor($color) {
		if (!empty($color))
			$this->attributes['REQUIRED_COLOR'] = $color;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::hasChildren
	// @desc		Verifica se a se��o possui elementos subordinados
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasChildren() {
		return (!empty($this->children));
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getChildren
	// @desc		Busca o vetor de elementos subordinados � se��o
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getChildren() {
		return $this->children;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::&getChild
	// @desc		Busca um elemento interno da se��o, a partir de seu �ndice
	// @param		index int	�ndice do elemento
	// @return		mixed Objeto alocado no �ndice ou NULL se n�o encontrado
	// @access		public	
	//!-----------------------------------------------------------------
	function &getChild($index) {
		$result = NULL;
		if (isset($this->children[$index]))
			$result =& $this->children[$index]['object'];
		return $result;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getChildType
	// @desc		Consulta o tipo de um determinado elemento da se��o
	// @param		index int	�ndice do elemento
	// @return		string Tipo do elemento ou NULL se o �ndice for inv�lido
	// @access		public	
	//!-----------------------------------------------------------------
	function getChildType($index) {
		if (isset($this->children[$index]))
			return $this->children[$index]['type'];
		else
			return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::&getField
	// @desc		Busca um campo a partir de seu nome dentre os campos filhos da se��o
	// @param		fieldName string	Nome do campo
	// @return		mixed Objeto correspondente ao campo ou NULL se n�o encontrado
	// @access		public	
	//!-----------------------------------------------------------------
	function &getField($fieldName) {
		$result = NULL;
		$index = (TypeUtils::isArray($this->childMap['FIELD']) ? array_search($fieldName, $this->childMap['FIELD']) : FALSE);
		if (!TypeUtils::isFalse($index))
			$result =& $this->getChild($index);
		return $result;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::&getSubSection
	// @desc		Busca uma subse��o a partir de seu ID dentre as subse��es filhas da se��o
	// @param		sectionId string	ID da se��o
	// @return		mixed Objeto correspondente � subse��o ou NULL se n�o encontrada
	// @access		public	
	//!-----------------------------------------------------------------
	function &getSubSection($sectionId) {
		$result = NULL;
		$index = (TypeUtils::isArray($this->childMap['SECTION']) ? array_search($sectionId, $this->childMap['SECTION']) : FALSE);
		if (!TypeUtils::isFalse($index))
			$result =& $this->getChild($index);
		return $result;
	}	
	
	//!-----------------------------------------------------------------
	// @function	FormSection::addChild
	// @desc		Adiciona uma subse��o, campo, bot�o ou grupo de bot�es � se��o atual
	// @param		&object mixed	Elemento a ser inserido
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function addChild(&$object) {
		$currentIndex = sizeof($this->children);
		// subse��o
		if (TypeUtils::isInstanceOf($object, 'FormSection')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'SECTION';
			$this->childMap['SECTION'][$currentIndex] = $object->getId();
		// campo
		} elseif (TypeUtils::isInstanceOf($object, 'FormField')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'FIELD';			
			$this->childMap['FIELD'][$currentIndex] = $object->getName();
		// bot�o
		} elseif (TypeUtils::isInstanceOf($object, 'FormButton')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'BUTTON';
			$this->childMap['BUTTON'][$currentIndex] = $object->getName();
		// grupo de bot�es
		} elseif (is_array($object) && is_object($object[0]) && TypeUtils::isInstanceOf($object[0], 'FormButton')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'BUTTONGROUP';
			for ($i=0; $i<sizeof($object); $i++) {
				$this->childMap['BUTTON'][$currentIndex] = $object[$i]->getName();
			}
		// tipo inv�lido
		} else {
			return FALSE;
		}
		// adiciona o elemento
		$this->children[] =& $newChild;
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::_parseSection
	// @desc		Captura os atributos poss�veis para uma se��o de formul�rio
	// @param		attrs array			Atributos da se��o na defini��o XML
	// @param		parentAttrs array	"array()" Atributos da se��o pai, na defini��o XML
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $parentAttrs=array()) {
		// id da se��o, atributo "ID"
		$this->setId(TypeUtils::ifNull(@$attrs['ID'], PHP2Go::generateUniqueId(parent::getClassName())));
		// nome da se��o, atributo "NAME"
		$this->setName(TypeUtils::ifNull(@$attrs['NAME'], $this->_Form->formName . ' - Section ' . sizeof($this->_Form->sections)));
		// se��o condicional e fun��o de avalia��o, atributo "EVALFUNCTION"
		$this->setConditional(resolveBooleanChoice(@$attrs['CONDITION']), @$attrs['EVALFUNCTION']);
		// configura��es das marcas de campos obrigat�rios
		$this->setRequiredFlag(TypeUtils::ifNull(
			resolveBooleanChoice(@$attrs['REQUIRED_FLAG']), 
			TypeUtils::ifNull(
				resolveBooleanChoice(@$parentAttrs['REQUIRED_FLAG']), 
				$this->_Form->requiredMark
			)
		));
		$this->setRequiredText(TypeUtils::ifNull(
			@$attrs['REQUIRED_TEXT'], 
			TypeUtils::ifNull(
				@$parentAttrs['REQUIRED_TEXT'], 
				$this->_Form->requiredText
			)
		));
		$this->setRequiredColor(TypeUtils::ifNull(
			@$attrs['REQUIRED_COLOR'], 
			TypeUtils::ifNull(
				@$parentAttrs['REQUIRED_COLOR'], 
				$this->_Form->requiredColor
			)
		));
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::_defineVisibility
	// @desc		Verifica se a se��o � vis�vel, de acordo com o retorno
	//				da sua fun��o de avalia��o de visibilidade (EVALFUNCTION)
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _defineVisibility() {
		$Callback = new Callback();
		$Callback->throwErrors = FALSE;
		$Callback->setFunction($this->attributes['EVALFUNCTION']);
		// se a fun��o definida no XML for inv�lida ou a fun��o padr�o id_evaluate 
		// n�o existir, utiliza o authorizer da aplica��o para definir a visibilidade
		if (!$Callback->isValid()) {
			// mostra um erro do tipo "E_USER_NOTICE" se uma callback inv�lida foi fornecida
			if ($this->attributes['EVALFUNCTION'] != "{$this->id}_evaluate")
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_CALLBACK', $Callback->toString()), E_USER_NOTICE, __FILE__, __LINE__);			
			$func = array();
			$func[0] =& Authorizer::getInstance();
			$func[1] = 'authorizeFormSection';
			$Callback->setFunction($func);
		}
		return ($this->attributes['INVERT'] ? !$Callback->invoke($this) : $Callback->invoke($this));
	}	
} 
?>