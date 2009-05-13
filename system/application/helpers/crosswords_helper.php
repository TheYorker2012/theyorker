<?php

// Ensure that the wikiparser is loaded.
$CI = & get_instance();
$CI->load->library('wikiparser');

/// A special wikitext parser for crossword clues.
class CrosswordClueParser extends Wikiparser
{
	/// Default constructor
	function __construct()
	{
		parent::__construct();
		$this->multi_line = false;
	}
}

/// A clue in a crossword puzzle.
class CrosswordClue
{
	/// Construct a clue.
	function __construct($solution, $quickClue, $crypticClue)
	{
		$this->m_solution = split(' ', strtoupper($solution));
		foreach ($this->m_solution as &$word) {
			$word = split('-', $word);
		}
		$this->m_quickClue = $quickClue;
		$this->m_crypticClue = $crypticClue;
	}

	/*
	 * Accessors
	 */

	/// Get the quick clue.
	function quickClue()
	{
		return $this->m_quickClue;
	}

	/// Get the cryptic clue.
	function crypticClue()
	{
		return $this->m_crypticClue;
	}

	/// Get the solution.
	function solution($sep = false)
	{
		$sep1 = ($sep?' ':'');
		$sep2 = ($sep?'-':'');
		$words = array();
		foreach ($this->m_solution as $word) {
			$words[] = join($sep2, $word);
		}
		return join($sep1, $words);
	}

	/// Get the absolute length of the solution.
	function length()
	{
		return strlen($this->solution());
	}

	/// Get the length of the solution words.
	function wordLengths()
	{
		$results = array();
		foreach ($this->m_solution as $word) {
			$word_lens = array();
			foreach ($word as $part) {
				$word_lens[] = strlen($part);
			}
			$results[] = $word_lens;
		}
		return $results;
	}

	/// Get a string describing the word lengths.
	function wordLengthsString()
	{
		$result = array();
		foreach ($this->m_solution as $word) {
			$word_lens = array();
			foreach ($word as $part) {
				$word_lens[] = strlen($part);
			}
			$result[] = join('-',$word_lens);
		}
		return join(',',$result);
	}

	/*
	 * Private variables
	 */

	/// The solution.
	private $m_solution;

	/// The quick clue.
	private $m_quickClue;

	/// The cryptic clue.
	private $m_crypticClue;
}

/// A light in the grid.
class CrosswordGridLight
{
	/// Construct.
	function __construct($x, $y, $orientation, $length)
	{
		$this->m_x = $x;
		$this->m_y = $y;
		$this->m_orientation = $orientation;
		$this->m_length = $length;
	}

	/*
	 * Accessors
	 */

	function x()
	{
		return $this->m_x;
	}
	function y()
	{
		return $this->m_y;
	}
	function orientation()
	{
		return $this->m_orientation;
	}
	function length()
	{
		return $this->m_length;
	}

	function dx()
	{
		return ($this->m_orientation == CrosswordGrid::$HORIZONTAL ? 1 : 0);
	}
	function dy()
	{
		return ($this->m_orientation == CrosswordGrid::$VERTICAL   ? 1 : 0);
	}

	/*
	 * Private variables
	 */

	/// Coordinates of light.
	private $m_x, $m_y;

	/// Orientation of light.
	private $m_orientation;

	/// Length of light.
	private $m_length;
}

/// A crossword grid.
class CrosswordGrid
{
	static $HORIZONTAL = 0;
	static $VERTICAL   = 1;
	static $validSpacers = array(' ', '-');


	/// Construct a crossword grid.
	function __construct($width, $height)
	{
		$this->m_width = $width;
		$this->m_height = $height;
		$this->m_lights = array(self::$HORIZONTAL => array(),
		                        self::$VERTICAL   => array());
		$this->m_grid = array();
		for ($x = 0; $x < $width; ++$x) {
			for ($y = 0; $y < $height; ++$y) {
				$this->m_grid[$x][$y] = null;
			}
		}
		$this->m_spacers = array();
	}

	/*
	 * Modifiers
	 */

	/// Clear the solutions from the grid.
	function clearSolutions()
	{
		for ($x = 0; $x < $this->m_width; ++$x) {
			for ($y = 0; $y < $this->m_height; ++$y) {
				if (null != $this->m_grid[$x][$y]) {
					$this->m_grid[$x][$y] = '';
				}
			}
		}
	}

	/** Add a light to the grid.
	 * @returns true if successful, false otherwise.
	 */
	function addLight($light)
	{
		$this->m_lights[$light->orientation()][$light->x()][$light->y()] = &$light;
		ksort($this->m_lights[$light->orientation()]);
		ksort($this->m_lights[$light->orientation()][$light->x()]);
		$dx = $light->dx();
		$dy = $light->dy();
		for ($i = 0; $i < $light->length(); ++$i) {
			$this->setCellState($light->x()+($i*$dx),
					$light->y()+($i*$dy), "", false);
		}
		return true;
	}

	/// Find whether a light is unused.
	function lightUnused($light)
	{
		$dx = $light->dx();
		$dy = $light->dy();
		for ($i = 0; $i < $light->length(); ++$i) {
			if (is_string($this->cellState($light->x()+($i*$dx),
			                               $light->y()+($i*$dy)))) {
				return false;
			}
		}
		return true;
	}

