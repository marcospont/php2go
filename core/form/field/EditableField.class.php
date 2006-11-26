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
// $Header: /www/cvsroot/php2go/core/form/field/EditableField.class.php,v 1.36 2006/10/29 18:28:52 mpont Exp $
// $Date: 2006/10/29 18:28:52 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

// @const EDITABLE_FIELD_DEFAULT_SIZE "10"
// Tamanho padrão para campos editáveis
define('EDITABLE_FIELD_DEFAULT_SIZE', 10);

//!-----------------------------------------------------------------
// @class		EditableField
// @desc		Classe abstrata que serve de base para a construção de campos
//				de edição de texto, gerenciando os atributos comuns entre
//				os mesmos
// @package		php2go.form.field
// @uses		TypeUtils
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.36 $
//!-----------------------------------------------------------------
class EditableField extends FormField
{
	var $readOnly = NULL;		// @var readOnly bool				"NULL" Indica se o campo é somente leitura
	var $minLength;				// @var minLength int				Número mínimo de caracteres permitido
	var $maxLength;				// @var maxLength int				Número máximo de caracteres permitido
	var $mask = '';				// @var mask string					"" Nome da máscara de digitação e checagem utilizada
	var $maskSetupScript = '';	// @var maskSetupScript string		"" Script para inicialização do controle de máscara no campo
	var $limiters;				// @var limiters array				Armazena os limitadores de tamanho usados nas máscaras ZIP e FLOAT

