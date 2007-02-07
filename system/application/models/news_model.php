<?php
/**
 * This model retrieves data for the News pages.
 *
 * @author Alex Fargus (agf501)
 *
 */

//TODO - prevent errors if no data present -> DONE?
//		 comment correctly
//		 article_breaking?
//		 optimisation

class News_model extends Model
{

	/**
	 * Returns an array of the Article IDs that are of a specified type in
	 * decending order by publish date.
	 * @param $type is 'article_type_codename' of 'article_id' to return
	 * @param $number is the max number of 'article_id' to return
	 * @return An array of Article IDs in decending order by publish date.
	 */
	function GetLatestId($type, $number)
	//Returns the '$number' most recent article ID of type '$type'
	//Odered by 'most recent'.
	{
		$sql = 'SELECT articles.article_id FROM articles 
			LEFT JOIN content_types
			ON	(content_types.content_type_id = articles.article_content_type_id)
			WHERE	(content_types.content_type_codename = ?
			AND	articles.article_publish_date < CURRENT_TIMESTAMP)
			ORDER BY articles.article_publish_date DESC
			LIMIT 0, ?';
		$query = $this->db->query($sql,array($type,$number));
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
	 * @param $dateformat is an optional string containg the format you wish the dates to be returned
	 * @return An array with 'id','date','heading','subheading','subtext',
	 * @return 'authors','photo'
	 */
	function GetSimpleArticle($id, $dateformat ='%a, %D %b %y')
	{
		$result['id'] = $id;
		$sql = 'SELECT articles.article_live_content_id,
			DATE_FORMAT(articles.article_publish_date, ?) AS article_publish_date
			FROM articles
			WHERE (articles.article_id = ?)
			LIMIT 0,1';
		$query = $this->db->query($sql, array($dateformat,$id));
		if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['date'] = $row->article_publish_date;
		    $content_id = $row->article_live_content_id;
		}
		$sql = 'SELECT article_contents.article_content_heading
			FROM article_contents
			WHERE (article_contents.article_content_id = ?)
			LIMIT 0,1';
		$query = $this->db->query($sql, array($content_id));
        if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['heading'] = $row->article_content_heading;
		}
		$sql = 'SELECT article_writers.article_writer_user_entity_id
			FROM article_writers
			WHERE (article_writers.article_writer_article_content_id = ?)
			LIMIT 0,10';
		$query = $this->db->query($sql, array($content_id));
		if ($query->num_rows() > 0)
		{
		    $authors = array();
		    foreach ($query->result() as $row)
			{			
				$sql = 'SELECT business_cards.business_card_name
					FROM business_cards
					WHERE (business_cards.business_card_user_entity_id = ?)';
				$author_query = $this->db->query($sql,array($row->article_writer_user_entity_id));
				if ($author_query->num_rows() > 0)
				{
					$author_row = $author_query->row();
					$authors[] = array(
						'name' => $author_row->business_card_name,
						'id' => $row->article_writer_user_entity_id
					);
				}
			}
			$result['authors'] = $authors;
		}
		$sql = 'SELECT article_photos.article_photo_photo_id
			FROM article_photos
				WHERE (article_photos.article_photo_article_id = ?
				AND article_photos.article_photo_number = 0)
				LIMIT 0,10';
		$query = $this->db->query($sql, array($content_id));
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
	function GetSummaryArticle($id, $dateformat='%W, %D %M %Y')
	{
		$result['id'] = $id;
		$sql = 'SELECT articles.article_live_content_id, 
				DATE_FORMAT(articles.article_publish_date, ?) AS article_publish_date
			FROM articles
			WHERE (articles.article_id = ?)
			LIMIT 0,1';
		$query = $this->db->query($sql, array($dateformat,$id));
		if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['date'] = $row->article_publish_date;
		    $content_id = $row->article_live_content_id;
		}
		$sql = 'SELECT article_contents.article_content_heading,
			article_contents.article_content_blurb
			FROM article_contents
			WHERE (article_contents.article_content_id = ?)
			LIMIT 0,1';
		$query = $this->db->query($sql,array($content_id));
        if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['heading'] = $row->article_content_heading;
		    $result['blurb'] = $row->article_content_blurb;
		}
		$sql = 'SELECT article_writers.article_writer_user_entity_id
			FROM article_writers
			WHERE (article_writers.article_writer_article_content_id = ?)
			LIMIT 0,10';
		$query = $this->db->query($sql,array($content_id));
		if ($query->num_rows() > 0)
		{
		    $authors = array();
		    foreach ($query->result() as $row)
			{			
				$sql = 'SELECT business_cards.business_card_name
						FROM business_cards
						WHERE (business_cards.business_card_user_entity_id = ?)';
				$author_query = $this->db->query($sql,array($row->article_writer_user_entity_id));
				if ($author_query->num_rows() > 0)
				{
					$author_row = $author_query->row();
					$authors[] = array(
						'name' => $author_row->business_card_name,
						'id' => $row->article_writer_user_entity_id
					);
				}
			}
			$result['authors'] = $authors;
		}
		$sql = 'SELECT article_photos.article_photo_photo_id
				FROM article_photos
				WHERE (article_photos.article_photo_article_id = ?
				AND article_photos.article_photo_number = 0)
				LIMIT 0,10';
		$query = $this->db->query($sql,array($content_id));
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
	 * @return 'text','blurb','authors' (just ids atm),'fact_boxes','photos' (just ids atm)
	 * @return 'links', 'related_articles'
	 */
	function GetFullArticle($id, $dateformat='%W, %D %M %Y')
	{
		$result['id'] = $id;
		$sql = 'SELECT articles.article_live_content_id, 
				DATE_FORMAT(articles.article_publish_date, ?) AS article_publish_date,
				articles.article_location
			FROM articles
			WHERE (articles.article_id = ?)
			LIMIT 0,1';
		$query = $this->db->query($sql, array($dateformat,$id));
		$row = $query->row();
		$result['date'] = $row->article_publish_date;
		$result['location'] = $row->article_location;
		$content_id = $row->article_live_content_id;
		$sql = 'SELECT article_contents.article_content_heading, article_contents.article_content_subheading,
				article_contents.article_content_subtext, article_contents.article_content_wikitext_cache,
				article_contents.article_content_blurb
			FROM article_contents
			WHERE (article_contents.article_content_id = ?)
			LIMIT 0,1';
		$query = $this->db->query($sql,array($content_id));
		$row = $query->row();
		$result['heading'] = $row->article_content_heading;
		$result['subheading'] = $row->article_content_subheading;
		$result['subtext'] = $row->article_content_subtext;
		$result['text'] = $row->article_content_wikitext_cache;
		$result['blurb'] = $row->article_content_blurb;

		$sql = 'SELECT article_writers.article_writer_user_entity_id
			FROM article_writers
			WHERE (article_writers.article_writer_article_content_id = ?)
			LIMIT 0,10';
		$query = $this->db->query($sql,array($content_id));
	    $authors = array();
		
	    foreach ($query->result() as $row)
		{			
			$sql = 'SELECT business_cards.business_card_name
					FROM business_cards
					WHERE (business_cards.business_card_user_entity_id = ?)';
			$author_query = $this->db->query($sql,array($row->article_writer_user_entity_id));
			if ($author_query->num_rows() > 0)
			{
				$author_row = $author_query->row();
				$name = $author_row->business_card_name;
			}
			$sql = 'SELECT user_has_properties.user_has_properties_photo_id
					FROM user_has_properties
					WHERE (user_has_properties.user_has_properties_user_entity_id = ?)';
			$author_query = $this->db->query($sql,array($row->article_writer_user_entity_id));
			if ($author_query->num_rows() > 0)
			{
				$author_row = $author_query->row();
				$authors[] = array('photo'=>$author_row->user_has_properties_photo_id,'name'=>$name,'id'=>$row->article_writer_user_entity_id);
			}
		}
		$result['authors'] = $authors;

		$sql = 'SELECT fact_boxes.fact_box_wikitext_cache, fact_boxes.fact_box_title
			FROM fact_boxes
			WHERE (fact_boxes.fact_box_article_content_id = ?
			AND fact_boxes.fact_box_deleted != 1)
			LIMIT 0,10';
		$query = $this->db->query($sql,array($content_id));

		$fact_boxes = array();
		foreach ($query->result() as $row)
		{
			$fact_boxes[] = array('wikitext'=>$row->fact_box_wikitext_cache,'title'=>$row->fact_box_title);
		}
		$result['fact_boxes'] = $fact_boxes;

		$sql = 'SELECT article_photos.article_photo_photo_id
			FROM article_photos
			WHERE (article_photos.article_photo_article_id = ?)
			LIMIT 0,10';
		$query = $this->db->query($sql,array($content_id));
		$photos = array();
		foreach ($query->result() as $row)
		{
			$photos[] = $row->article_photo_photo_id;
		}
		$result['photos'] = $photos;
	
		$sql = 'SELECT	article_links.article_link_name, article_links.article_link_url
				FROM article_links
				WHERE (article_links.article_link_article_id = ? 
				AND article_links.article_link_deleted != 1)
				LIMIT 0,10';
		$query = $this->db->query($sql,array($id));
		$links = array();
		foreach ($query->result() as $row)
		{
			$links[] = array('name'=>$row->article_link_name,'url'=>$row->article_link_url);
		}
		$result['links'] = $links;
	
		$sql = 'SELECT	related_articles.related_article_1_article_id, related_articles.related_article_2_article_id
				FROM	related_articles
				WHERE	(related_article_1_article_id = ?
				OR	related_article_2_article_id = ?)';
		$query = $this->db->query($sql,array($id, $id));
		$articles = array();
		foreach ($query->result() as $row)
		{
			if ($row->related_article_1_article_id != $id)
				$articles[] = $row->related_article_1_article_id;
			if ($row->related_article_2_article_id != $id)
				$articles[] = $row->related_article_2_article_id;
		}
		$related_articles = array();
		foreach (array_values(array_unique($articles)) as $related_id)
		{
			$related_articles[] = self::GetSimpleArticle($related_id);
		}
		$result['related_articles'] = $related_articles;
		
		return $result;
	}

}
?>