	/** Set the state of a cell.
	 * @param $x int X coordinate.
	 * @param $y int Y coordinate.
	 * @param $state null/string State at (@p $x, @p $y).
	 *               Must be null, "", or a single capital letter.
	 * @param $onlyIfLight bool Prevents change if coordinates specify a blank.
	 * @returns true on success, false otherwise.
	 */
	function setCellState($x, $y, $state, $onlyIfLight = true)
	{
		if ($x >= 0 && $y >= 0 &&
		    $x < $this->m_width && $y < $this->m_height &&
		    (!$onlyIfLight || $this->m_grid[$x][$y] !== null)
			) {
			if (null !== $state) {
				if (is_string($state)) {
					$state = "$state";
					if ($state === '_') {
						$state = '';
					}
					$len = strlen($state);
					if ($len > 1) {
						$state = substr($state, 0, 1);
					}
					$state = strtoupper($state);
				}
			}
			$this->m_grid[$x][$y] = $state;
			return true;
		}
		else {
			return false;
		}
	}

	/** Set the spacing in a direction at a cell.
	 * @param $orientation int orientation.
	 * @param $x int position x coordinate.
	 * @param $y int position y coordinate.
	 * @param $spaced null," ","-" spacer type for this cell.
	 */
	function setCellSpacer($orientation, $x, $y, $spacer = null)
	{
		if (null !== $spacer) {
			$this->m_spacers[$orientation][$x][$y] = $spacer;
		}
		else {
			unset($this->m_spacers[$orientation][$x][$y]);
		}
	}

	/** Set the text of multiple adjacent cells.
	 * @param $light Light Light description object.
	 * @param $text string text.
	 * @returns Number of cells changed.
	 */
	function setLightText($light, $text)
	{
		$len = strlen($text);
		$dx = $light->dx();
		$dy = $light->dy();
		$spaces = 0;
		for ($i = 0; $i < $len; ++$i) {
			$char = substr($text, $i, 1);
			if ($char === '_') {
				$char = '';
			}
			if ($char === ' ' || $char === '-') {
				$this->setCellSpacer($light->orientation(), $light->x()+(($i-$spaces)*$dx),
				                                            $light->y()+(($i-$spaces)*$dy), $char);
				++$spaces;
			}
			elseif (!$this->setCellState($light->x()+(($i-$spaces)*$dx),
			                             $light->y()+(($i-$spaces)*$dy), $char, true)) {
				return $i;
			}
		}
		return $len;
	}

	/** Get the text of multiple adjacent cells (ignoring spacing).
	 * @param $light Light Light description object.
	 */
	function lightText($light)
	{
		$len = $light->length();
		$dx = $light->dx();
		$dy = $light->dy();
		$result = '';
		for ($i = 0; $i < $len; ++$i) {
			$state = $this->cellState($light->x()+($i*$dx),
			                          $light->y()+($i*$dy));
			if (is_string($state)) {
				if ($state == '') {
					$result .= '_';
				}
				else {
					$result .= $state;
				}
			}
		}
		return $result;
	}

	/*
	 * Accessors
	 */

	/// Get the width of the grid.
	function width()
	{
		return $this->m_width;
	}

	/// Get the height of the grid.
	function height()
	{
		return $this->m_height;
	}

	/** Find the status of a cell.
	 * @returns (null, false, string) = (out of range, black, value)
	 */
	function cellState($x, $y)
	{
		if ($x >= 0 && $y >= 0 && $x < $this->m_width && $y < $this->m_height) {
			$state = $this->m_grid[$x][$y];
			if (null !== $state) {
				return $state;
			}
			else {
				return false;
			}
		}
		else {
			return null;
		}
	}

	/** Get whether a cell has spacers horizontally or vertically.
	 * @return array[bool] whether it has a spacer in x and y direction.
	 */
	function cellSpacers($x, $y)
	{
		return array(
			isset($this->m_spacers[0][$x][$y]) ? $this->m_spacers[0][$x][$y] : null,
			isset($this->m_spacers[1][$x][$y]) ? $this->m_spacers[1][$x][$y] : null,
		);
	}

	/** Get the list of lights overlapping a cell.
	 * @returns array[Light]
	 */
	function lightsAt($x, $y, $immediate = true)
	{
		$results = array();
		// Horizontal
		if ($x < $this->m_width) {
			$i = $x;
			while ($i >= 0 && null !== $this->m_grid[$i][$y]) {
				if (isset($this->m_lights[self::$HORIZONTAL][$i][$y])) {
					$results[] = &$this->m_lights[self::$HORIZONTAL][$i][$y];
					break;
				}
				if ($immediate) {
					break;
				}
				--$i;
			}
		}
		// Vertical
		if ($y < $this->m_height) {
			$i = $y;
			while ($i >= 0 && null !== $this->m_grid[$x][$i]) {
				if (isset($this->m_lights[self::$VERTICAL][$x][$i])) {
					$results[] = &$this->m_lights[self::$VERTICAL][$x][$i];
					break;
				}
				if ($immediate) {
					break;
				}
				--$i;
			}
		}
		return $results;
	}

	/*
	 * Private variables
	 */

	/// Number of cells wide.
	private $m_width;

	/// Number of cells high.
	private $m_height;

	/// Lights indexed by orientation, X, Y coordinate.
	private $m_lights;

