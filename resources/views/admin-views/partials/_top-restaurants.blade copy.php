<!-- Header -->
<div class="card-header align-items-center flex-wrap">
    <h5 class="card-header-title">
        <i class="tio-medal"></i> {{translate('messages.top_stores')}}
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
    <div class="top--resturant">
        @foreach($top_restaurants as $key=>$item)
        <li>
            <div class="top--resturant-item cursor-pointer" onclick="location.href='{{route('admin.store.view', $item->id)}}'">
                <img onerror="this.src='{{asset('public/assets/admin/img/100x100/1.png')}}'" src="{{asset('storage/app/public/store')}}/{{$item['logo']}}">
                <div class="top--resturant-item-content">
                    <h5 class="name mt-0 mb-2">
                        {{Str::limit($item->name??translate('messages.store deleted!'), 20, '...')}}
                    </h5>
                    <h5 class="info m-0">
                        {{$item['order_count']}} <i class="tio-shopping-cart text-primary"></i>
                    </h5>
                </div>
            </div>
        </li>
        @endforeach
    </div>
</div>
<!-- End Body -->
