<?php

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AdminPanel\UserController;
use App\Http\Controllers\AdminPanel\VendorController;
use App\Http\Controllers\AdminPanel\OrderController;
use App\Http\Controllers\AdminPanel\StoreAddressController;

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

//    orders
    Route::get('orders/{store_id}', [OrderController::class , 'index']);
    Route::get('orders_delete/{id}', [OrderController::class , 'destroy']);
    Route::get('cancel_order/{id}', [OrderController::class , 'cancel']);
    Route::get('order/{order_id}', [OrderController::class , 'show']);
    Route::post('delete_product_order/{order_product_id}', [OrderController::class , 'delete_product_order']);
    Route::post('delete_store_order/{order_product_id}', [OrderController::class , 'delete_store_order']);
    //    orders
    Route::get('addresses/{store_id}', [StoreAddressController::class , 'index']);
});
