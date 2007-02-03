<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	www.your-site.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://www.codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you se t a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.
|
*/

$route['default_controller'] = "home";
$route['scaffolding_trigger'] = "";

$org_name_regex = '[a-z_\d]+';
// 'directory' needs to map to 'yorkerdirectory'
// (the php class Directory is reserved)
$route['directory'] = 'yorkerdirectory';
// If 2 segments, seg2 ($1) should get sent to view function
$route['directory/('.$org_name_regex.')'] = 'yorkerdirectory/view/$1';
// If 3 segments, seg2 ($1) should get set to the function with name seg3 ($2)
$route['directory/('.$org_name_regex.')/([a-z]+)'] = 'yorkerdirectory/$2/$1';
// If >3 segments, same as for 3 and any extra segments ($3) appended.
$route['directory/('.$org_name_regex.')/([a-z]+)/(.+)'] = 'yorkerdirectory/$2/$1/$3';

// The default admin page is index
$route['admin'] = 'admin/index';
$route['viparea'] = 'viparea/index';
$route['office'] = 'office/index';

// 'admin/directory' needs to map to 'admin/yorkerdirectory'
// (the php class Directory is reserved)
/**
 * @NOTE Due to a bug in code ignitor, routing doesn't send the correct
 *	parameters to the controller function. These are got around by having an
 *	extra slash before the parameters to shift them forward a segment - jh559
 */
$route['admin/directory'] = 'admin/yorkerdirectory';
// If 2 segments, seg2 ($1) should get sent to view function
$route['admin/directory/('.$org_name_regex.')'] = 'admin/yorkerdirectory/view//$1';
// If 3 segments, seg2 ($1) should get set to the function with name seg3 ($2)
$route['admin/directory/('.$org_name_regex.')/([a-z]+)'] = 'admin/yorkerdirectory/$2//$1';

// /office/reviews/theyorker/ => /office/reviews/overview/theyorker/
$route['office/reviews/('.$org_name_regex.')'] = 'office/reviews/information//$1';
// /office/reviews/theyorker/food => /office/reviews/information/theyorker/food/
$route['office/reviews/('.$org_name_regex.')/([a-z]+)'] = 'office/reviews/information//$2/$1';
// /office/reviews/theyorker/food/comments => /office/reviews/comments/theyorker/food/
$route['office/reviews/('.$org_name_regex.')/([a-z]+)/([a-z]+)'] = 'office/reviews/$3//$2/$1';
// /office/reviews/theyorker/food/reviewedit/12 => /office/reviews/reviewedit/theyorker/food/12
$route['office/reviews/('.$org_name_regex.')/([a-z]+)/([a-z]+)/([0-9]+)'] = 'office/reviews/$3//$2/$1/$4';

$route['viparea/directory'] = 'viparea/yorkerdirectory';
// If 2 segments, seg2 ($1) should get sent to view function
$route['viparea/directory/('.$org_name_regex.')'] = 'viparea/yorkerdirectory/information//$1';
// If 3 segments, seg2 ($1) should get set to the function with name seg3 ($2)
$route['viparea/directory/('.$org_name_regex.')/([a-z]+)'] = 'viparea/yorkerdirectory/$2//$1';
unset($org_name_regex);

// Invalidate yorkerdirectory as its ugly and shouldn't be used
// jh559: this is just my opinion, feel free to comment these out if you disagree
$route['yorkerdirectory'] = 'not_yorkerdirectory';
$route['yorkerdirectory/(.+)'] = 'not_yorkerdirectory';

//The contact us page is now the members entry for the yorker in the directory
//This is to route /contact to that. oj502
$route['contact'] = 'yorkerdirectory/members/theyorker';

?>