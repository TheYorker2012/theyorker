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
		$crossword->addSlot(1, 0, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Ordered pile", "stack"));
		$crossword->addSlot(7, 0, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Abyss", "chasm"));
		$crossword->addSlot(0, 2, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Applaud with shouts", "cheer"));
		$crossword->addSlot(6, 2, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Destroy completely", "wipeout"));
		$crossword->addSlot(0, 4, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Small frozen blocks", "ice cubes"));
		$crossword->addSlot(9, 4, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Slight quarrel", "spat"));
		$crossword->addSlot(0, 6, CrosswordGrid::$HORIZONTAL, new CrosswordClue("One who damages property", "vandal"));
		$crossword->addSlot(7, 6, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Japanese garment", "kimono"));
		$crossword->addSlot(0, 8, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Drinking vessels", "mugs"));
		$crossword->addSlot(5, 8, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Moving to pity", "pathetic"));
		$crossword->addSlot(0, 10, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Knitting rods", "needles"));
		$crossword->addSlot(8, 10, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Summarise", "recap"));
		$crossword->addSlot(1, 12, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Book of maps", "atlas"));
		$crossword->addSlot(7, 12, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Coil, spin", "twist"));
		// Down
		$crossword->addSlot(2, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Topic", "theme"));
		$crossword->addSlot(4, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Widely distributed notice", "circular"));
		$crossword->addSlot(8, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Beer incredient", "hops"));
		$crossword->addSlot(10, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Star sign", "scorpio"));
		$crossword->addSlot(0, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("Accomplishment", "achievement"));
		$crossword->addSlot(6, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("Sugary", "sweet"));
		$crossword->addSlot(12, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("Medical instrument", "stethoscope"));
		$crossword->addSlot(8, 5, CrosswordGrid::$VERTICAL, new CrosswordClue("Remove or take away", "withdraw"));
		$crossword->addSlot(2, 6, CrosswordGrid::$VERTICAL, new CrosswordClue("Negligence", "neglect"));
		$crossword->addSlot(6, 7, CrosswordGrid::$VERTICAL, new CrosswordClue("Light wood", "balsa"));
		$crossword->addSlot(10, 8, CrosswordGrid::$VERTICAL, new CrosswordClue("Carpet nails", "tacks"));
		$crossword->addSlot(4, 9, CrosswordGrid::$VERTICAL, new CrosswordClue("Jumping insect", "flea"));

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
