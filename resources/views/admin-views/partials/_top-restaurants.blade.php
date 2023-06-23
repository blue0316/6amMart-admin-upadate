<!-- Header -->
<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        {{translate('top selling stores')}}
    </h5>
    @php($params=session('dash_params'))
    @if($params['zone_id']!='all')
        @php($zone_name=\App\Models\Zone::where('id',$params['zone_id'])->first()->name)
    @else
        @php($zone_name = translate('messages.all'))
    @endif
    {{--<label class="badge badge-soft-primary">{{translate('messages.zone')}} : {{$zone_name}}</label>--}}
        <a href="{{ route('admin.store.list') }}" class="fz-12px font-medium text-006AE5">{{translate('view_all')}}</a>
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body __top-resturant-card">
    <div class="__top-resturant">
        @foreach($top_restaurants as $key=>$item)
        <a href="{{route('admin.store.view', $item->id)}}">
            <div class="position-relative overflow-hidden">
                <img onerror="this.src='{{asset('public/assets/admin/img/100x100/1.png')}}'" src="{{asset('storage/app/public/store')}}/{{$item['logo']}}">
                <h5 class="info m-0">
                    {{translate('order : ')}} {{$item['order_count']}}
                </h5>
            </div>
        </a>
        @endforeach
    </div>
</div>
<!-- End Body -->
