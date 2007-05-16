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
**		  : I've added this in to your GetLeague function :-)
**		  Get some sleep
*/

class Review_model extends Model {

	function Review_Model()
	{
		parent::Model();
	}

	///	Return whether the review content exists
	/**
	 * @return A single review context for an organisation
	 */
	function GetReviewContextExists($organisation_shortname,$content_type_codename)
	{
		$sql =
			'
			SELECT
			   	review_contexts.review_context_live_content_id
			FROM review_contexts 
			INNER JOIN organisations 
			ON organisations.organisation_entity_id = review_contexts.review_context_organisation_entity_id 
			 AND organisations.organisation_directory_entry_name = ?
			INNER JOIN content_types
			ON review_contexts.review_context_content_type_id=content_types.content_type_id
			 AND content_types.content_type_codename = ?
			WHERE 1
			ORDER BY review_context_contents.review_context_content_last_author_timestamp DESC
			';

		$query = $this->db->query($sql, array($organisation_shortname,$content_type_codename) );

		return ($query->num_rows() != 0);
	}

	/// A single review context for an organisation.
	/**
	 * @return bool Whether successful
	 */
	function CreateReviewContext($organisation_shortname,$content_type_codename)
	{
		$sql = 'SELECT
			(SELECT organisation_entity_id FROM organisations WHERE organisations.organisation_directory_entry_name = ?)  as review_context_organisation_entity_id,
			(SELECT content_type_id FROM content_types WHERE content_types.content_type_codename = ?) as review_context_content_type_id';
		$query = $this->db->query($sql, array($organisation_shortname,$content_type_codename))->result_array();
		if (!array_key_exists(0, $query)) {
			return FALSE;
		}
		$org_entity_id = $query[0]['review_context_organisation_entity_id'];
		$content_id = $query[0]['review_context_content_type_id'];
		if (NULL === $org_entity_id || NULL === $content_id) {
			return FALSE;
		}
		// Create the new context.
		$sql = '
			INSERT INTO review_contexts 
			(
			 review_context_organisation_entity_id,
			 review_context_content_type_id
			) 
			VALUES ('.$org_entity_id.','.$content_id.')
			ON DUPLICATE KEY UPDATE review_context_deleted = FALSE
			';
		$this->db->query($sql);
		
		// check something's happened
		if ($this->db->affected_rows()) {
			$this->load->model('comments_model');
			$CI = & get_instance();
			$CI->comments_model->CreateThread(
				array(), 'review_contexts',
				$query[0], 'review_context_comment_thread_id');
			$CI->comments_model->CreateThread(
				array('allow_anonymous_comments' => FALSE), 'review_contexts',
				$query[0], 'review_context_office_comment_thread_id');
			return TRUE;
		} else {
			return FALSE;
		}
		
	}
	
	function DeleteReviewContext($organisation_shortname, $content_type_codename)
	{
		$sql = 'SELECT
			(SELECT organisation_entity_id FROM organisations WHERE organisations.organisation_directory_entry_name = ?)  as review_context_organisation_entity_id,
			(SELECT content_type_id FROM content_types WHERE content_types.content_type_codename = ?) as review_context_content_type_id';
		$query = $this->db->query($sql, array($organisation_shortname,$content_type_codename))->result_array();
		if (!array_key_exists(0, $query)) {
			return FALSE;
		}
		if (NULL === $query[0]['review_context_organisation_entity_id'] ||
			NULL === $query[0]['review_context_content_type_id'])
		{
			return FALSE;
		}
		$this->db->update('review_contexts',
			array('review_context_deleted' => TRUE),
			$query[0]);
		return $this->db->affected_rows() > 0;
	}

	///	Return published review context.
	/**
	 * @return A single review context for an organisation
	 */
	function GetPublishedReviewContextContents($organisation_shortname,$content_type_codename)
	{
		$sql =
			'
			SELECT
			 review_context_contents.review_context_content_blurb as content_blurb,
			 review_context_contents.review_context_content_quote as content_quote,
			 review_context_contents.review_context_content_average_price as average_price,
			 review_context_contents.review_context_content_recommend_item as recommended_item, 
			 review_context_contents.review_context_content_rating as content_rating,
			 review_context_contents.review_context_content_serving_times as serving_times,
			 "deal" as deal,
			 0 as deal_expires
			FROM review_contexts 
			INNER JOIN organisations 
			ON organisations.organisation_entity_id = review_contexts.review_context_organisation_entity_id 
			 AND organisations.organisation_directory_entry_name = ?
			INNER JOIN content_types
			ON review_contexts.review_context_content_type_id=content_types.content_type_id
			 AND content_types.content_type_codename = ?
			INNER JOIN review_context_contents 
			ON review_contexts.review_context_live_content_id=review_context_contents.review_context_content_id 
			WHERE 1
			';

		$query = $this->db->query($sql, array($organisation_shortname,$content_type_codename) );

		return $query->result_array();
	}
	
	
	///	Return revision, or most recent if no specified.
	/**
	 * @return A single review context for an organisation
	 */
	function GetReviewContextContents($organisation_shortname,$content_type_codename,$context_content_id = -1)
	{
		$sql =
			'
			SELECT
				review_context_contents.review_context_content_id as content_id,
				unix_timestamp(review_context_contents.review_context_content_last_author_timestamp) as timestamp,
				concat(users.user_firstname, " ",users.user_surname) as name,
				review_context_contents.review_context_content_last_author_user_entity_id as user_entity_id,
				review_context_contents.review_context_content_id as context_content_id,
				(review_contexts.review_context_live_content_id=review_context_contents.review_context_content_id ) as is_published,
				review_context_contents.review_context_content_blurb as content_blurb,
				review_context_contents.review_context_content_quote as content_quote,
				review_context_contents.review_context_content_average_price as average_price,
				review_context_contents.review_context_content_recommend_item as recommended_item, 
				review_context_contents.review_context_content_rating as content_rating,
				review_context_contents.review_context_content_serving_times as serving_times
			FROM review_contexts
			INNER JOIN organisations
				ON organisations.organisation_entity_id = review_contexts.review_context_organisation_entity_id
			INNER JOIN content_types
				ON review_contexts.review_context_content_type_id=content_types.content_type_id
			INNER JOIN review_context_contents
				ON review_contexts.review_context_content_type_id = review_context_contents.review_context_content_content_type_id
				AND review_contexts.review_context_organisation_entity_id = review_context_contents.review_context_content_organisation_entity_id
			INNER JOIN users 
				ON users.user_entity_id=review_context_contents.review_context_content_last_author_user_entity_id
			WHERE	organisations.organisation_directory_entry_name = ?
				AND	content_types.content_type_codename = ?';
		if ($context_content_id !== -1) {
			$sql .= ' AND review_context_contents.review_context_content_id = ?';
		}
		
		$sql .= '
			ORDER BY review_context_contents.review_context_content_last_author_timestamp DESC
			LIMIT 1
			';

		$query = $this->db->query($sql, array($organisation_shortname,$content_type_codename,$context_content_id) );

		return $query->result_array();
	}


