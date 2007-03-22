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

import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');
import('php2go.util.json.JSONEncoder');

/**
 * Name of the root block of a template
 */
define('TP_ROOTBLOCK', '_ROOT');
/**
 * Tag delimiter based on HTML comments: &lt;!-- --&gt;
 */
define('TEMPLATE_DELIM_COMMENT', 1);
/**
 * Tag delimiter based on curly braces: { }
 */
define('TEMPLATE_DELIM_BRACE', 2);
/**
 * Tag delimiter based on directives: &lt;% %&gt;
 *
 */
define('TEMPLATE_DELIM_DIRECTIVE', 3);
/**
 * Quoted string pattern
 */
define('TEMPLATE_QUOTED_STRING', '(?:"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\')');
/**
 * Number pattern
 */
define('TEMPLATE_NUMBER', '(?:\-?\d+(?:\.\d+)?)');
/**
 * Variable pattern
 */
define('TEMPLATE_VARIABLE', '\$?[\w\:\[\]]+(?:\.\$?[\w\:\[\]]+|->\$?[\w\:\[\]]+(?:\(\))?)*');
/**
 * Variable name pattern
 */
define('TEMPLATE_VARIABLE_NAME', '([\w\:\[\]]+)');
/**
 * Inner variable (used as a tag argument) pattern
 */
define('TEMPLATE_INNER_VARIABLE', '\$[\w\:\[\]]+(?:\.\$?[\w\:\[\]]+|->\$?[\w\:\[\]]+(?:\(\))?)*');
/**
 * Config variable
 */
define('TEMPLATE_CONFIG_VARIABLE', '\#\w+(?:\.\w+)?\#');
/**
 * Functions pattern
 */
define('TEMPLATE_FUNCTION', '[a-zA-Z_]\w*(::[a-zA-Z_]\w*)?');
/**
 * Variable modifier pattern
 */
define('TEMPLATE_MODIFIER', '((?:\|@?\w+(?::(?:\w+|' . TEMPLATE_INNER_VARIABLE . '|' . TEMPLATE_CONFIG_VARIABLE . '|' . TEMPLATE_NUMBER . '|' . TEMPLATE_QUOTED_STRING .'))*)*)');
/**
 * Dynamic blocks pattern
 */
define('TEMPLATE_BLOCK', '(START|END|INCLUDE|INCLUDESCRIPT|REUSE) BLOCK : ([a-zA-Z0-9_\.\-\\\/\~\s]+)');
/**
 * Include pattern
 */
define('TEMPLATE_INCLUDE', 'INCLUDE BLOCK : ([a-zA-Z0-9_\.\-\\\/\~\s]+)');
/**
 * Ignore tags pattern
 */
define('TEMPLATE_IGNORE', '(START|END) IGNORE');
/**
 * Function call pattern
 */
define('TEMPLATE_FUNCTION_CALL', '(START FUNCTION|END FUNCTION|FUNCTION)(?:\s+(.*))?');
/**
 * Condition tags pattern
 */
define('TEMPLATE_CONDITION', '(IF|ELSE IF|ELSE|END IF)[ ]?(.*)?');
/**
 * Loop tags pattern
 */
define('TEMPLATE_LOOP', '(LOOP|ELSE LOOP|END LOOP)(?:\s+(.*))?');
/**
 * Comments pattern
 */
define('TEMPLATE_COMMENT', '^\*.*\*$');
/**
 * Widget tags pattern
 */
define('TEMPLATE_WIDGET', '(START WIDGET|END WIDGET|WIDGET)(?:\s+(.*))?');
/**
 * Assign pattern
 */
define('TEMPLATE_ASSIGN', 'ASSIGN(\s+.*)?');
/**
 * Capture tags pattern
 */
define('TEMPLATE_CAPTURE', '(CAPTURE|END CAPTURE)(?:\s+(.*))?');
/**
 * Config tag pattern
 */
define('TEMPLATE_CONFIG', 'CONFIG(?:\s+(.*))?');

