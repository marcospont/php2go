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
// $Header: /www/cvsroot/php2go/core/Conf.class.php,v 1.19 2006/10/26 04:21:40 mpont Exp $
// $Date: 2006/10/26 04:21:40 $

//!-----------------------------------------------------------------
// @class		Conf
// @desc		Classe que gerencia os dados de configuraчуo do framework,
//				carregando os dados a partir de arquivos, buscando os valores
//				de entradas da configuraчуo ou modificando-as
// @author		Marcos Pont
// @version		$Revision: 1.19 $
//!-----------------------------------------------------------------
class Conf
{
	var $config;	// @var config array		Vetor com os dados de configuraчуo do framework

	//!-----------------------------------------------------------------
	// @function	Conf::Conf
	// @desc		Construtor da classe Conf
	// @access		public
	//!-----------------------------------------------------------------
	function Conf() {
	}

	//!-----------------------------------------------------------------
	// @function	Conf::&getInstance
	// @desc		Retorna uma instтncia њnica (singleton) da classe Conf
	// @access		public
	// @return		Conf object
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new Conf;
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	Conf::loadConfig
	// @desc		Carrega o conteњdo da configuraчуo do sistema a partir
	//				de um mѓdulo (arquivo no servidor)
	// @access		public
	// @param		configModule string	Caminho do mѓdulo
	// @return		void
	//!-----------------------------------------------------------------
	function loadConfig($configModule) {
		$this->config = includeFile($configModule, TRUE);
	}

	//!-----------------------------------------------------------------
	// @function	Conf::getConfig
	// @desc		Busca o valor de uma chave no vetor de configuraчѕes
	// @access		public
	// @param		configName string		Nome da entrada
	// @param		fallback mixed			"FALSE" Valor a ser retornado se a chave nуo for encontrada
	// @return		mixed Valor da entrada ou FALSE se ela nуo for encontrada
	//!-----------------------------------------------------------------
	function getConfig($configName, $fallback=FALSE) {
		return findArrayPath($this->config, $configName, '.', $fallback);
	}

	//!-----------------------------------------------------------------
	// @function	Conf::&getAll
	// @desc		Retorna o conjunto completo de configuraчѕes registradas
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function &getAll() {
		return $this->config;
	}

	//!-----------------------------------------------------------------
	// @function	Conf::getConnectionParameters
	// @desc		Mщtodo estсtico que busca no vetor de configuraчуo as propriedades
	//				de conexуo com o banco de dados
	// @access		public
	// @param		connectionId string		"NULL" ID da conexуo desejada
	// @return		array Vetor contendo os parтmetros da conexуo
	// @note		Se o parтmetro $cid for omitido, a conexуo default serс utilizada
	// @static
	//!-----------------------------------------------------------------
	function getConnectionParameters($connectionId=NULL) {
		$Conf =& Conf::getInstance();
		$connections = $Conf->getConfig('DATABASE.CONNECTIONS');
		if (is_array($connections)) {
			// foi solicitado um determinado ID de conexуo
			if ($connectionId !== NULL) {
				if (isset($connections[$connectionId]))
					$params = (array)$connections[$connectionId];
			} else {
				// ID de conexуo default
				$connectionId = $Conf->getConfig('DATABASE.DEFAULT_CONNECTION');
				if ($connectionId) {
					if (isset($connections[$connectionId]))
						$params = (array)$connections[$connectionId];
				} else {
					// nуo existe uma conexуo default, utilizar a primeira conexуo na lista
					list($connectionId, $value) = each($connections);
					if (is_array($value))
						$params = $value;
				}
			}
			// conexуo nуo encontrada
			if (!isset($params))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_DATABASE_PARAMETERS', $connectionId), E_USER_ERROR, __FILE__, __LINE__);
			if (array_key_exists('PERSISTENT', $params))
				$params['PERSISTENT'] = (bool)$params['PERSISTENT'];
			$params['ID'] = $connectionId;
		} else {
			$connectionId = 'DEFAULT';
			$params = array(
				'HOST' => TypeUtils::ifFalse($Conf->getConfig('DATABASE_HOST'), ''),
				'USER' => $Conf->getConfig('DATABASE_USER'),
				'PASS' => $Conf->getConfig('DATABASE_PASS'),
				'BASE' => $Conf->getConfig('DATABASE_BASE'),
				'TYPE' => $Conf->getConfig('DATABASE_TYPE'),
				'PERSISTENT' => ($Conf->getConfig('DATABASE_PCONNECTION') === TRUE),
				'AFTERCONNECT' => $Conf->getConfig('DATABASE_AFTERCONNECT'),
				'BEFORECLOSE' => $Conf->getConfig('DATABASE_BEFORECLOSE')
			);
			$params['ID'] = $connectionId;
		}
		if (!empty($params['DSN']) || (!empty($params['USER']) && !empty($params['TYPE'])))
			return $params;
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATABASE_PARAMETERS', $connectionId), E_USER_ERROR, __FILE__, __LINE__);
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Conf::setConfig
	// @desc		Define um valor para uma entrada do vetor de configuraчѕes
	//				ou adiciona/substitui valor ao vetor principal de configuraчуo
	// @access		public
	// @param		configName mixed	Vetor de configuraчуo a ser utilizado ou nome da entrada a ser setada
	// @param		configValue mixed	"" Valor para a entrada
	// @return		void
	// @note		Exemplos das duas possibilidades do mщtodo setConfig:
	//				$Conf->setConfig(array('foo'=>'bar', 'baz'=>'xpto')); ou
	//				$Conf->setConfig('foo', 'bar');
	//!-----------------------------------------------------------------
	function setConfig($configName, $configValue='') {
		// TODO: Permitir setar chaves internas de array utilizando uma string de caminho
		if (is_array($configName) && trim($configValue) == '') {
			if (isset($this->config))
				$this->config = array_merge($this->config, $configName);
			else
				$this->config = $configName;
		} else {
			$this->config[$configName] = $configValue;
		}
	}
}
?>