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

	//Normal Call to Page
	//No idea what is happening with this any more, no one has told me...
	//I was working on this page linked to the model
	//however I think I'll wait and find out before continuing

	function index()
	{	
		$data['title_image'] = 'images/prototype/reviews/reviews_01.gif';
		$data['food_image'] = '/images/prototype/reviews/reviews_07.jpg';
		$data['drink_image'] = '/images/prototype/reviews/reviews_07.jpg';
		$data['culture_image'] = '/images/prototype/reviews/reviews_07.jpg';
		
		$data['food_title'] = 'images/reviews/contentpage/food_title.gif';
		$data['drink_title'] = 'images/reviews/contentpage/drink_title.gif';
		$data['culture_title'] = 'images/reviews/contentpage/culture_title.gif';
		
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

	//Food Section - Dummy Data intill Model Ready
	function food()
	{
		//Dummy Data
		$data['article_title'] = 'Cake tastes nice';
		$data['article_author'] = 'Matthew Tole';
		$data['article_author_link'] = '/directory/view/1';
		$data['article_date'] = '5th December 2006';
		$data['article_link'] = '/reviews/foodreview/1';
		$data['article_content'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin';

		$type_array['name'] = array('Italian','Indian','Pub Dinners','Take Away Resturants','Thai','Chinese','All Types');
		$type_array['link'] = array('reviews/leagues/1/1','reviews/leagues/1/2','reviews/leagues/1/3','reviews/leagues/1/4','reviews/leagues/1/5','reviews/leagues/1/6','reviews/leagues/1/0');
		$data['type_array'] = $type_array;

		$price_array['name'] = array('Dirt Cheap','Super Cheap','Kinda Cheap','Meh','Mega Expensive','All Prices');
		$price_array['link'] = array('reviews/leagues/1/1','reviews/leagues/1/2','reviews/leagues/1/3','reviews/leagues/1/4','reviews/leagues/1/5','reviews/leagues/1/0');
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
		//Dummy Data
		$data['article_title'] = 'Liquid cake tastes awful';
		$data['article_author'] = 'Matthew Tole';
		$data['article_author_link'] = '/directory/view/1';
		$data['article_date'] = '5th December 2006';
		$data['article_link'] = '/reviews/drinkreview/1';
		$data['article_content'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin';

		$price_array['name'] = array('Dirt Cheap','Super Cheap','Kinda Cheap','Meh','Mega Expensive','All Prices');
		$price_array['link'] = array('reviews/leagues/2/1','reviews/leagues/2/2','reviews/leagues/2/3','reviews/leagues/2/4','reviews/leagues/2/5','reviews/leagues/2/0');
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
		//Dummy Data
		$data['article_title'] = 'Ever tried cake in the bath?';
		$data['article_author'] = 'Matthew Tole';
		$data['article_author_link'] = '/directory/view/1';
		$data['article_date'] = '5th December 2006';
		$data['article_link'] = '/reviews/culturereview/1';
		$data['article_content'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin';

		$location_array['name'] = array('York','Leeds','London','Manchester','Blackpool','All Locations');
		$location_array['link'] = array('reviews/leagues/3/1','reviews/leagues/3/2','reviews/leagues/3/3','reviews/leagues/3/4','reviews/leagues/3/5','reviews/leagues/3/0');
		$data['location_array'] = $location_array;

		$price_array['name'] = array('Dirt Cheap','Super Cheap','Kinda Cheap','Meh','Mega Expensive','All Prices');
		$price_array['link'] = array('reviews/leagues/3/1','reviews/leagues/3/2','reviews/leagues/3/3','reviews/leagues/3/4','reviews/leagues/3/5','reviews/leagues/3/0');
		$data['price_array'] = $price_array;
		
		// Set up the public frame
		$this->frame_public->SetTitle('Culture');
		$this->frame_public->SetContentSimple('reviews/culture',$data);
		
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
	//Yet more dummy data for view intill I replace it with data from model
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

		$data['review_link'] = array("/foodreview/","/foodreview/","/foodreview/","/foodreview/","/foodreview/","/foodreview/","/foodreview/","/foodreview/","/foodreview/","/foodreview/");

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

		$this->frame_public->SetTitle('Leagues');
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
