<?php

/// Main review controller
/**
 * @author Frank Burton
 *@improvements Owen Jones (oj502)
 */
class Reviews extends Controller
{
	/// Valid content types
	protected static $mContentType = array(
		'food','drink','culture','barcrawl'
	);


	/// Default constructor
	function Reviews()
	{
		parent::Controller();

		//Load Helper Functions so we can return dynamic url's
		//And possible forms later on for the admin pages
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('image');
		$this->load->library('Homepage_boxes');

		//Load page model
		$this->load->model('Home_Model');

		//Load reviews model
		$this->load->model('Review_model');
	}

	/// Remap function ALWAYS CALLED
	function _remap()
	{
		$method			= $this->uri->rsegment(2);
		$param_start	= 2;

		if (FALSE === $method) {
			return $this->index();
		}

		if (FALSE !== array_search($method, self::$mContentType)) {
			$content_type	= $method;
			$param_start	= 1;

			// valid content type
			$organisation_name	= $this->uri->rsegment(3);

			if (FALSE === $organisation_name) {
				$method = '_main';

			} else {
				$method = '_review';
			}
		}
 
		call_user_func_array(array(&$this, $method), array_slice($this->uri->rsegment_array(), $param_start));
	}

	/// Main page
	/**
	 * @note This just redirects to food reviews.
	 */
	function index()
	{
		redirect('/reviews/food'); //Send them to the food page instead
	}
	
	//Frontpage is now a special version of the table.
	function _main($content_type)
	{
		$this->table($content_type,'star');
	}
	
	/// Review page
	function _review($content_type, $organisation_name, $IncludedComment = 0)
	{
		if (!CheckPermissions('public')) return;

		$this->load->library('review_views');
		$this->review_views->DisplayReview($content_type, $organisation_name, $IncludedComment);

		$this->main_frame->Load();
	}


	/// Display table for review table (from puffers)
	function table(	$content_type = FALSE,
					$sorted_by = FALSE,
					$item_filter_by = FALSE,
					$where_equal_to = FALSE)
	{
		////////////LOAD MODELS
		$this->load->model('slideshow');

		//POST data set overwrites uri data
		if (isset($_POST['content_type'])) $content_type = $_POST['content_type'];
		if (isset($_POST['sorted_by'])) $sorted_by = $_POST['sorted_by'];
		if (isset($_POST['item_filter_by'])) $item_filter_by = $_POST['item_filter_by'];
		if (isset($_POST['where_equal_to'])) $where_equal_to = $_POST['where_equal_to'];

		//For next page so we remember the options given
		$data['content_type'] = $content_type;
		$data['sorted_by'] = $sorted_by;
		$data['item_filter_by'] = $item_filter_by;
		$data['where_equal_to'] = $where_equal_to;

		if (!CheckPermissions('public')) return;
		//Obtain banner for homepage
		$data['banner'] = $this->Home_Model->GetBannerImageForHomepage($content_type);
		
		//Set page code
		$this->pages_model->SetPageCode('review_main');
		//@TODO use the nice db held name rather than the content_type
		$this->main_frame->SetTitleParameters(array(
			'content_type' => $content_type
		));
		$data['page_header'] = $this->pages_model->GetPropertyText('header_'.$content_type);
		$data['main_review_header'] = $this->pages_model->GetPropertyText('main_review_header');
		$data['leagues_header'] = $this->pages_model->GetPropertyText('leagues_header');
		$data['page_about'] = $this->pages_model->GetPropertyWikiText('about_'.$content_type);
		
		///////////GET LEAGUES
		$league_data = $this->Review_model->GetLeagueDetails($content_type);
		$leagues = array();
		foreach ($league_data as &$league)
		{
			if (empty($league['image_id'])) {
				$has_image = false;
				$image_path = '';
			}
			else {
				$has_image = true;
				$image_path = '/image/'.$league['image_type_codename'].'/'.$league['image_id'];
			}
			$leagues[] = array(
				'has_image' => $has_image,
				'image_path'=> $image_path,
				'league_name'=>$league['league_name'],
				'league_size'=>$league['league_size'],
				'league_codename'=>$league['league_codename']
				);
		}
		//Pass tabledata straight to view it is in the proper format
		$data['league_data'] = $leagues;
		
		//GET REVIEWS FOR TABLE
		$database_result = $this->Review_model->GetTableReview($content_type,$sorted_by, $item_filter_by,$where_equal_to);

		//Get data for the links to the table page
		$tabledata = $this->Review_model->GetTags($content_type);

		//Pass tabledata straight to view it is in the proper format
		$data['table_data'] = $tabledata;

		//Incase of null result
		if ($database_result[0]['tag_groups'] == 'empty')
		{
			$data['entries'] = array();
		}
		else
		{ //Normal Case

			//A list of all tags
			$data['review_tags'] = $database_result[0]['tag_groups'];

			$this->load->library('image');
			$entries = array();//Array that the table reviews are loaded into
			
			//For each row in the table
			for($reviewno = 0; $reviewno < count($database_result); $reviewno++)
			{
				if (isset($database_result[$reviewno]['organisation_name']))
				{
					//surely this should be in the model
					$entries[$reviewno]['review_title'] = $database_result[$reviewno]['organisation_name'];
					$entries[$reviewno]['review_id'] = $database_result[$reviewno]['organisation_entity_id'];
					$entries[$reviewno]['review_rating'] = $database_result[$reviewno]['review_context_content_rating'];
					$entries[$reviewno]['review_blurb'] = $database_result[$reviewno]['review_context_content_blurb'];
					$entries[$reviewno]['review_table_link'] = base_url().'reviews/'.$content_type.'/'.$database_result[$reviewno]['organisation_directory_entry_name'];

					//get the slideshow images for the review item
					$first_photo_id = $this->slideshow->getFirstPhotoIDFromSlideShow($database_result[$reviewno]['organisation_directory_entry_name']);
					if(!empty($first_photo_id))
					{
						$entries[$reviewno]['review_image'] = $this->image->getThumb($first_photo_id, 'small');
					}

					//Change scope of $tagbox
					$tagbox = array();

					//Tags work as a array within a array, which is just confusing!
					for($tagno = 0; $tagno < count($data['review_tags']); $tagno++)
					{
						$tag_group_name = $data['review_tags'][$tagno];

						//Pass only if it exists for this organisation
						if (isset($database_result[$reviewno]['tags'][$tag_group_name]))
						{
							$tagbox[$data['review_tags'][$tagno]] = $database_result[$reviewno]['tags'][$tag_group_name];
						}
					}
					$entries[$reviewno]['tagbox'] = $tagbox;
				}
			}
			$data['entries'] = $entries;
		}
		
		//Load extra css and frame
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->IncludeCss('stylesheets/reviews.css');
		$this->main_frame->SetContentSimple('reviews/table',$data);
		$this->main_frame->Load();
	}