	///	Return review context revisions, and their author names.
	/**
	 * @return A single review context for an organisation
	 */
	function GetReviewContextContentRevisions($organisation_shortname,$content_type_codename, $revision_id = -1)
	{
		$sql =
			'SELECT
				review_context_contents.review_context_content_id AS id,
				unix_timestamp(review_context_contents.review_context_content_last_author_timestamp) as timestamp,
				concat(users.user_firstname, " ",users.user_surname) as author,
				review_context_contents.review_context_content_last_author_user_entity_id as user_entity_id,
				review_context_contents.review_context_content_id as content_id,
				(review_contexts.review_context_live_content_id=review_context_contents.review_context_content_id ) as published,
				review_context_contents.review_context_content_deleted AS deleted
			FROM review_contexts 
			INNER JOIN organisations 
			ON organisations.organisation_entity_id = review_contexts.review_context_organisation_entity_id 
			 AND organisations.organisation_directory_entry_name = ?
			INNER JOIN content_types
			ON review_contexts.review_context_content_type_id=content_types.content_type_id
			 AND content_types.content_type_codename = ?
			INNER JOIN review_context_contents
			  	ON  review_context_content_content_type_id = review_context_content_type_id
			  	AND  review_context_content_organisation_entity_id = review_context_organisation_entity_id';
		if ($revision_id !== -1) {
			$sql .= ' AND review_context_contents.review_context_content_id = '.$this->db->escape($revision_id);
		}
		$sql .= '
			 AND review_contexts.review_context_organisation_entity_id = review_context_contents.review_context_content_organisation_entity_id
			INNER JOIN users 
			ON users.user_entity_id=review_context_contents.review_context_content_last_author_user_entity_id
			ORDER BY review_context_contents.review_context_content_last_author_timestamp ASC
			';

		$query = $this->db->query($sql, array($organisation_shortname,$content_type_codename) );

		return $query->result_array();
	}

	/// Adds a review content to the db
	/**
	 * @return Number of affected rows.
	 */
	function SetReviewContextContent(
		$organisation_shortname, $content_type_codename, $user_entity_id, $blurb,
		$quote, $average_price, $recommended_item, $rating, $serving_times)
	{
		$sql =
			'
			INSERT INTO review_context_contents 
			(
				review_context_content_organisation_entity_id,
				review_context_content_content_type_id,
				review_context_content_last_author_user_entity_id,
				review_context_content_blurb,
				review_context_content_quote,
				review_context_content_average_price,
				review_context_content_recommend_item, 
				review_context_content_rating,
				review_context_content_serving_times
			) 
			SELECT
				review_contexts.review_context_organisation_entity_id as organisation_entity_id,
				review_contexts.review_context_content_type_id as content_type_id,
				? as user_entity_id,
				? as blurb,
				? as quote,
				? as average_price,
				? as recommended_item,
				? as rating,
				? as serving_times
			FROM review_contexts
			INNER JOIN organisations
				ON	organisations.organisation_entity_id = review_contexts.review_context_organisation_entity_id
				AND	organisations.organisation_directory_entry_name = ?
			INNER JOIN content_types
				ON	review_contexts.review_context_content_type_id=content_types.content_type_id
				AND	content_types.content_type_codename = ?
			LIMIT 1';

		$query = $this->db->query($sql, array(
			$user_entity_id, $blurb, $quote, $average_price, $recommended_item,
			$rating, $serving_times, $organisation_shortname, $content_type_codename,
			$organisation_shortname, $content_type_codename) );
		return $this->db->affected_rows();
	}
	
