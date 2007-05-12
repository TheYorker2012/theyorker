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

$org_name_regex = '[a-z_\-\d]+';


//****************************************************************************//
// Routing in the main controller directory                                   //
//****************************************************************************//

// 'directory' needs to map to 'yorkerdirectory'
// (the php class Directory is reserved)
// If 2 segments, seg2 ($1) should get sent to view function
// If 3 segments, seg2 ($1) should get set to the function with name seg3 ($2)
// If >3 segments, same as for 3 and any extra segments ($3) appended.
$route['directory'] = 'yorkerdirectory';
$route['directory/map'] = 'yorkerdirectory/map';
$route['directory/('.$org_name_regex.')'] = 'yorkerdirectory/view/$1';
$route['directory/('.$org_name_regex.')/([a-z]+)'] = 'yorkerdirectory/$2/$1';
$route['directory/('.$org_name_regex.')/([a-z]+)/(.+)'] = 'yorkerdirectory/$2/$1/$3';

// Invalidate yorkerdirectory as its ugly and shouldn't be used
// jh559: this is just my opinion, feel free to comment these out if you disagree
$route['yorkerdirectory'] = 'not_yorkerdirectory';
$route['yorkerdirectory/(.+)'] = 'not_yorkerdirectory';

//The contact us page is now the members entry for the yorker in the directory
//This is to route /contact to that. oj502
//$route['contact'] = 'yorkerdirectory/members/theyorker';

// /howdoi/category => /howdoi/viewcategory/category (-1 meaning all)
// /howdoi/category/12 => /howdoi/viewcategory/12
$route['howdoi/ask'] = 'howdoi/makesuggestion';
$route['howdoi/([a-z]+)'] = 'howdoi/viewcategory/$1/-1';
$route['howdoi/([a-z]+)/([0-9]+)'] = 'howdoi/viewcategory/$1/$2';

$route['campaign/preports'] = 'campaign/preports';

$route['account/links/add/([0-9]+)'] = 'account/links/add/$1';


//****************************************************************************//
// Routing to subdirectory index pages                                        //
//****************************************************************************//

$route['admin'] = 'admin/index';
$route['office'] = 'office/index';
$route['viparea'] = 'office/vipindex';


//****************************************************************************//
// VIP routing                                                                //
//****************************************************************************//

$route['viparea/'.$org_name_regex.'/directory(/.*)?'] = 'office/yorkerdirectory$1';
$route['viparea/'.$org_name_regex.'/members/list(/.*)?'] = 'office/members/memberlist$1';
$route['viparea/'.$org_name_regex] = 'office/vipindex';
$route['viparea/'.$org_name_regex.'/(.*)'] = 'office/$1';


//****************************************************************************//
// Office routing                                                             //
//****************************************************************************//


$route['office/directory'] = 'office/yorkerdirectory';
$route['office/pr'] = 'office/prindex';
$route['office/pr/org/'.$org_name_regex.'/directory(/.*)?'] = 'office/yorkerdirectory$1';
$route['office/pr/org/'.$org_name_regex.'/members/list(/.*)?'] = 'office/members/memberlist$1';
$route['office/pr/org/'.$org_name_regex] = 'office/prindex/orgindex';
$route['office/pr/org/'.$org_name_regex.'/(.*)'] = 'office/$1';
/*$route['office/pr/org/'.$org_name_regex.'/([^/]*)'] = 'office/$1';
$route['office/pr/org/'.$org_name_regex.'/([^/]*)/([^/]*)'] = 'office/$1/$2';
$route['office/pr/org/'.$org_name_regex.'/([^/]*)/([^/]*)/(.*)'] = 'office/$1/$2/$3';*/
$route['office/pr/summary/rep/([^/]*)'] = 'office/prindex/summary/rep/$1'; //pr rep
$route['office/pr/summary/org/([^/]*)'] = 'office/prindex/summary/org/$1'; //organisation
$route['office/pr/summary'] = 'office/prindex/summary/def/0'; //default
$route['office/pr/(.*)'] = 'office/prindex/$1';

$route['office/reviewlist/('.$org_name_regex.')'] = 'office/reviewlist/showleagues/$1';
$route['office/reviewlist/('.$org_name_regex.')/([a-z]+)'] = 'office/reviewlist/$2/$1';
$route['office/reviewlist/('.$org_name_regex.')/([a-z]+)/([0-9]+)'] = 'office/reviewlist/$2/$1/$3';

// send tag adding and deleting to the correct place
$route['office/reviews/addtag'] = 'office/reviews/addtag';
$route['office/reviews/deltag'] = 'office/reviews/deltag';

// /office/reviews/theyorker/ => /office/reviews/overview/theyorker/
$route['office/reviews/('.$org_name_regex.')'] = 'office/reviews/overview/$1';
// /office/reviews/theyorker/food => /office/reviews/information/theyorker/food/
$route['office/reviews/('.$org_name_regex.')/([a-z]+)'] = 'office/reviews/information/$2/$1';
// /office/reviews/theyorker/food/comments => /office/reviews/comments/theyorker/food/
$route['office/reviews/('.$org_name_regex.')/([a-z]+)/([a-z]+)'] = 'office/reviews/$3/$2/$1';
// /office/reviews/theyorker/food/reviewedit/12/11 => /office/reviews/reviewedit/theyorker/food/12/11
$route['office/reviews/('.$org_name_regex.')/([a-z]+)/([a-z]+)/([0-9]+)/([0-9]+)'] = 'office/reviews/$3/$2/$1/$4/$5';
// /office/reviews/theyorker/food/reviewedit/12 => /office/reviews/reviewedit/theyorker/food/12
$route['office/reviews/('.$org_name_regex.')/([a-z]+)/([a-z]+)/([0-9]+)'] = 'office/reviews/$3/$2/$1/$4/-1';


// /office/howdoi/editquestion/questionno/defaultrevision
$route['office/howdoi/editquestion/([0-9]+)'] = 'office/howdoi/questionedit/$1/-1';
// /office/howdoi/editquestion/questionno/revisionno
$route['office/howdoi/editquestion/([0-9]+)/([0-9]+)'] = 'office/howdoi/questionedit/$1/$2';

// /office/charity/article/cid/rid
$route['office/charity/article/([0-9]+)'] = 'office/charity/article/$1/-1';
// /office/charity/article/cid/rid
$route['office/charity/article/([0-9]+)/([0-9]+)'] = 'office/charity/article/$1/$2';

$route['office/gallery/return'] = 'office/gallery';
$route['office/gallery/([0-9]+)'] = 'office/gallery';


//****************************************************************************//
// Admin routing                                                              //
//****************************************************************************//

// 'admin/directory' needs to map to 'admin/yorkerdirectory'
// (the php class Directory is reserved)
/**
 * @NOTE Due to a bug in code ignitor, routing doesn't send the correct
 *	parameters to the controller function. These are got around by having an
 *	extra slash before the parameters to shift them forward a segment - jh559
 */
// If 2 segments, seg2 ($1) should get sent to view function
// If 3 segments, seg2 ($1) should get set to the function with name seg3 ($2)
$route['admin/directory'] = 'admin/yorkerdirectory';
$route['admin/directory/('.$org_name_regex.')'] = 'admin/yorkerdirectory/view//$1';
$route['admin/directory/('.$org_name_regex.')/([a-z]+)'] = 'admin/yorkerdirectory/$2//$1';

$route['admin/images/([0-9]+)'] = 'admin/images';

unset($org_name_regex);

?>
