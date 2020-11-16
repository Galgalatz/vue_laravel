<?php

use Illuminate\Support\Facades\Route;

Route::name('home')->get('/', 'FormController@index');
Route::post('/submit', 'FormController@submit');
Route::post('/process', 'FormController@process');

