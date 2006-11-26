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
// $Header: /www/cvsroot/php2go/core/util/Spreadsheet.class.php,v 1.21 2006/11/19 18:02:27 mpont Exp $
// $Date: 2006/11/19 18:02:27 $

//------------------------------------------------------------------
import('php2go.file.FileManager');
import('php2go.net.HttpResponse');
import('php2go.text.StringUtils');
import('php2go.util.System');
//------------------------------------------------------------------

// @const	SPRSH_LITTLE_ENDIAN		"0"
// Formato de ponto flutuante LITTLE ENDIAN
define('SPRSH_LITTLE_ENDIAN', 0);
// @const	SPTSH_BIG_ENDIAN		"1"
// Formato de ponto flutuante BIG_ENDIAN
define('SPRSH_BIG_ENDIAN', 1);
// @const	SPRSH_MAX_ROWS			"65536"
// Número máximo de linhas da planilha
define('SPRSH_MAX_ROWS', 65536);
// @const	SPRSH_MAX_COLS			"256"
// Número máximo de colunas da planilha
define('SPRSH_MAX_COLS', 256);
// @const	SPRSH_MAX_CHARS			"255"
// Número máximo de caracteres em uma célula do tipo string
define('SPRSH_MAX_CHARS', 255);
// @const	SPRSH_MAX_NOTE			"2048"
// Tamanho máximo de uma nota de célula
define('SPRSH_MAX_NOTE', 2048);
// @const	SPRSH_RECORD_LIMIT		"2048"
// Tamanho máximo em bytes de um registro BIFF
define('SPRSH_RECORD_LIMIT', 2084);
// @const	SPRSH_FONT_0			"0"
// Código hexadecimal da fonte índice zero
define('SPRSH_FONT_0', 0);
// @const	SPRSH_FONT_1			"0x40"
// Código hexadecimal da fonte índice 1
define('SPRSH_FONT_1', 0x40);
// @const	SPRSH_FONT_2			"0x80"
// Código hexadecimal da fonte índice 2
define('SPRSH_FONT_2', 0x80);
// @const	SPRSH_FONT_3			"0xC0"
// Código hexadecimal da fonte índice 3
define('SPRSH_FONT_3', 0xC0);
// @const	SPRSH_DATE				"2415033"
// Constante utilizada no cálculo de datas
define('SPRSH_DATE', 2415033);

