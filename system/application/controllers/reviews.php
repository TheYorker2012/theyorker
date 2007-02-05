<?php
// Review Controller by Frank Burton

class Reviews extends Controller {

	//Page Constructor
	//Loads each time page is called
	function Reviews()
	{
		//Needed for code igniter to work
		parent::Controller();
		
		//Load Helper Functions so we can return dynamic url's
		//And possible forms later on for the admin pages
		$this->load->helper('form');
		$this->load->helper('url');
		
		//Set us to show public pages
		SetupMainFrame('public');

		//Load page model
		$this->load->model('pages_model');

		//Load reviews model
		$this->load->model('Review_model');

	}

	//Normal Call to Page - Doesn't do anything anymore....

	function index()
	{	
		redirect('/reviews/food'); //Send them to the food page instead
	}

	//Food Frontpage
	function food()
	{
		//Set page code
		$this->pages_model->SetPageCode('review_food');

		//Load news model
		$this->load->model('News_model');

		//Get the last article_id
		$article_id = $this->News_model->GetLatestId('food',1); //1 is the amount of articles
		$article_id = $article_id[0]; //Only 1 article being retrieved so...

		//Get the directory name of the organistion it's about
		$organisation_code_name = $this->Review_model->GetDirectoryName($article_id);
		$organisation_content_type = 'food'; //This should be a constant...

		//Get data from GetReviews
		$reviews_database_result = $this->Review_model->GetReview($organisation_code_name,$organisation_content_type);

		//Incase of no data
		if (count($reviews_database_result) == 0) echo 'There are no articles...<BR> This page doesnt work under these conditions <BR>';

		//First row only since it should be unique
		$reviews_database_result = $reviews_database_result[0];


		//Get the article summary
		$article_database_result = $this->News_model->GetFullArticle($article_id);

		$data['article_title'] = $article_database_result['heading'];
		$data['article_author'] = $article_database_result['authors'][0];
		$data['article_content'] = $article_database_result['subtext'];
		$data['article_date'] = $article_database_result['date'];
		$data['article_link'] = '/reviews/foodreview/'.$organisation_code_name;

		//Set Blurb
		$data['main_blurb'] = $this->pages_model->GetPropertyText('food_blurb');

		//Dummy Data - Waiting for news_model to finish implementing, I feel like a theif..., frb501
		$data['article_author'] = 'Matthew Tole';
		$data['article_author_link'] = '/directory/view/1';
		$data['article_photo'] = '/images/prototype/news/thumb4.jpg';

		//More dummy data as part of the tables page
		$type_array['name'] = array('Italian','Indian','Pub Dinners','Take Away Resturants','Thai','Chinese','All Types');
		$type_array['link'] = array('reviews/table/food','reviews/table/food','reviews/table/food','reviews/table/food','reviews/table/food','reviews/table/food','reviews/table/food');
		$data['type_array'] = $type_array;

		$price_array['name'] = array('Dirt Cheap','Super Cheap','Kinda Cheap','Meh','Mega Expensive','All Prices');
		$price_array['link'] = array('reviews/table/food','reviews/table/food','reviews/table/food','reviews/table/food','reviews/table/food','reviews/table/food/');
		$data['price_array'] = $price_array;
			
		// Set up the public frame
		$this->frame_public->SetContentSimple('reviews/food',$data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	//Drink Section - Dummy Data intill Model Ready
	function drink()
	{
		//Set page code
		$this->pages_model->SetPageCode('review_drink');

		//Load news model
		$this->load->model('News_model');

		//Set Blurb
		$data['main_blurb'] = $this->pages_model->GetPropertyText('drink_blurb');

		//Get the last article_id
		$article_id = $this->News_model->GetLatestId('drink',1); //1 is the amount of articles
		$article_id = $article_id[0]; //Only 1 article being retrieved so...

		//Get the directory name of the organistion it's about
		$organisation_code_name = $this->Review_model->GetDirectoryName($article_id);
		$organisation_content_type = 'drink'; //This should be a constant...

		//Get data from GetReviews
		$reviews_database_result = $this->Review_model->GetReview($organisation_code_name,$organisation_content_type);

		//Incase of no data
		if (count($reviews_database_result) == 0) echo 'There are no articles...<BR> This page doesnt work under these conditions <BR>';

		//First row only since it should be unique
		$reviews_database_result = $reviews_database_result[0];

		//Get the article summary
		$article_database_result = $this->News_model->GetFullArticle($article_id);

		$data['article_title'] = $article_database_result['heading'];
		$data['article_author'] = $article_database_result['authors'][0];
		$data['article_content'] = $article_database_result['subtext'];
		$data['article_date'] = $article_database_result['date'];
		$data['article_link'] = '/reviews/drinkreview/'.$organisation_code_name;

		//Dummy Data
		$data['article_author'] = 'Matthew Tole';
		$data['article_author_link'] = '/directory/view/1';

		$price_array['name'] = array('Dirt Cheap','Super Cheap','Kinda Cheap','Meh','Mega Expensive','All Prices');
		$price_array['link'] = array('reviews/table/drink','reviews/table/drink','reviews/table/drink','reviews/table/drink','reviews/table/drink','reviews/table/drink');
		$data['price_array'] = $price_array;
		
		// Set up the public frame
		$this->frame_public->SetContentSimple('reviews/drink',$data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	
	//Culture Section - Dummy Data intill Model Ready
	function culture()
	{
		//Set page code
		$this->pages_model->SetPageCode('review_culture');

		//Load news model
		$this->load->model('News_model');

		//Set Blurb
		$data['main_blurb'] = $this->pages_model->GetPropertyText('culture_blurb');

		//Get the last article_id
		$article_id = $this->News_model->GetLatestId('culture',1); //1 is the amount of articles
		$article_id = $article_id[0]; //Only 1 article being retrieved so...

		//Get the directory name of the organistion it's about
		$organisation_code_name = $this->Review_model->GetDirectoryName($article_id);
		$organisation_content_type = 'culture'; //This should be a constant...

		//Get data from GetReviews
		$reviews_database_result = $this->Review_model->GetReview($organisation_code_name,$organisation_content_type);

		//Incase of no data
		if (count($reviews_database_result) == 0) echo 'There are no articles...<BR> This page doesnt work under these conditions <BR>';

		//First row only since it should be unique
		$reviews_database_result = $reviews_database_result[0];

		//Get the article summary
		$article_database_result = $this->News_model->GetFullArticle($article_id);

		$data['article_title'] = $article_database_result['heading'];
		$data['article_author'] = $article_database_result['authors'][0];
		$data['article_content'] = $article_database_result['subtext'];
		$data['article_date'] = $article_database_result['date'];
		$data['article_link'] = '/reviews/culturereview/'.$organisation_code_name;
		$data['article_author_link'] = '/directory/view/1';

		//Dummy data
		$location_array['name'] = array('York','Leeds','London','Manchester','Blackpool','All Locations');
		$location_array['link'] = array('reviews/table/culture','reviews/table/culture','reviews/table/culture','reviews/table/culture','reviews/table/culture','reviews/table/culture');
		$data['location_array'] = $location_array;

		$price_array['name'] = array('Dirt Cheap','Super Cheap','Kinda Cheap','Meh','Mega Expensive','All Prices');
		$price_array['link'] = array('reviews/table/culture','reviews/table/culture','reviews/table/culture','reviews/table/culture','reviews/table/culture','reviews/table/culture');
		$data['price_array'] = $price_array;
		
		// Set up the public frame
		$this->frame_public->SetContentSimple('reviews/culture',$data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	//Review Function for Food/Drink/Culture
	function mainreview($review_type, $page_code)
	{
		//Load news model
		$this->load->model('News_model');

		switch($review_type)
		{
			case 0:
				//Set page code
				$this->pages_model->SetPageCode('review_context_food');

				//Find our article_id
				$article_id = $this->Review_model->GetArticleID($page_code,7);
				$article_comment_id = $article_id[0];

				$data['page_id'] 	= 20;
				$data['comments'] 	= $this->Review_model->GetComments($data['page_id'],$article_comment_id);//User comments
				$review_database_result 	= $this->Review_model->GetReview($page_code,'food');
			break;
	
			case 1:
				//Set page code
				$this->pages_model->SetPageCode('review_context_drink');

				//Find our article_id
				$article_id = $this->Review_model->GetArticleID($page_code,8);
				$article_comment_id = $article_id[0];

				$data['page_id'] 	= 21;
				$data['comments'] 	= $this->Review_model->GetComments($data['page_id'],$article_comment_id);//User comments

				$review_database_result 	= $this->Review_model->GetReview($page_code,'drink');
			break;
	
			case 2:
				//Set page code
				$this->pages_model->SetPageCode('review_context_culture');

				//Find our article_id
				$article_id = $this->Review_model->GetArticleID($page_code,9);
				$article_comment_id = $article_id[0];

				$data['page_id'] 	= 22;
				$data['comments'] 	= $this->Review_model->GetComments($data['page_id'], $article_comment_id); //User comments

				$review_database_result 	= $this->Review_model->GetReview($page_code,'culture');
			break;
		}

		//Get the article for each article on the page
		for ($article_no = 0; $article_no < count($article_id); $article_no++)
		{
			$article_database_result = $this->News_model->GetFullArticle($article_id[$article_no]);

			$article[$article_no]['article_title'] = $article_database_result['heading'];
	//		$article[$article_no]['article_author'] = $article_database_result['authors'][0];
			$article[$article_no]['article_content'] = $article_database_result['text'];
			$article[$article_no]['article_date'] = $article_database_result['date'];

			$article[$article_no]['article_photo'] = '/images/prototype/news/benest.png';
			$article[$article_no]['article_author'] = 'Ian Benest - Top Yorker Author';
			$article[$article_no]['article_author_link'] = '/directory/view/1';
		}

		//Place articles into the data array to be passed along
		
		$data['article'] = $article;

		$review_database_result = $review_database_result[0]; //Unique so just first row

		$data['article_id'] = $article_id;
		$data['review_title'] 			= $review_database_result['organisation_name'];
		$data['review_blurb']			= $review_database_result['review_context_content_blurb'];
		$data['review_image']			= '/images/prototype/reviews/reviews_07.jpg';
		$data['email'] 				= $review_database_result['organisation_email_address'];
		$data['address_main']			= $review_database_result['organisation_postal_address'];
		$data['address_postcode']		= $review_database_result['organisation_postcode'];
		$data['website']				= $review_database_result['organisation_url'];
		$data['telephone']				= $review_database_result['organisation_phone_external'];
		$data['average_price']			= '£'.$review_database_result['review_context_content_average_price'];
		$data['opening_times']			= $review_database_result['organisation_opening_hours'];
		$data['yorker_recommendation']	= $review_database_result['review_context_content_rating'];
		$data['serving_times']			= $review_database_result['review_context_content_serving_times'];

		//Check the deal isn't expired
		if ($review_database_result['review_context_content_deal_expires'] < time())
		{
			$data['deal'] = $review_database_result['review_context_content_deal'];
		}

		//Dummy Data
		$data['also_does_state']		= 5;  //Food is 4, Drink is 2, Culture is 1, Add together
		$data['price_rating']			= 'Waiting on Model';

		//Set organisation name in title bar
		$this->main_frame->SetTitleParameters(array('organisation' => $review_database_result['organisation_name']));

		// Load the public frame view (which will load the content view)
		$this->frame_public->SetContentSimple('reviews/foodreview', $data);
		$this->frame_public->Load();

	}
	
	function foodreview()
	{
		Reviews::mainreview(0,$this->uri->segment(3)); //Both are same format
	}

	function drinkreview()
	{
		Reviews::mainreview(1,$this->uri->segment(3)); //Both are same format
	}
	
	//Culture Review
	function culturereview()
	{
		Reviews::mainreview(2,$this->uri->segment(3)); //Same again
	}

	function addcomment()
	{
		$this->Review_model->SetComment($_POST); //Gives model post data
		redirect($_POST['return_page']); //Send user back to previous page
	}

	//Bar Crawl Page
	function barcrawl()
	{
		//Set page code
		$this->pages_model->SetPageCode('review_context_barcrawl');

		//Start of controller for model
		$crawl_name = $this->uri->segment(3);

		//Dummy Data - frb501
		$data['crawl_title']='The Great Piss Up';
		$data['crawl_blurb']='A kick ass crawl. So cool I came back home with a dead donkey!';
		$data['crawl_image']='/images/prototype/reviews/reviews_07.jpg';
		$data['crawl_content']='The industry section around this area fart is particularly sexy and it likes nick evans sex The fart industry section around this area is sex particularly sexy and it longwordtastic likes nick evans The industry section longwordtastic around this area is particularly sexy fart and it likes nick evans The industry longwordtastic section around this area is particularly sexy and it likes nick evans The industry section around this area is particularly sexy and it likes nick evans The industry section around this area is particularly sexy and it likes nick evans The industry section around
';
		$data['crawl_rating'] = '5 skulls!';
		$data['crawl_directions']='Follow the white rabbit for he is on fire and will show you the way to new jersey, not ammarillo though or however you spells it check it Follow the white rabbit for he is on fire and will show you the way to new jersey, not ammarillo though or however you spells it check it Follow the white rabbit for he is on fire and will show you the way to new jersey, not ammarillo though or however you spells it check it Follow the white rabbit for he is on fire';

		$data['crawl_cost']='£12';
		$data['pub_list'] = array('Kings Head','Ducks Head','Your Head');
		$drink_guide[0] = array('Kings Head','Bloody Mary','£2');
		$drink_guide[1] = array('Ducks Head','Eggs Galore','£4');
		$drink_guide[2] = array('Your Head','Ale','£5');
		$data['drink_guide'] = $drink_guide;

		//Comment system
		$data['page_id'] = 105;
		$data['comments'] = $this->Review_model->GetComments(105,1);
		$data['article_id'] = 111;
		$this->main_frame->SetTitleParameters(array('organisation' => 'The Great Piss Up'));

		// Set up the public frame
		$this->frame_public->SetContentSimple('reviews/barcrawl',$data);
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	//Display table for review table (from puffers)
	function table()
	{
		//Set page code
		$this->pages_model->SetPageCode('review_table');

		$item_type = $this->uri->segment(3); //Expected food/drink/culture/any
		$sorted_by = $this->uri->segment(4); //Expected name/star/price/user/any

		$item_filter_by = $this->uri->segment(5); //Expected a valid tag group name or 'any'
		$where_equal_to = $this->uri->segment(6); //Expected a valid tag name or 'any'

		$columns = array(0);

		$database_result = $this->Review_model->TableReview($item_type,$sorted_by, $item_filter_by,$where_equal_to);

		$entries = array();

		//A list of all tags
		$data['review_tags'] = $database_result[0]['tag_groups'];

		//For each row in the table

		for($reviewno = 0; $reviewno < count($database_result); $reviewno++)
		{
			$entries[$reviewno]['review_image'] = '/images/prototype/news/thumb3.jpg';
			$entries[$reviewno]['review_title'] = $database_result[$reviewno]['organisation_name'];
			$entries[$reviewno]['review_website'] = $database_result[$reviewno]['organisation_url'];
			$entries[$reviewno]['review_rating'] = $database_result[$reviewno]['review_context_content_rating'];
			$entries[$reviewno]['review_user_rating'] = intval($database_result[$reviewno]['comment_summary_cache_average_rating']);
			$entries[$reviewno]['review_table_link'] = base_url().'reviews/'.$item_type.'review/'.$database_result[$reviewno]['organisation_directory_entry_name']; 

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
				else //Else pass a empty array
				{
					$tagbox[$data['review_tags'][$tagno]] = array();
				}
			}

			$entries[$reviewno]['tagbox'] = $tagbox;
		}

		$data['entries'] = $entries;

		$this->frame_public->SetContentSimple('reviews/table',$data);
		$this->frame_public->Load();
	}

	function leagues()
	{
		//Set page code
		$this->pages_model->SetPageCode('review_league');

		//Get leagues from model
		$leagues = $this->Review_model->GetLeague($this->uri->segment(3));

		//Set name of league
		$data['league_name'] = $leagues[0]['league_name']; //They should all be from the same league

		//Place remaining data into a array for the view
		for ($row = 0; $row < count($leagues); $row++)
		{
			$reviews['review_title'][$row] = $leagues[$row]['organisation_name'];
			$reviews['review_website'][$row] = $leagues[$row]['organisation_url'];
			$reviews['review_rating'][$row] = $leagues[$row]['average_user_rating'];
			$reviews['review_link'][$row] = '/reviews/foodreview/'.$leagues[$row]['organisation_directory_entry_name']; //This will need the use of a function which returns what a organisition has being reviews on
			$reviews['review_blurb'][$row] = $leagues[$row]['organisation_description'];
			$reviews['review_title'][$row] = $leagues[$row]['organisation_name'];
		}

		//Pass over the amount of entries to view
		$data['max_entries'] = $row;

//Dummy data
		$reviews['review_image'] = array(
			'/images/prototype/news/thumb9.jpg',
			'/images/prototype/news/thumb9.jpg',
			'/images/prototype/news/thumb9.jpg',
			'/images/prototype/news/thumb9.jpg',
			'/images/prototype/news/thumb9.jpg',
			'/images/prototype/news/thumb9.jpg',
			'/images/prototype/news/thumb9.jpg',
			'/images/prototype/news/thumb9.jpg',
			'/images/prototype/news/thumb9.jpg',
			'/images/prototype/news/thumb9.jpg');

			$data['reviews'] = $reviews;

		$this->frame_public->SetContentSimple('reviews/leagues',$data);
		$this->frame_public->Load();
	}
	
	/**
	* These are all the edit pages for the admin panel
	* Additional controllers will be required
	*/
	function edit()
	{
		$data['title_image'] = 'images/prototype/reviews/reviews_01.gif';
		
		// Set up the public frame
		$this->frame_public->SetTitle('Edit');
		$this->frame_public->SetContentSimple('reviews/mainedit', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	function editsection()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Edit Section');
		$this->frame_public->SetContentSimple('reviews/sectionedit');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	function editreview()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Edit Review');
		$this->frame_public->SetContentSimple('reviews/reviewedit');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>
