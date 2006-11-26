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
// $Header: /www/cvsroot/php2go/core/gui/LayerMenu.class.php,v 1.23 2006/10/26 04:58:32 mpont Exp $
// $Date: 2006/10/26 04:58:32 $

//------------------------------------------------------------------
import('php2go.gui.Menu');
//------------------------------------------------------------------

// @const LAYER_MENU_SIDE "1"
// Os itens de menu são dispostos lado a lado, cada qual com seu tamanho
define('LAYER_MENU_SIDE', 1);
// @const LAYER_MENU_EQUAL "2"
// Todos os itens são criados com a largura do maior item (baseada no comprimento da caption)
define('LAYER_MENU_EQUAL', 2);

//!-----------------------------------------------------------------
// @class		LayerMenu
// @desc		A partir da estrutura de dados definida na classe pai Menu,
//				esta classe monta o código de geração dos menus utilizando
//				camadas DHTML para representar os níveis
// @package		php2go.gui
// @extends		Menu
// @author		Marcos Pont
// @version		$Revision: 1.23 $
//!-----------------------------------------------------------------
class LayerMenu extends Menu
{
	var $width; 						// @var width int				Largura do menu
	var $height = 20; 					// @var height int				"20" Altura do menu na orientação horizontal ou da opção de menu na orientação vertical
	var $offsetX = 0; 					// @var offsetX int				"0" Deslocamento do menu em relação ao extremo esquerdo da página
	var $offsetY = 0; 					// @var offsetY int				"0" Deslocamento do menu em relação ao topo
	var $itemSpacing = 0;				// @var itemSpacing int			"0" Espaçamento entre os itens da raiz do menu
	var $charWidth = 7;					// @var charWidth int			"7" Largura de caracteres, para cálculo da largura dos itens de menu
	var $isHorizontal = TRUE; 			// @var isHorizontal bool		"TRUE" Indica se a orientação dos menus é horizontal - TRUE - ou vertical - FALSE
	var $addressPrefix = ''; 			// @var addressPrefix string	"" Prefixo a ser adicionado em todos os links do menu
	var $rootStyles = array(); 			// @var rootStyles array		"array()" Estilos para os elementos da raiz do menu
	var $rootDisposition;				// @var rootDisposition int		Forma como os itens da raiz são dispostos (somente para menu horizontal)
	var $childrenStyles = array(); 		// @var childrenStyles array	"array()" Estilos para os outros níveis do menu
	var $childrenHeight = 18; 			// @var childrenHeight int		"18" Altura dos níveis de menu abaixo da raiz
	var $childrenTimeout = 100; 		// @var childrenTimeout int		"100" Número de milisegundos para fechamento da opção do menu após perder o foco do mouse
	var $minimumChildWidth = 0;			// @var minimumChildWidth int	"0" Largura mínima para um item de menu
	var $menuCode = ''; 				// @var menuCode string			"" Código JavaScript resultante para geração do menu

