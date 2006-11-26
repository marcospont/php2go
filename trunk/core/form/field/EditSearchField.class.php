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
// $Header: /www/cvsroot/php2go/core/form/field/EditSearchField.class.php,v 1.18 2006/10/26 04:55:13 mpont Exp $
// $Date: 2006/10/26 04:55:13 $

//------------------------------------------------------------------
import('php2go.form.field.DbField');
import('php2go.form.field.LookupField');
import('php2go.util.service.ServiceJSRS');
//------------------------------------------------------------------

// @const EDITSEARCH_DEFAULT_SIZE "10"
// Tamanho padrão do campo que contém o termo de pesquisa
define('EDITSEARCH_DEFAULT_SIZE', 10);
// @const EDITSEARCH_DEFAULT_LOOKUP_WIDTH "250"
// Largura padrão em pixels para a lista de resultados
define('EDITSEARCH_DEFAULT_LOOKUP_WIDTH', 250);

//!-----------------------------------------------------------------
// @class		EditSearchField
// @desc		A classe EditSearchField implementa um pequeno e eficiente componente
//				de pesquisa, baseado em um conjunto de filtros e um campo de digitação
//				do termo de pesquisa. Partindo de uma consulta SQL base, o componente
//				inclui a cláusula definida para o filtro escolhido, e popula um campo
//				SELECT com os resultados da pesquisa. A requisição da pesquisa é
//				realizada utilizando JSRS
// @package		php2go.form.field
// @extends		DbField
// @author		Marcos Pont
// @version		$Revision: 1.18 $
//!-----------------------------------------------------------------
class EditSearchField extends DbField
{
	var $filters = array();		// @var filters array					"array()" Conjunto de filtros
	var $_LookupField;			// @var _LookupField LookupField object	Objeto LookupField usado para exibir os resultados da pesquisa

