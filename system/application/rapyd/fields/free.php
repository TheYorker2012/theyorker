<?php
/**
 * freeField - is a plain-text placeholder for forms
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version 1.0
 */
 
 
 /**
 * freeField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    1.0.1
 */
class freeField extends objField{

  var $type = "free";
  

  function freeField($label, $name, $content=""){
    parent::objField($label, $name);
    $this->value = $content;
  }


  function build(){

    $this->_getValue();
    
    $output = "";
    
    switch ($this->status){
    
      case "show":
      case "create":
      case "modify":
      
        $output = $this->value;
        break;
        
      case "hidden":
      
        $output = "";

        break;
        
      default:
    }
    $this->output = "\n".$output."\n";
  }
    
}
?>