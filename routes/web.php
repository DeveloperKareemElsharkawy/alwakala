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
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    \Auth::loginUsingId(17);
    return view('welcome');

    Storage::disk('s3')->download('/images/app-tv/166303082032.png');
})->middleware('auth');



Route::get('/logout', function () {
    \Auth::loginUsingId(21);
    return view('welcome');
})->name('login');

Route::get('/login', function () {
    \Auth::loginUsingId(21);
    return view('welcome');
})->name('logout');
