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

import('php2go.file.FileSystem');
import('php2go.template.Template');

/**
 * Extendeds base template class adding buffering capability and some utility methods
 *
 * @package template
 * @uses ADORecordSet
 * @uses Db
 * @uses FileSystem
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DocumentElement extends Template
{
	/**
	 * Content buffer
	 *
	 * @access protected
	 * @var string
	 */
	var $contentBuffer = '';

	/**
	 * Class constructor
	 *
	 * @return DocumentElement
	 */
	function DocumentElement() {
		parent::Template('', T_BYVAR);
	}

	/**
	 * Factory method. Builds an instance of the class
	 * based on 2 arguments: $src and $type
	 *
	 * @param string $src Element string content or file path
	 * @param int $type {@link T_BYVAR} or {@link T_BYFILE}
	 * @return DocumentElement
	 * @static
	 */
	function &factory($src, $type=T_BYFILE) {
		$Element = new DocumentElement();
		$Element->put($src, $type);
		$Element->parse();
		return $Element;
	}

	/**
	 * Get current content buffer
	 *
	 * @return string
	 */
	function getContentBuffer() {
		return $this->contentBuffer;
	}

	/**
	 * Adds content or a file in the element's buffer
	 *
	 * You can fill the element's buffer until {@link parse()}
	 * is called. After the template is parsed, no content can
	 * be added anymore.
	 *
	 * @param string $content Content or file path
	 * @param int $contentType Content type ({@link T_BYVAR} or {@link T_BYFILE})
	 */
	function put($content, $contentType = T_BYVAR) {
		if (parent::isPrepared()) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_PUT_ON_PREPARED_TEMPLATE'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			if ($contentType == T_BYFILE) {
				// adds file contents in the buffer
				$this->contentBuffer .= FileSystem::getContents($content);
				// renew last modified date (caching purposes)
				$mtime = FileSystem::lastModified($content, TRUE);
				if (!isset($this->tplMTime) || $mtime > $this->tplMTime)
					$this->tplMTime = $mtime;
			} elseif ($contentType == T_BYVAR) {
				$this->contentBuffer .= $content;
			}
		}
	}

	/**
	 * Overrides parent class implementation to run the parser
	 * with the contents of the buffer
	 */
	function parse() {
		$saveIncludeData = $this->Parser->tplIncludes;
		$this->Parser = new TemplateParser($this->contentBuffer, T_BYVAR);
		$this->Parser->tplIncludes = $saveIncludeData;
		parent::parse();
	}

	/**
	 * Populates a template variable based on the first
	 * cell of the results of a database query or statement
	 *
	 * @param string $variable Variable name
	 * @param mixed $sqlStmt Query or statement
	 * @param string $connectionId DB connection ID
	 * @return bool
	 */
	function assignFromQuery($variable, $sqlStmt, $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		if (parent::isVariableDefined($variable)) {
        	parent::assign($variable, $Db->getFirstCell($sqlStmt));
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Generates instances of a dynamic block based
	 * on the results of a database query or statement
	 *
	 * Each row in the result set will represent a block instance,
	 * and its fields will be assigned as local variables of the
	 * block.
	 *
	 * @param string $blockName Block name
	 * @param mixed $sqlStmt Query or statement
	 * @param string $connectionId DB connection ID
	 */
	function generateFromQuery($blockName, $sqlStmt, $connectionId=NULL) {
		if (!parent::isBlockDefined($blockName)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $blockName), E_USER_ERROR, __FILE__, __LINE__);
		} else if ($blockName == TP_ROOTBLOCK) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_REPLICATE_ROOT_BLOCK'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$Db =& Db::getInstance($connectionId);
			$oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
			if ($Rs =& $Db->query($sqlStmt)) {
				$saveCurrentBlock = parent::getCurrentBlockName();
				while (!$Rs->EOF) {
					parent::createAndAssign($blockName, $Rs->fields);
					$Rs->moveNext();
				}
				parent::setCurrentBlock($saveCurrentBlock);
			}
			$Db->setFetchMode($oldMode);
		}
	}

	/**
	 * Apply the contents of a data set in the template
	 *
	 * This method expects 3 names of dynamic blocks. The first
	 * is the container block, which will be created if the data
	 * set is not empty. The second is the empty block, which
	 * will be created for empty data sets. The third and last
	 * block will be repeated for all records of the data set.
	 *
	 * Example:
	 * <code>
	 * /* in your template {@*}
	 * <!-- start block : container_block -->
	 * Field1, Field2
	 * <!-- start block : repeater_block -->
	 * {$field1}, {$field2}
	 * <!-- end block : repeater_block -->
	 * <!-- end block : container_block -->
	 * <!-- start block : empty_block -->
	 * ( put here a message stating that the data set is empty )
	 * <!-- end block : empty_block -->
	 *
	 * /* in your PHP file {@*}
	 * $element = new DocumentElement();
	 * $element->put('mytemplate.tpl', T_BYFILE);
	 * $dataset =& DataSet::factory('db');
	 * $dataset->load('select field1, field2 from my_table');
	 * $element->generateFromDataSet($dataset, 'container_block', 'empty_block', 'repeater_block');
	 * </code>
	 *
	 * @param DataSet $DataSet Data set
	 * @param string $containerBlock Container block
	 * @param string $emptyBlock Empty block
	 * @param string $repeaterBlock Repeater block
	 */
	function generateFromDataSet($DataSet, $containerBlock, $emptyBlock, $repeaterBlock) {
		if ($containerBlock == TP_ROOTBLOCK || $emptyBlock == TP_ROOTBLOCK || $repeaterBlock == TP_ROOTBLOCK) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_REPLICATE_ROOT_BLOCK'), E_USER_ERROR, __FILE__, __LINE__);
		}
		if (TypeUtils::isInstanceOf($DataSet, 'DataSet')) {
			$saveCurrentBlock = parent::getCurrentBlockName();
			if ($DataSet->getRecordCount() > 0) {
				if (!parent::isBlockDefined($containerBlock))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $containerBlock), E_USER_ERROR, __FILE__, __LINE__);
				parent::createBlock($containerBlock);
				if (!parent::isBlockDefined($repeaterBlock))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $repeaterBlock), E_USER_ERROR, __FILE__, __LINE__);
				while (!$DataSet->eof()) {
					parent::createAndAssign($repeaterBlock, $DataSet->current());
					$DataSet->moveNext();
				}
			} else {
				if (!parent::isBlockDefined($emptyBlock))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $emptyBlock), E_USER_ERROR, __FILE__, __LINE__);
				parent::createBlock($emptyBlock);
			}
			parent::setCurrentBlock($saveCurrentBlock);
		}
	}
}
?>