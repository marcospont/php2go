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
// $Header: /www/cvsroot/php2go/core/cache/storage/AbstractCache.class.php,v 1.4 2006/10/26 04:42:39 mpont Exp $
// $Date: 2006/10/26 04:42:39 $

// @const CACHE_HIT "1"
// Status que indica que a њltima operaчуo get retornou os dados da cache com sucesso
define('CACHE_HIT', 1);
// @const CACHE_STALE "2"
// Status que indica que a њltima operaчуo get detectou dados expirados em cache
define('CACHE_STALE', 2);
// @const CACHE_MISS "3"
// Status que indica que a њltima operaчуo get nуo encontrou os dados em cache
define('CACHE_MISS', 3);
// @const CACHE_MEMORY_HIT "4"
// Status que indica que a њltima operaчуo get retornou os dados da cache em memѓria
define('CACHE_MEMORY_HIT', 4);

//!-----------------------------------------------------------------
// @class		AbstractCache
// @desc		Implementaчуo abstrata de um driver de armazenamento de cache.
//				Os mщtodos de leitura, escrita e remoчуo devem ser implementados
//				nas classes descendentes
// @package		php2go.cache.storage
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class AbstractCache extends PHP2Go
{
	var $lifeTime = CACHE_MANAGER_LIFETIME;		// @var lifeTime int			"CACHE_MANAGER_LIFETIME" Tempo de expiraчуo dos dados em cache
	var $lastValidTime;							// @var lastValidTime int		Timestamp mсximo em que um dado em cache щ vсlido
	var $currentStatus = CACHE_MISS;			// @var currentStatus int		"CACHE_MISS" кltimo status de leitura
	var $debug = FALSE;							// @var debug bool				"FALSE" Flag de debug
	var $memoryCache = FALSE;					// @var memoryCache bool		"FALSE" Indica que a cache em memѓria estс habilitada
	var $memoryCacheChanged = FALSE;			// @var memoryCacheChanged bool	"FALSE" Se este flag for FALSE, o valor da cache secundсria (memory cache) nуo serс salvo em disco pois nуo mudou
	var $memoryTable = array();					// @var memoryTable array		"array()" Espaчo de armazenamento da cache em memѓria
	var $memoryLimit = 100;						// @var memoryLimit int			"100" Limite da cache em memѓria
	var $memoryCacheGroup;						// @var memoryCacheGroup string	Grupo da cache em memѓria
	var $memoryFirstRead = TRUE;				// @var memoryFirstRead bool	"TRUE" Controle da primeira leitura da cache em memѓria
	var $checksum = TRUE;						// @var checksum bool			"TRUE" Flag que habilita ou desabilita checksum ao ler/escrever dados em cache
	var $checksumFunc = 'crc32';				// @var checksumFunc string		"crc32" Funчуo de checksum
	var $checksumLength = 32;					// @var checksumLength int		Tamanho da string de checksum
	var $autoCleanFrequency = 0;				// @var autoCleanFrequency int	"0" Indica a cada quantas escritas os dados expirados devem ser removidos (0 - desabilitado)
	var $writeCount = 0;						// @var writeCount int			"0" Controle de operaчѕes de escrita

	//!-----------------------------------------------------------------
	// @function	AbstractCache::AbstractCache
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function AbstractCache() {
		parent::PHP2Go();
		if ($this->isA('AbstractCache', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'AbstractCache'), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::__destruct
	// @desc		Destrutor da classe. Responsсvel por salvar o estado
	//				da cache em memѓria utilizando os mщtodos do driver de
	//				armazenamento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		if ($this->memoryCache && $this->memoryCacheChanged && !empty($this->memoryTable)) {
			$old = $this->debug;
			$this->debug = FALSE;
			if ($this->write($this->memoryTable, '__memCache', $this->memoryCacheGroup)) {
				$this->debug = $old;
				$this->_debug('Memory state saved');
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::initialize
	// @desc		Inicializa o driver de armazenamento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function initialize() {
		$this->lastValidTime = (time() - $this->lifeTime);
		PHP2Go::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::setLifeTime
	// @desc		Configura o tempo de expiraчуo
	// @param		time int	Tempo de expiraчуo, em segundos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLifeTime($time) {
		$oldLifeTime = $this->lifeTime;
		$lifeTime = abs(intval($time));
		if ($lifeTime > 0) {
			$this->lifeTime = $lifeTime;
			$this->lastValidTime = time() - $this->lifeTime;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MUST_BE_POSITIVE', array("\$lifeTime", 'setLifeTime')), E_USER_ERROR, __FILE__, __LINE__);
		}
		return $oldLifeTime;

	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::setLastValidTime
	// @desc		Configura o timestamp mсximo de um dado em cache
	// @param		time int	Unix timestamp
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLastValidTime($time) {
		$oldValidTime = $this->lastValidTime;
		$this->lifeTime = time() - $time;
		$this->lastValidTime = $time;
		return $oldValidTime;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::setDebug
	// @desc		Habilita/desabilita o modo de debug
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setDebug($setting=TRUE) {
		$this->debug = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::setMemoryCache
	// @desc		Define as configuraчѕes de cache em memѓria
	// @param		enable bool		Habilitar/desabilitar
	// @param		limit int		"100" Nmero mсximo de entradas
	// @param		group string	"NULL" Grupo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMemoryCache($enable, $limit=100, $group=NULL) {
		$this->memoryCache = (bool)$enable;
		if ($this->memoryCache) {
			$limit = abs(intval($limit));
			if ($limit > 0)
				$this->memoryLimit = $limit;
			else
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MUST_BE_POSITIVE', array("\$limit", 'setMemoryCache')), E_USER_ERROR, __FILE__, __LINE__);
			$this->memoryCacheGroup = TypeUtils::ifNull($group, CACHE_MANAGER_GROUP);
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::setReadChecksum
	// @desc		Habilita/desabilita controle de checksum na leitura/escrita em cache
	// @param		setting bool	Valor para o flag
	// @param		func string		"NULL" Funчуo de checksum
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setReadChecksum($setting, $func=NULL) {
		$this->checksum = (bool)$setting;
		if (!empty($func) && function_exists($func))
			$this->checksumFunc = $func;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::setAutoClean
	// @desc		Define a freqќъncia com que dados expirados em cache
	//				devem ser automaticamente removidos (0 - desabilitado,
	//				1 ou mais - nњmero de operaчѕes de escrita)
	// @param		frequency int	Freqќъncia
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoClean($frequency) {
		if ((int)$frequency > 0) {
			$this->autoCleanFrequency = $frequency;
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::load
	// @desc		Pesquisa por um ID/grupo nos dados em cache
	// @param		id string		ID
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo
	// @param		force bool		"FALSE" Ignorar controle de expiraчуo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function load($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		if ($this->memoryCache && $data = $this->_readMemory($id, $group))
			return $data;
		return $this->read($id, $group, $force);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::save
	// @desc		Salva um objeto em cache
	// @param		data mixed		Dado a ser salvo
	// @param		id string		ID de cache
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo de cache
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function save($data, $id, $group=CACHE_MANAGER_GROUP) {
		if ($this->autoCleanFrequency > 0) {
			if ($this->autoCleanFrequency == $this->writeCount) {
				$this->clear($group);
				$this->writeCount = 1;
			}
		}
		if ($this->memoryCache)
			$this->_writeMemory($data, $id, $group);
		return $this->write($data, $id, $group);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::read
	// @desc		Mщtodo abstrato de leitura de dados em cache
	// @param		id string		ID
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo
	// @param		force bool		"FALSE" Ignorar controle de expiraчуo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function read($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::write
	// @desc		Mщtodo abstrato de escrita de dados em cache
	// @param		data mixed		Dados
	// @param		id string		ID
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function write($data, $id, $group=CACHE_MANAGER_GROUP) {
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::remove
	// @desc		Mщtodo abstrato de remoчуo de um objeto da cache
	// @param		id string		ID
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function remove($id, $group=CACHE_MANAGER_GROUP) {
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::clear
	// @desc		Mщtodo de limpeza de dados expirados
	// @param		group string	"NULL" Grupo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function clear($group=NULL) {
		if ($this->memoryCache)
			$this->memoryTable = array();
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::_getChecksum
	// @desc		Calcula checksum para um determinado valor
	// @param		data mixed	Valor
	// @return		string Checksum
	// @access		protected
	//!-----------------------------------------------------------------
	function _getChecksum($data) {
		$func = $this->checksumFunc;
		$len = $this->checksumLength;
		switch($func) {
			case 'crc32' :
				return sprintf("% {$len}d", crc32($data));
			case 'md5' :
				return sprintf("% {$len}d", md5($data));
			case 'strlen' :
				return sprintf("% {$len}d", strlen($data));
			default :
				return sprintf("% {$len}d", crc32($data));
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::_readMemory
	// @desc		Busca por uma informaчуo na cache em memѓria
	// @param		id string		ID
	// @param		group string	Grupo
	// @access		protected
	// @return		mixed
	//!-----------------------------------------------------------------
	function _readMemory($id, $group) {
		if ($this->memoryFirstRead) {
			$old = $this->debug;
			$this->debug = FALSE;
			$data = $this->read('__memCache', $this->memoryCacheGroup);
			$this->debug = $old;
			if ($data) {
				$this->_debug('Memory state loaded - ' . sizeof($data) . ' entries');
				$this->memoryTable = $data;
			} else {
				$this->_debug('Memory state invalid or inexistent');
			}
			$this->memoryFirstRead = FALSE;
		}
		$key = $group . '-' . $id;
		if (array_key_exists($key, $this->memoryTable)) {
			$this->_debug('Memory cache hit');
			$this->currentStatus = CACHE_MEMORY_HIT;
			return $this->memoryTable[$key];
		}
		$this->_debug('Memory cache miss');
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::_writeMemory
	// @desc		Adiciona um objeto na cache em memѓria
	// @param		data mixed		Valor
	// @param		id string		ID
	// @param		group string	Grupo
	// @note		Quando o tamanho mсximo da cache em memѓria for alcanчado,
	//				a primeira entrada (na ordem de criaчуo) serс apagada
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function _writeMemory($data, $id, $group) {
		$key = $group . '-' . $id;
		$this->memoryTable[$key] = $data;
		$this->memoryCacheChanged = TRUE;
		if (sizeof($this->memoryTable) > $this->memoryLimit) {
			$this->_debug('Memory limit exceeded');
			list($key, $value) = each($this->memoryTable);
			unset($this->memoryTable[$key]);
		}
	}

	//!-----------------------------------------------------------------
	// @function	AbstractCache::_debug
	// @desc		Exibe uma mensagem de debug
	// @param		msg string	Mensagem
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function _debug($msg) {
		if ($this->debug)
			println("CACHE DEBUG (" . parent::getClassName() . ") --- {$msg}");
	}
}
?>