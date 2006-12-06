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
 * @version		0.8
 * @filesource
 */
 
/**
 * ancestor 
 */
require_once("dataform.php");

/**
 * DataEdit base class.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @author     Thierry Rey
 * @access     public
 */
class DataEdit extends DataForm{

  var $back_url = "";
  var $check_pk = true;
  
  var $_postprocess_uri = "";

  var $_undo_uri = "";
  var $_buttons = array();
	var $_pkey =0;

  
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    string   $title  widget title
  * @param    string   $table  db-tablename to be edited
  * @return   void
  */
  function DataEdit($title, $table){
     
     $dataobject = new DataObject($table);
     parent::DataForm(null, $dataobject);

     $this->_pkey = count($this->_dataobject->pk);

     $this->_sniff_status();
     $this->title($title);
  }
  

 /**
  * transforn a PK array in the same format of the one used in DO->load() function ie: array(pk1=>value1, pk2=>value2) 
  * in a string formated as we attent at the end (pk part)of the URI (as explain in conventions)=>/pk1_name/pk1_value/pk2_name/pk2_value/...
  * @access   private
  * @param    array   
  * @return   string
  */
	function pk_to_URI($pk)
	{
		  $result="";
		  foreach ($pk as $keyfield => $keyvalue){
		  	$result.= "/".$keyvalue;	
			}
			return $result;
	}
	/**
  * rebuild the PK array in the same format of the one used in DO->load() function ie: array(pk1=>value1, pk2=>value2) 
  * from the string formated as we attent at the end (pk part)of the URI (as explain in conventions)=>/pk1_name/pk1_value/pk2_name/pk2_value/...
  * @access   private
  * @param    string   
  * @return   array
  */
	function URI_to_pk($id_str , $do)
	{
		  $result=array();
	
		  //check and remove for '/' in first and last position for that explode work fine.
      /*
      if (strlen($id_str)>1)
      {
        if(strpos($id_str,'/') !== false)
        {
          if(strpos($id_str,'/') == 0){ $id_str = substr($id_str,1); }
          if(strpos($id_str,'/') == strlen($id_str)-1){ $id_str = substr($id_str,0,-1); }
        }
      }
      */
      $tmp_ar = explode("/",$id_str);
			$keys = array_keys($do->pk);
		 	for($i=0;$i <= count($tmp_ar)-1;$i++){
		 		$result[$keys[$i]]=$tmp_ar[$i];
		 	}

		 	return $result;
	}
	/**
	* rebuild the string formated as we attent at the end (pk part)of the URI (as explain in conventions)=>/pk1_name/pk1_value/pk2_name/pk2_value/...
	* without the first slash 
  * from the segment_array 
  * @access   private
  * @param    array    
  * @return   string
  */	
	function segment_id_str($segment_ar)
	{
		$id_segment = array_slice($segment_ar,-($this->_pkey));
		return join('/',$id_segment);
	}

  function _sniff_status(){
   
   $this->_status = "idle";
          
          
    $segment_array = $this->uri->segment_array();
    $segment_count = $this->uri->total_segments();
    $id_str = $this->segment_id_str($segment_array);
    
    $is_show = array_search("show",$segment_array);
    $is_modify = array_search("modify",$segment_array);
    $is_create = array_search("create",$segment_array);
    $is_delete = array_search("delete",$segment_array);
   
    //sanity check on the URI (can be remove) in testing phase.
    $check_array=array();
    if($is_show!==false)array_push($check_array,$is_show);
    if($is_modify!==false)array_push($check_array,$is_modify);
    if($is_create!==false)array_push($check_array,$is_create);
    if($is_delete!==false)array_push($check_array,$is_delete);
    if(count($check_array)!=1)return FALSE;
     
    ///// show /////
    if ($is_show!==false){	

      if (($segment_count>1) && ($is_show == $segment_count -($this->_pkey)))
      {
        $this->_status = "show";
        $this->_process_uri = "";
        $result = $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
        
        if (!$result){
          $this->_status = "unknow_record";
        } 

      }
     
    ///// modify /////
    } elseif ($is_modify!==false) { 
    
      if (($segment_count>1) && ($is_modify == $segment_count -($this->_pkey )))
      {
    
        $segment_array[$is_modify] = "update";
        $this->_status = "modify";
        $this->_process_uri = join("/",$segment_array);

        $result = $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
        if (!$result){
          $this->_status = "unknow_record";
        }
      }
    
    ///// create /////
    } elseif ($is_create!==false) { 
    
      if (($segment_count>0) && ($is_create == $segment_count))
      {
        $segment_array[$is_create] = "insert";
        $this->_status = "create";
        $this->_process_uri = join("/",$segment_array);
      }
    
    ///// delete /////
    } elseif ($is_delete!==false) {
    
      if (($segment_count>1) && ($is_delete == $segment_count-($this->_pkey)))
      {

        $pos = $is_delete;
        $segment_array[$pos] = "do_delete";
        $this->_status = "delete";
        $this->_process_uri = join("/",$segment_array);
        $segment_array[$pos] = "show";          
        $this->_undo_uri = join("/",$segment_array);

        $result = $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
        if (!$result){
          $this->_status = "unknow_record";
        }

      }  
    }
  }


