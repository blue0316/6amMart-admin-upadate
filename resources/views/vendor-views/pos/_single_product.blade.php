<div class="product-card card" onclick="quickView('{{$product->id}}')">
    <div class="card-header inline_product clickable p-0 initial--31">
        <div class="d-flex align-items-center justify-content-center d-block h-100">
            <img src="{{asset('storage/app/public/product')}}/{{$product['image']}}"
                onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" class="w-100 h-100 object-cover">
        </div>
    </div>


    <div class="card-body inline_product text-center p-1 clickable initial--32">
        <div class="position-relative product-title1 text-dark font-weight-bold text-capitalize">
            {{ Str::limit($product['name'], 12,'...') }}
        </div>
        <div class="justify-content-between text-center">
            <div class="product-price text-center">
                {{--@if($product->discount > 0)
                    <strike class="fz-12px color-8a8a8a">
                        {{\App\CentralLogics\Helpers::format_currency($product['price'])}}
                    </strike><br>
                @endif--}}
                <span class="text-accent text-dark font-weight-bold">
                    {{\App\CentralLogics\Helpers::format_currency($product['price']-\App\CentralLogics\Helpers::product_discount_calculate($product, $product['price'], $store_data))}}
                </span>
            </div>
        </div>
    </div>
</div>
