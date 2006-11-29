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
		
		$data['title_image'] = base_url().'images/reviews/contentpage/title_image.gif';
		$data['food_image'] = base_url().'images/reviews/contentpage/food_image.gif';
		$data['drink_image'] = base_url().'images/reviews/contentpage/drink_image.gif';
		$data['culture_image'] = base_url().'images/reviews/contentpage/culture_image.gif';
		
		$data['food_title'] = base_url().'images/reviews/contentpage/food_title.gif';
		$data['drink_title'] = base_url().'images/reviews/contentpage/drink_title.gif';
		$data['culture_title'] = base_url().'images/reviews/contentpage/culture_title.gif';
		
		//Links to the other pages / functions provided by review
		$data['food_link'] = base_url('reviews/food');
		$data['drink_link'] = base_url('reviews/drink');
		$data['culture_link'] = base_url('reviews/culture');
		
		$data['food_text'] = 'Food. This links to the food section of the website.
								hmmmmm................................. Food';
		$data['drink_text'] = 'Drink. This links to the drink section of the website.
								Strawberry milkshakes taste really nice';
		$data['culture_text'] = 'Culture. This links to the culture section of the website.
								Sometimes you need to eat pizza with a knife and folk not your hands';
		
		//Example tries
		$data['food_try1'] = 'pizza';
		$data['food_try2'] = 'hot dogs';
		$data['food_try3'] = 'north staffs oatcakes'; //They are really nice :)
		
		$data['drink_try1'] = 'cola';
		$data['drink_try2'] = 'fanta';
		$data['drink_try3'] = 'fresh orange juice';
			
		$data['culture_try1'] = 'fine dining';
		$data['culture_try2'] = 'absailing';
		$data['culture_try3'] = 'random housecalling';
	
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
	
}
