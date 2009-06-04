<?php

/**
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Flickr_model extends Model {

	function Flickr_model()
	{
		parent::Model();
	}

	function addPhoto ($id, $secret, $server, $farm, $license, $owner_id, $owner_realname, $owner_username, $title, $description, $posted, $taken, $lastupdate)
	{
		$sql = 'REPLACE INTO photos_flickr
				SET		id			= ?,
						secret		= ?,
						server		= ?,
						farm		= ?,
						license		= ?,
						owner_id	= ?,
						owner_name	= ?,
						owner_alias	= ?,
						title		= ?,
						description	= ?,
						date_posted	= ?,
						date_taken	= ?,
						date_update	= ?';
		$query = $this->db->query($sql, array($id, $secret, $server, $farm, $license, $owner_id, $owner_realname, $owner_username, $title, $description, $posted, $taken, $lastupdate));
	}

	function tag ($photo_id, $tag_id, $long_tag, $short_tag)
	{
		$sql = 'REPLACE INTO flickr_tags
				SET		tag_id		= ?,
						tag_display = ?,
						tag_code	= ?';
		$query = $this->db->query($sql, array($tag_id, $long_tag, $short_tag));
		$sql = 'REPLACE INTO flickr_photo_tags
				SET		tag_id		= ?,
						photo_id	= ?';
		$query = $this->db->query($sql, array($tag_id, $photo_id));
	}

	function getLatestPhotos ($count = 5) {
		$sql = 'SELECT * FROM photos_flickr ORDER BY date_posted DESC LIMIT 0, ?';
		$query = $this->db->query($sql, array($count));
		return $query->result_array();
	}

	function cronGetLast ()
	{
		$sql = 'SELECT last FROM cron_updates WHERE service_name = "flickr_recent_photos"';
		$query = $this->db->query($sql);
		return $query->row()->last;
	}

	function cronSetLast ($timestamp = NULL)
	{
		if (empty($timestamp)) $timestamp = mktime();
		$sql = 'UPDATE cron_updates SET last = ? WHERE service_name = "flickr_recent_photos"';
		$query = $this->db->query($sql, array($timestamp));
	}

}
?>