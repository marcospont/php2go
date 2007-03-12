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
 * The list below represents the variable modifiers bundled with the framework.
 * In order to add your custom modifiers, you can register them in the global
 * configuration settings or directly calling addModifier() on a Template instance.
 *
 * @package template
 */
$P2G_MODIFIER = array();
$P2G_MODIFIER['substring'] = 'substr';
$P2G_MODIFIER['upper'] = 'strtoupper';
$P2G_MODIFIER['lower'] = 'strtolower';
$P2G_MODIFIER['reverse'] = 'strrev';
$P2G_MODIFIER['pad'] = 'str_pad';
$P2G_MODIFIER['trim'] = 'trim';
$P2G_MODIFIER['nl2br'] = 'nl2br';
$P2G_MODIFIER['number_format'] = 'number_format';
$P2G_MODIFIER['round'] = 'round';
$P2G_MODIFIER['html_chars'] = 'htmlspecialchars';
$P2G_MODIFIER['html_entities'] = 'htmlentities';
$P2G_MODIFIER['add_slashes'] = 'addslashes';
$P2G_MODIFIER['strip_slashes'] = 'stripslashes';
$P2G_MODIFIER['strip_tags'] = 'strip_tags';
$P2G_MODIFIER['highlight_php'] = 'highlightPHP';
$P2G_MODIFIER['host'] = 'gethostbyaddr';
$P2G_MODIFIER['dump'] = 'dumpVariable';
$P2G_MODIFIER['gettext'] = 'gettext';
$P2G_MODIFIER['_'] = '_';
$P2G_MODIFIER['i18n'] = '__';
$P2G_MODIFIER['file_basename'] = 'basename';
$P2G_MODIFIER['file_size'] = 'filesize';
$P2G_MODIFIER['real_path'] = 'realpath';
$P2G_MODIFIER['last_modified'] = array('FileSystem', 'lastModified');
$P2G_MODIFIER['if_empty'] = array('StringUtils', 'ifEmpty');
$P2G_MODIFIER['strip_blank'] = array('StringUtils', 'stripBlank');
$P2G_MODIFIER['all_trim'] = array('StringUtils', 'allTrim');
$P2G_MODIFIER['concat'] = array('StringUtils', 'concat');
$P2G_MODIFIER['surround'] = array('StringUtils', 'surround');
$P2G_MODIFIER['replace'] = array('StringUtils', 'replace');
$P2G_MODIFIER['regex_replace'] = array('StringUtils', 'regexReplace');
$P2G_MODIFIER['explode'] = array('StringUtils', 'explode');
$P2G_MODIFIER['implode'] = array('StringUtils', 'implode');
$P2G_MODIFIER['map'] = array('StringUtils', 'map');
$P2G_MODIFIER['filter'] = array('StringUtils', 'filter');
$P2G_MODIFIER['escape'] = array('StringUtils', 'escape');
$P2G_MODIFIER['camelize'] = array('StringUtils', 'camelize');
$P2G_MODIFIER['capitalize'] = array('StringUtils', 'capitalize');
$P2G_MODIFIER['normalize'] = array('StringUtils', 'normalize');
$P2G_MODIFIER['indent'] = array('StringUtils', 'indent');
$P2G_MODIFIER['truncate'] = array('StringUtils', 'truncate');
$P2G_MODIFIER['insert_char'] = array('StringUtils', 'insertChar');
$P2G_MODIFIER['line_numbers'] = array('StringUtils', 'addLineNumbers');
$P2G_MODIFIER['count_chars'] = array('StringUtils', 'countChars');
$P2G_MODIFIER['count_words'] = array('StringUtils', 'countWords');
$P2G_MODIFIER['count_sentences'] = array('StringUtils', 'countSentences');
$P2G_MODIFIER['count_paragraphs'] = array('StringUtils', 'countParagraphs');
$P2G_MODIFIER['wrap'] = array('StringUtils', 'wrap');
$P2G_MODIFIER['colorize'] = array('HtmlUtils', 'colorize');
$P2G_MODIFIER['anchor'] = array('HtmlUtils', 'anchor');
$P2G_MODIFIER['mailto'] = array('HtmlUtils', 'mailtoAnchor');
$P2G_MODIFIER['image'] = array('HtmlUtils', 'image');
$P2G_MODIFIER['button'] = array('HtmlUtils', 'button');
$P2G_MODIFIER['window'] = array('HtmlUtils', 'window');
$P2G_MODIFIER['status_bar'] = array('HtmlUtils', 'statusBar');
$P2G_MODIFIER['scroll_area'] = array('HtmlUtils', 'scrollableArea');
$P2G_MODIFIER['item_list'] = array('HtmlUtils', 'itemList');
$P2G_MODIFIER['definition_list'] = array('HtmlUtils', 'definitionList');
$P2G_MODIFIER['table'] = array('HtmlUtils', 'table');
$P2G_MODIFIER['parse_links'] = array('HtmlUtils', 'parseLinks');
$P2G_MODIFIER['json'] = array('JSONEncoder', 'encode');
$P2G_MODIFIER['format_time'] = array('Date', 'formatTime');
$P2G_MODIFIER['date_euro_sql'] = array('Date', 'fromEuroToSqlDate');
$P2G_MODIFIER['date_euro_us'] = array('Date', 'fromEuroToUsDate');
$P2G_MODIFIER['date_us_euro'] = array('Date', 'fromUsToEuroDate');
$P2G_MODIFIER['date_us_sql'] = array('Date', 'fromUsToSqlDate');
$P2G_MODIFIER['date_sql_euro'] = array('Date', 'fromSqlToEuroDate');
$P2G_MODIFIER['date_sql_us'] = array('Date', 'fromSqlToUsDate');
$P2G_MODIFIER['decimal_currency'] = array('Number', 'fromDecimalToCurrency');
$P2G_MODIFIER['decimal_fraction'] = array('Number', 'fromDecimalToFraction');
$P2G_MODIFIER['arabic_roman'] = array('Number', 'fromArabicToRoman');
$P2G_MODIFIER['roman_arabic'] = array('Number', 'fromRomanToArabic');
$P2G_MODIFIER['byte_amount'] = array('Number', 'formatByteAmount');
return $P2G_MODIFIER;

?>