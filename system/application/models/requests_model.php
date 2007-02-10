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
	
	function GetPublishedArticles($type_id)
	{
		$sql = 'SELECT	article_id,
				article_publish_date,
				article_live_content_id,
				article_content_last_author_timestamp,
				article_content_heading,
                                article_content_last_author_user_entity_id,
				article_editor_approved_user_entity_id,
				editor_user.user_firstname as editor_user_firstname,
				editor_user.user_surname as editor_user_surname,
				author_user.user_firstname as author_user_firstname,
				author_user.user_surname as author_user_surname
			FROM	articles
			JOIN	article_contents
			ON      article_content_id = article_live_content_id
			JOIN	users as editor_user
			ON	editor_user.user_entity_id = article_editor_approved_user_entity_id
			JOIN	users as author_user
			ON	author_user.user_entity_id = article_content_last_author_user_entity_id
			WHERE	article_suggestion_accepted = 1
			AND	article_content_type_id = ?
			AND	article_live_content_id IS NOT NULL
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
					'heading'=>$row->article_content_heading,
					'publish'=>$row->article_publish_date,
					'lastedit'=>$row->article_content_last_author_timestamp,
					'editorid'=>$row->article_editor_approved_user_entity_id,
					'editorname'=>$row->editor_user_firstname.' '.$row->editor_user_surname,
					'authorid'=>$row->article_content_last_author_user_entity_id,
					'authorname'=>$row->author_user_firstname.' '.$row->author_user_surname
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
				request_user.user_firstname as request_user_firstname,
				request_user.user_surname as request_user_surname,
				editor_user.user_firstname as editor_user_firstname,
				editor_user.user_surname as editor_user_surname
			FROM	articles
			JOIN	users as request_user
			ON	request_user.user_entity_id = article_request_entity_id
			JOIN	users as editor_user
			ON	editor_user.user_entity_id = article_editor_approved_user_entity_id
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
					'requestuserid'=>$row->article_request_entity_id,
					'requestusername'=>$row->request_user_firstname.' '.$row->request_user_surname,
					'editorid'=>$row->article_editor_approved_user_entity_id,
					'editorname'=>$row->editor_user_firstname.' '.$row->editor_user_surname
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
				user_firstname,
				user_surname
			FROM	articles
			JOIN	users
			ON	user_entity_id = article_request_entity_id
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
					'username'=>$row->user_firstname.' '.$row->user_surname,
					'created'=>$row->article_created
					);
				$result[] = $result_item;
			}
		}

		return $result;
	} 

	function AddUserToRequest($article_id,$user_id)
	{
	
	}	
	
	function RemoveUserFromRequest($article_id,$user_id)
	{
	
	}
	
	function RequestPhoto($article_id,$user_id)
	{
	
	}
	
	function AcceptPhoto($article_id,$photo_id)
	{
	
	}
}
?>