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
// @desc		Constrói, a partir da estrutura de menu montada, uma
//				árvore com pastas e subpastas representando um menu
//				de opções
// @package		php2go.gui
// @extends		Menu
// @author		Marcos Pont
// @version		$Revision: 1.14 $
//!-----------------------------------------------------------------
class TreeMenu extends Menu
{
	var $offsetX = 0;				// @var offsetX int					"0" Descolamento do menu em relação ao extremo esquerdo da página, padrão é zero
	var $offsetY = 0;				// @var offsetY int					"0" Deslocamento do menu em relação ao topo da página, padrão é zero
	var $icons;						// @var icons array					Vetor de ícones utilizado na montagem da árvore
	var $iconWidth = 16;			// @var iconWidth int				"16" Largura dos ícones da árvore, padrão é 16
	var $iconHeight = 16;			// @var iconHeight int				"16" Altura dos ícones da árvore, padrão é 16
	var $showButtons = TRUE;		// @var showButtons bool			"TRUE" Indica se os botões + e - devem ser exibidos
	var $showFolders = TRUE;		// @var showFolders bool			"TRUE" Indica se os ícones de pasta e documento devem ser exibidos
	var $levelIdent = 16;			// @var levelIdent int				"16" Identação, em pixels, entre um nível e outro na árvore, padrão é 16
	var $backgroundColor = "";		// @var backgroundColor string		"" Cor de fundo da árvore
	var $oneBranch = FALSE;			// @var oneBranch bool				"FALSE" Indica se apenas uma opção da árvore pode estar ativa
	var $addressPrefix;				// @var addressPrefix string		Prefixo a ser utilizado em todos os links da árvore
	var $defaultStyle = "null";		// @var defaultStyle string			"null" Estilo CSS padrão em todos os nodos da árvore
	var $customStyles = array();	// @var customStyles array			"array()" Estilos customizados por nível da árvore
	var $itemPadding = 0;			// @var itemPadding int				"0" Espaçamento interno nos nodos
	var $itemSpacing = 0;			// @var itemSpacing int				"0" Espaçamento entre os nodos
	var $menuCode = '';				// @var menuCode string				"" Código gerado para o menu

	//!-----------------------------------------------------------------
	// @function	TreeMenu::TreeMenu
	// @desc		Construtor da classe TreeMenu, inicializa a classe superior
	// @access		public
	// @param		&Document Document object	Documento onde o menu será inserido
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
	// @desc 		Configura o ponto (X,Y) a partir do qual o menu deverá
	// 				ser gerado
	// @access 		public
	// @param 		left int	Posição X inicial, em relação ao extremo esquerdo
	// @param 		top int	Posição Y inicial, em relação ao topo
	// @return		void
	//!-----------------------------------------------------------------
	function setStartPoint($left, $top) {
		$this->offsetX = TypeUtils::parseIntegerPositive($left);
		$this->offsetY = TypeUtils::parseIntegerPositive($top);
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::hideButtons
	// @desc		Esconde os botões + e - na montagem da árvore do menu
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function hideButtons() {
    	$this->showButtons = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::hideFolders
	// @desc		Esconde as imagens das pastas (aberta, fechada, documento) na montagem da árvore
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
	// @desc		Configura a cor de fundo da árvore
	// @access		public
	// @param		color string	Qualquer cor aceitável em HTML
	// @return		void
	// @note		Se a cor de fundo não for configurada, o padrão é 'transparent'
	//!-----------------------------------------------------------------
	function setBackgroundColor($color) {
    	$this->backgroundColor = $color;
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setDefaultStyle
	// @desc		Configura o estilo padrão a ser aplicado a todos os nodos
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
	// @desc		Permite alterar uma das imagens utilizadas na árvore
	// @access		public
	// @param		which string		Qual imagem será trocada
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
	// @desc		Configura o tamanho dos ícones utilizados na árvore
	// @access		public
	// @param		width int		Largura para os ícones
	// @param		height int	Altura para os ícones
	// @return		void
	//!-----------------------------------------------------------------
	function setImageSize($width, $height) {
		$this->iconWidth = TypeUtils::parseIntegerPositive($width);
		$this->iconHeight = TypeUtils::parseIntegerPositive($height);
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setLevelIdent
	// @desc		Indica a quantidade de pixels que deverá ter a identação
	//				horizontal entre os níveis da árvore
	// @access		public
	// @param		ident int		Valor para a identação entre níveis
	// @return		void
	//!-----------------------------------------------------------------
	function setLevelIdent($ident) {
		$this->levelIdent = TypeUtils::parseIntegerPositive($ident);
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setLevelStyle
	// @desc		Customiza o estilo dos nodos de um determinado nível
	// @access		public
	// @param		which int		Índice do nível (baseado em zero)
	// @param		style string	Estilo CSS para o nível indicado
	// @return		void
	//!-----------------------------------------------------------------
	function setLevelStyle($which, $style) {
		if (TypeUtils::isInteger($which)) {
        	$this->customStyles[$which] = $style;
		}
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setItemPadding
	// @desc		Seta o espaço interno de um nodo, em pixels
	// @access		public
	// @param		padding int	Valor do espaçamento
	// @return		void
	//!-----------------------------------------------------------------
	function setItemPadding($padding) {
    	$this->itemPadding = TypeUtils::parseIntegerPositive($padding);
	}

	//!-----------------------------------------------------------------
	// @function	TreeMenu::setItemSpacing
	// @desc		Seta o espaçamento entre nodos na árvore
	// @access		public
	// @param		spacing int	Valor do espaçamento
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
	// @desc 		Constrói e retorna o código do menu
	// @access 		public
	// @return		string Conteúdo da árvore do menu
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		$this->_buildCode();
		return $this->menuCode;
	}

	//!-----------------------------------------------------------------
	// @function 	TreeMenu::display
	// @desc 		Constrói e imprime o código do menu
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
	// @desc 		Constrói o código JavaScript de construção da árvore a
	// 				partir dos dados já coletados e a partir das configurações
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
	// @desc		Método recursivo que constrói o código dos nodos de
	//				todos os níveis derivados da raiz da árvore
	// @access		private
	// @param		children array	Vetor de filhos de um nodo
	// @return		string	Porção de codigo javascript de geração de um ramo da árvore de menus
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