	/// array[int x=>array[int y=>{null,string}]]
	private $m_grid;

	/// Whether each cell is preceeded by a space, indexed by orientation, X, Y coordinate.
	private $m_spacers;
}

class CrosswordPuzzleLight extends CrosswordGridLight
{
	function __construct($x, $y, $orientation, &$clue)
	{
		parent::__construct($x, $y, $orientation, $clue->length());
		$this->m_clue = &$clue;
	}

	/*
	 * Accessors
	 */
	
	/// Get the clue in this light.
	function clue()
	{
		return $this->m_clue;
	}

	/*
	 * Private variables
	 */

	/// The clue in this light.
	private $m_clue;
}

/// A crossword puzzle.
class CrosswordPuzzle
{
	function __construct($width = null, $height = null)
	{
		if ($width != null) {
			$this->m_grid = new CrosswordGrid($width, $height);
		}
		else {
			$this->m_grid = null;
		}
	}

	/// Import from GET/POST data on top of existing data.
	function importGrid(&$data)
	{
		if (null === $this->m_grid) {
			return false;
		}

		// First clear the grid
		$this->m_grid->clearSolutions();

		if (isset($data['gr'])) {
			foreach ($data['gr'] as $x => $ys) {
				if (!is_numeric($x)) {
					continue;
				}
				$x = (int)$x;
				foreach ($ys as $y => $val) {
					if (!is_numeric($y)) {
						continue;
					}
					$y = (int)$y;
					if (strlen($val) > 0) {
						$this->m_grid->setCellState($x, $y, strtoupper(substr($val, 0, 1)));
					}
				}
			}
		}
		return true;
	}

	/// Import from GET/POST data
	function importData(&$data)
	{
		$width = -1;
		$height = -1;
		if (isset($data['width'])) {
			$width = $data['width'];
			if (is_numeric($width)) {
				$width = (int)$width;
			} else {
				$width = -1;
			}
		}
		if (isset($data['height'])) {
			$height = $data['height'];
			if (is_numeric($height)) {
				$height = (int)$height;
			} else {
				$height = -1;
			}
		}
		if ($width < 2 || $height < 2 || $width > 30 || $height > 30) {
			return false;
		}
		$this->m_grid = new CrosswordGrid($width, $height);
		// Lights are the interesting things
		if (isset($data['li'])) {
			foreach ($data['li'] as $x => $ys) {
				if (!is_numeric($x)) {
					continue;
				}
				$x = (int)$x;
				foreach ($ys as $y => $lights) {
					if (!is_numeric($y)) {
						continue;
					}
					$y = (int)$y;
					foreach ($lights as $o => $light) {
						if ($o !== 0 && $o !== 1) {
							continue;
						}
						$o = (int)$o;
						if (!isset($light['len']) || !is_numeric($light['len'])) {
							continue;
						}
						$len = (int)$light['len'];
						$dx = 1-$o;
						$dy = $o;
						$limit = $dx*($this->m_grid->width()-$x)
						       + $dy*($this->m_grid->height()-$y);
						if ($len > $limit) {
							$len = $limit;
						}
						$word = '';
						for ($i = 0; $i < $light['len']; ++$i) {
							$cx = $x + $dx*$i;
							$cy = $y + $dy*$i;
							if ($word !== '' && isset($data['sp'][$cx][$cy][$o])) {
								$spacer = $data['sp'][$cx][$cy][$o];
								if (is_string($spacer) && in_array($spacer, CrosswordGrid::$validSpacers)) {
									$word .= $spacer;
								}
							}
							$letter = (isset($data['gr'][$cx][$cy]) ? $data['gr'][$cx][$cy] : '');
							if ($letter === '') {
								$word .= '_';
							}
							else {
								$word .= substr($letter, 0,1);
							}
						}
						$quick = '';
						$cryptic = '';
						if (isset($light['clues'][0]) && is_string($light['clues'][0])) {
							$quick = $light['clues'][0];
						}
						if (isset($light['clues'][1]) && is_string($light['clues'][1])) {
							$cryptic = $light['clues'][1];
						}
						$this->addLight($x, $y, $o, new CrosswordClue($word, $quick, $cryptic));
					}
				}
			}
		}
		return true;
	}

	/*
	 * Modifiers
	 */

	/** Add a light to the grid.
	 */
	function addLight($x, $y, $orientation, &$clue)
	{
		$light = new CrosswordPuzzleLight($x, $y, $orientation, $clue);
		$this->m_grid->addLight($light);
		$this->m_grid->setLightText($light, $clue->solution(' '));
	}

	/*
	 * Accessors
	 */

	/// Get the grid.
	function grid()
	{
		return $this->m_grid;
	}

	/// Find whether the grid is correct.
	function isCorrect()
	{
		// All lights must have correct values
		$height = $this->m_grid->height();
		$width = $this->m_grid->width();
		for ($y = 0; $y < $height; ++$y) {
			for ($x = 0; $x < $width; ++$x) {
				$lights = $this->m_grid->lightsAt($x, $y, true);
				foreach ($lights as &$light) {
					$answer = $this->m_grid->lightText($light);
					$solution = $light->clue()->solution();
					if ($answer != $solution) {
						return false;
					}
				}
			}
		}
		return true;
	}

