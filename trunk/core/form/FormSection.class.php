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
// @desc 		Armazena informações sobre uma seção de formulário, que
// 				consiste em um grupo de campos agrupados em termos de
// 				disposição de interface
// @package		php2go.form
// @extends 	PHP2Go
// @author 		Marcos Pont
// @version		$Revision: 1.25 $ 
//!-----------------------------------------------------------------
class FormSection extends PHP2Go
{
	var $name;					// @var name string							Nome da seção
	var $id;					// @var id string							ID da seção
	var $attributes = array();	// @var attrs array							"array()" Conjunto de atributos secundários da seção
	var $conditional = FALSE;	// @var conditional bool					"FALSE" Indica a geração da seção no formulário é condicional
	var $visible = TRUE;		// @var visible bool						"TRUE" Indica se a seção deve ou não ser exibida
	var $children = array();	// @var children array						"array()" Elementos subordinados à seção (subseções, campos, botões)	
	var $childMap = array();	// @var childMap array						"array()" Armazena os filhos de diferentes tipos em áreas separadas, para facilitar a busca de um campo ou botão para alteração
	var $_Form = NULL;			// @var _Form Form object					"NULL" Formulário no qual a seção está incluída	
	 
	//!-----------------------------------------------------------------
	// @function 	FormSection::FormSection
	// @desc		Construtor da classe
	// @param 		&Form Form object		Objeto Form ao qual a seção pertence	
	// @access 		public
	//!-----------------------------------------------------------------
	function FormSection(&$Form) {
		parent::PHP2Go();
		$this->_Form =& $Form;		
	} 
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getId
	// @desc		Retorna o ID definido para a seção
	// @return		string
	// @access		public
	//!-----------------------------------------------------------------
	function getId() {
		return $this->id;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setId
	// @desc		Define o ID da seção
	// @param		id string	ID para a seção
	// @return		void
	//!-----------------------------------------------------------------
	function setId($id) {
		if (!empty($id))
			$this->id = $id;
		Form::verifySectionId($this->_Form->formName, $this->id);
	}
	
	//!-----------------------------------------------------------------
	// @function 	FormSection::getName
	// @desc 		Consulta o nome da seção atual
	// @return 		string Nome da seção
	// @access 		public 	
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setName
	// @desc		Define o nome da seção
	// @param		name string		Nome para a seção
	// @note		Nos formulários construídos com as classes FormTemplate e
	//				FormDataBind, o nome da seção pode ser exibido atráves da
	//				definição de uma variável simples com o mesmo valor do ID da seção
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
	// @desc		Verifica se a visibilidade da seção é condicional, ou
	//				seja, dependente de uma função de avaliação ou controle
	//				de acesso
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isConditional() {
		return $this->conditional;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setConditional
	// @desc		Define a seção como condicional, associando uma função
	//				de avaliação responsável por determinar sua visibilidade
	//				no formulário
	// @param		setting bool	"TRUE" Condicional (TRUE) ou não condicional (FALSE)
	// @param		function mixed	"" Função de avaliação da visibilidade da seção
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
	// @desc		Verifica se a seção deve ser exibida
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isVisible() {
		return $this->visible;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setRequiredFlag
	// @desc		Define se as marcas de campos obrigatórios devem ser
	//				exibidas nos elementos desta seção
	// @param		setting bool	"TRUE" Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setRequiredFlag($setting=TRUE) {
		$this->attributes['REQUIRED_FLAG'] = TypeUtils::toBoolean($setting);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::setRequiredText
	// @desc		Define o texto/marca indicativa de campo obrigatório para esta seção
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
	// @desc		Define a cor do texto indicativo de campos obrigatórios
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
	// @desc		Verifica se a seção possui elementos subordinados
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasChildren() {
		return (!empty($this->children));
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getChildren
	// @desc		Busca o vetor de elementos subordinados à seção
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getChildren() {
		return $this->children;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::&getChild
	// @desc		Busca um elemento interno da seção, a partir de seu índice
	// @param		index int	Índice do elemento
	// @return		mixed Objeto alocado no índice ou NULL se não encontrado
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
	// @desc		Consulta o tipo de um determinado elemento da seção
	// @param		index int	Índice do elemento
	// @return		string Tipo do elemento ou NULL se o índice for inválido
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
	// @desc		Busca um campo a partir de seu nome dentre os campos filhos da seção
	// @param		fieldName string	Nome do campo
	// @return		mixed Objeto correspondente ao campo ou NULL se não encontrado
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
	// @desc		Busca uma subseção a partir de seu ID dentre as subseções filhas da seção
	// @param		sectionId string	ID da seção
	// @return		mixed Objeto correspondente à subseção ou NULL se não encontrada
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
	// @desc		Adiciona uma subseção, campo, botão ou grupo de botões à seção atual
	// @param		&object mixed	Elemento a ser inserido
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function addChild(&$object) {
		$currentIndex = sizeof($this->children);
		// subseção
		if (TypeUtils::isInstanceOf($object, 'FormSection')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'SECTION';
			$this->childMap['SECTION'][$currentIndex] = $object->getId();
		// campo
		} elseif (TypeUtils::isInstanceOf($object, 'FormField')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'FIELD';			
			$this->childMap['FIELD'][$currentIndex] = $object->getName();
		// botão
		} elseif (TypeUtils::isInstanceOf($object, 'FormButton')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'BUTTON';
			$this->childMap['BUTTON'][$currentIndex] = $object->getName();
		// grupo de botões
		} elseif (is_array($object) && is_object($object[0]) && TypeUtils::isInstanceOf($object[0], 'FormButton')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'BUTTONGROUP';
			for ($i=0; $i<sizeof($object); $i++) {
				$this->childMap['BUTTON'][$currentIndex] = $object[$i]->getName();
			}
		// tipo inválido
		} else {
			return FALSE;
		}
		// adiciona o elemento
		$this->children[] =& $newChild;
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::_parseSection
	// @desc		Captura os atributos possíveis para uma seção de formulário
	// @param		attrs array			Atributos da seção na definição XML
	// @param		parentAttrs array	"array()" Atributos da seção pai, na definição XML
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $parentAttrs=array()) {
		// id da seção, atributo "ID"
		$this->setId(TypeUtils::ifNull(@$attrs['ID'], PHP2Go::generateUniqueId(parent::getClassName())));
		// nome da seção, atributo "NAME"
		$this->setName(TypeUtils::ifNull(@$attrs['NAME'], $this->_Form->formName . ' - Section ' . sizeof($this->_Form->sections)));
		// seção condicional e função de avaliação, atributo "EVALFUNCTION"
		$this->setConditional(resolveBooleanChoice(@$attrs['CONDITION']), @$attrs['EVALFUNCTION']);
		// configurações das marcas de campos obrigatórios
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
	// @desc		Verifica se a seção é visível, de acordo com o retorno
	//				da sua função de avaliação de visibilidade (EVALFUNCTION)
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _defineVisibility() {
		$Callback = new Callback();
		$Callback->throwErrors = FALSE;
		$Callback->setFunction($this->attributes['EVALFUNCTION']);
		// se a função definida no XML for inválida ou a função padrão id_evaluate 
		// não existir, utiliza o authorizer da aplicação para definir a visibilidade
		if (!$Callback->isValid()) {
			// mostra um erro do tipo "E_USER_NOTICE" se uma callback inválida foi fornecida
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