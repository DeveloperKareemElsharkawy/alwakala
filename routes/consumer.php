<?php

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'consumer-app', 'namespace' => 'Consumer'], function () {
    Route::post('/login', 'AuthController@login');
    Route::post('/logout', 'AuthController@logout')->middleware('consumer_auth');
    Route::post('/register', 'AuthController@register');
    Route::post('/update-device-token', 'AuthController@updateDeviceToken')->middleware('consumer_auth');
    Route::get('/test-push', 'AuthController@testPushNotification')->middleware('consumer_auth');
    Route::post('/reset-password', 'AuthController@resetPassword');
    Route::post('/validate-confirm-code', 'AuthController@validateConfirmCode');
    Route::post('/change-password', 'AuthController@changePassword');
    Route::get('sideData', 'AuthController@sideData')->middleware('consumer_auth');
    Route::group(['middleware' => ['consumer_auth']], function () {
        Route::get('search/stores', 'SearchController@searchStores');
        Route::get('search/products', 'SearchController@searchProducts');
        Route::get('search/near-by-stores', 'SearchController@getNearByStores');
        Route::post('/follow/toggle-stores', 'FollowedStoreController@followedStoresToggle');
        Route::get('/follow/get-followed-stores', 'FollowedStoreController@getFollowedStores');
        Route::post('/products/toggle-favorite-product', 'ProductsController@toggleFavoriteProduct');
        Route::get('/favorite/get-favorite-products', 'FavoritesController@getFavoriteProducts');
    });

    Route::post('/consumer-active', 'AuthController@activeConsumerAccount');
    Route::post('/resend-activation-code', 'AuthController@resendActivationCode');

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/store-home/{storeId}', 'ProfilesController@storeHome');
    });
    Route::group(['prefix' => 'stores'], function () {
        Route::get('/profile/{storeId}', 'ProfilesController@showProfile');
    });

    Route::group(['prefix' => 'home'], function () {
        Route::get('/', 'HomeController@home');
    });
    Route::group(['prefix' => 'store'], function () {
        Route::get('/profile/{store_id}', 'ProfilesController@showProfile');
    });
    Route::group(['prefix' => 'places'], function () {
        Route::get('/get-states', 'PlacesController@getStates');
        Route::get('get-cities', 'PlacesController@getCities');
    });
    Route::group(['prefix' => 'address', 'middleware' => 'consumer_auth'], function () {
        Route::get('/all', 'UserAddressController@index');
        Route::get('/show', 'UserAddressController@show');
        Route::post('/store', 'UserAddressController@store');
        Route::put('/update', 'UserAddressController@update');
        Route::delete('/delete', 'UserAddressController@delete');
        Route::post('/set-default-address', 'UserAddressController@setDefaultAddress');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoriesController@getParentCategories');
        Route::get('/details/{categoryId}', 'CategoriesController@getSelectedCategoryData');
        Route::get('/category-products', 'CategoriesController@getCategoryProducts');
        Route::get('/category-stores', 'CategoriesController@getCategoryStores');
        Route::get('/get-categories/{type}', 'CategoriesController@getCategories');
    });
    Route::group(['prefix' => 'shopping-cart', 'middleware' => 'consumer_auth'], function () {
        Route::post('/', 'ShoppingCartController@addShoppingCart');
    });

    Route::group(['prefix' => 'orders', 'middleware' => 'consumer_auth'], function () {
        Route::post('/', 'OrdersController@addOrder');
        Route::get('/', 'OrdersController@index');
        Route::get('{id}', 'OrdersController@show');
    });
    Route::group(['prefix' => 'search'], function () {
        Route::get('/products', 'SearchController@searchProducts');
        Route::get('/stores', 'SearchController@searchStores');
    });
    Route::group(['prefix' => 'home-sections'], function () {
        Route::get('brands', 'HomeSectionController@brands');
        Route::get('hot-offers', 'HomeSectionController@hotOffers');
        Route::get('category-product', 'HomeSectionController@categoryProduct');
        Route::get('new-arrivals', 'HomeSectionController@newArrivals');
        Route::get('most-popular', 'HomeSectionController@mostPopular');
        Route::get('stores-for-you', 'HomeSectionController@storesForYou');
        Route::get('feeds', 'HomeSectionController@feeds');
        Route::get('/sales', 'HomeSectionController@sales');
        Route::get('/best-selling/{store_id}', 'HomeSectionController@bestSelling');
        Route::get('/explore-product', 'HomeSectionController@exploreProduct');
        Route::get('just-for-you', 'HomeSectionController@justForYou');
    });

    Route::group(['prefix' => 'filters'], function () {
        Route::get('', 'FiltersController@getFilters');
    });
    Route::group(['prefix' => 'products'], function () {
        Route::get('/show', 'ProductsController@show');
        Route::get('/reviews', 'ProductsController@getProductReviews');
    });
});
Route::group(['prefix' => 'consumer-app', 'middleware' => 'consumer_auth'], function () {
    Route::get('/notification/list-notifications', 'NotificationController@listNotifications');
    Route::post('/notification/make-all-read', 'NotificationController@makeAllRead');
    Route::post('/notification/make-all-read-by-id/{id}', 'NotificationController@makeReadById');
    Route::get('/notification/un-read-count', 'NotificationController@unReadCount');
});

Route::group(['prefix' => 'coupons', 'middleware' => 'consumer_auth'], function () {
    Route::get('/', 'CouponController@getCoupons');
    Route::get('{id}', 'CouponController@getCoupon');
    Route::post('/create', 'CouponController@addCoupon');
    Route::put('/update', 'CouponController@editCoupon');
    Route::post('/activate/{id}', 'CouponController@activate');
});

Route::group(['prefix' => 'warehouses', 'middleware' => 'consumer_auth'], function () {
    Route::get('', 'WareHouseController@index');
    Route::get('{id}', 'WareHouseController@show');
    Route::delete('{id}', 'WareHouseController@destroy');
    Route::post('', 'WareHouseController@store');
    Route::post('update', 'WareHouseController@update');
});
