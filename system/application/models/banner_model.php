<?php
/*
 * Model for use with banners office
 *@author Owen Jones oj502
 */
class Banner_Model extends Model {
	/*
	 * Constructor, calls default model constructor
	 */
	function Banner_Model() {
		parent::Model();
	}
	
	//Function to find images (by default banners) that are not accosicated with any homepage (eg they have no entry in homepage_banners)
	//This is for the homepage office manager, so they can be assigned a homepage.
	//@param (optional) image type. You probably dont want to change this unless there is a new fancy banner type or something
	//@returns array of images (id, type_codename)
	function GetBannersWithNoHompage($type='banner')
	{
		$sql='
			SELECT 
				images.image_id AS banner_id, 
				image_title as banner_title, 
				image_types.image_type_codename AS banner_type 
			FROM images 
			LEFT JOIN homepage_banners ON 
				homepage_banners.homepage_banner_image_id = images.image_id 
			INNER JOIN image_types ON 
				image_types.image_type_id = images.image_image_type_id 
			WHERE homepage_banners.homepage_banner_content_type_id IS NULL 
			AND image_types.image_type_codename=?';
		$query = $this->db->query($sql,array($type));
		return $query->result_array();
	}
	
	/*
	 * Function to return a list of the schedualed and unscheduled banners.
	 * Returns the banner_id, banner_type,  banner_title, banner_homepage and banner_homepage_codename in an array. Also includes banner_last_displayed_timestamp for scheduled banners.
	 * Results are ordered by the banner_homepage then by the last shown time.
	 */
	function GetScheduledBannersByHomepage($type='banner',$section_name=null)
	{
		$sql = 'SELECT
					image_id as banner_id,
					image_title as banner_title,
					UNIX_TIMESTAMP(image_last_displayed_timestamp) as banner_last_displayed_timestamp,
					image_types.image_type_codename as banner_type,
					content_types.content_type_name as banner_homepage,
					content_types.content_type_codename as banner_homepage_codename,
					homepage_banners.homepage_banner_link AS banner_link
			FROM images 
			INNER JOIN image_types ON 
				image_types.image_type_id = images.image_image_type_id 
			INNER JOIN homepage_banners ON 
				images.image_id = homepage_banners.homepage_banner_image_id 
			INNER JOIN content_types ON 
				homepage_banners.homepage_banner_content_type_id = content_types.content_type_id 
			WHERE image_types.image_type_codename = ?
			AND DATE(image_last_displayed_timestamp) >= CURRENT_DATE() '; 
		if (!empty($section_name)) {
			$sql .= 'AND content_types.content_type_codename=? ';
			$inputs = array($type,$section_name);
		} else {
			$inputs = array($type);
		}	
		$sql .= 'ORDER BY content_types.content_type_codename ASC, DATE(image_last_displayed_timestamp) DESC';
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();
	}
	function GetPoolBannersByHomepage($type='banner',$section_name=null)
	{
		$sql = 'SELECT
					image_id AS banner_id,
					image_title AS banner_title,
					image_types.image_type_codename AS banner_type,
					content_types.content_type_name AS banner_homepage,
					content_types.content_type_codename AS banner_homepage_codename,
					homepage_banners.homepage_banner_link AS banner_link
			FROM images 
			INNER JOIN image_types ON 
				image_types.image_type_id = images.image_image_type_id 
			INNER JOIN homepage_banners ON 
				images.image_id = homepage_banners.homepage_banner_image_id 
			INNER JOIN content_types ON 
				homepage_banners.homepage_banner_content_type_id = content_types.content_type_id 
			WHERE image_types.image_type_codename = ? 
			AND DATE(image_last_displayed_timestamp) < CURRENT_DATE() ';
		if (!empty($section_name)) {
			$sql .= 'AND content_types.content_type_codename=? ';
			$inputs = array($type,$section_name);
		} else {
			$inputs = array($type);
		}
		$sql .= 'ORDER BY content_types.content_type_codename ASC, DATE(image_last_displayed_timestamp) DESC';
		$query = $this->db->query($sql, $inputs);
		return $query->result_array();
	}
	
