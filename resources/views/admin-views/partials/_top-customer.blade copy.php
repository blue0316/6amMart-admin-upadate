<!-- Header -->
<div class="card-header align-items-center flex-wrap">
    <h5 class="card-header-title">
        <i class="tio-users-switch"></i> {{translate('messages.top_customers')}}
    </h5>
    @php($params=session('dash_params'))
    @if($params['zone_id']!='all')
        @php($zone_name=\App\Models\Zone::where('id',$params['zone_id'])->first()->name)
    @else
        @php($zone_name = translate('messages.all'))
    @endif
    {{--<label class="badge badge-soft-info">{{translate('messages.zone')}} : {{$zone_name}}</label>__}}
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    <div class="row">
        @foreach($top_customer as $key=>$item)
            <div class="col-6 col-md-4 mt-2 initial--27"
                 onclick="location.href='{{route('admin.customer.view',[$item['user_id']])}}'">
                <div class="grid-card min-height-170">
                    <label class="label_1">Orders : {{$item['count']}}</label>
                    <center class="mt-6">
                        <img class="img--60 img--circle" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/profile/'.$item->customer->image??'')}}">
                    </center>
                    <div class="text-center mt-2">
                        <span class="fz--10">{{$item->customer['f_name']??'Not exist'}}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- End Body -->
