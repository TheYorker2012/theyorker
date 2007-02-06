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
	
	// If the user has an office login (does not indicate if they are
	//  logged in)
	public $officeLogin;

	// The current access level to the office (None, Low, High, Admin)
	public $officeType = 'None';

	// The interface id for the office
	public $officeInterface = -1;

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
			$this->officeLogin = $_SESSION['ua_hasoffice'];
			$this->officeType = $_SESSION['ua_officetype'];
			$this->officeInterface = $_SESSION['ua_officeinterface'];
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
					$this->loginByHash(
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
			$this->loginAuthed(
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
		
		$sql = 'SELECT user_firstname, user_surname, user_office_access
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
			$this->officeLogin = $row->user_office_access;
		} else {
			$sql = 'SELECT organisation_name 
				FROM organisations 
				WHERE organisation_entity_id = ?';
			
			$query = $db->query($sql, array($this->entityId));
			
			$this->isUser = false;
			$this->firstname = $row->organisation_name;
			$this->surname = '';
			$this->officeLogin = false;
			$this->organisationLogin = $this->entityId;
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
		$this->officeType = 'None';
		$this->officeInterface = -1;
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
		// TODO: test (awaiting interface)
		if (!$this->officeLogin) {
			throw new Exception('User does not have office access');
		}

		$hash = sha1($this->salt.$password);
		
		$sql = 'SELECT entity_password, user_office_interface_id, 
				user_office_password, user_admin
			FROM entities INNER JOIN users ON 
				entity_id = user_entity_id 
			WHERE entity_id = ?';
		
		$db = $this->object->db;
		$query = $db->query($sql, array($this->entityId));
		
		// We should always have a result at this stage
		if ($query->num_rows() == 0) {
			throw new Exception('Cannot find entity!');
		}

		$row = $query->row();
		
		if ($row->user_office_password == null) {
			// The user doesn't have a seperate password, this is a
			//  low level login
			if ($row->entity_password == $hash) {
				$this->officeType = 'Low';
				$this->officeInterface = $row->user_office_interface_id;
			} else {
				throw new Exception('Invalid password');
			}
		} else {
			// The user has a seperate password, this is a high
			//  level or admin login
			if ($row->user_office_password == $hash) {
				if ($row->user_admin) {
					$this->officeType = 'Admin';
				} else {
					$this->officeType = 'High';
				}
				$this->officeInterface = $row->user_office_interface_id;
			} else {
                                throw new Exception('Invalid password');
                        }
		}

		$this->localToSession();
	}
	
	// Logout of the yorker office
	public function logoutOffice() {
		$this->officeType = 'None';
		$this->officeInterface = -1;
		$this->localToSession();
	}
	
	// Checks if the current users password matches $password (returns true or false)
	public function checkPassword($password) {
		if (!$this->isLoggedIn | !$this->isUser)
			throw new Exception('You must be logged in as a student to do this');

		$hash = sha1($this->salt.$password);

		$sql = 'SELECT COUNT(*) AS valid FROM entities
			WHERE entities.entity_id = ?
				AND entity_password = ?';

		$db = $this->object->db;
		$query = $db->query($sql, array($this->entityId, $organisationId, $hash));

		$row = $query->row();
		return $row->valid;
	}

	// Get a list of organisations that the user can login to
	public function getOrganisationLogins() {
		if (!$this->isLoggedIn | !$this->isUser)
			throw new Exception('You must be logged in as a student to do this');

		$sql = 'SELECT organisation_entity_id, organisation_name FROM organisations 
				INNER JOIN subscriptions ON subscription_organisation_entity_id = organisation_entity_id
			WHERE subscription_user_entity_id = ? AND subscription_vip = TRUE';

		$db = $this->object->db;
		$query = $db->query($sql, array($this->entityId));

		return $query->result_array();
	}

	// Login to an organisations admin interface
	public function loginOrganisation($password, $organisationId) {
		if (!$this->isLoggedIn | !$this->isUser)
			throw new Exception('You must be logged in as a student to do this');
		
		$hash = sha1($this->salt.$password);

		$sql = 'SELECT COUNT(*) AS valid FROM entities 
			INNER JOIN subscriptions ON entities.entity_id = subscriptions.subscription_user_entity_id
			WHERE entities.entity_id = ?
				AND subscriptions.subscription_organisation_entity_id = ? 
				AND subscriptions.subscription_vip = TRUE
				AND entity_password = ?';
		
		$db = $this->object->db;
		$query = $db->query($sql, array($this->entityId, $organisationId, $hash));

		$row = $query->row();
		if ($row->valid) {
			$this->organisationLogin = $organisationId;
			$this->localToSession();
		} else {
			throw new Exception('Invalid organisation or password');
		}
	}

	// Logout of an organisation interface
	public function logoutOrganisation() {
		$this->organisationLogin = -1;
		$this->localToSession();
	}
	
	// Save all data from this class in the session
	private function localToSession() {
		$_SESSION['ua_loggedin'] = $this->isLoggedIn;
		$_SESSION['ua_username'] = $this->username;
		$_SESSION['ua_entityId'] = $this->entityId;
		$_SESSION['ua_isuser'] = $this->isUser;
		$_SESSION['ua_hasoffice'] = $this->officeLogin;
		$_SESSION['ua_officetype'] = $this->officeType;
		$_SESSION['ua_officeinterface'] = $this->officeInterface;
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
