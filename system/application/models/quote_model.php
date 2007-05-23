<?php
/*
 * Model for use with quotes office
 *
 *
 * \author Nick Evans nse500
 *
 *
 *
 */
class Quote_Model extends Model {
	/*
	 * Constructor, calls default model constructor
	 */
	function Quote_Model() {
		parent::Model();
	}

	/*
	 * Function to return a list of the schedualed and unscheduled quotes.
	 * Returns the quote_text and quote_author in an array.
	 */
	function GetQuotes() {
		$sql = 'SELECT quote_id, quote_text, quote_author, quote_last_displayed_timestamp
			FROM quotes
			WHERE DATE(quote_last_displayed_timestamp) >= CURRENT_DATE()';
		$query = $this->db->query($sql);

		$scheduled_quotes = $query->result_array();

		$sql = 'SELECT quote_id, quote_text, quote_author
			FROM quotes
			WHERE 1
			ORDER BY quote_last_displayed_timestamp
			LIMIT 0,10';
		$query = $this->db->query($sql);

		$unscheduled_quotes = $query->result_array();

		return array_merge($scheduled_quotes, $unscheduled_quotes);
	}

	/*
	 * Function to obtain a particular quote.
	 * Returns the quote_id, quote_text, quote_author and quote_last_displayed_timestamp in an array.
	 */
	function GetQuote($quote_id) {
		$sql = 'SELECT quote_id, quote_text, quote_author, IF( DATE(quote_last_displayed_timestamp) >= CURRENT_DATE(), quote_last_displayed_timestamp, null) as quote_last_displayed_timestamp
			FROM quotes
			WHERE quote_id = ?';
		$query = $this->db->query($sql, array($quote_id));
		return $query->row();
	}

	/*
	 * Function to update a particular quote.
	 * Returns the the number of rows affected.
	 */
	function UpdateQuote($quote_id, $quote_text, $quote_author, $quote_last_displayed_timestamp = null) {
			$sql = 'UPDATE quotes
				SET quote_text = ?, quote_author = ?
				'.($quote_last_displayed_timestamp != null ? ', quote_last_displayed_timestamp = ?' : '/* ? */').'
				WHERE quote_id = ?';
			$update = $this->db->query($sql,array($quote_text, $quote_author, $quote_last_displayed_timestamp, $quote_id));
		return true;
	}

	/*
	 * Function to update a particular quote.
	 * Returns the the number of rows affected.
	 */
	function RemoveQuote($quote_id) {
		$sql = 'DELETE FROM quotes WHERE quote_id = ?';
		$update = $this->db->query($sql,array($quote_id));
		return true;
	}


}
?>
