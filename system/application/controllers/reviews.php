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
		// Set up the public frame
		$this->frame_public->SetTitle('Reviews');
		$this->frame_public->SetContentSimple('reviews/index');
		
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
		$data['article_link'] = '/reviews/foodreview/evil_eye_lounge';
		$data['article_content'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin';

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
		//Dummy Data
		$data['article_title'] = 'Liquid cake tastes awful';
		$data['article_author'] = 'Matthew Tole';
		$data['article_author_link'] = '/directory/view/1';
		$data['article_date'] = '5th December 2006';
		$data['article_link'] = '/reviews/drinkreview/evil_eye_lounge';
		$data['article_content'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin';

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
		//Dummy Data
		$data['article_title'] = 'Ever tried cake in the bath?';
		$data['article_author'] = 'Matthew Tole';
		$data['article_author_link'] = '/directory/view/1';
		$data['article_date'] = '5th December 2006';
		$data['article_link'] = '/reviews/culturereview/evil_eye_lounge';
		$data['article_content'] = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin';

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
	function mainreview($review_type,$page_code)
	{
		// Set up the public frame

		switch($review_type)
		{
		case 0:
			$data['page_id'] = 101;
			$data['comments'] = $this->Review_model->GetComments(101,$page_code); //User comments
			$database_result = $this->Review_model->GetReview($page_code,'food');
			$this->frame_public->SetTitle('Food Review');
		break;

		case 1:
			$data['page_id'] = 102;
			$data['comments'] = $this->Review_model->GetComments(102,$page_code); //User comments
			$database_result = $this->Review_model->GetReview($page_code,'drink');
			$this->frame_public->SetTitle('Drink Review');
		break;

		case 2:
			$data['page_id'] = 103;
			$data['comments'] = $this->Review_model->GetComments(103,$page_code); //User comments
			$database_result = $this->Review_model->GetReview($page_code,'culture');
			$this->frame_public->SetTitle('Culture Review');
		break;
		
		}

		//N.B There should only be 1 row returned hence [0] as it should be unique
		$data['article_title']= $database_result[0]['organisation_name'];
		$data['also_does_state']= 5;  //Food is 4, Drink is 2, Culture is 1, Add together
		$data['article_blurb']= $database_result[0]['review_context_content_blurb'];
		$data['article_image']= '/images/prototype/reviews/reviews_07.jpg';
		$data['article_content']= 'The articles content - Waiting on model<BR>Evil Eye is blah fijsdofijsdofijsdfo djfsdoifjsdoifjsi place to be<BR>JIOJSDIFJSDio dkdk KOJOmk dsfmkdmfklsdf<BR>
So as you all now know the decline of pirates <BR> is the main factor behind global warming<BR>';
		$data['email'] = $database_result[0]['organisation_email_address'];
		$data['address_main']= $database_result[0]['organisation_postal_address'];
		$data['address_postcode']= $database_result[0]['organisation_postcode'];
		$data['website']= $database_result[0]['organisation_url'];
		$data['website_booking']= $database_result[0]['review_context_content_book_online'];
		$data['telephone']= $database_result[0]['organisation_phone_external'];
		$data['average_price']= '£'.($database_result[0]['review_context_content_average_price_upper']/100).' to £'.($database_result[0]['review_context_content_average_price_upper']/100);
		$data['opening_times']= $database_result[0]['organisation_opening_hours'];
		$data['yorker_recommendation']= $database_result[0]['review_context_content_rating'];
		$data['price_rating']= 'Waiting on Model';


		// Load the public frame view (which will load the content view)
		$this->frame_public->SetContentSimple('reviews/foodreview',$data);
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
	//Yet more dummy data for view intill I replace it with data from model
	function table()
	{
		$item_type = $this->uri->segment(3); //Expected food/drink/culture/any
//		Implentment/Normailise later
//		$item_filter_by = $this->uri->segment(4); //Expected price/sub type/etc..../any
//		$where_equal_to = $this->uri->segment(5); //Expected italian/late night/etc..../any
//		$sorted_by = $this->uri->segment(6); //name/star/price/user/any

		$database_result = $this->Review_model->TableReview($item_type);

		$reviews['']=''; //This line doesn't make sense to you? Don't touch it then :)

		for ($review_entry = 0; $review_entry < $database_result['item_count']; $review_entry++)
		{
			$reviews['review_image'][$review_entry] = '/images/prototype/news/thumb3.jpg';
			$reviews['review_title'][$review_entry] = $database_result[$review_entry]['organisation_name'];
			$reviews['review_website'][$review_entry] = $database_result[$review_entry]['organisation_url'];;
			$reviews['review_rating'][$review_entry] = $database_result[$review_entry]['review_context_content_rating'];
			$reviews['review_user_rating'][$review_entry] = '???';
			$reviews['review_cost_type'][$review_entry] = '???';
			$reviews['review_table_link'][$review_entry] = base_url().'reviews/'.$item_type.'review/'.$database_result[$review_entry]['organisation_directory_entry_name'];
			$reviews['review_blurb'][$review_entry] = $database_result[$review_entry]['review_context_content_blurb'];
		}

		$data['reviews'] = $reviews;

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
