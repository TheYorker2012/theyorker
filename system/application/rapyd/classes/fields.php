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
 
define('RAPYD_FIELD_SYMBOL_NULL',           '<em>[NULL]</em>');
define('RAPYD_FIELD_SYMBOL_TRUE',           '<img src="'.RAPYD_IMAGES.'/images/true.gif" />');
define('RAPYD_FIELD_SYMBOL_FALSE',          '<img src="'.RAPYD_IMAGES.'/images/false.gif" />');
define('RAPYD_FIELD_SYMBOL_REQUIRED',       '*');

/**
 * objField, normally you must to operate only with his descendant.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    0.7.1
 */
class objField {

  //main properties
  var $type = "field";
  
  var $label; 
  var $name; 
  var $data; //rapid dataobject
  var $db; //ci AR driver
  
  var $options = array(); //associative&multidim. array ($value => $description)
  var $operator = "";  //default operator in datafilter
	var $clause = "like";

  
  //field actions & field status
  var $status = "show";  //can be also: create/modify
  var $action = "idle";  //can be also: insert/update 
  var $when = null;
  var $mode = null;
  var $apply_rules = true;
  var $_required = "";
  
  //data settings
  var $newValue;
  var $insertValue = null;
  var $updateValue = null;  
  var $requestRefill = true;
  var $is_refill  = false;
  var $save_error = null;
  
  
  //other attributes
  var $maxlength;
  var $size;
  var $onclick;
  var $onchange;
  var $style;
  var $extra_output;
  
  //unused
  var $externalTable;
  var $externalJoinField;
  var $externalReplaceField;
  

  
  // layout
  var $layout = array("fieldSeparator"  => "<br />", 
                      "optionSeparator" => "");
  var $winWidth  = "500";
  var $winHeight = "400";
  var $winParams = "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes";
  
  // configurations
  var $config = array("optionSerializeSeparator" => "|");
  var $output = "";

 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $label   the field label
  * @param    string   $name    the field name/identifier
  * @return   void
  */
  function objField($label, $name){
  
    $this->ci =& get_instance();
    $this->rapyd =& $this->ci->rapyd;

    //load needed libraries 
    if (!isset($this->ci->validation)) {
      $this->ci->load->library('validation');
    }
    if (!isset($this->ci->upload)) {
      $this->ci->load->library('upload');
    }
    if (!isset($this->ci->image_lib)) {
      $this->ci->load->library('image_lib');
    }
    
    //load needed helpers 
    if (!isset($this->ci->load->helpers['form_helper'])) {
      $this->ci->load->helper('form_helper');
    }
    if (!isset($this->ci->load->helpers['url'])) { 
      $this->ci->load->helper('url');
    }
    if (!isset($this->ci->load->helpers['date'])) {  
      $this->ci->load->helper('date');
    }
    if (!isset($this->ci->load->helpers['text'])) {  
      $this->ci->load->helper('text');
    }


    $this->validation =& $this->ci->validation;
    $this->upload =& $this->ci->upload;
    $this->input =& $this->ci->input;
    $this->uri =& $this->ci->uri;    
    $this->image_lib =& $this->ci->image_lib;
    
    static $id = 0;
    $this->identifier = "field".$id++;
    $this->request = $_POST;
    
		$this->name = str_replace(".","_",$name);
		$this->db_name = $name;
    
    $this->label = $label;
    $this->value = null;
  }
  
 /**
  * it get the current value of field
  *
  * - if a default value is setted (insertValue, updateValue..)
  * - or if a data source (dataobject) exist.
  *
  * @access   private
  * @return   void
  */
  function _getValue(){
  
    if (($this->requestRefill == true) && isset($this->request[$this->name]))
    {
      $requestValue = $this->input->post($this->name); 
      if (get_magic_quotes_gpc()) $requestValue = stripslashes($requestValue);
      $this->value = $requestValue;
      $this->is_refill = true;        
    } elseif (($this->status == "create") && ($this->insertValue != null))
    {
      $this->value = $this->insertValue;
    } elseif (($this->status == "modify") && ($this->updateValue != null))
    {
      $this->value = $this->updateValue;
    } elseif ((isset($this->data)) && ($this->data->loaded) && (!isset($this->request[$this->name]))&&(isset($this->db_name)) )
    {
      $this->value = $this->data->get($this->db_name);
    }

    $this->_getMode();    
  }

