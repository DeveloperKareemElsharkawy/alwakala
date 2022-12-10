<?php


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

Route::group(['prefix' => 'dashboard', 'namespace' => 'Shipping'], function () {
    Route::group(['prefix' => 'shipping-companies', 'middleware' => 'admin_auth'], function () {
        Route::get('', 'ShippingCompaniesController@index');
        Route::post('', 'ShippingCompaniesController@createShippingCompany');
        Route::get('{id}', 'ShippingCompaniesController@show');
        Route::delete('{id}', 'ShippingCompaniesController@delete');
    });
});

Route::group(['prefix' => 'seller-app', 'namespace' => 'Shipping\SellerApp'], function () {
    Route::group(['prefix' => 'shipping-companies'], function () {
        Route::group(['middleware' => 'seller_auth'], function () {
            Route::post('rate', 'ShippingCompaniesController@rateShippingCompany');
        });
        Route::get('', 'ShippingCompaniesController@index');
        Route::get('locations/{id}', 'ShippingCompaniesController@getShippingCompanyLocations');
        Route::get('lines/{id}', 'ShippingCompaniesController@getShippingCompanyLines');
        Route::get('suggestion', 'ShippingCompaniesController@suggestedShippingCompany');
    });
});
