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
 

/**
 * DataSet - return a paged/subset of data from a given source (multidim array or database query).
 * It transform the result in a clean and standard array.
 * It support pagination.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni <felix@rapyd.com>
 * @access     public
 */
class DataSet {
  
  var $data        = array();
  var $recordCount = 0;
  
  //pagination default settings
  var $paged        = null;  
  var $_button_container = array( "TR"=>array(), "BL"=>array(), "BR"=>array() );
	var $_title = null;
	
  var $per_page     = null;
  var $base_url     = "";
  var $uri_segment  = null;
  var $num_links    = 5;
    
	var $first_link   		= '&lsaquo; First';
	var $next_link			= '&gt;';
	var $prev_link			= '&lt;';
	var $last_link    		= 'Last &rsaquo;';
  var $extra_anchor   = "";


	var $full_tag_open		= '';
	var $full_tag_close		= '';
	var $first_tag_open		= '';
	var $first_tag_close	= '&nbsp;';
	var $last_tag_open		= '&nbsp;';
	var $last_tag_close		= '';
	var $cur_tag_open		= '&nbsp;<b>';
	var $cur_tag_close		= '</b>';
	var $next_tag_open		= '&nbsp;';
	var $next_tag_close		= '&nbsp;';
	var $prev_tag_open		= '&nbsp;';
	var $prev_tag_close		= '';
	var $num_tag_open		= '&nbsp;';
	var $num_tag_close		= '';
	
    
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    array   $data   a multidimensional associative array of data
  * @return   void
  */
  function DataSet($data=null){
    
    $this->ci =& get_instance();
    $this->uri =& $this->ci->uri;
        
        
    //AR preset or SQL query passed, so database lib needed
    if (!isset($data) || is_string($data)){
      if (!isset($this->ci->db)) {
        $this->ci->load->database();
      }
      $this->db =& $this->ci->db;      
    }

    //tablename
    if (is_string($data))
    {
      $this->db->select("*");
      $this->db->from($data);
      $this->type = "query";            
      $this->_sniff_orderby();

    //array
    } elseif (is_array($data)){
      $this->type        = "array";
      $this->arraySet    = $data;

    //db (CI active record)
    } else {
      $this->type = "query";
      $this->_sniff_orderby();

    }
    
  }


  function _sniff_orderby(){
    $segment_array = $this->ci->uri->segment_array();
    $segment_count = $this->ci->uri->total_segments();
     
    //segments
    $do_orderby = array_search("orderby",$segment_array);
    $asc = array_search("asc",$segment_array);
    $desc = array_search("desc",$segment_array);
    

    
    ///// orderby /////
    if ($do_orderby!==false){
      $this->db->order_by($this->ci->uri->segment($do_orderby+1), $this->ci->uri->segment($do_orderby+2));
    }
    
  }


	
  function title($title){
    $this->_title = $title;
  }


 /**
  * useFunction add your custom(or php precompiled) functon/s to the list of "replace_functions" 
  * and enable the component to use a rapyd raplacing/formatting sintax  like these:
  * "..<customfunction><#body#>|0|100</customfunction>.."  (where <#body#> is a fieldname, and |0|100 are function parameters).
  *
  * @access   private
  * @return   void
  */
  function use_function(){
    $functions = func_get_args();
    foreach($functions as $function){
      if (!in_array(strtolower($function), $this->rapyd->config->item("replace_functions"))){
        array_push($this->rapyd->config->config["replace_functions"], strtolower($function));
      }
    }
  }


  function add($uri, $caption=RAPYD_BUTTON_ADD, $position="TR"){
     $action = "javascript:window.location='" . site_url($uri) . "'";
     $this->button("btn_add", $caption, $action, $position); 
  }



  function button($name, $caption, $action, $position="BL"){
     $this->_button_container[$position][] = HTML::button($name, $caption, $action, "button", "button");
  }

  function submit($name, $caption, $position="BL"){
     $this->_button_container[$position][] = HTML::button($name, $caption, "", "submit", "button");
  }


