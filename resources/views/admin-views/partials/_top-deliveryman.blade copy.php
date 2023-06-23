<!-- Header -->
<div class="card-header align-items-center flex-wrap">
    <h5 class="card-header-title">
        <i class="tio-bike"></i> {{translate('messages.top_deliveryman')}}
    </h5>
    @php($params=session('dash_params'))
    @if($params['zone_id']!='all')
        @php($zone_name=\App\Models\Zone::where('id',$params['zone_id'])->first()->name)
    @else
        @php($zone_name = translate('messages.all'))
    @endif
    {{--<label class="badge badge-soft-primary">{{translate('messages.zone')}} : {{$zone_name}}</label>--}}
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    <div class="row g-2">
        @foreach($top_deliveryman as $key=>$item)
            <div class="col-md-4 col-6" onclick="location.href='{{route('admin.delivery-man.preview',[$item['id']])}}'">
                <div class="grid-card style-2 position-relative">
                    <label class="label_1">{{ translate('messages.orders') }} : {{$item['order_count']}}</label>
                    <center class="mt-6">
                        <img class="img--60 img--circle" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/delivery-man')}}/{{$item['image']??''}}">
                    </center>
                    <div class="text-center mt-2">
                        <span class="fz--13">{{$item['f_name']??'Not exist'}}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- End Body -->
