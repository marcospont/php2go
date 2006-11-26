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
// $Header: /www/cvsroot/php2go/core/form/field/GroupField.class.php,v 1.27 2006/11/02 19:21:46 mpont Exp $
// $Date: 2006/11/02 19:21:46 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		GroupField
// @desc		A classe GroupField serve de base para a construção
//				de um grupo de campos RADIO ou um grupo de campos
//				CHECKBOX com opções estáticas (definidas via XML)
// @package		php2go.form.field
// @extends		FormField
// @uses		Template
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.27 $
//!-----------------------------------------------------------------
class GroupField extends FormField
{
	var $optionCount = 0;				// @var optionCount int			"0" Total de opções do grupo
	var $optionAttributes = array();	// @var optionAttributes array	"array()" Vetor de atributos das opções
	var $optionListeners = array();		// @var optionListeners array	"array()" Vetor de tratadores de evento por opção do grupo
	var $templateFile;					// @var templateFile string		Nome do arquivo template para renderização do grupo

	//!-----------------------------------------------------------------
	// @function	GroupField::display
	// @desc		Monta o código HTML do grupo de campos
	// @note		Os dados dos elementos do grupo são definidos nas
	//				classes filhas pelo método renderGroup()
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$group = $this->renderGroup();
		$elements =& $group['group'];
		print $group['prepend'];
		print sprintf("\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"%s>\n  <tr>", $this->attributes['TABLEWIDTH']);
		for ($i=0,$s=sizeof($elements); $i<$s; $i++) {
			print sprintf("\n    <td style=\"width:15px;height:15px;\">%s</td>", $elements[$i]['input']);
			print sprintf("\n    <td><label for=\"%s_%s\" id=\"%s_label\"%s%s>%s</label></td>",
				$elements[$i]['id'], $i, $elements[$i]['id'], $elements[$i]['alt'], $this->_Form->getLabelStyle(), $elements[$i]['caption']
			);
			if ((($i+1) % $this->attributes['COLS']) == 0 && $i<($s-1))
				print "\n  </tr><tr>";
		}
		$diff = ($i % $this->attributes['COLS']);
		if ($diff && $this->attributes['COLS'] > 1) {
			for ($i=$diff; $i<$this->attributes['COLS']; $i++)
				print "\n    <td colspan=\"2\"></td>";
		}
		print "\n  </tr>\n</table>";
		print $group['append'];
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::renderGroup
	// @desc		O método renderGroup deve ser implementado nas classes
	//				filhas retornando um array com os dados dos elementos do
	//				grupo. Cada item do array deve conter as chaves "input"
	//				(código do elemento do grupo), "name" e "caption"
	// @note		O atributo "alt" é opcional e pode ser incluído na
	//				especificação de cada item do grupo
	// @access		public
	// @return		array
	// @abstract
	//!-----------------------------------------------------------------
	function renderGroup() {
		return array();
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::getFocusId
	// @desc		Retorna o ID do primeiro elemento do grupo, que
	//				deverá receber foco quando o label do campo for clicado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFocusId() {
		return "{$this->id}_0";
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::getDisplayValue
	// @desc		Monta uma representação compreensível
	//				do valor do campo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getDisplayValue() {
		$display = NULL;
		$value = $this->value;
		$arrayValue = is_array($value);
		foreach ($this->optionAttributes as $index => $data) {
			if (!$arrayValue && $data['VALUE'] == $value) {
				$display = $data['CAPTION'];
				break;
			}
			if ($arrayValue && in_array($data['VALUE'], $value))
				$display[] = $data['CAPTION'];
		}
		return (is_array($display) ? '(' . implode(', ', $display) . ')' : $display);
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::getOptions
	// @desc		Retorna o vetor de opções inseridas no grupo
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getOptions() {
		return $this->optionAttributes;
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::getOptionCount
	// @desc		Busca o número de opções inseridas
	// @return		int	Número de itens
	// @access		public
	//!-----------------------------------------------------------------
	function getOptionCount() {
		return $this->optionCount;
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::addOption
	// @desc		Adiciona uma nova opção ao conjunto de OPTIONS do grupo
	// @param		value mixed			Valor para a opção
	// @param		caption string		Caption da opção
	// @param		alt string			"" Texto alternativo
	// @param		disabled bool		"FALSE" Indica se a opção deve estar desabilitado
	// @param		accessKey string	"NULL" Tecla de atalho
	// @param		index int			"NULL" Índice onde a opção deve ser inserida
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function addOption($value, $caption, $alt='', $disabled=FALSE, $accessKey=NULL, $index=NULL) {
		if ($index <= $this->optionCount && $index >= 0) {
			$newOption = array();
			if (trim($value) == '') {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_OPTION_VALUE', array($index, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			// atributos da nova opção
			$newOption['VALUE'] = $value;
			$newOption['CAPTION'] = (empty($caption) ? $newOption['VALUE'] : $caption);
			$newOption['ALT'] = $alt;
			$newOption['ACCESSKEY'] = $accessKey;
			if ($disabled || $this->_Form->readonly)
				$newOption['DISABLED'] = " disabled";
			else
				$newOption['DISABLED'] = (isset($this->attributes['DISABLED']) ? $this->attributes['DISABLED'] : '');
			// inserção na última posição
			if (!TypeUtils::isInteger($index) || $index == $this->optionCount) {
				$this->optionAttributes[$this->optionCount] = $newOption;
				$this->optionListeners[$this->optionCount] = array();
			// inserção em uma determinada posição
			} else {
				for ($i=$this->optionCount; $i>$index; $i--) {
					$this->optionAttributes[$i] = $this->optionAttributes[$i-1];
					$this->optionListeners[$i] = $this->optionListeners[$i-1];
				}
				$this->optionAttributes[$index] = $newOption;
				$this->optionListeners[$index] = array();
			}
			$this->optionCount++;
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::removeOption
	// @desc		Remove uma opção do grupo a partir de seu índice
	// @param		index int	Índice a ser removido
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function removeOption($index) {
		// índice inválido
		if ($this->optionCount == 1 || !TypeUtils::isInteger($index) || $index < 0 || $index >= $this->optionCount)
			return FALSE;
		// move as outras opções
		for ($i=$index; $i<($this->optionCount-1); $i++) {
			$this->optionAttributes[$i] = $this->optionAttributes[$i+1];
			$this->optionListeners[$i] = $this->optionListeners[$i+1];
		}
		unset($this->optionAttributes[$this->optionCount-1]);
		unset($this->optionListeners[$this->optionCount-1]);
		$this->optionCount--;
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::setCols
	// @desc		Seta o número de colunas da tabela que contém os campos,
	//				definindo assim quantos elementos devem ser exibidos por linha
	// @param		cols int	Número de colunas ou campos por linha
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCols($cols) {
		$this->attributes['COLS'] = max(1, $cols);
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::setTableWidth
	// @desc		Seta o tamanho (valor para o atributo WIDTH) da
	//				tabela construída para o grupo de campos
	// @param		tableWidth mixed	Tamanho da tabela
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " width=\"{$tableWidth}\"";
		else
			$this->attributes['TABLEWIDTH'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::setDisabled
	// @desc		Modifica o estado de uma das opções do
	//				grupo de campos (habilitado, desabilitado)
	// @param		setting bool	"TRUE" Estado a ser aplicado à opção
	// @param		index int		"NULL" Índice a ser alterado
	// @access		public
	// @return		TRUE
	//!-----------------------------------------------------------------
	function setDisabled($setting=TRUE, $index=NULL) {
		if ($index === NULL) {
			parent::setDisabled($setting);
			return TRUE;
		} else {
			// índice inválido
			if (!TypeUtils::isInteger($index) || $index < 0 || $index >= $this->optionCount)
				return FALSE;
			$this->optionAttributes[$index]['DISABLED'] = ($setting ? ' disabled' : '');
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::addEventListener
	// @desc		Sobrescreve a implementação do método na classe FormField
	//				para adicionar a possibilidade de inclusão de listeners individuais
	//				por opção do grupo (um elemento radio, ou um elemento checkbox)
	// @param		Listener FormEventListener object	Tratador de eventos
	// @param		index int	"NULL" Índice do elemento do grupo ao qual o tratador deve ser associado
	// @access		public
	// @return		void
	// @note		Se o parâmetro $index for omitido, o listener será incluído para todas as opções de grupo
	//!-----------------------------------------------------------------
	function addEventListener($Listener, $index=NULL) {
		if ($index === NULL) {
			parent::addEventListener($Listener);
		} elseif ($index < $this->optionCount && $index >= 0) {
			$Listener->setOwner($this, $index);
			if ($Listener->isValid())
				$this->optionListeners[$index][] =& $Listener;
		}
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// número de colunas
		$this->setCols(@$attrs['COLS']);
		// largura da tabela
		$this->setTableWidth(@$attrs['TABLEWIDTH']);
		// opções
		if (isset($children['OPTION'])) {
			$options = TypeUtils::toArray($children['OPTION']);
			for ($i=0,$s=sizeof($options); $i<$s; $i++) {
				$this->addOption($options[$i]->getAttribute('VALUE'), $options[$i]->getAttribute('CAPTION'), $options[$i]->getAttribute('ALT'), ($options[$i]->getAttribute('DISABLED') == 'T'), $options[$i]->getAttribute('ACCESSKEY'));
				// listeners individuais de cada opção
				$optChildren = $options[$i]->getChildrenTagsArray();
				if (isset($optChildren['LISTENER'])) {
					$listener = TypeUtils::toArray($optChildren['LISTENER']);
					foreach ($listener as $listenerNode)
						$this->addEventListener(FormEventListener::fromNode($listenerNode), $i);
				}
			}
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_GROUPFIELD_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::onPreRender
	// @desc		Executa tarefas de pré-renderização do componente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		// revalida a propriedade readonly do formulário
		if ($this->_Form->readonly) {
			for ($i=0; $i<$this->optionCount; $i++)
				$this->optionAttributes[$i]['DISABLED'] = " disabled";
		}
	}

	//!-----------------------------------------------------------------
	// @function	GroupField::renderListeners
	// @desc		A classe GroupField sobrescreve a implementação do método renderListeners
	//				para que os listeners gerais para todas as opções e os listeners individuais por
	//				opção possam ser agrupados na montagem da definição dos eventos das opções do campo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function renderListeners() {
		// processa os listeners para cada opção do grupo
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {
			$script = '';
			$optionEvents = array();
			foreach ($this->listeners as $globalListener) {
				$eventName = $globalListener->eventName;
				if (!isset($optionEvents[$eventName]))
					$optionEvents[$eventName] = array();
				$optionEvents[$eventName][] = $globalListener->getScriptCode($i);
			}
			// listeners individuais
			reset($this->optionListeners[$i]);
			foreach ($this->optionListeners[$i] as $optionListener) {
				$eventName = $optionListener->eventName;
				if (!isset($optionEvents[$eventName]))
					$optionEvents[$eventName] = array();
				$optionEvents[$eventName][] = $optionListener->getScriptCode();
			}
			foreach ($optionEvents as $name => $actions)
				$this->optionAttributes[$i]['SCRIPT'] .= " {$name}=\"" . str_replace('\"', '\'', implode(';', $actions)) . ";\"";
		}
	}
}
?>