	//This function outputs the current banner for every homepage. This does a simular job to the banner finder for a section, it finds the banner for today, or assigns one if there is not one.
	function GetAllCurrentHomepageBanners($type='banner')
	{
		//Find all sections that do not have a banner with a timestamp for today and have at least one banner (all sections that need updating!)
		$sql = 'SELECT DISTINCT(content_types.content_type_id) as section_id, content_types.content_type_codename 
				FROM content_types 
				LEFT JOIN homepage_banners ON
					content_types.content_type_id = homepage_banners.homepage_banner_content_type_id
				WHERE content_types.content_type_parent_content_type_id IS NULL 
				AND homepage_banners.homepage_banner_image_id IS NOT NULL
				AND NOT EXISTS (
					SELECT image_id as banner_id
					FROM images 
					INNER JOIN homepage_banners as hp ON 
						images.image_id = hp.homepage_banner_image_id 
					INNER JOIN content_types as ct ON 
						hp.homepage_banner_content_type_id = ct.content_type_id 
					WHERE DATE(image_last_displayed_timestamp) = CURRENT_DATE()
					AND hp.homepage_banner_content_type_id = content_types.content_type_id
				) 
				ORDER BY content_types.content_type_name ASC';
		$query = $this->db->query($sql);
		$sections_to_update = $query->result_array();
		
		//Each section found in $sections_to_update needs to be updated, so find the oldest banner for that section and re-assign it with the current date.
		foreach($sections_to_update as $section)
		{
			//Find the oldest homepage image of the right type
			$sql = 'SELECT images.image_id
					FROM images
					INNER JOIN image_types ON 
						image_types.image_type_id = images.image_image_type_id 
					INNER JOIN homepage_banners ON 
						images.image_id = homepage_banners.homepage_banner_image_id 
					WHERE homepage_banners.homepage_banner_content_type_id = ? 
					AND image_types.image_type_codename = ?
					ORDER BY images.image_last_displayed_timestamp LIMIT 1';
			$oldest_banner = $this->db->query($sql, array($section['section_id'],$type));
			
			//Update the oldest with the current time&date if there is one
			if($oldest_banner->num_rows() == 1){
				$new_banner_id = $oldest_banner->row()->image_id;
				$sql = '
					UPDATE images
					SET images.image_last_displayed_timestamp = CURRENT_TIMESTAMP()
					WHERE images.image_id = ?';
				$update_banner = $this->db->query($sql,array($new_banner_id));
			}
		}
		
		//Pick up all the banners being displayed today
		$sql = '
			SELECT
				image_id as banner_id,
				image_title as banner_title,
				UNIX_TIMESTAMP(image_last_displayed_timestamp) as banner_last_displayed_timestamp,
				image_types.image_type_codename as banner_type,
				content_types.content_type_name as banner_homepage,
				content_types.content_type_codename as banner_homepage_codename,
				homepage_banners.homepage_banner_link AS banner_link
			FROM images 
			INNER JOIN image_types ON 
				image_types.image_type_id = images.image_image_type_id 
			INNER JOIN homepage_banners ON 
				images.image_id = homepage_banners.homepage_banner_image_id 
			INNER JOIN content_types ON 
				homepage_banners.homepage_banner_content_type_id = content_types.content_type_id 
			WHERE image_types.image_type_codename = ?
			AND DATE(image_last_displayed_timestamp) = CURRENT_DATE() 
			ORDER BY content_types.content_type_name ASC';
		$query = $this->db->query($sql, array($type));
		$todays_banners = $query->result_array();
		
		return $todays_banners;
	}
	