	/// Generate image.
	function generateImage($cellsize = 4, $padding = 1, $border = 0, $spacing = 1)
	{
		// From GET array
		if (is_array($cellsize)) {
			$data = $cellsize;
			$cellsize = ((isset($data['cellsize']) && is_numeric($data['cellsize']) && (int)$data['cellsize'] > 0)
				? (int)$data['cellsize'] : 4);
			if ($cellsize > 50) {
				$cellsize = 50;
			}
			$padding = ((isset($data['padding']) && is_numeric($data['padding']) && (int)$data['padding'] >= 0)
				? (int)$data['padding'] : 1+(int)($cellsize/15));
			$border = ((isset($data['border']) && is_numeric($data['border']) && (int)$data['border'] >= 0)
				? (int)$data['border'] : (int)($cellsize/10));
			$spacing = ((isset($data['spacing']) && is_numeric($data['spacing']) && (int)$data['spacing'] >= 0)
				? (int)$data['spacing'] : $padding);
			if ($spacing >= $cellsize) {
				$spacing = $cellsize-1;
			}
		}
		$width = $this->m_grid->width();
		$height = $this->m_grid->height();
		$preview_img = imagecreate(	$border*2 + ($padding+$cellsize)*$width  + $padding,
									$border*2 + ($padding+$cellsize)*$height + $padding );
		$background = imagecolorallocate($preview_img, 0xFF, 0x6A, 0x00 );
		$text_colour = imagecolorallocate($preview_img, 0x00, 0x00, 0x00 );
		$space_colour = imagecolorallocate($preview_img, 0xFF, 0xFF, 0xFF);

		$sup_offset = array(
			(int)($cellsize/10),
			(int)($cellsize/10)-1,
		);
		$space_chars = array(
			' ' => true,
			'-' => true,
		);
		$sup_fonts = array(
			5 => 1,
			15 => 2,
			30 => 4,
		);
		$sup_font = 0;
		foreach ($sup_fonts as $min_size => $font)
		{
			if ($cellsize > $min_size) {
				$sup_font = $font;
			}
		}

		$clueNumber = 0;
		for ($j = 0; $j < $height; ++$j) {
			for ($i = 0; $i < $width; ++$i) {
				$state = $this->m_grid->cellState($i, $j);
				$used = is_string($state);
				if ($used) {
					// Find position
					$p1 = array(
						$border + $padding + ($cellsize+$padding)*$i,
						$border + $padding + ($cellsize+$padding)*$j,
					);
					$p2 = array(
						$p1[0] + $cellsize-1,
						$p1[1] + $cellsize-1,
					);
					// Adjust for spacers
					$spacers = $this->m_grid->cellSpacers($i, $j);
					if (isset($space_chars[$spacers[CrosswordGrid::$HORIZONTAL]])) {
						$p1[0] += $spacing;
					}
					elseif (isset($space_chars[$spacers[CrosswordGrid::$VERTICAL]])) {
						$p1[1] += $spacing;
					}
					// Fill in this cell
					imagefilledrectangle($preview_img, $p1[0],$p1[1], $p2[0],$p2[1], $space_colour);

					// Light numbering
					$lights = $this->m_grid->lightsAt($i, $j, true);
					if (!empty($lights)) {
						++$clueNumber;
						if ($sup_font > 0) {
							imagestring($preview_img, $sup_font,
								$p1[0]+$sup_offset[0],
								$p1[1]+$sup_offset[1],
								$clueNumber, $text_colour);
						}
					}
				}
			}
		}

		header("Content-type: image/png");
		imagepng($preview_img);
		imagecolordeallocate($preview_img, $space_colour);
		imagecolordeallocate($preview_img, $text_colour);
		imagecolordeallocate($preview_img, $background);
		imagedestroy($preview_img);
	}

	/*
	 * Private variables
	 */

	private $m_grid;
}

class CrosswordViewXml
{
	private $m_crossword;
	private $m_edit;

	function __construct(&$crossword, $edit = false)
	{
		$this->m_crossword = $crossword;
		$this->m_edit = $edit;
	}

	function Load()
	{
		$grid = &$this->m_crossword->grid();
		$height = $grid->height();
		$width = $grid->width();
		$clueNumber = 0;
		$clues = array();

		echo('<'.'?xml version="1.0" encoding="utf-8">'."\n");
		?><crossword><?php
		?><grid width="<?php echo($width); ?>" height="<?php echo($height); ?>"><?php


		//for ($y = 0; $y < $height; ++$y) {
		//	for ($x = 0; $x < $width; ++$x) {

		?></grid><?php
		?></crossword><?php
	}
}

class CrosswordView
{
	private $m_crossword;
	private $m_edit;
	private $m_readonly;
	private $m_keepInline;
	private $m_allowToggleClueType;
	private $m_cluesQuick;
	private $m_cluesCryptic;
	private $m_defaultCryptic;

