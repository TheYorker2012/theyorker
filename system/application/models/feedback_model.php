<?php
/**
 * This model inserts data from feedback forms.
 *
 * @author Richard Ingle (ri504)
 * @author Chris Travis (cdt502 - ctravis@gmail.com)
 *
 */
 
class Feedback_model extends Model
{
	function FeedbackModel()
	{
		//Call the Model Constructor
		parent::Model();
	}

	function AddNewFeedback($page_name, $author_name, $author_email, 
				$rating, $comment)
	{
		$sql = 'INSERT INTO feedback_entries (
				feedback_entry_page_name,
				feedback_entry_author_name,
				feedback_entry_author_email,
				feedback_entry_rating,
				feedback_entry_comment,
				feedback_entry_timestamp)
			VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)';
		$this->db->query($sql, array($page_name, $author_name,
				$author_email, $rating, $comment));
	}

	/// Get number of outstanding feedback comments
	function GetFeedbackCount ($deleted = 0)
	{
		$sql = 'SELECT COUNT(*) AS feedback_count
				FROM feedback_entries
				WHERE feedback_entry_deleted = ?';
		$query = $this->db->query($sql,array($deleted));
		$result = $query->row_array();
		return $result['feedback_count'];
	}

	/// Get all feedback entries for display in admin area for review
	function GetAllFeedback ($deleted = 0)
	{
		$sql = 'SELECT feedback_entry_id AS id,
				 feedback_entry_page_name AS page,
				 feedback_entry_author_name AS author,
				 feedback_entry_author_email AS email,
				 feedback_entry_rating AS rating,
				 feedback_entry_comment AS comment,
				 DATE_FORMAT(feedback_entry_timestamp, \'%a, %D %b %y @ %H:%i\') AS time
				FROM feedback_entries
				WHERE feedback_entry_deleted = ?
				ORDER BY feedback_entry_timestamp DESC';
		$query = $this->db->query($sql, array($deleted));
		$result = array();
		foreach ($query->result_array() as $row) {
			$result[] = $row;
		}
		return $result;
	}

	function DeleteFeedback ($entry_id)
	{
		$sql = 'UPDATE feedback_entries
				SET feedback_entry_deleted = 1
				WHERE feedback_entry_id = ?';
		$query = $this->db->query($sql, array($entry_id));
		return $this->db->affected_rows();
	}
}