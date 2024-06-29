<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MirrorController;

Route::post('/', 'MirrorController@MultipleTasks');
Route::get('/', 'MirrorController@show');
Route::get('/{path}', 'MirrorController@show')->where('path', '.*');
