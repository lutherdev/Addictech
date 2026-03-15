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

$routes->get('admin/users', 'Users::index');
$routes->get('admin/products', 'Products::index');
$routes->get('admin/orders', 'Orders::adminIndex');

$routes->get('dashboard', 'Dashboard::index'); //leads to all kinds of dashboard

// ======================== AUTH ==================================

$routes->get('login', 'Auth::loginview');
$routes->post('auth/login', 'Auth::login');

$routes->get('register', 'Auth::regview');
$routes->post('auth/register', 'Auth::register');

$routes->get('auth/logout', 'Auth::logout');
$routes->get('logout', 'Auth::logout');

// ========================USERS==================================

$routes->get('user/profile', 'Users::profile');

$routes->get('admin/users/view/(:num)', 'Users::view/$1');//view

$routes->get('admin/users/edit/(:num)', 'Users::edit/$1');//admin view
$routes->post('admin/users/update/(:num)', 'Users::update/$1'); //process

$routes->get('admin/user/edit/(:num)', 'Users::edit/$1');//user view
$routes->post('users/update/(:num)', 'Users::update/$1'); //process

$routes->get('admin/users/delete/(:num)', 'Users::delete/$1');

// $routes->get('user/deactivate', 'Users::deactview');
// $routes->post('/deactivate', 'Users::deact');

// $routes->get('users/status', 'Users::statuschangeview');
// $routes->post('users/statuschange', 'Users::statuschange');

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

// ========================PASSWORD==================================

$routes->get('password/forget', 'Password::forgetview'); // view
$routes->post('forget', 'Password::forget'); //not view

$routes->get('password/reset/(:any)', 'Password::resetview/$1'); // view
$routes->post('reset/(:any)', 'Password::reset/$1'); //not view

$routes->get('password/change', 'Password::changeview'); //view
$routes->post('passwordchange', 'Password::change'); //not view

// ======================== ORDERS (ADMIN) ==========================
// $routes->get('admin/orders', 'Order::adminIndex');
// $routes->get('admin/orders/view/(:num)', 'Order::adminView/$1');
// $routes->get('admin/orders/update/(:num)', 'Order::adminUpdateView/$1');
// $routes->post('admin/orders/update/(:num)', 'Order::adminUpdate/$1');
// $routes->get('admin/orders/delete/(:num)', 'Order::adminDelete/$1');

// ====================== CART=======================================

$routes->get('cart',                    'Cart::viewCart');
$routes->post('cart/add',   'Cart::add');

$routes->get('cart/remove/(:num)',      'Cart::remove/$1');
$routes->post('cart/update/(:num)',     'Cart::update/$1');


// ========================= ORDER ==================================
$routes->get('checkout',                    'Orders::checkout');
$routes->get('orders/confirmation/(:num)',  'Orders::confirmation/$1');
$routes->get('orders',                      'Orders::index');
$routes->get('orders/view/(:num)',          'Orders::view/$1');
$routes->post('orders/cancel/(:num)',       'Orders::cancel/$1');

$routes->get('orders/buynow/(:num)/(:num)', 'Orders::buyNow/$1/$2');

$routes->post('orders/buynow',   'Orders::buyNow');
$routes->post('orders/place', 'Orders::placeOrder');

// Admin
$routes->get('admin/orders',                        'Orders::adminIndex');
// $routes->get('admin/orders/view/(:num)',             'Orders::adminView/$1');
$routes->get('admin/orders/view/(:num)',             'Orders::adminIndex');
$routes->get('admin/orders/update/(:num)',          'Orders::adminEdit/$1');
$routes->post('admin/orders/update/(:num)',          'Orders::adminUpdateStatus/$1');
$routes->post('admin/orders/payment/(:num)',         'Orders::adminUpdatePayment/$1');


