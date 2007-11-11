<?php

//require highlight_code function (from CI text helper)

function highlight_code_file($path, $begin_str=null, $end_str=null) {
  
  $output = "";
  $content = file_get_contents($path);
   
  if (isset($begin_str) && isset($end_str)){
  
    $begin_pos = strpos($content, $begin_str);
    $begin_len = strlen($begin_str);
    $end_pos   = strpos($content, $end_str);
    $subcontent = substr($content, $begin_pos + $begin_len, $end_pos - $begin_pos - $begin_len);  
    $output =  highlight_code($subcontent);
    
  } else {
    $output = highlight_code($content);
  }
  return $output;

}



?>