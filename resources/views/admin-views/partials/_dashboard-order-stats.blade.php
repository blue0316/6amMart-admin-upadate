<div class="col-sm-6 col-lg-3">
    <div class="__dashboard-card-2">
        <img src="{{asset('/public/assets/admin/img/dashboard/grocery/items.svg')}}" alt="dashboard/grocery">
        <h6 class="name">Items</h6>
        <h3 class="count">{{ $data['total_items'] }}</h3>
        <div class="subtxt">{{ $data['new_items'] }} {{ translate('newly added') }}</div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="__dashboard-card-2">
        <img src="{{asset('/public/assets/admin/img/dashboard/grocery/orders.svg')}}" alt="dashboard/grocery">
        <h6 class="name">Orders</h6>
        <h3 class="count">{{ $data['total_orders'] }}</h3>
        <div class="subtxt">{{ $data['new_orders'] }} {{ translate('newly added') }}</div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="__dashboard-card-2">
        <img src="{{asset('/public/assets/admin/img/dashboard/grocery/stores.svg')}}" alt="dashboard/grocery">
        <h6 class="name">Stores</h6>
        <h3 class="count">{{ $data['total_stores'] }}</h3>
        <div class="subtxt">{{ $data['new_stores'] }} {{ translate('newly added') }}</div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="__dashboard-card-2">
        <img src="{{asset('/public/assets/admin/img/dashboard/grocery/customers.svg')}}" alt="dashboard/grocery">
        <h6 class="name">Customers</h6>
        <h3 class="count">{{ $data['total_customers'] }}</h3>
        <div class="subtxt">{{ $data['new_customers'] }} {{ translate('newly added') }}</div>
    </div>
</div>
<div class="col-12">
    <div class="row g-2">
        <div class="col-sm-6 col-lg-3">
            <a class="order--card h-100" href="{{route('admin.order.list',['searching_for_deliverymen'])}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('/public/assets/admin/img/order-status/unassigned.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>{{translate('messages.unassigned_orders')}}</span>
                    </h6>
                    <span class="card-title text-3F8CE8">
                        {{$data['searching_for_dm']}}
                    </span>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a class="order--card h-100" href="{{route('admin.order.list',['accepted'])}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('/public/assets/admin/img/order-status/accepted.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>{{translate('Accepted by Delivery Man')}}</span>
                    </h6>
                    <span class="card-title text-success">
                        {{$data['accepted_by_dm']}}
                    </span>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a class="order--card h-100" href="{{route('admin.order.list',['processing'])}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('/public/assets/admin/img/order-status/packaging.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>{{translate('Packaging')}}</span>
                    </h6>
                    <span class="card-title text-FFA800">
                        {{$data['preparing_in_rs']}}
                    </span>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a class="order--card h-100" href="{{route('admin.order.list',['item_on_the_way'])}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('/public/assets/admin/img/order-status/out-for.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>{{translate('Out for Delivery')}}</span>
                    </h6>
                    <span class="card-title text-success">
                        {{$data['picked_up']}}
                    </span>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a class="order--card h-100" href="{{route('admin.order.list',['delivered'])}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('/public/assets/admin/img/order-status/delivered.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>{{translate('messages.delivered')}}</span>
                    </h6>
                    <span class="card-title text-success">
                        {{$data['delivered']}}
                    </span>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a class="order--card h-100" href="{{route('admin.order.list',['canceled'])}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('/public/assets/admin/img/order-status/canceled.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>{{translate('messages.canceled')}}</span>
                    </h6>
                    <span class="card-title text-danger">
                        {{$data['canceled']}}
                    </span>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a class="order--card h-100" href="{{route('admin.order.list',['refunded'])}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('/public/assets/admin/img/order-status/refunded.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>{{translate('messages.refunded')}}</span>
                    </h6>
                    <span class="card-title text-danger">
                        {{$data['refunded']}}
                    </span>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a class="order--card h-100" href="{{route('admin.order.list',['failed'])}}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <img src="{{asset('/public/assets/admin/img/order-status/payment-failed.svg')}}" alt="dashboard" class="oder--card-icon">
                        <span>{{translate('messages.payment')}} {{translate('messages.failed')}}</span>
                    </h6>
                    <span class="card-title text-danger">
                        {{$data['refund_requested']}}
                    </span>
                </div>
            </a>
        </div>
    </div>
</div>
