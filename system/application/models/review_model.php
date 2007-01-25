<?php

// Review Model

class Review_model extends Model {

	function Review_Model()
	{
		parent::Model();
	}

	//Gets comments from database, frb501
	function GetComments($page_no)
	{
		$sql = "SELECT comment_author_name, comment_text, comment_timestamp, comment_rating FROM comments WHERE comment_page_id = ?";
		$query = $this->db->query($sql,$page_no);
		
		if ($query->num_rows() > 0)
		{
			$commentno = 0;
			foreach ($query->result() as $row)
			{
			$comments['comment_author'][$commentno] = $row->comment_author_name;
			$comments['comment_rating'][$commentno] = $row->comment_rating;
			$comments['comment_date'][$commentno] = $row->comment_timestamp;
			$comments['comment_content'][$commentno] = $row->comment_text;
			$commentno++;
			}

			return $comments;
		}	
		else
		{
		$nocomments = 'empty';
		return $nocomments;
		}
	}


// ** frb501 - Whole Section Below is outdated, Messy and needs to be redone to the new database *****
//   ** Just delete it probaility unless you see something you can use

//Section - Main Page

	//Gets data which does not change often
//	function getStatic($thingname)
//	{
//		$sql = "SELECT page_property_text FROM page_properties WHERE page_property_label=?";
//		$query = $this->db->query($sql,$thingname);
//		return $query->result_array();
//	}

	//Retreives a random set of 5 try ideas from the given type string
	//This should be it... I hope....
//	function getTryArray($type)
//	{
//		$sql = "SELECT /organisations.organisation_name,review_type_organisations.rto_organisation_entity_id,review_type_organisations.rto_review_type_id  FROM organisations,review_type_organisations,review_types WHERE (organisations.  	organisation_organisation_entity_id_parent = review_type_organisations.rto_organisation_entity_id AND review_type_organisations.rto_review_type_id = review_types.review_type_id AND review_types.review_type_name=?) LIMIT 0, 5";

//		$query = $this->db->query($sql,array($type));
	//	return $query->result_array();

//	}

// Review Types Page

	//Returns the blub for a query
//	function getReviewBlurb($reviewname)
//	{
//		$sql = "SELECT review_type_blurb FROM review_types WHERE review_type_name=?";
//		$query = $this->db->query($sql,$reviewname);
//		return $query->result_array();
//	}

//	function getReviewData($reviewid)
//	{
//		$sql = "SELECT * FROM rto_content WHERE rto_content_id = ?";
//		$query = $this->db->query($sql,$reviewid);
//		$reviewdata = $query->row();

//		//Get data which can be directly extracted
//		$returndata['price'] = $reviewdata->rto_content_price;
//		$returndata['article_content'] = $reviewdata->rto_content_blurb;
//		$returndata['yorker_recommendation'] = $reviewdata->rto_content_recommend;
//		$returndata['website_booking'] = $reviewdata->rto_content_book_online;
		
		//Get data about the organisation
//		$sql = "SELECT organisations.organisation_name,organisations_address,organisations.organisation_postcode,organisations.organisation_url,organisations.organisation_opening_hours FROM organisations, rto_content WHERE organisation_entity_id = ? AND (organisations.organisation_entity_id = rto_content.rto_content_organisation_entity_id)";
//		$query = $this->db->query($sql,$reviewdata->rto_content_organisation_entity_id);
//
//	}

}

