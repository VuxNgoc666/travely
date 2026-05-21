<?php

$app->get('', 'HomeController@index');
$app->get('contact', 'ContactController@index');
$app->post('contact', 'ContactController@send');
$app->get('tours', 'TourController@index');
$app->get('tours/domestic', 'TourController@domestic');
$app->get('tours/foreign', 'TourController@foreign');
$app->get('deals', 'TourController@deals');
$app->get('tour/{slug}', 'TourController@show');

$app->get('login', 'AuthController@login');
$app->post('login', 'AuthController@authenticate');
$app->get('register', 'AuthController@register');
$app->post('register', 'AuthController@store');
$app->get('logout', 'AuthController@logout');

$app->get('account', 'UserController@dashboard');
$app->post('booking/store', 'BookingController@store');
$app->post('favorite/toggle', 'UserController@toggleFavorite');

$app->get('admin', 'AdminController@dashboard');
$app->get('admin/tours', 'AdminController@tours');
$app->get('admin/tours/create', 'AdminController@tourForm');
$app->get('admin/tours/edit/{id}', 'AdminController@tourForm');
$app->post('admin/tours/save', 'AdminController@saveTour');
$app->post('admin/tours/delete/{id}', 'AdminController@deleteTour');
$app->get('admin/bookings', 'AdminController@bookings');
$app->post('admin/bookings/status/{id}', 'AdminController@updateBookingStatus');
$app->get('admin/contacts', 'AdminController@contacts');
$app->post('admin/contacts/status/{id}', 'AdminController@updateContactStatus');
$app->get('admin/users', 'AdminController@users');
$app->post('admin/users/role/{id}', 'AdminController@updateUserRole');
$app->post('admin/users/delete/{id}', 'AdminController@deleteUser');
