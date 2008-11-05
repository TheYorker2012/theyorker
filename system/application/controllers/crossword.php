<?php

/** Displays and manages crossword puzzles
 */
class Crossword extends Controller
{
	function __construct()
	{
		parent::Controller();

		$this->load->helper("crossword");
	}

	function index()
	{
		if (!CheckPermissions('admin')) return;

		$crossword = new CrosswordPuzzle(13, 13);
		// Across
		$crossword->addSlot(1, 0, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Ordered pile", "ababa"));
		$crossword->addSlot(7, 0, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Abyss", "ababa"));
		$crossword->addSlot(0, 2, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Applaud with shouts", "ababa"));
		$crossword->addSlot(6, 2, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Destroy completely", "aba baba"));
		$crossword->addSlot(0, 4, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Small frozen blocks", "aba babab"));
		$crossword->addSlot(9, 4, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Slight quarrel", "abab"));
		$crossword->addSlot(0, 6, CrosswordGrid::$HORIZONTAL, new CrosswordClue("One who damages property", "vandal"));
		$crossword->addSlot(7, 6, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Japanese garment", "ababab"));
		$crossword->addSlot(0, 8, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Drinking vessels", "abab"));
		$crossword->addSlot(5, 8, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Moving to pity", "abababab"));
		$crossword->addSlot(0, 10, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Knitting rods", "needles"));
		$crossword->addSlot(8, 10, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Summarise", "ababa"));
		$crossword->addSlot(1, 12, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Book of maps", "atlas"));
		$crossword->addSlot(7, 12, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Coil, spin", "ababa"));
		// Down
		$crossword->addSlot(2, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Topic", "ababa"));
		$crossword->addSlot(4, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Widely distributed notice", "abababab"));
		$crossword->addSlot(8, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Beer incredient", "abab"));
		$crossword->addSlot(10, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Star sign", "abababa"));
		$crossword->addSlot(0, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("Accomplishment", "abababababa"));
		$crossword->addSlot(6, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("Sugary", "ababa"));
		$crossword->addSlot(12, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("Medical instrument", "abababababa"));
		$crossword->addSlot(8, 5, CrosswordGrid::$VERTICAL, new CrosswordClue("Remove or take away", "abababab"));
		$crossword->addSlot(2, 6, CrosswordGrid::$VERTICAL, new CrosswordClue("Negligence", "abababa"));
		$crossword->addSlot(6, 7, CrosswordGrid::$VERTICAL, new CrosswordClue("Light wood", "ababa"));
		$crossword->addSlot(10, 8, CrosswordGrid::$VERTICAL, new CrosswordClue("Carpet nails", "ababa"));
		$crossword->addSlot(4, 9, CrosswordGrid::$VERTICAL, new CrosswordClue("Jumping insect", "abab"));

		$crosswordView = new CrosswordView($crossword);

		$data = array();
		$data['crossword'] = $crosswordView;

		$this->main_frame->SetContentSimple('crossword/index', $data);
		$this->main_frame->includeCss('stylesheets/crosswords.css');
		$this->main_frame->includeJs('javascript/crossword.js');
		$this->main_frame->Load();
	}

}

?>
