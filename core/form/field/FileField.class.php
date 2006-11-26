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
// $Header: /www/cvsroot/php2go/core/form/field/FileField.class.php,v 1.26 2006/10/26 04:55:13 mpont Exp $
// $Date: 2006/10/26 04:55:13 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FileField
// @desc		Esta classe constrói um campo de formulário do tipo FILE,
//				para upload de arquivos gravados na máquina do usuário
// @package		php2go.form.field
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.26 $
//!-----------------------------------------------------------------
class FileField extends FormField
{
	//!-----------------------------------------------------------------
	// @function	FileField::FileField
	// @desc		Construtor da classe FileField, inicializa os atributos do campo
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @access		public
	//!-----------------------------------------------------------------
	function FileField(&$Form) {
		parent::FormField($Form);
		$this->htmlType = 'FILE';
		$this->searchable = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	FileField::display
	// @desc		Gera o código HTML do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && parent::onPreRender());
		print sprintf("<input type=\"file\" id=\"%s\" name=\"%s\" size=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s>",
			$this->id, $this->name, $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'],
			$this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'], $this->attributes['STYLE'],
			$this->attributes['READONLY'], $this->attributes['DISABLED'], $this->attributes['DATASRC'],
			$this->attributes['DATAFLD']
		);
	}

	//!-----------------------------------------------------------------
	// @function	FileField::getValue
	// @desc		Sobrescreve o método getValue da classe FormField
	// @note		O valor de um campo do tipo FileField é buscado do vetor global $_FILES
	// @return		string Valor do campo
	// @access		public
	//!-----------------------------------------------------------------
	function getValue() {
		if (empty($_FILES) || !isset($_FILES[$this->getName()]))
			return '';
		return $_FILES[$this->getName()]['name'];
	}

	//!-----------------------------------------------------------------
	// @function	FileField::setSize
	// @desc		Altera ou define o tamanho do campo
	// @param		size int	Tamanho para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		$this->attributes['SIZE'] = TypeUtils::parseInteger($size);
	}

	//!-----------------------------------------------------------------
	// @function	FileField::setMaxFileSize
	// @desc		Define o tamanho máximo permitido para o upload do arquivo
	// @param		maxSize string	Tamanho máximo para o arquivo
	// @note		Este atributo aceita valores no padrão 500K, 2M ou números inteiros
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMaxFileSize($maxSize) {
		if (!empty($maxSize))
			$this->attributes['MAXFILESIZE'] = $maxSize;
	}

	//!-----------------------------------------------------------------
	// @function	FileField::setAllowedTypes
	// @desc		Define os tipos mime aceitos para o upload deste arquivo
	// @param		types string	Lista de tipos mime aceitos
	// @note		O parâmetro deve ser uma lista de tipos mime separados por vírgula
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAllowedTypes($types) {
		if (!empty($types)) {
			$types = explode(',', TypeUtils::parseString($types));
			$this->attributes['ALLOWEDTYPES'] = $types;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileField::setSaveFunction
	// @desc		Define a função customizada de gravação do arquivo
	// @param		function mixed	Função, método estático ou dinâmico a ser utilizado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSaveFunction($function) {
		if (!empty($function))
			$this->attributes['SAVEFUNCTION'] = $function;
		else
			$this->attributes['SAVEFUNCTION'] = NULL;
	}

	//!-----------------------------------------------------------------
	// @function	FileField::setSavePath
	// @desc		Define o caminho onde o arquivo deve ser salvo
	// @param		path string		Caminho relativo (em relação ao script atual)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSavePath($path) {
		$this->attributes['SAVEPATH'] = $path;
	}

	//!-----------------------------------------------------------------
	// @function	FileField::setSaveName
	// @desc		Seta o nome de gravação do arquivo
	// @param		name string		Nome de gravação do arquivo de upload
	// @note		Este atributo aceita variáveis no padrão ~variavel~
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSaveName($name) {
		if (!empty($name))
			$this->attributes['SAVENAME'] = $name;
		else
			$this->attributes['SAVENAME'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	FileField::setSaveMode
	// @desc		Seta o modo de criação do arquivo de upload
	// @param		mode int		Modo de gravação do arquivo (Ex: 0755)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSaveMode($mode) {
		if (!empty($mode)) {
			$mode = ereg_replace("[^0-9]+", "", TypeUtils::parseString($mode));
			eval("\$this->attributes['SAVEMODE'] = {$mode};");
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileField::setOverwrite
	// @desc		Habilita ou impede que arquivos existentes sejam sobrescritos na operação de upload
	// @param		overwrite bool	Habilitar ou desabilitar sobrescrita
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setOverwrite($overwrite) {
		$this->attributes['OVERWRITE'] = TypeUtils::toBoolean($overwrite);
	}

	//!-----------------------------------------------------------------
	// @function	FileField::isValid
	// @desc		Este método permite validar e executar a operação completa
	//				de upload do arquivo, se o POST do form for tratado utilizando
	//				os métodos isPosted() e isValid()
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		$result = parent::isValid();
		if ($this->attributes['ONVALIDATE'] === TRUE) {
			$attrs = $this->attributes;
			$attrs['FIELDNAME'] = $this->getName();
			$result &= Validator::validate('php2go.validation.UploadValidator', $attrs);
			// define o handler de upload (dados do arquivo original e do destino) como o valor submetido para o campo
			$Uploader =& FileUpload::getInstance();
			if ($handler = $Uploader->getHandlerByName($this->getName()))
				parent::setSubmittedValue($handler);
		}
		return TypeUtils::toBoolean($result);
	}

	//!-----------------------------------------------------------------
	// @function	FileField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// habilita upload no formulário
		$this->_Form->hasUpload = TRUE;
		// tamanho do campo
		// 1) atributo SIZE
		if (isset($attrs['SIZE']) && TypeUtils::isInteger($attrs['SIZE']))
			$this->setSize($attrs['SIZE']);
		// 2) atributo LENGTH
		elseif (isset($attrs['LENGTH']) && TypeUtils::isInteger($attrs['LENGTH']))
			$this->setSize($attrs['LENGTH']);
		// 3) constante da classe
		else
			$this->setSize(15);
		// tamanho máximo de arquivo
		$this->setMaxFileSize(@$attrs['MAXFILESIZE']);
		// tipos MIME permitidos
		$this->setAllowedTypes(@$attrs['ALLOWEDTYPES']);
		// callback para gravação do arquivo
		$this->setSaveFunction(@$attrs['SAVEFUNCTION']);
		// caminho de gravação do arquivo
		$this->setSavePath(@$attrs['SAVEPATH']);
		// nome de gravação do arquivo
		$this->setSaveName(@$attrs['SAVENAME']);
		// modo de gravação do arquivo
		$this->setSaveMode(@$attrs['SAVEMODE']);
		// sobrescrita de arquivos existentes
		if (isset($attrs['OVERWRITE']))
			$this->setOverwrite(resolveBooleanChoice($attrs['OVERWRITE']));
		// upload na validação
		if (isset($attrs['UPLOADONVALIDATE']))
			$this->attributes['ONVALIDATE'] = resolveBooleanChoice($attrs['UPLOADONVALIDATE']);
		else
			$this->attributes['ONVALIDATE'] = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	FileField::onDataBind
	// @desc		Resolve variáveis no atributo SAVENAME da especificação do campo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		if (preg_match("/~[^~]+~/", $this->attributes['SAVENAME']))
			$this->attributes['SAVENAME'] = $this->_Form->evaluateStatement($this->attributes['SAVENAME']);
	}
}
?>