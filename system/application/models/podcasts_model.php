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

	function Add_Entry($filename,$size)
	{
		$sql = '	INSERT INTO podcasts
					(podcast_file,podcast_file_size,podcast_timestamp)
					VALUES (?,?,UNIX_TIMESTAMP())';
		$this->db->query($sql,array($filename,$size));
		$sql = '	SELECT	podcast_id
					FROM	podcasts
					WHERE	podcast_file=?';
		$query = $this->db->query($sql,array($filename));
		if ($query->num_rows() >0)
		{
			return $query->row()->podcast_id;
		}else{ return 0;}
	}

	function Get_Fnames()
	{
		$sql = '	SELECT	podcast_file
					FROM	podcasts';
		$query = $this->db->query($sql);
		if ($query->num_rows() >0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = $row->podcast_file;
			}
		}else{ return 0;}
		return $result;
	}
}