	/// Publish a revision of the review context content
	/**
	 * @return Number of affected rows.
	 */
	function PublishContextContentRevision($organisation_directory_entry_name,$content_type_codename, $context_revision_id)
	{
		$sql =
			'UPDATE review_contexts
			INNER JOIN review_context_contents
				ON	review_context_content_id = '.$this->db->escape($context_revision_id).'
			SET
				review_contexts.review_context_live_content_id = '.$this->db->escape($context_revision_id).'
			WHERE	review_context_content_organisation_entity_id = review_context_organisation_entity_id
				AND	review_context_content_content_type_id = review_context_content_type_id
				AND	review_contexts.review_context_organisation_entity_id
						= (	SELECT organisation_entity_id
							FROM organisations
							WHERE organisations.organisation_directory_entry_name
								= '.$this->db->escape($organisation_directory_entry_name).')
				AND	review_contexts.review_context_content_type_id
						= (	SELECT content_type_id
							FROM content_types
							WHERE content_types.content_type_codename
								= '.$this->db->escape($content_type_codename).')';
		$this->db->query($sql);
		return $this->db->affected_rows();
	}


	function GetReview($organisation_directory_entry_name,$content_type_codename, $context_revision_id = -1) {

		#dgh500
		# need organisation type?
		# need organisation fileas - what IS this?? all null in DB
		$sql = '
			SELECT
			organisations.organisation_entity_id,
			organisations.organisation_name,
			organisations.organisation_fileas,
			organisations.organisation_location_id as organisation_location_id,

			organisation_contents.organisation_content_description as organisation_description,
			organisation_contents.organisation_content_postal_address as organisation_postal_address,
			organisation_contents.organisation_content_postcode as organisation_postcode,
			organisation_contents.organisation_content_phone_external as organisation_phone_external,
			organisation_contents.organisation_content_phone_internal as organisation_phone_internal,
			organisation_contents.organisation_content_fax_number as organisation_fax_number,
			organisation_contents.organisation_content_email_address as organisation_email_address,
			organisation_contents.organisation_content_url as organisation_url,
			organisation_contents.organisation_content_opening_hours as organisation_opening_hours,
			
			organisations.organisation_events,
			organisations.organisation_hits,
			organisations.organisation_timestamp,
			organisations.organisation_yorkipedia_entry,
			review_context_contents.review_context_content_blurb,
			review_context_contents.review_context_content_average_price,
			review_context_contents.review_context_content_recommend_item,
			review_context_contents.review_context_content_rating,
			review_context_contents.review_context_content_serving_times,
			review_context_contents.review_context_content_content_type_id
			  FROM review_contexts
			  INNER JOIN content_types
			  	ON content_types.content_type_id = review_contexts.review_context_content_type_id
			  INNER JOIN review_context_contents
			  	ON  review_context_content_content_type_id = review_context_content_type_id
			  	AND  review_context_content_organisation_entity_id = review_context_organisation_entity_id';
		if ($context_revision_id !== -1) {
			$sql .= ' AND review_context_contents.review_context_content_id = '.$this->db->escape($context_revision_id);
		} else {
			$sql .= ' AND review_context_contents.review_context_content_id = review_contexts.review_context_live_content_id';
		}
		$sql .= '
			  INNER JOIN organisations
			  ON review_contexts.review_context_organisation_entity_id = organisations.organisation_entity_id
			  INNER JOIN organisation_contents 
			  ON organisations.organisation_live_content_id = organisation_contents.organisation_content_id 
			  WHERE content_types.content_type_codename = '.$this->db->escape($content_type_codename).'
			  AND organisations.organisation_directory_entry_name = '.$this->db->escape($organisation_directory_entry_name);

		$result = $query = $this->db->query($sql);
		$reviews = $query->result_array();
	
		return $reviews;
	}

	function GetLeague($league_codename,$order='ASC',$sortby='league_entries.league_entry_position') {
		# make sortby **MUCH** more user friendly!
		# organisation image?
		$sql = '
				SELECT
					organisations.organisation_entity_id,
					organisations.organisation_name,
					organisation_contents.organisation_content_url,
					review_context_contents.review_context_content_blurb as organisation_description,
					review_context_contents.review_context_content_rating,
					review_context_contents.review_context_content_quote,
					organisations.organisation_directory_entry_name,
					league_entries.league_entry_position,
					leagues.league_name,
					leagues.league_content_type_id,
					leagues.league_image_id,
					content_types.content_type_name,
					content_types.content_type_codename,
					IF (comment_threads.comment_thread_num_ratings > 0,
						comment_threads.comment_thread_total_rating / comment_threads.comment_thread_num_ratings,
						NULL) AS average_user_rating
				FROM content_types
				INNER JOIN review_context_contents
					ON review_context_contents.review_context_content_content_type_id = content_types.content_type_id
				INNER JOIN organisations
					ON organisations.organisation_entity_id = review_context_contents.review_context_content_organisation_entity_id
			    INNER JOIN organisation_contents 
			    	ON organisations.organisation_live_content_id = organisation_contents.organisation_content_id 
				INNER JOIN league_entries
					ON league_entries.league_entry_organisation_entity_id = organisations.organisation_entity_id
				INNER JOIN leagues
					ON leagues.league_id = league_entries.league_entry_league_id
					AND content_types.content_type_id = leagues.league_content_type_id
				INNER JOIN review_contexts
					ON review_contexts.review_context_organisation_entity_id = organisations.organisation_entity_id
					AND review_contexts.review_context_content_type_id = content_types.content_type_id
				LEFT JOIN comment_threads
					ON comment_threads.comment_thread_id = review_contexts.review_context_comment_thread_id
				WHERE leagues.league_codename = ?
				ORDER BY ? ?
				';
		$query = $this->db->query($sql,array($league_codename,$sortby,$order));
		$tmpleague = array();
		$league    = array();
	
		// Assign nice names to the result
		foreach($query->result() as $row) {
			$tmpleague['organisation_id']          = $row->organisation_entity_id;
			$tmpleague['organisation_name']        = $row->organisation_name;
			$tmpleague['organisation_url']         = $row->organisation_content_url;
			$tmpleague['organisation_description'] = $row->organisation_description;
			$tmpleague['league_entry_position']    = $row->league_entry_position;
			$tmpleague['league_name']              = $row->league_name;
			$tmpleague['league_content_type_id']      = $row->league_content_type_id;
			$tmpleague['content_type_name']		   = $row->content_type_name;
			$tmpleague['content_type_codename']	   = $row->content_type_codename;
			$tmpleague['review_rating'] = $row->review_context_content_rating;
			$tmpleague['review_quote'] = $row->review_context_content_quote;
			$tmpleague['average_user_rating'] = 0;
			if ($row->average_user_rating != '')
				$tmpleague['average_user_rating']	   = $row->average_user_rating;
			$tmpleague['organisation_directory_entry_name'] = $row->organisation_directory_entry_name;
			$league[]                              = $tmpleague;
		}
	
		return $league;
	}

