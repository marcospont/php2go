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
// $Header: /www/cvsroot/php2go/core/validation/RegexValidator.class.php,v 1.12 2006/06/15 00:29:12 mpont Exp $
// $Date: 2006/06/15 00:29:12 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		RegexValidator
// @desc		Classe que valida valores em rela��o a um padr�o de express�o regular
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.12 $
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$pattern = "[a-zA-Z]{3}[-][0-9]{4}";
//				if (Validator::validate('php2go.validation.RegexValidator', $value, array('pattern'=>$pattern, 'type'=>'POSIX'))) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class RegexValidator extends Validator
{
	var $pattern;			// @var pattern string	Padr�o de express�o regular para valida��o
	var $type = 'POSIX';	// @var type string		"POSIX" Tipo do padr�o de express�o regular: POSIX ou PCRE
	var $fieldLabel;		// @var fieldLabel string		R�tulo do campo que est� sendo validado
	var $errorMessage;		// @var errorMessage string		Mensagem de erro	
	
	//!-----------------------------------------------------------------
	// @function	RegexValidator::RegexValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Par�metros para o validador
	// @note		Conjunto de par�metros:
	//				pattern => Express�o regular
	//				type => Tipo do padr�o (POSIX ou PCRE)
	//!-----------------------------------------------------------------
	function RegexValidator($params = NULL) {
		parent::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['pattern']))
				$this->pattern = $params['pattern'];
			if (isset($params['type']) && in_array(strtoupper($params['type']), array('POSIX','PCRE')))
				$this->type = $params['type'];
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];	
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	RegexValidator::execute
	// @desc		Executa a valida��o de um valor em rela��o ao padr�o
	//				de express�o regular fornecido
	// @access		public
	// @param		value string	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		$value = TypeUtils::parseString($value);		
		if (!isset($this->pattern)) {
			$result = TRUE;
		} else {
			switch($this->type) {
				case 'POSIX' : 
					$expFunction = 'eregi';					
					break;
				case 'PCRE' : 
					$expFunction = 'preg_match'; 
					break;
				default : 
					$expFunction = 'eregi'; break;
			}
			$result = (@$expFunction($this->pattern, $value));
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID', $this->fieldLabel);
		return $result;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	RegexValidator::getError
	// @desc		Retorna a mensagem de erro resultante da valida��o
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>