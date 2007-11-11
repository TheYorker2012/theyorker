<?php
/**
 * textEditor - is a field that can be used as wysiwyg editor.
 * It's a replacer of textarea buided in javascript on tinyMCE library.<br />
 * It can acquire xhtml formatted text, and it's the ideal field for aquire an "article description"
 *
 * Important Note.. for keep compact the rapyd package.. tinyMCE (and his 2.000 files) is NOT included... you can download it from the autor website
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version 1.0
 */


/**
 * wysiwyg editor, js replacement of textarea field.
 * based on tinyMCE
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    1.0.1
 */
class editorField extends objField{
  
  var $type = "editor";


 /**
  * if a data source (dataobject) exist, it get the current value of field
  * this class override this method for "formatting" purposes
  *
  * @access   private
  * @return   void
  */
  function _getValue(){
    parent::_getValue();
  }

 /**
  * if detect a $_POST["fieldname"] it acquire the new value
  * this class override this method for "formatting" purposes
  *
  * @access   private
  * @return   void
  */
  function _getNewValue(){
    parent::_getNewValue();
  }


 /**
  * build (only) the field (widhout labels or borders)
  *
  * @access   public
  * @return   void
  */
  function build(){
  
    $output = "";
    
    rapydlib("tinymce");

    if(!isset($this->cols)){
      $this->cols = 42;
    }
    if(!isset($this->rows)){
      $this->rows = 15;
    }

    $this->_getValue();
    
    switch ($this->status){
      
      case "disabled":
      case "show":
        if (!isset($this->value)) {
          $output = RAPYD_FIELD_SYMBOL_NULL;
        } elseif ($this->value == ""){
          $output = "";
        } else {  
          $output = '<span style="font-size:9px;">'.nl2br(htmlentities($this->value)).'</span>';
        }
        break;

      case "create":
      case "modify":
        
        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'cols'        => $this->cols,
          'rows'        => $this->rows,          
          'onclick'     => $this->onclick,
          'onchange'    => $this->onchange,
          'class'       => "mceEditor",
          'style'       => $this->style);
        $output = form_textarea($attributes, $this->value);
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