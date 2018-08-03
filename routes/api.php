<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('keywords', 'KeywordController@index');
Route::get('keywords/{keyword}', 'KeywordController@show');
Route::post('keywords', 'KeywordController@store');
Route::put('keywords/{keyword}', 'KeywordController@update');
Route::delete('keywords/{keyword}', 'KeywordController@delete');

Route::get('tasks', 'TaskController@index');
Route::post('tasks', 'TaskController@store');
Route::delete('tasks/{task}', 'TaskController@delete');

Route::get('results', 'ResultController@index');
Route::put('results/{result}', 'ResultController@update');
Route::delete('results/{result}', 'ResultController@delete');
Route::get('results/s', 'ResultController@search');
