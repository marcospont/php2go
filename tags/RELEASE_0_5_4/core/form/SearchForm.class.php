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
// $Header: /www/cvsroot/php2go/core/form/SearchForm.class.php,v 1.15 2006/11/19 18:30:28 mpont Exp $
// $Date: 2006/11/19 18:30:28 $

//------------------------------------------------------------------
import('php2go.db.QueryBuilder');
import('php2go.form.FormBasic');
import('php2go.form.FormTemplate');
import('php2go.net.Url');
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		SearchForm
// @desc		Esta classe implementa um formul�rio espec�fico para sistemas
//				de pesquisa. Seu objetivo � construir, a partir da parametriza��o
//				de pesquisa definida para cada campo do formul�rio, uma cl�usula de
//				condi��o que ser� utilizada em uma consulta de banco de dados. Ap�s
//				montada esta consulta, a classe pode retornar o c�digo SQL produzido
//				ou armazen�-lo na sess�o, para que este possa ser utilizado como filtro
//				de um DataSet ou de um Report em uma outra requisi��o
// @package		php2go.form
// @extends		Component
// @uses		Callback
// @uses		Db
// @uses		FormBasic
// @uses		FormTemplate
// @uses		SessionManager
// @uses		TypeUtils
// @uses		Url
// @author		Marcos Pont
// @version		$Revision: 1.15 $
//!-----------------------------------------------------------------
class SearchForm extends Component
{
	var $valid;							// @var valid bool					Armazena o status de valida��o da busca
	var $searchRawData = array();		// @var searchRawData array			"array()" Vetor que armazena os dados crus de retorno da pesquisa (cada linha cont�m valor e configura��es de cada campo)
	var $searchString = '';				// @var searchString string			"" Cont�m a string de pesquisa j� montada (cl�usula SQL de condi��o)
	var $searchDescription = '';		// @var searchDescription string	"" Cont�m a vers�o "human readable" da cl�usula de condi��o montada pela classe
	var $mainOperator = 'AND';			// @var mainOperator string			"AND" Operador principal a ser utilizado (AND ou OR)
	var $prefixOperator = '';			// @var prefixOperator string		"" Operador a ser inclu�do no in�cio da cl�usula (AND ou OR)
	var $acceptEmptySearch = FALSE;		// @var acceptEmptySearch bool		"FALSE" Indica se a pesquisa poder� ser submetida sem filtros
	var $emptySearchMessage;			// @var emptySearchMessage string	Mensagem de erro exibida para uma busca sem filtros, quando n�o permitida
	var $ignoreFields = array();		// @var ignoreFields array			Conjunto de campos que devem ser ignorados na constru��o da cl�usula de condi��o
	var $stringMinLength;				// @var stringMinLength int			Restri��o de m�nimo de caracteres para um campo que usa operadores string (CONTAINING, STARTING, ENDING)
	var $autoRedirect = FALSE;			// @var autoRedirect bool			"FALSE" Indica se uma busca v�lida ser� redirecionada para outra URL
	var $redirectUrl;					// @var redirectUrl string			URL para onde os resultados da busca devem ser enviados
	var $paramName = 'p2g_search';		// @var paramName string			"p2g_search" Vari�vel de requisi��o ou de sess�o para a cl�usula de busca
	var $useSession = FALSE;			// @var useSession bool				"FALSE" Utilizar sess�o para a persist�ncia da cl�usula
	var $useEncode = FALSE;				// @var useEncode bool				"FALSE" Utilizar codifica��o base64 no envio da cl�usula por GET
	var $preserveSession = FALSE;		// @var preserveSession bool		"FALSE" Preservar o filtro armazenado na sess�o (TRUE) ou deletar quando o formul�rio de pesquisa for gerado e exibido (FALSE)
	var $filterPersistence = FALSE;		// @var filterPersistence array		"array()" Configura��es de persist�ncia dos filtros de pesquisa
	var $connectionId = NULL;			// @var connectionId string			"NULL" ID da conex�o ao banco de dados (uma conex�o � utilizada para formatar strings e datas)
	var $checkboxMapping = array(		// @var checkboxMapping array		Mapa de convers�o para um campo do tipo checkbox
		'T' => 1,
		'F' => 0
	);
	var $validators = array();			// @var validators array			"array()" Vetor de validadores da pesquisa
	var $callbackObj = NULL;			// @var callbackObj object			"NULL" Possibilita a associa��o de um objeto para os callbacks definidos na especifica��o XML
	var $sqlCallbacks = array();		// @var sqlCallbacks array			"array()" Armazena callbacks que constr�em o c�digo SQL para um campo de pesquisa independente dos outros par�metros de configura��o
	var $valueCallbacks = array();		// @var valueCallbacks array		"array()" Armazena callbacks de transforma��o de valor j� utilizadas
	var $Form = NULL;					// @var Form Form object			Formul�rio constru�do pela classe para exibi��o dos filtros

