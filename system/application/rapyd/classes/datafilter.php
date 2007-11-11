<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Rapyd Components
 *
 * An open source library for CodeIgniter application development framework for PHP 4.3.2 or newer
 *
 * @package		rapyd.components
 * @author		Felice Ostuni <felix@rapyd.com>
 * @license		http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright	Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version		0.7
 * @filesource
 */
 
/**
 * rapyd's commons functions inclusion
 */
require_once("dataobject.php");
require_once("dataform.php");

/**
 * DataEdit base class.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    0.7.1
 */
class DataFilter extends DataForm{


	
  var $_buttons = array();

  
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $title  output title
  * @param    string   $table  table-name to be filtered (you can leave it empty.. and preset BEFORE a complex join active-record query)
  * @return   void
  */
  function DataFilter($title=null, $table=null){
     
        
    parent::DataForm();
    
    //prepare active record query ("select" and "from")
    if(isset($table)){
      $this->db->select('*');
      $this->db->from($table);
    }    
    
    //assign an output title
    $this->title($title);
    
    $this->session =& $this->rapyd->session;
    
    //sniff current action
    $this->_sniff_action();
  
  }




  function _sniff_action(){
          
    $segment_array = $this->uri->segment_array();
    $segment_count = $this->uri->total_segments();
     
    //actions
    $do_search = array_search("search",$segment_array);
    $do_reset = array_search("reset",$segment_array);
    $do_orderby = array_search("orderby",$segment_array);    
    
    ///// search /////
    if ($do_search!==false)
    {
      $this->_action = "search";

      $process_uri_arr = array_slice($segment_array, 0, $do_search);
      
      
      $this->_process_uri = join("/",$process_uri_arr);
      
      $process_uri_arr[$do_search-1] = "reset";
      $this->_reset_uri = join("/",$process_uri_arr);
      
      //a reset action must reset also the order by order
      if ($do_orderby!==false)
      {
        $reset_uri_arr = array_slice($process_uri_arr, 0, $do_orderby-1);
        $this->_reset_uri = join("/",$reset_uri_arr)."/reset";
      }
      
      ## persistence
      if (count($_POST)<1){  
        $oldpost = $this->session->get($this->_process_uri,"rapyd");
        if ( isset($oldpost) ){
           $_POST = unserialize($oldpost);
        }
      } else {
        $this->session->save($this->_process_uri,serialize($_POST), "rapyd");
      }
    
      

    ///// reset /////
    } elseif ($do_reset!==false) {
    
      $this->_action = "reset";
      
      $reset_uri_arr = array_slice($segment_array, 0, $do_reset);
      $this->_reset_uri = join("/",$reset_uri_arr);
      $reset_uri_arr[$do_reset-1] = "search";
      $this->_process_uri = join("/",$reset_uri_arr);
   
      $this->session->clear($this->_process_uri,"rapyd");
      
    ///// show /////
    } else {


      if(ctype_digit(array_pop($segment_array)))
      {
        $base_uri = join("/",$segment_array);
      } else {
        $base_uri = join("/", $this->uri->segment_array());  // $this->uri->uri_string(); in some cases uri_string may return a slash at first char "/controller/function/param"
      }

      $this->_process_uri = $base_uri . "/search";
      
      //a reset action must reset also the order by order
      if ($do_orderby!==false)
      {
        $reset_uri_arr = array_slice($this->uri->segment_array(), 0, $do_orderby-1);
        $this->_reset_uri = join("/",$reset_uri_arr)."/reset";
      
      } else {
      
        $this->_reset_uri = $base_uri . "/reset";
        
      }
      $this->session->clear($this->_process_uri,"rapyd");
    
    }
    
    
  }


  function process(){
  
    $result = parent::process();
   
    switch($this->_action){
      
      case "search":

        // prepare the WHERE clause
        foreach ($this->_fields as $fieldname=>$field){
        
          if ($field->value!=""){
                        
            if (strpos($field->name,"_copy")>0){
              $name = substr($field->db_name,0,strpos($field->db_name,"_copy"));
            } else {
              $name = $field->db_name;
            }
            
            $field->_getValue();
            $field->_getNewValue();
            $value = $field->newValue;
            
						
						switch ($field->clause){
						
								case "like":
										$this->db->like($name, $value);
								break;
								
								case "orlike":
										$this->db->orlike($name, $value);
								break;
						
								case "where":
										$this->db->where($name." ".$field->operator, $value);
								break;
									 
								case "orwhere":
										$this->db->orwhere($name." ".$field->operator, $value);
								break;
						
							//..
						
						}
						


          }
        }
        
        $this->_build_buttons();
        $this->build_form();
      break;
      
      case "reset":
        //pulire sessioni 
        
        $this->_build_buttons();
        $this->build_form();
      break;
      
      default:
        $this->_build_buttons();
        $this->build_form();
      break;      
    }
    
  }



 /**
  * append a default button
  *
  * @access   public
  * @param    string  $name     a default button name ('modify','save','undo','backedit','back')
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */ 
  function crud_button($name="",$caption=null){
    $this->_buttons[$name]=$caption;
  }


  
 /**
  * append a set of default buttons
  *
  * @access   public
  * @param    mixed  $names   a list of button names.  For example 'modify','save','undo','backedit','back'
  * @return   void
  */ 
  function buttons($names){
    $buttons = func_get_args();
    foreach($buttons as $button){
      $this->crud_button($button);
    }
  }


 /**
  * build the appended buttons
  *
  * @access   private
  * @return   void
  */ 
  function _build_buttons(){
    foreach($this->_buttons as $button=>$caption){
      $build_button = "_build_".$button."_button";
      if ($caption == null){
        $this->$build_button();
      } else {
        $this->$build_button($caption);      
      }
    }
    $this->_buttons = array();
  
  }


 /**
  * append the default "save" button,  save is the button that appears in the top-right corner when the status is "create" or "modify"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_search_button($caption=RAPYD_BUTTON_SEARCH){
    $this->submit("btn_submit", $caption, "BL"); 
  }


 /**
  * append the default "back" button, back is the button that appears in the bottom-left corner when the status is "show"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_reset_button($caption=RAPYD_BUTTON_CLEAR){
  
    $action = "javascript:window.location='".site_url($this->_reset_uri)."'";
    $this->button("btn_reset", $caption, $action, "BL");
  }



 /**
  * process , main build method, it lunch process() method
  *
  * @access   public
  * @return   void
  */
  function build(){


    //sniff and build fields
    $this->_sniff_fields();
    

    $this->_built = true;
    
    $this->process();
    

  }

  

}


?>