<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

	require_once('config.example.php');
	import('php2go.util.Spreadsheet');

	println('<b>PHP2Go Example</b> : php2go.util.Spreadsheet<br>');

	$sp = new Spreadsheet();

	/**
	 * create a font format. the method returns an index that can be used later
	 */
	$arialBold = $sp->addFont(array('bold'=>true, 'italic'=>true, 'name'=>'Arial'));
	/**
	 * create a cell format
	 */
	$borders = $sp->addCellFormat(array('left_border'=>true, 'right_border'=>true, 'shaded'=>true));

	/**
	 * writing cells and creating cell notes
	 */
	$sp->writeString(0, 0, 'Code', 0, 0, $arialBold, $borders);
	$sp->addCellNote(0, 0, 'The product code');
	$sp->writeString(0, 1, 'Short Desc', 0, 0, $arialBold, $borders);
	$sp->addCellNote(0, 1, 'A brief description of the product');
	$sp->writeString(0, 2, 'Long Desc', 0, 0, $arialBold, $borders);
	$sp->addCellNote(0, 2, 'A long description of the product');

	/**
	 * writing the results of a database query
	 */
	$db =& Db::getInstance();
	$rs =& $db->query("select * from products");
	while (!$rs->EOF) {
		$sp->writeString($rs->absolutePosition()+1, 0, $rs->fields['code']);
		$sp->writeString($rs->absolutePosition()+1, 1, $rs->fields['short_desc']);
		$sp->writeString($rs->absolutePosition()+1, 2, $rs->fields['long_desc']);
		$rs->moveNext();
	}

	/**
	 * create a frozen pane
	 */
	$sp->freezePanes(1, 10);

	/**
	 * save the final content
	 */
	$location = 'tmp/spreadsheet.example.xls';
	$sp->toFile($location);

	print 'Spreadsheet generated and saved at <a href=\'' . $location . '\'>' . $location . '</a>';

?>