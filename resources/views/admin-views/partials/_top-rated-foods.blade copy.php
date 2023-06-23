<!-- Header -->
<div class="card-header align-items-center flex-wrap">
    <h5 class="card-header-title text-capitalize">
        <i class="tio-star"></i> {{translate('messages.top_rated_items')}}
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
        @foreach($top_rated_foods as $key=>$item)
            <div class="col-md-4 col-6">
                <div class="grid-card top--rated-food pb-4 cursor-pointer" onclick="location.href='{{route('admin.item.view',[$item['id']])}}'">
                    <center>
                        <img class="rounded" src="{{asset('storage/app/public/product')}}/{{$item['image']}}" onerror="this.src='{{asset('public/assets/admin/img/100x100/2.png')}}'" alt="{{Str::limit($item->name??translate('messages.Item deleted!'),20,'...')}}">
                    </center>

                    <div class="text-center mt-3">
                        <h5 class="name m-0 mb-1">{{Str::limit($item->name??translate('messages.Item deleted!'),20,'...')}}</h5>
                        <div class="rating">
                            <span class="text-warning"><i class="tio-star"></i> {{round($item['avg_rating'],1)}}</span>
                            <span class="text--title">({{$item['rating_count']}}  {{ translate('messages.review') }})</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- End Body -->
