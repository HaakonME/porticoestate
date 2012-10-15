<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2012 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package	PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	1.7.8, 2012-10-12
 */


/**
 * PHPExcel_Writer_HTML
 *
 * @category   PHPExcel
 * @package	PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_HTML implements PHPExcel_Writer_IWriter {
	/**
	 * PHPExcel object
	 *
	 * @var PHPExcel
	 */
	protected $_phpExcel;

	/**
	 * Sheet index to write
	 *
	 * @var int
	 */
	private $_sheetIndex	= 0;

	/**
	 * Pre-calculate formulas
	 *
	 * @var boolean
	 */
	private $_preCalculateFormulas = true;

	/**
	 * Images root
	 *
	 * @var string
	 */
	private $_imagesRoot	= '.';

	/**
	 * Use inline CSS?
	 *
	 * @var boolean
	 */
	private $_useInlineCss = false;

	/**
	 * Array of CSS styles
	 *
	 * @var array
	 */
	private $_cssStyles = null;

	/**
	 * Array of column widths in points
	 *
	 * @var array
	 */
	private $_columnWidths = null;

	/**
	 * Default font
	 *
	 * @var PHPExcel_Style_Font
	 */
	private $_defaultFont;

	/**
	 * Flag whether spans have been calculated
	 *
	 * @var boolean
	 */
	private $_spansAreCalculated	= false;

	/**
	 * Excel cells that should not be written as HTML cells
	 *
	 * @var array
	 */
	private $_isSpannedCell	= array();

	/**
	 * Excel cells that are upper-left corner in a cell merge
	 *
	 * @var array
	 */
	private $_isBaseCell	= array();

	/**
	 * Excel rows that should not be written as HTML rows
	 *
	 * @var array
	 */
	private $_isSpannedRow	= array();

	/**
	 * Is the current writer creating PDF?
	 *
	 * @var boolean
	 */
	protected $_isPdf = false;

	/**
	 * Generate the Navigation block
	 *
	 * @var boolean
	 */
	private $_generateSheetNavigationBlock = true;

	/**
	 * Create a new PHPExcel_Writer_HTML
	 *
	 * @param	PHPExcel	$phpExcel	PHPExcel object
	 */
	public function __construct(PHPExcel $phpExcel) {
		$this->_phpExcel = $phpExcel;
		$this->_defaultFont = $this->_phpExcel->getDefaultStyle()->getFont();
	}

	/**
	 * Save PHPExcel to file
	 *
	 * @param	string		$pFilename
	 * @throws	Exception
	 */
	public function save($pFilename = null) {
		// garbage collect
		$this->_phpExcel->garbageCollect();

		$saveDebugLog = PHPExcel_Calculation::getInstance()->writeDebugLog;
		PHPExcel_Calculation::getInstance()->writeDebugLog = false;
		$saveArrayReturnType = PHPExcel_Calculation::getArrayReturnType();
		PHPExcel_Calculation::setArrayReturnType(PHPExcel_Calculation::RETURN_ARRAY_AS_VALUE);

		// Build CSS
		$this->buildCSS(!$this->_useInlineCss);

		// Open file
		$fileHandle = fopen($pFilename, 'wb+');
		if ($fileHandle === false) {
			throw new Exception("Could not open file $pFilename for writing.");
		}

		// Write headers
		fwrite($fileHandle, $this->generateHTMLHeader(!$this->_useInlineCss));

		// Write navigation (tabs)
		if ((!$this->_isPdf) && ($this->_generateSheetNavigationBlock)) {
			fwrite($fileHandle, $this->generateNavigation());
		}

		// Write data
		fwrite($fileHandle, $this->generateSheetData());

		// Write footer
		fwrite($fileHandle, $this->generateHTMLFooter());

		// Close file
		fclose($fileHandle);

		PHPExcel_Calculation::setArrayReturnType($saveArrayReturnType);
		PHPExcel_Calculation::getInstance()->writeDebugLog = $saveDebugLog;
	}

	/**
	 * Map VAlign
	 *
	 * @param	string		$vAlign		Vertical alignment
	 * @return string
	 */
	private function _mapVAlign($vAlign) {
		switch ($vAlign) {
			case PHPExcel_Style_Alignment::VERTICAL_BOTTOM:		return 'bottom';
			case PHPExcel_Style_Alignment::VERTICAL_TOP:		return 'top';
			case PHPExcel_Style_Alignment::VERTICAL_CENTER:
			case PHPExcel_Style_Alignment::VERTICAL_JUSTIFY:	return 'middle';
			default: return 'baseline';
		}
	}

	/**
	 * Map HAlign
	 *
	 * @param	string		$hAlign		Horizontal alignment
	 * @return string|false
	 */
	private function _mapHAlign($hAlign) {
		switch ($hAlign) {
			case PHPExcel_Style_Alignment::HORIZONTAL_GENERAL:				return false;
			case PHPExcel_Style_Alignment::HORIZONTAL_LEFT:					return 'left';
			case PHPExcel_Style_Alignment::HORIZONTAL_RIGHT:				return 'right';
			case PHPExcel_Style_Alignment::HORIZONTAL_CENTER:
			case PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS:	return 'center';
			case PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY:				return 'justify';
			default: return false;
		}
	}

	/**
	 * Map border style
	 *
	 * @param	int		$borderStyle		Sheet index
	 * @return	string
	 */
	private function _mapBorderStyle($borderStyle) {
		switch ($borderStyle) {
			case PHPExcel_Style_Border::BORDER_NONE:				return 'none';
			case PHPExcel_Style_Border::BORDER_DASHDOT:				return '1px dashed';
			case PHPExcel_Style_Border::BORDER_DASHDOTDOT:			return '1px dotted';
			case PHPExcel_Style_Border::BORDER_DASHED:				return '1px dashed';
			case PHPExcel_Style_Border::BORDER_DOTTED:				return '1px dotted';
			case PHPExcel_Style_Border::BORDER_DOUBLE:				return '3px double';
			case PHPExcel_Style_Border::BORDER_HAIR:				return '1px solid';
			case PHPExcel_Style_Border::BORDER_MEDIUM:				return '2px solid';
			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT:		return '2px dashed';
			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT:	return '2px dotted';
			case PHPExcel_Style_Border::BORDER_MEDIUMDASHED:		return '2px dashed';
			case PHPExcel_Style_Border::BORDER_SLANTDASHDOT:		return '2px dashed';
			case PHPExcel_Style_Border::BORDER_THICK:				return '3px solid';
			case PHPExcel_Style_Border::BORDER_THIN:				return '1px solid';
			default: return '1px solid'; // map others to thin
		}
	}

	/**
	 * Get sheet index
	 *
	 * @return int
	 */
	public function getSheetIndex() {
		return $this->_sheetIndex;
	}

	/**
	 * Set sheet index
	 *
	 * @param	int		$pValue		Sheet index
	 * @return PHPExcel_Writer_HTML
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}

	/**
	 * Get sheet index
	 *
	 * @return boolean
	 */
	public function getGenerateSheetNavigationBlock() {
		return $this->_generateSheetNavigationBlock;
	}

	/**
	 * Set sheet index
	 *
	 * @param	boolean		$pValue		Flag indicating whether the sheet navigation block should be generated or not
	 * @return PHPExcel_Writer_HTML
	 */
	public function setGenerateSheetNavigationBlock($pValue = true) {
		$this->_generateSheetNavigationBlock = (bool) $pValue;
		return $this;
	}

	/**
	 * Write all sheets (resets sheetIndex to NULL)
	 */
	public function writeAllSheets() {
		$this->_sheetIndex = null;
		return $this;
	}

	/**
	 * Generate HTML header
	 *
	 * @param	boolean		$pIncludeStyles		Include styles?
	 * @return	string
	 * @throws Exception
	 */
	public function generateHTMLHeader($pIncludeStyles = false) {
		// PHPExcel object known?
		if (is_null($this->_phpExcel)) {
			throw new Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		// Construct HTML
		$properties = $this->_phpExcel->getProperties();
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . PHP_EOL;
		$html .= '<!-- Generated by PHPExcel - http://www.phpexcel.net -->' . PHP_EOL;
		$html .= '<html>' . PHP_EOL;
		$html .= '  <head>' . PHP_EOL;
		$html .= '	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . PHP_EOL;
		if ($properties->getTitle() > '')
			$html .= '	  <title>' . htmlspecialchars($properties->getTitle()) . '</title>' . PHP_EOL;

		if ($properties->getCreator() > '')
			$html .= '	  <meta name="author" content="' . htmlspecialchars($properties->getCreator()) . '" />' . PHP_EOL;
		if ($properties->getTitle() > '')
			$html .= '	  <meta name="title" content="' . htmlspecialchars($properties->getTitle()) . '" />' . PHP_EOL;
		if ($properties->getDescription() > '')
			$html .= '	  <meta name="description" content="' . htmlspecialchars($properties->getDescription()) . '" />' . PHP_EOL;
		if ($properties->getSubject() > '')
			$html .= '	  <meta name="subject" content="' . htmlspecialchars($properties->getSubject()) . '" />' . PHP_EOL;
		if ($properties->getKeywords() > '')
			$html .= '	  <meta name="keywords" content="' . htmlspecialchars($properties->getKeywords()) . '" />' . PHP_EOL;
		if ($properties->getCategory() > '')
			$html .= '	  <meta name="category" content="' . htmlspecialchars($properties->getCategory()) . '" />' . PHP_EOL;
		if ($properties->getCompany() > '')
			$html .= '	  <meta name="company" content="' . htmlspecialchars($properties->getCompany()) . '" />' . PHP_EOL;
		if ($properties->getManager() > '')
			$html .= '	  <meta name="manager" content="' . htmlspecialchars($properties->getManager()) . '" />' . PHP_EOL;

		if ($pIncludeStyles) {
			$html .= $this->generateStyles(true);
		}

		$html .= '  </head>' . PHP_EOL;
		$html .= '' . PHP_EOL;
		$html .= '  <body>' . PHP_EOL;

		// Return
		return $html;
	}

	/**
	 * Generate sheet data
	 *
	 * @return	string
	 * @throws Exception
	 */
	public function generateSheetData() {
		// PHPExcel object known?
		if (is_null($this->_phpExcel)) {
			throw new Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		// Ensure that Spans have been calculated?
		if (!$this->_spansAreCalculated) {
			$this->_calculateSpans();
		}

		// Fetch sheets
		$sheets = array();
		if (is_null($this->_sheetIndex)) {
			$sheets = $this->_phpExcel->getAllSheets();
		} else {
			$sheets[] = $this->_phpExcel->getSheet($this->_sheetIndex);
		}

		// Construct HTML
		$html = '';

		// Loop all sheets
		$sheetId = 0;
		foreach ($sheets as $sheet) {
			// Write table header
			$html .= $this->_generateTableHeader($sheet);

			// Get worksheet dimension
			$dimension = explode(':', $sheet->calculateWorksheetDimension());
			$dimension[0] = PHPExcel_Cell::coordinateFromString($dimension[0]);
			$dimension[0][0] = PHPExcel_Cell::columnIndexFromString($dimension[0][0]) - 1;
			$dimension[1] = PHPExcel_Cell::coordinateFromString($dimension[1]);
			$dimension[1][0] = PHPExcel_Cell::columnIndexFromString($dimension[1][0]) - 1;

			// row min,max
			$rowMin = $dimension[0][1];
			$rowMax = $dimension[1][1];

			// calculate start of <tbody>, <thead>
			$tbodyStart = $rowMin;
			$tbodyEnd   = $rowMax;
			$theadStart = $theadEnd   = 0; // default: no <thead>	no </thead>
			if ($sheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
				$rowsToRepeatAtTop = $sheet->getPageSetup()->getRowsToRepeatAtTop();

				// we can only support repeating rows that start at top row
				if ($rowsToRepeatAtTop[0] == 1) {
					$theadStart = $rowsToRepeatAtTop[0];
					$theadEnd   = $rowsToRepeatAtTop[1];
					$tbodyStart = $rowsToRepeatAtTop[1] + 1;
				}
			}

			// Loop through cells
			$row = $rowMin-1;
			while($row++ < $rowMax) {
				// <thead> ?
				if ($row == $theadStart) {
					$html .= '		<thead>' . PHP_EOL;
				}

				// <tbody> ?
				if ($row == $tbodyStart) {
					$html .= '		<tbody>' . PHP_EOL;
				}

				// Write row if there are HTML table cells in it
				if ( !isset($this->_isSpannedRow[$sheet->getParent()->getIndex($sheet)][$row]) ) {
					// Start a new rowData
					$rowData = array();
					// Loop through columns
					$column = $dimension[0][0] - 1;
					while($column++ < $dimension[1][0]) {
						// Cell exists?
						if ($sheet->cellExistsByColumnAndRow($column, $row)) {
							$rowData[$column] = $sheet->getCellByColumnAndRow($column, $row);
						} else {
							$rowData[$column] = '';
						}
					}
					$html .= $this->_generateRow($sheet, $rowData, $row - 1);
				}

				// </thead> ?
				if ($row == $theadEnd) {
					$html .= '		</thead>' . PHP_EOL;
				}

				// </tbody> ?
				if ($row == $tbodyEnd) {
					$html .= '		</tbody>' . PHP_EOL;
				}
			}

			// Write table footer
			$html .= $this->_generateTableFooter();

			// Writing PDF?
			if ($this->_isPdf) {
				if (is_null($this->_sheetIndex) && $sheetId + 1 < $this->_phpExcel->getSheetCount()) {
					$html .= '<div style="page-break-before:always" />';
				}
			}

			// Next sheet
			++$sheetId;
		}

		// Return
		return $html;
	}

	/**
	 * Generate sheet tabs
	 *
	 * @return	string
	 * @throws Exception
	 */
	public function generateNavigation()
	{
		// PHPExcel object known?
		if (is_null($this->_phpExcel)) {
			throw new Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		// Fetch sheets
		$sheets = array();
		if (is_null($this->_sheetIndex)) {
			$sheets = $this->_phpExcel->getAllSheets();
		} else {
			$sheets[] = $this->_phpExcel->getSheet($this->_sheetIndex);
		}

		// Construct HTML
		$html = '';

		// Only if there are more than 1 sheets
		if (count($sheets) > 1) {
			// Loop all sheets
			$sheetId = 0;

			$html .= '<ul class="navigation">' . PHP_EOL;

			foreach ($sheets as $sheet) {
				$html .= '  <li class="sheet' . $sheetId . '"><a href="#sheet' . $sheetId . '">' . $sheet->getTitle() . '</a></li>' . PHP_EOL;
				++$sheetId;
			}

			$html .= '</ul>' . PHP_EOL;
		}

		return $html;
	}

	/**
	 * Generate image tag in cell
	 *
	 * @param	PHPExcel_Worksheet	$pSheet			PHPExcel_Worksheet
	 * @param	string				$coordinates	Cell coordinates
	 * @return	string
	 * @throws	Exception
	 */
	private function _writeImageTagInCell(PHPExcel_Worksheet $pSheet, $coordinates) {
		// Construct HTML
		$html = '';

		// Write images
		foreach ($pSheet->getDrawingCollection() as $drawing) {
			if ($drawing instanceof PHPExcel_Worksheet_Drawing) {
				if ($drawing->getCoordinates() == $coordinates) {
					$filename = $drawing->getPath();

					// Strip off eventual '.'
					if (substr($filename, 0, 1) == '.') {
						$filename = substr($filename, 1);
					}

					// Prepend images root
					$filename = $this->getImagesRoot() . $filename;

					// Strip off eventual '.'
					if (substr($filename, 0, 1) == '.' && substr($filename, 0, 2) != './') {
						$filename = substr($filename, 1);
					}

					// Convert UTF8 data to PCDATA
					$filename = htmlspecialchars($filename);

					$html .= PHP_EOL;
					$html .= '		<img style="position: relative; left: ' . $drawing->getOffsetX() . 'px; top: ' . $drawing->getOffsetY() . 'px; width: ' . $drawing->getWidth() . 'px; height: ' . $drawing->getHeight() . 'px;" src="' . $filename . '" border="0" width="' . $drawing->getWidth() . '" height="' . $drawing->getHeight() . '" />' . PHP_EOL;
				}
			}
		}

		// Return
		return $html;
	}

	/**
	 * Generate CSS styles
	 *
	 * @param	boolean	$generateSurroundingHTML	Generate surrounding HTML tags? (<style> and </style>)
	 * @return	string
	 * @throws	Exception
	 */
	public function generateStyles($generateSurroundingHTML = true) {
		// PHPExcel object known?
		if (is_null($this->_phpExcel)) {
			throw new Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		// Build CSS
		$css = $this->buildCSS($generateSurroundingHTML);

		// Construct HTML
		$html = '';

		// Start styles
		if ($generateSurroundingHTML) {
			$html .= '	<style type="text/css">' . PHP_EOL;
			$html .= '	  html { ' . $this->_assembleCSS($css['html']) . ' }' . PHP_EOL;
		}

		// Write all other styles
		foreach ($css as $styleName => $styleDefinition) {
			if ($styleName != 'html') {
				$html .= '	  ' . $styleName . ' { ' . $this->_assembleCSS($styleDefinition) . ' }' . PHP_EOL;
			}
		}

		// End styles
		if ($generateSurroundingHTML) {
			$html .= '	</style>' . PHP_EOL;
		}

		// Return
		return $html;
	}

	/**
	 * Build CSS styles
	 *
	 * @param	boolean	$generateSurroundingHTML	Generate surrounding HTML style? (html { })
	 * @return	array
	 * @throws	Exception
	 */
	public function buildCSS($generateSurroundingHTML = true) {
		// PHPExcel object known?
		if (is_null($this->_phpExcel)) {
			throw new Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		// Cached?
		if (!is_null($this->_cssStyles)) {
			return $this->_cssStyles;
		}

		// Ensure that spans have been calculated
		if (!$this->_spansAreCalculated) {
			$this->_calculateSpans();
		}

		// Construct CSS
		$css = array();

		// Start styles
		if ($generateSurroundingHTML) {
			// html { }
			$css['html']['font-family']	  = 'Calibri, Arial, Helvetica, sans-serif';
			$css['html']['font-size']		= '11pt';
			$css['html']['background-color'] = 'white';
		}


		// table { }
		$css['table']['border-collapse']  = 'collapse';
	    if (!$this->_isPdf) {
			$css['table']['page-break-after'] = 'always';
		}

		// .gridlines td { }
		$css['.gridlines td']['border'] = '1px dotted black';

		// .b {}
		$css['.b']['text-align'] = 'center'; // BOOL

		// .e {}
		$css['.e']['text-align'] = 'center'; // ERROR

		// .f {}
		$css['.f']['text-align'] = 'right'; // FORMULA

		// .inlineStr {}
		$css['.inlineStr']['text-align'] = 'left'; // INLINE

		// .n {}
		$css['.n']['text-align'] = 'right'; // NUMERIC

		// .s {}
		$css['.s']['text-align'] = 'left'; // STRING

		// Calculate cell style hashes
		foreach ($this->_phpExcel->getCellXfCollection() as $index => $style) {
			$css['td.style' . $index] = $this->_createCSSStyle( $style );
		}

		// Fetch sheets
		$sheets = array();
		if (is_null($this->_sheetIndex)) {
			$sheets = $this->_phpExcel->getAllSheets();
		} else {
			$sheets[] = $this->_phpExcel->getSheet($this->_sheetIndex);
		}

		// Build styles per sheet
		foreach ($sheets as $sheet) {
			// Calculate hash code
			$sheetIndex = $sheet->getParent()->getIndex($sheet);

			// Build styles
			// Calculate column widths
			$sheet->calculateColumnWidths();

			// col elements, initialize
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn()) - 1;
			$column = -1;
			while($column++ < $highestColumnIndex) {
				$this->_columnWidths[$sheetIndex][$column] = 42; // approximation
				$css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = '42pt';
			}

			// col elements, loop through columnDimensions and set width
			foreach ($sheet->getColumnDimensions() as $columnDimension) {
				if (($width = PHPExcel_Shared_Drawing::cellDimensionToPixels($columnDimension->getWidth(), $this->_defaultFont)) >= 0) {
					$width = PHPExcel_Shared_Drawing::pixelsToPoints($width);
					$column = PHPExcel_Cell::columnIndexFromString($columnDimension->getColumnIndex()) - 1;
					$this->_columnWidths[$sheetIndex][$column] = $width;
					$css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = $width . 'pt';

					if ($columnDimension->getVisible() === false) {
						$css['table.sheet' . $sheetIndex . ' col.col' . $column]['visibility'] = 'collapse';
						$css['table.sheet' . $sheetIndex . ' col.col' . $column]['*display'] = 'none'; // target IE6+7
					}
				}
			}

			// Default row height
			$rowDimension = $sheet->getDefaultRowDimension();

			// table.sheetN tr { }
			$css['table.sheet' . $sheetIndex . ' tr'] = array();

			if ($rowDimension->getRowHeight() == -1) {
				$pt_height = PHPExcel_Shared_Font::getDefaultRowHeightByFont($this->_phpExcel->getDefaultStyle()->getFont());
			} else {
				$pt_height = $rowDimension->getRowHeight();
			}
			$css['table.sheet' . $sheetIndex . ' tr']['height'] = $pt_height . 'pt';
			if ($rowDimension->getVisible() === false) {
				$css['table.sheet' . $sheetIndex . ' tr']['display']	= 'none';
				$css['table.sheet' . $sheetIndex . ' tr']['visibility'] = 'hidden';
			}

			// Calculate row heights
			foreach ($sheet->getRowDimensions() as $rowDimension) {
				$row = $rowDimension->getRowIndex() - 1;

				// table.sheetN tr.rowYYYYYY { }
				$css['table.sheet' . $sheetIndex . ' tr.row' . $row] = array();

				if ($rowDimension->getRowHeight() == -1) {
					$pt_height = PHPExcel_Shared_Font::getDefaultRowHeightByFont($this->_phpExcel->getDefaultStyle()->getFont());
				} else {
					$pt_height = $rowDimension->getRowHeight();
				}
				$css['table.sheet' . $sheetIndex . ' tr.row' . $row]['height'] = $pt_height . 'pt';
				if ($rowDimension->getVisible() === false) {
					$css['table.sheet' . $sheetIndex . ' tr.row' . $row]['display'] = 'none';
					$css['table.sheet' . $sheetIndex . ' tr.row' . $row]['visibility'] = 'hidden';
				}
			}
		}

		// Cache
		if (is_null($this->_cssStyles)) {
			$this->_cssStyles = $css;
		}

		// Return
		return $css;
	}

	/**
	 * Create CSS style
	 *
	 * @param	PHPExcel_Style		$pStyle			PHPExcel_Style
	 * @return	array
	 */
	private function _createCSSStyle(PHPExcel_Style $pStyle) {
		// Construct CSS
		$css = '';

		// Create CSS
		$css = array_merge(
			$this->_createCSSStyleAlignment($pStyle->getAlignment())
			, $this->_createCSSStyleBorders($pStyle->getBorders())
			, $this->_createCSSStyleFont($pStyle->getFont())
			, $this->_createCSSStyleFill($pStyle->getFill())
		);

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Alignment)
	 *
	 * @param	PHPExcel_Style_Alignment		$pStyle			PHPExcel_Style_Alignment
	 * @return	array
	 */
	private function _createCSSStyleAlignment(PHPExcel_Style_Alignment $pStyle) {
		// Construct CSS
		$css = array();

		// Create CSS
		$css['vertical-align'] = $this->_mapVAlign($pStyle->getVertical());
		if ($textAlign = $this->_mapHAlign($pStyle->getHorizontal())) {
			$css['text-align'] = $textAlign;
			if(in_array($textAlign,array('left','right')))
				$css['padding-'.$textAlign] = (string)((int)$pStyle->getIndent() * 9).'px';
		}

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Font)
	 *
	 * @param	PHPExcel_Style_Font		$pStyle			PHPExcel_Style_Font
	 * @return	array
	 */
	private function _createCSSStyleFont(PHPExcel_Style_Font $pStyle) {
		// Construct CSS
		$css = array();

		// Create CSS
		if ($pStyle->getBold()) {
			$css['font-weight'] = 'bold';
		}
		if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE && $pStyle->getStrikethrough()) {
			$css['text-decoration'] = 'underline line-through';
		} else if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE) {
			$css['text-decoration'] = 'underline';
		} else if ($pStyle->getStrikethrough()) {
			$css['text-decoration'] = 'line-through';
		}
		if ($pStyle->getItalic()) {
			$css['font-style'] = 'italic';
		}

		$css['color']		= '#' . $pStyle->getColor()->getRGB();
		$css['font-family']	= '\'' . $pStyle->getName() . '\'';
		$css['font-size']	= $pStyle->getSize() . 'pt';

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Borders)
	 *
	 * @param	PHPExcel_Style_Borders		$pStyle			PHPExcel_Style_Borders
	 * @return	array
	 */
	private function _createCSSStyleBorders(PHPExcel_Style_Borders $pStyle) {
		// Construct CSS
		$css = array();

		// Create CSS
		$css['border-bottom']	= $this->_createCSSStyleBorder($pStyle->getBottom());
		$css['border-top']		= $this->_createCSSStyleBorder($pStyle->getTop());
		$css['border-left']		= $this->_createCSSStyleBorder($pStyle->getLeft());
		$css['border-right']	= $this->_createCSSStyleBorder($pStyle->getRight());

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Border)
	 *
	 * @param	PHPExcel_Style_Border		$pStyle			PHPExcel_Style_Border
	 * @return	string
	 */
	private function _createCSSStyleBorder(PHPExcel_Style_Border $pStyle) {
		// Create CSS
		$css = $this->_mapBorderStyle($pStyle->getBorderStyle()) . ' #' . $pStyle->getColor()->getRGB();

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Fill)
	 *
	 * @param	PHPExcel_Style_Fill		$pStyle			PHPExcel_Style_Fill
	 * @return	array
	 */
	private function _createCSSStyleFill(PHPExcel_Style_Fill $pStyle) {
		// Construct HTML
		$css = array();

		// Create CSS
		$value = $pStyle->getFillType() == PHPExcel_Style_Fill::FILL_NONE ?
			'white' : '#' . $pStyle->getStartColor()->getRGB();
		$css['background-color'] = $value;

		// Return
		return $css;
	}

	/**
	 * Generate HTML footer
	 */
	public function generateHTMLFooter() {
		// Construct HTML
		$html = '';
		$html .= '  </body>' . PHP_EOL;
		$html .= '</html>' . PHP_EOL;

		// Return
		return $html;
	}

	/**
	 * Generate table header
	 *
	 * @param	PHPExcel_Worksheet	$pSheet		The worksheet for the table we are writing
	 * @return	string
	 * @throws	Exception
	 */
	private function _generateTableHeader($pSheet) {
		$sheetIndex = $pSheet->getParent()->getIndex($pSheet);

		// Construct HTML
		$html = '';

		if (!$this->_useInlineCss) {
			$gridlines = $pSheet->getShowGridLines() ? ' gridlines' : '';
			$html .= '	<table border="0" cellpadding="0" cellspacing="0" id="sheet' . $sheetIndex . '" class="sheet' . $sheetIndex . $gridlines . '">' . PHP_EOL;
		} else {
			$style = isset($this->_cssStyles['table']) ?
				$this->_assembleCSS($this->_cssStyles['table']) : '';

			if ($this->_isPdf && $pSheet->getShowGridLines()) {
				$html .= '	<table border="1" cellpadding="1" id="sheet' . $sheetIndex . '" cellspacing="4" style="' . $style . '">' . PHP_EOL;
			} else {
				$html .= '	<table border="0" cellpadding="1" id="sheet' . $sheetIndex . '" cellspacing="4" style="' . $style . '">' . PHP_EOL;
			}
		}

		// Write <col> elements
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($pSheet->getHighestColumn()) - 1;
		$i = -1;
		while($i++ < $highestColumnIndex) {
		    if (!$this->_isPdf) {
				if (!$this->_useInlineCss) {
					$html .= '		<col class="col' . $i . '">' . PHP_EOL;
				} else {
					$style = isset($this->_cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) ?
						$this->_assembleCSS($this->_cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) : '';
					$html .= '		<col style="' . $style . '">' . PHP_EOL;
				}
			}
		}

		// Return
		return $html;
	}

	/**
	 * Generate table footer
	 *
	 * @throws	Exception
	 */
	private function _generateTableFooter() {
		// Construct HTML
		$html = '';
		$html .= '	</table>' . PHP_EOL;

		// Return
		return $html;
	}

	/**
	 * Generate row
	 *
	 * @param	PHPExcel_Worksheet	$pSheet			PHPExcel_Worksheet
	 * @param	array				$pValues		Array containing cells in a row
	 * @param	int					$pRow			Row number (0-based)
	 * @return	string
	 * @throws	Exception
	 */
	private function _generateRow(PHPExcel_Worksheet $pSheet, $pValues = null, $pRow = 0) {
		if (is_array($pValues)) {
			// Construct HTML
			$html = '';

			// Sheet index
			$sheetIndex = $pSheet->getParent()->getIndex($pSheet);

			// DomPDF and breaks
			if ($this->_isPdf && count($pSheet->getBreaks()) > 0) {
				$breaks = $pSheet->getBreaks();

				// check if a break is needed before this row
				if (isset($breaks['A' . $pRow])) {
					// close table: </table>
					$html .= $this->_generateTableFooter();

					// insert page break
					$html .= '<div style="page-break-before:always" />';

					// open table again: <table> + <col> etc.
					$html .= $this->_generateTableHeader($pSheet);
				}
			}

			// Write row start
			if (!$this->_useInlineCss) {
				$html .= '		  <tr class="row' . $pRow . '">' . PHP_EOL;
			} else {
				$style = isset($this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow])
					? $this->_assembleCSS($this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]) : '';

				$html .= '		  <tr style="' . $style . '">' . PHP_EOL;
			}

			// Write cells
			$colNum = 0;
			foreach ($pValues as $cell) {
				$coordinate = PHPExcel_Cell::stringFromColumnIndex($colNum) . ($pRow + 1);

				if (!$this->_useInlineCss) {
					$cssClass = '';
					$cssClass = 'column' . $colNum;
				} else {
					$cssClass = array();
					if (isset($this->_cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum])) {
						$this->_cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum];
					}
				}
				$colSpan = 1;
				$rowSpan = 1;

				// initialize
				$cellData = '';

				// PHPExcel_Cell
				if ($cell instanceof PHPExcel_Cell) {
					if (is_null($cell->getParent())) {
						$cell->attach($pSheet);
					}
					// Value
					if ($cell->getValue() instanceof PHPExcel_RichText) {
						// Loop through rich text elements
						$elements = $cell->getValue()->getRichTextElements();
						foreach ($elements as $element) {
							// Rich text start?
							if ($element instanceof PHPExcel_RichText_Run) {
								$cellData .= '<span style="' . $this->_assembleCSS($this->_createCSSStyleFont($element->getFont())) . '">';

								if ($element->getFont()->getSuperScript()) {
									$cellData .= '<sup>';
								} else if ($element->getFont()->getSubScript()) {
									$cellData .= '<sub>';
								}
							}

							// Convert UTF8 data to PCDATA
							$cellText = $element->getText();
							$cellData .= htmlspecialchars($cellText);

							if ($element instanceof PHPExcel_RichText_Run) {
								if ($element->getFont()->getSuperScript()) {
									$cellData .= '</sup>';
								} else if ($element->getFont()->getSubScript()) {
									$cellData .= '</sub>';
								}

								$cellData .= '</span>';
							}
						}
					} else {
						if ($this->_preCalculateFormulas) {
							$cellData = PHPExcel_Style_NumberFormat::toFormattedString(
								$cell->getCalculatedValue(),
								$pSheet->getParent()->getCellXfByIndex( $cell->getXfIndex() )->getNumberFormat()->getFormatCode(),
								array($this, 'formatColor')
							);
						} else {
							$cellData = PHPExcel_Style_NumberFormat::ToFormattedString(
								$cell->getValue(),
								$pSheet->getParent()->getCellXfByIndex( $cell->getXfIndex() )->getNumberFormat()->getFormatCode(),
								array($this, 'formatColor')
							);
						}
						$cellData = htmlspecialchars($cellData);
						if ($pSheet->getParent()->getCellXfByIndex( $cell->getXfIndex() )->getFont()->getSuperScript()) {
							$cellData = '<sup>'.$cellData.'</sup>';
						} elseif ($pSheet->getParent()->getCellXfByIndex( $cell->getXfIndex() )->getFont()->getSubScript()) {
							$cellData = '<sub>'.$cellData.'</sub>';
						}
					}

					// Converts the cell content so that spaces occuring at beginning of each new line are replaced by &nbsp;
					// Example: "  Hello\n to the world" is converted to "&nbsp;&nbsp;Hello\n&nbsp;to the world"
					$cellData = preg_replace("/(?m)(?:^|\\G) /", '&nbsp;', $cellData);

					// convert newline "\n" to '<br>'
					$cellData = nl2br($cellData);

					// Extend CSS class?
					if (!$this->_useInlineCss) {
						$cssClass .= ' style' . $cell->getXfIndex();
						$cssClass .= ' ' . $cell->getDataType();
					} else {
						if (isset($this->_cssStyles['td.style' . $cell->getXfIndex()])) {
							$cssClass = array_merge($cssClass, $this->_cssStyles['td.style' . $cell->getXfIndex()]);
						}

						// General horizontal alignment: Actual horizontal alignment depends on dataType
						$sharedStyle = $pSheet->getParent()->getCellXfByIndex( $cell->getXfIndex() );
						if ($sharedStyle->getAlignment()->getHorizontal() == PHPExcel_Style_Alignment::HORIZONTAL_GENERAL
							&& isset($this->_cssStyles['.' . $cell->getDataType()]['text-align']))
						{
							$cssClass['text-align'] = $this->_cssStyles['.' . $cell->getDataType()]['text-align'];
						}
					}
				}

				// Hyperlink?
				if ($pSheet->hyperlinkExists($coordinate) && !$pSheet->getHyperlink($coordinate)->isInternal()) {
					$cellData = '<a href="' . htmlspecialchars($pSheet->getHyperlink($coordinate)->getUrl()) . '" title="' . htmlspecialchars($pSheet->getHyperlink($coordinate)->getTooltip()) . '">' . $cellData . '</a>';
				}

				// Should the cell be written or is it swallowed by a rowspan or colspan?
				$writeCell = ! ( isset($this->_isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum])
							&& $this->_isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum] );

				// Colspan and Rowspan
				$colspan = 1;
				$rowspan = 1;
				if (isset($this->_isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum])) {
					$spans = $this->_isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum];
					$rowSpan = $spans['rowspan'];
					$colSpan = $spans['colspan'];
				}

				// Write
				if ($writeCell) {
					// Column start
					$html .= '			<td';
						if (!$this->_useInlineCss) {
							$html .= ' class="' . $cssClass . '"';
						} else {
							//** Necessary redundant code for the sake of PHPExcel_Writer_PDF **
							// We must explicitly write the width of the <td> element because TCPDF
							// does not recognize e.g. <col style="width:42pt">
							$width = 0;
							$i = $colNum - 1;
							$e = $colNum + $colSpan - 1;
							while($i++ < $e) {
								if (isset($this->_columnWidths[$sheetIndex][$i])) {
									$width += $this->_columnWidths[$sheetIndex][$i];
								}
							}
							$cssClass['width'] = $width . 'pt';

							// We must also explicitly write the height of the <td> element because TCPDF
							// does not recognize e.g. <tr style="height:50pt">
							if (isset($this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'])) {
								$height = $this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'];
								$cssClass['height'] = $height;
							}
							//** end of redundant code **

							$html .= ' style="' . $this->_assembleCSS($cssClass) . '"';
						}
						if ($colSpan > 1) {
							$html .= ' colspan="' . $colSpan . '"';
						}
						if ($rowSpan > 1) {
							$html .= ' rowspan="' . $rowSpan . '"';
						}
					$html .= '>';

					// Image?
					$html .= $this->_writeImageTagInCell($pSheet, $coordinate);

					// Cell data
					$html .= $cellData;

					// Column end
					$html .= '</td>' . PHP_EOL;
				}

				// Next column
				++$colNum;
			}

			// Write row end
			$html .= '		  </tr>' . PHP_EOL;

			// Return
			return $html;
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}

	/**
	 * Takes array where of CSS properties / values and converts to CSS string
	 *
	 * @param array
	 * @return string
	 */
	private function _assembleCSS($pValue = array())
	{
		$pairs = array();
		foreach ($pValue as $property => $value) {
			$pairs[] = $property . ':' . $value;
		}
		$string = implode('; ', $pairs);

		return $string;
	}

	/**
	 * Get Pre-Calculate Formulas
	 *
	 * @return boolean
	 */
	public function getPreCalculateFormulas() {
		return $this->_preCalculateFormulas;
	}

	/**
	 * Set Pre-Calculate Formulas
	 *
	 * @param boolean $pValue	Pre-Calculate Formulas?
	 * @return PHPExcel_Writer_HTML
	 */
	public function setPreCalculateFormulas($pValue = true) {
		$this->_preCalculateFormulas = $pValue;
		return $this;
	}

	/**
	 * Get images root
	 *
	 * @return string
	 */
	public function getImagesRoot() {
		return $this->_imagesRoot;
	}

	/**
	 * Set images root
	 *
	 * @param string $pValue
	 * @return PHPExcel_Writer_HTML
	 */
	public function setImagesRoot($pValue = '.') {
		$this->_imagesRoot = $pValue;
		return $this;
	}

	/**
	 * Get use inline CSS?
	 *
	 * @return boolean
	 */
	public function getUseInlineCss() {
		return $this->_useInlineCss;
	}

	/**
	 * Set use inline CSS?
	 *
	 * @param boolean $pValue
	 * @return PHPExcel_Writer_HTML
	 */
	public function setUseInlineCss($pValue = false) {
		$this->_useInlineCss = $pValue;
		return $this;
	}

	/**
	 * Add color to formatted string as inline style
	 *
	 * @param string $pValue Plain formatted value without color
	 * @param string $pFormat Format code
	 * @return string
	 */
	public function formatColor($pValue, $pFormat)
	{
		// Color information, e.g. [Red] is always at the beginning
		$color = null; // initialize
		$matches = array();

		$color_regex = '/^\\[[a-zA-Z]+\\]/';
		if (preg_match($color_regex, $pFormat, $matches)) {
			$color = str_replace('[', '', $matches[0]);
			$color = str_replace(']', '', $color);
			$color = strtolower($color);
		}

		// convert to PCDATA
		$value = htmlspecialchars($pValue);

		// color span tag
		if ($color !== null) {
			$value = '<span style="color:' . $color . '">' . $value . '</span>';
		}

		return $value;
	}

	/**
	 * Calculate information about HTML colspan and rowspan which is not always the same as Excel's
	 */
	private function _calculateSpans()
	{
		// Identify all cells that should be omitted in HTML due to cell merge.
		// In HTML only the upper-left cell should be written and it should have
		//   appropriate rowspan / colspan attribute
		$sheetIndexes = $this->_sheetIndex !== null ?
			array($this->_sheetIndex) : range(0, $this->_phpExcel->getSheetCount() - 1);

		foreach ($sheetIndexes as $sheetIndex) {
			$sheet = $this->_phpExcel->getSheet($sheetIndex);

			$candidateSpannedRow  = array();

			// loop through all Excel merged cells
			foreach ($sheet->getMergeCells() as $cells) {
				list($cells, ) = PHPExcel_Cell::splitRange($cells);
				$first = $cells[0];
				$last  = $cells[1];

				list($fc, $fr) = PHPExcel_Cell::coordinateFromString($first);
				$fc = PHPExcel_Cell::columnIndexFromString($fc) - 1;

				list($lc, $lr) = PHPExcel_Cell::coordinateFromString($last);
				$lc = PHPExcel_Cell::columnIndexFromString($lc) - 1;

				// loop through the individual cells in the individual merge
				$r = $fr - 1;
				while($r++ < $lr) {
					// also, flag this row as a HTML row that is candidate to be omitted
					$candidateSpannedRow[$r] = $r;

					$c = $fc - 1;
					while($c++ < $lc) {
						if ( !($c == $fc && $r == $fr) ) {
							// not the upper-left cell (should not be written in HTML)
							$this->_isSpannedCell[$sheetIndex][$r][$c] = array(
								'baseCell' => array($fr, $fc),
							);
						} else {
							// upper-left is the base cell that should hold the colspan/rowspan attribute
							$this->_isBaseCell[$sheetIndex][$r][$c] = array(
								'xlrowspan' => $lr - $fr + 1, // Excel rowspan
								'rowspan'   => $lr - $fr + 1, // HTML rowspan, value may change
								'xlcolspan' => $lc - $fc + 1, // Excel colspan
								'colspan'   => $lc - $fc + 1, // HTML colspan, value may change
							);
						}
					}
				}
			}

			// Identify which rows should be omitted in HTML. These are the rows where all the cells
			//   participate in a merge and the where base cells are somewhere above.
			$countColumns = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
			foreach ($candidateSpannedRow as $rowIndex) {
				if (isset($this->_isSpannedCell[$sheetIndex][$rowIndex])) {
					if (count($this->_isSpannedCell[$sheetIndex][$rowIndex]) == $countColumns) {
						$this->_isSpannedRow[$sheetIndex][$rowIndex] = $rowIndex;
					};
				}
			}

			// For each of the omitted rows we found above, the affected rowspans should be subtracted by 1
			if ( isset($this->_isSpannedRow[$sheetIndex]) ) {
				foreach ($this->_isSpannedRow[$sheetIndex] as $rowIndex) {
					$adjustedBaseCells = array();
					$c = -1;
					$e = $countColumns - 1;
					while($c++ < $e) {
						$baseCell = $this->_isSpannedCell[$sheetIndex][$rowIndex][$c]['baseCell'];

						if ( !in_array($baseCell, $adjustedBaseCells) ) {
							// subtract rowspan by 1
							--$this->_isBaseCell[$sheetIndex][ $baseCell[0] ][ $baseCell[1] ]['rowspan'];
							$adjustedBaseCells[] = $baseCell;
						}
					}
				}
			}

			// TODO: Same for columns
		}

		// We have calculated the spans
		$this->_spansAreCalculated = true;
	}

}
