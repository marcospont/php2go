<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/core/template/Template.class.php,v 1.47 2006/11/21 23:24:48 mpont Exp $
// $Date: 2006/11/21 23:24:48 $

//------------------------------------------------------------------
import('php2go.auth.User');
import('php2go.cache.CacheManager');
import('php2go.data.DataSet');
import('php2go.template.TemplateParser');
import('php2go.template.widget.Widget');
import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');
import('php2go.util.json.JSONEncoder');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		Template
// @desc 		Esta classe � respons�vel por realizar opera��es sobre
// 				um arquivo template de c�digo HTML, onde podem ser
// 				substitu�das vari�veis, replicados blocos de c�digo,
// 				inclu�dos outros scripts/templates, etc...
// @package		php2go.template
// @extends 	PHP2Go
// @uses 		TemplateParser
// @uses		StringUtils
// @author 		Marcos Pont
// @version		$Revision: 1.47 $
//!-----------------------------------------------------------------
class Template extends Component
{
	var $cacheOptions = array();	// @var cacheOptions array				"array()" Configura��es de cache
	var $currentBlock = NULL;		// @var currentBlock mixed				"NULL" Ponteiro para o bloco ativo no template
	var $currentBlockName;			// @var currentBlockName string			Nome do bloco ativo no template
	var $tplComponents = array();	// @var tplComponents array				"array()" Conjunto de componentes utilizados no template
	var $tplContent = array();		// @var tplContent array				"array()" Estrutura interna de armazenamento de inst�ncias de blocos e vari�veis atribu�das
	var $tplGlobalVars = array();	// @var tplGlobalVars array				"array()" Vetor de vari�veis globais do template
	var $tplInternalVars = array();	// @var tplInternalVars array			"array()" Vetor de vari�veis especiais ou internas � engine de template
	var $tplLoop = array();			// @var tplLoop array					"array()" Vetor de controle runtime sobre loops
	var $tplCapture = array();		// @var tplCapture array				"array()" Vetor que armazena as �reas de captura do template durante a compila��o e execu��o
	var $tplMTime;					// @var tplMTime int					Timestamp de modifica��o do template base
	var $Parser = NULL;				// @var Parser TemplateParser object	Parser utilizado na interpreta��o do template

	//!-----------------------------------------------------------------
	// @function	Template::Template
	// @desc 		Construtor da classe
	// @access 		public
	// @param 		tplFile string	Caminho do arquivo template no servidor ou c�digo do template em formato string
	// @param 		type int		"T_BYFILE" Tipo do template: arquivo (T_BYFILE) ou string (T_BYVAR)
	//!-----------------------------------------------------------------
	function Template($tplFile, $type=T_BYFILE) {
		parent::Component();
		$this->Parser = new TemplateParser($tplFile, $type);
		$this->tplGlobalVars = array(
			'ldelim' => '{',
			'rdelim' => '}'
		);
		$this->tplInternalVars['loop'] = array();
		$this->tplInternalVars['user'] =& User::getInstance();
		/* @var $Conf Conf */
		$Conf =& Conf::getInstance();
		$this->tplInternalVars['conf'] =& $Conf->getAll();
		$this->cacheOptions['enabled'] = FALSE;
		$this->cacheOptions['group'] = 'php2goTemplate';
		// configura��es globais
		$globalConf = $Conf->getConfig('TEMPLATES');
		if (is_array($globalConf))
			$this->_loadGlobalSettings($globalConf);
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	Template::__destruct
	// @desc		Destrutor da classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	Template::setCacheProperties
	// @desc		Configura a classe para utilizar cache do template j�
	//				interpretado. Define diret�rio de cache, tempo de expira��o
	//				(lifetime) ou habilita renova��o da cache baseada no timestamp
	//				de modifica��o do arquivo original
	// @param		dir string		Diret�rio de cache
	// @param		lifeTime int	"0" Tempo de vida da cache, em segundos
	// @param		useMTime bool	"TRUE" Renovar a cache a partir de mudan�as no arquivo original
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCacheProperties($dir, $lifeTime=NULL, $useMTime=TRUE) {
		$this->cacheOptions['baseDir'] = $dir;
		if ($lifeTime)
			$this->cacheOptions['lifeTime'] = $lifeTime;
		$this->cacheOptions['useMTime'] = (bool)$useMTime;
		$this->cacheOptions['enabled'] = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Template::setTagDelimiter
	// @desc		Define o padr�o a ser utilizado nos templates para
	//				delimitar in�cio e fim de tags
	// @note		O padr�o da classe � TEMPLATE_DELIM_COMMENT (utiliza
	//				marcas de in�cio e fim de coment�rios html)
	// @param		type int	Tipo (vide constantes da classe)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTagDelimiter($type) {
		if ($type == TEMPLATE_DELIM_COMMENT || $type == TEMPLATE_DELIM_BRACE)
			$this->Parser->tagDelimType = $type;
	}

	//!-----------------------------------------------------------------
	// @function	Template::addModifier
	// @desc		Registra um modificador de vari�vel customizado
	// @param		name string	Nome do modificador (como ele ser� chamado a partir do template)
	// @param		spec mixed	Nome da fun��o, array classe+m�todo, ou array caminho+classe+m�todo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addModifier($name, $spec) {
		$this->Parser->tplModifiers[$name] = $spec;
	}

