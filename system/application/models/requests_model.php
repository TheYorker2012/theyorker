<?php
class	Requests_Model extends Model
{
	function Requests_Model()
	{
		parent::Model();
	}
	
	function CreateRequest($status)
	//Add a  new request to the article table
	{
		return $id
	}
	
	function UpdateRequest($id,$data)
	//Make a change to a request status in the article table
	{
		return $status
	}	
	
	function GetPublishedArticles($type)
	{
		$ids = array();
		return $ids
	}

	function GetUnpublishedArticles($type)
	{
		$ids = array();
		return $ids
	}	
	
	function GetRequestedArticles($type)
	{
		$ids = array();
		return $ids
	}
	
	function GetSuggestedArticles($type)
	{
		$ids = array();
		return $ids
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