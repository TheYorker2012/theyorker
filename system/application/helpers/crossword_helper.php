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

/// A slot in the grid.
class CrosswordGridSlot
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

	/// Coordinates of slot.
	private $m_x, $m_y;

	/// Orientation of slot.
	private $m_orientation;

	/// Length of slot.
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
		$this->m_slots = array(self::$HORIZONTAL => array(),
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

	/** Add a slot to the grid.
	 * @returns true if successful, false otherwise.
	 */
	function addSlot($slot)
	{
		$this->m_slots[$slot->orientation()][$slot->x()][$slot->y()] = &$slot;
		ksort($this->m_slots[$slot->orientation()]);
		ksort($this->m_slots[$slot->orientation()][$slot->x()]);
		$dx = $slot->dx();
		$dy = $slot->dy();
		for ($i = 0; $i < $slot->length(); ++$i) {
			$this->setCellState($slot->x()+($i*$dx),
					$slot->y()+($i*$dy), "", false);
		}
		return true;
	}

	/// Find whether a slot is unused.
	function slotUnused($slot)
	{
		$dx = $slot->dx();
		$dy = $slot->dy();
		for ($i = 0; $i < $slot->length(); ++$i) {
			if (is_string($this->cellState($slot->x()+($i*$dx),
			                               $slot->y()+($i*$dy)))) {
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
	 * @param $onlyIfSlot bool Prevents change if coordinates specify a blank.
	 * @returns true on success, false otherwise.
	 */
	function setCellState($x, $y, $state, $onlyIfSlot = true)
	{
		if ($x >= 0 && $y >= 0 &&
		    $x < $this->m_width && $y < $this->m_height &&
		    (!$onlyIfSlot || $this->m_grid[$x][$y] !== null)
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
	 * @param $slot Slot Slot description object.
	 * @param $text string text.
	 * @returns Number of cells changed.
	 */
	function setSlotText($slot, $text)
	{
		$len = strlen($text);
		$dx = $slot->dx();
		$dy = $slot->dy();
		for ($i = 0; $i < $len; ++$i) {
			if (!$this->setCellState($slot->x()+($i*$dx),
			                         $slot->y()+($i*$dy), substr($text, $i, 1), true)) {
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

	/** Get the list of slots overlapping a cell.
	 * @returns array[Slot]
	 */
	function slotsAt($x, $y, $immediate = true)
	{
		$results = array();
		// Horizontal
		if ($x < $this->m_width) {
			$i = $x;
			while ($i >= 0 && null !== $this->m_grid[$i][$y]) {
				if (isset($this->m_slots[self::$HORIZONTAL][$i][$y])) {
					$results[] = &$this->m_slots[self::$HORIZONTAL][$i][$y];
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
				if (isset($this->m_slots[self::$VERTICAL][$x][$i])) {
					$results[] = &$this->m_slots[self::$VERTICAL][$x][$i];
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

	/// Slots indexed by orientation, X, Y coordinate.
	private $m_slots;

	/// array[int x=>array[int y=>{null,string}]]
	private $m_grid;
}

class CrosswordPuzzleSlot extends CrosswordGridSlot
{
	function __construct($x, $y, $orientation, &$clue)
	{
		parent::__construct($x, $y, $orientation, $clue->length());
		$this->m_clue = &$clue;
	}

	/*
	 * Accessors
	 */
	
	/// Get the clue in this slot.
	function clue()
	{
		return $this->m_clue;
	}

	/*
	 * Private variables
	 */

	/// The clue in this slot.
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

	/** Add a slot to the grid.
	 */
	function addSlot($x, $y, $orientation, &$clue)
	{
		$slot = new CrosswordPuzzleSlot($x, $y, $orientation, $clue);
		$this->m_grid->addSlot($slot);
		$this->m_grid->setSlotText($slot, $clue->solution());
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
	function __construct(&$crossword)
	{
		$this->m_crossword = $crossword;
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
		// Render main crossword grid
		?><table class="crossword"><?php
		echo("\n");
		for ($y = 0; $y < $height; ++$y) {
			?><tr><?php
			for ($x = 0; $x < $width; ++$x) {
				$state = $grid->cellState($x, $y);
				$used = is_string($state);
				if ($used) {
					?><td id="<?php echo("$name-$x-$y"); ?>" <?php
						?>onclick="return crosswordClick(<?php echo("'$name', $x, $y, event") ?>)"><div><?php

					// Clue number
					$slots = $grid->slotsAt($x, $y, true);
					if (!empty($slots)) {
						++$clueNumber;
						foreach ($slots as &$slot) {
							$clues[$slot->orientation()][$clueNumber] = array($slot->clue(), $x, $y);
						}
						?><sup><?php echo($clueNumber); ?></sup><?
					}
					// Text input box
					?><input type="text" cols="1" maxlength="2" <?php
					       ?>id="<?php echo("$name-edit-$x-$y"); ?>" <?php
					       ?>onkeydown="return crosswordKeyDown(<?php echo("'$name',$x,$y,event") ?>)" <?php
					       ?>onkeypress="return crosswordKeyPress(<?php echo("'$name',$x,$y,event") ?>)" <?php
					       ?>value="<?php
							//echo(xml_escape($state));
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
		// <temporary>
		// progress bar
		?><div style="border: 1px solid #ff6a00; margin-top: 0.2em;"><?php
			?><div style="background-color: #ffaa00; width: 34%; color: white; text-align: right; padding: 2px;"><?php
				?>34%<?php
			?></div>10<?php
		?></div><?php
		?><div><?php
			?><input type="button" class="button" value="Reveal Clue" disabled="disabled" /><?php
			?><input type="button" class="button" value="Check Clue" /><?php
		?></div><?php
		?><br /><br /><?php
		?><div><?php
			?><input type="button" class="button" value="Check Finished Crossword" disabled="disabled" /><?php
			?><input type="button" class="button" value="Save Crossword for Later" /><?php
		?></div><?php
		// </temporary>
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
					?>onclick="crosswordSelectSlot(<?php echo("'$name', $x, $y, $orientation, event"); ?>)"><?php
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
}

?>
