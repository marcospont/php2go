<?php

	// $Header: /www/cvsroot/php2go/examples/spreadsheet.example.php,v 1.9 2006/06/09 04:38:46 mpont Exp $
	// $Revision: 1.9 $
	// $Date: 2006/06/09 04:38:46 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

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