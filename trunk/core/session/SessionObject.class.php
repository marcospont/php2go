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
// $Header: /www/cvsroot/php2go/core/session/SessionObject.class.php,v 1.21 2006/06/22 23:39:06 mpont Exp $
// $Date: 2006/06/22 23:39:06 $

//------------------------------------------------------------------
import('php2go.datetime.TimeCounter');
import('php2go.session.SessionManager');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		SessionObject
// @desc		Permite a criaчуo de um objeto dinтmico de sessуo atravщs
// 				da possibilidade de criar propriedades, contadores de tempo
// 				e statements para consulta SQL ou parametrizaчуo dinтmica
// 				de funчѕes. Permite armazenar na sessуo informaчѕes mais
// 				complexas do que em SessionManager
// @package		php2go.session
// @extends 	SessionManager
// @uses 		Statement
// @uses 		TimeCounter
// @author 		Marcos Pont
// @version		$Revision: 1.21 $
//!-----------------------------------------------------------------
class SessionObject extends SessionManager
{
	var $name;						// @var name string				Nome do objeto de sessуo
	var $id;						// @var id string				ID da sessуo
	var $registered = FALSE;		// @var registered bool			"FALSE" Indica se o objeto estс registrado na sessуo
	var $properties = array();		// @var properties array		"array()" Vetor de propriedades do objeto
	var $timeCounters = array(); 	// @var timeCounters array		"array()" Vetor de cronєmetros/contadores de tempo do objeto

