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
			' organisations.organisation_yorkipedia_entry,'.
			' organisations.organisation_postal_address,'.
			' organisations.organisation_email_address,'.
			' organisations.organisation_postcode,'.
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

}
?>