	//Get league type
	function GetLeagueType($league_code_name)
	{
		$sql = 'SELECT content_types.content_type_codename FROM leagues
				INNER JOIN content_types ON content_types.content_type_id = leagues.league_content_type_id
				WHERE leagues.league_codename = ?';
		$query = $this->db->query($sql, $league_code_name);
		$query = $query->result_array();
		$type = $query[0]['content_type_codename'];
		return $type;
	}

	//Gets the league details for the front pages /reviews/food, /reviews/drink etc...
	//Call with type 'food' 'drink' etc...
	//Returns a 2d array 0-> leagues.... 1-> leagues... etc...
	function GetLeagueDetails($type)
	{
		$sql = "SELECT leagues.league_image_id,
				leagues.league_name,
				leagues.league_size,
				leagues.league_codename
				FROM leagues
				INNER JOIN content_types ON
				content_types.content_type_id = leagues.league_content_type_id
				WHERE content_types.content_type_name = ?";
		$query = $this->db->query($sql,$type);
		return $query->result_array();

	}

	//Find the article id's for a review, frb501
	//This is useful since from this we can call the news_model to get the rest
	function GetArticleID($organisation_name,$content_type_id)
	{
		$sql = "SELECT article_id FROM articles
		INNER JOIN organisations ON organisations.organisation_entity_id = articles.article_organisation_entity_id
		WHERE
			articles.article_content_type_id = ? AND
			organisations.organisation_directory_entry_name = ?
		ORDER BY article_id DESC
		";
		$query = $this->db->query($sql,array($content_type_id,$organisation_name));
		if ($query->num_rows() != 0) //If article exists
		{
			$resultno = 0;
			foreach ($query->result() as $row) //Create a array of article_id's used by it
			{
				$article_id[$resultno] = $row->article_id;
				$resultno++;
			}
			return $article_id; //And return it
		}
		else //If no article
		{
			return array(); //Return a empty array
		}
	}

	//Changes between name of type and the id of the type, frb501
	function TranslateTypeNameToID($type_name)
	{
		$sql = "SELECT content_type_id FROM content_types WHERE
				content_type_codename = ?";
		$query = $this->db->query($sql,$type_name);
		$result = $query->row_array();
		return $result['content_type_id'];
	}

	//Changes between name of a tag and the id of the tag, frb501
	function TranslateTagNameToID($tag_name)
	{
		$sql = "SELECT tag_id FROM tags WHERE
				tag_name = ?";
		$query = $this->db->query($sql,$tag_name);
		$result = $query->row_array();
		return $result['tag_id'];
	}

	//Changes between a organisation directory name and it's id - This is too reduce problems further on in dev
	function TranslateDirectoryToID($directory_name)
	{
		$sql = "SELECT organisation_entity_id FROM organisations WHERE
				organisation_directory_entry_name = ?";
		$query = $this->db->query($sql,$directory_name);
		$result = $query->row_array();

		return $result['organisation_entity_id'];
	}

	//The invert of GetArticleID, it takes the article id and says which organisation it's about
	function GetDirectoryName($article_id)
	{
		$sql = 'SELECT organisation_directory_entry_name FROM organisations INNER JOIN articles ON articles.article_organisation_entity_id = organisations.organisation_entity_id WHERE articles.article_id = ?';
		$query = $this->db->query($sql,$article_id);

		if ($query->num_rows() != 0) //If article exists
		{
			$row = $query->row_array(); //Only one result
			return $row['organisation_directory_entry_name'];
		}
		else
		{
			return array(); //Else return a empty array
		}

	}

	//Translate a organisation name into a organisation id
	function FindOrganisationID($organisation_name)
	{
		$sql = "SELECT organisation_entity_id FROM organisations WHERE organisation_directory_entry_name = ?";
		$organisation_id = $this->db->query($sql,$organisation_name);
		$organisation_id = $organisation_id->result_array();
		$organisation_id = $organisation_id[0]['organisation_entity_id'];
		return $organisation_id;
	}

	//Translate a content type name into a content id
	function FindContentID($content_name)
	{
		$sql = "SELECT content_type_id FROM content_types WHERE content_type_codename = ?";
		$content_id = $this->db->query($sql,$content_name);
		$content_id = $content_id->result_array();
		$content_id = $content_id[0]['content_type_id'];
		return $content_id;
	}

	//Translate user id to name
	function TranslateUserIDToName($user_id)
	{
		$sql = 'SELECT user_firstname,user_surname FROM users WHERE user_entity_id = ?';
		$query = $this->db->query($sql,$user_id);
		$query = $query->result_array();
		if ($query == array()) return 'Unknown User';
		$user = $query[0]['user_firstname'].' '.$query[0]['user_surname'];
		return $user;
	}

