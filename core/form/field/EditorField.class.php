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
// $Header: /www/cvsroot/php2go/core/form/field/EditorField.class.php,v 1.26 2006/10/26 04:55:13 mpont Exp $
// $Date: 2006/10/26 04:55:13 $

//------------------------------------------------------------------
import('php2go.form.field.MemoField');
import('php2go.net.HttpRequest');
import('php2go.template.Template');
//------------------------------------------------------------------

// @const EDITOR_DEFAULT_WIDTH "500"
// Largura default do editor HTML, em pixels
define('EDITOR_DEFAULT_WIDTH', 500);
// @const EDITOR_DEFAULT_HEIGHT "200"
// Altura default do editor HTML, em pixels
define('EDITOR_DEFAULT_HEIGHT', 200);

//!-----------------------------------------------------------------
// @class		EditorField
// @desc		Classe responsável por construir um editor HTML WYSIWYG
//				com formatação de fonte, cor, inclusão de imagens e links,
//				marcação e indentação
// @package		php2go.form.field
// @uses		HttpRequest
// @uses		Template
// @extends		MemoField
// @author		Marcos Pont
// @version		$Revision: 1.26 $
// @note		O código gerado para o campo EditorField só irá construir
//				o editor WYSIWYG se o navegador do usuário for o Internet
//				Explorer. Para outros navegadores, será gerado um campo
//				do tipo MemoField
//!-----------------------------------------------------------------
class EditorField extends FormField
{
	var $readOnly = FALSE;	// @var readOnly bool	Indica que o componente está no modo somente-leitura

	//!-----------------------------------------------------------------
	// @function	EditorField::EditorField
	// @desc		Construtor da classe EditorField, inicializa os atributos do campo
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @access		public
	//!-----------------------------------------------------------------
	function EditorField(&$Form) {
		parent::FormField($Form);
		$this->htmlType = 'IFRAME';
	}

