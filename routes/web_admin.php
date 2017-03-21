<?php
Route::get('/admin', 'BusinessOwnerController@index');
Route::get('/admin/login', 'BusinessOwnerController@showLoginForm');
Route::get('/admin/register', 'BusinessOwnerController@showRegisterForm');

Route::post('/admin', 'BusinessOwnerController@create');
Route::post('/admin/login', 'BusinessOwnerController@login');