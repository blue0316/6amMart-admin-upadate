<!-- Header -->
<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        <span>{{translate('top selling')}} @if (Config::get('module.current_module_type')== 'food')
            {{ translate('messages.foods') }}
        @else
            {{ translate('messages.items') }}
        @endif</span>
    </h5>
    @php($params=session('dash_params'))
    @if($params['zone_id']!='all')
        @php($zone_name=\App\Models\Zone::where('id',$params['zone_id'])->first()->name)
    @else
        @php($zone_name = translate('messages.all'))
    @endif
    {{--<label class="badge badge-soft-primary">{{translate('messages.zone')}} : {{$zone_name}}</label>--}}
    <a href="{{ route('admin.item.list') }}" class="fz-12px font-medium text-006AE5">{{translate('view_all')}}</a>
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    <div class="top--selling">
        @foreach($top_sell as $key=>$item)
            <a class="grid--card" href="{{route('admin.item.view',[$item['id']])}}">
                <img class="initial--28"
                        src="{{asset('storage/app/public/product')}}/{{$item['image']}}"
                        onerror="this.src='{{asset('public/assets/admin/img/placeholder-2.png')}}'"
                        alt="{{$item->name}} image">
                <div class="cont pt-2">
                    <span class="fz--13">{{Str::limit($item['name'],20,'...')}}</span>
                </div>
                <div class="ml-auto">
                    <span class="badge badge-soft">
                        {{translate('messages.sold')}} : {{$item['order_count']}}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</div>
<!-- End Body -->
