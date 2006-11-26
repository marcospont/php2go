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
// @desc 		Esta classe é responsável por realizar operações sobre
// 				um arquivo template de código HTML, onde podem ser
// 				substituídas variáveis, replicados blocos de código,
// 				incluídos outros scripts/templates, etc...
// @package		php2go.template
// @extends 	PHP2Go
// @uses 		TemplateParser
// @uses		StringUtils
// @author 		Marcos Pont
// @version		$Revision: 1.47 $
//!-----------------------------------------------------------------
class Template extends Component
{
	var $cacheOptions = array();	// @var cacheOptions array				"array()" Configurações de cache
	var $currentBlock = NULL;		// @var currentBlock mixed				"NULL" Ponteiro para o bloco ativo no template
	var $currentBlockName;			// @var currentBlockName string			Nome do bloco ativo no template
	var $tplComponents = array();	// @var tplComponents array				"array()" Conjunto de componentes utilizados no template
	var $tplContent = array();		// @var tplContent array				"array()" Estrutura interna de armazenamento de instâncias de blocos e variáveis atribuídas
	var $tplGlobalVars = array();	// @var tplGlobalVars array				"array()" Vetor de variáveis globais do template
	var $tplInternalVars = array();	// @var tplInternalVars array			"array()" Vetor de variáveis especiais ou internas à engine de template
	var $tplLoop = array();			// @var tplLoop array					"array()" Vetor de controle runtime sobre loops
	var $tplCapture = array();		// @var tplCapture array				"array()" Vetor que armazena as áreas de captura do template durante a compilação e execução
	var $tplMTime;					// @var tplMTime int					Timestamp de modificação do template base
	var $Parser = NULL;				// @var Parser TemplateParser object	Parser utilizado na interpretação do template

	//!-----------------------------------------------------------------
	// @function	Template::Template
	// @desc 		Construtor da classe
	// @access 		public
	// @param 		tplFile string	Caminho do arquivo template no servidor ou código do template em formato string
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
		// configurações globais
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
	// @desc		Configura a classe para utilizar cache do template já
	//				interpretado. Define diretório de cache, tempo de expiração
	//				(lifetime) ou habilita renovação da cache baseada no timestamp
	//				de modificação do arquivo original
	// @param		dir string		Diretório de cache
	// @param		lifeTime int	"0" Tempo de vida da cache, em segundos
	// @param		useMTime bool	"TRUE" Renovar a cache a partir de mudanças no arquivo original
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
	// @desc		Define o padrão a ser utilizado nos templates para
	//				delimitar início e fim de tags
	// @note		O padrão da classe é TEMPLATE_DELIM_COMMENT (utiliza
	//				marcas de início e fim de comentários html)
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
	// @desc		Registra um modificador de variável customizado
	// @param		name string	Nome do modificador (como ele será chamado a partir do template)
	// @param		spec mixed	Nome da função, array classe+método, ou array caminho+classe+método
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addModifier($name, $spec) {
		$this->Parser->tplModifiers[$name] = $spec;
	}

