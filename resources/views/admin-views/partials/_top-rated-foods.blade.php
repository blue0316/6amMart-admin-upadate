<!-- Header -->
<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        <span>
            {{translate('top rated')}}@if (Config::get('module.current_module_type')== 'food')
            {{ translate('messages.foods') }}
        @else
            {{ translate('messages.items') }}
        @endif
        </span>
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
    <div class="rated--products">
        @foreach($top_rated_foods as $key=>$item)
            <a href="{{route('admin.item.view',[$item['id']])}}">
                <div class="rated-media d-flex align-items-center">
                    <img src="{{asset('storage/app/public/product')}}/{{$item['image']}}" onerror="this.src='{{asset('public/assets/admin/img/100x100/2.png')}}'" alt="{{Str::limit($item->name??translate('messages.Item deleted!'),20,'...')}}">
                    <span class="line--limit-1 w-0 flex-grow-1">
                        {{Str::limit($item->name??translate('messages.Item deleted!'),20,'...')}}
                    </span>
                    <div>
                        <span class="text-FF6D6D">{{$item['rating_count']}} <i class="tio-heart"></i></span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
<!-- End Body -->
