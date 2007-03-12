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

import('php2go.datetime.Date');
import('php2go.net.HttpResponse');

/**
 * Little endian byte order
 */
define('SPRSH_LITTLE_ENDIAN', 0);
/**
 * Big endian byte order
 */
define('SPRSH_BIG_ENDIAN', 1);
/**
 * Max spreadsheet rows
 */
define('SPRSH_MAX_ROWS', 65536);
/**
 * Max spreadsheet cols
 */
define('SPRSH_MAX_COLS', 256);
/**
 * Max chars inside a spreadsheet cell
 */
define('SPRSH_MAX_CHARS', 255);
/**
 * Max length of a cell note
 */
define('SPRSH_MAX_NOTE', 2048);
/**
 * Max size of a BIFF record in bytes
 */
define('SPRSH_RECORD_LIMIT', 2084);
/**
 * First font
 */
define('SPRSH_FONT_0', 0);
/**
 * Second font
 */
define('SPRSH_FONT_1', 0x40);
/**
 * Third font
 */
define('SPRSH_FONT_2', 0x80);
/**
 * Fourth font
 */
define('SPRSH_FONT_3', 0xC0);
/**
 * Used on date calculations
 */
define('SPRSH_DATE', 2415033);

/**
 * Streams data in the BIFF (Binary Interchange File Format) format
 *
 * The BIFF format can be read by applications like MS Excel and
 * OpenOffice.org. The class is compliant with BIFF version 2.1.
 *
 * @package util
 * @uses Date
 * @uses HttpResponse
 * @uses System
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Spreadsheet extends PHP2Go
{
	/**
	 * Whether errors should be thrown
	 *
	 * @var bool
	 */
	var $throwErrors = TRUE;

	/**
	 * Binary stream
	 *
	 * @var string
	 * @access private
	 */
	var $stream = '';

	/**
	 * Cell data stream
	 *
	 * @var string
	 * @access private
	 */
	var $dataStream = '';

	/**
	 * Cell notes stream
	 *
	 * @var string
	 * @access private
	 */
	var $noteStream = '';

	/**
	 * Byte order
	 *
	 * @var int
	 * @access private
	 */
	var $byteOrder = SPRSH_LITTLE_ENDIAN;

	/**
	 * Min row dimension
	 *
	 * @var int
	 * @access private
	 */
	var $minRowDimension;

	/**
	 * Max row dimension
	 *
	 * @var int
	 * @access private
	 */
	var $maxRowDimension;

	/**
	 * Min col dimension
	 *
	 * @var int
	 * @access private
	 */
	var $minColDimension;

	/**
	 * Max col dimension
	 *
	 * @var int
	 * @access private
	 */
	var $maxColDimension;

	/**
	 * Whether the spreadsheet should be protected with a password
	 *
	 * @var bool
	 * @access private
	 */
	var $protectBool = FALSE;

	/**
	 * Spreadsheet's password
	 *
	 * @var string
	 * @access private
	 */
	var $protectPasswd;

	/**
	 * Backup flag
	 *
	 * @var bool
	 * @access private
	 */
	var $backup = FALSE;

	/**
	 * Default cell width
	 *
	 * @var float
	 * @access private
	 */
	var $defaultColWidth = 8.43;

	/**
	 * Default row height
	 *
	 * @var float
	 * @access private
	 */
	var $defaultRowHeight = 12.75;

	/**
	 * Holds customized row heights
	 *
	 * @var array
	 * @access private
	 */
	var $rowInfo = array();

	/**
	 * Holds customized cell widths
	 *
	 * @var array
	 * @access private
	 */
	var $colInfo = array();

	/**
	 * Print headers flag
	 *
	 * @var bool
	 * @access private
	 */
	var $printHeaders = TRUE;

	/**
	 * Print grid lines flag
	 *
	 * @var bool
	 * @access private
	 */
	var $printGridLines = TRUE;

	/**
	 * Left margin
	 *
	 * @var float
	 * @access private
	 */
	var $leftMargin = 0.50;

	/**
	 * Right margin
	 *
	 * @var float
	 * @access private
	 */
	var $rightMargin = 0.50;

	/**
	 * Top margin
	 *
	 * @var float
	 * @access private
	 */
	var $topMargin = 0.50;

	/**
	 * Bottom margin
	 *
	 * @var float
	 * @access private
	 */
	var $bottomMargin = 0.50;

	/**
	 * Header text
	 *
	 * @var string
	 * @access private
	 */
	var $header = '';

	/**
	 * Header margin
	 *
	 * @var float
	 * @access private
	 */
	var $headerMargin = 0.50;

	/**
	 * Footer text
	 *
	 * @var string
	 * @access private
	 */
	var $footer = '';

	/**
	 * Footer margin
	 *
	 * @var float
	 * @access private
	 */
	var $footerMargin = 0.50;

	/**
	 * Horizontal page breaks
	 *
	 * @var array
	 * @access private
	 */
	var $horizontalBreaks = array();

	/**
	 * Vertical page breaks
	 *
	 * @var array
	 * @access private
	 */
	var $verticalBreaks = array();

	/**
	 * Indicates if the spreadsheet contains a selected area
	 *
	 * @var bool
	 * @access private
	 */
	var $selected = FALSE;

	/**
	 * Selection area
	 *
	 * @var array
	 * @access private
	 */
	var $selection = array(0, 0, 0, 0);

	/**
	 * Indicates if the spreadsheet contains frozen panes
	 *
	 * @var bool
	 * @access private
	 */
	var $freeze = TRUE;

	/**
	 * Frozen panes
	 *
	 * @var array
	 * @access private
	 */
	var $panes = array(0, 0, 0, 0);

	/**
	 * Active frozen pane
	 *
	 * @var int
	 * @access private
	 */
	var $activePane;

	/**
	 * Cell picures
	 *
	 * @var array
	 * @access private
	 */
	var $pictures = array (	'', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

	/**
	 * Cell fonts
	 *
	 * @var array
	 * @access private
	 */
	var $fonts;

	/**
	 * Cell formats
	 *
	 * @var array
	 * @access private
	 */
	var $cellFormats;

	/**
	 * Class constructor
	 *
	 * @return Spreadsheet
	 */
	function Spreadsheet() {
		parent::PHP2Go();
		$this->minRowDimension = SPRSH_MAX_ROWS + 1;
		$this->maxRowDimension = 0;
		$this->minColDimension = SPRSH_MAX_COLS + 1;
		$this->maxColDimension = 0;
		$this->addCellFormat();
		$this->_getByteOrder();
	}

	/**
	 * Adds a new cell picture
	 *
	 * @param string $picString Cell picture
	 * @return int New picture index
	 */
	function addPictureString($picString) {
		$count = sizeof($this->pictures);
		$this->pictures[] = $picString;
		return $count;
	}

	/**
	 * Adds a new cell font
	 *
	 * Font properties: name, size, bold, italic,
	 * underline and strikeout.
	 *
	 * @param array $properties Font properties
	 * @return int New font index
	 */
	function addFont($properties) {
		if (sizeof($this->fonts) < 4 && isset($properties['name'])) {
			$count = sizeof($this->fonts);
			$properties['font_index'] = constant('SPRSH_FONT_' . $count);
			$this->fonts[] = $properties;
			return $count;
		} else {
           	return 0;
		}
	}

	/**
	 * Adds a new cell format
	 *
	 * Formatting options: align, fill, shaded, box_border,
	 * left_border, right_border, top_border, bottom_border,
	 * locked and hidden.
	 *
	 * @param array $properties Formatting options
	 * @return int New format index
	 */
	function addCellFormat($properties=array()) {
		if (is_array($properties)) {
			$count = sizeof($this->cellFormats);
			$properties['format'] = $this->_buildFormat($properties);
			$status = 0x0;
			($properties['locked']) && ($status += 0x40);
			($properties['hidden']) && ($status += 0x80);
			$properties['status'] = $status;
            $this->cellFormats[] = $properties;
            return $count;
		} else {
           	return 0;
		}
	}

	/**
	 * Protects the spreadsheet with a password
	 *
	 * @param string $password Password
	 */
	function protectSheet($password) {
        $this->protectBool = TRUE;
		$this->protectPasswd = $this->_encodeSheetPasswd($password);
	}

	/**
	 * Enable/disable backup flag
	 *
	 * @param bool $flag Flag value
	 */
	function setBackup($flag) {
		$this->backup = (bool)$flag;
	}

	/**
	 * Set the height of a row or a range of rows
	 *
	 * @param int $height New height
	 * @param int $rowStart Row index
	 * @param int $rowEnd End row index
	 */
	function setRowHeight($height, $rowStart, $rowEnd=NULL) {
		if (TypeUtils::isInteger($rowStart) && TypeUtils::isInteger($rowEnd) && $rowEnd > $rowStart && $rowStart >= 0) {
        	for ($i=$rowStart; $i<=$rowEnd; $i++) {
				if (!in_array($i, $this->rowInfo)) {
                	$this->rowInfo[$i] = max(1, TypeUtils::parseInteger($height));
				}
			}
		} else if (TypeUtils::isInteger($rowStart) && $rowStart >= 0 && !in_array($rowStart, $this->rowInfo)) {
				$this->rowInfo[$rowStart] = max(1, TypeUtils::parseInteger($height));
		}
	}

	/**
	 * Set the width of a column or a range of columns
	 *
	 * @param int $width New width
	 * @param int $colStart Column index
	 * @param int $colEnd End column index
	 */
	function setColWidth($width, $colStart, $colEnd = NULL) {
		if (TypeUtils::isInteger($colStart) && TypeUtils::isInteger($colEnd) && $colEnd > $colStart && $colStart >= 0) {
        	for ($i=$colStart; $i<=$colEnd; $i++) {
				if (!in_array($i, $this->colInfo)) {
					$this->colInfo[$i] = max(1, TypeUtils::parseInteger($width));
                }
			}
		} else if (TypeUtils::isInteger($colStart) && $colStart >= 0 && !in_array($colStart, $this->colInfo)) {
        	$this->colInfo[$colStart] = max(1, TypeUtils::parseInteger($width));
		}
	}

	/**
	 * Enable/disable printing of grid lines
	 *
	 * @param bool $flag Flag value
	 */
	function setPrintGridlines($flag) {
    	$this->printGridlines = (bool)$flag;
	}

	/**
	 * Enable/disable printing of headers
	 *
	 * @param bool $flag Flag value
	 */
	function setPrintHeaders($flag) {
    	$this->printHeaders = (bool)$flag;
	}

	/**
	 * Set contents and margin of the spreadsheet header
	 *
	 * The margin values must be expressed in inches.
	 *
	 * @param string $header Text
	 * @param float $margin Margin
	 */
	function setHeader($header, $margin=0.50) {
		$this->header = $header;
		$this->headerMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	/**
	 * Set contents and margin of the spreadsheet footer
	 *
	 * The margin values must be expressed in inches.
	 *
	 * @param string $header Text
	 * @param float $margin Margin
	 */
	function setFooter($footer, $margin = 0.50) {
    	$this->footer = $footer;
		$this->footerMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	/**
	 * Set a single value to all spreadsheet margins
	 *
	 * The margin values must be expressed in inches.
	 *
	 * @param float $margin
	 */
	function setMargin($margin) {
    	$this->setLeftMargin($margin);
		$this->setRightMargin($margin);
		$this->setTopMargin($margin);
		$this->setBottomMargin($margin);
	}

	/**
	 * Set left margin
	 *
	 * @param float $margin Left margin
	 */
	function setLeftMargin($margin) {
		$this->leftMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	/**
	 * Set right margin
	 *
	 * @param float $margin Right margin
	 */
	function setRightMargin($margin) {
		$this->rightMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	/**
	 * Set top margin
	 *
	 * @param float $margin Top margin
	 */
	function setTopMargin($margin) {
		$this->topMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	/**
	 * Set bottom margin
	 *
	 * @param float $margin Bottom margin
	 */
	function setBottomMargin($margin) {
		$this->bottomMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	/**
	 * Adds an horizontal page break
	 *
	 * @param int $row Row index
	 */
	function addHorizontalBreak($row) {
		if (!in_array($row, $this->horizontalBreaks)) {
        	$this->horizontalBreaks[] = $row;
		}
	}

	/**
	 * Adds a vertical page break
	 *
	 * @param int $col Column index
	 */
	function addVerticalBreak($col) {
		if (!in_array($col, $this->verticalBreaks)) {
        	$this->verticalBreaks[] = $col;
		}
	}

	/**
	 * Set selection area
	 *
	 * @param int $firstRow First row
	 * @param int $firstCol First column
	 * @param int $lastRow Last row
	 * @param int $lastCol Last column
	 */
	function setSelection($firstRow, $firstCol, $lastRow, $lastCol) {
		$this->selected = TRUE;
		$frow = ($firstRow > $lastRow) ? $lastRow : $firstRow;
		$lrow = $lastRow;
		$fcol = ($firstCol > $lastCol) ? $lastCol : $firstCol;
		$lcol = $lastCol;
		$this->selection = array($frow, $fcol, $lrow, $lcol);
	}

	/**
	 * Adds a frozen pane
	 *
	 * @param int $width Width (number of columns)
	 * @param int $height Height (number of rows)
	 * @param int $leftCol First visible column on left side
	 * @param int $topRow First visible row on top side
	 */
	function freezePanes($width, $height, $leftCol=NULL, $topRow=NULL) {
		if (!TypeUtils::isInteger($width) || !TypeUtils::isInteger($height) || $height > 0 || $width > 0) {
			$this->freeze = TRUE;
			$this->panes[0] = $height;
			$this->panes[1] = $width;
			$this->panes[2] = TypeUtils::ifNull($topRow, $height);
			$this->panes[3] = TypeUtils::ifNull($leftCol, $width);
		}
	}

	/**
	 * Writes data on a given position
	 *
	 * @param int $row Row index
	 * @param int $col Column index
	 * @param mixed $value Value
	 * @param int $columnWidth Column width
	 * @param int $picture Picture index
	 * @param int $font Font index
	 * @param int $format Format index
	 * @return bool
	 */
	function writeData($row, $col, $value, $columnWidth=0, $picture=0, $font=0, $format=0) {
		if ( preg_match("/^=?[+-]?(\d|\.\d)?\d*(\.\d+)?([eE][+-]?(\d|\.\d)?\d*(\.\d+)?)?$/", ereg_replace(',', '.', $value)) ) {
			return $this->writeNumber($row, $col, $value, $columnWidth, $picture, $font, $format);
		} else if ( preg_match("/^(\d{2}[\/-]\d{2}[\/-]\d{4}|\d{4}[\/-]\d{2}[\/-]\d{2}).*$/", ereg_replace("\/", "-", $value) ) ) {
			return $this->writeDateTime($row, $col, $value, $columnWidth, $picture, $font, $format);
		} else if ( preg_match("/^(f|ht)tps?:\/\/.+$/", $value) ) {
        	//$this->writeUrl($row, $col, $value, $columnWidth, $picture, $font, $format);
        	return $this->writeString($row, $col, $value, $columnWidth, $picture, $font, $format);
        	return FALSE;
		} else if ( preg_match("/^mailto:.+$/", $value) ) {
        	//$this->writeUrl($row, $col, $value, $columnWidth, $picture, $font, $format);
        	return $this->writeString($row, $col, $value, $columnWidth, $picture, $font, $format);
		} else if ( trim($value) == '' ) {
        	//$this->writeBlank($row, $col, $columnWidth, $format);
        	return $this->writeString($row, $col, '', $columnWidth, $picture, $font, $format);
		} else {
        	return $this->writeString($row, $col, $value, $columnWidth, $picture, $font, $format);
		}
	}

	/**
	 * Writes a numeric value on a given position
	 *
	 * @param int $row Row index
	 * @param int $col Column index
	 * @param int|float $value Numeric value
	 * @param int $columnWidth Column width
	 * @param int $picture Picture index
	 * @param int $font Font index
	 * @param int $format Format index
	 * @return bool
	 */
	function writeNumber($row, $col, $value, $columnWidth=0, $picture=0, $font=0, $format=0) {
		if (!$this->_checkBounds($row, $col)) {
			if ($this->throwErrors) {
            	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SPRSH_OUT_OF_BOUNDS', array(SPRSH_MAX_ROWS, SPRSH_MAX_COLS)), E_USER_ERROR, __FILE__, __LINE__);
			}
			return FALSE;
		} else {
			$id = 0x0003;
			$length = 0x000F;
			$len = strlen(TypeUtils::parseString($value));
			$this->_adjustColWidth($col, $columnWidth, $len);
			$number = ($this->byteOrder == SPRSH_BIG_ENDIAN) ? strrev(pack('d', $value)) : pack('d', $value);
			$fontIndex = isset($this->fonts[$font]) ? $this->fonts[$font]['font_index'] : SPRSH_FONT_0;
			if (isset($this->cellFormats[$format])) {
            	$cellFormat = $this->cellFormats[$format]['format'];
				$cellStatus = $this->cellFormats[$format]['status'];
			} else {
            	$cellFormat = 0x0;
				$cellStatus = 0x0;
			}
			$data = pack('vvvvCCC', $id, $length, $row, $col, $cellStatus, $picture + $fontIndex, $cellFormat). $number;
			$this->dataStream .= $data;
			return TRUE;
		}
	}

	/**
	 * Writes a datetime value on a given position
	 *
	 * @param int $row Row index
	 * @param int $col Column index
	 * @param string $value Datetime value
	 * @param int $columnWidth Column width
	 * @param int $picture Picture index
	 * @param int $font Font index
	 * @param int $format Format index
	 * @return bool
	 */
	function writeDateTime($row, $col, $value, $columnWidth=0, $picture=0, $font=0, $format=0) {
		if (!$this->_checkBounds($row, $col)) {
			if ($this->throwErrors) {
            	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SPRSH_OUT_OF_BOUNDS', array(SPRSH_MAX_ROWS, SPRSH_MAX_COLS)), E_USER_ERROR, __FILE__, __LINE__);
			}
			return FALSE;
		} else {
			$id = 0x0003;
			$length = 0x000F;
			$value = $this->_transformDate($value);
			$len = strlen(TypeUtils::parseString($value));
			$this->_adjustColWidth($col, $columnWidth, $len);
			$value = ($this->byteOrder == SPRSH_BIG_ENDIAN) ? strrev(pack('d', $value)) : pack('d', $value);
			$fontIndex = isset($this->fonts[$font]) ? $this->fonts[$font]['font_index'] : SPRSH_FONT_0;
			if (isset($this->cellFormats[$format])) {
            	$cellFormat = $this->cellFormats[$format]['format'];
				$cellStatus = $this->cellFormats[$format]['status'];
			} else {
            	$cellFormat = 0x0;
				$cellStatus = 0x0;
			}
			$data = pack('vvvvCCC', $id, $length, $row, $col, $cellStatus, $picture + $fontIndex, $cellFormat). $value;
			$this->dataStream .= $data;
			return TRUE;
		}
	}

	/**
	 * Writes a string on a given position
	 *
	 * @param int $row Row index
	 * @param int $col Column index
	 * @param string $value Value to be written
	 * @param int $columnWidth Column width
	 * @param int $picture Picture index
	 * @param int $font Font index
	 * @param int $format Format index
	 * @return bool
	 */
	function writeString($row, $col, $value, $columnWidth=0, $picture=0, $font=0, $format=0) {
		if (!$this->_checkBounds($row, $col)) {
			if ($this->throwErrors) {
            	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SPRSH_OUT_OF_BOUNDS', array(SPRSH_MAX_ROWS, SPRSH_MAX_COLS)), E_USER_ERROR, __FILE__, __LINE__);
			}
			return FALSE;
		} else {
			$id = 0x0004;
			$length = 0x0008;
			$len = strlen(TypeUtils::parseString($value));
			$this->_adjustColWidth($col, $columnWidth, $len);
			$fontIndex = isset($this->fonts[$font]) ? $this->fonts[$font]['font_index'] : SPRSH_FONT_0;
			if (isset($this->cellFormats[$format])) {
            	$cellFormat = $this->cellFormats[$format]['format'];
				$cellStatus = $this->cellFormats[$format]['status'];
			} else {
            	$cellFormat = 0x0;
				$cellStatus = 0x0;
			}
			$data = pack('vvvvCCCC', $id, $length + $len, $row, $col, $cellStatus, $picture + $fontIndex, $cellFormat, $len). $value;
			$this->dataStream .= $data;
			return TRUE;
		}
	}

	/**
	 * Writes a blank string on a given position
	 *
	 * @param int $row Row index
	 * @param int $col Column index
	 * @param int $columnWidth Column width
	 * @param int $format Format index
	 * @return bool
	 */
	function writeBlank($row, $col, $columnWidth=0, $format=0) {
		if (!$this->_checkBounds($row, $col)) {
			if ($this->throwErrors) {
            	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SPRSH_OUT_OF_BOUNDS', array(SPRSH_MAX_ROWS, SPRSH_MAX_COLS)), E_USER_ERROR, __FILE__, __LINE__);
			}
			return FALSE;
		} else if ($format > 0) {
			$id = 0x0001;
			$length = 0x0007;
			$this->_adjustColWidth($col, $columnWidth, $len);
			$cellFormat = isset($this->cellFormats[$format]) ? $this->cellFormats[$format]['format'] : ALIGN_GENERAL;
			$data = pack('vvvvCCC', $id, $length, $row, $col, 0x0, 0x0, 0x80);
			$this->dataStream .= $data;
			return TRUE;
		}
	}

	/**
	 * Writes an array of values on a given row
	 *
	 * The method determines data type by calling
	 * {@link gettype} for each array entry.
	 *
	 * @param int $row Row index
	 * @param array $array Array of values
	 * @param int $columnWidth Column width
	 * @param int $font Font index
	 * @param int $format Format index
	 * @param int $startCol Index of the start column
	 */
	function writeArray($row, $array, $columnWidth=0, $font=0, $format=0, $startCol=0) {
		$array = (array)$array;
		$col = abs(intval($startCol));
		foreach ($array as $key => $value) {
			$type = gettype($value);
			switch ($type) {
				case 'integer' :
				case 'double' :
					$this->writeNumber($row, $col, $value, $columnWidth, 0, $font, $format);
					break;
				default :
					$this->writeString($row, $col, strval($value), $columnWidth, 0, $font, $format);
					break;
			}
			$col++;
		}
	}

	/**
	 * Adds a note on a given position
	 *
	 * @param int $row Row index
	 * @param int $col Column index
	 * @param string $value Cell note
	 * @return bool
	 */
	function addCellNote($row, $col, $value) {
		if (!$this->_checkBounds($row, $col)) {
			if ($this->throwErrors) {
            	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SPRSH_OUT_OF_BOUNDS', array(SPRSH_MAX_ROWS, SPRSH_MAX_COLS)), E_USER_ERROR, __FILE__, __LINE__);
			}
			return FALSE;
		} else {
			$len = strlen($value);
			if ($len <= SPRSH_MAX_NOTE) {
				$id = 0x001C;
            	$length = 0x0006;
				$this->noteStream .= pack('vvvvv', $id, $length + $len, $row, $col, $len) . $value;
				return TRUE;
			} else {
				if ($this->throwErrors) {
            		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SPRSH_MAX_NOTE_CHARS', $value), E_USER_ERROR, __FILE__, __LINE__);
				}
				return FALSE;
			}
		}
	}

	/**
	 * Sends the proper download headers and flushes the spreadsheet contents
	 *
	 * @uses _prepareSpreadsheet()
	 * @uses HttpResponse::download()
	 * @param string $fileName File name
	 * @param string $mimeType MIME type
	 */
	function download($fileName, $mimeType='') {
		if (!headers_sent()) {
			$this->_prepareSpreadsheet();
			HttpResponse::download($fileName, strlen($this->stream), $mimeType);
			print $this->stream;
		}
	}

	/**
	 * Renders and saves the spreadsheet contents to a file
	 *
	 * @uses _prepareSpreadsheet()
	 * @param string $fileName File name
	 * @param int $fileMode File mode
	 * @return bool
	 */
	function toFile($fileName, $fileMode=NULL) {
		$this->_prepareSpreadsheet();
		$fp = @fopen($fileName, 'wb');
		if ($fp !== FALSE) {
			fputs($fp, $this->stream);
			fclose($fp);
			if ($fileMode != NULL)
				chmod($fileName, $fileMode);
			return TRUE;
		} else {
        	if ($this->throwErrors)
            	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
	}

	/**
	 * Renders spreadsheet's binary stream
	 *
	 * @access private
	 */
	function _prepareSpreadsheet() {
		$this->_writeBeginOfFile();
		$this->_writeCodePage();
		$this->_writeBackup();
		$this->_writePrintHeaders();
		$this->_writePrintGridLines();
		$this->_writeBreaks();
		$this->_writeDefaultRowHeight();
		$this->_writeWsBool();
		$this->_writeFonts();
		$this->_writeHeader();
		$this->_writeFooter();
		$this->_writeMargins();
		$this->_writeColInfo();
		$this->_writePictures();
		$this->_writeRowInfo();
		$this->stream .= $this->dataStream;
		$this->_writeProtect();
		$this->_appendStream($this->noteStream);
		$this->_writeWindow1();
		$this->_writeWindow2();
		$this->_writePanes();
		$this->_writeSelection();
		$this->_writeEndOfFile();
	}

	/**
	 * Appends a value in the spreadsheet's binary stream
	 *
	 * @param string $value Value to append
	 * @access private
	 */
	function _appendStream($value) {
		if (strlen($value) > SPRSH_RECORD_LIMIT) {
 			$continueData = $this->_writeContinue($value);
        	$this->stream .= $continueData;
		} else {
        	$this->stream .= $value;
		}
	}

	/**
	 * Checks the floating point format of the server
	 *
	 * @access private
	 */
	function _getByteOrder() {
		// IEEE 64-bit 3F F3 C0 CA 42 83 DE 1B
		$numberA = pack("d", 1.2345);
		$numberB = pack("C8", 0x8D, 0x97, 0x6E, 0x12, 0x83, 0xC0, 0xF3, 0x3F);
		if ($numberB == $numberA) {
			$this->byteOrder = SPRSH_LITTLE_ENDIAN;
		} elseif ($numberB == strrev($numberA)) {
			$this->byteOrder = SPRSH_BIG_ENDIAN;
		}
	}

	/**
	 * Builds a hash from the properties of a cell format
	 *
	 * @param array $properties Cell format properties
	 * @return int Hash
	 * @access private
	 */
	function _buildFormat($properties) {
    	$format = 0x0;
		if ($properties['align']) {
			if ($properties['align'] == 'left') $format += 0x1;
			if ($properties['align'] == 'center') $format += 0x2;
			if ($properties['align'] == 'right') $format += 0x3;
		}
		if ($properties['fill'])
        	$format += 0x4;
		if ($properties['shaded'])
        	$format += 0x80;
		if ($properties['box_border']) {
        	$format += 0x78;
		} else {
			if ($properties['left_border']) $format += 0x8;
			if ($properties['right_border']) $format += 0x10;
			if ($properties['top_border']) $format += 0x20;
			if ($properties['bottom_border']) $format += 0x40;
		}
		return $format;
	}

	/**
	 * Encodes the spreadsheet's password
	 *
	 * @param string $password Plain text password
	 * @return string Encoded password
	 * @access private
	 */
	function _encodeSheetPasswd($password) {
		$i = 1;
		$encoded = 0x0000;
		$passwdLen = strlen($password);
		$chars = preg_split('//', $password, -1, PREG_SPLIT_NO_EMPTY);
		foreach($chars as $char) {
			$value     = ord($char) << $i;
			$bit_16    = $value & 0x8000;
			$bit_16  >>= 15;
			$value    &= 0x7fff;
			$encoded  ^= ($value | $bit_16);
			$i++;
		}
		$encoded ^= $passwdLen;
		$encoded ^= 0xCE4B;
		return $encoded;
	}

	/**
	 * Checks if a row/column pair respects the spreadsheet limits
	 *
	 * @param int $row Row index
	 * @param int $col Column index
	 * @access private
	 * @return bool
	 */
	function _checkBounds($row, $col) {
    	if ($row < 0 || $row > SPRSH_MAX_ROWS)
        	return FALSE;
		if ($col < 0 || $col > SPRSH_MAX_COLS)
        	return FALSE;
		if ($row < $this->minRowDimension)
        	$this->minRowDimension = $row;
		if ($row >= $this->maxRowDimension)
        	$this->maxRowDimension = $row;
		if ($col < $this->minColDimension)
        	$this->minColDimension = $col;
		if ($col >= $this->maxColDimension)
			$this->maxColDimension = $col;
		return TRUE;
	}

	/**
	 * Rebuilds the column width based on the length of inserted values
	 *
	 * @param int $col Column index
	 * @param int $colWidth Desired column width
	 * @param int $len Value length: defines the minimum width
	 * @access private
	 */
	function _adjustColWidth($col, $colWidth, $len) {
		if ($colWidth > 0) {
			$this->colInfo[$col] = $colWidth;
		}
		if ($colWidth == 0) {
			if (isset($this->colInfo[$col])) {
				if ($this->colInfo[$col] < $len) {
					$this->colInfo[$col] = $len;
				}
			} else {
				$this->colInfo[$col] = $len;
			}
		}
	}

	/**
	 * Converts a date or datetime string into a julian day count
	 *
	 * @uses Date::isEuroDate()
	 * @uses Date::isUsDate()
	 * @uses Date::isSqlDate()
	 * @uses System::loadExtension()
	 * @uses juliantojd()
	 * @param string $date Date or datetime
	 * @return int Julian day count
	 * @access private
	 */
	function _transformDate($date) {
		if (!System::loadExtension('calendar'))
			return '';
		$dateParts = array();
		if (Date::isEuroDate($date, $dateParts)) {
			$dtVal = juliantojd($dateParts[2], $dateParts[1], $dateParts[3]) - SPRSH_DATE + 1;
		} elseif (Date::isUsDate($date, $dateParts) || Date::isSqlDate($date, $dateParts)) {
			$dtVal = juliantojd($dateParts[2], $dateParts[3], $dateParts[1]) - SPRSH_DATE + 1;
		}
		if (isset($dateParts[5]))
			$dtVal += ( TypeUtils::parseInteger($dateParts[5]) / 24 );
		if (isset($dateParts[6]))
			$dtVal += ( TypeUtils::parseInteger($dateParts[6]) / 1440 );
		if (isset($dateParts[7]))
			$dtVal += ( TypeUtils::parseInteger($dateParts[7]) / 86400 );
		return $dtVal;
	}

	/**
	 * Writes a sequence of CONTINUE records
	 *
	 * Write a sequence of blocks of 2080 bytes to allow records greater
	 * than the limit imposed by the BIFF 2.1 format.
	 *
	 * @param string $data Block data
	 * @access private
	 * @return string
	 */
	function _writeContinue($data) {
        $limit = 2080;
        $record = 0x003C;
		// keep first 2080 bytes
        $result = substr($data, 0, 2) . pack("v", $limit - 4) . substr($data, 4, $limit - 4);
        // add next blocks of 2080 bytes
        for($i = 2080; $i < strlen($data) - $limit; $i += $limit) {
            $result .= pack("vv", $record, $limit);
            $result .= substr($data, $i, $limit - 4);
        }
		// add remaining bytes
        $result .= pack("vv", $record, strlen($data) - $i);
        $result .= substr($data, $i, strlen($data) - $i);
        return $result;
	}

	/**
	 * Writes BOF (begin of file) record
	 *
	 * @access private
	 */
	function _writeBeginOfFile() {
		$id = 0x0009;
		$length = 0x0004;
		$version = 0x0007;
		$type = 0x0010;
		$this->_appendStream(pack('vvvv', $id, $length, $version, $type));
	}

	/**
	 * Writes the CODEPAGE record
	 *
	 * @access private
	 */
	function _writeCodePage() {
		$id = 0x0042;
		$length = 0x0002;
		$this->_appendStream(pack('vvv', $id, $length, 0x8001));
	}

	/**
	 * Writes the BACKUP record
	 *
	 * @access private
	 */
	function _writeBackup() {
		$id = 0x0040;
		$length = 0x0002;
    	if ($this->backup) {
			$this->_appendStream(pack('vvv', $id, $length, 1));
		} else {
			$this->_appendStream(pack('vvv', $id, $length, 0));
		}
	}

	/**
	 * Writes the PRINTROWHEADERS record
	 *
	 * @access private
	 */
	function _writePrintHeaders() {
		$id = 0x002A;
		$length = 0x0002;
		$this->_appendStream(pack('vvv', $id, $length, TypeUtils::parseInteger($this->printHeaders)));
	}

	/**
	 * Writes the PRINTGRIDLINES record
	 *
	 * @access private
	 */
	function _writePrintGridLines() {
		$id = 0x002B;
		$length = 0x0002;
		$this->_appendStream(pack('vvv', $id, $length, TypeUtils::parseInteger($this->printGridLines)));
	}

	/**
	 * Writes the HBREAK and VBREAK records
	 *
	 * @access private
	 */
	function _writeBreaks() {
		$hCount = count($this->horizontalBreaks);
		if ($hCount > 0) {
			sort($this->horizontalBreaks);
			foreach($this->horizontalBreaks as $x) {
				$h .= pack('v', $x);
			}
			$id = 0x001B;
			$length = 0x0002;
			$this->_appendStream(pack('vvv', $id, $length + ($hCount * 2) , $hCount) . $h);
		}
		$vCount = count($this->verticalBreaks);
		if ($vCount > 0) {
			sort($this->verticalBreaks);
			foreach($this->verticalBreaks as $x) {
				$v .= pack('v', $x);
			}
			$id = 0x001A;
			$length = 0x0002;
			$this->_appendStream(pack('vvv', $id, $length + ($vCount * 2), $vCount) . $v);
		}
	}

	/**
	 * Writes the DEFAULTROWHEIGHT record
	 *
	 * @access private
	 */
	function _writeDefaultRowHeight() {
		$id = 0x0025;
		$length = 0x0002;
		$this->_appendStream(pack('vvv', $id, $length, $this->defaultRowHeight * 20));
	}

	/**
	 * Writes the WSBOOL record
	 *
	 * @access private
	 */
	function _writeWsBool() {
		$id = 0x0081;
		$length = 0x0002;
		$optionFlags = 0x04C1;
		$this->_appendStream(pack('vvv', $id, $length, $optionFlags));
	}

	/**
	 * Writes the cell fonts
	 *
	 * @access private
	 */
	function _writeFonts() {
		for ($i=0,$s=sizeof($this->fonts); $i<$s; $i++) {
			$font = $this->fonts[$i];
			$fontName = $font['name'];
			$fontSize = $font['size'] ? $font['size'] * 20 : 10 * 20;
			$fontLength = strlen($fontName);
			$length = 0x0005 + $fontLength;
			$id = 0x0031;
			$fontFormat = 0x0;
			if ($font['bold']) $fontFormat += 0x1;
			if ($font['italic']) $fontFormat += 0x2;
			if ($font['underline']) $fontFormat += 0x4;
			if ($font['strikeout']) $fontFormat += 0x8;
			$this->_appendStream(pack('vvvCCC', $id, $length, $fontSize, $fontFormat, 0x0, $fontLength) . $fontName);
		}
	}

	/**
	 * Writes the HEADER record
	 *
	 * @access private
	 */
	function _writeHeader() {
		$id = 0x0014;
		$length = 1;
		$len = strlen($this->header);
		$this->_appendStream(pack('vvC', $id, $length + $len, $len) . $this->header);
	}

	/**
	 * Writes the FOOTER record
	 *
	 * @access private
	 */
	function _writeFooter() {
		$id = 0x0015;
		$length = 1;
		$len = strlen($this->footer);
		$this->_appendStream(pack('vvC', $id, $length + $len, $len) . $this->footer);
	}

	/**
	 * Writes the LEFTMARGIN, RIGHTMARGIN, TOPMARGIN and BOTTOMMARGIN records
	 *
	 * @access private
	 */
	function _writeMargins() {
		$length = 0x0008;
		$left = ($this->byteOrder == SPRSH_BIG_ENDIAN) ? strrev(pack('d', $this->leftMargin)) : pack('d', $this->leftMargin);
		$right = ($this->byteOrder == SPRSH_BIG_ENDIAN) ? strrev(pack('d', $this->rightMargin)) : pack('d', $this->rightMargin);
		$top = ($this->byteOrder == SPRSH_BIG_ENDIAN) ? strrev(pack('d', $this->topMargin)) : pack('d', $this->topMargin);
    	$bottom = ($this->byteOrder == SPRSH_BIG_ENDIAN) ? strrev(pack('d', $this->bottomMargin)) : pack('d', $this->bottomMargin);
		$id = 0x0026;
		$this->_appendStream(pack('vv', $id, $length) . $left);
		$id = 0x0027;
		$this->_appendStream(pack('vv', $id, $length) . $right);
		$id = 0x0028;
		$this->_appendStream(pack('vv', $id, $length) . $top);
		$id = 0x0029;
		$this->_appendStream(pack('vv', $id, $length) . $bottom);
	}

	/**
	 * Writes the records containing the column widths
	 *
	 * @access private
	 */
	function _writeColInfo() {
		$id = 0x007D;
		$length = 0x000B;
		foreach($this->colInfo as $col => $width) {
			$this->_appendStream(pack('vvvvvvvC', $id, $length, $col, $col, ($width + 0.72) * 256, 0x0F, 0, 0x00));
		}
	}

	/**
	 * Writes the cell pictures
	 *
	 * @access private
	 */
	function _writePictures() {
		$formatCount = count($this->pictures);
		$id = 0x001F;
		$length = 0x0002;
		$this->_appendStream(pack('vvv', $id, $length, 0x15));
		for ($x = 0; $x < $formatCount; $x++) {
			$id = 0x001E;
			$length = 0x0001;
			$formatLength = strlen($this->pictures[$x]);
			$this->_appendStream(pack('vvC', $id, $length + $formatLength, $formatLength) . $this->pictures[$x]);
		}
	}

	/**
	 * Writes the row heights
	 *
	 * @access private
	 */
	function _writeRowInfo() {
		foreach($this->rowInfo as $row => $height) {
			$col_start = 0;
			$col_end = SPRSH_MAX_COLS;
			$res = 0x0;
			$id = 0x0008;
			$length = 0x000D;
			$this->_appendStream(pack('vvvvvvvCCC', $id, $length, $row, $col_start, $col_end, $height * 20, 0, 0, 0, 0));
		}
	}

	/**
	 * Writes the protection password, if enabled
	 *
	 * @access private
	 */
	function _writeProtect() {
		if ($this->protectBool && !empty($this->protectPasswd)) {
			$id = 0x0012;
			$length = 0x0002;
			$this->_appendStream(pack('vvv', $id, $length, $this->protectPasswd));
			$id = 0x0013;
			$length = 0x0002;
			$this->_appendStream(pack('vvv', $id, $length, 1));
		} else {
			$id = 0x0013;
			$length = 0x0002;
			$this->_appendStream(pack('vvv', $id, $length, 0));
		}
	}

	/**
	 * Writes the WINDOW1 record
	 *
	 * @access private
	 */
	function _writeWindow1() {
		$id = 0x003D;
		$length = 0x0012;
		$hPosition = 0x0000;
		$vPosition = 0x0000;
		$winWidth = 0x25BC;
		$winHeight = 0x1572;
		$optionFlags = 0x0038;
		$currentSheet = 0;
		$firstSheet = 0;
        $selected = $this->selected;
		$scrollbarRatio = 0x0258;
		$this->_appendStream(pack('vvvvvvvvvvv', $id, $length, $hPosition, $vPosition,
								$winWidth, $winHeight, $optionFlags,
								$currentSheet, $firstSheet, $selected,
								$scrollbarRatio));
	}

	/**
	 * Writes the WINDOW2 record
	 *
	 * @access private
	 */
	function _writeWindow2() {
		$id = 0x003E;
		$length = 0x000E;
		$displayFormula = FALSE;
		$displayGrid = TRUE;
		$displayRef = TRUE;
		$displayZero = TRUE;
		$topRow = 0x0000;
		$leftCol = 0x0000;
		$headersRgb = 0x0000;
		$gridRgb = 0x0000;
		$this->_appendStream(pack('vvCCCCCvvCvv', $id, $length,
								$displayFormula, $displayGrid, $displayRef,
								$this->freeze, $displayZero, $topRow, $leftCol, 1, $headersRgb, $gridRgb));
	}

	/**
	 * Writes the SELECTION record
	 *
	 * @access private
	 */
	function _writeSelection() {
		$id = 0x001D;
		$length = 0x000F;
		$this->_appendStream(pack('vvCvvvvvvCC', $id, $length, $this->activePane,
								$this->selection[0], $this->selection[1], 0, 1,
								$this->selection[0], $this->selection[2],
								$this->selection[1], $this->selection[3]));
	}

	/**
	 * Writes the PANE record
	 *
	 * @access private
	 */
	function _writePanes() {
		$id = 0x0041;
		$length = 0x000A;
		if (!$this->freeze) {
			if (!isset($this->panes[2]))
            	$this->panes[2] = 0;
			if (!isset($this->panes[3]))
            	$this->panes[3] = 0;
            $this->panes[0] = 20 * $this->panes[0] + 255;
			$this->panes[1] = 113.879 * $this->panes[1] + 390;
        }
        if ($this->panes[1] != 0 && $this->panes[0] != 0)
            $this->activePane = 0;
        if ($this->panes[1] != 0 && $this->panes[1] == 0)
            $this->activePane = 1;
        if ($this->panes[1] == 0 && $this->panes[0] != 0)
            $this->activePane = 2;
        if ($this->panes[1] == 0 && $this->panes[0] == 0)
            $this->activePane = 3;
		$this->_appendStream(pack('vvvvvvv', $id, $length, $this->panes[0], $this->panes[1], $this->panes[3], $this->panes[2], $this->activePane));
	}

	/**
	 * Writes the EOF (end of file) record
	 *
	 * @access private
	 */
	function _writeEndOfFile() {
		$id = 0x000A;
		$this->_appendStream(pack('v', $id));
	}
}
?>