	//!-----------------------------------------------------------------
	// @function	Template::parse
	// @desc 		Prepara o template para utilização e parseia todo o
	// 				seu conteúdo buscando por variaveís, blocos e outras
	// 				tags reservadas que permitem realizar operações sobre
	// 				o conteúdo do template
	// @note		Se a classe estiver configurada para utilizar cache, um template
	//				já compilado é buscado no diretório de cache
	// @note		Este método somente poderá ser executado somente uma vez. Na segunda
	//				execução, uma exceção do tipo E_USER_ERROR será disparada
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
			// diretório base
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
	// @desc		Remove todas as variáveis e blocos criados no template, retornando
	//				o template ao estado inicial após a compilação
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
	// @desc 		Verifica se o template já foi compilado
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
	// @note		Retorna NULL se o template não estiver preparado
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
	// @desc 		Verifica se uma variável está definida no template
	// @note		O parâmetro $variable pode representar uma variável no bloco ativo
	//				ou uma referência do tipo bloco.variavel
	// @param 		variable string	Nome da variável a ser buscada
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
	// @desc 		Busca as variáveis definidas para um determinado bloco
	// @param 		blockName string	"NULL" Nome do bloco
	// @return		array Vetor com os nomes de variáveis definidos
	// @note		Se um nome de bloco não foi fornecido, o bloco raiz será utilizado
	// @note		Retorna NULL se o template não estiver preparado
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
	// @desc 		Busca o valor atribuído a uma variável
	// @note		O parâmetro de consulta pode ser o nome de uma variável simples
	//				no bloco ativo ou uma referência do tipo bloco.variavel
	// @param 		variable string	Nome da variável buscada
	// @return		mixed Valor da variável se ela estiver definida ou FALSE em caso contrário
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
	// @desc		Cria uma instância do bloco $block
	// @note		O bloco criado passa a ser o bloco ativo, o que significa que
	//				as próximas atribuições de variáveis serão alocadas na instância criada
	// @note		Um erro será gerado se o bloco não existir ou se o nome do bloco
	//				for igual ao nome do bloco raiz (_ROOT), que é reservado
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
		// busca a instância do bloco pai
		$parent =& $this->_getLastInstance($this->Parser->blockParent[$block]);
		// primeira criação
		if (!isset($parent['blocks'][$block])) {
			$this->Parser->blockIndex[$block]++;
			$index = "{$block}:{$this->Parser->blockIndex[$block]}";
			$parent['blocks'][$block] = $index;
			$this->tplContent[$index] = array();
		} else {
			$index = $parent['blocks'][$block];
		}
		// cria a nova instância
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
	// @note		Um erro será gerado se o bloco solicitado não estiver definido
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
	// @desc		Atalho para criação de uma instância de bloco e atribuição de variáveis à instância criada
	// @param		blockName string	Nome do bloco a ser criado
	// @param		variable mixed		Nome da variável ou vetor de substituições
	// @param		value mixed			"" Valor para a variável, se for simples
	// @note		A semântica dos parâmetros $variable e $value é a mesma do método Template::assign
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
	// @desc 		Atribui valor a uma variável de um bloco do template
	// @note		Aceita um array associativo no parâmetro $variable para atribuir múltiplas variáveis
	// @note 		A variável, além de poder ser representada por um array associativo,
	//				pode referenciar-se a uma variável do bloco ativo ou usando referência
	//				explícita para um bloco utilizando a sintaxe bloco.variavel
	// @param 		variable mixed		Variável ou variáveis para substituição
	// @param 		value mixed			"" Valor que deverá ser associado à variável
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
	// @desc		Atribui um valor por referência a uma determinada
	//				variável do bloco ativo no template ou ao bloco
	//				informado na sintaxe bloco.variavel
	// @param		variable string		Nome da variável ou bloco+variável
	// @param		&value mixed		Referência para o valor da variável
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
	// @desc		Adiciona uma variável global no template
	// @note		Aceita um array associativo no parâmetro $variable para incluir múltiplas variáveis
	// @param 		variable string		Nome da variável global ou vetor de variáveis globais com seus valores
	// @param 		value string		"" Valor para a variável global
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
	// @desc		Define o valor de um bloco de inclusão definido no template
	// @note		As inclusões de scripts devem ser executadas antes da execução do método Template::parse()
	// @note		Ao utilizar o método includeAssign para atribuir valor a uma inclusão de
	//				script (diretiva INCLUDESCRIPT), e desejar utilizar o tipo T_BYVAR, inclua
	//				os caracteres &lt;? e ?&gt; no início e no final da string
	// @param		blockName string	Nome do bloco de inclusão
	// @param		value string		Caminho completo para o arquivo de inclusão (T_BYFILE) ou conteúdo string (T_BYVAR)
	// @param		type int			"T_BYFILE" Tipo de inclusão
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
	// @desc		Etapa de pré-renderização do template
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
	// @desc		Monta e retorna o conteúdo HTML do template
	// @note		Não é possível imprimir o conteúdo de um template cujo nome de arquivo é vazio ou cujo conteúdo é vazio
	// @return		string Código HTML resultante
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
	// @desc		Monta a envia para a saída padrão o conteúdo HTML do template
	// @note		Não é possível imprimir o conteúdo de um template cujo nome de arquivo é vazio ou cujo conteúdo é vazio
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
	// @desc		Inicializa a estrutura de conteúdo do template
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
	// @desc		Método interno de atribuição de valores a variáveis
	// @param		variable string		Referência para a variável
	// @param		value mixed			Valor de atribuição
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
	// @desc		Método interno de registro de variáveis globais
	// @param		variable string		Nome da variável
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
	// @desc		Para um determinado nome de bloco, busca a última instância criada,
	//				considerando o índice ativo do bloco
	// @param		blockName string	Nome do bloco
	// @return		array Referência para a instância mais recente
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
	// @desc		Monta um caminho único para uma variável em um bloco,
	//				levando em consideração o índice de utilização e o
	//				número da instância atual do bloco
	// @note		Exemplo: _ROOT:0:0:var, loop_cell:1:3:col_wid
	// @param		block string	Nome do bloco
	// @param		variable string	Nome da variável
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
	// @desc		Este método é chamado a partir do código compilado do template
	//				a fim de inserir em uma instância de bloco dinâmico as variáveis
	//				globais, a fim de que as mesmas estejam disponíveis
	// @param		&block array	Instância de bloco dinâmico de repetição
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
	// @desc		Utilizado a partir do código compilado do template para
	//				armazenar em uma pilha a última instância processada de
	//				um bloco, a fim de iniciar a iteração para outro bloco
	// @param		&stack array		Pilha de execução
	// @param		blockName string	Nome do último bloco ativo
	// @param		&block array		Instâncias do último bloco
	// @param		instance int		Índice da instância atual
	// @param		instanceCount int	Total de instâncias
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
	// @desc		Retorna da pilha da execução o último bloco processado,
	//				voltando as variáveis de controle ao estado anterior à
	//				última iteração
	// @param		&stack array		Pilha de execução
	// @param		&blockName string	Retorna o nome do bloco
	// @param		&instance int		Retorna o índice da última instância processada
	// @param		&instanceCount int	Retorna o total de instâncias
	// @return		array Conjunto de instâncias de bloco
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
	// @desc		Busca o próximo item para um determinado loop
	// @param		&loop mixed		Conjunto de dados do loop
	// @param		returnKey bool	"FALSE" Se TRUE, retorna chave e
	//								valor do item atual. Do contrário,
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
	// @desc		Método que busca o total de itens de um loop
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
	// @desc		Aplica as configurações globais para templates
	// @param		settings array	Configurações globais
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _loadGlobalSettings($settings) {
		if (is_array($settings['CACHE'])) {
			// propriedades de cache
			if (isset($settings['CACHE']['DIR']))
				$this->setCacheProperties($settings['CACHE']['DIR'], @$settings['CACHE']['LIFETIME'], @$settings['CACHE']['USEMTIME']);
			// não aplicar delimitador de tags para templates internos ao framework
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