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
 * HTML head manager
 *
 * This is a helper class used by {@link Document} class to build
 * and output the contents of the HEAD tag of an HTML document. It
 * contains the implementation of methods that allow to populate
 * a web document with meta tags, external scripts, script blocks,
 * external stylesheet files, style blocks and alternate links.
 *
 * In some special situations, the DocumentHead's singleton can
 * be used to add contents in the HEAD of the HTML document that
 * is currently being produced. That is how template widgets are able
 * to register the resources they need to run.
 *
 * @package base
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DocumentHead
{
	/**
	 * Document title
	 *
	 * Defaults to the configuration setting TITLE
	 *
	 * @var string
	 */
	var $title;

	/**
	 * Document language code
	 *
	 * Defaults to the active language code.
	 *
	 * @var string
	 */
	var $language;

	/**
	 * Document charset
	 *
	 * Defaults to the configuration setting CHARSET
	 *
	 * @var string
	 */
	var $charset;

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
	 * Set of inline script blocks
	 *
	 * @var array
	 */
	var $scriptBlocks = array();

	/**
	 * Set of stylesheet files
	 *
	 * @var array
	 */
	var $styleFiles = array();

	/**
	 * Set of imported stylesheet files
	 *
	 * This property is populated by {@link importStyle}.
	 *
	 * @var array
	 */
	var $styleImports = array();

	/**
	 * Set of inline style definitions
	 *
	 * @var string
	 */
	var $styleCode = '';

	/**
	 * Set of alternate links for this document
	 *
	 * @var array
	 */
	var $alternateLinks = array();

	/**
	 * Extra HTML content to be rendered in the
	 * end of the HEAD tag
	 *
	 * @var string
	 */
	var $extraContent = '';

	/**
	 * Class constructor
	 *
	 * Initializes some of the class properties: title, language,
	 * charset and meta tags
	 *
	 * @return DocumentHead
	 */
	function DocumentHead() {
		$this->title = PHP2Go::getConfigVal('TITLE', FALSE);
		$this->language = PHP2Go::getConfigVal('LOCALE', FALSE);
		$this->charset = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->metaTagsName['TITLE'] =& $this->title;
		$this->metaTagsName['AUTHOR'] = PHP2Go::getConfigVal('AUTHOR', FALSE);
		$this->metaTagsName['DESCRIPTION'] = PHP2Go::getConfigVal('DESCRIPTION', FALSE);
		$this->metaTagsName['KEYWORDS'] = PHP2Go::getConfigVal('KEYWORDS', FALSE);
		$this->metaTagsName['CATEGORY'] = PHP2Go::getConfigVal('CATEGORY', FALSE);
		$this->metaTagsName['CODE_LANGUAGE'] = 'PHP';
		$this->metaTagsName['GENERATOR'] = 'PHP2Go Web Development Framework ' . PHP2GO_VERSION;
		$this->metaTagsName['DATE_CREATION'] = PHP2Go::getConfigVal('DATE_CREATION', FALSE);
		$this->metaTagsHttp['Content-Language'] =& $this->language;
	}

	/**
	 * Get the singleton of the DocumentHead class
	 *
	 * @return Conf
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new DocumentHead();
		return $instance;
	}

	/**
	 * Get the document's title
	 *
	 * @return string
	 */
	function getTitle() {
		return $this->title;
	}

	/**
	 * Set the document's title
	 *
	 * @param string $title New title
	 */
	function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Set the document's language code
	 *
	 * @param string $lang New language code
	 */
	function setLanguage($lang) {
		$this->language = $lang;
	}

	/**
	 * Set the document's charset
	 *
	 * @param string $charset New charset code
	 */
	function setCharset($charset) {
		$this->charset = $charset;
	}

	/**
	 * Adds/replaces a meta tag in the document's head
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
	 * Removes a meta tag from the document's head
	 *
	 * @param string $name Meta name
	 * @param bool $httpEquiv Is this an http-equiv meta tag?
	 */
	function removeMetaData($name, $httpEquiv=FALSE) {
		if ($httpEquiv) {
			if (isset($this->metaTagsHttp[$name]))
				unset($this->metaTagsHttp[$name]);
		} else {
			if (isset($this->metaTagsName[$name]))
				unset($this->metaTagsHttp[$name]);
		}
	}

	/**
	 * Adds a script file in the document's head
	 *
	 * @param string $path Relative or absolute path to the script
	 * @param string $language Script language
	 * @param string $type Script type
	 * @param string $charset Script charset
	 * @param int $priority Priority
	 */
	function addScript($path, $language='Javascript', $type='text/javascript', $charset=NULL, $priority=1) {
		$path = htmlentities(html_entity_decode($path));
		if (!array_key_exists($path, $this->scriptFiles)) {
			$priority = max(array($priority, 0));
			if (!empty($language)) {
				$this->scriptFiles[$path] = array(sprintf("<script language=\"%s\" src=\"%s\" type=\"%s\"%s></script>\n",
					$language, $path, $type, (!empty($charset) ? " charset=\"{$charset}\"" : '')
				), $priority);
			} else {
				$this->scriptFiles[$path] = array(sprintf("<script src=\"%s\" type=\"%s\"%s></script>\n",
					$path, $type, (!empty($charset) ? " charset=\"{$charset}\"" : '')
				), $priority);
			}
		}
	}

	/**
	 * Adds a block of script code in the document's head
	 *
	 * @param string $block Block of script code
	 * @param string $language Language
	 */
	function addScriptCode($block, $language) {
		$this->scriptBlocks[$language] = (isset($this->scriptBlocks[$language]) ? $this->scriptBlocks[$language] . ltrim(rtrim($block), "\r\n") . "\n" : ltrim(rtrim($block), "\r\n") . "\n");
	}

	/**
	 * Add a stylesheet file in the document's head
	 *
	 * @param string $path Relative or absolute path to the stylesheet file
	 * @param string $media Media type
	 * @param string $charset Charset of the stylesheet file
	 * @param string $condition Conditional expression
	 * @param int $priority Priority
	 */
	function addStyle($path, $media=NULL, $charset=NULL, $condition=NULL, $priority=1) {
		$path = htmlentities(html_entity_decode($path));
		if (!array_key_exists($path, $this->styleFiles)) {
			$priority = max(array($priority, 0));
			$this->styleFiles[$path] = array(sprintf("%s<link rel=\"stylesheet\" type=\"text/css\" href=\"%s\"%s%s />\n%s",
				(!empty($condition) ? "<!--[if {$condition}]>\n\t" : ""), $path, (!empty($media) ? " media=\"{$media}\"" : ''),
				(!empty($charset) ? " charset=\"{$charset}\"" : ''), (!empty($condition) ? "<![endif]-->\n" : "")
			), $priority);
		}
	}

	/**
	 * Add a block of style definitions in the document's head
	 *
	 * @param string $styleCode Block of style definitions
	 */
	function addStyleCode($code) {
		$this->styleCode .= ltrim(rtrim($code), "\r\n") . "\n";
	}

	/**
	 * Import a stylesheet file onto the document
	 *
	 * @param string $url Relative or absolute path to the stylesheet file
	 */
	function importStyle($url) {
		$url = htmlentities(html_entity_decode($url));
		if (!in_array($url, $this->styleImports)) {
			$this->styleImports[] = $url;
			$this->styleCode .= sprintf("@import url(%s);\n", trim($url));
		}
	}

	/**
	 * Add an alternate link in the document
	 *
	 * @param string $type Link type
	 * @param string $url Link URL
	 * @param string $title Link title
	 */
	function addAlternateLink($type, $url, $title) {
		$url = htmlentities(html_entity_decode($url));
		if (!array_key_exists($url, $this->alternateLinks)) {
			$this->alternateLinks[$url] = sprintf(
				"<link rel=\"alternate\" type=\"%s\" href=\"%s\"%s />\n",
				$type, $url, (!empty($title) ? " title=\"{$title}\"" : "")
			);
		}
	}

	/**
	 * Append extra HTML content in the document's head
	 *
	 * @param string $value Value to append
	 */
	function appendContent($code) {
		$this->extraContent .= ltrim(rtrim($code), "\r\n") . "\n";
	}

	/**
	 * Generates and displays the contents of the document's HEAD
	 */
	function display() {
		// xml declaration - only on browsers that can correctly handle it
		$agent =& UserAgent::getInstance();
		if ($agent->matchBrowser('ie7+') || !$agent->matchBrowser('ie'))
			print "<?xml version=\"1.0\"?>\n";
		// doctype, head tag
		print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		print sprintf("<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"%s\" lang=\"%s\">\n<head>\n", $this->language, $this->language);
		// meta tags
		print sprintf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\" />\n", $this->charset);
		foreach($this->metaTagsHttp as $name => $content) {
			if (!empty($content))
				print sprintf("<meta http-equiv=\"%s\" content=\"%s\" />\n", $name, htmlspecialchars($content));
		}
		foreach($this->metaTagsName as $name => $content) {
			if (!empty($content))
				print sprintf("<meta name=\"%s\" content=\"%s\" />\n", $name, htmlspecialchars($content));
		}
		// title
		print sprintf("<title>%s</title>\n", htmlspecialchars($this->title));
		// base URL
		$baseUrl = PHP2Go::getConfigVal('BASE_URL', FALSE);
		if (!empty($baseUrl)) {
			$baseUrl = rtrim($baseUrl, '/') . '/';
			print sprintf("<base href=\"%s\" />\n", $baseUrl);
		}
		// style files
		$files = $this->_arrangeByPriority($this->styleFiles, $max);
		for ($i=0; $i<=$max; $i++) {
			if (isset($files[$i])) {
				foreach ($files[$i] as $file)
					print $file;
			}
		}
		// style blocks
		if (!empty($this->styleCode))
			print sprintf("<style type=\"text/css\">\n%s</style>\n", $this->styleCode);
		// alternate links
		print join("", array_values($this->alternateLinks));
		// script files
		$files = $this->_arrangeByPriority($this->scriptFiles, $max);
		for ($i=0; $i<=$max; $i++) {
			if (isset($files[$i])) {
				foreach ($files[$i] as $file)
					print $file;
			}
		}
		// inline script blocks
		if (isset($this->scriptBlocks)) {
			foreach($this->scriptBlocks as $language => $scripts) {
				if (substr($scripts, -1) != "\n")
					$scripts .= "\n";
				print sprintf("<script language=\"%s\" type=\"text/%s\">\n%s</script>\n", $language, strtolower(preg_replace("/[^a-zA-Z]/", "", $language)), $scripts);
			}
		}
		// extra content
		print $this->extraContent;
		print "</head>\n";
	}

	/**
	 * Arranges elements by priority
	 *
	 * @param array $src Source array
	 * @param int $max Highest priority found
	 * @return int
	 * @access private
	 */
	function _arrangeByPriority($src, &$max) {
		$elms = array();
		$max = 0;
		foreach ($src as $item) {
			if ($item[1] > $max)
				$max = $item[1];
			if (!isset($elms[$item[1]]))
				$elms[$item[1]] = array();
			$elms[$item[1]][] = $item[0];
		}
		return $elms;
	}
}
?>