<?php
/**
 * This model does queries for the searches.
 *
 * @author Mark Goodall (mg512)
 * 
 */

// To add a new filter:
// 1. use a new bit in a define and name it well
// 2. add a new if statement to full and ajax functions, with desired query

define("ORDER_RELEVANCE", 	1);
define("ORDER_EARLY", 		2);
define("ORDER_LATE", 		3);

define("FILTER_ALL", 		0b1111111111111111);
define("FILTER_NEWS", 		0b0000000000000001);
define("FILTER_FEATURES", 	0b0000000000000010);
define("FILTER_LIFESTYLE", 	0b0000000000000100);
define("FILTER_FOOD",		0b0000000000001000);
define("FILTER_DRINK", 		0b0000000000010000);
define("FILTER_CULTURE",	0b0000000000100000);
define("FILTER_YORK",		0b0000000001000000);

class Search extends Model
{
	function Search()
	{
		//Call the Model Constructor
		parent::Model();
	}
	
}

	/**
	 * @brief Do a full search
	 * @param $string string Search string entered.
	 * @param $ordering integer Ordering of the search, if defined.
	 * @param $filter integer Filtering of the search, if defined.
	 */
	function full($string, $ordering = ORDER_RELEVANCE, $filter =FILTER_ALL) {
		function ordering_addition($ordering, &$query) {
			switch($ordering) {
				case ORDER_RELEVANCE: return $this->db->orderby('something','desc');
				break;
				case ORDER_EARLY: return $this->db->orderby('something', 'asc')
				break;
				case ORDER_LATE: return $this->db->orderby('something', 'desc')
				break;
				default: return false;
				break;
			}
		}
		
		$result = array();
		
		//TODO replace pseudocode
		$query = $this->db->select('news title, text')->from('twelveteen tables')->join('table', 'conditions')
		
		if ($filter & FILTER_NEWS) {
		}
		if ($filter & FILTER_FEATURES) {
		}
		if ($filter & FILTER_LIFESTYLE) {
		}
		if ($filter & FILTER_FOOD) {
		}
		if ($filter & FILTER_DRINK) {
		}
		if ($filter & FILTER_CULTURE) {
		}
		if ($filter & FILTER_YORK) {
		}
		$query = ordering_addition($ordering, $query);
		...
		return $result;
	}

	/**
	 * @brief Do a small search, reply with max 16 results
	 * @param $string string Search string entered.
	 */
	function ajax($string) {
		
	}

?>