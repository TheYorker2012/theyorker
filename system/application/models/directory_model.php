<?php
/**
 * This model retrieves data from the directory.
 *
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Directory_model extends Model {

	function Directory_model()
	{
		// Call the Model constructor
		parent::Model();
	}

	///	Find the organisations in the directory.
	/**
	 * @return An array of organisations with:
	 *	- ['organisation_name']        (organisations)
	 *	- ['organisation_description'] (organisations)
	 *	- ['organisation_type_name']   (organisation_types)
	 */
	function GetDirectoryOrganisations()
	{
		$sql =
			'SELECT'.
			' organisations.organisation_name,'.
			' organisations.organisation_directory_entry_name,'.
			' organisations.organisation_description,'.
			' organisation_types.organisation_type_name '.
			'FROM organisations '.
			'INNER JOIN organisation_types '.
			'ON organisations.organisation_organisation_type_id=organisation_types.organisation_type_id '.
			'WHERE organisations.organisation_directory_entry_name IS NOT NULL'.
			' AND organisation_types.organisation_type_directory=1 '.
			'ORDER BY organisation_name';

		$query = $this->db->query($sql);

		return $query->result_array();
	}
	
	/// Get an organisation by name.
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 */
	function GetDirectoryOrganisationByEntryName($DirectoryEntryName)
	{
		$sql =
			'SELECT'.
			' organisations.organisation_name,'.
			' organisations.organisation_directory_entry_name,'.
			' organisations.organisation_description,'.
			' organisations.organisation_url,'.
			' organisations.organisation_location,'.
			' organisations.organisation_opening_hours,'.
			' organisation_types.organisation_type_name '.
			'FROM organisations '.
			'INNER JOIN organisation_types '.
			' ON organisations.organisation_organisation_type_id=organisation_types.organisation_type_id '.
			'WHERE organisations.organisation_directory_entry_name=? '.
			' AND organisation_types.organisation_type_directory=1 '.
			'ORDER BY organisation_name';
	
		$query = $this->db->query($sql, $DirectoryEntryName);
	
		return $query->result_array();
	}
	
	/// Get an organisation's business cards.
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @return array[business_card].
	 */
	function GetDirectoryOrganisationCardsByEntryName($DirectoryEntryName)
	{
		$sql =
			'SELECT'.
			' business_cards.business_card_name,'.
			' business_cards.business_card_title,'.
			' business_cards.business_card_blurb,'.
			' business_cards.business_card_email,'.
			' business_cards.business_card_mobile,'.
			' business_cards.business_card_phone_internal,'.
			' business_cards.business_card_phone_external,'.
			' business_cards.business_card_postal_address,'.
			' business_card_colours.business_card_colour_background,'.
			' business_card_colours.business_card_colour_foreground,'.
			' business_card_types.business_card_type_name '.
			'FROM business_cards '.
			'INNER JOIN business_card_colours '.
			' ON business_card_colours.business_card_colour_id = business_cards.business_card_business_card_colour_id '.
			'INNER JOIN business_card_types '.
			' ON business_card_types.business_card_type_id = business_cards.business_card_business_card_type_id '.
			'INNER JOIN organisations '.
			' ON organisations.organisation_entity_id = business_card_types.business_card_type_organisation_entity_id '.
			'INNER JOIN organisation_types '.
			' ON organisations.organisation_organisation_type_id = organisation_types.organisation_type_id '.
			'WHERE business_cards.business_card_deleted = 0 '.
			' AND organisations.organisation_directory_entry_name=? '.
			' AND organisation_types.organisation_type_directory=1 '.
			'ORDER BY business_cards.business_card_name';
	
		$query = $this->db->query($sql, $DirectoryEntryName);
	
		return $query->result_array();
	}
	
	/// Get an organisation's reviews.
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @return array[review].
	 */
	function GetDirectoryOrganisationReviewsByEntryName($DirectoryEntryName)
	{
		$sql =
			'SELECT'.
			' reviews.review_id,'.
			' articles.article_publish_date,'.
			' article_contents.article_content_blurb,'.
			' users.user_firstname,'.
			' users.user_surname,'.
			' users.user_email '.
			//' users.user_image_id '.
			'FROM reviews '.
			'INNER JOIN organisations '.
			' ON organisations.organisation_entity_id = reviews.rto_organisation_entity_id '.
			'INNER JOIN organisation_types '.
			' ON organisations.organisation_organisation_type_id = organisation_types.organisation_type_id '.
			'INNER JOIN articles '.
			' ON reviews.review_article_id = articles.article_id '.
			'INNER JOIN article_contents '.
			' ON articles.article_current_article_content_id = article_contents.article_content_id '.
			'INNER JOIN article_writers '.
			' ON article_contents.article_content_id = article_writers.article_writer_article_content_id '.
			'INNER JOIN users '.
			' ON article_writers.article_writer_user_entity_id = users.user_entity_id '.
			'WHERE organisations.organisation_directory_entry_name=? '.
			' AND organisation_types.organisation_type_directory=1 ';
	
		$query = $this->db->query($sql, $DirectoryEntryName);
		$authors = $query->result_array();
		$reviews = array();
		// Go through all authors, each will have the rest
		foreach ($authors as $data) {
			// If the review isn't there, get the main data
			if (!array_key_exists($data['review_id'], $reviews)) {
				$reviews[$data['review_id']] = array(
					'publish_date' => $data['article_publish_date'],
					'content' => array(
						'blurb' => $data['article_content_blurb'],
					),
					/// @todo Where is this link supposed to point?
					'link' => '/news/archive/reporter/2',
					'authors' => array(),
				);
			}
			// Now add the author
			$reviews[$data['review_id']]['authors'][] = array(
				'name' => $data['user_firstname'].' '.$data['user_surname'],
				'email' => $data['user_email'],
			);
		}
		
		// Return the full array of reviews
		return $reviews;
	}

}
?>