<?php
/**
 * This model retrieves data for the A to Z page.
 *
 * @author Nick Evans
 */
class Advert_model extends Model {

	function Advert_model()
	{
		// Call the Model constructor
		parent::Model();
	}
	
	/**
	 * @brief Selects the least recently used advert 
	 */
	function SelectLatestAdvert()
	{
		$this->db->trans_start();
			//select the latest advert with page views left
			$sql = 'SELECT
						advert_id as id,
						advert_image as image,
						advert_image_alt as alt,
						advert_image_url as url,
						advert_views_current as current_views,
						advert_live as live
					FROM
						adverts_simple
					WHERE
						advert_deleted = 0 AND
						advert_views_current < advert_views_max AND
						advert_live = 1
					ORDER BY
						advert_last_display ASC
					LIMIT
						1';
			$query = $this->db->query($sql);
			//if there is an advert
			if ($query->num_rows() == 1)
			{
				$row = $query->row();
				if ($row->live = true) {
					$result = array(
						'image' => $row->image,
						'alt' => $row->alt,
						'url' => $row->url
						);
					$id = $row->id;
					$views = $row->current_views;
					//update the views count
					$sql = 'UPDATE
								adverts_simple
							SET
								advert_last_display = CURRENT_TIMESTAMP,
								advert_views_current  = ?
							WHERE
								advert_id = ?
							AND	advert_deleted = 0';
					$this->db->query($sql,array($views+1, $id));
				}
				else
					$result = FALSE;
			}
			else
				$result = FALSE;
		$this->db->trans_complete();
		return $result;
	}
	
	/**
	 * @brief Returns all the adverts in the database 
	 */
	function GetAdverts()
	{
		//select the latest advert with page views left
		$sql = 'SELECT
					advert_id as id,
					advert_name as name,
					advert_views_current as current_views,
					advert_views_max as max_views,
					advert_live as is_live
				FROM
					adverts_simple
				WHERE
					advert_deleted = 0
				ORDER BY
					advert_name ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = array(
					'id'=>$row->id,
					'name'=>$row->name,
					'current_views'=>$row->current_views,
					'max_views'=>$row->max_views,
					'is_live'=>$row->is_live
					);
			}
		}
		return $result;
	}
	
	/**
	 * @brief Returns true if the advert exists 
	 */
	function AdvertExists($advert_id)
	{
		//select the latest advert with page views left
		$sql = 'SELECT
					advert_id as id,
					advert_name as name,
					advert_image as image,
					advert_image_alt as alt,
					advert_image_url as url,
					advert_views_current as current_views,
					advert_views_max as max_views,
					UNIX_TIMESTAMP(advert_last_display) as last_display,
					UNIX_TIMESTAMP(advert_created) as created,
					advert_live as is_live
				FROM
					adverts_simple
				WHERE
					advert_deleted = 0 AND
					advert_id = ?';
		$query = $this->db->query($sql, array($advert_id));
		//if there is an advert
		if ($query->num_rows() == 1) {
			$row = $query->row();
			$result = array(
				'id' => $row->id,
				'name' => $row->name,
				'image' => $row->image,
				'alt' => $row->alt,
				'url' => $row->url,
				'current_views' => $row->current_views,
				'max_views' => $row->max_views,
				'last_display' => $row->last_display,
				'created' => $row->created,
				'is_live' => $row->is_live
				);
			return $result;
		}
		else
			return FALSE;
	}
	
	/**
	 * @brief Adds a new advert to the database 
	 */
	function AddNewAdvert($name)
	{
		$sql = 'INSERT INTO
					adverts_simple (
						advert_name,
						advert_last_display,
						advert_created
						)
				VALUES (?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';
		$this->db->query($sql, array($name));
		return TRUE;
	}
	
	/**
	 * @brief Saves the new advert data 
	 */
	function SaveAdvert($id, $name, $url, $alt, $image, $max)
	{
		$sql = 'UPDATE
					adverts_simple
				SET
					advert_name = ?,
					advert_image_url = ?,
					advert_image_alt = ?,
					advert_image = ?,
					advert_views_max = ?
				WHERE
					advert_id = ?';
		$this->db->query($sql, array($name, $url, $alt, $image, $max, $id));
		return TRUE;
	}
	
	/**
	 * @brief Deletes the given advert
	 */
	function DeleteAdvert($id)
	{
		$sql = 'UPDATE
					adverts_simple
				SET
					advert_deleted = 1
				WHERE
					advert_id = ?';
		$this->db->query($sql, array($id));
		return TRUE;
	}
	
	/**
	 * @brief Pulls the given advert from the advert rotation
	 */
	function PullAdvert($id)
	{
		$sql = 'UPDATE
					adverts_simple
				SET
					advert_live = 0
				WHERE
					advert_id = ?';
		$this->db->query($sql, array($id));
		return TRUE;
	}
	
	/**
	 * @brief Adds the given advert to the advert rotation
	 */
	function MakeAdvertLive($id)
	{
		$sql = 'UPDATE
					adverts_simple
				SET
					advert_live = 1
				WHERE
					advert_id = ?';
		$this->db->query($sql, array($id));
		return TRUE;
	}
}
?>