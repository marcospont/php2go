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
// $Header: /www/cvsroot/php2go/core/util/Assertion.class.php,v 1.10 2006/06/18 18:45:00 mpont Exp $
// $Date: 2006/06/18 18:45:00 $

//!-----------------------------------------------------------------
// @class		Assertion
// @desc		A classe Assertion é uma interface para o mecanismo de
//				avaliação de expressões do PHP com as funções assert() e
//				assert_options(), que permitem a avaliação de expressões
//				e o tratamento de valores que diferem do valor esperado
// @package		php2go.util
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.10 $
//!-----------------------------------------------------------------
class Assertion extends PHP2Go
{
	var $active = 1;		// @var active int			"1" Indica se a avaliação de expressões está ativa
	var $warning = 0;		// @var warning int			"0" Se 1, indica se uma expressão falsa deve disparar um alerta
	var $quiet = 1;			// @var quiet int			"1" Se 1, indica que deve ser feita avaliação silenciosa das expressões
	var $bail = 0;			// @var bail int			"0" Se 1, indica que uma expressão falsa deve parar a execução do script
	var $callback = NULL;	// @var callback string		"NULL" Indica a função que deverá tratar as expressões falsas

	//!-----------------------------------------------------------------
	// @function	Assertion::Assertion
	// @desc		Construtor da classe Assertion, inicializa os parâmetros
	// @access		public
	//!-----------------------------------------------------------------
	function Assertion() {
		parent::PHP2Go();
		$this->_setOption(ASSERT_ACTIVE, $this->active);
		$this->_setOption(ASSERT_WARNING, $this->warning);
		$this->_setOption(ASSERT_QUIET_EVAL, $this->quiet);
		$this->_setOption(ASSERT_BAIL, $this->bail);
		$this->_setOption(ASSERT_CALLBACK, 'php2GoAssertionHandler');
	}

	//!-----------------------------------------------------------------
	// @function	Assertion::deactivate
	// @desc		Desativa o mecanismo de avaliação de expressões
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function deactivate() {
		$this->active = 0;
		$this->_setOption(ASSERT_ACTIVE, $this->active);
	}

	//!-----------------------------------------------------------------
	// @function	Assertion::activate
	// @desc		Ativa o mecanismo de avaliação de expressões
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function activate() {
		$this->active = 1;
		$this->_setOption(ASSERT_ACTIVE, $this->active);
	}

	//!-----------------------------------------------------------------
	// @function	Assertion::enableWarning
	// @desc		Habilita a exibição de alertas quando uma expressão falsa for encontrada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function enableWarning() {
		$this->warning = 1;
		$this->_setOption(ASSERT_WARNING, $this->warning);
	}

	//!-----------------------------------------------------------------
	// @function	Assertion::enableBail
	// @desc		Habilita a parada fatal do script quando uma expressão falsa for encontrada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function enableBail() {
		$this->bail = 1;
		$this->_setOption(ASSERT_BAIL, $this->bail);
	}

	//!-----------------------------------------------------------------
	// @function	Assertion::setCallback
	// @desc		Configura uma função para o tratamento customizado das expressões falsas
	// @access		public
	// @param		callback string	Nome da função
	// @return		void
	// @note		A função deve ser construída com três parâmetros de entrada: file, line e
	//				code, que representam o arquivo e a linha onde a expressão foi capturada e
	//				o código da expressão
	//!-----------------------------------------------------------------
	function setCallback($callback) {
		$this->callback = $callback;
		$this->_setOption(ASSERT_CALLBACK, $callback);
	}

	//!-----------------------------------------------------------------
	// @function	Assertion::evaluate
	// @desc		Avalia uma determinada expressão, pressupondo que o retorno
	//				desta expressão é booleano
	// @access		public
	// @param		expression mixed	Expressão a ser avaliada
	// @param		file string		"" Arquivo onde a avaliação é feita, opcional do usuário
	// @param		line int			"0" Linha do arquivo, opcional do usuário
	// @return		bool
	//!-----------------------------------------------------------------
	function evaluate($expression, $file = '', $line = 0) {
		if ($file != '')
			Registry::set('PHP2Go_assertion_file', $file);
		if ($line != 0)
			Registry::set('PHP2Go_assertion_line', $line);
		return assert($expression);
	}

	//!-----------------------------------------------------------------
	// @function	Assertion::_setOption
	// @desc		Configura o valor de uma opção do mecanismo de asserção do PHP
	// @access		private
	// @param		option int	Constante que representa o valor a ser configurado
	// @param		value mixed	Valor a ser atribuído à opção
	// @return		void
	//!-----------------------------------------------------------------
	function _setOption($option, $value) {
		assert_options($option, $value);
	}
}
?>
