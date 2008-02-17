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
		$this->load->library('image');
	}
	
	/**
	 *  @brief Listing of events with tickets on sale
	 */
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$data['basket'] = $this->_getbasket();
		
		$this->pages_model->SetPageCode('shop');
		
		$data['categories'] = $this->shop_model->GetCategoryListing();
				
		// Set up the public frame
		$this->main_frame->SetContentSimple('shop/categories', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function tickets()
	{
		redirect('shop/view/1');
	}
	
	function view($category_id)
	{
		if (!CheckPermissions('student')) return;
		
		$data['basket'] = $this->_getbasket();
		
		$this->pages_model->SetPageCode('shop');
		
		$data['items'] = $this->shop_model->GetCategoryItemListing($category_id);
		$data['category'] = $this->shop_model->GetCategoryInformation($category_id);
		foreach ($data['items'] as &$item)
		{
			$item['event_date_string'] = date('l, jS F Y', $item['event_date']);
			if ($item['price_range']['min'] == $item['price_range']['max'])
			{
				$item['price_string'] = '&pound;'.$item['price_range']['min'];
			}
			else
			{
				$item['price_string'] = '&pound;'.$item['price_range']['min'].' to '.'&pound;'.$item['price_range']['max'];
			}
			//$item['thumb_details'] = $this->image->getThumb($item['thumb_id'], 1);
		}
		
		/*
		foreach ($data['shop'] as $shops)
		{
			$source_id = 0; //events created on the yorker
			$this->load->library('calendar_backend');
			$this->load->library('calendar_source_my_calendar');
			$mMainSource = new CalendarSourceMyCalendar();
			$calendar_data = new CalendarData();
			$mMainSource->FetchEvent($calendar_data, $source_id, $shops['id']);
		}*/
				
		// Set up the public frame
		$this->main_frame->SetContentSimple('shop/items', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function item($item_id)
	{
		if (!CheckPermissions('student')) return;
		
		$data['basket'] = $this->_getbasket();
		
		if (isset($_POST['r_submit_add']))
		{
			$this->messages->AddDumpMessage('post', $_POST);
			$this->shop_model->AddToBasket(
				$data['basket']['id'], 
				$_POST['r_item_id'], 
				$_POST['a_customisation'], 
				$_POST['a_quantity']
				);
			$data['basket'] = $this->_getbasket();
		}
		
		$this->pages_model->SetPageCode('shop_item');
		
		$data['item'] = $this->shop_model->GetItemInformation($item_id);
		
		if ($data['item'] == array())
		{
			$this->messages->AddMessage('error', 'The specified item does not exist.');
			redirect('shop');
		}
		
		$data['item']['event_date_string'] = date('l, jS F Y', $data['item']['event_date']);
		if ($data['item']['price_range']['min'] == $data['item']['price_range']['max'])
		{
			$data['item']['price_string'] = '&pound;'.$data['item']['price_range']['min'];
		}
		else
		{
			$data['item']['price_string'] = '&pound;'.$data['item']['price_range']['min'].' to '.'&pound;'.$data['item']['price_range']['max'];
		}
		$data['item']['customisations'] = $this->shop_model->GetItemCustomisations($item_id);
		foreach ($data['item']['customisations'] as &$customisation)
		{
			$customisation['options'] = $this->shop_model->GetItemCustomisationOptions($customisation['id']);
			foreach ($customisation['options'] as &$option)
			{
				$option['price_string'] = '&pound;'.number_format($option['price'], 2);
			}
		}
				
		// Set up the public frame
		$this->main_frame->SetContentSimple('shop/item_details', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function checkout()
	{
		if (!CheckPermissions('student')) return;

		$data = array();
		
		$data['basket'] = $this->_getbasket();

		// Set up the public frame
		$this->main_frame->SetContentSimple('shop/checkout', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function testing()
	{
		if (!CheckPermissions('student')) return;
		
		$this->pages_model->SetPageCode('shop_testing');
		
		$data = array();
		
		//$data['categories'] = $this->shop_model->GetCategoryListing();
		//$data['cat_items'] = $this->shop_model->GetCategoryItemListing(1);
		//$data['cat_info_id_1'] = $this->shop_model->GetCategoryInformation(1);
		//$data['item_1_price_range'] = $this->shop_model->GetPriceRange(1);
		//$data['item_1_info'] = $this->shop_model->GetItemInformation(1);
		$data['item_1_customisations'] = $this->shop_model->GetItemCustomisations(3);
		$data['item_1_customisation_options'] = $this->shop_model->GetItemCustomisationOptions(1);
				
		// Set up the public frame
		$this->main_frame->SetContentSimple('shop/testing', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function _getbasket()
	{
		$data = $this->shop_model->HasCurrentBasket($this->user_auth->entityId);
		if ($data['id'] == false)
		{
			$data['id'] = $this->shop_model->CreateEmptyBasket($this->user_auth->entityId);
			$data['price'] = 0;
		}
		$data['price_string'] = '&pound;'.number_format($data['price'], 2);
		$data['items'] = $this->shop_model->GetItemsInBasket($data['id']);
		foreach ($data['items'] as &$basket_item)
		{
			$cust_array = array();
			foreach ($basket_item['customisations'] as $basket_item_cust)
			{
				$cust_array[] = $basket_item_cust['option_name'];
			}
			$basket_item['price_string'] = '&pound;'.number_format($basket_item['price'], 2);
			$basket_item['cust_string'] = implode(', ', $cust_array);
		}
		
		return $data;
	}
}
?>
