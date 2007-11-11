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
 * @version		0.7
 * @filesource
 */


/**
 * DataSettings provide a convenient way to store and retrieve system configuration settings in the database.
 * It assume to operate over a DB-Table (that store application settings: setting-name / setting-value one per row)
 * and provides a model with 3 basic methods "get" and "set" for values and a "save" that store or update values (rows).
 * $field_key_name must be unique whereas values have no such restriction.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 * @version    0.7.3
 */
class DataSettings
{
  var $fkey;
  var $fvalue;

  var $values = array();
  var $new_values = array();

  function DataSettings($tablename,$field_key_name,$field_value_name)
  {
    $this->fkey = $field_key_name;
    $this->fvalue = $field_value_name;   
   
    $sql = "SELECT * FROM $tablename";

    $query = this->db->query($sql);
   
    if ($query->num_rows()>0)
    {
      $rows = $query->resul_array();
      foreach ($rows as $row)
      {
        $this->values[$row[$this->fkey]] = $row[$this->fvalue];
        $this->new_values = clone($this->values); //to be cloned for php5 compatibility
      }
    }
  }
 
 
  function set($key,$value)
  {
    $this->new_values[$key][$this->field_value_name] = $value;
  }
 
 
  function get($key)
  {
    return $this->new_values[$key][$this->field_value_name];
  } 
 

  function save()
  {
 
    //foreach 
 
   //se esiste, nei vecchi 
 
    
  }

}

?>