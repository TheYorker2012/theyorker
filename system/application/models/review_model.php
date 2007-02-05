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

	function GetReview($organisation_directory_entry_name,$content_type_codename) {

	#dgh500
	# need organisation type?
	# need organisation fileas - what IS this?? all null in DB
	$sql = '
			SELECT
			organisations.organisation_entity_id,
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
				organisations.organisation_directory_entry_name,
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
				WHERE leagues.league_codename = ?
				ORDER BY ? ?
				';
	$query = $this->db->query($sql,array($league_codename,$sortby,$order));
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
		$tmpleague['organisation_directory_entry_name'] = $row->organisation_directory_entry_name;
		$league[]                              = $tmpleague;
	}

	return $league;
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
			$nocomments = array();
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
			SELECT o.organisation_entity_id, o.organisation_name, o.organisation_url, o.organisation_directory_entry_name,
			rcc.review_context_content_rating, csc.comment_summary_cache_average_rating
			FROM content_types AS ct
			INNER JOIN review_context_contents AS rcc
			ON ct.content_type_id = rcc.review_context_content_content_type_id
			INNER JOIN organisations AS o
			ON rcc.review_context_content_organisation_entity_id = o.organisation_entity_id
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
				WHERE ot.organisation_tag_organisation_entity_id=' . implode(" OR ", $entity_ids) . '
				ORDER BY t.tag_order ASC'; //Default sort by tag_order
	
			$query = $this->db->query($sql);
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
				WHERE ot.organisation_tag_organisation_entity_id=' . implode(" OR ", $entity_ids) . '
				ORDER BY tg.tag_group_order ASC';
			$tgquery = $this->db->query($sql);
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

		$reviews[0]['tag_groups'] = $tag_groups; //Add tag groups to array place 0

		return $reviews;
	}
}

