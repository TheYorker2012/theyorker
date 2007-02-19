<?php
/**
 * This model retrieves data from the directory.
 *
 * @author James Hogan (jh559@cs.york.ac.uk)
  * @author Owen Jones (oj502@cs.york.ac.uk)
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
	function GetDirectoryOrganisationByEntryName($DirectoryEntryName, $revision=false)
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
			' organisation_contents.organisation_content_id as organisation_revision_id,'.
			' organisations.organisation_yorkipedia_entry,'.
			' organisation_types.organisation_type_name '.
			'FROM organisations '.
			'INNER JOIN organisation_types '.
			' ON organisations.organisation_organisation_type_id=organisation_types.organisation_type_id '.
			'INNER JOIN organisation_contents ';
			if ($revision==false){
				$sql .= ' ON  organisation_contents.organisation_content_id=organisations.organisation_live_content_id '.
				'WHERE organisations.organisation_directory_entry_name=? '.
				' AND organisation_types.organisation_type_directory=1 '.
				'ORDER BY organisation_name';
				$query = $this->db->query($sql, $DirectoryEntryName);
			} else {
				$sql .= ' ON  organisation_contents.organisation_content_id=? '.
				'WHERE organisations.organisation_directory_entry_name=? '.
				' AND organisation_types.organisation_type_directory=1 '.
				'ORDER BY organisation_name';
				$query = $this->db->query($sql, array($revision, $DirectoryEntryName));
			}
			
	
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

	//Get revisons of a directory entry
	/*
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @return An array of revisions
	*/
	function GetRevisonsOfDirectoryEntry($DirectoryEntryName)
	{
		//Find the differant revisions
		$sql =
			'SELECT'.
			'	organisations.organisation_live_content_id, '.
			'	organisation_contents.organisation_content_id, '.
			'	organisation_contents.organisation_content_last_author_timestamp, '.
			'	users.user_firstname, '.
			'	users.user_surname '.
			'FROM	organisations '.
			'INNER JOIN organisation_contents '.
			'	ON	organisation_contents.organisation_content_organisation_entity_id '.
			'	=	organisations.organisation_entity_id '.
			'INNER JOIN users '.
			'	ON	users.user_entity_id '.
			'	=	organisation_contents.organisation_content_last_author_user_entity_id '.
			'WHERE	organisations.organisation_directory_entry_name=? '.
			'ORDER BY organisation_content_last_author_timestamp';
		$query = $this->db->query($sql, $DirectoryEntryName);
		$query_array = $query->result_array();
		$data = array();
		foreach ($query_array as $row){
			if($row['organisation_content_id'] == $row['organisation_live_content_id'){
				$live = true;
			}else{
				$live = false;
			}
			$data[] = array(
				'id'          => $row['organisation_content_id'],
				'author'          => $row['user_firstname'].' '.$row['user_surname'],
				'published'        => $live,
				'timestamp'        => $row['organisation_content_last_author_timestamp']
			);
		}
		return $data;
	}
	
	function PublishDirectoryEntryRevisionById($DirectoryEntryName, $id)
	{
		$sql =
		'UPDATE organisations SET'.
		' organisations.organisation_live_content_id='.$id.' '.
		'WHERE organisations.organisation_directory_entry_name=? ';
		$query = $this->db->query($sql, $DirectoryEntryName);
		return true;
	}
	
	function AddDirectoryEntryRevision($DirectoryEntryName, $Data)
	{
		//Get Org Id from name
		$sql =
			'SELECT'.
			' organisations.organisation_entity_id '.
			'FROM organisations '.
			'WHERE organisations.organisation_directory_entry_name=? ';
		$query = $this->db->query($sql, $DirectoryEntryName);
		$row = $query->row();
		
		$author_id =  $this->user_auth->entityId;
		$organisation_id = $row->organisation_entity_id;
		//Add entry now we have all the data
		$sql = 'INSERT INTO `organisation_contents`'.
		'(`organisation_content_id`,'.
		'`organisation_content_last_author_user_entity_id`,'.
		'`organisation_content_last_author_timestamp`,'.
		'`organisation_content_organisation_entity_id`,'.
		'`organisation_content_description`,'.
		'`organisation_content_location`,'.
		'`organisation_content_postal_address`,'.
		'`organisation_content_postcode`,'.
		'`organisation_content_phone_external`,'.
		'`organisation_content_phone_internal`,'.
		'`organisation_content_fax_number`,'.
		'`organisation_content_email_address`,'.
		'`organisation_content_url`,'.
		'`organisation_content_opening_hours`)'.
		' VALUES '.
		'(NULL, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);';
		$query = $this->db->query($sql, array($author_id, $organisation_id, $Data['description'], $Data['location'], $Data['postal_address'], $Data['postcode'], $Data['phone_external'], $Data['phone_internal'], $Data['fax_number'], $Data['email_address'], $Data['url'], $Data['opening_hours']));
		return true;
	}
	function IsRevisionPublished($DirectoryEntryName, $id){
		$sql =
			'SELECT'.
			' organisations.organisation_live_content_id '.
			'FROM organisations '.
			'WHERE organisations.organisation_directory_entry_name=? '.
			'LIMIT 1';
		$query = $this->db->query($sql, $DirectoryEntryName);
		$row = $query->row();
		//Get org id to look for and the one that should be live.
		$liveid = $row->organisation_live_content_id;
		
		if($id==$liveid){
			return true;
		}else{
			return false;
		}
	}
	//Removes a revisons of a directory entry
	/*
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @return $id the id of the revision to remove
	 * Will return true if removed and false if not removed, will not remove a live revision as it will break the site!!
	*/
	function DeleteEntryRevisionById($DirectoryEntryName, $id)
	{
		if($this->IsRevisionPublished($DirectoryEntryName, $id)){
			return false;
		}else{
			$sql =
			'DELETE FROM organisation_contents '.
			'WHERE organisation_content_id='.$id.' '.
			'LIMIT 1';
			$query = $this->db->query($sql);
			return true;
		}
	}
}
?>