<?php
/**
 *Template for request_model,  still to be tested
 *@author Alex Fargus (agf501)
 **/

class Requests_Model extends Model
{
	function RequestsModel()
	{
		parent::Model();
	}

	// Retrieve list of all reporters (this includes editors and photographers) that a request can be assigned to
	function getReporters ()
	{
		$sql = 'SELECT user_entity_id AS id, user_firstname AS firstname, user_surname AS surname
				FROM users
				WHERE user_office_access = 1
				AND user_admin = 0
				ORDER BY user_firstname ASC, user_surname ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// Retrieve a list of all the boxes a request can be assigned to
	function getBoxes ($section)
	{
		$sql = 'SELECT content_type_name AS name, content_type_codename AS code
				FROM content_types
				WHERE content_type_codename IS NOT NULL
				AND content_type_section = ?
				ORDER BY content_type_section_order ASC';
		$query = $this->db->query($sql,array($section));
		return $query->result_array();
	}

	//Add a  new request to the article table
	function CreateRequest($status,$type_id,$title,$description,$user,$date)
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
					article_request_entity_id,
					article_editor_approved_user_entity_id,
					article_publish_date)
				VALUES (?,CURRENT_TIMESTAMP,?,?,1,?,?,?)';
			$query = $this->db->query($sql,array($type_id,$title,$description,$user,$user,$date));
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

	//Make a change to a request status in the article table
	function UpdateRequestStatus($article_id,$status,$data)
	{
		if ($status == 'request')
		{
			$sql = 'UPDATE 	articles
				SET	article_suggestion_accepted = 1,
					article_editor_approved_user_entity_id = ?,
					article_publish_date = ?,
					article_request_title = ?,
					article_request_description = ?,
					article_content_type_id = ?
				WHERE	(article_id = ?)';
			$query = $this->db->query($sql,array($data['editor'],$data['publish_date'],$data['title'],$data['description'],$data['content_type'],$article_id));
		}
		else if ($status == 'publish')
		{
			$sql = 'UPDATE 	articles
				SET	article_live_content_id = ?,
					article_publish_date = ?,
					article_editor_approved_user_entity_id = ?,
					article_pulled = 0
				WHERE	(article_id = ?)';
			$query = $this->db->query($sql,array($data['content_id'],$data['publish_date'],$data['editor'],$article_id));
		}

		return $status;
	}

	//Make a change to a request status in the article table
	function UpdatePulledToRequest($article_id, $editor_id)
	{
		$sql = 'UPDATE 	articles
			SET	article_pulled = 0,
				article_editor_approved_user_entity_id = ?,
				article_publish_date = CURRENT_TIMESTAMP,
				article_live_content_id = 0
			WHERE	(article_id = ?)';
		$query = $this->db->query($sql,array($editor_id, $article_id));
	}

	//Make a change to the title, description and content type of a suggestion
	function UpdateSuggestion($article_id,$data)
	{
		$sql = 'UPDATE 	articles
			SET	article_request_title = ?,
				article_request_description = ?,
				article_content_type_id = ?
			WHERE	(article_id = ?)';
		$query = $this->db->query($sql,array($data['title'],$data['description'],$data['content_type'],$article_id));
	}

	//Make a change to the content type of an article
	function UpdateContentType($article_id,$content_type)
	{
		$sql = 'UPDATE 	articles
			SET	article_content_type_id = ?
			WHERE	(article_id = ?)';
		$query = $this->db->query($sql,array($content_type,$article_id));
	}

	function GetPulledArticles($type_id)
	{
		$sql = 'SELECT	article_id 
			FROM	articles
			LEFT JOIN content_types
			ON 	article_content_type_id = content_type_id
			WHERE	(content_type_codename = ?
			AND	article_pulled = 1)
			ORDER BY article_publish_date DESC';
		$query = $this->db->query($sql,array($type_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = $row->article_id;
			}
		}
	}

