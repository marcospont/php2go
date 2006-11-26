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
// $Header: /www/cvsroot/php2go/core/xml/CacheableSAXParser.class.php,v 1.7 2006/08/23 02:47:44 mpont Exp $
// $Date: 2006/08/23 02:47:44 $

//------------------------------------------------------------------
import('php2go.cache.CacheManager');
import('php2go.xml.AbstractSAXParser');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		CacheableSAXParser
// @desc		Esta classe extende as funcionalidades da classe AbstractSAXParser
//				a fim de incluir um mecanismo autom�tico de cache para o conte�do
//				XML interpretado. Os m�todos abstratos loadCacheData e getCacheData
//				devem ser implementados por uma classe extendida
// @note		O ID de cache do conte�do XML � baseado em um hash do nome do arquivo 
//				(se for utilizado um nome de arquivo) ou do conte�do do arquivo
// @package		php2go.xml
// @extends		AbstractSAXParser
// @uses		CacheManager
// @version		$Revision: 1.7 $
// @author		Marcos Pont
//!-----------------------------------------------------------------
class CacheableSAXParser extends AbstractSAXParser
{
	var $cacheOptions = array();	// @var cacheOptions array			"array()" Conjunto de op��es de cache
	
	//!-----------------------------------------------------------------
	// @function	CacheableSAXParser::CacheableSAXParser
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function CacheableSAXParser() {
		parent::AbstractSAXParser();
		$this->cacheOptions['group'] = 'php2goSAXParser';
		$this->cacheOptions['lifeTime'] = NULL;
		$this->cacheOptions['useMTime'] = TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheableSAXParser::setCacheDir
	// @desc		Define o diret�rio base para persist�ncia dos arquivos
	//				de cache gerados pela classe
	// @param		dir string		Diret�rio base para cache
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------	
	function setCacheDir($dir) {
		$this->cacheOptions['baseDir'] = $dir;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheableSAXParser::setCacheGroup
	// @desc		Define o grupo de cache a ser utilizado
	// @param		group string	Grupo para os arquivos de cache criados
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------	
	function setCacheGroup($group) {
		$this->cacheOptions['group'] = $group;		
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheableSAXParser::setCacheLifeTime
	// @desc		Define o tempo de expira��o da cache
	// @param		lifeTime int	Tempo de expira��o, em segundos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------	
	function setCacheLifeTime($lifeTime) {
		$this->cacheOptions['lifeTime'] = $lifeTime;
		$this->cacheOptions['useMTime'] = FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheableSAXParser::setUseFileMTime
	// @desc		Indica se a verifica��o de expira��o da cache deve levar em conta
	//				a data de modifica��o do arquivo XML original que � fornecido
	//				para o m�todo parse()
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------	
	function setUseFileMTime($setting) {
		$this->cacheOptions['useMTime'] = (bool)$setting;
		if ($this->cacheOptions['useMTime'])
			$this->cacheOptions['lifeTime'] = NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheableSAXParser::loadCacheData
	// @desc		M�todo abstrato que recebe os dados recuperados da cache.
	//				Deve ser implementado pelas classes filhas
	// @param		data string		Dados recuperados da cache
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadCacheData($data) {
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheableSAXParser::getCacheData
	// @desc		M�todo abstrato que deve retornar os dados que devem
	//				ser serializados na cache
	// @note		Este m�todo somente � executado se a interpreta��o do 
	//				conte�do XML for realizada com sucesso
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function getCacheData() {
		return array();
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheableSAXParser::parse
	// @desc		Consulta na base de cache pela exist�ncia de uma c�pia
	//				serializada dos dados. Se a cache n�o for encontrada,
	//				interpreta o conte�do XML
	// @param		xmlContent string	Caminho de arquivo XML ou string XML
	// @param		srcType int			"T_BYFILE" T_BYFILE: arquivo, T_BYVAR: string
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function parse($xmlContent, $srcType=T_BYFILE) {
		$Cache = CacheManager::factory('file');
		if ($this->cacheOptions['baseDir'])
			$Cache->Storage->setBaseDir($this->cacheOptions['baseDir']);
		if ($srcType == T_BYFILE) {
			$cacheId = realpath($xmlContent);
			if ($this->cacheOptions['useMTime'])
				$Cache->Storage->setLastValidTime(@filemtime($xmlContent));
			elseif ($this->cacheOptions['lifeTime'] > 0)
				$Cache->Storage->setLifeTime($this->cacheOptions['lifeTime']);
		} else {
			$cacheId = dechex(crc32($xmlContent));
			($this->cacheOptions['lifeTime'] > 0) && ($Cache->Storage->setLifeTime($this->cacheOptions['lifeTime']));
		}
		$cacheData = $Cache->load($cacheId, $this->cacheOptions['group']);
		if ($cacheData !== FALSE) {
			$this->loadCacheData($cacheData);
			return TRUE;
		} else {
			$result = parent::parse($xmlContent, $srcType);
			if ($result)
				$Cache->save($this->getCacheData(), $cacheId, $this->cacheOptions['group']);
			return $result;
		}		
	}	
}
?>