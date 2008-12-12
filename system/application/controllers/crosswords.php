<?php

/** Displays and manages crossword puzzles
 */
class Crosswords extends Controller
{
	function __construct()
	{
		parent::Controller();

		$this->load->helper('crosswords');
	}

	function index()
	{
		if (!CheckPermissions('admin')) return;
		redirect('crosswords/prototype');
	}

	function prototype()
	{
		if (!CheckPermissions('public')) return;

		$crossword = new CrosswordPuzzle(13, 13);
		// Across
		$crossword->addLight(1, 0, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Ordered pile", "stack"));
		$crossword->addLight(7, 0, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Abyss", "chasm"));
		$crossword->addLight(0, 2, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Applaud with shouts", "cheer"));
		$crossword->addLight(6, 2, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Destroy completely", "wipe out"));
		$crossword->addLight(0, 4, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Small frozen blocks", "ice cubes"));
		$crossword->addLight(9, 4, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Slight quarrel", "spat"));
		$crossword->addLight(0, 6, CrosswordGrid::$HORIZONTAL, new CrosswordClue("One who damages property", "vandal"));
		$crossword->addLight(7, 6, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Japanese garment", "kimono"));
		$crossword->addLight(0, 8, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Drinking vessels", "mugs"));
		$crossword->addLight(5, 8, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Moving to pity", "pathetic"));
		$crossword->addLight(0, 10, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Knitting rods", "needles"));
		$crossword->addLight(8, 10, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Summarise", "recap"));
		$crossword->addLight(1, 12, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Book of maps", "atlas"));
		$crossword->addLight(7, 12, CrosswordGrid::$HORIZONTAL, new CrosswordClue("Coil, spin", "twist"));
		// Down
		$crossword->addLight(2, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Topic", "theme"));
		$crossword->addLight(4, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Widely distributed notice", "circular"));
		$crossword->addLight(8, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Beer incredient", "hops"));
		$crossword->addLight(10, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("Star sign", "scorpio"));
		$crossword->addLight(0, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("Accomplishment", "achievement"));
		$crossword->addLight(6, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("Sugary", "sweet"));
		$crossword->addLight(12, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("Medical instrument", "stethoscope"));
		$crossword->addLight(8, 5, CrosswordGrid::$VERTICAL, new CrosswordClue("Remove or take away", "withdraw"));
		$crossword->addLight(2, 6, CrosswordGrid::$VERTICAL, new CrosswordClue("Negligence", "neglect"));
		$crossword->addLight(6, 7, CrosswordGrid::$VERTICAL, new CrosswordClue("Light wood", "balsa"));
		$crossword->addLight(10, 8, CrosswordGrid::$VERTICAL, new CrosswordClue("Carpet nails", "tacks"));
		$crossword->addLight(4, 9, CrosswordGrid::$VERTICAL, new CrosswordClue("Jumping insect", "flea"));

		$crosswordView = new CrosswordView($crossword);

		$data = array();
		$data['crossword'] = $crosswordView;

		$this->main_frame->SetContentSimple('crosswords/index', $data);
		$this->main_frame->includeCss('stylesheets/crosswords.css');
		$this->main_frame->includeJs('javascript/crosswords.js');
		$this->main_frame->Load();
	}

}

?>