/**
 * Template parser class
 *
 * This class parses template files into PHP code. This code is returned to the
 * template engine to be executed. Besides, parsing task can be skipped by enabling
 * cache. It's strongly recommended to use template cache to improve performance.
 *
 * @package template
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TemplateParser extends PHP2Go
{
	/**
	 * Holds information about the raw template source
	 *
	 * @var array
	 * @access private
	 */
	var $tplBase = array();

	/**
	 * Holds information about the defined blocks and variables
	 *
	 * @var array
	 * @access private
	 */
	var $tplDef = array();

	/**
	 * Holds name based include blocks
	 *
	 * @var array
	 * @access private
	 */
	var $tplIncludes = array();

	/**
	 * Aggregates bundled and custom variable modifiers
	 *
	 * @var array
	 * @access private
	 */
	var $tplModifiers = array();

	/**
	 * Holds declared template widgets
	 *
	 * @var array
	 * @access private
	 */
	var $tplWidgets = array();

	/**
	 * Tags delimiters
	 *
	 * @var int
	 * @access private
	 */
	var $tagDelimiters = array('<!--', '-->');

	/**
	 * Indicates if the template was already compiled
	 *
	 * @var bool
	 * @access private
	 */
	var $prepared = FALSE;

	/**
	 * Stack of dynamic blocks
	 *
	 * @var array
	 * @access private
	 */
	var $blockStack = array();

	/**
	 * Hashmap of control flags
	 *
	 * @var array
	 * @access private
	 */
	var $controlFlags = array();

	/**
	 * Stack used to control tags and widgets
	 *
	 * @var array
	 * @access private
	 */
	var $controlStack = array();

	/**
	 * Current nesting level for loops
	 *
	 * @var int
	 * @access private
	 */
	var $loopNestingLevel = 0;

	/**
	 * Template inclusion depth
	 *
	 * @var int
	 * @access private
	 */
	var $includeDepth = 0;

	/**
	 * Parser version
	 *
	 * @var string
	 * @access private
	 */
	var $parserVersion = '$Revision$';

	/**
	 * Cache manager
	 *
	 * @var object CacheManager
	 * @access private
	 */
	var $_Cache = NULL;

	/**
	 * Owner template
	 *
	 * @var object Template
	 */
	var $_Template = NULL;

	/**
	 * Class constructor
	 *
	 * @param string $src Source (variable or file)
	 * @param int $type Source type ({@link T_BYFILE} or {@link T_BYVAR})
	 * @return TemplateParser
	 */
	function TemplateParser($src, $type) {
		parent::PHP2Go();
		$this->tplBase = array(
			'source' => $src,
			'type' => ($type == T_BYFILE || $type == T_BYVAR ? $type : T_BYVAR),
			'compiled' => NULL
		);
		$this->tplDef = array(
			TP_ROOTBLOCK => array(
				'vars' => array(),
				'parent' => NULL
			)
		);
		$this->tplModifiers = include(PHP2GO_ROOT . 'core/template/templateModifiers.php');
		$this->controlFlags['ignore'] = FALSE;
		$this->controlFlags['loop'] = FALSE;
		$this->controlFlags['shortOpenTag'] = (bool)System::getIni('short_open_tag');
	}

	/**
	 * Triggers the parsing of the template
	 *
	 * @param Template &$Template Owner template
	 */
	function parse(&$Template) {
		if (!$this->prepared) {
			if (!TypeUtils::isInstanceOf($Template, 'Template'))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Template'), E_USER_ERROR, __FILE__, __LINE__);
			$this->_Template =& $Template;
			$controlBlock = TP_ROOTBLOCK;
			// initialization code
			$this->tplBase['compiled'] = $this->_compilePHPBlock(sprintf(
				'$stack = array(); ' .
				'$outputStack = array();' .
				'$blockName = "%s"; ' .
				'$block =& $this->tplContent["%s:0"]; ' .
				'$instance = 0; ' .
				'$instanceCount = 1; ' .
				'$widget = NULL; ' .
				'$block[$instance][\'vars\'] = array_merge($this->tplGlobalVars, $block[$instance][\'vars\']); ',
				TP_ROOTBLOCK, TP_ROOTBLOCK
			));
			// compilation
			$this->_parseTemplate($this->tplBase['source'], $this->tplBase['type'], $this->tplBase['compiled'], $this->tplWidgets, $controlBlock);
			// post process source
			$this->tplBase['compiled'] = preg_replace("~\?>\s*<\?(php)?~", '', $this->tplBase['compiled']);
			$this->tplBase['compiled'] = rtrim($this->tplBase['compiled'], "\n");
			$this->prepared = TRUE;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TEMPLATE_ALREADY_PREPARED'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Parses a template file (main file or include block)
	 *
	 * @param string $src Template source (string contents or file path)
	 * @param int $type Source type ({@link T_BYFILE} or {@link T_BYVAR})
	 * @param string &$output Used to return compiled contents
	 * @param array &$widgets Widgets registry
	 * @param string &$controlBlock Active dynamic block
	 */
	function _parseTemplate($src, $type, &$output, &$widgets, &$controlBlock)
	{
		// 1st phase: prepare template source
		if ($type == T_BYFILE) {
			if (empty($src))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_TEMPLATE_FILE'), E_USER_ERROR, __FILE__, __LINE__);
			$cacheId = realpath($src);
			$time = @filemtime($src);
			$fileSrc = @file_get_contents($src);
			if ($fileSrc === FALSE)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $src), E_USER_ERROR, __FILE__, __LINE__);
			else
				$src = $fileSrc;
		} else {
			$cacheId = dechex(crc32($src));
		}
		$src = preg_replace_callback(PHP2GO_I18N_PATTERN, array($this, '_i18nPreFilter'), $src);

		// 2nd phase: compile template source
		if ($this->_Template->cacheOptions['enabled']) {
			if (!isset($this->_Cache)) {
				import('php2go.cache.CacheManager');
				$this->_Cache = CacheManager::factory('file');
				if ($this->_Template->cacheOptions['baseDir'])
					$this->_Cache->Storage->setBaseDir($this->_Template->cacheOptions['baseDir']);
				elseif ($this->_Template->cacheOptions['lifeTime'])
					$this->_Cache->Storage->setLifeTime($this->_Template->cacheOptions['lifeTime']);
			}
			if ($this->_Template->cacheOptions['useMTime'] && $type == T_BYFILE)
				$this->_Cache->Storage->setLastValidTime($time);
			$cached = $this->_Cache->load($cacheId, $this->_Template->cacheOptions['group']);
			if ($cached) {
				$output .= $cached['output'];
				$includes = $cached['includes'];
				$widgets = $cached['widgets'];
				foreach ($cached['def'] as $block => $def) {
					if ($block != $controlBlock && array_key_exists($block, $this->tplDef))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_DEFINED_BLOCK', $block), E_USER_ERROR, __FILE__, __LINE__);
					$this->tplDef[$block] = array_merge((array)$this->tplDef[$block], $def);
				}
			} else {
				// save and reinitialize current control variables
				if ($this->includeDepth > 0) {
					$blockStack = $this->blockStack;
					$controlStack = $this->controlStack;
					$tplDef = $this->tplDef;
					$this->blockStack = array();
					$this->controlStack = array();
					$this->tplDef = array(
						$controlBlock => $tplDef[$controlBlock]
					);
				}
				// parse source
				$compiled = $this->_compileTemplate($src, $controlBlock);
				$output .= $compiled['output'];
				$includes = $compiled['includes'];
				$widgets = $compiled['widgets'];
				// repetition blocks balancing
				if (!empty($this->blockStack)) {
					$last = array_pop($this->blockStack);
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_BLOCKDEF', $last), E_USER_ERROR, __FILE__, __LINE__);
				}
				// control structures balancing
				if (!empty($this->controlStack)) {
					$last = array_pop($this->controlStack);
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_TAG', $last[0]), E_USER_ERROR, __FILE__, __LINE__);
				}
				// save it to cache
				$this->_Cache->save(array(
					'output' => $output,
					'includes' => $includes,
					'widgets' => $widgets,
					'def' => $this->tplDef,
				), $cacheId, $this->_Template->cacheOptions['group']);
				// restore control variables
				if ($this->includeDepth > 0) {
					$this->blockStack = $blockStack;
					$this->controlStack = $controlStack;
					foreach ($this->tplDef as $block => $def) {
						if ($block != $controlBlock && array_key_exists($block, $tplDef))
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_DEFINED_BLOCK', $block), E_USER_ERROR, __FILE__, __LINE__);
						$tplDef[$block] = array_merge((array)$tplDef[$block], $this->tplDef[$block]);
					}
					$this->tplDef = $tplDef;
				}
			}
		} else {
			// parse source
			$compiled = $this->_compileTemplate($src, $controlBlock);
			$output .= $compiled['output'];
			$includes = $compiled['includes'];
			$widgets = $compiled['widgets'];
			// repetition blocks balancing
			if (!empty($this->blockStack)) {
				$last = array_pop($this->blockStack);
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_BLOCKDEF', $last), E_USER_ERROR, __FILE__, __LINE__);
			}
			// control structures balancing
			if (!empty($this->controlStack)) {
				$last = array_pop($this->controlStack);
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_TAG', $last[0]), E_USER_ERROR, __FILE__, __LINE__);
			}
		}

		// 3rd phase: recurse into include blocks
		$matches = array();
		preg_match_all("~(\r?\n?[\s]*){$this->tagDelimiters[0]}\s*" . TEMPLATE_INCLUDE . "\s*{$this->tagDelimiters[1]}~si", $output, $matches, PREG_OFFSET_CAPTURE);
		$totalMatches = sizeof($matches[0]);
		if ($totalMatches > 0) {
			$offset = 0;
			$result = '';
			for ($i=0; $i<$totalMatches; $i++) {
				$result .= substr($output, $offset, $matches[0][$i][1]-$offset);
				$result .= $this->_compileInclude($matches[2][$i][0], $includes[$i]['block']);
				$offset = $matches[0][$i][1] + strlen($matches[0][$i][0]);
			}
			$result .= substr($output, $offset);
			$output = $result;
		}
	}

	/**
	 * Compiles a template source into PHP code
	 *
	 * @param string $src Template source
	 * @param array &$controlBlock Active dynamic block
	 * @return string Compiled PHP code
	 * @access private
	 */
	function _compileTemplate($src, &$controlBlock) {
		$tagBlocks = array();
		$outputBlocks = array();
		$includes = array();
		$widgets = array();
		// gather all tags and code blocks
		$matches = array();
		preg_match_all("~(\r?\n?[\s]*){$this->tagDelimiters[0]}\s*(.*?)\s*{$this->tagDelimiters[1]}|{([^}]+)}~s", $src, $matches, PREG_OFFSET_CAPTURE);
		for ($i=0,$s=sizeof($matches[2]); $i<$s; $i++) {
			$tagBlocks[] = array(
				$matches[0][$i][0],
				(empty($matches[3][$i][0]) ? $matches[2][$i][0] : $matches[3][$i][0]),
				$matches[0][$i][1],
				$matches[1][$i][0]
			);
		}
		$codeBlocks = preg_split("~(?:\r?\n?[\s]*){$this->tagDelimiters[0]}\s*(.*?)\s*{$this->tagDelimiters[1]}|{([^}]+)}~s", $src);
		// process tags one by one
		$tagParts = array();
		foreach ($tagBlocks as $tag) {
			// ignore block
			if (preg_match('~' . TEMPLATE_IGNORE . '~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				if ($operation == 'START') {
					if ($this->controlFlags['ignore']) {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_TAG', "IGNORE (position {$tag[2]})"), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
					$this->controlFlags['ignore'] = TRUE;
				} else {
					if (!$this->controlFlags['ignore']) {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_TAG', "IGNORE (position {$tag[2]})"), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
					$this->controlFlags['ignore'] = FALSE;
				}
				$outputBlocks[] = '';
			}
			// assign
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_ASSIGN . '$~i', $tag[1], $tagParts)) {
				if (!$this->_validateTag('ASSIGN', @$tagParts[1], TRUE, array(), $controlBlock))
					return FALSE;
				$outputBlocks[] = $this->_compileAssign($tagParts[1]);
			}
			// function calls
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_FUNCTION_CALL . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'FUNCTION' :
						if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
							return FALSE;
						$outputBlocks[] = $this->_compileFunctionCall(@$tagParts[2]);
						break;
					case 'START FUNCTION' :
						if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
							return FALSE;
						$outputBlocks[] = $this->_compileFunctionCall($tagParts[2], TRUE, $controlBlock);
						break;
					case 'END FUNCTION' :
						if (!$last = $this->_validateTag($operation, @$tagParts[2], FALSE, array('START FUNCTION'), $controlBlock))
							return FALSE;
						$outputBlocks[] = $this->_compileFunctionEnd($last[2]);
						break;
					default :
						$outputBlocks[] = $tag[0];
						break;
				}
			}
			// widgets
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_WIDGET . '$~is', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'WIDGET' :
						if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
							return FALSE;
						$outputBlocks[] = $this->_compileWidgetInclude($tagParts[2], $widgets);
						break;
					case 'START WIDGET' :
						if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
							return FALSE;
						$outputBlocks[] = $this->_compileWidgetStart($tagParts[2], $widgets, $controlBlock);
						break;
					case 'END WIDGET' :
						if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('START WIDGET'), $controlBlock))
							return FALSE;
						$outputBlocks[] = $this->_compileWidgetEnd();
						break;
					default :
						$outputBlocks[] = $tag[0];
						break;
				}
			}
			// block operations
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_BLOCK . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'START' :
						if ($this->controlFlags['loop']) {
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_BLOCKINSIDELOOP'), E_USER_ERROR, __FILE__, __LINE__);
							return FALSE;
						}
						$outputBlocks[] = $this->_compileBlockStart($tagParts[2], $controlBlock);
						$controlBlock = $tagParts[2];
						break;
					case 'END' :
						$last = array_pop($this->blockStack);
						if ($last === NULL) {
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_BLOCKDEF', $tagParts[2]), E_USER_ERROR, __FILE__, __LINE__);
							return FALSE;
						}
						if ($tagParts[2] != $last) {
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_BLOCKDEF', $last), E_USER_ERROR, __FILE__, __LINE__);
							return FALSE;
						}
						$controlBlock = (empty($this->blockStack) ? TP_ROOTBLOCK : $this->blockStack[sizeof($this->blockStack)-1]);
						$outputBlocks[] = $this->_compilePHPBlock('} $block =& $this->_popStack($stack, $blockName, $instance, $instanceCount); }');
						break;
					case 'INCLUDE' :
						$blockName = trim($tagParts[2]);
						$includes[] = array(
							'name' => $blockName,
							'block' => $controlBlock
						);
						$outputBlocks[] = $tag[0];
						break;
					case 'INCLUDESCRIPT' :
						$blockName = trim($tagParts[2]);
						$outputBlocks[] = $this->_compileIncludeScript($blockName);
						break;
					default :
						$outputBlocks[] = $tag[0];
						break;
				}
			}
			// loop operations
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_LOOP . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'LOOP' :
						if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
							return FALSE;
						$this->controlFlags['loop'] = TRUE;
						$this->controlStack[] = array('LOOP', $controlBlock);
						$outputBlocks[] = $this->_compileLoopStart($tagParts[2]);
						break;
					case 'ELSE LOOP' :
						if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('LOOP'), $controlBlock))
							return FALSE;
						$this->controlStack[] = array('ELSE LOOP', $controlBlock);
						$outputBlocks[] = $this->_compilePHPBlock('} } else {');
						break;
					case 'END LOOP' :
						$last = (!empty($this->controlStack) ? $this->controlStack[sizeof($this->controlStack)-1] : array());
						if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('LOOP', 'ELSE LOOP'), $controlBlock))
							return FALSE;
						if (array_search('LOOP', $this->controlStack) === FALSE && array_search('ELSE LOOP', $this->controlStack) === FALSE)
							$this->controlFlags['loop'] = FALSE;
						$outputBlocks[] = $this->_compilePHPBlock(@$last[0] == 'ELSE LOOP' ? '} unset($_loop' . $this->loopNestingLevel . '); }' : '} } unset($_loop' . $this->loopNestingLevel . ');  }');
						$this->loopNestingLevel--;
						break;
					default :
						$outputBlocks[] = $tag[0];
						break;
				}
			}
			// condition tags
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_CONDITION . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'IF' :
		 				if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
		 					return FALSE;
						$this->controlStack[] = array('IF', $controlBlock);
						$outputBlocks[] = $this->_compileIf($tagParts[2]);
						break;
					case 'ELSE IF' :
		 				if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array('IF', 'ELSE IF'), $controlBlock))
		 					return FALSE;
						$this->controlStack[] = array('ELSE IF', $controlBlock);
						$outputBlocks[] = $this->_compileIf($tagParts[2], TRUE);
						break;
					case 'ELSE' :
		 				if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('IF', 'ELSE IF'), $controlBlock))
		 					return FALSE;
						$this->controlStack[] = array('ELSE', $controlBlock);
						$outputBlocks[] = $this->_compilePHPBlock('} else {');
						break;
					case 'END IF' :
		 				if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('IF', 'ELSE IF', 'ELSE'), $controlBlock))
		 					return FALSE;
		 				$outputBlocks[] = $this->_compilePHPBlock('}');
						break;
					default :
						$outputBlocks[] = $tag[0];
						break;
				}
			}
			// capture
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_CAPTURE . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'CAPTURE' :
						if (!$this->_validateTag($operation, @$tagParts[2], NULL, array(), $controlBlock))
							return FALSE;
						$outputBlocks[] = $this->_compileCaptureStart(@$tagParts[2], $controlBlock);
						break;
					case 'END CAPTURE' :
						if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('CAPTURE'), $controlBlock))
							return FALSE;
						$outputBlocks[] = $this->_compileCaptureEnd();
						break;
					default :
						$outputBlocks[] = $tag[0];
						break;
				}
			}
			// config
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_CONFIG . '$~i', $tag[1], $tagParts)) {
				if (!$this->_validateTag('CONFIG', @$tagParts[1], TRUE, array(), $controlBlock))
					return FALSE;
				$outputBlocks[] = $this->_compileConfig($tagParts[1]);
			}
			// variable
			elseif (!$this->controlFlags['ignore'] && preg_match('~^(' . TEMPLATE_VARIABLE . '|' . TEMPLATE_CONFIG_VARIABLE . '|' . TEMPLATE_QUOTED_STRING . ')' . TEMPLATE_MODIFIER . '$~xs', $tag[1], $tagParts)) {
				$varDef = preg_replace('/^\$/', "", $tagParts[1]);
				if (!preg_match('~' . TEMPLATE_QUOTED_STRING . '~', $varDef) && array_search($varDef, $this->tplDef[$controlBlock]['vars']) === FALSE)
					$this->tplDef[$controlBlock]['vars'][] = $varDef;
				$outputBlocks[] = $this->_compileVariable($tagParts[1], $tagParts[2], TRUE);
			}
			// comments
			elseif (preg_match('~^' . TEMPLATE_COMMENT . '$~ms', $tag[1])) {
				$outputBlocks[] = '';
			}
			// others: recursive call
			else {
				if ($tag[0][0] == '{') {
					$compiled = $this->_compileTemplate(substr($tag[0], 1), $controlBlock);
					$includes = array_merge($includes, $compiled['includes']);
					$outputBlocks[] = '{' . $compiled['output'];
				} else {
					$pos = strpos($tag[0], $tag[1]);
					$startCode = substr($tag[0], 0, $pos);
					$endCode = substr($tag[0], $pos + strlen($tag[1]));
					$compiled = $this->_compileTemplate($tag[1], $controlBlock);
					$includes = array_merge($includes, $compiled['includes']);
					$outputBlocks[] = $startCode . $compiled['output'] . $endCode;
				}
			}
		}
		// merge code blocks and literal blocks
		$output = '';
		for ($i=0, $s=sizeof($outputBlocks); $i<$s; $i++) {
			if (preg_match('/^<\?(php)? print/', $outputBlocks[$i]))
				$codeBlocks[$i] .= $tagBlocks[$i][3];
			$output .= $codeBlocks[$i] . $outputBlocks[$i];
		}
		$output .= $codeBlocks[$i];
		return array(
			'output' => $output,
			'includes' => $includes,
			'widgets' => $widgets
		);
	}

	/**
	 * Compiles a variable reference
	 *
	 * The $print argument indicates if we're printing a variable (direct access)
	 * or using it as an argument of a tag (IF, LOOP, ASSIGN).
	 *
	 * @param string $varName Variable name
	 * @param string $varModifiers Variable modifiers
	 * @param bool $print Is direct access?
	 * @access private
	 * @return string
	 */
	function _compileVariable($varName, $varModifiers=NULL, $print=FALSE) {
		$quotedString = preg_match('~^' . TEMPLATE_QUOTED_STRING . '$~', $varName);
		$compiledName = ($quotedString ? $varName : $this->_compileVariableName($varName));
		if (!empty($varModifiers)) {
			$matches = array();
			$modCallBase = $compiledName;
			preg_match_all('~\|(@?\w+)((?>:(?:' . TEMPLATE_QUOTED_STRING . '|[^|]+))*)~', $varModifiers, $matches);
			list(, $modNames, $modArgs) = $matches;
			for ($i=0; $i<sizeof($modNames); $i++) {
				$modCallArgs = array();
				// @ means an external function
				if ($modNames[$i][0] == '@' && function_exists(substr($modNames[$i], 1))) {
					$modFunc = substr($modNames[$i], 1);
				} elseif (isset($this->tplModifiers[$modNames[$i]])) {
					$modSpec = (array)$this->tplModifiers[$modNames[$i]];
					if (sizeof($modSpec) == 1) {
						$modFunc = $modSpec[0];
					} elseif (sizeof($modSpec) == 2) {
						$modFunc = join('::', $modSpec);
					} elseif (sizeof($modSpec) == 3) {
						import(array_shift($modSpec));
						$modFunc = join('::', $modSpec);
					} else {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_INVALID_MODIFIER', $modNames[$i]), E_USER_ERROR, __FILE__, __LINE__);
					}
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_INVALID_MODIFIER', $modNames[$i]), E_USER_ERROR, __FILE__, __LINE__);
				}
				// build modifier arguments
				preg_match_all('~:(' . TEMPLATE_QUOTED_STRING . '|[^:]+)~', $modArgs[$i], $matches);
				for ($j=0; $j<sizeof($matches[1]); $j++) {
					if (preg_match('~(?:' . TEMPLATE_INNER_VARIABLE . '|' . TEMPLATE_CONFIG_VARIABLE . ')~', $matches[1][$j]))
						$modCallArgs[$j] = $this->_compileVariableName($matches[1][$j]);
					else
						$modCallArgs[$j] = $matches[1][$j];
				}
				$modCallBase = $modFunc . '(' . $modCallBase . (!empty($modCallArgs) ? ', ' . join(', ', $modCallArgs) : '') . ')';
			}
			return ($print ? (IS_PHP5 ? $this->_compilePHPBlock("print {$modCallBase};") : $this->_compilePHPBlock("__v({$modCallBase});")) : $modCallBase);
		} else {
			if ($print) {
				if (!$quotedString)
					return (IS_PHP5 ? $this->_compilePHPBlock("print {$compiledName};") : $this->_compilePHPBlock("__v({$compiledName});"));
				return substr($compiledName, 1, -1);
			} else {
				return $compiledName;
			}
		}
	}

	/**
	 * Compiles a variable name
	 *
	 * Resolves variables names containing access to array keys, object
	 * properties, method calls and dynamic variables resolution.
	 *
	 * Matches variable names such as: {$var}, {$array.key}, {$obj->property},
	 * {$array.inner.innerMost}, {$obj->arrayProperty.key}, {$obj->$dynamic},
	 * {$array.$dynamic}, {$p2g.get.request_access}, {$p2g.conf.conf_key},
	 * {$p2g.const.my_constant}, ...
	 *
	 * @param string $name Raw variable name
	 * @return string Compiled variable name
	 * @access private
	 */
	function _compileVariableName($name) {
		// internal variables access
		if ($variableBase = $this->_compileInternalVariable($name)) {
			$p2gInternal = TRUE;
		} else {
			$p2gInternal = FALSE;
			$variableBase = '$block[$instance][\'vars\']';
			if ($name[0] == '$')
				$name = substr($name, 1);
		}
		if ($p2gInternal && empty($name)) {
			return $variableBase;
		}
		// config variables access
		$matches = array();
		if (preg_match('~^#(\w+)(?:\.(\w+))?#$~', $name, $matches)) {
			if (isset($matches[2]))
				return '$this->tplConfigVars[0][\'' . $matches[1] . '\'][\'vars\'][\'' . $matches[2] . '\']';
			return '$this->tplConfigVars[0][\'' . $matches[1] . '\']';
		}
		$compiled = '';
		$state = 0;
		preg_match_all('~(?:\$?[\w\:\[\]]+(?:\(\))?|\.|\-\>)~x', $name, $matches);
		$tokens = $matches[0];
		foreach ($tokens as $token) {
			if ($token == '.') {
				if ($state != 4)
					$compiled .= ($state == 3 ? "]" : "']");
				$state = 0;
			} elseif ($token == '->') {
				$compiled .= ($state == 4 ? '->' : ($state == 3 ? "]->" : "']->"));
				$state = 2;
			} elseif ($token[0] == '$') {
				$tmp = substr($token, 1);
				$tmpVarBase = ($p2gInternal ? '$block[$instance][\'vars\']' : $variableBase);
				$resolved = "{$tmpVarBase}['{$tmp}']";
				$compiled .= ($state == 2 ? "{{$resolved}}" : "[{$resolved}");
				$state = ($state == 2 ? 4 : 3);
			} else {
				$compiled .= ($state == 2 ? $token : "['{$token}");
				$state = ($state == 2 ? 4 : 1);
			}
		}
		if ($state == 1)
			$compiled .= "']";
		if ($state == 3)
			$compiled .= "]";
		return "{$variableBase}{$compiled}";
	}

	/**
	 * Compiles an internal variable name
	 *
	 * @param string &$variableName Variable name
	 * @return string Compiled variable name
	 * @access private
	 */
	function _compileInternalVariable(&$variableName) {
		$compiled = NULL;
		$matches = array();
		if (preg_match('~^\$?p2g\.(request|post|get|sessionobject\.(\w+)?|session|cookie|server|env|registry|conf|const\.(\w+)$|time$|microtime$|capture)?~', $variableName, $matches)) {
			$removeLength = strlen($matches[0]);
			switch (@$matches[1]) {
				case 'request' :
				case 'post' :
				case 'get' :
				case 'cookie' :
				case 'server' :
				case 'env' :
					$compiled = '$_' . strtoupper($matches[1]);
					break;
				case 'session' :
					$compiled = '$_SESSION';
					break;
				case 'registry' :
					$compiled = '$GLOBALS';
					break;
				case 'conf' :
					$compiled = '$this->tplInternalVars[\'conf\']';
					break;
				case 'time' :
					$compiled = 'time()';
					break;
				case 'microtime' :
					$compiled = 'System::getMicrotime()';
					break;
				case 'capture' :
					$compiled = '$this->tplCapture';
					break;
				default :
					if (strpos(@$matches[1], 'sessionobject') === 0)
						$compiled = '$_SESSION[\'' . $matches[2] . '\'][\'properties\']';
					elseif (strpos(@$matches[1], 'const') === 0)
						$compiled = '@constant(\'' . $matches[3] . '\')';
					else
						$compiled = '$this->tplInternalVars';
					break;
			}
			$variableName = ltrim(substr($variableName, $removeLength), '.');
		}
		return $compiled;
	}

	/**
	 * Compiles an "assign" tag
	 *
	 * Examples:
	 * <code>
	 * <!-- assign var=$variable -->
	 * <!-- assign var=1 -->
	 * <!-- assign var=true -->
	 * <!-- assign var="string" -->
	 * <!-- assign var=$obj->method() -->
	 * </code>
	 *
	 * @param string $expression Assignment expression
	 * @return strign Compiled code
	 * @access private
	 */
	function _compileAssign($expression) {
		$exprParts = array();
		if (preg_match('~^' . TEMPLATE_VARIABLE_NAME . '\s*[=]\s*((' . TEMPLATE_INNER_VARIABLE . '|' . TEMPLATE_CONFIG_VARIABLE . '|' . TEMPLATE_QUOTED_STRING . ')' . TEMPLATE_MODIFIER . '|' . TEMPLATE_QUOTED_STRING . '|' . TEMPLATE_NUMBER . '|\b\w+\b)$~', trim($expression), $exprParts)) {
			return $this->_compilePHPBlock(
				'$block[$instance][\'vars\'][\'' . $exprParts[1] . '\'] = ' .
				(isset($exprParts[4]) ? $this->_compileVariable($exprParts[3], $exprParts[4]) : $exprParts[2]) . ';'
			);
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_TAG_SYNTAX', array('ASSIGN', $expression)), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Compiles a function call ("function" and "start function") tags
	 *
	 * The "name" attribute is mandatory and should contain a function
	 * name, a class::method pair or an object->method expression, where
	 * the object must be a template variable.
	 *
	 * Examples:
	 * <code>
	 * <!-- start function name="obj->doThis" p1="string" p2=true -->
	 * <!-- function name="obj->doThat" p1=$var p2=1 p3=yes -->
	 * <!-- function name="procFunc" p1=$anotherVar -->
	 * <!-- function name="Class::staticMethod" -->
	 * </code>
	 *
	 * @param string $funcProperties Raw function arguments
	 * @param bool $isBlockFunction Whether is a block function call (START FUNCTION)
	 * @param string $controlBlock Active dynamic block
	 * @return string Compiled code
	 * @access private
	 */
	function _compileFunctionCall($funcProperties, $isBlockFunction=FALSE, $controlBlock=NULL) {
		$props = $this->_parseProperties($funcProperties);
		if (!isset($props['name'])) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_REQUIRED_ATTRIBUTE', array('name', 'FUNCTION')), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$output = "";
		$nameMatches = array();
		if (preg_match('~^(?:\'|")' . TEMPLATE_FUNCTION . '(?:\'|")$~', $props['name'])) {
			$funcName = substr($props['name'], 1, -1);
		} elseif (preg_match('~^(?:\'|")(' . TEMPLATE_VARIABLE . ')->([a-zA-Z_]\w*)(?:\'|")$~', $props['name'], $nameMatches)) {
			$output .= '$obj =& ' . $this->_compileVariableName($nameMatches[1]) . '; ';
			$output .= 'if (is_object($obj)) ';
			$funcName = '$obj->' . $nameMatches[2];
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_INVALID_TAG_ATTRIBUTE', array('name', 'FUNCTION')), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$funcParams = array();
		if ($isBlockFunction) {
			foreach ($props as $key => $value) {
				if ($key != 'name')
					$funcParams[] = "'{$key}' => {$value}";
			}
			$this->controlStack[] = array('START FUNCTION', $controlBlock, array($funcName, $funcParams));
			$funcParams = '(array(' . join(', ', $funcParams) . '), NULL, $this)';
			$output .= $funcName . $funcParams . ';';
		} else {
			$paramIndex = 1;
			while (isset($props['p' . $paramIndex])) {
				$funcParams[] = $props['p' . $paramIndex];
				$paramIndex++;
			}
			$funcParams = '(' . join(', ', $funcParams) . ')';
			if (isset($props['assign']) && preg_match('~^(?:\'|")' . TEMPLATE_VARIABLE_NAME . '(?:\'|")$~', $props['assign']))
				$output .= '$block[$instance][\'vars\'][\'' . substr($props['assign'], 1, -1) . '\'] = ' . $funcName . $funcParams . ';';
			elseif (@$props['output'] === 'FALSE')
				$output .= $funcName . $funcParams . ';';
			else
				$output .= 'print ' . $funcName . $funcParams . ';';
		}
		if ($isBlockFunction) {
			$output .= 'ob_start();';
		}
		return $this->_compilePHPBlock($output);
	}

	/**
	 * Compiles an "end function" tag
	 *
	 * @param array $funcData Function arguments
	 * @return string Compiled code
	 * @access private
	 */
	function _compileFunctionEnd($funcData) {
		return $this->_compilePHPBlock('print ' . $funcData[0] . '(array(' . join(', ', $funcData[1]) . '), ob_get_clean(), $this);');
	}

	/**
	 * Compiles a "start block" tag
	 *
	 * Example:
	 * <code>
	 * <!-- start block : block_name -->
	 * </code>
	 *
	 * @param string $blockName Block name
	 * @param string $parentBlock Parent block name
	 * @return string Compiled code
	 * @access private
	 */
	function _compileBlockStart($blockName, $parentBlock) {
		$this->blockStack[] = $blockName;
		$this->tplDef[$blockName] = array(
			'vars' => array(),
			'parent' => $parentBlock
		);
		return $this->_compilePHPBlock('if (isset($block[$instance][\'blocks\'][\'' . $blockName . '\'])) { $this->_pushStack($stack, $blockName, $block, $instance, $instanceCount); $blockName = "' . $blockName . '"; $block =& $this->tplContent[$block[$instance][\'blocks\'][\'' . $blockName . '\']]; $instance = 0; $instanceCount = sizeof($block); for (; $instance<$instanceCount; $instance++) { $block[$instance][\'vars\'] = array_merge($this->tplGlobalVars, $block[$instance][\'vars\']);');
	}

	/**
	 * Compiles an "include block" tag
	 *
	 * Examples:
	 * <code>
	 * <!-- include block : templates/includes/include.tpl -->
	 * <!-- include block : include_block -->
	 * </code>
	 *
	 * @param string $includeName Include name or file path
	 * @return string Compiled code
	 * @access private
	 */
	function _compileInclude($includeName, $controlBlock) {
		$includeName = trim($includeName);
		if (array_key_exists($includeName, $this->tplIncludes)) {
			$src = $this->tplIncludes[$includeName]['src'];
			$type = $this->tplIncludes[$includeName]['type'];
		} elseif (file_exists($includeName)) {
			$src = $includeName;
			$type = T_BYFILE;
		} else {
			return '';
		}
		$this->includeDepth++;
		$output = $this->_compilePHPBlock('array_unshift(\$this->tplConfigVars, \$this->tplConfigVars[0]);');
		$this->_parseTemplate($src, $type, $output, $widgets, $controlBlock);
		$output .= $this->_compilePHPBlock('array_shift(\$this->tplConfigVars);');
		$this->includeDepth--;
		return $output;
	}

	/**
	 * Compiles an "includescript block" tag
	 *
	 * Examples:
	 * <code>
	 * <!-- includescript block : templates/php/run_scripts.php -->
	 * <!-- includescript block : include_block_name -->
	 * </code>
	 *
	 * @param Include name or file path $includeName
	 * @return string Compiled code
	 * @access private
	 */
	function _compileIncludeScript($includeName) {
		if (array_key_exists($includeName, $this->tplIncludes)) {
			$src = $this->tplIncludes[$includeName]['src'];
			$type = $this->tplIncludes[$includeName]['type'];
		} elseif (file_exists($includeName)) {
			$src = $includeName;
			$type = T_BYFILE;
		} else {
			return '';
		}
		if ($type == T_BYFILE)
			return $this->_compilePHPBlock('include("' . $src . '");');
		return (strpos($src, '<?') === 0 ? $src : $this->_compilePHPBlock($src));
	}

	/**
	 * Compiles "if" and "else if" tags
	 *
	 * Examples:
	 * <code>
	 * <!-- if $var eq 1 -->
	 * <!-- if ( ($a + $b) lt 20 ) -->
	 * <!-- if $row is odd -->
	 * <!-- if $data is not empty -->
	 * <!-- else if $data !== null -->
	 * <!-- else if ( ( $var eq 1 ) or ( $var eq 2 ) ) -->
	 * </code>
	 *
	 * @param string $expression Raw expression
	 * @param bool $elseif Is this an "else if"?
	 * @return string Compiled code
	 * @access private
	 */
	function _compileIf($expression, $elseif=FALSE) {
		$match = array();
        preg_match_all('~(?>
        		' . TEMPLATE_FUNCTION . ' | ' . TEMPLATE_INNER_VARIABLE . ' | ' . TEMPLATE_CONFIG_VARIABLE . ' | ' . TEMPLATE_QUOTED_STRING . ' |
                \-?0[xX][0-9a-fA-F]+|\-?\d+(?:\.\d+)?|\.\d+|!==|===|==|!=|<>|<<|>>|<=|>=|\&\&|\|\||\(|\)|,|\!|\^|=|\&|\~|<|>|\||\%|\+|\-|\/|\*|\@|
                \b\w+\b|\S+)~x', $expression, $match);
        $tokens = $match[0];
        // balanced parenthesis
        $tokenCount = array_count_values($tokens);
        if (isset($tokenCount['(']) && $tokenCount['('] != $tokenCount[')'])
        	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_PARENTHESIS', $expression), E_USER_ERROR, __FILE__, __LINE__);
        // process all tokens
        $isStack = array();
        for ($i=0; $i<sizeof($tokens); $i++) {
        	$token =& $tokens[$i];
        	switch (strtolower($token)) {
                case '!' :
                case '%' :
                case '!==' :
                case '==' :
                case '===' :
                case '>' :
                case '<' :
                case '!=' :
                case '<>' :
                case '<<' :
                case '>>' :
                case '<=' :
                case '>=' :
                case '&&' :
                case '||' :
                case '|' :
                case '^' :
                case '&' :
                case '~' :
                case ',' :
                case '+' :
                case '-' :
                case '*' :
                case '/' :
                case '@' :
                case 'true' :
                case 'false' :
                case 'null' :
                    break;
                case '(' :
					$isStack[] = $i;
					break;
				case ')' :
					$discard = array_pop($isStack);
					break;
                case 'eq' :
                	$token = '==';
                	break;
                case 'neq' :
                	$token = '!==';
                	break;
                case 'lt' :
                	$token = '<';
                	break;
                case 'loet' :
                	$token = '<=';
                	break;
                case 'gt' :
                	$token = '>';
                	break;
                case 'goet' :
                	$token = '>=';
                	break;
                case 'and' :
                	$token = '&&';
                	break;
                case 'or' :
                	$token = '||';
                	break;
                case 'mod' :
                	$token = '%';
                	break;
                case 'div' :
                	$token = '/';
                	break;
                case 'is' :
                	if ($tokens[$i-1] == ')')
                		$isStart = array_pop($isStack);
                	else
                		$isStart = $i-1;
                	$isTokens = array_slice($tokens, $isStart, $i-$isStart);
                	$resultTokens = $this->_compileIs(implode(' ', $isTokens), array_slice($tokens, $i+1));
                	array_splice($tokens, $isStart, sizeof($tokens), $resultTokens);
                default :
                	$tokenParts = array();
                	// functions, static methods, constants: do nothing
                	if (preg_match('~^' . TEMPLATE_FUNCTION . '$~', $token)) {
                	}
                	// variables: parse
                	elseif (preg_match('~^(?:' . TEMPLATE_INNER_VARIABLE . '|' . TEMPLATE_CONFIG_VARIABLE . ')$~', $token, $tokenParts)) {
                		$token = $this->_compileVariable($tokenParts[0], NULL);
                	}
                	// strings and numbers: do nothing
                	elseif (preg_match('~^' . TEMPLATE_QUOTED_STRING . '|' . TEMPLATE_NUMBER . '$~', $token)) {
                	}
                	// constants: do nothing
                	elseif (preg_match('~^\b\w+\b$~', $token)) {
                	}
                	// others: error
                	else {
                		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_INVALID_TOKEN', array($expression, $token)), E_USER_ERROR, __FILE__, __LINE__);
                	}
        	}
        }
        if ($elseif)
        	return $this->_compilePHPBlock('} elseif ('.implode(' ', $tokens).') {');
        else
        	return $this->_compilePHPBlock('if ('.implode(' ', $tokens).') {');
	}

	/**
	 * Compiles an "is (not) XXX" expression, which can be used
	 * inside condition tags (if and else if)
	 *
	 * @param string $expr Raw expression
	 * @param array $nextTokens Tokens that preceed the is expression
	 * @return array Modified tokens
	 * @access private
	 */
	function _compileIs($expr, $nextTokens) {
		$negate = FALSE;
		$result = '';
		if (($next = array_shift($nextTokens)) == 'not') {
			$negate = TRUE;
			$operation = array_shift($nextTokens);
		} else {
			$operation = $next;
		}
		switch ($operation) {
			case 'odd' :
				$result = '(' . $expr . ' % 2) == 0';
				break;
			case 'even' :
				$result = '(' . $expr . ' % 2) == 1';
				break;
			case 'null' :
				if ($expr[0] != '(')
					$expr = '(' . $expr . ')';
				$result = 'is_null' . $expr;
				break;
			case 'empty' :
				if ($expr[0] != '(')
					$expr = '(' . $expr . ')';
				$result = 'TypeUtils::isEmpty' . $expr;
				break;
			default :
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_INVALID_IS_EXPR', $operation), E_USER_ERROR, __FILE__, __LINE__);
				break;
		}
		if ($negate)
			$result = '!(' . $result . ')';
		array_unshift($nextTokens, $result);
		return $nextTokens;
	}

	/**
	 * Compiles a "loop" tag
	 *
	 * The var and item attributes are mandatory. "var" must point to
	 * the variable that will be used to iterate (arrays, data sets
	 * or db record sets). "item" must be the variable to be used to
	 * assign each loop record.
	 *
	 * Examples:
	 * <code>
	 * <!-- loop var=$loop item="row" -->
	 * <!-- loop var=$data item="line" key="key" -->
	 * <!-- loop name="people" var=$people item="person" -->
	 * </code>
	 *
	 * @param string $loopProperties Raw loop properties
	 * @return string Compiled code
	 * @access private
	 */
	function _compileLoopStart($loopProperties) {
		$props = $this->_parseProperties($loopProperties);
		if (!isset($props['var'])) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_REQUIRED_ATTRIBUTE', array('var', 'LOOP')), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} elseif ($props['var'][0] == '$') {
			$props['var'] = '&' . $props['var'];
		}
		if (!isset($props['item'])) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_REQUIRED_ATTRIBUTE', array('item', 'LOOP')), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// assign key and value, or just value
		if (isset($props['key']))
			$leftSide = 'list($block[$instance][\'vars\'][' . $props['key'] . '], $block[$instance][\'vars\'][' . $props['item'] . ']) =';
		else
			$leftSide = '$block[$instance][\'vars\'][' . $props['item'] . '] =';
		$this->loopNestingLevel++;
		$output = '$_loop' . $this->loopNestingLevel . ' = ' . $props['var'] . '; if (!empty($_loop' . $this->loopNestingLevel . ')) { ';
		// if the name attribute is present, the control variables are initialized and updated upon each iteration
		if (isset($props['name'])) {
			if ($props['name'][0] == '$') {
				$nameVar = '$_loopName' . $this->loopNestingLevel;
				$output .= $nameVar . ' = ' . $props['name'] . '; ';
			} else {
				$nameVar = $props['name'];
			}
			$output .= '$this->tplInternalVars[\'loop\'][' . $nameVar . '][\'iteration\'] = -1; ';
			$output .= '$this->tplInternalVars[\'loop\'][' . $nameVar . '][\'rownum\'] = 0; ';
			$output .= 'if (($_total = $this->_getLoopTotal($_loop' . $this->loopNestingLevel . '))  > 0) { ';
			$output .= '$this->tplInternalVars[\'loop\'][' . $nameVar . '][\'total\'] = $_total; ';
			$output .= 'while ((' . $leftSide . ' $this->_getLoopItem($_loop' . $this->loopNestingLevel . ', ' . (isset($props['key']) ? 'TRUE' : 'FALSE') . ')) !== FALSE) { ';
			$output .= '$this->tplInternalVars[\'loop\'][' . $nameVar . '][\'iteration\']++; ';
			$output .= '$this->tplInternalVars[\'loop\'][' . $nameVar . '][\'rownum\']++; ';
			$output .= '$this->tplInternalVars[\'loop\'][' . $nameVar . '][\'first\'] = ($this->tplInternalVars[\'loop\'][' . $nameVar . '][\'iteration\'] == 0); ';
			$output .= '$this->tplInternalVars[\'loop\'][' . $nameVar . '][\'last\'] = ($this->tplInternalVars[\'loop\'][' . $nameVar . '][\'iteration\'] == ($this->tplInternalVars[\'loop\'][' . $nameVar . '][\'total\'] - 1)); ';
			$output .= '$this->tplInternalVars[\'loop\'][' . $nameVar . '][\'prev\'] = ($this->tplInternalVars[\'loop\'][' . $nameVar . '][\'rownum\'] - 1); ';
			$output .= '$this->tplInternalVars[\'loop\'][' . $nameVar . '][\'next\'] = ($this->tplInternalVars[\'loop\'][' . $nameVar . '][\'rownum\'] + 1);';
		} else {
			$output .= 'if (($_total = $this->_getLoopTotal($_loop' . $this->loopNestingLevel . '))  > 0) { ';
			$output .= 'while ((' . $leftSide . ' $this->_getLoopItem($_loop' . $this->loopNestingLevel . ', ' . (isset($props['key']) ? 'TRUE' : 'FALSE') . ')) !== FALSE) {';
		}
		return $this->_compilePHPBlock($output);
	}

	/**
	 * Compiles an "include widget" tag
	 *
	 * @param string $widgetProperties Raw widget properties
	 * @param array &$widgets Widgets registry
	 * @return string Compiled code
	 * @access private
	 */
	function _compileWidgetInclude($widgetProperties, &$widgets) {
		$widgetData = $this->_parseWidgetProperties($widgetProperties);
		if ($widgetData) {
			// if no path is provided, then use the default path "php2go.gui"
			if (preg_match('/\w+/', $widgetData['path']))
				$widgetData['path'] = "php2go.gui.widget.{$widgetData['path']}";
			$widgets[$widgetData['path']] = TRUE;
			return $this->_compilePHPBlock(
				'$newWidget =& Widget::getInstance("' . $widgetData['path'] . '", ' . $widgetData['properties'] . '); ' .
				'if ($widget) { ' .
					'$newWidget->setParent($widget); ' .
				'} ' .
				'$newWidget->display();'
			);
		}
		return FALSE;
	}

	/**
	 * Compiles a "start widget" tag
	 *
	 * @param string $widgetProperties Raw widget properties
	 * @param array &$widgets Widgets registry
	 * @param string $controlBlock Active dynamic block
	 * @return string Compiled code
	 * @access private
	 */
	function _compileWidgetStart($widgetProperties, &$widgets, $controlBlock) {
		$widgetData = $this->_parseWidgetProperties($widgetProperties);
		if ($widgetData) {
			// if no path is provided, then use the default path "php2go.gui"
			if (preg_match('/\w+/', $widgetData['path']))
				$widgetData['path'] = "php2go.gui.widget.{$widgetData['path']}";
			$widgets[$widgetData['path']] = TRUE;
			$this->controlStack[] = array('START WIDGET', $controlBlock);
			return $this->_compilePHPBlock(
				'array_push($outputStack, array($widget)); ' .
				'$lastIndex = sizeof($outputStack)-1; ' .
				'$widget =& Widget::getInstance("' . $widgetData['path'] . '", ' . $widgetData['properties'] . '); ' .
				'if ($outputStack[$lastIndex][0]) { ' .
					'$newWidget->setParent($outputStack[$lastIndex][0]); ' .
				'} ' .
				'ob_start();'
			);
		}
		return FALSE;
	}

	/**
	 * Compiles an "end widget" tag
	 *
	 * @return string Compiled code
	 * @access private
	 */
	function _compileWidgetEnd() {
		return $this->_compilePHPBlock(
			'$widget->setContent(ob_get_clean()); ' .
			'$widget->display(); ' .
			'$last = array_pop($outputStack); ' .
			'$widget = $last[0];'
		);
	}

	/**
	 * Compiles a "capture" tag
	 *
	 * Examples:
	 * <code>
	 * <!-- capture -->
	 * <!-- capture name="my_capture" -->
	 * <!-- capture name="banner_capture" assign="banners" -->
	 * </code>
	 *
	 * @param string $captureProperties Raw capture properties
	 * @param string $controlBlock Active dynamic block
	 * @return string Compiled code
	 * @access private
	 */
	function _compileCaptureStart($captureProperties, $controlBlock) {
		$props = $this->_parseProperties($captureProperties, FALSE);
		$name = (isset($props['name']) ? $props['name'] : 'default');
		$assign = (isset($props['assign']) ? $props['assign'] : NULL);
		$this->controlStack[] = array('CAPTURE', $controlBlock);
		if (!empty($assign) && preg_match('~^' . TEMPLATE_VARIABLE_NAME . '$~', $assign))
			$output = 'array_push($outputStack, array("' . $name . '", "' . $assign . '"));';
		else
			$output = 'array_push($outputStack, array("' . $name . '"));';
		return $this->_compilePHPBlock("{$output} ob_start();");
	}

	/**
	 * Compiles an "end capture" tag
	 *
	 * @return string Compiled code
	 * @access private
	 */
	function _compileCaptureEnd() {
		return $this->_compilePHPBlock(
			'$last = array_pop($outputStack); ' .
			'if (is_array($last)) { ' .
				'if (isset($last[1])) { ' .
					'$block[$instance][\'vars\'][$last[1]] = ob_get_clean(); '.
				'} else { ' .
					'$this->tplCapture[$last[0]] = ob_get_clean(); '.
				'} '.
			'}'
		);
	}

	function _compileConfig($configProperties) {
		$props = $this->_parseProperties($configProperties);
		if (!isset($props['file'])) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_REQUIRED_ATTRIBUTE', array('file', 'CONFIG')), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$output = 'array(';
		foreach ($props as $key => $value)
			$output .= "'{$key}' => {$value},";
		$output = substr($output, 0, -1) . ')';
		return $this->_compilePHPBlock(
			'$this->_loadConfigVars(' . $output . ');'
		);
	}

	/**
	 * Parses tag properties written in the syntax
	 * "prop=val prop2=val2 ..."
	 *
	 * @param string $properties Raw properties string
	 * @param bool $compileToString Whether to compile property values to string
	 * @return array Compiled properties
	 * @access private
	 */
	function _parseProperties($properties, $compileToString=TRUE) {
		$match = array();
		preg_match_all('~(?:' . TEMPLATE_QUOTED_STRING . TEMPLATE_MODIFIER . '|' . TEMPLATE_NUMBER . '|(?:' . TEMPLATE_INNER_VARIABLE . '|' . TEMPLATE_CONFIG_VARIABLE . ')' . TEMPLATE_MODIFIER . '|(?>[^"\'=\s]+))+|[=]~m', $properties, $match);
		$tokens = $match[0];
		$props = array();
		$state = 0;
		foreach ($tokens as $token) {
			switch ($state) {
				case 0 :
					if (preg_match('~^\w+$~', $token)) {
						$propName = $token;
						$state = 1;
					} else {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_INVALID_ATTRIBUTENAME', $token), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
					break;
				case 1 :
					if ($token == '=') {
						$state = 2;
					} else {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_MISSING_ASSIGN'), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
					break;
				case 2 :
					$tokenParts = array();
					if ($token != '=') {
						if (preg_match('~^(on|yes|true|t)$~i', $token))
							$token = ($compileToString ? 'TRUE' : TRUE);
						elseif (preg_match('~^(off|no|false|f)$~i', $token))
							$token = ($compileToString ? 'FALSE' : FALSE);
						elseif (preg_match('~^(empty|null)$~i', $token))
							$token = ($compileToString ? 'NULL' : NULL);
						elseif (!$compileToString && preg_match('~^' . TEMPLATE_QUOTED_STRING . '$~', $token))
							$token = substr($token, 1, -1);
						elseif (preg_match('~^(' . TEMPLATE_INNER_VARIABLE . '|' . TEMPLATE_CONFIG_VARIABLE . '|' . TEMPLATE_QUOTED_STRING . ')' . TEMPLATE_MODIFIER . '$~', $token, $tokenParts))
							$token = $this->_compileVariable($tokenParts[1], $tokenParts[2]);
						elseif (!$compileToString && preg_match('~^' . TEMPLATE_NUMBER . '$~', $token))
							$token = floatval($token);
						elseif (!$compileToString && defined($token))
							$token = constant($compileToString);
						$props[$propName] = $token;
						$state = 0;
					} else {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_INVALID_ATTRIBUTE'), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
			}
		}
		return $props;
	}

	/**
	 * Parses the attributes of a widget, written in
	 * the syntax "prop=var prop2=var2 ..."
	 *
	 * @param string $widgetProperties Raw properties
	 * @return array Compiled properties
	 * @access private
	 */
	function _parseWidgetProperties($widgetProperties) {
		$props = $this->_parseProperties($widgetProperties);
		// the "path" property is mandatory
		if (!isset($props['path'])) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_REQUIRED_ATTRIBUTE', array('path', 'WIDGET')), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$path = substr($props['path'], 1, -1);
		unset($props['path']);
		$props['vars'] = '$block[$instance][\'vars\']';
		$output = 'array(';
		foreach ($props as $key => $value)
			$output .= '\'' . $key . '\'=>' . $value . ',';
		$output = substr($output, 0, -1) . ')';
		return array(
			'path' => $path,
			'properties' => $output
		);
	}

	/**
	 * Validates a tag declared in the template source
	 *
	 * @param string $tag Tag name
	 * @param string $tagArguments Tag arguments
	 * @param bool $needsArguments If this tag requires (TRUE), denies (FALSE) or ignore (NULL) arguments
	 * @param array $matchTags Matching tags, must be read from the top of the tag stack
	 * @param string $controlBlock Active dynamic block
	 * @return mixed Last tag data, or FALSE in case of errors
	 * @access private
	 */
	function _validateTag($tag, $tagArguments, $needsArguments, $matchTags=array(), $controlBlock) {
		$last = TRUE;
		if (!empty($matchTags)) {
			$last = array_pop($this->controlStack);
			if ($last === NULL || !in_array($last[0], $matchTags)) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNEXPECTED_TAG', $tag), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			if ($last[1] != $controlBlock) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_INCOMPLETE_BLOCKDEF', array($controlBlock, $last[0])), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
		}
		if ($needsArguments === TRUE && empty($tagArguments)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_MISSING_TAG_ARGS', $tag), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if ($needsArguments === FALSE && !empty($tagArguments)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_INVALID_TAG_ARGS', $tag), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		return $last;
	}

	/**
	 * Compiles a block of PHP code
	 *
	 * @param string $block Code block
	 * @access private
	 * @return string
	 */
	function _compilePHPBlock($block) {
		return ($this->controlFlags['shortOpenTag'] ? '<? ' : '<?php ') . $block . " ?>\n";
	}

	/**
	 * Resolve an I18n query during template compilation
	 *
	 * @param string $match Language entry
	 * @return string Entry value
	 * @access private
	 */
	function _i18nPreFilter($match) {
		return PHP2Go::getLangVal($match[1]);
	}
}
?>