	//For generating table list for front pages, frb501

	//Pre condition: A entry in the content_type table e.g. 'food' or 'drink'
	//Post condition: A array containing all tags with the key value of the tag_group_name
	//e.g. array['taggroupname'] = array('cool','splash')
	//The array also has a special array inside array['tag_group_names']
	// = array('Splashing out', 'Cool Digs')
	//Which contains the taggroupnames for all tags used

	//This function is expensive with queries however for the ordering it seems the best way
	function GetTags($type)
	{
		$sql = 'SELECT DISTINCT tag_groups.tag_group_name
				FROM tag_groups
				INNER JOIN tags ON tag_groups.tag_group_id = tag_groups.tag_group_id
				INNER JOIN organisation_tags ON organisation_tags.organisation_tag_tag_id = tags.tag_id
				INNER JOIN review_contexts ON review_contexts.review_context_organisation_entity_id = organisation_tags.organisation_tag_organisation_entity_id
				INNER JOIN content_types ON content_types.content_type_id = tag_groups.tag_group_content_type_id
				WHERE content_types.content_type_name = ?
				ORDER BY tag_group_order
				';
		$query = $this->db->query($sql,$type);
		$queryarray = $query->result_array();

		foreach ($queryarray as &$row)
		{
			$tag_group_names[] = $row['tag_group_name']; //Extract the names from the array
		}

		if (isset($tag_group_names) == 0) return array(); //No data so return empty array

		$index = 0; //For indexing
	
		foreach ($queryarray as &$row)
		{
			$index++;
			$tag_group_name[$index] = $row['tag_group_name']; //Stores the tag group names

			//First find out if these tags should be ordered by tag value or alphabetly
			$nsql = 'SELECT tag_groups.tag_group_ordered FROM tag_groups WHERE tag_group_name = ?';
			$nquery = $this->db->query($nsql,$tag_group_name[$index]);
			$ordering = $nquery->row_array();

			$ordering = $ordering['tag_group_ordered']; //Ordering says which ordering to use

			//Sub query finds all the tag names in the tag group
			//INNER JOIN with organisation_tags removes unused tags from the front page
			if ($ordering == TRUE)
			{								//Order by field tag_order
			$msql = '
					 SELECT DISTINCT tags.tag_name FROM tags
					 INNER JOIN tag_groups ON tags.tag_tag_group_id = tag_groups.tag_group_id
					 INNER JOIN organisation_tags ON organisation_tags.organisation_tag_tag_id = tags.tag_id
					 WHERE tag_groups.tag_group_name = ? ORDER BY tags.tag_order';
			}
			else
			{								//Order by field tag_name
			$msql = '						
					 SELECT DISTINCT tags.tag_name FROM tags
					 INNER JOIN tag_groups ON tags.tag_tag_group_id = tag_groups.tag_group_id
					 INNER JOIN organisation_tags ON organisation_tags.organisation_tag_tag_id = tags.tag_id
					 WHERE tag_groups.tag_group_name = ? ORDER BY tags.tag_name';
			}

			$mquery = $this->db->query($msql,$tag_group_name[$index]); //Do query
			$marray = $mquery->result_array();

			//Place all of the tags into the return array
			foreach ($marray as &$mrow)
			{
				//Place all tags in to the tag_group array with the key of the tag groups name
				$tag_group[$tag_group_name[$index]][] = $mrow['tag_name'];
			}
		}
		//Add the special case
		$tag_group['tag_group_names'] = $tag_group_names;

		//Return the result
		return $tag_group;
	}

	//For adding tags in the back pages, frb501

	//Pre condition: A entry in the content_type table e.g. 'food' or 'drink'
	//Post condition: A array containing all tags with the key value of the tag_group_name
	//e.g. array['taggroupname'] = array('cool','splash')
	//The array also has a special array inside array['tag_group_names']
	// = array('Splashing out', 'Cool Digs')
	//Which contains the taggroupnames for all tags used

	//This function is expensive with queries however for the ordering it seems the best way
	function GetAllTags($type)
	{
		$sql = 'SELECT DISTINCT tag_groups.tag_group_name
				FROM tag_groups
				INNER JOIN tags ON tag_groups.tag_group_id = tag_groups.tag_group_id
				INNER JOIN content_types ON content_types.content_type_id = tag_groups.tag_group_content_type_id
				WHERE content_types.content_type_name = ?
				ORDER BY tag_group_order
				';
		$query = $this->db->query($sql,$type);
		$queryarray = $query->result_array();

		foreach ($queryarray as &$row)
		{
			$tag_group_names[] = $row['tag_group_name']; //Extract the names from the array
		}

		if (!isset($tag_group_names)) return array(); //No tags in this type

		$index = 0; //For indexing
	
		foreach ($queryarray as &$row)
		{
			$index++;
			$tag_group_name[$index] = $row['tag_group_name']; //Stores the tag group names

			//First find out if these tags should be ordered by tag value or alphabetly
			$nsql = 'SELECT tag_groups.tag_group_ordered FROM tag_groups WHERE tag_group_name = ?';
			$nquery = $this->db->query($nsql,$tag_group_name[$index]);
			$ordering = $nquery->row_array();

			$ordering = $ordering['tag_group_ordered']; //Ordering says which ordering to use

			//Sub query finds all the tag names in the tag group
			//INNER JOIN with organisation_tags removes unused tags from the front page
			if ($ordering == TRUE)
			{								//Order by field tag_order
			$msql = '
					 SELECT DISTINCT tags.tag_name FROM tags
					 INNER JOIN tag_groups ON tags.tag_tag_group_id = tag_groups.tag_group_id
					 WHERE tag_groups.tag_group_name = ? ORDER BY tags.tag_order';
			}
			else
			{								//Order by field tag_name
			$msql = '						
					 SELECT DISTINCT tags.tag_name FROM tags
					 INNER JOIN tag_groups ON tags.tag_tag_group_id = tag_groups.tag_group_id
					 WHERE tag_groups.tag_group_name = ? ORDER BY tags.tag_name';
			}

			$mquery = $this->db->query($msql,$tag_group_name[$index]); //Do query
			$marray = $mquery->result_array();

			//Place all of the tags into the return array
			foreach ($marray as &$mrow)
			{
				//Place all tags in to the tag_group array with the key of the tag groups name
				$tag_group[$tag_group_name[$index]][] = $mrow['tag_name'];
			}
		}
		//Add the special case
		$tag_group['tag_group_names'] = $tag_group_names;

		//Return the result
		return $tag_group;
	}

//For backend pages for viewing / deleting existing tags, frb501

