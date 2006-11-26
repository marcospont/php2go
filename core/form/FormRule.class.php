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
// $Header: /www/cvsroot/php2go/core/form/FormRule.class.php,v 1.17 2006/10/19 00:49:03 mpont Exp $
// $Date: 2006/10/19 00:49:03 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
import('php2go.util.Statement');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormRule
// @desc		A classe FormRule representa uma regra de valida��o a ser
//				aplicada sobre um campo de formul�rio: compara��o entre valores
//				de campos, obrigatoriedade condicional baseada na exist�ncia de
//				valor em outro campo ou na compara��o do valor de outro campo com
//				um valor est�tico e express�o regular
// @package		php2go.form
// @uses		Date
// @uses		Statement
// @uses		TypeUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.17 $
//!-----------------------------------------------------------------
class FormRule extends PHP2Go
{
	var $type;					// @var type string				Tipo da regra
	var $field = NULL;			// @var field string			"NULL" Nome do campo com o qual � feita a compara��o
	var $value = NULL;			// @var value string			"NULL" Valor com o qual � feita a compara��o
	var $compareType = NULL;	// @var compareType string		"NULL" Tipo de compara��o (apenas para regras que envolvem compara��o)
	var $message = NULL;		// @var message string			"NULL" Mensagem customizada em caso de falha da regra
	var $functionBody = NULL;	// @var functionBody string		"NULL" Corpo da fun��o de valida��o (quanto $type == 'JSFUNC')
	var $_valid = FALSE;		// @var _valid bool				"FALSE" Armazena o resultado da valida��o dos dados da regra
	var $_Field = NULL;			// @var _Field FormField object	"NULL" Campo no qual a regra � inserida