	//!-----------------------------------------------------------------
	// @function	Template::parse
	// @desc 		Prepara o template para utiliza��o e parseia todo o
	// 				seu conte�do buscando por variave�s, blocos e outras
	// 				tags reservadas que permitem realizar opera��es sobre
	// 				o conte�do do template
	// @note		Se a classe estiver configurada para utilizar cache, um template
	//				j� compilado � buscado no diret�rio de cache
	// @note		Este m�todo somente poder� ser executado somente uma vez. Na segunda
	//				execu��o, uma exce��o do tipo E_USER_ERROR ser� disparada
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function parse() {
		if ($this->cacheOptions['enabled']) {
			$Cache = CacheManager::factory('file');
			// cache id
			if ($this->Parser->tplBase['type'] == T_BYFILE)
				$cacheId = realpath($this->Parser->tplBase['src']);
			else
				$cacheId = dechex(crc32($this->Parser->tplBase['src']));
			if ($this->cacheOptions['useMTime']) {
				if (!isset($this->tplMTime) && $this->Parser->tplBase['type'] == T_BYFILE)
					$this->tplMTime = FileSystem::lastModified($this->Parser->tplBase['src'], TRUE);
				$Cache->Storage->setLastValidTime($this->tplMTime);
			} elseif ($this->cacheOptions['lifeTime']) {
				$Cache->Storage->setLifeTime($this->cacheOptions['lifeTime']);
			}
			// diret�rio base
			if ($this->cacheOptions['baseDir'])
				$Cache->Storage->setBaseDir($this->cacheOptions['baseDir']);
			// consulta cache
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

	//!-----------------------------------------------------------------
	// @function	Template::resetTemplate
	// @desc		Remove todas as vari�veis e blocos criados no template, retornando
	//				o template ao estado inicial ap�s a compila��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function resetTemplate() {
		if ($this->isPrepared()) {
			$this->_initializeContent();
			$keys = array_keys($this->Parser->blockIndex);
			foreach ($keys as $block)
				$this->Parser->blockIndex[$block] = 0;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Template::isPrepared
	// @desc 		Verifica se o template j� foi compilado
	// @access 		public
	// @return 		bool
	//!-----------------------------------------------------------------
	function isPrepared() {
		return $this->Parser->prepared;
	}

	//!-----------------------------------------------------------------
	// @function	Template::isBlockDefined
	// @desc		Verifica se um determinado bloco foi definido no template
	// @note		A consulta pode ser realizada por um nome simples de bloco ou
	//				por uma caminho na estrutura de blocos aninhados
	// @access 		public
	// @param 		block string		Nome do bloco a ser buscado
	// @return		bool
	// @see 		Template::isVariableDefined
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Template::getDefinedBlocks
	// @desc 		Retorna a lista de blocos definidos no template
	// @note		Retorna NULL se o template n�o estiver preparado
	// @return		array Lista de blocos definidos
	// @access 		public
	// @see 		Template::getDefinedVariables
	//!-----------------------------------------------------------------
	function getDefinedBlocks() {
		if ($this->Parser->prepared)
			return array_keys($this->Parser->tplDef);
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	Template::isVariableDefined
	// @desc 		Verifica se uma vari�vel est� definida no template
	// @note		O par�metro $variable pode representar uma vari�vel no bloco ativo
	//				ou uma refer�ncia do tipo bloco.variavel
	// @param 		variable string	Nome da vari�vel a ser buscada
	// @access 		public
	// @return		bool
	// @see 		Template::getValue
	// @see 		Template::isBlockDefined
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Template::getDefinedVariables
	// @desc 		Busca as vari�veis definidas para um determinado bloco
	// @param 		blockName string	"NULL" Nome do bloco
	// @return		array Vetor com os nomes de vari�veis definidos
	// @note		Se um nome de bloco n�o foi fornecido, o bloco raiz ser� utilizado
	// @note		Retorna NULL se o template n�o estiver preparado
	// @see 		Template::getDefinedBlocks
	// @access 		public
	//!-----------------------------------------------------------------
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
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	Template::getValue
	// @desc 		Busca o valor atribu�do a uma vari�vel
	// @note		O par�metro de consulta pode ser o nome de uma vari�vel simples
	//				no bloco ativo ou uma refer�ncia do tipo bloco.variavel
	// @param 		variable string	Nome da vari�vel buscada
	// @return		mixed Valor da vari�vel se ela estiver definida ou FALSE em caso contr�rio
	// @see 		Template::isVariableDefined
	// @access 		public
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Template::createBlock
	// @desc		Cria uma inst�ncia do bloco $block
	// @note		O bloco criado passa a ser o bloco ativo, o que significa que
	//				as pr�ximas atribui��es de vari�veis ser�o alocadas na inst�ncia criada
	// @note		Um erro ser� gerado se o bloco n�o existir ou se o nome do bloco
	//				for igual ao nome do bloco raiz (_ROOT), que � reservado
	// @param 		block string	Nome do bloco a ser criado
	// @see 		Template::setCurrentBlock
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function createBlock($block) {
		// bloco existente
		if (!isset($this->Parser->tplDef[$block]))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $block), E_USER_ERROR, __FILE__, __LINE__);
		// bloco diferente do bloco raiz
		if ($block == TP_ROOTBLOCK)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_REPLICATE_ROOT_BLOCK'), E_USER_ERROR, __FILE__, __LINE__);
		// busca a inst�ncia do bloco pai
		$parent =& $this->_getLastInstance($this->Parser->blockParent[$block]);
		// primeira cria��o
		if (!isset($parent['blocks'][$block])) {
			$this->Parser->blockIndex[$block]++;
			$index = "{$block}:{$this->Parser->blockIndex[$block]}";
			$parent['blocks'][$block] = $index;
			$this->tplContent[$index] = array();
		} else {
			$index = $parent['blocks'][$block];
		}
		// cria a nova inst�ncia
		$nextInstance = sizeof($this->tplContent[$index]);
		$this->tplContent[$index][$nextInstance] = array(
			'vars' => array(),
			'blocks' => array()
		);
		$this->currentBlockName = $block;
		$this->currentBlock =& $this->tplContent[$index][$nextInstance];
	}

	//!-----------------------------------------------------------------
	// @function	Template::getCurrentBlockName
	// @desc		Retorna o nome do bloco ativo no template
	// @return		string Nome do bloco
	// @access		public
	//!-----------------------------------------------------------------
	function getCurrentBlockName() {
		return $this->currentBlockName;
	}

	//!-----------------------------------------------------------------
	// @function	Template::setCurrentBlock
	// @desc 		Move o ponteiro do bloco ativo para o bloco indicado pelo nome $block
	// @note		Um erro ser� gerado se o bloco solicitado n�o estiver definido
	// @param 		block string	Nome do bloco
	// @see 		Template::createBlock
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCurrentBlock($block) {
		if (!isset($this->Parser->tplDef[$block]) || ($block != TP_ROOTBLOCK && $this->Parser->blockIndex[$block] == 0))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $block), E_USER_ERROR, __FILE__, __LINE__);
		$this->currentBlockName = $block;
		$this->currentBlock =& $this->_getLastInstance($block);
	}

