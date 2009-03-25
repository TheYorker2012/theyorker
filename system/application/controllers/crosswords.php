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
		$crossword->addLight(1, 0, CrosswordGrid::$HORIZONTAL, new CrosswordClue("stack", "Ordered pile", ''));
		$crossword->addLight(7, 0, CrosswordGrid::$HORIZONTAL, new CrosswordClue("chasm", "Abyss", ''));
		$crossword->addLight(0, 2, CrosswordGrid::$HORIZONTAL, new CrosswordClue("cheer", "Applaud with shouts", ''));
		$crossword->addLight(6, 2, CrosswordGrid::$HORIZONTAL, new CrosswordClue("wipe out", "Destroy completely", ''));
		$crossword->addLight(0, 4, CrosswordGrid::$HORIZONTAL, new CrosswordClue("ice cubes", "Small frozen blocks", ''));
		$crossword->addLight(9, 4, CrosswordGrid::$HORIZONTAL, new CrosswordClue("spat", "Slight quarrel", ''));
		$crossword->addLight(0, 6, CrosswordGrid::$HORIZONTAL, new CrosswordClue("vandal", "One who damages property", ''));
		$crossword->addLight(7, 6, CrosswordGrid::$HORIZONTAL, new CrosswordClue("kimono", "Japanese garment", ''));
		$crossword->addLight(0, 8, CrosswordGrid::$HORIZONTAL, new CrosswordClue("mugs", "Drinking vessels", ''));
		$crossword->addLight(5, 8, CrosswordGrid::$HORIZONTAL, new CrosswordClue("pathetic", "Moving to pity", ''));
		$crossword->addLight(0, 10, CrosswordGrid::$HORIZONTAL, new CrosswordClue("needles", "Knitting rods", ''));
		$crossword->addLight(8, 10, CrosswordGrid::$HORIZONTAL, new CrosswordClue("recap", "Summarise", ''));
		$crossword->addLight(1, 12, CrosswordGrid::$HORIZONTAL, new CrosswordClue("atlas", "Book of maps", ''));
		$crossword->addLight(7, 12, CrosswordGrid::$HORIZONTAL, new CrosswordClue("twist", "Coil, spin", ''));
		// Down
		$crossword->addLight(2, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("theme", "Topic", ''));
		$crossword->addLight(4, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("circular", "Widely distributed notice", ''));
		$crossword->addLight(8, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("hops", "Beer incredient", ''));
		$crossword->addLight(10, 0, CrosswordGrid::$VERTICAL, new CrosswordClue("scorpio", "Star sign", ''));
		$crossword->addLight(0, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("achievement", "Accomplishment", ''));
		$crossword->addLight(6, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("sweet", "Sugary", ''));
		$crossword->addLight(12, 1, CrosswordGrid::$VERTICAL, new CrosswordClue("stethoscope", "Medical instrument", ''));
		$crossword->addLight(8, 5, CrosswordGrid::$VERTICAL, new CrosswordClue("withdraw", "Remove or take away", ''));
		$crossword->addLight(2, 6, CrosswordGrid::$VERTICAL, new CrosswordClue("neglect", "Negligence", ''));
		$crossword->addLight(6, 7, CrosswordGrid::$VERTICAL, new CrosswordClue("balsa", "Light wood", ''));
		$crossword->addLight(10, 8, CrosswordGrid::$VERTICAL, new CrosswordClue("tacks", "Carpet nails", ''));
		$crossword->addLight(4, 9, CrosswordGrid::$VERTICAL, new CrosswordClue("flea", "Jumping insect", ''));

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
