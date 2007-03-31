<?php
class Contact_Model extends Model {
	//Default constructor
	function Contact_Model() {
		parent::Model();
	}

	function InsertContact ($name,$email,$description) {
	//Inserts a contact to the contact_us table
		$sql = 'INSERT INTO contact_us
				(contact_us_name,
				contact_us_email,
				contact_us_description)
			VALUES (?,?,?)';
		$query = $this->db->query($sql, array($name,$email,$description));
	}

	function UpdateContact ($name =  null,$email = null,$description = null) {
	//Updates a contact currently in the contact_us table
		$sql = 'UPDATE contact_us SET ';
		$params = array();
		if ($name != null) {
			$sql = $sql.'contact_us_name = ?,';
			$params[] = $name;
		}
		if ($email != null) {
			$sql = $sql.'contact_us_email = ?,';
			$params[] = $email;
		}
		if ($description != null) {
			$sql = $sql.'contact_us_description = ?,';
			$params[] = $description;
		}
		$sql = rtrim($sql,',');
		$sql = $sql.' WHERE contact_us_id = ?';
		if (sizeof($params) != 0){
			$params[] = $id;
			$query = $this->db->query($sql,params);
		}
	}

	function DeleteContact ($id) {
	//Removes a contact from the contact_us table
		$sql = 'DELETE FROM contact_us WHERE contact_us_id = ?';
		$query = $this->db->query($sql,array($id));
	}

	function GetAllContacts () {
	//Returns an array containg id, name, email and description of contacts
		$sql = 'SELECT	contact_us_name,
				contact_us_email,
				contact_us_description
			FROM	contact_us';
		$query = $this->db->query($sql);
		$result = array();
		foreach ($query->result() as $row) {
			$result[] = array('name' => $row->contact_us_name,
					'email' => $row->contact_us_email,
					'description' => $row->contact_us_description);
		}
		return $result;
	}
}
?>
