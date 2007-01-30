<?php
/**
 * This model should add articles to the database. NOT yet complete.
 *
 * @author Alex Fargus (agf501)
 * 
 */
class Article_model extends Model
{
	function ArticleModel()
	{
		//Call the Model Constructor
		parent::Model();
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
			article_contents.article_content_blurb)
			VALUES (@article_id:=LAST_INSERT_ID(), ?, ?, ?, ?, ?)';
	$this->db->query($sql,array($heading, $subheading, $subtext, $wikitext, $blurb));
	$sql = 'UPDATE articles
			SET articles.article_live_content_id = LAST_INSERT_ID()
			WHERE (articles.article_id = @article_id)';
	$this->db->query($sql);	
	$this->db->trans_complete();
	}
}
?>