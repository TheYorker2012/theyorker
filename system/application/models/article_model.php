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
		//Call the Model Constructor
		parent::Model();
		$this->load->library('wikiparser');
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
	
	function GetArticleHeader($article_id)
	{
        	$sql = 'SELECT article_content_type_id,
				article_organisation_entity_id,
				article_created,
				article_publish_date,
				article_location,
				article_live_content_id
			FROM articles
			WHERE article_id = ?';
		$query = $this->db->query($sql, array($article_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return array(
				'content_type'=>$row->article_content_type_id,
				'organisation'=>$row->article_organisation_entity_id,
				'created'=>$row->article_created,
				'publish_date'=>$row->article_publish_date,
				'location'=>$row->article_location,
				'live_content'=>$row->article_live_content_id
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
	function InsertArticleWriter($user_entity_id, $article_content_id)
	{
		$sql = 'INSERT INTO article_writers (
				article_writer_user_entity_id,
				article_writer_article_content_id)
			VALUES (?, ?)';
		$this->db->query($sql, array($user_entity_id,$article_content_id));
	}

	/**
	 * Deletes the article writer with the given id.
	 */
	function DeleteArticleWriter($user_entity_id, $article_content_id)
	{
		$sql = 'DELETE FROM article_writers
			WHERE article_writer_user_entity_id = ? AND
				article_writer_article_content_id = ?';
		$this->db->query($sql, array($user_entity_id,$article_content_id));
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
}
?>
