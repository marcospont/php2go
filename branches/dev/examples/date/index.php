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

	require_once('../config/config.php');
	import('php2go.datetime.Date');

	println('<b>PHP2Go Examples</b> : php2go.datetime.Date<br />');

	// print current date based on local settings
	$local = Date::localDate();
	println('Current date using local format: ' . $local);

	// month name of a timestamp
	println('Month name for ' . $local . ' : ' . Date::monthName(time()));

	// day of week of a given date
	$dow = Date::dayOfWeek($local, TRUE, FALSE);
	println('Day of week for ' . $local . ' : ' . $dow);

	// days of a given month
	$dim = Date::daysInMonth(2, 1940);
	println('Days in month for February 1940 : ' . $dim);
	println('');

	// date operations
	println('<b>Date Operations</b>');
	$fd = Date::futureDate($local, 40);
	println('Future date (today + 40 days) : ' . $fd);
	$pd = Date::pastDate($local, 500);
	println('Past date (today - 500 days) : ' . $pd);
	$diff = Date::getDiff('03/05/1980', date('d/m/Y'), TRUE, 'EURO');
	println('Date Diff (today - 03/05/1980) : ' . $diff);
	$pd = Date::prevDay('01/03/2004', 'EURO');
	$nd = Date::nextDay('28/02/2004', 'EURO');
	println('Next and Prev day - prev 01/03/2004 : ' . $pd . '  - next 28/02/2004 : ' . $nd);
	$t = Date::tomorrow();
	$y = Date::yesterday();
	println('Tomorrow and yesterday : ' . $t . ' ' . $y);
	println('');

	// date validation
	println('<b>Date Validation</b>');
	$arr = array('29/02/2003', '31/05/2002', '31/11/2000', '29/02/2000');
	for ($i=0; $i<sizeof($arr); $i++) {
		if (Date::isValid($arr[$i], 'EURO'))
			println('&nbsp;&nbsp;&nbsp;- valid : ' . $arr[$i]);
		else
			println('&nbsp;&nbsp;&nbsp;- not valid : ' . $arr[$i]);
	}
	println('');

	// date conversions
	println('<b>Date Conversion</b>');
	println('From SQL to local date: ' . Date::fromSqlDate(date('Y-m-d')));
	println('From local to SQL date: ' . Date::toSqlDate($local));
	println('');

	// date format
	println('<b>Date and Time Format</b>');
	println('Date format (ISO8601) : ' . Date::formatDate(3, 5, 1980, DATE_FORMAT_ISO8601));
	println('Date format (RFC822) : ' . Date::formatDate(3, 5, 1980, DATE_FORMAT_RFC822));
	println('Date format (Local) : ' . Date::formatDate(3, 5, 1980, DATE_FORMAT_LOCAL));
	println('Date format (Custom) : ' . Date::formatDate(3, 5, 1980, DATE_FORMAT_CUSTOM, 'D d m Y'));

	// time format
	println('Time format (ISO8601) : ' . Date::formatTime(time(), DATE_FORMAT_ISO8601));
	println('Time format (RFC822) : ' . Date::formatTime(time(), DATE_FORMAT_RFC822));
	println('Time format (Local) : ' . Date::formatTime(time(), DATE_FORMAT_LOCAL));
	println('Time format (Custom) : ' . Date::formatTime(time(), DATE_FORMAT_CUSTOM, 'H:i:s'));
	println('');

?>