	//!-----------------------------------------------------------------
	// @function	EditableField::EditableField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function EditableField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		if ($this->isA('EditableField', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'EditableField'), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::getMask
	// @desc		Retorna a máscara (tipo de dado) definida para o campo
	// @return		string Máscara do campo
	// @access		public
	//!-----------------------------------------------------------------
	function getMask() {
		return $this->mask;
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::getMaskLimiters
	// @desc		Retorna os limitadores de tamanho da máscara, se existentes
	// @note		As máscaras FLOAT e ZIP possuem limitadores de tamanho
	// @return		mixed
	// @access		public
	//!-----------------------------------------------------------------
	function getMaskLimiters() {
		return $this->limiters;
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::setMask
	// @desc		Define a máscara de digitação e validação do campo
	// @param		mask string		Nome da máscara
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMask($mask) {
		$matches = array();
		$mask = trim(strtoupper($mask));
		if (!empty($mask)) {
			if (preg_match(PHP2GO_MASK_PATTERN, $mask, $matches)) {
				if (isset($matches[6]) && $matches[6] == 'ZIP') {
					$this->mask = $matches[6];
					$this->limiters = array($matches[8], $matches[9]);
					$this->setLength($matches[8] + $matches[9] + 1);
				} elseif (isset($matches[2]) && $matches[2] == 'FLOAT') {
					$this->mask = $matches[2];
					$this->limiters = array($matches[4], $matches[5]);
					$this->setLength($matches[4] + $matches[5] + 2);
				} elseif ($matches[0] == 'DATE') {
					$mask = $this->mask = 'DATE-' . PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
				} elseif ($matches[0] == 'LOGIN') {
					$this->mask = 'WORD';
				} else {
					$this->mask = $matches[0];
				}
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_INVALID_MASK', array($mask, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
			}
			$this->maskSetupScript = "\tInputMask.setup('%s', Mask.fromMaskName('{$mask}'));";
		}
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::setSize
	// @desc		Altera ou define o tamanho do campo
	// @param		size int		Tamanho para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = $size;
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::setLength
	// @desc		Define número máximo de caracteres do campo
	// @param		length int		Máximo de caracteres para o campo
	// @note		Este método define valor para o atributo LENGTH do campo,
	//				que será utilizado no atributo MAXLENGTH no código final do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLength($length) {
		if (TypeUtils::isInteger($length))
			$this->attributes['LENGTH'] = $length;
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::setMinLength
	// @desc		Define número mínimo de caracteres para o campo
	// @param		minLength int	Mínimo de caracteres para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMinLength($minLength) {
		if (TypeUtils::isInteger($minLength))
			$this->minLength = $minLength;
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::setMaxLength
	// @desc		Define número máximo de caracteres para o campo
	// @param		maxLength int	Máximo de caracteres para o campo
	// @note		O atributo MAXLENGTH, definido através deste método, criará
	//				um controle do máximo de caracteres digitados utilizando Javascript.
	//				Assim sendo, o input do tipo TEXTAREA, que não possui o atributo
	//				MAXLENGTH, pode receber controle de número máximo de caracteres
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMaxLength($maxLength) {
		if (TypeUtils::isInteger($maxLength)) {
			if (isset($this->limiters))
				$this->maxLength = max($maxLength, array_sum($this->limiters) + 1);
			else
				$this->maxLength = $maxLength;
		}
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::setAutoComplete
	// @desc		Define valor para o recurso autocompletar no campo
	// @param		setting mixed	Valor para o atributo AUTOCOMPLETE
	// @note		Se o valor fornecido for TRUE, habilita o recurso autocompletar.
	//				Se for FALSE, desabilita. Para qualquer outro valor, inibe a
	//				inclusão do atributo AUTOCOMPLETE no campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoComplete($setting) {
		if ($setting === TRUE)
			$this->attributes['AUTOCOMPLETE'] = " autocomplete=\"ON\"";
		elseif ($setting === FALSE)
			$this->attributes['AUTOCOMPLETE'] = " autocomplete=\"OFF\"";
		else
			$this->attributes['AUTOCOMPLETE'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::setReadonly
	// @desc		Permite habilitar ou desabilitar o atributo de somente leitura do campo
	// @param		setting bool	"TRUE" Valor para o atributo, TRUE torna o campo somente leitura
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setReadonly($setting=TRUE) {
		if ($setting) {
			$this->attributes['READONLY'] = " readonly";
			$this->readOnly = TRUE;
		} else {
			$this->attributes['READONLY'] = "";
			$this->readOnly = FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::setUpper
	// @desc		Habilita ou desabilita a conversão do conteúdo do campo
	//				para letras maiúsculas no momento da submissão do formulário
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setUpper($setting=TRUE) {
		if ($setting)
			$this->attributes['UPPER'] = "T";
		else
			$this->attributes['UPPER'] = "F";
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::setLower
	// @desc		Habilita ou desabilita a conversão do conteúdo do campo
	//				para letras minúsculas na submissão do formulário
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLower($setting=TRUE) {
		if ($setting)
			$this->attributes['LOWER'] = "T";
		else
			$this->attributes['LOWER'] = "F";
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::isValid
	// @desc		Executa as validações configuradas no campo, como tamanho mínimo,
	//				tamanho máximo e máscara de tipo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if ($this->attributes['UPPER'] == "T")
			$this->value = strtoupper($this->value);
		if ($this->attributes['LOWER'] == "T")
			$this->value = strtolower($this->value);
		$result = parent::isValid();
		$validators = array();
		if (trim($this->value) != '' && $this->mask != '') {
			switch ($this->mask) {
				case 'CPFCNPJ' :
					$validators[] = array('php2go.validation.CPFCNPJValidator', NULL, NULL);
					break;
				case 'CURRENCY' :
					$validators[] = array('php2go.validation.CurrencyValidator', NULL, NULL);
					break;
				case 'DATE-EURO' :
				case 'DATE-US' :
					$validators[] = array('php2go.validation.DateValidator', NULL, NULL);
					break;
				case 'EMAIL' :
					$validators[] = array('php2go.validation.EmailValidator', NULL, NULL);
					break;
				case 'FLOAT' :
					$validators[] = array('php2go.validation.FloatValidator', (is_array($this->limiters) ? array('limiters' => $this->limiters, 'decimalPoint' => '.') : array('decimalPoint' => '.')), NULL);
					break;
				case 'INTEGER' :
					$validators[] = array('php2go.validation.IntegerValidator', array('unsigned' => FALSE), NULL);
					break;
				case 'DIGIT' :
					$validators[] = array('php2go.validation.IntegerValidator', array('unsigned' => TRUE), NULL);
					break;
				case 'WORD' :
					$validators[] = array('php2go.validation.WordValidator', NULL, NULL);
					break;
				case 'TIME' :
					$validators[] = array('php2go.validation.TimeValidator', NULL, NULL);
					break;
				case 'URL' :
					$validators[] = array('php2go.validation.UrlValidator', NULL, NULL);
					break;
				case 'ZIP' :
					$validators[] = array('php2go.validation.ZipCodeValidator', array('limiters' => $this->limiters), NULL);
					break;
			}
		}
		if (!preg_match('/^(CPFCNPJ$|DATE|FLOAT$|TIME|ZIP$)/', $this->mask)) {
			if (isset($this->minLength))
				$validators[] = array('php2go.validation.MinLengthValidator', array('minlength' => $this->minLength, 'bypassEmpty' => TRUE), NULL);
			if (isset($this->maxLength))
				$validators[] = array('php2go.validation.MaxLengthValidator', array('maxlength' => $this->maxLength, 'bypassEmpty' => TRUE), NULL);
		}
		foreach ($validators as $validator) {
			$result &= Validator::validateField($this, $validator[0], $validator[1], $validator[2]);
		}
		return TypeUtils::toBoolean($result);
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// converte valores no formato array
		if (is_array($this->value))
			$this->value = '';
		// tamanho do widget
		// 1) atributo SIZE
		if (isset($attrs['SIZE']) && TypeUtils::isInteger($attrs['SIZE']))
			$this->setSize($attrs['SIZE']);
		// 2) atributo LENGTH
		elseif (isset($attrs['LENGTH']) && TypeUtils::isInteger($attrs['LENGTH']))
			$this->setSize($attrs['LENGTH']);
		// 3) tamanho default da classe
		else
			$this->setSize(EDITABLE_FIELD_DEFAULT_SIZE);
		// mínimo de caracteres
		if (isset($attrs['MINLENGTH']) && TypeUtils::isInteger($attrs['MINLENGTH']))
			$this->setMinLength($attrs['MINLENGTH']);
		// máximo de caracteres
		if (isset($attrs['MAXLENGTH']) && TypeUtils::isInteger($attrs['MAXLENGTH']))
			$this->setMaxLength($attrs['MAXLENGTH']);
		// tamanho digitável
		// 1) atributo LENGTH
		if (isset($attrs['LENGTH']) && TypeUtils::isInteger($attrs['LENGTH']))
			$this->setLength($attrs['LENGTH']);
		// 2) propriedade maxLength
		elseif (isset($this->maxLength))
			$this->setLength($this->maxLength);
		// 3) atributo SIZE definido anteriormente
		else
			$this->setLength($this->attributes['SIZE']);
		// mask
		$this->setMask(@$attrs['MASK']);
		// autocomplete
		$this->setAutoComplete(resolveBooleanChoice(@$attrs['AUTOCOMPLETE']));
		// readonly
		$readOnly = (resolveBooleanChoice(@$attrs['READONLY']) || $this->_Form->readonly);
		if ($readOnly)
			$this->setReadonly();
		// upper
		$this->setUpper(resolveBooleanChoice(@$attrs['UPPER']));
		// lower
		$this->setLower(resolveBooleanChoice(@$attrs['LOWER']));
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::onDataBind
	// @desc		Aplica conversão para string se o valor do campo for associado a um array
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		switch ($this->mask) {
			case 'DATE-EURO' :
			case 'DATE-US' :
				$this->searchDefaults['OPERATOR'] = 'EQ';
				$this->searchDefaults['DATATYPE'] = 'DATE';
				break;
			case 'FLOAT' :
			case 'INTEGER' :
				$this->searchDefaults['OPERATOR'] = 'EQ';
				$this->searchDefaults['DATATYPE'] = $this->mask;
				break;
		}
	}

	//!-----------------------------------------------------------------
	// @function	EditableField::onPreRender
	// @desc		Constrói e adiciona no formulário o código JavaScript para
	//				validação de máscara, validação de tamanho máximo e mínimo e
	//				conversão de valor
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		// revalida a propriedade "readonly"
		if ($this->readOnly === NULL) {
			if ($this->_Form->readonly)
				$this->setReadonly();
			else
				$this->setReadonly(FALSE);
		}
		// adiciona script para validação de máscara de digitação
		if ($this->mask != '') {
			$args = array();
			$args[] = "mask:'{$this->mask}'";
			if ($this->mask == 'FLOAT' && is_array($this->limiters)) {
				$msg = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_FLOAT', array($this->label, $this->limiters[0], $this->limiters[1]));
				$args[] = "msg:\"" . StringUtils::escape($msg, 'javascript') . "\"";
			}
			$this->_Form->validatorCode .= sprintf("\t%s_validator.add('%s', DataTypeValidator, {%s});\n", $this->_Form->formName, $this->name, implode(',', $args));
			$this->_Form->Document->addScriptCode(sprintf($this->maskSetupScript, $this->id), 'Javascript', SCRIPT_END);
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'inputmask.js');
		}
		// adiciona script para validação de tamanho mínimo e/ou máximo
		if (isset($this->minLength) && TypeUtils::isInteger($this->minLength))
			$this->_Form->validatorCode .= sprintf("\t%s_validator.add('%s', LengthValidator, {rule:'MINLENGTH',limit:%d});\n", $this->_Form->formName, $this->name, $this->minLength);
		if (isset($this->maxLength) && TypeUtils::isInteger($this->maxLength))
			$this->_Form->validatorCode .= sprintf("\t%s_validator.add('%s', LengthValidator, {rule:'MAXLENGTH',limit:%d});\n", $this->_Form->formName, $this->name, $this->maxLength);
		// adiciona código para as transformações de valor
		if ($this->attributes['UPPER'] == 'T')
			$this->_Form->beforeValidateCode .= sprintf("\t\tfrm.elements['%s'].value = frm.elements['%s'].value.toUpperCase();\n", $this->name, $this->name);
		if ($this->attributes['LOWER'] == 'T')
			$this->_Form->beforeValidateCode .= sprintf("\t\tfrm.elements['%s'].value = frm.elements['%s'].value.toLowerCase();\n", $this->name, $this->name);
	}
}
?>