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
 * @version		0.9
 * @filesource
 */ 
 
/**
 * rapyd's commons functions inclusion
 */
require_once("dataobject.php");
require_once("fields.php");

/**
 * DataForm base class.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @author     Thierry Rey 
 * @access     public
 */
class DataForm{

  //private
  var $_title = "";
  var $_status;
  var $_action = "idle";
  var $_dataobject;
  var $_fields = array();
  var $_multipart = false;
  var $_on_show = false;
  var $_on_error = false;
  var $_on_success = false;
  var $_built = false;
  var $_button_container = array( "TR"=>array(), "BL"=>array(), "BR"=>array() );
  var $_button_status = array();
  var $_script = array( "show"=>"", "create"=>"", "modify"=>"", "idle"=>"", "reset"=>"");

  
  //public
  var $cid  = "df";
  var $data = array();
  var $errors = array();
  var $error_string = "";
  var $output = "";
  var $default_group;

  
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $process_uri  uri/post action, if DF is used in a controller uri "contoller/registration".. it must have one segment more: "controller/registration/process"
  * @param    object   $data  a dataobject instance, if it's loaded.. the form is pre-filled by record values, and exec an update, else it's empty and exec an insert, if dataobject "is null".. the dataform is just a form helper. (with CI validations)
  * @return   void
  */
  function DataForm($process_uri=null, $dataobject=null){
  
    $this->ci =& get_instance();
    $this->rapyd =& $this->ci->rapyd;
    $this->uri =& $this->ci->uri;


    //load needed libraries 
    if (!isset($this->ci->validation)) {
      $this->ci->load->library('validation');
    }
		$this->validation =& $this->ci->validation;
    if (!isset($this->ci->db)) {
      $this->ci->load->database();
    }
		$this->db =& $this->ci->db;
		
    //load needed helpers 
    if (!isset($this->ci->load->helpers['form_helper'])) { 
      $this->ci->load->helper('form');
    }
    if (!isset($this->ci->load->helpers['url_helper'])) { 
 
      $this->ci->load->helper('url');
    }
    
    if (!isset($process_uri)){
       //prendere l'ultimo segmento.. e se � process.. non aggiungere "/process" al corrente uri_string
      $this->_process_uri = $this->uri->uri_string()."/process";
    } else {
      $this->_process_uri = $process_uri;
    }
  

    //detect form status (output)
    if (isset($dataobject)){
			if (strtolower(get_class($dataobject))=="dataobject"){
        $this->_dataobject =& $dataobject;
			} else {
				$this->_dataobject =& new DataObject($dataobject);
			}
      if ($this->_dataobject->loaded){
        $this->_status = "modify";
      } else {
        $this->_status = "create";
      }
    } else {
      $this->_dataobject = null;
      $this->_status = "create";
    }
    
    static $identifier = 0;
    $identifier++;
    $this->cid = $this->cid . (string)$identifier;   
  }

  function title($title){
    $this->_title = $title;
  }
  

 /**
  * detect $form->field from properties, and populate an array
  *
  * @access   private
  * @return   void
  */ 
  function _sniff_fields(){
    $this->_fields = array();
    
    $object = (get_object_vars($this));
    foreach ($object as $property_name=>$property){
      if (is_object($property)){
        if (is_subclass_of($property, 'objField')){
          
          
          if ($property->type == "upload") {
            $this->_multipart = true;
          }

          if (isset($this->_dataobject)){

            $fields = $this->_dataobject->field_names;

            if (in_array($this->$property_name->db_name,$fields)||!$this->$property_name->db_name ){

              $this->$property_name->data =& $this->_dataobject;
              
            }
          }
          $this->$property_name->status = $this->_status;
          
          if (isset($this->default_group) && !isset($this->$property_name->group)){
            $this->$property_name->group = $this->default_group;
          }
          
          if (isset($this->$property_name->rule)){
            if ((strpos($this->$property_name->rule,"required")!==false) && !isset($this->$property_name->no_star) ){
              $this->$property_name->_required = "*";
            }
          }
          
          $this->$property_name->build();

          $this->_fields[$property_name] =& $this->$property_name;
          
        }
      }
    }

  }


  function button_status($name, $caption, $action, $position="BL", $status="create", $class="button"){
     $this->_button_status[$status][$position][] = HTML::button($name, $caption, $action, "button", $class);
  }

  function button($name, $caption, $action, $position="BL"){
     $this->_button_container[$position][] = HTML::button($name, $caption, $action, "button", "button");
  }

