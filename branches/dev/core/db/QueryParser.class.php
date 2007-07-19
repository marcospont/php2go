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
 * Parses database queries
 *
 * This class receives an SQL string and breaks it into the
 * most important parts: fields, tables, condition clause, grouping
 * clause, sorting clause and limit clause.
 *
 * @package db
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class QueryParser extends PHP2Go
{
	/**
	 * Query parts
	 *
	 * @var array
	 */
	var $parts = array();

	/**
	 * Class constructor
	 *
	 * @param string $sql Input SQL string
	 * @return QueryParser
	 */
	function QueryParser($sql) {
		parent::PHP2Go();
		$this->_parse($sql);
	}

	/**
	 * PHP5 getter
	 *
	 * @param string $part Part name: fields, tables, clause, groupby, orderby
	 * @return string|FALSE
	 */
	function __get($part) {
		return $this->getQueryPart($part);
	}

	/**
	 * Get a query part
	 *
	 * @param string $part Part name: fields, tables, clause, groupby, orderby
	 * @return string|FALSE
	 */
	function getQueryPart($part) {
		return (array_key_exists($part, $this->parts) ? $this->parts[$part] : FALSE);
	}

	/**
	 * Get all query parts
	 *
	 * @return array
	 */
	function &getQueryParts() {
		return $this->parts;
	}

	/**
	 * Parses the SQL string
	 *
	 * @param string $sql Input SQL string
	 * @access private
	 */
	function _parse($sql) {
		// initialize parts
		$this->parts = array(
			'fields' => FALSE,
			'tables' => FALSE,
			'clause' => FALSE,
			'groupby' => FALSE,
			'orderby' => FALSE,
			'limit' => FALSE
		);
		// prepare SQL to be parsed correctly
		$sql = trim($sql);
		while ($sql[0] == '(' && $sql[strlen($sql)-1] == ')')
			$sql = trim(substr($sql, 1, -1));
		$sql = preg_replace("/group\s+by/i", "group by", $sql);
		$sql = preg_replace("/order\s+by/i", "order by", $sql);
		// extract "limit M offset N" clause from the end of the query string
		$limitMatches = array();
		if (preg_match("/limit(\s+[0-9]+\s*((,|offset)\s*[0-9]+\s*)?)$/i", $sql, $limitMatches)) {
			$this->parts['limit'] = $limitMatches[1];
			$sql = preg_replace("/limit\s+[0-9]+\s*((,|offset)\s*[0-9]+\s*)?$/i", '', $sql);
		}
		// match all special tokens
		preg_match_all("/(\bselect\b|\bfrom\b|\bwhere\b|\bgroup\b|\border\b|\bunion\b|\bminus\b|\(|\))/i", $sql, $matches, PREG_OFFSET_CAPTURE);
		// initialize control variables
		$tokenStack = 0;
		$parenStack = 0;
		$selectParen = -1;
		$selectParenStack = array();
		$offsets = array(
			'fields' => -1,
			'tables' => -1,
			'clause' => -1,
			'groupby' => -1,
			'orderby' => -1
		);
		// traverse through all tokens
		$tokens = $matches[0];
		for ($i=0,$s=sizeof($tokens); $i<$s; $i++) {
			$word = $tokens[$i][0];
			$offset = $tokens[$i][1];
			switch (strtolower($word)) {
				case 'union' :
				case 'minus' :
					// set operations that aren't inside a subquery
					if ($this->parts['fields'] && $this->parts['tables'] && $parenStack == 0) {
						$this->parts['fields'] = ' _p2g_alias_.* ';
						$this->parts['tables'] = '(' . $sql . ') _p2g_alias_';
						$this->parts['clause'] = FALSE;
						$this->parts['groupby'] = FALSE;
						$this->parts['orderby'] = FALSE;
						$this->parts['limit'] = FALSE;
						return;
					}
				case 'select' :
					// if this is our first "select" keyword
					if ($tokenStack == 0 && !$this->parts['fields'])
						$offsets['fields'] = $offset+6;
					// save parenthesis stack value for the last select command
					$selectParenStack[] = $selectParen = $parenStack;
					// increase token stack
					$tokenStack++;
					break;
				case 'from' :
					// prevent against functions that use the "from" keyword (extract, substring)
					if ($selectParen == $parenStack) {
						$tokenStack--;
						// we found the most significative "from" keyword
						if ($tokenStack == 0 && !$this->parts['fields']) {
							$this->parts['fields'] = substr($sql, $offsets['fields'], $offset-$offsets['fields']);
							$offsets['tables'] = $offset+4;
						}
					}
					break;
				case '(' :
					$parenStack++;
					break;
				case ')' :
					if ($parenStack == $selectParen && !empty($selectParenStack)) {
						array_pop($selectParenStack);
						if (!empty($selectParenStack))
							$selectParen = $selectParenStack[sizeof($selectParenStack)-1];
						else
							$selectParen = -1;
					}
					$parenStack--;
					break;
				case 'where' :
					// this is the most significative "where" keyword if we already stored
					// the query fields and if the parenthesis are balanced
					if ($this->parts['fields'] && $parenStack == 0) {
						$this->parts['tables'] = substr($sql, $offsets['tables'], $offset-$offsets['tables']);
						$offsets['clause'] = $offset+5;
					}
					break;
				case 'group' :
					// this is the most significative "group by" keyword if we already stored
					// the query fields and tables and if the parenthesis are balanced
					if ($this->parts['fields'] && $parenStack == 0) {
						if (!$this->parts['tables'] && $offsets['tables'] != -1)
							$this->parts['tables'] = substr($sql, $offsets['tables'], $offset-$offsets['tables']);
						elseif (!$this->parts['clause'] && $offsets['clause'] != -1)
							$this->parts['clause'] = substr($sql, $offsets['clause'], $offset-$offsets['clause']);
						$offsets['groupby'] = $offset+8;
					}
					break;
				case 'order' :
					// this is the most significative "order by" keyword if we already stored
					// the query fields and tables and if the parenthesis are balanced
					if ($this->parts['fields'] && $parenStack == 0) {
						if (!$this->parts['tables'] && $offsets['tables'] != -1)
							$this->parts['tables'] = substr($sql, $offsets['tables'], $offset-$offsets['tables']);
						elseif (!$this->parts['clause'] && $offsets['clause'] != -1)
							$this->parts['clause'] = substr($sql, $offsets['clause'], $offset-$offsets['clause']);
						elseif (!$this->parts['groupby'] && $offsets['groupby'] != -1)
							$this->parts['groupby'] = substr($sql, $offsets['groupby'], $offset-$offsets['groupby']);
						$offsets['orderby'] = $offset+8;
					}
					break;
			}
		}
		// catch missing parts
		if ($offsets['fields'] != -1 && !$this->parts['fields'])
			$this->parts['fields'] = substr($sql, $offsets['fields']);
		elseif ($offsets['tables'] != -1 && !$this->parts['tables'])
			$this->parts['tables'] = substr($sql, $offsets['tables']);
		elseif ($offsets['clause'] != -1 && !$this->parts['clause'])
			$this->parts['clause'] = substr($sql, $offsets['clause']);
		elseif ($offsets['groupby'] != -1 && !$this->parts['groupby'])
			$this->parts['groupby'] = substr($sql, $offsets['groupby']);
		elseif ($offsets['orderby'] != -1 && !$this->parts['orderby'])
			$this->parts['orderby'] = substr($sql, $offsets['orderby']);
	}
}
?>