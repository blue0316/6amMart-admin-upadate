<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
        <div class="navbar-vertical-container">
            <div class="navbar-brand-wrapper justify-content-between">
                <!-- Logo -->

                @php($store_data=\App\CentralLogics\Helpers::get_store_data())
                <a class="navbar-brand" href="{{route('vendor.dashboard')}}" aria-label="Front">
                    <img class="navbar-brand-logo initial--36" onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                         src="{{asset('storage/app/public/store/'.$store_data->logo)}}" alt="Logo">
                    <img class="navbar-brand-logo-mini initial--36" onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                         src="{{asset('storage/app/public/store/'.$store_data->logo)}}" alt="Logo">
                </a>
                <!-- End Logo -->

                <!-- Navbar Vertical Toggle -->
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                    <i class="tio-clear tio-lg"></i>
                </button>
                <!-- End Navbar Vertical Toggle -->

                <div class="navbar-nav-wrap-content-left">
                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                        data-placement="right" title="Collapse"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                        data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

            </div>

            <!-- Content -->
            <div class="navbar-vertical-content text-capitalize bg--005555" id="navbar-vertical-content">
                <form class="sidebar--search-form">
                    <div class="search--form-group">
                        <button type="button" class="btn"><i class="tio-search"></i></button>
                        <input type="text" class="form-control form--control" placeholder="{{ translate('messages.Search Menu...') }}" id="search-sidebar-menu">
                    </div>
                </form>
                <ul class="navbar-nav navbar-nav-lg nav-tabs">
                    <!-- Dashboards -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.dashboard')}}" title="{{translate('messages.dashboard')}}">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{translate('messages.dashboard')}}
                            </span>
                        </a>
                    </li>
                    <!-- End Dashboards -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('pos'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/pos')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link  " href="{{route('vendor.pos.index')}}"
                            title="{{translate('messages.pos')}}">
                            <i class="tio-shopping-basket-outlined nav-icon"></i>
                            <span
                                class="text-truncate">{{translate('messages.pos')}}</span>
                        </a>
                    </li>
                    @endif
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('order'))
                    <li class="nav-item">
                        <small class="nav-subtitle" title="{{translate('messages.order')}} {{translate('messages.section')}}">{{translate('messages.order')}} {{translate('messages.section')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <!-- Order -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/order*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                            title="{{translate('messages.orders')}}">
                            <i class="tio-shopping-cart nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{translate('messages.orders')}}
                            </span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('store-panel/order*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('store-panel/order/list/all')?'active':''}}">
                                <a class="nav-link" href="{{route('vendor.order.list',['all'])}}" title="{{translate('messages.all')}} {{translate('messages.orders')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        {{translate('messages.all')}}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{\App\Models\Order::where('store_id', \App\CentralLogics\Helpers::get_store_id())
                                                ->where(function($query){
                                                    return $query->whereNotIn('order_status',(config('order_confirmation_model') == 'store'|| \App\CentralLogics\Helpers::get_store_data()->self_delivery_system)?['failed','canceled', 'refund_requested', 'refunded']:['pending','failed','canceled', 'refund_requested', 'refunded'])
                                                    ->orWhere(function($query){
                                                        return $query->where('order_status','pending')->where('order_type', 'take_away');
                                                    });
                                            })->StoreOrder()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/order/list/pending')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.order.list',['pending'])}}" title="{{translate('messages.pending')}} {{translate('messages.orders')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        {{translate('messages.pending')}} {{(config('order_confirmation_model') == 'store' || \App\CentralLogics\Helpers::get_store_data()->self_delivery_system)?'':translate('messages.take_away')}}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                            @if(config('order_confirmation_model') == 'store' || \App\CentralLogics\Helpers::get_store_data()->self_delivery_system)
                                            {{\App\Models\Order::where(['order_status'=>'pending','store_id'=>\App\CentralLogics\Helpers::get_store_id()])->StoreOrder()->OrderScheduledIn(30)->count()}}
                                            @else
                                            {{\App\Models\Order::where(['order_status'=>'pending','store_id'=>\App\CentralLogics\Helpers::get_store_id(), 'order_type'=>'take_away'])->StoreOrder()->OrderScheduledIn(30)->count()}}
                                            @endif
                                        </span>
                                    </span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('store-panel/order/list/confirmed')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.order.list',['confirmed'])}}" title="{{translate('messages.confirmed_orders')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        {{translate('messages.confirmed')}}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                            {{\App\Models\Order::whereIn('order_status',['confirmed', 'accepted'])->StoreOrder()->whereNotNull('confirmed')->where('store_id', \App\CentralLogics\Helpers::get_store_id())->OrderScheduledIn(30)->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('store-panel/order/list/cooking')?'active':''}}">
                                <a class="nav-link" href="{{route('vendor.order.list',['cooking'])}}" title="{{translate('messages.processing_orders')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        {{translate('messages.cooking')}}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{\App\Models\Order::where(['order_status'=>'processing', 'store_id'=>\App\CentralLogics\Helpers::get_store_id()])->StoreOrder()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/order/list/ready_for_delivery')?'active':''}}">
                                <a class="nav-link" href="{{route('vendor.order.list',['ready_for_delivery'])}}" title="{{translate('messages.ready_for_delivery')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        {{translate('messages.ready_for_delivery')}}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{\App\Models\Order::where(['order_status'=>'handover', 'store_id'=>\App\CentralLogics\Helpers::get_store_id()])->StoreOrder()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/order/list/item_on_the_way')?'active':''}}">
                                <a class="nav-link" href="{{route('vendor.order.list',['item_on_the_way'])}}" title="{{translate('messages.items_on_the_way')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        {{translate('messages.item_on_the_way')}}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{\App\Models\Order::where(['order_status'=>'picked_up', 'store_id'=>\App\CentralLogics\Helpers::get_store_id()])->StoreOrder()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/order/list/delivered')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.order.list',['delivered'])}}" title="{{translate('messages.delivered_orders')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        {{translate('messages.delivered')}}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                            {{\App\Models\Order::where(['order_status'=>'delivered','store_id'=>\App\CentralLogics\Helpers::get_store_id()])->StoreOrder()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/order/list/refunded')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.order.list',['refunded'])}}" title="{{translate('messages.refunded_orders')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        {{translate('messages.refunded')}}
                                            <span class="badge badge-soft-danger bg-light badge-pill ml-1">
                                            {{\App\Models\Order::Refunded()->where(['store_id'=>\App\CentralLogics\Helpers::get_store_id()])->StoreOrder()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/order/list/scheduled')?'active':''}}">
                                <a class="nav-link" href="{{route('vendor.order.list',['scheduled'])}}" title="{{translate('messages.scheduled_orders')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        {{translate('messages.scheduled')}}
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{\App\Models\Order::where('store_id',\App\CentralLogics\Helpers::get_store_id())->StoreOrder()->Scheduled()->where(function($q){
                                                if(config('order_confirmation_model') == 'store' || \App\CentralLogics\Helpers::get_store_data()->self_delivery_system)
                                                {
                                                    $q->whereNotIn('order_status',['failed','canceled', 'refund_requested', 'refunded']);
                                                }
                                                else
                                                {
                                                    $q->whereNotIn('order_status',['pending','failed','canceled', 'refund_requested', 'refunded'])->orWhere(function($query){
                                                        $query->where('order_status','pending')->where('order_type', 'take_away');
                                                    });
                                                }

                                            })->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- End Order -->
                    @endif

                    <li class="nav-item">
                        <small
                            class="nav-subtitle">{{translate('messages.item')}} {{translate('messages.management')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <!-- AddOn -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('addon'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/addon*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.addon.add-new')}}" title="{{translate('messages.addons')}}"
                        >
                            <i class="tio-add-circle-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{translate('messages.addons')}}
                            </span>
                        </a>
                    </li>
                    @endif
                    <!-- End AddOn -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('item'))
                    <!-- Food -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/item*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('messages.items')}}"
                        >
                            <i class="tio-premium-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('messages.items')}}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('store-panel/item*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('store-panel/item/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.item.add-new')}}"
                                    title="{{translate('messages.add_new_item')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">{{translate('messages.add')}} {{translate('messages.new')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/item/list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.item.list')}}"
                                    title="{{translate('messages.items_list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('messages.list')}}</span>
                                </a>
                            </li>
                            @if ($store_data->module->module_type != 'food')

                            <li class="nav-item {{Request::is('store-panel/item/stock-limit-list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.item.stock-limit-list')}}" title="{{translate('messages.stock_limit_list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('messages.stock_limit_list')}}</span>
                                </a>
                            </li>
                            @endif
                            @if(\App\CentralLogics\Helpers::get_store_data()->item_section)
                            <li class="nav-item {{Request::is('store-panel/item/bulk-import')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.item.bulk-import')}}"
                                    title="{{translate('messages.bulk_import')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate text-capitalize">{{translate('messages.bulk_import')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/item/bulk-export')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.item.bulk-export-index')}}"
                                    title="{{translate('messages.bulk_export')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate text-capitalize">{{translate('messages.bulk_export')}}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    <!-- End Food -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/category*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="{{translate('messages.categories')}}"
                        >
                            <i class="tio-category nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('messages.categories')}}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('store-panel/category*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('store-panel/category/list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.category.add')}}"
                                    title="{{translate('messages.category')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('messages.category')}}</span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('store-panel/category/sub-category-list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.category.add-sub-category')}}"
                                    title="{{translate('messages.sub_category')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('messages.sub_category')}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    <!-- DeliveryMan -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('deliveryman'))
                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('messages.deliveryman')}} {{translate('messages.section')}}">{{translate('messages.deliveryman')}} {{translate('messages.section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/delivery-man/add')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('vendor.delivery-man.add')}}"
                               title="{{translate('messages.add_delivery_man')}}"
                            >
                                <i class="tio-running nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages.add_delivery_man')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/delivery-man/list')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('vendor.delivery-man.list')}}"
                               title="{{translate('messages.deliveryman')}}"
                            >
                                <i class="tio-filter-list nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages.deliverymen')}}
                                    {{translate('messages.list')}}
                                </span>
                            </a>
                        </li>

                        {{--<li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/delivery-man/reviews/list')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('vendor.delivery-man.reviews.list')}}" title="{{translate('messages.reviews')}}"
                            >
                                <i class="tio-star-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages.reviews')}}
                                </span>
                            </a>
                        </li>--}}
                    @endif
                <!-- End DeliveryMan -->

                    <li class="nav-item">
                        <small
                            class="nav-subtitle">{{translate('messages.marketing')}} {{translate('messages.section')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <!-- Campaign -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('campaign'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/campaign*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('messages.campaigns')}}">
                            <i class="tio-image nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('messages.campaigns')}}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"  style="display: {{Request::is('store-panel/campaign*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('store-panel/campaign/list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.campaign.list')}}" title="{{translate('messages.basic_campaigns')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('messages.basic_campaigns')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/campaign/item/list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.campaign.itemlist')}}" title="{{translate('messages.Item Campaigns')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('messages.Item Campaigns')}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    <!-- End Campaign -->

                    <!-- Business Section-->
                    <li class="nav-item">
                        <small class="nav-subtitle"
                                title="{{translate('messages.business')}} {{translate('messages.section')}}">{{translate('messages.business')}} {{translate('messages.section')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('store_setup'))
                    <li class="nav-item {{Request::is('store-panel/business-settings/store-setup')?'active':''}}">
                        <a class="nav-link " href="{{route('vendor.business-settings.store-setup')}}" title="{{translate('messages.store')}} {{translate('messages.config')}}"
                        >
                            <span class="tio-settings nav-icon"></span>
                            <span
                                class="text-truncate">{{translate('messages.store')}} {{translate('messages.config')}}</span>
                        </a>
                    </li>
                    @endif

                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('my_shop'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/store/*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.shop.view')}}"
                            title="{{translate('messages.my_shop')}}">
                            <i class="tio-home nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{translate('messages.my_shop')}}
                            </span>
                        </a>
                    </li>
                    @endif
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('bank_info'))
                    <!-- Business Settings -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/profile/bank-view')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.profile.bankView')}}"
                            title="{{translate('messages.bank_info')}}">
                            <i class="tio-shop nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{translate('messages.bank_info')}}
                            </span>
                        </a>
                    </li>
                    @endif


                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('wallet'))
                    <!-- StoreWallet -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/wallet')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('vendor.wallet.index')}}" title="{{translate('messages.my_wallet')}}"
                        >
                            <i class="tio-table nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('messages.my_wallet')}}</span>
                        </a>
                    </li>
                    @endif
                    <!-- End StoreWallet -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('reviews'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/reviews')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.reviews')}}" title="{{translate('messages.reviews')}}"
                        >
                            <i class="tio-star-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{translate('messages.reviews')}}
                            </span>
                        </a>
                    </li>
                    @endif
                    <!-- End Business Settings -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('chat'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/message*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.message.list')}}" title="{{translate('messages.chat')}}"
                        >
                            <i class="tio-chat nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{__('messages.Chat')}}
                            </span>
                        </a>
                    </li>
                    @endif

                    <!-- Employee-->
                    <li class="nav-item">
                        <small class="nav-subtitle" title="{{translate('messages.employee')}} {{translate('messages.section')}}">{{translate('messages.employee')}} {{translate('messages.section')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('custom_role'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/custom-role*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('vendor.custom-role.create')}}"
                        title="{{translate('messages.employee')}} {{translate('messages.Role')}}">
                            <i class="tio-incognito nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('messages.employee')}} {{translate('messages.Role')}}</span>
                        </a>
                    </li>
                    @endif

                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('employee'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('store-panel/employee*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                        title="{{translate('messages.employees')}}">
                            <i class="tio-user nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('messages.employees')}}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"  style="display: {{Request::is('store-panel/employee*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('store-panel/employee/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.employee.add-new')}}" title="{{translate('messages.add')}} {{translate('messages.new')}} {{translate('messages.Employee')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('messages.add')}} {{translate('messages.new')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('store-panel/employee/list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.employee.list')}}" title="{{translate('messages.Employee')}} {{translate('messages.list')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{translate('messages.list')}}</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                    @endif
                    <!-- End Employee -->

                    <li class="nav-item py-5">

                    </li>
                </ul>
            </div>
            <!-- End Content -->
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>

@push('script_2')
<script>
    $(window).on('load' , function() {
        if($(".navbar-vertical-content li.active").length) {
            $('.navbar-vertical-content').animate({
                scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
            }, 10);
        }
    });

    var $rows = $('#navbar-vertical-content li');
    $('#search-sidebar-menu').keyup(function() {
        var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

        $rows.show().filter(function() {
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
    });
</script>
@endpush
