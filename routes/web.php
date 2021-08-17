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


Route::get('/', function () {
    return view('welcome');
});

Route::get('index','HomeController@index');
Route::post('upload','HomeController@upload');
Route::get('list','HomeController@list');
Route::get('show','HomeController@show');
Route::get('copy','HomeController@copy');
Route::get('move','HomeController@move');
Route::get('download','HomeController@download');
Route::get('delete','HomeController@delete');