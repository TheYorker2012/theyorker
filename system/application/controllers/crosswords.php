<?php

/** Displays and manages crossword puzzles
 */
class Crosswords extends Controller
{
	function __construct()
	{
		parent::Controller();

		$this->load->helper('crosswords');
		$this->load->model('crosswords_model');
	}

	function _remap($arg = null)
	{
		if (null === $arg) {
			return $this->index();
		}
		$args = func_get_args();

		// First argument is one of:
		// "tips"
		if ('tips' === $arg) {
			return call_user_func_array(array(&$this, 'tips'), $args);
		}
		// crossword id
		if (is_numeric($arg)) {
			return call_user_func_array(array(&$this, 'crossword_by_id'), $args);
		}
		// date
		if (false) {
			return call_user_func_array(array(&$this, 'crossword_by_date'), $args);
		}
		// category short name
		return call_user_func_array(array(&$this, 'category'), $args);
	}

	function index()
	{
		if (!CheckPermissions('public')) return;

		// Load categories
		$categories = $this->crosswords_model->GetAllCategories();
		foreach ($categories as &$category) {
			// And information about the latest few crosswords
			$category['latest']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,true,null, 3,'DESC');
			$category['next']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,false,null, 1,'ASC');
		}
		$data = array(
			'Categories' => &$categories,
		);
		$this->main_frame->IncludeCss('stylesheets/crosswords_index.css');
		$this->main_frame->SetContentSimple('crosswords/index', $data);
		$this->main_frame->Load();
	}

	function category($cat = null, $arg2 = null)
	{
		$category = $this->crosswords_model->GetCategoryByShortName($cat);
		if (null === $category) {
			show_404();
		}

		if (!CheckPermissions('public')) return;

		if (null === $arg2) {
			$category['latest']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,true,null, 5,'DESC');
			$category['next']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,false,null, 1,'ASC');

			$data = array(
				'Category' => &$category,
			);
			$this->main_frame->IncludeCss('stylesheets/crosswords_index.css');
			$this->main_frame->SetContentSimple('crosswords/category', $data);
		}
		elseif ($arg2 == 'archive') {
			$category['latest']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,true,null, null,'DESC');
			$category['next']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,false,null, 1,'ASC');

			$data = array(
				'Category' => &$category,
			);
			$this->main_frame->IncludeCss('stylesheets/crosswords_index.css');
			$this->main_frame->SetContentSimple('crosswords/category', $data);
		}
		else {
			show_404();
		}

		$this->main_frame->Load();
	}

	function crossword_by_id($id = null)
	{
		if (!CheckPermissions('public')) return;

		if (null === $id || !is_numeric($id)) {
			show_404();
		}
		$id = (int)$id;

		$crossword = $this->crosswords_model->GetCrosswords($id,null, null,true,null);
		if (count($crossword) == 0) {
			show_404();
		}
		$crossword = $crossword[0];

		$puzzle = 0;
		$worked = $this->crosswords_model->LoadCrossword($crossword['id'], $puzzle);
		if (!$worked) {
			show_404();
		}
		$crosswordView = new CrosswordView($puzzle);

		$data = array();
		$data['Crossword'] = &$crossword;
		$data['Grid'] = &$crosswordView;

		$this->main_frame->includeCss('stylesheets/crosswords.css');
		$this->main_frame->includeJs('javascript/simple_ajax.js');
		$this->main_frame->includeJs('javascript/crosswords.js');
		$this->main_frame->SetContentSimple('crosswords/crossword', $data);
		$this->main_frame->Load();
	}

	function crossword_by_date($date = null)
	{
		if (!CheckPermissions('public')) return;
		$this->main_frame->Load();
	}

	function tips()
	{
		if (!CheckPermissions('public')) return;
		$this->main_frame->Load();
	}
}

?>
