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
		$result['min'] = 0;
		$result['max'] = 0;
		$sql = 'SELECT	shop_item_customisation_id as id
				FROM	shop_item_customisations
				WHERE	shop_item_customisation_shop_item_id = ?
				AND		shop_item_customisation_deleted = 0';
		$query1 = $this->db->query($sql,array($item_id));
		foreach($query1->result() as $row1)
		{
			$sql = 'SELECT	max(shop_item_customisation_option_price) as max,
							min(shop_item_customisation_option_price) as min
					FROM	shop_item_customisation_options
					WHERE	shop_item_customisation_option_shop_item_customisation_id = ?
					AND		shop_item_customisation_option_deleted = 0';
			$query2 = $this->db->query($sql,array($row1->id));
			foreach($query2->result() as $row2)
			{
				$result['min'] += $row2->min;
				$result['max'] += $row2->max;
			}
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
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function HasCurrentBasket($user_id)
	{
		$sql = 'SELECT	shop_order_id as id,
						shop_order_price as price
				FROM	shop_orders
				WHERE	shop_order_user_entity_id = ?
				AND		shop_order_google_checkout_order_id IS NULL
				AND		shop_order_deleted = 0';
		$query = $this->db->query($sql,array($user_id));
		if ($query->num_rows() == 1)
		{
			return $query->row_array();
		}
		else
		{
			return false;
		}
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function CreateEmptyBasket($user_id)
	{
		$sql = 'INSERT INTO shop_orders (
					shop_order_user_entity_id,
					shop_order_price,
					shop_order_google_checkout_order_id,
					shop_order_deleted
				)
				VALUES (?, 0, NULL, 0)';
		$this->db->query($sql,array($user_id));
		return $this->db->insert_id();;
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetBasketItems($item_customisation_id)
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
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function CalculateItemCustPrice($item_id, $customisations, $quantity)
	{
		$price = 0;
		foreach($customisations as $customisation_id => $customisation_option_id)
		{
			$sql = 'SELECT	shop_item_customisation_option_price as price
					FROM	shop_item_customisation_options
					WHERE	shop_item_customisation_option_id = ?
					AND		shop_item_customisation_option_shop_item_customisation_id = ?
					AND		shop_item_customisation_option_deleted = 0';
			$query = $this->db->query($sql,array($customisation_option_id, $customisation_id));
			if ($query->num_rows() == 1)
			{
				$row = $query->row();
				$price += $row->price;
			}
		}
		return $price;
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function CalculateBasketPrice($basket_id)
	{
		$price = 0;
		$sql = 'SELECT	shop_order_item_price as price,
						shop_order_item_quantity as quantity
				FROM	shop_order_items
				WHERE	shop_order_item_shop_order_id = ?
				AND		shop_order_item_deleted = 0';
		$query = $this->db->query($sql,array($basket_id));
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$price += ($row->price * $row->quantity);
			}
		}
		return $price;
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function AddToBasket($basket_id, $item_id, $customisations, $quantity)
	{
		//does this item already exist? (so we can increase quantity)
		$sql = 'SELECT	shop_order_item_id as id,
						shop_order_item_quantity as quantity
				FROM	shop_order_items
				WHERE	shop_order_item_shop_order_id = ?
				AND		shop_order_item_shop_item_id = ?
				AND		shop_order_item_deleted = 0';
		$query1 = $this->db->query($sql,array($basket_id, $item_id));
		$shop_order_item_id = NULL;
		foreach($query1->result() as $row1)
		{
			$sql = 'SELECT	shop_order_item_customisation_shop_item_customisation_id as customisation_id,
							shop_order_item_customisation_shop_item_customisation_option_id as customisation_option_id
					FROM	shop_order_item_customisations
					WHERE	shop_order_item_customisation_shop_order_item_id = ?';
			$query2 = $this->db->query($sql,array($row1->id));
			$item_cust_match = true;
			foreach($customisations as $customisation_id => $customisation_option_id)
			{
				$cust_match = false;
				foreach($query2->result() as $row2)
				{
					if ($row2->customisation_id == $customisation_id &&
						$row2->customisation_option_id == $customisation_option_id)
					{
						$cust_match = true;						
					}
				}
				if (!$cust_match)
				{
					$item_cust_match = false;
				}
			}
			if ($item_cust_match)
			{
				$shop_order_item_id = $row1->id;
				$shop_order_item_quantity = $row1->quantity;
			}
		}
		//doesn't exist add new
		if ($shop_order_item_id == NULL)
		{
			$this->db->trans_start();
			$price = $this->CalculateItemCustPrice($item_id, $customisations, $quantity);
			$sql = 'INSERT INTO shop_order_items (
						shop_order_item_shop_order_id,
						shop_order_item_shop_item_id,
						shop_order_item_quantity,
						shop_order_item_deleted,
						shop_order_item_price
					)
					VALUES (?, ?, ?, 0, ?)';
			$this->db->query($sql,array($basket_id, $item_id, $quantity, $price));
			$shop_order_item_id = $this->db->insert_id();
			foreach($customisations as $customisation_id => $customisation_option_id)
			{
				$sql = 'INSERT INTO shop_order_item_customisations (
							shop_order_item_customisation_shop_order_item_id,
							shop_order_item_customisation_shop_item_customisation_id,
							shop_order_item_customisation_shop_item_customisation_option_id
						)
						VALUES (?, ?, ?)';
				$this->db->query($sql,array($shop_order_item_id, $customisation_id, $customisation_option_id));
			}
			$this->db->trans_complete();
		}
		//update existing
		else
		{
			$new_quantity = $shop_order_item_quantity + $quantity;
			$sql = 'UPDATE	shop_order_items
					SET		shop_order_item_quantity = ?
					WHERE	shop_order_item_id = ?
					AND		shop_order_item_deleted = 0';
			$this->db->query($sql, array($new_quantity, $shop_order_item_id));
		}
		$total_price = $this->CalculateBasketPrice($basket_id);
		$sql = 'UPDATE	shop_orders
				SET		shop_order_price = ?
				WHERE	shop_order_id = ?
				AND		shop_order_deleted = 0';
		$this->db->query($sql, array($total_price, $basket_id));
		return true;
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetItemsInBasket($basket_id)
	{
		$sql = 'SELECT	shop_order_item_id as order_item_id,
						shop_order_item_shop_item_id as item_id,
						shop_order_item_quantity as quantity,
						shop_order_item_id as basket_item_id,
						shop_item_name as item_name,
						shop_item_description as item_description,
						shop_order_item_price as price
				FROM	shop_order_items
				JOIN	shop_items
				ON		shop_item_id = shop_order_item_shop_item_id
				WHERE	shop_order_item_shop_order_id = ?
				AND		shop_order_item_deleted = 0
				AND		shop_order_item_quantity > 0
				ORDER BY item_name ASC';
		$query = $this->db->query($sql, array($basket_id));
		$basket_items = $query->result_array();
		foreach($basket_items as &$basket_item)
		{
			$sql = 'SELECT	shop_order_item_customisation_id as order_item_cust_id,
							shop_order_item_customisation_shop_item_customisation_id as customisation_id,
							shop_order_item_customisation_shop_item_customisation_option_id as customisation_option_id,
							shop_item_customisation_option_name as option_name
					FROM	shop_order_item_customisations
					JOIN	shop_item_customisation_options
					ON		shop_item_customisation_option_id = shop_order_item_customisation_shop_item_customisation_option_id
					WHERE	shop_order_item_customisation_shop_order_item_id = ?';
			$query2 = $this->db->query($sql, array($basket_item['order_item_id']));
			$basket_item['customisations'] = $query2->result_array();
		}
		return $basket_items;
	}
	
	/*
	* description
	* @assumes	- $item_order_id is in the users basket and not anothers
	* @return	- thing 1
			- thing 2
	*/
	function UpdateOrderQuantity($item_order_id, $quantity_change, $basket_id)
	{
		$sql = 'UPDATE	shop_order_items
				SET		shop_order_item_quantity = (shop_order_item_quantity + ?)
				WHERE	shop_order_item_id = ?
				AND		shop_order_item_deleted = 0';
		$this->db->query($sql, array($quantity_change, $item_order_id));
		$total_price = $this->CalculateBasketPrice($basket_id);
		$sql = 'UPDATE	shop_orders
				SET		shop_order_price = ?
				WHERE	shop_order_id = ?
				AND		shop_order_deleted = 0';
		$this->db->query($sql, array($total_price, $basket_id));
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function IsItemOrderInBasket($item_order_id, $basket_id)
	{
		$sql = 'SELECT	shop_order_item_shop_order_id as basket_id
				FROM	shop_order_items
				WHERE	shop_order_item_id = ?
				AND		shop_order_item_deleted = 0';
		$query = $this->db->query($sql, array($item_order_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			if ($basket_id == $row->basket_id)
			{
				return true;
			}
		}
		return false;
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GeneratePasscode($item_customisation_id)
	{
		return 'RI45SDFS3D';
	}

}