	//!-----------------------------------------------------------------
	// @function	FormRule::FormRule
	// @desc		Construtor da classe
	// @param		type string			Tipo da regra
	// @param		field string		"NULL" Campo de compara��o
	// @param		value string		"NULL" Valor de compara��o
	// @param		compareType string	"NULL" Tipo de dado da compara��o
	// @param		message string		"NULL" Mensagem de erro customizada
	// @param		functionBody string	"NULL" Corpo da fun��o de valida��o
	// @access		public
	//!-----------------------------------------------------------------
	function FormRule($type, $field=NULL, $value=NULL, $compareType=NULL, $message=NULL, $functionBody=NULL) {
		parent::PHP2Go();
		$this->type = strtoupper($type);
		if (!empty($field))
			$this->field = $field;
		if (!TypeUtils::isNull($value, TRUE))
			$this->value = $value;
		if (!empty($compareType))
			$this->compareType = strtoupper($compareType);
		if (!empty($message))
			$this->message = $message;
		if (!empty($functionBody))
			$this->functionBody = $functionBody;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::&fromNode
	// @desc		M�todo factory que constr�i uma regra de valida��o a partir
	//				de um nodo XML da especifica��o de formul�rios
	// @param		Node XmlNode object	Nodo da regra na especifica��o XML
	// @return		FormRule object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function &fromNode($Node) {
		$type = trim($Node->getAttribute('TYPE'));
		$field = trim($Node->getAttribute('FIELD'));
		$value = trim($Node->getAttribute('VALUE'));
		$compareType = trim($Node->getAttribute('COMPARETYPE'));
		if (!$compareType) {
			$Parent = $Node->getParentNode();
			if ($Parent->getTag() == 'DATEPICKERFIELD') {
				$compareType = 'DATE';
			} else {
				$compareType = trim($Parent->getAttribute('MASK'));
			}
		}
		$message = trim($Node->getAttribute('MESSAGE'));
		$functionBody = $Node->getData();
		$Rule = new FormRule($type, $field, $value, $compareType, resolveI18nEntry($message), $functionBody);
		return $Rule;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::setOwnerField
	// @desc		Define o campo no qual a regra ser� inserida
	// @access		public
	// @param		&Field FormField object		Campo para inclus�o da regra
	// @return		void
	//!-----------------------------------------------------------------
	function setOwnerField(&$Field) {
		$this->_Field =& $Field;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::getType
	// @desc		Retorna o tipo da regra
	// @access		public
	// @return		string Tipo: EQ, NEQ, LT, GT, LOET, GOET, REGEX, REQIF, ...
	//!-----------------------------------------------------------------
	function getType() {
		return $this->type;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::getTargetField
	// @desc		Retorna o nome do campo de compara��o definido na regra
	// @access		public
	// @return		string Nome do campo
	//!-----------------------------------------------------------------
	function getTargetField() {
		return $this->field;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::getValueArgument
	// @desc		Retorna o argumento de valor da regra (valor de compara��o,
	//				padr�o de express�o regular, ...)
	// @access		public
	// @return		mixed Argumento de valor utilizado na regra
	//!-----------------------------------------------------------------
	function getValueArgument() {
		return $this->value;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::getCompareType
	// @desc		Busca o tipo de compara��o definido na regra
	// @access		public
	// @return		string Tipo de compara��o
	//!-----------------------------------------------------------------
	function getCompareType() {
		return $this->compareType;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::getMessage
	// @desc		Retorna a mensagem de erro customizada definida para a regra
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getMessage() {
		return $this->message;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::&getOwnerField
	// @desc		Retorna o campo onde a regra est� inserida
	// @access		public
	// @return		FormField object
	//!-----------------------------------------------------------------
	function &getOwnerField() {
		return $this->_Field;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::&getOwnerForm
	// @desc		Retorna o formul�rio ao qual a regra pertence
	// @access		public
	// @return		Form object
	//!-----------------------------------------------------------------
	function &getOwnerForm() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_Field, 'FormField'))
			$result =& $this->_Field->getOwnerForm();
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::getScriptCode
	// @desc		Monta o c�digo JavaScript de defini��o da regra
	// @access		public
	// @return		string C�digo JS da regra
	//!-----------------------------------------------------------------
	function getScriptCode() {
		if (TypeUtils::isInstanceOf($this->_Field, 'FormField')) {
			$args = array();
			$Form =& $this->_Field->_Form;
			$args[] = "ruleType:\"{$this->type}\"";
			if ($this->compareType != NULL)
				$args[] = "dataType:\"{$this->compareType}\"";
			if ($this->field != NULL)
				$args[] = "peerField:\"{$this->field}\"";
			if ($this->value != NULL) {
				$value = ($this->type == 'REGEX' ? preg_replace("|\/|", "/", $this->value) : "\"{$this->value}\"");
				$args[] = "peerValue:{$value}";
			}
			if ($this->message != NULL)
				$args[] = "msg:\"" . $this->message . "\"";
			if ($this->type == 'JSFUNC') {
				$funcName = PHP2Go::generateUniqueId(preg_replace("~[^\w]+~", "", $this->_Field->getName()) . 'ValidateFunc');
				$funcBody = rtrim(ltrim($this->functionBody, "\r\n "));
				preg_match("/^([\t]+)/", $funcBody, $matches);
				$funcBody = (isset($matches[1]) ? preg_replace("/^\t{" . strlen($matches[1]) . "}/m", "\t\t", $funcBody) : $funcBody);
				$funcBody = preg_replace("~this~", "element", $funcBody);
				$args[] = "func:{$funcName}";
				$Form->Document->addScriptCode(
					"\tfunction {$funcName}(element) {\n" . $funcBody . "\n\t}"
				);
			}
			return sprintf("\t%s_validator.add('%s', RuleValidator, {%s});\n", $Form->formName, $this->_Field->validationName, implode(',', $args));
		}
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::isValid
	// @desc		Verifica se os dados da regra s�o v�lidos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if ($this->_valid === TRUE)
			return $this->_valid;
		if (TypeUtils::isInstanceOf($this->_Field, 'FormField')) {
			$this->_valid = TRUE;
			// verifica se o tipo da regra � v�lido
			if (!ereg("^REGEX$|^REQIF$|^((REQIF)?(EQ|NEQ|GT|LT|GOET|LOET))$|^JSFUNC$", $this->type)) {
				$this->_valid = FALSE;
			// se � uma regra de obrigatoriedade condicional, com ou sem compara��o, verifica se foi fornecido um campo para a compara��o
			} elseif (ereg("^REQIF(EQ|NEQ|GT|LT|LOET|GOET)?$", $this->type, $matches) && (TypeUtils::isNull($this->field) || $this->field == $this->_Field->getName())) {
				$this->_valid = FALSE;
			// se � uma regra de obrigatoriedade condicional com compara��o, o valor de compara��o � obrigat�rio
			} elseif (ereg("^REQIF(EQ|NEQ|GT|LT|LOET|GOET)$", $this->type, $matches) && TypeUtils::isNull($this->value, TRUE)) {
				$this->_valid = FALSE;
			// se � uma regra de compara��o simples, verifica se foi fornecido um campo ou um valor para a compara��o
			} elseif (ereg("^EQ|NEQ|GT|LT|GOET|LOET$", $this->type) && (TypeUtils::isNull($this->field) || $this->field == $this->_Field->getName()) && TypeUtils::isNull($this->value)) {
				$this->_valid = FALSE;
			// se � uma regra de express�o regular, o valor da express�o � obrigat�rio
			} elseif ($this->type == 'REGEX' && TypeUtils::isNull($this->value)) {
				$this->_valid = FALSE;
			// se � uma regra que depende do resultado de uma fun��o JavaScript, o corpo da fun��o � obrigat�rio
			} elseif ($this->type == 'JSFUNC' && TypeUtils::isNull($this->functionBody)) {
				$this->_valid = FALSE;
			// valida o tipo de compara��o, se foi fornecido
			} elseif (!TypeUtils::isNull($this->compareType) && !preg_match("/^(INTEGER|FLOAT|DATE)$/", $this->compareType)) {
				$this->compareType = 'STRING';
			} else {
				// elimina o atributo value nas regras do tipo REQIF
				if ($this->type == 'REQIF')
					$this->value = NULL;
				// elimina o atributo comparetype nas regras do tipo REGEX
				if ($this->type == 'REGEX')
					$this->compareType = NULL;
				// corrige valor de express�o regular
				if ($this->type == 'REGEX') {
					if ($this->value[0] == '/')
						$this->value = substr($this->value, 1);
					if ($this->value{(strlen($this->value)-1)} == '/')
						$this->value = substr($this->value, 0, -1);
					// insere quotes nos caracteres "/" n�o precedidos por "\"
					$this->value = '/' . ereg_replace('([^\])/', '\\1\/', $this->value) . '/';
				}
			}
			if (!$this->_valid)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_WRONG_RULE', $this->_getRuleInfo()), E_USER_ERROR, __FILE__, __LINE__);
			return $this->_valid;
		} else {
			$this->_valid = FALSE;
			return $this->_valid;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::onDataBind
	// @desc		Resolve express�es e vari�veis utilizadas no atributo VALUE da regra
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		// resolve vari�veis e express�es no atributo value
		if (!empty($this->value) && preg_match("/~[^~]+~/", $this->value)) {
			$Form =& $this->getOwnerForm();
			$this->value = $Form->evaluateStatement($this->value);
		}
		// processa express�es de data
		$regs = array();
		if ($this->compareType == 'DATE' && !empty($this->value) && !Date::isEuroDate($this->value, $regs) && !Date::isUsDate($this->value, $regs))
			$this->value = Date::parseFieldExpression($this->value);
	}

	//!-----------------------------------------------------------------
	// @function	FormRule::_getRuleInfo
	// @desc		Monta informa��es da regra, para exibi��o de mensagens de erro
	// @access		private
	// @return		string Texto descritivo da regra
	//!-----------------------------------------------------------------
	function _getRuleInfo() {
		$info = $this->_Field->getName() . " - [{$this->type}";
		if (!empty($this->field))
			$info .= "; {$this->field}";
		if (!empty($this->value))
			$info .= "; {$this->value}";
		if (!empty($this->message))
			$info .= "; {$this->message}";
		$info .= ']';
		return $info;
	}
}

?>