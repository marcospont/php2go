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

import('php2go.base.DocumentHead');
import('php2go.datetime.TimeCounter');
import('php2go.template.Template');
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
 * @uses TimeCounter
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Document extends PHP2Go
{
	/**
	 * Set of document body's attributes
	 *
	 * @var array
	 */
	var $bodyCfg = array();

	/**
	 * Set of document body's event listeners
	 *
	 * @var array
	 */
	var $bodyEvents = array();

	/**
	 * Set of inline script blocks
	 *
	 * @var array
	 */
	var $scriptBlocks = array();

	/**
	 * Sequence of scripts to run when the document loads
	 *
	 * @var array
	 */
	var $onLoadCode = array();

	/**
	 * Extra HTML content that must be rendered in the top or in
	 * the bottom of the HTML document
	 *
	 * @var array
	 */
	var $extraContent = array();

	/**
	 * Whether to enable browser cache
	 *
	 * @var bool
	 */
	var $cacheEnabled = FALSE;

	/**
	 * Whether to enable gzip compression for this page
	 *
	 * @var bool
	 */
	var $compressionEnabled = FALSE;

	/**
	 * Compression level
	 *
	 * @var int
	 */
	var $compressionLevel;
	
	/**
	 * Whether jQuery library must be loaded or not
	 * 
	 * If jQuery is loaded, the jQuery function will be redefined to $j, 
	 * as the PHP2Go Javascript Framework already uses the $ identifier.
	 *
	 * @var bool
	 */
	var $jQuery = FALSE;

	/**
	 * Set of document elements detected in the layout template
	 *
	 * @var array
	 */
	var $elements;

	/**
	 * {@link DocumentHead} instance used to manage and generate the HEAD section of the page
	 *
	 * @var object DocumentHead
	 */
	var $Head;

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
		$this->_initialize($docLayout, $docIncludes);
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
	 * @uses DocumentHead::getTitle()
	 */
	function getTitle() {
		return $this->Head->getTitle();
	}

	/**
	 * Set the document's title
	 *
	 * @param string $title New title
	 * @uses DocumentHead::setTitle()
	 */
	function setTitle($title) {
		$this->Head->setTitle($title);
	}

	/**
	 * Set the document's title based on a given SQL query
	 *
	 * The first cell of the first row of the SQL results
	 * will be used as the document's title.
	 *
	 * @param string $sql SQL query that defines the document title
	 * @param string $connectionId DB connection ID
	 * @uses DocumentHead::setTitle()
	 */
	function setTitleFromDb($sql, $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		$dbTitle = $Db->getFirstCell($sql);
		if ($dbTitle)
			$this->Head->setTitle($dbTitle);
	}

	/**
	 * Append a given string in the document's title
	 *
	 * @param string $title Value to be appended
	 * @param bool $useSeparator Whether to use a separator between existent title and appended value
	 * @param string $separator Separator to be used
	 * @uses DocumentHead::setTitle()
	 */
	function appendTitle($title, $useSeparator=TRUE, $separator='-') {
		$currentTitle = $this->Head->getTitle();
		if ($currentTitle == "") {
			$this->Head->setTitle($title);
		} else {
			$newTitle = $currentTitle . ($useSeparator ? ' ' . $separator . ' ' : '') . $title;
			$this->Head->setTitle($newTitle);
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
	 * Set the document's language code
	 *
	 * @param string $lang New language code
	 * @uses DocumentHead::setLanguage()
	 */
	function setLanguage($lang) {
		$this->Head->setLanguage($lang);
	}

	/**
	 * Set the document's charset
	 *
	 * @param string $charset New charset code
	 * @uses DocumentHead::setCharset()
	 */
	function setCharset($charset) {
		$this->Head->setCharset($charset);
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
		$this->cacheEnabled = (bool)$flag;
	}

	/**
	 * Enable/disable gzip compression on this document
	 *
	 * GZIP compression isn't enabled by default. However, this is one of
	 * the good practices when dealing with pages that produce large HTML
	 * payloads.
	 *
	 * @param bool $flag Enable/disable
	 * @param int $level Compression level
	 */
	function setCompression($flag=TRUE, $level=9) {
		$this->compressionEnabled = (bool)$flag;
		if ($this->compressionEnabled)
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
	 * @param string $fieldName Field name
	 */
	function setFocus($formName, $fieldName=NULL) {
		$this->Head->addScript(PHP2GO_JAVASCRIPT_PATH . 'form.js');
		if (empty($fieldName))
			$this->addOnloadCode(sprintf("Form.focusFirstField('%s');", $formName));
		else
			$this->addOnloadCode(sprintf("if (__fld = \$FF('%s', '%s')) { __fld.focus(); }", $formName, $fieldName));
	}

	/**
	 * Adds/replaces a meta tag in the document
	 *
	 * @param string $name Meta name
	 * @param string $value Meta value
	 * @param bool $httpEquiv Is this an http-equiv meta tag?
	 * @uses DocumentHead::addMetaData()
	 */
	function addMetaData($name, $value, $httpEquiv=FALSE) {
		$this->Head->addMetaData($name, $value, $httpEquiv);
	}

	/**
	 * Removes a meta tag from the document
	 *
	 * @param string $name Meta name
	 * @param bool $httpEquiv Is this an http-equiv meta tag?
	 */
	function removeMetaData($name, $httpEquiv=FALSE) {
		$this->Head->removeMetaData($name, $httpEquiv);
	}

	/**
	 * Adds a script file in the document's head
	 *
	 * @param string $path Relative or absolute path to the script
	 * @param string $language Script language
	 * @param string $type Script type
	 * @param string $charset Script charset
	 * @param int $priority Priority
	 * @uses DocumentHead::addScript()
	 */
	function addScript($path, $language="Javascript", $type='text/javascript', $charset=NULL, $priority=1) {
		$this->Head->addScript($path, $language, $type, $charset, $priority);
	}

	/**
	 * Adds a block of script code in the document
	 *
	 * The $position argument determines if the script will be added
	 * in the end of the document's head ({@link SCRIPT_START}) or in
	 * the end of the document's body ({@link SCRIPT_END}).
	 *
	 * @param string $block Block of script code
	 * @param string $language Language
	 * @param int $position Insert position
	 * @uses DocumentHead::addScriptBlock()
	 */
	function addScriptCode($block, $language="Javascript", $position=SCRIPT_START) {
		switch ($position) {
			case SCRIPT_END :
				$this->scriptBlocks[$language] = (isset($this->scriptBlocks[$language]) ? $this->scriptBlocks[$language] . $block . "\n" : $block . "\n");
				break;
			default :
				$this->Head->addScriptCode($block, $language);
		}
	}

	/**
	 * Register a Javascript instruction that must be
	 * executed when document is loaded
	 *
	 * @param string $instruction Script instruction
	 */
	function addOnloadCode($instruction) {
		$instruction = ltrim(preg_replace("/\s{1,}/", ' ', $instruction));
		$instruction = rtrim($instruction, ';') . ';';
		$this->onLoadCode[] = $instruction;
	}
	
	/**
	 * Add a stylesheet file in the document's head
	 *
	 * @param string $path Relative or absolute path to the stylesheet file
	 * @param string $media Media type
	 * @param string $charset Charset of the stylesheet file
	 * @param string $condition Conditional expression
	 * @param int $priority Priority
	 * @uses DocumentHead::addStyle()
	 */
	function addStyle($path, $media=NULL, $charset=NULL, $condition=NULL, $priority=1) {
		$this->Head->addStyle($path, $media, $charset, $condition, $priority);
	}

	/**
	 * Add a block of style definitions in the document's head
	 *
	 * @param string $styleCode Block of style definitions
	 * @uses DocumentHead::addStyleCode()
	 */
	function addStyleCode($styleCode) {
		$this->Head->addStyleCode($styleCode);
	}

	/**
	 * Import a stylesheet file onto the document
	 *
	 * In contrast with {@link addStyle}, which builds a link element, this method
	 * builds a style element containing an @import(styleUrl) statement.
	 *
	 * @param string $url Relative or absolute path to the stylesheet file
	 * @uses DocumentHead::importStyle()
	 */
	function importStyle($url) {
		$this->Head->importStyle($url);
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
	 * @param string $url Link URL
	 * @param string $title Link title
	 * @uses DocumentHead::addAlternateLink()
	 */
	function addAlternateLink($type, $url, $title) {
		$this->Head->addAlternateLink($type, $url, $title);
	}

	/**
	 * Define the shortcut icon of this document
	 *
	 * The shortcut icon is used by browsers in the main address bar
	 * and in the bookmark sections
	 *
	 * @param string $url Relative or absolute path to the icon
	 * @uses DocumentHead::appendContent()
	 */
	function setShortcutIcon($url) {
		$this->Head->appendContent(sprintf("<link rel=\"shortcut icon\" href=\"%s\" />", htmlentities(html_entity_decode($url))));
	}

	/**
	 * Flags the document to prevent robots using a meta tag
	 *
	 * @uses DocumentHead::addMetaData()
	 */
	function preventRobots() {
		$this->Head->addMetaData('ROBOTS', 'NOINDEX,NOFOLLOW,NOARCHIVE');
	}

	/**
	 * Append extra HTML content in the document's head
	 *
	 * @param string $value Value to append
	 * @uses DocumentHead::appendContent()
	 */
	function appendHeaderContent($value) {
		$this->Head->appendContent($value);
	}

	/**
	 * Set one or more properties of the document's body
	 *
	 * @param string|array $attr Proprety name or hash array of properties
	 * @param string $value Property value
	 */
	function addBodyCfg($attr, $value="") {
		if (is_array($attr)) {
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
		$this->extraContent[$position] = isset($this->extraContent[$position]) ? $this->extraContent[$position] . $content . "\n" : $content . "\n";
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
		if ($this->compressionEnabled && !headers_sent() && !connection_aborted() && extension_loaded('zlib')) {
			import('php2go.net.UserAgent');
			$Agent =& UserAgent::getInstance();
			if ($encoding = $Agent->matchAcceptList(array('x-gzip', 'gzip'), 'encoding')) {
				ini_set('zlip.output_compression', $this->compressionLevel);
				ob_start('ob_gzhandler');
				$this->_printDocumentHeader();
				$this->_printDocumentBody();
				print "\n" . PHP2Go::getLangVal('COMPRESS_USE_MSG', $encoding) . "\n";
				ob_end_flush();
			} else {
				$this->_printDocumentHeader();
				$this->_printDocumentBody();
			}
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
	 * Initializes document's properties
	 *
	 * @access private
	 */
	function _initialize($docLayout, $docIncludes=array()) {
		// start time counter
		$this->TimeCounter = new TimeCounter();
		// initialize document's head
		$this->Head =& DocumentHead::getInstance();
		// initialize document's template
		$this->Template = new Template($docLayout);
		if (!empty($docIncludes) && TypeUtils::isHashArray($docIncludes)) {
			foreach ($docIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
		// initialize document slots
		$elements = $this->Template->getDefinedVariables();
		$elementCount = sizeof($elements);
		if (!$elementCount)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_DOC_LAYOUT'), E_USER_ERROR, __FILE__, __LINE__);
		for ($i=0; $i<$elementCount; $i++)
			$this->elements[$elements[$i]] = '';
		// add basic JS libraries
		$Conf =& Conf::getInstance();
		$this->Head->addScript(PHP2GO_JAVASCRIPT_PATH . 'php2go.js?locale=' . $Conf->getConfig('LANGUAGE_CODE') . '&date=' . $Conf->getConfig('LOCAL_DATE_FORMAT') . '&charset=' . $Conf->getConfig('CHARSET'), '', 'text/javascript', NULL, 0);
	}

	/**
	 * Processes all document elements before rendering the page body
	 *
	 * # For template elements, it will call {@link Template::parse()} if not called yet
	 * # For component elements, it will call {@link Component::onPreRender()} if not called yet
	 * # It will assign object elements by ref and scalar elements by value
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
			} else {
				$this->Template->assign($name, $element);
			}
		}
	}

	/**
	 * Perform configuration routines before rendering the document's head
	 *
	 * @param bool $display Indicates if the page will be displayed or serialized into a file
	 * @uses DocumentHead::addMetaData
	 * @uses DocumentHead::addScriptCode
	 * @access private
	 */
	function _preRenderHeader($display=TRUE) {
		if (!empty($this->onLoadCode)) {
			$this->Head->addScriptCode("\tfunction p2gOnLoad() {\n\t\t" . join("\n\t\t", $this->onLoadCode) . "\n\t}", 'Javascript');
			$this->attachBodyEvent('onload', 'p2gOnLoad();', TRUE);
		}
		if ($this->jQuery) {
			$this->Head->addScript('http://jqueryjs.googlecode.com/files/jquery-1.3.1.min.js', '', 'text/javascript', NULL, 0);
			$this->addScriptCode("\t\$j = jQuery.noConflict();");			
		}
		if (!$this->cacheEnabled) {
			if ($display && !headers_sent() && !$this->compressionEnabled) {
				@header('Expires: Tue, 1 Jan 1980 12:00:00 GMT');
				@header('Last-Modified: ', gmdate('D, d M Y H:i:s') . ' GMT');
				@header('Cache-Control: no-cache');
				@header('Pragma: no-cache');
			}
			$this->Head->addMetaData('Expires', 'Tue, 1 Jan 1980 12:00:00 GMT', TRUE);
			$this->Head->addMetaData('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', TRUE);
			$this->Head->addMetaData('Cache-Control', 'no-cache');
			$this->Head->addMetaData('Pragma', 'no-cache');
		}
	}

	/**
	 * Prints the contents of the document's head
	 *
	 * @access private
	 * @uses DocumentHead::display()
	 */
	function _printDocumentHeader() {
		$this->Head->display();
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
		if (!empty($this->extraContent[BODY_START]))
			print $this->extraContent[BODY_START];
		$this->Template->display();
		if (!empty($this->extraContent[BODY_END]))
			print "\n" . $this->extraContent[BODY_END];
		// scripts located in the end of the document's body
		if (isset($this->scriptBlocks)) {
			foreach($this->scriptBlocks as $language => $scripts) {
				if (substr($scripts, -1) != "\n")
					$scripts .= "\n";
				print sprintf("\n<script language=\"%s\" type=\"text/%s\">\n%s\n</script>", $language, strtolower(preg_replace("/[^a-zA-Z]/", "", $language)), $scripts);
			}
		}
		print "\n</body>\n</html>";
		print "\n<!-- This page is powered by PHP2Go " . PHP2GO_VERSION . " (http://www.php2go.com.br) -->";
		$this->TimeCounter->stop();
		print sprintf("\n<!-- Timespent : %.3f -->", $this->TimeCounter->getElapsedTime());
	}
}
?>
