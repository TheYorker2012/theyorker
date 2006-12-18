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
		$this->load->helper('url');
		$this->load->helper('form');
		
		// Load the public frame
		$this->load->library('frame_public');
	}

	//Normal Call to Page i.e. http://real.theyorker.co.uk/reviews
	//Therefore load up review content page
	function index()
	{
		//Since model isn't sorted yet we are going to return test data for now
		
		//Following comment is useless but funny
		// :PARSER: frb501 - site_url should take the url inside it and return the completed string
		// Unforunaility it doesn't do what it says it should do in the user guide
		// And appends .htm (As if any page would be valid with that on the end in code ignitor :/)
		// This is due to someone (I WILL FIND YOU!!) adding .htm setting in config.php!!!
		// This will be removed when the setting is removed
		
		$data['title_image'] = base_url().'images/prototype/reviews/reviews_01.gif';
		$data['food_image'] = base_url().'/images/prototype/reviews/reviews_07.jpg';
		$data['drink_image'] = base_url().'/images/prototype/reviews/reviews_07.jpg';
		$data['culture_image'] = base_url().'/images/prototype/reviews/reviews_07.jpg';
		
		$data['food_title'] = base_url().'images/reviews/contentpage/food_title.gif';
		$data['drink_title'] = base_url().'images/reviews/contentpage/drink_title.gif';
		$data['culture_title'] = base_url().'images/reviews/contentpage/culture_title.gif';
		
		//Links to the other pages / functions provided by review
		//Second thoughts these are static
//		$data['food_link'] = base_url('reviews/food');
//		$data['drink_link'] = base_url('reviews/drink');
//		$data['culture_link'] = base_url('reviews/culture');
		
		$data['food_text'] = 'Food. This links to the food section of the website.
								yuummmm........ Pizza and Food';
		$data['drink_text'] = 'Drink. This links to the drink section of the website.
								Strawberry milkshakes taste really nice';
		$data['culture_text'] = 'Culture. This links to the culture section of the website.
								The home of fine wines and errr stuff';
		
		//Example tries
		$data['food_try1'] = 'pizza';
		$data['food_try2'] = 'hot dogs';
		$data['food_try3'] = 'north staffs oatcakes'; //They are really nice :)
		$data['food_try4'] = 'north staffs oatcakes'; //They are really nice :)
		$data['food_try5'] = 'north staffs oatcakes'; //They are really nice :)
		
		$data['drink_try1'] = 'cola';
		$data['drink_try2'] = 'fanta';
		$data['drink_try3'] = 'fresh orange juice';
		$data['drink_try4'] = 'toffs';
		$data['drink_try5'] = 'the oldmans pub';
			
		$data['culture_try1'] = 'fine dining';
		$data['culture_try2'] = 'absailing';
		$data['culture_try3'] = 'random housecalling';
		$data['culture_try4'] = 'watching paint try';
		$data['culture_try5'] = 'drinking wine by the bottleful';
	
		//Send return ids for the try suggestions
		$data['food_try_1_id'] = '1';
		$data['food_try_2_id'] = '2';
		$data['food_try_3_id'] = '3';
		$data['food_try_4_id'] = '4';
		$data['food_try_5_id'] = '5';
		$data['drink_try_1_id'] = '6';
		$data['drink_try_2_id'] = '7';
		$data['drink_try_3_id'] = '8';
		$data['drink_try_4_id'] = '9';
		$data['drink_try_5_id'] = '10';
		$data['culture_try_1_id'] = '1';
		$data['culture_try_2_id'] = '2';
		$data['culture_try_3_id'] = '3';
		$data['culture_try_4_id'] = '4';
		$data['cultrue_try_5_id'] = '5';
	
		// Set up the public frame
		$this->frame_public->SetTitle('Reviews');
		$this->frame_public->SetContentSimple('reviews/index', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
		
	}

	//This 
	//Food Link
	function food()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Food');
		$this->frame_public->SetContentSimple('reviews/food');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	//Drink Link
	function drink()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Drink');
		$this->frame_public->SetContentSimple('reviews/drink');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	//Culture Link
	function culture()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Culture');
		$this->frame_public->SetContentSimple('reviews/culture');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	//Food Review
	function foodreview()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Food Review');
		$this->frame_public->SetContentSimple('reviews/foodreview');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	//Culture Review
	function culturereview()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Culture Review');
		$this->frame_public->SetContentSimple('reviews/culturereview');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
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

	//Display tables for review lists
	function table()
	{
		$this->frame_public->SetTitle('Reviews');
		$this->frame_public->SetContentSimple('reviews/table');
		$this->frame_public->Load();
	}

	//Display tables for review leagues (from puffers)
	function leagues()
	{
		$this->frame_public->SetTitle('Leagues');
		$this->frame_public->SetContentSimple('reviews/leagues');
		$this->frame_public->Load();
	}
	
	/**
	* These are all the edit pages for the admin panel
	* Additional controllers will be required
	*/
	function edit()
	{
		$data['title_image'] = base_url().'images/prototype/reviews/reviews_01.gif';
		
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