	//!-----------------------------------------------------------------
	// @function	Template::createAndAssign
	// @desc		Atalho para cria��o de uma inst�ncia de bloco e atribui��o de vari�veis � inst�ncia criada
	// @param		blockName string	Nome do bloco a ser criado
	// @param		variable mixed		Nome da vari�vel ou vetor de substitui��es
	// @param		value mixed			"" Valor para a vari�vel, se for simples
	// @note		A sem�ntica dos par�metros $variable e $value � a mesma do m�todo Template::assign
	// @see			Template::assign
	// @see			Template::globalAssign
	// @see			Template::includeAssign
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function createAndAssign($blockName, $variable, $value='') {
		$this->createBlock($blockName);
		$this->assign($variable, $value);
	}

	//!-----------------------------------------------------------------
	// @function	Template::assign
	// @desc 		Atribui valor a uma vari�vel de um bloco do template
	// @note		Aceita um array associativo no par�metro $variable para atribuir m�ltiplas vari�veis
	// @note 		A vari�vel, al�m de poder ser representada por um array associativo,
	//				pode referenciar-se a uma vari�vel do bloco ativo ou usando refer�ncia
	//				expl�cita para um bloco utilizando a sintaxe bloco.variavel
	// @param 		variable mixed		Vari�vel ou vari�veis para substitui��o
	// @param 		value mixed			"" Valor que dever� ser associado � vari�vel
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function assign($variable, $value='') {
		if (is_array($variable)) {
			foreach ($variable as $name => $value)
				$this->_assign($name, $value);
		} else {
			$this->_assign($variable, $value);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Template::assignByRef
	// @desc		Atribui um valor por refer�ncia a uma determinada
	//				vari�vel do bloco ativo no template ou ao bloco
	//				informado na sintaxe bloco.variavel
	// @param		variable string		Nome da vari�vel ou bloco+vari�vel
	// @param		&value mixed		Refer�ncia para o valor da vari�vel
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Template::globalAssign
	// @desc		Adiciona uma vari�vel global no template
	// @note		Aceita um array associativo no par�metro $variable para incluir m�ltiplas vari�veis
	// @param 		variable string		Nome da vari�vel global ou vetor de vari�veis globais com seus valores
	// @param 		value string		"" Valor para a vari�vel global
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function globalAssign($variable, $value='') {
		if (is_array($variable)) {
			foreach ($variable as $name => $value)
				$this->_globalAssign($name, $value);
		} else {
			$this->_globalAssign($variable, $value);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Template::includeAssign
	// @desc		Define o valor de um bloco de inclus�o definido no template
	// @note		As inclus�es de scripts devem ser executadas antes da execu��o do m�todo Template::parse()
	// @note		Ao utilizar o m�todo includeAssign para atribuir valor a uma inclus�o de
	//				script (diretiva INCLUDESCRIPT), e desejar utilizar o tipo T_BYVAR, inclua
	//				os caracteres &lt;? e ?&gt; no in�cio e no final da string
	// @param		blockName string	Nome do bloco de inclus�o
	// @param		value string		Caminho completo para o arquivo de inclus�o (T_BYFILE) ou conte�do string (T_BYVAR)
	// @param		type int			"T_BYFILE" Tipo de inclus�o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function includeAssign($blockName, $value, $type=T_BYFILE) {
		if (!empty($value) && ($type == T_BYFILE || $type == T_BYVAR)) {
			if ($type == T_BYFILE && !is_readable($value))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $value), E_USER_ERROR, __FILE__, __LINE__);
			$this->Parser->tplIncludes[$blockName] = array($value, $type);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Template::onPreRender
	// @desc		Etapa de pr�-renderiza��o do template
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Template::getContent
	// @desc		Monta e retorna o conte�do HTML do template
	// @note		N�o � poss�vel imprimir o conte�do de um template cujo nome de arquivo � vazio ou cujo conte�do � vazio
	// @return		string C�digo HTML resultante
	// @see 		Template::display
	// @access 		public
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		//highlight_string($this->Parser->tplBase['compiled']);
		ob_start();
		eval('?>' . $this->Parser->tplBase['compiled']);
		return ob_get_clean();
	}

	//!-----------------------------------------------------------------
	// @function	Template::display
	// @desc		Monta a envia para a sa�da padr�o o conte�do HTML do template
	// @note		N�o � poss�vel imprimir o conte�do de um template cujo nome de arquivo � vazio ou cujo conte�do � vazio
	// @see 		Template::getContent
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		$this->onPreRender();
		//highlight_string($this->Parser->tplBase['compiled']);
		eval('?>' . $this->Parser->tplBase['compiled']);
	}

	//!-----------------------------------------------------------------
	// @function	Template::_initializeContent
	// @desc		Inicializa a estrutura de conte�do do template
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Template::_assign
	// @desc		M�todo interno de atribui��o de valores a vari�veis
	// @param		variable string		Refer�ncia para a vari�vel
	// @param		value mixed			Valor de atribui��o
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Template::_globalAssign
	// @desc		M�todo interno de registro de vari�veis globais
	// @param		variable string		Nome da vari�vel
	// @param		value mixed			Valor
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _globalAssign($variable, $value) {
		if (TypeUtils::isInstanceOf($value, 'Component'))
			$this->tplComponents["global:{$variable}"] =& $value;
		$this->tplGlobalVars[$variable] = $value;
	}

	//!-----------------------------------------------------------------
	// @function	Template::&_getLastInstance
	// @desc		Para um determinado nome de bloco, busca a �ltima inst�ncia criada,
	//				considerando o �ndice ativo do bloco
	// @param		blockName string	Nome do bloco
	// @return		array Refer�ncia para a inst�ncia mais recente
	// @access		private
	//!-----------------------------------------------------------------
	function &_getLastInstance($blockName) {
		$index = "$blockName:{$this->Parser->blockIndex[$blockName]}";
		$lastInstanceKey = sizeof($this->tplContent[$index]) - 1;
		$lastInstance =& $this->tplContent[$index][$lastInstanceKey];
		return $lastInstance;
	}

	//!-----------------------------------------------------------------
	// @function	Template::_getFullPath
	// @desc		Monta um caminho �nico para uma vari�vel em um bloco,
	//				levando em considera��o o �ndice de utiliza��o e o
	//				n�mero da inst�ncia atual do bloco
	// @note		Exemplo: _ROOT:0:0:var, loop_cell:1:3:col_wid
	// @param		block string	Nome do bloco
	// @param		variable string	Nome da vari�vel
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _getFullPath($block, $variable) {
		$index = "$block:{$this->Parser->blockIndex[$block]}";
		$lastInstanceKey = sizeof($this->tplContent[$index]) - 1;
		return "{$index}:{$lastInstanceKey}:{$variable}";
	}

	//!-----------------------------------------------------------------
	// @function	Template::_prepareBlock
	// @desc		Este m�todo � chamado a partir do c�digo compilado do template
	//				a fim de inserir em uma inst�ncia de bloco din�mico as vari�veis
	//				globais, a fim de que as mesmas estejam dispon�veis
	// @param		&block array	Inst�ncia de bloco din�mico de repeti��o
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _prepareBlock(&$block) {
		foreach ($this->tplGlobalVars as $name => $value) {
			if (!array_key_exists($name, $block['vars']))
				$block['vars'][$name] = $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Template::_pushStack
	// @desc		Utilizado a partir do c�digo compilado do template para
	//				armazenar em uma pilha a �ltima inst�ncia processada de
	//				um bloco, a fim de iniciar a itera��o para outro bloco
	// @param		&stack array		Pilha de execu��o
	// @param		blockName string	Nome do �ltimo bloco ativo
	// @param		&block array		Inst�ncias do �ltimo bloco
	// @param		instance int		�ndice da inst�ncia atual
	// @param		instanceCount int	Total de inst�ncias
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _pushStack(&$stack, $blockName, &$block, $instance, $instanceCount) {
		$newItem = array();
		$newItem[0] = $blockName;
		$newItem[1] =& $block;
		$newItem[2] = $instance;
		$newItem[3] = $instanceCount;
		$stack[sizeof($stack)] =& $newItem;
	}

	//!-----------------------------------------------------------------
	// @function	Template::&_popStack
	// @desc		Retorna da pilha da execu��o o �ltimo bloco processado,
	//				voltando as vari�veis de controle ao estado anterior �
	//				�ltima itera��o
	// @param		&stack array		Pilha de execu��o
	// @param		&blockName string	Retorna o nome do bloco
	// @param		&instance int		Retorna o �ndice da �ltima inst�ncia processada
	// @param		&instanceCount int	Retorna o total de inst�ncias
	// @return		array Conjunto de inst�ncias de bloco
	// @access		private
	//!-----------------------------------------------------------------
	function &_popStack(&$stack, &$blockName, &$instance, &$instanceCount) {
		$lastItem =& $stack[sizeof($stack)-1];
		$blockName = $lastItem[0];
		$instance = $lastItem[2];
		$instanceCount = $lastItem[3];
		array_pop($stack);
		return $lastItem[1];
	}

	//!-----------------------------------------------------------------
	// @function	Template::_getLoopItem
	// @desc		Busca o pr�ximo item para um determinado loop
	// @param		&loop mixed		Conjunto de dados do loop
	// @param		returnKey bool	"FALSE" Se TRUE, retorna chave e
	//								valor do item atual. Do contr�rio,
	//								retorna somente o item atual
	// @access		private
	// @return		mixed
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Template::_getLoopTotal
	// @desc		M�todo que busca o total de itens de um loop
	// @param		loop mixed		Conjunto de dados do loop
	// @return		int	Total de itens do loop
	// @access		private
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Template::_loadGlobalSettings
	// @desc		Aplica as configura��es globais para templates
	// @param		settings array	Configura��es globais
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _loadGlobalSettings($settings) {
		if (is_array($settings['CACHE'])) {
			// propriedades de cache
			if (isset($settings['CACHE']['DIR']))
				$this->setCacheProperties($settings['CACHE']['DIR'], @$settings['CACHE']['LIFETIME'], @$settings['CACHE']['USEMTIME']);
			// n�o aplicar delimitador de tags para templates internos ao framework
			$path = realpath($this->Parser->tplBase['src']);
			if (!$path || strpos(str_replace("\\", "/", $path), PHP2GO_ROOT) === 0)
				$this->setTagDelimiter(@$settings['TAG_DELIMITER_TYPE']);
			// modificadores custom
			foreach ((array)$settings['MODIFIERS'] as $name => $spec)
				$this->Parser->tplModifiers[$name] = $spec;
		}
	}
}
?>