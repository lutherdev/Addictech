<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// ======================== Home ==================================

$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');
$routes->get('catalog', 'Home::viewcatalog'); //view
$routes->get('wishlist', 'Home::viewwishlist');



$routes->get('dashboard', 'Dashboard::index'); //leads to all kinds of dashboard

// ======================== AUTH ==================================

$routes->get('login', 'Auth::loginview');
$routes->post('auth/login', 'Auth::login');

$routes->get('register', 'Auth::regview');
$routes->post('auth/register', 'Auth::register');

$routes->get('auth/logout', 'Auth::logout');

// ========================USERS==================================

$routes->get('/users', 'Users::index'); //view leads to nowhere

$routes->get('user/profile', 'Users::profile');


$routes->get('users/view/(:num)', 'Users::view/$1');//view

$routes->get('users/edit/(:num)', 'Users::edit/$1');//view
$routes->get('user/edit/(:num)', 'Users::edit/$1');//view
$routes->post('users/update/(:num)', 'Users::update/$1'); //not view

$routes->get('users/delete/(:num)', 'Users::delete/$1');



$routes->get('user/deactivate', 'Users::deactview');
$routes->post('/deactivate', 'Users::deact');

$routes->get('users/status', 'Users::statuschangeview');
$routes->post('users/statuschange', 'Users::statuschange');

// ========================EQUIPMENTS==================================

$routes->get('products', 'Products::index'); //view

$routes->get('products/add', 'Products::add'); //view
$routes->post('products/insert', 'Products::insert'); //not view

$routes->get('products/view/(:num)', 'Products::view/$1'); // view

$routes->get('products/edit/(:num)', 'Products::edit/$1');
$routes->post('products/update/(:num)', 'Products::update/$1');

$routes->get('products/delete/(:num)', 'Products::delete/$1');

$routes->get('products/status', 'Products::statuschangeview');
$routes->post('products/statuschange', 'Products::statuschange');

// ========================ORDER==================================


$routes->post('/borrow/equipment', 'Borrow::borrow');
$routes->post('/borrow/borrow', 'Borrow::borrow'); //FIXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

$routes->get('/borrow/view/(:num)', 'Borrow::view/$1');

$routes->get('/borrow/edit/(:num)', 'Borrow::edit/$1'); //view
$routes->post('/borrow/update/(:num)', 'Borrow::update/$1'); //controller

$routes->get('borrow/delete/(:num)', 'Borrow::delete/$1');


// ========================PASSWORD==================================

$routes->get('password/forget', 'Password::forgetview'); // view
$routes->post('forget', 'Password::forget'); //not view

$routes->get('password/reset/(:any)', 'Password::resetview/$1'); // view
$routes->post('reset/(:any)', 'Password::reset/$1'); //not view

$routes->get('password/change', 'Password::changeview'); //view
$routes->post('passwordchange', 'Password::change'); //not view


