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
// $Header: /www/cvsroot/php2go/core/gui/TreeMenu.class.php,v 1.14 2006/10/26 04:58:32 mpont Exp $
// $Date: 2006/10/26 04:58:32 $

//------------------------------------------------------------------
import('php2go.gui.Menu');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		TreeMenu
// @desc		Constr�i, a partir da estrutura de menu montada, uma
//				�rvore com pastas e subpastas representando um menu
//				de op��es
// @package		php2go.gui
// @extends		Menu
// @author		Marcos Pont
// @version		$Revision: 1.14 $
//!-----------------------------------------------------------------
class TreeMenu extends Menu
{
	var $offsetX = 0;				// @var offsetX int					"0" Descolamento do menu em rela��o ao extremo esquerdo da p�gina, padr�o � zero
	var $offsetY = 0;				// @var offsetY int					"0" Deslocamento do menu em rela��o ao topo da p�gina, padr�o � zero
	var $icons;						// @var icons array					Vetor de �cones utilizado na montagem da �rvore
	var $iconWidth = 16;			// @var iconWidth int				"16" Largura dos �cones da �rvore, padr�o � 16
	var $iconHeight = 16;			// @var iconHeight int				"16" Altura dos �cones da �rvore, padr�o � 16
	var $showButtons = TRUE;		// @var showButtons bool			"TRUE" Indica se os bot�es + e - devem ser exibidos
	var $showFolders = TRUE;		// @var showFolders bool			"TRUE" Indica se os �cones de pasta e documento devem ser exibidos
	var $levelIdent = 16;			// @var levelIdent int				"16" Identa��o, em pixels, entre um n�vel e outro na �rvore, padr�o � 16
	var $backgroundColor = "";		// @var backgroundColor string		"" Cor de fundo da �rvore
	var $oneBranch = FALSE;			// @var oneBranch bool				"FALSE" Indica se apenas uma op��o da �rvore pode estar ativa
	var $addressPrefix;				// @var addressPrefix string		Prefixo a ser utilizado em todos os links da �rvore
	var $defaultStyle = "null";		// @var defaultStyle string			"null" Estilo CSS padr�o em todos os nodos da �rvore
	var $customStyles = array();	// @var customStyles array			"array()" Estilos customizados por n�vel da �rvore
	var $itemPadding = 0;			// @var itemPadding int				"0" Espa�amento interno nos nodos
	var $itemSpacing = 0;			// @var itemSpacing int				"0" Espa�amento entre os nodos
	var $menuCode = '';				// @var menuCode string				"" C�digo gerado para o menu

	//!-----------------------------------------------------------------
	// @function	TreeMenu::TreeMenu
	// @desc		Construtor da classe TreeMenu, inicializa a classe superior
	// @access		public
	// @param		&Document Document object	Documento onde o menu ser� inserido
	//!-----------------------------------------------------------------
	function TreeMenu(&$Document) {
    	parent::Menu($Document);
		$this->icons = array(
							'collapse' => PHP2GO_ICON_PATH . 'tree_collapse.gif',
							'expand' => PHP2GO_ICON_PATH . 'tree_expand.gif',
							'blank' => PHP2GO_ICON_PATH . 'tree_blank.gif',
							'document' => PHP2GO_ICON_PATH . 'tree_document.gif',
							'folder_opened' => PHP2GO_ICON_PATH . 'tree_folder_opened.gif',
							'folder_closed' => PHP2GO_ICON_PATH . 'tree_folder_closed.gif');
	}

