<?php
/*
 * Model for use with banners office
 *
 *
 * \author Nick Evans nse500
 *
 *
 *
 */
class Banner_Model extends Model {
	/*
	 * Constructor, calls default model constructor
	 */
	function Banner_Model() {
		parent::Model();
	}

	/*
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

}
?>