	//Should this be get completed articles?
	function GetPublishedArticles($type_id, $is_published, $is_pulled = FALSE)
	{
		$sql = 'SELECT	article_id,
			article_publish_date,
			article_live_content_id,
			article_content_last_author_timestamp,
			article_content_heading,
                                article_content_last_author_user_entity_id,
			article_editor_approved_user_entity_id,
			author_user.business_card_name as author_name,
			editor_user.business_card_name as editor_name

			FROM	articles
			JOIN	article_contents
			ON      article_content_id = article_live_content_id

			JOIN	business_cards as editor_user
			ON	editor_user.business_card_user_entity_id = article_editor_approved_user_entity_id
			JOIN	business_cards as author_user
			ON	author_user.business_card_user_entity_id = article_content_last_author_user_entity_id

			WHERE	article_suggestion_accepted = 1
			AND	article_content_type_id = ?
			AND	article_live_content_id IS NOT NULL
			AND	article_deleted = 0
			AND	article_pulled = ?';
		if ($is_published == TRUE)
			$sql .= ' AND	article_publish_date <= CURRENT_TIMESTAMP';
		else
			$sql .= ' AND	article_publish_date > CURRENT_TIMESTAMP';
		$query = $this->db->query($sql,array($type_id, $is_pulled));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item = array(
					'id'=>$row->article_id,
					'heading'=>$row->article_content_heading,
					'publish'=>$row->article_publish_date,
					'lastedit'=>$row->article_content_last_author_timestamp,
					'editorid'=>$row->article_editor_approved_user_entity_id,
					'editorname'=>$row->editor_name,
					'authorid'=>$row->article_content_last_author_user_entity_id,
					'authorname'=>$row->author_name
					);
				$result[] = $result_item;
			}
		}

		return $result;
	}
	
	function GetRequestedArticles($type_id)
	{
		$sql = 'SELECT	article_id,
				article_request_title,
				article_request_description,
				article_request_entity_id,
				article_publish_date,
				article_request_entity_id,
				article_editor_approved_user_entity_id,
				suggestion_user.business_card_name as suggestion_name,
				editor_user.business_card_name as editor_name
			FROM	articles

			JOIN	business_cards as editor_user
			ON	editor_user.business_card_user_entity_id = article_editor_approved_user_entity_id
			JOIN	business_cards as suggestion_user
			ON	suggestion_user.business_card_user_entity_id = article_request_entity_id
			
			WHERE	article_suggestion_accepted = 1
			AND	article_content_type_id = ?
			AND	article_live_content_id IS NULL
			AND	article_deleted = 0
			AND	article_pulled = 0';
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
					'deadline'=>$row->article_publish_date,
					'suggestionuserid'=>$row->article_request_entity_id,
					'suggestionusername'=>$row->suggestion_name,
					'editorid'=>$row->article_editor_approved_user_entity_id,
					'editorname'=>$row->editor_name
					);
				$result[] = $result_item;
			}
		}

		return $result;
	}
	
	function GetSuggestedArticles($type_id)
	{
		$sql = 'SELECT	article_id,
				article_request_title,
				article_request_description,
				article_created,
				article_request_entity_id,
				business_card_name
			FROM	articles
			JOIN	business_cards
			ON	business_card_user_entity_id = article_request_entity_id
			WHERE	article_suggestion_accepted = 0
			AND	article_content_type_id = ?
			AND	article_deleted = 0
			AND	article_pulled = 0';
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
					'userid'=>$row->article_request_entity_id,
					'username'=>$row->business_card_name,
					'created'=>$row->article_created
					);
				$result[] = $result_item;
			}
		}
		return $result;
	} 
	
	function GetArticleRevisions($article_id)
	{
		$sql = 'SELECT	article_content_id,
				article_content_heading,
				article_content_last_author_user_entity_id,
				article_content_last_author_timestamp,
				business_card_name
			FROM	article_contents

			JOIN	business_cards
			ON      business_card_user_entity_id = article_content_last_author_user_entity_id

			WHERE	article_content_article_id = ?
			ORDER BY	article_content_last_author_timestamp DESC';
		$query = $this->db->query($sql,array($article_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item = array(
					'id'=>$row->article_content_id,
					'title'=>$row->article_content_heading,
					'updated'=>$row->article_content_last_author_timestamp,
					'userid'=>$row->article_content_last_author_user_entity_id,
					'username'=>$row->business_card_name
					);
				$result[] = $result_item;
			}
		}
		return $result;
	}

	function AddUserToRequest($article_id,$user_id)
	{
		$sql = 'INSERT	INTO article_writers(
				article_writer_user_entity_id,
				article_writer_article_id)
			VALUES	(?,?)';
		$query = $this->db->query($sql,array($user_id,$article_id));
	}	
	
	function RemoveUserFromRequest($article_id,$user_id)
	{
		$sql = 'DELETE FROM article_writers WHERE (article_writer_article_id = ?
			AND article_writer_user_entity_id = ?)';
		$query = $this->db->query($sql,array($article_id,$user_id));
	}
	
	function RequestPhoto($article_id,$user_id)
	{
		$sql = 'INSERT INTO ';
	}
	
	function AcceptPhoto($article_id,$photo_id)
	{
		$sql = '';
	}
	
	function AcceptRequest($id, $user)
	{
		$sql = 'UPDATE 	article_writers
			SET	article_writer_status = "accepted"
			WHERE	(article_writer_user_entity_id = ?
			AND	article_writer_article_id = ?)';
		$query = $this->db->query($sql,array($user,$id));
	}
	
	function DeclineRequest($id,$user)
	{
	
		$sql = 'UPDATE 	article_writers
			SET	article_writer_status = "declined"
			WHERE	(article_writer_user_entity_id = ?
			AND	article_writer_article_id = ?)';
		$query = $this->db->query($sql,array($user,$id));
	} 

	function CreateArticleRevision($id,$user,$heading,$subheading,$subtext,$wikitext,$blurb)
	{
		$this->load->library('wikiparser');
		$cache = $this->wikiparser->parse($wikitext);
		$sql = 'INSERT	INTO article_contents(
				article_content_article_id,
				article_content_last_author_user_entity_id,
				article_content_heading,
				article_content_subheading,
				article_content_subtext,
				article_content_wikitext,
				article_content_wikitext_cache,
				article_content_blurb)
			VALUES	(?,?,?,?,?,?,?,?)';
		$query = $this->db->query($sql,array($id,$user,$heading,$subheading,$subtext,$wikitext,$cache,$blurb));
		return $this->db->insert_id();
	}

	function UpdateArticleRevision($id,$user,$heading,$subheading,$subtext,$wikitext,$blurb)
	{
		$this->load->library('wikiparser');
                $cache = $this->wikiparser->parse($wikitext);
		$sql = 'UPDATE	article_contents
			SET	article_content_last_author_user_entity_id = ?,
				article_content_heading = ?,
				article_content_subheading = ?,
				article_content_subtext = ?,
				article_content_wikitext = ?,
				article_content_wikitext_cache = ?,
				article_content_blurb = ?
			WHERE	(article_content_id = ?)';
		$query = $this->db->query($sql,array($user,$heading,$subheading,$subtext,$wikitext,$cache,$blurb,$id));
	}	

	function RejectSuggestion($id)
	{
		$sql = 'UPDATE	articles
			SET	article_deleted = 1
			WHERE	(article_id = ?)';
		$query = $this->db->query($sql,array($id));
	}
}
?>
