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
		$this->load->helper('images');

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
		$data['content_type'] = $content_type;

		//Set page code
		$this->pages_model->SetPageCode('review_main');

		$this->main_frame->SetTitleParameters(array(
			'content_type' => $content_type
		));

		//Load news model
		$this->load->model('News_model');

		//For culture - The right side barcrawl list
		if ($content_type == 'culture' && FALSE)
		{
			$barcrawl_id = $this->News_model->GetLatestId('barcrawl',5); //Latest 5 entries

			if ($barcrawl_id != array()) //If not empty
			{
				$barcrawl_index = 0;

				foreach ($barcrawl_id as $id) //Get all the information from news model
				{
					$barinfo = $this->News_model->GetSimpleArticle($id);
					$barcrawls[$barcrawl_index]['barcrawl_name'] = $barinfo['heading'];
					$barcrawls[$barcrawl_index]['barcrawl_link'] = '/reviews/barcrawl/'.$this->Review_model->GetDirectoryName($id);
				}
			}
			$data['barcrawls'] = $barcrawls;

		}

		//Get the last article_id
		$article_id = $this->News_model->GetLatestId($content_type,1); //1 is the amount of articles

		//Get the directory name of the organistion it's about
		//$organisation_code_name = $this->Review_model->GetDirectoryName($article_id);

		//Get data from GetReviews
		//$reviews_database_result = $this->Review_model->GetReview($organisation_code_name, $content_type);

		//Incase of no data
		//if (count($reviews_database_result) != 0) {
			//$this->messages->AddMessage('information', 'No articles could be found', FALSE);
		//}

		//First row only since it should be unique
		//$reviews_database_result = $reviews_database_result[0];

		//Get the article summary
		//$article_database_result = $this->News_model->GetFullArticle($article_id);

		/// If there are no articles for this particular section then show a page anyway
		if (count($article_id) == 0) {
			$main_article = array(
				'id'						=>	0,
				'date'					=>	date('l, jS F Y'),
				'location'				=> 0,
				'public_thread_id'	=>	NULL,
				'heading'				=>	$this->pages_model->GetPropertyText('news:no_articles_heading',TRUE),
				'subheading'			=>	NULL,
				'subtext'				=>	NULL,
				'text'					=>	$this->pages_model->GetPropertyWikiText('news:no_articles_text',TRUE),
				'blurb'					=>	NULL,
				'authors'				=>	array(),
				'links'					=>	array(),
				'related_articles'	=> array(),
				'fact_boxes'			=>	array()
			);
		} else {
			$main_article = $this->News_model->GetFullArticle($article_id[0]);
		}

		$data['main_article'] = $main_article;

		// Create byline --- Note to byliner... dynamic data done
		//$this->load->library('byline');
		//$this->byline->AddReporter($article_database_result['authors']);
		//$this->byline->SetDate($article_database_result['date']);

		//Set Blurb
		//$data['main_blurb'] = $this->pages_model->GetPropertyText('blurb');
		//if (isset($article_database_result['photos']))
		//{
		//	$data['article_photo'] = imageLocation($article_database_result['photos'][0]);
		//}
		//else
		//{
		//	$data['article_photo'] = imageLocation(1);
		//}

		//$data['article_photo_alt_text'] = "Article Image";
		//$data['article_photo_title'] = "Recent Title";

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
				'league_image_path'=>(imageLocation($league['league_image_id'], 'puffer')),
				'league_name'=>$league['league_name'],
				'league_size'=>$league['league_size'],
				'league_codename'=>$league['league_codename']
				);
		}

		//Pass tabledata straight to view it is in the proper format
		$data['league_data'] = $leagues;

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

	/// Add a comment
	function addcomment()
	{
		/// @todo Model shouldn't have to deal with post data.
		$this->Review_model->SetComment($_POST); //Gives model post data
		redirect($_POST['return_page']); //Send user back to previous page
	}

 //Old dummy data - Will returns error etc...
	/// Bar Crawl Page
	function barcrawls($CrawlName = FALSE)
	{
		if (!CheckPermissions('public')) return;

		//Set page code
		$this->pages_model->SetPageCode('review_context_barcrawl');

		//Dummy Data - frb501
		$data['crawl_title']='The Great Piss Up';
		$data['crawl_blurb']='A kick ass crawl. So cool I came back home with a dead donkey!';
		$data['crawl_image']='/images/prototype/reviews/reviews_07.jpg';
		$data['crawl_content']='The industry section around this area fart is particularly sexy and it likes nick evans sex The fart industry section around this area is sex particularly sexy and it longwordtastic likes nick evans The industry section longwordtastic around this area is particularly sexy fart and it likes nick evans The industry longwordtastic section around this area is particularly sexy and it likes nick evans The industry section around this area is particularly sexy and it likes nick evans The industry section around this area is particularly sexy and it likes nick evans The industry section around
';
		$data['crawl_rating'] = '5 skulls!';
		$data['crawl_directions']='Follow the white rabbit for he is on fire and will show you the way to new jersey, not ammarillo though or however you spells it check it Follow the white rabbit for he is on fire and will show you the way to new jersey, not ammarillo though or however you spells it check it Follow the white rabbit for he is on fire and will show you the way to new jersey, not ammarillo though or however you spells it check it Follow the white rabbit for he is on fire';

		$data['crawl_cost']='12';
		$data['pub_list'] = array('Kings Head','Ducks Head','Your Head');
		$drink_guide[0] = array('Kings Head','Bloody Mary','2');
		$drink_guide[1] = array('Ducks Head','Eggs Galore','4');
		$drink_guide[2] = array('Your Head','Ale','5');
		$data['drink_guide'] = $drink_guide;

		//Comment system
		$data['page_id'] = 105;
		//$data['comments'] 	= $this->Review_model->GetComments($organisation_name,$content_id,$article_comment_id);
		$data['article_id'] = 111;

		$this->main_frame->SetTitleParameters(array('organisation' => 'The Great Piss Up'));

		// Set up the public frame
		$this->main_frame->SetContentSimple('reviews/barcrawl',$data);

		// Load the public frame view (which will load the content view)
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
					
					//complete temp hack that doesn't even work
					$sql = 'SELECT content_type_id
				FROM content_types
				WHERE content_type_codename = ?';
		$query = $this->db->query($sql,array($item_type));
		$row = $query->row();
		if ($query->num_rows() == 1){
			$type_id = $row->content_type_id;
			}
			else{
			$type_id = NULL;}
					
					$slideshow_array = $this->slideshow->getPhotos($database_result[$reviewno]['organisation_entity_id'], $type_id);
					$slideshow = array();
					foreach ($slideshow_array->result() as $slide){
						$slideshow[] = array(
							'title' => $slide->photo_title,
							'id' => $slide->photo_id,
							'url' => imageLocation($slide->photo_id, 'slideshow'),
						);
					}
					
					$entries[$reviewno]['slideshow'] = $slideshow;

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
							$tagbox[$data['review_tags'][$tagno]] = array('n/a');
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

	/// Report a comment
	function reportcomment($comment_id)
	{
		$this->Review_model->ReportComment($comment_id);
		redirect('/reviews/food');
	}

	/// Leages
	function leagues($league_code_name = NULL)
	{
		if (!CheckPermissions('public')) return;

		//Set page code
		$this->pages_model->SetPageCode('review_league');

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
				$reviews[$row]['review_title'] = $leagues[$row]['organisation_name'];
				$reviews[$row]['review_website'] = $leagues[$row]['organisation_url'];
				$reviews[$row]['review_rating'] = $leagues[$row]['review_rating'];
				$reviews[$row]['review_quote'] = $leagues[$row]['review_quote'];
				$reviews[$row]['review_user_rating'] = $leagues[$row]['average_user_rating'];
				//This will need the use of a function which returns what a organisition has being reviews on
				$reviews[$row]['review_link'] = '/reviews/'.$content_type.'/'.$leagues[$row]['organisation_directory_entry_name'];
				$reviews[$row]['review_blurb'] = $leagues[$row]['organisation_description'];
				$reviews[$row]['review_title'] = $leagues[$row]['organisation_name'];
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
				'league_image_path'=>(imageLocation($league['league_image_id'], 'puffer')),
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
