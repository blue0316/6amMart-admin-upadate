<div class="card-header border-0 text-center">
    <h4>Stock</h4>
    <input name="product_id" value="{{$product['id']}}" class="initial-hidden">
</div>
<div class="card-body">
    <div class="form-group">
        <div class="mb-4">
            <div class="variant_combination" id="variant_combination">
                @include('vendor-views.product.partials._edit-combinations',['combinations'=>json_decode($product['variations'],true),'stock'=>config('module.'.$product->module->module_type)['stock']])
            </div>
            <div id="quantity">
                <label class="form-label" for="total_stock">{{translate('messages.total_stock')}}</label>                                
                <input type="number" class="form-control" name="current_stock" value="{{$product->stock}}" id="quantity" {{count(json_decode($product['variations'],true)) > 0 ? 'readonly' : ""}}>
            </div>
        </div>
    </div>
</div>