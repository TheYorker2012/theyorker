<?php

/// A clue in a crossword puzzle.
class CrosswordClue
{
	/// Construct a clue.
	function __construct($solution, $quickClue, $crypticClue)
	{
		$this->m_solution = split(' ', strtoupper($solution));
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
	function solution($sep = '')
	{
		return join($sep, $this->m_solution);
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
		foreach ($this->m_solution as $word)
		{
			$results[] = strlen($word);
		}
		return $results;
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
		$this->m_spacers = array();
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
	 * @param $spaced bool whether spaced for this cell.
	 */
	function setCellSpacer($orientation, $x, $y, $spaced = true)
	{
		if ($spaced) {
			$this->m_spacers[$orientation][$x][$y] = true;
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
			if ($char === ' ') {
				$this->setCellSpacer($light->orientation(), $light->x()+(($i-$spaces)*$dx),
				                                            $light->y()+(($i-$spaces)*$dy));
				++$spaces;
			}
			elseif (!$this->setCellState($light->x()+(($i-$spaces)*$dx),
			                             $light->y()+(($i-$spaces)*$dy), $char, true)) {
				return $i;
			}
		}
		return $len;
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
			isset($this->m_spacers[0][$x][$y]),
			isset($this->m_spacers[1][$x][$y]),
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
								$word .= ' ';
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
	 * Modifiers
	 */

	/// Get the grid.
	function grid()
	{
		return $this->m_grid;
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

	function __construct(&$crossword, $edit = false)
	{
		$this->m_crossword = $crossword;
		$this->m_edit = $edit;
	}

	function crossword()
	{
		return $this->m_crossword;
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
					if ($spacers[CrosswordGrid::$HORIZONTAL]) {
						$classes[] = 'hsp';
					}
					if ($spacers[CrosswordGrid::$VERTICAL]) {
						$classes[] = 'vsp';
					}
					?><td <?php
						if (!empty($classes)) {
							echo('class="'.implode(' ',$classes).'" ');
						}
						?>id="<?php echo("$name-$x-$y"); ?>" <?php
						?>onclick="return crosswordClick(<?php echo("'$name', $x, $y, event") ?>)"><div><?php

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
					?><input type="text" maxlength="2" <?php
					       ?>id="<?php echo("$name-edit-$x-$y"); ?>" <?php
					       ?>onkeydown="return crosswordKeyDown(<?php echo("'$name',$x,$y,event") ?>)" <?php
					       ?>onkeypress="return crosswordKeyPress(<?php echo("'$name',$x,$y,event") ?>)" <?php
					       ?>value="<?php
					if ($this->m_edit) {
							echo(xml_escape($state));
					}
						   ?>" /><?php
					?></div></td><?php
				}
				else {
					// Nothing but a blank placemarker
					?><td class="blank" <?php
						?>onclick="return crosswordDeselect(<?php echo("'$name', event") ?>)" /><?php
				}
			}
			?></tr><?php
			echo("\n");
		}
		?></table><?php
		?><a id="toggleInlineAnswers" onclick="crosswordToggleInlineAnswers();">Show inline answers</a><br /><?php
		?><a id="toggleCrypticClues" onclick="crosswordToggleCrypticClues();">Show cryptic clues</a><br /><?php
		?></div><?php

		// List of clues
		$titles = array(CrosswordGrid::$HORIZONTAL => "Across",
		                CrosswordGrid::$VERTICAL   => "Down");
		$orClasses = array(CrosswordGrid::$HORIZONTAL => "horizontal",
		                   CrosswordGrid::$VERTICAL   => "vertical");
		$dx = array(CrosswordGrid::$HORIZONTAL	=> 1,
					CrosswordGrid::$VERTICAL	=> 0);
		$dy = array(CrosswordGrid::$HORIZONTAL	=> 0,
					CrosswordGrid::$VERTICAL	=> 1);
		foreach ($clues as $orientation => &$oclues) {
			?><div class="crosswordCluesBox"><?php
			?><div id="<?php echo("$name-$orientation-clues"); ?>" name="crosswordClues" class="<?php echo($orClasses[$orientation]); ?> hideValues hideCryptic"><?php
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

					?><span class="quickClue" id="<?php echo("$name-$orientation-cluetext0-$x-$y"); ?>"><?php
					echo(xml_escape($clue->quickClue()));
					?></span><?php
					?><span class="crypticClue" id="<?php echo("$name-$orientation-cluetext1-$x-$y"); ?>"><?php
					echo(xml_escape($clue->crypticClue()));
					?></span><?php

					$lengths = $clue->wordLengths();
					?> (<span id="<?php echo("$name-$orientation-wordlen-$x-$y"); ?>"><?php echo(join(',', $lengths)); ?></span>)<?php
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

				if (true || $this->m_edit) {
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
							if ($spacers[CrosswordGrid::$HORIZONTAL]) {
								$classes[] = 'hsp';
							}
							if ($spacers[CrosswordGrid::$VERTICAL]) {
								$classes[] = 'vsp';
							}
							?><td <?php
								if (!empty($classes)) {
									echo('class="'.implode(' ',$classes).'" ');
								}
							?>id="<?php echo("$name-$orientation-$cx-$cy"); ?>" <?php
								?>onclick="return crosswordClueClick(<?php echo("'$name', $cx, $cy, $orientation, event") ?>)"><div><?php
							?><input type="text" cols="1" maxlength="2" <?php
								   ?>id="<?php echo("$name-$orientation-edit-$cx-$cy"); ?>" <?php
								   ?>onkeydown="return crosswordKeyDown(<?php echo("'$name',$cx,$cy,event") ?>)" <?php
								   ?>onkeypress="return crosswordKeyPress(<?php echo("'$name',$cx,$cy,event") ?>)" <?php
								   ?>value="<?php
							if ($this->m_edit) {
								$letter = substr($solution, $i, 1);
								if ($letter == '_') {
									$letter = '';
								}
								echo(xml_escape($letter));
							}
							?>" /><?php
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

?>
