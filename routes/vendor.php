<?php

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'Vendor', 'as' => 'vendor.'], function () {
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit');
        Route::get('logout', 'LoginController@logout')->name('logout');
        Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
            Route::post('login', 'EmployeeLoginController@submit')->name('login');
            Route::get('logout', 'EmployeeLoginController@logout')->name('logout');
        });
    });
    /*authentication*/

    Route::group(['middleware' => ['vendor']], function () {
        Route::get('/', 'DashboardController@dashboard')->name('dashboard');
        Route::get('/get-store-data', 'DashboardController@store_data')->name('get-store-data');
        Route::post('/store-token', 'DashboardController@updateDeviceToken')->name('store.token');
        Route::get('/reviews', 'ReviewController@index')->name('reviews')->middleware('module:reviews');


        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
            Route::post('variant_price', 'POSController@variant_price')->name('variant_price');
            Route::group(['middleware' => ['module:pos']], function () {
                Route::get('/', 'POSController@index')->name('index');
                Route::get('quick-view', 'POSController@quick_view')->name('quick-view');
                Route::get('quick-view-cart-item', 'POSController@quick_view_card_item')->name('quick-view-cart-item');
                Route::post('add-to-cart', 'POSController@addToCart')->name('add-to-cart');
                Route::post('add-delivery-info', 'POSController@addDeliveryInfo')->name('add-delivery-info');
                Route::post('remove-from-cart', 'POSController@removeFromCart')->name('remove-from-cart');
                Route::post('cart-items', 'POSController@cart_items')->name('cart_items');
                Route::post('update-quantity', 'POSController@updateQuantity')->name('updateQuantity');
                Route::post('empty-cart', 'POSController@emptyCart')->name('emptyCart');
                Route::post('tax', 'POSController@update_tax')->name('tax');
                Route::post('paid', 'POSController@update_paid')->name('paid');
                Route::post('discount', 'POSController@update_discount')->name('discount');
                Route::get('customers', 'POSController@get_customers')->name('customers');
                Route::post('order', 'POSController@place_order')->name('order');
                Route::get('orders', 'POSController@order_list')->name('orders');
                Route::post('search', 'POSController@search')->name('search');
                Route::get('order-details/{id}', 'POSController@order_details')->name('order-details');
                Route::get('invoice/{id}', 'POSController@generate_invoice');
                Route::post('customer-store', 'POSController@customer_store')->name('customer-store');
            });
        });

        Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
            Route::post('order-stats', 'DashboardController@order_stats')->name('order-stats');
        });

        Route::group(['prefix' => 'category', 'as' => 'category.', 'middleware' => ['module:item']], function () {
            Route::get('get-all', 'CategoryController@get_all')->name('get-all');
            Route::get('list', 'CategoryController@index')->name('add');
            Route::get('sub-category-list', 'CategoryController@sub_index')->name('add-sub-category');
            Route::post('search', 'CategoryController@search')->name('search');
            Route::post('sub-search', 'CategoryController@sub_search')->name('sub-search');
            Route::get('export-categories/{type}', 'CategoryController@export_categories')->name('export-categories');
            Route::get('export-sub-categories/{type}', 'CategoryController@export_sub_categories')->name('export-sub-categories');
        });

        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:custom_role']], function () {
            Route::get('create', 'CustomRoleController@create')->name('create');
            Route::post('create', 'CustomRoleController@store')->name('store');
            Route::get('edit/{id}', 'CustomRoleController@edit')->name('edit');
            Route::post('update/{id}', 'CustomRoleController@update')->name('update');
            Route::delete('delete/{id}', 'CustomRoleController@distroy')->name('delete');
            Route::post('search', 'CustomRoleController@search')->name('search');
        });

        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.', 'middleware' => ['module:deliveryman']], function () {
            Route::get('add', 'DeliveryManController@index')->name('add');
            Route::post('store', 'DeliveryManController@store')->name('store');
            Route::get('list', 'DeliveryManController@list')->name('list');
            Route::get('preview/{id}/{tab?}', 'DeliveryManController@preview')->name('preview');
            Route::get('status/{id}/{status}', 'DeliveryManController@status')->name('status');
            Route::get('earning/{id}/{status}', 'DeliveryManController@earning')->name('earning');
            Route::get('edit/{id}', 'DeliveryManController@edit')->name('edit');
            Route::post('update/{id}', 'DeliveryManController@update')->name('update');
            Route::delete('delete/{id}', 'DeliveryManController@delete')->name('delete');
            Route::post('search', 'DeliveryManController@search')->name('search');
            Route::get('get-deliverymen', 'DeliveryManController@get_deliverymen')->name('get-deliverymen');
            Route::post('transation/search', 'DeliveryManController@transaction_search')->name('transaction-search');

            Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
                Route::get('list', 'DeliveryManController@reviews_list')->name('list');
            });
        });

        Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['module:employee']], function () {
            Route::get('add-new', 'EmployeeController@add_new')->name('add-new');
            Route::post('add-new', 'EmployeeController@store');
            Route::get('list', 'EmployeeController@list')->name('list');
            Route::get('edit/{id}', 'EmployeeController@edit')->name('edit');
            Route::post('update/{id}', 'EmployeeController@update')->name('update');
            Route::delete('delete/{id}', 'EmployeeController@distroy')->name('delete');
            Route::post('search', 'EmployeeController@search')->name('search');
            Route::get('list-export', 'EmployeeController@list_export')->name('export-employee');
        });

        Route::group(['prefix' => 'item', 'as' => 'item.', 'middleware' => ['module:item']], function () {
            Route::get('add-new', 'ItemController@index')->name('add-new');
            Route::post('variant-combination', 'ItemController@variant_combination')->name('variant-combination');
            Route::post('store', 'ItemController@store')->name('store');
            Route::get('edit/{id}', 'ItemController@edit')->name('edit');
            Route::post('update/{id}', 'ItemController@update')->name('update');
            Route::get('list', 'ItemController@list')->name('list');
            Route::delete('delete/{id}', 'ItemController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'ItemController@status')->name('status');
            Route::post('search', 'ItemController@search')->name('search');
            Route::get('view/{id}', 'ItemController@view')->name('view');
            Route::get('remove-image', 'ItemController@remove_image')->name('remove-image');
            Route::get('get-categories', 'ItemController@get_categories')->name('get-categories');

            //Mainul
            Route::get('get-variations', 'ItemController@get_variations')->name('get-variations');
            Route::get('stock-limit-list', 'ItemController@stock_limit_list')->name('stock-limit-list');
            Route::post('stock-update', 'ItemController@stock_update')->name('stock-update');

            //Import and export
            Route::get('bulk-import', 'ItemController@bulk_import_index')->name('bulk-import');
            Route::post('bulk-import', 'ItemController@bulk_import_data');
            Route::get('bulk-export', 'ItemController@bulk_export_index')->name('bulk-export-index');
            Route::post('bulk-export', 'ItemController@bulk_export_data')->name('bulk-export');
        });

        Route::group(['prefix' => 'banner', 'as' => 'banner.', 'middleware' => ['module:banner']], function () {
            Route::get('list', 'BannerController@list')->name('list');
            Route::get('join_campaign/{id}/{status}', 'BannerController@status')->name('status');
        });

        Route::group(['prefix' => 'campaign', 'as' => 'campaign.', 'middleware' => ['module:campaign']], function () {
            Route::get('list', 'CampaignController@list')->name('list');
            Route::get('item/list', 'CampaignController@itemlist')->name('itemlist');
            Route::get('remove-store/{campaign}/{store}', 'CampaignController@remove_store')->name('remove-store');
            Route::get('add-store/{campaign}/{store}', 'CampaignController@addstore')->name('add-store');
            Route::post('search', 'CampaignController@search')->name('search');
            Route::post('search-item', 'CampaignController@searchItem')->name('searchItem');
        });

        Route::group(['prefix' => 'wallet', 'as' => 'wallet.', 'middleware' => ['module:wallet']], function () {
            Route::get('/', 'WalletController@index')->name('index');
            Route::post('request', 'WalletController@w_request')->name('withdraw-request');
            Route::delete('close/{id}', 'WalletController@close_request')->name('close-request');
        });


        // Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['module:coupon']], function () {
        //     Route::get('add-new', 'CouponController@add_new')->name('add-new');
        //     Route::post('store', 'CouponController@store')->name('store');
        //     Route::get('update/{id}', 'CouponController@edit')->name('update');
        //     Route::post('update/{id}', 'CouponController@update');
        //     Route::get('status/{id}/{status}', 'CouponController@status')->name('status');
        //     Route::delete('delete/{id}', 'CouponController@delete')->name('delete');
        // });

        Route::group(['prefix' => 'addon', 'as' => 'addon.', 'middleware' => ['module:addon']], function () {
            Route::get('add-new', 'AddOnController@index')->name('add-new');
            Route::post('store', 'AddOnController@store')->name('store');
            Route::get('edit/{id}', 'AddOnController@edit')->name('edit');
            Route::post('update/{id}', 'AddOnController@update')->name('update');
            Route::delete('delete/{id}', 'AddOnController@delete')->name('delete');
        });

        Route::group(['prefix' => 'order', 'as' => 'order.' , 'middleware' => ['module:order']], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::put('status-update/{id}', 'OrderController@status')->name('status-update');
            Route::post('search', 'OrderController@search')->name('search');
            Route::post('add-to-cart', 'OrderController@add_to_cart')->name('add-to-cart');
            Route::post('remove-from-cart', 'OrderController@remove_from_cart')->name('remove-from-cart');
            Route::get('update/{order}', 'OrderController@update')->name('update');
            Route::get('edit-order/{order}', 'OrderController@edit')->name('edit');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::get('status', 'OrderController@status')->name('status');
            Route::get('quick-view', 'OrderController@quick_view')->name('quick-view');
            Route::get('quick-view-cart-item', 'OrderController@quick_view_cart_item')->name('quick-view-cart-item');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', 'OrderController@add_payment_ref_code')->name('add-payment-ref-code');
            Route::post('update-order-amount', 'OrderController@edit_order_amount')->name('update-order-amount');
            Route::post('update-discount-amount', 'OrderController@edit_discount_amount')->name('update-discount-amount');
        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.', 'middleware' => ['module:store_setup']], function () {
            Route::get('store-setup', 'BusinessSettingsController@store_index')->name('store-setup');
            Route::post('add-schedule', 'BusinessSettingsController@add_schedule')->name('add-schedule');
            Route::get('remove-schedule/{store_schedule}', 'BusinessSettingsController@remove_schedule')->name('remove-schedule');
            Route::get('update-active-status', 'BusinessSettingsController@active_status')->name('update-active-status');
            Route::post('update-setup/{store}', 'BusinessSettingsController@store_setup')->name('update-setup');
            Route::get('toggle-settings-status/{store}/{status}/{menu}', 'BusinessSettingsController@store_status')->name('toggle-settings');
        });

        Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['module:bank_info']], function () {
            Route::get('view', 'ProfileController@view')->name('view');
            // Route::get('update', 'ProfileController@edit')->name('update');
            Route::post('update', 'ProfileController@update')->name('update');
            Route::post('settings-password', 'ProfileController@settings_password_update')->name('settings-password');
            Route::get('bank-view', 'ProfileController@bank_view')->name('bankView');
            Route::get('bank-edit', 'ProfileController@bank_edit')->name('bankInfo');
            Route::post('bank-update', 'ProfileController@bank_update')->name('bank_update');
        });

        Route::group(['prefix' => 'store', 'as' => 'shop.', 'middleware' => ['module:my_shop']], function () {
            Route::get('view', 'RestaurantController@view')->name('view');
            Route::get('edit', 'RestaurantController@edit')->name('edit');
            Route::post('update', 'RestaurantController@update')->name('update');
        });

        Route::group(['prefix' => 'message', 'as' => 'message.'], function () {
            Route::get('list', 'ConversationController@list')->name('list');
            Route::post('store/{user_id}/{user_type}', 'ConversationController@store')->name('store');
            Route::get('view/{conversation_id}/{user_id}', 'ConversationController@view')->name('view');
        });

    });
});