	//!-----------------------------------------------------------------
	// @function	SessionObject::SessionObject
	// @desc		Construtor da classe. Inicializa as propriedades da classe
	//				a partir da sessуo se o objeto estiver registrado
	// @access 		public
	//!-----------------------------------------------------------------
	function SessionObject($name) {
		parent::SessionManager();
		if (parent::isRegistered($name)) {
			if (is_array($_SESSION[$name])) {
				$props = $_SESSION[$name];
				foreach ($props as $name => $value) {
					if ($name == 'timeCounters') {
						foreach ($value as $tName => $tData) {
							$this->timeCounters[$tName] = new TimeCounter($tData['begin']);
							$this->timeCounters[$tName]->active = $tData['active'];
							$this->timeCounters[$tName]->end = $tData['end'];
						}
					} else {
						$this->{$name} = $value;
					}
				}
			}
		} else {
			$this->name = $name;
			$this->id = parent::getSessionId();
			$this->registered = FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::register
	// @desc		Sobrecarrega o mщtodo SessionManager::register para gravar
	//				na variсvel de sessуo as propriedades do objeto na forma de um array
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function register() {
		$this->_serialize();
		if (parent::isRegistered($this->name))
			$this->registered = TRUE;
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::update
	// @desc		Atualiza o conteњdo do objeto na sessуo
	// @note		Este mщtodo deve ser executado sempre que uma propriedade
	//				do objeto tiver seu valor alterado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function update() {
		$this->_serialize();
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::unregister
	// @desc		Sobrecarrega o mщtodo SessionManager::unregister para
	//				remover o objeto do vetor de sessуo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function unregister() {
		if (parent::unregister($this->name)) {
			$this->registered = FALSE;
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::isRegistered
	// @desc		Verifica se o objeto estс registrado na sessуo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isRegistered() {
		$this->registered = parent::isRegistered($this->name);
		return $this->registered;
	}

	//!-----------------------------------------------------------------
	// @function 	SessionObject::createProperty
	// @desc 		Cria uma propriedade no objeto de sessуo com o
	// 				nome $pName, podendo inicializс-la com o valor
	// 				$pValue. Adicionalmente, $pName pode ser um array
	// 				associativo de propriedades a serem criadas
	// @access 		public
	// @param 		pName mixed		Nome da propriedade ou vetor de propriedades
	// @param 		pValue mixed	"" Valor da propriedade
	// @return		void
	//!-----------------------------------------------------------------
	function createProperty($pName, $pValue='') {
		if (TypeUtils::isArray($pName)) {
			foreach($pName as $prop => $val)
				$this->properties[$prop] = $val;
		} else {
			$this->properties[$pName] = $pValue;
		}
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::hasProperty
	// @desc		Verifica se uma determinada propriedade existe no objeto
	// @param		pName string	Nome da propriedade
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasProperty($pName) {
		return (isset($this->properties[$pName]));
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::getPropertyValue
	// @desc 		Busca o valor definido para uma propriedade
	// @param 		pName string		Nome da propriedade
	// @param 		throwError bool		"TRUE" Disparar ou nуo erro de propriedade nуo encontrada
	// @return 		mixed Valor da propriedade, se existente
	// @access 		public
	//!-----------------------------------------------------------------
	function getPropertyValue($pName, $throwError=TRUE) {
		if (!isset($this->properties[$pName])) {
			if ($throwError)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SESSION_PROPERTY_NOT_FOUND', array($pName, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			return $this->properties[$pName];
		}
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::&getPropertyValueByRef
	// @desc 		Busca uma referъncia para o valor de uma propriedade do objeto de sessуo
	// @access 		public
	// @param 		pName string		Nome da propriedade
	// @param 		throwError bool		"TRUE" Disparar ou nуo erro de propriedade nуo encontrada
	// @return 		mixed Valor da propriedade, se existente
	//!-----------------------------------------------------------------
	function &getPropertyValueByRef($pName, $throwError=TRUE) {
		$result = FALSE;
		if (isset($this->properties[$pName]))
			$result =& $this->properties[$pName];
		elseif ($throwError)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SESSION_PROPERTY_NOT_FOUND', array($pName, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::getProperties
	// @desc		Busca o vetor de propriedades do objeto de sessуo
	// @return		array Vetor de propriedades
	// @access		public
	//!-----------------------------------------------------------------
	function getProperties() {
		return $this->properties;
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::comparePropertyValue
	// @desc		Mщtodo utilitсrio para comparaчуo do valor de uma propriedade
	//				com um determinado valor, utilizando strict comparison ou nуo
	// @access		public
	// @param		pName string	Nome da propriedade
	// @param		cValue mixed	Valor de comparaчуo
	// @param		strict bool		"FALSE" Utilizar ou nуo strict comparison
	// @return		bool
	//!-----------------------------------------------------------------
	function comparePropertyValue($pName, $cValue, $strict=FALSE) {
		$pValue = $this->getPropertyValue($pName, FALSE);
		return ($strict ? $pValue === $cValue : $pValue == $cValue);
	}

	//!-----------------------------------------------------------------
	// @function 	SessionObject::&getTimeCounter
	// @desc 		Busca um objeto TimeCounter (cronєmetro) a partir de seu nome
	// @access 		public
	// @param 		tName string		Nome do cronєmetro
	// @param 		throwError bool		"TRUE" Disparar ou nуo erro de cronєmetro nуo encontrado
	// @return 		TimeCounter object Cronєmetro correspondente, se existente
	//!-----------------------------------------------------------------
	function &getTimeCounter($tName, $throwError=TRUE) {
		$result = FALSE;
		if (isset($this->timeCounters[$tName]))
			$result =& $this->timeCounters[$tName];
		elseif ($throwError)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SESSION_TIMECOUNTER_NOT_FOUND', array($tName, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::setPropertyValue
	// @desc 		Atribui um valor a uma propriedade
	// @param 		pName string	Nome da propriedade ou vetor associativo de propriedades
	// @param 		pValue mixed	"" Valor para a propriedade
	// @note		Se um vetor associativo for passado no parтmetro $pName,
	//				o parтmetro $pValue pode ser omitido
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setPropertyValue($pName, $pValue='') {
		if (TypeUtils::isHashArray($pName)) {
			foreach ($pName as $name => $value) {
				if (!$this->hasProperty($name))
					$this->createProperty($name, $value);
				else
					$this->properties[$name] = $value;
			}
		} else {
			if (!$this->hasProperty($pName))
				$this->createProperty($pName, $pValue);
			else
				$this->properties[$pName] = $pValue;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	SessionObject::createTimeCounter
	// @desc 		Inicializa um cronєmetro do tipo TimeCounter para
	// 				o objeto de sessуo, com nome $tName e podendo opcionalmente
	// 				iniciar no timestamp indicado por $begin
	// @access 		public
	// @param 		tName string	Nome para o cronєmetro
	// @param 		begin int		"0" Timestamp inicial para a contagem, padrуo щ 0
	// @return		void
	//!-----------------------------------------------------------------
	function createTimeCounter($tName, $begin=0) {
		$this->timeCounters[$tName] = new TimeCounter($begin);
	}

	//!-----------------------------------------------------------------
	// @function	SessionObject::_serialize
	// @desc		Realiza a persistъncia dos dados do objeto na sessуo
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _serialize() {
		$vars = get_object_vars($this);
		$tmp = $vars['timeCounters'];
		foreach ($tmp as $tName => $tValue) {
			$vars['timeCounters'][$tName] = array(
				'begin' => $tValue->begin,
				'active' => $tValue->active,
				'end' => (isset($tValue->end) ? $tValue->end : NULL)
			);
		}
		$_SESSION[$this->name] = $vars;
	}
}
?>