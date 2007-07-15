<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
 * Collection of utility methods to build and handle HTML code
 *
 * @package util
 * @uses UserAgent
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class HtmlUtils extends PHP2Go
{
	/**
	 * Builds an anchor tag
	 *
	 * Examples:
	 * <code>
	 * HtmlUtils::anchor('another_page.php');
	 * HtmlUtils::anchor('http://www.website.com', 'Website', 'Website');
	 * HtmlUtils::anchor('javascript:;', 'Link', 'Link', '', array('onclick'=>'doSomething();'));
	 * </code>
	 *
	 * @param string $url Target URL
	 * @param string $text Caption text
	 * @param string $statusBarText Status bar text (appears upon onmouseover)
	 * @param string $cssClass CSS class
	 * @param array $jsEvents Hashmap of JS event listeners
	 * @param string $target Target frame or window
	 * @param string $name Anchor name
	 * @param string $id Anchor ID
	 * @param string $rel Relathionship between the current document and the target document
	 * @param string $accessKey Access key
	 * @return string
	 * @static
	 */
	function anchor($url, $text, $statusBarText='', $cssClass='', $jsEvents=array(), $target='', $name='', $id='', $rel='', $accessKey='') {
		if (empty($url))
			$url = "javascript:void(0);";
		$scriptStr = '';
		$jsEvents = array_change_key_case((array)$jsEvents, CASE_LOWER);
		if (!empty($jsEvents) && $statusBarText != "") {
			$jsEvents['onmouseover'] = (isset($jsEvents['onmouseover']) ? $jsEvents['onmouseover'] . "window.status='$statusBarText';return true;" : "window.status='$statusBarText';return true;");
			$jsEvents['onmouseover'] = (isset($jsEvents['onmouseover']) ? $jsEvents['onmouseover'] . "window.status='';return true;" : "window.status='';return true;");
		} else if ($statusBarText) {
			$scriptStr .= "onmouseover=\"window.status='$statusBarText';return true;\" onmouseout=\"window.status='';return true;\"";
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

	/**
	 * Builds a "mailto:" anchor
	 *
	 * Optionally, the email address can be obfuscated
	 * to prevent against spammers.
	 *
	 * Examples:
	 * <code>
	 * HtmlUtils::mailtoAnchor('foo@bar.org');
	 * HtmlUtils::mailtoAnchor('foo@bar.org', 'Send me an e-mail', 'Send me an-email');
	 * </code>
	 *
	 * @param string $email E-mail address
	 * @param string $text Caption text
	 * @param string $statusBarText Status bar text (appears upon onmouseover)
	 * @param string $cssClass CSS class
	 * @param string $id Anchor ID
	 * @param bool $obfuscate Whether email address should be obfuscated
	 * @return string
	 * @static
	 */
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

	/**
	 * Builds an IMG tag
	 *
	 * Examples:
	 * <code>
	 * // image path only
	 * HtmlUtils::image('images/picture.gif');
	 * // inline dimensions
	 * HtmlUtils::image('images/photo.gif', '', 50, 100);
	 * // id and swap image
	 * HtmlUtils::image('images/button_off.gif', '', 0, 0, -1, -1, '', 'btnId', 'images/button_on.gif');
	 * </code>
	 *
	 * @param string $src Image path
	 * @param string $alt Alternate text
	 * @param int $wid Image width
	 * @param int $hei Image height
	 * @param int $hspace Horizontal spacing
	 * @param int $vspace Vertical spacing
	 * @param string $align Image align
	 * @param string $id Image ID
	 * @param string $swpImage Swap image path
	 * @param string $cssClass CSS class
	 * @return string
	 * @static
	 */
	function image($src, $alt='', $wid=0, $hei=0, $hspace=-1, $vspace=-1, $align='', $id='', $swpImage='', $cssClass='') {
		if (empty($id))
			$id = PHP2Go::generateUniqueId('htmlimage');
		return sprintf ("<img id=\"%s\" src=\"%s\" alt=\"%s\" border=\"0\"%s%s%s%s%s%s%s />",
			$id, htmlentities($src), $alt,
			($wid > 0 ? " width=\"{$wid}\"" : ""),
			($hei > 0 ? " height=\"{$hei}\"" : ""),
			($hspace >= 0 ? " hspace=\"{$hspace}\"" : ""),
			($vspace >= 0 ? " vspace=\"{$vspace}\"" : ""),
			(!empty($align) ? " align=\"{$align}\"" : ""),
			(!empty($cssClass) ? " class=\"{$cssClass}\"" : ""),
			(!empty($swpImage) ? " onload=\"var {$id}_swp=new Image();{$id}_swp.src='$swpImage'\" onmouseover=\"this.src='$swpImage'\" onmouseout=\"this.src='$src'\"" : "")
		);
	}

	/**
	 * Builds a BUTTON tag
	 *
	 * Examples:
	 * <code>
	 * HtmlUtils::button('BUTTON', 'btnGo', 'Go', "onclick='go();'");
	 * HtmlUtils::button('SUBMIT', 'btnSubmit', 'Send');
	 * HtmlUtils::button('BUTTON', 'btnBack', 'Back', "onclick='history.back();'");
	 * </code>
	 *
	 * @param string $type Button type
	 * @param string $id Button ID
	 * @param string $value Button value
	 * @param string $script JS event listeners
	 * @param string $alt Alternate text
	 * @param string $cssClass CSS class
	 * @param string $accessKey Access key
	 * @return string
	 * @static
	 */
	function button($type='SUBMIT', $id='', $value='', $script='', $alt='', $cssClass='', $accessKey='') {
		$type = strtolower($type);
		if ($type != 'button' && $type != 'submit' && $type != 'reset')
			$type = 'button';
		if (empty($id))
			$id = PHP2Go::generateUniqueId('htmlbutton');
		if (!empty($cssClass)) {
			$Agent =& UserAgent::getInstance();
			if (!$Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')))
				$cssClass = '';
		}
		$Lang =& LanguageBase::getInstance();
		return sprintf ("<button type=\"%s\" id=\"%s\"%s%s%s%s>%s</button>",
			$type, $id,
			(!empty($script) ? " {$script}" : ""),
			(!empty($alt) ? " alt=\"{$alt}\"" : ""),
			(!empty($cssClass) ? " class=\"{$cssClass}\"" : ""),
			(!empty($accessKey) ? " accesskey=\"{$accessKey}\"" : ""),
			(!empty($value) ? $value : $Lang->getLanguageValue('DEFAULT_BTN_VALUE')));
	}

	/**
	 * Builds a call to the Window.open JS function
	 *
	 * Example:
	 * <code>
	 * $window = HtmlUtils::window('page.php', 48, 600, 400);
	 * $link = HtmlUtils::anchor('javascript:;', 'Open me', 'Open me', '', array('onclick'=>$window));
	 * </code>
	 *
	 * @param string $url Window URL
	 * @param int $windowType Window type (bitmap of chrome properties)
	 * @param int $windowWidth Window width
	 * @param int $windowHeight Window height
	 * @param int $windowX Window X position
	 * @param int $windowY Window Y position
	 * @param string $windowTitle Window title
	 * @param bool $windowReturn Whether to return the window object
	 * @return string
	 * @static
	 */
	function window($url, $windowType=255, $windowWidth=640, $windowHeight=480, $windowX=0, $windowY=0, $windowTitle='', $windowReturn=FALSE) {
		if ($windowTitle == '')
			$windowTitle = PHP2Go::generateUniqueId('window');
		if ($windowReturn)
			return "return Window.open('{$url}', {$windowWidth}, {$windowHeight}, {$windowX}, {$windowY}, {$windowType} , '{$windowTitle}', true);";
		else
			return "Window.open('{$url}', {$windowWidth}, {$windowHeight}, {$windowX}, {$windowY}, {$windowType}, '{$windowTitle}', false);";
	}

	/**
	 * Builds the HTML code of a DIV with overflow handling
	 *
	 * @param string $content Contents
	 * @param int $width Width
	 * @param int $height Height
	 * @param string $overflow Overflow handling type
	 * @param string $cssClass CSS class
	 * @param string $id ID
	 * @return string Generated HTML code
	 * @static
	 */
	function scrollableArea($content, $width, $height, $overflow='auto', $cssClass='', $id='') {
		if (empty($id))
			$id = PHP2Go::generateUniqueId('scrollarea');
		$style = "width:{$width}px;height:{$height}px;overflow:{$overflow}";
		$cssClass = (!empty($cssClass) ? " class=\"{$cssClass}\"" : '');
		return "<div id=\"{$id}\" style=\"{$style}\"{$cssClass}>{$content}</div>";
	}

	/**
	 * Builds the HTML code of a list of items, using OL or UL tags
	 *
	 * @param array $values List elements
	 * @param bool $ordered Whether to render an ordered (OL) or an unordered (UL) list
	 * @param string $listAttr Attributes of the list
	 * @param string $itemAttr Attributes for all list items
	 * @return string Generated HTML code
	 * @static
	 */
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

	/**
	 * Builds the HTML code a list of terms and definitions
	 *
	 * @param array $values Hash array of terms and definitions
	 * @param string $listAttr Attributes of the list
	 * @param string $termAttr Attributes for all terms
	 * @param string $defAttr Attributes for all definitions
	 * @return string Generated HTML code
	 * @static
	 */
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

	/**
	 * Builds the HTML code of a table, based on a given array
	 *
	 * Example:
	 * <code>
	 * $table = array(array('Name' => 'Foo'), array('Name' => 'Bar'), array('Name' => 'Baz'));
	 * print HtmlUtils::table($table, TRUE, "", " class='odd_row'", " class='even_row'");
	 * </code>
	 *
	 * @param array $table Table data
	 * @param bool $headers Whether table headers should be rendered
	 * @param string $tableAttr Attributes of the TABLE element
	 * @param string $cellAttr Attributes for all TD elements
	 * @param string $alternateCellAttr Alternating style (even/odd rows)
	 * @param string $headerAttr Attributes for all TH elements
	 * @return string Generated HTML code
	 * @static
	 */
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
		// render headers when required
		if ($headers) {
			list(, $row) = each($table);
			$row = array_keys((array)$row);
			$buf .= "<tr>";
			foreach ($row as $cell)
				$buf .= "<th{$headerAttr}>{$cell}</th>";
			$buf .= "</tr>";
		}
		// iterate throuth table rows
		$count = 1;
		foreach ($table as $entry) {
			$attr = (!empty($alternateCellAttr) && ($count%2) == 0 ? $alternateCellAttr : $cellAttr);
			$buf .= "<tr>\n";
			if (!is_array($entry)) {
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

	/**
	 * Surrounds a given text with an HTML tag with color specification
	 *
	 * Examples:
	 * <code>
	 * HtmlUtils::colorize($myText, 'red', 'span');
	 * HtmlUtils::colorize($myText, '#0000ff', 'div');
	 * </code>
	 *
	 * @param string $text Text
	 * @param string $color Color specification (name or RGB string)
	 * @param string $tagName Tag name
	 * @return string
	 * @static
	 */
	function colorize($text, $color, $tagName='span') {
		if (!empty($text) || strlen($text) > 0)
			return sprintf("<%s style=\"color:%s\">%s</%s>", $tagName, $color, $text, $tagName);
		return '';
	}

	/**
	 * Render a sequence of no-break space chars
	 *
	 * @param int $n Amount of chars
	 * @return string
	 * @static
	 */
	function noBreakSpace($n=1) {
		return str_repeat('&nbsp;', $n);
	}

	/**
	 * Surrounds a text with a given tag N times
	 *
	 * Example:
	 * <code>
	 * // prints <big><big>Hello World!</big></big>
	 * print HtmlUtils::tagRepeat('big', 'Hello World!', 2);
	 * </code>
	 *
	 * @param string $tag Tag name
	 * @param string $content Tag contents
	 * @param int $n How many times $tag must be rendered
	 * @return string
	 * @static
	 */
	function tagRepeat($tag, $content, $n=1) {
		$n = max(1, $n);
		$tag = strtolower($tag);
		return str_repeat("<{$tag}>", $n) . $content . str_repeat("</{$tag}>", $n);
	}

	/**
	 * Transforms new lines into <br /> tags
	 *
	 * @param string $str Input text
	 * @return string Processed text
	 * @static
	 */
	function newLineToBr($str) {
		return str_replace("\n", "<br />\n", $str);
	}

	/**
	 * Transforms URLs found in a given text into links
	 *
	 * Parses the following protocols: http, https, ftp, mailto and news.
	 *
	 * @param string $str Input text
	 * @return string Processed text
	 * @static
	 */
	function parseLinks($str) {
		function buildAnchor($matches) {
	        $href = $matches[1] . $matches[2];
			return HtmlUtils::anchor($href, $href, $href, '', array(), '_blank') . $matches[3];
		}
        return preg_replace_callback('=(http://|https://|ftp://|mailto:|news:)(\S+)(\*\s|\=\s|&quot;|&lt;|&gt;|<|>|\(|\)|\s|$)=Usmix', 'buildAnchor', $str);
	}

	/**
	 * Builds the HTML code of an embedded flash movie
	 *
	 * @param string $src SWF path
	 * @param int $wid Movie width
	 * @param int $hei Movie height
	 * @param array $vars Movie variables
	 * @param bool $transparent Transparent flag
	 * @return string Generated HTML code
	 * @static
	 */
	function flashMovie($src, $wid=0, $hei=0, $vars=array(), $transparent=FALSE) {
		$id = PHP2Go::generateUniqueId('flashmovie');
		$src = htmlentities($src);
		$tmp = array();
		if (is_array($vars) && !empty($vars)) {
			foreach ($vars as $name=>$value)
				$tmp[] = "{$name}={$value}";
			$vars = join('&', $tmp);
			$src .= "?{$vars}";
		} else {
			$vars = '';
		}
		return sprintf (
			"<object id=\"%s\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\"%s%s align=\"top\">\n" .
			"  <param name=\"movie\" value=\"%s\"/>\n" .
			"  <param name=\"quality\" value=\"high\"/>\n%s%s" .
			"  <embed id=\"%s\" src=\"%s\" quality=\"high\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\"%s%s%s%s align=\"top\" scale=\"exactfit\"></embed>\n" .
			"</object>\n",
			$id, ($wid > 0 ? " width=\"{$wid}\"" : ""), ($hei > 0 ? " height=\"{$hei}\"" : ""),
			$src, ($vars ? "  <param name=\"flashvars\" value=\"{$vars}\">\n" : ''),
			($transparent ? "  <param name=\"bgcolor\" value=\"#ffffff\"/>\n  <param name=\"wmode\" value=\"transparent\"/>\n" : ''),
			$id, $src, ($vars ? " flashvars=\"{$vars}\"" : ''), ($transparent ? " wmode=\"transparent\"" : ""),
			($wid > 0 ? " width=\"{$wid}\"" : ''), ($hei > 0 ? " height=\"{$hei}\"" : '')
		);
	}

	/**
	 * Builds the HTML code of an embedded RealPlayer movie
	 *
	 * Supported flags: CLIP_INFO, CLIP_STATUS, CONTROLS,
	 * AUTO_START and LOOP. Flag names are case-sensitive.
	 *
	 * @param string $src Movie path
	 * @param int $wid Movie width
	 * @param int $hei Movie height
	 * @param array $flags Flags
	 * @return string Generated HTML code
	 * @static
	 */
	function realPlayerMovie($src, $wid=0, $hei=0, $flags) {
		$srcVals = split("[/\\.]", strtolower($src));
		$extension = $srcVals[sizeof($srcVals)-1];
		if ($extension == 'ram' || $extension == 'ra' || $extension == 'rm' || $extension == 'rpm' || $extension == 'smil') {
			$src = htmlentities($src);
			$flags = array_merge(array(
				'CONTROLS' => TRUE,
				'CLIP_STATUS' => FALSE,
				'CLIP_INFO' => FALSE,
				'AUTO_START' => FALSE,
				'LOOP' => FALSE
			), (array)$flags);
			$movieCode = sprintf("<embed name=\"realVideo\" src=\"%s\" type=\"audio/x-pn-realaudio\" pluginspage=\"http://www.real.com/player\"
										%s%shspace=\"0\" vspace=\"0\" border=\"0\" nojava=\"True\" controls=\"ImageWindow\" console=\"_master\" autostart=\"%d\" loop=\"%s\">",
				$src, ($wid > 0 ? " width=\"" . $wid . "\"" : ""), ($hei > 0 ? " height=\"" . $hei . "\"" : ""), ($flags['AUTO_START']) ? 1 : 0, ($flags['LOOP'] ? "TRUE" : "FALSE"));
			if ($flags['CONTROLS']) {
				$movieCode .= sprintf("<br /><embed name=\"realVideo\" src=\"%s\" type=\"audio/x-pn-realaudio\" pluginspage=\"http://www.real.com/player\"
											  %s%shspace=\"0\" vspace=\"0\" border=\"0\" nojava=\"True\" controls=\"ControlPanel\" console=\"rVideo\" autostart=\"%d\" loop=\"%s\">",
					$src, ($wid > 0 ? " width=\"" . $wid . "\"" : ""), " height=\"35\"", ($flags['AUTO_START']) ? 1 : 0, ($flags['LOOP'] ? "TRUE" : "FALSE"));
			}
			if ($flags['CLIP_STATUS']) {
				$movieCode .= sprintf("<br /><embed name=\"realVideo\" src=\"%s\" type=\"audio/x-pn-realaudio\" pluginspage=\"http://www.real.com/player\"
											  %s%shspace=\"0\" vspace=\"0\" border=\"0\" nojava=\"True\" controls=\"StatusBar\" console=\"rVideo\">",
					$src, ($wid > 0 ? " width=\"" . $wid . "\"" : ""), " height=\"30\"");
			}
			if ($flags['CLIP_INFO']) {
				$movieCode .= sprintf("<br /><embed name=\"realVideo\" src=\"%s\" type=\"audio/x-pn-realaudio\" pluginspage=\"http://www.real.com/player\"
											  %s%shspace=\"0\" vspace=\"0\" border=\"0\" nojava=\"True\" controls=\"TACCtrl\" console=\"rVideo\">",
					$src, ($wid > 0 ? " width=\"" . $wid . "\"" : ""), " height=\"32\"");
			}
			return $movieCode;
		}
		return "";
	}

	/**
	 * Builds the HTML code of an embedded Windows Media Player movie
	 *
	 * The supported flags are: CLIP_INFO, CLIP_STATUS, CONTROLS,
	 * AUTO_SIZE and AUTO_START.
	 *
	 * @param string $src Movie path
	 * @param int $wid Movie width
	 * @param int $hei Movie height
	 * @param array $flags Flags
	 * @return string Generated HTML code
	 * @static
	 */
	function mediaPlayerMovie($src, $wid=0, $hei=0, $flags=array()) {
		$srcVals = split("[/\\.]", strtolower($src));
		$extension = $srcVals[sizeof($srcVals)-1];
		if ($extension == 'asf' || $extension == 'asx' || $extension == 'wmv' || $extension == 'wma') {
			$src = htmlentities($src);
			$id = PHP2Go::generateUniqueId('mplayermovie');
			$flags = array_merge(array(
				'CLIP_INFO' => FALSE,
				'CLIP_STATUS' => FALSE,
				'CONTROLS' => TRUE,
				'AUTO_START' => FALSE,
				'AUTO_SIZE' => FALSE
			), (array)$flags);
			return sprintf (
				"<object id=\"%s\" classid=\"CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95\" codebase=\"http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715\" standby=\"Loading Microsoft® Windows® Media Player components...\" type=\"application/x-oleobject\"%s%s>\n" .
				"  <param name=\"FileName\" value=\"%s\">\n" .
				"  <param name=\"ShowDisplay\" value=\"%s\">\n" .
				"  <param name=\"ShowStatusBar\" value=\"%s\">" .
				"  <param name=\"StatusBar\" value=\"True\">\n" .
				"  <param name=\"AnimationAtStart\" value=\"True\">\n" .
				"  <param name=\"ShowAudioControls\" value=\"%s\">\n" .
				"  <param name=\"ShowPositionControls\" value=\"%s\">\n" .
				"  <param name=\"ShowControls\" value=\"%s\">\n" .
				"  <param name=\"AutoSize\" value=\"%s\">\n" .
				"  <param name=\"AutoStart\" value=\"%d\">\n" .
				"  <param name=\"AutoRewind\" value=\"True\">\n" .
				"  <embed%s%s filename=\"%s\" src=\"%s\" pluginspage=\"http://www.microsoft.com/Windows/MediaPlayer/\" name=\"MPlay1\" type=\"video/x-mplayer2\" showdisplay=\"%s\" showstatusbar=\"%s\" statusbar=\"true\" autorewind=\"1\" animationatstart=\"true\" showaudiocontrols=\"%s\" showpositioncontrols=\"%s\" showcontrols=\"%s\" autosize=\"%s\" autostart=\"%d\"></embed>\n" .
				"</object>\n",
				$id, ($wid > 0) ? " width=\"" . $wid . "\"" : "",
				($hei > 0) ? " height=\"" . $hei . "\"" : "", $src,
				($flags['CLIP_INFO'] ? "TRUE" : "FALSE"),
				($flags['CLIP_STATUS'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['AUTO_SIZE'] ? "TRUE" : "FALSE"),
				($flags['AUTO_START'] ? 1 : 0),
				($wid > 0) ? " width=\"" . $wid . "\"" : "",
				($hei > 0) ? " height=\"" . $hei . "\"" : "", $src, $src,
				($flags['CLIP_INFO'] ? "1" : "0"),
				($flags['CLIP_STATUS'] ? "1" : "0"),
				($flags['CONTROLS'] ? "1" : "0"),
				($flags['CONTROLS'] ? "1" : "0"),
				($flags['CONTROLS'] ? "1" : "0"),
				($flags['AUTO_SIZE'] ? "1" : "0"),
				($flags['AUTO_START'] ? 1 : 0)
			);
		}
		return '';
	}

	/**
	 * Builds the HTML code of an embedded QuickTime movie
	 *
	 * The supported flags are: CACHE, CONTROLS, LOOP,
	 * AUTO_START and AUTO_SIZE.
	 *
	 * @param string $src Movie path
	 * @param int $wid Movie width
	 * @param int $hei Movie height
	 * @param array $flags Flags
	 * @return string Generated HTML code
	 * @static
	 */
	function quickTimeMovie($src, $wid=0, $hei=0, $flags=array()) {
		$srcVals = split("[/\\.]", strtolower($src));
		$extension = $srcVals[sizeof($srcVals)-1];
		if ($extension == 'mov' || $extension == 'qt') {
			$id = PHP2Go::generateUniqueId('quicktimemovie');
			$src = htmlentities($src);
			$flags = array_merge(array(
				'CACHE' => FALSE,
				'CONTROLS' => TRUE,
				'LOOP' => FALSE,
				'AUTO_START' => FALSE,
				'AUTO_SIZE' => FALSE
			), (array)$flags);
			return sprintf(
				"<embed id=\"%s\" name=\"Quick Time Video\" src=\"%s\" type=\"video/quicktime\" pluginspage=\"http://www.apple.com/quicktime/download/indext.html\"" .
				"%s%s autostart=\"%s\" kioskmode=\"TRUE\" cache=\"%s\" controller=\"%s\" loop=\"%s\" moviename=\"quickTime\" scale=\"%s\"></embed>",
				$id, $src, ($wid > 0 ? " width=\"" . $wid . "\"" : ""),
				($hei > 0 ? " height=\"" . $hei . "\"" : ""),
				($flags['AUTO_START'] ? "TRUE" : "FALSE"),
				($flags['CACHE'] ? "TRUE" : "FALSE"),
				($flags['CONTROLS'] ? "TRUE" : "FALSE"),
				($flags['LOOP'] ? "TRUE" : "FALSE"),
				($flags['AUTO_SIZE'] ? "1" : "TOFIT")
			);
		}
		return '';
	}

	/**
	 * Renders code that shows a message in the browser's status bar
	 *
	 * @param string $str Message
	 * @param bool $return Whether the generated code should be returned or printed
	 * @return mixed
	 * @static
	 */
	function statusBar($str, $return=TRUE) {
		$mText = '';
		if (!empty($str))
			$mText = "title=\"$str\" onmouseover=\"window.status='$str';return true;\" onmouseout=\"window.status='';return true;\"";
		if ($return) {
			return $mText;
		} else {
			print $mText;
			return TRUE;
		}
	}

	/**
	 * Builds the HTML code of a DHTML tooltip
	 *
	 * The tooltip is based on the overlib library. The first
	 * argument must be a valid instance of {@link Document} class.
	 *
	 * @link http://www.bosrup.com/web/overlib
	 * @param Document &$_Document HTML document that will contain the tooltip
	 * @param string $caption Tooltip message
	 * @param string $argumentList Overlib arguments
	 * @return string
	 * @static
	 */
	function overPopup(&$_Document, $caption, $argumentList='') {
		static $divInserted;
		if (!isset($divInserted)) {
			$_Document->appendBodyContent("<div id=\"overDiv\" style=\"position:absolute;visibility:hidden;z-index:1000;\"></div>");
			$divInserted = TRUE;
		}
		$_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/overlib/overlib.js");
		return "onmouseover='return overlib(\"" . $caption . "\"" . ($argumentList != '' ? ',' . $argumentList : '') . ");' onmouseout='return nd();'";
	}

	/**
	 * Prints a Javascript alert command
	 *
	 * @param string $msg Alert message
	 * @static
	 */
	function alert($msg) {
		echo "<script language=\"Javascript\" type=\"text/javascript\">alert(\"", $msg, "\");</script>";
	}

	/**
	 * Prints a Javascript confirm command
	 *
	 * @param string $msg Dialog message
	 * @param string $trueAction Ok action
	 * @param string $falseAction Cancel action
	 * @static
	 */
	function confirm($msg, $trueAction='', $falseAction='') {
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

	/**
	 * Prints JS code that redirects to another URL
	 *
	 * @param string $url Target URL
	 * @param string $object Source document
	 * @static
	 */
	function redirect($url, $object="document") {
		if ($object[strlen($object)-1] != '.')
			$object .= ".";
		echo "<script language=\"Javascript\" type=\"text/javascript\">", $object, "location.href = \"", $url, "\"</script>\n";
		exit;
	}

	/**
	 * Prints JS code that replaces the current URL by another
	 *
	 * @param string $url Target URL
	 * @static
	 */
	function replace($url) {
		echo "<script language=\"Javascript\" type=\"text/javascript\">location.replace(\"", $url, "\");</script>\n";
		exit;
	}

	/**
	 * Prints a META refresh tag
	 *
	 * @param string $url Target URL
	 * @param int $time Seconds to wait before redirection
	 * @static
	 */
	function refresh($url, $time=1) {
		echo "<meta http-equiv=\"refresh\" content=\"", $time, "; url=", htmlentities($url), "\" />";
	}

	/**
	 * Prints JS code to go back N positions based on browser history
	 *
	 * @param int $n Number of positions
	 * @static
	 */
	function goBackN($n=1) {
		$n = ($n > 0) ? strval($n) : "1";
		echo "<script language=\"Javascript\" type=\"text/javascript\">history.go(-", $n, ")</script>\n";
		exit;
	}

	/**
	 * Prints JS code to close the current browser window
	 *
	 * @static
	 */
	function closeWindow() {
		echo "<script language=\"Javascript\" type=\"text/javascript\">if (parent) parent.close(); else window.close();</script>\n";
		exit;
	}

	/**
	 * Builds JS code that requests focus to a given form or form input
	 *
	 * @param string $form Form name
	 * @param string $field Field name
	 * @param string $object Base document
	 * @param bool $return Whether the code should be returned or printed
	 * @return mixed
	 * @static
	 */
	function focus($form, $field, $object='', $return=FALSE) {
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