	//Pre condition: A entry in the content_type table e.g. 'food' or 'drink'
	//
	//Post condition: A array containing all tags with the key value of the tag_group_name
	//e.g. array['taggroupname'] = array('cool','splash')
	//The array also has a special array inside array['tag_group_names']
	// = array('Splashing out', 'Cool Digs')
	//Which contains the taggroupnames for all tags used

function GetTagOrganisation($type,$organisation)
{
		$sql = 'SELECT DISTINCT tag_groups.tag_group_name
				FROM tag_groups
				INNER JOIN tags ON tag_groups.tag_group_id = tags.tag_tag_group_id
				INNER JOIN organisation_tags ON organisation_tags.organisation_tag_tag_id = tags.tag_id
				INNER JOIN organisations ON organisations.organisation_entity_id = organisation_tags.organisation_tag_organisation_entity_id
				INNER JOIN content_types ON content_types.content_type_id = tag_groups.tag_group_content_type_id WHERE (content_types.content_type_name = ? && organisations.organisation_directory_entry_name = ?)
				ORDER BY tag_group_order
				';
		$query = $this->db->query($sql,array($type,$organisation));
		$queryarray = $query->result_array();

		foreach ($queryarray as &$row)
		{
			$tag_group_names[] = $row['tag_group_name']; //Extract the names from the array
		}

		$index = 0; //For indexing
	
		foreach ($queryarray as &$row)
		{
			$index++;
			$tag_group_name[$index] = $row['tag_group_name']; //Stores the tag group names

			//First find out if these tags should be ordered by tag value or alphabetly
			$nsql = 'SELECT tag_groups.tag_group_ordered FROM tag_groups WHERE tag_group_name = ?';
			$nquery = $this->db->query($nsql,$tag_group_name[$index]);
			$ordering = $nquery->row_array();

			$ordering = $ordering['tag_group_ordered']; //Ordering says which ordering to use

			//Sub query finds all the tag names in the tag group
			//INNER JOIN with organisation_tags removes unused tags from the front page
			if ($ordering == TRUE)
			{								//Order by field tag_order
			$msql = '
					 SELECT tags.tag_name FROM tags
					 INNER JOIN tag_groups ON tags.tag_tag_group_id = tag_groups.tag_group_id
					 INNER JOIN organisation_tags ON organisation_tags.organisation_tag_tag_id = tags.tag_id
					 INNER JOIN organisations ON organisations.organisation_entity_id = organisation_tags.organisation_tag_organisation_entity_id
					 WHERE (tag_groups.tag_group_name = ? && organisations.organisation_directory_entry_name = ?) ORDER BY tags.tag_order';
			}
			else
			{								//Order by field tag_name
			$msql = '						
					 SELECT tags.tag_name FROM tags
					 INNER JOIN tag_groups ON tags.tag_tag_group_id = tag_groups.tag_group_id
					 INNER JOIN organisation_tags ON organisation_tags.organisation_tag_tag_id = tags.tag_id
					 INNER JOIN organisations ON organisations.organisation_entity_id = organisation_tags.organisation_tag_organisation_entity_id
					 WHERE (tag_groups.tag_group_name = ? && organisations.organisation_directory_entry_name = ?) ORDER BY tags.tag_name';
			}

			$mquery = $this->db->query($msql,array($tag_group_name[$index],$organisation)); //Do query
			$marray = $mquery->result_array();

			//Place all of the tags into the return array
			foreach ($marray as &$mrow)
			{
				//Place all tags in to the tag_group array with the key of the tag groups name
				$tag_group[$tag_group_name[$index]][] = $mrow['tag_name'];
			}
		}

		//Incase no tags
		if (isset($tag_group_names))
		{
			//Add the special case
			$tag_group['tag_group_names'] = $tag_group_names;

			//Return the result
			return $tag_group;
		}
		else
		{
			return array(); //Return a empty array
		}
	}

	//This function returns a array containing all tags which a organisation does not yet have
	function GetTagWithoutOrganisation($type,$organisation)
	{
		//Get all the tags which it does have
		$currenttags = $this->GetTagOrganisation($type,$organisation);
		
		//Get all possible tags
		$possibletags = $this->GetAllTags($type);

		if ($possibletags == array()) return array(); //In case tags are empty

		//Find the different between the 2 array'ies
		foreach ($possibletags['tag_group_names'] as $tag_group_name)
		{
			if (isset($currenttags[$tag_group_name])) //If the index of the second array doesn't exist we don't need to do anything
			{
				$possibletags[$tag_group_name] = array_diff($possibletags[$tag_group_name],$currenttags[$tag_group_name]);
			}
		}

		return $possibletags;

	}