	//This is just a simple function to get a nice print name for a homepage section, used in the title for reviews.
	function GetHomepageSectionNameFromCodename($section_codename) {
		$sql = 'SELECT content_type_name as name, content_type_id as id
				FROM content_types 
				WHERE content_type_codename = ?
				LIMIT 1';
		$query = $this->db->query($sql, array($section_codename));
		if($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return '';
		}
	}
	function GetHomepageSectionNameFromId($section_id) {
		$sql = 'SELECT content_type_name as name, content_type_codename as codename
				FROM content_types 
				WHERE content_type_id = ?
				LIMIT 1';
		$query = $this->db->query($sql, array($section_id));
		if($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return '';
		}
	}
	/*
	 * Function to obtain a particular banner.
	 * Returns the banner_id, banner_text, banner_author and banner_last_displayed_timestamp in an array.
	 */
	function GetBanner($banner_id) {
		$sql = 'SELECT 	
					images.image_id as banner_id, 
					images.image_title as banner_title, 
					image_types.image_type_codename as banner_type,
					IF( DATE(image_last_displayed_timestamp) >= CURRENT_DATE(), UNIX_TIMESTAMP(image_last_displayed_timestamp), null) 
					as banner_last_displayed_timestamp,
					homepage_banners.homepage_banner_link as link,
					homepage_banner_content_type_id as homepage_id
				FROM images
				LEFT JOIN homepage_banners ON 
					homepage_banner_image_id = images.image_id
				INNER JOIN image_types ON
					images.image_image_type_id = image_types.image_type_id
				WHERE image_id = ?
				LIMIT 1';
		$query = $this->db->query($sql, array($banner_id));
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return false;
		}
	}
	
	/*
	 * Function to obtain details about a banners homepage (if there is one)
	 * Returns the banner_homepage_id Returns NULL value if homepage doesnt exist.
	 */
	function GetBannersHomepage($banner_id){
		$sql='
			SELECT 
				content_types.content_type_id as homepage_id,
				content_types.content_type_name as homepage_name,
				content_types.content_type_codename as homepage_codename
			FROM images 
			LEFT OUTER JOIN homepage_banners ON 
				images.image_id = homepage_banners.homepage_banner_image_id 
			LEFT OUTER JOIN content_types ON 
				homepage_banners.homepage_banner_content_type_id = content_types.content_type_id 
			WHERE images.image_id = ?
			LIMIT 1';
		$query = $this->db->query($sql, array($banner_id));
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return false;
		}
	}
	/*
	 * Function to update a particular banner.
	 * If the timestamp is left null it will not update the time, this may or may not put it back in the pool.
	 */
	function UpdateBanner($banner_id, $banner_title, $banner_last_displayed_timestamp = null) {
		//If the timestamp is null, do not update it.
		$sql = 'UPDATE images SET 
				image_title = ?';
		if (!empty($banner_last_displayed_timestamp)) {
			$sql .= ', image_last_displayed_timestamp = ?';
			$inputs = array($banner_title, $banner_last_displayed_timestamp, $banner_id);
		} else {
			$inputs = array($banner_title, $banner_id);
		}
		$sql .= ' WHERE image_id = ?';
		$query = $this->db->query($sql,$inputs);
		return true;
	}
	
	/*
	 * Returns true if the given image id has an entry in homepage banners.
	 */
	function HasHomepageEntry($image_id)
	{
		$sql = 'SELECT	homepage_banner_image_id
				FROM	homepage_banners
				WHERE	homepage_banner_image_id = ?';
		$query = $this->db->query($sql,array($image_id));
		if ($query->num_rows() == 1) {
			return true;
		}
		else {
			return false;
		}
	}
	
	//Creates a link so image will be in the pool to display on that homepage (images can be on multiple homepages)
	//@param $image_id - Id of image to use.
	//@param $homepage_id (optional) - Id from content_types of that homepage, default is the main homepage.
	function LinkImageToHomepage($image_id,$homepage_id='',$banner_link='')
	{
		if($homepage_id==''){
			$sql='SELECT content_types.content_type_id 
			FROM content_types WHERE content_types.content_type_codename=? LIMIT 1';
			$query = $this->db->query($sql,array('home'));
			$homepage_id = $query->row()->content_type_id;
		}
		$sql='INSERT INTO homepage_banners (homepage_banner_image_id, homepage_banner_content_type_id, homepage_banner_link) VALUES ( ?, ?, ?)';
		$query = $this->db->query($sql,array($image_id,$homepage_id,$banner_link));
	}
	
	//Deletes a link so image will no longer be in the pool to display on that homepage (images can be on multiple homepages)
	//@param $image_id - Id of image to use.
	//@param $homepage_type (optional) - Codename from content_types of that homepage.
	function DeleteImageHomepageLink($image_id,$homepage_id='')
	{
		if($homepage_id==''){
			$sql='SELECT content_types.content_type_id 
			FROM content_types WHERE content_types.content_type_codename=? LIMIT 1';
			$query = $this->db->query($sql,array('home'));
			$homepage_id = $query->row()->content_type_id;
		}
		$sql='DELETE FROM homepage_banners WHERE
			homepage_banner_image_id= ? AND 
			homepage_banner_content_type_id = ? LIMIT 1';
		$query = $this->db->query($sql,array($image_id,$homepage_id));
	}
	//Pools all banners from $section_id apart from $ignore_banner_id with the same date as timestamp
	//$timestamp is of the format yyyy-mm-dd hh:mm:ss
	//@note The ignore banner id is optional, but is usefull if you are pooling banners to then schedule one. 
	//If you are updating one and not changing the timestamp, include it in here to avoid unassigning it then re-assigning it.
	function PoolAllBannersWithThisDate($timestamp, $section_id, $ignore_banner_id=null)
	{
		//Just interested in the date of the timestamp
		$date_timestamp = substr($timestamp,0,10);
		//Select all the banners with this date for this section
		$sql = 'SELECT images.image_id
				FROM images
				INNER JOIN homepage_banners ON 
					homepage_banner_image_id = images.image_id
				WHERE DATE(images.image_last_displayed_timestamp)=? 
				AND homepage_banners.homepage_banner_content_type_id = ? ';
		if (!empty($ignore_banner_id)) {
			$sql .='AND NOT images.image_id=?';
			$inputs = array($date_timestamp,$section_id,$ignore_banner_id);
		} else {
			$inputs = array($date_timestamp,$section_id);
		}
		$query = $this->db->query($sql,$inputs);
		
		//Force every result back into the pool, (assign it to this time yesterday)
		foreach($query->result() as $row) {
			$new_timestamp = date('Y-m-d H:i:s', time() - 86400);
			$sql = 'UPDATE images SET image_last_displayed_timestamp=? WHERE image_id = ?';
			$query = $this->db->query($sql,array($new_timestamp,$row->image_id));
		}
	}
	
	//Deletes all the links to an image (images can be on multiple homepages) to prepare for deleting the image
	//@param $image_id - Id of image to use.
	function DeleteAllLinksToImage($image_id)
	{
		$sql='DELETE FROM homepage_banners WHERE 
		homepage_banner_image_id=?
		';
	$query = $this->db->query($sql,array($image_id));
	}
}
?>
