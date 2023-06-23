<div id="sidebarMain" class="d-none">
    <aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-brand-wrapper justify-content-between">
                <!-- Logo -->
                @php($store_logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first()->value)
                <a class="navbar-brand" href="{{ route('admin.users.dashboard') }}" aria-label="Front">
                    <img class="navbar-brand-logo initial--36" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'" src="{{ asset('storage/app/public/business/' . $store_logo) }}" alt="Logo">
                    <img class="navbar-brand-logo-mini initial--36" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'" src="{{ asset('storage/app/public/business/' . $store_logo) }}" alt="Logo">
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
            <div class="navbar-vertical-content bg--005555" id="navbar-vertical-content">
                <form class="sidebar--search-form">
                    <div class="search--form-group">
                        <button type="button" class="btn"><i class="tio-search"></i></button>
                        <input type="text" class="form-control form--control" placeholder="{{ translate('Search Menu...') }}" id="search-sidebar-menu">
                    </div>
                </form>
                <ul class="navbar-nav navbar-nav-lg nav-tabs">
                    <!-- Dashboards -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users') ? 'show active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.users.dashboard') }}" title="{{ translate('messages.dashboard') }}">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{ translate('User Overview') }}
                            </span>
                        </a>
                    </li>
                    <!-- End Dashboards -->
                <!-- DeliveryMan -->
                @if (\App\CentralLogics\Helpers::module_permission_check('deliveryman'))
                <li class="nav-item">
                    <small class="nav-subtitle" title="{{ translate('messages.deliveryman') }} {{ translate('messages.section') }}">{{ translate('messages.deliveryman') }}
                        {{ translate('messages.management') }}</small>
                    <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                </li>
                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/delivery-man/add') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.users.delivery-man.add') }}" title="{{ translate('messages.add_delivery_man') }}">
                        <i class="tio-running nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                            {{ translate('messages.add_delivery_man') }}
                        </span>
                    </a>
                </li>

                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/delivery-man/new') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.users.delivery-man.new') }}" title="{{ translate('messages.new_delivery_man') }}">
                        <i class="tio-man nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                            {{ translate('messages.new_delivery_man') }}
                        </span>
                    </a>
                </li>


                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/delivery-man/list') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.users.delivery-man.list') }}" title="{{ translate('messages.deliveryman') }} {{ translate('messages.list') }}">
                        <i class="tio-filter-list nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                            {{ translate('messages.deliveryman_list') }}
                        </span>
                    </a>
                </li>

                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/delivery-man/reviews/list') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.users.delivery-man.reviews.list') }}" title="{{ translate('messages.reviews') }}">
                        <i class="tio-star-outlined nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                            {{ translate('messages.reviews') }}
                        </span>
                    </a>
                </li>
                @endif
                <!-- End DeliveryMan -->

                <!-- Customer Section -->
                @if (\App\CentralLogics\Helpers::module_permission_check('customerList'))
                <li class="nav-item">
                    <small class="nav-subtitle" title="{{ translate('messages.customer') }} {{ translate('messages.section') }}">{{ translate('messages.customer') }}
                        {{ translate('messages.management') }}</small>
                    <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                </li>
                <!-- Custommer -->

                <li class="navbar-vertical-aside-has-menu {{ (Request::is('admin/users/customer/list') || Request::is('admin/users/customer/view*')) ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.users.customer.list') }}" title="{{ translate('messages.customers') }}">
                        <i class="tio-poi-user nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                            {{ translate('messages.customers') }}
                        </span>
                    </a>
                </li>

                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/customer/wallet*') ? 'active' : '' }}">

                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{ __('messages.customer_wallet') }}">
                        <i class="tio-wallet nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate  text-capitalize">
                            {{ __('messages.customer_wallet') }}
                        </span>
                    </a>

                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display:{{ Request::is('admin/users/customer/wallet*') ? 'block' : 'none' }}">
                        <li class="nav-item {{ Request::is('admin/users/customer/wallet/add-fund') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.users.customer.wallet.add-fund') }}" title="{{ __('messages.add_fund') }}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate text-capitalize">{{ __('messages.add_fund') }}</span>
                            </a>
                        </li>

                        <li class="nav-item {{ Request::is('admin/users/customer/wallet/report*') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.users.customer.wallet.report') }}" title="{{ __('messages.report') }}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate text-capitalize">{{ __('messages.report') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/customer/loyalty-point*') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link  nav-link-toggle" href="javascript:" title="{{ __('messages.customer_loyalty_point') }}">
                        <i class="tio-medal nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate  text-capitalize">
                            {{ __('messages.customer_loyalty_point') }}
                        </span>
                    </a>

                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display:{{ Request::is('admin/users/customer/loyalty-point*') ? 'block' : 'none' }}">
                        <li class="nav-item {{ Request::is('admin/users/customer/loyalty-point/report*') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.users.customer.loyalty-point.report') }}" title="{{ __('messages.report') }}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate text-capitalize">{{ __('messages.report') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- End Custommer -->
                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/customer/subscribed') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.users.customer.subscribed') }}" title="{{translate('subscribed_emails')}}">
                        <i class="tio-email-outlined nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                            {{ translate('messages.subscribed_mail_list') }}
                        </span>
                    </a>
                </li>
                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/contact/contact-list') ? 'active' : '' }}">
                    <a class="nav-link " href="{{ route('admin.users.contact.contact-list') }}" title="{{ translate('messages.contact_messages') }}">
                        <span class="tio-message nav-icon"></span>
                        <span class="text-truncate">{{ translate('messages.contact_messages') }}</span>
                    </a>
                </li>
                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/customer/settings') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.users.customer.settings') }}" title="{{ __('messages.Customer') }} {{ __('messages.settings') }}">
                        <i class="tio-settings nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                            {{ __('messages.Customer') }} {{ __('messages.settings') }}
                        </span>
                    </a>
                </li>
                {{-- <li
                class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/message/list') ? 'active' : '' }}">
                <a class="js-navbar-vertical-aside-menu-link nav-link"
                    href="{{ route('admin.users.message.list') }}"
                    title="{{ __('messages.customer_chat') }}">
                    <i class="tio-chat nav-icon"></i>
                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                        {{ __('messages.Customer') }} {{ translate('Chat') }}
                    </span>
                </a>
            </li> --}}
                @endif
                <!-- End customer Section -->

                

                <!-- Employee-->

                <li class="nav-item">
                    <small class="nav-subtitle" title="{{ translate('messages.employee_handle') }}">{{ translate('messages.employee') }}
                        {{ translate('management') }}</small>
                    <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                </li>

                @if (\App\CentralLogics\Helpers::module_permission_check('custom_role'))
                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/custom-role*') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.users.custom-role.create') }}" title="{{ translate('messages.employee') }} {{ translate('messages.Role') }}">
                        <i class="tio-incognito nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.employee') }}
                            {{ translate('messages.Role') }}</span>
                    </a>
                </li>
                @endif

                @if (\App\CentralLogics\Helpers::module_permission_check('employee'))
                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/users/employee*') ? 'active' : '' }}">
                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{ translate('messages.Employee') }}">
                        <i class="tio-user nav-icon"></i>
                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.employees') }}</span>
                    </a>
                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub"  style="display:{{ Request::is('admin/users/employee*') ? 'block' : 'none' }}">
                        <li class="nav-item {{ Request::is('admin/users/employee/add-new') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.users.employee.add-new') }}" title="{{ translate('messages.add') }} {{ translate('messages.new') }} {{ translate('messages.Employee') }}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">{{ translate('messages.add') }}
                                    {{ translate('messages.new') }}</span>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::is('admin/users/employee/list') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.users.employee.list') }}" title="{{ translate('messages.Employee') }} {{ translate('messages.list') }}">
                                <span class="tio-circle nav-indicator-icon"></span>
                                <span class="text-truncate">{{ translate('messages.list') }}</span>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif
                <!-- End Employee -->


                <li class="nav-item py-5">

                </li>


                <li class="__sidebar-hs-unfold px-2">
                    <div class="hs-unfold w-100">
                        <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper" href="javascript:;"
                            data-hs-unfold-options='{
                                    "target": "#accountNavbarDropdown",
                                    "type": "css-animation"
                                }'>
                            <div class="cmn--media right-dropdown-icon d-flex align-items-center">
                                <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img"
                                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                        src="{{asset('storage/app/public/admin')}}/{{auth('admin')->user()->image}}"
                                        alt="Image Description">
                                    <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                </div>
                                <div class="media-body pl-3">
                                    <span class="card-title h5">
                                        {{auth('admin')->user()->f_name}}
                                        {{auth('admin')->user()->l_name}}
                                    </span>
                                    <span class="card-text">{{auth('admin')->user()->email}}</span>
                                </div>
                            </div>
                        </a>

                        <div id="accountNavbarDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account min--240">
                            <div class="dropdown-item-text">
                                <div class="media align-items-center">
                                    <div class="avatar avatar-sm avatar-circle mr-2">
                                        <img class="avatar-img"
                                                onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                                src="{{asset('storage/app/public/admin')}}/{{auth('admin')->user()->image}}"
                                                alt="Image Description">
                                    </div>
                                    <div class="media-body">
                                        <span class="card-title h5">{{auth('admin')->user()->f_name}}</span>
                                        <span class="card-text">{{auth('admin')->user()->email}}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="{{route('admin.settings')}}">
                                <span class="text-truncate pr-2" title="Settings">{{translate('messages.settings')}}</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="javascript:" onclick="Swal.fire({
                                title: '{{translate("logout_warning_message")}}',
                                showDenyButton: true,
                                showCancelButton: true,
                                confirmButtonColor: '#FC6A57',
                                cancelButtonColor: '#363636',
                                confirmButtonText: `Yes`,
                                denyButtonText: `Don't Logout`,
                                }).then((result) => {
                                if (result.value) {
                                location.href='{{route('admin.auth.logout')}}';
                                } else{
                                Swal.fire('{{ translate('messages.canceled') }}', '', 'info')
                                }
                                })">
                                <span class="text-truncate pr-2" title="Sign out">{{translate('messages.sign_out')}}</span>
                            </a>
                        </div>
                    </div>
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
