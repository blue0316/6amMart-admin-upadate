<!-- Header -->
<div class="card-header align-items-center flex-wrap">
    <h5 class="card-header-title text-capitalize">
        <i class="tio-restaurant"></i> {{translate('messages.popular_stores')}}
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
    <ul class="most-popular">
    @foreach($popular as $key=>$item)
        <li class="cursor-pointer" onclick="location.href='{{route('admin.store.view', $item->store_id)}}'">
            <div class="img-container">
                <img onerror="this.src='{{asset('public/assets/admin/img/100x100/1.png')}}'" src="{{asset('storage/app/public/store')}}/{{$item->store['logo']}}" alt="{{translate('store')}}">
                <span class="ml-2"> {{Str::limit($item->store->name??translate('messages.store deleted!'), 20, '...')}} </span>
            </div>
            <span class="count">
                {{$item['count']}} <i class="tio-heart"></i>
            </span>
        </li>
    @endforeach
    </ul>
</div>
