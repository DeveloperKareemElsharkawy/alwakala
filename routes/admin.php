<?php

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AdminPanel\UserController;
use App\Http\Controllers\AdminPanel\VendorController;
use App\Http\Controllers\AdminPanel\OrderController;
use App\Http\Controllers\AdminPanel\StoreAddressController;
use App\Http\Controllers\AdminPanel\ShippingAddressController;
use App\Http\Controllers\AdminPanel\ProductController;
use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\AdminPanel\AttributeController;

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
    //    shipping addresses
    Route::get('shipping_addresses/{store_id}', [ShippingAddressController::class , 'index']);
    Route::post('new_shipping_address', [ShippingAddressController::class , 'form']);
    Route::patch('new_shipping_address/{address_id}', [ShippingAddressController::class , 'form']);
    Route::get('shipping_address_info/{address_id}', [ShippingAddressController::class , 'shipping_address_info']);
    Route::delete('delete_address/{address_id}', [ShippingAddressController::class , 'delete_address']);

//    products
    Route::get('products/{store_id}', [ProductController::class , 'index']);
    Route::get('products/{store_id}/{product_id}/show', [ProductController::class , 'show']);
    Route::post('products/{store_id}/create', [ProductController::class , 'store']);
    Route::patch('products/{product_id}/edit', [ProductController::class , 'update']);
    Route::get('products/{product_id}/{store_id}/edit', [ProductController::class , 'edit']);
//    product attribute
    Route::get('attributes/{product_store_id}', [AttributeController::class , 'index']);
    Route::post('attributes/{product_store_id}/create', [AttributeController::class , 'store']);
    Route::get('attributes/{attribute_id}/show', [AttributeController::class , 'attribute_info']);
    Route::patch('attributes/{attribute_id}/edit', [AttributeController::class , 'update']);
    Route::delete('attributes/{attribute_id}', [AttributeController::class , 'destroy']);
});

Route::get('city_ajax', [AdminController::class , 'city_ajax']);
