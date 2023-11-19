<?php

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AdminPanel\UserController;
use App\Http\Controllers\AdminPanel\VendorController;
use App\Http\Controllers\AdminPanel\OrderController;
use App\Http\Controllers\AdminPanel\StoreAddressController;
use App\Http\Controllers\AdminPanel\ShippingAddressController;
use App\Http\Controllers\AdminPanel\DeliveryAddressController;
use App\Http\Controllers\AdminPanel\ProductController;
use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\AdminPanel\AttributeController;
use App\Http\Controllers\AdminPanel\BrandController;
use App\Http\Controllers\AdminPanel\ColorController;
use App\Http\Controllers\AdminPanel\SizeController;
use App\Http\Controllers\AdminPanel\CountryController;
use App\Http\Controllers\AdminPanel\MaterialController;
use App\Http\Controllers\AdminPanel\PurchaseController;
use App\Http\Controllers\AdminPanel\CategoryController;
use App\Http\Controllers\AdminPanel\RegionController;
use App\Http\Controllers\AdminPanel\StateController;
use App\Http\Controllers\AdminPanel\CityController;

\App::setLocale('ar');
view()->share('lang', \App::getLocale());

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
    Route::resource('vendors', VendorController::class)->except(['create' , 'edit','destroy']);
    Route::get('archive/store/{id}', [VendorController::class , 'archive']);
    Route::post('branch/{branch?}', [VendorController::class, 'branch']);
    Route::patch('branch/{branch?}', [VendorController::class, 'branch']);

//    orders
    Route::get('orders/{store_id}', [OrderController::class , 'index']);
    Route::get('orders_delete/{id}', [OrderController::class , 'destroy']);
    Route::get('cancel_order/{id}', [OrderController::class , 'cancel']);
    Route::get('order/{order_id}', [OrderController::class , 'show']);
    Route::post('delete_product_order/{order_product_id}', [OrderController::class , 'delete_product_order']);
    Route::post('delete_store_order/{order_product_id}', [OrderController::class , 'delete_store_order']);

//    purchases
    Route::get('purchases/{store_id}', [PurchaseController::class , 'index']);
    Route::get('purchase/{order_id}/{store_id}', [PurchaseController::class , 'show']);
    Route::post('purchase_status/{order_id}/{store_id}', [PurchaseController::class , 'update']);
    //    shipping addresses
    Route::get('shipping_addresses/{store_id}', [ShippingAddressController::class , 'index']);
    Route::post('new_shipping_address', [ShippingAddressController::class , 'form']);
    Route::patch('new_shipping_address/{address_id}', [ShippingAddressController::class , 'form']);
    Route::get('shipping_address_info/{address_id}', [ShippingAddressController::class , 'shipping_address_info']);
    Route::delete('delete_address/{address_id}', [ShippingAddressController::class , 'delete_address']);


    //  delivery addresses
    Route::get('delivery_addresses/{store_id}', [DeliveryAddressController::class , 'index']);
    Route::get('primary_address/{address_id}', [DeliveryAddressController::class , 'primary_address']);
    Route::post('new_delivery_address', [DeliveryAddressController::class , 'form']);
    Route::patch('edit_delivery_address/{address_id}', [DeliveryAddressController::class , 'form']);
    Route::get('delivery_address_info/{address_id}', [DeliveryAddressController::class , 'delivery_address_info']);
    Route::get('delete_delivery_address/{address_id}', [DeliveryAddressController::class , 'delete_address']);

//    products
    Route::get('products/{store_id}', [ProductController::class , 'index']);
    Route::get('products/{store_id}/create', [ProductController::class , 'create']);
    Route::get('products/{store_id}/{product_id}/show', [ProductController::class , 'show']);
    Route::get('product_attr/{product_id}/{store_id}', [ProductController::class , 'product_attr']);
    Route::post('product_attr_save', [ProductController::class , 'product_attr_save']);
    Route::post('products/{store_id}/create', [ProductController::class , 'store']);
    Route::patch('products/{product_id}/edit', [ProductController::class , 'update']);
    Route::get('products/{product_id}/{store_id}/edit', [ProductController::class , 'edit']);
    Route::delete('products/{product_id}', [ProductController::class , 'destroy']);
//    product attribute
    Route::get('attributes/{product_store_id}', [AttributeController::class , 'index']);
    Route::post('attributes/{product_store_id}/create', [AttributeController::class , 'store']);
    Route::get('attributes/{attribute_id}/show', [AttributeController::class , 'attribute_info']);
    Route::patch('attributess/{attribute_id}/edit', [AttributeController::class , 'update']);
    Route::delete('attributes/{attribute_id}', [AttributeController::class , 'destroy']);
});
Route::group(array('prefix' => 'admin_panel/settings'), function () {
    //    brands
    Route::resource('brands', BrandController::class)->except(['create']);
    Route::get('brands/archive/{id}', [BrandController::class , 'archive']);
    //    colors
    Route::resource('colors', ColorController::class)->except(['create']);
    Route::get('colors/archive/{id}', [ColorController::class , 'archive']);
    //    sizes
    Route::resource('sizes', SizeController::class)->except(['create']);
    Route::get('sizes/archive/{id}', [SizeController::class , 'archive']);
    //    materials
    Route::resource('materials', MaterialController::class)->except(['create']);
    Route::get('materials/archive/{id}', [MaterialController::class , 'archive']);

    //    countreis
    Route::resource('countries', CountryController::class)->except(['create']);
    Route::get('countries/archive/{id}', [CountryController::class , 'archive']);
    //    regions
    Route::resource('regions', RegionController::class)->except(['create']);
    Route::get('regions/archive/{id}', [RegionController::class , 'archive']);

    //    states
    Route::resource('cities', StateController::class)->except(['create']);
    Route::get('cities/archive/{id}', [StateController::class , 'archive']);

    //    states
    Route::resource('districts', CityController::class)->except(['create']);
    Route::get('districts/archive/{id}', [CityController::class , 'archive']);

    //    categories
    Route::resource('categories', CategoryController::class)->except(['create']);
    Route::get('categories/archive/{id}', [CategoryController::class , 'archive']);
    //    subcategories
    Route::resource('subcategories', CategoryController::class)->except(['create']);
    Route::get('subcategories/archive/{id}', [CategoryController::class , 'archive']);
    //    subsubcategories
    Route::resource('subsubcategories', CategoryController::class)->except(['create']);
    Route::get('subsubcategories/archive/{id}', [CategoryController::class , 'archive']);

    Route::get('categories_trees', [CategoryController::class , 'tree']);

});
Route::get('ajax_subcatgeories', [CategoryController::class , 'ajax_subcatgeories']);
Route::get('city_ajax', [AdminController::class , 'city_ajax']);
Route::get('state_ajax', [AdminController::class , 'state_ajax']);
Route::get('ajax_regions', [AdminController::class , 'region_ajax']);
