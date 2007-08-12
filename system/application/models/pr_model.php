<?php

// Pr Model

class Pr_model extends Model {

	function Pr_Model()
	{
		parent::Model();
	}

	///	Return list of all organisations and thier Name of Place, Date of Last Review, Number of Reviews, and Info Complete status
	function GetContentTypeId($content_type_codename)
	{
		$sql = 'SELECT content_type_id FROM content_types WHERE content_type_codename = ?';

		$query = $this->db->query($sql, $content_type_codename );

		if ($query->num_rows() != 0) {

			$query = $query->result_array();

			$query = $query[0];

			$content_type_id = $query['content_type_id'];

			return $content_type_id;
		} else {
			return 0;
		}
	}

	///	Return list of all organisations and thier Name of Place, Date of Last Review, Number of Reviews, and Info Complete status
	function GetReviewContextListFromId($content_type, $urlpath='directory/', $urlpostfix='')
	{

		$sql =
		'
		SELECT
			organisations.organisation_name as name,
			organisations.organisation_directory_entry_name as shortname,
			CONCAT(?, organisations.organisation_directory_entry_name, ?) as link,
			review_contexts.review_context_assigned_user_entity_id as assigned_user_id,
			CONCAT(users.user_firstname, " ", users.user_surname) as assigned_user_name,

			(
			 review_context_contents.review_context_content_blurb IS NOT NULL AND
			 review_context_contents.review_context_content_quote IS NOT NULL AND
			 review_context_contents.review_context_content_average_price IS NOT NULL AND
			 review_context_contents.review_context_content_recommend_item IS NOT NULL AND
			 review_context_contents.review_context_content_rating IS NOT NULL
			) as info_complete,

			(
			 SELECT MAX(article_publish_date)
			 FROM articles
			 WHERE
				articles.article_content_type_id = ?
			  AND
				articles.article_organisation_entity_id = organisations.organisation_entity_id
			  AND
			  	articles.article_deleted = 0
			  AND
			  	articles.article_live_content_id IS NOT NULL
			) as date_of_last_review,

			(
			 SELECT COUNT(*)
			 FROM articles
			 WHERE
				articles.article_content_type_id = ?
			  AND
				articles.article_organisation_entity_id = organisations.organisation_entity_id
			  AND
			  	articles.article_deleted = 0
			  AND
			  	articles.article_live_content_id IS NOT NULL
			) as review_count

		FROM organisations
		INNER JOIN review_contexts
		  ON
			review_contexts.review_context_organisation_entity_id = organisations.organisation_entity_id
		  AND
			review_contexts.review_context_content_type_id = ?
		  AND
			review_contexts.review_context_deleted = 0

		LEFT JOIN users
		  ON
		   	users.user_entity_id =  review_contexts.review_context_assigned_user_entity_id

		LEFT JOIN review_context_contents
		  ON
			review_contexts.review_context_live_content_id = review_context_contents.review_context_content_id

		WHERE organisation_parent_organisation_entity_id IS NULL

		ORDER BY info_complete, date_of_last_review ASC
		';

		$query = $this->db->query($sql, array($urlpath, $urlpostfix, $content_type, $content_type, $content_type) );

		return $query->result_array();

	}
	
	function GetSuggestedOrganistions()
	{
		$sql = 'SELECT	organisation_entity_id,
						organisation_name,
						organisation_directory_entry_name,
						organisation_content_last_author_user_entity_id,
						organisation_content_last_author_timestamp,
						user_firstname,
						user_surname
				FROM	organisations
				LEFT JOIN organisation_contents
				ON		organisation_live_content_id = organisation_content_id
				INNER JOIN users
				ON		organisation_content_last_author_user_entity_id = user_entity_id
				WHERE	organisation_needs_approval = 1
				AND		organisation_pr_rep IS NULL
				ORDER BY organisation_name ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['org_id'] = $row->organisation_entity_id;
				$result_item['org_name'] = $row->organisation_name;
				$result_item['org_dir_entry_name'] = $row->organisation_directory_entry_name;
				$result_item['user_id'] = $row->organisation_content_last_author_user_entity_id;
				$result_item['user_firstname'] = $row->user_firstname;
				$result_item['user_surname'] = $row->user_surname;
				$result_item['suggested_time'] = $row->organisation_content_last_author_timestamp;
				$result[] = $result_item;
			}
		}
		return $result;
	}
}


