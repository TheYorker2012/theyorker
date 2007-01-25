<?php
/**
 * This model retrieves data for the News pages.
 *
 * @author Alex Fargus (agf501)
 * 
 */
 
//TODO - prevent erros if no data present
//		 convert to use bind
//		 article_beaking?
 
class News_model extends Model
{
	function NewsModel()
	{
		//Call the Model Constructor
		parent::Model();
	}
	
	/**
	 * Returns an array of the Article IDs that are of a specified type in 
	 * decending order by publish date.
	 * @param $type is 'article_type_id' of 'article_id' to return
	 * @param $number is the max number of 'article_id' to return
	 * @return An array of Article IDs in decending order by publish date.
	 */
	function GetLatestId($type, $number)
	//Returns the '$number' most recent article ID of type '$type'
	//Odered by 'most recent'.
	{
		$sql = 'SELECT articles.article_id FROM articles
				WHERE (articles.article_content_type_id ='.$type.'
				AND	articles.article_publish_date < CURRENT_TIMESTAMP)
				ORDER BY articles.article_publish_date DESC 
				LIMIT 0,'.$number;
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = $row->article_id;
			}
		}
		return $result;		
	}
	
	/**
	 * Get array containing data needed for 'NewsOther'
	 * @param $id is the article_id of the article data to return
	 * @return An array with 'id','date','heading','subheading','subtext',
	 * @return 'authors','photo'
	 */
	function GetSimpleArticle($id)
	{
		$result['id'] = $id;
		$sql = 'SELECT article_contents.article_content_id, articles.article_publish_date
				FROM articles
				LEFT JOIN article_contents
				ON articles.article_id = 
					article_contents.article_content_article_id
				WHERE (articles.article_id = '.$id.')
				LIMIT 0,1';
		$query = $this->db->query($sql);		
		if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['date'] = $row->article_publish_date;
		    $content_id = $row->article_content_id;
		}
		$sql = 'SELECT article_contents.article_content_heading
				FROM article_contents
				WHERE (article_contents.article_content_id = '.$content_id.')
				LIMIT 0,1';
		$query = $this->db->query($sql);		
        if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['heading'] = $row->article_content_heading;
		}
		$sql = 'SELECT article_writers.article_writer_user_entity_id
				FROM article_writers
				WHERE (article_writers.article_writer_article_content_id = '.$content_id.')
				LIMIT 0,10';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{		
		    $authors = array();
		    foreach ($query->result() as $row)
			{
				$authors[] = $row->article_writer_user_entity_id;
			}
			$result['authors'] = $authors;
		}
		$sql = 'SELECT article_photos.article_photo_photo_id
				FROM article_photos
				WHERE (article_photos.article_photo_article_id = '.$content_id.'
				AND article_photos.article_photo_number = 0)
				LIMIT 0,10';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			$row = $query->result();
			$result['photos'] = $row->article_photo_photo_id;
		}
		return $result;	
	}

	/**
	 * Get array containing data needed for 'NewsSummary'
	 * @param $id is the article_id of the article data to return
	 * @return An array with 'id','date','heading','subheading','subtext',
	 * @return 'authors','photo','blurb'
	 */	
	function GetSummaryArticle($id)
	{
		$result['id'] = $id;
		$sql = 'SELECT article_contents.article_content_id, articles.article_publish_date
				FROM articles
				LEFT JOIN article_contents
				ON articles.article_id = 
					article_contents.article_content_article_id
				WHERE (articles.article_id = '.$id.')
				LIMIT 0,1';
		$query = $this->db->query($sql);		
		if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['date'] = $row->article_publish_date;
		    $content_id = $row->article_content_id;
		}
		$sql = 'SELECT article_contents.article_content_heading,
				article_contents.article_content_blurb
				FROM article_contents
				WHERE (article_contents.article_content_id = '.$content_id.')
				LIMIT 0,1';
		$query = $this->db->query($sql);		
        if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['heading'] = $row->article_content_heading;
		    $result['blurb'] = $row->article_content_blurb;
		}
		$sql = 'SELECT article_writers.article_writer_user_entity_id
				FROM article_writers
				WHERE (article_writers.article_writer_article_content_id = '.$content_id.')
				LIMIT 0,10';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{		
		    $authors = array();
		    foreach ($query->result() as $row)
			{
				$authors[] = $row->article_writer_user_entity_id;
			}
			$result['authors'] = $authors;
		}
		$sql = 'SELECT article_photos.article_photo_photo_id
				FROM article_photos
				WHERE (article_photos.article_photo_article_id = '.$content_id.'
				AND article_photos.article_photo_number = 0)
				LIMIT 0,10';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			$row = $query->result();
			$result['photos'] = $row->article_photo_photo_id;
		}
		return $result;	
	}
	
	/**
	 * Get array containing all data needed to display a full news article.
	 * -Currently does not return related articles and only returns photo_id-
	 * @param $id is the article_id of the article data to return
	 * @return An array with 'id','date','heading','subheading','subtext',
	 * @return 'text','blurb','authors','fact_boxes','pull_quotes','photos'
	 */
	function GetFullArticle($id)
	{
		$result['id'] = $id;
		$sql = 'SELECT article_contents.article_content_id, articles.article_publish_date
				FROM articles
				LEFT JOIN article_contents
				ON articles.article_id = 
					article_contents.article_content_article_id
				WHERE (articles.article_id = '.$id.')
				LIMIT 0,1';
		$query = $this->db->query($sql);		
		$row = $query->row();
		$result['date'] = $row->article_publish_date;
		$content_id = $row->article_content_id;
		$sql = 'SELECT article_contents.article_content_heading, article_contents.article_content_subheading,
				article_contents.article_content_subtext, article_contents.article_content_wikitext,
				article_contents.article_content_blurb
				FROM article_contents
				WHERE (article_contents.article_content_id = '.$content_id.')
				LIMIT 0,1';
		$query = $this->db->query($sql);		
		$row = $query->row();
		$result['heading'] = $row->article_content_heading;
		$result['subheading'] = $row->article_content_subheading;
		$result['subtext'] = $row->article_content_subtext;
		$result['text'] = $row->article_content_wikitext;
		$result['blurb'] = $row->article_content_blurb;

		$sql = 'SELECT article_writers.article_writer_user_entity_id
				FROM article_writers
				WHERE (article_writers.article_writer_article_content_id = '.$content_id.')
				LIMIT 0,10';
		$query = $this->db->query($sql);		
		$authors = array();
		foreach ($query->result() as $row)
		{
			$authors[] = $row->article_writer_user_entity_id;
		}
		$result['authors'] = $authors;

		$sql = 'SELECT fact_boxes.fact_box_wikitext
				FROM fact_boxes
				WHERE (fact_boxes.fact_box_article_content_id = '.$content_id.'
				AND fact_boxes.fact_box_deleted != 1)
				LIMIT 0,10';
		$query = $this->db->query($sql);		

		$fact_boxes = array();
		foreach ($query->result() as $row)
		{
			$fact_boxes[] = $row->fact_box_wikitext;
		}
		$result['fact_boxes'] = $fact_boxes;
		
		$sql = 'SELECT pull_quotes.pull_quote_text, pull_quotes.pull_quote_person,
				pull_quotes.pull_quote_position, pull_quotes.pull_quote_order
				FROM pull_quotes
				WHERE (pull_quotes.pull_quote_article_content_id = '.$content_id.'
				AND pull_quotes.pull_quote_deleted != 1)
				LIMIT 0,10';
		$query = $this->db->query($sql);		

		$pull_quotes = array();
		$quote = array();
		foreach ($query->result() as $row)
		{
			$quote['text'] = $row->pull_quote_text;
			$quote['person'] = $row->pull_quote_person;
			$quote['position'] = $row->pull_quote_position;
			$quote['order'] = $row->pull_quote_order;
			$pull_quotes[] = $quote;
		}
		$result['pull_quotes'] = $pull_quotes;
		
		$sql = 'SELECT article_photos.article_photo_photo_id
				FROM article_photos
				WHERE (article_photos.article_photo_article_id = '.$content_id.')
				LIMIT 0,10';
		$query = $this->db->query($sql);		
		$photos = array();
		foreach ($query->result() as $row)
		{
			$photos[] = $row->article_photo_photo_id;
		}
		$result['photos'] = $photos;
		return $result;		
		//Still to do - retrieve related articles.
	}	

}
?>