	/// Leagues
	function leagues($league_code_name = NULL)
	{
		if (!CheckPermissions('public')) return;

		//Set page code
		$this->pages_model->SetPageCode('review_league');
		$data['leagues_header'] = $this->pages_model->GetPropertyText('leagues_header');
		$data['empty_league'] = $this->pages_model->GetPropertyWikiText('empty_league');
		
		//Load slideshow model
		$this->load->model('slideshow');

		//Load review  model
		$this->load->model('review_model');

		//Check we have being passed a league to view otherwise the query returns badly...
		if ($league_code_name === NULL) redirect('/reviews'); //It doesn't matter if the code below is executed or not...

		//Find out the content_type
		$content_type = $this->Review_model->GetLeagueType($league_code_name);
		if(empty($content_type)){show_404();}
		
		//Get leagues from model
		$leagues = $this->Review_model->GetLeague($league_code_name);

		//Check for if zero
		if (!empty($leagues))
		{
			//Set name of league
			$data['league_name'] = $leagues[0]['league_name']; //They should all be from the same league
			//Place remaining data into a array for the view
			for ($row = 0; $row < count($leagues); $row++)
			{
				$reviews[$row]['review_org_id'] = $leagues[$row]['organisation_id'];
				$reviews[$row]['review_title'] = $leagues[$row]['organisation_name'];
				$reviews[$row]['review_website'] = $leagues[$row]['organisation_url'];
				$reviews[$row]['review_rating'] = $leagues[$row]['review_rating'];
				$reviews[$row]['review_quote'] = $leagues[$row]['review_quote'];
				$reviews[$row]['review_user_rating'] = $leagues[$row]['average_user_rating'];
				//This will need the use of a function which returns what a organisition has being reviews on
				$reviews[$row]['review_link'] = '/reviews/'.$content_type.'/'.$leagues[$row]['organisation_directory_entry_name'];
				$reviews[$row]['review_blurb'] = $leagues[$row]['organisation_description'];
				$reviews[$row]['review_title'] = $leagues[$row]['organisation_name'];
				$reviews[$row]['review_content_type_id'] = $leagues[$row]['league_content_type_id'];
				$reviews[$row]['review_org_directory_entry_name'] = $leagues[$row]['organisation_directory_entry_name'];


				//get the slideshow image for the league item
				$reviews[$row]['image'] = $this->slideshow->getFirstPhotoIDFromSlideShow($leagues[$row]['organisation_directory_entry_name']);

				//very hacky two lines here:
				$content_codename = $this->review_model->ContentTypeIDToCodename($reviews[$row]['review_content_type_id']);
				$reviews[$row]['tags'] = $this->review_model->GetTagOrganisation($content_codename,$reviews[$row]['review_org_directory_entry_name']);
				$reviews[$row]['alltags'] = $this->review_model->GetTags('food');
			}

		//Pass over the amount of entries to view
		$data['max_entries'] = $row;
		$data['reviews'] = $reviews;
		}
		else
		{	//No rows returned
			$data['max_entries'] = 0;
			//Dont have nice title because there are no leagues, so force get it.
			$data['league_name'] = $this->review_model->GetLeagueNiceName($league_code_name);
		}

		//Have nice title, use it.
		$this->main_frame->SetTitleParameters(array(
			'section_name' => ucfirst($content_type),
			'league_name' => $data['league_name']
		));
		
		//Get league data
		$league_data = $this->Review_model->GetLeagueDetails($content_type);
		$leagues = array();
		foreach ($league_data as &$league)
		{
			if (empty($league['image_id'])) {
				$has_image = false;
				$image_path = '';
			}
			else {
				$has_image = true;
				$image_path = '/image/'.$league['image_type_codename'].'/'.$league['image_id'];
			}
			$leagues[] = array(
				'has_image' => $has_image,
				'image_path'=> $image_path,
				'league_name'=>$league['league_name'],
				'league_size'=>$league['league_size'],
				'league_codename'=>$league['league_codename']
				);
		}

		//Pass tabledata straight to view it is in the proper format
		$data['content_type'] = $content_type;
		$data['league_data'] = $leagues;


		$this->main_frame->SetContentSimple('reviews/leagues',$data);
		$this->main_frame->Load();
	}
}

?>
