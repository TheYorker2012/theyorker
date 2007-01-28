<?php

// Review Model
/*
**  Author: Dave Huscroft
**  dgh500
**  Does: Gets the data for review page, and leagues
**  Todo: Make the 'return' values properly formatted (not just result arrays)
**        Do the /table/x/y function. NOTE: where does the 'star' 'price' and 'user' ratings come from - price is a tag group? /confused!
**		  : Should probably be something like GetTagGroup($tag_group_id,$order_by (enum 'star','price','user'?],$order_direction)
**		  : If tag_group_ordered is 1 for a tag group then order by tag_order, otherwise by tag_name
**		  : 'star' and 'price' are names of tags, ordered by tag_order and tag_name respectively
**		  : user rating comes from a join like this (left join in case the the organisation is unrated):
**				LEFT JOIN comment_summary_cache
**				ON comment_summary_cache.comment_summary_cache_content_type_id = tag_groups.tag_group_content_type_id
**				   AND comment_summary_cache.comment_summary_cache_organisation_entity_id = organisations.organisation_entity_id
**				   AND comment_summary_cache.comment_summary_cache_article_id IS NULL
**		  : I've added this in to your GetLeague function :-)
**		  Get some sleep
*/

class Review_model extends Model {

	function Review_Model()
	{
		parent::Model();
	}

// Notice Commit Clash - Not sure how to handle it properly.... we seem to have done the same work but I'm indexing from comments.comment_page_id and you are indexing from the organisations table. My system is currently in a working state however your's is probility the better way to do it... so I commented out yours temporatly, sorry frb501
/*	//Gets comments from database, frb501
	function GetComments($organisation_directory_entry_name, $content_type_codename)
	{
		$sql = "SELECT comment_text, comment_timestamp, comment_rating
				FROM comments
				LEFT JOIN (
				content_types, organisations
				) ON ( comments.comment_content_type_id = content_types.content_type_id
				AND comments.comment_organisation_entity_id = organisations.organisation_entity_id )
				WHERE content_types.content_type_codename = ? AND organisations.organisation_directory_entry_name = ?";
		$query = $this->db->query($sql,array($content_type_codename, $organisation_directory_entry_name));

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
*/
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

	function GetLeague($league_codename,$order='ASC',$sortby='league_entries.league_entry_position') {
		# make sortby **MUCH** more user friendly!
		# organisation image?
		$sql = '
				SELECT
				organisations.organisation_name,
				organisations.organisation_url,
				organisations.organisation_description,
				league_entries.league_entry_position,
				leagues.league_name,
				content_types.content_type_name,
				content_types.content_type_codename,
				comment_summary_cache.comment_summary_cache_average_rating
				FROM content_types
				INNER JOIN review_context_contents
				ON review_context_contents.review_context_content_content_type_id = content_types.content_type_id
				INNER JOIN organisations
				ON organisations.organisation_entity_id = review_context_contents.review_context_content_organisation_entity_id
				INNER JOIN league_entries
				ON league_entries.league_entry_organisation_entity_id = organisations.organisation_entity_id
				INNER JOIN leagues
				ON leagues.league_id = league_entries.league_entry_league_id AND content_types.content_type_id = leagues.league_content_type_id
				LEFT JOIN comment_summary_cache
				ON comment_summary_cache.comment_summary_cache_content_type_id = content_types.content_type_id
				   AND comment_summary_cache.comment_summary_cache_organisation_entity_id = organisations.organisation_entity_id
				   AND comment_summary_cache.comment_summary_cache_article_id IS NULL
				WHERE leagues.league_codename = "'.$league_codename.'"
				ORDER BY '.$sortby.' '.$order.'
				';
	$query = $this->db->query($sql);
	$tmpleague = array();
	$league    = array();

	// Assign nice names to the result
	foreach($query->result() as $row) {
		$tmpleague['organisation_name']        = $row->organisation_name;
		$tmpleague['organisation_url']         = $row->organisation_url;
		$tmpleague['organisation_description']         = $row->organisation_description;
		$tmpleague['league_entry_position']    = $row->league_entry_position;
		$tmpleague['league_name']              = $row->league_name;
		$tmpleague['content_type_name']		   = $row->content_type_name;
		$tmpleague['content_type_codename']	   = $row->content_type_codename;
		$tmpleague['average_user_rating']	   = $row->comment_summary_cache_average_rating;
		$league[]                              = $tmpleague;
	}

	return $league;
	}

	//Gets comments from database, frb501
	function GetComments($page_no, $subject)
	{
		$sql = "SELECT comment_text, comment_timestamp, comment_rating FROM comments WHERE comment_content_type_id = ? AND comment_article_id = ?";
		$query = $this->db->query($sql,array($page_no,$subject));

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

	//Adds a comment to the database, frb501
	function SetComment($post_data)
	{
		$comment['comment_content_type_id'] = $post_data['comment_page_id'];
		$comment['comment_article_id'] = $post_data['comment_article_id'];
		$comment['comment_user_entity_id'] = $post_data['comment_user_entity_id'];
		$comment['comment_text'] = $post_data['comment_text'];
		$this->db->insert('comments',$comment); //Add users comment to database
	}

	//Mirrored from GetReview - frb501 - This should return all the rows in a given type
	function TableReview($content_type_codename) {
	
	#dgh500
	# need organisation type?
	# need organisation fileas - what IS this?? all null in DB
	$sql = '
			SELECT 
			organisations.organisation_name,
			organisations.organisation_url,
			organisations.organisation_opening_hours,
			organisations.organisation_directory_entry_name,
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
			  WHERE content_types.content_type_codename = "'.$content_type_codename.'"';
	
	$result = $query = $this->db->query($sql);
	
	$reviews = $query->result_array();
	$reviews['item_count'] = $query->num_rows();
	
	return $reviews;
	
	}

}

