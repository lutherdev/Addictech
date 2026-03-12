<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// ======================== DASH BOARD ==================================

$routes->get('/', 'Home::index');
//$routes->get('home', 'Home::index');
$routes->get('home', 'Users::profile');
$routes->get('dashboard', 'Dashboard::index'); //leads to all kinds of dashboard

// ======================== AUTH ==================================

$routes->get('login', 'Auth::loginview');
$routes->post('auth/login', 'Auth::login');

$routes->get('register', 'Auth::regview');
$routes->post('auth/register', 'Auth::register');

$routes->get('auth/logout', 'Auth::logout');

$routes->get('auth/verify/(:any)', 'Auth::verify/$1');

// ========================USERS==================================

$routes->get('/users', 'Users::index'); //view leads to user dashboard

// $routes->get('/users/add', 'Users::add');
// $routes->post('/users/insert', 'Users::insert');

$routes->get('users/view/(:num)', 'Users::view/$1');//view

$routes->get('users/edit/(:num)', 'Users::edit/$1');//view
$routes->get('user/edit/(:num)', 'Users::edit/$1');//view
$routes->post('users/update/(:num)', 'Users::update/$1'); //not view

$routes->get('users/delete/(:num)', 'Users::delete/$1');

$routes->get('user/profile', 'Users::profile');

$routes->get('user/deactivate', 'Users::deactview');
$routes->post('/deactivate', 'Users::deact');

$routes->get('users/status', 'Users::statuschangeview');
$routes->post('users/statuschange', 'Users::statuschange');

// ========================EQUIPMENTS==================================

$routes->get('equipments', 'Equipments::index'); //view

$routes->get('equipments/add', 'Equipments::add'); //view
$routes->post('equipments/insert', 'Equipments::insert'); //not view

$routes->get('equipments/view/(:num)', 'Equipments::view/$1'); // view

$routes->get('equipments/edit/(:num)', 'Equipments::edit/$1');
$routes->post('equipments/update/(:num)', 'Equipments::update/$1');

$routes->get('equipments/delete/(:num)', 'Equipments::delete/$1');

$routes->get('equipments/status', 'Equipments::statuschangeview');
$routes->post('equipments/statuschange', 'Equipments::statuschange');

// ========================BORROW==================================

$routes->get('/borrow', 'Borrow::borrowview'); //view
$routes->post('/borrow/equipment', 'Borrow::borrow');
$routes->post('/borrow/borrow', 'Borrow::borrow'); //FIXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

$routes->get('/borrow/view/(:num)', 'Borrow::view/$1');

$routes->get('/borrow/edit/(:num)', 'Borrow::edit/$1'); //view
$routes->post('/borrow/update/(:num)', 'Borrow::update/$1'); //controller

$routes->get('borrow/delete/(:num)', 'Borrow::delete/$1');



// ========================RETURN==================================
$routes->get('/return', 'ReturnItem::returnview');
$routes->post('/return/equipment', 'ReturnItem::return');
$routes->post('return/return', 'ReturnItem::returnItem');//FIXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
// ========================RESERVE==================================

$routes->get('/reserve', 'Reservation::reserveview');
$routes->post('/reserve/equipment', 'Reservation::reserve');

$routes->get('reservation/view/(:num)', 'Reservation::view/$1');

$routes->get('reservation/edit/(:num)', 'Reservation::edit/$1');
$routes->post('reservation/update/(:num)', 'Reservation::update/$1');

$routes->get('reservation/delete/(:num)', 'Reservation::delete/$1');

$routes->get('reservation/confirm/(:num)', 'Reservation::release/$1');
$routes->get('reservation/done/(:num)', 'Reservation::done/$1');


// ========================PASSWORD==================================

$routes->get('password/forget', 'Password::forgetview'); // view
$routes->post('forget', 'Password::forget'); //not view

$routes->get('password/reset/(:any)', 'Password::resetview/$1'); // view
$routes->post('reset/(:any)', 'Password::reset/$1'); //not view

$routes->get('password/change', 'Password::changeview'); //view
$routes->post('passwordchange', 'Password::change'); //not view




