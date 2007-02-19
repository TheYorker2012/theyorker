<?php
/**
 * This model should add articles to the database. NOT yet complete.
 *
 * @author Alex Fargus (agf501)
 * @author Richard Ingle (ri504)
 *
 */
class Article_model extends Model
{

	function ArticleModel()
	{
		// Call the Model Constructor
		parent::Model();
		$this->load->library('wikiparser');
	}

	// Returns names from Business Card, assumes $user_id exists
	function GetBusinessCardName($user_id)
	{
		$sql = 'SELECT business_card_name
				FROM business_cards
				WHERE business_card_user_entity_id = ?';
		$query = $this->db->query($sql, array($user_id));
		$row = $query->row();
		return $row->business_card_name;
	}

	// Get the majority of information about an article
	function GetArticleDetails($article_id)
	{
		$sql = 'SELECT articles.article_id AS id,
				 articles.article_content_type_id AS box_id,
				 UNIX_TIMESTAMP(articles.article_created) AS date_created,
				 UNIX_TIMESTAMP(articles.article_publish_date) AS date_deadline,
				 articles.article_request_title AS request_title,
				 articles.article_request_description AS request_description,
				 articles.article_request_entity_id AS suggest_userid,
				 articles.article_editor_approved_user_entity_id AS editor_userid,
				 content_types.content_type_name AS box_name,
				 content_types.content_type_codename AS box_codename
				FROM articles, content_types
				WHERE articles.article_id = ?
				AND articles.article_suggestion_accepted = 1
				AND articles.article_deleted = 0
				AND articles.article_live_content_id IS NULL
				AND articles.article_content_type_id = content_types.content_type_id';
		$query = $this->db->query($sql, array($article_id));
		$result = array();
		if ($query->num_rows() == 1) {
			$result = $query->row_array();
			$result['suggest_name'] = $this->GetBusinessCardName($result['suggest_userid']);
			$result['editor_name'] = $this->GetBusinessCardName($result['editor_userid']);
			$sql = 'SELECT article_writers.article_writer_user_entity_id AS id,
					 article_writers.article_writer_status AS status,
					 business_cards.business_card_name AS name
					FROM article_writers, business_cards
					WHERE article_writers.article_writer_article_id = ?
					AND article_writers.article_writer_user_entity_id = business_cards.business_card_user_entity_id';
			$query = $this->db->query($sql, array($result['id']));
			$result['reporters'] = array();
			foreach ($query->result_array() as $row) {
				$result['reporters'][] = $row;
			}
		}
		return $result;
	}

	// Gets all comments for a particular article whilst it is still accessible via News Office, assumes article_id exists
	function GetArticleComments ($article_id)
	{
		$sql = 'SELECT comments.comment_id AS id,
				 comments.comment_user_entity_id AS userid,
				 comments.comment_text AS text,
				 UNIX_TIMESTAMP(comments.comment_timestamp) AS time,
				 business_cards.business_card_name AS name
				FROM comments, business_cards
				WHERE comments.comment_article_id = ?
				AND comments.comment_deleted = 0
				AND comments.comment_user_entity_id = business_cards.business_card_user_entity_id
				ORDER BY comments.comment_timestamp DESC';
		$query = $this->db->query($sql, array($article_id));
		$result = array();
		foreach ($query->result_array() as $row) {
			$result[] = $row;
		}
		return $result;
	}

	function InsertArticleComment ($article_id, $user_id, $comment)
	{
		$sql = 'INSERT INTO comments
				SET comment_article_id = ?,
				 comment_user_entity_id = ?,
				 comment_text = ?';
		$query = $this->db->query($sql, array($article_id, $user_id, $comment));
		$sql = 'SELECT UNIX_TIMESTAMP(comments.comment_timestamp) AS time,
				 business_cards.business_card_name AS name
				FROM comments, business_cards
				WHERE comments.comment_user_entity_id = business_cards.business_card_user_entity_id
				AND comments.comment_id = ?';
		$query = $this->db->query($sql, array($this->db->insert_id()));
		return $query->row_array();
	}

	function GetLatestRevision ($article_id)
	{
		$sql = 'SELECT article_content_id
				FROM article_contents
				WHERE article_content_article_id = ?
				ORDER BY article_content_last_author_timestamp DESC
				LIMIT 0,1';
		$query = $this->db->query($sql, array($article_id));
		if ($query->num_rows() == 1) {
			$result = $query->row_array();
			return $result['article_content_id'];
		} else {
			return false;
		}
	}

