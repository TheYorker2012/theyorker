<?php

// writers model

class Podcasts_model extends Model
{

	function Podcasts_model()
	{
		parent::Model();
	}
	
	function GetPodcasts()
	{
		$sql = 'SELECT	podcast_id as id,
						podcast_name as name,
						podcast_is_live as is_live
				FROM	podcasts
				WHERE	podcasts.podcast_deleted = 0
				ORDER BY podcast_timestamp DESC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function GetPodcastDetails($id)
	{
		$sql = 'SELECT	podcast_id as id,
						podcast_name as name,
						podcast_description as description,
						podcast_file as file,
						podcast_file_size as file_size,
						podcast_is_live as is_live,
						UNIX_TIMESTAMP(podcast_timestamp) as date
				FROM	podcasts
				WHERE	podcasts.podcast_deleted = 0
				AND		podcasts.podcast_id = ?
				ORDER BY podcast_timestamp DESC';
		$query = $this->db->query($sql,array($id));
		return $query->result_array();
	}

}