<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.datetime.Date');
import('php2go.datetime.TimeCounter');
import('php2go.net.HttpRequest');
import('php2go.net.HttpResponse');
import('php2go.template.DocumentElement');

/**
 * Indicates scripts positioned inside the document's HEAD
 */
define('SCRIPT_START', 1);
/**
 * Indicates scripts positioned in the end of document's BODY
 */
define('SCRIPT_END', 2);
/**
 * Content positioned in the start of document's BODY
 */
define('BODY_START', 1);
/**
 * Content positioned in the end of document's BODY
 */
define('BODY_END', 2);

/**
 * HTML documents builder
 *
 * The Document class is the main gateway to produce HTML output in PHP2Go.
 * Based on a master layout template provided by the developer, this class
 * builds an HTML document, putting the layout contents inside a BODY tag.
 *
 * Each variable declared in the layout template is considered a "document
 * element". A document element is a page slot whose contents should be defined
 * by the developer.
 *
 * Normally, a page will contain a main element, which represents the main content
 * area. Other elements can be created to encapsulate and reuse interface elements
 * or areas that repeat over multiple pages, like a header, a navigation menu and
 * a footer.
 *
 * Document class also controls items of the document head (external scripts, inline
 * scripts, alternate links, external CSS files, inline CSS code) and properties of
 * the document body (Javascript events, inline attributes, inline scripts).
 *
 * The main member of the bundled Javascript framework (javascript/php2go.js) is
 * included by default in all pages built with this class. This gives you access
 * to a wide set of features, like DOM functions, event handling functions, logging
 * helper and much more.
 *
 * <code>
 * /* my_templates/my_layout_template.tpl {@*}
 * <div style="width:800px">
 * This is my website!
 * {$main}
 * </div>
 * /* my_templates/my_home_template.tpl {@*}
 * <div>{$message}</div>
 * /* my_page.php {@*}
 * $doc = new Document('my_templates/my_layout_template.tpl');
 * $doc->setTitle('My Home Page');
 * $doc->addBodyCfg(array('style'=>'background-color:#fff'));
 * /* enable browser cache and gzip compression {@*}
 * $doc->setCache(true);
 * $doc->setCompression(true, 9);
 * /* add JS and CSS files {@*}
 * $doc->addScript('my_js_files/functions.js');
 * $doc->addStyle('my_css_files/site.css');
 * /* populate the main page element {@*}
 * $main = new Template('my_template/my_home_template.tpl');
 * $main->parse();
 * $main->assign('message', 'Hello World!');
 * $doc->assignByRef('main', $main);
 * /* build and display the final output {@*}
 * $doc->display();
 * </code>
 *
 * @package base
 * @uses Db
 * @uses DocumentElement
 * @uses HttpResponse
 * @uses System
 * @uses TimeCounter
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Document extends PHP2Go
{
	/**
	 * Document title
	 *
	 * Defaults to the configuration setting TITLE
	 *
	 * @var string
	 */
	var $docTitle;

	/**
	 * Document charset
	 *
	 * Defaults to the configuration setting CHARSET
	 *
	 * @var string
	 */
	var $docCharset;

	/**
	 * Document language code
	 *
	 * Defaults to the active language code.
	 *
	 * @var string
	 */
	var $docLanguage;

	/**
	 * Set of "name" meta tags
	 *
	 * @var array
	 */
	var $metaTagsName = array();

	/**
	 * Set of "http-equiv" meta tags
	 *
	 * @var array
	 */
	var $metaTagsHttp = array();

	/**
	 * Set of external script files
	 *
	 * @var array
	 */
	var $scriptFiles = array();

	/**
	 * Set of inline scripts
	 *
	 * @var array
	 */
	var $scriptExtCode = array();

	/**
	 * Sequence of script actions to perform when document loads
	 *
	 * @var array
	 */
	var $onLoadCode = array();

	/**
	 * Set of stylesheet files
	 *
	 * @var array
	 */
	var $styles = array();

	/**
	 * Set of imported stylesheet files
	 *
	 * This property is populated by {@link importStyle}.
	 *
	 * @var array
	 */
	var $importedStyles = array();

	/**
	 * Set of inline style definitions
	 *
	 * @var string
	 */
	var $styleExtCode = '';

	/**
	 * Set of alternate links for this document
	 *
	 * @var array
	 */
	var $alternateLinks = array();

	/**
	 * Extra HTML content to be included inside the head tag
	 *
	 * @var string
	 */
	var $extraHeaderCode = '';

	/**
	 * Set of script event handlers for document's body
	 *
	 * @var array
	 */
	var $bodyEvents = array();

	/**
	 * Set of attributes for document's body
	 *
	 * @var array
	 */
	var $bodyCfg = array();

	/**
	 * Extra HTML content that must be rendered in the top or in
	 * the bottom of the HTML document
	 *
	 * @var array
	 */
	var $extraBodyContent = array();

	/**
	 * Whether to allow crawlers and robots
	 *
	 * @var bool
	 */
	var $allowRobots = TRUE;

	/**
	 * Whether to enable browser cache
	 *
	 * @var bool
	 */
	var $makeCache = FALSE;

	/**
	 * Whether to enable gzip compression for this page
	 *
	 * @var bool
	 */
	var $makeCompression = FALSE;

	/**
	 * Compression level
	 *
	 * @var int
	 */
	var $compressionLevel;

	/**
	 * Set of document elements detected in the layout template
	 *
	 * @var array
	 */
	var $elements;

	/**
	 * {@link Template} instance used to manage the master layout template
	 *
	 * @var object Template
	 */
	var $Template;

	/**
	 * Used to measure time spent to create and display the HTML document
	 *
	 * @var object TimeCounter
	 * @access private
	 */
	var $TimeCounter;

	/**
	 * Class constructor
	 *
	 * Initializes the layout template using the provided $docLayout. The
	 * array of $docIncludes could contain a set of include files for the
	 * layout template.
	 *
	 * @param string $docLayout Layout template file name
	 * @param array $docIncludes Set of template includes
	 * @return Document
	 */
	function Document($docLayout, $docIncludes=array()) {
		parent::PHP2Go();
		$this->docCharset = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->docLanguage = PHP2Go::getConfigVal('LOCALE', FALSE);
		$this->docTitle = PHP2Go::getConfigVal('TITLE', FALSE);
		$this->Template = new Template($docLayout);
		if (!empty($docIncludes) && TypeUtils::isHashArray($docIncludes)) {
			foreach ($docIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
		$this->TimeCounter = new TimeCounter();
		$this->_initialize();
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
	function __destruct() {
		unset($this);
	}

	/**
	 * Get the document's title
	 *
	 * @return string
	 */
	function getTitle() {
		return $this->docTitle;
	}

	/**
	 * Set the document's title
	 *
	 * @param string $title New title
	 * @param bool $ignoreSpaces Whether to remove trailing whitespaces
	 */
	function setTitle($title, $ignoreSpaces=FALSE) {
		if ($ignoreSpaces)
			$this->docTitle = $title;
		else
			$this->docTitle = trim($title);
	}

	/**
	 * Set the document's title based on a given SQL query
	 *
	 * The first cell of the first row of the SQL results
	 * will be used as the document's title.
	 *
	 * @param string $sql SQL query that defines the document title
	 * @param string $connectionId DB connection ID
	 */
	function setTitleFromDb($sql, $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		$dbTitle = $Db->getFirstCell($sql);
		if ($dbTitle)
			$this->docTitle = $dbTitle;
	}

	/**
	 * Append a given string in the document's title
	 *
	 * @param string $aTitle Value to be appended
	 * @param bool $useSeparator Whether to use a separator between existent title and appended value
	 * @param string $separator Separator to be used
	 */
	function appendTitle($aTitle, $useSeparator=TRUE, $separator='-') {
		if ($this->docTitle == "") {
			$this->setTitle($aTitle);
		} else {
			if ($useSeparator)
				$this->docTitle .= ' ' . $separator;
			$this->docTitle .= ' ' . ltrim($aTitle);
		}
	}

	/**
	 * Append some value in the document's title based on an SQL query
	 *
	 * The first cell of the first row of the SQL results will be
	 * appended in the document's title.
	 *
	 * @param string $sql SQL query
	 * @param bool $useSeparator Whether to use separator between existent title and appended value
	 * @param string $separator Separator to be used
	 * @param string $connectionId DB connection ID
	 */
	function appendTitleFromDb($sql, $useSeparator=TRUE, $separator='-', $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		$dbTitle = $Db->getFirstCell($sql);
		if ($dbTitle)
			$this->appendTitle($dbTitle, $useSeparator, $separator);
	}

	/**
	 * Get the document's charset
	 *
	 * @return string
	 */
	function getCharset() {
		return $this->docCharset;
	}

	/**
	 * Set the document's charset
	 *
	 * @param string $charset New charset code
	 */
	function setCharset($charset) {
		$this->docCharset = $charset;
	}

	/**
	 * Set the document's language code
	 *
	 * @param string $lang New language code
	 */
	function setLanguage($lang) {
		$this->docLanguage = $lang;
	}

	/**
	 * Enable or disable browser cache for this document
	 *
	 * Browser cache is not enabled by the default inside the class. Although,
	 * it could be useful for pages that need more performance and thus need
	 * to rely on caching tecniques.
	 *
	 * @param bool $flag Enable/disable
	 */
	function setCache($flag=TRUE) {
		$this->makeCache = TypeUtils::toBoolean($flag);
	}

	/**
	 * Enable/disable gzip compression for this document
	 *
	 * GZIP compression isn't enabled by default. However, this is one of
	 * the good practices when dealing with pages that produce large HTML
	 * payloads.
	 *
	 * @param bool $flag Enable/disable
	 * @param int $level Compression level
	 */
	function setCompression($flag=TRUE, $level=9) {
		$this->makeCompression = TypeUtils::toBoolean($flag);
		if ($this->makeCompression)
			$this->compressionLevel = ($level >= 1 ? min($level, 9) : 9);
	}

	/**
	 * Set the form (or form+field) that should receive focus
	 * right after the document is loaded
	 *
	 * If $formField is missing, the first available field of
	 * $formName will get the focus.
	 *
	 * @param string $formName Form ID or name
	 * @param string $formField Field name
	 */
	function setFocus($formName, $formField=NULL) {
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . 'form.js');
		if (empty($formField))
			$this->addOnloadCode(sprintf("Form.focusFirstField('%s');", $formName));
		else
			$this->addOnloadCode(sprintf("if (__fld = \$FF('%s', '%s')) { __fld.focus(); }", $formName, $formField));
	}

	/**
	 * Adds a script file in the document head
	 *
	 * @param string $scriptFile Relative or absolute path to the script
	 * @param string $language Script language
	 * @param string $charset Script charset
	 * @see addScriptCode
	 */
	function addScript($scriptFile, $language="Javascript", $charset=NULL) {
		$scriptFile = htmlentities($scriptFile);
		if (!array_key_exists($scriptFile, $this->scriptFiles)) {
			$this->scriptFiles[$scriptFile] = sprintf("<script language=\"%s\" src=\"%s\" type=\"text/%s\"%s></script>\n",
				$language, $scriptFile, strtolower(preg_replace("/[^a-zA-Z]/", "", $language)), (!empty($charset) ? " charset=\"{$charset}\"" : '')
			);
		}
	}

	/**
	 * Adds a block of script code in the document
	 *
	 * The $position argument determines if the script will be added
	 * in the end of the document's head ({@link SCRIPT_START}) or in
	 * the end of the document's body ({@link SCRIPT_END}).
	 *
	 * @param string $scriptCode Block of script code
	 * @param string $language Script language
	 * @param int $position Insert position
	 * @see addScript
	 */
	function addScriptCode($scriptCode, $language="Javascript", $position=SCRIPT_START) {
		if ($position != SCRIPT_START && $position != SCRIPT_END)
			$position = SCRIPT_START;
		$this->scriptExtCode[$position][$language] = isset($this->scriptExtCode[$position][$language]) ? $this->scriptExtCode[$position][$language] . $scriptCode . "\n" : $scriptCode . "\n";
	}

	/**
	 * Register a script instruction that must be
	 * executed when document is loaded
	 *
	 * @param string $instruction Script instruction
	 */
	function addOnloadCode($instruction) {
		$instruction = ltrim(preg_replace("/\s{1,}/", ' ', $instruction));
		$this->onLoadCode[] = $instruction;
	}

	/**
	 * Add a stylesheet file in the document's head
	 *
	 * @param string $styleFile Relative or absolute path to the stylesheet file
	 * @param string $media Media type
	 * @param string $charset Charset of the stylesheet file
	 * @see importStyle
	 * @see addStyleCode
	 */
	function addStyle($styleFile, $media=NULL, $charset=NULL) {
		$styleFile = htmlentities($styleFile);
		if (!array_key_exists($styleFile, $this->styles))
			$this->styles[$styleFile] = sprintf("<link rel=\"stylesheet\" type=\"text/css\" href=\"%s\"%s%s>\n",
				$styleFile, (!empty($media) ? " media=\"{$media}\"" : ''), (!empty($charset) ? " charset=\"{$charset}\"" : '')
			);
	}

	/**
	 * Import a stylesheet file onto the document
	 *
	 * In contrast with {@link addStyle}, which builds a link element, this method
	 * builds a style element containing an @import(styleUrl) statement.
	 *
	 * @param string $styleUrl Relative or absolute path to the stylesheet file
	 * @see addStyle
	 * @see addStyleCode
	 */
	function importStyle($styleUrl) {
		$styleUrl = htmlentities($styleUrl);
		if (!in_array($styleUrl, $this->importedStyles)) {
			$this->importedStyles[] = $styleUrl;
			$this->styleExtCode .= sprintf("@import url(%s);\n", trim($styleUrl));
		}
	}

	/**
	 * Add a block of style definitions in the document's head
	 *
	 * @param string $styleCode Block of style definitions
	 * @see addStyle
	 * @see importStyle
	 */
	function addStyleCode($styleCode) {
		$this->styleExtCode .= ltrim($styleCode) . "\n";
	}

	/**
	 * Add an alternate link in the document
	 *
	 * This method can be used to build references to feeds:
	 * <code>
	 * $doc->addAlternateLink('application/rss+xml', 'feeds/latest.xml', 'RSS Feed');
	 * </code>
	 *
	 * @param string $type Link type
	 * @param string $linkUrl Link URL
	 * @param string $linkTitle Link title
	 */
	function addAlternateLink($type, $linkUrl, $linkTitle) {
		$linkUrl = htmlentities($linkUrl);
		if (!array_key_exists($linkUrl, $this->alternateLinks))
			$this->alternateLinks[$linkUrl] = sprintf("<link rel=\"alternate\" type=\"%s\" href=\"%s\"%s>\n", $type, $linkUrl, (!empty($linkTitle) ? " title=\"" . $linkTitle . "\"" : ""));
	}

	/**
	 * Define the shortcut icon of this document
	 *
	 * The shortcut icon is used by browsers in the main address bar
	 * and in the bookmark sections
	 *
	 * @param string $iconUrl Relative or absolute path to the icon
	 */
	function setShortcutIcon($iconUrl) {
		$iconUrl = htmlentities($iconUrl);
		$this->appendHeaderContent("<link rel=\"shortcut icon\" href=\"{$iconUrl}\">");
	}

	/**
	 * Add/replace a meta tag in the document
	 *
	 * @param string $name Meta name
	 * @param string $value Meta value
	 * @param bool $httpEquiv Is this an http-equiv meta tag?
	 */
	function addMetaData($name, $value, $httpEquiv=FALSE) {
		if ($httpEquiv) {
			$this->metaTagsHttp[$name] = $value;
		} else {
			$name = strtoupper($name);
			$this->metaTagsName[$name] = $value;
		}
	}

	/**
	 * Flags the document to prevent robots using a meta tag
	 */
	function preventRobots() {
		$this->allowRobots = FALSE;
	}

	/**
	 * Append extra HTML content in the document's head
	 *
	 * @param string $value Value to append
	 */
	function appendHeaderContent($value) {
		$this->extraHeaderCode .= $value . "\n";
	}

	/**
	 * Set one or more properties of the document's body
	 *
	 * @param string|array $attr Proprety name or hash array of properties
	 * @param string $value Property value
	 */
	function addBodyCfg($attr, $value="") {
		if (TypeUtils::isArray($attr)) {
			foreach($attr as $key => $value)
				$this->bodyCfg[strtoupper($key)] = $value;
		} else {
			$this->bodyCfg[strtoupper($attr)] = $value;
		}
	}

	/**
	 * Add a script event handler in the document
	 *
	 * <code>
	 * $doc->attachBodyEvent('onLoad', "alert('this will execute first!');");
	 * $doc->attachBodyEvent('onLoad', "alert('and then, this!');");
	 * </code>
	 *
	 * @param string $event Event name
	 * @param string $action Associated action
	 * @param bool $pushStart Whether to add the event listener before all existent ones
	 */
	function attachBodyEvent($event, $action, $pushStart=FALSE) {
		$event = strtolower($event);
		$action = str_replace("\"", "'", $action);
		if (substr($action, -1, 1) != ';')
			$action .= ';';
		if (!isset($this->bodyEvents[$event]))
			$this->bodyEvents[$event] = $action;
		else
			$this->bodyEvents[$event] = ($pushStart ? $action . $this->bodyEvents[$event] : $this->bodyEvents[$event] . $action);
	}

	/**
	 * Append extra HTML contents in the document's body
	 *
	 * @param string $content Contents to append
	 * @param int $position Insert position: {@link BODY_START} or {@link BODY_END}
	 */
	function appendBodyContent($content, $position=BODY_END) {
		if ($position != BODY_START && $position != BODY_END)
			$position = BODY_START;
		$this->extraBodyContent[$position] = isset($this->extraBodyContent[$position]) ? $this->extraBodyContent[$position] . $content . "\n" : $content . "\n";
	}

	/**
	 * Create a {@link DocumentElement} given its name, the path
	 * of the template source and the source type
	 *
	 * <code>
	 * $doc = new Document('layout.tpl');
	 * /* bind "header" slot with an external template file {@*}
	 * $header =& $doc->createElement('header', 'header.tpl', T_BYFILE);
	 * /* bind "menu" slot with an external template file and assign a simple collection of data {@*}
	 * $menu =& $doc->createElement('menu', 'menu.tpl', T_BYFILE);
	 * $menu->assign('items', $db->query("select * from menu"));
	 * /* create and populate the main page slot {@*}
	 * $main = new Template('main.tpl');
	 * $main->parse();
	 * $main->assign('message', 'This is the main slot!');
	 * $doc->assignByRef('main', $main);
	 * /* display the document {@*}
	 * $doc->display();
	 * </code>
	 *
	 * @param string $elementName Element name
	 * @param string $elementSrc Element source
	 * @param int $srcType Source type
	 * @return DocumentElement
	 */
	function &createElement($elementName, $elementSrc='', $srcType=T_BYFILE) {
		if (!empty($elementSrc))
			$this->elements[$elementName] =& DocumentElement::factory($elementSrc, $srcType);
		else
			$this->elements[$elementName] =& new DocumentElement();
		return $this->elements[$elementName];
	}

	/**
	 * Set the contents of a given document element
	 *
	 * When assigning objects to document elements under PHP4, always
	 * use {@link assignByRef} instead of {@link assign}.
	 *
	 * @param string $elementName Element name
	 * @param string|Component $elementValue Element contents
	 * @see assignByRef
	 */
	function assign($elementName, $elementValue) {
		$this->elements[$elementName] = $elementValue;
	}

	/**
	 * Set the contents of a given document element by reference
	 *
	 * This method is specially suited to assign renderizable
	 * components to document elements. By using this approach,
	 * significant performance improvements could be achieved.
	 * <code>
	 * $doc = new Document('layout.tpl');
	 * $form = new FormBasic('form.xml', 'form', $doc);
	 * $doc->assignByRef('main', $form);
	 * $doc->display();
	 * </code>
	 *
	 * @param string $elementName Element name
	 * @param Component &$ContentObj Component to fill the slot
	 * @see assign
	 */
	function assignByRef($elementName, &$ContentObj) {
		$this->elements[$elementName] =& $ContentObj;
	}

	/**
	 * Builds and <b>displays</b> the final document output
	 *
	 * Uses {@link ob_start()} when gzip compression is enabled. If
	 * you run into any issues when compression is enabled, maybe
	 * ob_gzhandler doesn't work very well at your environment.
	 */
	function display() {
		$this->_buildBodyContent();
		$this->_preRenderHeader(TRUE);
		$Agent =& UserAgent::getInstance();
		if ($this->makeCompression && !HttpResponse::headersSent() && !connection_aborted() && extension_loaded('zlib') && ($encoding = $Agent->matchAcceptList(array('x-gzip', 'gzip'), 'encoding'))) {
			System::setIni('zlip.output_compression', $this->compressionLevel);
			ob_start('ob_gzhandler');
			$this->_printDocumentHeader();
			$this->_printDocumentBody();
			print "\n" . PHP2Go::getLangVal('COMPRESS_USE_MSG', $encoding) . "\n";
			ob_end_flush();
		} else {
			$this->_printDocumentHeader();
			$this->_printDocumentBody();
		}
	}

	/**
	 * Builds and <b>returns</b> the final document output
	 *
	 * @return string
	 */
	function getContent() {
		$this->_buildBodyContent();
		$this->_preRenderHeader(FALSE);
		ob_start();
		$this->_printDocumentHeader();
		$this->_printDocumentBody();
		return ob_get_clean();
	}

	/**
	 * Generate the HTML output for this document and
	 * save it in a given file name
	 *
	 * @param string $fileName File path
	 * @return bool Whether the file was saved or not
	 */
	function toFile($fileName) {
		$fp = @fopen($fileName, 'wb');
		if ($fp === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_CREATE_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			fputs($fp, $this->getContent());
			fclose($fp);
			return TRUE;
		}
	}

	/**
	 * Initialize some properties of the document
	 *
	 * @access private
	 */
	function _initialize() {
		// initialize meta tags
		$this->metaTagsName['TITLE'] =& $this->docTitle;
		$this->metaTagsName['AUTHOR'] = PHP2Go::getConfigVal('AUTHOR', FALSE);
		$this->metaTagsName['DESCRIPTION'] = PHP2Go::getConfigVal('DESCRIPTION', FALSE);
		$this->metaTagsName['KEYWORDS'] = PHP2Go::getConfigVal('KEYWORDS', FALSE);
		$this->metaTagsName['CATEGORY'] = PHP2Go::getConfigVal('CATEGORY', FALSE);
		$this->metaTagsName['CODE_LANGUAGE'] = 'PHP';
		$this->metaTagsName['GENERATOR'] = 'PHP2Go Web Development Framework ' . PHP2GO_VERSION;
		$this->metaTagsName['DATE_CREATION'] = PHP2Go::getConfigVal('DATE_CREATION', FALSE);
		$this->metaTagsHttp['Content-Language'] = $this->docLanguage;
		// initialize document slots
		$elements = $this->Template->getDefinedVariables();
		$elementCount = sizeof($elements);
		if (!$elementCount) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_DOC_LAYOUT'), E_USER_ERROR, __FILE__, __LINE__);
		}
		for ($i=0; $i<$elementCount; $i++)
			$this->elements[$elements[$i]] = '';
		// add basic JS libraries
		$Conf =& Conf::getInstance();
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . 'php2go.js?locale=' . $Conf->getConfig('LANGUAGE_CODE') . '&charset=' . $Conf->getConfig('CHARSET'));
	}

	/**
	 * Perform configuration routines before rendering the document's head
	 *
	 * @param bool $display Indicates if the page will be displayed or serialized into a file
	 * @access private
	 */
	function _preRenderHeader($display=TRUE) {
		if (!$this->makeCache) {
			if ($display && !headers_sent() && !$this->makeCompression) {
				@header('Expires: Tue, 1 Jan 1980 12:00:00 GMT');
				@header('Last-Modified: ', gmdate('D, d M Y H:i:s') . ' GMT');
				@header('Cache-Control: no-cache');
				@header('Pragma: no-cache');
			}
			$this->metaTagsHttp['Expires'] = 'Tue, 1 Jan 1980 12:00:00 GMT';
			$this->metaTagsHttp['Last-Modified'] = gmdate('D, d M Y H:i:s') . ' GMT';
			$this->metaTagsHttp['Cache-Control'] = 'no-cache';
			$this->metaTagsHttp['Pragma'] = 'no-cache';
		}
		if (!$this->allowRobots)
			$this->metaTagsName['ROBOTS'] = 'NOINDEX,NOFOLLOW,NOARCHIVE';
	}

	/**
	 * Prints the document head: meta tags, scripts, stylesheets, links
	 *
	 * @access private
	 */
	function _printDocumentHeader() {
		print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
		print "<html>\n<head>\n";
		print sprintf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n", $this->docCharset);
		foreach($this->metaTagsHttp as $name => $content) {
			if (!empty($content))
				print sprintf("<meta http-equiv=\"%s\" content=\"%s\">\n", $name, htmlspecialchars($content));
		}
		foreach($this->metaTagsName as $name => $content) {
			if (!empty($content))
				print sprintf("<meta name=\"%s\" content=\"%s\">\n", $name, htmlspecialchars($content));
		}
		print "<title>{$this->docTitle}</title>\n";
		// base URL
		$baseUrl = PHP2Go::getConfigVal('BASE_URL', FALSE);
		if (!empty($baseUrl)) {
			$baseUrl = rtrim($baseUrl, '/') . '/';
			print sprintf("<base href=\"%s\">\n", $baseUrl);
		}
		print join("", array_values($this->styles));
		if (!empty($this->styleExtCode))
			print sprintf("<style type=\"text/css\">\n<!--\n%s//-->\n</style>\n", $this->styleExtCode);
		print join("", array_values($this->alternateLinks));
		print join("", array_values($this->scriptFiles));
		if (!empty($this->onLoadCode)) {
			$onLoad = "\tfunction p2gOnLoad() {\n";
			foreach ($this->onLoadCode as $instruction)
				$onLoad .= "\t\t$instruction\n";
			$onLoad .= "\t}";
			$this->addScriptCode($onLoad, 'Javascript');
			$this->attachBodyEvent('onload', 'p2gOnLoad();', TRUE);
		}
		// inline scripts located in the document's head
		if (isset($this->scriptExtCode[SCRIPT_START])) {
			foreach($this->scriptExtCode[SCRIPT_START] as $language => $scripts) {
				if (substr($scripts, -1) != "\n")
					$scripts .= "\n";
				print sprintf("<script language=\"%s\" type=\"text/%s\">\n<!--\n%s//-->\n</script>\n", $language, strtolower(preg_replace("/[^a-zA-Z]/", "", $language)), $scripts);
			}
		}
		print $this->extraHeaderCode;
		print "</head>\n";
	}

	/**
	 * Prints the document body: body tag with properties and event
	 * handlers, layout template, inline scripts and extra HTML code
	 *
	 * @access private
	 */
	function _printDocumentBody() {
		print "<body";
		foreach($this->bodyCfg as $attr => $value)
			print sprintf(" %s=\"%s\"", strtolower($attr), str_replace('"', '\'', $value));
		foreach($this->bodyEvents as $event => $actions)
			print sprintf(" %s=\"%s\"", $event, $actions);
		print ">\n<a id=\"php2go_top\" name=\"php2go_top\"></a>\n";
		if (!empty($this->extraBodyContent[BODY_START]))
			print $this->extraBodyContent[BODY_START];
		$this->Template->display();
		if (!empty($this->extraBodyContent[BODY_END]))
			print "\n" . $this->extraBodyContent[BODY_END];
		// scripts located in the end of the document's body
		if (isset($this->scriptExtCode[SCRIPT_END])) {
			foreach($this->scriptExtCode[SCRIPT_END] as $language => $scripts) {
				if (substr($scripts, -1) != "\n")
					$scripts .= "\n";
				print sprintf("\n<script language=\"%s\" type=\"text/%s\">\n<!--\n%s//-->\n</script>", $language, strtolower(preg_replace("/[^a-zA-Z]/", "", $language)), $scripts);
			}
		}
		print "\n</body>\n</html>";
		print "\n<!-- This content is powered by PHP2Go v. " . PHP2GO_VERSION . " (http://www.php2go.com.br) -->";
		$this->TimeCounter->stop();
		print sprintf("\n<!-- Timespent : %.3f -->", $this->TimeCounter->getElapsedTime());
	}

	/**
	 * Process all document elements before rendering the page body
	 *
	 * # For template elements, call {@link Template::parse()} if not called yet
	 * # For component elements, call {@link Component::onPreRender()} if not called yet
	 * # Assign object elements by ref and scalar elements by value
	 *
	 * @access private
	 */
	function _buildBodyContent() {
		$elmNames = array_keys($this->elements);
		foreach ($elmNames as $name) {
			$element =& $this->elements[$name];
			if (is_object($element)) {
				if (TypeUtils::isInstanceOf($element, 'Template') && !$element->isPrepared())
					$element->parse();
				if (TypeUtils::isInstanceOf($element, 'Component'))
					$element->onPreRender();
				$this->Template->assignByRef($name, $element);
			} elseif (is_scalar($element) && !is_bool($element)) {
				$this->Template->assign($name, $element);
			}
		}
	}
}
?>
