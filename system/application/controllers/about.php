<?php

class About extends Controller
{

	// Variable declarations
	var $navbar;

	function __construct()
	{
		parent::Controller();
		$this->load->library('image');
	}

	function _navbar_options ($selected = 'main')
	{
		// Create navigation menu
		$this->navbar = $this->main_frame->GetNavbar();
		$this->navbar->AddItem('main', 'Aims', '/about/');
		$this->navbar->AddItem('directors', 'Directors', '/about/directors/');
		$this->navbar->AddItem('people', 'The Team', '/about/theteam/');
		$this->navbar->SetSelected($selected);
	}

	/// Main page
	function index()
	{
		if (!CheckPermissions('public')) return;

		$this->pages_model->SetPageCode('about_us');
		// Get the blocks array from page properties.
		$blocks = $this->pages_model->GetPropertyArray('blocks', array(
			// First index is [int]
			array('pre' => '[', 'post' => ']', 'type' => 'int'),
			// Second index is .string
			array('pre' => '.', 'type' => 'enum',
				'enum' => array(
					array('title',	'text'),
					array('blurb',	'wikitext'),
					array('image',	'text'),
				),
			),
		));
		if (FALSE === $blocks) {
			$blocks = array();
		}
		
		// Create data array.
		$data = array();
		$data['textblocks'] = array();

		// Process page properties.
		foreach ($blocks as $key => $block) {
			$curdata = array();
			$curdata['shorttitle'] = str_replace(' ','_',$block['title']);
			$curdata['blurb'] = $block['blurb'];
			if (array_key_exists('image', $block)) {
				$curdata['image'] = $this->image->getThumb($block['image'], 'medium');
			} else {
				$curdata['image'] = null;
			}
			$data['textblocks'][] = $curdata;
		}
		
		// Load the main frame
		$this->_navbar_options('main');
		$this->main_frame->SetContentSimple('about/about', $data);
		$this->main_frame->Load();
	}

	function directors ()
	{
		if (!CheckPermissions('public')) return;

		$this->pages_model->SetPageCode('about_us_directors');
		// Get the blocks array from page properties.
		$blocks = $this->pages_model->GetPropertyArray('blocks', array(
			// First index is [int]
			array('pre' => '[', 'post' => ']', 'type' => 'int'),
			// Second index is .string
			array('pre' => '.', 'type' => 'enum',
				'enum' => array(
					array('title',	'text'),
					array('blurb',	'wikitext'),
					array('image',	'text'),
				),
			),
		));
		if (FALSE === $blocks) {
			$blocks = array();
		}
		
		// Create data array.
		$data = array();
		$data['textblocks'] = array();

		// Process page properties.
		foreach ($blocks as $key => $block) {
			$curdata = array();
			$curdata['shorttitle'] = str_replace(' ','_',$block['title']);
			$curdata['blurb'] = $block['blurb'];
			if (array_key_exists('image', $block)) {
				$curdata['image'] = $this->image->getThumb($block['image'], 'medium');
			} else {
				$curdata['image'] = null;
			}
			$data['textblocks'][] = $curdata;
		}
		
		// Load the main frame
		$this->_navbar_options('directors');
		$this->main_frame->SetContentSimple('about/about', $data);
		$this->main_frame->Load();
	}

	function theteam ($team_id = NULL, $year = NULL)
	{
		if (!CheckPermissions('public')) return;

		// Load model that deals with bylines
		$this->load->model('businesscards_model');

		$data = array();
		$this->pages_model->SetPageCode('about_us_people');
		$data['intro_heading'] = $this->pages_model->GetPropertyText('heading');
		$data['intro_text'] = $this->pages_model->GetPropertyWikitext('intro');
		$data['first_year'] = 2006;
		$data['last_year'] = (date('m') < 10) ? date('Y')-1 : date('Y');
		$data['selected_team'] = ((is_numeric($team_id)) && (count($this->businesscards_model->BylineTeamInfo($team_id)) > 0)) ? $team_id : 0;
		$data['selected_year'] = (($year == NULL) || ($year < $data['first_year']) || ($year > $data['last_year'])) ? $data['last_year'] : $year;
		$data['byline_teams'] = $this->businesscards_model->GetBylineTeams($data['selected_year']);

		// Get bylines for team
		if ($data['selected_team'] != 0) {
			$data['bylines'] = $this->businesscards_model->GetTeamBylines($data['selected_team'], $data['selected_year']);
			// Get byline photos
			foreach ($data['bylines'] as &$byline) {
				if ($byline['business_card_image_id'] === NULL) {
					$byline['business_card_image_href'] = '';
				} else {
					$byline['business_card_image_href'] = $this->image->getPhotoURL($byline['business_card_image_id'], 'userimage');
				}
			}
		}

		// Load the main frame
		$this->_navbar_options('people');
		$this->main_frame->SetContentSimple('about/people', $data);
		$this->main_frame->Load();
	}

}

?>