	//!-----------------------------------------------------------------
	// @function	LayerMenu::LayerMenu
	// @desc		Construtor da classe, inicializa a classe superior
	// @access		public
	// @param		&Document Document object	Documento onde o menu será construído
	//!-----------------------------------------------------------------
	function LayerMenu(&$Document) {
		parent::Menu($Document);
		$this->rootDisposition = LAYER_MENU_EQUAL;
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setSize
	// @desc 		Ajusta o tamanho do menu
	// @access 		public
	// @param 		width int		Largura para o menu
	// @param 		height int		"0" Altura para o menu. Se não informada, utilizará o padrão pré-definido
	// @return		void
	// @note 		Na orientação horizontal, a largura será interpretada como
	// 				o tamanho geral do menu (todas as opções lado a lado). Já
	// 				na orientação vertical, será interpretada como a largura
	// 				do nível zero do menu (raiz)
	//!-----------------------------------------------------------------
	function setSize($width, $height = 0) {
		$this->width = abs($width);
		if ($height) $this->height = abs($height);
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setStartPoint
	// @desc 		Configura o ponto (X,Y) a partir do qual o menu deverá
	// 				ser gerado
	// @access 		public
	// @param 		left int		Posição X inicial, em relação ao extremo esquerdo
	// @param 		top int	Posição Y inicial, em relação ao topo
	// @return		void
	//!-----------------------------------------------------------------
	function setStartPoint($left, $top) {
		$this->offsetX = TypeUtils::parseIntegerPositive($left);
		$this->offsetY = TypeUtils::parseIntegerPositive($top);
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setVertical
	// @desc 		Configura o menu para ser gerado verticalmente
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setVertical() {
		$this->isHorizontal = false;
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setAddressPrefix
	// @desc 		Configura um prefixo a ser inserido em todos os links
	// 				presentes no menu
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAddressPrefix($prefix) {
		$this->addressPrefix = $prefix;
	}

	//!-----------------------------------------------------------------
	// @function	LayerMenu::setRootDisposition
	// @desc		Define como deve ser dispostos os itens do primeiro nível do menu
	// @access		public
	// @param		disposition int		Tipo de disposição de elementos
	// @note		Este método somente tem efeito em menus horizontais
	// @note		Valores possíveis:<br>
	//				LAYER_MENU_SIDE - os itens são dispostos lado a lado, cada qual com seu tamanho<br>
	//				LAYER_MENU_EQUAL - todos os itens são criados com a largura do maior item
	//!-----------------------------------------------------------------
	function setRootDisposition($disposition) {
		if ($disposition == LAYER_MENU_SIDE || $disposition == LAYER_MENU_EQUAL) {
			$this->rootDisposition = $disposition;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setRootStyles
	// @desc 		Permite fornecer uma lista de configurações para as
	// 				opções do nível raiz do menu
	// @access 		public
	// @param 		reg string		Estilo tipo CLASS padrão para a opção de menu
	// @param 		over string		"" Estilo tipo CLASS para a opção logo após o evento onMouseOver
	// @param 		border string	"" Estilo para a borda, se existir
	// @param 		borderX int	"1" Tamanho das bordas inferior/superior da opção de menu
	// @param 		borderY int	"1" Tamanho das bordas laterais da opção de menu
	// @return		void
	// @see 		Menu::setChildrenStyles
	//!-----------------------------------------------------------------
	function setRootStyles($reg, $over = '', $border = '', $borderX = '', $borderY = '') {
		$this->rootStyles['reg'] = $reg;
		$this->rootStyles['over'] = ($over == '') ? $reg : $over;
		$this->rootStyles['border'] = $border;
		$this->rootStyles['borderX'] = !empty($borderX) ? $borderX : '0.00001';
		$this->rootStyles['borderY'] = !empty($borderY) ? $borderY : '0.00001';
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setChildrenStyles
	// @desc 		Permite fornecer uma lista de configurações para as
	// 				opções dos outros níveis do menu, independentemente das
	// 				configurações da raiz
	// @access 		public
	// @param 		reg string		Estilo tipo CLASS padrão para as opções
	// @param 		over string		"" Estilo tipo CLASS para as opções logo após o evento onMouseOver
	// @param 		border string	"" Estilo para as bordas, se existir
	// @param 		borderX int	"1" Tamanho das bordas inferior/superior das opções de menu
	// @param 		borderY int	"1" Tamanho das bordas laterais das opções de menu
	// @return		void
	// @see 		Menu::setRootStyles
	//!-----------------------------------------------------------------
	function setChildrenStyles($reg, $over = '', $border = '', $borderX = '', $borderY = '') {
		$this->childrenStyles['reg'] = $reg;
		$this->childrenStyles['over'] = ($over == '') ? $reg : $over;
		$this->childrenStyles['border'] = $border;
		$this->childrenStyles['borderX'] = !empty($borderX) ? $borderX : '0.00001';
		$this->childrenStyles['borderY'] = !empty($borderY) ? $borderY : '0.00001';
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setChildrenHeight
	// @desc 		Configura a altura das opções de menu abaixo da raiz
	// @access 		public
	// @param 		height int		Altura desejada
	// @return		void
	//!-----------------------------------------------------------------
	function setChildrenHeight($height) {
		$this->childrenHeight = TypeUtils::parseIntegerPositive($height);
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setChildrenTimeout
	// @desc 		Configura o tempo em milisegundos que uma opção de menu
	// 				se mantém aberta após a perda do foco do mouse
	// @access 		public
	// @param 		timeout int		Timeout desejado
	// @return		void
	//!-----------------------------------------------------------------
	function setChildrenTimeout($timeout) {
		$this->childrenTimeout = TypeUtils::parseIntegerPositive($timeout);
	}

	//!-----------------------------------------------------------------
	// @function	LayerMenu::setMinimumChildWidth
	// @desc		Define a largura mínima para qualquer dos itens
	//				do menu, a partir do segundo nível
	// @access		public
	// @param		min int			Largura mínima para itens de menu, em pixels
	// @return		void
	//!-----------------------------------------------------------------
	function setMininumChildWidth($min) {
		$this->minimumChildWidth = $min;
	}

	//!-----------------------------------------------------------------
	// @function	LayerMenu::setItemSpacing
	// @desc		Define o espaçamento entre os itens da raiz do menu, em pixels
	// @access		public
	// @param		itemSpacing int	Espaçamento entre os itens do menu
	// @return		void
	//!-----------------------------------------------------------------
	function setItemSpacing($itemSpacing) {
		$this->itemSpacing = TypeUtils::parseIntegerPositive($itemSpacing);
	}

	//!-----------------------------------------------------------------
	// @function	LayerMenu::setCharWidth
	// @desc		Define a largura dos caracteres utilizados nas captions
	//				dos itens de menu. O valor padrão definido na classe para
	//				esta propriedade é 7 (em pixels)
	// @param		width int	Largura para os caracteres dos itens de menu
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCharWidth($width) {
		$this->charWidth = TypeUtils::parseIntegerPositive($width);
	}

	//!-----------------------------------------------------------------
	// @function	LayerMenu::onPreRender
	// @desc		Prepara o menu para renderização: scripts e código
	//				no HEAD da página necessários
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			parent::buildMenu();
			$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/coolmenus/coolmenus4.js");
			$this->_Document->appendHeaderContent(
				"<style type=\"text/css\">\n" .
				"     .clCMEvent { position:absolute; z-index:300; width:100%; height:100%; clip:rect(0,100%,100%,0); left:0; top:0; visibility:hidden }\n" .
				"     .clCMAbs { position:absolute; width:10; height:10; left:0; top:0; visibility:hidden }\n" .
				"</style>\n"
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::getContent
	// @desc 		Constrói e retorna o código do menu
	// @access 		public
	// @return		string Conteúdo responsável pela geração do menu de camadas
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		$this->_buildCode();
		return $this->menuCode;
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::display
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
	// @function 	LayerMenu::_buildCode
	// @desc 		Constrói o código JavaScript de construção do menu a
	// 				partir dos dados já coletados e a partir das configurações
	// 				existentes no objeto
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildCode() {
		$this->menuCode .=
			"<script type=\"text/javascript\" language=\"Javascript\">\n" .
			"     {$this->name} = new makeCM(\"{$this->name}\");\n" .
			"     {$this->name}.resizeCheck = 1;\n" .
			"     {$this->name}.openOnClick = 1;\n" .
			"     {$this->name}.rows = " . ((int)$this->isHorizontal) . ";\n" .
			"     {$this->name}.onlineRoot = \"{$this->addressPrefix}\";\n" .
			"     {$this->name}.menuPlacement = 0;\n" .
			"     {$this->name}.pxBetween = {$this->itemSpacing};\n" .
			"     {$this->name}.fromLeft = {$this->offsetX};\n" .
			"     {$this->name}.fromTop = {$this->offsetY};\n" .
			"     {$this->name}.wait = {$this->childrenTimeout};\n" .
			"     {$this->name}.zIndex = 400;\n";
		// criação dos níveis para o menu horizontal
		if ($this->isHorizontal) {
			// altura da raiz
			$rootHeight = $this->height+1;
			// os itens da raiz devem ser dispostos em larguras iguais
			if ($this->rootDisposition == LAYER_MENU_EQUAL) {
				$textSize = 1;
				for ($i = 0; $i < sizeof($this->tree); $i++)
					$textSize = (strlen($this->tree[$i]['CAPTION']) > $textSize) ? strlen($this->tree[$i]['CAPTION']) : $textSize;
				$textSize = ($textSize * $this->charWidth) + 15;
				for ($i=0; $i<sizeof($this->tree); $i++)
					$rootWidth[$i] = $textSize;
			}
			// os itens da raiz são dispostos lado a lado
			elseif ($this->rootDisposition == LAYER_MENU_SIDE) {
				for ($i=0; $i<sizeof($this->tree); $i++)
					$rootWidth[$i] = (strlen($this->tree[$i]['CAPTION']) * $this->charWidth) + 15;
			}
			$this->menuCode .=
				"     var menuSize = " . (isset($this->width) ? $this->width : "screen.width") . "-" . $this->offsetX . "-22;\n" .
				"     {$this->name}.useBar = 1;\n" .
				"     {$this->name}.barWidth = menuSize;\n" .
				"     {$this->name}.barHeight = {$this->height};\n" .
				"     {$this->name}.barX = {$this->offsetX};\n" .
				"     {$this->name}.barY = {$this->offsetY};\n" .
				"     {$this->name}.barClass = \"" . $this->_getStyle(0, 'reg') . "\";\n" .
				"     {$this->name}.barBorderX = 0;\n" .
				"     {$this->name}.barBorderY = 0;\n";
			for ($i = 0; $i <= $this->lastLevel; $i++) {
				$this->menuCode .= "     {$this->name}.level[$i] = new cm_makeLevel(80, " . (($i==0) ? $this->height : $this->childrenHeight) . ", \"" . $this->_getStyle($i, 'reg') . "\", \"" . $this->_getStyle($i, 'over') . "\", " . $this->_getStyle($i, 'borderX') . ", " . $this->_getStyle($i, 'borderY') . ", \"" . $this->_getStyle($i, 'border') . "\" , 0, \"bottom\", -1, -1, \"\", 10, 10, 0);\n";
			}
		} else {
			// altura da raiz
			$rootHeight = $this->childrenHeight;
			// cálculo da largura máxima dos itens da raiz
			if (isset($this->width)) {
				$textSize = $this->width;
			} else {
				$textSize = 1;
				for ($i = 0; $i < sizeof($this->tree); $i++)
					$textSize = (strlen($this->tree[$i]['CAPTION']) > $textSize) ? strlen($this->tree[$i]['CAPTION']) : $textSize;
				$textSize = ($textSize * $this->charWidth) + 15;
			}
			// define posições para os itens da raiz
			$placement = $this->offsetY;
			$rootWidth[0] = $textSize;
			for ($i = 1; $i < $this->rootSize; $i++) {
				$rootWidth[$i] = $textSize;
				$placement .= "," . ($this->offsetY + ($i*$this->childrenHeight));
			}
			$this->menuCode .= "     {$this->name}.menuPlacement = new Array($placement);\n";
			// constrói os níveis do menu
			for ($i = 0; $i <= $this->lastLevel; $i++) {
				$this->menuCode .= "     {$this->name}.level[$i] = new cm_makeLevel(" . (isset($this->width) ? $this->width : 100) . ", " . $this->childrenHeight . ", \"" . $this->_getStyle($i, 'reg') . "\", \"" . $this->_getStyle($i, 'over') . "\", " . $this->_getStyle($i, 'borderX') . ", " . $this->_getStyle($i, 'borderY') . ", \"" . $this->_getStyle($i, 'border') . "\" , 0, \"right\", 0, -1, \"\", 10, 10);\n";
			}
		}
		// criação dos itens do menu
		for ($i = 0; $i < sizeof($this->tree); $i++) {
			$thisId = "m" . $i;
			$this->menuCode .= "     {$this->name}.makeMenu('{$thisId}', '', '{$this->tree[$i]['CAPTION']}', '{$this->tree[$i]['LINK']}', '{$this->tree[$i]['TARGET']}', {$rootWidth[$i]}, {$rootHeight});\n";
			if (!empty($this->tree[$i]['CHILDREN']))
				$this->_buildChildrenCode($this->tree[$i]['CHILDREN'], $thisId, 0);
		}
		$this->menuCode .= "     {$this->name}.construct();\n</script>";
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::_buildChildrenCode
	// @desc 		Função recursiva que monta as criações de instâncias de
	// 				menu DHTML para a árvore de opções de menu da classe
	// @access 		private
	// @param 		children array	Vetor atual/inicial de elementos da árvore
	// @param 		parentId mixed	Índice do nodo superior ao vetor $children fornecido
	// @param 		parentLevel int	Nível onde se encontra o nodo superior ao atual
	// @return		void
	//!-----------------------------------------------------------------
	function _buildChildrenCode($children, $parentId, $parentLevel) {
		$textSize = 1;
		for ($i = 0; $i < sizeof($children); $i++) {
			$textSize = (strlen($children[$i]['CAPTION']) > $textSize) ? strlen($children[$i]['CAPTION']) : $textSize;
		}
		$itemWidth = max(($textSize * $this->charWidth) + 15, $this->minimumChildWidth);
		for ($i = 0; $i < sizeof($children); $i++) {
			$thisId = $parentId . "_c" . $i;
			$this->menuCode .= "     {$this->name}.makeMenu('{$thisId}', '{$parentId}', '{$children[$i]['CAPTION']}', '{$children[$i]['LINK']}', '{$children[$i]['TARGET']}', {$itemWidth}, {$this->childrenHeight}, '', '', '', '', 'right');\n";
			if (!empty($children[$i]['CHILDREN'])) {
				$this->_buildChildrenCode($children[$i]['CHILDREN'], $thisId, ($parentLevel + 1));
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::_getStyle
	// @desc 		Retorna um estilo nas configurações passadas para a montagem
	// 				da raiz e dos demais níveis do menu
	// @access 		private
	// @param 		level int			Nível onde está sendo feita a consulta, para
	// 									que se possa diferenciar entre raiz e demais níveis
	// @param 		element string    Elemento buscado
	// @return 		string Valor do elemento buscado, '' se não encontrado e o tipo do valor
	// 				é texto/string, 1 se não encontrado e o tipo do valor é numérico
	//!-----------------------------------------------------------------
	function _getStyle($level, $element) {
		$repository = ($level > 0) ? $this->childrenStyles : $this->rootStyles;
		$value = (isset($repository[$element])) ? $repository[$element] : ($element == 'borderX' || $element == 'borderY' ? 1 : '');
		return $value;
	}
}
?>