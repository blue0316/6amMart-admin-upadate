<!-- Header -->
<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        {{translate('most rated')}} @if (Config::get('module.current_module_type')== 'food')
            {{ translate('messages.restaurants') }}
        @else
            {{ translate('messages.stores') }}
        @endif
    </h5>
    @php($params=session('dash_params'))
    @if($params['zone_id']!='all')
        @php($zone_name=\App\Models\Zone::where('id',$params['zone_id'])->first()->name)
    @else
        @php($zone_name = translate('messages.all'))
    @endif
    {{--<label class="badge badge-soft-primary">{{translate('messages.zone')}} : {{$zone_name}}</label>
        <a href="{{ route('admin.store.list') }}" class="fz-12px font-medium text-006AE5">{{translate('view_all')}}</a>--}}
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    <ul class="most-popular">
    @foreach($popular as $key=>$item)
        <li class="cursor-pointer" onclick="location.href='{{route('admin.store.view', $item->store_id)}}'">
            <div class="img-container">
                <img onerror="this.src='{{asset('public/assets/admin/img/100x100/1.png')}}'" src="{{asset('storage/app/public/store')}}/{{$item->store['logo']}}" alt="{{translate('store')}}">
                <span class="ml-2"> {{Str::limit($item->store->name??translate('messages.store deleted!'), 20, '...')}} </span>
            </div>
            <span class="badge badge-soft text--primary px-2">
                <span>
                    {{$item['count']}}
                </span>
                <i class="tio-star"></i>
            </span>
        </li>
    @endforeach
    </ul>
</div>
