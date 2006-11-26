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
// $Header: /www/cvsroot/php2go/core/form/field/MemoField.class.php,v 1.30 2006/10/29 17:31:58 mpont Exp $
// $Date: 2006/10/29 17:31:58 $

//------------------------------------------------------------------
import('php2go.form.field.EditableField');
import('php2go.template.Template');
//------------------------------------------------------------------

// @const	MEMOFIELD_DEFAULT_COLS		"40"
// Número de colunas padrão na c7onstrução de campos do tipo TEXTAREA
define('MEMOFIELD_DEFAULT_COLS', 40);
// @const	MEMOFIELD_DEFAULT_ROWS		"5"
// Número de linhas padrão para campos do tipo TEXTAREA
define('MEMOFIELD_DEFAULT_ROWS', 5);

//!-----------------------------------------------------------------
// @class		MemoField
// @desc		Classe responsável por construir um INPUT HTML do
//				tipo TEXTAREA, para edição de texto com várias linhas
// @package		php2go.form.field
// @extends		EditableField
// @uses		Template
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.30 $
//!-----------------------------------------------------------------
class MemoField extends EditableField
{
	var $charCountControl = FALSE; // @var charCountControl bool	"FALSE" Indica se deve ser incluído um contador de caracteres

	//!-----------------------------------------------------------------
	// @function	MemoField::MemoField
	// @desc		Construtor da classe MemoField, inicializa os atributos do campo
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function MemoField(&$Form, $child=FALSE) {
		parent::EditableField($Form, $child);
		$this->htmlType = 'TEXTAREA';
	}

	//!-----------------------------------------------------------------
	// @function	MemoField::display
	// @desc		Gera o código HTML do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		if (isset($this->maxLength) && $this->charCountControl) {
			print sprintf("
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
  <tr><td><textarea id=\"%s\" name=\"%s\" cols=\"%s\" rows=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s>%s</textarea></td></tr>
  <tr><td align=\"right\"><span%s>%s</span>&nbsp;<input type=\"text\" id=\"%s_count\" name=\"%s_count\" size=\"5\" value=\"%s\" disabled%s></td></tr>
</table><script type=\"text/javascript\">new MemoField('%s', %s);</script>",
				$this->id, $this->name, $this->attributes['COLS'], $this->attributes['ROWS'], $this->label,
				$this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'],  $this->attributes['TABINDEX'],
				$this->attributes['STYLE'], $this->attributes['INLINESTYLE'], $this->attributes['READONLY'],
				$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'],
				$this->value, $this->_Form->getLabelStyle(), PHP2Go::getLangVal('MEMO_COUNT_LABEL'), $this->id,
				$this->name,  (max(0, $this->maxLength-strlen($this->value))), $this->attributes['STYLE'],
				$this->id, $this->maxLength
			);
		} else {
			print sprintf("<textarea id=\"%s\" name=\"%s\" cols=\"%s\" rows=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s>%s</textarea>",
				$this->id, $this->name, $this->attributes['COLS'], $this->attributes['ROWS'], $this->label,
				$this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
				$this->attributes['STYLE'], $this->attributes['INLINESTYLE'], $this->attributes['READONLY'],
				$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'],
				$this->value
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	MemoField::setCols
	// @desc		Define o número de colunas (largura) do campo
	// @access		public
	// @param		cols int	Número de colunas
	// @return		void
	//!-----------------------------------------------------------------
	function setCols($cols) {
		$this->attributes['COLS'] = $cols;
	}

	//!-----------------------------------------------------------------
	// @function	MemoField::setRows
	// @desc		Define o número de linhas (altura) do campo
	// @access		public
	// @param		rows int 	Número de linhas
	// @return		void
	//!-----------------------------------------------------------------
	function setRows($rows) {
		$this->attributes['ROWS'] = $rows;
	}

	//!-----------------------------------------------------------------
	// @function	MemoField::setWidth
	// @desc		Define a largura em pixels (via CSS) do campo memo
	// @param		width int	Largura, em pixels
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = $width;
	}

	//!-----------------------------------------------------------------
	// @function	MemoField::setWidth
	// @desc		Define a altura em pixels (via CSS) do campo memo
	// @param		height int	Altura, em pixels
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setHeight($height) {
		if (TypeUtils::isInteger($height))
			$this->attributes['HEIGHT'] = $height;
	}

	//!-----------------------------------------------------------------
	// @function	MemoField::charCount
	// @desc		Habilita ou desabilita o controle de máximo de caracteres no campo
	//				com exibição no número de caracteres restantes em um campo de edição
	// @access		public
	// @param		setting bool	Valor para a propriedade (habilitado ou desabilitado)
	// @param		maxLength int	"NULL" Permite setar o número máximo de caracteres para o campo
	// @return		void
	// @note		O mesmo efeito é obtido na seguinte seqüencia de comandos:<br>
	//				<pre>
	//
	//				$field->charCount(TRUE);
	//				$field->setMaxLength(20);
	//
	//				</pre>
	//!-----------------------------------------------------------------
	function charCount($setting, $maxLength=NULL) {
		$this->charCountControl = TypeUtils::toBoolean($setting);
		if (TypeUtils::isInteger($maxLength))
			parent::setMaxLength($maxLength);
	}

	//!-----------------------------------------------------------------
	// @function	MemoField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// número de colunas
		if (isset($attrs['COLS']) && TypeUtils::isInteger($attrs['COLS']))
			$this->setCols($attrs['COLS']);
		else
			$this->setCols(MEMOFIELD_DEFAULT_COLS);
		// número de linhas
		if (isset($attrs['ROWS']) && TypeUtils::isInteger($attrs['ROWS']))
			$this->setRows($attrs['ROWS']);
		else
			$this->setRows(MEMOFIELD_DEFAULT_ROWS);
		// largura em pixels
		$this->setWidth(@$attrs['WIDTH']);
		// altura em pixels
		$this->setHeight(@$attrs['HEIGHT']);
		// contador de caracteres
		$this->charCount(resolveBooleanChoice(@$attrs['CHARCOUNT']));
	}
	
	//!-----------------------------------------------------------------
	// @function	MemoField::onDataBind
	// @desc		Evita arrays como valor do campo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		if (is_array($this->value))
			$this->value = '';			
	}	

	//!-----------------------------------------------------------------
	// @function	MemoField::onPreRender
	// @desc		Define o INLINESTYLE do componente a partir do valor
	//				dos atributos WIDTH e HEIGHT fornecidos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		if (isset($this->maxLength) && $this->charCountControl)
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/memofield.js');
		$inlineStyle = array();
		if (isset($this->attributes['WIDTH']))
			$inlineStyle[] = "width:{$this->attributes['WIDTH']}px";
		if (isset($this->attributes['HEIGHT']))
			$inlineStyle[] = "height:{$this->attributes['HEIGHT']}px";
		$this->attributes['INLINESTYLE'] = (empty($inlineStyle) ? '' : " style=\"" . join(';', $inlineStyle) . "\"");
	}
}
?>