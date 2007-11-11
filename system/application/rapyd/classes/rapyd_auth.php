<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|==========================================================
| Rapyd User Authentication Class
|
| (Basic User-Type/Persistence autentication from Felice Ostuni: felix at rapyd dot com)
|==========================================================
*/
 
 
 
 
class rapyd_auth{
 
 
 
	var $object;
	var $allowed_users = array();
	var $denied_users = array();
	var $allowed_set = false;
	var $denied_set = false;
	var $acl_denied = 'You are not permitted to view this page.';
  
  var $uname  = "nick";  
 
	function rapyd_auth(){
	
		$this->ci =& get_instance();
    $this->rapyd =& $this->ci->rapyd;
    $this->session =& $this->rapyd->session;
		
    //load needed libraries 
    if (!isset($this->ci->db)) {
      $this->ci->load->library('database');
    }
    $this->db =& $this->ci->db;

	}
 
 
 
 
	/**
	 * Logout user and reset session data
	 */
  function logout($namespace="auth"){
    $expiration = time() - (86400*365); //one year in the past
    setcookie($namespace.'_cookie', "", $expiration, "/");
    $this->session->clear(null, $namespace);
  }
 
 
	/**
	 * Try and validate a login and optionally set session data
	 *
	 * @param  string  $username  Username to login
	 * @param  string  $password  Password to match user
	 * @param  bool  $session (true)  Set session data here. False to set your own
	 */
	function trylogin( $username, $password, $cookie = true , $namespace="auth"){
			
			
		// Check details in DB
    $this->db->where($this->uname, $username);
    $this->db->where("pass", $password);
    $this->db->where("active", 'y');
    $query = $this->db->get("users", 1);
    	
		// If user/pass is OK then should return 1 row containing username,fullname
		$return = $query->num_rows();
    $row = $query->row();
		
		if($return == 1){
			
			// update last login datetime
      $this->db->set("lastlogin", date("%Y-%m-%d %H:%i:%s"));
			$this->db->where($this->uname, $username);
      $this->db->update("users");
				
			// Set session data array
      $this->session->save('username', $username, $namespace);
      $this->session->save('email', $row->email, $namespace);
      $this->session->save('loggedin', true, $namespace);
      $this->session->save('name', $row->name, $namespace);
      $this->session->save('user_type', $row->user_type, $namespace);
      $this->session->save('user_id', $row->user_id, $namespace);      
	    
			if( $cookie == true ){
			   $this->set_cookie($username,$password, $namespace);
			}			
			  
      return true;			
			
		} else {
			return false;
		}

	}
 
  function trylogin_bycookie($namespace="auth"){
    if (!$this->loggedin() && isset($_COOKIE[$namespace.'_cookie'])){
      $authfields = unserialize($_COOKIE[$namespace.'_cookie']);
      $this->trylogin( $authfields['username'] , $authfields['pass'], true, $namespace);
    }
    
  }
 
 
 
  function set_cookie($username,$password, $namespace="auth"){
    $expiration = time() + (86400*365); //one year 
    $authfields = array();
    $authfields['username']   = $username;
    $authfields['pass'] = $password;
    $cookie = serialize($authfields);
    setcookie($namespace.'_cookie', $cookie, $expiration, "/");
  }
 
 
 
	/**
	 * Checks to see if the user is allowed "by user_type" to view the page or not.
	 *
	 *
	 * @param  string  $message  Message displayed if denied access
	 * @param  bool  $ret  TRUE:return bool. FALSE:die on false (denied)
	 * @return  bool  True if allowed. False/die() if denied
	 */
	function check_type($type="admin", $namespace="auth"){
		
		if(!$this->session->get('loggedin', $namespace)){
		//redirect('user/login', 'location');
      return false;
		}
		
		$session_user_type = $this->session->get('user_type', $namespace);
		if ($session_user_type == $type){
		  return true;
	  } else {
		  return false;
   }		  
		
	} 
 
 
 
	/**
	 * Checks to see if the supplied user exists in the DB
	 *
	 * @param  string  $username  Username to look up
	 * @return  bool  True if user exists
	 */
	function user_exists( $username ){
		$sql = "SELECT user_id FROM users WHERE ".$this->uname."='$username'";
		$query = $this->db->query($sql);
		$c = $query->num_rows();
		$row = $query->row();
		return ($c == 1) ? true : false;
	}
 
  
 
	/**
	 * Check if account is enabled or not
	 *
	 * @param  string  $user  Single username
	 * @return  bool  User is enabled:true
	 */
	function active( $username ){
		$sql = "SELECT active FROM users WHERE ".$this->uname."='$username'";
		$query = $this->db->query($sql);
		$row = $query->row();
		$ret = ($row->active == 'y') ? true : false;
		return $ret;
	}
 
	function loggedin($namespace="auth"){
		$session_username = $this->session->get('username',$namespace);
		$session_bool = $this->session->get('loggedin',$namespace);
		if( (isset($session_username) && $session_username != '') && $session_bool ){
			return true;
		} else {
			return false;
		}
	}
 
	function getuserid($username){
		$sql = "SELECT user_id FROM users WHERE ".$this->uname."='$username'";
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->user_id;
	}
	
	function getusername($userid){
		$sql = "SELECT ".$this->uname." FROM users WHERE user_id='$userid'";
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->user_id;
	}
  
 
}

?>