 /**
  * exec the query and fills the $data property, with a result array
  *
  * @access   public
  * @return   void
  */
  function build(){
    
    $segment_array = $this->ci->uri->segment_array();


    $do_orderby = array_search("orderby",$segment_array);
    $do_reset = array_search("reset",$segment_array);

    if ($do_reset!==false){
      while(array_pop($segment_array)!="reset");
    }

    $segment_count = count($segment_array);
    
		if (ctype_digit($segment_array[$segment_count])){
			array_pop($segment_array);
		}		
		

		$this->base_url = join("/",$segment_array); //site_url(join("/",$segment_array));
			
    if (!isset($this->uri_segment)){			
      $this->uri_segment = count($segment_array)+1;
    }
		
    if ($do_orderby!==false){
      $this->uri_segment = count($segment_array)+1;  
      while(array_pop($segment_array)!="orderby");
    }

    $order_by_base = join("/",$segment_array);


    $this->_orderby_asc_url = site_url($order_by_base."/orderby/<#field#>/asc");
    $this->_orderby_desc_url = site_url($order_by_base."/orderby/<#field#>/desc");
		
		

    $this->pageLength = $this->per_page;

    if ( isset($this->pageLength) && !isset($this->paged) ){
      $this->paged = true;
    } else {
      $this->paged = false;    
    }

    
    if (!isset($this->pageIndex) || !is_numeric($this->pageIndex)){
        $this->pageIndex = 0;
    }
    
        
    switch($this->type){
    
      case "array":
        $this->recordCount = count($this->arraySet);
        if ($this->paged){
          $this->data = array_slice ($this->arraySet, $this->ci->uri->segment($this->uri_segment), $this->pageLength);          
        } else {
          $this->data = $this->arraySet;
        }
        if (!$this->paged){
          $this->data = array_slice ($this->data, 0, $this->pageLength);
        }
        
        break;
      
             
      case "query":
                              
        if ($this->paged){
          
          //pagination limit and offset
          $this->db->limit($this->per_page, $this->ci->uri->segment($this->uri_segment));

          //compile the select
          $sql = $this->db->_compile_select();
      
          //rebuild AR query to get total rows (needed for navigator)
          $this->db->ar_select = array('COUNT(*) AS totalrows');
          $this->db->ar_limit = FALSE;   
          //postres compat. suggested by thierry
          $this->db->ar_orderby = array();
          $this->db->ar_order = FALSE; 
 
          //get total rows
          $query = $this->db->get();
          if ($query===false) show_error("DB Error");
          $row = $query->row();     
          $this->recordCount = $row->totalrows;
                
          //exec original query
          $query = $this->db->query($sql);     
          $this->recordSet = $query->result_array();

        } else {
          $query = $this->db->get();

          if (isset($query->num_rows)){
            $this->recordCount = $query->num_rows;
          } else {
            $this->recordCount = 0;
          }
          $this->recordSet = $query->result_array();          
        }
                    
        if(!$this->recordSet){
          $this->data = array();
        } else {
          $this->data = $this->recordSet;
        }

        if (!$this->paged){
          $this->data = array_slice ($this->data, 0, $this->pageLength);
        }
          
        break;
        
        
    }
    
    //navigator 
    if ($this->paged) {

      //load needed libraries 
      if (!isset($this->ci->pagination)) {
        $this->ci->load->library('pagination');
      }
      
      
      $config = array();
      $config['total_rows']  = $this->recordCount; //computed
      $config['per_page']    = $this->per_page;  
      $config['base_url']    = $this->base_url;      
      $config['uri_segment'] = $this->uri_segment;
      $config['num_links']   = $this->num_links;
      
      $config['first_link']  = $this->first_link;
      $config['next_link']   = $this->next_link;  
      $config['prev_link']   = $this->prev_link;
      $config['last_link']   = $this->last_link;
      
      $config['full_tag_open']  = $this->full_tag_open;      
      $config['full_tag_close'] = $this->full_tag_close;
      $config['first_tag_open'] = $this->first_tag_open;  
      $config['first_tag_close']= $this->first_tag_close;        
      $config['last_tag_open']  = $this->last_tag_open;
      $config['last_tag_close'] = $this->last_tag_close;
      $config['cur_tag_open']   = $this->cur_tag_open;      
      $config['cur_tag_close']  = $this->cur_tag_close;
      $config['next_tag_open']  = $this->next_tag_open;  
      $config['next_tag_close'] = $this->next_tag_close;        
      $config['prev_tag_open']  = $this->prev_tag_open;
      $config['prev_tag_close'] = $this->prev_tag_close;
      $config['num_tag_open']   = $this->num_tag_open;
      $config['num_tag_close']  = $this->num_tag_close;      
      
      $this->ci->pagination->initialize($config);

      $this->navigator = $this->ci->pagination->create_links();
			
      //this enhancement fix pagination links when CI has page extensions (ex: .html)
			$this->navigator = ereg_replace($this->base_url."+/+[[:alnum:]/]", site_url("\\0").$this->extra_anchor,$this->navigator);
			$this->navigator = str_replace('href="'.$this->base_url.'/"', 'href="'.site_url($this->base_url).$this->extra_anchor.'"' ,$this->navigator);			
	
      
    }

  }


}

?>