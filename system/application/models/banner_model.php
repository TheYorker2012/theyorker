<?php
/*
 * Model for use with banners office
 * @author Nick Evans nse500
 *@author Owen Jones oj502
 *@note Most code replaced/changed by oj502, contact me with problems.
 */
class Banner_Model extends Model {
	/*
	 * Constructor, calls default model constructor
	 */
	function Banner_Model() {
		parent::Model();
	}

	/* REPLACED, DONT USE
	 * Function to return a list of the schedualed and unscheduled banners.
	 * Returns the banner_text and banner_author in an array.
	 */
	function GetBanners() {
		$sql = 'SELECT image_id as banner_id, image_title as banner_title, image_last_displayed_timestamp as banner_last_displayed_timestamp
			FROM images
			WHERE image_image_type_id = 9
			AND DATE(image_last_displayed_timestamp) >= CURRENT_DATE()';
		$query = $this->db->query($sql);

		$scheduled_banners = $query->result_array();

		$sql = 'SELECT image_id as banner_id, image_title as banner_title
			FROM images
			WHERE image_image_type_id = 9
			ORDER BY image_last_displayed_timestamp
			LIMIT 0,10';
		$query = $this->db->query($sql);

		$unscheduled_banners = $query->result_array();

		return array_merge($scheduled_banners, $unscheduled_banners);
	}
	
	///////////////////NEW BANNER SYSTEM
	//////////////////@author Owen Jones (oj502@york.ac.uk)
	
	//Function to find images (by default banners) that are not accosicated with any homepage (eg they have no entry in homepage_banners)
	//This is for the homepage office manager, so they can be assigned a homepage.
	//@param (optional) image type. You probably dont want to change this unless there is a new fancy banner type or something
	//@returns array of images (id, type_codename)
	function GetBannersWithNoHompage($type='banner')
	{
		$sql='SELECT images.image_id AS id, image_types.image_type_codename AS type_codename 
		FROM images 
			LEFT JOIN homepage_banners ON homepage_banners.homepage_banner_image_id = images.image_id 
			INNER JOIN image_types ON image_types.image_type_id = images.image_image_type_id 
		WHERE homepage_banners.homepage_banner_content_type_id IS NULL 
		AND image_types.image_type_codename=?';
		$query = $this->db->query($sql,array($type));
	}
	
	/*
	 * Function to return a list of the schedualed and unscheduled banners.
	 * Returns the banner_id, banner_type,  banner_title, banner_homepage and banner_homepage_codename in an array. Also includes banner_last_displayed_timestamp for scheduled banners.
	 * Results are ordered by the banner_homepage then by the last shown time.
	 */
	function GetBannersByHomepage($type='banner')
	{
		$sql = 'SELECT
					image_id as banner_id,
					image_title as banner_title,
					image_last_displayed_timestamp as banner_last_displayed_timestamp,
					image_types.image_type_codename as banner_type,
					content_types.content_type_name as banner_homepage,
					content_types.content_type_codename as banner_homepage_codename
			FROM images 
			INNER JOIN image_types ON image_types.image_type_id = images.image_image_type_id 
			INNER JOIN homepage_banners ON images.image_id = homepage_banners.homepage_banner_image_id 
			INNER JOIN content_types ON homepage_banners.homepage_banner_content_type_id = content_types.content_type_id 
			WHERE image_types.image_type_codename = ?
			AND DATE(image_last_displayed_timestamp) >= CURRENT_DATE() 
			ORDER BY content_types.content_type_codename ASC, DATE(image_last_displayed_timestamp) DESC';
		$query = $this->db->query($sql, array($type));
		$scheduled_banners = $query->result_array();
		
		$sql = 'SELECT
					image_id as banner_id,
					image_title as banner_title,
					image_types.image_type_codename as banner_type,
					content_types.content_type_name as banner_homepage,
					content_types.content_type_codename as banner_homepage_codename
			FROM images 
			INNER JOIN image_types ON image_types.image_type_id = images.image_image_type_id 
			INNER JOIN homepage_banners ON images.image_id = homepage_banners.homepage_banner_image_id 
			INNER JOIN content_types ON homepage_banners.homepage_banner_content_type_id = content_types.content_type_id 
			WHERE image_types.image_type_codename = ?
			ORDER BY content_types.content_type_codename ASC, DATE(image_last_displayed_timestamp) DESC';
		$query = $this->db->query($sql, array($type));
		$unscheduled_banners = $query->result_array();
		
		return array_merge($scheduled_banners, $unscheduled_banners);
	}
	
	/*
	 * Function to obtain a particular banner.
	 * Returns the banner_id, banner_text, banner_author and banner_last_displayed_timestamp in an array.
	 */
	function GetBanner($banner_id) {
		$sql = 'SELECT image_id as banner_id, image_title as banner_title, IF( DATE(image_last_displayed_timestamp) >= CURRENT_DATE(), image_last_displayed_timestamp, null) as banner_last_displayed_timestamp
			FROM images
			WHERE image_id = ?';
		$query = $this->db->query($sql, array($banner_id));
		return $query->row();
	}

	/*
	 * Function to update a particular banner.
	 * Returns the the number of rows affected.
	 */
	function UpdateBanner($banner_id, $banner_title, $banner_last_displayed_timestamp = null) {
			$sql = 'UPDATE images
				SET image_title = ?
				'.($banner_last_displayed_timestamp != null ? ', image_last_displayed_timestamp = ?' : '/* ? */').'
				WHERE image_id = ?';
			$update = $this->db->query($sql,array($banner_title, $banner_last_displayed_timestamp, $banner_id));
		return true;
	}
	
	//Creates a link so image will be in the pool to display on that homepage (images can be on multiple homepages)
	//@param $image_id - Id of image to use.
	//@param $homepage_type (optional) - Codename from content_types of that homepage.
	function LinkImageToHomepage($image_id,$homepage_type='home')
	{
		//Insert row into homepage_banners
		//Get Content_type id from content_types using the codename.
		$sql='
		INSERT INTO homepage_banners (homepage_banner_image_id, homepage_banner_content_type_id) 
		SELECT ?, content_types.content_type_id 
		FROM content_types WHERE content_types.content_type_codename=? LIMIT 1 ';
		$query = $this->db->query($sql,array($image_id,$homepage_type));
	}
	
	//Deletes a link so image will no longer be in the pool to display on that homepage (images can be on multiple homepages)
	//@param $image_id - Id of image to use.
	//@param $homepage_type (optional) - Codename from content_types of that homepage.
	function DeleteImageHomepageLink($image_id,$homepage_type='home')
	{
	$sql='DELETE FROM homepage_banners WHERE
		homepage_banner_image_id=? AND 
		homepage_banner_content_type_id = (SELECT content_types.content_type_id FROM content_types WHERE content_types.content_type_codename=? LIMIT 1 ) LIMIT 1';
	$query = $this->db->query($sql,array($image_id,$homepage_type));
	}
}
?>