	function __construct(&$crossword, $edit = false)
	{
		$this->m_crossword = $crossword;
		$this->m_edit = $edit;
		$this->m_readonly = false;
		$this->m_keepInline = false;
		// so that the hidden clues aren't lost when saved
		$this->m_allowToggleClueType = $edit;
		$this->m_cluesQuick = true;
		$this->m_cluesCryptic = false;
		$this->m_defaultCryptic = false;

		// Set up stylesheets and javascript
		$ci = &get_instance();
		$ci->main_frame->IncludeCss('stylesheets/crosswords.css');
		$ci->main_frame->IncludeCss('stylesheets/crosswords-iefix.css',null,null,'IE');
		$ci->main_frame->IncludeJs('javascript/simple_ajax.js');
		$ci->main_frame->IncludeJs('javascript/css_classes.js');
		$ci->main_frame->IncludeJs('javascript/crosswords.js');
		if ($edit) {
			$ci->main_frame->IncludeJs('javascript/crosswords_edit.js');
		}
	}

	function crossword()
	{
		return $this->m_crossword;
	}

	function setReadOnly($readonly, $keep_inline = false)
	{
		$this->m_readonly = $readonly;
		$this->m_keepInline = $keep_inline;
	}

	function setClueTypes($quick, $cryptic)
	{
		// or edit so that the hidden clues aren't lost when saved
		$this->m_allowToggleClueType = $this->m_edit || ($quick && $cryptic);
		$this->m_cluesQuick = $quick;
		$this->m_cluesCryptic = $cryptic;
		$this->m_defaultCryptic = $cryptic;
	}