//!-----------------------------------------------------------------
// @class		Spreadsheet
// @desc		Esta classe implementa a exportação de dados para arquivos
//				no formato BIFF (Binary Interchange File Format), interpretado
//				por planilhas eletrônicas como o MS Excel
// @package		php2go.util
// @extends 	PHP2Go
// @uses		FileManager
// @uses		HttpResponse
// @uses		System
// @author		Marcos Pont
// @version		$Revision: 1.21 $
// @note		A classe é compatível com a versão 2.1 do formato BIFF
//!-----------------------------------------------------------------
class Spreadsheet extends PHP2Go
{
	var $stream = '';						// @var stream string				"" Conteúdo da planilha
	var $streamSize = 0;					// @var streamSize int				Tamanho da planilha em bytes
	var $dataStream = '';					// @var dataStream string			"" Stream de dados da planilha
	var $noteStream = '';					// @var noteStream string			"" Stream que contém as notas de células
	var $byteOrder = SPRSH_LITTLE_ENDIAN;	// @var byteOrder int				"SPRSH_LITTLE_ENDIAN" Formato de ponto flutuante da plataforma utilizada
	var $minRowDimension;					// @var minRowDimension int			Menor linha nas dimensões da planilha
	var $maxRowDimension;					// @var maxRowDimension int			Última linha da planilha
	var $minColDimension;					// @var minColDimension int			Primeira coluna da planilha
	var $maxColDimension;					// @var maxColDimension int			Maior índice de coluna da planilha
	var $protectBool = FALSE;				// @var protectBool bool			"FALSE" Indica se a planilha usa proteção de senha
	var $protectPasswd;						// @var	protectPasswd string		Senha de proteção da planilha contra escrita
	var $backup = FALSE;					// @var backup bool					"FALSE" Indica se deve ser feito backup da planilha quando aberta
	var $defaultColWidth = 8.43;			// @var defaultColWidth float		"8.43" Largura padrão das colunas
	var $defaultRowHeight = 12.75;			// @var defaultRowHeight float		"12.75" Altura padrão das linhas
	var $rowInfo = array();					// @var rowInfo array				"array()" Vetor contendo tamanhos customizados de linhas
	var $colInfo = array();					// @var colInfo array				"array()" Vetor contendo tamanhos customizados de colunas
	var $printHeaders = TRUE;				// @var printHeaders bool			"TRUE" Indica se devem ser impressos os cabeçalhos de linhas e colunas
	var $printGridLines = TRUE;				// @var printGridLines bool			"TRUE" Flag para impressão de linhas de grade
	var $leftMargin = 0.50;					// @var leftMargin float			"0.50" Margem esquerda
	var $rightMargin = 0.50;				// @var rightMargin float			"0.50" Margem direita
	var $topMargin = 0.50;					// @var topMargin float				"0.50" Margem superior
	var $bottomMargin = 0.50;				// @var bottomMargin float			"0.50" Margem inferior
	var $header = '';						// @var header string				"" Cabeçalho
	var $headerMargin = 0.50;				// @var headerMargin float			"0.50" Margem do cabeçalho
	var $footer = '';						// @var footer string				"" Rodapé
	var $footerMargin = 0.50;				// @var footerMargin float			"0.50" Margem do rodapé
	var $horizontalBreaks = array();		// @var horizontalBreaks array		"array()" Vetor de quebras de página horizontal
	var $verticalBreaks = array();			// @var verticalBreaks array		"array()" Vetor de quebras de página vertical
	var $selected = FALSE;					// @var selected bool				"FALSE" Indica se há seleção na planilha
	var $selection = array(0, 0, 0, 0);		// @var selection array				"array(0, 0, 0, 0)" Vetor de seleção da planilha: primeira e segunda coordenadas
	var $freeze = TRUE;						// @var freeze bool               	"TRUE" Indica se há área fixa
	var $panes = array(0, 0, 0, 0);			// @var panes array					"array(0, 0)" Área fixa de visualização
	var $activePane;						// @var activePane int				Área fixa ativa
	var $pictures = array (	'', '', '', '',	// @var pictures array				Vetor de formatos de conteúdo de célula, contendo
							'', '', '', '',	// 									21 formatos pré-definidos
							'', '', '', '',
							'', '', '', '',
							'', '', '', '',
							'' );
	var $fonts;								// @var fonts array					Vetor de fontes da planilha
	var $fontCount = 0;						// @var fontCount int				"0" Número de fontes inseridas
	var $cellFormats;						// @var cellFormats array			Vetor de conjuntos de atributos de célula
	var $cellFormatCount = 0;				// @var cellFormatCount int			"0" Número de formatos definidos na classe
	var $throwErrors = TRUE;				// @var throwErrors bool			"TRUE" Indica se os erros ocorridos devem ser reportados

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::Spreadsheet
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Spreadsheet() {
		parent::PHP2Go();
		// Inicializa as dimensões
		$this->minRowDimension = SPRSH_MAX_ROWS + 1;
		$this->maxRowDimension = 0;
		$this->minColDimension = SPRSH_MAX_COLS + 1;
		$this->maxColDimension = 0;
		// Adiciona o formato padrão de célula
		$this->addCellFormat();
		// Busca o formato de ponto flutuante
		$this->_getByteOrder();
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::addPictureString
	// @desc		Adiciona um formato de célula (Ex: 9999.999)
	// @access		public
	// @param		picString string		Formato de célula
	// @return		int Índice criado para este formato
	// @see			Spreadsheet::addFont
	// @see			Spreadsheet::addCellFormat
	//!-----------------------------------------------------------------
	function addPictureString($picString) {
		$this->pictures[] = $picString;
		return sizeof($this->pictures) - 1;
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::addFont
	// @desc		Cria uma nova fonte na planilha, a partir de seus atributos
	// @access		public
	// @param		properties array		Vetor de propriedades da fonte
	// @return		int Índice da fonte criada
	// @note		Ao adicionar uma fonte à planilha, o índice retornado
	//				pode ser utilizado nos métodos writeXXX() para indicar
	//				o índice da fonte que se deseja utilizar
	// @see			Spreadsheet::addPictureString
	// @see			Spreadsheet::addCellFormat
	//!-----------------------------------------------------------------
	function addFont($properties) {
		if ($this->fontCount < 4 && isset($properties['name'])) {
			$aCount = $this->fontCount;
			eval("\$properties[font_index] = SPRSH_FONT_$aCount;");
			$this->fonts[] = $properties;
			$this->fontCount++;
			return $this->fontCount - 1;
		} else {
           	return 0;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::addCellFormat
	// @desc		Cria um novo formato de célula na planilha, para ser
	//				utilizado nos métodos de inserção de dados
	// @access		public
	// @param		properties array		"array()" Vetor de propriedades do formato
	// @return		int Índice do formato criado
	// @note		O índice retornado por este método pode ser utilizado
	//				diretamente no parâmetro cellFormat das funções de inserção
	//				de dados writeXXX()
	// @see			Spreadsheet::addPictureString
	// @see			Spreadsheet::addFont
	//!-----------------------------------------------------------------
	function addCellFormat($properties=array()) {
		if (TypeUtils::isArray($properties)) {
			// Busca o somatório do formato da célula
			$properties['format'] = $this->_buildFormat($properties);
			// Constrói o status da célula
			$status = 0x0;
			if ($properties['locked']) $status += 0x40;
			if ($properties['hidden']) $status += 0x80;
			$properties['status'] = $status;
            $this->cellFormats[] = $properties;
			$this->cellFormatCount++;
			return $this->cellFormatCount - 1;
		} else {
           	return 0;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::protectSheet
	// @desc		Habilita proteção com senha na planilha
	// @access		public
	// @param		password string	Senha 'plain text' de proteção
	// @return		void
	//!-----------------------------------------------------------------
	function protectSheet($password) {
        $this->protectBool = TRUE;
		$this->protectPasswd = $this->_encodeSheetPasswd($password);
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setBackup
	// @desc		Habilita a opção de backup na planilha criada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setBackup() {
		$this->backup = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setRowHeight
	// @desc		Configura a altura de uma ou mais linhas da planilha
	// @access		public
	// @param		height float	Valor para a altura, em pixels
	// @param		rowStart int	Primeira linha
	// @param		rowEnd int	"NULL" Última linha
	// @return		void
	// @note		O método pode ser executado sem o terceiro parâmetro,
	//				permitindo que seja configurada a altura apenas para
	//				uma linha
	//!-----------------------------------------------------------------
	function setRowHeight($height, $rowStart, $rowEnd = NULL) {
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setColWidth
	// @desc		Configura a largura de uma ou mais colunas da planilha
	// @access		public
	// @param		width int		Valor para a largura, em caracteres
	// @param		colStart int	Primeira coluna
	// @param		colEnd int	"NULL" Última coluna
	// @return		void
	// @note		O método pode ser executado sem o terceiro parâmetro,
	//				permitindo que seja configurada a largura apenas para
	//				uma coluna
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setPrintGridlines
	// @desc		Habilita ou desabilita a impressão de linhas de grade
	// @access		public
	// @param		flag bool			Habilitação ou desabilitação
	// @return		void
	// @see			Spreadsheet::setPrintHeaders
	//!-----------------------------------------------------------------
	function setPrintGridlines($flag) {
    	$this->printGridlines = TypeUtils::toBoolean($flag);
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setPrintHeaders
	// @desc		Habilita ou desabilita a impressão de cabeçalhos de linhas e colunas
	// @access		public
	// @param		flag bool			Habilitação ou desabilitação
	// @return		void
	// @see			Spreadsheet::setPrintGridlines
	//!-----------------------------------------------------------------
	function setPrintHeaders($flag) {
    	$this->printHeaders = TypeUtils::toBoolean($flag);
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setHeader
	// @desc		Configura o valor e a margem do cabeçalho da planilha
	// @access		public
	// @param		header string		Valor para o cabeçalho
	// @param		margin float		"0.50" Margem do cabeçalho
	// @return		void
	// @see			Spreadsheet::setFooter
	//!-----------------------------------------------------------------
	function setHeader($header, $margin = 0.50) {
		$this->header = $header;
		$this->headerMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setFooter
	// @desc		Configura o valor e a margem do rodapé da planilha
	// @access		public
	// @param		footer string		Valor para o rodapé da planilha
	// @param		margin float		"0.50" Margem para o rodapé
	// @return		void
	// @see			Spreadsheet::setHeader
	//!-----------------------------------------------------------------
	function setFooter($footer, $margin = 0.50) {
    	$this->footer = $footer;
		$this->footerMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setMargin
	// @desc		Configura as 4 margens do corpo da planilha para o mesmo valor
	// @access		public
	// @param		margin float		Valor para as margens
	// @return		void
	// @note		Todos os parâmetros de margem são medidos em polegadas
	//!-----------------------------------------------------------------
	function setMargin($margin) {
    	$this->setLeftMargin($margin);
		$this->setRightMargin($margin);
		$this->setTopMargin($margin);
		$this->setBottomMargin($margin);
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setLeftMargin
	// @desc		Configura a margem esquerda da planilha
	// @access		public
	// @param		margin float		Valor para a margem esquerda
	// @return		void
	// @see			Spreadsheet::setMargin
	//!-----------------------------------------------------------------
	function setLeftMargin($margin) {
		$this->leftMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setRightMargin
	// @desc		Configura a margem direita da planilha
	// @access		public
	// @param		margin float		Valor para a margem direita
	// @return		void
	// @see			Spreadsheet::setMargin
	//!-----------------------------------------------------------------
	function setRightMargin($margin) {
		$this->rightMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setTopMargin
	// @desc		Configura a margem superior da planilha
	// @access		public
	// @param		margin float		Valor para a margem superior
	// @return		void
	// @see			Spreadsheet::setMargin
	//!-----------------------------------------------------------------
	function setTopMargin($margin) {
		$this->topMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setBottomMargin
	// @desc		Configura a margem inferior da planilha
	// @access		public
	// @param		margin float		Valor para a margem inferior
	// @return		void
	// @see			Spreadsheet::setMargin
	//!-----------------------------------------------------------------
	function setBottomMargin($margin) {
		$this->bottomMargin = TypeUtils::parseFloatPositive(str_replace(',', '.', $margin));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::addHorizontalBreak
	// @desc		Adiciona uma quebra de página horizontal
	// @access		public
	// @param		rowNum int		Número da linha para a quebra
	// @return		void
	// @see			Spreadsheet::addVerticalBreak
	//!-----------------------------------------------------------------
	function addHorizontalBreak($row) {
		if (!in_array($row, $this->horizontalBreaks)) {
        	$this->horizontalBreaks[] = $row;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::addVerticalBreak
	// @desc		Adiciona uma quebra de página vertical
	// @access		public
	// @param		rowNum int		Número da linha para a quebra
	// @return		void
	// @see			Spreadsheet::addHorizontalBreak
	//!-----------------------------------------------------------------
	function addVerticalBreak($col) {
		if (!in_array($col, $this->verticalBreaks)) {
        	$this->verticalBreaks[] = $col;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::setSelection
	// @desc		Armazena quais células deverão ser selecionadas na planilha construída
	// @access		public
	// @param		firstRow int		Primeira linha
	// @param		firstCol int		Primeira coluna
	// @param		lastRow int		Última linha
	// @param		lastCol int		Última coluna
	// @return		void
	//!-----------------------------------------------------------------
	function setSelection($firstRow, $firstCol, $lastRow, $lastCol) {
		$this->selected = TRUE;
		$frow = ($firstRow > $lastRow) ? $lastRow : $firstRow;
		$lrow = $lastRow;
		$fcol = ($firstCol > $lastCol) ? $lastCol : $firstCol;
		$lcol = $lastCol;
		$this->selection = array($frow, $fcol, $lrow, $lcol);
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::freezePanes
	// @desc		Cria uma área fixa na planilha
	// @access		public
	// @param		width int			Largura, em número de colunas
	// @param		height int			Altura, em número linhas
	// @param		leftCol int			"NULL" Primeira coluna visível à direita da área fixa
	// @param		topRow int			"NULL" Primeira linha superior vísivel
	// @return		void
	//!-----------------------------------------------------------------
	function freezePanes($width, $height, $leftCol=NULL, $topRow=NULL) {
		if (!TypeUtils::isInteger($width) || !TypeUtils::isInteger($height) || $height > 0 || $width > 0) {
			$this->freeze = TRUE;
			$this->panes[0] = $height;
			$this->panes[1] = $width;
			$this->panes[2] = TypeUtils::ifNull($topRow, $height);
			$this->panes[3] = TypeUtils::ifNull($leftCol, $width);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::writeData
	// @desc		Função principal de inserção de conteúdo na planilha.
	//				Identifica a categoria da informação e executa um método
	//				específico
	// @access		public
	// @param		row int			Índice da linha, baseado em zero
	// @param		col int			Índice da coluna, baseado em zero
	// @param		value mixed		Valor a ser inserido na célula
	// @param		columnWidth int	"0" Largura para a célula, em caracteres
	// @param		picture int		"0" Índice do formato de conteúdo
	// @param		font int			"0" Índice da fonte utilizada
	// @param		format int		"0" Índice do formato de célula
	// @return		bool
	//!-----------------------------------------------------------------
	function writeData($row, $col, $value, $columnWidth=0, $picture=0, $font=0, $format=0) {
		// Números
		if ( preg_match("/^=?[+-]?(\d|\.\d)?\d*(\.\d+)?([eE][+-]?(\d|\.\d)?\d*(\.\d+)?)?$/", ereg_replace(',', '.', $value)) ) {
			return $this->writeNumber($row, $col, $value, $columnWidth, $picture, $font, $format);
		}
        // Datas
		else if ( preg_match("/^(\d{2}[\/-]\d{2}[\/-]\d{4}|\d{4}[\/-]\d{2}[\/-]\d{2}).*$/", ereg_replace("\/", "-", $value) ) ) {
			return $this->writeDateTime($row, $col, $value, $columnWidth, $picture, $font, $format);
		}
		// URL
		else if ( preg_match("/^(f|ht)tps?:\/\/.+$/", $value) ) {
        	//$this->writeUrl($row, $col, $value, $columnWidth, $picture, $font, $format);
        	return FALSE;
		}
		// E-mail
		else if ( preg_match("/^mailto:.+$/", $value) ) {
        	//$this->writeUrl($row, $col, $value, $columnWidth, $picture, $font, $format);
        	return FALSE;
		}
		// Célula em branco
		else if ( StringUtils::allTrim($value) == '' ) {
        	//$this->writeBlank($row, $col, $columnWidth, $format);
        	return FALSE;
		}
		// Outros valores
		else {
        	return $this->writeString($row, $col, $value, $columnWidth, $picture, $font, $format);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::writeNumber
	// @desc		Insere um valor numérico inteiro ou decimal em uma
	//				determinada célula da planilha
	// @access		public
	// @param		row int			Índice da linha
	// @param		col int			Índice da coluna
	// @param		value mixed		Valor inteiro ou decimal
	// @param		columnWidth int	"0" Largura para a célula, em caracteres
	// @param		picture int		"0" Índice do formato de conteúdo
	// @param		font int			"0" Índice da fonte utilizada
	// @param		format int		"0" Índice do formato de célula
	// @return		bool
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::writeDateTime
	// @desc		Insere um valor de data e/ou hora em uma determinada
	//				célula da planilha
	// @access		public
	// @param		row int			Índice da linha
	// @param		col int			Índice da coluna
	// @param		value string		Data
	// @param		columnWidth int	"0" Largura para a célula, em caracteres
	// @param		picture int		"0" Índice do formato de conteúdo
	// @param		font int			"0" Índice da fonte utilizada
	// @param		format int		"0" Índice do formato de célula
	// @return		bool
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::writeString
	// @desc		Insere um valor de texto em uma determinada célula
	//				da planilha
	// @access		public
	// @param		row int			Índice da linha
	// @param		col int			Índice da coluna
	// @param		value string		Texto a ser inserido
	// @param		columnWidth int	"0" Largura para a célula, em caracteres
	// @param		picture int		"0" Índice do formato de conteúdo
	// @param		font int			"0" Índice da fonte utilizada
	// @param		format int		"0" Índice do formato de célula
	// @return		bool
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::writeBlank
	// @desc		Insere uma célula sem conteúdo na planilha
	// @access		public
	// @param		row int			Índice da linha
	// @param		col int			Índice da coluna
	// @param		columnWidth int	"0" Largura para a célula, em caracteres
	// @param		format int		"0" Índice do formato de célula
	// @return		bool
	//!-----------------------------------------------------------------
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
	
	//!-----------------------------------------------------------------
	// @function	Spreadsheet::writeArray
	// @desc		Adiciona um array de dados em uma determinada linha da planilha
	// @param		row int			Índice da linha
	// @param		array array		Array de dados
	// @param		columnWidth int	"0" Largura para as células, em caracteres
	// @param		font int		"0" Índice da fonte
	// @param		format int		"0" Índice do formato de célula
	// @param		startCol int	"0" Coluna inicial
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::addCellNote
	// @desc		Adiciona uma nota a uma determinada célula
	// @param		row int			Índice da linha
	// @param		col int			Índice da coluna
	// @param		value string	Nota para a célula
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::download
	// @desc		Retorna o conteúdo da planilha para o usuário, gerando
	//				antes os headers específicos
	// @param		fileName string		Nome do arquivo para download
	// @param		mimeType string		"" Permite sobrescrever o mime-type default
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function download($fileName, $mimeType='') {
		if (!headers_sent()) {
			$this->_prepareSpreadsheet();
			HttpResponse::download($fileName, $this->streamSize, $mimeType);
			print $this->stream;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::toFile
	// @desc		Prepara e grava em arquivo o conteúdo da planilha
	// @param		fileName string		Nome do arquivo
	// @param		fileMode int		Modo para o arquivo criado
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function toFile($fileName, $fileMode=NULL) {
		$this->_prepareSpreadsheet();
		$Mgr = new FileManager();
		$Mgr->throwErrors = FALSE;
		if ($Mgr->open($fileName, FILE_MANAGER_WRITE_BINARY)) {
        	$Mgr->write($this->stream);
			if (!TypeUtils::isNull($fileMode))
				$Mgr->changeMode($fileMode);
			$Mgr->close();
			return TRUE;
		} else {
        	if ($this->throwErrors) {
            	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
			}
			return FALSE;
		}
	}

	//------------------------------------------------------------------
	// MÉTODOS INTERNOS
	//------------------------------------------------------------------

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_prepareSpreadsheet
	// @desc		Executa as funções de gravação dos registros BIFF e
	//				dos dados contidos na planilha
	// @access		private
	// @return		void
	// @note		Este método é executado a partir de download() e toFile()
	//!-----------------------------------------------------------------
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
		// ----------------- dados
		$this->stream .= $this->dataStream;
		$this->streamSize += strlen($this->dataStream);
		// ----------------- dados
		$this->_writeProtect();
		$this->_appendStream($this->noteStream);
		$this->_writeWindow1();
		$this->_writeWindow2();
		$this->_writePanes();
		$this->_writeSelection();
		$this->_writeEndOfFile();
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_appendStream
	// @desc		Concatena um registro no stream binário da planilha
	// @access		private
	// @param		value string		Valor do registro
	// @return		void
	// @note		Se o tamanho do valor for superior a 2080 bytes,
	//				será executado o método _writeContinue() para gerar
	//				blocos encadeados
	//!-----------------------------------------------------------------
	function _appendStream($value) {
		if (strlen($value) > SPRSH_RECORD_LIMIT) {
 			$continueData = $this->_writeContinue($value);
        	$this->stream .= $continueData;
			$this->streamSize += strlen($continueData);
		} else {
        	$this->stream .= $value;
			$this->streamSize += strlen($value);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeContinue
	// @desc		Cria uma seqüência de blocos de 2080 bytes para permitir
	//				a inserção de registros maiores do que o limite imposto
	//				pela versão 2.1 do formato BIFF
	// @access		private
	// @param		data string		Valor a ser processado
	// @return		string Valor reconstruído em blocos contínuos separados por
	//				um cabeçalho contendo o id CONTINUE
	//!-----------------------------------------------------------------
	function _writeContinue($data) {
        $limit      = 2080;
        $record     = 0x003C;         // Record identifier
		// Mantém os primeiros 2080 bytes intactos
        $result = substr($data, 0, 2) . pack("v", $limit - 4) . substr($data, 4, $limit - 4);
        // Insere os próximos bytes de N em N, onde N tem tamanho 2080
        for($i = 2080; $i < strlen($data) - $limit; $i += $limit) {
            $result .= pack("vv", $record, $limit);
            $result .= substr($data, $i, $limit - 4);
        }
		// Insere o conteúdo restante
        $result .= pack("vv", $record, strlen($data) - $i);
        $result .= substr($data, $i, strlen($data) - $i);
		// Retorna o resultado final
        return $result;
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_getByteOrder
	// @desc		Verifica o formato de ponto flutuante da plataforma
	//				do usuário (big endian ou little endian)
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_buildFormat
	// @desc		Constrói o número composto pelas configurações de um formato de célula
	// @access		private
	// @param		properties array		Vetor de propriedades do formato
	// @return		int Número composto pela soma das configurações do formato
	// @note		Este método é executado em addCellFormat()
	// @see			Spreadsheet::addCellFormat
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_encodeSheetPasswd
	// @desc		Codifica a senha textual da planilha fornecida pelo usuário
	// @access		private
	// @param		password string	Senha 'plain text'
	// @return		string Senha codificada
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_checkBounds
	// @desc		Verifica se uma dupla linha,coluna está dentro dos limites
	//				aceitos pela planilha
	// @access		private
	// @param		row int		Índice da linha
	// @param		col int		Índice da coluna
	// @return		bool
	// @note        Atualiza as dimensões utilizadas, indicadas nas propriedades
	//				minRowDimension, maxRowDimension, minColDimension e maxColDimension
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_adjustColWidth
	// @desc		Recalcula o tamanho de uma coluna em caracteres a
	//				cada célula inserida
	// @access		private
	// @param		col int		Índice da coluna
	// @param		colWidth int	Tamanho desejado para a célula
	// @param		len int		Tamanho da string (define a largura mínima a ser utilizada)
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function    Spreadsheet::_transformDate
	// @desc		Converte um valor expresso em data ou data/hora para
	//				número de dias no calendário Juliano
	// @access		private
	// @param		date string		Data
	// @return		string Data convertida ou vazio caso a extensão calendar não esteja presente no sistema
	// @note		Este método é executado em writeDateTime()
	//!-----------------------------------------------------------------
	function _transformDate($date) {
		if (!System::loadExtension('calendar')) {
			return '';
		} else {
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
	}

	//------------------------------------------------------------------
	// MÉTODOS PARA GRAVAÇÃO DE REGISTROS BIFF
	//------------------------------------------------------------------

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeBeginOfFile
	// @desc		Grava o registro BOF no stream binário
	// @access		private
	// @return		void
	// @see			Spreadsheet::_writeEndOfFile
	//!-----------------------------------------------------------------
	function _writeBeginOfFile() {
		$id = 0x0009;
		$length = 0x0004;
		$version = 0x0007;
		$type = 0x0010;
		$this->_appendStream(pack('vvvv', $id, $length, $version, $type));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeCodePage
	// @desc		Grava o registro CODEPAGE no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writeCodePage() {
		$id = 0x0042;
		$length = 0x0002;
		$this->_appendStream(pack('vvv', $id, $length, 0x8001));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeBackup
	// @desc		Grava o registro BACKUP no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writeBackup() {
		$id = 0x0040;
		$length = 0x0002;
    	if ($this->backup) {
			$this->_appendStream(pack('vvv', $id, $length, 1));
		} else {
			$this->_appendStream(pack('vvv', $id, $length, 0));
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writePrintHeaders
	// @desc		Grava o registro PRINTROWHEADERS no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writePrintHeaders() {
		$id = 0x002A;
		$length = 0x0002;
		$this->_appendStream(pack('vvv', $id, $length, TypeUtils::parseInteger($this->printHeaders)));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writePrintGridLines
	// @desc		Grava o registro PRINTGRIDLINES no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writePrintGridLines() {
		$id = 0x002B;
		$length = 0x0002;
		$this->_appendStream(pack('vvv', $id, $length, TypeUtils::parseInteger($this->printGridLines)));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeBreaks
	// @desc		Grava os registros HBREAK e VBREAK no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeDefaultRowHeight
	// @desc		Grava o registro DEFAULTROWHEIGHT no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writeDefaultRowHeight() {
		$id = 0x0025;
		$length = 0x0002;
		$this->_appendStream(pack('vvv', $id, $length, $this->defaultRowHeight * 20));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeWsBool
	// @desc		Grava o registro WSBOOL no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writeWsBool() {
		$id = 0x0081;
		$length = 0x0002;
		$optionFlags = 0x04C1;
		$this->_appendStream(pack('vvv', $id, $length, $optionFlags));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeFonts
	// @desc		Grava as fontes declaradas para a planilha
	// @access		private
	// @return		void
	// @see			Spreadsheet::_getFont
	//!-----------------------------------------------------------------
	function _writeFonts() {
		for ($i=0; $i<$this->fontCount; $i++) {
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeHeader
	// @desc		Grava o registro HEADER no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writeHeader() {
		$id = 0x0014;
		$length = 1;
		$len = strlen($this->header);
		$this->_appendStream(pack('vvC', $id, $length + $len, $len) . $this->header);
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeFooter
	// @desc		Grava o registro FOOTER no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writeFooter() {
		$id = 0x0015;
		$length = 1;
		$len = strlen($this->footer);
		$this->_appendStream(pack('vvC', $id, $length + $len, $len) . $this->footer);
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeMargins
	// @desc		Grava os registros de margem no stream binário
	// @access		private
	// @return		void
	// @note		Os registros são LEFTMARGIN, RIGHTMARGIN, TOPMARGIN e BOTTOMMARGIN
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeColInfo
	// @desc		Grava as larguras de coluna no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writeColInfo() {
		$id = 0x007D;
		$length = 0x000B;
		foreach($this->colInfo as $col => $width) {
			$this->_appendStream(pack('vvvvvvvC', $id, $length, $col, $col, ($width + 0.72) * 256, 0x0F, 0, 0x00));
		}
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writePictures
	// @desc		Grava os formatos de célula no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeRowInfo
	// @desc		Grava os tamanhos de linha definidos pelo usuário no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeProtect
	// @desc		Grava a indicação de planilha protegida por senha
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeWindow1
	// @desc		Grava o registro WINDOW1 no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeWindow2
	// @desc		Grava o registro WINDOW2 no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeSelection
	// @desc		Grava o registro SELECTION no stream binário, que
	//				contém a área selecionada da planilha
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _writeSelection() {
		$id = 0x001D;
		$length = 0x000F;
		$this->_appendStream(pack('vvCvvvvvvCC', $id, $length, $this->activePane,
								$this->selection[0], $this->selection[1], 0, 1,
								$this->selection[0], $this->selection[2],
								$this->selection[1], $this->selection[3]));
	}

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writePanes
	// @desc		Grava o registro PANE no stream binário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	Spreadsheet::_writeEndOfFile
	// @desc		Grava o registro EOF no stream binário
	// @access		private
	// @return		void
	// @see			Spreadsheet::_writeBeginOfFile
	//!-----------------------------------------------------------------
	function _writeEndOfFile() {
		$id = 0x000A;
		$this->_appendStream(pack('v', $id));
	}
}
?>