	//SetOrganisationTag - takes 2 arguments the organisation id and tag id and adds the row in the link table
	function SetOrganisationTag($organisation_id, $tag_id)
	{
		$insert['organisation_tag_organisation_entity_id'] = $organisation_id;
		$insert['organisation_tag_tag_id'] = $tag_id;
		$this->db->insert('organisation_tags',$insert);
	}

	//SetOrganisationTag - takes 2 arguments the organisation id and tag id and adds the row in the link table
	function RemoveOrganisationTag($organisation_id, $tag_id)
	{
		$sql = 'DELETE FROM organisation_tags WHERE (organisation_tag_organisation_entity_id = ? && organisation_tag_tag_id = ?)';
		$this->db->query($sql,array($organisation_id, $tag_id));
	}

	//Gets a table review for a section which is sorted depending on parameters
	function GetTableReview($content_type_codename,$sorted_by = 'any',$item_filter_by = 'any',$where_equal_to = 'any')
	{

	//Used later on
	$select_tag_group = '';
	
		switch ($sorted_by) //Set sorting query
		{
			case 'name':
				$sort_sql = 'ORDER BY o.organisation_name, ';
			break;

			case 'star':
				$sort_sql = 'ORDER BY rcc.review_context_content_rating DESC, ';
			break;

			case 'user':
				$sort_sql = 'ORDER BY IF (thread.comment_thread_num_ratings > 0,
					thread.comment_thread_total_rating / thread.comment_thread_num_ratings,
					NULL) DESC, ';
			break;

			case 'any':
				$sort_sql = 'ORDER BY o.organisation_name, '; //Lets default to name sorting can be changed later
			break;

			default:
				$sort_sql = 'ORDER BY correct_tag, t.tag_name, ';
				$select_tag_group = ', IF(tg.tag_group_name ='.$this->db->escape($sorted_by).',0,1) AS correct_tag';
			break;
		}

		$sql = '
			SELECT
				o.organisation_entity_id,
				o.organisation_name,
				o.organisation_entity_id,
				oc.organisation_content_url,
				o.organisation_directory_entry_name,
				tg.tag_group_name,t.tag_name,
				rcc.review_context_content_rating,
				rcc.review_context_content_blurb,
				rcc.review_context_content_quote,
				IF (thread.comment_thread_num_ratings > 0,
					thread.comment_thread_total_rating / thread.comment_thread_num_ratings,
					NULL) AS average_user_rating'.
				$select_tag_group.'
			FROM content_types AS ct
			INNER JOIN review_context_contents AS rcc
				ON ct.content_type_id = rcc.review_context_content_content_type_id
			INNER JOIN organisations AS o
				ON rcc.review_context_content_organisation_entity_id = o.organisation_entity_id
			INNER JOIN organisation_contents AS oc
				ON o.organisation_live_content_id = oc.organisation_content_id
			INNER JOIN review_contexts
				ON review_contexts.review_context_organisation_entity_id = o.organisation_entity_id
				AND review_contexts.review_context_content_type_id = ct.content_type_id
			LEFT JOIN comment_threads AS thread
				ON thread.comment_thread_id = review_contexts.review_context_comment_thread_id 
			LEFT JOIN organisation_tags AS ot ON ot.organisation_tag_organisation_entity_id = o.organisation_entity_id 
			INNER JOIN tags AS t
				ON t.tag_id = ot.organisation_tag_tag_id
			INNER JOIN tag_groups AS tg
				ON tg.tag_group_id = t.tag_tag_group_id
			LEFT JOIN tags ON tags.tag_id = ot.organisation_tag_tag_id 
			WHERE ct.content_type_codename = ? '.$sort_sql.'tg.tag_group_order ASC, t.tag_order ASC';

		$query = $this->db->query($sql, array($content_type_codename));
		$raw_reviews = $query->result_array();

		//Ok now we need to rearrange this into a useful format (Since a lot of duplicated data currently exists)
		$reviews = array(); //Make array scope for rest of function
		$data_present = 0;
		foreach ($raw_reviews as $single_review)
		{
			//Check to see if the information is already in the array
			$exists = -1;
			for ($row = 0; $row < count($reviews); $row++)
			{
				if ($data_present)
				{
					if ($single_review['organisation_entity_id'] == $reviews[$row]['organisation_entity_id'])
					{
						$exists = $row;
						break;
					}
				}
			}

			if ($exists == -1) //New entry
			{
			$display = FALSE;

				//Before allowing new entry make sure it is not filtered out by tag name
				if ($item_filter_by == 'any' || $where_equal_to == 'any' || $item_filter_by == '' || $where_equal_to == '')
				{
					//No filter
					$display = TRUE;
				}
				else
				{
					//Filter
					foreach ($raw_reviews as $search_review)
					{
						if ($search_review['tag_name'] == $where_equal_to && $search_review['tag_group_name'] == $item_filter_by && $search_review['organisation_entity_id'] == $single_review['organisation_entity_id'])
						{
							$display = TRUE;
						}
					}

				}

				if ($display)
				{
					$reviews[] = $single_review;
					$data_present = 1;
					//Add tag information to entry
					$reviews[$row]['tags'][$single_review['tag_group_name']][] = $single_review['tag_name'];
				}

			}
			else
			{
				//Add tag information to entry
				$reviews[$row]['tags'][$single_review['tag_group_name']][] = $single_review['tag_name'];
			}

		}
		
