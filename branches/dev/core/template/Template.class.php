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

import('php2go.auth.User');
import('php2go.data.DataSet');
import('php2go.gui.Widget');
import('php2go.template.TemplateParser');

/**
 * Template processor class
 *
 * This class is the primary interface to deal with template files: text files
 * that can be transformed by a template engine. The template engine provided
 * by PHP2Go supports basic templating tools, like variables substitution and
 * include support, and advanced tools, such as condition/iteration tags, support
 * for function calls, capture areas, nested repetition blocks.
 *
 * To read more about the pattern understood by the template parser, please
 * consult the examples included in the framework's distribution.
 *
 * @package template
 * @uses CacheManager
 * @uses TemplateParser
 * @uses TypeUtils
 * @uses User
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Template extends Component
{
	/**
	 * Cache options
	 *
	 * @var array
	 * @access private
	 */
	var $cacheOptions = array();

	/**
	 * Current block name (defaults to {@link TP_ROOTBLOCK})
	 *
	 * @var string
	 * @access private
	 */
	var $currentBlockName;

	/**
	 * Points to the current dynamic block
	 *
	 * @var array
	 * @access private
	 */
	var $currentBlock = NULL;

	/**
	 * Config variables
	 *
	 * @var array
	 * @access private
	 */
	var $tplConfigVars = array(0 => array());

	/**
	 * Global template variables
	 *
	 * @var array
	 * @access private
	 */
	var $tplGlobalVars = array();

	/**
	 * Internal template variables
	 *
	 * @var array
	 * @access private
	 */
	var $tplInternalVars = array();

	/**
	 * Capture control variables
	 *
	 * @var array
	 * @access private
	 */
	var $tplCapture = array();

	/**
	 * Registered components
	 *
	 * @var array
	 * @access private
	 */
	var $tplComponents = array();

	/**
	 * Control structure that holds template's dynamic
	 * content: block instances and variables
	 *
	 * @var array
	 * @access private
	 */
	var $tplContent = array();

	/**
	 * Modified time of the template's source
	 *
	 * @var int
	 * @access private
	 */
	var $tplMTime;

	/**
	 * Parser instance
	 *
	 * @var object TemplateParser
	 * @access private
	 */
	var $Parser = NULL;

	/**
	 * TemplateConfigFile instance
	 *
	 * @var object TemplateConfigFile
	 * @access private
	 */
	var $ConfigLoader = NULL;

	/**
	 * Class constructor
	 *
	 * @param string $tplFile Template source (string or file name)
	 * @param int $type Source type ({@link T_BYFILE} or {@link T_BYVAR})
	 * @return Template
	 */
	function Template($tplFile, $type=T_BYFILE) {
		parent::Component();
		$this->Parser = new TemplateParser($tplFile, $type);
		$this->tplGlobalVars = array(
			'ldelim' => '{',
			'rdelim' => '}'
		);
		$this->tplInternalVars['loop'] = array();
		$this->tplInternalVars['user'] =& User::getInstance();
		$Conf =& Conf::getInstance();
		$this->tplInternalVars['conf'] =& $Conf->getAll();
		$this->cacheOptions['enabled'] = FALSE;
		$this->cacheOptions['group'] = 'php2goTemplate';
		$globalConf = $Conf->getConfig('TEMPLATES');
		if (is_array($globalConf))
			$this->_loadGlobalSettings($globalConf);
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Enable cache and configure cache properties
	 *
	 * @param string $dir Cache base dir
	 * @param int $lifeTime Cache lifetime in seconds
	 * @param bool $useMTime Whether to control cache based on original source's modified time
	 */
	function setCacheProperties($dir, $lifeTime=NULL, $useMTime=TRUE) {
		$this->cacheOptions['baseDir'] = $dir;
		if ($lifeTime)
			$this->cacheOptions['lifeTime'] = $lifeTime;
		$this->cacheOptions['useMTime'] = (bool)$useMTime;
		$this->cacheOptions['enabled'] = TRUE;
	}

	/**
	 * Set the tag delimiter type
	 *
	 * This template engine currently supports 2 types of tag delimiters:
	 * # {@link TEMPLATE_DELIM_COMMENT}: tags are surrounded by HTML comments.
	 * <code>
	 * <!-- if $var eq 1 -->
	 * <!-- loop var=$data item="item" -->
	 * </code>
	 * # {@link TEMPLATE_DELIM_BRACE}: tags are surrounded by curly braces
	 * <code>
	 * {if $var eq 1}
	 * {loop var=$data item="item"}
	 * </code>
	 *
	 * @param int $type Tag delimiter type
	 */
	function setTagDelimiter($type) {
		if ($type == TEMPLATE_DELIM_COMMENT || $type == TEMPLATE_DELIM_BRACE)
			$this->Parser->tagDelimType = $type;
	}

	/**
	 * Register a custom variable modifier
	 *
	 * Modifiers can be specified in 3 different ways:
	 * # 'function_name' or array('function_name'): a procedural function from an already included file
	 * # array('class', 'method'): a static method from an already included class
	 * # array('path.to.the.class', 'class', 'method'): a static method from a class that should be imported (path is provided as the first array entry)
	 *
	 * This method can't be used to replace modifiers bundled with PHP2Go.
	 *
	 * @param string $name Modifier name
	 * @param array $spec Modifier spec
	 */
	function addModifier($name, $spec) {
		if (!isset($this->Parser->tplModifiers[$name]))
			$this->Parser->tplModifiers[$name] = $spec;
	}

	/**
	 * Triggers the compilation of the template
	 *
	 * This method must be called manually before the template
	 * is populated with content (variables, blocks). If cache is enabled,
	 * a previously compiled template will be loaded from the cache storage.
	 *
	 * This method must be called only once.
	 *
	 * @uses TemplateParser::parse()
	 * @uses TemplateParser::getCacheData()
	 * @uses TemplateParser::loadCacheData()
	 */
	function parse() {
		if ($this->cacheOptions['enabled']) {
			import('php2go.cache.CacheManager');
			$Cache = CacheManager::factory('file');
			if ($this->Parser->tplBase['type'] == T_BYFILE)
				$cacheId = realpath($this->Parser->tplBase['src']);
			else
				$cacheId = dechex(crc32($this->Parser->tplBase['src']));
			if ($this->cacheOptions['useMTime']) {
				if (!isset($this->tplMTime) && $this->Parser->tplBase['type'] == T_BYFILE)
					$this->tplMTime = filemtime($this->Parser->tplBase['src']);
				$Cache->Storage->setLastValidTime($this->tplMTime);
			} elseif ($this->cacheOptions['lifeTime']) {
				$Cache->Storage->setLifeTime($this->cacheOptions['lifeTime']);
			}
			if ($this->cacheOptions['baseDir'])
				$Cache->Storage->setBaseDir($this->cacheOptions['baseDir']);
			$data = $Cache->load($cacheId, $this->cacheOptions['group']);
			if ($data && isset($data['parserVersion']) && $data['parserVersion'] == $this->Parser->parserVersion) {
				$this->Parser->loadCacheData($data);
			} else {
				$this->Parser->parse();
				$Cache->save($this->Parser->getCacheData(), $cacheId, $this->cacheOptions['group']);
			}
		} else {
			$this->Parser->parse();
		}
		$this->_initializeContent();
	}

	/**
	 * Returns the template to the state immediately after the compilation
	 *
	 * All assigned variables are cleared and all
	 * block instances are destroyed.
	 */
	function resetTemplate() {
		if ($this->isPrepared()) {
			$this->_initializeContent();
			$keys = array_keys($this->Parser->blockIndex);
			foreach ($keys as $block)
				$this->Parser->blockIndex[$block] = 0;
		}
	}

	/**
	 * Checks if the template was already compiled
	 *
	 * @return bool
	 */
	function isPrepared() {
		return $this->Parser->prepared;
	}

	/**
	 * Get all declared blocks
	 *
	 * Returns FALSE when the template wasn't compiled yet.
	 *
	 * @return array
	 */
	function getDefinedBlocks() {
		if ($this->Parser->prepared)
			return array_keys($this->Parser->tplDef);
		return FALSE;
	}

	/**
	 * Checks if a given block name is defined in the template source
	 *
	 * The $block argument can be either a block name or a path
	 * in the blocks tree. Examples:
	 * <code>
	 * /* tpl file {@*}
	 * <!-- start block : row -->
	 * <tr>
	 * <!-- start block : column -->
	 *   <td>{$data}</td>
	 * <!-- end block : column -->
	 * </tr>
	 * <!-- end block : row -->
	 * /* php file {@*}
	 * $tpl = new Template('my_template.tpl');
	 * $def = $tpl->isBlockDefined('row');
	 * $def2 = $tpl->isBlockDefined('row.column');
	 * </code>
	 *
	 * @param string $block Block name
	 * @return bool
	 */
	function isBlockDefined($block) {
		if ($this->Parser->prepared) {
			$parts = explode('.', $block);
			if (sizeof($parts) == 1) {
				return (isset($this->Parser->tplDef[$block]));
			} else {
				$i = 1;
				$ptr = $this->Parser->tplDef[$parts[0]];
				while ($i < sizeof($parts)) {
					if (!array_key_exists($parts[$i], $ptr['blocks']))
						return FALSE;
					$ptr = @$this->Parser->tplDef[$parts[$i]];
					$i++;
				}
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Get all variables defined in a given block
	 *
	 * If $blockName is missing, {@link TP_ROOTBLOCK} will be used.
	 *
	 * Returns FALSE when the template wasn't compiled yet.
	 *
	 * @param string $blockName Block name
	 * @return array|bool
	 */
	function getDefinedVariables($blockName=NULL) {
		if ($this->Parser->prepared) {
			if (!empty($blockName)) {
				if (!isset($this->Parser->tplDef[$blockName]))
					return NULL;
			} else {
				$blockName = TP_ROOTBLOCK;
			}
			return $this->Parser->tplDef[$blockName]['vars'];
		}
		return FALSE;
	}

	/**
	 * Checks if a given variable name is declared
	 *
	 * The $variable argument can be either a variable name
	 * or a 'block.variable' expression. Examples:
	 * <code>
	 * /* tpl file {@*}
	 * {$title}
	 * <!-- start block : row -->
	 * <tr>
	 *   <td>{$line}</td>
	 * <!-- start block : column -->
	 *   <td>{$data}</td>
	 * <!-- end block : column -->
	 * </tr>
	 * <!-- end block : row -->
	 * /* php file {@*}
	 * $tpl = new Template('my_template.tpl');
	 * $tpl->parse();
	 * $def = $tpl->isVariableDefined('title');
	 * $def2 = $tpl->isVariableDefined('row.line');
	 * $def3 = $tpl->isVariableDefined('column.data');
	 * </code>
	 *
	 * @param string $variable Variable name or path
	 * @return bool
	 */
	function isVariableDefined($variable) {
		if ($this->Parser->prepared) {
			if (sizeof($regs = explode('.', $variable)) == 2) {
				if (!isset($this->Parser->tplDef[$regs[0]])) {
					return FALSE;
				} else {
					$blockName = $regs[0];
					$variable = $regs[1];
				}
			} else {
				$blockName = TP_ROOTBLOCK;
			}
			return (array_search($variable, $this->Parser->tplDef[$blockName]['vars']) !== FALSE);
		}
		return FALSE;
	}

	/**
	 * Get the current value of a variable
	 *
	 * Just as {@link isVariableDefined}, this method accepts
	 * a 'block.variable' expression in the $variable argument.
	 *
	 * @param string $variable Variable name or path
	 * @return mixed
	 */
	function getValue($variable) {
        if (sizeof($regs = explode('.', $variable)) == 2) {
			if (isset($this->Parser->tplDef[$regs[0]])) {
				$block =& $this->_getLastInstance($regs[0]);
				$variable = $regs[1];
			}
        } else {
            $block =& $this->currentBlock;
        }
		return @$block['vars'][$variable];
	}

	/**
	 * Get the name of the active block
	 *
	 * @return string
	 */
	function getCurrentBlockName() {
		return $this->currentBlockName;
	}

	/**
	 * Creates a new instance of a dynamic block
	 *
	 * The dynamic block must be declared in the template source
	 * using the following syntax:
	 * <code>
	 * <!-- start block : block_name -->
	 * <!-- end block : block_name -->
	 * </code>
	 *
	 * The recently created block is transformed into the active block,
		 * so that all subsequent assign operations will be applied on it
	 *
	 * @param string $block Block name
	 */
	function createBlock($block) {
		// checks if block exists
		if (!isset($this->Parser->tplDef[$block]))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $block), E_USER_ERROR, __FILE__, __LINE__);
		// prevent blocks with the same name of the root block
		if ($block == TP_ROOTBLOCK)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_REPLICATE_ROOT_BLOCK'), E_USER_ERROR, __FILE__, __LINE__);
		// get the last instance of the parent block
		$parent =& $this->_getLastInstance($this->Parser->blockParent[$block]);
		// already instantiated or not
		if (!isset($parent['blocks'][$block])) {
			$this->Parser->blockIndex[$block]++;
			$index = "{$block}:{$this->Parser->blockIndex[$block]}";
			$parent['blocks'][$block] = $index;
			$this->tplContent[$index] = array();
		} else {
			$index = $parent['blocks'][$block];
		}
		// creates and initializes the new instance
		$nextInstance = sizeof($this->tplContent[$index]);
		$this->tplContent[$index][$nextInstance] = array(
			'vars' => array(),
			'blocks' => array()
		);
		// set as current block
		$this->currentBlockName = $block;
		$this->currentBlock =& $this->tplContent[$index][$nextInstance];
	}

	/**
	 * Changes the internal pointer to a given block name
	 *
	 * Example:
	 * <code>
	 * /* tpl file {@*}
	 * {$out}
	 * <!-- start block : internal -->
	 * {$var}
	 * <!-- end block : internal -->
	 * /* php file {@*}
	 * $tpl = new Template('my_template.php');
	 * $tpl->parse();
	 * $tpl->createBlock('internal');
	 * $tpl->assign('var', 'blah');
	 * $tpl->setCurrentBlock(TP_ROOTBLOCK);
	 * $tpl->assign('out', 'Hello World!');
	 * </code>
	 *
	 * @param string $block Block name
	 */
	function setCurrentBlock($block) {
		if (!isset($this->Parser->tplDef[$block]) || ($block != TP_ROOTBLOCK && $this->Parser->blockIndex[$block] == 0))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $block), E_USER_ERROR, __FILE__, __LINE__);
		$this->currentBlockName = $block;
		$this->currentBlock =& $this->_getLastInstance($block);
	}

	/**
	 * Creates a new instance of a given block and
	 * assigns a variable or a set of variables
	 *
	 * @param string $blockName Block name
	 * @param string|array $variable Variable name or hashmap of variables and values
	 * @param mixed $value Variable value
	 */
	function createAndAssign($blockName, $variable, $value='') {
		$this->createBlock($blockName);
		$this->assign($variable, $value);
	}

	/**
	 * Assigns a variable
	 *
	 * The $variable argument can be a hashmap of variables and values,
	 * a simple variable name (and so, must be present in the current
	 * active block) or a 'block.variable' expression, which allows to
	 * assign variables on the most recent instance of a different block
	 * (not the current one).
	 *
	 * @param string|array $variable Variable name or path or hashmap or variables and values
	 * @param mixed $value Variable value
	 */
	function assign($variable, $value='') {
		if (is_array($variable)) {
			foreach ($variable as $name => $value)
				$this->_assign($name, $value);
		} else {
			$this->_assign($variable, $value);
		}
	}

	/**
	 * Assigns a variable by reference
	 *
	 * The $variable argument can be a variable name
	 * or a 'block.variable' expression.
	 *
	 * @param string $variable Variable name
	 * @param mixed &$value Variable reference
	 */
	function assignByRef($variable, &$value) {
		if (sizeof($parts = explode('.', $variable)) == 2) {
			if (isset($this->Parser->tplDef[$parts[0]])) {
				if (TypeUtils::isInstanceOf($value, 'Component'))
					$this->tplComponents[$this->_getFullPath($parts[0], $parts[1])] =& $value;
				$block =& $this->_getLastInstance($parts[0]);
				$block['vars'][$parts[1]] =& $value;
			}
		} else {
			if (TypeUtils::isInstanceOf($value, 'Component'))
				$this->tplComponents[$this->_getFullPath($this->currentBlockName, $variable)] =& $value;
			$block =& $this->currentBlock;
			$block['vars'][$variable] =& $value;
		}
	}

	/**
	 * Assigns a global template variable
	 *
	 * Global template variables are available in all scopes. This means
	 * that, no matter which is the current block, you'll be able to
	 * use it.
	 *
	 * @param string|array $variable Variable name or hashmap of variables and values
	 * @param mixed $value Variable value
	 */
	function globalAssign($variable, $value='') {
		if (is_array($variable)) {
			foreach ($variable as $name => $value)
				$this->_globalAssign($name, $value);
		} else {
			$this->_globalAssign($variable, $value);
		}
	}

	/**
	 * Assigns an include block
	 *
	 * The assignment of include blocks must be done before the
	 * {@link parse()} method is called.
	 *
	 * Example:
	 * <code>
	 * /* tpl file {@*}
	 * <!-- include block : include1 -->
	 * <!-- include block : include2 -->
	 * /* php file {@*}
	 * $tpl = new Template('templates/my_template.tpl');
	 * $tpl->includeAssign('include1', 'templates/my_include1.tpl', T_BYFILE);
	 * $tpl->includeAssign('include2', 'templates/my_include2.tpl', T_BYFILE);
	 * $tpl->parse();
	 * </code>
	 *
	 * @param string $blockName Include block name
	 * @param string $value Include block contents (file path or variable)
	 * @param int $type Content type({@link T_BYFILE} or {@link T_BYVAR})
	 */
	function includeAssign($blockName, $value, $type=T_BYFILE) {
		if (!empty($value) && ($type == T_BYFILE || $type == T_BYVAR)) {
			if ($type == T_BYFILE && !is_readable($value))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $value), E_USER_ERROR, __FILE__, __LINE__);
			$this->Parser->tplIncludes[$blockName] = array($value, $type);
		}
	}

	/**
	 * Prepares the template to be rendered
	 *
	 * Automatically called inside {@link getContent} and {@link display}.
	 */
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			$keys = array_keys($this->tplComponents);
			foreach ($keys as $key) {
				$component =& $this->tplComponents[$key];
				if (!$component->preRendered)
					$component->onPreRender();
			}
		}
	}

	/**
	 * Builds and returns the template's output code
	 *
	 * @return string
	 */
	function getContent() {
		$this->onPreRender();
		//highlight_string($this->Parser->tplBase['compiled']);
		ob_start();
		eval('?>' . $this->Parser->tplBase['compiled']);
		return ob_get_clean();
	}

	/**
	 * Builds and displays the template's output code
	 */
	function display() {
		$this->onPreRender();
		highlight_string($this->Parser->tplBase['compiled']);
		eval('?>' . $this->Parser->tplBase['compiled']);
	}

	/**
	 * Initializes the internal control variables
	 *
	 * @access private
	 */
	function _initializeContent() {
		$this->tplContent = array(
			TP_ROOTBLOCK . ':0' => array(
				0 => array(
					'vars' => array(),
					'blocks' => array()
				)
			)
		);
		$this->currentBlockName = TP_ROOTBLOCK;
		$this->currentBlock =& $this->tplContent[TP_ROOTBLOCK . ':0'][0];
	}

	/**
	 * Internal method used to assign variables
	 *
	 * @param string $variable Variable name
	 * @param mixed $value Variable value
	 * @access private
	 */
	function _assign($variable, $value) {
		if (sizeof($parts = explode('.', $variable)) == 2) {
			if (isset($this->Parser->tplDef[$parts[0]])) {
				if (TypeUtils::isInstanceOf($value, 'Component'))
					$this->tplComponents[$this->_getFullPath($parts[0], $parts[1])] = $value;
				$block =& $this->_getLastInstance($parts[0]);
				$block['vars'][$parts[1]] = $value;
			}
		} else {
			if (TypeUtils::isInstanceOf($value, 'Component'))
				$this->tplComponents[$this->_getFullPath($this->currentBlockName, $variable)] = $value;
			$block =& $this->currentBlock;
			$block['vars'][$variable] = $value;
		}
	}

	/**
	 * Internal method used to assign global variables
	 *
	 * @param string $variable Variable name
	 * @param mixed $value Variable value
	 * @access private
	 */
	function _globalAssign($variable, $value) {
		if (TypeUtils::isInstanceOf($value, 'Component'))
			$this->tplComponents["global:{$variable}"] =& $value;
		$this->tplGlobalVars[$variable] = $value;
	}

	/**
	 * Given a block name, returns its last instance
	 *
	 * @param string $blockName
	 * @access private
	 * @return array
	 */
	function &_getLastInstance($blockName) {
		$index = "$blockName:{$this->Parser->blockIndex[$blockName]}";
		$lastInstanceKey = sizeof($this->tplContent[$index]) - 1;
		$lastInstance =& $this->tplContent[$index][$lastInstanceKey];
		return $lastInstance;
	}

	/**
	 * Builds a full path of a variable, considering block index,
	 * block name, block instance and variable name
	 *
	 * @param string $block Block name
	 * @param string $variable Variable name
	 * @access private
	 * @return string
	 */
	function _getFullPath($block, $variable) {
		$index = "$block:{$this->Parser->blockIndex[$block]}";
		$lastInstanceKey = sizeof($this->tplContent[$index]) - 1;
		return "{$index}:{$lastInstanceKey}:{$variable}";
	}

	/**
	 * Used at runtime to push a dynamic block onto the block stack
	 *
	 * @param array &$stack Block stack
	 * @param string $blockName Block name
	 * @param array &$block Block data
	 * @param int $instance Current block instance
	 * @param int $instanceCount Total instances
	 * @access private
	 */
	function _pushStack(&$stack, $blockName, &$block, $instance, $instanceCount) {
		$newItem = array();
		$newItem[0] = $blockName;
		$newItem[1] =& $block;
		$newItem[2] = $instance;
		$newItem[3] = $instanceCount;
		$stack[sizeof($stack)] =& $newItem;
	}

	/**
	 * Used at runtime to pop a dynamic block from the block stack
	 *
	 * @param array &$stack Block stack
	 * @param string &$blockName Block name
	 * @param int &$instance Current instance
	 * @param int &$instanceCount Total instances
	 * @return array Block data
	 * @access private
	 */
	function &_popStack(&$stack, &$blockName, &$instance, &$instanceCount) {
		$lastItem =& $stack[sizeof($stack)-1];
		$blockName = $lastItem[0];
		$instance = $lastItem[2];
		$instanceCount = $lastItem[3];
		array_pop($stack);
		return $lastItem[1];
	}

	/**
	 * Get the next item of a given loop
	 *
	 * The loop can be an array, a hashmap, a DataSet instance
	 * or an ADORecordSet instance. For each type, there's a
	 * different way to fetch and return the next record.
	 *
	 * @param array|object &$loop Loop
	 * @param bool $returnKey Whether to return an array containing key and value or just the value
	 * @access private
	 * @return mixed
	 */
	function _getLoopItem(&$loop, $returnKey=FALSE) {
		if (is_array($loop)) {
			if ($returnKey) {
				return each($loop);
			} else {
				$item = current($loop);
				next($loop);
				return $item;
			}
		} elseif (TypeUtils::isInstanceOf($loop, 'DataSet') && !$loop->eof()) {
			$key = $loop->getAbsolutePosition();
			$value = $loop->fetch();
			return ($returnKey ? array($key, $value) : $value);
		} elseif (TypeUtils::isInstanceOf($loop, 'ADORecordSet') && !$loop->EOF) {
			$key = $loop->absolutePosition();
			$value = $loop->fetchRow();
			return ($returnKey ? array($key, $value) : $value);
		}
		return FALSE;
	}

	/**
	 * Calculate the total iterations of a given loop
	 *
	 * @param array|object $loop
	 * @access private
	 * @return int
	 */
	function _getLoopTotal($loop) {
		if (is_array($loop)) {
			return sizeof($loop);
		} elseif (TypeUtils::isInstanceOf($loop, 'DataSet')) {
			return $loop->getRecordCount();
		} elseif (TypeUtils::isInstanceOf($loop, 'ADORecordSet')) {
			return $loop->recordCount();
		} else {
			return 0;
		}
	}

	/**
	 * Load a set of config variables
	 *
	 * @param array $props Properties from the CONFIG tag
	 * @access private
	 */
	function _loadConfigVars($props) {
		if (!isset($this->ConfigLoader)) {
			import('php2go.template.TemplateConfigFile');
			$this->ConfigLoader = new TemplateConfigFile($this, array(
				'caseSensitive' => @$props['caseSensitive'],
				'booleanize' => @$props['booleanize']
			));
		}
		$scope = @$props['scope'];
		switch ($scope) {
			case 'parent' :
				$this->tplConfigVars[1] = array_merge($this->tplConfigVars[1], $this->ConfigLoader->get($props['file'], @$props['section']));
				break;
			case 'global' :
				for ($i=0,$s=sizeof($this->tplConfigVars); $i<$s; $i++)
					$this->tplConfigVars[$i] = array_merge($this->tplConfigVars[$i], $this->ConfigLoader->get($props['file'], @$props['section']));
				break;
			default :
				$this->tplConfigVars[0] = array_merge($this->tplConfigVars[0], $this->ConfigLoader->get($props['file'], @$props['section']));
				break;
		}
	}

	/**
	 * Loads global configuration settings
	 *
	 * @param array $settings Settings
	 * @access private
	 */
	function _loadGlobalSettings($settings) {
		if (is_array($settings['CACHE'])) {
			// cache properties
			if (isset($settings['CACHE']['DIR']))
				$this->setCacheProperties($settings['CACHE']['DIR'], @$settings['CACHE']['LIFETIME'], @$settings['CACHE']['USEMTIME']);
			// don't change tag delimiter of internal templates
			$path = realpath($this->Parser->tplBase['src']);
			if (!$path || strpos(str_replace("\\", "/", $path), PHP2GO_ROOT) === 0)
				$this->setTagDelimiter(@$settings['TAG_DELIMITER_TYPE']);
			// custom modifiers
			foreach ((array)$settings['MODIFIERS'] as $name => $spec) {
				if (!isset($this->Parser->tplModifiers[$name]))
					$this->Parser->tplModifiers[$name] = $spec;
			}
		}
	}
}
?>