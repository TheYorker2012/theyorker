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
		$type_array['link'] = array('reviews/tables/1/1','reviews/tables/1/2','reviews/tables/1/3','reviews/tables/1/4','reviews/tables/1/5','reviews/tables/1/6','reviews/tables/1/0');
		$data['type_array'] = $type_array;

		$price_array['name'] = array('Dirt Cheap','Super Cheap','Kinda Cheap','Meh','Mega Expensive','All Prices');
		$price_array['link'] = array('reviews/tables/1/1','reviews/tables/1/2','reviews/tables/1/3','reviews/tables/1/4','reviews/tables/1/5','reviews/tables/1/0');
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
		$price_array['link'] = array('reviews/tables/2/1','reviews/tables/2/2','reviews/tables/2/3','reviews/tables/2/4','reviews/tables/2/5','reviews/tables/2/0');
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
		$location_array['link'] = array('reviews/tables/3/1','reviews/tables/3/2','reviews/tables/3/3','reviews/tables/3/4','reviews/tables/3/5','reviews/tables/3/0');
		$data['location_array'] = $location_array;

		$price_array['name'] = array('Dirt Cheap','Super Cheap','Kinda Cheap','Meh','Mega Expensive','All Prices');
		$price_array['link'] = array('reviews/tables/3/1','reviews/tables/3/2','reviews/tables/3/3','reviews/tables/3/4','reviews/tables/3/5','reviews/tables/3/0');
		$data['price_array'] = $price_array;
		
		// Set up the public frame
		$this->frame_public->SetTitle('Culture');
		$this->frame_public->SetContentSimple('reviews/culture',$data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	//Review Function for Food/Drink/Culture
	function mainreview($type)
	{
		//Dummy data
		$data['article_title']= 'The Blue Bicycle';
		$data['also_does_state']= 5;  //Food is 4, Drink is 2, Culture is 1, Add together
		$data['article_blurb']= 'An unholy resturant which defies the laws the gravity and other such stuffs';
		$data['article_image']= '/images/prototype/reviews/reviews_07.jpg';
		$data['article_content']= 'The Blue Bicycle was founded in 1953 by the roman empire. They discovered a large patch of land covered in blue objects, at the time they did not know of what these objects were. It wasnt until the late 90s that the romans realised these framed objects with strange spherical rubbery things were infact bicycles. Blue bicycles. This was a shock to the romans so they built a time machine and erased history using the time machine built by the great roman god Dudeadeus. This is probably why no one knows what I am talking about.';
		$data['email'] = 'bob@thebluebicycle.co.uk';
		$data['address_line_1']= '21 Blue Road,';
		$data['address_line_2']= 'Fulford';
		$data['address_line_3']= 'York';
		$data['address_postcode']= 'YO10 5PP';
		$data['website']= 'www.thebluebicycle.co.uk';
		$data['website_booking']= 'none';
		$data['telephone']= '0194 729572';
		$data['average_price']= '2.50';
		$data['opening_times']= 'MON - FRI 9:00 - Late';
		$data['yorker_recommendation']= 'Duck on Your Face';
		$data['price_rating']= 'Dirt Cheap';

		//User Comments
		
		$data['comments'] = Reviews::comments(1); //Calls function comments with Page No
		
		// Set up the public frame

		switch($type)
		{
		case 0:
			$this->frame_public->SetTitle('Food Review');
			$this->frame_public->SetContentSimple('reviews/foodreview',$data);
		break;

		case 1:
			$this->frame_public->SetTitle('Drink Review');
			$this->frame_public->SetContentSimple('reviews/foodreview',$data);
		break;

		case 2:
			$this->frame_public->SetTitle('Culture Review');
			$this->frame_public->SetContentSimple('reviews/foodreview',$data);
		break;
		
		}

			// Load the public frame view (which will load the content view)
			$this->frame_public->Load();

	}
	
	function foodreview()
	{
		Reviews::mainreview(0); //Both are same format
	}

	function drinkreview()
	{
		Reviews::mainreview(1); //Both are same format
	}
	
	//Culture Review
	function culturereview()
	{
		Reviews::mainreview(2); //Same again
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

	//Display tables for review tables (from puffers)
	//Yet more dummy data for view intill I replace it with data from model
	function table()
	{
		
		$reviews['review_image'] = array(
			'/images/prototype/news/thumb3.jpg',
			'/images/prototype/news/thumb3.jpg',
			'/images/prototype/news/thumb3.jpg',
			'/images/prototype/news/thumb3.jpg',
			'/images/prototype/news/thumb3.jpg',
			'/images/prototype/news/thumb3.jpg',
			'/images/prototype/news/thumb3jpg',
			'/images/prototype/news/thumb3.jpg',
			'/images/prototype/news/thumb3.jpg',
			'/images/prototype/news/thumb3.jpg');

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

		$reviews['review_user_rating'] = array(7,3,8,3,2,8,4,6,2,5);

		$reviews['review_cost_type'] = array('Expensive','Cheap','Cheap','Average','Expensive',
											'Cheap','Average','Expensive','Cheap','Average');

		$data['review_link'] = array('/foodreview/','/foodreview/','/foodreview/','/foodreview/','/foodreview/','/foodreview/','/foodreview/','/foodreview/','/foodreview/','/foodreview/');

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

		$this->frame_public->SetTitle('tables');
		$this->frame_public->SetContentSimple('reviews/table',$data);
		$this->frame_public->Load();
	}

	function comments($pageno)
	{
		$comments['comment_author'][0] = 'Serious Girl San';
		$comments['comment_score'][0] = '9';
		$comments['comment_date'][0] = '16:09 12/12/07';
		$comments['comment_content'][0] = 'This resturant is a lovely place. I like to come here with my parents on rainy weekends. I think teddy and very immature and should be reported. Becky is even worse for supporting his stupidity.';

		$comments['comment_author'][1] = 'Becky DoomFace';
		$comments['comment_score'][1] = '4';
		$comments['comment_date'][1] = '14:56 12/12/07';
		$comments['comment_content'][1] = 'Hah! Teddy is so funny. He is rite tho. LololoCaPITALS!';

		$comments['comment_author'][2] = 'Teddy BranVan';
		$comments['comment_score'][2] = '5';
		$comments['comment_date'][2] = '14:51 12/12/07';
		$comments['comment_content'][2] = 'I thought it sucked';

		return $comments;

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
