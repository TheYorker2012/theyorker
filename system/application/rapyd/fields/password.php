<?php
/**
 * passwordField - is common input field (type=password) 
 * with ss encryption and frontend obsuration by *** chars
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version 1.0
 */
 
 
/**
 * passwordField
 *
 * @package    rapyd.components.fields
 * @author     Nick Crossland
 * @access     public
 */
class passwordField extends objField{

  var $type = "password";
  var $encrypt = true;
  // We can choose not to encrypt the value by using $form->password->encrypt = false;
  var $show_null = '** Not set (null) **';
  var $show_empty = '** Not set **';
  var $show_mask_encrypted = '** Encrypted **';
  var $show_mask_hidden = '** Hidden **';

  function _getValue(){
    parent::_getValue();
    
  }
  
  function _getNewValue(){
    parent::_getNewValue();
    if (isset($this->request[$this->name]) && $this->request[$this->name] != '' ){
        if ($this->encrypt) {
            $this->newValue = crypt($this->newValue);  // if a new text has been entered
        }
    } else {
      $this->newValue = $this->data->data[$this->name];// if no new text has been entered, keep the existing value
    }
  }

  function build(){
    if(!isset($this->size)){
      $this->size = 45;
    }
    $this->_getValue();
    
    $output = "";
    
    switch ($this->status){
    
      case "disabled":
      case "show":
        if ( (!isset($this->value)) ){
          $output = $this->show_null;
        } elseif ($this->value == ''){
          $output = $this->show_empty;
        } else {  
          $output = (($this->encrypt)?$this->show_mask_encrypted:$this->show_mask_hidden);
        }
        break;

      case "create":
      case "modify":
      
          
        $value = '';

        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => $this->type,          
          'value'       => '', // Do not show the value in modify form
          'maxlength'   => $this->maxlength,
          'size'        => $this->size,
          'onclick'     => $this->onclick,
          'onchange'     => $this->onchange,
          'style'       => $this->style);
        $output = form_input($attributes) . $this->extra_output;
        break;
        
      case "hidden":
      
        $output = form_hidden($this->name, $this->value);

        break;
        
      default:
    }
    $this->output = "\n".$output."\n";
  }
    
}
?>