	// Assumes revision id exists
	function GetRevisionData ($revision_id)
	{
		$sql = 'SELECT article_contents.article_content_last_author_user_entity_id AS last_author,
				 UNIX_TIMESTAMP(article_contents.article_content_last_author_timestamp) AS last_edit,
				 article_contents.article_content_heading AS headline,
				 article_contents.article_content_subheading AS subheadline,
				 article_contents.article_content_subtext AS subtext,
				 article_contents.article_content_wikitext AS text,
				 article_contents.article_content_blurb AS blurb
				FROM article_contents
				WHERE article_contents.article_content_id = ?';
		$query = $this->db->query($sql, array($revision_id));
		return $query->row_array();
	}

	function CreateNewRevision ($article_id, $user_id, $headline, $subheadline, $subtext, $blurb, $wiki, $wiki_cache)
	{
		$sql = 'INSERT INTO article_contents
				SET article_content_article_id = ?,
				 article_content_last_author_user_entity_id = ?,
				 article_content_heading = ?,
				 article_content_subheading = ?,
				 article_content_subtext = ?,
				 article_content_wikitext = ?,
				 article_content_blurb = ?,
				 article_content_wikitext_cache = ?';
		$query = $this->db->query($sql, array($article_id, $user_id, $headline, $subheadline, $subtext, $wiki, $blurb, $wiki_cache));
		return $this->db->insert_id();
	}

	function GetArticleRevisionToEdit ($article_id, $user_id, $revision = 0)
	{
		$sql = 'SELECT article_content_id
				FROM article_contents
				WHERE article_content_article_id = ?
				AND article_content_last_author_user_entity_id = ?
				AND (DATE_ADD(article_content_last_author_timestamp, INTERVAL 1 HOUR) > CURRENT_TIMESTAMP) ';
		if ($revision > 0) {
			$sql .= 'AND article_content_id = ' . $revision. ' ';
		}
		$sql .= 'ORDER BY article_content_last_author_timestamp DESC
				LIMIT 0,1';
		$query = $this->db->query($sql, array($article_id, $user_id));
		if ($query->num_rows() == 0) {
			return 0;
		} else {
			$row = $query->row_array();
			return $row['article_content_id'];
		}
	}

	function UpdateRevision ($revision,$headline,$subheadline,$subtext,$blurb,$wiki,$wiki_cache)
	{
		$sql = 'UPDATE article_contents
				SET article_content_heading = ?,
				 article_content_subheading = ?,
				 article_content_subtext = ?,
				 article_content_blurb = ?,
				 article_content_wikitext = ?,
				 article_content_wikitext_cache = ?
				WHERE article_content_id = ?';
		$query = $this->db->query($sql, array($headline,$subheadline,$subtext,$blurb,$wiki,$wiki_cache,$revision));
	}






	/**
	 * Adds an article to the database
	 * @param 'content_type_id' 'organisation_entity_id' 'initial_editor' /
	 * 'publish_date' 'heading' 'subheading' 'subtext' 'wikitext' 'blurb'
	 *
	 *
	 */

