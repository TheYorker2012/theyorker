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
	
	function GetPublishedArticles($type_id)
	{
		$ids = array();
		return $ids;
	}

	function GetUnpublishedArticles($type_id)
	{
		$ids = array();
		return $ids;
	}	
	
	function GetRequestedArticles($type_id)
	{
		$ids = array();
		return $ids;
	}
	
	function GetSuggestedArticles($type_id)
	{
		$sql = 'SELECT	article_id,
				article_request_title,
				article_request_description,
				article_request_entity_id,
				article_created
			FROM	articles
			WHERE	article_suggestion_accepted = 0
			AND	article_content_type_id = ?';
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
					'title'=>$row->article_request_entity_id,
					'title'=>$row->article_created
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