  function submit($name, $caption, $position="BL"){
     $this->_button_container[$position][] = HTML::button($name, $caption, "", "submit", "button");
  }

  function script($script, $status="create"){
     $this->_script[$status] .= $script;
  }

  function pre_process($action,$function,$arr_values=array()){
    $this->_dataobject->pre_process($action,$function,$arr_values);
  }
  
  function post_process($action,$function,$arr_values=array()){
    $this->_dataobject->post_process($action,$function,$arr_values);
  }
  
  
 /**
  * build the form output with current rapyd component theme.
  * Note, this is optional, you can use $form->build() and field-by-field output:
  * $form->validation->error_string 
  * $form->form_open 
  * $form->article_title->output 
  * $form->article_body->output 
  * ...
  *
  * @access   private
  * @return   void
  */
  function build_form(){
       
    if (!$this->_built) $this->build();
    

    if ($this->_multipart){
      $this->form_open = form_open_multipart($this->_process_uri);
    } else {
      $this->form_open = form_open($this->_process_uri);
    }
    $this->form_close = form_close();
		
		
		$this->rapyd->set_view_path();
		
		$data["title"] = "";
		$data["error_string"] = "";
		$data["form_scripts"] = "";
		$data["container_tr"] = "";
		$data["container_bl"] = "";
		$data["container_br"] = "";
		
		//title
		$data["title"] = $this->_title;

		//buttons
    if ( (count($this->_button_container["TR"])>0) || (isset($this->_button_status[$this->_status]["TR"])) ){
      if (isset($this->_button_status[$this->_status]["TR"])){
        foreach ($this->_button_status[$this->_status]["TR"] as $state_buttons){
          $this->_button_container["TR"][] = $state_buttons;
        }
      }
			$data["container_tr"] = join("&nbsp;", $this->_button_container["TR"]);
    }
    if ( (count($this->_button_container["BL"])>0) || (isset($this->_button_status[$this->_status]["BL"])) ){
      if (isset($this->_button_status[$this->_status]["BL"])){
        foreach ($this->_button_status[$this->_status]["BL"] as $state_buttons){
          $this->_button_container["BL"][] = $state_buttons;
        }
      }
			$data["container_bl"] = join("&nbsp;", $this->_button_container["BL"]);
    }
    if ( (count($this->_button_container["BR"])>0) || (isset($this->_button_status[$this->_status]["BR"])) ){
      if (isset($this->_button_status[$this->_status]["BR"])){
        foreach ($this->_button_status[$this->_status]["BR"] as $state_buttons){
          $this->_button_container["BR"][] = $state_buttons;
        }
      }
			$data["container_br"] = join("&nbsp;", $this->_button_container["BR"]);
    }
    
		$data["form_scripts"] = HTML::javascriptTag($this->_script[$this->_status]);
		$data["title"] = $this->_title;
		$data["error_string"] = $this->error_string;
		$data["form_begin"] = $this->form_open;
		$data["form_end"] = $this->form_close;

    foreach ( $this->_fields as $field_name => $field_ref ) {
      if (isset($field_ref->group)){
        $ordered_fields[$field_ref->group][] = $field_name; 
      } else {
        $ordered_fields["ungrouped"][] = $field_name;
      }
    }
   
    foreach ($ordered_fields as $group=>$fields){
      
			unset($gr);

			$gr["group_name"] = $group;

      foreach ($fields as $field_name ) {
        $field_ref =& $this->$field_name;

        if ($field_ref->label!=""){
					$fld["field_tr"] = 'id="tr_'.$field_ref->name.'"';
					$fld["field_td"] = 'id="td_'.$field_ref->name.'"';
					$fld["label"] = $field_ref->label.$field_ref->_required;
					$fld["field"] = $field_ref->output;
					$fld["type"] = $field_ref->type;
          $fld["status"] = $field_ref->status;
					$gr["fields"][] = $fld;
        }
        
      }
			$grps[] = $gr;

    }
    $data["groups"] = $grps;

		$this->output = $this->ci->load->view('dataform', $data, true);

		$this->rapyd->reset_view_path();

		return  $this->output;
    
  }