	function CommitArticle($content_type_id, $organisation_entity_id, $initial_editor, $publish_date,
							$heading, $subheading, $subtext, $wikitext, $blurb)
	{
	$this->db->trans_start();
	$wiki_cache = $this->wikiparser->parse($wikitext);
	$sql = 'INSERT INTO articles (
			articles.article_content_type_id,
			articles.article_organisation_entity_id,
			articles.article_initial_editor_user_entity_id,
			articles.article_publish_date,
			articles.article_created)
			VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)';
	$this->db->query($sql,array($content_type_id,$organisation_entity_id,$initial_editor,$publish_date));
	$sql = 'INSERT INTO article_contents (
			article_contents.article_content_article_id,
			article_contents.article_content_heading,
			article_contents.article_content_subheading,
			article_contents.article_content_subtext,
			article_contents.article_content_wikitext,
			article_contents.article_content_wikitext_cache,
			article_contents.article_content_blurb)
			VALUES (@article_id:=LAST_INSERT_ID(), ?, ?, ?, ?, ?)';
	$this->db->query($sql,array($heading, $subheading, $subtext, $wikitext, $wikitext_cache, $blurb));
	$sql = 'UPDATE articles
			SET articles.article_live_content_id = LAST_INSERT_ID()
			WHERE (articles.article_id = @article_id)';
	$this->db->query($sql);
	$this->db->trans_complete();
	}





















	/*****************************************************
	*  PINGU HOW DO I
	*****************************************************/

        /**
	 * Gets the header information of the specified article id,
	 * if the article doesn't exist returns FALSE.
	 * returns the fields to work out its status (suggested, requested
	 * published, etc)
	 */
	function GetArticleHeader($article_id)
	{
        	$sql = 'SELECT	article_content_type_id,
				article_organisation_entity_id,
				article_created,
				article_publish_date,
				article_location,
				article_live_content_id,
				article_suggestion_accepted,
				article_pulled,
				article_request_title,
				article_request_description
			FROM	articles
			WHERE	article_id = ?
			AND	article_deleted = FALSE';
		$query = $this->db->query($sql, array($article_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			$returndata = array(
				'content_type'=>$row->article_content_type_id,
				'organisation'=>$row->article_organisation_entity_id,
				'created'=>$row->article_created,
				'publish_date'=>$row->article_publish_date,
				'location'=>$row->article_location,
				'live_content'=>$row->article_live_content_id,
				'suggestion_accepted'=>$row->article_suggestion_accepted,
				'pulled'=>$row->article_pulled,
				'requesttitle'=>$row->article_request_title,
				'requestdescription'=>$row->article_request_description
				);
			if ($row->article_suggestion_accepted == FALSE)
				$returndata['status'] = 'suggestion';
			else
				if ($row->article_live_content_id != FALSE)
				{
					$returndata['status'] = 'published';
					if ($row->article_pulled == TRUE)
						$returndata['status'] = 'pulled';
				}
				else
					$returndata['status'] = 'request';
			return $returndata;
		}
		else
			return FALSE;
	}

	/**
	 * Gets the the content data for a specific revision,
	 * if the revisions doesn't exist returns FALSE.
	 * NOTE: doesn't yet return extras such as fact boxes
	 */
	function GetRevisionContent($article_id, $revision_id)
	{
        	$sql = 'SELECT	article_content_id,
				article_content_last_author_user_entity_id,
				article_content_last_author_timestamp,
				article_content_heading,
				article_content_subheading,
				article_content_subtext,
				article_content_wikitext,
				article_content_blurb,
				editor_user.business_card_name as editor_name
			FROM	article_contents

			JOIN	business_cards as editor_user
			ON	editor_user.business_card_user_entity_id = article_content_last_author_user_entity_id

			WHERE	article_content_id = ?
			AND	article_content_article_id = ?';
		$query = $this->db->query($sql, array($revision_id, $article_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return array(
				'id'=>$row->article_content_id,
				'lasteditid'=>$row->article_content_last_author_user_entity_id,
				'lasteditname'=>$row->editor_name,
				'updated'=>$row->article_content_last_author_timestamp,
				'heading'=>$row->article_content_heading,
				'subheading'=>$row->article_content_subheading,
				'subtext'=>$row->article_content_subtext,
				'wikitext'=>$row->article_content_wikitext,
				'blurb'=>$row->article_content_blurb
				);
		}
		else
			return FALSE;
	}

	/*****************************************************
	*  FACT BOXES
	*****************************************************/

	/**
	 * Inserts a new fact box into the database.
	 */
	function InsertFactBox($article_content_id, $title, $wikitext)
	{
		$wiki_cache = $this->wikiparser->parse($wikitext);
		$sql = 'INSERT INTO fact_boxes (
				fact_box_article_content_id,
				fact_box_title,
				fact_box_wikitext,
				fact_box_wikitext_cache,
				fact_box_timestamp)
			VALUES (?, ?, ?, CURRENT_TIMESTAMP)';
		$this->db->query($sql, array($article_content_id,$title,$wikitext));
	}

	/**
	 * Given a fact box id, it updates the fact box with the given data.
	 */
	function UpdateFactBox($id, $article_content_id, $title, $wikitext, $deleted)
	{
		$wiki_cache = $this->wikiparser->parse($wikitext);
		$sql = 'UPDATE fact_boxes
			SET fact_box_article_content_id = ?,
				fact_box_title = ?,
				fact_box_wikitext = ?,
				fact_box_deleted = ?
			WHERE fact_box_id = ?';
		$this->db->query($sql, array($article_content_id,$title,$wikitext,$deleted,$id));
	}

	/*****************************************************
	*  ARTICLE LINKS
	*****************************************************/

	/**
	 * Inserts a new article link into the database.
	 */
	function InsertArticleLink($article_id, $name, $url)
	{
		$sql = 'INSERT INTO article_links (
				article_link_article_id,
				article_link_name,
				article_link_url,
				article_link_timestamp)
			VALUES (?, ?, ?, CURRENT_TIMESTAMP)';
		$this->db->query($sql, array($article_id,$name,$url));
	}

	/**
	 * Given an article link id, it updates the article link with the given data.
	 */
	function UpdateArticleLink($id, $article_id, $name, $url, $deleted)
	{
		$sql = 'UPDATE article_links
			SET article_link_article_id = ?,
				article_link_name = ?,
				article_link_url = ?,
				article_link_deleted = ?
			WHERE article_link_id = ?';
		$this->db->query($sql, array($article_id,$name,$url,$deleted,$id));
	}

	/*****************************************************
	*  RELATED ARTICLES
	*****************************************************/

	/**
	 * Inserts a new related article into the database.
	 */
	function InsertRelatedArticle($article_id_1, $article_id_2)
	{
		if ($article_id_2 < $article_id_1)
		{
			$temp = $article_id_1;
			$article_id_1 = $article_id_2;
			$article_id_2 = $temp;
		}
		$sql = 'INSERT INTO related_articles (
				related_article_1_article_id,
				related_article_2_article_id)
			VALUES (?, ?)';
		$this->db->query($sql, array($article_id_1,$article_id_2));
	}

	/**
	 * Deletes the related article with the given id.
	 */
	function DeleteRelatedArticle($article_id_1, $article_id_2)
	{
		if ($article_id_2 < $article_id_1)
		{
			$temp = $article_id_1;
			$article_id_1 = $article_id_2;
			$article_id_2 = $temp;
		}
		$sql = 'DELETE FROM related_articles
			WHERE related_article_1_article_id = ? AND
				related_article_2_article_id = ?';
		$this->db->query($sql, array($article_id_1,$article_id_2));
	}

	/*****************************************************
	*  ARTICLE WRITERS
	*****************************************************/

	/**
	 * Inserts a new article writer into the database.
	 */
	function InsertArticleWriter($user_entity_id, $article_id)
	{
		$sql = 'INSERT INTO article_writers (
				article_writer_user_entity_id,
				article_writer_article_id)
			VALUES (?, ?)';
		$this->db->query($sql, array($user_entity_id,$article_id));
	}

	/**
	 * Deletes the article writer with the given id.
	 */
	function DeleteArticleWriter($user_entity_id, $article_id)
	{
		$sql = 'DELETE FROM article_writers
			WHERE article_writer_user_entity_id = ? AND
				article_writer_article_id = ?';
		$this->db->query($sql, array($user_entity_id,$article_id));
	}

	/*****************************************************
	*  ARTICLE EVENTS
	*****************************************************/

	/**
	 * Inserts a new article event into the database.
	 */
	function InsertArticleEvent($article_id, $event_id)
	{
		$sql = 'INSERT INTO article_events (
				article_event_article_id,
				article_event_event_id)
			VALUES (?, ?)';
		$this->db->query($sql, array($article_id,$event_id));
	}

	/**
	 * Deletes the article event with the given id.
	 */
	function DeleteArticleEvent($article_id, $event_id)
	{
		$sql = 'DELETE FROM article_events
			WHERE article_event_article_id = ? AND
				article_event_event_id = ?';
		$this->db->query($sql, array($article_id,$event_id));
	}

	/*****************************************************
	*  ARTICLE TAGS
	*****************************************************/

	/**
	 * Inserts a new article tag into the database.
	 */
	function InsertArticleTag($article_id, $tag_id)
	{
		$sql = 'INSERT INTO article_tags (
				article_tag_article_id,
				article_tag_tag_id)
			VALUES (?, ?)';
		$this->db->query($sql, array($article_id,$tag_id));
	}

	/**
	 * Deletes the article tag with the given id.
	 */
	function DeleteArticleTag($article_id, $tag_id)
	{
		$sql = 'DELETE FROM article_tags
			WHERE article_tag_article_id = ? AND
				article_tag_tag_id = ?';
		$this->db->query($sql, array($article_id,$tag_id));
	}

	/*****************************************************
	*  ARTICLE PHOTOS
	*****************************************************/

	/**
	 * Inserts a new article photo into the database.
	 */
	function InsertArticlePhoto($article_id, $photo_id, $number, $image_type)
	{
		$sql = 'INSERT INTO article_photos (
				article_photo_article_id,
				article_photo_photo_id,
				article_photo_number,
				article_photo_image_type)
			VALUES (?, ?, ?, ?)';
		$this->db->query($sql, array($article_id,$photo_id,$number,$image_type));
	}

	/**
	 * Given an article photo id, it updates the article photo with the given data.
	 */
	function UpdateArticlePhoto($id, $article_id, $photo_id, $number, $image_type)
	{
		$sql = 'UPDATE article_photos
			SET article_photo_article_id = ?,
				article_photo_photo_id = ?,
				article_photo_number = ?,
				article_photo_image_type = ?
			WHERE article_photo_id = ?';
		$this->db->query($sql, array($article_id,$photo_id,$number,$image_type,$id));
	}

        /**
	 * Deletes the article photo with the given id.
	 */
	function DeleteArticlePhoto($id)
	{
		$sql = 'DELETE FROM article_photos
			WHERE article_photo_id = ?';
		$this->db->query($sql, array($id));
	}

	function PullArticle($id, $editor_id)
	{
		$sql = 'UPDATE	articles
			SET	article_pulled = 1,
				article_editor_approved_user_entity_id = ?,
				article_publish_date = CURRENT_TIMESTAMP
			WHERE	(article_id = ?)';
		$query = $this->db->query($sql,array($editor_id, $id));
	}
}
?>