	//!-----------------------------------------------------------------
	// @function	SearchForm::SearchForm
	// @desc		Construtor da classe
	// @param		xmlFile string	Arquivo XML de especifica��o do formul�rio
	// @param		templateFile string "NULL" Template para o formul�rio. Se for NULL, a classe FormBasic ser� utilizada para montar o formul�rio
	// @param		formName string	Nome para o formul�rio
	// @param		&Doc Document object Documento ao qual o formul�rio est� associado
	// @param		tplIncludes array array()" Vetor de valores para blocos de inclus�o no template
	// @access		public
	//!-----------------------------------------------------------------
	function SearchForm($xmlFile, $templateFile=NULL, $formName, &$Doc, $tplIncludes=array()) {
		parent::Component();
		if (TypeUtils::isNull($templateFile))
			$this->Form = new FormBasic($xmlFile, $formName, $Doc);
		else
			$this->Form = new FormTemplate($xmlFile, $templateFile, $formName, $Doc, $tplIncludes);
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::isValid
	// @desc		Verifica se o formul�rio de busca foi postado e � v�lido,
	//				seguindo apenas as regras de valida��o b�sicas do formul�rio
	// @note		A valida��o segundo os validadores de busca s� � executada dentro
	//				do m�todo run() desta mesma classe
	// @see			SearchForm::run
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if (!isset($this->valid))
			$this->valid = ($this->Form->isPosted() && $this->Form->isValid());
		if (!$this->valid && $this->useSession && !$this->preserveSession) {
			unset($_SESSION[$this->paramName]);
			unset($_SESSION[$this->paramName . '_description']);
		}
		return $this->valid;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::getSearchRawData
	// @desc		Retorna um vetor com os valores e configura��es dos campos de pesquisa
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getSearchRawData() {
		return $this->searchRawData;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::getSearchString
	// @desc		Retorna a cl�usula de condi��o montada a partir dos valores submetidos na pesquisa
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getSearchString() {
		return $this->searchString;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::getSearchDescription
	// @desc		Retorna a representa��o textual da cl�usula de busca
	//				constru�da, substituindo nomes de campos por seus r�tulos,
	//				operadores por seus nomes na linguagem ativa, e valores por
	//				sua representa��o compreens�vel
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getSearchDescription() {
		return $this->searchDescription;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::getMainOperator
	// @desc		Retorna o operador principal configurado na classe
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getMainOperator() {
		return $this->mainOperator;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setMainOperator
	// @desc		Define o operador principal a ser utilizado entre as
	//				cl�usulas de cada campo de pesquisa. Aceita os valores AND e OR
	// @param		operator string	Operador principal da busca
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMainOperator($operator) {
		$operator = strtoupper($operator);
		if ($operator == 'OR' || $operator == 'AND')
			$this->mainOperator = $operator;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::getPrefixOperator
	// @desc		Retorna o operador configurado para prefixar a cl�usula de pesquisa
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getPrefixOperator() {
		return $this->prefixOperator;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setPrefixOperator
	// @desc		Define o operador que deve prefixar a cl�usula de pesquisa.
	//				Aceita os valores AND e OR
	// @note		Esta configura��o deve ser utilizada quando a consulta base onde a
	//				cl�usula de pesquisa ser� utilizada j� possui uma cl�usula de condi��o.
	//				Desta forma, a cl�usula vinda do formul�rio seria combinada com a existente
	//				utilizando o operador AND ou o operador OR
	// @param		operator string	Operador que deve prefixar a cl�usula montada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setPrefixOperator($operator) {
		$operator = strtoupper($operator);
		if ($operator == 'OR' || $operator == 'AND')
			$this->prefixOperator = $operator;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setAcceptEmptySearch
	// @desc		Utilizando este m�todo com o par�metro $setting == TRUE,
	//				as buscas sem filtros estar�o habilitadas, ou seja, n�o
	//				gerar�o erro e reapresenta��o do formul�rio
	// @param		setting bool "TRUE" Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAcceptEmptySearch($setting=TRUE) {
		$this->acceptEmptySearch = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setEmptySearchMessage
	// @desc		Define a mensagem de erro para uma busca enviada sem filtros
	// @param		message string	Mensagem de erro
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setEmptySearchMessage($message) {
		$this->emptySearchMessage = $message;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::getIgnoreFields
	// @desc		Retorna o conjunto de campos que devem ser ignorados
	//				na constru��o da cl�usula de pesquisa
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getIgnoreFields() {
		return $this->ignoreFields;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setIgnoreFields
	// @desc		Define o conjunto de campos que devem ser ignorados
	//				na valida��o do formul�rio de pesquisa e na constru��o
	//				da cl�usula SQL de pesquisa
	// @note		A compara��o dos nomes desta lista com os nomes dos
	//				campos do formul�rio � sens�vel ao caso
	// @param		fields array	Array contendo os nomes dos campos (atributo NAME na especifica��o XML)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setIgnoreFields($fields) {
		$this->ignoreFields = TypeUtils::toArray($fields);
	}

	//!-----------------------------------------------------------------
	// @function	SearcForm::setStringMinLength
	// @desc		Define o tamanho m�nimo de caracteres para campos que
	//				utilizam operadores string (STARTING, ENDING, CONTAINING)
	// @note		Por padr�o, n�o � feita valida��o nesse sentido, ou seja,
	//				um filtro onde apenas um caractere � fornecido poderia comprometer
	//				a performance da consulta
	// @param		minlength int	Tamanho m�nimo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setStringMinLength($minlength) {
		if (TypeUtils::isInteger($minlength) && $minlength > 0) {
			$this->stringMinLength = $minlength;
		}
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setAutoRedirect
	// @desc		Permite definir uma URL de redirecionamento para a pesquisa
	// @param		setting bool		"TRUE" Utilizar ou n�o redirecionamento autom�tico
	// @param		redirectUrl string	URL de redirecionamento
	// @param		paramName string	Par�metro de requisi��o ou de sess�o para armazenamento da cl�usula montada
	// @param		useSession bool		"FALSE" Utilizar sess�o na persist�ncia da cl�usula
	// @param		useEncode bool		"FALSE" Utilizar codifica��o base64 no envio da cl�usula na requisi��o GET
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoRedirect($setting=TRUE, $url, $paramName='p2g_search', $useSession=FALSE, $useEncode=FALSE) {
		$this->autoRedirect = (bool)$setting;
		if ($this->autoRedirect) {
			$this->redirectUrl = $url;
			$this->paramName = $paramName;
			$this->useSession = (bool)$useSession;
			$this->useEncode = (bool)$useEncode;
		}
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setPreserveSession
	// @desc		Define se o filtro armazenado na sess�o deve ser preservado ou
	//				deve ser deletado a cada vez que o formul�rio de pesquisa for
	//				gerado e exibido
	// @note		Se os filtros forem submetidos como um par�metro GET da requisi��o,
	//				o valor desta propriedade � indiferente
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setPreserveSession($setting) {
		$this->preserveSession = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setFilterPersistence
	// @desc		Define se os valores submetidos pelo formul�rio de
	//				pesquisa devem ser persistidos em uma vari�vel de sess�o
	//				e automaticamente recuperados na pr�xima exibi��o do
	//				formul�rio
	// @param		enable bool			Habilitar/desabilitar
	// @note		A escrita na sess�o � realizada no momento em que uma busca
	//				v�lida � detectada. A recupera��o dos valores � realizada
	//				quando o formul�rio de busca se encontra no estado inicial
	//				(n�o postado)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFilterPersistence($enable) {
		$this->filterPersistence = (bool)$enable;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::clearFilterPersistence
	// @desc		Limpa os filtros de pesquisa armazenados na sess�o, se existentes
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clearFilterPersistence() {
		if (isset($_SESSION['p2g_filters'][$this->Form->getSignature()]))
			unset($_SESSION['p2g_filters'][$this->Form->getSignature()]);
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setCheckboxMapping
	// @desc		Define o mapeamento de valores para campos do tipo checkbox
	// @param		trueValue mixed		Valor de subsitui��o para T (marcado)
	// @param		falseValue mixed	Valor de substitui��o para F (n�o marcado)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCheckboxMapping($trueValue, $falseValue) {
		$this->checkboxMapping = array(
			'T' => $trueValue,
			'F' => $falseValue
		);
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setConnectionId
	// @desc		Define o ID da conex�o a banco de dados a ser utilizada na classe
	// @param		id string	ID da conex�o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setConnectionId($id) {
		$this->connectionId = $id;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::setCallbackObject
	// @desc		Define o objeto que ser� utilizado na pesquisa por callbacks
	//				definidas na especifica��o XML
	// @param		&obj object		Objeto base para callbacks
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCallbackObject(&$obj) {
		$this->callbackObj =& $obj;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::addValidator
	// @desc		Adiciona um validador para os valores de busca submetidos
	// @param		validator string	Caminho para o validador (usando nota��o de pontos: dir.dir2.MyClass)
	// @param		arguments array		"array()" Argumentos para o validador
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addValidator($validator, $arguments=array()) {
		$this->validators[] = array($validator, TypeUtils::toArray($arguments));
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::run
	// @desc		M�todo principal da classe. Deve ser chamado ap�s a configura��o
	//				do formul�rio para que a verifica��o de busca postada e montagem
	//				da cl�usula de condi��o seja executada
	// @note		O retorno deste m�todo indica se o formul�rio n�o foi postado ou
	//				foi postado com erros (FALSE) ou foi postado com sucesso (TRUE)
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function run() {
		if ($this->isValid()) {
			if ($this->_buildSearchString()) {
				foreach ($this->validators as $validator) {
					if (!Validator::validate($validator[0], $this->searchRawData, $validator[1]))
						$this->valid = FALSE;
				}
				if (!$this->valid) {
					$this->Form->addErrors(Validator::getErrors());
					return FALSE;
				} else {
					// persist�ncia dos filtros em sess�o
					if ($this->filterPersistence) {
						$signature = $this->Form->getSignature();
						$formVars = ($this->Form->formMethod == 'POST' ? $_POST : $_GET);
						$_SESSION['p2g_filters'][$signature] = $formVars;
					} else {
						$this->clearFilterPersistence();
					}
					// auto redirecionamento
					if ($this->autoRedirect) {
						$Url = new Url($this->redirectUrl);
						if ($this->useSession) {
							$_SESSION[$this->paramName] = $this->searchString;
							if (!empty($this->searchDescription))
								$_SESSION[$this->paramName . '_description'] = $this->searchDescription;
						} else {
							if ($this->useEncode)
								$Url->addParameter($this->paramName, base64_encode($this->searchString));
							else
								$Url->addParameter($this->paramName, urlencode($this->searchString));
						}
						HttpResponse::redirect($Url);
					}
				}
				return $this->searchString;
			} else {
				$errorMessage = (isset($this->emptySearchMessage) ? $this->emptySearchMessage : (isset($this->stringMinLength) ? PHP2Go::getLangVal('ERR_SEARCHFORM_INVALID', $this->stringMinLength) : PHP2Go::getLangVal('ERR_SEARCHFORM_EMPTY')));
				$this->Form->addErrors($errorMessage);
				$this->valid = FALSE;
				return FALSE;
			}
		} elseif (!$this->Form->isPosted()) {
			// recupera filtros de sess�o salvos
			if ($this->filterPersistence) {
				$signature = $this->Form->getSignature();
				if (isset($_SESSION['p2g_filters'][$signature])) {
					$filters = (array)$_SESSION['p2g_filters'][$signature];
					foreach ($filters as $name => $value)
						Registry::set($name, $value);
				}
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::onPreRender
	// @desc		Pr�-renderiza��o do formul�rio de pesquisa
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (!$this->preRendered)
			$this->Form->onPreRender();
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::display
	// @desc		Imprime o formul�rio de pesquisa
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		$this->onPreRender();
		$this->Form->display();
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::getContent
	// @desc		Retorna o conte�do do formul�rio de pesquisa
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		return $this->Form->getContent();
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::_buildSearchString
	// @desc		M�todo interno de constru��o da cl�usula de condi��o a partir
	//				dos valores submetidos para cada campo do formul�rio
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _buildSearchString() {
		$fieldNames = array_keys($this->Form->fields);
		foreach ($fieldNames as $name) {
			$field =& $this->Form->fields[$name];
			$sd = $field->getSearchData();
			if ($field->child || !$field->searchable || $sd['IGNORE'] || in_array($name, $this->ignoreFields) || !$this->_validadeSearchField($sd))
				continue;
			$this->searchRawData[$name] = $sd;
		}
		$result = array();
		$description = array();
		$operators = PHP2Go::getLangVal('OPERATORS');
		$dbConn =& Db::getInstance($this->connectionId);
		foreach ($this->searchRawData as $fldName => $args) {
			// par�metros de pesquisa cuja SQL � constru�da por uma callback
			if (isset($args['SQLFUNC'])) {
				$clause = $this->_resolveSqlCallback($args['SQLFUNC'], $args['VALUE']);
			}
			// operador BETWEEN
			elseif ($args['FIELDTYPE'] == 'RANGEFIELD') {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				list($tmp, $bottom) = each($args['VALUE']);
				list($tmp, $top) = each($args['VALUE']);
				$bottom = $this->_resolveValueCallback(@$args['VALUEFUNC'], $bottom);
				$top = $this->_resolveValueCallback(@$args['VALUEFUNC'], $top);
				if ($args['DATATYPE'] == 'STRING') {
					$bottom = $dbConn->quoteString($bottom);
					$top = $dbConn->quoteString($top);
				} elseif ($args['DATATYPE'] == 'DATE') {
					$bottom = $dbConn->date($bottom);
					$top = $dbConn->date($top);
				} elseif ($args['DATATYPE'] == 'DATETIME') {
					// completa o intervalo com hora, minuto e segundo
					$bottom .= " 00:00:00";
					$top .= " 23:59:59";
					$bottom = $dbConn->date($bottom, TRUE);
					$top = $dbConn->date($top, TRUE);
				}
				$clause .= $this->_resolveOperator($args['OPERATOR']) . $bottom . ' and ' . $top;
			}
			// operadores IN e NOTIN (array como valor)
			elseif ($args['OPERATOR'] == 'IN' || $args['OPERATOR'] == 'NOTIN') {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				$value = $this->_resolveValueCallback(@$args['VALUEFUNC'], TypeUtils::toArray($args['VALUE']));
				if ($args['DATATYPE'] == 'STRING') {
					foreach ($value as $key => $entry)
						$value[$key] = $dbConn->quoteString($entry);
				} elseif ($args['DATATYPE'] == 'DATE' || $args['DATATYPE'] == 'DATETIME') {
					foreach ($value as $key => $entry)
						$value[$key] = $dbConn->date($entry, ($args['DATATYPE'] == 'DATETIME'));
				}
				$clause .= $this->_resolveOperator($args['OPERATOR']) . '(' . implode(',', $value) . ')';
			}
			// operadores string
			elseif ($args['OPERATOR'] == 'STARTING' || $args['OPERATOR'] == 'ENDING' || $args['OPERATOR'] == 'CONTAINING') {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				$value = $this->_resolveValueCallback(@$args['VALUEFUNC'], TypeUtils::parseString($args['VALUE']));
				if ($args['OPERATOR'] == 'ENDING' || $args['OPERATOR'] == 'CONTAINING')
					$value = '%' . $value;
				if ($args['OPERATOR'] == 'STARTING' || $args['OPERATOR'] == 'CONTAINING')
					$value .= '%';
				$value = $dbConn->quoteString($value);
				$clause .= $this->_resolveOperator($args['OPERATOR']) . $value;
			}
			// outros operadores
			else {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				$value = $this->_resolveValueCallback(@$args['VALUEFUNC'], @$args['VALUE']);
				if ($args['FIELDTYPE'] == 'CHECKFIELD')
					$value = $this->checkboxMapping[$value];
				elseif ($args['DATATYPE'] == 'STRING')
					$value = $dbConn->quoteString($value);
				elseif ($args['DATATYPE'] == 'DATE')
					$value = $dbConn->date($value);
				elseif ($value['DATATYPE'] == 'DATETIME')
					$value = $dbConn->date($value, TRUE);
				$clause .= $this->_resolveOperator($args['OPERATOR']) . $value;
			}
			if (!empty($clause)) {
				$result[] = $clause;
				if ($args['FIELDTYPE'] != 'HIDDENFIELD' && $args['DISPLAYVALUE'] !== NULL)
					$description[] = $args['DISPLAYVALUE'];
			}
		}
		$this->searchString = implode(" {$this->mainOperator} ", $result);
		$this->searchDescription = implode(sprintf(" %s ", $operators[$this->mainOperator]), $description);
		if (!empty($this->prefixOperator) && !empty($this->searchString))
			$this->searchString = ($this->prefixOperator == 'OR' ? " OR ({$this->searchString})" : " AND {$this->searchString}");
		return ($this->acceptEmptySearch || !empty($this->searchString));
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::_validateSearchField
	// @desc		Aplica valida��o b�sica de valor vazio em um campo de pesquisa
	// @param		args array	Valor e configura��es de um campo de pesquisa
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _validadeSearchField($args) {
		if ($args['FIELDTYPE'] == 'RANGEFIELD') {
			$sv = TypeUtils::toArray($args['VALUE']);
			if (sizeof($sv) != 2)
				return FALSE;
			list($tmp, $bottom) = each($sv);
			list($tmp, $top) = each($sv);
			if (empty($bottom) && strlen($bottom) == 0 && empty($top) && strlen($top) == 0)
				return FALSE;
			return TRUE;
		} else {
			// valida��o de tamanho m�nimo quando � um operador de string
			if (in_array($args['OPERATOR'], array('STARTING', 'CONTAINING', 'ENDING')) && isset($this->stringMinLength)) {
				return (!TypeUtils::isNull($args['VALUE']) && strlen($args['VALUE']) >= $this->stringMinLength);
			} elseif (is_array($args['VALUE'])) {
				return (!empty($args['VALUE']));
			} else {
				$str = strval($args['VALUE']);
				return (!empty($str) || strlen($str) > 0);
			};
			//return (!TypeUtils::isNull($args['VALUE']));
		}
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::_resolveOperator
	// @desc		Transforma um valor de operador definido no XML para um
	//				nome de operador v�lido para a especifica��o SQL-ANSI
	// @param		op string 	Operador
	// @return		string C�digo SQL correspondente ao operador
	// @access		private
	//!-----------------------------------------------------------------
	function _resolveOperator($op) {
		switch ($op) {
			case 'EQ' : return ' = ';
			case 'NEQ' : return ' <> ';
			case 'LT' : return ' < ';
			case 'LOET' : return ' <= ';
			case 'GT' : return ' > ';
			case 'GOET' : return ' >= ';
			case 'STARTING' :
			case 'ENDING' :
			case 'CONTAINING' :
				return ' LIKE ';
			case 'IN' :
				return ' IN ';
			case 'NOTIN' :
				return ' NOT IN ';
			case 'BETWEEN' :
				return ' BETWEEN ';
			default :
				return ' = ';
		}
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::_resolveSqlCallback
	// @desc		Verifica se um determinado campo possui uma fun��o de
	//				constru��o da cl�usula SQL, ignorando todos os outros par�metros
	//				de configura��o
	// @param		callback string	Callback de constru��o de cl�usula SQL
	// @param		value mixed		Valor do campo de pesquisa
	// @return		mixed Cl�usula SQL montada para o campo
	// @access		private
	//!-----------------------------------------------------------------
	function _resolveSqlCallback($callback, $value) {
		if (empty($callback))
			return FALSE;
		if (!isset($this->sqlCallbacks[$callback])) {
			// associa��o com callback object
			if (is_object($this->callbackObj) && !function_exists($callback) && method_exists($this->callbackObj, $callback))
				$this->sqlCallbacks[$callback] = new Callback(array($this->callbackObj, $callback));
			else
				$this->sqlCallbacks[$callback] = new Callback($callback);
		}
		return $this->sqlCallbacks[$callback]->invoke($value);
	}

	//!-----------------------------------------------------------------
	// @function	SearchForm::_resolveValueCallback
	// @desc		Verifica se um determinado campo possui uma fun��o de
	//				transforma��o de valor e executa a mesma se existir
	// @param		callback string	Callback de transforma��o de valor
	// @param		value mixed		Valor do campo de pesquisa
	// @return		mixed Valor de pesquisa (transformado, se a fun��o existir)
	// @access		private
	//!-----------------------------------------------------------------
	function _resolveValueCallback($callback, $value) {
		if (empty($callback))
			return $value;
		if (!isset($this->valueCallbacks[$callback])) {
			// associa��o com callback object
			if (is_object($this->callbackObj) && !function_exists($callback) && method_exists($this->callbackObj, $callback))
				$this->valueCallbacks[$callback] = new Callback(array($this->callbackObj, $callback));
			else
				$this->valueCallbacks[$callback] = new Callback($callback);
		}
		return $this->valueCallbacks[$callback]->invoke($value);
	}
}
?>