<?php
/**
 *  @file games.php
 *  @author David Garbett (dg516)
 *  Contains public site controller class
 */

class Games extends Controller {

	function __construct()
	{
		parent::Controller();
		/// Used in all all page requests
		$this->load->model('games_model','games_model');
	}
	
	/**
	 *  @brief Gets list of activated games
	 */
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('games');
		
		$data['section_games'] = array (
			'title' 	=> $this->pages_model->GetPropertyText('section_games_title'),
			'text'		=> $this->pages_model->GetPropertyWikiText('section_games_text'),
			'footer'	=> $this->pages_model->GetPropertyWikiText('section_games_footer'));
		
		$this->load->library('image');
		/// Change False to true in following line when play counts higher
		foreach ($this->games_model->GetGamesList(False) as $game_id =>$game) 
		{
			// Get image xhtml
			$data['games'][$game_id]['image'] =
				$this->image->getImage($game['image_id'], 'gamethumb',  array('title' => $game['title']));
		}
				
		// Set up the public frame
		$this->main_frame->SetContentSimple('games/games', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	/**
	 *  @brief Displays a game
	 */
	function view($game_id)
	{
		if (!CheckPermissions('public')) return;
		$this->pages_model->SetPageCode('games');
		$data['game'] = $this->games_model->GetGame($game_id);
		/// Don't allow a disactivated game to be viewed - improve to be based on Permissions?
		if ($data['game'] == 0 OR !$data['game']['activated'])
		{
			show_404();
		}
		$data['game']['filename'] = $this->config->item('static_web_address').'/games/'.$data['game']['filename'];
		$this->main_frame->SetContentSimple('games/view',$data);
		$this->main_frame->Load();
	}
}
?>
