<!-- Header -->
<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        <span>{{translate('messages.top_customers')}}</span>
    </h5>
        <a href="{{ route('admin.users.customer.list') }}" class="fz-12px font-medium text-006AE5">{{translate('view_all')}}</a>
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    <div class="top--selling">
        
            @foreach($top_customers as $key=>$item)
            <a class="grid--card" href="{{route('admin.users.customer.view',[$item['id']])}}">
                <img onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/profile/'.$item->image??'')}}">
                <div class="cont pt-2">
                    <h6 class="mb-1">{{$item['f_name']?? translate('Not exist')}}</h6>
                    <span>{{$item['phone']??''}}</span>
                </div>
                <div class="ml-auto">
                    <span class="badge badge-soft">{{ translate('Orders') }} : {{$item['order_count']}}</span>
                </div>
            </a>
            @endforeach

    </div>
</div>
<!-- End Body -->
