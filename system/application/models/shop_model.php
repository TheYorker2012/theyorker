<?php

class Shop_model extends Model
{

	function Shop_model()
	{
		parent::Model();
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetCategoryInformation($category_id)
	{
		$sql = 'SELECT	shop_category_id as id,
						shop_category_name as name
				FROM	shop_categories
				WHERE	shop_category_deleted = 0
				AND		shop_category_id = ?';
		$query = $this->db->query($sql,array($category_id));
		return $query->row_array();
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetCategoryListing()
	{
		$sql = 'SELECT	shop_category_id as id,
						shop_category_name as name,
						shop_category_order as list_order
				FROM	shop_categories
				WHERE	shop_category_deleted = 0
				ORDER BY shop_category_order ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetCategoryItemListing($category_id)
	{
		$sql = 'SELECT	shop_item_category_shop_category_id as cid,
						shop_item_category_shop_item_id as sid,
						shop_item_id as id,
						shop_item_name as name,
						shop_item_blurb as blurb,
						shop_item_description as description,
						shop_item_thumb_photo_id as thumb_id,
						UNIX_TIMESTAMP(shop_item_event_date) as event_date
				FROM	shop_item_categories
				JOIN	shop_items
				ON		shop_item_id = shop_item_category_shop_item_id
				WHERE	shop_item_deleted = 0
				AND		shop_item_approved = 1
				AND		shop_item_category_shop_category_id = ?
				AND		shop_item_start_sell_time <= CURRENT_TIMESTAMP
				AND		shop_item_end_sell_time >= CURRENT_TIMESTAMP
				ORDER BY shop_item_end_sell_time DESC';
		$query = $this->db->query($sql,array($category_id));
		$result = $query->result_array();
		foreach ($result as &$item) {
			$item['price_range'] = $this->GetItemPriceRange($item['id']);
		}
		return $result;
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetItemInformation($item_id)
	{
		$sql = 'SELECT	shop_item_id as id,
						shop_item_name as name,
						shop_item_blurb as blurb,
						shop_item_description as description,
						shop_item_thumb_photo_id as thumb_id,
						shop_item_num_available as num_available,
						shop_item_max_per_user as max_per_user,
						UNIX_TIMESTAMP(shop_item_event_date) as event_date
				FROM	shop_items
				WHERE	shop_item_deleted = 0
				AND		shop_item_approved = 1
				AND		shop_item_id = ?
				AND		shop_item_start_sell_time <= CURRENT_TIMESTAMP
				AND		shop_item_end_sell_time >= CURRENT_TIMESTAMP';
		$query = $this->db->query($sql,array($item_id));
		$result = $query->row_array();
		$result['price_range'] = $this->GetItemPriceRange($result['id']);
		return $result;
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetItemPriceRange($item_id)
	{
		$sql = 'SELECT	max(shop_item_customisation_option_price) as max,
						min(shop_item_customisation_option_price) as min
				FROM	shop_item_customisation_options
				JOIN	shop_item_customisations
				ON		shop_item_customisation_id = shop_item_customisation_option_shop_item_customisation_id
				WHERE	shop_item_customisation_shop_item_id = ?
				AND		shop_item_customisation_option_deleted = 0
				AND		shop_item_customisation_deleted = 0';
		$query = $this->db->query($sql,array($item_id));
		foreach ($query->row_array() as $key => $price) {
			$result[$key] = number_format($price, 2);
		}
		return $result;
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetItemCustomisations($item_id)
	{
		$sql = 'SELECT	shop_item_customisation_id as id,
						shop_item_customisation_name as name
				FROM	shop_item_customisations
				WHERE	shop_item_customisation_deleted = 0
				AND		shop_item_customisation_shop_item_id = ?
				ORDER BY shop_item_customisation_order ASC';
		$query = $this->db->query($sql,array($item_id));
		return $query->result_array();
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetItemCustomisationOptions($item_customisation_id)
	{
		$sql = 'SELECT	shop_item_customisation_option_id as id,
						shop_item_customisation_option_name as name,
						shop_item_customisation_option_price as price
				FROM	shop_item_customisation_options
				WHERE	shop_item_customisation_option_deleted = 0
				AND		shop_item_customisation_option_shop_item_customisation_id = ?
				ORDER BY shop_item_customisation_option_order ASC';
		$query = $this->db->query($sql,array($item_customisation_id));
		return $query->result_array();
	}

}