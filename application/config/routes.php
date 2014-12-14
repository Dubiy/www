<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "main";
$route['404_override'] = '';

$route['parents'] = "main/index/all/parents";
$route['(:any)/parents'] = "main/index/$1/parents";

$route['children'] = "main/index/all/children";
$route['(:any)/children'] = "main/index/$1/children";

//???
$route['all'] = "main/index/all/all";
$route['(:any)/all'] = "main/index/$1/all";

$route['top'] = "main/index/all/top";
$route['(:any)/top'] = "main/index/$1/top";

$route['best'] = "main/index/all/best";
$route['(:any)/best'] = "main/index/$1/best";

$route['age'] = "main/index/all/age";
$route['(:any)/age'] = "main/index/$1/age";

$route['age/(:num)'] = "main/index/all/age/$1";
$route['(:any)/age/(:num)'] = "main/index/$1/age/$2";

$route['age/(:num)/(:num)'] = "main/index/all/age/$1/$2";
$route['(:any)/age/(:num)/(:num)'] = "main/index/$1/age/$2/$3";

$route['show/(:num)'] = "main/question/$1";

$route['add_question'] = "main/add_question";



$route['page/(:any)'] = "page/index/$1";
$route['main/403'] = "main/page_403";


/* End of file routes.php */
/* Location: ./application/config/routes.php */