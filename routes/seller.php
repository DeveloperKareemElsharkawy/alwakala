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

use App\Lib\Helpers\Categories\CategoryHelper;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'seller-app', 'namespace' => 'Seller'], function () {
    Route::post('/login', 'AuthController@login');
    Route::post('/register', 'AuthController@register');
    Route::post('/register-validate', 'AuthController@validateFirstScreen');
    Route::post('/update-device-token', 'AuthController@updateDeviceToken')->middleware('seller_auth');
    Route::get('/test-push', 'AuthController@testPushNotification')->middleware('seller_auth');
    Route::post('/reset-password', 'AuthController@resetPassword');
    Route::post('/validate-confirm-code', 'AuthController@validateConfirmCode');
    Route::post('/change-password', 'AuthController@changePassword');
    Route::post('/change-credentials-request', 'CredentialsController@changeCredentials')->middleware('seller_auth');//
    Route::post('/change-credentials-validate', 'CredentialsController@validateConfirmCode')->middleware('seller_auth');//
    Route::post('/change-credentials-submit', 'CredentialsController@changeCredentialsSubmit')->middleware('seller_auth');//
    Route::get('store-main-data', 'AuthController@sideData')->middleware('seller_auth');
    Route::get('agreement/{type}/{app}', 'AuthController@getAgreement');
    Route::post('update-password', 'AuthController@updatePassword')->middleware(['seller_auth']);
    Route::post('sync-contacts', 'AuthController@syncContacts')->middleware(['seller_auth', 'user_activation']);
    Route::post('check-token', 'AuthController@checkToken');
    Route::post('confirm_mobile_code', 'ProfilesController@ConfirmMobileCode')->middleware(['seller_auth']);;
    Route::post('check_confirmation_account', 'ProfilesController@checkConfirmationAccount')->middleware(['seller_auth']);;
    Route::group(['prefix' => 'sellers', 'middleware' => 'seller_auth'], function () { // apis for seller
        Route::post('', 'SellersController@update')->middleware('seller_auth', 'user_activation');
        Route::get('', 'SellersController@show')->middleware('seller_auth', 'user_activation');
        Route::post('add-image', 'SellersController@addSellerImage');
        Route::post('/upload-store-document', 'ProfilesController@uploadStoreDocument');
        Route::get('/get-store-document', 'ProfilesController@getStoreDocument');
        Route::get('/get-store-status', 'ProfilesController@getStoreStatus');

        Route::post('/update-mobile', 'SellersController@sendUpdateMobileCode')->middleware('seller_can_access:sEditProfile');
        Route::post('/update-mobile/resend-code', 'SellersController@sendUpdateMobileCode')->middleware('seller_can_access:sEditProfile');
        Route::post('/update-mobile/confirm', 'SellersController@updateMobileConfirmation')->middleware('seller_can_access:sEditProfile');

    });

    Route::group(['prefix' => 'visitors'], function () {
        Route::post('/register', 'VisitorsController@RegisterVisitor');
        Route::get('/categories-for-selection', 'VisitorsController@getCategories');
    });

    Route::group(['prefix' => 'complaints'], function () {
        Route::post('', 'ComplaintsController@addComplaint');
        Route::get('topics', 'ComplaintsController@getTopics');
    });

    Route::group(['prefix' => 'home'], function () {
        Route::get('/', 'HomeController@home');
        Route::get('app-tv', 'HomeController@getAppTv');
        Route::get('/just-for-you', 'HomeController@justForYou');
    });

    Route::group(['prefix' => 'slides'], function () {
        Route::get('/{slideId}/products', 'SlidesController@products');
        Route::get('/{slideId}/category-products', 'SlidesController@category_products');
    });

    Route::group(['prefix' => 'home-sections'], function () {
        Route::get('get-section/{sectionId}', 'HomeSectionController@getSection');
        Route::get('brands', 'HomeSectionController@brands');
        Route::get('just-for-you', 'HomeSectionController@justForYou');
        Route::get('hot-offers', 'HomeSectionController@hotOffers');
        Route::get('hot-offers/categories', 'HomeSectionController@hotOffersCategories');
        Route::get('category-product', 'HomeSectionController@categoryProduct');
        Route::get('sale-product', 'HomeSectionController@discount');
        Route::group(['prefix' => 'global-collection'], function () {
            Route::get('new-arrivals', 'HomeSectionController@newArrivals');
            Route::get('most-popular', 'HomeSectionController@mostPopular');
            Route::get('stores-for-you', 'HomeSectionController@storesForYou');
            Route::get('feeds', 'HomeSectionController@feeds');
        });
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/store-home/{storeId}', 'ProfilesController@storeHome');
        Route::get('/consumers', 'ProfilesController@getStoreConsumers')->middleware('seller_auth', 'user_activation');
        Route::get('/store-products/{storeId}', 'ProfilesController@storeProducts');
        Route::get('/hot-offers/{storeId}', 'ProfileSectionsController@hotOffer');
        Route::get('/best-selling/{storeId}', 'ProfileSectionsController@bestSelling');
        Route::get('/new-arrival/{storeId}', 'ProfileSectionsController@newArrival');
        Route::get('/store-rate/{storeId}', 'ProfilesController@storeRates');
        Route::get('/feeds/{storeId}', 'ProfilesController@storeFeeds');
        Route::post('/store-rate', 'ProfilesController@addStoreRate')->middleware('seller_auth', 'user_activation', 'seller_can_access:sAddStoreRate');
        Route::get('/store-category-products/{storeId}', 'ProfileSectionsController@categoryProduct');
        Route::post('/add-store-image', 'StoresController@addStoreImage')->middleware('seller_auth', 'user_activation', 'seller_can_access:sAddStoreImage');
        Route::post('/add-store-cover', 'ProfilesController@addStoreCover')->middleware('seller_auth', 'user_activation', 'seller_can_access:sAddStoreCover');
        Route::delete('/delete-store-image/{id}', 'StoresController@deleteStoreImage')->middleware('seller_auth', 'user_activation', 'seller_can_access:sDeleteStoreImage');
        Route::get('/my-permissions', 'RoleController@getUserRolesPermissions')->middleware('seller_auth');
    });

    Route::group(['prefix' => 'system', 'middleware' => ['seller_auth']], function () {
        Route::get('/all-permissions', 'RoleController@getSellerSystemPermissions')->middleware('seller_auth');
    });

    Route::group(['prefix' => 'statistics', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::group(['prefix' => 'products'], function () {
            Route::get('/', 'StatisticsController@productsStatistics')->middleware('seller_can_access:sShowProductsStatistics');
            Route::get('{period}', 'StatisticsController@productsStatisticsCharts')->middleware('seller_can_access:sShowProductsStatisticsCharts');
        });

        Route::group(['prefix' => 'orders'], function () {
            Route::get('/', 'StatisticsController@orderStatistics')->middleware('seller_can_access:sShowOrderStatistics');
            Route::get('{period}', 'StatisticsController@ordersStatisticsCharts')->middleware('seller_can_access:sShowOrdersStatisticsCharts');
        });
    });

    Route::group(['prefix' => 'stores'], function () {
        Route::get('/{storeId}/supplier-branches', 'StoresController@storeSupplierBranches');
        Route::get('/{storeId}/retailers-branches', 'StoresController@storeRetailersBranches');
        Route::get('/search/{storeId}', 'StoresController@search');
        Route::get('/profile/{storeId}', 'ProfilesController@showProfile');
        Route::get('/store-sub-categories/{storeId}', 'StoresController@storeSubCategories');
        Route::get('/get-for-selection', 'StoresController@getStoresForSelection');
        Route::get('/related-products/{storeId}', 'ProfilesController@relatedProductsVistor');
        Route::group(['middleware' => ['seller_auth', 'user_activation']], function () {
            Route::get('/profile', 'ProfilesController@getProfile');
            Route::post('/update-profile', 'ProfilesController@updateProfile')->middleware('seller_can_access:sEditProfile');
            Route::post('/update-categories', 'ProfilesController@updateStoreCategories')->middleware('seller_can_access:sEditStoreCategories');
            Route::post('/update-feed', 'ProfilesController@updateFeedLink')->middleware('seller_can_access:sEditFeedLink');
            Route::get('/get-seller-stores', 'StoresController@getSellerStores');
            Route::post('/rate-store', 'StoresController@rateStore')->middleware('seller_can_access:sRateStore');
            Route::get('/store-products', 'StoresController@getStoreProducts');
//            Route::post('/toggle-favorite-store', 'StoresController@toggleFavoriteStore')->middleware('seller_can_access:sToggleFavoriteStore');
            Route::post('/add-call', 'StoresController@storeCall')->middleware('seller_can_access:sMakeStoreCall');
            Route::get('/related-products', 'ProfilesController@relatedProducts');

            Route::post('/update-mobile', 'ProfilesController@sendUpdateMobileCode')->middleware('seller_can_access:sEditProfile');
            Route::post('/update-mobile/resend-code', 'ProfilesController@sendUpdateMobileCode')->middleware('seller_can_access:sEditProfile');
            Route::post('/update-mobile/confirm', 'ProfilesController@updateMobileConfirmation')->middleware('seller_can_access:sEditProfile');

        });
        Route::post('sync-contacts-stores', 'ProfilesController@syncContactsStores')->middleware('seller_auth');
    });

    Route::group(['prefix' => 'brands'], function () {
        Route::get('get-brands', 'BrandsController@getBrandsByCategory');
        Route::get('get-all-brands', 'BrandsController@getAllBrandsForSellection');
        Route::group(['middleware' => ['seller_auth', 'user_activation']], function () {
            Route::post('add-brand', 'BrandsController@store')->middleware('seller_can_access:sMakeBrand');
        });
        Route::get('products/{brandId}', 'BrandsController@getBrandProducts');
        Route::get('products/{brandId}/sub-categories', 'BrandsController@getSubCategoriesFromBrandProducts');
        Route::get('stores/{brandId}', 'BrandsController@getBrandStores');
    });

    Route::group(['prefix' => 'favorite', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::get('get-favorite-products', 'FavoritesController@getFavoriteProducts')->middleware('seller_can_access:sIndexFavoriteProducts');
        Route::get('get-favorite-products-count', 'FavoritesController@getFavoriteProductsCount');
    });
    // create store data
    Route::get('/categories/for-selection', 'CategoriesController@getForSelection')->middleware('seller_auth');
    Route::get('/store-types/for-selection', 'StoreTypesController@getForSelection');
    Route::get('get-countries', 'PlacesController@getCountries');
    Route::get('get-states/{country_id}', 'PlacesController@getStates');
    Route::get('get-cities/{state_id}', 'PlacesController@getCities');
    Route::get('week-days', 'StoreOpeningHoursController@getWeekDays');

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoriesController@index');
        Route::get('/list/{type}', 'CategoriesController@getCategories');
        Route::get('/category-products', 'CategoriesController@getCategoryProducts');
        Route::get('/category-stores', 'CategoriesController@getCategoryStores');
        Route::get('/sub-category/{sub_category_id}/products', 'CategoriesController@getSubCategoryProducts');
        Route::get('/sub-category/{sub_category_id}/products/filter-data', 'CategoriesController@getSubCategoryProductsFilterData');
        Route::get('/sub-category/{sub_category_id}/stores', 'CategoriesController@getSubCategoryStores');
        Route::get('/{category_id}/sub-categories', 'CategoriesController@getSubCategories');
    });
    Route::get('/sizes', 'SizesController@getSizes');
    Route::get('/packing-units', 'PackingUnitsController@getPackingUnits');
    Route::get('/generate-barcode', 'SellersController@generateBarcode')->middleware('seller_auth');


    Route::get('/logout', 'AuthController@logout')->middleware('seller_auth');

    Route::group(['prefix' => 'cover-area', 'middleware' => 'seller_auth'], function () {
        Route::get('/', 'CoverageAreaController@getStoreCoverArea')->middleware('seller_can_access:sGetStoreCoverArea');
        Route::post('/', 'CoverageAreaController@addCoverArea')->middleware('seller_can_access:sAddCoverArea');
        Route::put('/', 'CoverageAreaController@updateCoverArea')->middleware('seller_can_access:sEditCoverArea');
        Route::delete('/', 'CoverageAreaController@deleteCoverArea')->middleware('seller_can_access:sDeleteCoverArea');
    });

    Route::group(['prefix' => 'filters'], function () {
        Route::get('', 'FiltersController@getFilters');
    });

    Route::group(['prefix' => 'inventory', 'middleware' => 'seller_auth'], function () {
        Route::get('/', 'InventoryController@getInventory')->middleware('user_activation', 'seller_can_access:sShowAllInventory');
        Route::get('/{productId}', 'InventoryController@getProductInventory')->middleware('user_activation', 'seller_can_access:sShowProductInventory');
        Route::post('/add-discount', 'InventoryController@addDiscount')->middleware('user_activation', 'seller_can_access:sAddDiscount');
        Route::post('/increase-stock', 'InventoryController@increaseStock')->middleware('user_activation', 'seller_can_access:sIncreaseStock');
        Route::post('/toggle-activation', 'InventoryController@toggleSwitchActivation')->middleware('user_activation', 'seller_can_access:sSwitchProductActivation');
        Route::get('/product/status', 'InventoryController@getStatus')->middleware('user_activation', 'seller_can_access:sShowStatus');
        Route::post('/toggle-active-product', 'InventoryController@toggleActiveProduct')->middleware('user_activation', 'seller_can_access:sToggleActiveProduct');
        Route::group(['prefix' => 'update', 'middleware' => 'user_activation'], function () {
            Route::post('info', 'UpdateInventoryController@updateInfo')->middleware('seller_can_access:sEditInventoryInfo');
            Route::post('barcode', 'UpdateInventoryController@updateBarcode')->middleware('seller_can_access:sEditBarcode');
            Route::post('stock', 'UpdateInventoryController@updateStock')->middleware('seller_can_access:sEditStock');
            Route::post('package', 'UpdateInventoryController@updatePackage')->middleware('seller_can_access:sEditPackage');
            Route::post('store-details', 'UpdateInventoryController@updateStoreDetails')->middleware('seller_can_access:sEditStoreDetails');
            Route::post('bundle-price', 'UpdateInventoryController@updateBundlePrice')->middleware('seller_can_access:sEditBundlePrice');
            Route::post('add-image', 'UpdateInventoryController@addImage')->middleware('seller_can_access:sAddImage');
            Route::post('add-bundle', 'UpdateInventoryController@addBundle')->middleware('seller_can_access:sAddBundle');
        });
        Route::group(['prefix' => 'delete', 'middleware' => 'user_activation'], function () {
            Route::delete('image/{id}', 'DeleteInventoryController@deleteImage')->middleware('seller_can_access:sDeleteImage');
            Route::delete('bundle-price/{id}', 'DeleteInventoryController@deleteBundlePrice')->middleware('seller_can_access:sDeleteBundlePrice');
        });
        Route::group(['prefix' => 'non-completed', 'middleware' => 'user_activation'], function () {
            Route::get('/products', 'NonCompletedProductsController@getNonCompletedProducts')->middleware('seller_can_access:sIndexNonCompletedProducts');
            Route::delete('/products/{id}', 'NonCompletedProductsController@deleteNonCompletedProducts')->middleware('seller_can_access:sDeleteNonCompletedProducts');
        });
    });

    Route::group(['prefix' => 'open-hours'], function () {
        Route::group(['middleware' => 'seller_auth'], function () {
            Route::post('', 'StoreOpeningHoursController@storeOpeningHours')->middleware('user_activation', 'seller_can_access:sStoreOpeningHours');
            Route::put('', 'StoreOpeningHoursController@updateOpeningHours')->middleware('user_activation', 'seller_can_access:sEditOpeningHours');
        });
        Route::get('{storeId}', 'StoreOpeningHoursController@getOpeningHours')->middleware('seller_can_access:sIndexOpeningHours');
        Route::get('{storeId}/working-days', 'StoreOpeningHoursController@storeWorkingTime');
    });


    Route::group(['prefix' => 'address', 'middleware' => 'seller_auth'], function () {
        Route::get('', 'AddressesController@getAddresses')->middleware('seller_can_access:sIndexAddresses');
        Route::get('{id}', 'AddressesController@getAddress')->middleware('seller_can_access:sShowAddress');
        Route::post('', 'AddressesController@addAddress')->middleware('user_activation', 'seller_can_access:sAddAddress');
        Route::put('', 'AddressesController@editAddress')->middleware('user_activation', 'seller_can_access:sEditAddress');
        Route::delete('', 'AddressesController@deleteAddress')->middleware('user_activation', 'seller_can_access:sDeleteAddress');
        Route::put('default', 'AddressesController@changeDefaultAddress')->middleware('user_activation', 'seller_can_access:sChangeDefaultAddress');
    });

    Route::get('/my-offers', 'OffersController@myOffers')->middleware('seller_auth');
    Route::get('/my-offers-statuses', 'OffersController@myOffersStatuses')->middleware('seller_auth');

    Route::group(['prefix' => 'offers', 'middleware' => 'seller_auth'], function () {
        Route::get('/', 'OffersController@getOffers')->middleware('seller_can_access:sIndexAddresses');
        Route::get('/available', 'OffersController@availableOffers')->middleware('seller_can_access:sIndexAddresses');
        Route::get('/my-active-offers', 'OffersController@myActiveOffers')->middleware('seller_can_access:sIndexAddresses');
        Route::get('/my-rejected-offers', 'OffersController@myRejectedOffers')->middleware('seller_can_access:sIndexAddresses');
        Route::get('/my-ended-offers', 'OffersController@myEndedOffers')->middleware('seller_can_access:sIndexAddresses');
        Route::get('{id}', 'OffersController@getOffer')->middleware('seller_can_access:sIndexAddresses');
        Route::post('{id}/close-offer', 'OffersController@closeOffer')->middleware('seller_can_access:sIndexAddresses');
        Route::get('{id}/approved-stores', 'OffersController@enrolledStores')->middleware('seller_can_access:sIndexAddresses');
        Route::get('{id}/rejected-stores', 'OffersController@rejectedStores')->middleware('seller_can_access:sIndexAddresses');
        Route::post('/approve-reject-offer', 'OffersController@approveRejectOffer')->middleware('seller_can_access:sIndexAddresses');
        Route::post('/create', 'OffersController@addOffer')->middleware('seller_can_access:sIndexAddresses');
        Route::post('/edit', 'OffersController@editOffer')->middleware('seller_can_access:sIndexAddresses');
        Route::delete('/delete/{id}', 'OffersController@deleteOffer')->middleware('seller_can_access:sIndexAddresses');
    });

    Route::group(['prefix' => 'shopping-cart', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::get('/', 'ShoppingCartController@getShoppingCart')->middleware('seller_can_access:sShowShoppingCart');
        Route::post('/validate-product', 'ShoppingCartController@validateProducts')->middleware('seller_can_access:sValidateProducts');
        Route::post('/add-shopping-cart', 'ShoppingCartController@addShoppingCart')->middleware('seller_can_access:sAddShoppingCart');
        Route::post('/check-cart-available-quantities', 'ShoppingCartController@checkCartAvailableQuantities')->middleware('seller_can_access:sCheckCartAvailableQuantities');
    });

    Route::group(['prefix' => 'orders', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('/add-order', 'OrdersController@addOrderV2')->middleware('seller_can_access:sAddOrder');

        Route::get('/purchase-orders', 'OrdersController@PurchaseOrders')->middleware('seller_can_access:sPurchaseOrders');
        Route::get('/purchased-products', 'OrdersController@purchasedProducts')->middleware('seller_can_access:sPurchaseOrders');
        Route::get('/get-order', 'OrdersController@getOrder')->middleware('seller_can_access:sShowOrder');
        Route::post('/cancel-order', 'OrdersController@cancelOrder')->middleware('seller_can_access:sCancelOrder');
        Route::post('/cancel-order-product', 'OrdersController@cancelOrderProduct')->middleware('seller_can_access:sCancelOrder');

        Route::get('/sales-orders', 'OrdersController@salesOrders')->middleware('seller_can_access:sSalesOrders');
        Route::post('/shipping-order', 'OrdersController@shippingOrder');
        Route::post('/approve', 'OrdersController@approveOrder')->middleware('seller_can_access:sApproveOrder');
        Route::post('/reject', 'OrdersController@rejectOrder')->middleware('seller_can_access:sRejectOrder');
        Route::post('/reject-order-product', 'OrdersController@rejectOrderProduct')->middleware('seller_can_access:sRejectOrder');
        Route::post('change-order-product-status', 'OrdersController@changeStatusOfOrdersProducts');

        Route::get('/statuses', 'OrdersController@orderStatuses')->middleware('seller_can_access:sOrderStatuses');
        Route::get('/re-order/{id}', 'OrdersController@reOrder')->middleware('seller_can_access:sReOrder');
        Route::post('/receive-order', 'OrdersController@receiveOrder')->middleware('seller_can_access:sReceiveOrder');
        Route::post('/send-purchased-product-to-inventory', 'OrdersController@sendPurchasedProductToInventoryRequest')->middleware('seller_can_access:sReceiveOrder');
        Route::get('{id}', 'OrdersController@show');
    });

    Route::group(['prefix' => 'orders-by-barcode', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('/make-order', 'OrdersByQrController@createOrderByBarcode')->middleware('seller_can_access:sAddOrder');
        Route::post('/accept-order', 'OrdersByQrController@acceptOrderByBarcode')->middleware('seller_can_access:sAddOrder');
    });

    Route::group(['prefix' => 'barcode'], function () {
        Route::get('/get-product', 'ProductsController@getProductByBarcode');
    });

    Route::group(['prefix' => 'productsV2', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('/create-product', 'CreateProductsController@storeSupplierProductV2');
        Route::post('/create-product-step2', 'CreateProductsController@storeSupplierProductV2Step2');
        Route::post('/create-retail-product', 'CreateProductsController@storeRetailerProductV2');
        Route::post('/create-retail-product-step2', 'CreateProductsController@storeRetailerProductV2Step2');
        Route::post('find-product-by-barcode', 'CreateProductsController@findProductByBarcode')->middleware('seller_can_access:sFindProductByBarcode');
        Route::post('clone-product', 'CreateProductsController@cloneProduct')->middleware('seller_can_access:sCloneProduct');
        Route::get('get-product-attributes/{productId}', 'CreateProductsController@getProductAttributes')->middleware('seller_can_access:sAddProduct');
        Route::post('/ship-products', 'CreateProductsController@ReadyForShip')->middleware('seller_can_access:sAddProduct');
    });
    Route::group(['prefix' => 'products', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('/rate-product', 'ProductsController@rateProduct')->middleware('seller_can_access:sRateProduct');
        Route::post('/toggle-favorite-product', 'ProductsController@toggleFavoriteProduct')->middleware('seller_can_access:sToggleFavoriteProduct');
    });

    Route::get('/get-product', 'ProductsController@getProduct');
    Route::post('/toggle-show-product-to-consumer', 'ProductsController@toggleShowProductToConsumer')->middleware('seller_can_access:sToggleShowProductToConsumer');
    Route::get('/get-product-rates/{productId}/store/{storeId}', 'ProductsController@getProductRates');
    Route::get('/get-product/store/{storeId}', 'ProductsController@getStoreOfProduct');
    Route::get('/related-products/{productId}', 'ProductsController@relatedProducts');


    Route::group(['prefix' => 'shipment-methods', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::get('/get-all', 'ShippingMethodsController@getShipmentMethods');
    });

    Route::group(['prefix' => 'payment-methods', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::get('/get-all', 'PaymentMethodsController@getPaymentMethods');
    });

    Route::group(['prefix' => 'policies'], function () {
        Route::get('/get-all', 'PoliciesController@getPolicies');
    });

    Route::group(['prefix' => 'shipping-methods'], function () {
        Route::get('/get-all', 'ShippingMethodsController@getShipmentMethods');
    });

    Route::group(['prefix' => 'bundles', 'middleware' => 'seller_auth'], function () {
        Route::get('/get-bundles', 'BundlesController@getBundles');
    });

    Route::group(['prefix' => 'colors', 'middleware' => 'seller_auth'], function () {
        Route::get('/get-for-selection', 'ColorsController@getForSelection');
    });

    Route::group(['prefix' => 'units', 'middleware' => 'seller_auth'], function () {
        Route::get('/get-for-selection', 'UnitsController@getForSelection');
    });

    Route::group(['prefix' => 'reports', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('', 'ReportsController@report');
    });

    Route::group(['prefix' => 'qrcode'], function () {
        Route::get('/get-store', 'QRCodeController@getStoreByQRCode');
        Route::get('/my-qrcode', 'QRCodeController@myStoreQRCode')->middleware('seller_auth');
    });

    Route::group(['prefix' => 'search'], function () {
        Route::get('stores', 'SearchController@searchStores');
        Route::get('products', 'SearchController@searchProducts');
        Route::get('/near-by-stores', 'SearchController@getNearByStores');
    });
    Route::group(['prefix' => 'follow', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('/toggle-stores', 'FollowedStoreController@followedStoresToggle');
        Route::get('/get-followed-stores', 'FollowedStoreController@getFollowedStores');
        Route::get('/get-followers-stores', 'FollowedStoreController@getFollowersStores');
        Route::get('/get-followed-stores-count', 'FollowedStoreController@getFollowedStoresCount');
    });
    Route::group(['prefix' => 'users', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('/create', 'RoleController@createUser')->middleware('seller_can_access:sAddStoreUser');
        Route::put('/update', 'RoleController@editUser')->middleware('seller_can_access:sEditStoreUser');


        Route::delete('/delete', 'RoleController@deleteUser')->middleware('seller_can_access:sDeleteStoreUser');
        Route::get('/get-roles/{user_type}', 'RoleController@getRolesForUserType')->middleware('seller_can_access:sShowRolesForUserType');
    });
    Route::group(['prefix' => 'logs', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('/create', 'RoleController@createUser')->middleware('seller_can_access:sCreateLogos');
        Route::put('/update', 'RoleController@editUser')->middleware('seller_can_access:sEditLogos');
        Route::delete('/delete', 'RoleController@deleteUser')->middleware('seller_can_access:sDeleteLogos');
        Route::get('/get-logs', 'ProfilesController@getSellerLogos')->middleware('seller_can_access:sShowSellerLogos');
    });

    Route::group(['prefix' => 'suggested', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::get('stores', 'SuggestedController@suggestedStores');
        Route::get('products', 'SuggestedController@suggestedProducts');
    });
    Route::group(['prefix' => 'store-setting', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('/store', 'StoreSettingController@store')->middleware('seller_can_access:sAddStoreSetting');
        Route::get('/list', 'StoreSettingController@list')->middleware('seller_can_access:sShowStoreSetting');
    });

    Route::group(['prefix' => 'materials', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::get('/', 'MaterialsController@getMaterials');
    });
    Route::group(['prefix' => 'users', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::get('', 'RoleController@index')->middleware('seller_can_access:sIndexStoreUsers');
        Route::post('', 'RoleController@createUser')->middleware('seller_can_access:sAddStoreUser');
        Route::put('', 'RoleController@editUser')->middleware('seller_can_access:sEditStoreUser');
        Route::get('/get-roles/{user_type}', 'RoleController@getRolesForUserType')->middleware('seller_can_access:sShowRolesForUserType');
        Route::get('/get-roles-for-select', 'RoleController@getRolesForSelection')->middleware('seller_can_access:sShowRolesForSelection');
    });
    Route::group(['prefix' => 'activities', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::get('', 'ActivitiesController@index')->middleware('seller_can_access:sShowActivities');
        Route::get('/{userId}', 'ActivitiesController@userActivity')->middleware('seller_can_access:sShowUserActivity');
    });
    Route::group(['prefix' => 'setting', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('/save', 'StoreSettingController@store')->middleware('seller_can_access:sAddStoreSetting');
        Route::put('/update', 'StoreSettingController@update')->middleware('seller_can_access:sEditStoreSetting');
        Route::get('/show-all/{storeId}', 'StoreSettingController@index')->middleware('seller_can_access:sShowStoreSetting');
        Route::delete('/delete/{id}', 'StoreSettingController@destroy')->middleware('seller_can_access:sDeleteStoreSetting');
    });

    Route::group(['prefix' => 'measurements', 'middleware' => 'seller_auth'], function () {
        Route::get('', 'MeasurementsController@storeMeasurements')->middleware('seller_can_access:sIndexStoreMeasurements');
        Route::post('', 'MeasurementsController@store')->middleware('seller_can_access:sAddStoreMeasurements');
        Route::put('/update/{id}', 'MeasurementsController@update')->middleware('seller_can_access:sEditStoreMeasurements');
        Route::delete('delete/{id}', 'MeasurementsController@destroy')->middleware('seller_can_access:sDeleteStoreMeasurements');

    });

    Route::group(['prefix' => 'coupons', 'middleware' => 'seller_auth'], function () {
        Route::get('/', 'CouponController@getCoupons')->middleware('seller_can_access:sIndexAddresses');
        Route::get('{id}', 'CouponController@getCoupon')->middleware('seller_can_access:sIndexAddresses');
        Route::post('/create', 'CouponController@addCoupon')->middleware('seller_can_access:sIndexAddresses');
        Route::post('/update', 'CouponController@editCoupon');
        Route::post('/toggle-activate/{id}', 'CouponController@activate')->middleware('seller_can_access:sIndexAddresses');
        Route::get('/all/active', 'CouponController@activatedCoupons')->middleware('seller_can_access:sIndexAddresses');
        Route::get('/all/inactive', 'CouponController@inactiveCoupons')->middleware('seller_can_access:sIndexAddresses');
    });

    Route::group(['prefix' => 'warehouses', 'middleware' => 'seller_auth'], function () {
        Route::get('', 'WarehouseController@index')->middleware('seller_can_access:sIndexAddresses');
        Route::get('{id}', 'WarehouseController@show')->middleware('seller_can_access:sIndexAddresses');
        Route::delete('{id}', 'WarehouseController@destroy')->middleware('seller_can_access:sIndexAddresses');
        Route::post('', 'WarehouseController@store')->middleware('seller_can_access:sIndexAddresses');
        Route::put('update', 'WarehouseController@update')->middleware('seller_can_access:sIndexAddresses');
    });


    Route::group(['prefix' => 'statistics', 'middleware' => ['seller_auth', 'user_activation', 'seller_can_access:sStoreDashboardStatistics']], function () {
        Route::get('', 'DashboardController@statistics');
    });

    Route::group(['prefix' => 'target-offers', 'middleware' => ['seller_auth', 'user_activation']], function () {
        Route::post('', 'TargetOffersController@store');
        Route::get('{offer_id}/take-action', 'TargetOffersController@takeAction');
    });


    Route::group(['prefix' => 'cart', 'middleware' => ['seller_auth']], function () {
        Route::get('list', 'CartController@index');
        Route::get('get-count', 'CartController@getCount');
        Route::get('summary ', 'CartController@summary');
        Route::post('add', 'CartController@addCart');
        Route::post('apply-coupon', 'CartController@applyCoupon');
        Route::post('change-quantity', 'CartController@changeCartQuantity');
        Route::post('remove-cart', 'CartController@removeCart');
    });

    Route::group(['prefix' => 'my-feeds', 'middleware' => ['seller_auth']], function () {
        Route::get('/', 'FeedsController@myFeeds');
        Route::get('/{feedId}', 'FeedsController@getFeed');
        Route::post('create', 'FeedsController@createFeed');
        Route::post('/{feedId}/update', 'FeedsController@updateFeed');
        Route::post('/{feedId}/delete', 'FeedsController@deleteFeed');

    });
    Route::group(['prefix' => 'feeds'], function () {
        Route::get('/', 'FeedsController@allFeedsList');
        Route::post('/toggle-favorite-feed', 'FeedsController@toggleFavoriteFeed')->middleware('seller_auth');
        Route::get('/my-favorites', 'FeedsController@myFavorites')->middleware('seller_auth');

    });

    Route::group(['prefix' => 'feeds', 'middleware' => ['seller_auth']], function () {
        Route::post('upload-video', 'FeedsVideosController@uploadFeedsVideos');
        Route::delete('delete-video ', 'FeedsVideosController@deleteFeedsVideos');
        Route::get('all-video/{id}', 'FeedsVideosController@listAllVideos');
    });
    Route::group(['prefix' => 'reviews', 'middleware' => ['seller_auth']], function () {
        Route::post('add-review', 'ReviewController@addReview');
        Route::put('share-review-to-feeds', 'ReviewController@changeShareReviewToFeedsStatus');
        Route::delete('delete-review/{id}', 'ReviewController@deleteReview');
        Route::get('list-reviews-by-product-id/{id}/{store_id}', 'ReviewController@listReviewsByProductId');
    });
    Route::group(['prefix' => 'packages', 'middleware' => ['seller_auth']], function () {
        Route::post('subscribe-to-package', 'PackageController@subscribeToPackage');
        Route::get('get-packages-by-store-type-id/{type}', 'PackageController@getPackagesByStoreTypeId');
        Route::get('get-package-subscribe-by-store', 'PackageController@getPackageSubscribeByStore');
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::get('social', 'SettingsController@getSocial');
    });

    Route::group(['prefix' => 'pages'], function () {
        Route::get('terms-and-conditions', 'PagesController@termsAndConditions');
        Route::get('privacy-policy', 'PagesController@privacyPolicy');
        Route::get('refund-policy', 'PagesController@refundPolicy');
        Route::get('product-policies', 'PagesController@productPolicies');
        Route::get('shipping-methods-policy', 'PagesController@shippingMethodsPolicies');
        Route::get('product-materials-policy', 'PagesController@productMaterialsPolicies');
    });

    Route::group(['prefix' => 'faqs/categories'], function () {
        Route::get('/', 'FaqsController@categories');
        Route::get('/{faqCategoryId}', 'FaqsController@faqs');
    });

    Route::group(['prefix' => 'chat', 'middleware' => 'seller_auth'], function () {
        Route::get('/conversations', 'ChatController@conversations');
        Route::get('/conversations/{store_id}', 'ChatController@showConversation');
        Route::post('/{store_id}/send-message', 'ChatController@sendMessage');
    });
});

Route::group(['prefix' => 'seller-app/notifications', 'middleware' => 'seller_auth'], function () {
    Route::get('', 'NotificationController@listNotifications')->middleware('seller_can_access:sListStoreNotifications');
    Route::post('make-all-read', 'NotificationController@makeAllRead')->middleware('seller_can_access:sReadNotification');
    Route::post('make-read-by-id/{id}', 'NotificationController@makeReadById')->middleware('seller_can_access:sReadNotificationById');
    Route::get('un-read-count', 'NotificationController@unReadCount')->middleware('seller_can_access:sUnReadNotificationsCount');
});


Route::get('/test', function () {

    foreach (\App\Models\Category::all() as $category) {
        if (CategoryHelper::checkCategoryLevel('sub_sub_category', $category->id)) {
            $category->sizes()->syncWithoutDetaching([2, 3, 4]);
            $category->brands()->syncWithoutDetaching([1, 2, 3, 4]);
        }
    }
});
Route::post('pull', function () {
      $output = shell_exec('git pull 2>&1');
      dd($output);
});
