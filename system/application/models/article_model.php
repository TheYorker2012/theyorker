<?php
/**
 * This model should add articles to the database. NOT yet complete.
 *
 * @author Alex Fargus (agf501)
 * 
 */
class Article_model extends Model
{
	function NewsModel()
	{
		//Call the Model Constructor
		parent::Model();
	}
	
	function CommitArticle($article_data)
	{
		$sql = 'START TRANSACTION;
				INSERT INTO articles (article.articles_content_type_id, articles.article_organisation_entity_id,
				articles.article_initial_editor_user_entity_id, articles.article_publish_date, articles.article_created)
				VALUES (?, ?, ?, ?, ?);
				INSERT INTO article_contents (article_contents.article_content_article_id, article_contents.article_content_heading,
				article_contents.article_content_subheading, article_contents.article_content_subtext,
				article_contents.article_content_wikitext, article_contents.article_content_blurb)
				VALUES (@article_id:=LAST_INSERT_ID(), ?, ?, ?, ?);
				UPDATE articles
				SET articles.article_live_content_id = LAST_INSERT_ID()
				WHERE (articles.article_id = @article_id);
				COMMIT;';
		//Query, bind, etc.			
	}
?>