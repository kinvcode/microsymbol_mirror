<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MirrorController;

Route::get('/', 'MirrorController@show');
Route::get('/{path}', 'MirrorController@show')->where('path', '.*');
