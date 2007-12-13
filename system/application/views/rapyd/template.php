<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Rapyd Components - Samples</title>
<style type="text/css">
body { margin: 0; padding: 0;	font: 70% Verdana, Arial, Helvetica, sans-serif;	 background-color:#FBFBFD; color: #7e7e7e; line-height: 16px; }
a {	color: #33ADDB;	background-color: inherit; }
a:hover {	color: #575757;	background-color: inherit;}
h1 { font: bold 1.8em Verdana, Sans-Serif; letter-spacing: -1px; margin: 0;	padding: 0; }
h1 a { text-decoration: none; }
h2 { margin: 0 0 10px 0; padding: 3px 0 6px 0; font: bold 1.2em Verdana, Arial, Sans-Serif;	color: #808080; border-bottom: 1px solid #e6e6e6; background-color: inherit;}
h2 a { color: #6AC65D; background-color: inherit;	text-decoration: none; }
h3 { margin: 0; padding: 3px 0 6px 0; font: bold 1em Verdana, Arial, Sans-Serif;	color: #808080;  background-color: inherit;}
p {	padding: 2px 0 5px;	margin: 0;}
#content { margin: 0px auto; width: 780px;}
.divider { clear:both; width:550px; margin-top:5px; margin-bottom:10px; border-bottom: 1px solid #cccccc; }
.left {	float: left; padding: 5px 0 0 5px; width: 150px;}
.right { float: right; width: 600px; margin: 0 0 20px 0;  padding: 5px 5px 0 15px;  color: #000; background-color: #FFFFFF; border-left: 1px solid #e6e6e6;}
.line {	height: 2px; margin: 10px 0 10px 0;}
.footer { clear: both; color: #999999; padding: 10px 0 10px 0; border-top: 1px solid #e6e6e6; text-align: center; line-height: 13px;}
.footer a { text-decoration: underline; }
.code { margin: 0 0 20px 0; font: 11px "courier new",Tahoma,Arial,sans-serif;	background: #eeeeee;	padding: 0px;	border: 1px solid #dddddd; }
.note {	padding: 10px; background-color: #FFFFCC; }
.note hr { border: none 0; border-bottom: 1px solid #D9D900; height:1px;}
</style>

<?php echo $rapyd_head?>
</head>
<body>

	<div id="content">
  
		<div class="line"></div>
  
    <h1>Rapyd Components - Samples</h1>
		<div>version 0.9 | based on CI 1.5.0 | posted 01/11/2006</div>
  
		<div class="line"></div>


    <div class="left">

      <div><a href="http://www.rapyd.com">www.rapyd.com</a></div>
      <div class="line"></div>
			
      <div><?=anchor("rapyd/samples/index","Index")?></div>

      <div class="line"></div>

      <h3>data presentation</h3>
      <div><?=anchor("rapyd/samples/dataset","DataSet")?></div>
      <div><?=anchor("rapyd/samples/datatable","DataTable")?></div>
      <div><?=anchor("rapyd/samples/datagrid","DataGrid")?></div>
		
      <div class="line"></div>
      
      <h3>data manipulation</h3>(database and user input)<br />
      <div><?=anchor("rapyd/datam/dataobject","DataObject")?> + rel support</div>
      <div><?=anchor("rapyd/datam/prepostprocess","DataObject")?> adv. functions</div>
      <div><?=anchor("rapyd/datam/dataform","DataForm")?></div> 
      
      <div class="line"></div>
      
      <h3>data editing</h3>(build crud interfaces)<br />
      <div><?=anchor("rapyd/crudsamples/filteredgrid","Filtered Grid")?> &amp; <?=anchor("rapyd/crudsamples/dataedit/show/1","DataEdit")?></div>
      
      <div><?=anchor("rapyd/supercrud/dataedit/show/1","DataEdit")?> + many-to-many</div>

      <div class="line"></div>
      
      note: from DataGrid sample a <?=anchor("rapyd/samples/index","test database")?> is required<br />

      
      <div class="line"></div> 
    </div>
    
    
		<div class="right">

      <?php echo $content?>
      
      <div class="line"></div>
      
      <div class="code"><?php echo $code?></div>

		</div>
    
    <div class="line"></div>
    
		<div class="footer">
			<p>rendered in {elapsed_time} seconds | ver 0.9 | <a href="http://www.codeigniter.com">Code Igniter Home</a> | <a href="http://www.rapyd.com">Rapyd Home</a></p>
		</div>
    
	</div>

  
</body>
</html>