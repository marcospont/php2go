<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

	require_once('config.example.php');
	import('php2go.datetime.TimeCounter');

	println('<b>PHP2Go Examples</b> : php2go.datetime.TimeCounter<br>');

	$timeCounter = new TimeCounter();
	println('Start<br>Sleep 2 seconds...');
	flush();
	sleep(2);
	println('Elapsed time : ' . $timeCounter->getElapsedTime() . '<br>Sleep 2 seconds...');
	flush();
	sleep(2);
	println('Elapsed time : ' . $timeCounter->getElapsedTime() . '<br>Sleep 2 seconds...');
	flush();
	sleep(2);
	$timeCounter->stop();
	println('Stop<br>Final interval : ' . $timeCounter->getInterval() . '');


?>