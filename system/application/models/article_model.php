<?php
/**
 * This model retrieves data for the News pages.
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
	}
?>