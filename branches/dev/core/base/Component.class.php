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
// $Header: /www/cvsroot/php2go/core/base/Component.class.php,v 1.2 2006/11/19 18:41:50 mpont Exp $
// $Date: 2006/11/19 18:41:50 $

//!-----------------------------------------------------------------
// @class		Component
// @desc		A classe Component щ base para todos os elementos renderizсveis
//				que podem ser incluэdos em um documento HTML: templates, formulсrios
//				(Form e classes filhas, campos e botѕes), relatѓrios (Report)
//				e outros elementos grсficos, como menus (php2go.gui.Menu)
// @package		php2go.base
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.2 $
//!-----------------------------------------------------------------
class Component extends PHP2Go
{
	var $attributes = array();	// @var attributes array	"array()" Atributos do componente
	var $preRendered = FALSE;	// @var preRendered bool	"FALSE" Indica se a etapa de prщ-renderizaчуo jс foi executada

	//!-----------------------------------------------------------------
	// @function	Component::Component
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Component() {
		parent::PHP2Go();
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	Component::__destruct
	// @desc		Destrutor da classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	Component::getAttribute
	// @desc		Busca o valor de um atributo do componente
	// @param		name string		Nome do atributo
	// @param		fallback mixed	"FALSE" Valor de retorno para atributos inexistentes
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getAttribute($name, $fallback=FALSE) {
		return (array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $fallback);
	}

	//!-----------------------------------------------------------------
	// @function	Component::setAttribute
	// @desc		Define o valor de um atributo
	// @param		name string		Nome do atributo
	// @param		value mixed		Valor do atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAttribute($name, $value) {
		$this->attributes[$name] = $value;
	}

	//!-----------------------------------------------------------------
	// @function	Component::onPreRender
	// @desc		Etapa de prщ-renderizaчуo. Deve ser implementada
	//				pelas classes filhas, desde que a implementaчуo
	//				da classe superior tambщm seja executada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		$this->preRendered = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Component::getContent
	// @desc		Retorna a saэda gerada pelo componente
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getContent() {
		ob_start();
		$this->display();
		return ob_get_clean();
	}

	//!-----------------------------------------------------------------
	// @function	Component::display
	// @desc		Mщtodo abstrato de impressуo da saэda gerada pelo componente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
	}

	//!-----------------------------------------------------------------
	// @function	Component::__toString
	// @desc		Retorna a saэda do componente como forma de
	//				representс-lo como uma string
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function __toString() {
		ob_start();
		$this->display();
		return ob_get_clean();
	}
}

?>