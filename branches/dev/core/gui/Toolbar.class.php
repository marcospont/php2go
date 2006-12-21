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
// $Header: /www/cvsroot/php2go/core/template/widget/Toolbar.class.php,v 1.4 2006/10/26 04:32:49 mpont Exp $
// $Date: 2006/10/26 04:32:49 $

// @const TOOLBAR_MODE_ICONS "1"
// Modo de construчуo dos itens da toolbar na forma de эcones
define('TOOLBAR_MODE_ICONS', 1);
// @const TOOLBAR_MODE_BUTTONS "2"
// Modo de construчуo dos itens da toolbar na forma de botѕes
define('TOOLBAR_MODE_BUTTONS', 2);
// @const TOOLBAR_MODE_LINKS "3"
// Modo de construчуo dos itens da toolbar na forma de links (padrуo da classe)
define('TOOLBAR_MODE_LINKS', 3);

//!-----------------------------------------------------------------
// @class		Toolbar
// @desc		A toolbar щ uma tabela contendo um conjunto de itens (эcones,
//				botѕes ou links). Pode ser gerada horizontalmente ou verticalmente,
//				permite configuraчуo de estilos (atravщs de nomes de pseudo-classes),
//				tamanhos e alinhamento
// @package		php2go.template.widget
// @extends		Widget
// @uses		Template
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class Toolbar extends Widget
{
	var $Template = NULL;	// @var Template Template object	"NULL" Template base para construчуo da toolbar
	
	//!-----------------------------------------------------------------
	// @function	Toolbar::Toolbar
	// @desc		Construtor da classe
	// @param		properties array	Conjunto de propriedades
	// @access		public
	//!-----------------------------------------------------------------
	function Toolbar($properties) {
		parent::Widget($properties);
		$this->mandatoryProperties[] = 'items';
		$this->hasBody = FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Toolbar::loadProperties
	// @desc		Define as propriedades do widget reunindo os valores default
	//				e as propriedades fornecidas, e aplicando transformaчѕes
	//				necessсrias
	// @param		properties array	Conjunto de propriedades
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadProperties($properties) {
		$defaults = array(
			'id' => PHP2Go::generateUniqueId(parent::getClassName()),
			'mode' => TOOLBAR_MODE_LINKS,
			'align' => 'center',
			'horizontal' => TRUE,
			'itemHeight' => '20px',
			'descriptionAlign' => 'center',
			'activeIndex' => NULL
		);
		$properties = array_merge($defaults, $properties);
		if (TypeUtils::isInteger($properties['width']))
			$properties['width'] .= 'px';		
		if (TypeUtils::isInteger($properties['itemHeight']))
			$properties['itemHeight'] .= 'px';
		parent::loadProperties($properties);
	}
	
	//!-----------------------------------------------------------------
	// @function	Toolbar::onPreRender
	// @desc		Mщtodo de prщ-processamento da toolbar: cria e configura
	//				o template base, aplicando as propriedades no widget
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		$this->Template = new Template(PHP2GO_TEMPLATE_PATH . 'toolbar.tpl');
		$this->Template->parse();
		// propriedades gerais
		$this->Template->assign('id', $this->properties['id']);
		$this->Template->assign('mode', $this->properties['mode']);
		$this->Template->assign('align', $this->properties['align']);
		$this->Template->assign('horizontal', $this->properties['horizontal']);
		if (isset($this->properties['class']))
			$this->Template->assign('class', " class=\"{$this->properties['class']}\"");
		if (isset($this->properties['width']))
			$this->Template->assign('width', $this->properties['width']);
		// propriedades dos itens
		$this->Template->assign('items', $this->properties['items']);
		$this->Template->assign('itemHeight', $this->properties['itemHeight']);
		if (isset($this->properties['itemClass']))
			$this->Template->assign('itemClass', " class=\"{$this->properties['itemClass']}\"");
		// propriedades da description
		$this->Template->assign('descriptionAlign', $this->properties['descriptionAlign']);
		if (isset($this->properties['descriptionClass']))
			$this->Template->assign('descriptionClass', " class=\"{$this->properties['descriptionClass']}\"");
		// propriedades do эndice ativo
		$this->Template->assign('activeIndex', $this->properties['activeIndex']);
		if (isset($this->properties['activeClass']))
			$this->Template->assign('activeClass', " class=\"{$this->properties['activeClass']}\"");
	}
	
	//!-----------------------------------------------------------------
	// @function	Toolbar::render
	// @desc		Renderiza o conteњdo do template associado р toolbar
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function render() {		
		return $this->Template->getContent();
	}
}
?>