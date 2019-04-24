<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['registration'] = 'CompanyApi/register';
$route['check_email'] = 'CompanyApi/checkEmail';
$route['email_verification'] = 'CompanyApi/emailVerification';

$route['login_check'] = 'CompanyApi/login';
$route['check_session'] = 'CompanyApi/check_session';
$route['logout'] = 'CompanyApi/logout';

$route['user/(:num)'] = 'User/getData/$1/2';
$route['userlist'] = 'User/list';
$route['userstore'] = 'User/store';
$route['userupdate'] = 'User/edit';
$route['userremove'] = 'User/destroy';

$route['judge/(:num)'] = 'User/getData/$1/3';
$route['judgelist'] = 'User/list';
$route['judgestore'] = 'User/store';
$route['judgeupdate'] = 'User/edit';
$route['judgeremove'] = 'User/destroy';

$route['get_companylist'] = 'User/getCompanyData';

$route['event/(:num)'] = 'Event/getData/$1/3';
$route['eventlist'] = 'Event/list';
$route['eventstore'] = 'Event/store';
$route['eventupdate'] = 'Event/edit';
$route['eventremove'] = 'Event/destroy';
$route['getJudgementCriteria'] = 'Event/getJudgementCriteria';
$route['updateCriteria'] = 'Event/updateCriteria';

$route['get_coordinatorlist'] = 'Event/getCoordinatorData';
$route['getjudgelist'] = 'Event/getAssignJudges';
$route['updatejudgelist'] = 'Event/editAssignJudges';
$route['uploadfile'] = 'Event/uploadfile';

$route['contestant/(:num)'] = 'Contestant/getData/$1/3';
$route['contestantlist'] = 'Contestant/list';
$route['contestantstore'] = 'Contestant/contestantstore';
$route['contestantupdate'] = 'Contestant/edit';
$route['contestantremove'] = 'Contestant/destroy';

$route['judgeeventlist'] = 'JudgeAction/list';
$route['checkeventaccess'] = 'JudgeAction/checkEvent';
$route['judgementlist'] = 'JudgeAction/judgementList';
$route['checkconnection'] = 'JudgeAction/checkconnection';
$route['judgementstore'] = 'JudgeAction/store';

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
