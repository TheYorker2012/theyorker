<?php


//common
define('TAG_CSS',                '<style>%s</style>');
define('TAG_CSS_LINK',           '<link rel="stylesheet" href="%s" type="text/css" />');
define('TAG_CHARSET',            '<meta http-equiv="Content-Type" content="text/html; charset=%s" />');
define('TAG_JAVASCRIPT',         '<script language="javascript" type="text/javascript">%s</script>');
define('TAG_JAVASCRIPT_LINK',    '<script language="javascript" type="text/javascript" src="%s"></script>');
define('TAG_BUTTON',             '<input type="%s" name="%s" value="%s" onclick="%s" class="%s" />');



class HTML{
  
  function cssTag($path){
    return sprintf(TAG_CSS, $path)."\n";
  }
  
  function cssLinkTag($path){
    return sprintf(TAG_CSS_LINK, $path)."\n";
  }

  function charsetTag($charset){
    return sprintf(TAG_CHARSET, $charset)."\n";
  }

  function javascriptTag($script){
    return sprintf(TAG_JAVASCRIPT, $script)."\n";
  }

  function javascriptLinkTag($path){
    return sprintf(TAG_JAVASCRIPT_LINK, $path)."\n";
  }
  
    
  function button($name, $value, $onclick="", $type="button", $class="button"){
    return sprintf(TAG_BUTTON, $type, $name, $value, $onclick, $class)."\n";
  }
  
  
}

?>