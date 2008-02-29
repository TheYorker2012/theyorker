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
						podcast_is_live as is_live,
						podcast_timestamp as timestamp
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
		$this->db->trans_start();
		$sql = '	INSERT INTO podcasts
					(podcast_file,podcast_file_size)
					VALUES (?,?)';
		$this->db->query($sql,array($filename,$size));
		$id= $this->db->insert_id();
		$sql = '	SELECT		podcast_id,
								podcast_is_live
					FROM		podcasts
					ORDER BY	podcast_timestamp DESC
					LIMIT		15,9999999';
		$query=$this->db->query($sql);
		foreach ($query->result() as $row)
		{
			if($row->podcast_is_live==1)
			{
				$sql = '	UPDATE	podcasts
							SET		podcast_is_live=0
							WHERE	podcast_id=?';
				$this->db->query($sql,$row->podcast_id);
			}
		}
		$this->db->trans_complete();
		return $id;
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
	
	function Del($id)
	{
		$sql = '	UPDATE	podcasts
					SET		podcasts.podcast_deleted = 1
					WHERE	podcasts.podcast_id = ?';
		return $this->db->query($sql,array($id));
	}
	
	function Toggle_Live($id)
	{
		$sql = '	UPDATE	podcasts
					SET		podcast_is_live = IF(podcast_is_live=1,0,1)
					WHERE	podcasts.podcast_id = ?';
		$this->db->query($sql,array($id));
		$sql = '	SELECT	podcasts.podcast_is_live
					FROM	podcasts
					WHERE	podcasts.podcast_id = ?';
		$query = $this->db->query($sql,array($id));
		return $query->row()->podcast_is_live;			
	}
	
	function CanLiveChange($id)
	{
		$sql = '	SELECT 	podcast_id
					FROM 		podcasts
					ORDER BY	podcast_timestamp
					LIMIT		15';
		$query = $this->db->query($sql);
		$result=false;
		foreach ($query->result() as $row)
		{
			if($row->podcast_id==$id){$result=true;}
		}
		return $result;
	}
	
	function Edit_Podcast_Update($id,$name,$description,$is_live)
	{
		$sql = '	UPDATE	podcasts
					SET		podcast_name=?,
							podcast_description=?,
							podcast_is_live=?
					WHERE	podcast_id=?';
		return $this->db->query($sql,array($name,$description,($is_live?1:0),$id));
	}
}
