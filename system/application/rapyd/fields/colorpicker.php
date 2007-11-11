<?php
/**
 * colorpickerField buided on a js/dhtmllib lib
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @todo    bisogna usare "anche" le funzioni di adodb per acquisire la data 
 * @version 1.0
 */
 

require_once(RAPYD_PATH."helpers/html.php");

/**
 * dateField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    1.0.1
 */
class colorpickerField extends objField{

  var $type = "colorpicker";

  //costruttore
  function colorpickerField($label, $name, $format="us"){

    parent::objField($label, $name);

    
  }


  function _getValue(){
    parent::_getValue();
  }
  
  function _getNewValue(){
    parent::_getNewValue();
  }



  function build(){
  
    $this->_getValue();  
    $output = "";
  
    rapydlib("colorpicker");
  

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
          $value = $this->value;
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
             $value = $this->value;
           }
        }


        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => $this->type,          
          'value'       => $value,
          'maxlength'   => $this->maxlength,
          'size'        => $this->size,
          'onclick'     => $this->onclick,
          'onchange'     => $this->onchange,
          'style'       => $this->style);

        $output  = form_input($attributes); 
				$output .= '<img src="'.RAPYD_DIR.'images/spacer.gif" style="width:15px;height:15px;background-color:'.$value.';vertical-align:middle;" id="'.$this->name.'_image_pick" /> <A HREF="#" onClick="cp2.select(document.getElementById(\''.$this->name.'\'),\''.$this->name.'_pick\');return false;" NAME="'.$this->name.'_pick" ID="'.$this->name.'_pick">Tavolozza</A>' . $this->extra_output;
				$output .= HTML::javascriptTag('cp2.writeDiv()');
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