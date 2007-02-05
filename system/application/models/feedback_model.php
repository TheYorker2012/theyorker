<?php
/**
 * This model inserts data from feedback forms.
 *
 * @author Richard Ingle (ri504)
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
}