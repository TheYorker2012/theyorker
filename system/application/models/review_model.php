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

	///	Return review context.
	/**
	 * @return A single review context for an organisation
	 */
	function GetReviewContextContents($organisation_shortname,$content_type_codename)
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
			 review_context_contents.review_context_content_deal as deal,
			 review_context_contents.review_context_content_deal_expires as deal_expires
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

	///	Return review context revisions, and their author names.
	/**
	 * @return A single review context for an organisation
	 */
	function GetReviewContextContentRevisions($organisation_shortname,$content_type_codename)
	{
		$sql =
			'
			SELECT
			 unix_timestamp(review_context_contents.review_context_content_last_author_timestamp) as timestamp,
			 business_cards.business_card_name as name,
			 review_context_contents.review_context_content_last_author_user_entity_id as user_entity_id,
			 review_context_contents.review_context_content_id as context_content_id,
			 (review_contexts.review_context_live_content_id=review_context_contents.review_context_content_id ) as is_published
			FROM review_contexts 
			INNER JOIN organisations 
			ON organisations.organisation_entity_id = review_contexts.review_context_organisation_entity_id 
			 AND organisations.organisation_directory_entry_name = ?
			INNER JOIN content_types
			ON review_contexts.review_context_content_type_id=content_types.content_type_id
			 AND content_types.content_type_codename = ?
			INNER JOIN review_context_contents 
			ON review_contexts.review_context_content_type_id = review_context_contents.review_context_content_content_type_id
			 AND review_contexts.review_context_organisation_entity_id = review_context_contents.review_context_content_organisation_entity_id
			INNER JOIN business_cards 
			ON business_cards.business_card_user_entity_id=review_context_contents.review_context_content_last_author_user_entity_id
			INNER JOIN business_card_groups
			ON business_card_groups.business_card_group_id=business_cards.business_card_business_card_group_id
			 AND business_card_group_organisation_entity_id =
			     (SELECT organisation_entity_id FROM organisations WHERE organisations.organisation_directory_entry_name = "theyorker")
			WHERE 1
			ORDER BY review_context_contents.review_context_content_last_author_timestamp DESC
			';

		$query = $this->db->query($sql, array($organisation_shortname,$content_type_codename) );

		return $query->result_array();
	}

	/**
	 * Adds a review content to the db
	 *
	 */

	function SetReviewContextContent($organisation_shortname, $content_type_codename, $user_entity_id, $blurb, $quote, $average_price, $recommended_item,
							$rating, $serving_times, $deal, $deal_expires, $publish = false)
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
			 review_context_content_serving_times,
			 review_context_content_deal,
			 review_context_content_deal_expires
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
			? as serving_times,
			? as deal,
			? as deal_expires
			FROM review_contexts 
			INNER JOIN organisations 
			ON organisations.organisation_entity_id = review_contexts.review_context_organisation_entity_id 
			 AND organisations.organisation_directory_entry_name = ?
			INNER JOIN content_types
			ON review_contexts.review_context_content_type_id=content_types.content_type_id
			 AND content_types.content_type_codename = ?
			WHERE 1
			LIMIT 1
			;
			';
			
		if ($publish) $sql +=
			'
			UPDATE review_contexts
			SET review_contexts.review_context_live_content_id = LAST_INSERT_ID() 
			WHERE review_contexts.review_context_organisation_entity_id = (SELECT organisation_entity_id FROM organisations WHERE organisations.organisation_directory_entry_name = ?)
			 AND review_contexts.review_context_content_type_id = (SELECT content_type_id FROM content_types WHERE content_types.content_type_codename = ?)
			;
			';

		$query = $this->db->query($sql, array($user_entity_id, $blurb, $quote, $average_price, $recommended_item,
							$rating, $serving_times, $deal, $deal_expires, $organisation_shortname, $content_type_codename, $organisation_shortname, $content_type_codename) );
	}


	function GetReview($organisation_directory_entry_name,$content_type_codename) {

	#dgh500
	# need organisation type?
	# need organisation fileas - what IS this?? all null in DB
	$sql = '
			SELECT
			organisations.organisation_entity_id,
			organisations.organisation_name,
			organisations.organisation_fileas,

			organisation_contents.organisation_content_description as organisation_description,
			organisation_contents.organisation_content_location as organisation_location,
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
			review_context_contents.review_context_content_deal,
			review_context_contents.review_context_content_deal_expires,
			review_context_contents.review_context_content_rating,
			review_context_contents.review_context_content_serving_times,
			review_context_contents.review_context_content_content_type_id
			  FROM content_types
			  INNER JOIN review_context_contents
			  ON content_types.content_type_id = review_context_contents.review_context_content_content_type_id
			  INNER JOIN organisations
			  ON review_context_contents.review_context_content_organisation_entity_id = organisations.organisation_entity_id
			  INNER JOIN organisation_contents 
			  ON organisations.organisation_live_content_id = organisation_contents.organisation_content_id 
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
				organisation_contents.organisation_content_url,
				review_context_contents.review_context_content_blurb as organisation_description,
				organisations.organisation_directory_entry_name,
				league_entries.league_entry_position,
				leagues.league_name,
				leagues.league_image_id,
				content_types.content_type_name,
				content_types.content_type_codename,
				comment_summary_cache.comment_summary_cache_average_rating
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
				ON leagues.league_id = league_entries.league_entry_league_id AND content_types.content_type_id = leagues.league_content_type_id
				LEFT JOIN comment_summary_cache
				ON comment_summary_cache.comment_summary_cache_content_type_id = content_types.content_type_id
				   AND comment_summary_cache.comment_summary_cache_organisation_entity_id = organisations.organisation_entity_id
				   AND comment_summary_cache.comment_summary_cache_article_id IS NULL
				WHERE leagues.league_codename = ?
				ORDER BY ? ?
				';
	$query = $this->db->query($sql,array($league_codename,$sortby,$order));
	$tmpleague = array();
	$league    = array();

	// Assign nice names to the result
	foreach($query->result() as $row) {
		$tmpleague['organisation_name']        = $row->organisation_name;
		$tmpleague['organisation_url']         = $row->organisation_content_url;
		$tmpleague['organisation_description']         = $row->organisation_description;
		$tmpleague['league_entry_position']    = $row->league_entry_position;
		$tmpleague['league_name']              = $row->league_name;
		$tmpleague['content_type_name']		   = $row->content_type_name;
		$tmpleague['content_type_codename']	   = $row->content_type_codename;
		$tmpleague['average_user_rating']	   = $row->comment_summary_cache_average_rating;
		$tmpleague['organisation_directory_entry_name'] = $row->organisation_directory_entry_name;
		$league[]                              = $tmpleague;
	}

	return $league;
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
			organisations.organisation_directory_entry_name = ?";
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
				content_type_name = ?";
		$query = $this->db->query($sql,$type_name);
		$result = $query->row_array();

		return $result['content_type_id'];
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

	//Gets comments from database, frb501
	function GetComments($organisation_name, $type, $article_id)
	{
		$sql = "SELECT comment_text, comment_timestamp, comment_rating, comment_reported_count FROM comments WHERE comment_organisation_entity_id = ? AND comment_content_type_id = ? AND comment_article_id = ?";
		$query = $this->db->query($sql,array($this->FindOrganisationID($organisation_name),$type,$article_id));

		if ($query->num_rows() > 0)
		{
			$commentno = 0;
			foreach ($query->result() as $row)
			{
			$comments['comment_author'][$commentno] = 'nothing';
			$comments['comment_rating'][$commentno] = $row->comment_rating;
			$comments['comment_date'][$commentno] = $row->comment_timestamp;
			$comments['comment_content'][$commentno] = $row->comment_text;
			$comments['comment_reported_count'][$commentno] = $row->comment_reported_count;
			$commentno++;
			}

			return $comments;
		}
		else
		{
			$nocomments = array();
			return $nocomments;
		}
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
				INNER JOIN content_types ON content_types.content_type_id = tag_groups.tag_group_content_type_id WHERE content_types.content_type_name = ?
				ORDER BY tag_group_order
				';
		$query = $this->db->query($sql,$type);
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
					 WHERE tag_groups.tag_group_name = ? ORDER BY tags.tag_order';
			}
			else
			{								//Order by field tag_name
			$msql = '						
					 SELECT tags.tag_name FROM tags
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

	//Adds a comment to the database, frb501
	function SetComment($post_data)
	{
		$comment['comment_content_type_id'] = $post_data['comment_type_id'];
		$comment['comment_organisation_entity_id'] = $post_data['comment_organisation_id'];
		$comment['comment_article_id'] = $post_data['comment_article_id'];
		$comment['comment_user_entity_id'] = $post_data['comment_user_entity_id'];
		$comment['comment_text'] = $post_data['comment_text'];
		$this->db->insert('comments',$comment); //Add users comment to database
	}

	//Mirrored from GetReview - This should return all the rows in a given type
	function TableReview($content_type_codename,$sorted_by = 'any',$item_filter_by = 'any',$where_equal_to = 'any')
	{
		
		switch ($sorted_by) //Set sorting query
		{
			case 'name':
				$sort_sql = 'ORDER BY o.organisation_name';
			break;

			case 'star':
				$sort_sql = 'ORDER BY rcc.review_context_content_rating DESC';			
			break;

			case 'user':
				$sort_sql = 'ORDER BY csc.comment_summary_cache_average_rating DESC';
			break;

			default:
				$sort_sql = 'ORDER BY o.organisation_name'; //Lets default to name sorting can be changed later
			break;
		}

		if ($item_filter_by == 'any' || $where_equal_to == 'any' || $item_filter_by == '' || $where_equal_to == '') //Set no filter in these cases by tags
			{
				$filter_sql = ''; $tag_join = '';
			}
			else
			{
			//Filtering by tag, link the query to the tag table
			$tag_join = ' LEFT JOIN organisation_tags AS ot ON ot.organisation_tag_organisation_entity_id = o.organisation_entity_id 
LEFT JOIN tags ON tags.tag_id = ot.organisation_tag_tag_id ';

				$filter_sql = " AND tags.tag_name = '" . $where_equal_to . "' ";
			}

		$sql = '
			SELECT o.organisation_entity_id, o.organisation_name, oc.organisation_content_url, o.organisation_directory_entry_name,
			rcc.review_context_content_rating, csc.comment_summary_cache_average_rating
			FROM content_types AS ct
			INNER JOIN review_context_contents AS rcc
			ON ct.content_type_id = rcc.review_context_content_content_type_id
			INNER JOIN organisations AS o
			ON rcc.review_context_content_organisation_entity_id = o.organisation_entity_id
			INNER JOIN organisation_contents AS oc
			ON o.organisation_live_content_id = oc.organisation_content_id
			LEFT JOIN comment_summary_cache AS csc
			ON csc.comment_summary_cache_content_type_id = ct.content_type_id
			AND csc.comment_summary_cache_organisation_entity_id = o.organisation_entity_id
			'.$tag_join.'
			WHERE ct.content_type_codename = ? '. $filter_sql . $sort_sql;



		$query = $this->db->query($sql, array($content_type_codename));
		$reviews = $query->result_array();
		
		if (! empty($reviews))
		{
			$entity_ids = array();
			foreach ($reviews as &$review)
			{
				$entity_ids[] = $review['organisation_entity_id'];
				$review['tags'] = array();
			}
			
			//Get the tags	
			$sql = '
				SELECT ot.organisation_tag_organisation_entity_id, t.tag_name, tg.tag_group_name
				FROM organisation_tags AS ot
				INNER JOIN tags AS t
				ON t.tag_id = ot.organisation_tag_tag_id
				INNER JOIN tag_groups AS tg
				ON tg.tag_group_id = t.tag_tag_group_id
				INNER JOIN content_types ON content_types.content_type_id = 				tg.tag_group_content_type_id
				WHERE ot.organisation_tag_organisation_entity_id=' . implode(" OR ", $entity_ids) . ' && content_types.content_type_name = ?
				ORDER BY t.tag_order ASC'; //Default sort by tag_order
	
			$query = $this->db->query($sql,$content_type_codename);
			$tags = $query->result_array();

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
				WHERE ot.organisation_tag_organisation_entity_id=' . implode(" OR ", $entity_ids) . ' && content_types.content_type_name = ?
				ORDER BY tg.tag_group_order ASC';
			$tgquery = $this->db->query($sql,$content_type_codename);
			$tg_array = $tgquery->result_array();

			$tag_groups = array(); //Array for holding the tag groups in sorted order
			$new_tag_group = 0;

			foreach ($tg_array as &$tag_table)
			{
				//Form a list of all the group names used
				if (in_array($tag_table['tag_group_name'],$tag_groups) == FALSE)
				{
					$tag_groups[$new_tag_group] = $tag_table['tag_group_name'];
					$new_tag_group++;
				}
			}

			foreach ($reviews as &$review)
			{
				foreach ($tags as $id => $tag)
				{
					if ($tag['organisation_tag_organisation_entity_id'] == $review['organisation_entity_id'])
					{
						$review['tags'][$tag['tag_group_name']][] = $tag['tag_name'];
					}
				}

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
}

