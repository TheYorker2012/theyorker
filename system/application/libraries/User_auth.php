<?php 
// User authentication class by Andrew Oakley (ado500)

// TODO: change comment styles to JavaDoc

// This library is automatically loaded and provides the login status of users.
//  Currently information is avaliable as member variables.  This may be
//  changed to accessors at some point, but if this happens I will update all
//  pages in the SVN to use them at the same time.  
class User_auth {

	// True if the user is logged in
	public $isLoggedIn;

	// The username of the logged in user
	public $username;

	// The entityId of the logged in user
	public $entityId;

	// True if the user is an actual user rather than an organisation
	public $isUser;
	
	// True if the user has logged into the office
	public $officeLogin = false;
	
	// The firstname of the logged in user
	public $firstname;

	// The surname of the logged in user
	public $surname;
	
	// The permission of the logged in user
	public $permissions;

	// The organisation (if any) the user has logged into the admin for
	public $organisationLogin = -1;

	// The salt used to generate the password hash
	private $salt;

	// The code igniter object
	private $object;
	
	// The default constructor
	public function __construct() {
		$this->object = &get_instance();

		// Ensure we have a session
		session_start();

		// Check if we already have login details
		if (isset($_SESSION['ua_loggedin'])) {
			$this->isLoggedIn = $_SESSION['ua_loggedin'];
			$this->username = $_SESSION['ua_username'];
			$this->entityId = $_SESSION['ua_entityId'];
			$this->isUser = $_SESSION['ua_isuser'];
			$this->officeLogin = $_SESSION['ua_isoffice'];
			$this->firstname = $_SESSION['ua_firstname'];
			$this->surname = $_SESSION['ua_surname'];
			$this->permissions = $_SESSION['ua_permissions'];
			$this->organisationLogin = $_SESSION['ua_organisation'];
			$this->salt = $_SESSION['ua_salt'];
		}

		if (!$this->isLoggedIn && isset($_COOKIE['SavedLogin'])) {
			// Try to perform the login from a cookie
			try {
				$details = explode(
					':$:', 
					$_COOKIE['SavedLogin']
				);
				if (count($details) == 2) {
					// We have a username and has, login
					loginByHash(
						$details[0], 
						$details[1], 
						true
					);
				} elseif (count($details) == 1) {
					// We just have the username
					$this->username = $details[0];
				}
			} catch (Exception $e) {
				// Failing is fine
			}
		}
	}
	
	// Attempts to log a user in based on a username and hash
	private function loginByHash($username, $hash, $savelogin) {
		$sql = 'SELECT entity_id, entity_salt 
			FROM entities 
			WHERE entity_username = ? 
				AND entity_password = ? 
				AND entity_deleted = FALSE';

		$db = $this->object->db;
		$query = $db->query($sql, array($username, $hash));
		
		// See if there was a result for this username and hash
		if ($query->num_rows() > 0) {
			$row = $query->row();
			
			// Get the salt
			$this->salt = $row->entity_salt;

			// Perform actual login
			loginAuthed(
				$username, 
				$row->entity_id, 
				$savelogin, 
				$hash
			);
		} else {
			throw new Exception('Invalid username or password');
		}
	}
	
	// Performs the actual login once a user is authenticated
	private function loginAuthed($username, $entityId, $savelogin, $hash) {
		$this->isLoggedIn = true;
		$this->username = $username;
		$this->entityId = $entityId;
		$this->officeLogin = false;
		
		// Create a new session to prevent hijacking of sessions
		session_regenerate_id(true);
		
		// Set a cookie if appropriate
		if ($savelogin) {
			setcookie(
				'SavedLogin', 
				implode(':$:', array($username, $hash))
			);
		}
		
		$sql = 'SELECT user_firstname, user_surname, user_permission 
			FROM users 
			WHERE user_entity_id = ?';

		$db = $this->object->db;
		$query = $db->query($sql, array($this->entityId));
		
		// See if there was a result (i.e. we have a user)
		if ($query->num_rows() > 0) {
			$row = $query->row();
			
			$this->isUser = true;
			$this->firstname = $row->user_firstname;
			$this->surname = $row->user_surname;
			$this->permissions = $row->user_permission;
		} else {
			$sql = 'SELECT organisation_name 
				FROM organisations 
				WHERE organisation_entity_id = ?';

			$query = $db->query($sql, array($this->entityId));

			$this->isUser = false;
			$this->firstname = $row->organisation_name;
			$this->surname = '';
			$this->permissions = 0;
		}
		
		// Set session variables to persist information
		$this->localToSession();
	}
	
