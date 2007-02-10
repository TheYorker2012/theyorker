<?php
/**
 *Template for request_model, not complete
 *@author Alex Fargus (agf501)
 **/

class Requests_Model extends Model
{
	function RequestsModel()
	{
		parent::Model();
	}

	//Add a  new request to the article table
	function CreateRequest($status,$type_id,$title,$description,$user)
	{
		if ($status == 'suggestion')
		{
			$this->db->trans_start();
			$sql = 'INSERT INTO articles(
					article_content_type_id,
					article_created,
					article_request_title,
					article_request_description,
					article_suggestion_accepted,
					article_request_entity_id)
				VALUES (?,CURRENT_TIMESTAMP,?,?,0,?)';
			$this->db->query($sql,array($type_id,$title,$description,$user));
			$sql = 'SELECT 	article_id 
				FROM	articles
				WHERE	(article_id=LAST_INSERT_ID())';
			$query = $this->db->query($sql);
			$id = $query->row()->article_id;
			$this->db->trans_complete();
			
			return $id;
		}
		elseif ($status == 'request')
		{
			$this->db->trans_start();
			$sql = 'INSERT INTO articles(
					article_content_type_id,
					article_created,
					article_request_title,
					article_request_description,
					article_suggestion_accepted,
					article_request_entity_id
					article_editor_approved_user_etity_id)
				VALUES (?,CURRENT_TIMESTAMP,?,?,1,?,?)';
			$query = $this->db->query($sql,array($type_id,$title,$description,$user,$user));
			$sql = 'SELECT 	article_id 
				FROM	articles
				WHERE	(article_id=LAST_INSERT_ID())';
			$this->db->query($sql);
			$id = $query->row()->article_id;
			$this->db->trans_complete();
			
			return $id;
		}
		else
		{
			return FALSE;
		}
	}

	//Make a change to a request status in the article table
	function UpdateRequest($article_id,$data)
	{
		return $status;
	}

	function GetPulledArticles($type_id)
	{

	}

	//Should this be get completed articles?
	function GetPublishedArticles($type_id, $is_published)
	{
		$sql = 'SELECT	article_id,
				article_publish_date,
				article_live_content_id,
				article_content_last_author_timestamp,
				article_content_heading,
                                article_content_last_author_user_entity_id,
				article_editor_approved_user_entity_id,
				author_user.business_card_name as author_name
				editor_user.business_card_name as editor_name
			FROM	articles
			JOIN	article_contents
			ON      article_content_id = article_live_content_id

			JOIN	business_cards as editor_user
			ON	editor_user.business_card_user_entity_id = article_editor_approved_user_entity_id
			JOIN	business_cards as author_user
			ON	author_user.business_card_user_entity_id = article_content_last_author_user_entity_id

			WHERE	article_suggestion_accepted = 1
			AND	article_content_type_id = ?
			AND	article_live_content_id IS NOT NULL
			AND	article_deleted = 0
			AND	article_pulled = 0';
		if ($is_published == TRUE)
			$sql = $sql.' AND	article_publish_date <= CURRENT_TIMESTAMP';
		else
			$sql = $sql.' AND	article_publish_date > CURRENT_TIMESTAMP';
		$query = $this->db->query($sql,array($type_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item = array(
					'id'=>$row->article_id,
					'heading'=>$row->article_content_heading,
					'publish'=>$row->article_publish_date,
					'lastedit'=>$row->article_content_last_author_timestamp,
					'editorid'=>$row->article_editor_approved_user_entity_id,
					'editorname'=>$row->editor_name,
					'authorid'=>$row->article_content_last_author_user_entity_id,
					'authorname'=>$row->author_name
					);
				$result[] = $result_item;
			}
		}

		return $result;
	}
	
	function GetRequestedArticles($type_id)
	{
		$sql = 'SELECT	article_id,
				article_request_title,
				article_request_description,
				article_request_entity_id,
				article_publish_date,
				article_request_entity_id,
				article_editor_approved_user_entity_id,
				suggestion_user.business_card_name as suggestion_name
				editor_user.business_card_name as editor_name
			FROM	articles

			JOIN	business_cards as editor_user
			ON	editor_user.business_card_user_entity_id = article_editor_approved_user_entity_id
			JOIN	business_cards as suggestion_user
			ON	suggestion_user.business_card_user_entity_id = article_request_entity_id
			
			WHERE	article_suggestion_accepted = 1
			AND	article_content_type_id = ?
			AND	article_live_content_id IS NULL
			AND	article_deleted = 0
			AND	article_pulled = 0';
		$query = $this->db->query($sql,array($type_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item = array(
					'id'=>$row->article_id,
					'title'=>$row->article_request_title,
					'description'=>$row->article_request_description,
					'deadline'=>$row->article_publish_date,
					'suggestionuserid'=>$row->article_request_entity_id,
					'suggestionusername'=>$row->suggestion_user,
					'editorid'=>$row->article_editor_approved_user_entity_id,
					'editorname'=>$row->editor_user
					);
				$result[] = $result_item;
			}
		}

		return $result;
	}
	
	function GetSuggestedArticles($type_id)
	{
		$sql = 'SELECT	article_id,
				article_request_title,
				article_request_description,
				article_created,
				article_request_entity_id,
				business_card_name
			FROM	articles
			JOIN	business_cards
			ON	business_card_user_entity_id = article_request_entity_id
			WHERE	article_suggestion_accepted = 0
			AND	article_content_type_id = ?
			AND	article_deleted = 0
			AND	article_pulled = 0';
		$query = $this->db->query($sql,array($type_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item = array(
					'id'=>$row->article_id,
					'title'=>$row->article_request_title,
					'description'=>$row->article_request_description,
					'userid'=>$row->article_request_entity_id,
					'username'=>$row->business_card_name,
					'created'=>$row->article_created
					);
				$result[] = $result_item;
			}
		}

		return $result;
	} 
	
	function GetArticleRevisions($article_id)
	{
		$sql = 'SELECT	article_content_id,
				article_content_heading,
				article_content_last_author_user_entity_id,
				article_content_last_author_timestamp,
				business_card_name
			FROM	article_contents
			JOIN	business_cards
			ON      business_card_user_entity_id = article_content_last_author_user_entity_id
			WHERE	article_content_article_id = ?
			ORDER BY	article_content_last_author_timestamp DESC';
		$query = $this->db->query($sql,array($article_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item = array(
					'id'=>$row->article_content_id,
					'title'=>$row->article_content_heading,
					'updated'=>$row->article_content_last_author_timestamp,
					'userid'=>$row->article_content_last_author_user_entity_id,
					'username'=>$row->business_card_name
					);
				$result[] = $result_item;
			}
		}

		return $result;
	}

	function AddUserToRequest($article_id,$user_id)
	{
		$sql = 'INSERT	INTO article_writers(
				article_writer_user_entity_id
				article_writer_article_id)
			VALUES	(?,?)';
		$query = $this->db->query($sql,array($user_id,$article_id));

	}	
	
	function RemoveUserFromRequest($article_id,$user_id)
	{
		$sql = 'DELETE FROM article_writers WHERE (artice_writer_article_id = ?
			AND article_writer_user_entiy_id = ?)
	}
	
	function RequestPhoto($article_id,$user_id)
	{
	
	}
	
	function AcceptPhoto($article_id,$photo_id)
	{
	
	}
	
	function AcceptRequest($id, $user)
	{
	
	}
	
	function DeclineRequest($id,$user)
	{
	
	} 
}
?>
