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
use Illuminate\Http\Request;


// Authentication Routes...
Route::get('auth/login', 'Auth\LoginController@getLogin');
Route::post('auth/login', 'Auth\LoginController@postLogin');
Route::get('/logout', 'Auth\LoginController@logout');

// Registration Routes...
Route::get('auth/register', 'Auth\RegisterController@getRegister');
Route::post('auth/register', 'Auth\RegisterController@postRegister');

//book routes
Route::delete('/books/removebook/{book}', 'BookController@remove');
Route::post('/books/edit/{book}', 'BookController@updateBook');
Route::get('/books', 'BookController@index');
Route::get('/books/load_data', 'BookController@load_data');
Route::post('/books/new', 'BookController@save');
Route::post('/books/updatelist', 'BookController@updateList');
Route::get('/books/details', 'BookController@load_detail');


Auth::routes();

Route::get('/home', 'Auth\RegisterController@getRegister');
Route::get('/', 'Auth\RegisterController@getRegister');