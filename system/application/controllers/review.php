<?php
// Review Controller by Frank Burton

class Review extends Controller {

	//Page Constructor
	//Loads each time page is called
	function Review()
	{
		//Needed for code igniter to work
		parent::Controller();
		
		//Load Helper Functions so we can return dynamic url's
		//And possible forms later on for the admin pages
		$this->load->helper('url');
		$this->load->helper('form');
		
	}


	//Normal Call to Page i.e. http://real.theyorker.co.uk/review
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
//		$data['food_link'] = base_url('review/food');
//		$data['drink_link'] = base_url('review/drink');
//		$data['culture_link'] = base_url('review/culture');
		
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
	
		//Load View Page
		$data['content_view'] = 'reviews/index';
		$this->load->view('frames/student_frame',$data);
		
	}

	//This 
	//Food Link
	function food()
	{
		//Load View Page
		$data['content_view'] = 'reviews/food';
		$this->load->view('frames/student_frame',$data);
	}
	
	//Drink Link
	function drink()
	{
		$data['content_view'] = 'reviews/drink';
		$this->load->view('frames/student_frame',$data);
	}
	
	//Culture Link
	function culture()
	{
		$data['content_view'] = 'reviews/culture';
		$this->load->view('frames/student_frame',$data);
	}
	
	//Food Review
	function foodreview()
	{
		$data['content_view'] = 'reviews/foodreview';
		$this->load->view('frames/student_frame',$data);
	}
	
	//Culture Review
	function culturereview()
	{
		$data['content_view'] = 'reviews/culturereview';
		$this->load->view('frames/student_frame',$data);
	}

	//Bar Crawl Page
	function barcrawl()
	{
		$data['content_view'] = 'reviews/barcrawl';
		$this->load->view('frames/student_frame',$data);
	}
	
	/**
	* These are all the edit pages for the admin panel
	* Additional controllers will be required
	*/
	function edit()
	{
		$data['title_image'] = base_url().'images/prototype/reviews/reviews_01.gif';
		$data['content_view'] = 'reviews/mainedit';
		$this->load->view('frames/student_frame',$data);
	}
	function editsection()
	{
		$data['content_view'] = 'reviews/sectionedit';
		$this->load->view('frames/student_frame',$data);
	}
	function editreview()
	{
		$data['content_view'] = 'reviews/reviewedit';
		$this->load->view('frames/student_frame',$data);
	}
}

?>
