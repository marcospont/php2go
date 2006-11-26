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
// $Header: /www/cvsroot/php2go/core/datetime/TimeCounter.class.php,v 1.10 2006/02/28 21:55:51 mpont Exp $
// $Date: 2006/02/28 21:55:51 $

//!-----------------------------------------------------------------
// @class		TimeCounter
// @desc 		Esta classe implementa um cron�metro de tempo utilizando
// 				o timestamp do servidor. Iniciado e parado em momentos
// 				diferentes no tempo, � capaz de contar o tempo de execu��o
// 				de uma tarefa ou o tempo decorrido entre dois pontos distintos
// 				de uma seq��ncia de opera��es no sistema
// @package		php2go.datetime
// @extends 	PHP2Go
// @uses		System
// @author 		Marcos Pont 
// @version		$Revision: 1.10 $
//!-----------------------------------------------------------------
class TimeCounter extends PHP2Go 
{
	var $begin; 			// @var begin int			Timestamp inicial da contagem
	var $end; 				// @var end int				Timestamp final da contagem
	var $active; 			// @var active bool			Indica se o contador est� ativo
	var $zeroOffset = 7200; // @var zeroOffset int		"7200" Armazena o timestamp da hora zero de 01/01/1970 

	//!-----------------------------------------------------------------
	// @function	TimeCounter::TimeCounter
	// @desc 		Construtor do cron�metro. Inicializa o marcador
	// 				de in�cio de contagem com a vari�vel $begin se
	// 				ela for fornecida ou o timestamp atual do contr�rio
	// @access 		public 
	// @param 		begin int		"0" Timestamp para in�cio do cron�metro,
	// 								se omitido utiliza o timestamp atual da m�quina
	//!-----------------------------------------------------------------
	function TimeCounter($begin=0) {
		parent::PHP2Go();
		if (!$begin) {
			list($usec, $sec) = explode(" ",microtime()); 
			$this->begin = (float)$usec + (float)$sec;
		} else {
			$this->begin = $begin;
		} 
		$this->active = TRUE;
	} 

	//!-----------------------------------------------------------------
	// @function	TimeCounter::stop
	// @desc 		Para o cron�metro de tempo
	// @access 		public 
	// @param 		end int		"0" Marcador do fim do cron�metro, se omitido utiliza o timestamp atual
	// @return 		bool Retorna FALSE em caso de erros
	// @note 		O par�metro $end � opcional. Se for omitido, a marca
	// 				de fim do cron�metro ser� o timestamp atual
	//!-----------------------------------------------------------------
	function stop($end=0) {
		if ($end == 0) {
			list($usec, $sec) = explode(" ",microtime()); 
			$this->end = (float)$usec + (float)$sec;
		} else if ($end >= $this->begin) {
			$this->end = $end;
		} else {
			return FALSE;
		} 
		$this->active = FALSE;
		return TRUE;
	} 

	//!-----------------------------------------------------------------
	// @function	TimeCounter::restart
	// @desc 		Reinicializa o cron�metro
	// @access 		public 	
	// @param 		begin int	"0" Timestamp para in�cio do cron�metro, se omitido utiliza o timestamp atual do servidor
	// @return		void
	//!-----------------------------------------------------------------
	function restart($begin=0) {
		$this->active = FALSE;
		unset($this->end);
		if (!$begin) {
			list($usec, $sec) = explode(" ",microtime()); 
			$this->begin = (float)$usec + (float)$sec;
		} else {
			$this->begin = $begin;
		} 
		$this->active = TRUE;
	} 

	//!-----------------------------------------------------------------
	// @function 	TimeCounter::getInterval
	// @desc 		Calcula o tempo medido pelo cron�metro
	// @access 		public 
	// @return 		float N�mero de segundos desde o in�cio do cron�metro
	// 				ou NULL caso o cron�metro n�o tenha sido parado com
	// 				o m�todo stop()
	//!-----------------------------------------------------------------
	function getInterval() {
		if (!isset($this->end))
			return NULL;
		return ($this->end - $this->begin);
	}
	
	//!-----------------------------------------------------------------
	// @function	TimeCounter::getElapsedTime
	// @desc		Calcula o tempo medido at� o momento pelo cron�metro
	// @return		float N�mero de segundos desde o in�cio do cron�metro
	// @access		public	
	//!-----------------------------------------------------------------
	function getElapsedTime() {
		list($usec, $sec) = explode(" ",microtime()); 
		$now = (float)$usec + (float)$sec;			
		$interval = $now - $this->begin;
		return $interval;
	}

	//!-----------------------------------------------------------------
	// @function 	TimeCounter::getMinutes
	// @desc 		Calcula e formata o resultado do cron�metro para
	// 				minutos e segundos
	// @access 		public 
	// @param 		returnAsArray bool	"FALSE" Retornar os resultados em um array, do contr�rio exibe XXmYYs
	// @return 		string Intervalo de minutos e segundos formatado ou array com os valores
	// 				ou NULL caso o cron�metro n�o tenha sido parado com a fun��o stop()
	// @see 		TimeCounter::getInterval
	// @see 		TimeCounter::getHours
	//!-----------------------------------------------------------------
	function getMinutes($returnAsArray=FALSE) {
		if (!isset($this->end))
			return NULL;
		$interval = $this->getInterval();
		$hours = floor($interval / 3600);
		$calcTime = date("i:s", ($interval + $this->zeroOffset));
		list($minutes, $seconds) = explode(':', $calcTime);
		if ($returnAsArray)
			return array($minutes + ($hours * 60), $seconds);
		else
			return ($minutes + ($hours * 60)) . "m" . $seconds . "s";
	} 

	//!-----------------------------------------------------------------
	// @function	TimeCounter::getHours
	// @desc 		Calcula e formata o resultado do cron�metro para
	// 				horas, minutos e segundos
	// @access 		public 
	// @param 		returnAsArray bool	"FALSE" Retornar os resultados em um array, do contr�rio exibe XXhYYmZZs
	// @return 		string Intervalo de horas, minutos e segundos formatado ou array com os valores
	// 				ou NULL caso o cron�metro n�o tenha sido parado com a fun��o stop()
	// @see 		TimeCounter::getInterval
	// @see 		TimeCounter::getMinutes
	//!-----------------------------------------------------------------
	function getHours($returnAsArray=FALSE) {
		if (!isset($this->end))
			return NULL;
		$interval = $this->getInterval();
		$days = floor($interval / 86400);
		$calcTime = date("H:i:s", ($interval + $this->zeroOffset));
		list($hours, $minutes, $seconds) = explode(':', $calcTime);
		if ($returnAsArray) {
			return (array($hours + ($days * 24), $minutes, $seconds));
		} else {
			return ($hours + ($days * 24)) . "h" . $minutes . "m" . $seconds . "s";
		} 
	}
} 
?>