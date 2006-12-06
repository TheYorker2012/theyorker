<?php
/*
|--------------------------------------------------------------------------
| view theme folder
|--------------------------------------------------------------------------
|
| (folder name)  /application/rapyd/views/[theme]/component.php
| You may develop your themes, copy and paste the default theme then change at least the CSS, and then set here the theme
| You chan change themplate at runtime for example in a controller you can do:
| $this->rapyd->config->set_item("theme","mytheme");   
*/

$rpd['theme'] = 'default';


/*
|--------------------------------------------------------------------------
| images "base url"
|--------------------------------------------------------------------------
|
| rapyd need some icons/images for his components, so it need an accessible image folder
| by default this folder is /application/rapyd/images
|
| normally you need to change this configuration if CI is outside the website root
| in this case you need to move rapyd image folder in a accessible path, and set an absolute path like:
| $config['images_url'] = '/images/';
|
| and/or if you use revrite roules, you may need to add in this folder an .htaccess file like this:
|  <IfModule mod_rewrite.c>
|    RewriteEngine off
|  </IfModule>
|
*/

$rpd['images_url'] = RAPYD_DIR.'images/';



/*
|--------------------------------------------------------------------------
| language (buttons & messages)
|--------------------------------------------------------------------------
| rapyd need some messages for his components, so to keep multilanguage support, you need to set a language string to load constants file/files.
|
| supported languages are: en (english), it (italian)
*/

$rpd['language'] = 'english';


/*
|--------------------------------------------------------------------------
| languages for internationalization (see rapyd_lang class)
|--------------------------------------------------------------------------
| if your application is multilanguage, you can specify accepted languages.
|
| note: CI language files & rapyd language files "are needed" for all language you decide to use
| by default CI include only english language; by default rapyd include english,italian,french.
*/

$rpd['languages'] = array('english','italian');

$rpd['ip-to-country'] = false;
$rpd['country-to-language'] = array('ITA'=>'italian');


/*
|--------------------------------------------------------------------------
| replace_functions 
|--------------------------------------------------------------------------
|
| is an array of php functions that can be used when a rapyd component parse the his content
|
| for example in a datagrid you can use the susbstr function to get the fists 100 chars of body field:
| $datagrid = new DataGrid();
| $datagrid->base_url = site_url('controller/function');
| $datagrid->per_page = 2;
| $datagrid->column("Title","title");
| //here I use a replace function (substr).  Note  the parameters, they are joined by | (pipe)
| $datagrid->column("Body", "<substr><#body#>|0|100</substr>..");
| $datagrid->build();
|
*/
$rpd['replace_functions'] = array("htmlspecialchars","htmlentities","strtolower","strtoupper","substr","nl2br","dbdate_to_human", "number_format");

//... to be continued
  
?>