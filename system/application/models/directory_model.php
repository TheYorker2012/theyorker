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
			' organisation_contents.organisation_content_description as organisation_description,'.
			' organisation_types.organisation_type_name '.
			'FROM organisations '.
			'INNER JOIN organisation_types '.
			'ON organisations.organisation_organisation_type_id=organisation_types.organisation_type_id '.
			'INNER JOIN organisation_contents '.
			'ON organisations.organisation_live_content_id = organisation_contents.organisation_content_id '.
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
			' organisations.organisation_entity_id,'.
			' organisations.organisation_name,'.
			' organisations.organisation_directory_entry_name,'.
			' organisation_contents.organisation_content_description as organisation_description,'.
			' organisation_contents.organisation_content_url as organisation_url,'.
			' organisation_contents.organisation_content_location as organisation_location,'.
			' organisation_contents.organisation_content_opening_hours as organisation_opening_hours,'.
			' organisation_contents.organisation_content_postal_address as organisation_postal_address,'.
			' organisation_contents.organisation_content_email_address as organisation_email_address,'.
			' organisation_contents.organisation_content_postcode as organisation_postcode,'.
			' organisation_contents.organisation_content_phone_internal as organisation_phone_internal,'.
			' organisation_contents.organisation_content_phone_external as organisation_phone_external,'.
			' organisation_contents.organisation_content_fax_number as organisation_fax_number,'.
			' organisations.organisation_yorkipedia_entry,'.
			' organisation_types.organisation_type_name '.
			'FROM organisations '.
			'INNER JOIN organisation_types '.
			' ON organisations.organisation_organisation_type_id=organisation_types.organisation_type_id '.
			'INNER JOIN organisation_contents '.
			' ON organisations.organisation_live_content_id = organisation_contents.organisation_content_id '.
			'WHERE organisations.organisation_directory_entry_name=? '.
			' AND organisation_types.organisation_type_directory=1 '.
			'ORDER BY organisation_name';
	
		$query = $this->db->query($sql, $DirectoryEntryName);
	
		return $query->result_array();
	}
	
	/// Get an organisation's business cards, for a business card group.
	/**
	 * @param $BusinessCardGroupId business card group to return
	 * @return array[business_card].
	 */
	function GetDirectoryOrganisationCardsByGroupId($BusinessCardGroupId)
	{
		$sql =
			'SELECT'.
			' business_cards.business_card_id,'.
			' business_cards.business_card_image_id,'.
			' business_cards.business_card_name,'.
			' business_cards.business_card_title,'.
			' business_cards.business_card_course,'.
			' business_cards.business_card_blurb,'.
			' business_cards.business_card_email,'.
			' business_cards.business_card_mobile,'.
			' business_cards.business_card_phone_internal,'.
			' business_cards.business_card_phone_external,'.
			' business_cards.business_card_postal_address,'.
			' business_card_groups.business_card_group_name '.
			'FROM business_cards '.
			'INNER JOIN business_card_groups '.
			' ON business_card_groups.business_card_group_id = business_cards.business_card_business_card_group_id '.
			'WHERE business_cards.business_card_deleted = 0 '.
			'AND business_card_groups.business_card_group_id = ? '.
			'ORDER BY business_cards.business_card_order';
	
		$query = $this->db->query($sql, $BusinessCardGroupId);
	
		return $query->result_array();
	}

	/// Get an individual business card
	/**
	 * @param $BusinessCardId business card to return
	 * @return array[business_card].
	 */
	function GetDirectoryOrganisationCardsById($BusinessCardId)
	{
		$sql =
			'SELECT'.
			' business_cards.business_card_id,'.
			' business_cards.business_card_image_id,'.
			' business_cards.business_card_name,'.
			' business_cards.business_card_title,'.
			' business_cards.business_card_course,'.
			' business_cards.business_card_business_card_group_id,'.
			' business_cards.business_card_blurb,'.
			' business_cards.business_card_email,'.
			' business_cards.business_card_mobile,'.
			' business_cards.business_card_phone_internal,'.
			' business_cards.business_card_phone_external,'.
			' business_cards.business_card_postal_address,'.
			' organisations.organisation_directory_entry_name '.
			'FROM business_cards '.
			'INNER JOIN business_card_groups '.
			' ON business_card_groups.business_card_group_id = business_cards.business_card_business_card_group_id '.
			'INNER JOIN organisations '.
			' ON business_card_groups.business_card_group_organisation_entity_id = organisations.organisation_entity_id '.
			'WHERE business_cards.business_card_deleted = 0 '.
			'AND business_cards.business_card_id = ? '.
			'LIMIT 1';
	
		$query = $this->db->query($sql, $BusinessCardId);
	
		return $query->result_array();
	}
	
	/// Get an organisation's business card groups.
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @return array[business_card_group].
	 */
	function GetDirectoryOrganisationCardGroups($DirectoryEntryName)
	{
		$sql =
			'SELECT'.
			' business_card_groups.business_card_group_name, '.
			' business_card_groups.business_card_group_id '.
			'FROM business_card_groups '.
			'INNER JOIN organisations '.
			' ON organisations.organisation_entity_id = business_card_groups.business_card_group_organisation_entity_id '.
			'WHERE organisations.organisation_directory_entry_name=? '.
			'ORDER BY business_card_groups.business_card_group_order';
	
		$query = $this->db->query($sql, $DirectoryEntryName);
	
		return $query->result_array();
	}

	/// Get an organisation's business card groups.
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @return array[business_card_group].
	 */
	function UpdateOrganisationDetails($DirectoryEntryName, $Data)
	{
		$sql =
			'SELECT'.
			' organisations.organisation_entity_id '.
			'FROM organisations '.
			'WHERE organisations.organisation_directory_entry_name=? ';
		$query = $this->db->query($sql, $DirectoryEntryName);
		$row = $query->row();
		$id = $row->organisation_entity_id;
		
		$sql2 =
			'UPDATE organisation_contents SET'.
			' organisation_contents.organisation_content_description=?, '.
			' organisation_contents.organisation_content_location=?, '.
			' organisation_contents.organisation_content_postal_address=?, '.
			' organisation_contents.organisation_content_postcode=?, '.
			' organisation_contents.organisation_content_phone_external=?, '.
			' organisation_contents.organisation_content_phone_internal=?, '.
			' organisation_contents.organisation_content_fax_number=?, '.
			' organisation_contents.organisation_content_email_address=?, '.
			' organisation_contents.organisation_content_url=?, '.
			' organisation_contents.organisation_content_opening_hours=? '.
			'WHERE organisation_contents.organisation_content_organisation_entity_id=? ';
	
		$query2 = $this->db->query($sql2, array($Data['description'], $Data['location'], $Data['postal_address'], $Data['postcode'], $Data['phone_external'], $Data['phone_internal'], $Data['fax_number'], $Data['email_address'], $Data['url'], $Data['opening_hours'], $id));
	
		return true;
	}

}
?>