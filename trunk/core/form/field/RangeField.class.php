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
// $Header: /www/cvsroot/php2go/core/form/field/RangeField.class.php,v 1.23 2006/11/19 18:02:38 mpont Exp $
// $Date: 2006/11/19 18:02:38 $

//------------------------------------------------------------------
import('php2go.form.field.EditField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		RangeField
// @desc		Classe responsável pela construção de um par de campos,
//				formando um intervalo de valores. Os campos podem ser
//				do tipo texto (EDITFIELD), caixas de seleção (COMBOFIELD e
//				LOOKUPFIELD) e seleções de data (DATEPICKERFIELD). Também
//				é possível configurar o texto que deverá envolver o código
//				dos campos (Entre %s e %s, por exemplo) e os atributos
//				da regra de comparação que é aplicada sobre os 2 campos
// @package		php2go.form.field
// @uses		EditField
// @uses		TypeUtils
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.23 $
//!-----------------------------------------------------------------
class RangeField extends FormField
{
	var $_StartField = NULL;	// @var _StartField FormField object	"NULL" Objeto FormField que representa o campo inicial do intervalo
	var $_EndField = NULL;		// @var _EndField FormField object		"NULL" Objeto FormField que representa o campo final do intervalo

	//!-----------------------------------------------------------------
	// @function	RangeField::RangeField
	// @desc		Construtor da classe
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @access		public
	//!-----------------------------------------------------------------
	function RangeField(&$Form) {
		parent::FormField($Form);
		$this->composite = TRUE;
		$this->searchDefaults['OPERATOR'] = 'BETWEEN';
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::display
	// @desc		Gera o código HTML do componente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		if (TypeUtils::isInstanceOf($this->_StartField, 'DatePickerField')) {
			print sprintf("<table id=\"%s\" cellspacing=\"0\"><tr><td style=\"padding-right:5px\" valign=\"top\">%s</td><td style=\"padding-left:5px\" valign=\"top\">%s</td></tr></table>",
				$this->id, $this->_StartField->getContent(), $this->_EndField->getContent()
			);
		} elseif (isset($this->attributes['SURROUNDTEXT'])) {
			print sprintf("<span id=\"%s\"%s%s>%s</span>",
				$this->id, $this->attributes['STYLE'], $this->attributes['TABINDEX'],
				sprintf($this->attributes['SURROUNDTEXT'], $this->_StartField->getContent(), $this->_EndField->getContent())
			);
		} else {
			print sprintf("<span id=\"%s\"%s>%s&nbsp;%s</span>",
				$this->id, $this->attributes['TABINDEX'], $this->_StartField->getContent(), $this->_EndField->getContent()
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::getFocusId
	// @desc		Retorna o nome do campo inicial do intervalo,
	//				que deverá receber foco quando o label do campo for clicado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFocusId() {
		return $this->_StartField->getId();
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::getDisplayValue
	// @desc		Monta uma representação compreensível
	//				do valor do campo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getDisplayValue() {
		if (is_array($this->value)) {
			$values = array_values($this->value);
			if (sizeof($values) == 2) {
				$operators = PHP2Go::getLangVal('OPERATORS');
				return sprintf("%s %s %s", $values[0], $operators['AND'], $values[1]);
			}
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::&getStartField
	// @desc		Retorna uma referência para o campo inicial do intervalo
	// @return		FormField object
	// @access		public
	//!-----------------------------------------------------------------
	function &getStartField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_StartField, 'FormField'))
			$result =& $this->_StartField;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::&getEndField
	// @desc		Retorna uma referência para o campo final do intervalo
	// @return		FormField object
	// @access		public
	//!-----------------------------------------------------------------
	function &getEndField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_EndField, 'FormField'))
			$result =& $this->_EndField;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::getSearchData
	// @desc		Sobrescreve a implementação da classe superior para que as
	//				máscaras definidas nos campos filhos sejam utilizadas como
	//				DATATYPE de pesquisa
	// @return		array Dados específicos de busca para este campo
	// @access		public
	//!-----------------------------------------------------------------
	function getSearchData() {
		$searchData = parent::getSearchData();
		$mask = $this->attributes['MASK'];
		if (!$mask && TypeUtils::isInstanceOf($this->_StartField, 'EditField')) {
			$bottomMask = $this->_StartField->getMask();
			$topMask = $this->_EndField->getMask();
			if ($bottomMask == $topMask)
			 	$mask = $bottomMask;
		}
		switch ($mask) {
			case 'DATE' :
			case 'DATE-EURO' :
			case 'DATE-US' :
				if ($searchData['DATATYPE'] != 'DATETIME')
					$searchData['DATATYPE'] = 'DATE';
				break;
			case 'INTEGER' :
			case 'FLOAT' :
				$searchData['DATATYPE'] = $mask;
				break;
		}
		return $searchData;
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::setMask
	// @desc		Define a máscara de digitação e validação do campo
	// @param		mask string		Nome da máscara
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMask($mask) {
		$matches = array();
		$mask = trim(strtoupper($mask));
		if (!empty($mask)) {
			if (preg_match(PHP2GO_MASK_PATTERN, $mask, $matches))
				$this->attributes['MASK'] = $mask;
			else
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_INVALID_MASK', array($mask, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::setSurroundText
	// @desc		Permite definir o texto que circunda os dois campos do intervalo. Um exemplo
	//				deste texto em português poderia ser: "Entre %s e %s". Os dois pontos de
	//				substituição são obrigatórios
	// @param		text string		Texto a ser utilizado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSurroundText($text) {
		if (!empty($text))
			$this->attributes['SURROUNDTEXT'] = resolveI18nEntry($text);
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// construção dos 2 campos do intervalo
		$fieldMap = array(
			'EDITFIELD' => 'php2go.form.field.EditField',
			'COMBOFIELD' => 'php2go.form.field.ComboField',
			'LOOKUPFIELD' => 'php2go.form.field.LookupField',
			'DATEPICKERFIELD' => 'php2go.form.field.DatePickerField'
		);
		$fieldNames = array_keys($fieldMap);
		if (!empty($children)) {
			foreach ($children as $key => $value) {
				if (in_array($key, $fieldNames)) {
					if (!TypeUtils::isArray($value) || sizeof($value) != 2) {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_RANGEFIELD_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
					} else {
						$start = @$attrs['STARTNAME'];
						$end = @$attrs['ENDNAME'];
						if (!$start || !$end || $start == $end) {
							$start = 'start';
							$end = 'end';
						}
						$this->attributes['STARTNAME'] = $start;
						$this->attributes['ENDNAME'] = $end;
						$value[0]->setAttribute('NAME', "{$this->name}[{$start}]");
						if (!isset($attrs['MASK']) && isset($value[0]->attrs['MASK']))
							$attrs['MASK'] = $value[0]->attrs['MASK'];
						if (!$value[0]->hasAttribute('LABEL'))
							$value[0]->setAttribute('LABEL', $this->label . ' (' . ucfirst(strtolower($start)) . ')');
						$value[1]->setAttribute('NAME', "{$this->name}[{$end}]");
						if (!$value[1]->hasAttribute('LABEL'))
							$value[1]->setAttribute('LABEL', $this->label . ' (' . ucfirst(strtolower($end)) . ')');
						$fieldClass = classForPath($fieldMap[$key]);
						$this->_StartField = new $fieldClass($this->_Form, TRUE);
						$this->_StartField->onLoadNode($value[0]->getAttributes(), $value[0]->getChildrenTagsArray());
						$this->_StartField->setRequired($this->required);
						$this->_Form->fields[$this->_StartField->getName()] =& $this->_StartField;
						$this->_EndField = new $fieldClass($this->_Form, TRUE);
						$this->_EndField->onLoadNode($value[1]->getAttributes(), $value[1]->getChildrenTagsArray());
						$this->_EndField->setRequired($this->required);
						$this->_Form->fields[$this->_EndField->getName()] =& $this->_EndField;
					}
				}
			}
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_RANGEFIELD_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
		// somente leitura
		$this->attributes['READONLY'] = resolveBooleanChoice(@$attrs['READONLY']);
		// máscara para os elementos do intervalo
		$this->setMask(@$attrs['MASK']);
		// texto envolvendo os 2 campos EDITFIELD
		$this->setSurroundText(@$attrs['SURROUNDTEXT']);
		// inclusão da regra de validação
		$type = resolveBooleanChoice(@$attrs['RULEEQUAL']);
		if (isset($attrs['RULEMESSAGE']))
			$attrs['RULEMESSAGE'] = resolveI18nEntry($attrs['RULEMESSAGE']);
		$mask = TypeUtils::ifNull(@$attrs['MASK'], 'STRING');
		if ($mask == 'DATE-EURO' || $mask == 'DATE-US')
			$mask = 'DATE';
		$this->_EndField->addRule(new FormRule(
			($type ? 'GOET' : 'GT'), $this->_StartField->getName(),
			NULL, $mask, @$attrs['RULEMESSAGE']
		));
	}

	//!-----------------------------------------------------------------
	// @function	RangeField::onPreRender
	// @desc		Executa configurações necessárias antes da construção
	//				do código HTML final do campo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		if (is_array($this->value) && isset($this->value[$this->attributes['STARTNAME']]) && isset($this->value[$this->attributes['ENDNAME']])) {
			$this->_StartField->setValue($this->value[$this->attributes['STARTNAME']]);
			$this->_EndField->setValue($this->value[$this->attributes['ENDNAME']]);
		}
		// propagação dos atributos DISABLED e READONLY
		if ($this->disabled) {
			$this->_StartField->setDisabled();
			$this->_EndField->setDisabled();
		}
		if (TypeUtils::isInstanceOf($this->_StartField, 'EditField')) {
			$this->_StartField->setReadonly($this->attributes['READONLY']);
			$this->_EndField->setReadonly($this->attributes['READONLY']);
			if (isset($this->attributes['MASK'])) {
				$this->_StartField->setMask($this->attributes['MASK']);
				$this->_EndField->setMask($this->attributes['MASK']);
			}
		}
		if (TypeUtils::isInstanceOf($this->_StartField, 'DatePickerField')) {
			$this->_StartField->setMultiple(FALSE);
			$this->_EndField->setMultiple(FALSE);
		}
		// se não foi definido um estilo, utiliza estilo de rótulos do formulário
		if (!isset($this->attributes['STYLE']))
			$this->attributes['STYLE'] = $this->_Form->getLabelStyle();
		// repassa o accesskey para o campo inicial do intervalo
		if ($this->accessKey)
			$this->_StartField->setAccessKey($this->accessKey);
		$this->_StartField->onPreRender();
		$this->_EndField->onPreRender();
	}
}
?>