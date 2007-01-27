<?php

// Review Model
/*  
**  Author: Dave Huscroft
**  dgh500
**  Does: Gets the data for review page, and leagues
**  Todo: Make the 'return' values properly formatted (not just result arrays)
**        Do the /table/x/y function. NOTE: where does the 'star' 'price' and 'user' ratings come from - price is a tag group? /confused!
**		  Get some sleep
*/  
 
class Review_model extends Model {

	function Review_Model()
	{
		parent::Model();
	}

	//Gets comments from database, frb501
	function GetComments($page_no)
	{
		$sql = "SELECT comment_text, comment_timestamp, comment_rating FROM comments WHERE comment_content_type_id = ?";
		$query = $this->db->query($sql,$page_no);

		if ($query->num_rows() > 0)
		{
			$commentno = 0;
			foreach ($query->result() as $row)
			{
			$comments['comment_author'][$commentno] = 'nothing';
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
	
	function GetReview($organisation_directory_entry_name,$content_type_codename) {
	
	#dgh500
	# need organisation type?
	# need organisation fileas - what IS this?? all null in DB
	$sql = '
			SELECT 
			organisations.organisation_name,
			organisations.organisation_fileas,
			organisations.organisation_description,
			organisations.organisation_location,
			organisations.organisation_postal_address,
			organisations.organisation_postcode,
			organisations.organisation_phone_external,
			organisations.organisation_phone_internal,
			organisations.organisation_fax_number,
			organisations.organisation_email_address,
			organisations.organisation_url,
			organisations.organisation_opening_hours,
			organisations.organisation_events,
			organisations.organisation_hits,
			organisations.organisation_timestamp,
			organisations.organisation_yorkipedia_entry,
			review_context_contents.review_context_content_blurb,
			review_context_contents.review_context_content_recommend_item_price,
			review_context_contents.review_context_content_recommend_item,
			review_context_contents.review_context_content_average_price_upper,
			review_context_contents.review_context_content_average_price_lower,
			review_context_contents.review_context_content_rating,
			review_context_contents.review_context_content_directions,
			review_context_contents.review_context_content_book_online
			  FROM content_types 
			  INNER JOIN review_context_contents
			  ON content_types.content_type_id = review_context_contents.review_context_content_content_type_id 
			  INNER JOIN organisations
			  ON review_context_contents.review_context_content_organisation_entity_id = organisations.organisation_entity_id
			  WHERE content_types.content_type_codename = "'.$content_type_codename.'" 
			  AND organisations.organisation_directory_entry_name = "'.$organisation_directory_entry_name.'"
			';
	
	$result = $query = $this->db->query($sql);
	$reviews = $query->result_array();
	
	return $reviews;
	
	}
	
	function GetLeague($league_codename) {
		# organisation image?
		$sql = '
				SELECT 
				organisations.organisation_name,
				organisations.organisation_url,
				league_entries.league_entry_position,
				leagues.league_name
				FROM organisations
				INNER JOIN league_entries
				ON league_entries.league_entry_organisation_entity_id = organisations.organisation_entity_id
				INNER JOIN leagues
				ON leagues.league_id = league_entries.league_entry_league_id
				WHERE leagues.league_codename = "'.$league_codename.'"
				';
	$query = $this->db->query($sql);
	$tmpleague = array();
	$league    = array();
	
	// Assign nice names to the result
	foreach($query->result() as $row) {
		$tmpleague['organisation_name']        = $row->organisation_name;
		$tmpleague['organisation_url']         = $row->organisation_url;
		$tmpleague['league_entry_position']    = $row->league_entry_position;
		$tmpleague['league_name']              = $row->league_name;
		$league[]                              = $tmpleague;
	}
	
	return $league;
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

