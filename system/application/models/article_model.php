<?php
/**
 * This model should add articles to the database. NOT yet complete.
 *
 * @author Alex Fargus (agf501)
 * @author Richard Ingle (ri504)
 *@author Owen Jones (oj502) -- The article_types stuff
 *
 */
class Article_model extends Model
{

	function __construct()
	{
		// Call the Model Constructor
		parent::Model();
		$this->load->library('wikiparser');
	}

	/// Retrieves all the information for a reporter's byline
	function GetReporterByline($user_id)
	{
		$sql = 'SELECT business_cards.business_card_name,
				 business_cards.business_card_image_id
				FROM business_cards
				WHERE business_card_user_entity_id = ?
				AND business_cards.business_card_deleted = 0';
		$query = $this->db->query($sql, array($user_id));
		$result = array();
		if ($query->num_rows() == 1) {
			$row = $query->row();
			$result['name'] = $row->business_card_name;
			$result['photo'] = $row->business_card_image_id;
		}
		return $result;
	}

	// Returns names from Business Card
	function GetBusinessCardName($user_id)
	{
		$sql = 'SELECT business_card_name
				FROM business_cards
				WHERE business_card_user_entity_id = ?';
		$query = $this->db->query($sql, array($user_id));
		if ($query->num_rows() == 1) {
			$row = $query->row();
			return $row->business_card_name;
		} else {
			return '';
		}
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
				 articles.article_thumbnail_photo_id AS photo_thumbnail,
				 articles.article_main_photo_id AS photo_main,
				 content_types.content_type_name AS box_name,
				 content_types.content_type_codename AS box_codename
				FROM articles, content_types
				WHERE articles.article_id = ?
				AND articles.article_suggestion_accepted = 1
				AND articles.article_deleted = 0
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
		$sql = 'SELECT article_contents.article_content_id AS id,
				 article_contents.article_content_last_author_user_entity_id AS last_author,
				 UNIX_TIMESTAMP(article_contents.article_content_last_author_timestamp) AS last_edit,
				 article_contents.article_content_heading AS headline,
				 article_contents.article_content_subheading AS subheadline,
				 article_contents.article_content_subtext AS subtext,
				 article_contents.article_content_wikitext AS text,
				 article_contents.article_content_blurb AS blurb
				FROM article_contents
				WHERE article_contents.article_content_id = ?';
		$query = $this->db->query($sql, array($revision_id));
		$result = $query->row_array();
		$sql = 'SELECT fact_box_id AS id,
				 fact_box_title AS title,
				 fact_box_wikitext AS text
				FROM fact_boxes
				WHERE fact_box_article_content_id = ?
				AND fact_box_deleted = 0';
		$query = $this->db->query($sql, array($revision_id));
		if ($query->num_rows() == 1) {
			$result['fact_box'] = $query->row_array();
		}
		return $result;
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
		$sql = 'SELECT article_contents.article_content_id
				FROM article_contents
				INNER JOIN articles
				ON articles.article_id = ? AND article_contents.article_content_article_id = articles.article_id
				WHERE (articles.article_live_content_id IS NULL OR articles.article_live_content_id != article_contents.article_content_id)
				AND article_contents.article_content_last_author_user_entity_id = ?
				AND (DATE_ADD(article_contents.article_content_last_author_timestamp, INTERVAL 1 HOUR) > CURRENT_TIMESTAMP) ';
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
						article_location_id,
						article_live_content_id,
						article_suggestion_accepted,
						article_pulled,
						article_request_title,
						article_request_description
				FROM	articles
				WHERE	article_id = ?
				AND		article_deleted = FALSE';
		$query = $this->db->query($sql, array($article_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			$returndata = array(
				'content_type'=>$row->article_content_type_id,
				'organisation'=>$row->article_organisation_entity_id,
				'created'=>$row->article_created,
				'publish_date'=>$row->article_publish_date,
				'location'=>$row->article_location_id,
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
				user_firstname,
				user_surname
			FROM	article_contents

			JOIN	users
			ON      user_entity_id = article_content_last_author_user_entity_id

			WHERE	article_content_id = ?
			AND	article_content_article_id = ?';
		$query = $this->db->query($sql, array($revision_id, $article_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return array(
				'id'=>$row->article_content_id,
				'lasteditid'=>$row->article_content_last_author_user_entity_id,
				'lasteditname'=>$row->user_firstname.' '.$row->user_surname,
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
	function InsertFactBox($article_content_id, $title = "", $wikitext = "")
	{
		$wiki_cache = $this->wikiparser->parse($wikitext);
		$sql = 'INSERT INTO fact_boxes (
				fact_box_article_content_id,
				fact_box_title,
				fact_box_wikitext,
				fact_box_wikitext_cache,
				fact_box_timestamp)
			VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)';
		$this->db->query($sql, array($article_content_id,$title,$wikitext,$wiki_cache));
		return $this->db->insert_id();
	}

	/**
	 * Given an article content id, it updates the fact box contents for that revision (if it exists)
	 */
	function UpdateRevisionFactBox ($revision, $title, $text)
	{
		$wiki_cache = $this->wikiparser->parse($text);
		$sql = 'UPDATE fact_boxes
				SET fact_box_title = ?,
				 fact_box_wikitext = ?,
				 fact_box_wikitext_cache = ?
				WHERE fact_box_article_content_id = ?
				AND fact_box_deleted = 0';
		$this->db->query($sql, array($title,$text,$wiki_cache,$revision));
	}

	/**
	 * Given an article content id, it deletes the corresponding fact box
	 */
	function DeleteRevisionFactBox ($revision)
	{
		$sql = 'UPDATE fact_boxes
				SET fact_box_deleted = 1
				WHERE fact_box_article_content_id = ?';
		$this->db->query($sql, array($revision));
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


	function PullArticle($id, $editor_id)
	{
		$sql = 'UPDATE	articles
			SET	article_pulled = 1,
				article_editor_approved_user_entity_id = ?,
				article_publish_date = CURRENT_TIMESTAMP
			WHERE	(article_id = ?)';
		$query = $this->db->query($sql,array($editor_id, $id));
	}
	
	/*********************************************************
	*ARTICLE TYPES
	*********************************************************/
	
	/**
	*Returns infomation about all main types of article.
	*@return array[types == array[id(content_type_id),codename(content_type_codename),
	*@return image(content_type_image_id),image_title(image_title),image_extension(image_file_extension),
	*@return image_codename(image_type_codename),name(content_type_name)]
	*@note use LEFT OUTER on last to joins to allow for children that dont have images.
	**/
	function getMainArticleTypes ($allow_with_no_children=false)
	{
		$result = array();
		$sql = 'SELECT  content_types.content_type_id, content_types.content_type_codename, content_types.content_type_blurb, 
						content_types.content_type_name, image_id, image_file_extension, image_type_codename,image_title			
			FROM    content_types 
			LEFT OUTER JOIN      images
			ON      content_types.content_type_image_id = image_id
			LEFT OUTER JOIN      image_types
			ON      image_image_type_id = image_type_id
			WHERE  content_types.content_type_parent_content_type_id IS NULL ';
			if($allow_with_no_children==false){
				$sql .= 'AND content_types.content_type_has_children=1 ';
			}
			$sql .= 'ORDER BY content_types.content_type_name ASC';
		$query = $this->db->query($sql);
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
	//Returns true if the codename given is a valid content_type
	function doesCodenameExist($codename){
	$sql= "SELECT content_types.content_type_id FROM content_types WHERE content_type_codename=? LIMIT 1";
	$query = $this->db->query($sql,array($codename));
	return ($query->num_rows() > 0);
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
				WHERE content_type_id = ? 
				LIMIT 1';
		$query = $this->db->query($sql,array($type_id));
		$row = $query->row();
		return $row->content_type_codename;
	}
	
	//Returns true if the article_type is a parent
	function isArticleTypeAParent($id){
	$sql= "SELECT content_types.content_type_has_children FROM content_types WHERE content_type_id=? LIMIT 1";
	$query = $this->db->query($sql,array($id));
		if ($query->num_rows() > 0){
			$row = $query->row();
			return ($row->content_type_has_children);
		}else{
			return false;
		}
	}
	
	/**
	*Returns infomation about all content subtypes.
	*@return array[subtypes == array[id(content_type_id),codename(content_type_codename),parent_name,parent_codename,
	*@return image(content_type_image_id),image_title(image_title),image_extension(image_file_extension),
	*@return image_codename(image_type_codename),name(content_type_name)]
	*@note use LEFT OUTER on last to joins to allow for children that dont have images.
	**/
	function getAllSubArticleTypes ()
	{
		$result = array();
		$sql = 'SELECT  child.content_type_id,
						child.content_type_codename,
						child.content_type_blurb,
						child.content_type_name,
						child.content_type_archive,
						parent.content_type_name AS parent_name,
						parent.content_type_codename AS parent_codename,
						image_id, image_file_extension,
						image_type_codename, image_title
			FROM    content_types AS parent
			INNER JOIN      content_types AS child
			ON      parent.content_type_id = child.content_type_parent_content_type_id
			LEFT OUTER JOIN      images
			ON      child.content_type_image_id = image_id
			LEFT OUTER JOIN      image_types
			ON      image_image_type_id = image_type_id
			WHERE     parent.content_type_has_children = 1
			ORDER BY        parent.content_type_name ASC,  child.content_type_section_order ASC';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$result[] = array(
					'id' => $row->content_type_id,
					'codename' => $row->content_type_codename,
					'name' => $row->content_type_name,
					'parent_name' => $row->parent_name,
					'parent_codename' => $row->parent_codename,
					'in_archive' => $row->content_type_archive,
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
	//Creates a new content type
	//@param codename - string to use as codename a-z A-Z only
	//@param $name - string name
	//@param $parent_id - id of parent content_type
	//@param $image_id - id of related image, should be a puffer
	//@param $archive (0,1) if articlesubtype is shown in the archive
	//@param $blurb - String, description
	//NOTE Make sure the parent exists, and thecodename is not already taken!
	function insertArticleSubType($codename,$name,$parent_id,$image_id,$archive,$blurb)
	{
		//Find order position to give
		$sql = 'SELECT	MAX(content_type_section_order) as max_section_order
			FROM	content_types
			WHERE	content_type_parent_content_type_id = ?';
		$query = $this->db->query($sql,array($parent_id));
		$row = $query->row();
		$order = $row->max_section_order + 1;
		//Get org id of parent Should i be doing this???
		$sql = 'SELECT  content_types.content_type_related_organisation_entity_id 
			FROM    content_types 
			WHERE  content_types.content_type_id = ?  
			LIMIT 1';
		$query = $this->db->query($sql,array($parent_id));
		$row = $query->row();
		$org_id = $row->content_type_related_organisation_entity_id;
		//Insert new type
		$sql = 'INSERT INTO content_types (
				content_type_codename, 
				content_type_related_organisation_entity_id, 
				content_type_parent_content_type_id, 
				content_type_image_id, 
				content_type_name, 
				content_type_archive, 
				content_type_blurb, 
				content_type_has_reviews, 
				content_type_has_children, 
				content_type_section, 
				content_type_section_order 
				)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
		$this->db->query($sql, array($codename,$org_id,$parent_id,$image_id,$name,$archive,$blurb,0,0,'news',$order));
	}
	//Updates an existing content type
	//@param $id - id of content type to change
	//@param codename - string to use as codename a-z A-Z only
	//@param $name - string name
	//@param $parent_id - id of parent content_type
	//@param $archive (0,1) if articlesubtype is shown in the archive
	//@param $blurb - String, description
	function updateArticleSubType($id,$codename,$name,$parent_id,$archive,$blurb)
	{
		//Update type
		$sql = 'UPDATE content_types SET 
		content_types.content_type_codename = ?, 
		content_types.content_type_name = ?, 
		content_types.content_type_parent_content_type_id = ?, 
		content_types.content_type_archive = ?, 
		content_types.content_type_blurb = ? 
		WHERE content_types.content_type_id = ? 
		LIMIT 1';
		$this->db->query($sql, array($codename,$name,$parent_id,$archive,$blurb,$id));
	}
	
	function updateArticleSubTypeImage($id,$image_id)
	{
		//Update type
		$sql = 'UPDATE content_types SET 
		content_types.content_type_image_id = ? 
		WHERE content_types.content_type_id = ? 
		LIMIT 1';
		$this->db->query($sql, array($image_id,$id));
	}
	
	//check for articles by this subtype first!
	function deleteSubArticleType($id, $parent_id)
	{
		$sql = 'SELECT  content_types.content_type_has_children, content_type_section 
			FROM    content_types 
			WHERE  content_types.content_type_id = ?  
			LIMIT 1';
		$query = $this->db->query($sql,array($id));
		$row = $query->row();
		if($row->content_type_has_children == 1 || $row->content_type_section=='hardcoded'){
			//must not delete!!
			return false;
		}else{
			$sql = 'DELETE FROM content_types 
				WHERE  content_types.content_type_id = ?  
				LIMIT 1';
			$query = $this->db->query($sql,array($id));
			return true;
		}
	}
		/**
	*Returns information about a particular content_type
	*@param $type_id This is a content_type_id for the desired content_type.
	**/
	function getSubArticleType ($type)
	{
		$sql = 'SELECT 
				 content_types.content_type_codename AS codename,
				 content_types.content_type_has_children AS has_children,
				 content_types.content_type_parent_content_type_id AS parent_id,
				 content_types.content_type_name AS name,
				 content_types.content_type_section AS section,
				 content_types.content_type_archive AS archive,
				 content_types.content_type_blurb AS blurb,
				 content_types.content_type_section_order AS section_order ,
				 content_types.content_type_image_id AS image_id,
				 images.image_title AS image_title, 
				 image_types.image_type_codename AS image_type_codename 
				FROM content_types 
				LEFT OUTER JOIN      images
				ON      content_types.content_type_image_id = images.image_id 
				LEFT OUTER JOIN      image_types
				ON      images.image_image_type_id = image_types.image_type_id
				WHERE content_type_id = ?';
		$query = $this->db->query($sql,array($type));
		$result = array();
		if ($query->num_rows() == 1) {
			$result = $query->row_array();
		}
		return $result;
	}
	
	//Checks to see if $parent_type has a content_type of a certain position returns true if order position exists.
	//Use this to check if a swap is valid
	function DoesOrderPositionExist($parent_type, $order_number)
	{
		$sql = 'SELECT content_type_codename FROM content_types  
				WHERE content_type_parent_content_type_id = ? AND content_type_section_order=? LIMIT 1';
		$query = $this->db->query($sql,array($parent_type, $order_number));
		return ($query->num_rows() > 0);
	}
	function SwapCategoryOrder($category_id_1, $category_id_2, $parent_type)
	{
		$this->db->trans_start();
		$sql = 'SELECT	content_type_id
			FROM	content_types
			WHERE	content_type_section_order = ?
			AND	content_type_parent_content_type_id = ?';
		$query = $this->db->query($sql,array($category_id_1, $parent_type));
		$row = $query->row();
		$content_type_id_1 = $row->content_type_id;

		$sql = 'SELECT	content_type_id
			FROM	content_types
			WHERE	content_type_section_order = ?
			AND	content_type_parent_content_type_id = ?';
		$query = $this->db->query($sql,array($category_id_2, $parent_type));
		$row = $query->row();
		$content_type_id_2 = $row->content_type_id;

		$sql = 'UPDATE	content_types
			SET	content_type_section_order = ?
			WHERE	content_type_section_order = ?
			AND	content_type_id = ?';
		$query = $this->db->query($sql,array($category_id_2, $category_id_1, $content_type_id_1));

		$sql = 'UPDATE	content_types
			SET	content_type_section_order = ?
			WHERE	content_type_section_order = ?
			AND	content_type_id = ?';
		$query = $this->db->query($sql,array($category_id_1, $category_id_2, $content_type_id_2));
		$this->db->trans_complete();
	}
	
	//only allow delete of children and non hardcoded sections to prevent major site break
	//@NOTE check the content_type does not have any articles published in it!!!
	function DeleteCategory($id, $parent_id)
	{
		$this->db->trans_start();
		//Check its not a parent
		$sql = 'SELECT  content_types.content_type_has_children, content_type_section 
			FROM    content_types 
			WHERE  content_types.content_type_id = ?  
			LIMIT 1';
		$query = $this->db->query($sql,array($id));
		$row = $query->row();
		if($row->content_type_has_children == 1 || $row->content_type_section=='hardcoded'){
			//must not delete otherwise children would be orphend
			$this->db->trans_complete();
			return false;
		}else{
			/////////////start reordering to be able to delete it
			$sql = 'SELECT	content_type_section_order
				FROM	content_types
				WHERE	content_type_id = ?';
			$query = $this->db->query($sql,array($id));
			$row = $query->row();
			$delete_section_order = $row->content_type_section_order;//Its order number

			$sql = 'SELECT	MAX(content_type_section_order) as max_section_order
				FROM	content_types
				WHERE	content_type_parent_content_type_id = ?';
			$query = $this->db->query($sql,array($parent_id));
			$row = $query->row();
			$max_section_order = $row->max_section_order;//The highest order number

			for($i = $delete_section_order; $i < $max_section_order; $i++)
			{
				self::SwapCategoryOrder($i, $i + 1, $parent_id);//keep swaping untill the highest
			}
			
			//can delete now its the highest
			$sql = 'DELETE FROM content_types
				WHERE	content_type_id = ?';
			$query = $this->db->query($sql,array($id));
			$this->db->trans_complete();
			return true;
		}
	}
	
	function SetArticleFeatured($id, $featured)
	{
		$sql = 'UPDATE articles 
				SET	articles.article_featured = ? 
				WHERE articles.article_id = ? 
				LIMIT 1';
		$query = $this->db->query($sql,array($featured, $id));
	}
}
?>