	function Load()
	{
		$name = 'xw';
		$grid = &$this->m_crossword->grid();
		$height = $grid->height();
		$width = $grid->width();
		$clueNumber = 0;
		$clues = array(
			CrosswordGrid::$HORIZONTAL	=> array(),
			CrosswordGrid::$VERTICAL	=> array(),
		);

		if ($this->m_edit) {
			?><div class="crosswordEdit"><?php
		}
		?><noscript><?php
		?><div class="crosswordAjaxNotify error">please enable javascript in your browser</div><?php
		?></noscript><?php
		?><div id="<?php echo("$name-notify"); ?>" class="crosswordAjaxNotify"></div><?php
		if (!$this->m_edit) {
			?><div id="<?php echo("$name-complete"); ?>" class="crosswordAjaxNotify hidden"><?php
				?><fieldset><input	class="button" type="button" value="submit for marking" <?php
								?>	onclick="<?php echo(xml_escape("crossword('$name').submit()")); ?>"<?php
								?>	/>crossword complete</fieldset><?php
			?></div><?php
		}
		?><div class="crosswordBox"><?php
		// Render main crossword grid
		?><table class="crossword"><?php
		echo("\n");
		for ($y = 0; $y < $height; ++$y) {
			?><tr id="<?php echo("$name-row-$y"); ?>"><?php
			for ($x = 0; $x < $width; ++$x) {
				$state = $grid->cellState($x, $y);
				$used = is_string($state);
				if ($used || $this->m_edit) {
					$classes = array();
					if (!$used) {
						$classes[] = 'blank';
					}
					// Spacers on this cell?
					$spacers = $grid->cellSpacers($x, $y);
					if ($spacers[CrosswordGrid::$HORIZONTAL] === ' ') {
						$classes[] = 'hsp';
					}
					elseif ($spacers[CrosswordGrid::$HORIZONTAL] === '-') {
						$classes[] = 'hhy';
					}
					if ($spacers[CrosswordGrid::$VERTICAL] === ' ') {
						$classes[] = 'vsp';
					}
					elseif ($spacers[CrosswordGrid::$VERTICAL] === '-') {
						$classes[] = 'vhy';
					}
					?><td <?php
						if (!empty($classes)) {
							echo('class="'.implode(' ',$classes).'" ');
						}
						?>id="<?php echo("$name-$x-$y"); ?>" <?php
						?>onclick="<?php echo("xwc('$name',$x,$y,event);") ?>"><div><?php

					// Clue number
					$lights = $grid->lightsAt($x, $y, true);
					if (!empty($lights)) {
						++$clueNumber;
						foreach ($lights as &$light) {
							$clues[$light->orientation()][$clueNumber] = array($light->clue(), $x, $y);
						}
						?><sup id="<?php echo("$name-num-$x-$y"); ?>"><?php echo($clueNumber); ?></sup><?php
					}
					elseif ($this->m_edit) {
						?><sup id="<?php echo("$name-num-$x-$y"); ?>"></sup><?php
					}
					// Text input box
					?><input	type="text"<?php
						   ?>	id="<?php echo("$name-edit-$x-$y"); ?>"<?php
						if (!$this->m_readonly) {
						   ?>	onkeydown="<?php echo("return xwkd('$name',$x,$y,event);"); ?>"<?php
						   ?>	onkeypress="<?php echo("return xwkp('$name',$x,$y,event);"); ?>"<?php
						}
						   ?>	value="<?php echo(xml_escape($state)); ?>"<?php
						if ($this->m_readonly) {
							?>	readonly="readonly"<?php
						}
						   ?>	/><?php
					?></div></td><?php
				}
				else {
					// Nothing but a blank placemarker
					?><td class="blank" <?php
						?>onclick="<?php echo("xwd('$name');"); ?>" /><?php
				}
			}
			?></tr><?php
			echo("\n");
		}
		?></table><?php
		if (!$this->m_edit && !$this->m_readonly) {
			// Initially hidden, if expired this will get unhidden by javascript
			?><div id="<?php echo("$name-checks"); ?>" style="display:none"><?php
				?><fieldset><?php
					$check_actions = array(
						// check buttons
						'stop' => 'crosswordStopCheck('.js_literalise($name).');',
						'check selected light' => 'crosswordCheck('.js_literalise($name).','.js_literalise('cur_light').', false);',
						'check all lights' => 'crosswordCheck('.js_literalise($name).','.js_literalise('all_lights').', false);',
						null,
						// solve buttons
						'reveal selected light' => 'crosswordCheck('.js_literalise($name).','.js_literalise('cur_light').', true);',
						'reveal all lights' => 'crosswordCheck('.js_literalise($name).','.js_literalise('all_lights').', true);',
					);
					foreach ($check_actions as $action_name => $javascript) {
						if ($javascript == null) {
							?><br /><?php
							continue;
						}
						?><input	type="button"<?php
								?>	class="button"<?php
								?>	value="<?php echo(xml_escape($action_name)); ?>"<?php
								?>	onclick="<?php echo(xml_escape(
										$javascript
									)); ?>"<?php
								?>	/><?php
					}
				?></fieldset><?php
			?></div><?php
			// Clear grid button
			?><fieldset><?php
				?><input	type="button"<?php
						?>	class="button"<?php
						?>	value="clear grid"<?php
						?>	onclick="<?php echo(xml_escape(
								'crosswordClear('.js_literalise($name).');'
							)); ?>"<?php
						?>	/><?php
			?></fieldset><?php
		}
		?></div><?php

		// Clues bar
		$have_inline = !$this->m_readonly || $this->m_keepInline;
		if ($have_inline || $this->m_allowToggleClueType) {
			?><div class="crosswordCluesHeader"><?php
				?><div class="header"><?php
					// Toggles inline display of grid cells for each clue
					if ($have_inline) {
						?><fieldset><?php
							?><label	for="<?php echo("$name-clues-inline"); ?>"><?php
							if ($this->m_edit) {
								?>show solutions with clues<?php
							}
							else {
								?>show your answers with the clues<?php
							}
							?></label><?php
							?><input	id="<?php echo("$name-clues-inline"); ?>"<?php
									?>	type="checkbox"<?php
									?>	onclick="<?php echo(xml_escape("return crosswordInlineAnswersUpdated('$name');")); ?>"<?php
									?>	/><?php
						?></fieldset><?php
					}
					// Choice between quick and cryptic clues
					if ($this->m_allowToggleClueType) {
						?><fieldset<?php
								if (!$this->m_cluesQuick) {
									?> class="undesired"<?php
								}
								?>><?php
							?><label	for="<?php echo("$name-clues-show-quick"); ?>">show quick clues</label><?php
							?><input	id="<?php echo("$name-clues-show-quick"); ?>"<?php
									?>	name="<?php echo("$name[cluetype]"); ?>"<?php
									?>	class="radio"<?php
									?>	type="radio"<?php
									?>	value="quick"<?php
									?>	onclick="<?php echo(xml_escape("return crosswordClueTypeUpdated('$name');")); ?>"<?php
								if (!$this->m_defaultCryptic) {
									?>	checked="checked"<?php
								}
									?>	/><?php
						?></fieldset><?php
						?><fieldset<?php
								if (!$this->m_cluesCryptic) {
									?> class="undesired"<?php
								}
								?>><?php
							?><label	for="<?php echo("$name-clues-show-cryptic"); ?>">show cryptic clues</label><?php
							?><input	id="<?php echo("$name-clues-show-cryptic"); ?>"<?php
									?>	name="<?php echo("$name[cluetype]"); ?>"<?php
									?>	class="radio"<?php
									?>	type="radio"<?php
									?>	value="cryptic"<?php
									?>	onclick="<?php echo(xml_escape("return crosswordClueTypeUpdated('$name');")); ?>"<?php
								if ($this->m_defaultCryptic) {
									?>	checked="checked"<?php
								}
									?>	/><?php
						?></fieldset><?php
					}
					?><div style="clear:both"></div><?php
				?></div><?php
			?></div><?php
		}

		// List of clues
		$clue_parser = new CrosswordClueParser();
		$titles = array(CrosswordGrid::$HORIZONTAL => "across",
		                CrosswordGrid::$VERTICAL   => "down");
		$orClasses = array(CrosswordGrid::$HORIZONTAL => "horizontal",
		                   CrosswordGrid::$VERTICAL   => "vertical");
		$commonClasses = 'hideValues';
		if ($this->m_allowToggleClueType) {
			$commonClasses .= ' '.($this->m_defaultCryptic ? 'hideQuick' : 'hideCryptic');
		}
		$dx = array(CrosswordGrid::$HORIZONTAL	=> 1,
					CrosswordGrid::$VERTICAL	=> 0);
		$dy = array(CrosswordGrid::$HORIZONTAL	=> 0,
					CrosswordGrid::$VERTICAL	=> 1);
		foreach ($clues as $orientation => &$oclues) {
			?><div	class="crosswordCluesBox"><?php
			?><div	id="<?php echo("$name-$orientation-clues"); ?>"<?php
				?>	class="<?php echo($orClasses[$orientation].' '.$commonClasses); ?>"<?php
				?>><?php
			?><h2><?php
			echo(xml_escape($titles[$orientation]));
			?></h2><?php
			echo("\n");
			foreach ($oclues as $number => &$clueInfo) {
				$clue = &$clueInfo[0];
				$x = $clueInfo[1];
				$y = $clueInfo[2];
				?><div	class="clueBox"
						id="<?php echo("$name-$orientation-clue-$x-$y"); ?>" <?php
					?>><?php

				?><div	class="clueHeader" id="<?php echo("$name-$orientation-head-$x-$y"); ?>"
						onclick="crosswordSelectLight(<?php echo("'$name', $x, $y, $orientation, true"); ?>)"><?php
					?><span id="<?php echo("$name-$orientation-num-$x-$y"); ?>"><?php
					echo($number);
					?></span><?php
					echo(' ');

					if ($this->m_allowToggleClueType || !$this->m_defaultCryptic) {
						?><span class="quickClue" id="<?php echo("$name-$orientation-cluetext0-$x-$y"); ?>"><?php
						$clue_wikitext = $clue->quickClue();
						echo($clue_parser->parse($clue_wikitext));
						?></span><?php
					}
					if ($this->m_allowToggleClueType || $this->m_defaultCryptic) {
						?><span class="crypticClue" id="<?php echo("$name-$orientation-cluetext1-$x-$y"); ?>"><?php
						$clue_wikitext = $clue->crypticClue();
						echo($clue_parser->parse($clue_wikitext));
						?></span><?php
					}

					$lengths = $clue->wordLengthsString();
					?> (<span id="<?php echo("$name-$orientation-wordlen-$x-$y"); ?>"><?php echo($lengths); ?></span>)<?php
				?></div><?php

				if ($this->m_edit) {
					?><fieldset class="clueInputs"><?php
					?><input	id="<?php echo("$name-$orientation-clueinput0-$x-$y"); ?>"
								class="quickClue" type="text"
								value="<?php echo(xml_escape($clue->quickClue())); ?>"
								onfocus="return crosswordSelectLight(<?php echo("'$name', $x, $y, $orientation, false"); ?>);"
								onchange="return crosswordClueChanged(<?php echo("'$name', $x, $y, $orientation, 0"); ?>);"
								/><?php
					?><input	id="<?php echo("$name-$orientation-clueinput1-$x-$y"); ?>"
								class="crypticClue" type="text"
								value="<?php echo(xml_escape($clue->crypticClue())); ?>"
								onfocus="return crosswordSelectLight(<?php echo("'$name', $x, $y, $orientation, false"); ?>);"
								onchange="return crosswordClueChanged(<?php echo("'$name', $x, $y, $orientation, 1"); ?>);"
								/><?php
					?></fieldset><?php
				}

				if ($have_inline) {
					$solution = $clue->solution();
					$length = strlen($solution);
					?><table class="crossword"><?php
					?><tr id="<?php echo("$name-$orientation-inline-$x-$y"); ?>" class="small"><?php
					for ($i = 0; $i < $length; ++$i) {
						$cx = $x + $dx[$orientation]*$i;
						$cy = $y + $dy[$orientation]*$i;
						$state = $grid->cellState($cx, $cy);
						$used = is_string($state);
						if ($used || $this->m_edit) {
							$classes = array();
							if (!$used) {
								$classes[] = 'blank';
							}
							// Spacers on this cell?
							$spacers = $grid->cellSpacers($cx, $cy);
							if ($spacers[CrosswordGrid::$HORIZONTAL] === ' ') {
								$classes[] = 'hsp';
							}
							elseif ($spacers[CrosswordGrid::$HORIZONTAL] === '-') {
								$classes[] = 'hhy';
							}
							if ($spacers[CrosswordGrid::$VERTICAL] === ' ') {
								$classes[] = 'vsp';
							}
							elseif ($spacers[CrosswordGrid::$VERTICAL] === '-') {
								$classes[] = 'vhy';
							}
							?><td <?php
								if (!empty($classes)) {
									echo('class="'.implode(' ',$classes).'" ');
								}
							?>id="<?php echo("$name-$orientation-$cx-$cy"); ?>" <?php
								?>onclick="<?php echo("xwcc('$name',$cx,$cy,$orientation);") ?>"><?php
							?><input	type="text"<?php
								   ?>	id="<?php echo("$name-$orientation-edit-$cx-$cy"); ?>"<?php
								if (!$this->m_readonly) {
								   ?>	onkeydown="<?php echo("return xwkd('$name',$cx,$cy,event);") ?>"<?php
								   ?>	onkeypress="<?php echo("return xwkp('$name',$cx,$cy,event);") ?>"<?php
								}
								   ?>	value="<?php echo(xml_escape($state)); ?>"<?php
								if ($this->m_readonly) {
									?>	readonly="readonly"<?php
								}
									?>	/><?php
							?></td><?php
						}
					}
					?></tr><?php
					?></table><?php
				}

				?></div><?php
				echo("\n");
			}
			?></div><?php
			?></div><?php
		}
		if ($this->m_edit) {
			?></div><?php
		}
	}
}

