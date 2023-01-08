<?php

use App\Http\Controllers\Sync\SyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'dashboard', 'namespace' => 'Dashboard'], function () {
    Route::post('/login', 'AdminsController@login2');

    Route::group(['middleware' => 'admin_auth'], function () {
        Route::get('/logout', 'AdminsController@logout');
        Route::group(['prefix' => 'statistics'], function () {
            Route::get('numbers', 'DashboardController@getNumbers')->middleware('admin_can_access:aShowStatisticsNumbers');
            Route::get('category-products', 'DashboardController@productPerCategory')->middleware('admin_can_access:aShowStatisticsCategoryProducts');
            Route::get('store-types', 'DashboardController@storTypes')->middleware('admin_can_access:aShowStatisticsStoreTypes');
            Route::get('popular-products', 'DashboardController@mostPopularProducts')->middleware('admin_can_access:aShowStatisticsPopularProducts');
            Route::get('popular-stores', 'DashboardController@mostPopularStores')->middleware('admin_can_access:aShowPopularStores');
//            Route::get('numbers', 'DashboardController@getNumbers')->middleware('admin_can_access:aShowStatisticsNumbers');
//            Route::get('category-products', 'DashboardController@productPerCategory')->middleware('admin_can_access:aShowStatisticsCategoryProducts');
//            Route::get('store-types', 'DashboardController@storTypes')->middleware('admin_can_access:aShowStatisticsStoreTypes');
//            Route::get('popular-products', 'DashboardController@mostPopularProducts')->middleware('admin_can_access:aShowStatisticsPopularProducts');
//            Route::get('popular-stores', 'DashboardController@mostPopularStores')->middleware('admin_can_access:aShowPopularStores');
        });
        Route::group(['prefix' => 'productsV2'], function () {
            Route::post('/create-product', 'CreateProductsController@storeSupplierProductV2');
            Route::post('/create-product-step2', 'CreateProductsController@storeSupplierProductV2Step2');
            Route::post('/create-retail-product', 'CreateProductsController@storeRetailerProductV2');
            Route::post('/create-retail-product-step2', 'CreateProductsController@storeRetailerProductV2Step2');
        });
        Route::group(['prefix' => 'products'], function () {
            Route::get('/get-products-for-selection', 'ProductsController@getProductsForSelection')->middleware('admin_can_access:aIndexProductsForSelection');
            Route::get('/get-product-details', 'ProductsController@getProductDetails')->middleware('admin_can_access:aShowProductDetails');
            Route::put('/approve-pending-product', 'ProductsController@approvePendingProduct')->middleware('admin_can_access:aApprovePendingProduct');
            Route::get('', 'ProductsController@index')->middleware('admin_can_access:aIndexProducts');
            Route::get('/get-owners-for-selection', 'ProductsController@getOwnersForSelection')->middleware('admin_can_access:aIndexOwnersForSelection');
            Route::get('/{id}/barcode-images', 'ProductsController@showBarcodesImages')->middleware('admin_can_access:aShowProductInfo');
            Route::get('/{id}/info', 'ProductsController@showInfo')->middleware('admin_can_access:aShowProductInfo');
            Route::get('/{id}/store-details', 'ProductsController@showStoreDetails')->middleware('admin_can_access:aShowStoreDetails');
            Route::get('/{productId}/{storeId}/stock', 'ProductsController@showStock')->middleware('admin_can_access:aIndexProductStock');
            Route::get('/{productId}/{storeId}/bundles', 'ProductsController@showBundles')->middleware('admin_can_access:aIndexProductBundles');
            Route::get('export/excel', 'ProductsController@export')->middleware('admin_can_access:aProductsExport');
            Route::get('view-gallery', 'ProductsController@viewGallery');
            Route::post('upload-image', 'ProductsController@uploadProductImage');
            Route::delete('delete-image', 'ProductsController@deleteImage');
            Route::post('highlight-product', 'ProductsController@highlightProduct');
            Route::post('make-image-primary', 'ProductsController@makeImagePrimary');
            Route::delete('delete-product', 'ProductsController@deleteProduct');

//            Route::get('/get-products-for-selection', 'ProductsController@getProductsForSelection');
//            Route::get('/get-product-details', 'ProductsController@getProductDetails')->middleware('admin_can_access:aListProducts');
//            Route::put('/approve-pending-product', 'ProductsController@approvePendingProduct')->middleware('admin_can_access:aApprovePendingProduct');
//            Route::get('', 'ProductsController@index')->middleware('admin_can_access:aListProducts');
//            Route::get('/get-owners-for-selection', 'ProductsController@getOwnersForSelection')->middleware('admin_can_access:aListProducts');
//            Route::get('/{id}/barcode-images', 'ProductsController@showBarcodesImages')->middleware('admin_can_access:aListProducts');
//            Route::get('/{id}/info', 'ProductsController@showInfo')->middleware('admin_can_access:aListProducts');
//            Route::get('/{id}/store-details', 'ProductsController@showStoreDetails')->middleware('admin_can_access:aListProducts');
//            Route::get('/{productId}/{storeId}/stock', 'ProductsController@showStock')->middleware('admin_can_access:aListProducts');
//            Route::get('/{productId}/{storeId}/bundles', 'ProductsController@showBundles')->middleware('admin_can_access:aListProducts');

            Route::group(['prefix' => 'updates'], function () {
                Route::put('/info', 'UpdateProductsController@updateInfo')->middleware('admin_can_access:aProductsEditInfo');
                Route::put('/store-details', 'UpdateProductsController@updateStoreDetails')->middleware('admin_can_access:aEditStoreDetails');
                Route::put('/stock', 'UpdateProductsController@updateProductStock')->middleware('admin_can_access:aEditProductStock');
                Route::put('/bundle', 'UpdateProductsController@updateProductBundle')->middleware('admin_can_access:aEditProductBundle');
//                Route::put('/barcode-images', 'UpdateProductsController@updateProductBarcode');

            });

            Route::group(['prefix' => 'create'], function () {
                Route::post('supplier-step1', 'CreatProductsController@CreateSupplierProductStep1')->middleware('admin_can_access:aAddSupplierProduct');
                Route::post('supplier-step2', 'CreatProductsController@CreateSupplierProductStep2')->middleware('admin_can_access:aAddSupplierProduct');
                Route::post('retailer-step1', 'CreatProductsController@CreateRetailerProductStep1')->middleware('admin_can_access:aAddRetailerProduct');
                Route::post('retailer-step2', 'CreatProductsController@CreateRetailerProductStep2')->middleware('admin_can_access:aAddRetailerProduct');

            });
        });

        Route::group(['prefix' => 'orders'], function () {
            Route::get('', 'OrdersController@index')->middleware('admin_can_access:aShowOrders');
            Route::post('change-order-product-status', 'OrdersController@changeStatusOfOrdersProducts');
            Route::get('get-orders-by-store-type-for-store-rating/{type}', 'OrdersController@getOrdersByStoreTypeForStoreRating');
            Route::post('change-order-status', 'OrdersController@changeStatusOfOrder');
            Route::get('{id}', 'OrdersController@show')->middleware('admin_can_access:aIndexOrder');
            Route::get('product-package/{productId}', 'OrdersController@getPackage')->middleware('admin_can_access:aShowProductPackage');
            Route::get('export/excel', 'OrdersController@export')->middleware('admin_can_access:aOrdersExport');
        });

        Route::group(['prefix' => 'orders_parent'], function () {
            Route::get('', 'ParentOrdersController@index')->middleware('admin_can_access:aShowOrders');
            Route::get('{id}', 'ParentOrdersController@show')->middleware('admin_can_access:aIndexOrder');
        });

        Route::group(['prefix' => 'order-statuses'], function () {
            Route::get('', 'OrdersController@getStatuses')->middleware('admin_can_access:aIndexOrdersStatues');
        });

        Route::group(['prefix' => 'pending-sellers'], function () {
            Route::get('/', 'PendingUsersController@index')->middleware('admin_can_access:aIndexOrdersPendingSellers');
        });

        Route::group(['prefix' => 'admins'], function () {
            Route::get('/get-admins', 'AdminsController@getAdmins')->middleware('admin_can_access:aIndexAdmins');
            Route::post('/', 'AdminsController@store')->middleware('admin_can_access:aAddAdmin');
            Route::get('/', 'AdminsController@index')->middleware('admin_can_access:aIndexAdmins');
            Route::get('/{id}', 'AdminsController@show')->middleware('admin_can_access:aShowAdmin');
            Route::post('/update', 'AdminsController@update')->middleware('admin_can_access:aEditAdmin');
            Route::delete('/', 'AdminsController@destroy')->middleware('admin_can_access:aDeleteAdmin');
            Route::delete('/image/{id}', 'AdminsController@deleteImage')->middleware('admin_can_access:aDeleteAdminImage');
            Route::get('export/excel', 'AdminsController@export')->middleware('admin_can_access:aAdminsExport');
        });

        Route::group(['prefix' => 'suppliers'], function () {
            Route::get('/', 'SuppliersController@index')->middleware('admin_can_access:aIndexSuppliers');
            Route::get('/{id}', 'SuppliersController@show')->middleware('admin_can_access:aShowSupplier');
            Route::post('/update', 'SuppliersController@update')->middleware('admin_can_access:aEditSuppliers');
            Route::delete('/', 'SuppliersController@delete')->middleware('admin_can_access:aDeleteSuppliers');
        });

        Route::group(['prefix' => 'sellers'], function () {
            Route::post('/send-mail', 'SellerController@sendEmail');
            Route::get('/get-for-selection', 'SellerController@getForSelection')->middleware('admin_can_access:aIndexSellersForSelection');
            Route::get('/get-sellers', 'SellerController@getSellers')->middleware('admin_can_access:aIndexSellers');
            Route::get('/get-seller-details', 'SellerController@getSellerDetails')->middleware('admin_can_access:aShowSeller');
            Route::put('approve-pending-seller', 'SellerController@approvePendingSeller')->middleware('admin_can_access:sApprovePendingSeller');
            Route::get('/', 'SellerController@index')->middleware('admin_can_access:aIndexSellers');
            Route::get('/{id}', 'SellerController@show')->middleware('admin_can_access:aShowSeller');
//            Route::post('/', 'SellerController@store');
            Route::post('/update', 'SellerController@update')->middleware('admin_can_access:aEditSellers');
            Route::delete('/', 'SellerController@delete')->middleware('admin_can_access:aDeleteSellers');
            Route::get('export/excel', 'SellerController@export')->middleware('admin_can_access:aSellersExport');
            Route::post('unactive-seller', 'SellerController@unactiveSeller');
        });

        Route::group(['prefix' => 'consumers'], function () {
            Route::post('/send-mail', 'ConsumerController@sendEmail');
            Route::get('/show', 'ConsumerController@show')->middleware('admin_can_access:aShowConsumer');
            Route::get('/', 'ConsumerController@index')->middleware('admin_can_access:aIndexConsumers');
            Route::put('/update', 'ConsumerController@update')->middleware('admin_can_access:aEditConsumers');
            Route::put('/change-consumer-status', 'ConsumerController@changeConsumerStatus')->middleware('admin_can_access:aEditConsumers');
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::get('/get-all-main-sub-child', 'CategoriesController@getCategoriesAllWithTree')->middleware('admin_can_access:aIndexCategoriesForAdmin');
            Route::get('/get-categories-for-admin', 'CategoriesController@getCategoriesForAdmin')->middleware('admin_can_access:aIndexCategoriesForAdmin');
            Route::get('/for-selection', 'CategoriesController@getCategoriesForSelection')->middleware('admin_can_access:aIndexCategoriesForSelection');
            Route::get('parent-and-sub', 'CategoriesController@selectParentAndSub')->middleware('admin_can_access:aIndexParentAndSub');
            Route::get('/{id}', 'CategoriesController@show')->middleware('admin_can_access:aShowCategory');
            Route::get('/', 'CategoriesController@index')->middleware('admin_can_access:aIndexCategories');
            Route::post('/', 'CategoriesController@store')->middleware('admin_can_access:aAddCategory');
            Route::post('/update', 'CategoriesController@update')->middleware('admin_can_access:aEditCategory');
            Route::delete('/', 'CategoriesController@delete')->middleware('admin_can_access:aDeleteCategory');
            Route::get('/list/{type}/{parent_id?}', 'CategoriesController@selectCategories');
            Route::get('export/excel', 'CategoriesController@export')->middleware('admin_can_access:aCategoriesExport');
            Route::delete('delete/image/{id}', 'CategoriesController@deleteImage')->middleware('admin_can_access:aDeleteCategoryImage');
        });

        Route::group(['prefix' => 'store-types'], function () {
            Route::get('/for-selection', 'StoreTypesController@getForSelection')->middleware('admin_can_access:aIndexStoreTypesForSelection');
        });

        Route::group(['prefix' => 'stores'], function () {
            Route::get('', 'StoresController@index')->middleware('admin_can_access:aIndexStores');
            Route::post('rate-store-by-admin', 'StoresController@rateStoreByAdmin');
            Route::get('/get-for-selection', 'StoresController@getStoresForSelection')->middleware('admin_can_access:aIndexStoresForSelection');
            Route::get('/{id}/store', 'StoresController@showStoreData')->middleware('admin_can_access:aShowStoreData');
            Route::get('/{id}/change_status', 'StoresController@changeStoreStatus')->middleware('admin_can_access:aShowStoreData');
            Route::get('/{id}/get-credentials', 'StoresController@showStoreCredentials')->middleware('admin_can_access:aShowStoreData');
            Route::get('/get-store-details', 'StoresController@getStoreDetails')->middleware('admin_can_access:aShowStoreDetails');
            Route::get('/get-by-user-id/{id}', 'StoresController@getStoreByUserId')->middleware('admin_can_access:aIndexStoreByUserId');
            Route::get('/{id}', 'StoresController@getStoreById')->middleware('admin_can_access:aShowStoreById');
            Route::get('/{id}/store-address', 'StoresController@showStoreMainAddress')->middleware('admin_can_access:aIndexStoreMainAddress');
//            Route::post('/', 'StoresController@store');
            Route::post('/update', 'StoresController@updateStore')->middleware('admin_can_access:aEditStore');// TODO make it update
            // Route::post('/update-credentials', 'StoresController@updateStoreCredentials')->middleware('admin_can_access:aEditStore');// TODO make it update
            Route::post('/update_store_documents', 'StoresController@updateStoreDocumentsStatus')->middleware('admin_can_access:aEditStore');// TODO make it update
            Route::post('/update-store-auth', 'StoresController@updateStoreAuth')->middleware('admin_can_access:aEditStore');// TODO make it update
            Route::post('/update-address', 'StoresController@updateStoreAddress')->middleware('admin_can_access:aEditStoreAddress');
            Route::delete('/', 'StoresController@delete')->middleware('admin_can_access:aDeleteStore');
            Route::post('sync-badges', 'StoresController@syncBadges');
            Route::get('export/excel', 'StoresController@export')->middleware('admin_can_access:aStoresExport');
            Route::get('view-documents/{store_id}', 'StoresController@viewDocuments');
            Route::post('verify-documents/{store_id}/{document_id}/{status}', 'StoresController@verifyDocument');
            Route::put('approve-pending', 'StoresController@approvePending')->middleware('admin_can_access:aEditStore');
        });

        Route::get('/cover-area/{store_id}', 'CoverAreasController@getStoreCoverArea')->middleware('admin_can_access:aIndexStoreCoverArea');

        Route::group(['prefix' => 'roles'], function () {
            Route::get('/get-for-selection', 'RolesController@getForSelection')->middleware('admin_can_access:aIndexForSelection');
        });

//        Route::group(['prefix' => 'shopping-cart'], function () {
//            Route::get('/{id}', 'ShoppingCartController@show')->middleware('admin_can_access:aShowShoppingCart');
//            Route::get('/', 'ShoppingCartController@index')->middleware('admin_can_access:aIndexShoppingCart');
//            Route::post('/', 'ShoppingCartController@store')->middleware('admin_can_access:aAddShoppingCart');
//            Route::put('/', 'ShoppingCartController@update')->middleware('admin_can_access:aEditShoppingCart');
//            Route::delete('/', 'ShoppingCartController@delete')->middleware('admin_can_access:aDeleteShoppingCart');
//        });

        Route::group(['namespace' => 'Places'], function () {
            Route::group(['prefix' => 'countries'], function () {
                Route::get('/for-selection', 'CountriesController@getCountriesForSelection')->middleware('admin_can_access:aIndexCountriesForSelection');
                Route::get('/{id}', 'CountriesController@show')->middleware('admin_can_access:aShowCountries');
                Route::get('/', 'CountriesController@index')->middleware('admin_can_access:aIndexCountries');
                Route::post('/', 'CountriesController@store')->middleware('admin_can_access:aAddCountries');
                Route::put('/', 'CountriesController@update')->middleware('admin_can_access:aEditCountries');
                Route::delete('/', 'CountriesController@delete')->middleware('admin_can_access:aDeleteCountries');
                Route::get('export/excel', 'CountriesController@export')->middleware('admin_can_access:aCountriesExport');
            });
            Route::group(['prefix' => 'regions'], function () {
                Route::get('/for-selection', 'RegionsController@getRegionsForSelection')->middleware('admin_can_access:aIndexRegionsForSelection');
                Route::get('/{id}', 'RegionsController@show')->middleware('admin_can_access:aShowRegion');
                Route::get('/', 'RegionsController@index')->middleware('admin_can_access:aIndexRegions');
                Route::post('/', 'RegionsController@store')->middleware('admin_can_access:aAddRegions');
                Route::put('/', 'RegionsController@update')->middleware('admin_can_access:aEditRegions');
                Route::delete('/', 'RegionsController@delete')->middleware('admin_can_access:aDeleteRegions');
                Route::get('export/excel', 'RegionsController@export')->middleware('admin_can_access:aRegionsExport');
            });
            Route::group(['prefix' => 'states'], function () {
                Route::get('/for-selection', 'StatesController@getStatesForSelection')->middleware('admin_can_access:aIndexStatesForSelection');
                Route::get('/{id}', 'StatesController@show')->middleware('admin_can_access:aShowState');
                Route::get('/', 'StatesController@index')->middleware('admin_can_access:aIndexState');
                Route::post('/', 'StatesController@store')->middleware('admin_can_access:aAddState');
                Route::put('/', 'StatesController@update')->middleware('admin_can_access:aEditState');
                Route::delete('/', 'StatesController@delete')->middleware('admin_can_access:aDeleteState');
                Route::get('export/excel', 'StatesController@export')->middleware('admin_can_access:aStatesExport');
            });
            Route::group(['prefix' => 'cities'], function () {
                Route::get('/for-selection', 'CitiesController@getCitiesForSelection')->middleware('admin_can_access:aIndexCitiesForSelection');
                Route::get('/{id}', 'CitiesController@show')->middleware('admin_can_access:aShowCity');
                Route::get('/', 'CitiesController@index')->middleware('admin_can_access:aIndexCity');
                Route::post('/', 'CitiesController@store')->middleware('admin_can_access:aAddCity');
                Route::put('/', 'CitiesController@update')->middleware('admin_can_access:aEditCity');
                Route::delete('/', 'CitiesController@delete')->middleware('admin_can_access:aDeleteCity');
                Route::get('export/excel', 'CitiesController@export')->middleware('admin_can_access:aCitiesExport');
            });
            Route::group(['prefix' => 'areas'], function () {
                Route::get('for-selection', 'AreasController@getAreasForSelection')->middleware('admin_can_access:aIndexAreasForSelection');
                Route::get('/{id}', 'AreasController@show')->middleware('admin_can_access:aShowAreas');
                Route::get('/', 'AreasController@index')->middleware('admin_can_access:aIndexAreas');
                Route::post('/', 'AreasController@store')->middleware('admin_can_access:aAddAreas');
                Route::put('/', 'AreasController@update')->middleware('admin_can_access:aEditAreas');
                Route::delete('/', 'AreasController@delete')->middleware('admin_can_access:aDeleteAreas');
            });
            Route::group(['prefix' => 'zones'], function () {
                Route::get('/for-selection', 'ZonesController@getZonesForSelection')->middleware('admin_can_access:aIndexZonesForSelection');
                Route::get('/{id}', 'ZonesController@show')->middleware('admin_can_access:aShowZone');
                Route::get('/', 'ZonesController@index')->middleware('admin_can_access:aIndexZone');
                Route::post('/', 'ZonesController@store')->middleware('admin_can_access:aAddZone');
                Route::put('/', 'ZonesController@update')->middleware('admin_can_access:aEditZone');
                Route::delete('/', 'ZonesController@delete')->middleware('admin_can_access:aDeleteZone');
            });
        });

        Route::group(['prefix' => 'app-tv'], function () {
            Route::group(['prefix' => 'select'], function () {
                Route::get('/types', 'AppTvsController@getAppTvTypes')->middleware('admin_can_access:aIndexAppTvTypes');
                Route::get('/locations', 'AppTvsController@getAppTvLocations')->middleware('admin_can_access:aIndexAppTvLocations');

            });
            Route::get('/', 'AppTvsController@index')->middleware('admin_can_access:aIndexAppTvs');
            Route::get('/{id}', 'AppTvsController@show')->middleware('admin_can_access:aShowAppTv');
            Route::post('/', 'AppTvsController@store')->middleware('admin_can_access:aAddAppTv');
            Route::post('/update', 'AppTvsController@update')->middleware('admin_can_access:aEditAppTv');
            Route::delete('/{id}', 'AppTvsController@delete')->middleware('admin_can_access:aDeleteAppTv');
            Route::get('export/excel', 'AppTvsController@export')->middleware('admin_can_access:aAppTvsExport');
        });

        Route::group(['prefix' => 'home-sections'], function () {
            Route::get('/', 'HomeSectionController@index')->middleware('admin_can_access:aIndexHomeSections');
            Route::get('/{id}', 'HomeSectionController@show')->middleware('admin_can_access:aShowHomeSections');
            Route::post('/', 'HomeSectionController@store')->middleware('admin_can_access:aAddHomeSections');
            Route::post('/update', 'HomeSectionController@update')->middleware('admin_can_access:aEditHomeSections');
            Route::delete('/{id}', 'HomeSectionController@delete')->middleware('admin_can_access:aDeleteHomeSections');
        });

        Route::group(['prefix' => 'apps'], function () {
            Route::get('/get-for-selection', 'AppsController@getForSelection')->middleware('admin_can_access:aIndexAppsForSelection');
        });

        Route::group(['prefix' => 'brands'], function () {
            Route::post('', 'BrandsController@store')->middleware('admin_can_access:aAddBrands');
            Route::get('', 'BrandsController@index')->middleware('admin_can_access:aIndexBrands');
            Route::get('for-selection', 'BrandsController@getBrands')->middleware('admin_can_access:aIndexBrands');
            Route::get('{id}', 'BrandsController@show')->middleware('admin_can_access:aShowBrands');
            Route::post('update', 'BrandsController@update')->middleware('admin_can_access:aEditBrands');
            Route::delete('', 'BrandsController@delete')->middleware('admin_can_access:aDeleteBrands');
            Route::get('export/excel', 'BrandsController@export')->middleware('admin_can_access:aBrandsExport');
        });


        Route::group(['prefix' => 'permissions'], function () {
            Route::get('/get-permissions', 'PermissionController@getPermissions')->middleware('admin_can_access:aIndexPermissions');
            Route::get('/get-permissions-for-admin', 'PermissionController@getPermissionForAdmin')->middleware('admin_can_access:aIndexPermissionsForAdmin');
            Route::get('/get-roles-permissions', 'PermissionController@getRolesPermissions')->middleware('admin_can_access:aIndexRolePermissions');
            Route::post('/toggle-permissions', 'PermissionController@togglePermissions')->middleware('admin_can_access:aTogglePermissions');
        });
        Route::group(['prefix' => 'sizes'], function () {
            Route::get('', 'SizesController@index')->middleware('admin_can_access:aIndexSizes');
            Route::get('get-all/for-selection', 'SizesController@getAllForSelection');
            Route::post('', 'SizesController@store')->middleware('admin_can_access:aAddSize');
            Route::put('', 'SizesController@update')->middleware('admin_can_access:aEditSize');
            Route::delete('/{id}', 'SizesController@delete')->middleware('admin_can_access:aDeleteSize');
            Route::get('types', 'SizesController@getSizeType')->middleware('admin_can_access:aIndexSizeTypes');
            Route::get('/{id}', 'SizesController@show')->middleware('admin_can_access:aShowSize');
            Route::get('categories/child', 'SizesController@categories')->middleware('admin_can_access:aIndexCategories');
            Route::get('export/excel', 'SizesController@export')->middleware('admin_can_access:aSizesExport');
        });
        Route::group(['prefix' => 'offers'], function () {
            Route::get('', 'OffersController@index')->middleware('admin_can_access:aIndexOffers');
            // Route::post('', 'OffersController@store')->middleware('admin_can_access:AddOffer');
            // Route::put('', 'OffersController@update')->middleware('admin_can_access:EditOffer');
            // Route::delete('{id}', 'OffersController@delete')->middleware('admin_can_access:DeleteOffer');
            Route::get('/{id}', 'OffersController@show')->middleware('admin_can_access:ShowOffer');
        });
        Route::group(['prefix' => 'offer-types'], function () {
            Route::get('get-for-selection', 'OfferTypesController@getForSelection')->middleware('admin_can_access:aIndexOfferTypesForSelection');
        });
        Route::group(['prefix' => 'users'], function () {
            Route::get('get-for-selection', 'UsersController@getForSelection')->middleware('admin_can_access:aIndexOfferTypesForSelection');
        });
        Route::group(['prefix' => 'system-setup'], function () {
            Route::post('/create', 'SystemSetupController@create')->middleware('admin_can_access:aIndexSystemSetup');
            Route::put('/update', 'SystemSetupController@update')->middleware('admin_can_access:aEditSystemSetup');
            Route::delete('/delete/{id}', 'SystemSetupController@delete')->middleware('admin_can_access:aDeleteSystemSetup');
            Route::get('/view', 'SystemSetupController@view')->middleware('admin_can_access:aShowSystemSetup');
        });

        Route::group(['prefix' => 'colors'], function () {
            Route::get('get-all/for-selection', 'ColorsController@getForSelection')->middleware('admin_can_access:aIndexColors');
            Route::get('', 'ColorsController@index')->middleware('admin_can_access:aIndexColors');
            Route::get('{id}', 'ColorsController@show')->middleware('admin_can_access:aShowColors');
            Route::post('', 'ColorsController@store')->middleware('admin_can_access:aAddColors');
            Route::put('', 'ColorsController@update')->middleware('admin_can_access:aEditColors');
            Route::delete('{id}', 'ColorsController@delete')->middleware('admin_can_access:aDeleteColors');
            Route::get('export/excel', 'ColorsController@export')->middleware('admin_can_access:aColorsExport');
        });
        Route::group(['prefix' => 'materials'], function () {
            Route::get('', 'MaterialController@index')->middleware('admin_can_access:aIndexMaterials');
            Route::get('{id}', 'MaterialController@show')->middleware('admin_can_access:aShowMaterial');
            Route::delete('{id}', 'MaterialController@destroy')->middleware('admin_can_access:aDeleteMaterials');
            Route::post('', 'MaterialController@store')->middleware('admin_can_access:aAddMaterials');
            Route::put('', 'MaterialController@update')->middleware('admin_can_access:aEditMaterials');
            Route::get('export/excel', 'MaterialController@export')->middleware('admin_can_access:aMaterialsExport');
            Route::get('/get-all/for-selection', 'MaterialController@getMaterials');
        });
        Route::group(['prefix' => 'logs'], function () {
            Route::get('/', 'LogsController@getLogs')->middleware('admin_can_access:aIndexLogs');
            Route::get('{collection}/{id}', 'LogsController@getLogsByRefId')->middleware('admin_can_access:aShowLog');
            Route::get('{collection}', 'LogsController@getLogsByColloction')->middleware('admin_can_access:aIndexLogs');
        });

        Route::group(['prefix' => 'badges'], function () {
            Route::get('', 'BadgesController@index');
            Route::post('', 'BadgesController@store');
            Route::post('update', 'BadgesController@update');
            Route::get('{id}', 'BadgesController@show');
            Route::delete('{id}', 'BadgesController@destroy');
            Route::get('export/excel', 'BadgesController@export')->middleware('admin_can_access:aCategoriesExport');
            Route::get('/get-all/for-selection', 'BadgesController@getAllForSelection');
        });

        Route::group(['prefix' => 'packing-units'], function () {
            Route::get('', 'PackingUnitsController@index');
            Route::post('', 'PackingUnitsController@store');
            Route::post('update', 'PackingUnitsController@update');
            Route::get('{id}', 'PackingUnitsController@show');
            Route::delete('{id}', 'PackingUnitsController@destroy');
            Route::get('export/excel', 'PackingUnitsController@export')->middleware('admin_can_access:aCategoriesExport');
            Route::get('/get-all/for-selection', 'PackingUnitsController@getAllForSelection');
        });

        Route::group(['prefix' => 'policies'], function () {
            Route::get('', 'PoliciesController@index')->middleware('admin_can_access:aIndexMaterials');
            Route::get('{id}', 'PoliciesController@show')->middleware('admin_can_access:aShowMaterial');
            Route::post('', 'PoliciesController@store')->middleware('admin_can_access:aAddMaterials');
            Route::post('update', 'PoliciesController@update')->middleware('admin_can_access:aEditMaterials');
            Route::delete('{id}', 'PoliciesController@destroy')->middleware('admin_can_access:aDeleteMaterials');
            Route::get('export/excel', 'PoliciesController@export')->middleware('admin_can_access:aIndexMaterials');
            Route::get('/get-all/for-selection', 'PoliciesController@getPolicies');
        });

        Route::group(['prefix' => 'shipping-methods'], function () {
            Route::get('', 'ShippingMethodsController@index')->middleware('admin_can_access:aIndexMaterials');
            Route::get('{id}', 'ShippingMethodsController@show')->middleware('admin_can_access:aShowMaterial');
            Route::post('', 'ShippingMethodsController@store')->middleware('admin_can_access:aAddMaterials');
            Route::post('update', 'ShippingMethodsController@update')->middleware('admin_can_access:aEditMaterials');
            Route::delete('{id}', 'ShippingMethodsController@destroy')->middleware('admin_can_access:aDeleteMaterials');
            Route::get('export/excel', 'ShippingMethodsController@export')->middleware('admin_can_access:aIndexMaterials');
            Route::get('/get-all/for-selection', 'ShippingMethodsController@getShipmentMethods');
        });

        Route::group(['prefix' => 'payment-methods'], function () {
            Route::get('', 'PaymentMethodsController@index')->middleware('admin_can_access:aIndexMaterials');
            Route::get('{id}', 'PaymentMethodsController@show')->middleware('admin_can_access:aShowMaterial');
            Route::post('', 'PaymentMethodsController@store')->middleware('admin_can_access:aAddMaterials');
            Route::post('update', 'PaymentMethodsController@update')->middleware('admin_can_access:aEditMaterials');
            Route::delete('{id}', 'PaymentMethodsController@destroy')->middleware('admin_can_access:aDeleteMaterials');
            Route::get('export/excel', 'PaymentMethodsController@export')->middleware('admin_can_access:aIndexMaterials');
        });

        Route::group(['prefix' => 'warehouses'], function () {
            Route::get('', 'WareHouseController@index')->middleware('admin_can_access:aIndexMaterials');
            Route::get('{id}', 'WareHouseController@show')->middleware('admin_can_access:aShowMaterial');
            Route::delete('{id}', 'WareHouseController@destroy')->middleware('admin_can_access:aDeleteMaterials');
            Route::post('', 'WareHouseController@store')->middleware('admin_can_access:aAddMaterials');
            Route::post('update', 'WareHouseController@update')->middleware('admin_can_access:aEditMaterials');
            Route::post('/accept-products' , 'WareHouseController@accept_product')->middleware('admin_can_access:aIndexMaterials');
        });

        Route::group(['prefix' => 'stock'], function () {
            Route::get('', 'StockController@index')->middleware('admin_can_access:aIndexMaterials');
            Route::put('/approve-pending', 'StockController@approvePendingStock')->middleware('admin_can_access:aApprovePendingProduct');
            Route::put('/reject-pending', 'StockController@rejectPendingStock')->middleware('admin_can_access:aApprovePendingProduct');
            Route::post('/edit-stock', 'StockController@editStock')->middleware('admin_can_access:aApprovePendingProduct');
        });
        Route::group(['prefix' => 'feeds'], function () {
            Route::post('order-products-in-feeds', 'FeedsController@orderProductsInFeeds');
            Route::get('/', 'FeedsController@feeds');
            Route::put('change-feeds-video-status', 'FeedsController@changeFeedsVideoStatus');
            Route::get('get-all-videos', 'FeedsController@getAllVideos');
        });
        Route::group(['prefix' => 'reviews'], function () {
            Route::post('add-review', 'ReviewController@addReview');
            Route::put('share-review-to-feeds', 'ReviewController@changeShareReviewToFeedsStatus');
            Route::delete('delete-review/{id}', 'ReviewController@deleteReview');
            Route::get('list-all-reviews', 'ReviewController@listAllReviews');
            Route::get('list-reviews-by-product-id/{id}/{store_id}', 'ReviewController@listReviewsByProductId');
        });
        Route::group(['prefix' => 'packages'], function () {
            Route::post('add-package', 'PackageController@addPackage');
            Route::put('update-package', 'PackageController@updatePackage');
            Route::put('change-package-status', 'PackageController@changePackageStatus');
            Route::delete('delete-package/{id}', 'PackageController@deletePackage');
            Route::post('subscribe-to-package', 'PackageController@subscribeToPackage');
            Route::put('change-package-status-to-store', 'PackageController@changePackageStatusToStore');
            Route::get('get-packages-by-store-type-id/{type}', 'PackageController@getPackagesByStoreTypeId');
            Route::get('get-subscribers-stores/{type}', 'PackageController@getSubscribersStores');
            Route::get('get-package-subscribe-by-store', 'PackageController@getPackageSubscribeByStore');
        });
        Route::group(['prefix' => 'commissions'], function () {
            Route::post('create-commission', 'CommissionController@createCommission');
            Route::put('update-commission', 'CommissionController@updateCommission');
            Route::delete('delete-commission/{id}', 'CommissionController@deleteCommission');
            Route::put('change-commission-status', 'CommissionController@changeCommissionStatus');
            Route::put('paid-commission', 'CommissionController@paidCommission');
            Route::get('list-all-stores-commissions', 'CommissionController@listAllStoresCommissions');
            Route::get('list-all-commissions', 'CommissionController@listAllCommissions');
        });
    });


});


Route::group(['prefix' => 'dev', 'namespace' => 'Development'], function () {
    Route::get('seller/{count}', 'FakerController@createSeller');
    Route::get('product/{count}/{userId}', 'FakerController@createProducts');
//    Route::post('upload', 'ImagesController@uploadImage');
//    Route::post('test-upload', 'ImagesController@testImageFromMobile');
//    Route::get('data', 'ImagesController@getData');
//    Route::get('lang', 'ImagesController@lang');
//    Route::get('send-email', 'ImagesController@sendEmail');

});

Route::group(['prefix' => 'sync', 'namespace' => 'Sync'], function () {

    Route::post('users', [SyncController::class, 'syncUsers']);
    Route::post('stores', [SyncController::class, 'syncStores']);

});


Route::post('some', function () {
    return 'some response';
});
