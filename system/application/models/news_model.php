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
	*Returns the scheduled and live (published within the last 7 days) articles on the site.
	**/
	function getNewsArticlesSitemap()
	{
	$sql = 'SELECT
			articles.article_id as article_id,
			article_contents.article_content_last_author_timestamp as updated_date,
			content_types.content_type_codename as content_type_codename

			FROM articles

			INNER JOIN content_types
			ON articles.article_content_type_id = content_types.content_type_id
			AND (content_types.content_type_section = "news" OR content_types.content_type_section = "blogs")

			INNER JOIN article_contents
			  ON article_contents.article_content_id = articles.article_live_content_id
			 AND article_pulled = 0
			 AND article_deleted = 0
			 AND article_suggestion_accepted = 1

			WHERE DATE(articles.article_publish_date) <= CURRENT_DATE()

			ORDER BY articles.article_publish_date DESC';

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	*Returns the scheduled and live (published within the last 7 days) articles on the site.
	**/
	function getScheduledAndLive()
	{
	$sql = 'SELECT
			articles.article_id as article_id,
			articles.article_publish_date as publish_date,
			DATE(articles.article_publish_date) <= CURRENT_DATE() as is_live,
			article_contents.article_content_heading as headline,
			article_contents.article_content_last_author_timestamp as updated_date,

			GROUP_CONCAT(business_cards.business_card_name
				 ORDER BY business_cards.business_card_name
				 SEPARATOR ", <br />") as authors,

			IF (content_types.content_type_parent_content_type_id IS NOT NULL, CONCAT(ct_parent.content_type_name, " - ", content_types.content_type_name), content_types.content_type_name) as content_type_name

			FROM articles

			INNER JOIN content_types
			ON articles.article_content_type_id = content_types.content_type_id
			AND (content_types.content_type_section = "news" OR content_types.content_type_section = "blogs")

			LEFT JOIN content_types ct_parent
			ON ct_parent.content_type_id = content_types.content_type_parent_content_type_id

			INNER JOIN article_writers
			ON article_writers.article_writer_article_id = articles.article_id
			AND article_writers.article_writer_status = "accepted"
			AND article_writers.article_writer_editor_accepted_user_entity_id IS NOT NULL

			LEFT JOIN business_cards
			ON article_writers.article_writer_byline_business_card_id = business_cards.business_card_id

			INNER JOIN article_contents
			  ON article_contents.article_content_id = articles.article_live_content_id
			 AND article_pulled = 0
			 AND article_suggestion_accepted = 1
			 AND articles.article_deleted = 0

			WHERE DATE(articles.article_publish_date) > DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)

			GROUP BY articles.article_id

			ORDER BY articles.article_publish_date DESC';

		$query = $this->db->query($sql);
		return $query->result_array();
	}


	/**
	*Returns the scheduled articles that are not yet live.
	**/
	function getContentSchedule()
	{
	$sql = 'SELECT
			articles.article_id as article_id,
			DATE(articles.article_publish_date) as publish_date,
			DATE(articles.article_publish_date) <= CURRENT_DATE() as overdue,

			articles.article_request_title as headline,

			GROUP_CONCAT(DISTINCT CONCAT(users.user_firstname," ",users.user_surname)
				 ORDER BY users.user_surname
				 SEPARATOR ", <br />") as authors,

			CONCAT(editors.user_firstname," ",editors.user_surname) as editor,

			COUNT(article_writers.article_writer_status) != 0 as is_requested,
			IFNULL(MAX(article_writers.article_writer_status) = "accepted", 0) as is_accepted,

			IF (content_types.content_type_parent_content_type_id IS NOT NULL, CONCAT(ct_parent.content_type_name, " - ", content_types.content_type_name), content_types.content_type_name) as content_type_name

			FROM articles

			INNER JOIN content_types
			ON articles.article_content_type_id = content_types.content_type_id
			AND (content_types.content_type_section = "news" OR content_types.content_type_section = "blogs")

			LEFT JOIN content_types ct_parent
			ON ct_parent.content_type_id = content_types.content_type_parent_content_type_id

			LEFT JOIN (article_writers JOIN users ON users.user_entity_id = article_writers.article_writer_user_entity_id)
			ON article_writers.article_writer_article_id = articles.article_id

			INNER JOIN users AS editors ON editors.user_entity_id = articles.article_editor_approved_user_entity_id

			WHERE articles.article_live_content_id IS NULL AND articles.article_deleted = 0

			GROUP BY articles.article_id

			ORDER BY DATE(articles.article_publish_date), content_type_name';

		$query = $this->db->query($sql);
		return $query->result_array();
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
				 content_type_name AS name,
				 content_type_section AS section
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
	*@note use LEFT OUTER on last to joins to allow for children that dont have images.
	**/
	function getSubArticleTypes ($main_type)
	{
		$result = array();
		$sql = 'SELECT  child.content_type_id, child.content_type_codename, child.content_type_blurb,
		        	child.content_type_name, image_id, image_file_extension,
			        image_type_codename, image_title
			FROM    content_types AS parent
			INNER JOIN      content_types AS child
			ON      parent.content_type_id = child.content_type_parent_content_type_id
			LEFT OUTER JOIN      images
			ON      child.content_type_image_id = image_id
			LEFT OUTER JOIN      image_types
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
					'blurb' => $row->content_type_blurb,
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
	function GetLatestId($type, $number, $remove_featured=true)
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
				AND	articles.article_deleted = 0 ';
				if ($remove_featured){
				$sql .='AND articles.article_featured = 0 ';
				}
				$sql .='AND	articles.article_editor_approved_user_entity_id IS NOT NULL ';
		if (!empty($types)) {
			$sql .= '	AND (';
			$first = TRUE;
			foreach ($types as $type) {
				if (!$first) {
					$sql .= ' OR ';
				} else {
					$first = FALSE;
				}
				$sql .= 'content_types.content_type_codename = ?';
			}
			$sql .= ') ';
		}
		$sql .= 'ORDER BY articles.article_publish_date DESC
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
	
	//@input $type codename article_type
	//@return An Article ID of a featured article of that type, picked at random
	function GetLatestFeaturedId($type)
	//Returns one featured article of type '$type', picked at random
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
				AND	articles.article_deleted = 0 
				AND articles.article_featured = 1 
				AND	articles.article_editor_approved_user_entity_id IS NOT NULL ';
		
		if (!empty($types)) {
			$sql .= '	AND (';
			$first = TRUE;
			foreach ($types as $type) {
				if (!$first) {
					$sql .= ' OR ';
				} else {
					$first = FALSE;
				}
				$sql .= 'content_types.content_type_codename = ?';
			}
			$sql .= ') ';
		}
		$sql .= ' LIMIT 1';
		$query = $this->db->query($sql,$types);
		
		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			return $row->article_id;
		}
		return NULL;
	}
	/**
	 * Get array containing data needed for 'NewsOther'
	 * @param $id is the article_id of the article data to return
	 * @param $dateformat is an optional string containg the format you wish the dates to be returned
	 * @return An array with 'id','date','heading','subheading','subtext',
	 * @return 'authors','photo'
	 */
	function GetSimpleArticle($id, $image_class = "", $dateformat ='%a, %D %b %y')
	{
		$result['id'] = $id;
		$sql = 'SELECT		articles.article_live_content_id,
							DATE_FORMAT(articles.article_publish_date, ?) AS article_publish_date,
							content_types.content_type_codename,
							photo_requests.photo_request_chosen_photo_id,
							photo_requests.photo_request_title
				FROM		articles
				INNER JOIN	content_types
					ON		articles.article_id = ? AND articles.article_content_type_id = content_types.content_type_id
				LEFT JOIN	photo_requests
					ON		(articles.article_thumbnail_photo_id = photo_requests.photo_request_relative_photo_number
					AND		articles.article_id = photo_requests.photo_request_article_id
					AND		photo_requests.photo_request_deleted = 0
					AND		photo_requests.photo_request_chosen_photo_id IS NOT NULL
					AND		photo_requests.photo_request_approved_user_entity_id IS NOT NULL)
				LIMIT		0,1';
		$query = $this->db->query($sql, array($dateformat,$id));
		$content_id = null;
		if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['date'] = $row->article_publish_date;
			$result['article_type'] = $row->content_type_codename;
		    $content_id = $row->article_live_content_id;

			if ($row->photo_request_chosen_photo_id > 0) {
				$this->load->library('image');
				$result['photo_xhtml'] = $this->image->getThumb($row->photo_request_chosen_photo_id, 'small', false, array('class' => $image_class));
			} else {
				$result['photo_xhtml'] = '<img src="/images/prototype/news/small-default.jpg" alt="" class="'.$image_class.'" />';
			}
		}
		if ($content_id === NULL) return NULL;
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
				$sql = 'SELECT	business_cards.business_card_name,
								business_cards.business_card_id
						FROM	business_cards
						WHERE	(business_cards.business_card_user_entity_id = ?)';
				$author_query = $this->db->query($sql,array($row->article_writer_user_entity_id));
				if ($author_query->num_rows() > 0)
				{
					$author_row = $author_query->row();
					$authors[] = array(
						'name' => $author_row->business_card_name,
						'id' => $author_row->business_card_id
					);
				}
			}
			$result['authors'] = $authors;
		}
		return $result;
	}

	/**
	 * Get array containing data needed for 'NewsSummary'
	 * @param $id is the article_id of the article data to return
	 * @return An array with 'id','date','heading','subheading','subtext',
	 * @return 'authors','photo','blurb'
	 */
	function GetSummaryArticle($id, $image_class = "", $dateformat='%W, %D %M %Y', $pic_size='small', $request_primary_thumbnail=false)
	{
		$result['id'] = $id;

		$sql = 'SELECT		articles.article_live_content_id,
							DATE_FORMAT(articles.article_publish_date, ?) AS article_publish_date,
							articles.article_main_photo_id,
							content_types.content_type_codename,
							content_types.content_type_name,
							photo_requests.photo_request_chosen_photo_id,
							photo_requests.photo_request_title
				FROM		articles
				INNER JOIN	content_types
					ON		articles.article_id = ? AND articles.article_content_type_id = content_types.content_type_id
				LEFT JOIN	photo_requests
					ON		(articles.article_thumbnail_photo_id = photo_requests.photo_request_relative_photo_number
					AND		articles.article_id = photo_requests.photo_request_article_id
					AND		photo_requests.photo_request_deleted = 0
					AND		photo_requests.photo_request_chosen_photo_id IS NOT NULL
					AND		photo_requests.photo_request_approved_user_entity_id IS NOT NULL)
				LIMIT		0,1';
		$query = $this->db->query($sql, array($dateformat,$id));
		$content_id = null;
		if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['date'] = $row->article_publish_date;
			$result['article_type'] = $row->content_type_codename;
			$result['article_type_name'] = $row->content_type_name;
			$result['main_photo_id'] = $row->article_main_photo_id;
		    $content_id = $row->article_live_content_id;

			$this->load->library('image');
			if ($row->photo_request_chosen_photo_id > 0) {
				$result['photo_id'] = $row->photo_request_chosen_photo_id;
				$result['photo_xhtml'] = $this->image->getThumb($row->photo_request_chosen_photo_id, $pic_size, false, array('class' => $image_class));
			} else {
				$result['photo_xhtml'] = $this->image->getThumb(-1, $pic_size, false, array('class' => $image_class, 'alt' => 'Image not available', 'title' => 'Image not available'));
			}
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
				$sql = 'SELECT	business_cards.business_card_name,
								business_cards.business_card_id
						FROM	business_cards
						WHERE	(business_cards.business_card_user_entity_id = ?)';
				$author_query = $this->db->query($sql,array($row->article_writer_user_entity_id));
				if ($author_query->num_rows() > 0)
				{
					$author_row = $author_query->row();
					$authors[] = array(
						'name' => $author_row->business_card_name,
						'id' => $author_row->business_card_id
					);
				}
			}
			$result['authors'] = $authors;
		}

		if($request_primary_thumbnail) {
			$sql = 'SELECT	photo_requests.photo_request_chosen_photo_id	as photo_id,
							photo_requests.photo_request_title				as photo_title
					FROM	photo_requests
					WHERE	photo_requests.photo_request_article_id = ?
					AND		photo_requests.photo_request_deleted = 0
					AND		photo_requests.photo_request_chosen_photo_id IS NOT NULL
					AND		photo_requests.photo_request_approved_user_entity_id IS NOT NULL
					AND		photo_requests.photo_request_relative_photo_number = ?';
			$query = $this->db->query($sql, array($id,$result['main_photo_id']));
			$this->load->library('image');
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$result['photo_xhtml'] = $this->image->getThumb($row->photo_id, $pic_size, false, array('class' => $image_class));
			}
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
	function GetFullArticle($id, $image_class = "", $dateformat='%W, %D %M %Y', $preview = 0)
	{
		$result['id'] = $id;
		$sql = 'SELECT	articles.article_live_content_id,
						DATE_FORMAT(articles.article_publish_date, ?)	AS article_publish_date,
						articles.article_location_id,
						articles.article_main_photo_id,
						articles.article_public_comment_thread_id
				FROM	articles
				WHERE	articles.article_id = ?
				AND		articles.article_pulled = 0
				AND		articles.article_deleted = 0
				LIMIT	0,1';
		$query = $this->db->query($sql, array($dateformat,$id));
		if ($query->num_rows() == 0) return NULL;
		$row = $query->row();
		$result['date'] = $row->article_publish_date;
		$result['location'] = $row->article_location_id;
		$result['public_thread_id'] = $row->article_public_comment_thread_id;
		$result['main_photo'] = $row->article_main_photo_id;
		$content_id = $row->article_live_content_id;
		if ($preview) {
			$result['date'] = date('l, jS F Y');
			$content_id = $preview;
		}
		if ($content_id === NULL) return NULL;
		$sql = 'SELECT	article_contents.article_content_heading,
						article_contents.article_content_subheading,
						article_contents.article_content_subtext,
						article_contents.article_content_wikitext_cache,
						article_contents.article_content_blurb
				FROM	article_contents
				WHERE	article_contents.article_content_id = ?
				LIMIT	0,1';
		$query = $this->db->query($sql,array($content_id));
		$row = $query->row();
		$result['heading'] = $row->article_content_heading;
		$result['subheading'] = $row->article_content_subheading;
		$result['subtext'] = $row->article_content_subtext;
		$result['text'] = $row->article_content_wikitext_cache;
		$result['blurb'] = $row->article_content_blurb;

		$sql = 'SELECT article_writers.article_writer_byline_business_card_id,
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
				'id' => $row->article_writer_byline_business_card_id,
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

		$sql = 'SELECT		photo_requests.photo_request_chosen_photo_id	as photo_id,
							photo_requests.photo_request_view_large			as view_large,
							photo_requests.photo_request_title				as photo_caption,
							photo_requests.photo_request_description		as photo_alt
				FROM		photo_requests
				WHERE		photo_requests.photo_request_article_id = ?
				AND			photo_requests.photo_request_deleted = 0
				AND			photo_requests.photo_request_chosen_photo_id IS NOT NULL
				AND			photo_requests.photo_request_approved_user_entity_id IS NOT NULL
				AND			photo_requests.photo_request_relative_photo_number = ?';
		$query = $this->db->query($sql, array($id,$result['main_photo']));

		$this->load->library('image');
		if ($query->num_rows() == 1) {
			$row = $query->row();
			$result['primary_photo_link'] = $this->image->getPhotoURL($row->photo_id, 'medium');
			$result['primary_photo_xhtml'] = $this->image->getThumb($row->photo_id, 'medium', $row->view_large, array('class' => $image_class));
			$result['primary_photo_caption'] = $row->photo_caption;
		}

		$sql = 'SELECT	article_links.article_link_name,
						article_links.article_link_url,
						article_links.article_link_id
				FROM	article_links
				WHERE	(article_links.article_link_article_id = ?
				AND		article_links.article_link_deleted != 1)';
		$query = $this->db->query($sql,array($id));
		$links = array();
		foreach ($query->result() as $row)
		{
			$links[] = array('name'=>$row->article_link_name,
							'url'=>$row->article_link_url,
							'id'=>$row->article_link_id);
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
			$temp_related = $this->GetSimpleArticle($related_id, "Left");
			if ($temp_related !== NULL) {
				$related_articles[] = $temp_related;
			}
		}
		$result['related_articles'] = $related_articles;

		return $result;
	}

	/// Get information about the public comments thread.
	/**
	 * @param $ArticleId int ID of the article.
	 * @pre loaded(model comments_model)
	 * @return Same as comments_model::GetThreadByLinkTable
	 */
	function GetPublicThread($ArticleId)
	{
		return $this->comments_model->GetThreadByLinkTable(
			'articles','article_public_comment_thread_id',
			array('article_id' => $ArticleId)
		);
	}

	/**
	 *	@param	$type:string - 'count' = retrieve num of matching results, 'search' = retrieve chosen articles
	 */
	function GetArchive($type = 'search', $filters = array(), $limit = 0, $rows = 10)
	{
		// Process filters
		$extra_from = array();
		$extra_where = array();
		$extra_where_or = array();
		foreach ($filters as $filter) {
			switch ($filter[0]) {
				case 'reporter':
					if (is_numeric($filter[1])) {
						$extra_from[] = 'article_writers';
						$extra_where[] = 'article_writers.article_writer_article_id = articles.article_id';
						$extra_where[] = 'article_writers.article_writer_status = "accepted"';
						$extra_where_or['reporter'][] = 'article_writers.article_writer_byline_business_card_id = ' . $filter[1];
					}
					break;
				case 'section':
					if (is_numeric($filter[1])) {
						// Criteria is a content_type_id
						$extra_where_or['section'][] = 'content_types.content_type_id = ' . $filter[1];
						$extra_where_or['section'][] = 'content_types.content_type_parent_content_type_id = ' . $filter[1];
					} else {
						// Criteria is a content_type_codename
						$extra_where_or['section'][] = 'content_types.content_type_codename = "' . $filter[1] . '"';
					}
					break;
			}
		}
		$extra_from = array_unique($extra_from);
		$extra_where = array_unique($extra_where);

		$result = array();
		if ($type == 'count') {
			$sql = 'SELECT		COUNT(*)	AS	count'."\n";
		} else {
			$sql = 'SELECT		articles.article_id								AS id,
								UNIX_TIMESTAMP(articles.article_publish_date)	AS date,
								content_types.content_type_codename				AS type_codename,
								IF (content_types.content_type_parent_content_type_id IS NOT NULL, CONCAT(parent_type.content_type_name, " - ", content_types.content_type_name), content_types.content_type_name) AS type_name,
								article_contents.article_content_heading		AS heading,
								article_contents.article_content_blurb			AS blurb,
								photo_requests.photo_request_chosen_photo_id	AS photo_id,
								photo_requests.photo_request_title				AS photo_title'."\n";
		}
		$sql .= 'FROM		articles
				LEFT JOIN	photo_requests
					ON	(	articles.article_thumbnail_photo_id = photo_requests.photo_request_relative_photo_number
					AND		articles.article_id = photo_requests.photo_request_article_id
					AND		photo_requests.photo_request_deleted = 0
					AND		photo_requests.photo_request_chosen_photo_id IS NOT NULL
					AND		photo_requests.photo_request_approved_user_entity_id IS NOT NULL
						)	,
							article_contents,';
		foreach ($extra_from as $from) {
			$sql .= '		'.$from.',';
		}
		$sql .='			content_types
				LEFT JOIN	content_types AS parent_type
					ON		content_types.content_type_parent_content_type_id = parent_type.content_type_id
				WHERE		articles.article_content_type_id = content_types.content_type_id
				AND			articles.article_pulled = 0
				AND			articles.article_publish_date < CURRENT_TIMESTAMP()
				AND			articles.article_deleted = 0
				AND			articles.article_live_content_id IS NOT NULL
				AND			articles.article_live_content_id = article_contents.article_content_id
				AND			articles.article_id = article_contents.article_content_article_id
				AND			content_types.content_type_archive = 1';
		foreach ($extra_where as $where) {
			$sql .= '	AND		'.$where;
		}
		foreach ($extra_where_or as $where) {
			$sql .= ' AND (' . implode(' OR ', $where) . ')';
		}
		$sql .='	ORDER BY	articles.article_publish_date DESC';
		if ($type == 'search') {
			$sql .= "\n".'LIMIT		'. $limit . (($rows !== NULL) ? ','.$rows : '');
		}
		$query = $this->db->query($sql);
		if ($type == 'count') {
			$result = $query->row();
		} elseif ($query->num_rows() > 0) {
			foreach ($query->result_array() as $article) {
				$article['reporters'] = array();
				$sql = 'SELECT		article_writers.article_writer_byline_business_card_id,
									business_cards.business_card_name
						FROM		article_writers,
									business_cards
						WHERE		article_writers.article_writer_article_id = ?
						AND			article_writers.article_writer_status = "accepted"
						AND			article_writers.article_writer_editor_accepted_user_entity_id IS NOT NULL
						AND			business_cards.business_card_id = article_writers.article_writer_byline_business_card_id';
				$query = $this->db->query($sql,array($article['id']));
				foreach ($query->result() as $row) {
					$article['reporters'][] = array(
						'name'	=>	$row->business_card_name,
						'id'	=>	$row->article_writer_byline_business_card_id
					);
				}
				$result[] = $article;
			}
		}
		return $result;
	}

	/// Get information about the private comments thread.
	/**
	 * @param $ArticleId int ID of the article.
	 * @pre loaded(model comments_model)
	 * @return Same as comments_model::GetThreadByLinkTable
	 */
	function GetPrivateThread($ArticleId)
	{
		return $this->comments_model->GetThreadByLinkTable(
			'articles','article_private_comment_thread_id',
			array('article_id' => $ArticleId)
		);
	}
}
?>
