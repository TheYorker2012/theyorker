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
		
		// Load the public frame
		$this->load->library('frame_public');

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
		//Load news model
		$this->load->model('News_model');

		//Get the last article_id
		$article_id = $this->News_model->GetLatestId(7,1); //7 is food, 1 is the amount of articles
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
		$this->frame_public->SetTitle('Food');
		$this->frame_public->SetContentSimple('reviews/food',$data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	//Drink Section - Dummy Data intill Model Ready
	function drink()
	{
		//Load news model
		$this->load->model('News_model');

		//Get the last article_id
		$article_id = $this->News_model->GetLatestId(8,1); //8 is drink, 1 is the amount of articles
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
		$this->frame_public->SetTitle('Drink');
		$this->frame_public->SetContentSimple('reviews/drink',$data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	
	//Culture Section - Dummy Data intill Model Ready
	function culture()
	{
		//Load news model
		$this->load->model('News_model');

		//Get the last article_id
		$article_id = $this->News_model->GetLatestId(9,1); //9 is culture, 1 is the amount of articles
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
		$this->frame_public->SetTitle('Culture');
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
				//Find our article_id
				$article_id = $this->Review_model->GetArticleID($page_code,7);
				$article_comment_id = $article_id[0];

				$data['page_id'] 	= 20;
				$data['comments'] 	= $this->Review_model->GetComments($data['page_id'],$article_comment_id);//User comments
				$review_database_result 	= $this->Review_model->GetReview($page_code,'food');
				$this->frame_public->SetTitle('Food Review');
			break;
	
			case 1:
				//Find our article_id
				$article_id = $this->Review_model->GetArticleID($page_code,8);
				$article_comment_id = $article_id[0];

				$data['page_id'] 	= 21;
				$data['comments'] 	= $this->Review_model->GetComments($data['page_id'],$article_comment_id);//User comments

				$review_database_result 	= $this->Review_model->GetReview($page_code,'drink');
				$this->frame_public->SetTitle('Drink Review');
			break;
	
			case 2:
				//Find our article_id
				$article_id = $this->Review_model->GetArticleID($page_code,9);
				$article_comment_id = $article_id[0];

				$data['page_id'] 	= 22;
				$data['comments'] 	= $this->Review_model->GetComments($data['page_id'], $article_comment_id); //User comments

				$review_database_result 	= $this->Review_model->GetReview($page_code,'culture');
				$this->frame_public->SetTitle('Culture Review');
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
		$data['website_booking']		= $review_database_result['review_context_content_book_online'];
		$data['telephone']				= $review_database_result['organisation_phone_external'];
		$data['average_price']			= '�'.($review_database_result['review_context_content_average_price_lower']/100).' to �'.($review_database_result['review_context_content_average_price_upper']/100);
		$data['opening_times']			= $review_database_result['organisation_opening_hours'];
		$data['yorker_recommendation']	= $review_database_result['review_context_content_rating'];

		//Dummy Data
		$data['also_does_state']		= 5;  //Food is 4, Drink is 2, Culture is 1, Add together
		$data['price_rating']			= 'Waiting on Model';

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
		// Set up the public frame
		$this->frame_public->SetTitle('Barcrawl');
		$this->frame_public->SetContentSimple('reviews/barcrawl');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	//Display table for review table (from puffers)
	function table()
	{
		$item_type = $this->uri->segment(3); //Expected food/drink/culture/any
//		Implentment/Normailise later
//		$item_filter_by = $this->uri->segment(4); //Expected price/sub type/etc..../any
//		$where_equal_to = $this->uri->segment(5); //Expected italian/late night/etc..../any
//		$sorted_by = $this->uri->segment(6); //name/star/price/user/any

		$columns = array(0);

		$database_result = $this->Review_model->TableReview($item_type, -1);
		$entries = array();

		foreach($database_result as &$result)
		{
			$entries[] = array(
				'review_image' 			=> '/images/prototype/news/thumb3.jpg',
				'review_title' 			=> $result['organisation_name'],
				'review_website' 		=> $result['organisation_url'],
				'review_rating' 		=> $result['review_context_content_rating'],
				'review_user_rating' 	=> intval($result['comment_summary_cache_average_rating']),
				'review_cost_type' 		=> isset($result['tags']['Price']) ? $result['tags']['Price'][0] : '',
				'review_tags'			=> array
				(
					'Atmosphere' => isset($result['tags']['Atmosphere']) ? implode("<br />", $result['tags']['Atmosphere']) : '',
					'Cuisine' => isset($result['tags']['Cuisine']) ? implode("<br />", $result['tags']['Cuisine']) : '',
				),
				'review_table_link'		=> base_url().'reviews/'.$item_type.'review/'.$result['organisation_directory_entry_name'],
			);
		}

		$data['entries'] = $entries;

		$this->frame_public->SetTitle('table');
		$this->frame_public->SetContentSimple('reviews/table',$data);
		$this->frame_public->Load();
	}

	function leagues()
	{
		
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

		$reviews['review_title'] = array(
			'Evil Eye',
			'Gallery',
			'Toffs',
			'Nexus',
			'The Lion Storm',
			'Toffs',
			'Nexus',
			'The Lion Storm',
			'Ha ha',
			'The Red Bull');
		
		$reviews['review_website'] = array(
			'http://www.mywebsite.co.uk',
			'http://www.mywebsite.co.uk',
			'http://www.mywebsite.co.uk',
			'http://www.mywebsite.co.uk',
			'http://www.mywebsite.co.uk',
			'http://www.mywebsite.co.uk',
			'http://www.mywebsite.co.uk',
			'http://www.mywebsite.co.uk',
			'http://www.mywebsite.co.uk',
			'http://www.mywebsite.co.uk');

		$reviews['review_rating'] = array(2,6,2,7,3,8,3,2,8,4);

				$data['review_link'] = array("/context/evil_eye_lounge/food","/context/evil_eye_lounge/food","/context/evil_eye_lounge/food","/context/evil_eye_lounge/food","/context/evil_eye_lounge/food","/context/evil_eye_lounge/food","/context/evil_eye_lounge/food","/context/evil_eye_lounge/food","/context/evil_eye_lounge/food","/context/evil_eye_lounge/food");

		$reviews['review_blurb'] = array(
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!",
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!",
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!",
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!",
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!",
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!",
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!",
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!",
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!",
			"The most romantic place in york is the blue bicycle. A wonderful place to go. I've had some romantic nights in here before. Believe me!");

		$data['reviews'] = $reviews;

		$this->frame_public->SetTitle('leagues');
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