/// List of tips
class CrosswordTipsList
{
	private $tips;
	private $category_id;
	private $crossword_id;
	private $add_form = null;
	private $office = false;

	function __construct($category_id = null, $crossword_id = null, $office = false, $allow_add = true)
	{
		$this->category_id = $category_id;
		$this->crossword_id = $crossword_id;
		$this->office = $office;

		$ci = &get_instance();

		if ($office) {
			$ci->load->helper('input');
			$ci->load->helper('input_wikitext');

			$categories = $ci->crosswords_model->GetTipCategories();
			$category_options = array();
			foreach ($categories as &$category) {
				$category_options[$category['id']] = $category['name'];
			}

			// Allow adding of new tips to specific crosswords
			if (null != $crossword_id && $allow_add) {
				// Can't add if there aren't any categories!
				if (!empty($category_options)) {
					$this->add_form = new InputInterfaces;

					$new_tip = array(
						'category_id' => (($this->category_id === null) ? $categories[0]['id'] : (int)$this->category_id),
						'crossword_id' => $this->crossword_id,
						'content_wikitext' => '',
						'content_xhtml' => '',
					);

					if (null === $this->category_id) {
						// Tip category
						$category_interface = new InputSelectInterface('new_tip_category', $new_tip['category_id']);
						$category_interface->SetOptions($category_options);
						$this->add_form->Add('Tip category', $category_interface);
					}
					else {
						$category_interface = null;
					}

					// Wikitext
					$content_interface = new InputWikitextInterface('new_tip_content', $new_tip['content_wikitext']);
					$content_interface->SetRequired(true);
					$content_interface->SetWikiparser();
					$this->add_form->Add('Content (wikitext)', $content_interface);

					$num_errors = $this->add_form->Validate();
					if (0 == $num_errors && $this->add_form->Updated()) {
						if (null === $this->category_id) {
							$new_tip['category_id'] = (int)$category_interface->Value();
						}
						$new_tip['content_wikitext'] = $content_interface->Value();
						$new_tip['content_xhtml'] = $content_interface->ValueXhtml();
						if (null !== $ci->crosswords_model->AddTip($new_tip)) {
							$ci->messages->AddMessage('success', 'Tip added');
							$content_interface->Reset();
						}
						else {
							$ci->messages->AddMessage('error', 'Tip could not be added');
						}
					}
				}
				else {
					$ci->messages->AddMessage('information', 'There are no crossword tip categories. You will need to <a href="/office/crosswords/tips/add?ret='.urlencode($ci->uri->uri_string()).'">create one</a> before you can add any tips for this crossword.');
				}
			}
		}

		$this->tips = $ci->crosswords_model->GetTips($category_id, $crossword_id, null, ($office ? null : true));

		if ($this->office) {
			foreach ($this->tips as $index => &$tip) {
				$form = new InputInterfaces;
				$tip['edit_form'] = &$form;
				$name ='tip_'.$tip['id'].'_';
				$inputs = array();
				$tip['inputs'] = &$inputs;

				// Delete tip
				$inputs['delete'] = new InputCheckboxInterface($name.'delete', false);
				$form->Add('Delete tip', $inputs['delete']);

				// Tip category
				$inputs['category'] = new InputSelectInterface($name.'category', $tip['category_id']);
				$inputs['category']->SetOptions($category_options);
				$form->Add('Tip category', $inputs['category']);
				
				// Wikitext
				$inputs['content'] = new InputWikitextInterface($name.'content', $tip['content_wikitext']);
				$inputs['content']->SetRequired(true);
				$inputs['content']->SetWikiparser();
				$form->Add('Content (wikitext)', $inputs['content']);

				// Delete it?
				if ($inputs['delete']->Value()) {
					$success = $ci->crosswords_model->DeleteTipById($tip['id']);
					if ($success) {
						unset($this->tips[$index]);
						$ci->messages->AddMessage('success', 'Tip deleted');
					}
					else {
						$ci->messages->AddMessage('error', 'Tip could not be deleted');
					}
				}
				// Update it?
				else {
					$num_errors = $form->Validate();
					if (0 == $num_errors && $form->Changed()) {
						$changes = $form->ChangedValues();
						$values = array();
						if (isset($changes[$name.'category'])) {
							$values['category_id'] = $changes[$name.'category'];
						}
						if (isset($changes[$name.'content'])) {
							$values['content_wikitext'] = $changes[$name.'content'];
							$values['content_xhtml'] = $inputs['content']->ValueXhtml();
						}
						$values['id'] = $tip['id'];
						if (!$ci->crosswords_model->UpdateTip($values)) {
							$ci->messages->AddMessage('error', 'Tip could not be saved');
						}
						else {
							$ci->messages->AddMessage('success', 'Tip saved successfully');
							foreach ($values as $id => $value) {
								$tip[$id] = $value;
							}
							if (isset($values['category_id'])) {
								$tip['category_name'] = $category_options[$values['category_id']];
								// Remove if no longer matching criteria
								if (null !== $this->category_id && ($this->category_id != $values['category_id'])) {
									unset($this->tips[$index]);
								}
							}
							$form->ResetDefaults();
						}
					}
				}

				unset($form);
				unset($inputs);
			}
		}
	}

	function IsEmpty()
	{
		return empty($this->tips);
	}

	function Load()
	{
		$ci = &get_instance();
		$data = array(
			'Tips' => $this->tips,
			'AddForm' => $this->add_form,
			'SelfUri' => $ci->uri->uri_string(),
			'ShowCrosswordInfo' => ($this->crossword_id === null),
			'ShowCategoryInfo' => ($this->category_id === null),
			'Office' => $this->office,
		);
		$ci->load->view('crosswords/tips_list', $data);
	}
}

?>
