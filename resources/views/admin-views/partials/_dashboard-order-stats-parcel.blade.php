<div class="col-sm-6 col-lg-3">
    <!-- Card -->
    <a class="resturant-card dashboard--card __dashboard-card card--bg-1" href="{{route('admin.parcel.list',['searching_for_deliverymen'])}}">
    <span class="meter">
            <span style="height:{{$data['total_orders']>0?($data['searching_for_dm']*100)/$data['total_orders']:0}}%"></span>
    </span>
    <h4 class="title">{{$data['searching_for_dm']}}</h4>
    <span class="subtitle">{{translate('unassigned_orders')}}</span>
    <img src="{{asset('/public/assets/admin/img/dashboard/1.png')}}" alt="img" class="resturant-icon">
    </a>
    <!-- End Card -->
</div>

<div class="col-sm-6 col-lg-3">
    <!-- Card -->
    <a class="resturant-card dashboard--card __dashboard-card card--bg-2" href="{{route('admin.parcel.orders',['item_on_the_way'])}}">
    <span class="meter">
            <span style="height:{{$data['total_orders']>0?($data['picked_up']*100)/$data['total_orders']:0}}%"></span>
    </span>
    <h4 class="title">{{$data['picked_up']}}</h4>
    <span class="subtitle">{{translate('out_for_delivery')}}</span>
    <img src="{{asset('/public/assets/admin/img/dashboard/4.png')}}" alt="img" class="resturant-icon">
    </a>
    <!-- End Card -->
</div>
<div class="col-sm-6 col-lg-3">
    <!-- Card -->
    <a class="resturant-card dashboard--card __dashboard-card card--bg-3" href="{{route('admin.parcel.orders',['delivered'])}}">
    <span class="meter">
            <span style="height:{{$data['total_orders']>0?($data['delivered']*100)/$data['total_orders']:0}}%"></span>
    </span>
    <h4 class="title">{{$data['delivered']}}</h4>
    <span class="subtitle">{{translate('delivered')}}</span>
    <img src="{{asset('/public/assets/admin/img/dashboard/2.png')}}" alt="img" class="resturant-icon">
    </a>
    <!-- End Card -->
</div>

<div class="col-sm-6 col-lg-3">
    <!-- Card -->
    <a class="resturant-card dashboard--card __dashboard-card card--bg-4" href="{{route('admin.parcel.orders',['failed'])}}">
    <span class="meter">
            <span style="height:{{$data['total_orders']>0?($data['refund_requested']*100)/$data['total_orders']:0}}%"></span>
    </span>
    <h4 class="title">{{$data['refund_requested']}}</h4>
    <span class="subtitle">{{translate('Failed Orders')}}</span>
    <img src="{{asset('/public/assets/admin/img/dashboard/5.png')}}" alt="img" class="resturant-icon">
    </a>
    <!-- End Card -->
</div>