	//!-----------------------------------------------------------------
	// @function	EditSearchField::EditSearchField
	// @desc		Construtor da classe
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @access		public
	//!-----------------------------------------------------------------
	function EditSearchField(&$Form) {
		parent::DbField($Form, FALSE);
		$this->composite = TRUE;
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::display
	// @desc		Gera o código HTML do componente, composto pelas
	//				opções e termo de pesquisa, e o campo de exibição
	//				dos resultados
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'editsearchfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('frm', $this->_Form->formName);
		$Tpl->assign('label', $this->label);
		$Tpl->assign('labelStyle', $this->_Form->getLabelStyle());
		$Tpl->assign('buttonStyle', $this->_Form->getButtonStyle());
		$comboValue = $this->_LookupField->getValue();
		$Tpl->assign('value', (!StringUtils::isEmpty($comboValue) ? "'{$comboValue}'" : 'null'));
		$masks = array();
		$requestFilter = TypeUtils::parseString(HttpRequest::getVar($this->name . '_filters'));
		$filters = sprintf("<select id=\"%s_filters\" name=\"%s_filters\" title=\"%s\"%s%s%s%s>",
			$this->id, $this->name, $this->label, $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['DISABLED']
		);
		foreach ($this->filters as $value => $data) {
			$filters .= sprintf("<option value=\"%s\"%s>%s</option>", $value, ($value == $requestFilter ? ' selected' : ''), $data[0]);
			$masks[] = "'{$data[2]}'";
		}
		$filters .= "</select>";
		$Tpl->assign('filters', $filters);
		$Tpl->assign('masks', implode(',', $masks));
		$Tpl->assign('search', sprintf("<input type=\"text\" id=\"%s_search\" name=\"%s_search\" value=\"%s\" maxlength=\"%s\" size=\"%s\" title=\"%s\"%s%s%s%s%s>&nbsp;",
			$this->id, $this->name, strval(HttpRequest::getVar($this->name . '_search')), $this->attributes['LENGTH'], $this->attributes['SIZE'], $this->label,
			$this->attributes['SCRIPT'], $this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['DISABLED'], $this->attributes['AUTOCOMPLETE']
		));
		$Tpl->assign('tabIndex', $this->attributes['TABINDEX']);
		$Tpl->assign('disabled', $this->attributes['DISABLED']);
		$Tpl->assign('btnImg', $this->attributes['BTNIMG']);
		$Tpl->assign('btnValue', $this->attributes['BTNVALUE']);
		$Tpl->assignByRef('results', $this->_LookupField);
		$Tpl->assign('resultsName', $this->_LookupField->getName());
		$Tpl->assign('idx', ($this->_LookupField->attributes['NOFIRST'] == 'T' ? '0' : '1'));
		$Tpl->assign('url', $this->attributes['URL']);
		$Tpl->assign('autoTrim', ($this->attributes['AUTOTRIM'] ? 'true' : 'false'));
		$Tpl->assign('autoDispatch', ($this->attributes['AUTODISPATCH'] ? 'true' : 'false'));
		$Tpl->assign('debug', ($this->attributes['DEBUG'] ? 'true' : 'false'));
		$Tpl->display();
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::getValue
	// @desc		O valor do componente mapeia diretamente para o valor
	//				do campo LOOKUPFIELD associado
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getValue() {
		return $this->_LookupField->getValue();
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::getDisplayValue
	// @desc		Retorna a representação textual do valor do componente
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function getDisplayValue() {
		return $this->_LookupField->getDisplayValue();
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::getFocusId
	// @desc		Retorna o ID da lista de campos de pesquisa, que
	//				deverá receber foco quando o label do campo for clicado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFocusId() {
		return "{$this->id}_filters";
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::&getLookupField
	// @desc		Retorna o objecto LookupField que representa a lista de de resultados da pesquisa
	// @note		Retorna NULL se o objeto não foi definido
	// @return		LookupField object	Campo LookupField associado à este campo
	// @access		public
	//!-----------------------------------------------------------------
	function &getLookupField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_LookupField, 'LookupField'))
			$result =& $this->_LookupField;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setSize
	// @desc		Altera ou define o tamanho do campo
	// @param		size int	Tamanho para o campo de pesquisa
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = $size;
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setLength
	// @desc		Define número máximo de caracteres do campo
	// @param		length int	Máximo de caracteres para o campo de pesquisa
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLength($length) {
		if (TypeUtils::isInteger($length))
			$this->attributes['LENGTH'] = $length;
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setUrl
	// @desc		Define a URL para onde a busca deve ser enviada
	// @note		Normalmente, a URL de pesquisa é a mesma do formulário. Porém,
	//				em alguns casos, pode ser necessária a utilização de um endereço diferente
	// @param		url string	URL de pesquisa
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setUrl($url) {
		if (!empty($url))
			$this->attributes['URL'] = $this->_Form->evaluateStatement($url);
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setAutoComplete
	// @desc		Define valor para o recurso autocompletar no campo
	// @param		setting mixed	Valor para o atributo AUTOCOMPLETE
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoComplete($setting) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['AUTOCOMPLETE'] = " autocomplete=\"ON\"";
		else if (TypeUtils::isFalse($setting))
			$this->attributes['AUTOCOMPLETE'] = " autocomplete=\"OFF\"";
		else
			$this->attributes['AUTOCOMPLETE'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setAutoTrim
	// @desc		Habilita ou desabilita a remoção automática dos caracteres
	//				brancos no início e no fim do termo de pesquisa no momento
	//				da submissão
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoTrim($setting=TRUE) {
		$this->attributes['AUTOTRIM'] = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setAutoDispatch
	// @desc		Habilita ou desabilita a execução automática da pesquisa
	//				desde que os filtros estejam preenchidos e sejam válidos
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoDispatch($setting=TRUE) {
		$this->attributes['AUTODISPATCH'] = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setButtonValue
	// @desc		Define o valor do botão de pesquisa usado no componente
	// @param		value string	Valor para o botão
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setButtonValue($value) {
		if ($value)
			$this->attributes['BTNVALUE'] = resolveI18nEntry($value);
		else
			$this->attributes['BTNVALUE'] = PHP2Go::getLangVal('DEFAULT_BTN_VALUE');
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setButtonImage
	// @desc		Define uma imagem a ser utilizada no botão de pesquisa
	// @param		img string	Caminho da imagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setButtonImage($img) {
		if ($img)
			$this->attributes['BTNIMG'] = trim($img);
		else
			$this->attributes['BTNIMG'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setDebug
	// @desc		Habilita ou desabilita debug no mecanismo de pesquisa JSRS
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setDebug($setting) {
		$this->attributes['DEBUG'] = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		if (!empty($this->dataSource) &&  isset($children['DATAFILTER']) && isset($children['LOOKUPFIELD']) &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'], 'XmlNode')
		) {
			// armazenamento dos filtros
			$filters = TypeUtils::toArray($children['DATAFILTER']);
			foreach ($filters as $filterNode) {
				$id = $filterNode->getAttribute('ID');
				$label = $filterNode->getAttribute('LABEL');
				$expression = $filterNode->getAttribute('EXPRESSION');
				$mask = TypeUtils::ifFalse($filterNode->getAttribute('MASK'), 'STRING');
				if (empty($id) || empty($label) || empty($expression) || substr_count($expression, '%s') != 1)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EDITSEARCH_INVALID_DATAFILTER', (empty($id) ? '?' : $id)), E_USER_ERROR, __FILE__, __LINE__);
				if ($mask != 'STRING' && !preg_match(PHP2GO_MASK_PATTERN, $mask))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EDITSEARCH_INVALID_DATAFILTER_MASK', $id), E_USER_ERROR, __FILE__, __LINE__);
				if ($mask == 'DATE')
					$mask .= '-' . PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
				if (isset($this->filters[$id]))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EDITSEARCH_DUPLICATED_DATAFILTER', $id), E_USER_ERROR, __FILE__, __LINE__);
				$this->filters[$id] = array($label, $expression, $mask);
			}
			// inicializa o handler JSRS
			$Service =& ServiceJSRS::getInstance();
			$Service->registerHandler(array($this, 'performSearch'), strtolower($this->id) . 'PerformSearch');
			$this->_Form->postbackFields[] = $this->id;
			// tamanho do campo de pesquisa
			if (isset($attrs['SIZE']))
				$this->setSize($attrs['SIZE']);
			elseif (isset($attrs['LENGTH']))
				$this->setSize($attrs['LENGTH']);
			else
				$this->setSize(EDITSEARCH_DEFAULT_SIZE);
			// número máximo de caracteres do termo de pesquisa
			if ($attrs['LENGTH'])
				$this->setLength($attrs['LENGTH']);
			else
				$this->setLength($this->attributes['SIZE']);
			// url de pesquisa
			$this->setUrl(@$attrs['URL']);
			// autocomplete
			$this->setAutoComplete(resolveBooleanChoice(@$attrs['AUTOCOMPLETE']));
			// autotrim
			$this->setAutoTrim(resolveBooleanChoice(@$attrs['AUTOTRIM']));
			// autodispatch
			$this->setAutoDispatch(resolveBooleanChoice(@$attrs['AUTODISPATCH']));
			// valor e imagem do botão
			$this->setButtonValue(@$attrs['BTNVALUE']);
			$this->setButtonImage(@$attrs['BTNIMG']);
			// debug da requisição JSRS
			$this->setDebug(resolveBooleanChoice(@$attrs['DEBUG']));
			// cria o campo lookupfield
			$lookupAttrs =& $children['LOOKUPFIELD']->getAttributes();
			if (!isset($lookupAttrs['WIDTH']))
				$lookupAttrs['WIDTH'] = EDITSEARCH_DEFAULT_LOOKUP_WIDTH;
			$this->_LookupField = new LookupField($this->_Form, TRUE);
			$this->_LookupField->onLoadNode($lookupAttrs, $children['LOOKUPFIELD']->getChildrenTagsArray());
			$this->_Form->fields[$this->_LookupField->getName()] =& $this->_LookupField;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_EDITSEARCH_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::onDataBind
	// @desc		Se o formulário foi submetido, o valor do campo é
	//				copiado do valor determinado para o LOOKUPFIELD.
	//				Adicionalmente, os valores selecionados para filtro e
	//				termo de busca são lidos do request para que o dataset
	//				dos resultados possa ser reconstruído
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		if ($this->_Form->isPosted) {
			//$this->_LookupField->onDataBind();
			$lastFilter = HttpRequest::getVar($this->id . '_lastfilter', $this->_Form->formMethod);
			$lastSearch = HttpRequest::getVar($this->id . '_lastsearch', $this->_Form->formMethod);
			if ($lastFilter !== NULL && $lastSearch !== NULL) {
				$clause = sprintf($this->filters[$lastFilter][1], $lastSearch);
				if (empty($this->dataSource['CLAUSE']))
					$this->dataSource['CLAUSE'] = $clause;
				else
					$this->dataSource['CLAUSE'] = "({$this->dataSource['CLAUSE']}) AND {$clause}";
				@parent::processDbQuery(ADODB_FETCH_NUM);
				$this->_LookupField->setRecordSet($this->_Rs);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::onPreRender
	// @desc		Executa as configurações necessárias antes da construção
	//				do código HTML final do componente
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/editsearchfield.js');
		$this->_LookupField->onDataBind();
		$this->_LookupField->setRequired(FALSE);
		$this->_LookupField->setDisabled($this->disabled);
		$this->_LookupField->onPreRender();
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::performSearch
	// @desc		Método responsável por executar a pesquisa, utilizando
	//				o tipo de filtro e o termo de pesquisa escolhidos no
	//				formulário. O retorno produzido é uma string contendo
	//				separadores padrão para linhas "|" e colunas "~"
	// @param		identifier string	Identificador do campo
	// @param		filter int			ID do Filtro
	// @param		term mixed			Termo de pesquisa
	// @return		string Resultados
	// @access		public
	//!-----------------------------------------------------------------
	function performSearch($filter, $term) {
		if (isset($this->filters[$filter])) {
			// constrói a nova cláusula
			$clause = sprintf($this->filters[$filter][1], $term);
			if (empty($this->dataSource['CLAUSE']))
				$this->dataSource['CLAUSE'] = $clause;
			else
				$this->dataSource['CLAUSE'] = "({$this->dataSource['CLAUSE']}) AND {$clause}";
			// executa a consulta
			@parent::processDbQuery(ADODB_FETCH_NUM, ServiceJSRS::debugEnabled());
			// monta a string de resultados
			if ($this->_Rs->RecordCount() > 0) {
				$lines = array();
				while (!$this->_Rs->EOF) {
					$lines[] = @$this->_Rs->fields[0] . '~' . @$this->_Rs->fields[1];
					$this->_Rs->MoveNext();
				}
				return implode('|', $lines);
			}
		}
		return '';
	}
}
?>