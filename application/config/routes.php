<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Router Utama
$route['default_controller'] = 'dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Route untuk Mahasiswa
$route['dashboard']   ='dashboard/index';
$route['leaderboard']               = 'Leaderboard/index';
$route['customerservice']               = 'Leaderboard/customerService';
$route['identify']   = 'dashboard/identify';
$route['login']   = 'login/index';

?>