			//Get a sorted list of tag group names
			//Sort tag_groups by tag_group_order
			$sql = '
				SELECT tg.tag_group_name
				FROM organisation_tags AS ot
				INNER JOIN tags AS t
				ON t.tag_id = ot.organisation_tag_tag_id
				INNER JOIN tag_groups AS tg
				ON tg.tag_group_id = t.tag_tag_group_id
				INNER JOIN content_types ON content_types.content_type_id = 						tg.tag_group_content_type_id
				WHERE content_types.content_type_name = ?
				ORDER BY tg.tag_group_order ASC';
			$tgquery = $this->db->query($sql,$content_type_codename);
			$tg_array = $tgquery->result_array();

			$tag_groups = array(); //Array for holding the tag groups in sorted order

			foreach ($tg_array as &$tag_table)
			{
				//Form a list of all the group names used
				if (in_array($tag_table['tag_group_name'],$tag_groups) == FALSE)
				{
					$tag_groups[] = $tag_table['tag_group_name'];
				}
			}

			foreach ($reviews as &$review)
			{
				//Sort the sub tags into order (as in tag_name not the tag groups)
				foreach ($tag_groups as &$group)
				{
					//Find if we should sort the tags by tag name or by tag order
					$msql = "SELECT tag_group_ordered FROM tag_groups WHERE tag_group_name = ?";
					$mquery = $this->db->query($msql, $group);
					$mrow = $mquery->row();
					$msort = $mrow->tag_group_ordered; //If true then we don't need to do anything since it is sorted already by the query

					if ($msort == 0 && isset($review['tags'][$group])) //Sort the tags alphabetally if it isn't null
					{
						sort($review['tags'][$group]); //Sort alphabetally
					}
				}

			}


		if (isset($tag_groups)) //Incase the tag list is empty
		{
			$reviews[0]['tag_groups'] = $tag_groups; //Add tag groups to array place 0
		}
		else
		{
			$reviews[0]['tag_groups'] = 'empty';
		}

		return $reviews;
	}

	//Returns a content_type_id from a content_type_codename
	function GetContentTypeID($codename)
	{
		$sql = 'SELECT content_type_id FROM content_types WHERE content_type_codename = ?';
		$query = $this->db->query($sql, $codename);
		$query = $query->result_array();
		if ($query == NULL) return NULL;
		$query = $query[0]['content_type_id'];
		return $query;
	}

	//Returns a array of Pub names, Items and Costs for use in Barcrawls
	function GetPubList($organisation_id)
	{
		$sql = 'SELECT organisation_name,bar_crawl_organisation_recommend,bar_crawl_organisation_recommend_price FROM bar_crawl_organisations INNER JOIN organisations ON bar_crawl_organisation_organisation_entity_id = organisation_entity_id WHERE organisation';
		
		$bar_list[0]['bar_name'] = 'Toffs';
		$bar_list[1]['bar_name'] = 'Gallery';
		$bar_list[2]['bar_name'] = 'Evil Eye Lounge';
		$bar_list[3]['bar_name'] = 'The Winchester';
		$bar_list[0]['bar_drink'] = 'Deaths Calling';
		$bar_list[1]['bar_drink'] = 'House';
		$bar_list[2]['bar_drink'] = 'Deadly Rose';
		$bar_list[3]['bar_drink'] = 'The Killer';
		$bar_list[0]['bar_drink_cost'] = 140;
		$bar_list[1]['bar_drink_cost'] = 230;
		$bar_list[2]['bar_drink_cost'] = 240;
		$bar_list[3]['bar_drink_cost'] = 350;
	
		return $bar_list;
	}
	
	
	/// Get information about the private comments thread.
	/**
	 * @param $OrganisationId int ID of the organisation.
	 * @param $ContentTypeId int ID of the content type.
	 * @pre loaded(model comments_model)
	 * @return Same as comments_model::GetThreadByLinkTable
	 */
	function GetReviewContextOfficeCommentThread($OrganisationId, $ContentTypeId)
	{
		return $this->comments_model->GetThreadByLinkTable(
			'review_contexts','review_context_office_comment_thread_id',
			array(
				'review_context_organisation_entity_id'	=> $OrganisationId,
				'review_context_content_type_id'		=> $ContentTypeId,
			)
		);
	}
	
	/// Get information about the public comments thread.
	/**
	 * @param $OrganisationId int ID of the organisation.
	 * @param $ContentTypeId int ID of the content type.
	 * @pre loaded(model comments_model)
	 * @return Same as comments_model::GetThreadByLinkTable
	 */
	function GetReviewContextCommentThread($OrganisationId, $ContentTypeId)
	{
		return $this->comments_model->GetThreadByLinkTable(
			'review_contexts','review_context_comment_thread_id',
			array(
				'review_context_organisation_entity_id'	=> $OrganisationId,
				'review_context_content_type_id'		=> $ContentTypeId,
			)
		);
	}
	
	function GetOrgReviews($type_codename, $org_id)
	{
		$sql = 'SELECT 	content_type_id
			FROM	content_types
			WHERE	(content_type_codename = ?)';
		$query = $this->db->query($sql,array($type_codename));
		if ($query->num_rows() == 1)
		{
			$type_id = $query->row()->content_type_id;
			$sql = 'SELECT	article_id
				FROM	articles
				WHERE	(article_content_type_id = ?
				AND	article_organisation_entity_id = ?
				AND	article_deleted = 0
				AND	article_pulled = 0)';
			$query = $this->db->query($sql,array($type_id, $org_id));
			$result = array();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result() as $row)
				{
					$result[] = array(
						'id'=>$row->article_id
						);
				}
			}
			return $result;
		}
		else
			return false;
	}

}


