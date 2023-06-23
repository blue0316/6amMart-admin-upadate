<!-- Header -->
<div class="card-header">
    <h5 class="card-header-title text-capitalize">
        <i class="tio-star"></i> {{translate('messages.top_rated_items')}}
    </h5>
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    <div class="row g-2">
        @foreach($most_rated_items as $key=>$item)
        <div class="col-md-4 col-6">
            <div class="grid-card top--rated-food pb-4 cursor-pointer" onclick="location.href='{{route('vendor.item.view',[$item['id']])}}'">
                <center>
                    <img class="rounded" src="{{asset('storage/app/public/product')}}/{{$item['image']}}" onerror="this.src='{{asset('public/assets/admin/img/100x100/2.png')}}'" alt="{{Str::limit($item->name??translate('messages.Item deleted!'),20,'...')}}">
                </center>

                <div class="text-center mt-3">
                    <h5 class="name m-0 mb-1">{{Str::limit($item->name??translate('messages.Item deleted!'),20,'...')}}</h5>
                    <div class="rating">
                        <span class="text-warning"><i class="tio-star"></i> {{round($item['avg_rating'],1)}}</span>
                        <span class="text--title">({{$item['rating_count']}}  {{ translate('messages.reviews') }})</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        </tbody>
    </div>
</div>
<!-- End Body -->
