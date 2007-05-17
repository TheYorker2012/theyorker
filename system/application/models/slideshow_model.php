<?php
/**
 * This model retrieves data for the slideshow
 *
 * @author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Slideshow_model extends Model {

    function Slideshow_model()
    {
        // Call the Model constructor
        parent::Model();
    }

	/**
	 *	General
	 */

	/**
	 * getSlideshowImages
	 * 
	 * DEPRECIATED
	 * 
	 */
function getSlideshowImages ($org_id)
{
	$sql =
		'SELECT'.
		' photos.photo_title,'.
		' photos.photo_id '.
		'FROM photos, organisation_slideshows AS slideshow '.
		'WHERE slideshow.organisation_slideshow_organisation_entity_id = ?'.
		' AND photos.photo_deleted = 0'.
		' AND slideshow.organisation_slideshow_photo_id = photos.photo_id '.
		'ORDER BY slideshow.organisation_slideshow_order ASC';
	$query = $this->db->query($sql, array($org_id));
	return $query->result_array();
}

function getReviewSlideshowImages ($org_id, $context_id)
{
	$sql =
		'SELECT'.
		' photos.photo_title,'.
		' photos.photo_id '.
		'FROM photos, review_context_slideshows AS slideshow '.
		'WHERE slideshow.review_context_slideshow_organisation_entity_id = ?'.
		' AND slideshow.review_context_slideshow_content_type_id = ? '.
		' AND photos.photo_deleted = 0'.
		' AND slideshow.review_context_slideshow_photo_id = photos.photo_id '.
		'ORDER BY slideshow.review_context_slideshow_order ASC';
	$query = $this->db->query($sql, array($org_id, $context_id));
	return $query->result_array();
}

}