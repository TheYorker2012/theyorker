<?php

/**
 * This is the controller for the sport section.
 *
 *@authot Owen Jones (oj502@york.ac.uk)
*Code stolen from news section --
 * @author Chris Travis	(cdt502 - ctravis@gmail.com)
 */

class Sport extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('News_model');
		$this->load->model('Home_Hack_Model');
		$this->load->model('Home_Model');
	}
	
	function index()
	{
		// Load public view
		if (!CheckPermissions('public')) return;

		$type_info = $this->News_model->getArticleTypeInformation('sport');
		$this->pages_model->SetPageCode('sport');

		$data['latest_heading'] = $this->pages_model->GetPropertyText('latest_heading');
		$data['more_heading'] = $this->pages_model->GetPropertyText('more_heading');
		$data['links_heading'] = $this->pages_model->GetPropertyText('links_heading');

		//Obtain banner
		$data['banner'] = $this->Home_Model->GetBannerImage();
		
		//Get sport ids (main category)
		$latest_sport_ids = $this->News_model->GetLatestId('sport',3);
		//MAIN HEAD fixed one general sport with summery, and two simples
		$main_sport_previews[0] = $this->News_model->GetSummaryArticle($latest_sport_ids[0], "Left", '%W, %D %M %Y', "medium");
		/// Get some of the 2nd- and 3rd-latest articles
		for ($index = 1; $index <= 2 && $index < count($latest_sport_ids); $index++) {
			array_push($main_sport_previews, $this->News_model->GetSimpleArticle($latest_sport_ids[$index], "Left"));
		}
		
		
		$show_sports_data = $this->News_model->getSubArticleTypes('sport');
		$more_sports_num = (int)$this->pages_model->GetPropertyText('max_num_more_sport_articles');//Max number of more sports to show, TODO get from page properties
		
		$sport_index = 0;//index for going through each sport
		$sports_previews = array();//Where the simple articles will go
		
		//////For each sport get simple articles
		foreach ( $show_sports_data as $a_sport){
			//Get article id's for that sport up to limit of $more_sports_num
			$sports_ids[$sport_index] = $this->News_model->GetLatestId($a_sport['codename'],$more_sports_num);
				//for the new sport found get a simple article for each of the ids found.
				for ($index = 0; $index <= ($more_sports_num-1) && $index < count($sports_ids[$sport_index]); $index++) {
					$sports_previews[$sport_index][] = $this->News_model->GetSimpleArticle($sports_ids[$sport_index][$index], "Left");
				}
			$sport_index++;
		}
		
		//Move previews into send data
		$data['show_sports'] = $show_sports_data;
		$data['sports'] = $sports_previews;
		$data['main_sport'] = $main_sport_previews;
		
		// Set up the public frame
		$this->main_frame->SetContentSimple('sport/index', $data);
		$this->main_frame->SetExtraCss('/stylesheets/home.css');
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

}
?>
