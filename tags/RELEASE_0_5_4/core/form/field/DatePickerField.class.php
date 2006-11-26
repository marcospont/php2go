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
// $Header: /www/cvsroot/php2go/core/form/field/DatePickerField.class.php,v 1.4 2006/10/26 04:55:13 mpont Exp $
// $Date: 2006/10/26 04:55:13 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
import('php2go.form.field.FormField');
import('php2go.util.json.JSONEncoder');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DatePickerField
// @desc		Tipo especial de controle de formulário que implementa
//				uma seleção simples ou múltipla de datas a partir de
//				um calendário (biblioteca JSCalendar)
// @package		php2go.form.field
// @uses		Date
// @uses		JSONEncoder
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class DatePickerField extends FormField
{
	var $options = array();		// @var options array	"array()" Conjunto de opções para o calendário

	//!-----------------------------------------------------------------
	// @function	DatePickerField::DatePickerField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function DatePickerField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->searchDefaults['DATATYPE'] = 'DATE';
		$this->options = array(
			'cache' => TRUE,
			'selectDefault' => FALSE,
			'firstDay' => 0,
			'showOthers' => FALSE,
			'weekNumbers' => FALSE,
			'electric' => TRUE,
			'ifFormat' => (PHP2Go::getConfigVal('LOCAL_DATE_TYPE') == 'EURO' ? "%d/%m/%Y" : "%Y/%m/%d"),
			'dateSep' => '#',
			'range' => array(),
			'statusFunc' => NULL
		);
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::display
	// @desc		Gera o código HTML contendo o calendário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		print sprintf(
			"<input id=\"%s\" name=\"%s\" type=\"hidden\" value=\"%s\" title=\"%s\"%s%s/><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td id=\"%s_calendar\"%s></td></tr></table>" .
			"<script type=\"text/javascript\">new DatePickerField(\"%s\", %s);</script>",
			$this->id, $this->name, $this->value, $this->label, $this->attributes['SCRIPT'],
			$this->attributes['DISABLED'], $this->id, $this->attributes['STYLE'],
			$this->id, JSONEncoder::encode($this->options)
		);
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::setMultiple
	// @desc		Define o tipo de escolha de data (simples ou múltipla)
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMultiple($setting) {
		$this->attributes['MULTIPLE'] = (bool)$setting;
		$this->searchDefaults['OPERATOR'] = ($this->attributes['MULTIPLE'] ? 'IN' : 'EQ');
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::setDateStatusFunc
	// @desc		Define a função JS que deverá tratar o status de cada
	//				uma das datas do calendário, habilitando-as ou desabilitando-as
	// @param		funcName string	Nome da função
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setDateStatusFunc($funcName) {
		if ($funcName)
			$this->options['statusFunc'] = $funcName;
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::setShowTime
	// @desc		Habilita ou desabilita a seleção de hora juntamente com a data
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setShowTime($setting) {
		$setting = (bool)$setting;
		$dateType = PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
		if ($setting) {
			$this->searchDefaults['DATATYPE'] = 'DATETIME';
			$this->options['showsTime'] = TRUE;
			$this->options['ifFormat'] = ($dateType == 'EURO' ? "%d/%m/%Y %H:%M:%S" : "%Y/%m/%d %H:%M:%S");
		} else {
			$this->searchDefaults['DATATYPE'] = 'DATE';
			$this->options['showsTime'] = FALSE;
			$this->options['ifFormat'] = ($dateType == 'EURO' ? "%d/%m/%Y" : "%Y/%m/%d");
		}
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::setFirstWeekDay
	// @desc		Define qual será o primeiro dia da semana no calendário
	// @note		O padrão desta configuração é 0 (Domingo). 0 significa
	//				domingo e 6 significa Sábado
	// @param		day int	Primeiro dia da semana no calendário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFirstWeekDay($day) {
		$day = intval($day);
		if ($day >= 0 && $day <= 6)
			$this->options['firstDay'] = $day;
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::setShowOthers
	// @desc		Habilita ou desabilita a exibição de dias pertencentes
	//				a outros meses no calendário
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setShowOthers($setting) {
		$this->options['showOthers'] = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::setShowWeekNumbers
	// @desc		Habilita ou desabilita a exibição dos números das
	//				semanas do ano no calendário
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setShowWeekNumbers($setting) {
		$this->options['weekNumbers'] = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::setYearRange
	// @desc		Define o intervalo de anos aceito pelo calendário
	// @param		start int Ano inicial
	// @param		end int Ano final
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setYearRange($start, $end) {
		if ($start && $end)
			$this->options['range'] = array($start, $end);
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// seleção múltipla
		$this->setMultiple(resolveBooleanChoice(@$attrs['MULTIPLE']));
		// função JS que define o status das datas
		$this->setDateStatusFunc(@$attrs['DATESTATUSFUNC']);
		// hora
		$this->setShowTime(resolveBooleanChoice(@$attrs['TIME']));
		// primeiro dia da semana
		$this->setFirstWeekDay(@$attrs['FIRSTWEEKDAY']);
		// dias de outros meses
		$this->setShowOthers(resolveBooleanChoice(@$attrs['SHOWOTHERS']));
		// números das semanas do ano
		$this->setShowWeekNumbers(resolveBooleanChoice(@$attrs['SHOWWEEKNUMBERS']));
		// intervalo de anos
		$matches = array();
		$range = @$attrs['YEARRANGE'];
		if ($range && preg_match('/^([0-9]{4})\s*,\s*([0-9]{4})$/', $range, $matches))
			$this->setYearRange($matches[1], $matches[2]);
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::onDataBind
	// @desc		Sobrecarrega o método onDataBind da classe FormField para interpretar
	//				expressões de data e para converter seleções de datas em formato de array
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		$regs = array();
		if (!$this->attributes['MULTIPLE'] && !empty($this->value) && !Date::isEuroDate($this->value, $regs) && !Date::isUsDate($this->value, $regs))
			parent::setValue(Date::parseFieldExpression($this->value));
		if ($this->attributes['MULTIPLE'] && is_array($this->value))
			$this->value = (!empty($this->value) ? join($this->options['dateSep'], $this->value) : "");
		if ($this->_Form->isPosted())
			parent::setSubmittedValue(!empty($this->value) ? explode($this->options['dateSep'], $this->value) : array());
	}

	//!-----------------------------------------------------------------
	// @function	DatePickerField::onPreRender
	// @desc		Prepara as opções de configuração do JS Calendar
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->importStyle(PHP2GO_JAVASCRIPT_PATH . "vendor/jscalendar/calendar-system.css");
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "form/datepickerfield.js");
		if ($this->attributes['MULTIPLE']) {
			$multiple = array();
			$list = (!empty($this->value) ? explode($this->options['dateSep'], $this->value) : array());
			foreach ($list as $date) {
				if ($this->options['showsTime'])
					$multiple[] = date("F d, Y H:i:s", Date::dateToTime($date));
				else
					$multiple[] = date("F d, Y", Date::dateToTime($date));
			}
			$this->options['multiple'] = $list;//$multiple;
		} else {
			if (!empty($this->value)) {
				if ($this->options['showsTime'])
					$date = date("F d, Y H:i:s", Date::dateToTime($this->value));
				else
					$date = date("F d, Y", Date::dateToTime($this->value));
			} else {
				$date = NULL;
			}
			$this->options['date'] = $date;
		}
	}
}
?>