	// Login based on username and password
	public function login($username, $password, $savelogin) {
		$sql = 'SELECT entity_id, entity_username, entity_password, 
				entity_salt 
			FROM entities 
			WHERE entity_username = ? AND entity_deleted = FALSE';
		
		$db = $this->object->db;
		$query = $db->query($sql, array($username));
		
		// See if we have an entity with this username
		if ($query->num_rows() > 0) {
			$row = $query->row();
			
			// Store the salt for any further login
			$this->salt = $row->entity_salt;

			// Create a (badly salted) hash
			$hash = sha1($row->entity_salt.$password);

			if ($hash == $row->entity_password) {
				// The hashes match, login
				$this->loginAuthed(
					$username, 
					$row->entity_id, 
					$savelogin, 
					$hash
				);

			} else {
				throw new Exception('Invalid password');
			}
		} else {
			throw new Exception('User does not exist');
		}
	}
	
	// Logout of the site
	public function logout() {
		// Change the cookie not to store the password, if present
		if (isset($_COOKIE['SavedLogin'])) {
			setcookie('SavedLogin', $username);
		}

		// Set login status to 'not logged in'.  Username is left for
		//  login form.  
		$this->isLoggedIn = false;
		$this->entityId = -1;
		$this->officeLogin = false;
		$this->firstname = '';
		$this->surname = '';
		$this->permissions = 0;
		$this->organisationLogin = -1;
		$this->salt = '';

		// Save values in session
		$this->localToSession();
	}
	
	// Login to the yorker office
	public function loginOffice($password) {
		// TODO: fix for those who have same password, rather than a 
		//  secondary password
		$hash = sha1($this->salt.$password);

		$sql = 'SELECT COUNT(*) AS Valid 
			FROM users 
			WHERE user_entity_id = ? AND user_office_password = ?';
		
		$db = $this->object->db;
		$query = $db->query($sql, array($this->entityId, $hash));

		$row = $query->row();
		if ($row->Valid == 1) {
			$this->officeLogin = true;
			$this->localToSession();
		}
	}
	
	// Logout of the yorker office
	public function logoutOffice() {
		$this->officeLogin = false;
		$this->localToSession();
	}

	// Login to an organisations admin interface
	public function loginOrganisation() {
		// TODO: implement this :)
	}

	// Logout of an organisation interface
	public function logoutOrganisation() {
		// TODO: implement this :)
	}
	
	// Save all data from this class in the session
	private function localToSession() {
		$_SESSION['ua_loggedin'] = $this->isLoggedIn;
		$_SESSION['ua_username'] = $this->username;
		$_SESSION['ua_entityId'] = $this->entityId;
		$_SESSION['ua_isuser'] = $this->isUser;
		$_SESSION['ua_isoffice'] = $this->officeLogin;
		$_SESSION['ua_firstname'] = $this->firstname;
		$_SESSION['ua_surname'] = $this->surname;
		$_SESSION['ua_permissions'] = $this->permissions;
		$_SESSION['ua_organisation'] = $this->organisationLogin;
		$_SESSION['ua_salt'] = $this->salt;
	}

	private function setPassword($password) {
		if (!$this->isLoggedIn) {
			$this->salt = '';
			for ($i = 0; $i < 16; $i++) {
				$this->salt .= dechex(rand(0, 15));
			}
			$hash = sha1($this->salt.$password);

			$sql = 'UPDATE entities
				SET entity_password = ?, entity_salt = ?
				WHERE entity_id = ?';

			$db = $this->object->db;
			$query = $db->query($sql, array($password, $this->salt, $this->entityId));
		}
	}
}

?>
