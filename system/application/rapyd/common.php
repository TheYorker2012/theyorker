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
 * @version		0.9
 * @filesource
 */
 
 
 
	include_once(RAPYD_PATH."libraries/loader.php");

  if (version_compare(phpversion(), '5.0') < 0) {
    eval(' function clone($object) { return $object; }');
  }

  /**
   * Questa funzione consente di utilizzare una stringa per effettuare chiamate a funzioni php.
   * La ricorsivita' consente anche funzioni innestate.
   *
   * E' utilizzabile nei casi in cui si vuole fare e si puo' fare a meno di eval 
   * 
   * Consente una formattazione veloce dei campi di rapyd
   *
   * <code>
   *   $pattern  = 'e si... <strtolower><substr>Io Sono fesso|0|8</substr>Bravo</strtolower> <strtoupper>Davvero!</strtoupper>';
   *   echo htmlspecialchars(replaceFunctions($pattern)); 
   * </code>
   */

  function replaceFunctions($content){
    $ci =& get_instance();
		$rapyd =& $ci->rapyd;
  
    //funzioni php consentite
    $functions = $rapyd->config->item("replace_functions");
    
    //le funzioni user defined sono consentite tutte
    //$arr = get_defined_functions();
    //$functions = array_merge($functions,$arr["user"]);
  
    foreach ($functions as $function){
      $tagName = $function;
      $beginTag = "<".$tagName.">";
      $beginLen = strlen($beginTag);    
      $endTag   = "</".$tagName.">";
      $endLen   = strlen($endTag);
      $beginPos = strpos($content, $beginTag);
      $endPos   = strpos($content, $endTag);
      
      $subcontent = "";
      
      if($endPos>0){
      
        $subcontent = substr($content, $beginPos + $beginLen, $endPos - $beginPos - $beginLen);
        
        foreach ($functions as $nestedfunction){
          
          $nestedTag   = "</".$nestedfunction.">";
          if (strpos($subcontent, $nestedTag)>0){
            $subcontent = replaceFunctions($subcontent);
          }
          
        }
        
        if (strpos($subcontent,"|")===false){
          $result = $function($subcontent);
        } else {
          $arguments = split("\|",$subcontent);
          $result = call_user_func_array($function, $arguments);
        }
        
        $content = substr($content, 0, $beginPos) . $result . substr($content, $endPos + $endLen);
        
        $endPos  = strpos($content, $endTag);
        if($endPos>0){
          $content = replaceFunctions($content);
        }
      }
    
    } 

    return $content;
  }
  
  
  
    //spostare in Html
  function filetype_icon($filename){
  
    if ($filename=="") return "";
    $filename = strtolower($filename);
    
    $arrfilename = explode (".",$filename);
    $extension = array_pop($arrfilename);

    switch($extension) {


      case "bmp": 
                  $icon = "image.gif";
                  break;
      case "jpg": 
      case "jpeg":       
                  $icon = "jpg.gif";
                  break;
      case "gif": 
                  $icon = "gif.gif";
                  break;
      case "tif": 
      case "tiff": 
                  $icon = "tiff.gif";
                  break;
      case "dwg": 
                  $icon = "dwg.gif";
                  break;
      case "dwf": 
                  $icon = "dwf.gif";
                  break;
      case "dot": 
      case "doc": 
                  $icon = "doc.gif";
                  break;
      case "xls": 
                  $icon = "xls.gif";
                  break;
      case "pdf": 
                  $icon = "pdf.gif";
                  break;
      case "xml": 
                  $icon = "icons.gif";
                  break;
      case "txt": 
                  $icon = "txt.gif";
                  break;  
      case "mov": 
                  $icon = "mov.gif";
                  break;  
      case "html": 
      case "htm": 
                  $icon = "htm.gif";
                  break;
      case "exe": 
                  $icon = "exe.gif";
                  break;
      case "zip": 
      case "tar":       
      case "rar":       
      case "ark":             
                  $icon = "zip.gif";
                  break;                  
      default:
                  $icon = "txt.gif";
                  break;
    }
    
    return RAPYD_IMAGES."tree/".$icon;
    
  }
  
  
  function language_file_exist($langfile,$idiom){

    $langfile = str_replace(EXT, '', str_replace('_lang.', '', $langfile)).'_lang'.EXT;
    if (file_exists(APPPATH.'language/'.$idiom.'/'.$langfile))
    {
      return true;
    }
    else
    {		
      if (file_exists(BASEPATH.'language/'.$idiom.'/'.$langfile))
      {
        return true;
      }
      else
      {
        return false;
      }
    }
  }
  
  function thumb_name($filename, $thumb_postfix="_thumb")
	{

	  $arrfilename = explode(".",$filename);
    $extension = array_pop($arrfilename);
    $thumbname = join(".", $arrfilename).$thumb_postfix.".".$extension;
    return $thumbname;
  }
  
?>