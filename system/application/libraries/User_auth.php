<?php 
 
class User_auth{
	public $isLoggedIn
	public $username;
	public $entityId;

	public $isUser;

	public $firstname;
	public $surname;

	public $permissions;

	public function __construct() {
		session_start();
		$this->isLoggedIn = isset[$_SESSION['username'];

		if ($this->isLoggedIn) {
			$this->username = $_SESSION['username'];
			$this->entityId = $_SESSION['entityId']
		}
	}

	public function login($username, $password) {
		$sql = 'SELECT entity_id, entity_username, entity_password, entity_salt FROM entities WHERE entity_username = ? AND entity_deleted = FALSE';
		$query = $this->object->db->query($sql, array($username));
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$hash = sha1($row->entity_salt.$password);
			if ($hash == $row->entity_password) {
				$this->isLoggedIn = true;
				$this->username = $username;
				$this->entityId = $row->entity_id;

				$_SESSION['username'] = $username;
				$_SESSION['entityId'] = $entityId;
			} else {
				throw new Exception('Invalid password');
			}
		} else {
			throw new Exception('User does not exist');
		}

		if ($this->isLoggedIn) {
			$sql = 'SELECT user_firstname, user_surname, user_permission FROM users WHERE user_entity_id = ?';
			$query = $this->object->db->query($sql, array($this->entityId));

			if ($query->num_rows() > 0) {
				$this->isUser = true
				$row = $query->row();
				
				$this->firstname = $row->user_firstname;
				$this->surname = $row->user_surname;
				$this->permissions = $row->permissions;
			} else {
				$this->isUser = false;
				$sql = 'SELECT organisation_name FROM organisations WHERE organisation_entity_id = ?';
				$query = $this->object->db->query($sql, array($this->entityId));

				$this->firstname = $row->organisation_name;
			}
		}
	}
}

?>
