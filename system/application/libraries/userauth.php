<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|==========================================================
| Code Igniter User Authentication Class
|
| By Craig Rodway, craig dot rodway at gmail dot com
| Modded by Mark Goodall, doxygen comments&doc coming shortly, along with a
| version that is more intergrated into The Yorker.
| TODO use sql structures in specification
|==========================================================
*/
 
 
 
 
class Userauth{
 
    var $object;
    var $allowed_users = array();
    var $denied_users = array();
    var $allowed_set = false;
    var $denied_set = false;
    var $acl_denied = 'You are not permitted to view this page.';
 
 
 
 
    function Userauth(){
        $this->object =& get_instance();
        $this->object->load->database();
        log_message('debug','User Authentication Class Initialised via '.get_class($this->object));
    }
 
 
 
 
    /**
     * Logout user and reset session data
     */
    function logout(){
        log_message('debug','Userauth: Logout: '.$this->object->session->userdata('username'));
        $sessdata = array('username'=>'', 'loggedin'=>'false');
        $this->object->session->set_userdata($sessdata);
    }
 
 
 
 
    /**
     * Try and validate a login and optionally set session data
     *
     * @param  string  $username  Username to login
     * @param  string  $password  Password to match user as sha1 or plaintext
     * @param  bool  $session (true)  Set session data here. False to set your own
     */
    function trylogin( $username, $password, $session = true ){
        if( $username != '' && $password != ''){
            // Only continue if user and pass are supplied
            
            // SHA1 the password if it isn't already
            if( strlen( $password ) != 40 ){ $password = sha1( $password ); }
            
            // Check details in DB
            $sql =  "SELECT username, fullname FROM ci_users ".
                            "WHERE username='$username' AND password='$password' LIMIT 1";
            $query = $this->object->db->query($sql);
            
            // If user/pass is OK then should return 1 row containing username,fullname
            $return = $query->num_rows();
            
            // Log message
            log_message('debug', "Userauth: Query result: '$return'");
            
            if($return == 1){
                // 1 row returned with matching user & pass = validated!
                
                // Update the DB with the last login time (now)..
                $sql =  "UPDATE ci_users ".
                                "SET lastlogin='".gmdate("%Y-%m-%d %H:%i:%s")."' ".
                                "WHERE username='$username'";
                $this->object->db->query($sql);
                
                // Log
                log_message('debug',"Last login by $username SQL: $sql");
                
                // Get row from query (fullname, email)
                $row = $query->row();
                
                // Set session data array           
                $sessdata = array(
                                                    'username' => $username,
                                                    'loggedin' => 'true',
                                                    'fullname' => $row->fullname,
                                                    );
                
                if( $session == true ){
                    // param to set the session = true
                    log_message('debug', "Userauth: trylogin: setting session data");
                    log_message('debug', "Userauth: trylogin: Session: ".var_export($sessdata, true) );
                    // Set the session
                    $this->object->session->set_userdata($sessdata);
                    return true;
                } else {
                    // param to set the session = false: return the data only without setting session
                    return $sessdata;
                }
            } else {
                // no rows with matching user & pass - ACCESS DENIED!!
                return false;
            }
        } else {
            return false;
        }
    }
 
 
 
 
    /**
     * Checks to see if the user is allowed to view the page or not.
     *
     * This function relies upon one of/both of allow/deny ACLs being set.
     *
     * @param  string  $message  Message displayed if denied access
     * @param  bool  $ret  TRUE:return bool. FALSE:die on false (denied)
     * @return  bool  True if allowed. False/die() if denied
     */
    function check($message = NULL, $ret = false){
        log_message('debug', "Check function URI: ".$this->object->uri->uri_string());
        $session_username = $this->object->session->userdata('username');
        log_message('debug', "Userauth: Check: Session variable 'username': $session_username");
        
        if( $this->object->session->userdata('loggedin') != 'true' ){
            log_message('debug', "Userauth: Check: Username is null or not set");
            if($ret == true){
                return false;       // Return false for the function
            }
            else if ($ret == false){        // Do an action (function param)
                show_error( ($message) ? $message : $this->acl_denied );
                // Show a CI error msg
            }
        }
        
        $username = $session_username;
        $allow = false;
        /* Logic:
            User sets denied list only: allow everyone, deny denied_users[]
            User sets allowed list only: deny everyone, allow valid_users[]
            User sets allowed and denied lists: deny denied_users[], allow allowed_users[]
        */
        if($this->denied_set == true && $this->allowed_set == false){
            
            //  User has set denied list: YES
            //  User has set allowed list: NO
            $allow = true;      // Allow everyone
            if( in_array($username, $this->denied_users) ){ $allow = false; }
            // Deny people in the denied list
        
        } else if( $this->allowed_set == true && $this->denied_set == false){
            
            //  User has set denied list: NO
            //  User has set allowed list: YES
            $allow = false;     // Deny everyone
            if( in_array($username, $this->allowed_users) ){ $allow = true; }
            // Allow people in the allowed list
        
        } else if( $this->allowed_set == true && $this->denied_set == true ) {
            
            //  User has set denied list: YES
            //  User has set allowed list: YES
            if( in_array( $username, $this->denied_users ) ){ $deny = true; }
            // If user is in the deny list, deny=true,allow=false
            if( !$deny && in_array($username, $this->allowed_users)){ $allow = true; }
            // Only see if the user is in the valid list if he isn't in the deny list                   
        
        } else {
            $allow = true;
        }
        if($allow){             // Final check
            return true;        // User is allowed, just carry on
        } else {
            // Access denied!
            log_message('info','Userauth: Access Denied for '.$username.' in: '.get_class($this->object).'.');
            if($ret == true){
                return false;       // Return false for the function
            }
            else if ($ret == false){        // Do an action (function param)
                show_error( ($message) ? $message : $this->acl_denied );
                // Show a CI error msg
            }
        }
    }
 
 
 
 
    function get_allowed($sep = ' '){   return implode($sep, $this->allowed_users); }
    function get_denied($sep = ' '){ return implode($sep, $this->denied_users); }
 
 
 
 
    /**
     * Put users into ALLOW ACL
     *
     * Calls the function set_allowdeny - shared code for allow/deny functions.
     *
     * @param  string  $allow  Space-separated list of usernames/groupnames
     */
    function set_allow( $allow ){
        $this->set_allowdeny( $allow, $this->allowed_users );
        $this->allowed_set = true;
    }
 
 
 
 
    /**
     * Put users into DENY ACL
     *
     * Calls the function set_allowdeny - shared code for allow/deny functions.
     *
     * @param  string  $deny  Space-separated list of usernames/groupnames
     */
    function set_deny( $deny ){
        $this->set_allowdeny( $deny, $this->denied_users );
        $this->denied_set = true;
    }
 
 
 
 
    /**
     * Put users into appropriate ACL. Is called via set_allow()/set_deny()
     *
     * @param  string  $str  Space-separated list of usernames/groupnames
     * @param  array_ptr  $acl  Pointer to the array to update
     */
    function set_allowdeny( $str, &$acl ){
        $arr = explode(' ', $str);                                                  // Split string by spaces
        foreach($arr as $item){
            $group = $this->isGroup($item);                                     // Check to see if this item is a group or a user
            if($group != false){                                                            // It's a group!
                $users = $this->UsersInGroup($group);                       // Loop this group to get it's users
                foreach($users as $user){ $acl[] = $user; }         // Add each user in the group to the valid_users list
            } else {
                $acl[] = $item;                                                                 // Add user to the list as this item isn't a group
            }
        }
    }
 
 
 
 
    /**
     * Check to see if the supplied acl item is a group or not
     *
     * If the item begins with an @ symbol, then the item is a group (UNIX style)
     *
     * @param  string  $name  Utem you are checking
     * @return  string/bool  If the item is a group, the name (
     *   without the @) is returned, 
     *   otherwise the return value is false
    */
    function isGroup( $name ){
        if($name{0} == '@'){ return substr($name, 1); } else { return false; }
    }
 
 
 
 
    /**
     * List all users or all groups depending on parameter
     *
     * @param  string  $option  Can be one of:
     *   'users', 'groups'
     * @return  array  Array containing list of users/groups
     */
    function list_ug( $option ){
        switch($option){
            case 'users':
                $sql = 'SELECT username, email, fullname, lastlogin, enabled FROM ci_users';
                $query = $this->object->db->query($sql);
                $result = $query->result_array();
                $arr = $result;
            break;
            case 'groups':
                $sql = 'SELECT groupname, description FROM ci_groups';
                $query = $this->object->db->query($sql);
                $result = $query->result_array();
                /*foreach($result as $a=>$b)
                {
                    foreach($b as $c)
                    {
                        $arr[] = $c;
                    }
                }*/
                $arr = $result;
            break;
        }
        return $arr;
    }
 
 
 
 
    /**
     * Get a list of the groups that the supplied username belongs to
     *
     * @param  array  $username  Username to find the groups he belongs to
     * @return  array  Group names the specified user belongs to
     */
    function GroupsOfUser( $username ){
        $sql =   "SELECT ci_groups.groupname " 
                        ."FROM ci_usersgroups " 
                        ."LEFT JOIN ci_groups ON ci_usersgroups.groupid=ci_groups.groupid "
                        ."LEFT JOIN ci_users ON ci_usersgroups.userid=ci_users.userid "
                        ."WHERE ci_users.username='$username'";
        $query = $this->object->db->query($sql);
        $result = $query->result_array();
        $groups = array();
        
        if($result){
            foreach($result as $group){
                $groups[] = $group['groupname'];
            }
        }
        if(count($groups) == 0){
            $groups[] = 'None';
        }
        
        return $groups;
    }
 
 
 
 
    /**
     * Returns an array of users belonging to specified group
     *
     * @param  array  $groupname  Name of group you want
     * @return  array  Users belonging to the group specified
     */
    function UsersInGroup( $groupname ){
        $sql =   "SELECT ci_users.username, ci_users.userid "
                        ."FROM ci_usersgroups "
                        ."LEFT JOIN ci_groups ON ci_usersgroups.groupid = ci_groups.groupid "
                        ."LEFT JOIN ci_users ON ci_usersgroups.userid = ci_users.userid "
                        ."WHERE ci_groups.groupname = '$groupname'";
        $query = $this->object->db->query($sql);
        $result = $query->result_array();
        $users = array();
        if($result){
            foreach($result as $user){
                $users[] = $user['username'];
            }
        }
        return $users;
    }
 
 
 
 
    /**
     * Checks to see if the supplied user exists in the DB
     *
     * @param  string  $username  Username to look up
     * @return  bool  True if user exists
     */
    function user_exists( $username ){
        $sql = "SELECT userid FROM ci_users WHERE username='$username'";
        $query = $this->object->db->query($sql);
        $c = $query->num_rows();
        $row = $query->row();
        return ($c == 1) ? true : false;
    }
 
 
 
 
    /**
     * Add a user to the DB
     *
     * @param  array  $userarray  Array containing the user attributes
     * @return  int  0:Not added,1:User added,2:Already exists
     */
    function adduser( $userarray ){
        if( ! is_array( $userarray ) ){ return 0; }
 
        // Only add user if he doesn't already exist
        if( !$this->user_exists( $userarray['username'] ) ){
            // Get only fields we want from the array
            $data['username'] = $userarray['username'];
            $data['fullname'] = $userarray['fullname'];
            $data['password'] = $userarray['password'];
            $data['email'] = $userarray['email'];
            $data['enabled'] = $userarray['enabled'];
 
            // If password length is less than 40 chars (not SHA1) then SHA1() it
            if( strlen( $data['password'] ) < 40 ){ $data['password'] = sha1( $data['password'] ); }
            
            $this->object->db->insert('ci_users', $data);
 
            if( count( $userarray['groups'] ) == 1 ){
                // A single group means that users can only belong ot one group
                $this->putuseringroup( $userarray['username'], $userarray['groups'] );
            }
            
            #addusertogroup( $username, $userarray['groups'] );
            return 1;       // User added
        } else {
            return 2;       // User already exists
        }
    }
 
 
 
 
    function edituser( $userid, $userarray ){
        if( !is_array( $userarray ) ){ return 0; }
        
        // Get only fields we want from the array
        $data['username'] = $userarray['username'];
        $data['fullname'] = $userarray['fullname'];
        $data['password'] = $userarray['password'];
        $data['email'] = $userarray['email'];
        $data['enabled'] = $userarray['enabled'];
 
        // If password length is less than 40 chars (not SHA1) then SHA1() it
        if( strlen( $data['password'] ) < 40 ){ $data['password'] = sha1( $data['password'] ); }
 
        $this->object->db->where('userid', $userid);
        $this->object->db->update('ci_users', $data);
        
        #echo $userarray['groups'];
        if( count( $userarray['groups'] ) == 1 ){
            // A single group means that users can only belong ot one group
            $this->putuseringroup( $userarray['username'], $userarray['groups'] );
        }
 
    }
 
 
 
 
    /**
     * Remove a user
     *
     * Note: function also removes the user from all groups they are a member of
     *
     * @param  string  $username  Username of the user to remove
     * @return  bool
     */
    function deleteuser( $username ){
        if( $username == $this->object->session->userdata('username') )
        {
            // Exit if delete object is same as session user (same person)
            log_message('info', 'User change: User '.$username.' tried to delete themself.');
            show_error('You can not delete yourself!');
            exit();
        }
        if( $this->user_exists( $username ) )
        {
            // User exists
 
            // Delete group
            $sql =  "DELETE FROM ci_usersgroups WHERE userid='".$this->getuserid($username)."'";
            $del_ci_usersgroups = $this->object->db->query($sql);
            
            // Delete user
            $sql =  "DELETE FROM ci_users WHERE username='$username' LIMIT 1";
            $del_ci_users = $this->object->db->query($sql);
            
            return true;
        } else {
            // User didn't exist in the first place!
            return false;
        }
    }
 
 
 
 
    /**
     * Check if account is enabled or not
     *
     * @param  string  $user  Single username
     * @return  bool  User is enabled:true
     */
    function enabled( $username ){
        $sql = "SELECT enabled FROM ci_users WHERE username='$username'";
        $query = $this->object->db->query($sql);
        $row = $query->row();
        $ret = ($row->enabled == 1) ? true : false;
        return $ret;
    }
 
 
 
 
    function loggedin(){
        $session_username = $this->object->session->userdata('username');
        $session_bool = $this->object->session->userdata('loggedin');
        if( ( isset($session_username) && $session_username != '') && ( isset($session_bool) && $session_bool == 'true' ) ){
            return true;
        } else {
            return false;
        }
    }
 
 
 
 
    function getuserid($username){
        $sql = "SELECT userid FROM ci_users WHERE username='$username'";
        $query = $this->object->db->query($sql);
        $row = $query->row();
        return $row->userid;
    }
    
    
    
    
    function getusername($userid){
        $sql = "SELECT username FROM ci_users WHERE userid='$userid'";
        $query = $this->object->db->query($sql);
        $row = $query->row();
        return $row->userid;
    }
 
 
 
 
    function getgroupid($groupname){
        $sql = "SELECT groupid FROM ci_groups WHERE groupname='$groupname'";
        $query = $this->object->db->query($sql);
        $row = $query->row();
        return $row->groupid;
    }
 
 
 
 
    function getgroupname($groupid){
        $sql = "SELECT groupname FROM ci_groups WHERE groupid='$groupid'";
        $query = $this->object->db->query($sql);
        $row = $query->row();
        return $row->groupname;
    }
 
 
 
 
    /**
     * Add user(s) to group(s)
     *
     * Note: If both users and groups are supplied, each user is added to each 
     * group, they are not matched by array keys user1=group1, user2=group2 etc.
     *
     * @param  string/array  $users  List of or single username
     * @param  string/array  $groups  List of or single group name
     */
    function add_user_to_group( $users, $groups ){
        // If a string is supplied (one user/group) - create an array of it
        if( !is_array( $users ) ){ $users = array( $users ); }
        if( !is_array( $groups ) ){ $groups = array( $groups ); }
    }
    
    
    
    /**
     * Function to put a user in an exclusive group (no belonging to multiple groups)
     */     
    function putuseringroup( $username, $groupname ){
        $userid = $this->getuserid( $username );
        $groupid = $this->getgroupid( $groupname );
        // Remove user form all groups first
        $sql = "DELETE FROM ci_usersgroups WHERE userid='$userid'";
        $query = $this->object->db->query($sql);
        // Add user to group
        $sql = "INSERT INTO ci_usersgroups (groupid,userid) VALUES ('$groupid','$userid')";
        $query = $this->object->db->query($sql);
    }
 
 
 
 
}

?>