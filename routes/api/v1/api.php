<?php

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

Route::group(['namespace' => 'Api\V1', 'middleware'=>'localization'], function () {
    Route::get('zone/list', 'ZoneController@get_zones');
    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
        Route::post('sign-up', 'CustomerAuthController@register');
        Route::post('login', 'CustomerAuthController@login');
        Route::post('verify-phone', 'CustomerAuthController@verify_phone');

        Route::post('check-email', 'CustomerAuthController@check_email');
        Route::post('verify-email', 'CustomerAuthController@verify_email');

        Route::post('forgot-password', 'PasswordResetController@reset_password_request');
        Route::post('verify-token', 'PasswordResetController@verify_token');
        Route::put('reset-password', 'PasswordResetController@reset_password_submit');

        Route::group(['prefix' => 'delivery-man'], function () {
            Route::post('login', 'DeliveryManLoginController@login');
            Route::post('store', 'DeliveryManLoginController@store');

            Route::post('forgot-password', 'DMPasswordResetController@reset_password_request');
            Route::post('verify-token', 'DMPasswordResetController@verify_token');
            Route::put('reset-password', 'DMPasswordResetController@reset_password_submit');
        });
        Route::group(['prefix' => 'vendor'], function () {
            Route::post('login', 'VendorLoginController@login');
            Route::post('forgot-password', 'VendorPasswordResetController@reset_password_request');
            Route::post('verify-token', 'VendorPasswordResetController@verify_token');
            Route::put('reset-password', 'VendorPasswordResetController@reset_password_submit');
            Route::post('register','VendorLoginController@register');
        });

        Route::post('social-login', 'SocialAuthController@social_login');
        Route::post('social-register', 'SocialAuthController@social_register');
    });

    // Module
    Route::get('module', 'ModuleController@index');

    Route::post('newsletter/subscribe','NewsletterController@index');

    Route::group(['prefix' => 'delivery-man'], function () {
        Route::get('last-location', 'DeliverymanController@get_last_location');


        Route::group(['prefix' => 'reviews','middleware'=>['auth:api']], function () {
            Route::get('/{delivery_man_id}', 'DeliveryManReviewController@get_reviews');
            Route::get('rating/{delivery_man_id}', 'DeliveryManReviewController@get_rating');
            Route::post('/submit', 'DeliveryManReviewController@submit_review');
        });
        Route::group(['middleware'=>['dm.api']], function () {
            Route::get('profile', 'DeliverymanController@get_profile');
            Route::get('notifications', 'DeliverymanController@get_notifications');
            Route::put('update-profile', 'DeliverymanController@update_profile');
            Route::post('update-active-status', 'DeliverymanController@activeStatus');
            Route::get('current-orders', 'DeliverymanController@get_current_orders');
            Route::get('latest-orders', 'DeliverymanController@get_latest_orders');
            Route::post('record-location-data', 'DeliverymanController@record_location_data');
            Route::get('all-orders', 'DeliverymanController@get_all_orders');
            Route::get('order-delivery-history', 'DeliverymanController@get_order_history');
            Route::put('accept-order', 'DeliverymanController@accept_order');
            Route::put('update-order-status', 'DeliverymanController@update_order_status');
            Route::put('update-payment-status', 'DeliverymanController@order_payment_status_update');
            Route::get('order-details', 'DeliverymanController@get_order_details');
            Route::get('order', 'DeliverymanController@get_order');
            Route::put('update-fcm-token', 'DeliverymanController@update_fcm_token');
            //Remove account
            Route::delete('remove-account', 'DeliverymanController@remove_account');

            // Chatting
            Route::group(['prefix' => 'message'], function () {
                Route::get('list', 'ConversationController@dm_conversations');
                Route::get('search-list', 'ConversationController@dm_search_conversations');
                Route::get('details', 'ConversationController@dm_messages');
                Route::post('send', 'ConversationController@dm_messages_store');
            });
        });
    });

    Route::group(['prefix' => 'vendor', 'namespace' => 'Vendor', 'middleware'=>['vendor.api']], function () {
        Route::get('notifications', 'VendorController@get_notifications');
        Route::get('profile', 'VendorController@get_profile');
        Route::post('update-active-status', 'VendorController@active_status');
        Route::get('earning-info', 'VendorController@get_earning_data');
        Route::put('update-profile', 'VendorController@update_profile');
        Route::get('current-orders', 'VendorController@get_current_orders');
        Route::get('completed-orders', 'VendorController@get_completed_orders');
        Route::get('all-orders', 'VendorController@get_all_orders');
        Route::put('update-order-status', 'VendorController@update_order_status');
        Route::put('update-order-amount', 'VendorController@edit_order_amount');
        Route::get('order-details', 'VendorController@get_order_details');
        Route::get('order', 'VendorController@get_order');
        Route::put('update-fcm-token', 'VendorController@update_fcm_token');
        Route::get('get-basic-campaigns', 'VendorController@get_basic_campaigns');
        Route::put('campaign-leave', 'VendorController@remove_store');
        Route::put('campaign-join', 'VendorController@addstore');
        Route::get('get-withdraw-list', 'VendorController@withdraw_list');
        Route::get('get-items-list', 'VendorController@get_items');
        Route::put('update-bank-info', 'VendorController@update_bank_info');
        Route::post('request-withdraw', 'VendorController@request_withdraw');

        //remove account
        Route::delete('remove-account', 'VendorController@remove_account');

        Route::get('unit','UnitController@index');
        // Business setup
        Route::put('update-business-setup', 'BusinessSettingsController@update_store_setup');

        // Reataurant schedule
        Route::post('schedule/store', 'BusinessSettingsController@add_schedule');
        Route::delete('schedule/{store_schedule}', 'BusinessSettingsController@remove_schedule');

        // Attributes
        Route::get('attributes', 'AttributeController@list');

        // Addon
        Route::group(['prefix'=>'addon'], function(){
            Route::get('/', 'AddOnController@list');
            Route::post('store', 'AddOnController@store');
            Route::put('update', 'AddOnController@update');
            Route::get('status', 'AddOnController@status');
            Route::delete('delete', 'AddOnController@delete');
        });
        //category
        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', 'CategoryController@get_categories');
            Route::get('childes/{category_id}', 'CategoryController@get_childes');
        });

        Route::group(['prefix' => 'delivery-man'], function () {
            Route::post('store', 'DeliveryManController@store');
            Route::get('list', 'DeliveryManController@list');
            Route::get('preview', 'DeliveryManController@preview');
            Route::get('status', 'DeliveryManController@status');
            Route::post('update/{id}', 'DeliveryManController@update');
            Route::delete('delete', 'DeliveryManController@delete');
            Route::post('search', 'DeliveryManController@search');
        });
        // Food
        Route::group(['prefix'=>'item'], function(){
            Route::post('store', 'ItemController@store');
            Route::put('update', 'ItemController@update');
            Route::delete('delete', 'ItemController@delete');
            Route::get('status', 'ItemController@status');
            Route::POST('search', 'ItemController@search');
            Route::get('reviews', 'ItemController@reviews');

        });

        // POS
        Route::group(['prefix'=>'pos'], function(){
            Route::get('orders', 'POSController@order_list');
            Route::post('place-order', 'POSController@place_order');
            Route::get('customers', 'POSController@get_customers');
        });

        // Chatting
        Route::group(['prefix' => 'message'], function () {
            Route::get('list', 'ConversationController@conversations');
            Route::get('search-list', 'ConversationController@search_conversations');
            Route::get('details', 'ConversationController@messages');
            Route::post('send', 'ConversationController@messages_store');
        });
    });

    Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@configuration');
        Route::get('/get-zone-id', 'ConfigController@get_zone');
        Route::get('place-api-autocomplete', 'ConfigController@place_api_autocomplete');
        Route::get('distance-api', 'ConfigController@distance_api');
        Route::get('place-api-details', 'ConfigController@place_api_details');
        Route::get('geocode-api', 'ConfigController@geocode_api');
    });

    Route::group(['middleware'=>['module-check']], function(){
        Route::group(['prefix' => 'customer', 'middleware' => 'auth:api'], function () {
            Route::get('notifications', 'NotificationController@get_notifications');
            Route::get('info', 'CustomerController@info');
            Route::get('update-zone', 'CustomerController@update_zone');
            Route::post('update-profile', 'CustomerController@update_profile');
            Route::post('update-interest', 'CustomerController@update_interest');
            Route::put('cm-firebase-token', 'CustomerController@update_cm_firebase_token');
            Route::get('suggested-items', 'CustomerController@get_suggested_item');
            //Remove account
            Route::delete('remove-account', 'CustomerController@remove_account');

            Route::group(['prefix' => 'address'], function () {
                Route::get('list', 'CustomerController@address_list');
                Route::post('add', 'CustomerController@add_new_address');
                Route::put('update/{id}', 'CustomerController@update_address');
                Route::delete('delete', 'CustomerController@delete_address');
            });

            Route::group(['prefix' => 'order'], function () {
                Route::get('list', 'OrderController@get_order_list');
                Route::get('running-orders', 'OrderController@get_running_orders');
                Route::get('details', 'OrderController@get_order_details');
                Route::post('place', 'OrderController@place_order');
                Route::post('prescription/place', 'OrderController@prescription_place_order');
                Route::put('cancel', 'OrderController@cancel_order');
                Route::post('refund-request', 'OrderController@refund_request');
                Route::get('refund-reasons', 'OrderController@refund_reasons');
                Route::get('track', 'OrderController@track_order');
                Route::put('payment-method', 'OrderController@update_payment_method');
            });


            // Chatting
            Route::group(['prefix' => 'message'], function () {
                Route::get('list', 'ConversationController@conversations');
                Route::get('search-list', 'ConversationController@search_conversations');
                Route::get('details', 'ConversationController@messages');
                Route::post('send', 'ConversationController@messages_store');
            });

            Route::group(['prefix' => 'wish-list'], function () {
                Route::get('/', 'WishlistController@wish_list');
                Route::post('add', 'WishlistController@add_to_wishlist');
                Route::delete('remove', 'WishlistController@remove_from_wishlist');
            });

            //Loyalty
            Route::group(['prefix'=>'loyalty-point'], function() {
                Route::post('point-transfer', 'LoyaltyPointController@point_transfer');
                Route::get('transactions', 'LoyaltyPointController@transactions');
            });

            Route::group(['prefix'=>'wallet'], function() {
                Route::get('transactions', 'WalletController@transactions');
            });

        });

        Route::group(['prefix' => 'items'], function () {
            Route::get('latest', 'ItemController@get_latest_products');
            Route::get('popular', 'ItemController@get_popular_products');
            Route::get('most-reviewed', 'ItemController@get_most_reviewed_products');
            Route::get('set-menu', 'ItemController@get_set_menus');
            Route::get('search', 'ItemController@get_searched_products');
            Route::get('details/{id}', 'ItemController@get_product');
            Route::get('related-items/{item_id}', 'ItemController@get_related_products');
            Route::get('reviews/{item_id}', 'ItemController@get_product_reviews');
            Route::get('rating/{item_id}', 'ItemController@get_product_rating');
            Route::post('reviews/submit', 'ItemController@submit_product_review')->middleware('auth:api');
        });

        Route::group(['prefix' => 'stores'], function () {
            Route::get('get-stores/{filter_data}', 'StoreController@get_stores');
            Route::get('latest', 'StoreController@get_latest_stores');
            Route::get('popular', 'StoreController@get_popular_stores');
            Route::get('details/{id}', 'StoreController@get_details');
            Route::get('reviews', 'StoreController@reviews');
            Route::get('search', 'StoreController@get_searched_stores');
        });

        Route::group(['prefix' => 'banners'], function () {
            Route::get('/', 'BannerController@get_banners');
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', 'CategoryController@get_categories');
            Route::get('childes/{category_id}', 'CategoryController@get_childes');
            Route::get('items/{category_id}', 'CategoryController@get_products');
            Route::get('items/{category_id}/all', 'CategoryController@get_all_products');
            Route::get('stores/{category_id}', 'CategoryController@get_stores');
        });

        Route::group(['prefix' => 'campaigns'], function () {
            Route::get('basic', 'CampaignController@get_basic_campaigns');
            Route::get('basic-campaign-details', 'CampaignController@basic_campaign_details');
            Route::get('item', 'CampaignController@get_item_campaigns');
        });

        Route::group(['prefix' => 'coupon', 'middleware' => 'auth:api'], function () {
            Route::get('list', 'CouponController@list');
            Route::get('apply', 'CouponController@apply');
        });

        Route::get('parcel-category','ParcelCategoryController@index');
    });
});
