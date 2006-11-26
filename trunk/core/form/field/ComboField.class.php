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
// $Header: /www/cvsroot/php2go/core/form/field/ComboField.class.php,v 1.29 2006/10/26 04:55:12 mpont Exp $
// $Date: 2006/10/26 04:55:12 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		ComboField
// @desc		A classe ComboField monta campos do tipo SELECT com
//				todas as op��es explicitadas na defini��o do arquivo XML
// @package		php2go.form.field
// @uses		TypeUtils
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.29 $
//!-----------------------------------------------------------------
class ComboField extends FormField
{
	var $optionCount = 0;				// @var optionCount int				"0" Total de op��es do grupo radio
	var $optionAttributes = array();	// @var optionAttributes array		"array()" Vetor de atributos das op��es

	//!-----------------------------------------------------------------
	// @function	ComboField::ComboField
	// @desc		Construtor da classe ComboField
	// @access		public
	// @param		&Form Form object	Formul�rio no qual o campo ser� inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo � membro de um campo composto
	//!-----------------------------------------------------------------
	function ComboField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'SELECT';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::display
	// @desc		Gera o c�digo HTML do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && parent::onPreRender());
		$name = ($this->attributes['MULTIPLE'] && substr($this->name, -2) != '[]' ? $this->name . '[]' : $this->name);
		print sprintf("\n<select id=\"%s\" name=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s%s>\n",
				$this->id, $name, $this->label, $this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
				$this->attributes['STYLE'], ($this->attributes['MULTIPLE'] ? ' multiple' : ''), $this->attributes['SIZE'],
				$this->attributes['WIDTH'], $this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD']
		);
		if (!$this->attributes['NOFIRST'])
			print sprintf("\t<option value=\"\">%s</option>\n", $this->attributes['FIRST']);
		$hasValue = ((is_array($this->value) && !empty($this->value)) || $this->value != '');
		$arrayValue = (is_array($this->value));
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {
			$key = $this->optionAttributes[$i]['VALUE'];
			if ($hasValue) {
				if ($arrayValue)
					$optionSelected = in_array($key, $this->value) ? ' selected' : '';
				else
					$optionSelected = !strcasecmp($key, $this->value) ? ' selected' : '';
			} else {
				$optionSelected = '';
			}
			print sprintf("\t<option value=\"%s\"%s%s>%s</option>\n",
					$key, (!empty($this->optionAttributes[$i]['ALT']) ? " title=\"{$this->optionAttributes[$i]['ALT']}\"" : ''),
					$optionSelected, $this->optionAttributes[$i]['CAPTION']
			);
		}
		print "</select>";
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::setName
	// @desc		Define o campo como escolha m�ltipla se o nome
	//				contiver os caracteres "[]" no final
	// @param		newName string	Nome para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setName($newName) {
		if (preg_match("/\[\]$/", $newName))
			$this->setMultiple();
		parent::setName($newName);
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::getDisplayValue
	// @desc		Monta uma representa��o compreens�vel
	//				do valor do campo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getDisplayValue() {
		$display = NULL;
		$value = $this->value;
		$arrayValue = is_array($value);
		foreach ($this->optionAttributes as $index => $data) {
			if (!$arrayValue && $data['VALUE'] == $value) {
				$display = $data['CAPTION'];
				break;
			}
			if ($arrayValue && in_array($data['VALUE'], $value))
				$display[] = $data['CAPTION'];
		}
		return (is_array($display) ? '(' . implode(', ', $display) . ')' : $display);
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::getOptions
	// @desc		Retorna o vetor de op��es inseridas no campo
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getOptions() {
		return $this->optionAttributes;
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::getOptionCount
	// @desc		Busca o n�mero de op��es inseridas no campo de sele��o
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getOptionCount() {
		return $this->optionCount;
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::setFirstOption
	// @desc		O campo constru�do com a classe ComboField possui por padr�o
	//				uma primeira op��o em branco n�o selecion�vel na lista
	//				de op��es. Este m�todo permite definir um texto para este item
	// @param		first string	Texto para a primeira op��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFirstOption($first) {
		$this->attributes['FIRST'] = ($first ? resolveI18nEntry($first) : '');
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::disableFirstOption
	// @desc		Desabilita ou habilita a inser��o de uma primeira op��o
	//				em branco na lista de op��es
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function disableFirstOption($setting=TRUE) {
		$this->attributes['NOFIRST'] = (bool)$setting;
		if ($this->attributes['NOFIRST'])
			$this->attributes['FIRST'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::setMultiple
	// @desc		Habilita ou desabilita a possibilidade de sele��o de m�ltiplas op��es na lista
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMultiple($setting=TRUE) {
		$this->attributes['MULTIPLE'] = (bool)$setting;
		$this->searchDefaults['OPERATOR'] = ($this->attributes['MULTIPLE'] ? 'IN' : 'EQ');
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::setSize
	// @desc		O atributo SIZE de um campo do tipo SELECT define o n�mero
	//				de op��es vis�veis na constru��o do campo, ou seja, a altura
	//				do campo em n�mero de linhas
	// @param		size int	Quantidade de op��es v�siveis
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = " size=\"{$size}\"";
		else
			$this->attributes['SIZE'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::setWidth
	// @desc		Define a largura da lista de op��es, em pixels
	// @param		width int	Largura para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = " style=\"width:{$width}px\"";
		else
			$this->attributes['WIDTH'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::addOption
	// @desc		Adiciona uma nova op��o na lista de sele��o
	// @param		value mixed		Valor do item
	// @param		caption string	Caption do item
	// @param		alt string		"" Texto alternativo
	// @param		index int		"NULL" �ndice onde a op��o deve ser inserida
	// @note		O �ndice de inser��o � baseado em zero e deve ser maior que zero
	//				e menor do que o total de op��es j� inseridas. Com valor NULL para
	//				o par�metro $index, a op��o ser� inserida no final da lista
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function addOption($value, $caption, $alt="", $index=NULL) {
		$currentCount = $this->getOptionCount();
		if ($index > $currentCount || $index < 0) {
			return FALSE;
		} else {
			$newOption = array();
			$value = trim(strval($value));
			if ($value == '')
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_COMBOOPTION_VALUE', array(($currentCount-1), $this->name)), E_USER_ERROR, __FILE__, __LINE__);
			else
				$newOption['VALUE'] = $value;
			if (!$caption || trim($caption) == '')
				$newOption['CAPTION'] = $newOption['VALUE'];
			else
				$newOption['CAPTION'] = trim($caption);
			$newOption['ALT'] = trim($alt);
			if ($index == $currentCount || !TypeUtils::isInteger($index)) {
				$this->optionAttributes[$currentCount] = $newOption;
			} else {
				for ($i=$currentCount; $i>$index; $i--) {
					$this->optionAttributes[$i] = $this->optionAttributes[$i-1];
				}
				$this->optionAttributes[$index] = $newOption;
			}
			$this->optionCount++;
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::removeOption
	// @desc		Remove uma op��o da lista de sele��o
	// @param		index int	�ndice a ser removido
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function removeOption($index) {
		$currentCount = $this->getOptionCount();
		if ($currentCount == 1 || !TypeUtils::isInteger($index) || $index >= $currentCount || $index < 0) {
			return FALSE;
		} else {
			for ($i=$index; $i<($currentCount-1); $i++) {
				$this->optionAttributes[$i] = $this->optionAttributes[$i+1];
			}
			unset($this->optionAttributes[$currentCount-1]);
			$this->optionCount--;
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::onLoadNode
	// @desc		M�todo respons�vel por processar atributos e nodos filhos
	//				provenientes da especifica��o XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// escolha m�ltipla
		$this->setMultiple(($this->attributes['MULTIPLE'] || resolveBooleanChoice(@$attrs['MULTIPLE'])));
		// tamanho
		$size = @$attrs['SIZE'];
		(!$size && $this->attributes['MULTIPLE']) && ($size = 2);
		($size) && ($attrs['NOFIRST'] = 'T');
		$this->setSize($size);
		// texto da primeira op��o
		$this->setFirstOption(@$attrs['FIRST']);
		// primeira op��o (vazia ou n�o) desabilitada
		$this->disableFirstOption(resolveBooleanChoice(@$attrs['NOFIRST']));
		// largura em pixels
		$this->setWidth(@$attrs['WIDTH']);
		// op��es
		if (isset($children['OPTION'])) {
			$options = TypeUtils::toArray($children['OPTION']);
			for ($i=0, $s=sizeof($options); $i<$s; $i++)
				$this->addOption($options[$i]->getAttribute('VALUE'), $options[$i]->getAttribute('CAPTION'), $options[$i]->getAttribute('ALT'));
		}
	}

	//!-----------------------------------------------------------------
	// @function	ComboField::onPreRender
	// @desc		Define um tamanho m�nimo para campos SELECT de escolha m�ltipla
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if ($this->attributes['MULTIPLE'] && substr($this->validationName, -2) != '[]')
			$this->validationName .= '[]';
		parent::onPreRender();
		if ($this->attributes['MULTIPLE'] && !$this->attributes['SIZE'])
			$this->attributes['SIZE'] = " size=\"2\"";
	}
}
?>