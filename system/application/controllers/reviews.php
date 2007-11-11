<?php

/// Main review controller
/**
 * @author Frank Burton
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

		//Load page model
		$this->load->model('pages_model');

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

	/// Main context frontpage
	function _main($content_type)
	{
		if (!CheckPermissions('public')) return;

		//Pass content_type to view

		$main_review = $this->Review_model->GetFrontPageReview($content_type);
		$data['content_type'] = $main_review['content_type_name'];

		//Set page code
		$this->pages_model->SetPageCode('review_main');

		$this->main_frame->SetTitleParameters(array(
			'content_type' => $data['content_type']
		));

		/// If there are no reviews for this particular section then show a page anyway
		if ($main_review != null) {
			$this->load->model('slideshow');
			$slideshow_array = $this->slideshow->getPhotos($main_review['organisation_entity_id']);
			$slideshow = array();

			$this->load->library('image');
			foreach ($slideshow_array->result() as $slide){
				$slideshow[] = array(
					'title' => $slide->photo_title,
					'id' => $slide->photo_id,
					'url' => $this->image->getPhotoURL($slide->photo_id, 'slideshow')
				);
			}
			$main_review['slideshow'] = $slideshow;
		}

		$data['main_review'] = $main_review;

		//Get data for the links to the table page
		$tabledata = $this->Review_model->GetTags($content_type);

		//Pass tabledata straight to view it is in the proper format
		$data['table_data'] = $tabledata;

		//Get league data
		$league_data = $this->Review_model->GetLeagueDetails($content_type);
		$leagues = array();
		foreach ($league_data as &$league)
		{
			$leagues[] = array(
				'league_image_path'=> '/images/puffer/'.$league['league_image_id'],
				'league_name'=>$league['league_name'],
				'league_size'=>$league['league_size'],
				'league_codename'=>$league['league_codename']
				);
		}

		//Pass tabledata straight to view it is in the proper format
		$data['league_data'] = $leagues;

		$this->main_frame->SetExtraHead('
		<script type="text/javascript" src="/javascript/prototype.js"></script>
		<script type="text/javascript" src="/javascript/scriptaculous.js"></script>
		<script src="/javascript/slideshow_new.js" type="text/javascript"></script>
		');

		// Set up the public frame
		$this->main_frame->SetContentSimple('reviews/main',$data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
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
	function table(	$item_type = FALSE,
					$sorted_by = FALSE,
					$item_filter_by = FALSE,
					$where_equal_to = FALSE)
	{
		//Load slideshow model
		$this->load->model('slideshow');

		//POST data set overwrites uri data
		if (isset($_POST['item_type'])) $item_filter_by = $_POST['item_type'];
		if (isset($_POST['item_filter_by'])) $item_filter_by = $_POST['item_filter_by'];
		if (isset($_POST['where_equal_to'])) $where_equal_to = $_POST['where_equal_to'];
		if (isset($_POST['sorted_by'])) $where_equal_to = $_POST['sorted_by'];

		//For next page so we remember the options given
		$data['item_filter_by'] = $item_filter_by;
		$data['where_equal_to'] = $where_equal_to;
		$data['sorted_by'] = $sorted_by;
		$data['item_type'] = $item_type;

		if (!CheckPermissions('public')) return;

		//Set page code
		$this->pages_model->SetPageCode('review_table');

		$database_result = $this->Review_model->GetTableReview($item_type,$sorted_by, $item_filter_by,$where_equal_to);

		$columns = array(0);
		$entries = array();

		//Get data for the links to the table page
		$tabledata = $this->Review_model->GetTags($item_type);

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

			//For each row in the table
			for($reviewno = 0; $reviewno < count($database_result); $reviewno++)
			{
				if (isset($database_result[$reviewno]['organisation_name']))
				{
				//surely this should be in the model
					$entries[$reviewno]['review_title'] = $database_result[$reviewno]['organisation_name'];
					$entries[$reviewno]['review_id'] = $database_result[$reviewno]['organisation_entity_id'];
					$entries[$reviewno]['review_website'] = $database_result[$reviewno]['organisation_content_url'];
					$entries[$reviewno]['review_rating'] = $database_result[$reviewno]['review_context_content_rating'];
					$entries[$reviewno]['review_blurb'] = $database_result[$reviewno]['review_context_content_blurb'];
					$entries[$reviewno]['review_quote'] = $database_result[$reviewno]['review_context_content_quote'];
					$entries[$reviewno]['review_user_rating'] = intval($database_result[$reviewno]['average_user_rating']);
					$entries[$reviewno]['review_table_link'] = base_url().'reviews/'.$item_type.'/'.$database_result[$reviewno]['organisation_directory_entry_name'];

					//get the slideshow images for the review item
					$slideshow_array = $this->slideshow->getPhotos($database_result[$reviewno]['organisation_entity_id']); //$item_type, true);
					if($slideshow_array->num_rows() > 0)
					{
						$entries[$reviewno]['review_image'] = $this->image->getPhotoURL($slideshow_array->row()->photo_id, 'slideshow');
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
						else //Else pass a empty array - Changed a array containing 'n/a'
						{
							//Hide the n/a as they serves no purpose... good work frank. nse500
							//-> to clarify:
							//"well done for making it so i only need to comment out one line to turn off the whole na thing, saves me alot of work :-)"
							//$tagbox[$data['review_tags'][$tagno]] = array('n/a');
						}
					}
					$entries[$reviewno]['tagbox'] = $tagbox;
				}
			}

			$data['entries'] = $entries;

		}

		$this->main_frame->SetExtraCss('/stylesheets/reviews.css');
		$this->main_frame->SetContentSimple('reviews/table',$data);
		$this->main_frame->Load();
	}

	/// Leagues
	function leagues($league_code_name = NULL)
	{
		if (!CheckPermissions('public')) return;

		//Set page code
		$this->pages_model->SetPageCode('review_league');

		//Load slideshow model
		$this->load->model('slideshow');

		//Load review  model
		$this->load->model('review_model');

		//Check we have being passed a league to view otherwise the query returns badly...
		if ($league_code_name === NULL) redirect('/reviews'); //It doesn't matter if the code below is executed or not...

		//Find out the content_type
		$content_type = $this->Review_model->GetLeagueType($league_code_name);

		//Get leagues from model
		$leagues = $this->Review_model->GetLeague($league_code_name);

		//Check for if zero
		if (isset($leagues[0]['league_name']) == 1)
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


				//get the slideshow images for the league item
				$slideshow_array = $this->slideshow->getPhotos($reviews[$row]['review_org_id']); //, $reviews[$row]['review_content_type_id'], false);
				if($slideshow_array->num_rows() > 0)
				{
					$reviews[$row]['image'] = $slideshow_array->row()->photo_id;
				}

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
		}

		//Get other league table data
		$league_data = $this->Review_model->GetLeagueDetails($content_type);
		$leagues = array();
		foreach ($league_data as &$league)
		{
			$leagues[] = array(
				'league_image_path'=> '/image/puffer/'.$league['league_image_id'],
				'league_name'=>$league['league_name'],
				'league_size'=>$league['league_size'],
				'league_codename'=>$league['league_codename']
				);
		}

		//Pass tabledata straight to view it is in the proper format
		$data['league_data'] = $leagues;


		$this->main_frame->SetContentSimple('reviews/leagues',$data);
		$this->main_frame->Load();
	}
}

?>
