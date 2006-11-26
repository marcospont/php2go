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
// $Header: /www/cvsroot/php2go/core/form/field/AutoCompleteField.class.php,v 1.6 2006/11/19 18:28:30 mpont Exp $
// $Date: 2006/11/19 18:28:30 $

//------------------------------------------------------------------
import('php2go.db.QueryBuilder');
import('php2go.form.field.EditableField');
import('php2go.util.json.JSONEncoder');
//------------------------------------------------------------------

// @const AUTOCOMPLETE_NORMAL "normal"
// Style key for normal option rows
define('AUTOCOMPLETE_NORMAL', 'normal');
// @const AUTOCOMPLETE_SELECTED "selected"
// Style key for selected option rows
define('AUTOCOMPLETE_SELECTED', 'selected');
// @const AUTOCOMPLETE_HOVER "hover"
// Style key for option rows when hovered
define('AUTOCOMPLETE_HOVER', 'hover');

//!-----------------------------------------------------------------
// @class		AutoCompleteField
// @desc		Implementação de um campo texto onde o conteúdo fornecido
//				pelo usuário é utilizado para realizar uma pesquisa
//				e fornecer opções no estilo "auto-completar"
// @package		php2go.form.field
// @extends		EditableField
// @uses		Db
// @uses		HttpRequest
// @uses		JSONEncoder
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class AutoCompleteField extends EditableField
{
	var $options = array();		// @var options array		"array()" Opções de configuração do componente JS utilizado
	var $choices = array();		// @var choices array		"array()" Conjunto de opções
	var $dataSource = array();	// @var dataSource array	"array()" Datasource do componente

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::AutoCompleteField
	// @desc		Construtor da classe
	// @param		&Form Form object		Formulário onde o campo será inserido
	// @param		child bool				"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function AutoCompleteField(&$Form, $child=FALSE) {
		parent::EditableField($Form, $child);
		$this->htmlType = 'TEXT';
		$this->options = array(
			'maxChoices' => 10,
			'separator' => ',',
			'style' => array(
				AUTOCOMPLETE_NORMAL => 'autoCompleteNormal',
				AUTOCOMPLETE_SELECTED => 'autoCompleteSelected',
				AUTOCOMPLETE_HOVER => 'autoCompleteHover'
			),
			'url' => HttpRequest::uri(FALSE)
		);
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::display
	// @desc		Monta o código HTML do componente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		if ($this->options['ajax'] && !$this->options['throbber']) {
			$throbber = sprintf("\n<span id=\"%s_throbber\" style=\"display:none\"><img src=\"%sindicator.gif\" border=\"0\" align=\"top\" alt=\"\"></span>", $this->id, PHP2GO_ICON_PATH);
			$this->options['throbber'] = "{$this->id}_throbber";
		} else {
			$throbber = '';
		}
		if ($this->options['incremental']) {
			print sprintf(
"\n<textarea id=\"%s\" name=\"%s\" cols=\"%s\" rows=\"5\" title=\"%s\"%s%s%s%s%s%s%s%s>%s</textarea>%s" .
"\n<div id=\"%s_choices\" style=\"position:absolute;display:none\" class=\"autoCompleteChoices\"></div>" .
"\n<script type=\"text/javascript\">\n\tnew AutoCompleteField('%s', %s);\n</script>",
				$this->id, $this->name, $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'],
				$this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],  $this->attributes['STYLE'],
				$this->attributes['READONLY'], $this->attributes['DISABLED'], $this->attributes['DATASRC'],
				$this->attributes['DATAFLD'], $this->value, $throbber, $this->id, $this->id, JSONEncoder::encode($this->options)
			);
		} else {
			print sprintf(
"\n<input id=\"%s\" name=\"%s\" type=\"text\" value=\"%s\" maxlength=\"%s\" size=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s>%s" .
"\n<div id=\"%s_choices\" style=\"position:absolute;display:none\" class=\"autoCompleteChoices\"></div>" .
"\n<script type=\"text/javascript\">\n\tnew AutoCompleteField('%s', %s);\n</script>",
				$this->id, $this->name, $this->value, $this->attributes['LENGTH'], $this->attributes['SIZE'],
				$this->label, $this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
				$this->attributes['STYLE'], $this->attributes['READONLY'], $this->attributes['DISABLED'],
				$this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->attributes['AUTOCOMPLETE'],
				$throbber, $this->id, $this->id, JSONEncoder::encode($this->options)
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setSource
	// @desc		Define a fonte das opções de pesquisa
	// @note		<b>LOCAL</b> utiliza opções locais (Javascript) definidas
	//				estaticamente (nodos CHOICE) ou dinamicamente (DATASOURCE).
	//				<b>AJAX</b> significa busca através de requisições AJAX, utilizando
	//				o DATASOURCE definido na especificação XML
	// @param		src string		LOCAL ou AJAX
	// @param		option array	"array()" Opções de configuração
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSource($src, $options=array()) {
		if ($src == 'LOCAL') {
			$this->options['ajax'] = FALSE;
		} elseif ($src == 'AJAX') {
			$this->options['ajax'] = TRUE;
			if (isset($options['options']))
				$this->options['ajaxOptions'] = (string)$options;
			if (isset($options['throbber']))
				$this->options['throbber'] = $options['throbber'];
			if (isset($options['searchField']))
				$this->attributes['SEARCHFIELD'] = $options['searchField'];
		}
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setDelay
	// @desc		Define o delay de execução da pesquisa
	// @note		Se o intervalo entre a digitação de 2 caracteres no
	//				campo de pesquisa for superior ao delay, o filtro
	//				será aplicado (local ou remotamente)
	// @param		delay float	Delay de pesquisa
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setDelay($delay) {
		$delay = (float)$delay;
		if ($delay > 0)
		$this->options['delay'] = $delay;
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setMultiple
	// @desc		Define se o campo permite múltiplas escolhas
	// @param		setting bool		Múltiplo (TRUE) ou simples (FALSE)
	// @param		separator string	Separador (múltipla escolha)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMultiple($setting, $separator) {
		$this->options['incremental'] = (bool)$setting;
		if ($this->options['incremental'] && $separator)
			$this->options['separator'] = $separator;
	}
	
	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setHeight
	// @desc		Define a altura do container que exibe as opções de escolha
	// @note		Se este atributo for omitido, a altura necessária para
	//				comportar as opções exibidas será utilizada
	// @param		height int	Altura para o container
	// @return		void
	//!-----------------------------------------------------------------
	function setHeight($height) {
		$height = intval($height);
		if ($height)
			$this->options['height'] = $height;
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setMaxChoices
	// @desc		Define o máximo de opções a ser exibido
	// @param		max int	Máximo de opções
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMaxChoices($max) {
		$max = intval($max);
		if ($max > 0)
			$this->options['maxChoices'] = $max;
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setMinChars
	// @desc		Define o tamanho mínimo em caracteres para que a
	//				pesquisa seja executada
	// @param		min int	Mínimo de caracteres
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMinChars($min) {
		$min = intval($min);
		if ($min > 0)
			$this->options['minChars'] = $min;
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setIgnoreCase
	// @desc		Habilita/desabilita pesquisa insensível ao caso
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setIgnoreCase($setting) {
		$this->options['ignoreCase'] = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setFullSearch
	// @desc		Habilita/desabilita pesquisa em toda a string (contendo)
	//				e não só no início (iniciando com)
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFullSearch($setting) {
		$this->options['fullSearch'] = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setAutoSelect
	// @desc		Habilita o recurso de auto-selecionar quando a
	//				pesquisa retornar apenas uma opção
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoSelect($setting) {
		$this->options['autoSelect'] = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setChoiceValueNode
	// @desc		Define o nodo que determina o valor a ser utilizado
	//				quando as escolhas possuem conteúdo HTML.
	// @note		Se as escolhas utilizam o padrão HTML &lt;div&gt;choice value&lt;/div&gt;
	//				&lt;div&gt;auxiliar text&lt;/div&gt;, o valor do atributo seria "div",
	//				o que faz com que o componente busque a primeira ocorrência deste tipo
	//				de nodo para determinar o valor a ser copiado para o campo texto
	// @param		node string Tipo de nodo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setChoiceValueNode($node) {
		if (!empty($node))
			$this->options['choiceValueNode'] = trim($node);
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::setItemStyle
	// @desc		Define estilo CSS para os resultados de pesquisa
	// @param		style string	Estilo CSS
	// @param		type int		Nome da propriedade de estilo (normal, hover, selected)
	// @note		As constantes da classe contêm os valores possíveis para o parâmetro $type
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setItemStyle($style, $type) {
		if (!empty($style))
			$this->options['style'][$type] = $style;
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		// quando escolha múltipla estiver habilitada, máscaras não são suportadas
		$multiple = resolveBooleanChoice(@$attrs['MULTIPLE']);
		if ($multiple)
			unset($attrs['MASK']);
		parent::onLoadNode($attrs, $children);
		// datasource (utilizado para escolhas locais dinâmicas ou via ajax)
		$this->dataSource = parent::parseDataSource($children['DATASOURCE']);
		// fonte da busca
		$source = TypeUtils::ifNull(@$attrs['SOURCE'], 'LOCAL');
		if ($source == 'AJAX') {
			$opts = array();
			$opts['searchField'] = @$attrs['AJAXFIELD'];
			$opts['options'] = @$attrs['AJAXOPTIONS'];
			$opts['throbber'] = @$attrs['AJAXTHROBBER'];
			$this->setSource($source, $opts);
			$this->_Form->postbackFields[] = $this->name;
		} elseif ($source == 'LOCAL') {
			$this->setSource($source);
			$this->options['choices'] = array();
			if (is_array($children['CHOICE'])) {
				foreach ($children['CHOICE'] as $Choice) {
					if (!empty($Choice->value))
						$this->options['choices'][] = trim($Choice->value);
				}
			}
		}
		// delay
		if (isset($attrs['DELAY']))
			$this->setDelay($attrs['DELAY']);
		// escolha múltipla
		$this->setMultiple($multiple, @$attrs['SEPARATOR']);
		// altura do container
		$this->setHeight(@$attrs['HEIGHT']);
		// máximo de opções de escolha
		$this->setMaxChoices(@$attrs['MAXCHOICES']);
		// tamanho mínimo de token
		$this->setMinChars(@$attrs['MINCHARS']);
		// busca case-insensitive ou não
		if ($attrs['IGNORECASE'])
			$this->setIgnoreCase(resolveBooleanChoice($attrs['IGNORECASE']));
		// full search
		$this->setFullSearch(resolveBooleanChoice(@$attrs['FULLSEARCH']));
		// auto selecionar quando 1 resultado só for retornado
		$this->setAutoSelect(resolveBooleanChoice(@$attrs['AUTOSELECT']));
		// nodo de seleção de valor (quando as escolhas possuem conteúdo HTML)
		$this->setChoiceValueNode(@$attrs['CHOICEVALUENODE']);
		// estilos
		$this->setItemStyle(@$attrs['NORMALSTYLE'], AUTOCOMPLETE_NORMAL);
		$this->setItemStyle(@$attrs['SELECTEDSTYLE'], AUTOCOMPLETE_SELECTED);
		$this->setItemStyle(@$attrs['HOVERSTYLE'], AUTOCOMPLETE_HOVER);
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::onDataBind
	// @desc		Executa as configurações do componente que dependem
	//				de valores dinâmicos. É também utilizado para executar
	//				a pesquisa quando o componente estiver utilizando AJAX
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		// variáveis do datasource e montagem das opções usando datasource
		if (!empty($this->dataSource)) {
			foreach ((array)$this->dataSource as $name => $value) {
				if (preg_match("/~[^~]+~/", $value))
					$this->dataSource[$name] = $this->_Form->evaluateStatement($value);
			}
			if (!$this->_Form->isPosted())
				$this->_getChoices();
		}
		// busca das opções por ajax
		$headers = HttpRequest::getHeaders();
		if (@$headers['X-Requested-With'] == 'XMLHttpRequest') {
			$token = HttpRequest::post($this->name);
			if ($token !== NULL) {
				$this->_printChoices($token);
				exit;
			}
		}
		// processamento do valor, quando incremental=true
		if ($this->options['incremental']) {
			$this->searchDefaults['OPERATOR'] = 'IN';
			if ($this->_Form->isPosted()) {
				$tmp = $this->value;
				$val = array();
				if (!is_array($tmp) && $this->options['incremental']) {
					$tmp = explode($this->options['separator'], trim($tmp));
					foreach ($tmp as $item) {
						$item = trim($item);
						if (!empty($item))
							$val[] = $item;
					}
					parent::setSubmittedValue($val);
				}
			} elseif (is_array($this->value)) {
				$this->value = (string)$this->value;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::onPreRender
	// @desc		Executa tarefas de pré-renderização do componente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/autocompletefield.js');
		$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'autocompletefield.css');
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::_getChoices
	// @desc		Busca as consultas a partir da consulta SQL armazenada
	//				no datasource do componente
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _getChoices() {
		if (empty($this->choices)) {
			$Db =& Db::getInstance($this->dataSource['CONNECTION']);
			$old = $Db->setFetchMode(ADODB_FETCH_NUM);
			if (isset($this->dataSource['PROCEDURE'])) {
				$Rs =& $Db->execute(
					$Db->getProcedureSql($this->dataSource['PROCEDURE']),
					FALSE, @$this->dataSource['CURSORNAME']
				);
			} else {
				$Query = new QueryBuilder(
					$this->dataSource['KEYFIELD'] . ',' . $this->dataSource['DISPLAYFIELD'],
					$this->dataSource['LOOKUPTABLE'], $this->dataSource['CLAUSE'],
					$this->dataSource['GROUPBY'], $this->dataSource['ORDERBY']
				);
				if (isset($this->dataSource['LIMIT']) && preg_match("/([0-9]+)(,[0-9]+)?/", trim($this->dataSource['LIMIT']), $matches))
					$Rs =& $Db->limitQuery($Query->getQuery(), intval($matches[1]), intval($matches[2]));
				else
					$Rs =& $Db->query($Query->getQuery());
			}
			$Db->setFetchMode($old);
			$this->options['choices'] = array();
			if ($Rs) {
				while (!$Rs->EOF) {
					$this->options['choices'][] = $Rs->fields[1];
					$Rs->moveNext();
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	AutoCompleteField::_printChoices
	// @desc		Executa a pesquisa e retorna seus resultados em HTML
	// @note		Utilizado quando o componente funcionar em modo AJAX
	// @param		token string Token de pesquisa
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _printChoices($token) {
		$ign = HttpRequest::post('ignorecase');
		$full = HttpRequest::post('fullsearch');
		$fld = TypeUtils::ifNull(@$this->attributes['SEARCHFIELD'], $this->dataSource['DISPLAYFIELD']);
		$clause = $fld . " LIKE '" . ($full?'%':'') . ($ign?strtolower($token):$tok) . "%'";
		$this->dataSource['CLAUSE'] = (empty($this->dataSource['CLAUSE']) ? $clause : $this->dataSource['CLAUSE'] . " AND {$clause}");
		$this->_getChoices();
		if (!empty($this->options['choices'])) {
			$cnt = 0;
			print "<ul>";
			foreach ($this->options['choices'] as $choice) {
				$cnt++;
				$pos = strpos(($ign?strtolower($choice):$choice), ($ign?strtolower($token):$token));
				if ($pos == 0)
					print "<li><b><u>" . substr($choice, 0, strlen($token)) . "</u></b>" . substr($choice, strlen($token)) . "</li>";
				else
					print "<li>" . substr($choice, 0, $pos) . "<b><u>" . substr($choice, $pos, strlen($token)) . "</u></b>" . substr($choice, $pos+strlen($token)) . "</li>";
				if ($cnt == $this->options['maxChoices'])
					break;
			}
			print "</ul>";
		}
	}
}
?>