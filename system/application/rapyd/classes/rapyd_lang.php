<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
|==========================================================
| Rapyd Multi-Language & ip-to-country Support Class
|
| Support app. internationalization by a persistence system (sessions) plus an optional ip-to-country detection
|==========================================================
*/
 

class rapyd_lang{
 
	var $object;

	var $ci;
	var $rapyd;

	var $db;
	var $session;
	var $languages;
	var $language;

	//ip-to-country
	var $prefix1;
	var $prefix2;
	var $country;
	
	function rapyd_lang(&$rapyd)
	{
		$this->ci =& get_instance();
		$this->rapyd =& $rapyd;
		$this->session =& $rapyd->session;
    
    //var_dump($this->rapyd);
    //die();
    
		$this->languages = $this->rapyd->config->item("languages");
    
		//get current language from current session or rapyd config file
		$this->get_language();
		

	}
 
	/**
	 * get current language (form session or config file)
	 * and load rapyd language file
	 *
	 * @access public 
	 * @return  current luanguage
	 */
	function get_language()
	{
		$language = $this->session->get("language","rapyd");
		
		//languase is already in session
		if (isset($language) && in_array($language,$this->languages))
		{
			$this->language = $language;
		} elseif ($this->rapyd->config->item("ip-to-country")){

      //load needed libraries 
      if (!isset($this->ci->db))
      {
        $this->ci->load->database();
      }
      $this->db =& $this->ci->db;

      $this->country = $this->ip_to_country($_SERVER["REMOTE_ADDR"]);
			$this->language = $this->get_language_from_country($this->country["prefix2"]);
		} else {
			$this->language = $this->rapyd->config->item("language");
		}	
		//$this->_load_language($this->language);
		return $this->language;
		
	}


	/**
	 * seve current detected language in session
	 *
	 * @access public 
	 * @return  void
	 */
	function save_language()
	{
		$this->set_language($this->language);
  }

	/**
	 * clear current stored language in session
	 *
	 * @access public 
	 * @return  void
	 */
	function clear_language()
	{
		$this->session->clear("language","rapyd");
  }

	/**
	 * set a new language env
	 *
	 * @access public 
	 * @param  string  $language  new language ("english", "italian", etc..)
	 * @return  void
	 */
	function set_language($language)
	{
		//languase is already in session
		if (isset($language) && in_array($language,$this->languages))
		{
			$this->language = $language;
		} else {
			$this->language = $this->rapyd->config->item("language");
		}	
		$this->session->save("language", $this->language, "rapyd");
		//$this->_load_language($this->language);
  }
  
	/**
	 * load rapyd components constats file
	 *
	 * @access public
	 * @return  void
	 */
	function load_language()
	
	{
		if (file_exists(RAPYD_PATH.'language/'.$this->language.EXT))
		{
			include_once(RAPYD_PATH.'language/'.$this->language.EXT);
		} else {
			show_error("Error, rapyd language not found: ".RAPYD_PATH.'language/'.$this->language.EXT);
		}
  }


############ ip-to-country functions #############

	/**
	 * convert an IP to a network address (to be compared with ip-to-country db)
	 * note: mysql and some dbs have this function builtin!
	 *
	 * @param  string  $ip of current user
	 * @return  string network address 
	 */
	function inet_aton($ip)
	{
		$iparr = explode(".",$ip);
		return ($iparr[0] * pow(256,3)) + ($iparr[1] * pow(256,2)) + ($iparr[2] * 256) + $iparr[3]; 
	}


	function ip_to_country($ip)
	{
		$address = $this->inet_aton($ip);

		$sql = "SELECT * FROM net_blocks WHERE $address>=ip_from AND $address<=ip_to";
		$query = $this->ci->db->query($sql);

		if ($query->num_rows() > 0)
		{
			$country = $query->row_array();
			return $country; 
		} else {
			return false;
		}
	}

	function get_language_from_country($country)
  {
    $ctl = $this->rapyd->config->item("country-to-language");
    if (array_key_exists($country, $ctl))
    {
      return $ctl[$country];
    } else {
      return $this->rapyd->config->item("language");
    }
    
  }


}

?>