<?php

/**
 * @file User_auth.php
 */

/// User authentication class.
/**
 * @author Andrew Oakley (ado500)
 *
 * This model is automatically loaded and provides the login status of users.
 *  Currently information is avaliable as member variables.  This may be
 *  changed to accessors at some point, but if this happens I will update all
 *  pages in the SVN to use them at the same time.
 */
class User_auth extends model {

	/// bool True if the user is logged in
	public $isLoggedIn = FALSE;

	/// string The username of the logged in user
	public $username = '';

	/// int The entityId of the logged in user
	public $entityId;

	/// bool True if the user is an actual user rather than an organisation
	public $isUser;
	
	/// '12','24' Time format of the user
	public $timeFormat = '12';

	/// bool If the user has an office login (does not indicate if they are
	///  logged in)
	public $officeLogin;

	/// string The current access level to the office (None, Low, High, Admin)
	public $officeType = 'None';

	/// int The interface id for the office
	public $officeInterface = -1;

	/// string The firstname of the logged in user
	public $firstname = '';

	/// string The surname of the logged in user
	public $surname = '';

	/// int The permission of the logged in user
	public $permissions;

	/// entity_id The organisation (if any) the user has logged into the admin
	///  for (-1 if none)
	public $organisationLogin = -1;

	/// string The name of the organisation that has been logged in to (if any)
	public $organisationName = '';

	/// string The short name of the organisation that has been logged in to (if
	///  any)
	public $organisationShortName = '';

	/// array Array indexed by organisation_directory_entry_name of organisation
	///  entity id ('id'), name ('name') of all decendents of logged in organisation (empty
	///  when not logged in as an organisation/vip).
	public $allTeams = array();

	/// string The salt used to generate the password hash
	private $salt;

