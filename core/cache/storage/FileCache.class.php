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
// $Header: /www/cvsroot/php2go/core/cache/storage/FileCache.class.php,v 1.3 2006/11/19 18:05:46 mpont Exp $
// $Date: 2006/11/19 18:05:46 $

//!-----------------------------------------------------------------
import('php2go.cache.storage.AbstractCache');
//!-----------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FileCache
// @desc		Driver de armazenamento de cache baseado em arquivos
// @package		php2go.cache.storage
// @extends		AbstractCache
// @uses		System
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class FileCache extends AbstractCache
{
	var $baseDir;						// @var baseDir string			Diretório base. O valor padrão é o diretório temporário do servidor
	var $autoSerialize = TRUE;			// @var autoSerialize bool		"TRUE" Aplicar serialização automaticamente
	var $lockFiles = FALSE;				// @var lockFiles bool			"FALSE" Indica se arquivos devem ser travados para leitura/escrita
	var $obfuscateFileNames = TRUE;		// @var obfuscateFileNames bool	"TRUE" Ofuscar valores de ID e grupo ao criar nomes de arquivos

	//!-----------------------------------------------------------------
	// @function	FileCache::FileCache
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function FileCache() {
		parent::AbstractCache();
		$this->baseDir = System::getTempDir() . '/';
	}

	//!-----------------------------------------------------------------
	// @function	FileCache::setBaseDir
	// @desc		Define o diretório base para os arquivos de cache
	// @param		dir string	Diretório base
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setBaseDir($dir) {
		$dir = str_replace("\\", "/", $dir);
		$this->baseDir = (!preg_match("~\/$~", $dir) ? $dir . '/' : $dir);
	}

	//!-----------------------------------------------------------------
	// @function	FileCache::setFileLocking
	// @desc		Habilita/desabilita lock nas operações de leitura/escrita em arquivos
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFileLocking($setting) {
		$this->lockFiles = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	FileCache::setAutoSerialize
	// @desc		Define se os valores lidos/escritos devem ser
	//				serializados/unserializados automaticamente
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoSerialize($setting) {
		$this->autoSerialize = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	FileCache::setObfuscateFileNames
	// @desc		Define se os valores de ID e grupo devem ser ofuscados
	//				na criação dos nomes de arquivos de cache
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setObfuscateFileNames($setting) {
		$this->obfuscateFileNames = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	FileCache::read
	// @desc		Busca por um determinado ID/grupo de cache no sistema de arquivos
	// @param		id string		ID
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo
	// @param		force bool		"FALSE" Ignorar controle de expiração
	// @note		O método retornará FALSE se não for possível ler o conteúdo do arquivo
	// @note		Se o uso de checksum estiver habilitado, uma validação será realizada nos
	//				dados lidos do arquivo. Se o checksum não for válido, o método retornará FALSE
	//				e a expiração do arquivo em cache ser forçada
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function read($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		$fileName = $this->_getFileName($id, $group);
		$exists = file_exists($fileName);
		if ($exists) {
			clearstatcache();
			$mtime = filemtime($fileName);
			$this->_debug('File cache comparison: ' . date('d/m/Y H:i:s', $mtime) . ' <= ' . date('d/m/Y H:i:s', $this->lastValidTime));
			if ($mtime > $this->lastValidTime || $force) {
				$fp = @fopen($fileName, 'rb');
				if ($this->lockFiles)
					@flock($fp, LOCK_SH);
				if ($fp !== FALSE) {
					$size = filesize($fileName);
					if ($this->checksum) {
						$savedChecksum = fread($fp, $this->checksumLength);
						$savedData = fread($fp, $size-$this->checksumLength);
						$checksum = $this->_getChecksum($savedData);
						if ($this->lockFiles)
							@flock($fp, LOCK_UN);
						fclose($fp);
						if ($savedChecksum != $checksum) {
							$this->_debug('Checksum error');
							@touch($fileName, time()-abs($this->lifeTime*2));
							$this->currentStatus = CACHE_MISS;
							return FALSE;
						}
					} else {
						$savedData = fread($fp, $size);
						if ($this->lockFiles)
							@flock($fp, LOCK_UN);
						fclose($fp);
					}
					$this->currentStatus = CACHE_HIT;
					$this->_debug('File cache hit');
					if ($this->autoSerialize)
						$savedData = unserialize($savedData);
					return $savedData;
				} else {
					$this->_debug('Cache file read error');
				}
			} else {
				@unlink($fileName);
				$this->currentStatus = CACHE_STALE;
				$this->_debug('Cache file is stale');
			}
		} else {
			$this->_debug('Cache file doesn\'t exist');
		}
		$this->currentStatus = CACHE_MISS;
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	FileCache::write
	// @desc		Cria um arquivo de cache para um determinado ID/grupo
	// @param		data mixed		Dados
	// @param		id string		ID
	// @param		group string	"CACHE_MANAGER_GROUP"
	// @note		O método retornará FALSE se não for possvel escrever no arquivo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function write($data, $id, $group=CACHE_MANAGER_GROUP) {
		$fp = @fopen($this->_getFileName($id, $group), 'wb');
		if ($this->lockFiles)
			@flock($fp, LOCK_EX);
		if ($fp !== FALSE) {
			if ($this->autoSerialize)
				$data = serialize($data);
			if ($this->checksum) {
				$checksum = $this->_getChecksum($data);
				fwrite($fp, $checksum, $this->checksumLength);
			}
			fwrite($fp, $data);
			if ($this->lockFiles)
				@flock($fp, LOCK_UN);
			$this->_debug('Cache file written successfully');
			fclose($fp);
			return TRUE;
		}
		$this->_debug('File write error');
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	FileCache::remove
	// @desc		Remove um arquivo de cache a partir dos valores de ID/grupo
	// @param		id string		ID
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function remove($id, $group=CACHE_MANAGER_GROUP) {
		$fileName = $this->_getFileName($id, $group);
		if (!@unlink($fileName)) {
			if (!file_exists($fileName))
				return FALSE;
			return @touch($fileName, time()-abs($this->lifeTime*2));
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileCache::clear
	// @desc		Remove todos os arquivos correspondentes a um determinado
	//				grupo de cache ou todos os arquivos de cache expirados
	// @param		group string	"NULL" Grupo de cache (opcional)
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function clear($group=NULL) {
		parent::clear($group);
		$res = TRUE;
		$dir = @dir($this->baseDir);
		$pattern = preg_quote((!empty($group) ? ($this->obfuscateFileNames ? 'cache_' . md5($group) : 'cache_' . $group) : 'cache_'), '/');
		clearstatcache();
		while ($entry = $dir->read()) {
			if ($entry == '.' || $entry == '..' || is_dir($entry))
				continue;
			if (empty($group)) {
				if (preg_match($pattern, $entry) && filemtime($this->baseDir . $entry) <= $this->lastValidTime)
					$res = @unlink($this->baseDir . $entry);
			} else {
				if (preg_match($pattern, $entry))
					$res = @unlink($this->baseDir . $entry);
			}
			if (!$res)
				break;
		}
		return $res;
	}

	//!-----------------------------------------------------------------
	// @function	FileCache::_getFileName
	// @desc		Monta um caminho completo de arquivo baseado no
	//				diretório base e nos valores de ID e grupo fornecidos
	// @param		id string		ID de cache
	// @param		group string	Grupo de cache
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _getFileName($id, $group) {
		$id = preg_replace("/[^0-9a-zA-Z_\.\-\:]+/", '', $id);
		$group = preg_replace("/[^0-9a-zA-Z_\.\-\:]+/", '', $group);
		if ($this->obfuscateFileNames)
			return $this->baseDir . 'cache_' . md5($group) . '_' . md5($id);
		else
			return $this->baseDir . 'cache_' . $group . '_' . $id;
	}
}
?>