  function _sniff_action(){
          
    $segment_array = $this->uri->segment_array();
    $segment_count = $this->uri->total_segments();
    $id_str = $this->segment_id_str($segment_array);
    
    //actions
    $do_insert = array_search("insert",$segment_array);
    $do_update = array_search("update",$segment_array);
    $do_delete = array_search("do_delete",$segment_array);

    //sanity check on the URI (can be remove) in testing phase.
    $check_array=array();
    if($do_insert!==false)array_push($check_array,$do_insert);
    if($do_update!==false)array_push($check_array,$do_update);
    if($do_delete!==false)array_push($check_array,$do_delete);
    if(count($check_array)!=1)return FALSE;
       
    ///// insert /////
    if ($do_insert!==false){ 
      if (($segment_count>0) && ($do_insert == $segment_count))
      {
        $segment_array[$do_insert] = "show";
        $this->_action = "insert";
        $this->_postprocess_uri = join("/",$segment_array);
      }
     
    ///// update /////
    } elseif ($do_update!==false) {
    
      if (($segment_count>1) && ($do_update == $segment_count-($this->_pkey)))
      {
        $segment_array[$do_update] = "show";
        $this->_action = "update";
        $clean_segment = array_slice($segment_array,0,$segment_count-($this->_pkey));
        $this->_postprocess_uri = join("/",$clean_segment);

        $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
      }
      
    ///// delete /////
    } elseif ($do_delete!==false) {
    
      if (($segment_count>1) && ($do_delete == $segment_count -($this->_pkey)))
      {

        $this->_action = "delete";
        $result = $this->_dataobject->load($this->URI_to_pk($id_str,$this->_dataobject));
        if (!$result){
          $this->_status = "unknow_record";
        } 

      }    
    }
  }



  function is_valid(){
    $result = parent::is_valid();

    if (!$this->check_pk) return $result;

    if ($this->_action=="update" || $this->_action=="insert"){
      $pk_check=array();
      $pk_error = "";
      $hiddens = array();
      
      //pk fields mode can setted to "autohide" or "readonly" (so pk integrity violation check isn't needed)
      foreach ($this->_fields as $field_name => $field_copy){
        //reference
        $field =& $this->$field_name;
        $field->_getValue();
        if (!$field->apply_rules){
          $hiddens[$field->db_name] = $field->value;
        }
      }
          
      //We build a pk array from the form value that is submit if its a writing action (update & insert)
      foreach ($this->_dataobject->pk as $keyfield => $keyvalue){
        if (isset($this->validation->$keyfield)){
          $pk_check[$keyfield] = $this->validation->$keyfield;
        // detect that a pk is hidden, so no integrity check needed
        } elseif (array_key_exists($keyfield,$hiddens)){
          $pk_check[$keyfield] = $hiddens[$keyfield];
        }
      }
      
      if (sizeof($pk_check) != $this->_pkey){
      //If PK is Autoincrement we don't need to check PK integrity, But its supose that for a none AutoIcrement PK the form always contain the right PK fields
        if (sizeof($this->_dataobject->pk)==1 && sizeof($pk_check)==0)return $result;
      }
      // this check the unicity of PK with the new DO function
      if ($result && !$this->_dataobject->are_unique($pk_check)){
        $result = false;
        $pk_error .= RAPYD_MSG_0210."<br />";
      }

    }
    $this->error_string = $pk_error.$this->error_string;
    return $result;
  }



