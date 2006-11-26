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
// $Header: /www/cvsroot/php2go/core/cache/CacheManager.class.php,v 1.12 2006/07/22 13:42:33 mpont Exp $
// $Date: 2006/07/22 13:42:33 $

//!-----------------------------------------------------------------
import('php2go.cache.storage.*');
//!-----------------------------------------------------------------

// @const CACHE_MANAGER_LIFETIME "3600"
// Tempo padrуo de expiraчуo dos arquivos de cache criados pela classe
define('CACHE_MANAGER_LIFETIME', 3600);
// @const CACHE_MANAGER_GROUP "php2goCache"
// Nome padrуo para grupo de arquivos de cache
define('CACHE_MANAGER_GROUP', 'php2goCache');

//!-----------------------------------------------------------------
// @class		CacheManager
// @desc		Esta classe implementa um mecanismo simples de cache para qualquer informaчуo serializсvel.
//				A operaчуo de consulta ou escrita na cache deve conter, alщm da informaчуo a ser avaliada,
//				um ID њnico e um identificador opcional de grupo (categoria, divisуo).
//				Para ganho de  velocidade, tambщm pode ser utilizada cache em memѓria. Para aumento da
//				seguranчa, pode ser aplicada verificaчуo de checksum nas operaчѕes de leitura de dados da cache
// @package		php2go.cache
// @extends		PHP2Go
// @uses		DirectoryManager
// @uses		FileManager
// @uses		StringUtils
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.12 $
//!-----------------------------------------------------------------
class CacheManager extends PHP2Go
{
	var $currentId;			// @var currentId string		ID de cache atual
	var $currentGroup;		// @var currentGroup string		Grupo de cache atual
	var $Storage = NULL;	// @var Storage object			Camada de armazenamento de cache	

	//!-----------------------------------------------------------------
	// @function	CacheManager::CacheManager
	// @desc		Construtor da classe
	// @param		Storage AbstractCache object	"NULL" Driver de armazenamento
	// @note		Se um driver de armazenamento nуo for fornecido, o driver
	//				padrуo utilizado serс php2go.cache.storage.FileCache
	// @access		public
	//!-----------------------------------------------------------------
	function CacheManager($Storage=NULL) {
		parent::PHP2Go();
		if (!TypeUtils::isInstanceOf($Storage, 'AbstractCache'))
			$Storage = new FileCache();
		$this->Storage = $Storage;
		$this->Storage->initialize();
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::&getInstance
	// @desc		Retorna um singleton de um gerenciador de cache
	// @param		storage string	"NULL" Tipo de armazenamento
	// @note		Se nуo for fornecido um tipo de armazenamento, "file" serс utilizado
	// @return		CacheManager object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function &getInstance($storage=NULL) {
		static $instances = array();
		$storage = TypeUtils::ifNull($storage, 'file');
		if (!isset($instances[$storage])) {
			$instances[$storage] = CacheManager::factory($storage);
		}
		return $instances[$storage];
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::factory
	// @desc		Constrѓi uma nova instтncia da classe CacheManager
	// @param		storage string	Tipo de armazenamento
	// @return		CacheManager object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function factory($storage) {
		$class = ucfirst(strtolower($storage)) . 'Cache';
		if (class_exists($class)) {
			$Manager = new CacheManager(new $class());
			return $Manager;
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_LOAD_MODULE', $class), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	CacheManager::getLastStatus
	// @desc		Retorna o status da њltima consulta (load)
	// @note		As constantes da classe AbstractCache descrevem os possэveis 
	//				valores de retorno deste mщtodo
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getLastStatus() {
		return $this->Storage->currentStatus;
	}

	//!-----------------------------------------------------------------
	// @function	CacheManager::load
	// @desc		Procura por um objeto na cache, a partir de seu ID e grupo
	// @param		id string 		ID do objeto
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo de cache do objeto
	// @param		force bool		"FALSE" Ignorar ou nуo controle de expiraчуo
	// @access		public	
	// @return		mixed	
	//!-----------------------------------------------------------------
	function load($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		$this->currentId = $id;
		$this->currentGroup = $group;
		return $this->Storage->load($this->currentId, $this->currentGroup, $force);	
	}

	//!-----------------------------------------------------------------
	// @function	CacheManager::save
	// @desc		Insere um objeto na cache
	// @param		data mixed		Valor do objeto
	// @param		id string		ID de cache
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo de cache
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function save($data, $id=NULL, $group=CACHE_MANAGER_GROUP) {
		if (!empty($id)) {
			$this->currentId = $id;
			$this->currentGroup = $group;			
		}
		return $this->Storage->save($data, $this->currentId, $this->currentGroup);	
	}

	//!-----------------------------------------------------------------
	// @function	CacheManager::remove
	// @desc		Remove um objeto da cache
	// @param		id string		ID de cache
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo de cache
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function remove($id, $group=CACHE_MANAGER_GROUP) {
		$this->currentId = $id;
		$this->currentGroup = $group;
		return $this->Storage->remove($this->currentId, $this->currentGroup);
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::clear
	// @desc		Limpa dados em cache
	// @param		group string	"NULL" Grupo de cache
	// @note		Se for fornecido um grupo de cache, todos os dados deste grupo
	//				serуo removidos da cache. Se for omitido, todos os dados
	//				em cache que estiverem expirados serуo removidos
	// @access		public	
	// @return		bool	
	//!-----------------------------------------------------------------
	function clear($group=NULL) {
		return $this->Storage->clear($group);
	}	
}
?>