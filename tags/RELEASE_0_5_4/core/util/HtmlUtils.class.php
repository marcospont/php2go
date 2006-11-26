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
// $Header: /www/cvsroot/php2go/core/util/HtmlUtils.class.php,v 1.37 2006/10/19 02:00:44 mpont Exp $
// $Date: 2006/10/19 02:00:44 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		HtmlUtils
// @desc		Classe que cont�m um conjunto de fun��es utilit�rias
//				para a constru��o de tags ou por��es de c�digo HTML,
//				bem como para a execu��o de algumas a��es utilizando
//				JavaScript
// @package		php2go.util
// @extends		PHP2Go
// @uses		HttpRequest
// @author		Marcos Pont
// @version		$Revision: 1.37 $
// @static
//!-----------------------------------------------------------------
class HtmlUtils extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	HtmlUtils::anchor
	// @desc		Monta o c�digo de uma �ncora HTML 'A', a partir
	// 				dos par�metros b�sicos
	// @param		url string			URL ou fun��o JavaScript para o par�metro HREF do �ncora
	// @param		text string			Texto interno ao �ncora
	// @param		stBarText string	"" Texto para a barra de status no evento onMouseOver
	// @param		css string			"" Estilo CSS para o texto interno ao �ncora
	// @param		extraScript array	"array()" Vetor associativo evento=>a��o para tratamento de eventos JavaScript
	// @param		target string		"" Alvo para a �ncora
	// @param		name string			"" Nome para a �ncora
	// @param		id string			"" Identifica��o de objeto para a �ncora
	// @param		rel string			"" Rela��o do documento atual com o documento indicado no par�metro 'url'
	// @param		accessKey string	"" Tecla de atalho
	// @return		string C�digo formatado para a �ncora
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function anchor($url, $text, $statusBarText='', $cssClass='', $jsEvents=array(), $target='', $name='', $id='', $rel='', $accessKey='') {
		if (empty($url))
			$url = "javascript:void(0);";
		$scriptStr = '';
		if (!empty($jsEvents) && $statusBarText != "") {
			$jsEvents['onMouseOver'] = (isset($jsEvents['onMouseOver']) ? $jsEvents['onMouseOver'] . "window.status='$statusBarText';return true;" : "window.status='$statusBarText';return true;");
			$jsEvents['onMouseOut'] = (isset($jsEvents['onMouseOut']) ? $jsEvents['onMouseOut'] . "window.status='';return true;" : "window.status='';return true;");
		} else if ($statusBarText) {
			$scriptStr .= "onMouseOver=\"window.status='$statusBarText';return true;\" onMouseOut=\"window.status='';return true;\"";
		}
		foreach ($jsEvents as $event => $action)
			$scriptStr .= " $event=\"" . ereg_replace("\"", "'", $action) . "\"";
		return sprintf("<a href=\"%s\"%s%s%s%s%s%s%s%s>%s</a>", htmlentities($url),
			(!empty($name) ? " name=\"{$name}\"" : ""),
			(!empty($id) ? " id=\"{$id}\"" : ""),
			(!empty($rel) ? " rel=\"{$rel}\"" : ""),
			(!empty($accessKey) ? " accesskey=\"{$accessKey}\"" : ""),
			(!empty($target) ? " target=\"{$target}\"" : ""),
			(!empty($cssClass) ? " class=\"{$cssClass}\"" : ""),
			(!empty($statusBarText) ? " title=\"{$statusBarText}\"" : ""),
			(!empty($scriptStr) ? " {$scriptStr}" : ""),
			$text);
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::mailtoAnchor
	// @desc		Constr�i uma �ncora do tipo mailto:, com a possibilidade
	//				de ofuscar o c�digo gerado para proteger o endere�o de e-mail
	// @param		email string			Endere�o de e-mail
	// @param		text string				"" Texto para o �ncora. Se n�o for fornecido, o texto do �ncora ser� o endere�o de e-mail
	// @param		statusBarText string	"" Texto para a barra de status
	// @param		cssClass string			"" Estilo CSS para o �ncora
	// @param		id string				"" ID do �ncora
	// @param		obfuscate bool			"TRUE" Ofuscar o c�digo do �ncora
	// @return		string C�digo HTML gerado
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function mailtoAnchor($email, $text='', $statusBarText='', $cssClass='', $id='', $obfuscate=TRUE) {
		$scriptStr = (!empty($statusBarText) ? HtmlUtils::statusBar($statusBarText, TRUE) : '');
		$anchor = sprintf("<a href=\"mailto:%s\"%s%s%s>%s</a>", $email,
			(!empty($id) ? " id=\"{$id}\"" : ""),
			(!empty($cssClass) ? " class=\"{$cssClass}\"" : ""),
			$scriptStr,
			(empty($text) ? $email : $text)
		);
		if ($obfuscate) {
			$s = chunk_split(bin2hex($anchor), 2, '%');
			$s = '%' . substr($s, 0, strlen($s)-1);
			$s = chunk_split($s, 54, "'+'");
			$s = substr($s, 0, strlen($s)-3);
			$result = "<script type=\"text/javascript\" language=\"Javascript\">document.write(unescape('$s'));</script>";
			return $result;
		} else {
			return $anchor;
		}
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::image
	// @desc		Constr�i uma tag IMG para uma imagem
	// @param		src string		Caminho completo para a imagem
	// @param		alt string		"" Texto alt para a imagem
	// @param		wid int			"0" Largura da imagem
	// @param		hei int			"0" Altura da imagem
	// @param		hspace int		"-1" Espa�amento horizontal da imagem
	// @param		vspace int		"-1" Espa�amento vertical da imagem
	// @param		align string	"" Alinhamento da imagem
	// @param		id string		"" ID para o objeto criado
	// @param		swpImage string	"" Caminho completo para a imagem de swap a ser utilizada
	// @param		cssClass string	"" Estilo CSS para a imagem
	// @return		string C�digo da tag IMG da imagem
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function image($src, $alt='', $wid=0, $hei=0, $hspace=-1, $vspace=-1, $align='', $id='', $swpImage='', $cssClass='') {
		// ID padr�o
		if (empty($id))
			$id = PHP2Go::generateUniqueId('htmlimage');
		return sprintf ("<img id=\"%s\" src=\"%s\" alt=\"%s\" border=\"0\"%s%s%s%s%s%s%s>",
			$id, htmlentities($src), $alt,
			($wid > 0 ? " width=\"{$wid}\"" : ""),
			($hei > 0 ? " height=\"{$hei}\"" : ""),
			($hspace >= 0 ? " hspace=\"{$hspace}\"" : ""),
			($vspace >= 0 ? " vspace=\"{$vspace}\"" : ""),
			(!empty($align) ? " align=\"{$align}\"" : ""),
			(!empty($cssClass) ? " class=\"{$cssClass}\"" : ""),
			(!empty($swpImage) ? " onLoad=\"var {$id}_swp=new Image();{$id}_swp.src='$swpImage'\" onMouseOver=\"this.src='$swpImage'\" onMouseOut=\"this.src='$src'\"" : "")
		);
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::button
	// @desc		Constr�i uma tag BUTTON, que representa um bot�o
	// @param		type string			"SUBMIT" Tipo do bot�o: button, submit ou reset
	// @param		name string			"" Nome do bot�o
	// @param		value string		"" Valor do bot�o
	// @param		script string		"" Eventos JavaScript a serem tratados
	// @param		alt string			"" Texto alternativo para o bot�o
	// @param		css string			"" Estilo CSS para o bot�o. Ser� ignorado se o browser n�o suportar CSS em bot�es
	// @param		accessKey string	"" Tecla de atalho
	// @return		string C�digo HTML constru�do para o bot�o
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function button($type='SUBMIT', $name='', $value='', $script='', $alt='', $css='', $accessKey='') {
		$type = strtolower($type);
		if ($type != 'button' && $type != 'submit' && $type != 'reset')
			$type = 'button';
		// nome padr�o
		if (empty($name))
			$name = PHP2Go::generateUniqueId('htmlbutton');
		// compatibilidade do browser para uso de CSS
		if (!empty($css)) {
			$Agent =& UserAgent::getInstance();
			if (!$Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')))
				$css = '';
		}
		$Lang =& LanguageBase::getInstance();
		return sprintf ("<button type=\"%s\" id=\"%s\" name=\"%s\"%s%s%s%s>%s</button>",
			$type, $name, $name,
			(!empty($script) ? " {$script}" : ""),
			(!empty($alt) ? " alt=\"{$alt}\"" : ""),
			(!empty($css) ? " class=\"{$css}\"" : ""),
			(!empty($accessKey) ? " accesskey=\"{$accessKey}\"" : ""),
			(!empty($value) ? $value : $Lang->getLanguageValue('DEFAULT_BTN_VALUE')));
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::window
	// @desc		Constr�i a chamada para a fun��o JavaScript createWindow, que cria uma nova janela
	// @param		url string			URL da nova janela a ser aberta
	// @param		windowType int		Tipo da janela. Para maiores informa��es, consulte a documenta��o do arquivo window.js
	// @param		windowWidth int		"640" Largura da janela
	// @param		windowHeight int	"480" Altura da janela
	// @param		windowX int			"0" Coordenada X da janela
	// @param		windowY int			"0" Coordenada Y da janela
	// @param		windowTitle string	"" T�tulo da janela
	// @param		windowReturn bool	"FALSE" Indica se a fun��o de cria��o da janela deve retornar o objeto Window
	// @return		string Chamada da fun��o
	// @note		Esta fun��o � �til em conjunto com HtmlUtils::anchor, na constru��o de links para abertura de popups
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function window($url, $windowType, $windowWidth=640, $windowHeight=480, $windowX=0, $windowY=0, $windowTitle='', $windowReturn=FALSE) {
		if ($windowTitle == '')
			$windowTitle = PHP2Go::generateUniqueId('window');
		if ($windowReturn)
			return "return Window.open('{$url}', {$windowWidth}, {$windowHeight}, {$windowX}, {$windowY}, {$windowType} , '{$windowTitle}', true);";
		else
			return "Window.open('{$url}', {$windowWidth}, {$windowHeight}, {$windowX}, {$windowY}, {$windowType}, '{$windowTitle}', false);";
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::scrollableArea
	// @desc		Constr�i uma DIV com rolagem horizontal e/ou vertical se
	//				o conte�do exceder o tamanho definido
	// @param		content string		Conte�do
	// @param		width int			Largura do container
	// @param		height int			Altura do container
	// @param		overflow string		"auto" Valor para o atributo overflow da defini��o de estilos do container
	// @param		cssClass string		"" Estilo CSS para o container
	// @param		id string			"" ID para o container
	// @return		string C�digo HTML resultante
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function scrollableArea($content, $width, $height, $overflow='auto', $cssClass='', $id='') {
		if (empty($id))
			$id = PHP2Go::generateUniqueId('scrollarea');
		$style = "width:{$width}px;height:{$height}px;overflow:{$overflow}";
		$cssClass = (!empty($cssClass) ? " class=\"{$cssClass}\"" : '');
		return "<div id=\"{$id}\" style=\"{$style}\"{$cssClass}>{$content}</div>";
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::itemList
	// @desc		Constr�i uma lista de itens em HTML utilizando as tags OL ou UL
	// @param		values array		Array de valores
	// @param		ordered bool		"FALSE" Lista ordenada ou n�o ordenada
	// @param		listAttr string		"" String de atributos para a lista
	// @param		itemAttr string		"" String de atributos para cada um dos itens
	// @return		string C�digo HTML da lista
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function itemList($values, $ordered=FALSE, $listAttr='', $itemAttr='') {
		$array = (array)$values;
		if (empty($array))
			return '';
		$tag = ($ordered ? 'ol' : 'ul');
		if (!empty($listAttr))
			$listAttr = ' ' . ltrim($listAttr);
		if (!empty($itemAttr))
			$itemAttr = '  ' . ltrim($itemAttr);
		$buf = "<{$tag}{$listAttr}>";
		foreach ($array as $entry) {
			if (is_array($entry))
				$buf .= HtmlUtils::itemList($entry, $ordered, $listAttr, $itemAttr);
			else
				$buf .= "<li{$itemAttr}>{$entry}</li>";
		}
		$buf .= "</{$tag}>";
		return $buf;
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::definitionList
	// @desc		Monta, a partir de um array associativo, uma lista de termos e
	//				defini��es, onde as chaves s�o os termos e os valores as defini��es,
	//				utilizando as tags DL, DT e DD
	// @param		values array		Array de valores
	// @param		listAttr string		"" String de atributos para a lista
	// @param		termAttr string		"" String de atributos para os termos
	// @param		defAttr string		"" String de atributos para as defini��es
	// @return		string C�digo HTML da lista
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function definitionList($values, $listAttr='', $termAttr='', $defAttr='') {
		$array = (array)$values;
		if (empty($array))
			return '';
		if (!empty($listAttr))
			$listAttr = ' ' . ltrim($listAttr);
		if (!empty($termAttr))
			$termAttr = ' ' . ltrim($termAttr);
		if (!empty($defAttr))
			$defAttr = ' ' . ltrim($defAttr);
		$buf = "<dl{$listAttr}>";
		foreach ($array as $key => $value) {
			$buf .= "<dt{$termAttr}>{$key}";
			if (is_array($value))
				$buf .= HtmlUtils::definitionList($value, $listAttr, $termAttr, $defAttr);
			else
				$buf .= "<dd{$defAttr}>{$value}";
		}
		$buf .= "</dl>";
		return $buf;
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::table
	// @desc		M�todo utilit�rio de constru��o de uma tabela a partir de um array bidimensional
	// @param		table array					Tabela - array bidimensional
	// @param		headers bool				"TRUE" Exibir cabe�alhos a partir das chaves da primeira entrada do array
	// @param		tableAttr string			"" Atributos da tabela
	// @param		cellAttr string				"" Atributos de c�lula
	// @param		alternateCellAttr string	"" Atributos para c�lula �mpar
	// @param		headerAttr string			"" Atributos para os cabe�alhos
	// @note		Se o par�metro $alternateCellAttr for fornecido, ser� utilizada altern�ncia de atributos a cada linha, para todas as c�lulas
	// @return		string C�digo HTML da tabela
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function table($table, $headers=TRUE, $tableAttr='', $cellAttr='', $alternateCellAttr='', $headerAttr='') {
		$table = (array)$table;
		if (empty($table))
			return '';
		if (!empty($tableAttr))
			$tableAttr = ' ' . ltrim($tableAttr);
		if (!empty($headerAttr))
			$headerAttr = ' ' . ltrim($headerAttr);
		if (!empty($cellAttr))
			$cellAttr = ' ' . ltrim($cellAttr);
		if (!empty($alternateCellAttr))
			$alternateCellAttr = ' ' . ltrim($alternateCellAttr);
		$buf = "<table{$tableAttr}>\n";
		// inclui cabe�alhos, se solicitado
		if ($headers) {
			list(, $row) = each($table);
			$row = array_keys((array)$row);
			$buf .= "<tr>";
			foreach ($row as $cell)
				$buf .= "<th{$headerAttr}>{$cell}</th>";
			$buf .= "</tr>";
		}
		// itera��o nas linhas da tabela
		$count = 1;
		foreach ($table as $entry) {
			$attr = (!empty($alternateCellAttr) && ($count%2) == 0 ? $alternateCellAttr : $cellAttr);
			$buf .= "<tr>\n";
			if (!TypeUtils::isArray($entry)) {
				$buf .= "<td{$attr}>{$entry}</td>";
			} else {
				foreach ($entry as $cellValue)
					$buf .= "<td{$attr}>{$cellValue}</td>";
			}
			$buf .= "</tr>\n";
			$count++;
		}
		$buf .= "</table>";
		return $buf;
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::colorize
	// @desc		Adiciona tags HTML e cor com CSS a um determinado texto
	// @note		As tags HTML somente s�o adicionadas se o texto fornecido for n�o vazio
	// @param		text string		Texto a ser formatado
	// @param		color string	Cor
	// @param		tagName string	"span" Tag HTML a ser utilizada
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function colorize($text, $color, $tagName='span') {
		if (!StringUtils::isEmpty($text))
			return sprintf("<%s style=\"color:%s\">%s</%s>", $tagName, $color, $text, $tagName);
		return '';
	}

	//!-----------------------------------------------------------------
	// @function 	HtmlUtils::noBreakSpace
	// @desc 		Imprime uma seq��ncia de espa�os em branco (&nbsp;)
	// @param 		n int			"1" N�mero de espa�os em branco a exibir
	// @return 		string Seq��ncia de caracteres '&nbsp;'
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function noBreakSpace($n=1) {
		return str_repeat('&nbsp;', $n);
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::tagRepeat
	// @desc		Imprime uma mesma tag $n vezes
	// @param		tag string		Nome da tag
	// @param		content string	Conte�do para a tag
	// @param		n int			"1" N�mero de repeti��es
	// @return		string C�digo HTML gerado
	// @note		Este m�todo � �til para tags como BIG, SMALL, BR, BLOCKQUOTE, etc...
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function tagRepeat($tag, $content, $n=1) {
		$n = max(1, $n);
		$tag = strtolower($tag);
		return str_repeat("<{$tag}>", $n) . $content . str_repeat("</{$tag}>", $n);
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::newLineToBr
	// @desc		Transforma quebras de linha em tags <br> HTML, otimizando
	//				a funcionalidade j� oferecida pela fun��o nl2br()
	// @param		str string		String original
	// @return		string String com as quebras de linha transformadas
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function newLineToBr($str) {
		return str_replace("\n", "<br>\n", $str);
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::parseLinks
	// @desc		Parseia por��es clic�veis (links) dentro de uma string,
	//				gerando c�digo HTML para os �ncoras encontrados
	// @param		str string		String original
	// @return		string String com os links encontrados transformados em �ncoras
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function parseLinks($str) {
        $str = preg_replace_callback('=(http://|https://|ftp://|mailto:|news:)(\S+)(\*\s|\=\s|&quot;|&lt;|&gt;|<|>|\(|\)|\s|$)=Usmix', array('HtmlUtils', 'buildAnchor'), $str);
        return $str;
	}


    //!-----------------------------------------------------------------
	// @function	HtmlUtils::buildAnchor
	// @desc		Constr�i um �ncora para uma das express�es interpretadas
	//				em HtmlUtils::parseLinks
	// @param		aMatches array	Uma das ocorr�ncias retornadas no m�todo HtmlUtils::parseLinks
	// @return		string �ncora correspondente
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function buildAnchor($aMatches) {
        $sHref = $aMatches[1] . $aMatches[2];
		return HtmlUtils::anchor($sHref, $sHref, $sHref, '', array(), '_blank') . $aMatches[3];
    }


	//!-----------------------------------------------------------------
	// @function	HtmlUtils::flashMovie
	// @desc		Monta o c�digo de exibi��o de um movie SWF, a
	// 				partir dos par�metros b�sicos
	// @param		src string		URL do filme SWF
	// @param		wid int			"0" Largura para o movie
	// @param		hei int			"0" Altura para o movie
	// @param		arrPars array	"array()" Array associativo de par�metros
	// @return		string C�digo da tag EMBED/OBJECT do movie
	// @see			HtmlUtils::realPlayerMovie
	// @see			HtmlUtils::mediaPlayerMovie
	// @see			HtmlUtils::quickTimeMovie
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function flashMovie($src, $wid=0, $hei=0, $arrPars=array(), $transparent=FALSE) {
		$src = htmlentities($src);
		$srcP = $src;
		if (TypeUtils::isArray($arrPars) && !empty($arrPars)) {
			$srcP .= "?";
			foreach($arrPars as $key => $value)
				$srcP .= $key . "=" . $value . "&";
			$srcP = substr($srcP, 0, -1);
		}
		$srcP = htmlentities($srcP);
		return sprintf ("<!-- IE -->
						  <object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\"%s%s align=\"top\">
						  <param name=\"movie\" value=\"%s\"/><param name=\"quality\" value=\"high\"/>%s
						  <!-- NN -->
						  <embed src=\"%s\" quality=\"high\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\"%s%s%s align=\"top\" scale=\"exactfit\"></embed>
						  </object>",
			($wid > 0 ? " width=\"{$wid}\"" : ""),
			($hei > 0 ? " height=\"{$hei}\"" : ""),
			$srcP,
			($transparent ? "<param name=\"bgcolor\" value=\"#ffffff\"/><param name=\"wmode\" value=\"transparent\"/>" : ""),
			$src,
			($transparent ? " wmode=\"transparent\"" : ""),
			($wid > 0 ? " width=\"{$wid}\"" : ""),
			($hei > 0 ? " height=\"{$hei}\"" : ""));
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::realPlayerMovie
	// @desc		Monta o c�digo HTML para exibi��o de um filme
	// 				nos formatos do Real Player
	// @param		src string		URL do movie
	// @param		wid int			"0" Largura para o movie
	// @param		hei int			"0" Altura para a janela de exibi��o
	// @param		flags array	Vetor associativo de par�metros
	// @return		string C�digo da tag EMBED do movie
	// @note		Os par�metros aceitos s�o CLIP_INFO, CLIP_STATUS,
	// 				CONTROLS, AUTO_START e LOOP. S�o considerados verdadeiros
	// 				se fornecidos com valor != 0
	// @see			HtmlUtils::flashMovie
	// @see			HtmlUtils::mediaPlayerMovie
	// @see			HtmlUtils::quickTimeMovie
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function realPlayerMovie($src, $wid = 0, $hei = 0, $flags) {
		$srcVals = split("[/\\.]", strtolower($src));
		$extension = $srcVals[sizeof($srcVals)-1];
		if ($extension == 'ram' || $extension == 'ra' || $extension == 'rm' || $extension == 'rpm' || $extension == 'smil') {
			$src = htmlentities($src);
			$movieCode = sprintf("<embed name=\"realVideo\" src=\"%s\" type=\"audio/x-pn-realaudio\" pluginspage=\"http://www.real.com/player\"
										%s%shspace=\"0\" vspace=\"0\" border=\"0\" nojava=\"True\" controls=\"ImageWindow\" console=\"_master\" autostart=\"%d\" loop=\"%s\">",
				$src, ($wid > 0 ? " width=\"" . $wid . "\"" : ""), ($hei > 0 ? " height=\"" . $hei . "\"" : ""), ($flags['AUTO_START']) ? 1 : 0, ($flags['LOOP'] ? "TRUE" : "FALSE"));
			if ($flags['CONTROLS']) {
				$movieCode .= sprintf("<br><embed name=\"realVideo\" src=\"%s\" type=\"audio/x-pn-realaudio\" pluginspage=\"http://www.real.com/player\"
											  %s%shspace=\"0\" vspace=\"0\" border=\"0\" nojava=\"True\" controls=\"ControlPanel\" console=\"rVideo\" autostart=\"%d\" loop=\"%s\">",
					$src, ($wid > 0 ? " width=\"" . $wid . "\"" : ""), " height=\"35\"", ($flags['AUTO_START']) ? 1 : 0, ($flags['LOOP'] ? "TRUE" : "FALSE"));
			}
			if ($flags['CLIP_STATUS']) {
				$movieCode .= sprintf("<br><embed name=\"realVideo\" src=\"%s\" type=\"audio/x-pn-realaudio\" pluginspage=\"http://www.real.com/player\"
											  %s%shspace=\"0\" vspace=\"0\" border=\"0\" nojava=\"True\" controls=\"StatusBar\" console=\"rVideo\">",
					$src, ($wid > 0 ? " width=\"" . $wid . "\"" : ""), " height=\"30\"");
			}
			if ($flags['CLIP_INFO']) {
				$movieCode .= sprintf("<br><embed name=\"realVideo\" src=\"%s\" type=\"audio/x-pn-realaudio\" pluginspage=\"http://www.real.com/player\"
											  %s%shspace=\"0\" vspace=\"0\" border=\"0\" nojava=\"True\" controls=\"TACCtrl\" console=\"rVideo\">",
					$src, ($wid > 0 ? " width=\"" . $wid . "\"" : ""), " height=\"32\"");
			}
			return $movieCode;
		} else
			return "";
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::mediaPlayerMovie
	// @desc		Monta o c�digo HTML para exibi��o de um filme
	// 				nos formatos do Windows Media Player
	// @param		src string	URL do movie
	// @param		wid int			"0" Largura para o movie
	// @param		hei int			"0" Altura para a janela de exibi��o
	// @param		flags array		"array()" Vetor de par�metros
	// @return		string C�digo da tag EMBED/OBJECT do movie
	// @note		Os par�metros aceitos s�o CLIP_INFO, CLIP_STATUS,
	// 				CONTROLS, AUTO_START e AUTO_SIZE. S�o considerados
	// 				verdadeiros se fornecidos com valor != 0
	// @see			HtmlUtils::flashMovie
	// @see			HtmlUtils::realPlayerMovie
	// @see			HtmlUtils::quickTimeMovie
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function mediaPlayerMovie($src, $wid = 0, $hei = 0, $flags = array()) {
		$srcVals = split("[/\\.]", strtolower($src));
		$extension = $srcVals[sizeof($srcVals)-1];
		if ($extension == 'asf' || $extension == 'asx' || $extension == 'wmv' || $extension == 'wma') {
			$src = htmlentities($src);
			return sprintf ("<!-- IE -->
							   <object id=\"MPlay1\" classid=\"CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95\" codebase=\"http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715\" standby=\"Loading Microsoft� Windows� Media Player components...\" type=\"application/x-oleobject\"%s%s>
							   <param name=\"FileName\" value=\"%s\">
							   <param name=\"ShowDisplay\" value=\"%s\">
							   <param name=\"ShowStatusBar\" value=\"%s\">
							   <param name=\"StatusBar\" value=\"True\">
							   <param name=\"AnimationAtStart\" value=\"True\">
							   <param name=\"ShowAudioControls\" value=\"%s\">
							   <param name=\"ShowPositionControls\" value=\"%s\">
							   <param name=\"ShowControls\" value=\"%s\">
							   <param name=\"AutoSize\" value=\"%s\">
							   <param name=\"AutoStart\" value=\"%d\">
							   <param name=\"AutoRewind\" value=\"TRUE\">
							   <!-- NN -->
							   <embed%s%s filename=\"%s\" src=\"%s\" pluginspage=\"http://www.microsoft.com/Windows/MediaPlayer/\" name=\"MPlay1\" type=\"video/x-mplayer2\" showdisplay=\"%s\" showstatusbar=\"%s\" statusbar=\"true\" autorewind=\"1\" animationatstart=\"true\" showaudiocontrols=\"%s\" showpositioncontrols=\"%s\" showcontrols=\"%s\" autosize=\"%s\" autostart=\"%d\">
							   </embed>
							   </object>",
				($wid > 0) ? " width=\"" . $wid . "\"" : "",
				($hei > 0) ? " height=\"" . $hei . "\"" : "", $src,
				($flags['CLIP_INFO'] ? "TRUE" : "FALSE"),
				($flags['CLIP_STATUS'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['AUTO_SIZE'] ? "TRUE" : "FALSE"),
				($flags['AUTO_START'] ? 1 : 0),
				($wid > 0) ? " width='" . $wid . "'" : "",
				($hei > 0) ? " height='" . $hei . "'" : "", $src, $src,
				($flags['CLIP_INFO'] ? "1" : "0"),
				($flags['CLIP_STATUS'] ? "1" : "0"),
				($flags['CONTROLS'] ? "1" : "0"),
				($flags['CONTROLS'] ? "1" : "0"),
				($flags['CONTROLS'] ? "1" : "0"),
				($flags['AUTO_SIZE'] ? "1" : "0"),
				($flags['AUTO_START'] ? 1 : 0)
				);
		} else
			return '';
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::quickTimeMovie
	// @desc		Monta o c�digo HTML para a exibi��o de um movie
	// 				atrav�s do plug-in Quick Time
	// @param 		src string	URL ou caminho no servidor do movie
	// @param 		wid int			"0" Largura para o movie
	// @param 		hei int			"0" Altura para o movie
	// @param 		flags array		"array()" Atributos de configura��o da exibi��o do movie
	// @return		string C�digo da tag EMBED do movie
	// @note 		Os atributos aceitos s�o AUTO_START, CACHE, CONTROLS, LOOP e AUTO_SIZE
	// @see			HtmlUtils::flashMovie
	// @see			HtmlUtils::realPlayerMovie
	// @see			HtmlUtils::mediaPlayerMovie
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function quickTimeMovie($src, $wid = 0, $hei = 0, $flags = array()) {
		$srcVals = split("[/\\.]", strtolower($src));
		$extension = $srcVals[sizeof($srcVals)-1];
		if ($extension == 'mov' || $extension == 'qt') {
			$src = htmlentities($src);
			return sprintf("<embed name=\"Quick Time Video\" src=\"%s\" type=\"video/quicktime\" pluginspage=\"http://www.apple.com/quicktime/download/indext.html\"
							   %s%s autostart=\"%s\" kioskmode=\"TRUE\" cache=\"%s\" controller=\"%s\" loop=\"%s\" moviename=\"quickTime\" scale=\"%s\">
								</embed>", $src,
				($wid > 0 ? " width=\"" . $wid . "\"" : ""),
				($hei > 0 ? " height=\"" . $hei . "\"" : ""),
				($flags['AUTO_START'] ? "TRUE" : "FALSE"),
				($flags['CACHE'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['LOOP'] ? "TRUE" : "FALSE"),
				($flags['AUTO_SIZE'] ? "1" : "TOFIT")
				);
		} else {
			return "";
		}
	}

	//!-----------------------------------------------------------------
	// @function 	HtmlUtils::statusBar
	// @desc 		Imprime um texto na barra de status no evento
	// 				onMouseOver de um objeto do documento
	// @param 		str string			Texto para a barra de status e o hint
	// @param 		return bool			"TRUE" TRUE para retornar a string, FALSE para imprimi-la
	// @return		mixed	Retorna a string montada se $return == TRUE. Do contr�rio, retorna TRUE
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function statusBar($str, $return=TRUE) {
		$mText = '';
		if (!empty($str))
			$mText = "title=\"$str\" onMouseOver=\"window.status='$str';return true;\" onMouseOut=\"window.status='';return true;\"";
		if ($return) {
			return $mText;
		} else {
			print $mText;
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	HtmlUtils::overPopup
	// @desc 		Gera uma 'popup' de texto associada ao evento
	// 				onMouseOver do elemento atual
	// @param  		&_Document Document object	Objeto Document onde a popup ser� gerada
	// @param 		caption string		Texto para a popup
	// @param 		argumentList string	"" Lista de argumentos para a gera��o da popup
	// @return		string String da chamada dos eventos onMouseOver e onMouseOut
	// @note 		Esta fun��o utiliza a biblioteca overLIB, desenvolvida por Erik Bosrup.
	// 				Para maiores informa��es sobre como construir o par�metro $argumentList,
	// 				consulte a documenta��o do projeto em http://www.bosrup.com/web/overlib
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function overPopup(&$_Document, $caption, $argumentList = '') {
		static $divInserted;
		if (!isset($divInserted)) {
			$_Document->appendBodyContent("<div id=\"overDiv\" style=\"position:absolute;visibility:hidden;z-index:1000;\"></div>");
			$divInserted = TRUE;
		}
		$_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/overlib/overlib.js");
		return "onMouseOver='return overlib(\"" . $caption . "\"" . ($argumentList != '' ? ',' . $argumentList : '') . ");' onMouseOut='return nd();'";
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::alert
	// @desc		Imprime um 'alert' JavaScript
	// @param		msg string			Mensagem para o alert
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function alert($msg) {
		echo "<script language=\"Javascript\" type=\"text/javascript\">alert(\"", $msg, "\");</script>";
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::confirm
	// @desc		Imprime um di�logo 'confirm' JavaScript
	// @param		msg string			Mensagem para a caixa de di�logo
	// @param		trueAction string	"" A��o para retorno TRUE do usu�rio
	// @param		falseAction string	"" A��o para retorno FALSE do usu�rio
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function confirm($msg, $trueAction = '', $falseAction = '') {
		$confirm = "";
		if ($trueAction != "") {
			$confirm .= "<script type=\"text/javascript\">\n";
			$confirm .= "if (confirm(\"$msg\")) {\n";
			$confirm .= $trueAction . "\n";
			$confirm .= "}\n";
			if ($falseAction != "") {
				$confirm .= "else {";
				$confirm .= $falseAction . "\n";
				$confirm .= "}";
			}
			$confirm .= "</script>\n";
		} elseif ($falseAction != "") {
			$confirm .= "<script type=\"text/javascript\">\n";
			$confirm .= "if (!confirm(\"$msg\")) {\n";
			$confirm .= $falseAction . "\n";
			$confirm .= "}\n";
			$confirm .= "</script>\n";
		}
		echo $confirm;
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::closeWindow
	// @desc		Fecha a janela atual do browser utilizando JavaScript
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function closeWindow() {
		echo "<script language=\"Javascript\" type=\"text/javascript\">if (parent) parent.close(); else window.close();</script>\n";
		exit;
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::redirect
	// @desc		Redireciona para uma URL usando JavaScript
	// @param		url string		Url para redirecionamento
	// @param		object string	"document" Objeto base para o redirecionamento
	// @see			HtmlUtils::replace
	// @see			HttpResponse::redirect
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function redirect($url, $object = "document") {
		if ($object[strlen($object)-1] != '.')
			$object .= ".";
		echo "<script language=\"Javascript\" type=\"text/javascript\">", $object, "location.href = \"", $url, "\"</script>\n";
		exit;
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::replace
	// @desc		Substitui a entrada atual do hist�rico por outra URL
	// @param		url string		Url a ser carregada
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function replace($url) {
		echo "<script language=\"Javascript\" type=\"text/javascript\">location.replace(\"", $url, "\");</script>\n";
		exit;
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::refresh
	// @desc		Imprime a tag META de redirecionamento
	// @access		public
	// @param		url string		Url para redirecionamento
	// @param		time int		"1" Nro. de segundos de espera para o redirecionamento
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function refresh($url, $time = 1) {
		echo "<meta http-equiv=\"refresh\" content=\"", $time, "; url=", htmlentities($url), "\">";
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::goBackN
	// @desc		Volta 'n' posi��es no hist�rico do browser
	// @param		n int			"1" N�mero de posi��es para retornar
	// @return		void
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function goBackN($n = 1) {
		$n = ($n > 0) ? TypeUtils::parseString($n) : "1";
		echo "<script language=\"Javascript\" type=\"text/javascript\">history.go(-", $n, ")</script>\n";
		exit;
	}

	//!-----------------------------------------------------------------
	// @function	HtmlUtils::focus
	// @desc		Gera um script JavaScript para requisitar foco
	// 				em um campo de um formul�rio
	// @param		form string		Nome do formul�rio
	// @param		field string	Nome do campo
	// @param		object string	"" Objeto base para a fun��o
	// @param		return bool		"FALSE" Indica que o c�digo JavaScript deve ser retornado e n�o impresso
	// @return		mixed	Retorna o c�digo JavaScript se $return == TRUE. Do contr�rio, retorna TRUE
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function focus($form, $field, $object = "", $return=FALSE) {
		if ($object != '')
			$object .= '.';
		$strScript =
			"\n<script type=\"text/javascript\">\n" .
			"var fld = \$F({$object}document.forms['{$form}'], '{$field}');\n" .
			"if (fld) { fld.focus(); }\n" .
			"</script>\n";
		if ($return) {
			return $strScript;
		} else {
			echo $strScript;
			return TRUE;
		}
	}
}
?>