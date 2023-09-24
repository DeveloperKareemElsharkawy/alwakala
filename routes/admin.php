<?php

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AdminPanel\UserController;
use App\Http\Controllers\AdminPanel\VendorController;

Route::get('/admin_panel/home', function () {
    return view('admin.home');
})->name('adminHome');
Route::get('/admin_panel', function () {
    return view('admin.home');
});

Route::group(array('prefix' => 'admin_panel'), function () {
//    users
    Route::resource('users', UserController::class)->except(['show', 'create' , 'edit']);
    Route::get('user/{id}', [UserController::class, 'user_info']);
    Route::get('user_show/{id}', [UserController::class, 'user_show']);
    Route::get('archive/user/{id}', [UserController::class, 'archive']);
    Route::get('restore/user/{id}', [UserController::class, 'restore']);

//    Vendors
    Route::resource('vendors', VendorController::class)->except(['create' , 'edit']);
    Route::post('branch/{branch?}', [VendorController::class, 'branch']);
    Route::patch('branch/{branch?}', [VendorController::class, 'branch']);
});
