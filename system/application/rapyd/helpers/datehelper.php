<?php

  //normally the input date is:
  //"eu"  DD-MM-YYYY [hh:mm:ss] european date (with "-" or "/" separator)
  //"us"  MM-DD-YYYY [hh:mm:ss] north-america date (with "-" or "/" separator)
  
  function timestampFromInputDate($datetime, $format="us") { 
    $date = "";
    $His  = "";
    $dd = "";
    $mm = "";
    $yyyy = "";
    
    if (($datetime=="") || !ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datetime) ){
      return false;

    } else {
      $datetime = ereg_replace("/", "-", $datetime); 
      $arr_date = explode(" ",$datetime);
      $date = $arr_date[0];
      if (isset($arr_date[1])){
        $His = $arr_date[1];
      }
      
      //european
      if ($format=="eu"){
        list($dd, $mm, $yyyy) = explode("-","$date"); 
        
      //american
      } else {
        list($mm, $dd, $yyyy) = explode("-","$date"); 
      }
        
      if ($dd != ""){    
        if($His=="" || $His==null) { 
          $datetime="$yyyy-$mm-$dd";
        } else {
          $datetime="$yyyy-$mm-$dd $His";
        } 
      }
      return strtotime($datetime);
      

    }
  }
  
  function inputDateFromTimestamp($timestamp, $format="us"){
  
  
    //european
    if ($format=="eu"){
    
      $stamp = "d/m/Y";

    //american
    } else {
      $stamp = "m/d/Y";
    }
  
    if (!$timestamp){
       return "";
    } else {
      if (date("H:i:s",$timestamp) == "00:00:00"){
        return date($stamp,$timestamp);
      } else {
        return date($stamp." H:i:s",$timestamp);    
      }
    }
  }
  
  
  //normally the db-date is an ISO-DATE: YYYY-MM-DD [hh:mm:ss]
  
  function timestampFromDBDate($date) { 
    if ((strpos($date,"0000-00-00")!==false) || ($date=="")){
      return false;
    } else {
      return strtotime($date);
    }
  }  

  function dbDateFromTimestamp($timestamp){
    if (!$timestamp){
      return "";
    } else {
      return date("Y-m-d H:i:s",$timestamp);
    }
  }
  
  
  #######  final functions
  
  //get a human date "from" the DB field (assume that is ISO: YYYY-MM-DD [hh:mm:ss])
  function dbdate_to_human($date,$format="us") {
    //adodb have SQLDate() 
    return inputDateFromTimestamp(timestampFromDBDate($date),$format);
  }

  //prepare a human date "to" the DB field (assume that is ISO: YYYY-MM-DD [hh:mm:ss])
  function human_to_dbdate($date,$format="us") {
    //adodb have DBDate() 
    return dbDateFromTimestamp(timestampFromInputDate($date,$format));
  }
  
  //utility.. to be used in jscalendar..
  function datestamp_from_format($format="us"){
    //european
    if ($format=="eu"){
      $stamp = '%d/%m/%Y';
      
    //american
    } else {
      $stamp = '%m/%d/%Y';
    }
    return $stamp;
  }
?>