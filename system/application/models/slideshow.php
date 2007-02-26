<?php
/**
 * This model retrieves data for the A to Z page.
 *
 * @author Nick Evans
 */
class Slideshow extends Model {

	function Slideshow()
	{
		// Call the Model constructor
		parent::Model();
	}

	/**
	 * This grabs photos from an organisation in order
	 * @param organisation_type_id Is the type of organisations to return
	 * @return Result object
	 */
	function getPhotos($organisation_id) {

		$query = $this->db->select('*')->from('photos');
		$query = $query->join('organisation_slideshows', 'organisation_slideshow_photo_id = photo_id');
		$query = $query->where('organisation_slideshow_organisation_entity_id', $organisation_id);
		$result = $query->orderby('organisation_slideshow_order', 'asc')->get();

		return $result;
	}
	
	function pushUp($photo_id, $organisation_id, $order = 'asc') {
		$query = $this->db->orderby('organisation_slideshow_order', $order)->getwhere('organisation_slideshows', array('organisation_slideshow_organisation_entity_id' => $organisation_id), 1);
		foreach($query->result() as $result){
			if ($photo_id == $result->organisation_slideshow_photo_id) {
				return false;
			}
		}
		if ($order == 'asc') {
			$sql = 'UPDATE organisation_slideshows
		            SET organisation_slideshow_order=organisation_slideshow_order-1
		            WHERE organisation_slideshow_organisation_entity_id=? AND organisation_slideshow_photo_id=?';
		} else {
			$sql = 'UPDATE organisation_slideshows
		            SET organisation_slideshow_order=organisation_slideshow_order+1
		            WHERE organisation_slideshow_organisation_entity_id=? AND organisation_slideshow_photo_id=?';
		}
		$this->db->query($sql, array($organisation_id, $photo_id));
		$result = $this->db->getwhere('organisation_slideshows', array('organisation_slideshow_organisation_entity_id' => $organisation_id, 'organisation_slideshow_photo_id' => $photo_id), 1);
		foreach ($result->result() as $row) {
			$sql = 'UPDATE organisation_slideshows
			        SET organisation_slideshow_order=organisation_slideshow_order+1
			        WHERE organisation_slideshow_organisation_entity_id=? AND organisation_slideshow_photo_id!=? AND organisation_slideshow_order=?';
			$this->db->query($sql, array($organisation_id, $photo_id, $row->organisation_slideshow_order));
		}
		return true;
	}
	
	function pushDown($photo_id, $organisation_id) {
		return $this->pushUp($photo_id, $organisation_id, 'desc');
	}

	function deletePhoto($photo_id, $organisation_id) {
		return $this->db->delete('organisation_slideshows', array('organisation_slideshow_organisation_entity_id' => $organisation_id,
		                                                          'organisation_slideshow_photo_id' => $photo_id));
	}


}
?>