<?php


class Crudsamples extends Controller {

  var $data_type = null;   
  var $data = null;

	function Crudsamples()
	{
		parent::Controller(); 

    //required helpers for samples
    $this->load->helper('url');
    $this->load->helper('text');

		//rapyd library
		$this->load->library("rapyd");
    
    //I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
    define ("THISFILE",   APPPATH."controllers/rapyd/". $this->uri->segment(2).EXT);
	}



  ##### index #####
  function index()
  {
    redirect("rapyd/crudsamples/filteredgrid");
  }



  ##### DataFilter + DataGrid #####
  function filteredgrid()
  {
    //filteredgrid//
  
    $this->rapyd->load("datafilter","datagrid");
    
    //filter
    $filter = new DataFilter("Article Filter");
    $filter->db->select("articles.*, authors.*");
    $filter->db->from("articles");
    $filter->db->join("authors","authors.author_id=articles.author_id","LEFT");

    $filter->title = new inputField("Title", "title");
    $filter->ispublic = new dropdownField("Public", "public");
    $filter->ispublic->option("","");
    $filter->ispublic->options(array("y"=>"Yes","n"=>"No"));
    
    $filter->buttons("reset","search");    
    $filter->build();
    

    //grid
    $link = site_url('rapyd/crudsamples/dataedit/show/<#article_id#>');
    
    $grid = new DataGrid("Article List");
    $grid->per_page = 5;
    $grid->use_function("substr");
    $grid->column_detail("ID","article_id", $link);
    $grid->column_orderby("title","title","title");
    $grid->column("body","<substr><#body#>|0|4</substr>....");
    $grid->column("Author","<#firstname#> <#lastname#>");
    $grid->add("rapyd/crudsamples/dataedit/create");
    $grid->build();
    
    $data["crud"] = $filter->output . $grid->output;
    
    //endfilteredgrid//
    
    $content["content"] = $this->load->view('rapyd/crud', $data, true);    
    $content["rapyd_head"] = $this->rapyd->get_head();
    $content["code"] = highlight_code_file(THISFILE, "//filteredgrid//", "//endfilteredgrid//");
    $this->load->view('rapyd/template', $content);
  }
  
  

  ##### dataedit #####
  function dataedit()
  {  
    if (($this->uri->segment(5)==="1") && ($this->uri->segment(4)==="do_delete")){
      show_error("Please do not delete the first record, it's required by DataObject sample");
    }
  
    //dataedit//
    $this->rapyd->load("dataedit");

    $edit = new DataEdit("Article Detail", "articles");
    $edit->back_url = site_url("rapyd/crudsamples/filteredgrid");

    $edit->title = new inputField("Title", "title");
    $edit->title->rule = "trim|required|max_length[20]";
    
    $edit->body = new editorField("Body", "body");
    $edit->body->rule = "required";
    $edit->body->rows = 10;    

    $edit->author = new dropdownField("Author", "author_id");
    $edit->author->option("","");
    $edit->author->options("SELECT author_id, firstname FROM authors");

    $edit->checkbox = new checkboxField("Public", "public", "y","n");
    
    $edit->datefield = new dateField("Date", "datefield","eu"); 
    
    if ($this->uri->segment(4)==="1"){
      $edit->buttons("modify", "save", "undo", "back");
    } else {
      $edit->buttons("modify", "save", "undo", "delete", "back");
    }
    
    $edit->build();
    $data["edit"] = $edit->output;
     
    //enddataedit//

    $content["content"] = $this->load->view('rapyd/dataedit', $data, true);    
    $content["rapyd_head"] = $this->rapyd->get_head();
    $content["code"] = highlight_code_file(THISFILE, "//dataedit//", "//enddataedit//");
    $this->load->view('rapyd/template', $content);
  }
  

}
?>