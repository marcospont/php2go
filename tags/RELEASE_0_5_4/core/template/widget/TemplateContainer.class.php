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
// $Header: /www/cvsroot/php2go/core/template/widget/TemplateContainer.class.php,v 1.3 2006/07/12 07:18:46 mpont Exp $
// $Date: 2006/07/12 07:18:46 $

//!-----------------------------------------------------------------
// @class		TemplateContainer
// @desc		O widget TemplateContainer permite criar um template e atribuir
//				um valor para o conteúdo interno deste template. Pode ser muito útil
//				para centralizar em um ponto único o código de containers básicos das
//				aplicações: tabelas que contêm listas, tabelas que contêm formulários,
//				entre outros
// @note		A propriedade "tpl" é obrigatória para o funcionamento deste widget
// @package		php2go.template.widget
// @extends		Widget
// @uses		Template
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class TemplateContainer extends Widget
{
	var $Template = NULL;	// @var Template Template object	"NULL" Manipula e gera o template
	
	//!-----------------------------------------------------------------
	// @function	TemplateContainer::TemplateContainer
	// @desc		Construtor da classe
	// @param		properties array	Conjunto de propriedades
	// @access		public
	//!-----------------------------------------------------------------
	function TemplateContainer($properties) {
		parent::Widget($properties);
		$this->mandatoryProperties[] = 'tpl';
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateContainer::onPreRender
	// @desc		Instancia e configura o template a ser utilizado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		$this->Template = new Template($this->properties['tpl'], T_BYFILE);
		$this->Template->parse();
		$this->Template->assign($this->properties['localVars']);
		if (!$this->Template->isVariableDefined('_ROOT.body'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_VARIABLE', array('body', $this->properties['tpl'], 'body')), E_USER_ERROR, __FILE__, __LINE__);		
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateContainer::render
	// @desc		Monta e retorna o conteúdo final do template
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function render() {
		$this->Template->assign('_ROOT.body', $this->bodyContent);
		return $this->Template->getContent();
	}
}
?>