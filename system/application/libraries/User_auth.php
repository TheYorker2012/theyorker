<?php 
 
class User_auth {
	public $isLoggedIn
	public $username;
	public $entityId;
	private $salt;

	public $isUser;

	public $officeLogin = false;

	public $firstname;
	public $surname;

	public $permissions;

	public function __construct() {
		session_start();
		$this->isLoggedIn = isset[$_SESSION['username'];

		if ($this->isLoggedIn) {
			$this->username = $_SESSION['username'];
			$this->entityId = $_SESSION['entityId']
		} elseif (isset($_COOKIE['SavedLogin'])) {
			try {
				$details = explode(':$:', $_COOKIE['SavedLogin']);
				if (count($details) == 2) {
					loginByHash($details[0], $details[1], true);
				} elseif (count($details) == 1) {
					$this->$username = details[0];
				}
			}
		}
	}

	private function loginByHash($username, $hash, $savelogin) {
		$sql = 'SELECT entity_id, entity_salt FROM entities WHERE entity_username = ? AND entity_password = ? AND entity_deleted = FALSE';
		$query = $this->object->db->query($sql, array($username, $hash));
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			
			$this->salt = $row->entity_salt;
			loginAuthed($username, $row->entity_id, $savelogin);
		} else {
			throw new Exception('Invalid username or password');
		}
	}

	private function loginAuthed($username, $entityId, $savelogin) {
		$this->isLoggedIn = true;
		$this->username = $username;
		$this->entityId = $entityId;

		$_SESSION['username'] = $username;
		$_SESSION['entityId'] = $entityId;

		if ($savelogin) {
			setcookie('SavedLogin', implode(':$:', $username, $hash);
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

	public function login($username, $password, $savelogin) {
		$sql = 'SELECT entity_id, entity_username, entity_password, entity_salt FROM entities WHERE entity_username = ? AND entity_deleted = FALSE';
		$query = $this->object->db->query($sql, array($username));
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$this->salt = $row->entity_salt;
			$hash = sha1($row->entity_salt.$password);
			if ($hash == $row->entity_password) {
				loginAuthed($username, $row->entity_id, $savelogin);
			} else {
				throw new Exception('Invalid password');
			}
		} else {
			throw new Exception('User does not exist');
		}
	}

	public function logout() {
		if (isset($_COOKIE['SavedLogin'])) {
			setcookie('SavedLogin', $username);
		}
		$this->isLoggedIn = false;
		$this->officeLogin = false;
	}

	public function loginOffice($password) {
		$hash = sha1($this->salt.$password);
		$sql = 'SELECT COUNT(*) AS Valid FROM users WHERE user_entity_id = ? AND user_office_password = ?';
		$query = $this->object->db->query($sql, array($this->entityId, $hash));

		$row = $query->row();
		if ($row->Valid == 1) {
			$this->officeLogin = true;
		}
	}

	public function logoutOffice() {
		$this->officeLogin = false;
	}
}

?>
