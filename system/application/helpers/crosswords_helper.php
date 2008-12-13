<?php

/// A clue in a crossword puzzle.
class CrosswordClue
{
	/// Construct a clue.
	function __construct($clue, $solution)
	{
		$this->m_clue = $clue;
		$this->m_solution = split(' ', strtoupper($solution));
	}

	/*
	 * Accessors
	 */

	/// Get the clue.
	function clue()
	{
		return $this->m_clue;
	}

	/// Get the solution.
	function solution()
	{
		return join('', $this->m_solution);
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

	/// The clue.
	private $m_clue;

	/// The solution.
	private $m_solution;
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
		for ($i = 0; $i < $len; ++$i) {
			if (!$this->setCellState($light->x()+($i*$dx),
			                         $light->y()+($i*$dy), substr($text, $i, 1), true)) {
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
	function __construct($width, $height)
	{
		$this->m_grid = new CrosswordGrid($width, $height);
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
		$this->m_grid->setLightText($light, $clue->solution());
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

class CrosswordView
{
	function __construct(&$crossword, $edit = false)
	{
		$this->m_crossword = $crossword;
		$this->m_edit = $edit;
	}

	function Load()
	{
		$name = 'xw';
		$grid = &$this->m_crossword->grid();
		$height = $grid->height();
		$width = $grid->width();
		$clueNumber = 0;
		$clues = array();

		?><div class="crosswordBox"><?php
		if ($this->m_edit) {
			?><div class="crosswordEdit"><?php
		}
		// Render main crossword grid
		?><table class="crossword"><?php
		echo("\n");
		for ($y = 0; $y < $height; ++$y) {
			?><tr><?php
			for ($x = 0; $x < $width; ++$x) {
				$state = $grid->cellState($x, $y);
				$used = is_string($state);
				if ($used || $this->m_edit) {
					?><td <?php if (!$used) { ?>class="blank" <?php } ?>id="<?php echo("$name-$x-$y"); ?>" <?php
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
					?><input type="text" cols="1" maxlength="2" <?php
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
		if ($this->m_edit) {
			?></div><?php
		}
		?></div><?php

		// List of clues
		$titles = array(CrosswordGrid::$HORIZONTAL => "Across",
		                CrosswordGrid::$VERTICAL   => "Down");
		?><div class="crosswordCluesBox"><?php
		foreach ($clues as $orientation => &$oclues) {
			?><div class="crosswordClues"><?php
			?><h2><?php
			echo(xml_escape($titles[$orientation]));
			?></h2><?php
			echo("\n");
			foreach ($oclues as $number => &$clueInfo) {
				$clue = &$clueInfo[0];
				$x = $clueInfo[1];
				$y = $clueInfo[2];
				?><p id="<?php echo("$name-clue-$x-$y-$orientation"); ?>" <?php
					?>onclick="crosswordSelectLight(<?php echo("'$name', $x, $y, $orientation, event"); ?>)"><?php
				echo($number.' ');
				$lengths = $clue->wordLengths();
				echo(xml_escape($clue->clue()));
				?> (<?php echo(join(',', $lengths)); ?>)<?php
				?></p><?php

				if (false) {
					$solution = $clue->solution();
					$length = strlen($solution);
					?><table class="crossword"><?php
					?><tr class="small"><?php
					for ($i = 0; $i < $length; ++$i) {
						?><td><?php
						echo(xml_escape(substr($solution, $i, 1)));
						?></td><?php
					}
					?></tr><?php
					?></table><?php
				}
				echo("\n");
			}
			?></div><?php
		}
		?></div><?php
	}

	private $m_crossword;
	private $m_edit;
}

?>