	/// The default constructor
	public function __construct() {
		parent::model();

		$this->load->model('organisation_model');

		// Check if we already have login details
		if (isset($_SESSION['ua_loggedin'])) {
			$this->isLoggedIn = $_SESSION['ua_loggedin'];
			$this->username = $_SESSION['ua_username'];
			$this->entityId = $_SESSION['ua_entityId'];
			$this->isUser = $_SESSION['ua_isuser'];
			if (array_key_exists('ua_timeformat', $_SESSION)) {
				$this->timeFormat = $_SESSION['ua_timeformat'];
			}
			$this->officeLogin = $_SESSION['ua_hasoffice'];
			$this->officeType = $_SESSION['ua_officetype'];
			$this->officeInterface = $_SESSION['ua_officeinterface'];
			$this->firstname = $_SESSION['ua_firstname'];
			$this->surname = $_SESSION['ua_surname'];
			$this->permissions = $_SESSION['ua_permissions'];
			$this->organisationLogin = $_SESSION['ua_organisation'];
			$this->organisationName = $_SESSION['ua_organisationname'];
			$this->organisationShortName = $_SESSION['ua_organisationshortname'];
			$this->allTeams = $_SESSION['ua_allteams'];
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

	/// Attempts to log a user in based on a username and hash.
	private function loginByHash($username, $hash, $savelogin) {
		$sql = 'SELECT entity_id, entity_salt
			FROM entities
			WHERE entity_username = ?
				AND entity_password = ?
				AND entity_deleted = FALSE';

		$query = $this->db->query($sql, array($username, $hash));

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

	/// Performs the actual login once a user is authenticated
	private function loginAuthed($username, $entityId, $savelogin, $hash) {
		$this->isLoggedIn = true;
		$this->username = $username;
		$this->entityId = $entityId;
		$this->officeLogin = false;

		// Create a new session to prevent hijacking of sessions
		session_regenerate_id(true);

		// Set/unset a cookie if appropriate
		if ($savelogin) {
			setcookie(
				'SavedLogin',
				implode(':$:', array($username, $hash)),
				time()+$this->config->item('saved_login_duration'),
				$this->config->item('base_url')
			);
		} /* elseif (isset($_COOKIE['SavedLogin'])) {
			setcookie(
				'SavedLogin',
				$username,
				time()-$this->config->item('saved_login_duration'),
				$this->config->item('base_url')
			);
		}*/

		$sql = 'SELECT user_firstname, user_surname, user_office_access, user_time_format
			FROM users
			WHERE user_entity_id = ?';

		$query = $this->db->query($sql, array($this->entityId));

		// See if there was a result (i.e. we have a user)
		if ($query->num_rows() > 0) {
			$row = $query->row();

			$this->isUser = true;
			$this->timeFormat = $row->user_time_format;
			$this->firstname = $row->user_firstname;
			$this->surname = $row->user_surname;
			$this->officeLogin = $row->user_office_access;
		} else {
			$this->isUser = false;
			$this->timeFormat = '12';
			$this->surname = '';
			$this->officeLogin = false;
			$this->organisationLogin = $this->entityId;
			$this->updateTeamsData();
		}

		// Set session variables to persist information
		$this->localToSession();
	}

	/// Get organisation + team information.
	/**
	 * Fills organisationName, organisationShortName, and allTeams
	 */
	function updateTeamsData()
	{
		// This function generates SQL for getting teams of an organisation.
		$team_query_data = $this->organisation_model->GetTeams_QueryData(
			$this->organisationLogin,	// main organisation id
			'team',	// team alias
			TRUE,	// Include the organisation itself
			NULL	// Default depth
		);
		$sql = '
		SELECT	team.organisation_entity_id AS id,
				team.organisation_name AS name,
				team.organisation_directory_entry_name AS shortname
		FROM	organisations AS team ' .
			// Joins to parent organisations
			$team_query_data['joins'] .
			// Conditions for descent from main organisation
			'	WHERE	' . $team_query_data['where'];
		$query = $this->db->query($sql);
		// Store the teams indexed by the shortname
		$this->allTeams = array();
		foreach ($query->result_array() as $team) {
			$this->allTeams[$team['shortname']] = array($team['id'], $team['name']);
			// If this team is the main organisation, store it separately
			if ($team['id'] == $this->organisationLogin) {
				$this->organisationName = $team['name'];
				$this->organisationShortName = $team['shortname'];
			}
		}
	}

	/// Login based on username and password.
	/**
	 * @param $username string Username.
	 * @param $password string Password.
	 * @param $savelogin bool Stay logged in.
	 */
	public function login($username, $password, $savelogin, $newpass = false) {
		// Ensure logged out!
		if ($this->isLoggedIn) {
			$this->logout();
		}
		$sql = 'SELECT entity_id, entity_username, entity_password,
				entity_salt, entity_pwreset
			FROM entities
			WHERE entity_username = ? AND entity_deleted = FALSE';

		$query = $this->db->query($sql, array($username));

		// See if we have an entity with this username
		if ($query->num_rows() > 0) {
			$row = $query->row();

			if ($row->entity_id == $this->config->Item('company_entity_id'))
				/// @throw Exception Could not find user or uni login
				throw new Exception('Could not find user or uni login');

			// Store the salt for any further login
			$this->salt = $row->entity_salt;

			// Create a (badly salted) hash
			$hash = sha1($row->entity_salt.$password);

			if ($newpass)
				$success = $row->entity_pwreset == $password;
			else
				$success = $hash == $row->entity_password;

			if ($success) {
				// The hashes match, login
				$this->loginAuthed(
					$username,
					$row->entity_id,
					$savelogin,
					$hash
				);

			} else {
				/// @throw Exception Invalid password.
				throw new Exception('Invalid password');
			}
		} else {
			throw new Exception('Invalid username or password.');
		}
	}

	public function resetpassword($username) {
		$sql = 'SELECT entity_id, entity_username, entity_password,
				entity_salt, user_nickname
			FROM entities
			INNER JOIN users ON
				user_entity_id = entity_id
			WHERE entity_username = ?';

		$query = $this->db->query($sql, array($username));
		$random = $this->getRandomData();

		// See if we have an entity with this username
		if ($query->num_rows() == 0) {
			$sql = 'INSERT INTO entities (entity_username) VALUES (?)';
			$query = $this->db->query($sql, array($username));
			$entityId = $this->db->insert_id();
			$sql = 'INSERT INTO users (user_entity_id) VALUES (?)';
			$query = $this->db->query($sql, array($entityId));
			$new = true;
			$nick = '';
		} else {
			$row = $query->row();
			$entityId = $row->entity_id;
			$nick = $row->user_nickname;
			$new = false;
		}

		$sql = 'UPDATE
				entities
			SET
				entity_pwreset = ?
			WHERE
				entity_id = ?';

		$query = $this->db->query($sql, array($random, $entityId));
		if ($this->db->affected_rows() == 0) {
			throw new Exception('Internal error: failed setting passkey');
		}

		$to = $username.$this->config->Item('username_email_postfix');
		$from = $this->pages_model->GetPropertyText('system_email', true);
		$subject = $this->pages_model->GetPropertyText(
			$new ?
				'user_password_new_email_subject' :
				'user_password_reset_email_subject',
			true
		);
		$body = $this->pages_model->GetPropertyText(
			$new ?
				'user_password_new_email_body' :
				'user_password_reset_email_body',
			true
		);
		$body = str_replace(
			'%%link%%',
			'http://www.theyorker.co.uk/login/newpass/'.
				$username.'/'.$random,
			$body
		);
		$body = str_replace(
			'%%nickname%%',
			$nick,
			$body
		);

		$this->load->helper('yorkermail');
		try {
			yorkermail($to,$subject,$body,$from);
			return true;
		} catch (Exception $e) {
			//Do nothing
		}

		return false;
	}

	/// Logout of the entire site
	public function logout() {
		if (!$this->isLoggedIn) {
			return;
		}
		// Change the cookie not to store the password, if present
		if (isset($_COOKIE['SavedLogin'])) {
			setcookie(
				'SavedLogin',
				$this->username,
				time()+$this->config->item('saved_login_duration'),
				$this->config->item('base_url')
			);
		}

		// Set login status to 'not logged in'.  Username is left for
		//  login form.
		$this->isLoggedIn = false;
		$this->entityId = -1;
		$this->officeLogin = false;
		$this->officeType = 'None';
		$this->officeInterface = -1;
		$this->username = '';
		$this->firstname = '';
		$this->surname = '';
		$this->permissions = 0;
		$this->organisationLogin = -1;
		$this->allTeams = array();
		$this->salt = '';

		// Clear the session
		session_destroy();
		session_start();
	}

	/// Login to the yorker office
	/**
	 * @param $password string Password.
	 */
	public function loginOffice($password) {
		/// @TODO: test (awaiting interface)
		if (!$this->officeLogin) {
			throw new Exception('User does not have office access');
		}

		$hash = sha1($this->salt.$password);

		$sql = 'SELECT entity_password, user_office_interface_id,
				user_office_password, user_admin
			FROM entities INNER JOIN users ON
				entity_id = user_entity_id
			WHERE entity_id = ?';

		$query = $this->db->query($sql, array($this->entityId));

		// We should always have a result at this stage
		if ($query->num_rows() == 0) {
			/// @throw Exception Cannot find entity!
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
				/// @throw Exception Invalid password
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
				/// @throw Exception Invalid password
				throw new Exception('Invalid password');
			}
		}

		$this->localToSession();
	}

	/// Checks if the current users office password matches $password (returns true or false)
	/**
	 * @param $password string Password
	 */
	public function checkOfficePassword($password) {
		if (!$this->isLoggedIn | !$this->isUser | $this->officeType == 'None' | $this->officeType == 'Low') {
			/// @throw Exception You must be logged into the office with a seperate password to do this
			throw new Exception('You must be logged into the office with a seperate password to do this');
		}

		$hash = sha1($this->salt.$password);

		$sql = 'SELECT COUNT(*) AS valid
			FROM entities INNER JOIN users ON
				entity_id = user_entity_id
			WHERE entity_id = ? AND
				(user_office_password = ?)';

		$query = $this->db->query($sql, array($this->entityId, $hash));

		$row = $query->row();
		return $row->valid;
	}


	/// Logout of the yorker office
	public function logoutOffice() {
		$this->officeType = 'None';
		$this->officeInterface = -1;
		$this->localToSession();
	}

	/// Checks if the current users password matches $password (returns true or false)
	/**
	 * @param $password string Password
	 */
	public function checkPassword($password) {
		if (!$this->isLoggedIn | !$this->isUser) {
			/// @throw Exception You must be logged in as a student to do this
			throw new Exception('You must be logged in as a student to do this');
		}

		$hash = sha1($this->salt.$password);

		$sql = 'SELECT COUNT(*) AS valid FROM entities
			WHERE entities.entity_id = ?
				AND entity_password = ?';

		$query = $this->db->query($sql, array($this->entityId, $hash));

		$row = $query->row();
		return $row->valid;
	}

	/// Sets an entities password (and salt if necessary).
	/**
	 * @param $password string Password
	 * @param $entity defaults to NULL
	 *	- integer Entity ID to set password for.
	 *	- NULL Current logged in entity.
	 *
	 * Defaults to the logged in entity, otherwise uses @a entity.
	 */
	public function setPassword($password, $entity = null) {
		if ($entity == null) {
			if (!$this->isLoggedIn | !$this->isUser)
				/// @throw Exception You must be logged in as a student to do this
				throw new Exception('You must be logged in as a student to do this');

			$entity = $this->entityId;
			$salt = $this->salt;
		} else {
			$sql = 'SELECT entity_salt FROM entities
				WHERE entity_id = ?';

			$query = $this->db->query($sql, array($entity));
			$row = $query->row();
			$salt = $query->entity_salt;
		}

		/// @TODO: check that null is returned for no salt
		if ($salt == null) {
			$salt = $this->getRandomData();
		}

		$hash = sha1($salt.$password);
		$sql = 'UPDATE entities
			SET entity_salt = ?, entity_password = ?
			WHERE entity_id = ?';

		$query = $this->db->query($sql, array($salt, $hash, $entity));
	}

	/// Sets an entities office password.
	/**
	 * @param $password string Password
	 * @param $entity defaults to NULL
	 *	- integer Entity ID to set password for.
	 *	- NULL Current logged in entity.
	 *
	 * Defaults to the logged in entity, otherwise uses @a entity.
	 */
	public function setOfficePassword($password, $entity = null) {
		/// @pre User already has a user record and salt.

		if ($entity == null) {
			if (!$this->isLoggedIn | !$this->isUser)
				/// @throw Exception You must be logged in as a student to do this
				throw new Exception('You must be logged in as a student to do this');
			$entity = $this->entityId;
			$salt = $this->salt;
		} else {
			$sql = 'SELECT entity_salt FROM entities
				WHERE entity_id = ?';

			$query = $this->db->query($sql, array($entity));
			$row = $query->row();
			$salt = $row->entity_salt;
		}

		if ($salt == null) {
			/// Generate a salt if none exists
			$salt = $this->getRandomData();
			/// Save new salt to db
			$sql = 'UPDATE entities
					SET entity_salt = ?
					WHERE entity_id = ?';
			$query = $this->db->query($sql, array($salt, $entity));
		}

		$hash = sha1($salt.$password);

		$sql = 'UPDATE users
			SET user_office_password = ?
			WHERE user_entity_id = ?';

		$query = $this->db->query($sql, array($hash, $entity));
	}

	/// Get a list of organisations that the user can login to
	/**
	 * @return array of arrays Straight from organisations table with fields:
	 *	- 'organisation_entity_id'
	 *	- 'organisation_name'
	 *	- 'organisation_directory_entry_name'
	 */
	public function getOrganisationLogins() {
		if (!$this->isLoggedIn | !$this->isUser)
			/// @throw Exception You must be logged in as a student to do this
			throw new Exception('You must be logged in as a student to do this');

		$sql = 'SELECT organisation_entity_id, organisation_name, organisation_directory_entry_name FROM organisations
				INNER JOIN subscriptions ON subscription_organisation_entity_id = organisation_entity_id
			WHERE subscription_user_entity_id = ? AND subscription_vip_status = "approved"';

		$query = $this->db->query($sql, array($this->entityId));

		return $query->result_array();
	}

	/// Login to an organisations admin interface
	/**
	 * @param $password string Password.
	 * @param $organisationId enttity_id Organisation entity id.
	 */
	public function loginOrganisation($password, $organisationId) {
		if (!$this->isLoggedIn | !$this->isUser)
			/// @throw Exception You must be logged in as a student to do this
			throw new Exception('You must be logged in as a student to do this');
		if ($organisationId == $this->config->Item('company_entity_id'))
			/// @throw Exception You cannot enter the VIP area of this organisation
			throw new Exception('You cannot enter the VIP area of this organisation');

		$hash = sha1($this->salt.$password);

		$sql = 'SELECT organisation_name, organisation_directory_entry_name FROM entities
			INNER JOIN subscriptions ON entities.entity_id = subscriptions.subscription_user_entity_id
			INNER JOIN organisations ON organisations.organisation_entity_id = subscriptions.subscription_organisation_entity_id
			WHERE entities.entity_id = ?
				AND subscriptions.subscription_organisation_entity_id = ?
				AND subscriptions.subscription_vip_status = "approved"
				AND entity_password = ?';

		$query = $this->db->query($sql, array($this->entityId, $organisationId, $hash));

		if ($query->num_rows() != 1) {
			/// @throw Exception Invalid organisation or password
			throw new Exception('Invalid organisation or password');
		}

		//$row = $query->row();

		$this->organisationLogin = $organisationId;
		$this->updateTeamsData();
		$this->localToSession();
	}

	/// Logout of an organisation interface
	public function logoutOrganisation() {
		$this->organisationLogin = -1;
		$this->organisationName = '';
		$this->organisationShortName = '';
		$this->allTeams = array();
		$this->localToSession();
	}

	/// Save all data from this class in the session
	private function localToSession() {
		$_SESSION['ua_loggedin'] = $this->isLoggedIn;
		$_SESSION['ua_username'] = $this->username;
		$_SESSION['ua_entityId'] = $this->entityId;
		$_SESSION['ua_isuser'] = $this->isUser;
		$_SESSION['ua_timeformat'] = $this->timeFormat;
		$_SESSION['ua_hasoffice'] = $this->officeLogin;
		$_SESSION['ua_officetype'] = $this->officeType;
		$_SESSION['ua_officeinterface'] = $this->officeInterface;
		$_SESSION['ua_firstname'] = $this->firstname;
		$_SESSION['ua_surname'] = $this->surname;
		$_SESSION['ua_permissions'] = $this->permissions;
		$_SESSION['ua_organisation'] = $this->organisationLogin;
		$_SESSION['ua_organisationname'] = $this->organisationName;
		$_SESSION['ua_organisationshortname'] = $this->organisationShortName;
		$_SESSION['ua_allteams'] = $this->allTeams;
		$_SESSION['ua_salt'] = $this->salt;
	}

	private function getRandomData() {
		$val = '';
		for ($i = 0; $i < 32; $i++) {
			$val .= chr(rand(65,90));
		}
		return $val;
	}
}

?>