  function process(){
  
    $result = parent::process();
   
    switch($this->_action){
      
      case "update": 
        if ($this->on_error()){
          $this->_status = "modify";
          $this->_process_uri = $this->uri->uri_string();
          $this->_sniff_fields();
          $this->_build_buttons();
          $this->build_form();
        }
        if ($this->on_success()){

        	$this->_postprocess_uri .= $this->pk_to_URI($this->_dataobject->pk);
          //$this->_exec_post_process_functions("update"); 
          redirect("/".$this->_postprocess_uri,'refresh');
        }
      break;
      
      case "insert":  
        if ($this->on_error()){
          $this->_status = "create";
          $this->_process_uri = $this->uri->uri_string();
          $this->_sniff_fields();
          $this->_build_buttons();
          $this->build_form();
        }
        if ($this->on_success()){

          $this->_postprocess_uri .= $this->pk_to_URI($this->_dataobject->pk);
          //$this->_exec_post_process_functions("insert");          
          redirect($this->_postprocess_uri,'refresh');
        }
      break;
      
      case "delete": 
        if ($this->on_error()){
          $this->_build_buttons();
          $this->build_message_form(RAPYD_MSG_0206);
        }
        if ($this->on_success()){
          $this->_build_buttons();
          $this->build_message_form(RAPYD_MSG_0202);
          //$this->_exec_post_process_functions("delete");          
        }
      break;
      
    }
    
    switch($this->_status){
    
      case "show":      
      case "modify":
      case "create":
        $this->_build_buttons();
        $this->build_form();
      break;
      case "delete":
        $this->_build_buttons();
        $this->build_message_form(RAPYD_MSG_0209);
      break;
      case "unknow_record":
        $this->_build_buttons();
        $this->build_message_form(RAPYD_MSG_0208);
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
  * append the default "modify" button, modify is the button that appears in the top-right corner when the status is "show"
  *
  * @access   public
  * @param    string $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_modify_button($caption=RAPYD_BUTTON_MODIFY){
    if ($this->_status == "show"){
      $segment_array = $this->uri->segment_array();
      $is_show = array_search("show",$segment_array);
      if ($is_show!==false){
        $segment_array[$is_show] = "modify";
        
        $modify_uri = join("/",$segment_array);
        $action = "javascript:window.location='" . site_url($modify_uri) . "'";
        $this->button("btn_delete", $caption, $action, "TR"); 
      }
    }
  }

 /**
  * append the default "delete" button, delete is the button that appears in the top-right corner when the status is "show"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_delete_button($caption=RAPYD_BUTTON_DELETE){

    if ($this->_status == "show"){
      
      $segment_array = $this->uri->segment_array();
      $is_show = array_search("show",$segment_array);
      if ($is_show!==false){
        $segment_array[$is_show] = "delete";
        
        $delete_uri = join("/",$segment_array);
        $action = "javascript:window.location='" . site_url($delete_uri) . "'";
        $this->button("btn_delete", $caption, $action, "TR"); 
      }
    } elseif($this->_status == "delete") {
     
        $action = "javascript:window.location='" . site_url($this->_process_uri) . "'";
        $this->button("btn_delete", $caption, $action, "BL"); 
    }
  }

 /**
  * append the default "save" button,  save is the button that appears in the top-right corner when the status is "create" or "modify"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_save_button($caption=RAPYD_BUTTON_SAVE){
    if (($this->_status == "create") || ($this->_status == "modify")){  
      $this->submit("btn_submit", $caption, "TR"); 
    }
  }

 /**
  * append the default "undo" button, undo is the button that appears in the top-right corner when the status is "create" or "modify"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_undo_button($caption=RAPYD_BUTTON_UNDO){
  
    if ($this->_status == "create"){
      $action = "javascript:window.location='{$this->back_url}'";
      $this->button("btn_undo", $caption, $action, "TR"); 
     
    } elseif($this->_status == "modify") {
    
      $segment_array = $this->uri->segment_array();
      $is_modify = array_search("modify",$segment_array);
      $is_modify_onerror = array_search("update",$segment_array);
      
      if ($is_modify!==false){
        $segment_array[$is_modify] = "show";
      } elseif ($is_modify_onerror!==false){
        $segment_array[$is_modify_onerror] = "show";
      }
      $undo_uri = join("/",$segment_array);
      $action = "javascript:window.location='" . site_url($undo_uri) . "'";
      
      $this->button("btn_undo", $caption, $action, "TR"); 
      
    } elseif($this->_status == "delete") {
      $undo_uri = site_url($this->_undo_uri);
      $action = "javascript:window.location='$undo_uri'";
      $this->button("btn_undo", $caption, $action, "TR"); 
    }
  }

 /**
  * append the default "back" button, back is the button that appears in the bottom-left corner when the status is "show"
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_back_button($caption=RAPYD_BUTTON_BACK){
    if (($this->_status == "show") || ($this->_status == "unknow_record") || ($this->_action == "delete")){
      $action = "javascript:window.location='{$this->back_url}'";
      $this->button("btn_back", $caption, $action, "BL");
    }
  }

 /**
  * append the default "backerror" button
  *
  * @access   public
  * @param    string  $caption  the label of the button (if not set, the default labels will used)
  * @return   void
  */
  function _build_backerror_button($caption=RAPYD_BUTTON_BACKERROR){
    if (($this->_action == "do_delete") && ($this->_on_error)){   
      $action = "javascript:window.history.back()";
      $this->button("btn_backerror", $caption, $action, "TR");       
    }
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
    
    //sniff and perform action
    $this->_sniff_action();


    $this->_built = true;
    
    $this->process();
    

  }
}


?>