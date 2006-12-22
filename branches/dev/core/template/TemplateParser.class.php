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

import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');
import('php2go.util.json.JSONEncoder');

/**
 * Name of the root block of a template
 */
define('TP_ROOTBLOCK', '_ROOT');
/**
 * Tag delimiter based on HTML comments
 */
define('TEMPLATE_DELIM_COMMENT', 1);
/**
 * Tag delimiter based on curly braces
 */
define('TEMPLATE_DELIM_BRACE', 2);
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
 * Functions pattern
 */
define('TEMPLATE_FUNCTION', '[a-zA-Z_]\w*(::[a-zA-Z_]\w*)?');
/**
 * Variable modifier pattern
 */
define('TEMPLATE_MODIFIER', '((?:\|@?\w+(?::(?:\w+|' . TEMPLATE_INNER_VARIABLE . '|' . TEMPLATE_NUMBER . '|' . TEMPLATE_QUOTED_STRING .'))*)*)');
/**
 * Dynamic blocks pattern
 */
define('TEMPLATE_BLOCK', '(START|END|INCLUDE|INCLUDESCRIPT|REUSE) BLOCK : ([a-zA-Z0-9_\.\-\\\/\~\s]+)');
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

//!-----------------------------------------------------------------
// @class		TemplateParser
// @desc		A classe TemplateParser é utilizada na interpretação de templates,
//				e na construção de uma estrutura de dados contendo as definições e declarações
// @package		php2go.template
// @extends		PHP2Go
// @uses		FileSystem
// @author		Marcos Pont
// @version		$Revision: 1.30 $
//!-----------------------------------------------------------------
class TemplateParser extends PHP2Go
{
	var $tplBase = array();			// @var tplBase array			"array()" Armazena o conteúdo, o tipo e a versão compilada do template base
	var $tplDef = array();			// @var tplDef array			"array()" Conjunto de blocos definidos no template, cada qual com suas variáveis
	var $tplIncludes = array();		// @var tplIncludes array		"array()" Conjunto de arquivos/templates de inclusão
	var $tplModifiers = array();	// @var tplModifiers array		"array()" Conjunto de modificadores de variáveis
	var $tplWidgets = array();		// @var tplWidgets array		"array()" Conjunto de widgets declarados no template
	var $tagDelimType;				// @var tagDelimType int		Tipo de delimitador de tags
	var $prepared = FALSE;			// @var prepared bool			"FALSE" Indica se o template já foi preparado/compilado
	var $blockParent = array();		// @var blockParent array		"array()" Armazena a estrutura de relacionamento entre blocos
	var $blockIndex = array();		// @var blockIndex array		"array()" Armazena a estrutura de índice de instâncias de bloco
	var $blockStack = array();		// @var blockStack array		"array()" Armazena a pilha de definição de blocos, utilizada na compilação
	var $controlFlags = array();	// @var controlFlags array		"array()" Flags de controle para o processo de compilação
	var $controlStack = array();	// @var controlStack array		"array()" Armazena a pilha de estruturas de controle, utilizada na compilação
	var $loopNestingLevel = 0;		// @var loopNestingLevel int	"0" Nível de aninhamento de loops no template
	var $parserVersion;				// @var parserVersion string	Versão do parser, para que um template em cache não possua uma estrutura diferente da gerada pelo parser

