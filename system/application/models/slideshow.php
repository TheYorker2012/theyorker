<?php
/**
 * This model retrieves for Slideshows of any type.
 *
 * @author Nick Evans
 * @author Mark Goodall
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
	function getPhotos($organisation_id, $contextType = null) {
		//This is why its better not to affix table names to columns...
		if (is_null($contextType)) {
			$query = $this->db->select('*')->from('photos');
			$query = $query->join('organisation_slideshows', 'organisation_slideshow_photo_id = photo_id');
			$query = $query->where('organisation_slideshow_organisation_entity_id', $organisation_id);
			$result = $query->orderby('organisation_slideshow_order', 'asc')->get();
		} else {
			$query = $this->db->select('*')->from('photos');
			$query = $query->join('review_context_slideshows', 'review_context_slideshow_organisation_entity_id = photo_id');
			$query = $query->where('review_context_slideshow_organisation_entity_id', $organisation_id);
			//one line different...
			$query = $query->where('review_context_slideshow_content_type_id', $contextType);
			$result = $query->orderby('review_context_slideshow_order', 'asc')->get();
		}

		return $result;
	}
	
	function pushUp($photo_id, $organisation_id, $contextType = null, $order = 'asc') {
		//ditto
		if (is_null($contextType)) {
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
				if ($order == 'asc') {
					$sql = 'UPDATE organisation_slideshows
					        SET organisation_slideshow_order=organisation_slideshow_order+1
					        WHERE organisation_slideshow_organisation_entity_id=? AND (NOT organisation_slideshow_photo_id=?) AND organisation_slideshow_order=?';
				} else {
					$sql = 'UPDATE organisation_slideshows
					        SET organisation_slideshow_order=organisation_slideshow_order-1
					        WHERE organisation_slideshow_organisation_entity_id=? AND (NOT organisation_slideshow_photo_id=?) AND organisation_slideshow_order=?';
					
				}
				$this->db->query($sql, array($organisation_id, $photo_id, $row->organisation_slideshow_order));
			}
		} else {
			$query = $this->db->orderby('review_context_slideshow_order', $order)->getwhere('review_context_slideshows', array('review_context_slideshow_organisation_entity_id' => $organisation_id), 1);
			foreach($query->result() as $result) {
				if ($photo_id == $result->review_context_slideshow_photo_id) {
					return false;
				}
			}
			if ($order == 'asc') {
				$sql = 'UPDATE review_context_slideshows
			            SET review_context_slideshow_order=organisation_slideshow_order-1
			            WHERE review_context_slideshow_organisation_entity_id=? AND organisation_slideshow_photo_id=?';
			} else {
				$sql = 'UPDATE review_context_slideshows
			            SET organisation_slideshow_order=review_context_slideshow_order+1
			            WHERE organisation_slideshow_organisation_entity_id=? AND review_context_slideshow_photo_id=?';
			}
			$this->db->query($sql, array($organisation_id, $photo_id));
			$result = $this->db->getwhere('review_context_slideshows', array('review_context_slideshow_organisation_entity_id' => $organisation_id, 'review_context_slideshow_photo_id' => $photo_id), 1);
			foreach ($result->result() as $row) {
				if ($order == 'asc') {
					$sql = 'UPDATE review_context_slideshows
					        SET review_context_slideshow_order=review_context_slideshow_order+1
					        WHERE review_context_slideshow_organisation_entity_id=? AND (NOT review_context_slideshow_photo_id=?) AND review_context_slideshow_order=?';
				} else {
					$sql = 'UPDATE review_context_slideshows
					        SET review_context_slideshow_order=review_context_slideshow_order-1
					        WHERE review_context_slideshow_organisation_entity_id=? AND (NOT review_context_slideshow_photo_id=?) AND review_context_slideshow_order=?';
					
				}
				$this->db->query($sql, array($organisation_id, $photo_id, $row->review_context_slideshow_order));
			}
		}
		return true;
	}
	
	function pushDown($photo_id, $organisation_id, $contextType = null) {
		return $this->pushUp($photo_id, $organisation_id, $contextType, 'desc');
	}

	function deletePhoto($photo_id, $organisation_id, $contextType = null) {
		return $this->db->delete('organisation_slideshows', array('organisation_slideshow_organisation_entity_id' => $organisation_id,
		                                                          'organisation_slideshow_photo_id' => $photo_id));
	}

	function addPhoto($photo_id, $organisation_id, $contextType = null) {
		$count = $this->db->query('SELECT COUNT(*) AS row_count FROM organisation_slideshows WHERE organisation_slideshow_organisation_entity_id = '.$organisation_id);
		$count = $count->first_row()->row_count;
		return $this->db->insert('organisation_slideshows', array('organisation_slideshow_organisation_entity_id' => $organisation_id,
		                                                          'organisation_slideshow_photo_id' => $photo_id,
		                                                          'organisation_slideshow_order' => $count));
	}

}
?>