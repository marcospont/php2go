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
// $Header: /www/cvsroot/php2go/core/security/DistinguishedName.class.php,v 1.3 2006/02/28 21:55:59 mpont Exp $
// $Date: 2006/02/28 21:55:59 $

//!-----------------------------------------------------------------
// @class		DistinguishedName
// @desc		Esta classe implementa uma estrutura de Distinguished Name, originário
//				do padrão X.500, que é utilizada como uma chave única global. Alguns
//				exemplos de utilização de distinguished names são os dados de identificação
//				de usuários no protocolo LDAP e a estrutura de informações sobre um proprietário
//				ou sobre um provedor de um certificado digital
// @package		php2go.security
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.3 $
// @note		A estrutura de dados utilizada nesta classe está de acordo com 
//				a especificação contida nos RFCs 2253 e 1779
//!-----------------------------------------------------------------
class DistinguishedName extends PHP2Go 
{
	var $info;		// @var info array		Vetor contendo informações do DN (distinguished name)
	
	//!-----------------------------------------------------------------
	// @function	DistinguishedName::DistinguishedName
	// @desc		Construtor da classe
	// @access		public
	// @param		info mixed		Representação string ou array do DN
	//!-----------------------------------------------------------------
	function DistinguishedName($info) {
		parent::PHP2Go();
		if (TypeUtils::isArray($info)) {
			$this->info = array_change_key_case($info, CASE_UPPER);
		} else {
			$matches = array();
			$tmp = explode('/', $info);
			foreach ($tmp as $entry) {
				if (!empty($entry) && eregi("^([^=])=(.*)$", $entry, $matches))
					$this->info[strtoupper($matches[1])] = $matches[2];
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	DistinguishedName::getCommonName
	// @desc		Retorna a denominação comum do DN
	// @access		public
	// @return		string Denominação comum
	//!-----------------------------------------------------------------
	function getCommonName() {
		return (array_key_exists('CN', $this->info) ? $this->info['CN'] : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DistinguishedName::getEmail
	// @desc		Retorna o endereço de e-mail associado ao DN
	// @access		public
	// @return		string Endereço de e-mail
	//!-----------------------------------------------------------------
	function getEmail() {
		return (array_key_exists('EMAIL', $this->info) ? $this->info['EMAIL'] : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DistinguishedName::getCountry
	// @desc		Retorna o código ISO do país associado ao DN
	// @access		public
	// @return		string Código do país
	//!-----------------------------------------------------------------
	function getCountry() {
		return (array_key_exists('C', $this->info) ? $this->info['C'] : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DistinguishedName::getState
	// @desc		Retorna o nome do estado ou província associado ao DN
	// @access		public
	// @return		string Estado ou província
	//!-----------------------------------------------------------------
	function getState() {
		return (array_key_exists('ST', $this->info) ? $this->info['ST'] : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DistinguishedName::getLocality
	// @desc		Busca o nome da localidade (geralmente uma cidade) associada ao DN
	// @access		public
	// @return		string Nome da localidade
	//!-----------------------------------------------------------------
	function getLocality() {
		return (array_key_exists('L', $this->info) ? $this->info['L'] : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DistinguishedName::getOrganization
	// @desc		Retorna o nome da organização (razão social) associada ao DN
	// @access		public
	// @return		string Nome da organização
	//!-----------------------------------------------------------------
	function getOrganization() {
		return (array_key_exists('O', $this->info) ? $this->info['O'] : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DistinguishedName::getOrganizationalUnit
	// @desc		Retorna o nome da unidade organizacional (departamento) armazenada no DN
	// @access		public
	// @return		string Nome da unidade
	//!-----------------------------------------------------------------
	function getOrganizationalUnit() {
		return (array_key_exists('OU', $this->info) ? $this->info['OU'] : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DistinguishedName::toString
	// @desc		Monta a representação string do objeto, exibindo todas
	//				as informações disponíveis sobre o DN (distinguished name)
	// @access		public
	// @return		string Representação textual do objeto
	//!-----------------------------------------------------------------
	function toString() {
		$result = '';
		foreach ($this->info as $k => $v) {
			$result .= "/{$k}={$v}";
		}
		return $result;
	}
}
?>