 /**
  * if detect a $_POST["fieldname"] it acquire the new value
  *
  * or if the field action is forced to "insert" or "update" 
  * note: in descendant classes you can override this method for "formatting" purposes
  *
  * @access   private
  * @return   void
  */
  function _getNewValue(){
    if (isset($this->request[$this->name])){
      if ($this->status == "create"){
        $this->action = "insert";
      } elseif ($this->status == "modify"){
        $this->action = "update";      
      }
      $requestValue = $this->input->post($this->name); //$this->request[$this->name];
      if (get_magic_quotes_gpc()) $requestValue = stripslashes($requestValue);
      $this->newValue = $requestValue;
    } elseif( ($this->action == "insert") && ($this->insertValue != null)) {
      $this->newValue = $this->insertValue;
    } elseif( ($this->action == "update") && ($this->updateValue != null)) {
      $this->newValue = $this->updateValue;
    } else {
      $this->action = "idle";
    }
  }



 /**
  * change field status for manage special fields (hiddens, read only, and so on)
  *
  * @access   private
  * @return   void
  */
  function _getMode(){
    switch ($this->mode){
    
      case "autohide":
        if (($this->status == "modify")||($this->action == "update")){
          $this->status = "show";
          $this->apply_rules = false;
        }
         
        break;
      case "readonly":
        $this->status = "show";
        $this->apply_rules = false;
        break;
      case "show":
        break;      
      default:
    }
    
    if (isset($this->when)){
      if (!in_array($this->status,$this->when)){
	        $this->status = "hidden";
	    }
	  }
  }
  

 /**
  * options are necessary for multiple value inputs (like select, radio.. )
  * this method get an associative array of possible options by a given SQL string
  *
  * @access   public
  * @param    mixed   $options  can be a "select" query or a multidim. array of values
  * @return   void
  */
  function options($options){
  
    if (is_array($options)){
      
      foreach ($options as $key=>$value){
          $this->option($key, $value);
      }
    
    } else {
      //load needed libraries 
      if (!isset($this->ci->db)) {
        $this->ci->load->library('database');
      }
      $this->db =& $this->ci->db;
      $query = $this->db->query($options);

      $result = $query->result_array();
      
      $new_options = array();
      
      if ($query->num_rows() > 0){
      
        foreach ($result as $row){
          $values = array_values($row);
          if (count($values)===2){
            
            $this->option($values[0], $values[1]);
            
          }
        }
      }
    }

  }


  function option($value,$description){
  
    $this->options[$value] = $description;

  }




 /**
  * one of the most important methods
  * when it's called, the data source of field (a dataobject) set the eventual new value.
  *
  * @access   public
  * @param    string  $save  if true, the dataobject is forced to save/store new value.
  * @return   bool
  */
  function autoUpdate($save=false){

    $this->_getValue();

    $this->_getNewValue();

    if (is_object($this->data)&& isset($this->db_name)){

      if (isset($this->newValue)){
        $this->data->set($this->db_name,$this->newValue);
      } else {
        $this->data->set($this->db_name,$this->value);         
      }
      if($save){
        return $this->data->save();
      }
    }
    return true;

  }




 /**
  * build (only) the field (widhout labels or borders)
  *
  * @access   public
  * @return   string  the field output 
  */
  function build(){
    $this->_getValue();

    switch ($this->status){
      case "show":
  
        $output = $this->value;
        break;
        
      default:
    }
    return $this->output = $output.$this->extra_output."\n";
  }
  
  
 /**
  * append text to field output
  *
  * @access   public
  * @access   string  $text (or html to be appended)
  * @return   void
  */
  function append($text){
    $this->extra_output .= $text;
  }  
  
  
 /**
  * draw, build & print the component
  *
  * @access   public
  * @return   void
  */
  function draw(){
    $this->buildRow();
    echo $this->output;
  }   

}


/**
 * all the extended fields
 *
 */
include_once(RAPYD_PATH."fields/input.php");
include_once(RAPYD_PATH."fields/dropdown.php");
include_once(RAPYD_PATH."fields/textarea.php");
include_once(RAPYD_PATH."fields/checkbox.php");
include_once(RAPYD_PATH."fields/datetime.php");
include_once(RAPYD_PATH."fields/editor.php");
include_once(RAPYD_PATH."fields/autoupdate.php");
include_once(RAPYD_PATH."fields/submit.php");
include_once(RAPYD_PATH."fields/reset.php");
include_once(RAPYD_PATH."fields/button.php");
include_once(RAPYD_PATH."fields/upload.php");

include_once(RAPYD_PATH."fields/free.php");
include_once(RAPYD_PATH."fields/container.php");
include_once(RAPYD_PATH."fields/iframe.php");
include_once(RAPYD_PATH."fields/colorpicker.php");
include_once(RAPYD_PATH."fields/html.php");
include_once(RAPYD_PATH."fields/password.php");
/*
//to be continued..
include_once("radio.php");
include_once("captcha.php");
*/
?>