	//!-----------------------------------------------------------------
	// @function	TemplateParser::TemplateParser
	// @desc		Construtor da classe
	// @param		value string	Nome do arquivo ou conteúdo string para o template
	// @param		type int		T_BYFILE (arquivo) ou T_BYVAR (string)
	// @access		public
	//!-----------------------------------------------------------------
	function TemplateParser($value, $type) {
		parent::PHP2Go();
		if ($type != T_BYFILE && $type != T_BYVAR)
			$type = T_BYVAR;
		$this->tplBase = array(
			'src' => $value,
			'compiled' => NULL,
			'type' => $type
		);
		$this->tplDef = array(
			TP_ROOTBLOCK => array(
				'vars' => array(),
				'blocks' => array()
			)
		);
		$this->tplModifiers = include(PHP2GO_ROOT . 'core/template/templateModifiers.php');
		$this->tagDelimType = TEMPLATE_DELIM_COMMENT;
		$this->blockIndex[TP_ROOTBLOCK] = 0;
		$this->controlFlags['ignore'] = FALSE;
		$this->controlFlags['loop'] = FALSE;
		$this->controlFlags['shortOpenTag'] = (bool)System::getIni('short_open_tag');
		$this->parserVersion = '$Revision: 1.30 $';
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::getCacheData
	// @desc		Retorna o conjunto de dados para gravação de cache
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getCacheData() {
		return array(
			'compiled' => $this->tplBase['compiled'],
			'tplDef' => $this->tplDef,
			'blockIndex' => $this->blockIndex,
			'blockParent' => $this->blockParent,
			'parserVersion' => $this->parserVersion
		);
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::loadCacheData
	// @desc		Carrega para a classe informações carregadas da cache,
	//				evitando que o conteúdo do template seja interpretado novamente
	// @param		data array	Dados carregados do arquivo de cache
	// @access		public
	//!-----------------------------------------------------------------
	function loadCacheData($data) {
		$this->tplBase['compiled'] = $data['compiled'];
		$this->tplDef = $data['tplDef'];
		$this->blockIndex = $data['blockIndex'];
		$this->blockParent = $data['blockParent'];
		$this->prepared = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::parse
	// @desc		Executa a interpretação do conteúdo do template
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function parse() {
		if (!$this->prepared) {
			// pré-compilação
			$src = $this->_prepareTemplate($this->tplBase['src'], $this->tplBase['type']);
			// compilação
			$controlBlock = TP_ROOTBLOCK;
			$compiled = $this->_compilePHPBlock(sprintf(
				'$stack = array(); ' .
				'$outputStack = array();' .
				'$blockName = "%s"; ' .
				'$block =& $this->tplContent["%s:0"]; ' .
				'$instance = 0; ' .
				'$instanceCount = 1; ' .
				'$widget = NULL; ' .
				'$this->_prepareBlock($block[$instance]); ',
				TP_ROOTBLOCK, TP_ROOTBLOCK
			));
			$compiled .= strval($this->_parseTemplate($src, $controlBlock));
			$compiled = preg_replace('/\s*\?>\n<\?(php)?\s*/', ' ', $compiled);
			if (substr($compiled, -1) == "\n")
				$compiled = substr($compiled, 0, -1);
			// balanceamento de blocos de repetição
			if (!empty($this->blockStack)) {
				$last = array_pop($this->blockStack);
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_BLOCKDEF', $last), E_USER_ERROR, __FILE__, __LINE__);
			// balanceamento de estruturas de controle
			} elseif (!empty($this->controlStack)) {
				$last = array_pop($this->controlStack);
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_TAG', $last[0]), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$this->tplBase['compiled'] = $compiled;
				$this->prepared = TRUE;
			}
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TEMPLATE_ALREADY_PREPARED'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_prepareTemplate
	// @desc		Executa a etapa de pré-processamento de um template
	// @note		A tarefa de resolução de variáveis de linguagem é realizada nesta etapa
	// @param		value string	Nome de arquivo ou conteúdo string
	// @param		type int		T_BYFILE (arquivo) ou T_BYVAL (string)
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _prepareTemplate($value, $type) {
		if ($type == T_BYFILE) {
			if (empty($value))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_TEMPLATE_FILE'), E_USER_ERROR, __FILE__, __LINE__);
			$src = @file_get_contents($value);
			if ($src === FALSE)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $value), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$src = $value;
		}
		$src = preg_replace_callback(PHP2GO_I18N_PATTERN, array($this, '_i18nPreFilter'), $src);
		return $src;
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_parseTemplate
	// @desc		Método de interpretação de um arquivo template
	// @param		src string				Código pré-processado do template
	// @param		&controlBlock string	Nome do bloco ativo
	// @return		bool
	//!-----------------------------------------------------------------
	function _parseTemplate($src, &$controlBlock) {
		$tagBlocks = array();
		$tplOutput = array();
		switch ($this->tagDelimType) {
			case TEMPLATE_DELIM_BRACE :
				$tagStartDelim = '{';
				$tagEndDelim = '}';
				break;
			default :
				$tagStartDelim = '<!--';
				$tagEndDelim = '-->';
				break;
		}
		// busca todas as tags e todos os blocos de código complementares
		$matches = array();
		preg_match_all("~(\r?\n?[\s]*){$tagStartDelim}\s*(.*?)\s*{$tagEndDelim}|{([^}]+)}~s", $src, $matches, PREG_OFFSET_CAPTURE);
		for ($i=0,$s=sizeof($matches[2]); $i<$s; $i++) {
			$tagBlocks[] = array(
				$matches[0][$i][0],
				(empty($matches[3][$i][0]) ? $matches[2][$i][0] : $matches[3][$i][0]),
				$matches[0][$i][1],
				$matches[1][$i][0]
			);
		}
		$codeBlocks = preg_split("~(?:\r?\n?[\s]*){$tagStartDelim}\s*(.*?)\s*{$tagEndDelim}|{([^}]+)}~s", $src);
		// processa uma a uma as tags
		$tagParts = array();
		foreach ($tagBlocks as $tag) {
			// bloco ignore
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
				$tplOutput[] = '';
			}
			// assign
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_ASSIGN . '$~i', $tag[1], $tagParts)) {
				if (!$this->_validateTag('ASSIGN', @$tagParts[1], TRUE, array(), $controlBlock))
					return FALSE;
				$tplOutput[] = $this->_compileAssign($tagParts[1]);
			}
			// chamadas de função
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_FUNCTION_CALL . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'FUNCTION' :
						if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
							return FALSE;
						$tplOutput[] = $this->_compileFunctionCall(@$tagParts[2]);
						break;
					case 'START FUNCTION' :
						if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
							return FALSE;
						$tplOutput[] = $this->_compileFunctionCall($tagParts[2], TRUE, $controlBlock);
						break;
					case 'END FUNCTION' :
						if (!$last = $this->_validateTag($operation, @$tagParts[2], FALSE, array('START FUNCTION'), $controlBlock))
							return FALSE;
						$tplOutput[] = $this->_compileFunctionEnd($last[2]);
						break;
					default :
						$tplOutput[] = $this->_compileLiteralBlock($tag[0]);
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
						$tplOutput[] = $this->_compileWidgetInclude($tagParts[2]);
						break;
					case 'START WIDGET' :
						if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
							return FALSE;
						$tplOutput[] = $this->_compileWidgetStart($tagParts[2], $controlBlock);
						break;
					case 'END WIDGET' :
						if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('START WIDGET'), $controlBlock))
							return FALSE;
						$tplOutput[] = $this->_compileWidgetEnd();
						break;
					default :
						$tplOutput[] = $this->_compileLiteralBlock($tag[0]);
						break;
				}
			}
			// operações de bloco
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_BLOCK . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'START' :
						if ($this->controlFlags['loop']) {
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_BLOCKINSIDELOOP'), E_USER_ERROR, __FILE__, __LINE__);
							return FALSE;
						}
						$tplOutput[] = $this->_compileBlockStart($tagParts[2], $controlBlock);
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
						$tplOutput[] = $this->_compilePHPBlock('} $block =& $this->_popStack($stack, $blockName, $instance, $instanceCount); }');
						break;
					case 'INCLUDE' :
						$tplOutput[] = $this->_compileInclude($tagParts[2], $controlBlock);
						break;
					case 'INCLUDESCRIPT' :
						$tplOutput[] = $this->_compileInclude($tagParts[2], $controlBlock, TRUE);
						break;
					default :
						$tplOutput[] = $this->_compileLiteralBlock($tag[0]);
						break;
				}
			}
			// operações com loops
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_LOOP . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'LOOP' :
						if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
							return FALSE;
						$this->controlFlags['loop'] = TRUE;
						$this->controlStack[] = array('LOOP', $controlBlock);
						$tplOutput[] = $this->_compileLoopStart($tagParts[2]);
						break;
					case 'ELSE LOOP' :
						if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('LOOP'), $controlBlock))
							return FALSE;
						$this->controlStack[] = array('ELSE LOOP', $controlBlock);
						$tplOutput[] = $this->_compilePHPBlock('} } else {');
						break;
					case 'END LOOP' :
						$last = (!empty($this->controlStack) ? $this->controlStack[sizeof($this->controlStack)-1] : array());
						if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('LOOP', 'ELSE LOOP'), $controlBlock))
							return FALSE;
						if (array_search('LOOP', $this->controlStack) === FALSE && array_search('ELSE LOOP', $this->controlStack) === FALSE)
							$this->controlFlags['loop'] = FALSE;
						$tplOutput[] = $this->_compilePHPBlock(@$last[0] == 'ELSE LOOP' ? '} unset($_loop' . $this->loopNestingLevel . '); }' : '} } unset($_loop' . $this->loopNestingLevel . ');  }');
						$this->loopNestingLevel--;
						break;
					default :
						$tplOutput[] = $this->_compileLiteralBlock($tag[0]);
						break;
				}
			}
			// operações de condição
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_CONDITION . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'IF' :
		 				if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array(), $controlBlock))
		 					return FALSE;
						$this->controlStack[] = array('IF', $controlBlock);
						$tplOutput[] = $this->_compileIf($tagParts[2]);
						break;
					case 'ELSE IF' :
		 				if (!$this->_validateTag($operation, @$tagParts[2], TRUE, array('IF', 'ELSE IF'), $controlBlock))
		 					return FALSE;
						$this->controlStack[] = array('ELSE IF', $controlBlock);
						$tplOutput[] = $this->_compileIf($tagParts[2], TRUE);
						break;
					case 'ELSE' :
		 				if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('IF', 'ELSE IF'), $controlBlock))
		 					return FALSE;
						$this->controlStack[] = array('ELSE', $controlBlock);
						$tplOutput[] = $this->_compilePHPBlock('} else {');
						break;
					case 'END IF' :
		 				if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('IF', 'ELSE IF', 'ELSE'), $controlBlock))
		 					return FALSE;
		 				$tplOutput[] = $this->_compilePHPBlock('}');
						break;
					default :
						$tplOutput[] = $this->_compileLiteralBlock($tag[0]);
						break;
				}
			}
			// tag de capture
			elseif (!$this->controlFlags['ignore'] && preg_match('~^' . TEMPLATE_CAPTURE . '$~i', $tag[1], $tagParts)) {
				$operation = strtoupper($tagParts[1]);
				switch ($operation) {
					case 'CAPTURE' :
						if (!$this->_validateTag($operation, @$tagParts[2], NULL, array(), $controlBlock))
							return FALSE;
						$tplOutput[] = $this->_compileCaptureStart(@$tagParts[2], $controlBlock);
						break;
					case 'END CAPTURE' :
						if (!$this->_validateTag($operation, @$tagParts[2], FALSE, array('CAPTURE'), $controlBlock))
							return FALSE;
						$tplOutput[] = $this->_compileCaptureEnd();
						break;
					default :
						$tplOutput[] = $this->_compileLiteralBlock($tag[0]);
						break;
				}
			}
			// variável
			elseif (!$this->controlFlags['ignore'] && preg_match('~^(' . TEMPLATE_VARIABLE . '|' . TEMPLATE_QUOTED_STRING . ')' . TEMPLATE_MODIFIER . '$~xs', $tag[1], $tagParts)) {
				$varDef = preg_replace('/^\$/', "", $tagParts[1]);
				if (!preg_match('~' . TEMPLATE_QUOTED_STRING . '~', $varDef) && array_search($varDef, $this->tplDef[$controlBlock]['vars']) === FALSE)
					$this->tplDef[$controlBlock]['vars'][] = $varDef;
				$tplOutput[] = $this->_compileVariable($tagParts[1], $tagParts[2], TRUE);
			}
			// comentários que devem ser omitidos no código de saída
			elseif (preg_match('~^' . TEMPLATE_COMMENT . '$~ms', $tag[1])) {
				$tplOutput[] = '';
			}
			// outros : chamada recursiva
			else {
				if ($tag[0][0] == '{') {
					$tplOutput[] = $this->_compileLiteralBlock('{') . $this->_parseTemplate(substr($tag[0], 1), $controlBlock);
				} else {
					$pos = strpos($tag[0], $tag[1]);
					$startCode = substr($tag[0], 0, $pos);
					$endCode = substr($tag[0], $pos + strlen($tag[1]));
					$tplOutput[] = $this->_compileLiteralBlock($startCode) . $this->_parseTemplate($tag[1], $controlBlock) . $this->_compileLiteralBlock($endCode);
				}
			}
		}
		// constrói e retorna o código de saída
		$output = '';
		for ($i=0, $s=sizeof($tplOutput); $i<$s; $i++) {
			if (preg_match('/^<\?(php)? print/', $tplOutput[$i]))
				$codeBlocks[$i] .= $tagBlocks[$i][3];
			$output .= $this->_compileLiteralBlock($codeBlocks[$i]) . $tplOutput[$i];
		}
		$output .= $this->_compileLiteralBlock($codeBlocks[$i]);
		return $output;
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileVariable
	// @desc		Compila uma referência de variável do template. Valida os modificadores
	//				e inclui as chamadas para os mesmos, com seus parâmetros
	// @param		variableName string	Nome ou caminho da variável
	// @param		modifiers string	"NULL" Modifier(s) da variável
	// @param		print bool			"FALSE" Indica se a variável está sendo impressa ou utilizada em uma tag
	// @return		string Código de acesso à variável
	// @access		private
	//!-----------------------------------------------------------------
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
				// função ou método modificador
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
				// argumentos do modificador
				preg_match_all('~:(' . TEMPLATE_QUOTED_STRING . '|[^:]+)~', $modArgs[$i], $matches);
				for ($j=0; $j<sizeof($matches[1]); $j++) {
					if (preg_match('~' . TEMPLATE_INNER_VARIABLE . '~', $matches[1][$j]))
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

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileVariableName
	// @desc		Resolve nomes de variáveis que contêm acesso a elementos
	//				de arrays, propriedades de objetos e referências dinâmicas
	// @param		name string			Formato original da variável
	// @note		Interpreta formatos como:
	//				{$var}, {$array.key}, {$array.inner.innerMost},
	//				{$obj->property}, {$obj->arrayProperty.key},
	//				{$obj->$dynamicProperty}, {$array.$dinamicKey},
	//				{$p2g.get.request_direct_access}
	// @access		private
	// @return		string Variável com acessos a arrays e objetos traduzidos
	//!-----------------------------------------------------------------
	function _compileVariableName($name) {
		// acesso a variáveis internas da engine de template
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
		} else {
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
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileInternalVariable
	// @desc		Traduz acessos a variáveis internas especiais da engine
	//				de templates, como timestamp atual, superglobals, registry,
	//				objetos de sessão e usuário
	// @param		&variableName string	Variável
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileAssign
	// @desc		Gera o código necessário para uma atribuição de valor para variável
	// @param		expression string	Expressão de atribuição
	// @note		Interpreta comandos de atribuição como:
	//				var=$variable, var=1, var=true, var="string"
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileAssign($expression) {
		$exprParts = array();
		if (preg_match('~^' . TEMPLATE_VARIABLE_NAME . '\s*[=]\s*((' . TEMPLATE_INNER_VARIABLE . '|' . TEMPLATE_QUOTED_STRING . ')' . TEMPLATE_MODIFIER . '|' . TEMPLATE_QUOTED_STRING . '|' . TEMPLATE_NUMBER . '|\b\w+\b)$~', trim($expression), $exprParts)) {
			return $this->_compilePHPBlock(
				'$block[$instance][\'vars\'][\'' . $exprParts[1] . '\'] = ' .
				(isset($exprParts[4]) ? $this->_compileVariable($exprParts[3], $exprParts[4]) : $exprParts[2]) . ';'
			);
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_TAG_SYNTAX', array('ASSIGN', $expression)), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileFunctionCall
	// @desc		Traduz uma tag de função para a chamada da função ou método,
	//				interpretando os parâmetros envolvidos
	// @param		funcProperties string	Propriedades da declaração da função
	// @param		isBlockFunction bool	"FALSE" Indica se é uma chamada de função de bloco (START FUNCTION)
	// @param		controlBlock string		"NULL" Nome do bloco de repetição ativo
	// @note		O parâmetro "name" é obrigatório é pode conter nomes de funções
	//				procedurais, métodos estáticos ou nomes de variáveis e métodos,
	//				no formato objeto->metodo. Este último caso só será válido se o
	//				objeto informado for válido para o template
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileFunctionEnd
	// @desc		Gera o código necessário para a tag END FUNCTION. No momento
	//				em que esta tag é interpretada, a função relacionada é chamada
	//				pela segunda vez, recebendo o conteúdo acumulado no segundo
	//				parâmetro
	// @param		funcData array	Array e parâmetros da função relacionada
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileFunctionEnd($funcData) {
		return $this->_compilePHPBlock('print ' . $funcData[0] . '(array(' . join(', ', $funcData[1]) . '), ob_get_clean(), $this);');
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileBlockStart
	// @desc		Gera o código necessário para um início de bloco
	// @param		blockName string	Nome do bloco
	// @param		parentBlock string	Nome do bloco pai
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileBlockStart($blockName, $parentBlock) {
		$this->blockIndex[$blockName] = 0;
		$this->blockParent[$blockName] = $parentBlock;
		$this->blockStack[] = $blockName;
		$this->tplDef[$blockName] = array(
			'vars' => array(),
			'blocks' => array()
		);
		return $this->_compilePHPBlock('if (isset($block[$instance][\'blocks\'][\'' . $blockName . '\'])) { $this->_pushStack($stack, $blockName, $block, $instance, $instanceCount); $blockName = "' . $blockName . '"; $block =& $this->tplContent[$block[$instance][\'blocks\'][\'' . $blockName . '\']]; $instance = 0; $instanceCount = sizeof($block); for (; $instance<$instanceCount; $instance++) { $this->_prepareBlock($block[$instance]);');
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileInclude
	// @desc		Processa uma tag de inclusão de template, gerando e
	//				retornando o código correspondente
	// @param		includeName string	Nome/valor do include
	// @param		controlBlock string	Nome do bloco ativo
	// @param		evaluate bool		"FALSE" Avaliar como script ou não o retorno
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileInclude($includeName, $controlBlock, $evaluate=FALSE) {
		$defined = TRUE;
		if (isset($this->tplIncludes[$includeName])) {
			$value = $this->tplIncludes[$includeName][0];
			$type = $this->tplIncludes[$includeName][1];
		} elseif (file_exists($includeName)) {
			$value = $includeName;
			$type = T_BYFILE;
		} else {
			$defined = FALSE;
		}
		if ($defined) {
			if ($evaluate) {
				if ($type == T_BYFILE)
					return $this->_compilePHPBlock('include("' . $value . '");');
				else
					return (strpos($value, '<?') === 0 ? $value : $this->_compilePHPBlock($value));
			} else {
				$src = $this->_prepareTemplate($value, $type);
				return strval($this->_parseTemplate($src, $controlBlock));
			}
		}
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileIf
	// @desc		Compila uma declaração de um teste (IF ou ELSE IF),
	//				montando e retornando o código PHP correspondente
	// @note		Divide em tokens e valida a expressão de condição associada
	// @param		expression string	Expressão de condição
	// @param		elseif bool			"FALSE" Indica se é uma tag do tipo 'ELSE IF' ou um 'IF' simples
	// @note		Interpreta expressões de condição como as seguintes:
	//				funcao($var) eq true, $var gt 2, $var === 3,
	//				$var is odd, $var is not even, $var is empty,
	//				($varA + $varB) lt 20
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileIf($expression, $elseif=FALSE) {
		$match = array();
        preg_match_all('~(?>
        		' . TEMPLATE_FUNCTION . ' | ' . TEMPLATE_INNER_VARIABLE . ' | ' . TEMPLATE_QUOTED_STRING . ' |
                \-?0[xX][0-9a-fA-F]+|\-?\d+(?:\.\d+)?|\.\d+|!==|===|==|!=|<>|<<|>>|<=|>=|\&\&|\|\||\(|\)|,|\!|\^|=|\&|\~|<|>|\||\%|\+|\-|\/|\*|\@|
                \b\w+\b|\S+)~x', $expression, $match);
        $tokens = $match[0];
        // balanceamento de parênteses
        $tokenCount = array_count_values($tokens);
        if (isset($tokenCount['(']) && $tokenCount['('] != $tokenCount[')'])
        	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_UNBALANCED_PARENTHESIS', $expression), E_USER_ERROR, __FILE__, __LINE__);
        // processa os tokens
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
                	// funções, métodos estáticos, constantes
                	if (preg_match('~^' . TEMPLATE_FUNCTION . '$~', $token)) {
                	}
                	// variáveis
                	elseif (preg_match('~^' . TEMPLATE_INNER_VARIABLE . '$~', $token, $tokenParts)) {
                		$token = $this->_compileVariable($tokenParts[0], NULL);
                	}
                	// strings e números
                	elseif (preg_match('~^' . TEMPLATE_QUOTED_STRING . '|' . TEMPLATE_NUMBER . '$~', $token)) {
                	}
                	// constantes
                	elseif (preg_match('~^\b\w+\b$~', $token)) {
                	}
                	// outros
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

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileLoopStart
	// @desc		Gera o código necessário para o início do processamento
	//				de uma estrutura do tipo LOOP
	// @param		loopProperties string	Propriedades do LOOP
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileLoopStart($loopProperties) {
		$props = $this->_parseProperties($loopProperties, FALSE);
		// atributos obrigatórios
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
		// atribuir chave e valor, ou somente valor
		if (isset($props['key']))
			$leftSide = 'list($block[$instance][\'vars\'][\'' . $props['key'] . '\'], $block[$instance][\'vars\'][\'' . $props['item'] . '\']) =';
		else
			$leftSide = '$block[$instance][\'vars\'][\'' . $props['item'] . '\'] =';
		// se o atributo name está presente, as variáveis de
		// controle são criadas e atualizadas a cada iteração
		$name = @$props['name'];
		$this->loopNestingLevel++;
		$output = '$_loop' . $this->loopNestingLevel . ' = ' . $props['var'] . '; if (!empty($_loop' . $this->loopNestingLevel . ')) { ';
		if ($name) {
			$output .= '$this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'iteration\'] = -1; ';
			$output .= '$this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'rownum\'] = 0; ';
			$output .= 'if (($_total = $this->_getLoopTotal($_loop' . $this->loopNestingLevel . '))  > 0) { ';
			$output .= '$this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'total\'] = $_total; ';
			$output .= 'while ((' . $leftSide . ' $this->_getLoopItem($_loop' . $this->loopNestingLevel . ', ' . (isset($props['key']) ? 'TRUE' : 'FALSE') . ')) !== FALSE) { ';
			$output .= '$this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'iteration\']++; ';
			$output .= '$this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'rownum\']++; ';
			$output .= '$this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'first\'] = ($this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'iteration\'] == 0); ';
			$output .= '$this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'last\'] = ($this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'iteration\'] == ($this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'total\'] - 1)); ';
			$output .= '$this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'prev\'] = ($this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'rownum\'] - 1); ';
			$output .= '$this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'next\'] = ($this->tplInternalVars[\'loop\'][\'' . $name . '\'][\'rownum\'] + 1);';
		} else {
			$output .= 'if (($_total = $this->_getLoopTotal($_loop' . $this->loopNestingLevel . '))  > 0) { ';
			$output .= 'while ((' . $leftSide . ' $this->_getLoopItem($_loop' . $this->loopNestingLevel . ', ' . (isset($props['key']) ? 'TRUE' : 'FALSE') . ')) !== FALSE) {';
		}
		return $this->_compilePHPBlock($output);
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileWidgetInclude
	// @desc		Gera o código necessário para a inclusão de um widget
	//				no template, através da tag INCLUDE WIDGET
	// @param		widgetProperties string	Propriedades declaradas para o widget no template
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileWidgetInclude($widgetProperties) {
		$widgetData = $this->_parseWidgetProperties($widgetProperties);
		if ($widgetData) {
			// if no path is provided, then use the default path "php2go.gui"
			if (preg_match('/\w+/', $widgetData['path']))
				$widgetData['path'] = "php2go.gui.{$widgetData['path']}";
			return $this->_compilePHPBlock(
				'$widgetClass = classForPath("' . $widgetData['path'] . '"); ' .
				'$widget = new $widgetClass(' . $widgetData['properties'] . '); ' .
				'print "\n"; $widget->display();'
			);
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileWidgetStart
	// @desc		Gera o código necessário para o início de uma declaração de widget
	//				no template, através da tag START WIDGET
	// @param		widgetProperties string	Propriedades declaradas para o widget
	// @param		controlBlock string		Bloco de template ativo
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileWidgetStart($widgetProperties, $controlBlock) {
		$widgetData = $this->_parseWidgetProperties($widgetProperties);
		if ($widgetData) {
			$this->controlStack[] = array('START WIDGET', $controlBlock);
			return $this->_compilePHPBlock(
				'array_push($outputStack, array($widget)); ' .
				'$widgetClass = classForPath("' . $widgetData['path'] . '"); ' .
				'$widget = new $widgetClass(' . $widgetData['properties'] . '); ' .
				'ob_start();'
			);
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileWidgetEnd
	// @desc		Gera o código de encerramento da declaração de um widget
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileWidgetEnd() {
		return $this->_compilePHPBlock(
			'$widget->setContent(ob_get_clean()); ' .
			'print "\n"; $widget->display(); ' .
			'$last = array_pop($outputStack); ' .
			'$widget = $last[0];'
		);
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileCaptureStart
	// @desc		Gera o código de início de uma área de captura
	// @param		captureProperties string	Declaração das propriedades da área de captura
	// @param		controlBlock string			Nome do bloco de repetição ativo
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileCaptureEnd
	// @desc		Gera o código de encerramento de uma área de captura
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileIs
	// @desc		Processa uma expressão do tipo is (not)? xxx, que pode
	//				ser utilizadas nos testes de condição e nas expressões
	//				condicionais
	// @param		expr string			Expressão sobre a qual deve ser aplicado o resultado da expressão IS
	// @param		nextTokens array	Tokens que precedem a expressão IS
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_parseProperties
	// @desc		Interpreta um conjunto de propriedades
	//				na sintaxe "prop=val prop2=val2"
	// @param		properties string		Conjunto de propriedades
	// @param		compileToString bool	"TRUE" Compilar para strings ou retornar os valores reais
	// @access		private
	// @return		array
	//!-----------------------------------------------------------------
	function _parseProperties($properties, $compileToString=TRUE) {
		$match = array();
		preg_match_all('~(?:' . TEMPLATE_QUOTED_STRING . TEMPLATE_MODIFIER . '|' . TEMPLATE_NUMBER . '|' . TEMPLATE_INNER_VARIABLE . TEMPLATE_MODIFIER . '|(?>[^"\'=\s]+))+|[=]~m', $properties, $match);
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
						elseif (preg_match('~^(' . TEMPLATE_INNER_VARIABLE . '|' . TEMPLATE_QUOTED_STRING . ')' . TEMPLATE_MODIFIER . '$~', $token, $tokenParts)) {
							if (!$compileToString && $tokenParts[1][0] != '$' && empty($tokenParts[2]))
								$token = substr($token, 1, -1);
							else
								$token = $this->_compileVariable($tokenParts[1], $tokenParts[2]);
						} elseif (!$compileToString && preg_match('~^' . TEMPLATE_QUOTED_STRING . '$~', $token))
							$token = substr($token, 1, -1);
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

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_parseWidgetProperties
	// @desc		Processa a string de propriedades/atributos definidos para um widget
	// @param		widgetProperties string	Lista de propriedades
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _parseWidgetProperties($widgetProperties) {
		$props = $this->_parseProperties($widgetProperties);
		// propriedade "path" obrigatória
		if (!isset($props['path'])) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TPLPARSE_REQUIRED_ATTRIBUTE', array('path', 'WIDGET')), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$path = substr($props['path'], 1, -1);
		unset($props['path']);
		$props['vars'] = '$block[$instance][\'vars\']';
		// transforma a lista de propriedades na definição string de um array
		$output = 'array(';
		foreach ($props as $key => $value)
			$output .= '\'' . $key . '\'=>' . $value . ',';
		$output = substr($output, 0, -1) . ')';
		return array(
			'path' => $path,
			'properties' => $output
		);
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compilePHPBlock
	// @desc		Gera a saída de um bloco de código PHP, como resultado
	//				da declaração de um comando ou tag no template
	// @param		block string	Bloco de código PHP
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compilePHPBlock($block) {
		return ($this->controlFlags['shortOpenTag'] ? '<? ' : '<?php ') . $block . " ?>\n";
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_compileLiteralBlock
	// @desc		Gera código para um literal (código HTML ou código não
	//				enquadrado nos padrões de variáveis e tags)
	// @param		value string		Valor do literal
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _compileLiteralBlock($value) {
		return $value;
	}

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_validateTag
	// @desc		Valida a declaração de uma tag, de acordo a necessidade
	//				ou não de argumentos e validando contra as últimas tags processadas
	//				se necessário
	// @param		tag string				Nome da tag
	// @param		tagArguments string		Argumentos da tag
	// @param		needsArguments bool		Exige ou não argumentos
	// @param		matchTags array			"array()" Tags válidas na última posição da pilha
	// @param		controlBlock string		Bloco de repetição ativo
	// @return		array Retorna os dados retirados do topo da pilha de controle
	// @access		private
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	TemplateParser::_i18PreFilter
	// @desc		Substitui uma referência de linguagem/internacionalização
	// @param		match array		Variável de internacionalização
	// @note		Este método é utilizado dentro do método _prepareTemplate
	// @return		string Variável traduzida para a linguagem ativa
	// @access		private
	//!-----------------------------------------------------------------
	function _i18nPreFilter($match) {
		return PHP2Go::getLangVal($match[1]);
	}
}
?>