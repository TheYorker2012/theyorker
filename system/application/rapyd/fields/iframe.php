<?php
/**
 * containerField - is a plain-text container (of fields) for forms
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni <felix@rapyd.com>
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version 0.9
 */
 
 
 /**
 * containerField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni
 * @author     Thierry Rey
 * @access     public
 */
class iframeField extends objField{

  var $type = "iframe";

  var $fieldList = array();
	var $url ="";
	
  function iframeField($name, $uri, $height="200", $scrolling="auto", $frameborder="0"){
    $label = $name;
    parent::objField($label, $name);
    $this->db_name = null;
    
    $this->url = site_url($uri);
    $this->value = '<IFRAME src="<##>" width="100%" height="'.$height.'" scrolling="'.$scrolling.'" frameborder="'.$frameborder.'" id="'.$name.'">Usa un browser che supporti i frame!</IFRAME>';

    //I test if the URI contain field pattern
    if (strpos($uri,"#>")>0){
    			$this->_parsePattern($uri);
  	}
	}

  
 /** 
  * duplicate function from DataGrid:
  * from a given pattern it fill an array of required fields (fieldList)
  *
  * @access   private
  * @param    string   $pattern column pattern
  * @return   void
  */
  function _parsePattern($pattern){
    $template = $pattern;
    $parsedcount = 0;
    while (strpos($template,"#>")>0) {
      $parsedcount++;
      $parsedfield = substr($template,strpos($template,"<#")+2,strpos($template,"#>")-strpos($template,"<#")-2);

      $this->fieldList[]=$parsedfield;
      $template = str_replace("<#".$parsedfield ."#>","",$template);
    }
  }
  
  function build(){
  	//if there is field in pattern
		if(sizeof($this->fieldList)>0){
				//I test if the DO is loaded to process the pattern on the current record
				if(isset($this->data) && $this->data->loaded )
				{
						$link = $this->url;
			      foreach ($this->fieldList as $fieldName){
			      	$fieldValue =$this->data->get($fieldName);
			        if (isset($fieldValue)){
			          $link = str_replace("<#$fieldName#>",$fieldValue,$link);
			        }
			      }
						$this->url =$link ;
				}
			
		}
		$this->value = str_replace("<##>",$this->url,$this->value);

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
    //A FAIRE comment ce fait il que ici je n'ai pas de return mais un $this->output => comprendre le mechanisme et voir si possible de l'utiliser pour notre systeme de layeur

  }
    
}
?>