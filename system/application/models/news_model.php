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

	function News_Model()
	{
		parent::Model();
	}

	/**
	*Determines wheter the provided ID is of specified type.
	*@param $id The article_id to test.
	*@param $type The content_type_codename being tested.
	*@return boolean
	**/	
		
	function IdIsOfType($id,$type)
	{
		$sql = 'SELECT content_type_codename
			FROM content_types
			LEFT JOIN articles
			ON (article_content_type_id = content_type_id)
			WHERE article_id = ?
			AND content_type_codename = ?';
		$query = $this->db->query($sql,array($id,$type));
		if ($query->num_rows() == 1)
		{
			//If more than one is returned, something has gone wrong.
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	*Returns the content_type_codename of a content_type_id.
	*@param $type_id The content_type_id.
	*@return content_type_codename This corresponds to the content_type_id provided.
	**/
	function getArticleTypeCodename ($type_id)
	{
		$sql = 'SELECT content_type_codename
				FROM content_types
				WHERE content_type_id = ?';
		$query = $this->db->query($sql,array($type_id));
		return $query->row_array();
	}

	/**
	*Returns information about a particular content_type
	*@param $type This is a content_type_codename for the desired content_type.
	*@return array[codename(string),has_children(int),parent_id(content_type_id),name(string)]
	**/
	function getArticleTypeInformation ($type)
	{
		$sql = 'SELECT content_type_codename AS codename,
				 content_type_has_children AS has_children,
				 content_type_parent_content_type_id AS parent_id,
				 content_type_name AS name
				FROM content_types
				WHERE content_type_codename = ?';
		$query = $this->db->query($sql,array($type));
		$result = array();
		if ($query->num_rows() == 1) {
			$result = $query->row_array();
		}
		return $result;
	}

	/**
	*Returns infomation about all content subtypes of a particular content type
	*@param $main_type This is the content_type_codename that you want all subtypes of.
	*@return array[subtypes == array[id(content_type_id),codename(content_type_codename),
	*@return image(content_type_image_id),image_title(image_title),image_extension(image_file_extension),
	*@return image_codename(image_type_codename),name(content_type_name)]
	**/
	function getSubArticleTypes ($main_type)
	{
		$result = array();
		$sql = 'SELECT  child.content_type_id, child.content_type_codename,
		        	child.content_type_name, image_id, image_file_extension,
			        image_type_codename, image_title
			FROM    content_types AS parent
			INNER JOIN      content_types AS child
			ON      parent.content_type_id = child.content_type_parent_content_type_id
			INNER JOIN      images
			ON      child.content_type_image_id = image_id
			INNER JOIN      image_types
			ON      image_image_type_id = image_type_id
			WHERE   parent.content_type_codename = ?
			AND     parent.content_type_has_children = 1
			ORDER BY        child.content_type_section_order ASC';
		$query = $this->db->query($sql,array($main_type));
		if ($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$result[] = array(
					'id' => $row->content_type_id,
					'codename' => $row->content_type_codename,
					'name' => $row->content_type_name,
					'image' => $row->image_id,
					'image_title' => $row->image_title,
					'image_extension' => $row->image_file_extension,
					'image_codename' => $row->image_type_codename
				);
			}
		}
		return $result;
	}

	/**
	 * Returns an array of the Article IDs that are of a specified type in
	 * decending order by publish date.
	 * @param $type is 'article_type_codename' of 'article_id' to return
	 * @param $number is the max number of 'article_id' to return
	 * @return An array of Article IDs in decending order by publish date.
	 */
	function GetLatestId($type, $number)
	//Returns the '$number' most recent article ID of type '$type'
	//Ordered by 'most recent'.
	{
		$sql = 'SELECT content_type_id, content_type_has_children
				FROM content_types
				WHERE content_type_codename = ?';
		$query = $this->db->query($sql,array($type));
		$row = $query->row();
		if (($query->num_rows() > 0) && ($row->content_type_has_children)) {
			$sql = 'SELECT content_type_codename
					FROM content_types
					WHERE content_type_parent_content_type_id = ?';
			$query = $this->db->query($sql,array($row->content_type_id));
			$types = array();
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					$types[] = $row->content_type_codename;
				}
			}
		} else {
			$types = array($type);
		}

		$sql = 'SELECT articles.article_id FROM articles
				LEFT JOIN content_types
				ON (content_types.content_type_id = articles.article_content_type_id)
				WHERE articles.article_publish_date < CURRENT_TIMESTAMP
				AND articles.article_live_content_id IS NOT NULL
				AND	articles.article_editor_approved_user_entity_id IS NOT NULL
				AND (';
		for ($i = 1; $i <= count($types); $i++) {
			$sql .= 'content_types.content_type_codename = ? OR ';
		}
		$sql = substr($sql, 0, -4);
		$sql .= ')
				ORDER BY articles.article_publish_date DESC
				LIMIT 0, ?';
		$types[] = $number;
		$query = $this->db->query($sql,$types);
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
			DATE_FORMAT(articles.article_publish_date, ?) AS article_publish_date,
			content_types.content_type_codename
			FROM articles, content_types
			WHERE (articles.article_id = ?)
			AND articles.article_content_type_id = content_types.content_type_id
			LIMIT 0,1';
		$query = $this->db->query($sql, array($dateformat,$id));
		if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['date'] = $row->article_publish_date;
			$result['article_type'] = $row->content_type_codename;
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
			WHERE (article_writers.article_writer_article_id = ?
			AND article_writers.article_writer_status = "accepted"
			AND article_writer_editor_accepted_user_entity_id IS NOT NULL)
			
			LIMIT 0,10';
		$query = $this->db->query($sql, array($id));
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
		$sql = 'SELECT article_photos.article_photo_photo_id,
				 photos.photo_title
				FROM article_photos, photos
				WHERE article_photos.article_photo_article_id = ?
				AND article_photos.article_photo_photo_id = photos.photo_id
				AND article_photos.article_photo_number = 0';
		$query = $this->db->query($sql, array($id));
		$this->load->helper('images');
		if ($query->num_rows() == 1) {
			$row = $query->row();
			$result['photo_id'] = $row->article_photo_photo_id;
			$result['photo_url'] = imageLocation($row->article_photo_photo_id, 'small');
			$result['photo_title'] = $row->photo_title;
		} else {
			$result['photo_id'] = 0;
			$result['photo_url'] = '/images/prototype/news/small-default.jpg';
			$result['photo_title'] = 'The Yorker - News';
		}
		return $result;
	}

	/**
	 * Get array containing data needed for 'NewsSummary'
	 * @param $id is the article_id of the article data to return
	 * @return An array with 'id','date','heading','subheading','subtext',
	 * @return 'authors','photo','blurb'
	 */
	function GetSummaryArticle($id, $dateformat='%W, %D %M %Y', $pic_size='small')
	{
		$result['id'] = $id;
		$sql = 'SELECT articles.article_live_content_id,
				DATE_FORMAT(articles.article_publish_date, ?) AS article_publish_date,
				content_types.content_type_codename
			FROM articles, content_types
			WHERE (articles.article_id = ?)
			AND articles.article_content_type_id = content_types.content_type_id
			LIMIT 0,1';
		$query = $this->db->query($sql, array($dateformat,$id));
		if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['date'] = $row->article_publish_date;
			$result['article_type'] = $row->content_type_codename;
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
			WHERE (article_writers.article_writer_article_id = ?
			AND article_writers.article_writer_status = "accepted"
			AND article_writer_editor_accepted_user_entity_id IS NOT NULL)
			LIMIT 0,10';
		$query = $this->db->query($sql,array($id));
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
		$sql = 'SELECT article_photos.article_photo_photo_id,
				 photos.photo_title
				FROM article_photos, photos
				WHERE article_photos.article_photo_article_id = ?
				AND article_photos.article_photo_photo_id = photos.photo_id
				AND article_photos.article_photo_number = 0';
		$query = $this->db->query($sql, array($id));
		$this->load->helper('images');
		if ($query->num_rows() == 1) {
			$row = $query->row();
			$result['photo_id'] = $row->article_photo_photo_id;
			$result['photo_url'] = imageLocation($row->article_photo_photo_id, $pic_size);
			$result['photo_title'] = $row->photo_title;
		} else {
			$result['photo_id'] = 0;
			$result['photo_url'] = '/images/prototype/news/small-default.jpg';
			$result['photo_title'] = 'The Yorker - News';
		}
		return $result;
	}

	/**
	 * Get array containing all data needed to display a full news article.
	 * -Currently does not return related articles and only returns photo_id-
	 * @param $id This is the article_id of the article data to return.
	 * @return An array with 'id','date','heading','subheading','subtext',
	 * @return 'text','blurb','authors' (just ids atm),'fact_boxes','photos' (just ids atm)
	 * @return 'links', 'related_articles'
	 */
	function GetFullArticle($id, $dateformat='%W, %D %M %Y', $preview = 0)
	{
		$result['id'] = $id;
		if ($preview == 0) {
			$sql = 'SELECT articles.article_live_content_id,
					DATE_FORMAT(articles.article_publish_date, ?) AS article_publish_date,
					articles.article_location_id
				FROM articles
				WHERE (articles.article_id = ?)
				LIMIT 0,1';
			$query = $this->db->query($sql, array($dateformat,$id));
			if ( $query->num_rows() == 0 ) return NULL;
			$row = $query->row();
			$result['date'] = $row->article_publish_date;
			$result['location'] = $row->article_location_id;
			$content_id = $row->article_live_content_id;
		} else {
			$result['date'] = date('l, jS F Y');
			$result['location'] = 0;
			$content_id = $preview;
		}
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

		$sql = 'SELECT article_writers.article_writer_user_entity_id,
				business_cards.business_card_name
			FROM article_writers, business_cards
			WHERE article_writers.article_writer_article_id = ?
			AND article_writers.article_writer_status = "accepted"
			AND article_writers.article_writer_editor_accepted_user_entity_id IS NOT NULL
			AND article_writers.article_writer_user_entity_id = business_cards.business_card_user_entity_id
			LIMIT 0,10';
		$query = $this->db->query($sql,array($id));
	    $authors = array();
	    foreach ($query->result() as $row)
		{
			$authors[] = array(
				'id' => $row->article_writer_user_entity_id,
				'name' => $row->business_card_name
			);
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

		$sql = 'SELECT article_photos.article_photo_photo_id,
				 article_photos.article_photo_number
			FROM article_photos
			WHERE (article_photos.article_photo_article_id = ?)
			LIMIT 0,10';
		$query = $this->db->query($sql,array($id));
		$this->load->helper('images');
		$photos = array();
		foreach ($query->result() as $row)
		{
			$photos[$row->article_photo_number] = imageLocation($row->article_photo_photo_id, 'medium');
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
