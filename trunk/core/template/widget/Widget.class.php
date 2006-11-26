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
// $Header: /www/cvsroot/php2go/core/template/widget/Widget.class.php,v 1.6 2006/10/26 04:32:49 mpont Exp $
// $Date: 2006/10/26 04:32:49 $

//------------------------------------------------------------------
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		Widget
// @desc		Os widgets s�o componentes que geram trechos de interface a partir
//				de um conjunto de par�metros que adicionam a ele comportamentos ou
//				caracter�sticas. A class Widget � a base para todos estes componentes,
//				com alguns controles j� implementados: hash de propriedades, controle
//				de propriedades obrigat�rias e conjunto de scripts necess�rios
// @package		php2go.template.widget
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class Widget extends PHP2Go
{
	var $content = '';						// @var content string				"" Conte�do gerado para o widget
	var $bodyContent;						// @var bodyContent string			Conte�do (corpo) que � atribu�do a widgets que funcionam como "containers"
	var $properties = array();				// @var properties array			"array()" Conjunto de propriedades do widget
	var $mandatoryProperties = array();		// @var mandatoryProperties array	"array()" Conjunto de propriedades obrigat�rias para o funcionamento correto do widget
	var $hasBody = TRUE;					// @var hasBody bool				"TRUE" Aceita conte�do interno. Caracter�stica de widgets que funcionam como "containers"

	//!-----------------------------------------------------------------
	// @function	Widget::Widget
	// @desc		Construtor da classe
	// @param		properties array	"array()" Conjunto de propriedades
	// @access		public
	//!-----------------------------------------------------------------
	function Widget($properties=array()) {
		parent::PHP2Go();
		$this->loadProperties((array)$properties);
	}

	//!-----------------------------------------------------------------
	// @function	Widget::setPropertyValue
	// @desc		Cria ou altera uma propriedade
	// @param		property string		Nome da propriedade
	// @param		value mixed			Valor da propriedade
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setPropertyValue($property, $value) {
		$this->properties[$property] = $value;
	}

	//!-----------------------------------------------------------------
	// @function	Widget::setBody
	// @desc		Define o "conte�do" do widget. Este m�todo somente tem
	//				efeito em widgets do tipo "container"
	// @param		content string		Conte�do do widget
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setBody($content) {
		if (!$this->hasBody)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_WIDGET_INCLUDE', parent::getClassName()), E_USER_ERROR, __FILE__, __LINE__);
		$this->bodyContent = $content;
	}

	//!-----------------------------------------------------------------
	// @function	Widget::loadProperties
	// @desc		M�todo respons�vel por carregar para o objeto as propriedades do widget
	// @note		Pode ser sobrescrito nas classes filhas para adicionar transforma��es
	//				ou defini��o de valores default para propriedades n�o fornecidas
	// @param		properties array	Conjunto de propriedades
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadProperties($properties) {
		$this->properties = $properties;
	}

	//!-----------------------------------------------------------------
	// @function	Widget::validate
	// @desc		Valida a presen�a das propriedades definidas como
	//				obrigat�rias para a constru��o do widget
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function validate() {
		foreach ($this->mandatoryProperties as $property) {
			if (!isset($this->properties[$property]))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_WIDGET_MANDATORY_PROPERTY', array($property, parent::getClassName())), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Widget::onPreRender
	// @desc		M�todo que pode ser sobrescrito em classes filhas a fim
	//				de executar opera��es antes do momento da constru��o do
	//				c�digo final do widget
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
	}

	//!-----------------------------------------------------------------
	// @function	Widget::render
	// @desc		Renderiza o conte�do final do widget. Cada classe filha
	//				pode possuir uma implementa��o pr�pria deste m�todo
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function render() {
		return preg_replace("/{\$?body}/", $this->bodyContent, $this->content);
	}

	//!-----------------------------------------------------------------
	// @function	Widget::getContent
	// @desc		O m�todo getContent � utilizado para disparar a gera��o do
	//				conte�do do widget e devolver este conte�do ao template
	// @return		string Conte�do gerado para o widget
	// @access		public
	//!-----------------------------------------------------------------
	function getContent() {
		$this->validate();
		$this->onPreRender();
		return $this->render();
	}
}
?>