 /**
  *
  */
  function build_message_form($message){
       
    if (!$this->_built) $this->build();

    $this->form_open = form_open($this->_process_uri);
    $this->form_close = form_close();
    
		$data["title"] = "";
		$data["error_string"] = "";
		$data["form_scripts"] = "";
		$data["container_tr"] = "";
		$data["container_bl"] = "";
		$data["container_br"] = "";
		
		$this->rapyd->set_view_path();
		
		//title
		$data["title"] = $this->_title;

		//buttons
		if (count($this->_button_container["TR"])>0){
			$data["container_tr"] = join("&nbsp;", $this->_button_container["TR"]);
		}
		if (count($this->_button_container["BL"])>0){
			$data["container_bl"] = join("&nbsp;", $this->_button_container["BL"]);
		}
		if (count($this->_button_container["BR"])>0){
			$data["container_br"] = join("&nbsp;", $this->_button_container["BR"]);
		}

		$data["message"] = $message;
		$data["form_begin"] = $this->form_open;
		$data["form_end"] = $this->form_close;
    
		$this->output = $this->ci->load->view('dataform', $data, true);

		$this->rapyd->reset_view_path();

		return  $this->output;
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
    
    //form open
    if ($this->_multipart){
      $this->form_open = form_open_multipart($this->_process_uri);
    } else {
      $this->form_open = form_open($this->_process_uri);
    }
    $this->form_close = form_close();


    //detect action
    $current_uri = join("/",$this->uri->segment_array());
  
    if ( isset($_POST) && ($current_uri == $this->_process_uri) ){

      $this->_action = ($this->_status=="modify")?"update":"insert";
    }
    
    $this->_built = true;
    
    //process
    $this->process();
    
  }



 /**
  * validation passed?
  *
  * @access   private
  * @return   bool  validation passed?
  */ 
  function is_valid(){
      
    //some fields mode can disable or change some rules.
    foreach ($this->_fields as $field_name => $field_copy){

      //reference
      $field =& $this->$field_name;
      $field->action = $this->_action;
      $field->_getMode();
      
      if (isset($field->rule)){

        if (($field->type != "upload") && $field->apply_rules){
          $fieldnames[$field->name] = $field->label;
          $rules[$field->name]	= $field->rule;
        } else {
          $field->_required = "";
        }
      }

    }
    
    if (isset($rules)){
      $this->validation->set_rules($rules);
      $this->validation->set_fields($fieldnames);     
    }
    if ((count($_POST) == 0 || count($this->validation->_rules)==0)){
      return true;
    } 

    $result = $this->validation->run();
    $this->error_string = $this->validation->error_string;
    
    return $result;
    
  }




 /**
  * process form, and perform dataobject action (update/insert)
  *
  * @access   public
  * @return   string   component html output
  */
  function process(){
    
    //database save
    switch($this->_action){
    
      case "update":
      case "insert":

        //validation failed
        if (!$this->is_valid()){
        
          $this->_on_show = false;
          $this->_on_success = false;
          $this->_on_error = true;
 
          foreach ($this->_fields as $field){
            $field->action = "idle";
          }
          return false;
          
        } else {
        
          $this->_on_show = false;
          $this->_on_success = true;
          $this->_on_error = false;
        }

        foreach ($this->_fields as $field){
          $field->action = $this->_action;
          $result = $field->autoUpdate(); 
          if (!$result){
            $this->_on_show = false;
            $this->_on_success = false;
            $this->_on_error = true;

            $this->error_string = $field->save_error;
            
            return false;
          }
          
        } 
        if (isset($this->_dataobject)){
          $return = $this->_dataobject->save();
        } else {
          $return = true;
        }
        
        if (!$return){
        	if($this->_dataobject->pre_process_result===false)$this->error_string .= ($this->_action=="update")?$this->_dataobject->error_message_ar['pre_upd']:$this->_dataobject->error_message_ar['pre_ins'];
          $this->_on_show = false;
          $this->_on_success = false;
          $this->_on_error = true;
        }
        
        return $return;
        
        break;
        
      case "delete":
        $return = $this->_dataobject->delete();
        
        if (!$return){
        	if($this->_dataobject->pre_process_result===false)$this->error_string .= $this->_dataobject->error_message_ar['pre_del'];
          $this->_on_show = false;
          $this->_on_success = false;
          $this->_on_error = true;
        } else {
          $this->_on_show = false;
          $this->_on_success = true;
          $this->_on_error = false;
        }
        
        break;
        
      case "idle":
          $this->_on_show = true;
          $this->_on_success = false;
          $this->_on_error = false;
          return true;
        break;
        
      default:
       return false;
    
    }
    
  }

  function on_show(){
    return $this->_on_show;
  }

  function on_error(){
    return $this->_on_error;
  }
  
  function on_success(){
    return $this->_on_success;
  }
  

}


?>