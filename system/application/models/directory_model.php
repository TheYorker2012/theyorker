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
			' AND organisations.organisation_show_in_directory=1 '.
			' AND organisations.organisation_needs_approval=0 '.
			'ORDER BY organisation_name';

		$query = $this->db->query($sql);

		return $query->result_array();
	}
	
	/// Get an organisation by name.
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @param $RevisionNumber integer Revision number to display (Default to false).
	 *
	 * Returns the organisation left joined with the directory content.
	 * This means the directory content is optional and may be set to NULL.
	 */
	function GetDirectoryOrganisationByEntryName($DirectoryEntryName, $RevisionNumber=false)
	{
		$sql =
			'SELECT'.
			' organisations.organisation_entity_id,'.
			' organisations.organisation_name,'.
			' organisations.organisation_directory_entry_name,'.
			' organisation_contents.organisation_content_description as organisation_description,'.
			' organisation_contents.organisation_content_url as organisation_url,'.
			' organisation_contents.organisation_content_opening_hours as organisation_opening_hours,'.
			' organisation_contents.organisation_content_postal_address as organisation_postal_address,'.
			' organisation_contents.organisation_content_email_address as organisation_email_address,'.
			' organisation_contents.organisation_content_postcode as organisation_postcode,'.
			' organisation_contents.organisation_content_phone_internal as organisation_phone_internal,'.
			' organisation_contents.organisation_content_phone_external as organisation_phone_external,'.
			' organisation_contents.organisation_content_fax_number as organisation_fax_number,'.
			' organisation_contents.organisation_content_id as organisation_revision_id,'.
			' organisations.organisation_yorkipedia_entry,'.
			' organisation_types.organisation_type_name,'.
			' organisations.organisation_location_id,'.
			' locations.location_lat, locations.location_lng '.
			'FROM organisations '.
			// Get organisation type, but make sure the type allows directory entries.
			'INNER JOIN organisation_types '.
			' ON	organisations.organisation_organisation_type_id '.
			'			= organisation_types.organisation_type_id '.
			// Optionally get any matching content as well.
			'LEFT JOIN organisation_contents '.
			' ON	organisation_contents.organisation_content_organisation_entity_id '.
			'			= organisations.organisation_entity_id '.
			' AND	organisation_contents.organisation_content_id=';
		$bind_data = array();
		if ($RevisionNumber === false){
			$sql .= 'organisations.organisation_live_content_id';
		} else {
			$sql .= '?';
			$bind_data[] = $RevisionNumber;
		}
		$sql .= ' LEFT JOIN locations '.
			' ON locations.location_id'.
			'			= organisations.organisation_location_id';
		$sql .= ' WHERE organisations.organisation_directory_entry_name=? '.
			' AND organisation_types.organisation_type_directory=1 '.
			'ORDER BY organisation_name';
		$bind_data[] = $DirectoryEntryName;
		$query = $this->db->query($sql, $bind_data);
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
			' business_cards.business_card_user_entity_id,'.
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
			' business_cards.business_card_user_entity_id,'.
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
	function GetRevisonsOfDirectoryEntry($DirectoryEntryName, $showall=false)
	{
		//Find the differant revisions
		$sql =
			'SELECT'.
			'	organisations.organisation_live_content_id, '.
			'	organisation_contents.organisation_content_id, '.
			'	organisation_contents.organisation_content_last_author_timestamp, '.
			'	organisation_contents.organisation_content_deleted, '.
			'	users.user_firstname, '.
			'	users.user_surname '.
			'FROM	organisations '.
			'INNER JOIN organisation_contents '.
			'	ON	organisation_contents.organisation_content_organisation_entity_id '.
			'	=	organisations.organisation_entity_id '.
			'INNER JOIN users '.
			'	ON	users.user_entity_id '.
			'	=	organisation_contents.organisation_content_last_author_user_entity_id '.
			'WHERE	organisations.organisation_directory_entry_name=? ';
			if($showall==true){
			}else{
			$sql .= 'AND organisation_contents.organisation_content_deleted=0 ';
			}
			$sql .= 'ORDER BY organisation_content_last_author_timestamp';
		$query = $this->db->query($sql, $DirectoryEntryName);
		$query_array = $query->result_array();
		$data = array();
		foreach ($query_array as $row){
			$live = ($row['organisation_content_id'] == $row['organisation_live_content_id']);
			$data[] = array(
				'id'		=> $row['organisation_content_id'],
				'author'	=> $row['user_firstname'].' '.$row['user_surname'],
				'published'	=> $live,
				'deleted' => $row['organisation_content_deleted'],
				'timestamp'	=> $row['organisation_content_last_author_timestamp']
			);
		}
		return $data;
	}

	function UpdateDirectoryEntryLocation($DirectoryEntryName, $locationid, $lat, $lng) {
		if ($locationid === null) {
			$this->db->trans_start();
			$sql = 'INSERT INTO locations (location_lat, location_lng)
					VALUES (?, ?)';
			$query = $this->db->query($sql, array($lat, $lng));
			$sql = 'UPDATE organisations
					SET organisation_location_id = LAST_INSERT_ID()
					WHERE organisation_directory_entry_name = ?';
			$query = $this->db->query($sql, array($DirectoryEntryName));
		} else {
			$sql = 	'UPDATE locations
					SET location_lat = ?, location_lng = ?
					WHERE location_id = ?;';
			$query = $this->db->query($sql, array($lat, $lng, $locationid));
		}
		return ($this->db->affected_rows() > 0);
	}
	
	function PublishDirectoryEntryRevisionById($DirectoryEntryName, $id)
	{
		$sql =
		'UPDATE organisations SET'.
		' organisations.organisation_live_content_id='.$id.' '.
		'WHERE organisations.organisation_directory_entry_name=? ';
		$query = $this->db->query($sql, $DirectoryEntryName);
		return ($this->db->affected_rows() > 0);
	}
	
	/// Add a directory entry revision
	/**
	 * @param $DirectoryEntryName string Directory entry name of org.
	 * @param $Data array Data of revision.
	 * @return bool Whether a row was successfully added or not.
	 */
	function AddDirectoryEntryRevision($DirectoryEntryName, $Data, $NoUserEntity=false)
	{
		$sql = 'INSERT INTO `organisation_contents` ('.
			'`organisation_content_last_author_user_entity_id`,'.
			'`organisation_content_last_author_timestamp`,'.
			'`organisation_content_organisation_entity_id`,'.
			'`organisation_content_description`,'.
			'`organisation_content_postal_address`,'.
			'`organisation_content_postcode`,'.
			'`organisation_content_phone_external`,'.
			'`organisation_content_phone_internal`,'.
			'`organisation_content_fax_number`,'.
			'`organisation_content_email_address`,'.
			'`organisation_content_url`,'.
			'`organisation_content_opening_hours`) '.
			'SELECT'.
			'	?,'. // author enitity id
			'	NOW(),'. // timestamp
			'	organisations.organisation_entity_id,'. // organisation_id
			'	?, ?, ?, ?, ?, ?, ?, ?, ? '.
			'FROM organisations '.
			'WHERE organisations.organisation_directory_entry_name = ?';
		if($NoUserEntity){
			//This exists for suggestions, they have no entity id stored.
			$EntityId = 0;
		}else{
			$EntityId = $this->user_auth->entityId;
		}
		$query = $this->db->query($sql, array(
			$EntityId,
			$Data['description'],
			$Data['postal_address'],
			$Data['postcode'],
			$Data['phone_external'],
			$Data['phone_internal'],
			$Data['fax_number'],
			$Data['email_address'],
			$Data['url'],
			$Data['opening_hours'],
			$DirectoryEntryName));
		return ($this->db->affected_rows() > 0);
	}
	
	function IsRevisionPublished($DirectoryEntryName, $id)
	{
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
		
		return ($id == $liveid);
	}
	
	function IsRevisionDeleted($id)
	{
		$sql =
			'SELECT'.
			' organisation_contents.organisation_content_deleted '.
			'FROM organisation_contents '.
			'WHERE organisation_content_id=? '.
			'LIMIT 1';
		$query = $this->db->query($sql, $id);
		$row = $query->row();
		//Get org id to look for and the one that should be live.
		$is_deleted = $row->organisation_content_deleted;
		
		return $is_deleted;
	}
	
	/// Removes a revisons of a directory entry
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @param $id the id of the revision to remove
	 * @return bool Whether any organisation contents were removed.
	 */
	function DeleteEntryRevisionById($DirectoryEntryName, $id)
	{
		// Remove the organisation content with the given id and which is not live.
		$sql =
			'DELETE FROM organisation_contents '.
			'USING organisation_contents, organisations '.
			'WHERE	organisation_contents.organisation_content_id = ? '.
			// Join to organisations table
			'	AND	organisation_contents.organisation_content_organisation_entity_id '.
			'		= organisations.organisation_entity_id '.
			// Ensure that it ISN'T live
			'	AND organisations.organisation_live_content_id '.
			'		!= organisation_contents.organisation_content_id '.
			// And that the directory entry name actually matches
			'	AND organisations.organisation_directory_entry_name = ?';
		$query = $this->db->query($sql, array($id, $DirectoryEntryName));
		return ($this->db->affected_rows() > 0);
	}
	function FlagEntryRevisionAsDeletedById($DirectoryEntryName, $id, $value=true)
	{
		if($value==false){
			$value = 0;
		}else{
			$value = 1;
		}
		$sql =
		'UPDATE organisation_contents, organisations '.
		'SET organisation_content_deleted='.$value.' '.
		'WHERE	organisation_contents.organisation_content_id = ? '.
			// Join to organisations table
			'	AND	organisation_contents.organisation_content_organisation_entity_id '.
			'		= organisations.organisation_entity_id '.
			// Ensure that it ISN'T live
			'	AND organisations.organisation_live_content_id '.
			'		!= organisation_contents.organisation_content_id '.
			// And that the directory entry name actually matches
			'	AND organisations.organisation_directory_entry_name = ?';
		$query = $this->db->query($sql, array($id, $DirectoryEntryName));
		return ($this->db->affected_rows() > 0);
	}
	
	//Finds out if the directory entry is listed in the directory
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @return bool true if the directory entry is listed in the directory.
	 **/
	function IsEntryShownInDirectory($DirectoryEntryName)
	{
		// Remove the organisation content with the given id and which is not live.
		$sql =
			'SELECT'.
			' organisations.organisation_show_in_directory '.
			'FROM organisations '.
			'WHERE organisations.organisation_directory_entry_name=? '.
			'LIMIT 1';
		$query = $this->db->query($sql, $DirectoryEntryName);
		$row = $query->row();
		//Check result to find out if its in the directory and return value.
		$in_directory = $row->organisation_show_in_directory;
		if($in_directory == 1){
			return true;
		}else{
			return false;
		}
	}
	//Updates the visiblity of a directory entry in the directory
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @param $valuee true to make the directory entry visible false to hide it.
	 * @return bool true if the directory entry is listed in the directory.
	 **/
	function MakeDirectoryEntryVisible($DirectoryEntryName, $value=1)
	{
		// If $value is set to false the directory will not be visible, defaults to showing the organisation in the directory.
		if($value == false){
			$value = 0;
		}else{
			$value = 1;
		}
		$sql =
		'UPDATE organisations SET'.
		' organisation_show_in_directory='.$value.' '.
		'WHERE organisations.organisation_directory_entry_name=? ';
		$query = $this->db->query($sql, $DirectoryEntryName);
		return true;
	}

	//Updates the directory entry long and short names
	/**
	*@param $DirectoryEntryName string Directory entry name of the organisation.
	*@param $NewDirectoryEntryLongName string full text name of the directory entry
	*@param $NewDirectoryEntryName string must be valid for a url, must be unique! process the long name to make a short name.
	*@return true if successful
	**/
	function UpdateDirctoryEntryNames($DirectoryEntryName, $NewDirectoryEntryLongName, $NewDirectoryEntryName)
	{
		$sql =
		'UPDATE organisations SET'.
		' organisations.organisation_name=?,'.
		' organisations.organisation_directory_entry_name=? '.
		'WHERE organisations.organisation_directory_entry_name=? ';
		$query = $this->db->query($sql, array($NewDirectoryEntryLongName, $NewDirectoryEntryName, $DirectoryEntryName));
		return ($this->db->affected_rows() > 0);
	}
	
	//Updates the directory entry type
	/**
	*@param $DirectoryEntryName string Directory entry name of the organisation.
	*@param $TypeId int of the organisation type
	*@return true if successful
	**/
	function UpdateDirctoryEntryType($DirectoryEntryName, $TypeId)
	{
		$sql =
		'UPDATE organisations SET'.
		' organisations.organisation_organisation_type_id='.$TypeId.' '.
		'WHERE organisations.organisation_directory_entry_name=? ';
		$query = $this->db->query($sql, $DirectoryEntryName);
		return ($this->db->affected_rows() > 0);
	}
    
    //Gets all organisation types in the directory
	/**
	 * @return array of organisations with their organisation_type_name, organisation_type_id, organisation_type_codename
	 **/
	function GetOrganisationTypes()
	{
		$sql =
			'SELECT'.
			' organisation_types.organisation_type_name,'.
			' organisation_types.organisation_type_id, '.
			' organisation_types.organisation_type_codename '.
			'FROM organisation_types '.
			'WHERE organisation_types.organisation_type_directory=1 '.
			'ORDER BY organisation_types.organisation_type_name';
	
		$query = $this->db->query($sql);
	
		return $query->result_array();
	}

    //Creates an unaproved a organisation in the directory
	/**
	 * @param $type_id int Organisation type id.
	 * @param $name string Display name of the organisation.
         	 * @param $directory_entry_name string with no spaces or non alphanumerical symbols .
            * @param $suggestors_name name of person suggesting the organisation .
            * @param $suggestors_position position of the person suggesting the organisation .
            * @param $suggestors_email email address of the suggestor .
            * @param $suggestors_notes any notes from the suggestor .
	 * @return bool true if the organisation is listed in the directory.
	 **/
	function AddDirectoryEntry($Data)
	{
		$sql = 'INSERT INTO `organisations` ('.
			'`organisation_organisation_type_id`,'.
			'`organisation_name`,'.
			'`organisation_directory_entry_name`,'.
			'`organisation_suggesters_name`,'.
			'`organisation_suggesters_position`,'.
            '`organisation_suggesters_email`,'.
            '`organisation_suggesters_notes`,'.
			'`organisation_needs_approval`) '.
			' VALUES'.
			' (?, ?, ?, ?, ?, ?, ?, ? )';
		$query = $this->db->query($sql, array(
			$Data['type_id'],
			$Data['name'],
			$Data['directory_entry_name'],
			$Data['suggestors_name'],
			$Data['suggestors_position'],
            $Data['suggestors_email'],
            $Data['suggestors_notes'],
			1
			));
		return ($this->db->affected_rows() > 0);
	}
}
?>
