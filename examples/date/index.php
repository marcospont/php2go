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

	// current month name, based on the active locale
	$mn = Date::monthName(time());
	println('Current month name (today) : ' . $mn);

	// day of week of a given date
	$dow = Date::dayOfWeek(date('d/m/Y'), true, false);
	println('Day of week ('.date('d/m/Y').') : ' . $dow);

	// days of a given month
	$dim = Date::daysInMonth(2, 1940);
	println('Days in month (02/1940) : ' . $dim);

	// future date
	$fd = Date::futureDate(date('d/m/Y'), 40);
	println('Future date (today + 40 days) : ' . $fd);

	// difference between days
	$diff = Date::getDiff('03/05/1980', date('d/m/Y'));
	println('Diff (today - 03/05/1980) : ' . $diff);

	// date validation
	println('Date Validation');
	$arr = array('29/02/2003', '31/05/2002', '31/11/2000', '29/02/2000');
	for ($i=0; $i<sizeof($arr); $i++) {
		if (Date::isValid($arr[$i]))
			println(' - valid : ' . $arr[$i]);
		else
			println(' - not valid : ' . $arr[$i]);
	}
	println('');

	// date conversions
	println('Date Conversion');
	println('From SQL to local date: ' . Date::fromSqlDate(date('Y-m-d')));
	println('From local to SQL date: ' . Date::toSqlDate(Date::localDate()));
	println('');

	// local current date
	println('Local date : ' . Date::localDate());

	// month name of a timestamp
	println('Month name (current date) : ' . Date::monthName(time()));

	// past date
	$pd = Date::pastDate(Date::localDate(), 100);
	println('Past date (today - 100 days) : ' . $pd);

	// previous and next day
	$pd = Date::prevDay('01/03/2004');
	$nd = Date::nextDay('28/02/2004');
	println('Next and Prev day - prev 01/03/2004 : ' . $pd . '  - next 28/02/2004 : ' . $nd);

	// print local date
	println('Print date : ' . Date::printDate());

	// tomorrow and yesterday
	$t = Date::tomorrow();
	$y = Date::yesterday();
	println('Tomorrow and yesterday : ' . $t . ' ' . $y);

?>