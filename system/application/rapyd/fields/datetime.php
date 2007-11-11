<?php
/**
 * dateField buided on jscalendar lib
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @todo    bisogna usare "anche" le funzioni di adodb per acquisire la data 
 * @version 1.0
 */
 

require_once(RAPYD_PATH."helpers/datehelper.php");
require_once(RAPYD_PATH."helpers/html.php");

/**
 * dateField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    1.0.1
 */
class dateField extends objField{

  var $type = "date";
  var $format;

  //costruttore
  function dateField($label, $name, $format="us"){

    parent::objField($label, $name);
    $this->format = $format;
   
   $this->description = ($this->format=="eu")?'dd/mm/yyyy':'mm/dd/yyyy';
    
  }


  function _getValue(){
    
    parent::_getValue();
    
/*    if (isset($this->request[$this->name]) && !$this->is_refill){
      $this->value = human_to_dbdate($this->value, $this->format); 
    }*/
  }
  
  function _getNewValue(){
    parent::_getNewValue();
    if (isset($this->request[$this->name])){
      $this->newValue = human_to_dbdate($this->newValue, $this->format); 
    }
  }



  function build(){
  
    $this->_getValue();  
    $output = "";
  
    rapydlib("jscalendar");
  

    if(!isset($this->size)){
      $this->size = 25;
    }

   
  
    switch ($this->status){
      case "show":
        if (!isset($this->value)) {
          $value = RAPYD_FIELD_SYMBOL_NULL;
        } elseif ($this->value == ""){
          $value = "";
        } else {  
          $value = dbdate_to_human($this->value, $this->format);
        }
        $output = $value;
        break;

      case "create":
      case "modify":
        
        $value = "";
        
        //integrazione con jscalendar
        if ($this->value != ""){
           if ($this->is_refill){             
             $value = $this->value;
           } else {
             $value = dbdate_to_human($this->value, $this->format);
           }
        }

        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'value'       => $value,
          'size'        => $this->size,
          'onclick'     => $this->onclick,
          'onchange'    => $this->onchange,
          'style'       => $this->style);
        $output  = form_input($attributes); //'<div>'.
        $output .= ' <img src="'.RAPYD_DIR.'libraries/jscalendar/calender_icon.gif" id="'.$this->name.'_button" border="0" style="vertical-align:middle;" />'.$this->extra_output;
        $output .= HTML::javascriptTag('
         Calendar.setup({
        inputField  : "'.$this->name.'",
        ifFormat    : "'.datestamp_from_format($this->format).'",
        button      : "'.$this->name.'_button",
        align       : "Bl",
        singleClick : false,
        mondayFirst : true,
        weekNumbers : false
       });');
        
        break;

        
        
      case "disabled":
      
        //versione encoded 
        $output = dbdate_to_human($this->value, $this->format);
        break;
        
      case "hidden":
      
        $output = form_hidden($this->name, $this->value);
        break;
        
      default:
    }
    $this->output = $output;
  }
    
}
?>