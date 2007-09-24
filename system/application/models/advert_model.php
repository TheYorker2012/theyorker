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
						advert_views_current as current_views
					FROM
						adverts_simple
					WHERE
						advert_deleted = 0 AND
						advert_views_current < advert_views_max
					ORDER BY
						advert_last_display ASC
					LIMIT
						1';
			$query = $this->db->query($sql);
			//if there is an advert
			if ($query->num_rows() == 1)
			{
				$row = $query->row();
				$query = array(
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
				$query = FALSE;
		$this->db->trans_complete();
		return $query;
	}
}
?>