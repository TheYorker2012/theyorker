<?php
/**
 *  @file shop.php
 *  @author Richard Ingle (ri504)
 *  Contains public site controller class
 */

class Shop extends Controller {

	function __construct()
	{
		parent::Controller();
		/// Used in all all page requests
		$this->load->model('shop_model','shop_model');
	}
	
	/**
	 *  @brief Listing of events with tickets on sale
	 */
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('shop');
		
		$data = array();
		
		$data['shop'] = 'shop index';
				
		// Set up the public frame
		$this->main_frame->SetContentSimple('shop/listing', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function view($category_name)
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('shop');
		
		$data = array();
		
		$data['shop'] = $this->shop_model->GetEventIDs();
		
		foreach ($data['shop'] as $shops)
		{
			$source_id = 0; //events created on the yorker
			$this->load->library('calendar_backend');
			$this->load->library('calendar_source_my_calendar');
			$mMainSource = new CalendarSourceMyCalendar();
			$calendar_data = new CalendarData();
			$mMainSource->FetchEvent($calendar_data, $source_id, $shops['id']);
		}
				
		// Set up the public frame
		$this->main_frame->SetContentSimple('shop/listing', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