	//!-----------------------------------------------------------------
	// @function	EditorField::getFocusId
	// @desc		Retorna o ID do IFRAME associado ao editor, que
	//				deverá receber foco quando o label do campo for clicado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFocusId() {
		return "{$this->id}_composition";
	}

	//!-----------------------------------------------------------------
	// @function	EditorField::setReadonly
	// @desc		Permite habilitar ou desabilitar o atributo de somente leitura do campo
	// @param		setting bool	"TRUE" Valor para o atributo, TRUE torna o campo somente leitura
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setReadonly($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['READONLY'] = " readonly";
			$this->readOnly = TRUE;
		} else {
			$this->attributes['READONLY'] = "";
			$this->readOnly = FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	EditorField::setWidth
	// @desc		Define a largura em pixels (via CSS) do editor HTML
	// @param		width int	Largura, em pixels
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = $width;
	}

	//!-----------------------------------------------------------------
	// @function	EditorField::setWidth
	// @desc		Define a altura em pixels (via CSS) do editor HTML
	// @param		height int	Altura, em pixels
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setHeight($height) {
		if (TypeUtils::isInteger($height))
			$this->attributes['HEIGHT'] = $height;
	}

	//!-----------------------------------------------------------------
	// @function	EditorField::setStylesheet
	// @desc		Associa um arquivo de folha de estilos (CSS) ao
	//				documento HTML produzido pelo editor
	// @param		stylesheet string	Caminho relativo para o arquivo CSS
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setStylesheet($stylesheet) {
		if (!empty($stylesheet))
			$this->attributes['STYLESHEET'] = $stylesheet;
	}

	//!-----------------------------------------------------------------
	// @function	EditorField::setResizeMode
	// @desc		Configura o modo de redimensionamento da área do editor
	// @param		resizable string	Valor do atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setResizeMode($resizable) {
		$expr = "/^(horizontal|vertical|both|none)$/i";
		$val = trim($resizable);
		if (preg_match($expr, $val))
			$this->attributes['RESIZEMODE'] = $val;
		else
			$this->attributes['RESIZEMODE'] = 'none';
	}

	//!-----------------------------------------------------------------
	// @function	EditorField::display
	// @desc		Gera o código HTML do componente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'editorfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('labelStyle', $this->_Form->getLabelStyle());
		$Tpl->assign('inputStyle', $this->attributes['STYLE']);
		$options = array();
		$options[] = "'readOnly':" . ($this->disabled || $this->attributes['READONLY'] || $this->_Form->readonly ? 'true' : 'false');
		$options[] = "'resizeMode':'{$this->attributes['RESIZEMODE']}'";
		if (isset($this->attributes['STYLESHEET']))
			$options[] = "'styleSheet':'" . $this->attributes['STYLESHEET'] . "'";
		$Tpl->assign('options', '{' . join(',', $options) . '}');
		$Tpl->assign('iconPath', PHP2GO_ICON_PATH);
		$Tpl->assign('resizeMode', $this->attributes['RESIZEMODE']);
		$Tpl->assign('width', (isset($this->attributes['WIDTH']) ? $this->attributes['WIDTH'] : EDITOR_DEFAULT_WIDTH));
		$Tpl->assign('height', (isset($this->attributes['HEIGHT']) ? $this->attributes['HEIGHT'] : EDITOR_DEFAULT_HEIGHT));
		$Tpl->assign('hiddenField', sprintf("<input type=\"hidden\" id=\"%s\" name=\"%s\" value=\"%s\" title=\"%s\"%s%s>",
				$this->id, $this->name, htmlspecialchars($this->value), $this->label, $this->attributes['DATASRC'], $this->attributes['DATAFLD']));
		$Tpl->assign(PHP2Go::getLangVal('EDITOR_VARS'));
		$Tpl->assign('fontNames', array(
			'arial,helvetica,sans-serif' => 'Arial',
			'arial black,avant garde' => 'Arial Black',
			'book antiqua,palatino' => 'Book Antiqua',
			'comic sans ms,sand' => 'Comic Sans',
			'courier new,courier' => 'Courier New',
			'georgia,palatino' => 'Georgia',
			'helvetica' => 'Helvetica',
			'impact,chicago' => 'Impact',
			'symbol' => 'Symbol',
			'tahoma,arial,helvetica,sans-serif' => 'Tahoma',
			'terminal,monaco' => 'Terminal',
			'times new roman,times' => 'Times',
			'trebuchet ms,geneva' => 'Trebuchet',
			'verdana,geneva' => 'Verdana',
			'webdings' => 'Webdings',
			'wingdings,zapf dingbats' => 'Wingdings'
		));
		$Tpl->assign('emoticons', array(
			'smiley', 'lol', 'surprise', 'blink', 'sad', 'confused', 'disappointed',
			'cry', 'shame', 'glasses', 'angry', 'angel', 'devil', 'creekingteeth',
			'nerd', 'sarcastic', 'secret', 'party', 'thumbup', 'thumbdown', 'boy',
			'girl', 'hug', 'heart', 'brokenheart', 'kiss', 'gift', 'flower',
			'bulb', 'coffee', 'beer', 'cake', 'gift', 'camera', 'phone',
			'moon', 'star', 'email', 'clock',  'plate', 'pizza', 'ball',
			'computer', 'car', 'plane', 'umbrella', 'island', 'storm', 'money'
		));
		$Tpl->display();
	}

	//!-----------------------------------------------------------------
	// @function	EditorField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// readonly
		$readOnly = (resolveBooleanChoice(@$attrs['READONLY']) || $this->_Form->readonly);
		if ($readOnly)
			$this->setReadonly();
		// largura em pixels
		$this->setWidth(@$attrs['WIDTH']);
		// altura em pixels
		$this->setHeight(@$attrs['HEIGHT']);
		// arquivo CSS a ser utilizado
		$this->setStylesheet(@$attrs['STYLESHEET']);
		// modo de redimensionamento
		$this->setResizeMode(@$attrs['RESIZEMODE']);
	}

	//!-----------------------------------------------------------------
	// @function	EditorField::onPreRender
	// @desc		Realiza configurações necessárias antes da construção
	//				do código HTML do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		$this->listeners = array();
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/editorfield.js');
		$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'colorpicker.css');
		$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'editorfield.css');
		// revalida a propriedade "readonly"
		if ($this->readOnly === NULL) {
			if ($this->_Form->readonly)
				$this->setReadonly();
			else
				$this->setReadonly(FALSE);
		}
	}
}
?>