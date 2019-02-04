<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', 'PersonController@index');
Route::post('/store-person', 'PersonController@store')->name('person.store');
Route::post('/update-person/{id}', 'PersonController@update')->name('person.update');