	//!-----------------------------------------------------------------
	// @function 	TreeMenu::setStartPoint
	// @desc 		Configura o ponto (X,Y) a partir do qual o menu dever�
	// 				ser gerado
	// @access 		public
	// @param 		left int	Posi��o X inicial, em rela��o ao extremo esquerdo
	// @param 		top int	Posi��o Y inicial, em rela��o ao topo
	// @return		void
	//!-----------------------------------------------------------------
	function setStartPoint($left, $top) {
		$this->offsetX = TypeUtils::parseIntegerPositive($left);
		$this->offsetY = TypeUtils::parseIntegerPositive($top);
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::hideButtons
	// @desc		Esconde os bot�es + e - na montagem da �rvore do menu
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function hideButtons() {
    	$this->showButtons = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::hideFolders
	// @desc		Esconde as imagens das pastas (aberta, fechada, documento) na montagem da �rvore
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function hideFolders() {
    	$this->showFolders = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::oneBranchAtOnce
	// @desc		Indica que apenas um submenu deve estar aberto por vez
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function oneBranchAtOnce() {
		$this->oneBranch = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setBackgroundColor
	// @desc		Configura a cor de fundo da �rvore
	// @access		public
	// @param		color string	Qualquer cor aceit�vel em HTML
	// @return		void
	// @note		Se a cor de fundo n�o for configurada, o padr�o � 'transparent'
	//!-----------------------------------------------------------------
	function setBackgroundColor($color) {
    	$this->backgroundColor = $color;
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setDefaultStyle
	// @desc		Configura o estilo padr�o a ser aplicado a todos os nodos
	// @access		public
	// @param		style string	Nome do estilo CSS para os nodos
	// @return		void
	//!-----------------------------------------------------------------
	function setDefaultStyle($style) {
		$this->defaultStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function 	TreeMenu::setAddressPrefix
	// @desc 		Configura um prefixo a ser inserido em todos os links
	// 				presentes no menu
	// @access		public
	// @param		prefix string	Prefixo para os links
	// @return		void
	//!-----------------------------------------------------------------
	function setAddressPrefix($prefix) {
		$this->addressPrefix = $prefix;
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setImage
	// @desc		Permite alterar uma das imagens utilizadas na �rvore
	// @access		public
	// @param		which string		Qual imagem ser� trocada
	// @param		imageName string	Caminho completo da nova imagem
	// @return		void
	//!-----------------------------------------------------------------
	function setImage($which, $imageName) {
    	if (isset($this->icons[$which])) {
        	$this->icons[$which] = $imageName;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_INVALID_IMAGE', array($which, 'collapse, expand, blank, document, folder_opened, folder_closed')), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setImageSize
	// @desc		Configura o tamanho dos �cones utilizados na �rvore
	// @access		public
	// @param		width int		Largura para os �cones
	// @param		height int	Altura para os �cones
	// @return		void
	//!-----------------------------------------------------------------
	function setImageSize($width, $height) {
		$this->iconWidth = TypeUtils::parseIntegerPositive($width);
		$this->iconHeight = TypeUtils::parseIntegerPositive($height);
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setLevelIdent
	// @desc		Indica a quantidade de pixels que dever� ter a identa��o
	//				horizontal entre os n�veis da �rvore
	// @access		public
	// @param		ident int		Valor para a identa��o entre n�veis
	// @return		void
	//!-----------------------------------------------------------------
	function setLevelIdent($ident) {
		$this->levelIdent = TypeUtils::parseIntegerPositive($ident);
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setLevelStyle
	// @desc		Customiza o estilo dos nodos de um determinado n�vel
	// @access		public
	// @param		which int		�ndice do n�vel (baseado em zero)
	// @param		style string	Estilo CSS para o n�vel indicado
	// @return		void
	//!-----------------------------------------------------------------
	function setLevelStyle($which, $style) {
		if (TypeUtils::isInteger($which)) {
        	$this->customStyles[$which] = $style;
		}
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setItemPadding
	// @desc		Seta o espa�o interno de um nodo, em pixels
	// @access		public
	// @param		padding int	Valor do espa�amento
	// @return		void
	//!-----------------------------------------------------------------
	function setItemPadding($padding) {
    	$this->itemPadding = TypeUtils::parseIntegerPositive($padding);
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setItemSpacing
	// @desc		Seta o espa�amento entre nodos na �rvore
	// @access		public
	// @param		spacing int	Valor do espa�amento
	// @return		void
	//!-----------------------------------------------------------------
	function setItemSpacing($spacing) {
    	$this->itemSpacing = TypeUtils::parseIntegerPositive($spacing);
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::onPreRender
	// @desc		Adiciona no documento HTML a biblioteca COOLjsTree
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			parent::buildMenu();
			$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/cooljstree/cooltree.js");
		}
	}

	//!-----------------------------------------------------------------
	// @function 	TreeMenu::getContent
	// @desc 		Constr�i e retorna o c�digo do menu
	// @access 		public
	// @return		string Conte�do da �rvore do menu
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		$this->_buildCode();
		return $this->menuCode;
	}

	//!-----------------------------------------------------------------
	// @function 	TreeMenu::display
	// @desc 		Constr�i e imprime o c�digo do menu
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		$this->onPreRender();
		$this->_buildCode();
		print $this->menuCode;
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::_buildCode
	// @desc 		Constr�i o c�digo JavaScript de constru��o da �rvore a
	// 				partir dos dados j� coletados e a partir das configura��es
	// 				existentes no objeto
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildCode() {
    	$this->menuCode = "<script type=\"text/javascript\" language=\"Javascript\">\n";
		$this->menuCode .= 'var ' . $this->name . '_format = [';
		$this->menuCode .= $this->offsetX . ', ' . $this->offsetY . ', ';
		$this->menuCode .= $this->showButtons ? 'true, ' : 'false, ';
		$this->menuCode .= "[\"" . $this->icons['collapse'] . "\", \"" . $this->icons['expand'] . "\", \"" . $this->icons['blank'] . "\"], ";
		$this->menuCode .= '[' . $this->iconWidth . ', ' . $this->iconHeight . ', 0], ';
		$this->menuCode .= $this->showFolders ? 'true, ' : 'false, ';
		$this->menuCode .= "[\"" . $this->icons['folder_closed'] . "\", \"" . $this->icons['folder_opened'] . "\", \"" . $this->icons['document'] . "\"], ";
		$this->menuCode .= '[' . $this->iconWidth . ', ' . $this->iconHeight . '], ';
		$this->menuCode .= '[';
		for ($i=0; $i<=$this->lastLevel; $i++) {
			$this->menuCode .= $i * $this->levelIdent;
			if ($i < $this->lastLevel) $this->menuCode .= ", ";
		}
		$this->menuCode .= '], ';
		$this->menuCode .= "\"" . $this->backgroundColor . "\", ";
		$this->menuCode .= "\"" . $this->defaultStyle . "\", ";
		$this->menuCode .= "[";
		for ($i=0; $i<=$this->lastLevel; $i++) {
			if (isset($this->customStyles[$i])) {
				$this->menuCode .= "\"" . $this->customStyles[$i] . "\"";
			}
			if ($i < $this->lastLevel) $this->menuCode .= ",";
		}
		$this->menuCode .= "], ";
		$this->menuCode .= $this->oneBranch ? 'true, ' : 'false, ';
		$this->menuCode .= '[' . $this->itemPadding . ', ' . $this->itemSpacing . ']];';
		$this->menuCode .= "var " . $this->name . "_nodes = [";
		for ($i=0; $i<$this->rootSize; $i++) {
			$this->menuCode .= "['" . $this->tree[$i]['CAPTION'] . "',";
			$this->menuCode .= " '" . (isset($this->addressPrefix) ? $this->addressPrefix : '') . $this->tree[$i]['LINK'] . "', null";
			$this->menuCode .= $this->_buildChildrenCode($this->tree[$i]['CHILDREN']) . "]";
			if ($i < ($this->rootSize - 1)) $this->menuCode .= ", ";
		}
		$this->menuCode .= "];\n";
		$this->menuCode .= "new COOLjsTree (\"" . $this->name . "\", " . $this->name . "_nodes, " . $this->name . "_format);";
		$this->menuCode .= "\n</script>\n";
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::_buildChidrenCode
	// @desc		M�todo recursivo que constr�i o c�digo dos nodos de
	//				todos os n�veis derivados da raiz da �rvore
	// @access		private
	// @param		children array	Vetor de filhos de um nodo
	// @return		string	Por��o de codigo javascript de gera��o de um ramo da �rvore de menus
	//!-----------------------------------------------------------------
	function _buildChildrenCode($children) {
		$childrenSize = TypeUtils::isArray($children) ? sizeof($children) : 0;
    	if (!$childrenSize) {
        	return '';
 		} else {
			$code = ', ';
			for ($i=0; $i<$childrenSize; $i++) {
				$code .= "['" . $children[$i]['CAPTION'] . "',";
				$code .= " '" . $children[$i]['LINK'] . "', null";
				$code .= $this->_buildChildrenCode($children[$i]['CHILDREN']) . "]";
				if ($i < ($childrenSize - 1)) $code .= ", ";
			}
			return $code;
		}
	}
}
?>