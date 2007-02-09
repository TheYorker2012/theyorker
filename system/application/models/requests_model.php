/**
 *Template for request_model, not complete
 *@author Alex Fargus (agf501)
 **/

<?php
class	Requests_Model extends Model
{
	function Requests_Model()
	{
		parent::Model();
	}
	
	function CreateRequest($status,$type,$title,$description,$user)
	//Add a  new request to the article table
	{
		if ($status == 'suggestion')
		{
			$this->db->trans_start(TRUE);
			$sql = 'INSERT INTO articles(
					article_content_type_id,
					article_created,
					article_request_title,
					article_request_description,
					article_suggestion_accepted,
					article_request_entity_id)
				VALUES (?,CURRENT_TIMESTAMP,?,?,0,?)';
			$query = $this->db->query($sql,array($type,$title,$description,$user));
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
			$this->db->trans_start(TRUE);
			$sql = 'INSERT INTO articles(
					article_content_type_id,
					article_created,
					article_request_title,
					article_request_description,
					article_suggestion_accepted,
					article_request_entity_id
					article_editor_approved_user_etity_id)
				VALUES (?,CURRENT_TIMESTAMP,?,?,1,?,?)';
			$query = $this->db->query($sql,array($type,$title,$description,$user,$user));
			$sql = 'SELECT 	article_id 
				FROM	articles
				WHERE	(article_id=LAST_INSERT_ID())';
			$query = $this->db->query($sql);
			$id = $query->row()->article_id;
			$this->db->trans_complete();
			
			return $id;
		}
		else
		{
			return FALSE;
		}
	}
	
	function UpdateRequest($id,$data)
	//Make a change to a request status in the article table
	{
		return $status;
	}	
	
	function GetPublishedArticles($type)
	{
		$ids = array();
		return $ids;
	}

	function GetUnpublishedArticles($type)
	{
		$ids = array();
		return $ids;
	}	
	
	function GetRequestedArticles($type)
	{
		$ids = array();
		return $ids;
	}
	
	function GetSuggestedArticles($type)
	{
		$ids = array();
		return $ids;
	} 

	function AddUserToRequest($id,$user)
	{
	
	}	
	
	function RemoveUserFromRequest($id,$user)
	{
	
	}
	
	function RequestPhoto($id,$user)
	{
	
	}
	
	function AcceptPhoto($id,$photo)
	{
	
	}
}
?>