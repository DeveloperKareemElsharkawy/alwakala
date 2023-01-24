<?php

use \App\Enums\Activity\Activities;

return [
    'general' => [
        'error' => 'Error Occurred',
        'at_least_one' => 'you souled at least add one field',
        'created' => 'Record Created',
        'listed' => 'Data listed successfully',
        'not_found' => 'Not found',
        'forbidden' => 'You cannot do this action',
        'store_exists' => 'Check existence of store',
        'success' => 'Success',
    ],

    'auth' => [
        'error' => 'Error Occurred',
        'no_store' => 'You do not have store',
        'invalid_login_data' => 'Wrong Password',
        'seller_not_found' => 'This account was not found',
        'login' => 'Logged In',
        'logout' => 'Logged Out',
        'store_created' => 'Store created successfully data will be reviewed',
        'register_step1' => 'First step done successfully',
        'device_token_updated' => 'Device token updated',
        'notification_sent' => 'Notification sent',
        'reset_code_sent' => 'Reset code sent',
        'invalid_code' => 'Please enter the correct code sent on your number',
        'valid_code' => 'Valid code',
        'expired_code' => 'Expired code request another one',
        'cannot_change_pass' => 'You cannot change password check code',
        'pass_changed' => 'Password Changed',
        'mobile_changed' => 'Mobile Changed',
        'email_changed' => 'Email Changed',
        'side_data' => 'Side data',
        'type_error' => 'not valid',
        'user_created' => 'user created successfully',
        'access_deny' => 'Access Deny',
        'user_updated' => 'user updated successfully',
        'user_deleted' => 'user deleted successfully',
        'agreement' => 'F.A.Q Agreement',
        'wrong_password' => 'wrong password',
        'chose_diff_pass' => 'chose new password',
        'access_denied' => 'Access Denied',
        'token_check' => 'token check',
        'mobile_or_email_required' => 'Email Or Mobile Is Required',
        'email_exists_validation' => 'Email Not Found',
        'mobile_exists_validation' => 'Mobile Not Found',
        'name_regex' => 'Seller name is supported only letters, space, single quote, dash, and dot',
        'store_name_regex' => 'Seller name is supported only letters, space, single quote, dash, and dot',
        'legal_name_regex' => 'Seller name is supported only letters, space, single quote, dash, and dot',
    ],

    'inventory' => [
        'not_active' => 'Product is not active!',
        'unauthorized' => 'you do not own this Product',
        'stock_updated' => 'Stock has been updated',
        'discount_added' => 'Discount added',
        'has_bundle' => 'Product should have only one price to add discount',
        'delete_bundle_price' => 'Delete Bundle Price',
        'bundle_price_deleted' => 'Bundle Price Deleted',
        'bundle_price_not_found' => 'Bundle Price Deleted',
        'image_deleted' => 'Image Deleted',
        'product_minimum_image_limit' => 'product must have at least one image for every color',
        'product_inventory' => 'Product Inventory',
        'inventory_inserted_successfully' => 'Inventory Inserted Successfully',
        'inventory_status' => 'Inventory Status',
        'packing_units' => 'packing units',
        'owner_update_info_limit' => 'only owner can update main info',
        'update_image_limit' => 'only owner can add image',
    ],

    'cart' => [
        'available' => 'Available',
        'not_available' => 'Not Available',
        'valid' => 'Valid',
        'invalid_cart' => 'you cannot buy your products',
        'product_not_found' => 'Product Not Found',
        'shopping_cart_added' => 'Shopping Cart Added',
        'product_color_stock_empty' => 'Product Does Not Have Stock',
        'you_own_this_product' => 'You own this product',
        'invalid_quantity' => 'Invalid Quantity',
        'quantity_updated' => 'Quantity Updated',
        'cart_deleted' => ' Cart Deleted',
        'apply' => 'Coupon isn\'t valid anymore',
        'quantity' => 'Coupon quantity is 0',
        'purchase_amount' => 'Cart total is less than allowed purchase amount',
        'coupon_inactive' => 'This coupon is inactive'

    ],

    'coupon' => [
        'quantity' => 'The available amount of coupon is 0',
        'purchase_amount' => 'Your cart total is less than the total available for using this coupon',
        'not_found' => 'Coupon not found',
        'inactive' => 'The coupon is not available for the current period',
        'ended' => 'Coupon isn\'t valid anymore',
        'qty_is_over' => 'The maximum limit for this coupon has been used',
        'no_products_for_this_coupon' => 'There are no products for this coupon',
    ],


    'status' => [
        'available' => 'Available',
        'not_available' => 'Not Available',
        'in_review' => 'In Review',
        'soon' => 'Coming Soon'
    ],

    'order' => [
        'no_cart_available' => 'there is no cart available',
        'no_quantities_available' => 'quantities not available',
        'cannot_cancel' => 'cannot cancel not issued orders',
        'canceled' => 'order canceled successfully',
        'already_canceled' => 'Order Already Canceled',
        'add_order_id' => 'please add order id',
        'cannot_receive_in_progress_order' => 'cannot receive not in progress orders',
        'consumer_unit_publish_date' => 'please enter consumer price and publish date for these products',
        'received' => 'order received successfully',
        'available' => 'Available',
        'retrieved_all' => 'orders got successfully',
        'retrieved' => 'order',
        'not_found' => 'order not found',
        'only_approve_reject' => 'you can only approve or reject this order',
        'only_approve_reject_yours' => 'you can only approve or reject your orders',
        'no_products_to_reject' => 'No Products To Reject',
        'no_products_to_approve' => 'No Products To Approve',
        'is_issued' => 'you cannot approve or reject not issued orders',
        'rejected' => 'order rejected successfully',
        'approved' => 'order accepted successfully',
        'today_orders' => 'Today Orders',
        'statuses' => 'Order Statuses',
        'add' => 'order placed successfully',
        'payment_methods' => 'Payment Methods',
        'shipping' => 'The order is being shipped',
        'order_product_false_ownership' => 'you do not own this product',
        'order_products_status_not_valid' => 'Status of products sent :products Not yet shipped',

        'product' => [
            'rejected' => 'Order Product Rejected',
        ]
    ],

    'addresses' => [
        'retrieved_all' => 'Address retrieved',
        'retrieved' => 'Address',
        'added' => 'Address added successfully',
        'edited' => 'Address updated successfully',
        'deleted' => 'Address deleted successfully',
        'set_default' => 'Receiving address changed',
        'wrong_address' => 'Wrong Address',
    ],

    'stores' => [
        'image_added' => 'Image Added',
        'image_deleted' => 'Image Deleted',
        'many_images' => 'You can only add 5 images',
        'call_added' => 'Call added',
        'image_not_found' => 'Image not found',
        'follow_store' => 'followed the store successfully',
        'un_follow_store' => 'unfollowed the store successfully',
        'follow_denied' => 'you cannot follow',
        'favorite_denied' => 'You cannot Favorite your Own product, choose another product',
        'show_to_consumer' => 'show to consumer successfully',
        'hide_to_consumer' => 'hide from consumer successfully',
        'store_list' => 'Stores',
        'store_cover_area' => 'Store Cover Area',
        'store_area_not_found' => 'Store Area Not Found',
        'store_profile' => 'Store Profile',
        'store_status' => 'Store Status',
        'store_not_found' => 'Store Not Found',
        'profile_updated' => 'Profile Updated',
        'link_updated' => 'Link Updated',
        'store_home' => 'Store Home',
        'store_products' => 'Store Products',
        'store_feeds' => 'Store Feeds',
        'store_rates' => 'Store Rates',
        'store_cover_added' => 'Store Cover Added',
        'store_rate_added' => 'Store Rate Added',
        'store_logs' => 'store_logs',
        'store_hot_offers' => 'Store Hot Offers',
        'store_best_selling' => 'Store Best Selling',
        'store_new_arrival' => 'Store New Arrival',
        'store_category_products' => 'Store Category Products',
        'near_by_stores' => 'near_by_stores',
        'search_products' => 'Search Products',
        'search_stores' => 'Search Stores',
        'open_hours_added' => 'open hours added',
        'open_hours_not_found' => 'Business hours have not been added by this merchant',
        'week_days' => 'Week Days',
        'store_favorite' => 'Store Favorite',
        'store_unfavorite' => 'Store removed from Favorite List',
        'store_types_retrieved' => 'store types retrieved successfully',
        'category_store' => 'Category Store',
        'colors_retrieved' => 'Colors Recovered Successfully',
        'updated' => 'store details updated',
        'stores_count' => 'followed stores count',
        'upload_documents' => 'Documents Uploaded Successfully',
        'not_found' => 'Store not Found',
        'choose_another_number' => 'This is the number currently used, please use another number'

    ],

    'shipping' => [
        'shipping_company' => 'Shipping Companies',
        'shipping_method' => 'Shipping Method',
        'shipping_company_created' => 'Shipping Company Created',
        'company_noy_exists' => 'Shipping Company Not Exists',
        'company_deleted' => 'Shipping Company Deleted',
        'company_location' => 'Shipping Company Lines',
        'company_rated' => 'ٌRate Done',
    ],
    'reports' => [
        'report_added' => 'Report sent'
    ],
    'category' => [
        'un_valid_parent' => 'UnValid CategoryId',
        'un_valid_sub_category' => 'UnValid CategoryId',
        'un_required_packing_unit' => 'Packing Unit Is Required',
        'id_required' => 'category_id Is Required',
        'delete_error' => 'The Category cannot be deleted because it is not associated with :model table',

    ],
    'actions' => [
        'create_productS1' => 'product step 1 creation',
        'create_productS2' => 'product step 2 creation',
        'product_added' => 'Product ِ Added',
        'create_store' => 'store created',
        'add_discount' => 'discount added',
        'increase_stock' => 'stock updated',
        'inventory_created' => 'inventory updated',
        'update_store' => 'store updated',
        'rate_added' => 'rate done',
        'store_cover_added' => 'cover added',
        'store_categories_updated' => 'categories updated',
        'store_feed_link_updated' => 'feed link added',
        'store_area_added' => 'Store Area Added',
        'store_area_updated' => 'Store Area Updated',
        'store_area_deleted' => 'Store Area Deleted',
        'un_valid_parent' => 'UnValid CategoryId',
        'un_required_packing_unit' => 'un required packing unit id',
        'id_required' => 'category_id is required',
        'favorite_products' => 'Favorite Products',
        Activities::CREATE_PRODUCT_S1 => 'create productS1',
        Activities::CREATE_PRODUCT_S2 => 'create productS1',
        Activities::UPDATE_PRODUCT => 'update product',
        Activities::APPROVE_PRODUCT => 'approve product',
        Activities::UPDATE_PRODUCT_INFO => 'update product info',
        Activities::UPDATE_PRODUCT_DETAILS => 'update product details',
        Activities::UPDATE_PRODUCT_STOCK => 'update product stock',
        Activities::UPDATE_PRODUCT_BUNDLE => 'update product bundle',

        Activities::CREATE_STORE => 'create store',
        Activities::UPDATE_STORE => 'update store',
        Activities::UPDATE_STORE_ADDRESS => 'update store address',
        Activities::DELETE_STORE => 'delete store',

        Activities::CREATE_ORDER => 'create order',
        Activities::RECEIVE_ORDER => 'receive order',
        Activities::APPROVE_ORDER => 'approve order',
        Activities::REJECT_ORDER => 'reject order',
        Activities::CANCEL_ORDER => 'cancel order',

        Activities::INVENTORY_STORE => 'inventory store',
        Activities::ADD_DISCOUNT => 'add discount',
        Activities::INCREASE_STOCK => 'increase stock',

        Activities::ADD_RATE => 'add rate',

        Activities::ADD_COVER_AREA => 'add cover area',
        Activities::UPDATE_CATEGORIES => 'update categories',
        Activities::UPDATE_FEED_LINK => 'update feed link',
        Activities::UPDATE_PROFILE => 'update profile',
        Activities::CREATE_ADMIN => 'create admin',
        Activities::UPDATE_ADMIN => 'update admin',
        Activities::DELETE_ADMIN => 'delete admin',
        Activities::CREATE_BRAND => 'create brand',
        Activities::UPDATE_BRAND => 'update brand',
        Activities::DELETE_BRAND => 'delete brand',
        Activities::DELETE_APP_TV => 'delete app tv',
        Activities::UPDATE_APP_TV => 'update app tv',
        Activities::CREATE_APP_TV => 'create app tv',
        Activities::DELETE_CATEGORY => 'delete category',
        Activities::UPDATE_CATEGORY => 'update category',
        Activities::CREATE_CATEGORY => 'create category',
        Activities::DELETE_COLOR => 'delete color',
        Activities::UPDATE_COLOR => 'update color',
        Activities::CREATE_COLOR => 'create color',
        Activities::DELETE_CONSUMER => 'delete consumer',
        Activities::CREATE_SECTION => 'create section',
        Activities::UPDATE_SECTION => 'update section',
        Activities::DELETE_SECTION => 'delete section',
        Activities::DELETE_MATERIAL => 'delete material',
        Activities::UPDATE_MATERIAL => 'update material',
        Activities::CREATE_MATERIAL => 'create material',
        Activities::DELETE_OFFER => 'delete offer',
        Activities::CREATE_OFFER => 'create offer',
        Activities::DELETE_UNIT => 'delete unit',
        Activities::CREATE_UNIT => 'create unit',
        Activities::UPDATE_UNIT => 'update unit',
        Activities::DELETE_PERMISSION => 'delete permission',
        Activities::CREATE_PERMISSION => 'create permission',
        Activities::APPROVE_SELLER => 'approve seller',
        Activities::UPDATE_SELLER => 'update seller',
        Activities::DELETE_SELLER => 'delete seller',
        Activities::DELETE_SIZE => 'delete size',
        Activities::CREATE_SIZE => 'create size',
        Activities::UPDATE_SIZE => 'update size',
        Activities::UPDATE_SUPPLIER => 'update supplier',
        Activities::DELETE_SUPPLIER => 'delete supplier',
        Activities::CREATE_SYSTEM_SETUP => 'create system setup',
        Activities::DELETE_SYSTEM_SETUP => 'delete system setup',
        Activities::UPDATE_SYSTEM_SETUP => 'update system setup',
        Activities::DELETE_AREA => 'delete area',
        Activities::CREATE_AREA => 'create area',
        Activities::UPDATE_AREA => 'update area',
        Activities::UPDATE_CITY => 'update city',
        Activities::CREATE_CITY => 'create city',
        Activities::DELETE_CITY => 'delete city',
        Activities::UPDATE_COUNTRY => 'update country',
        Activities::CREATE_COUNTRY => 'create country',
        Activities::DELETE_COUNTRY => 'delete country',
        Activities::UPDATE_REGION => 'update region',
        Activities::CREATE_REGION => 'create region',
        Activities::DELETE_REGION => 'delete region',
        Activities::UPDATE_STATE => 'update state',
        Activities::CREATE_STATE => 'create state',
        Activities::DELETE_STATE => 'delete state',
        Activities::UPDATE_ZONE => 'update zone',
        Activities::CREATE_ZONE => 'create zone',
        Activities::DELETE_ZONE => 'delete zone',
    ],
    'system' => [
        'retrieved' => 'retrieved successfully',
        'created' => 'created successfully',
        'updated' => 'updated successfully',
        'deleted' => 'deleted successfully',
    ],
    'product' => [
        'not_valid' => 'you cant complete this action',
        'not_owner' => 'edit deny you are not the owner',
        'active' => 'activation done',
        'not_active' => 'deactivation done',
        'reviewed' => 'Product Reviewed Successfully',
        'cant_reviewed' => 'Product cant Reviewed',
        'barcode_used' => 'Barcode is used for other product',
        'created' => 'Product Created',
        'sumOfMaterial' => 'materials rate should not be greater than 100',
        'favorite' => 'product added to your favorites',
        'unfavourite' => 'product removed from your favorites ',
        'product_under_review' => 'product is under review',
        'product_not_found' => 'product Not Found',
        'products' => 'products',
        'review_added' => 'Review Added',
        'colors_retrieved' => 'colors retrieved successfully',
        'updated' => 'product main info updated',
        'price_updated' => 'Price Updated',
        'product_stock_owner_update_limit' => 'Only Owner Can Update Stocks',
        'wrong_barcode' => 'Wrong Barcode',
        'fav_products_count' => 'favorite products count',
        'no_product' => 'Please review your product ID',
        'no_store' => 'Please review the store of this product',
        'seller_type_not_supplier' => 'You must be a Supplier to add this product',

    ],
    'errors' => [
        '500' => 'connection error',
    ],

    'materials' => [
        'destroy' => 'destroyed successfully',
        'update' => 'updated successfully',
        'save' => 'saved successfully',
        'can_not_destroy' => 'you can not delete there is products under this material '
    ],

    'colors' => [
        'exists' => 'this color already exists',
    ],
    'notifications' => [
        'review_product' => 'your product has been reviewed',
        'favorite_store' => 'your store has been add to favorite',
        'follow_store' => 'your store has been followed ',
        'approve_order' => 'your order has been approved',
        'reject_order' => 'your order has been rejected',
        'add_order' => 'your order has been added',
        'receive_order' => 'your order has been received',
        'favorite_product' => 'your product has been followed',
        'make_read' => 'notification mark as read successfully',
        'approve_seller' => 'Your account has been activated successfully',

    ],
    'roles' => [
        'purchase_manger' => 'Purchase Manger',
        'sales_manger' => 'Sales Manger',
        'sales' => 'Sales',
        'cannot_creat_owner' => 'you can\'t create other owner',
        'list_roles' => 'role listed',
    ],
    'sections' => [
        'home' => 'Home',
        'just_for_you' => 'Just For You',
        'feeds_list' => 'Feeds List',
        'sections' => 'Sections',
        'brands' => 'Brands',
        'new_arrivals' => 'New Arrivals',
        'most_popular' => 'Most Popular',
        'inventory' => 'Inventory',
        'countries' => 'Countries',
        'states' => 'States',
        'cities' => 'Cities',
        'sizes' => 'Sizes',
    ],

    'barcode' => [
        'update_limit' => 'only owner can update barcode',
        'updated' => 'Barcode Updated'
    ],

    'package' => [
        'update_limit' => 'only owner can update package',
        'updated' => 'package Updated',
        'update_bundles_limit' => 'you can only add three bundles',
        'bundle_added' => 'Bundle Added'
    ],
    'measurements' => [
        'save' => 'Measurement is saved successfully ',
        'already_exist' => 'The size already exists. Please choose another section or size',
        'deleted' => 'Measurement deleted successfully',
        'not_found' => 'Measurement Not Found',
        'updated' => 'Measurement updated successfully',

    ],
    'policy' => [
        'get_policies' => 'Get Policies',
    ],

    'payment_methods' => [
        'created' => 'Payment Method Created Successfully',
        'updated' => 'Payment Method Updated Successfully',
        'deleted' => 'Payment Method Deleted Successfully',
    ],
    'sms' => [
        'store_register' => 'Your elwekala account is successfully registered. Activation Code is: :code',
        'store_forget_password' => 'Your elwekala account forget password Code is: :code',
        'store_change_credential_mobile' => 'Your elwekala account change mobile Code is: :code',
        'store_change_credential_email' => 'Your elwekala account change email Code is: :code',
    ],
    'notifications' => [
        'store_account_confirmed' => 'Your elwekala account is successfully confirmed',
    ],
    'feeds' => [
        'upload_video' => 'Video Uploaded SuccessFully',
        'delete_video' => 'Video Deleted SuccessFully',
        'favorite' => 'Feed Added To Favorites',
        'unfavorite' => 'Feed removed To Favorites',
    ],
    'reviews' => [
        'add' => 'Review Added Successfully',
        'delete' => 'Review Deleted Successfully',
        'forbidden' => 'Review Forbidden',
        'in_valid' => 'Status Invalid',
        'status_changed' => 'Status Changed Successfully'
    ],
    'packages' => ['add' => 'Your Request Under Processing', 'forbidden' => 'You Cannot Subscribe in two Packages'],
    'warehouse' => [
        'not_found' => 'Please check warehouse first',
        'not_owner' => 'You can not use this warehouse '
    ],

    'feed' => [
        'created' => 'New Feed Created Successfully',
        'updated' => 'Feed Updated Successfully',
        'deleted' => 'Feed Deleted Successfully',
        'unauthorized' => 'You do not have the specified Feed',

        'validation' => [
            'youtube_url_error' => 'youtube url is not valid',
            'only_images_or_youtube_url' => 'Please Select Only  Images Or Youtube',
        ]
    ],

    'offers' => [
        'invalid_product' => "Product doesn't exist or Policy Non Wekala ",
        'accept_your_offer_err' => "you can't Approve your own Offers ",
        'accept_not_your_offer_err' => "you can't Approve this Offers ",
        'already_submitted_this_offer' => "You have already accepted or declined this offer ",
        'offer_approved_successfully' => "Offer Approved Successfully",
        'offer_rejected_successfully' => "Offer Rejected Successfully",
        'offer_ownership_err' => "you don't own this Offer",
        'offer_closed' => "Offer Closed Successfully",
        'offer_already_closed' => "Offer is Already Closed",
        'offer_already_approved' => "Offer is Already Approved",
        'offer_product_already_exist' => "Product name: Already exist in Active Products",

    ],
];
